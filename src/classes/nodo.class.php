<?php
	if (!class_exists('Condicao'))
	{ include(__DIR__.'/condicao.class.php'); }
	
	if (!class_exists('Nodo')) :
	
	class Nodo {
		// int - ID da regra a que o nodo se refere
		public $id;
		
		// int - ID do sistema ao que este nodo está relacionado;
		public $sistema;
		
		// Nodo[] - lista de Nodos filhos
		public $filhos = array();
		
		// Condicao[] - lista de Condicao para que as consequências sejam verdadeiras
		public $condicoes = array();
		
		// Consequencia[] - lista de Condicao para que as consequências sejam verdadeiras
		public $consequencias = array();
		
		// bool - marca se o nodo já teve todas as variáveis nas condições com algum valor e foi, consequentemente, inferido
		public $resolvido = FALSE;
		
		// bool - marca temporariamente se as condicoes estão resolvidas
		private $resolvidoTemp = FALSE;
		
		// bool - marca se o nodo já teve os nodos expandidos
		public $expandido = FALSE;
		
		// float - certeza das condições
		public $certeza = 1;
		
		// void - construtor
		function __construct($id){ $this->id = $id; }
		
		// Variavel - retorna a variável que deve ser questionada
		public function proximaPergunta(){
			global $_SESSION;
			
			if (!$this->resolvido){
				if (!$this->expandido)
				{ $this->expandir(); }
				
				if (count($this->filhos) > 0){
					// para cada filho
					for ($i = 0; $i < count($this->filhos); $i++){
						$result = $this->filhos[$i]->proximaPergunta();
						if (!empty($result))
						{ return $result; }
					}
				}
				
				$this->resolvidoTemp = TRUE;
				if (count($this->filhos) == 0){
					// Para cada condicao
					for ($i = 0; $i < count($this->condicoes); $i++){
						$res = $this->question($this->condicoes[$i]);
						
						if (!$res['resolvido']){
							$this->resolvidoTemp = FALSE;
							
							if (!empty($res['variavel']))
							{ return $res['variavel']; }
						}
					}
				}
				
				if ($this->resolvidoTemp)
				{ $this->resolvido = TRUE; }
			}
			return NULL;
		}
		
		
		public function question($cond){
			$tempVar;
			$result = array(
				'resolvido' => TRUE,
				'variavel' => NULL
			);
			
			if ($cond instanceof CondicaoValor){
				if ($_SESSION['s'.$this->sistema]['variaveis'][$cond->variavel->id]['valor'] === NULL){
					$tempVar = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$cond->variavel->id]['variavel']);
					if ($tempVar->questionavel){
						$result['resolvido'] = FALSE;
						$result['variavel'] = $tempVar;
						return $result;
					}
				}
			} else {
				for ($i = 0; $i < count($cond->condicoes); $i++){
					$res = $this->question($cond->condicoes[$i]);
					
					if (!$res['resolvido']){
						$result['resolvido'] = FALSE;

						if (!empty($res['variavel'])){
							$result['variavel'] = $res['variavel'];
							return $result;
						}
					}
				}
			}
			
			return $result;
		}
		
		// void - gerar filhos
		public function expandir(){
			if ($this->expandido || $this->resolvido || count($this->filhos) > 0)
			{ return; }
			
			global $conn, $arvore;
			
			$query = "SELECT regra FROM consequencia WHERE variavel IN (SELECT DISTINCT(variavel) FROM condicao WHERE regra = {$this->id})";
			//$query = "SELECT regra FROM consequencia WHERE variavel IN (SELECT DISTINCT(variavel) FROM condicao WHERE regra = {$this->id}) && NOT IN (-----CONDICOES JA ATENDIDADS-----)";
			$resultRegra = $conn->query($query);
			
			if ($resultRegra->num_rows > 0) {
				
				// faz a convesão
				while ($rule = $resultRegra->fetch_assoc()) {
					// nodo filho
					$nodoFilho = new Nodo($rule['regra']);
					$nodoFilho->sistema = $this->sistema;
					
					// pega as consequencias para aquela regra
					$query = "SELECT * FROM consequencia WHERE regra = {$nodoFilho->id};";
					$resulConsequencia = $conn->query($query);
					
					if ($resulConsequencia->num_rows > 0){
						while ($row = $resulConsequencia->fetch_assoc()){
							$consequenciaNodo = new Consequencia();
							$consequenciaNodo->id = $row['id'];
							$consequenciaNodo->variavel = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$consequenciaNodo->valor = $row['valor'];
							$consequenciaNodo->certeza = floatval($row['certeza']);
							
							$nodoFilho->consequencias[] = $consequenciaNodo;
						}
					}
					
					// pega as condições para aquela regra
					$query = "SELECT * FROM condicao WHERE regra = {$nodoFilho->id} AND pai IS NULL;";
					$resultCondicao = $conn->query($query);
					if ($resultCondicao->num_rows > 0) {
						while ($row = $resultCondicao->fetch_assoc()) {
							if (in_array($row['op'], array("&&", "||", "!"))){
								$condicaoFilho = new CondicaoLogica();
								$condicaoFilho->id = $row['id'];
								$condicaoFilho->sistema = $this->sistema;
								$condicaoFilho->op = $row['op'];
								$condicaoFilho->condicoes = $this->getFilhos($nodoFilho->id, $condicaoFilho->id);
							} else {
								$condicaoFilho = new CondicaoValor();
								$condicaoFilho->id = $row['id'];
								$condicaoFilho->sistema = $this->sistema;
								$condicaoFilho->op = $row['op'];
								$condicaoFilho->variavel = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$row['variavel']]['variavel']);
								$condicaoFilho->valor = $row['valor'];	
							}
							 
							$nodoFilho->condicoes[] = $condicaoFilho;

						}
					}
					
					if (count($nodoFilho->condicoes) > 0){
						$nodoFilho->verificar();
						// condicionar a aceitação do filho, por hora caminho feliz
						$this->filhos[] = $nodoFilho;
					}
				}
			}
			
			$this->expandido = TRUE;
		}
		
		// array de condicoes filhas - achar as condições filhas de uma condição logica
		public function getFilhos($regra, $condicaoPaiId){
			global $conn;
			
			$condicoes = array();
			$query = "SELECT * FROM condicao WHERE regra = {$regra} AND pai = {$condicaoPaiId};";
				$resultCondicao = $conn->query($query);
				if ($resultCondicao->num_rows > 0) {
					while ($row = $resultCondicao->fetch_assoc()) {
						if (in_array($row['op'], array("&&", "||", "!"))){
							$condicaoFilho = new CondicaoLogica();
							$condicaoFilho->id = $row['id'];
							$condicaoFilho->sistema = $this->sistema;
							$condicaoFilho->op = $row['op'];
							$condicaoFilho->condicoes = getFilhos($regra, $condicaoFilho->id);
						} else {
							$condicaoFilho = new CondicaoValor();
							$condicaoFilho->id = $row['id'];
							$condicaoFilho->sistema = $this->sistema;
							$condicaoFilho->op = $row['op'];
							$condicaoFilho->variavel = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$condicaoFilho->valor = $row['valor'];	
						}

						$condicoes[] = $condicaoFilho;
					}
				}
			return $condicoes;
		}
		
		// void - verifica se as condições dos filhos não foram concluídas, e chama verificar()
		public function verificarFilhos(){
			if (count($this->filhos) > 0){
				for ($i = 0; $i < count($this->filhos); ){
					// verifica se o filho foi resolvido
					$this->filhos[$i]->verificarFilhos();
					if ($this->filhos[$i]->resolvido)
					{ array_splice($this->filhos, $i, 1); }
					else $i++;
				}
			}
			
			$this->verificar();
		}
		
		// void - verifica se as próprias condições já não foram concluídas
		public function verificar(){
			global $_SESSION;
			
			if (!isset($_SESSION['s'.$this->sistema]['regras'][$this->id])){
				$this->certeza = 1;
				
				$condicoesCorretas = TRUE;
				$condicoesConcluidas = TRUE;
				if (count($this->condicoes) > 0){
					foreach ($this->condicoes as $condicao){
						$val = $condicao->isTrue();
//						print_r("val " . $val);
						if (empty($val['result'])){
							$condicoesCorretas = FALSE;
							if ($val['result'] === NULL)
							{ $condicoesConcluidas = FALSE; }
						}
						
						$this->certeza *= $val['certeza'];
					}
					unset($condicao);
				}

				if ($condicoesConcluidas){						
					$this->resolvido = TRUE;
					if ($condicoesCorretas)
					{ $this->aplicarConsequencias(); }
				}
			} else {
				$this->resolvido = TRUE;
				$this->filhos = array();
			}
		}
		
		// void - aplica as consequencias
		public function aplicarConsequencias(){
			if (!isset($_SESSION))
			{ session_start(); }

			if(!isset($_SESSION['s'.$this->sistema]['regras'][$this->id])){
				if (count($this->consequencias) > 0){
					foreach ($this->consequencias as $consequencia){
						$_SESSION['s'.$this->sistema]['variaveis'][$consequencia->variavel->id]['valor'] = $consequencia->valor;
						$_SESSION['s'.$this->sistema]['variaveis'][$consequencia->variavel->id]['certeza'] = $consequencia->certeza * $this->certeza;
					}
					unset($consequencia);
				}
				$_SESSION['s'.$this->sistema]['regras'][$this->id] = TRUE;
			}
		}
	}
	
	endif;
?>