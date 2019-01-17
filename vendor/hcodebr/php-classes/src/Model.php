<?php 

namespace Hcode;

class Model{
	private $values = []; // $values -> é responsável por todos os campos dos valores que temos dentro do objeto 
	// função __call -> serve para saber qual é método que foi chamado
	public function __call($name, $args) // os valores são passados no atributo $args
	{	// Detectar quais são primeiros digitos que foi passado 
		$method = substr($name, 0, 3);//substr-> quer dizer a partir de posição zero traz 3 valores que foram passado
		// descobrir o nome do campo que foi chamado
		$fieldName = substr($name, 3, strlen($name));// conta a partir da posição 2 ate o final
		
		switch ($method) {
			case 'get':
					return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
				break;

			case 'set':
					$this->values[$fieldName] = $args[0];
				break;		
		}
	}

	// função setData -> serve para criar atributo para cada campo encontrado no banco 
	public function setData($data = array())
	{
		foreach ($data as $key => $value) {
			// a ideia desta linha é seguinte: idusuario tem que criar um set idusuario
			// obs: lembra que o nome do campo é recebido no campo $key
			$this->{"set".$key}($value);
			//  {"set".$key} => set é uma string mas como está entre chaves {} é um método mágico 
			
		}
	}

	// função getvalues serve para recuperar os valores 
	public function getValues()
	{
		return $this->values;
	}

	
}

 ?>