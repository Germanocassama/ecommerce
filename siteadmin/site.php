<?php
use \Hcode\page; // página principal 

$app->get('', function(){
	$page = new page();
	$page->setTpl("index.php");
});

?>