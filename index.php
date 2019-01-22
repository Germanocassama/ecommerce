<?php 
session_start();// iniciar sessao
// autoload 
require_once("vendor/autoload.php");
//Namespaces
use \Slim\Slim;

$app = new Slim();
$app->config('debug', true); 

//////// Includs ////////////////
require_once("siteadmin/site.php");
require_once("siteadmin/admin.php");
require_once("siteadmin/users-admin.php");
require_once("siteadmin/admin-categories.php");
require_once("siteadmin/admin-products.php");


$app->run();
 ?>