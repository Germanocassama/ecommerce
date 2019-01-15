<?php 
session_start();// iniciar sessao
// autoload 
require_once("vendor/autoload.php");
//Namespaces
use \Slim\Slim;
use Hcode\page; // página principal 
use Hcode\pageAdmin; // página de admin 
use Hcode\Model\User;

$app = new Slim();
$app->config('debug', true); 

// Página de conteúdos index
$app->get('/', function() {
	$page = new page(); // New page cria uma página e coloca o header e setTpl correga o conteúdo que está dentro do index
	$page->setTpl("index");//Depois desta linha o destrut é chamado para mostrar rodapé da página 

});
// Página de admin
$app->get('/admin', function() {
	// método statico para verificar se usuário está logado
	User::verifyLogin();
	$page = new pageAdmin(); 
	$page->setTpl("index");

});

// Página de login
$app->get('/admin/login', function() {
	$page = new pageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("login");

});
// receber o login via post e validar 
$app->post('/admin/login', function(){
	// metodo statico
	User::login($_POST["login"], $_POST["password"]);
	// se não der erro
	header("Location: /admin");
	exit;
});
// Página logout
$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});

// Página users 
$app->get("/admin/users", function(){
	User::verifyLogin();
	$users = User::listAll();
	$page = new pageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));

});

// create 
$app->get("/admin/users/create", function(){
	User::verifyLogin();
	$page = new pageAdmin();
	$page->setTpl("users-create");


});
// Delete 
$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;

});

// update 
$app->get('/admin/users/:iduser', function($iduser){
   User::verifyLogin();
   $user = new User();
   $user->get((int)$iduser); // Carregar os dados 
   $page = new PageAdmin();
   $page ->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
 
});

// salvar create  
$app->post("/admin/users/create", function(){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // verificar se é um usuário admin 
	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;
	

});
// salvar update
$app->post("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // verificar se é um usuário admin 
	$user->get((int)$iduser); // Carregar os dados 
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;

});
 
 ////////////////////// admin esqueceu a senha ////////////////////////////////////////////
// Página forgot
$app->get('/admin/forgot', function() {
	$page = new pageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("forgot");

});

// receber dados 
$app->post("/admin/forgot", function(){
	$user = User::getForgot($_POST["email"]);
	header("Location: /admin/forgot/sent");
	exit;
});
$app->get("/admin/forgot/sent", function(){
	$page = new pageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("forgot-sent");

});

$app->get('/admin/forgot/reset',function(){
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new PageAdmin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl("forgot-reset",array(
      "name"=>$user["desperson"],
      "code"=>$_GET["code"]
    ));
});


$app->post("/admin/forgot/reset", function (){
	$forgot = User::validForgotDecrypt($_POST["code"]);
	User::setForgotUsed($forgot["idrecovery"]);
	$user = new User();
	$user->get((int)$forgot["iduser"]);
	// criar rest do password
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT,[
		"cost"=>12
	]);
	$user->setPassword($password);
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");
});

$app->run();

 ?>