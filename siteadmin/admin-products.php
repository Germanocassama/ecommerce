<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;
	
$app->get("/admin/products", function(){
	User::verifyLogin();
	$products = Product::listAll();
	$page = new pageAdmin();
	$page->setTpl("products", [
		"products"=>$products
	]);
	

});
// criar produtos 
$app->get("/admin/products/create", function(){
	User::verifyLogin();
	$page = new pageAdmin();
	$page->setTpl("products-create");

});

// salvar produtos
$app->post("/admin/products/create", function(){
	User::verifyLogin();
	$product = new Product();
	$product->setData($_POST);
	$product->save();
	header("Location: /admin/products");
	exit;
});
// Obter produtos para atualizar
$app->get("/admin/products/:idproduct", function($idproduct){
	User::verifyLogin();
	$page = new pageAdmin();
	$product = new Product();
	$product->get((int)$idproduct);

	$page->setTpl("products-update", [
		'product'=>$product->getvalues()
	]);

});
// atualizar produtos 
$app->post("/admin/products/:idproduct", function($idproduct){
	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->setData($_POST);
	$product->save();
	$product->setPhoto($_FILES["file"]);
	header("Location: /admin/products");
	exit;


});
// Deletar produtos 
$app->get("/admin/products/:idproduct/delete", function($idproduct){
	User::verifyLogin();
	$page = new pageAdmin();
	$product = new Product();
	$product->get((int)$idproduct);
	$product->delete();
	header("Location: /admin/products");
	exit;


});




?>