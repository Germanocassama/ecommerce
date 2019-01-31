<?php
use \Hcode\page;
use \Hcode\pageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
 
/////// Admin Categorias //////////////////////////////////////////////////////////////////////////////
// Listar categorias
$app->get("/admin/categories", function(){
	User::verifyLogin();
	// método static $categories -> serve para acessar todas categorias no banco de dados 
	$categories = Category::listAll(); 
	$page = new PageAdmin();
	$page->setTpl("categories",[
		// array de categorias 
		'categories'=>$categories
	]);

});
// carregar página categoria 
$app->get("/admin/categories/create", function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");

});

// criar categorias 
$app->post("/admin/categories/create", function(){
	User::verifyLogin();
	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header("Location: /admin/categories");
	exit;

});

// apagar uma categoria
$app->get("/admin/categories/:idcategory/delete", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();
	header("Location: /admin/categories");
	exit;
});

// Carregar categorias para atualizar 
$app->get("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$page = new PageAdmin();
	$category->get((int)$idcategory);
	
	$page->setTpl("categories-update",[
		// array de categorias
		'category'=>$category->getValues()
	]);
});

// salvar atualização categorias 
$app->post("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Category(); 
	$category->get((int)$idcategory);
	$category->setData($_POST);  
	$category->save();
	header("Location: /admin/categories");
	exit;

});

$app->get("/categories/:idcategory", function($idcategory){
	$category = new Category();
	$page = new page();
	$category->get((int)$idcategory);
	$page->setTpl("category",[
		'category'=>$category->getValues(),
		// array de produtos 
		'products'=>[]
	]);
});

?>