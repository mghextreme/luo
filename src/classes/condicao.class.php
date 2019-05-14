<?php
	if (!class_exists('Variavel'))
	{ include(__DIR__.'/variavel.class.php'); }
	
	abstract class Condicao {
		// string - operação
		public $op = '=';
		
		// int - id do sistema
		public $sistema;
		
		// void - construtor
		function __construct(){ }
		
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
		function __construct(){ }
		
		// bool - implementação do método descrito em Condicao
		public function IsTrue(){
			$resposta = array(
				'result' => NULL,
				'certeza' => 1
			);
			
			if (isset($_SESSION['s'.$this->sistema]['variaveis'][$this->variavel->id]['valor'])){
				$variavelValor = $_SESSION['s'.$this->sistema]['variaveis'][$this->variavel->id]['valor'];
				$resposta['certeza'] = $_SESSION['s'.$this->sistema]['variaveis'][$this->variavel->id]['certeza'];
				
				if ($variavelValor !== NULL){
					switch ($this->op){
						case '=': $resposta['result'] = $variavelValor == $this->valor; break;
						case '!=': $resposta['result'] = $variavelValor != $this->valor; break;
						case '<': $resposta['result'] = $variavelValor < $this->valor; break;
						case '<=': $resposta['result'] = $variavelValor <= $this->valor; break;
						case '>=': $resposta['result'] = $variavelValor >= $this->valor; break;
						case '>': $resposta['result'] = $variavelValor > $this->valor; break;
					}
				}
			}
			
			return $resposta;
		}
	}
	
	class CondicaoLogica extends Condicao {
		// Condicao[] - lista de condições a serem atendidas
		public $condicoes = array();
		
		// void - construtor
		function __construct(){ }
		
		// void - adiciona uma Condicao à lista
		public function addCondicao($cond){
			if ($cond instanceof Condicao){
				$condicoes[] = $cond;
			}
		}
		
		// bool - implementação do método descrito em Condicao
		// priorizar execução
		public function IsTrue(){
			$resposta = array(
				'result' => NULL,
				'certeza' => 1
			);
			
			if (count($this->condicoes) > 0){
				$anyNull = FALSE;
				foreach ($this->condicoes as $condicao){
					$val = $condicao->isTrue();
//					var_dump($val);
					
					if ($val['result'] === NULL)
					{ $anyNull = TRUE; }
					
					$resposta['certeza'] *= $val['certeza'];
					
					switch ($this->op){
						case '&&':
							if ($val['result'] === FALSE)
							{ $resposta['result'] = FALSE; }
							break;
						case '||':
							if ($val['result'])
							{ $resposta['result'] = TRUE; }
							break;
						case '!':
							$resposta['result'] = $anyNull ? NULL : !$val['result'];
							break;
					}
				}
				unset($condicao);
				
				if ($resposta['result'] === NULL && !$anyNull && $this->op != '!')
				{ $resposta['result'] = $this->op == '&&'; }
			}
			
			return $resposta;
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
		function __construct(){ }
	}
?>