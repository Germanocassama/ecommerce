<?php 
namespace Hcode;
class PageAdmin extends Page {
	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
	{
		// chamada dos construtores 
		parent::__construct($opts, $tpl_dir);
	}
}
 ?>