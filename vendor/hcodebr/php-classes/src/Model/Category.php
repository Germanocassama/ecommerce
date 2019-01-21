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
	}
?>
