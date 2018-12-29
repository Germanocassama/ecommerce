<?php 

// Definir namespace da classe Hcode
namespace Hcode;
// Definir Namespace do Rain TPL
use Rain\TPL;


class page{

	private $tpl;
	// criar variaveis que vão serem passadas por slim de acordo com as rotas nos contrutores e destrutores 
	private $options = [];
	private $defaults = [
		"data"=>[]
	];
	public function __construct($opts = array()){
		$this->options = array_merge($this->defaults, $opts); /* array_merge --> serve para mesclar, o último sobscreve anteriores*/
		// configurar diretório
	$config = array(

		"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
		// $_SERVER["DOCUMENT_ROOT"]."/views/" --> Procur no diretório root (principal) do projeto o tamplate views
		"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
		"debug"         => false // set to false to improve the speed
		);

	Tpl::configure( $config);
	// criar classes tpl 
	$this->tpl = new TPL; // instanciar tpl com $this para poder ter acesso nos outros atributos e métodos 
	$this->setData($this->options["data"]); // chamar a função setData() e acessar inforçoes que estão dentro do array data
	// Cabeçalho da nossa página
	$this->tpl->draw("header");  // Draw é um método do tpl por isso usamsos  tpl->draw("header")
}
	
	// Criar método setData para acessar os dados 
	private  function setData($data = array())
	{

		foreach ($data as $key => $value) {
		$this->tpl->assign($key, $value); 
		}
	}

	
	// Criar corpo da nossa página 
	//$nome = nome das nossas paginas, $data = nossos dados 
	// $retunHTML = serve para retornar html ou mostrar conteúdo na tela 
	public function setTpl($nome, $data = array(), $returnHTML = false)
	{
		$this->setData($data); // chamar a função setData
		// desenhar tamplate na tela
		return $this->tpl->draw($nome, $returnHTML);

	}

	public function __destruct()
	{
		$this->tpl->draw("footer"); // rodapé da página 

	}
}


 ?>