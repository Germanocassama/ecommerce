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


?>