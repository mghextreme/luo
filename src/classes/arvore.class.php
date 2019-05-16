<?php
	if (!class_exists('Nodo'))
	{ include(__DIR__.'/nodo.class.php'); }
	
	if (!class_exists('Arvore')) :
	
	class Arvore {
		// Variavel - variável objetivo desta árvore
		public $objetivo;

		// bool - resolvido
		public $resolvido = false;

		// Nodo - nodo raíz da árvore
		public $raiz;

		// float - certeza da informação da variável objetivo
		public $certeza = 1;

		// void - construtor
		function __construct($objetivo){
			$this->objetivo = $objetivo;
		}
		
		public function proximaPergunta(){
			global $sistema;
			
			if (!$this->resolvido){
				// para cada filho
				for ($i = 0; $i < count($this->raiz->filhos); $i++){
					$result = $this->raiz->filhos[$i]->proximaPergunta();
					if (!empty($result))
					{ return $result; }
				}
			}
			return NULL;
		}
		
		public function verificarFilhos(){
			global $sistema;
			
			if (!$this->resolvido){
				if (count($this->raiz->filhos) > 0){
					// para cada filho - enquanto não estiver resolvido
					for ($i = 0; !$this->resolvido && $i < count($this->raiz->filhos);){
						// verifica se o filho foi resolvido
						$this->raiz->filhos[$i]->verificarFilhos();
						if ($this->raiz->filhos[$i]->resolvido){
							// retira o filho
							array_splice($this->raiz->filhos, $i, 1);
						}
						else $i++;
					}
				}
				$this->verificar();
			}
		}
		
		public function verificar(){
			global $sistema;
			
			if ($_SESSION['s'.$sistema]['variaveis'][$this->objetivo->id]['valor'] !== NULL || count($this->raiz->filhos) == 0){
				$this->resolvido = TRUE;
				$this->raiz->filhos = array();
			}
		}
		
		public function expandirRaiz(){
			global $sistema, $conn;
			
			$this->raiz = new Nodo(0);
			$this->raiz->sistema = $sistema;
			
			$query = "SELECT regra FROM consequencia WHERE variavel = {$this->objetivo->id}";
			//$query = "SELECT regra FROM consequencia WHERE variavel NOT IN (-----CONDICOES JA ATENDIDADS-----)";
			$resultRegra = $conn->query($query);
			
			if ($resultRegra->num_rows > 0) {
				// faz a convesão
				while ($rule = $resultRegra->fetch_assoc()) {
					// nodo filho
					$nodoFilho = new Nodo($rule['regra']);
					$nodoFilho->sistema = $sistema;
					
					// pega as consequencias para aquela regra
					$query = "SELECT * FROM consequencia WHERE regra = {$nodoFilho->id};";
					$resulConsequencia = $conn->query($query);
					
					if ($resulConsequencia->num_rows > 0){
						while ($row = $resulConsequencia->fetch_assoc()){
							$consequenciaNodo = new Consequencia();
							$consequenciaNodo->id = $row['id'];
							$consequenciaNodo->variavel = unserialize($_SESSION['s'.$sistema]['variaveis'][$row['variavel']]['variavel']);
							$consequenciaNodo->valor = $row['valor'];
							$consequenciaNodo->certeza = floatval($row['certeza']);
							
							$nodoFilho->consequencias[] = $consequenciaNodo;
						}
					}
					
					// pega as condições para aquela regra
					$query = "SELECT * FROM condicao WHERE regra={$nodoFilho->id} AND pai IS NULL;";
					$resultCondicao = $conn->query($query);
					if ($resultCondicao->num_rows > 0) {
						while ($row = $resultCondicao->fetch_assoc()) {
							if(in_array($row['op'], array("&&", "||", "!"))){
								$condicaoFilho = new CondicaoLogica();
								$condicaoFilho->id = $row['id'];
								$condicaoFilho->sistema = $sistema;
								$condicaoFilho->op = $row['op'];
								$condicaoFilho->condicoes = $nodoFilho->getFilhos($nodoFilho->id, $condicaoFilho->id);
							} else {
								$condicaoFilho = new CondicaoValor();
								$condicaoFilho->id = $row['id'];
								$condicaoFilho->sistema = $sistema;
								$condicaoFilho->op = $row['op'];
								$condicaoFilho->variavel = unserialize($_SESSION['s'.$sistema]['variaveis'][$row['variavel']]['variavel']);
								$condicaoFilho->valor = $row['valor'];	
							}
							 
							$nodoFilho->condicoes[] = $condicaoFilho;
						}
					}
					
					if (count($nodoFilho->condicoes) > 0){
//						print_r($nodoFilho);
						$nodoFilho->verificar();
//						print_r($nodoFilho);
						// condicionar a aceitação do filho, por hora caminho feliz
						$this->raiz->filhos[] = $nodoFilho;
					}
				}
			}
		}
	}
	
	endif;
?>