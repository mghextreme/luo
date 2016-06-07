<?php
	if (!class_exists('Variavel'))
	{ include(__DIR__.'/variavel.class.php'); }
	
	abstract class Condicao {
		// string - operação
		public $op = '=';
		
		// int - id do sistema
		public $sistema;
		
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
			if(isset($_SESSION[$this->sistema]['variaveis'][$this->variavel->id]['valor'])){
				$variavelValor = $_SESSION[$this->sistema]['variaveis'][$this->variavel->id]['valor'];
				switch ($this->op){
					case '=':
						return $variavelValor == $this->valor;
						break;
					case '>':
						return $variavelValor > $this->valor;
						break;
					case '<':
						return $variavelValor < $this->valor;
						break;
					case '>=':
						return $variavelValor >= $this->valor;
						break;
					case '<=':
						return $variavelValor <= $this->valor;
						break;
					case '!=':
						return $variavelValor != $this->valor;
						break;
				}
			}
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
			// priorizar execução
		public function IsTrue(){
			if(count($condicoes) > 0){
				foreach($condicoes as $condicao){
					switch ($this->op){
						case '&&':
							if(!$condicao->isTrue()){
								return FALSE;
							}
							break;
						case '||':
							if($condicao->isTrue()){
								return TRUE;
							}
							break;

							// ta feito não garanto que vai ter
						case '!':
							return !$condicao->isTrue();
							break;
					}
				}
				unset($condicao);
				
				return $this->op == '&&';
			}
			
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