<?php 

namespace Hcode;

class Model{
	private $values = [];

	public function __call($name, $args) // $args são valores passados por este atributo
	{
		$method = substr($name, 0, 3); // a partir de posição zero traz 3 valores (0, 1, 2)
		// descobrir o nome do campo que foi chamado
		$fieldName = substr($name, 3, strlen($name));// conta a partir da posição 2 ate o final
		
		switch ($method) {
			case 'get':
					return $this->values[$fieldName];
				break;

			case 'set':
					$this->values[$fieldName] = $args[0];		
		}
	}

	// função setData -> serve para criar atributo para cada campo encontrado no banco 
	public function setData($data = array())
	{
		foreach ($data as $key => $value) {
			$this->{"set".$key}($value);
			//  {"set".$key} => nome do Método
			//  ($value) => são valores
		}
	}

	// função get value serve para recuperar os valores 
	public function getValues()
	{
		return $this->values;
	}
}

 ?>