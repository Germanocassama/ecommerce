<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
class Product extends Model {
		
		// Listar produtos 
		public static function listAll()
		{
			$sql = new Sql();
			return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
		}

		public static function checkList($list)
		{
		foreach ($list as &$row) {
			
			$p = new Product();
			$p->setData($row);
			$row = $p->getValues();
		}
		return $list;
		}

		


		// Salvar produtos 
		public function save()
		{
			$sql = new Sql();
			$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
				// baindParam
				":idproduct"=>$this->getidproduct(),
				":desproduct"=>$this->getdesproduct(),
				":vlprice"=>$this->getvlprice(),
				":vlwidth"=>$this->getvlwidth(),
				":vlheight"=>$this->getvlheight(),
				":vllength"=>$this->getvllength(),
				":vlweight"=>$this->getvlweight(),
				":desurl"=>$this->getdesurl()
			));
			$this->setData($results[0]);
		}

		// obter produto
		public function get($idproduct)
		{
			$sql = new Sql();
			$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
				// baindParam
				':idproduct'=>$idproduct
			]);
			$this->setData($results[0]);
		}
		
		// apagar produto
		public function delete()
		{
			$sql = new Sql();
			$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
				// baindParam
				':idproduct'=>$this->getidproduct()
			]);
		}
		// verificar se a foto existe
		public function checkPhoto()
		{
		if (file_exists($_SERVER['DOCUMENT_ROOT']
			.DIRECTORY_SEPARATOR."resourse"
			.DIRECTORY_SEPARATOR."site"
			.DIRECTORY_SEPARATOR."img"
			.DIRECTORY_SEPARATOR."products"
			.DIRECTORY_SEPARATOR.$this->getidproduct().".jpg"
			)) {
			$url = "/resourse/site/img/products/" . $this->getidproduct().".jpg";
		} else {
			$url = "/resourse/site/img/product.jpg";
		}
		return $this->setdesphoto($url);
		}
	// obter foto
		public function getValues()
		{
		$this->checkPhoto();
		$values = parent::getValues();
		return $values;
		}
		// inserir foto 
		public function setPhoto($file){
			//verificar tipo de arquivo 
			$extension = explode('.', $file['name']);
			//depois do ponto é extensão do arquivo 
			$extension = end($extension);
			switch ($extension) {
				case 'jpg':
				case 'jpeg':
				$image = imagecreatefromjpeg($file['tmp_name']);
					break;
					case 'gif':
					$image = imagecreatefromgif($file['tmp_name']);
					break;
					case 'png':
					$image = imagecreatefrompng($file['tmp_name']);
					break;
			}
			$dist = $_SERVER['DOCUMENT_ROOT']
			.DIRECTORY_SEPARATOR."resourse"
			.DIRECTORY_SEPARATOR."site"
			.DIRECTORY_SEPARATOR."img"
			.DIRECTORY_SEPARATOR."products"
			.DIRECTORY_SEPARATOR.$this->getidproduct().".jpg";

			imagejpeg($image, $dist);
			imagedestroy($image);
			$this->checkPhoto();


		}

		// Detalhes dos produtos 
		public function getFromURL($desurl){
			$sql = new sql();
			$rows = $sql->select("SELECT * FROM tb_products where desurl = :desurl",[
				':desurl'=>$desurl
			]);
			$this->setData($rows[0]);
		}

		public function getCategories(){
			$sql = new sql();

			return $sql->select("
				SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory
				WHERE b.idproduct = :idproduct",[
					':idproduct'=>$this->getidproduct()
				]);
		}

	}
?>
