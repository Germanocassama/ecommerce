<?php 
session_start();
// 1ª vendor autoload do composer é constate
require_once("vendor/autoload.php");
//  Nossos namespaces, classes dentro do vendor que vamos precisar 
use \Slim\Slim;
use Hcode\page; // página principal 
use Hcode\pageAdmin; // página de admin 
use Hcode\Model\User;



$app = new Slim();
$app->config('debug', true);


// #Criar rotas para nossas paginas 

// Página de conteúdos index
$app->get('/', function() {
	$page = new page(); // New page cria uma página e coloca o header e setTpl correga o conteúdo que está dentro do index
	$page->setTpl("index");//Depois desta linha o destrut chamado para mostrar rodapé da nossa página 

});
// Página de admin
$app->get('/admin', function() {
	// método statico 
	User::verifyLogin();
	$page = new pageAdmin(); 
	$page->setTpl("index");

});

// Página de admin
$app->get('/admin/login', function() {
	$page = new pageAdmin([
		// desablitar a chamada automatica do header e do footer
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("login");

});

// receber o login e validar 
$app->post('/admin/login', function(){
	// metodo statico
	User::login($_POST["login"], $_POST["password"]);
	// se não der erro
	header("Location: /admin");
	exit;
});

$app->get('/admin/logout', function(){
	User::logout();

	header("Location: /admin/login");
	exit;

});





// Rodar rotas 
$app->run();

 ?>