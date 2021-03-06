<?php 

namespace Hcode\DB;

class Sql {

	const HOSTNAME = "127.0.0.1";
	const DBNAME = "db_ecommerce";
	const USERNAME = "root";
	const PASSWORD = "musquebalassana94";
	

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD
		);

	}

		// ASSOCIAR PARÂMETROS COM OS DADOS 
	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	// FUNÇÃO bindParam()
	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	// Função query 
	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);
		// Função setParams()
		$this->setParams($stmt, $params);

		$stmt->execute();

	}
	// Função select 
	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);
		// Função setParams()
		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>