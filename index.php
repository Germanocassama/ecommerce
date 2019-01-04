<?php 
// 1ª vendor autoload do composer é constate
require_once("vendor/autoload.php");
//  Nossos namespaces, classes dentro do vendor que vamos precisar 
use \Slim\Slim;
use Hcode\page; // página principal de apresentação de conteúdos definido no composer.json
$app = new Slim();
$app->config('debug', true);


// Criar rotas para nossas paginas 
$app->get('/', function() {
	$page = new page(); // New page cria uma página e coloca o header e setTpl correga o conteúdo que está dentro do index

	$page->setTpl("index");// chamar a página index.html depois desta linha ele vai chamar o destrut para mostrar rodapé 

});

$app->get('/', function(){
	$page = new page();

	$page->setTpl("laranja.html");
});

// Rodar rotas 
$app->run();

 ?>