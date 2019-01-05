<?php 
// 1ª vendor autoload do composer é constate
require_once("vendor/autoload.php");
//  Nossos namespaces, classes dentro do vendor que vamos precisar 
use \Slim\Slim;
use Hcode\page; // página principal 
use Hcode\pageAdmin; // página de admin 
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
	$page = new pageAdmin(); 
	$page->setTpl("index");

});


// Rodar rotas 
$app->run();

 ?>