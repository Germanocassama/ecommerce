<?php
use \Hcode\page;
use \Hcode\pageAdmin; // página principal 
use \Hcode\Model\User;
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
	User::setFogotUsed($forgot["idrecovery"]);
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

?>