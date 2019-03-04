<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;
class Cart extends Model {
	const SESSION = "Cart";
	const SESSION_ERROR = "CartError";

		// verificar se existe session
	public static function getFromSession()
	{
		$cart = new Cart();

		if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

			$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
		} else {

			$cart->getFromSessionID();

			if (!(int)$cart->getidcart() > 0) {

				$data = [
					'dessessionid'=>session_id()
				];
				if (User::checkLogin(false)) {

					$user = User::getFromSession();
					
					$data['iduser'] = $user->getiduser();	
				}
				
				$cart->setData($data);
				$cart->save();
				$cart->setToSession();
			}
		}
		return $cart;
	}

		// carregar session 
		public function setToSession(){
			$_SESSION[Cart::SESSION] = $this->getValues();

		}

		// obter sessão 
		public function getFromSessionID(){
			$sql = new sql();

			$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid",[
				':dessessionid'=>session_id()
			]);

			if (count($results) > 0) {
				$this->setData($results[0]);

			}
			
		}
		// salvar dados 
		public function save(){
			$sql = new sql();
			$results = $sql->select(" CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)",
				[
					':idcart'=>$this->getidcart(),
					':dessessionid'=>$this->getdessessionid(),
					':iduser'=>$this->getiduser(),
					':deszipcode'=>$this->getdeszipcode(),
					':vlfreight'=>$this->getvlfreight(),
					':nrdays'=>$this->getnrdays()
				]);
			$this->setData($results[0]);
	}	

	// Adicionar Produto no carrinho
	public function addProduct(Product $product)
	{
		$sql = new Sql();
		$sql->query("INSERT INTO tb_cartsproducts (idcart, idproduct) VALUES(:idcart, :idproduct)", [
			':idcart'=>$this->getidcart(),
			':idproduct'=>$product->getidproduct()
		]);

		$this->getCalculateTotal();
	}
	// Remover produto do carrinho 
	public function removeProduct(Product $product, $all = false)
	{
		$sql = new Sql();
		if ($all) {
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL", [
				':idcart'=>$this->getidcart(),
				':idproduct'=>$product->getidproduct()
			]);
		} else {
			// Remover um produto 
			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1", [
				':idcart'=>$this->getidcart(),
				':idproduct'=>$product->getidproduct()
			]);
		}
		$this->getCalculateTotal();
	}

	// Listar todos produtos dentro do carrinho 
	public function getProducts(){
     $sql = new Sql();
     $rows = $sql->select("
         SELECT b.idproduct,b.desproduct,b.vlprice,b.vlwidth,b.vlheight,b.vllength,b.vlweight,b.desurl,
         COUNT(*) AS nrqtd,SUM(b.vlprice) as vltotal
         FROM tb_cartsproducts a 
         INNER JOIN tb_products b USING (idproduct) 
         WHERE a.idcart = :idcart AND a.dtremoved IS NULL
         GROUP BY b.idproduct,b.desproduct,b.vlprice,b.vlwidth,b.vlheight,b.vllength,b.vlweight,b.desurl
         ORDER BY b.desproduct", [
             ":idcart"=>$this->getidcart()
     ]);
		return Product::checkList($rows);
	}

	// obter id do produto no carrinho
	public function get(int $idcart)
	{
	 
	    $sql = new Sql();
	 
	    $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
	        ':idcart'=>$idcart
	    ]);
	 
	    if (count($results) > 0) {
	 
	        $this->setData($results[0]);
	 
	    }
	 
	}

	// obter total dos produtos 
	public function getProductsTotals()
	{
		$sql = new Sql();
		$results = $sql->select("
			SELECT SUM(vlprice) AS vlprice, SUM(vlwidth) AS vlwidth, SUM(vlheight) AS vlheight, SUM(vllength) AS vllength, SUM(vlweight) AS vlweight, COUNT(*) AS nrqtd
			FROM tb_products a
			INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct
			WHERE b.idcart = :idcart AND dtremoved IS NULL;
		", [
			':idcart'=>$this->getidcart()
		]);
		if (count($results) > 0) {
			return $results[0];
		} else {
			return [];
		}
	}
	// calculo de freid 
	public function setFreight($nrzipcode)
	{
		$nrzipcode = str_replace('-', '', $nrzipcode);
		$totals = $this->getProductsTotals();
		// verificar carrinho de compra 
		if ($totals['nrqtd'] > 0) {
			if ($totals['vlheight'] < 2) $totals['vlheight'] = 2;
			if ($totals['vllength'] < 16) $totals['vllength'] = 16;
			// calcular freid
			$qs = http_build_query([  //http_build_query — Gera a string de consulta (query) em formato URL
				'nCdEmpresa'=>'',
				'sDsSenha'=>'',
				'nCdServico'=>'40010', // codigo postal 
				'sCepOrigem'=>'09853120',
				'sCepDestino'=>$nrzipcode,
				'nVlPeso'=>$totals['vlweight'],
				'nCdFormato'=>'1',
				'nVlComprimento'=>$totals['vllength'],
				'nVlAltura'=>$totals['vlheight'],
				'nVlLargura'=>$totals['vlwidth'],
				'nVlDiametro'=>'0',
				'sCdMaoPropria'=>'S',
				'nVlValorDeclarado'=>$totals['vlprice'],
				'sCdAvisoRecebimento'=>'S'
			]);
			// api xml para calculo de freid
			// simplexml_load_file - Interpreta um arquivo XML em um objeto
			// EX: 'http://example.com/?a='. Urlencode ('b & c'))
			$xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);
			$result = $xml->Servicos->cServico;
			if ($result->MsgErro != '') {
				Cart::setMsgError($result->MsgErro);
			} else {
				Cart::clearMsgError();
			}
			$this->setnrdays($result->PrazoEntrega);
			$this->setvlfreight(Cart::formatValueToDecimal($result->Valor));
			$this->setdeszipcode($nrzipcode);
			$this->save();
			return $result;
		} else {
		}
	}

	// Formatar valor para decimal 
	public static function formatValueToDecimal($value):float
	{
		$value = str_replace('.', '', $value); // substituir ponto por vazio
		return str_replace(',', '.', $value); // substituir vírgula por ponto 
	}

	// adicionar sessão
	public static function setMsgError($msg){
		$_SESSION[Cart::SESSION_ERROR] = $msg;
	}

	// obter sessão
	public static function getMsgError()
	{
		$msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";
		Cart::clearMsgError();
		return $msg;
	}

	// Limpar sessão
	public static function clearMsgError()
	{
		$_SESSION[Cart::SESSION_ERROR] = NULL;

	}

	// Atualizar freid 
	public function updateFreight()
	{
		if ($this->getdeszipcode() != '') {
			$this->setFreight($this->getdeszipcode());
		}

	}

	// Calcular Total e subtotal
	public function getValues()
	{
		$this->getCalculateTotal();
		return parent::getValues(); // herdar funçoes do getValues 
	}

	// Obter calculode de total
	public function getCalculateTotal()
	{
		$this->updateFreight(); // atualizar freid

		$totals = $this->getProductsTotals(); // trazar valores 
		$this->setvlsubtotal($totals['vlprice']);
		$this->setvltotal($totals['vlprice'] + $this->getvlfreight());

	}
		
}
?>
