<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
	class Category extends Model
	{
		
		public static function listAll()
		{
			$sql = new Sql();
			return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
		}

		public function save()
		{
			$sql = new Sql();
			$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
				// bindParam
				":idcategory"=>$this->getidcategory(),
				":descategory"=>$this->getdescategory()
			));
			$this->setData($results[0]);
			// método updateFile()
			Category::updateFile();
		}
		
		// método get
		public function get($idcategory)
		{
			$sql = new Sql();
			$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
				':idcategory'=>$idcategory
			]);
			$this->setData($results[0]);
		}

		// Paginação 
		public function getProductsPage($page = 1, $itemsPerPage = 4)
	{
		$start = ($page - 1) * $itemsPerPage;
		$sql = new Sql();
		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;
		", [
			':idcategory'=>$this->getidcategory()
		]);
		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");
		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}
		// método delete 
		public function delete()
		{
			$sql = new Sql();
			$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
				':idcategory'=>$this->getidcategory()
			]);

			//método updateFile()
			Category::updateFile();

		}

		// updateFile -> serve para atualizar categorias criadas 
		public static function updateFile(){

			$Categories = Category::listAll();
			// criar li's dinamicamente para trazer todas as categorias 
			$html = [];
			foreach ($Categories as $row) {
				array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
			}
			// salvar o arquivo
			// implode -> converte um array para String 
			file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html",implode('', $html));
		}

		// Produtos categorias 
		public function getProducts($related = true){
		$sql = new sql();
		
			if ($related === true) {
				// produtos relecionados 
				return $sql->select("SELECT * FROM tb_products where idproduct IN(
				select a.idproduct
				FROM tb_products a
				INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
				where b.idcategory = :idcategory); ",[
						':idcategory'=>$this->getidcategory()
					]);
			}else{
				// produtos não relecionados 
					return $sql->select("
					SELECT * FROM tb_products where idproduct NOT IN(
					select a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
					where b.idcategory = :idcategory

					);

				",[
						':idcategory'=>$this->getidcategory()
				]);
			}

		}

		// addProduct
		public function addProduct(Product $product)
		{
			$sql = new Sql();
			$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
		}
		// removeProduct
		public function removeProduct(Product $product)
		{
			$sql = new Sql();
			$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
				':idcategory'=>$this->getidcategory(),
				':idproduct'=>$product->getidproduct()
			]);
		}
	}
?>
