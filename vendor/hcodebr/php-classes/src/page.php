<?php 

// Definir namespace da classe Hcode
namespace Hcode;
// Definir Namespace do Rain TPL
use Rain\TPL;


class page{

	private $tpl;
	// criar variaveis que serão passadas por slim de acordo com as rotas nos contrutores e destrutores 
	private $options = [];
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];
	public function __construct($opts = array(), $tpl_dir = "/views/"){ 
		$this->options = array_merge($this->defaults, $opts); /* array_merge --> serve para mesclar, a última variável passada sobscreve anteriores*/
		// configurar diretório
	$config = array(

		"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
		// $_SERVER["DOCUMENT_ROOT"]."/views/" --> Procurar no diretório root (raiz do projecto) tamplate views
		"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
		"debug"         => false // set to false to improve the speed
		);

	Tpl::configure( $config);
	// criar classes tpl 
	$this->tpl = new TPL; // instanciar tpl com $this para que possa ser acessado nos outros atributos e métodos 
	$this->setData($this->options["data"]); // chamar a função setData() e acessar informaçoes que estão dentro do array data
	// Cabeçalho da nossa página
	// se a opção header for chamado então carrega o header
	if($this->options["header"] === true) $this->tpl->draw("header");  /* Draw é um método do tpl por isso usamsos  tpl->draw("header")*/
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
	// $returnHTML = serve para retornar html ou mostrar conteúdo na tela 
	public function setTpl($nome, $data = array(), $returnHTML = false)
	{
		$this->setData($data); // chamar a função setData
		// desenhar tamplate na tela
		return $this->tpl->draw($nome, $returnHTML);

	}

	public function __destruct()
	{
		// se a opção footer foi chamado então carrega o footer
		if($this->options["footer"] === true) $this->tpl->draw("footer"); // rodapé da página 

	}
}


 ?>