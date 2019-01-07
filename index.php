<?php 
session_start();// iniciar sessao
// 1ª vendor autoload do composer é constate
require_once("vendor/autoload.php");
//  Nossos namespaces, classes dentro do vendor que vamos precisar 
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
		// desablitar a chamada automatica do header e do footer
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
// rota de logout
$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
});
// users
$app->get('/admin/users', function() {
	User::verifyLogin();
	$page = new pageAdmin(); 
	$page->setTpl("users");

});
// users-create
$app->get('/admin/users/create', function(){
	User::verifyLogin();
	$page = new pageAdmin(); 
	$page->setTpl("users/create");

});
// users-update
$app->get('/admin/users/:iduser', function($iduser){ // iduser--> passamos id do usuário que será alterado 
	User::verifyLogin();
	$page = new pageAdmin(); 
	$page->setTpl("users-update");

});

// rota insert
$app->post('/admin/users/create', function(){
	User::verifyLogin();

});
// rota salvar update
$app->post('/admin/users/:iduser', function($iduser){
	User::verifyLogin();

});
// rota delete 
$app->delete('/admin/users/:iduser', function($iduser){
	User::verifyLogin();

});


// Rodar rotas 
$app->run();

 ?>