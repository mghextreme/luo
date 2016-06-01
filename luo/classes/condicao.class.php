<?php
	if (!class_exists('Variavel'))
	{ include(__DIR__.'/variavel.class.php'); }
	
	abstract class Condicao {
		// string - operação
		public $op = '=';
		
		// void - construtor
		public __construct(){
			
		}
		
		// bool - retorna se a condição já foi atendida e NULL em caso de desconhecimento
		public function IsTrue(){
			return false;
		}
	}
	
	class CondicaoValor extends Condicao {
		// Variavel - objeto de variável
		public $variavel;
		
		// mixed - o valor com o qual ela deve ser comparado
		public $valor;
		
		// void - construtor
		public __construct(){
			
		}
		
		// bool - implementação do método descrito em Condicao
		public function IsTrue(){
			return NULL;
		}
	}
	
	class CondicaoLogica extends Condicao {
		// Condicao[] - lista de condições a serem atendidas
		public $condicoes = array();
		
		// void - construtor
		public __construct(){
			
		}
		
		// void - adiciona uma Condicao à lista
		public addCondicao($cond){
			if ($cond instanceof Condicao){
				$condicoes[] = $cond;
			}
		}
		
		// bool - implementação do método descrito em Condicao
		public function IsTrue(){
			return NULL;
		}
	}
	
	class Consequencia {
		// Variavel - variável que terá seu valor alterado
		public $variavel;
		
		// mixed - valor que a variável irá receber
		public $valor;
		
		// float - certeza da consequência
		public $certeza;
		
		// void - construtor
		public __construct(){
			
		}
	}
?>