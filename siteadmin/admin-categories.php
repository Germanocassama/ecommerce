<?php
use \Hcode\pageAdmin; // página de admin 
use \Hcode\Model\User;
use \Hcode\Model\Category;
 
/////// Admin Categorias //////////////////////////////////////////////////////////////////////////////
// Listar categorias
$app->get("/admin/categories", function(){
	User::verifyLogin();
	// método $categories -> serve para acessar todas categorias no banco de dados 
	$categories = Category::listAll(); 
	$page = new PageAdmin();
	$page->setTpl("categories",[
		// array de categorias 
		'categories'=>$categories
	]);

});
// link categoria 
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
	$category->get((int)$idcategory);
	$page = new PageAdmin();

	$page->setTpl("categories-update",[
		// array de categorias
		'category'=>$category->getValues()
	]);
});

// atualizar categorias 
$app->post("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Category(); // criar uma categoria 
	$category->get((int)$idcategory);// carregar categoria 
	$category->setData($_POST); // inserir uma categoria 
	$category->save(); // salvar categoria 
	header("Location: /admin/categories");
	exit;

});

$app->get("/categories/:idcategory", function($idcategory){
	$category = new Category();
	$category->get((int)$idcategory);

	// chamar tamplate
	$page = new page();
	$page->setTpl("category", [
		// carregar dados dentro da categoria 
		'category'=>$category->getValues(),
		// array de produtos 
		'products'=>[]
	]);


});
?>