<?php 
	namespace Hcode\Model; 
	use \Hcode\Model;
	use \Hcode\DB\sql; // namespace sql

	class User extends Model{
		const SESSION = "User"; // difinir variável de sessão
		public static function login($login, $password)
		{
			$sql = new sql();

			$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
				":LOGIN"=>$login
			));
			if (count($results) === 0) 
			{
				
				throw new \Exception("Usuário enexistente ou senha inválida."); 
				//barra invertidade pk o nosso Exception esta no raiz
				
			}
			$data = $results[0]; 

			if (password_verify($password, $data["despassword"]) === true)
			{
				$user = new User();
				$user->setData($data);
				// verificar se o usuário existe na sessão 
				$_SESSION[User::SESSION] = $user->getValues();// capturar valores da sessão de usuário
				return $user;
			}else{
				throw new \Exception("Usuário enexistente ou senha inválida."); 
			}
		}

		public static function verifyLogin($inadmin = true)//$inadmin-> verificar se usuário está logado no admin
		{ 
			if(
				// se sessão não existir 
				!isset($_SESSION[User::SESSION])
				||// se for falso
				!$_SESSION[User::SESSION]
				||// se o id não for maior que zero
				!(int)$_SESSION[User::SESSION]["iduser"] > 0
				||// verificar se usuário pode acessar admin
				(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
			){
				header("Location: /admin/login");
				exit;
			}
		}

				
		// função logout
		public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
	}
}

 ?>
