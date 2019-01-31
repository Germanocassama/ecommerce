<?php
use \Hcode\page;
use \Hcode\pageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;
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
// Produtos com os seus respetivos categorias 
$app->get("/admin/categories/:idcategory/products", function($idcategory){
	User::verifyLogin();
	$category = new Category();
	$page = new pageAdmin();
	$category->get((int)$idcategory);
	$page->setTpl("categories-products",[
		'category'=>$category->getValues(),
		// array de produtos 
		'productsRelated'=>$category->getProducts(),
		'productsNotRelated'=>$category->getProducts(false)
	]);

});

// adicionar Produtos nos seus respetivos categorias 
$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$product = new Product();
	$product->get((int)$idproduct);
	$category->addProduct($product);
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});
// remover Produtos nos seus respetivos categorias 
$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){
	User::verifyLogin();
	$category = new Category();
	$category->get((int)$idcategory);
	$product = new Product();
	$product->get((int)$idproduct);
	$category->removeProduct($product);
	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});
?>