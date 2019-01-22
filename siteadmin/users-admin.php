<?php
use \Hcode\pageAdmin; // página de admin 
use \Hcode\Model\User;
////////////////////////// página de usuários /////////////////////////
// Página users 
$app->get("/admin/users", function(){
	User::verifyLogin();
	$users = User::listAll();
	$page = new pageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));
});
// create 
$app->get("/admin/users/create", function(){
	User::verifyLogin();
	$page = new pageAdmin();
	$page->setTpl("users-create");
});
// Delete 
$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;
});
// update 
$app->get('/admin/users/:iduser', function($iduser){
   User::verifyLogin();
   $user = new User();
   $user->get((int)$iduser); // Carregar os dados 
   $page = new PageAdmin();
   $page ->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
 
});
// salvar create  
$app->post("/admin/users/create", function () {
 	User::verifyLogin();
	$user = new User();
 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
 	// criar rest do password
 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);
 	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
 	exit;

});
// salvar update
$app->post("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // verificar se é um usuário admin 
	$user->get((int)$iduser); // Carregar os dados 
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});

?>