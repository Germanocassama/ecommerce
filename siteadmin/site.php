<?php
use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;

$app->get('/', function() {
	$products = Product::listAll();
	$page = new Page();
	$page->setTpl("index",[
		'products'=>Product::checkList($products)
	]);
});

$app->get("/categories/:idcategory", function($idcategory){
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$category = new Category();
	$category->get((int)$idcategory);
	$pagination = $category->getProductsPage($page);
	$pages = [];
	for ($i=1; $i <= $pagination['pages']; $i++) { 
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}
	$page = new Page();
	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);
});

// detalhes do produto 
$app->get("/products/:desurl", function($desurl){
	$product = new Product();
	$product->getFromURL($desurl);
	$page = new Page();
	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()
	]);
});

// cart
$app->get("/cart", function(){
	$cart = Cart::getFromSession();
	$page = new Page();
	$page->setTpl("cart",[
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts(),
		'error'=>Cart::getMsgError()
	]);
});

// Add Product
$app->get("/cart/:idproduct/add", function($idproduct){
	$product = new Product();
	$product->get((int)$idproduct);
	// Recuperar sessão 
	$cart = Cart::getFromSession();
	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;
	for ($i=0; $i < $qtd; $i++) { 
		$cart->addProduct($product);
	}
	header("Location: /cart");
	exit;

});

// Remover on Products
$app->get("/cart/:idproduct/minus", function($idproduct){
	$product = new Product();
	$product->get((int)$idproduct);
	// Recuperar sessão 
	$cart = Cart::getFromSession();
	$cart->removeProduct($product);
	header("Location: /cart");
	exit;
});

// Remover All Product
$app->get("/cart/:idproduct/remove", function($idproduct){
	$product = new Product();
	$product->get((int)$idproduct);
	// Recuperar sessão 
	$cart = Cart::getFromSession();
	$cart->removeProduct($product, true);
	header("Location: /cart");
	exit;
});

/// calculo de fred
$app->post('/cart/freight', function(){
	$cart = Cart::getFromSession();
	$cart->setFreight($_POST['zipcode']);
	header("Location: /cart");
	exit;
});

// Obrigar usuário fazer login para finalizar compra 
$app->get('/checkout', function(){
	User::verifyLogin(false);
	$cart = Cart::getFromSession();
	$address = new Address();
	$page = new Page();
	$page->setTpl("checkout", [
		'cart'=>$cart->getValues(),
		'address'=>$address->getValues()
	]);
});

// logar para finalizar compra 
$app->get('/login', function(){
	$page = new Page();
	$page->setTpl("login", [
		'error'=>User::getError(),
		'ErrorRegister'=>User::getErrorRegister(), // Exibir mensagem de erro na tela
		'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=> '', 'email'=>'', 'phone'=>'']
	]);
});

// fazer login no site
$app->post("/login", function(){
	try {
		User::login($_POST['login'], $_POST['password']);
	} catch(Exception $e) {
		User::setError($e->getMessage());
	}
	header("Location: /checkout");
	exit;
});

$app->get("/logout", function(){
	User::logout();
	header("Location: /login");
	exit;
});

// Registar usuário 
$app->post('/register', function(){
	// Registar valore recebidos 
	$_SESSION['registerValues'] = $_POST;
	// Verificar Usuário 
	if (!isset($_POST['name']) || $_POST['name'] == '') {
		User::setErrorRegister('Preencha o seu nome');
		header("Location: /login");
		exit;
	}
	if (!isset($_POST['email']) || $_POST['email'] == '') {
		User::setErrorRegister('Preencha o seu email');
		header("Location: /login");
		exit;
	}
	if (!isset($_POST['password']) || $_POST['password'] == '') {
		User::setErrorRegister('Preencha a senha');
		header("Location: /login");
		exit;
	}
	// verificar se usuário não existe 
	if (User::checkLoginExist($_POST['email']) == true) {
		User::setErrorRegister('Este email já existe!');
		header("Location: /login");
		exit;
	}
	$user = new User();
	$user->setData([
		'inadmin'=>0, // 0 pk não é um admin
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone']
	]);
	$user->save();
	User::login($_POST['email'], $_POST['password']); // Logar usuário logo 
	header("Location: /checkout");
	exit;
});



?>