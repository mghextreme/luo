<?php
	if (!class_exists('Condicao'))
	{ include(__DIR__.'/condicao.class.php'); }
	
	if (!class_exists('Nodo')) :
	
	class Nodo {
		// int - ID da regra a que o nodo se refere
		public $id;
		
		// int - ID do sistema ao que este nodo está relacionado;
		/*Isso tem que ser discutido*/
		public $sistema;
		
		// Nodo[] - lista de Nodos filhos
		public $filhos = array();
		
		// Condicao[] - lista de Condicao para que as consequências sejam verdadeiras
		public $condicoes = array();
		
		// Consequencia[] - lista de Condicao para que as consequências sejam verdadeiras
		public $consequencias = array();
		
		// void - construtor
		function __construct($id){ $this->id = $id; }
		
		// Variavel - gerar filhos
		public function expandir(){
			// ids para perguntar
			$aumento = count($this->filhos);

			if(count($this->condicoes) > 0){
				// para cada condição de condições
				foreach($this->condicoes as $condicao){
					// verifica se a variavel tem valor ou não
					if($_SESSION[$this->sistema]['variaveis'][$condicao->variavel->id]['valor'] === NULL){
						$this->seekFilhos($condicao->variavel->id);

						if(count($this->filhos) == $aumento){
							$variavel = unserialize($_SESSION[$this->sistema]['variaveis'][$condicao->variavel->id]['variavel']);

							if($variavel->questionavel){
								// retorna uma variavel que deve ser questionada
								return $variavel;
							} else {
								return NULL;
							}
						} else {
							// fazer a decida, para achar uma variavel a questionar
							return $this->filhos[count($this->filhos) - 1]->expandir();
						}

						$aumento = count($this->filhos);
					}
				}
				unset($condicao);
			} 
			return NULL;
		}
		
		// metodo pra procurar e gerar nodos filhos
		public function seekFilhos($id){
			global $conn;
			
			$query = "SELECT consequencia.regra FROM consequencia WHERE consequencia.variavel = {$id};";
			$resultRegra = $conn->query($query);
			
			if($resultRegra->num_rows > 0) {
				// faz a convesão
				while($row = $resultRegra->fetch_assoc()) {
					// nodo filho
					$nodoFilho = new Nodo($row['regra']);
					$nodoFilho->sistema = $this->sistema;
					
					// pegas as consequencias para aquela regra
					$query = "SELECT consequencia.* FROM consequencia WHERE consequencia.regra = {$row['regra']};";
					$resulConsequencia = $conn->query($query);
					
					if($resulConsequencia->num_rows > 0){
						while($row = $resulConsequencia->fetch_assoc()){
							$consequenciaNodo = new Consequencia();
							$consequenciaNodo->id = $row['id'];
							$consequenciaNodo->variavel = unserialize($_SESSION[$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$consequenciaNodo->valor = $row['valor'];
							$consequenciaNodo->certeza = $row['certeza'];
							
							$nodoFilho->consequencias[] = $consequenciaNodo;
						}
					}
					
					// pegas as condições para aquela regra
					$query = "SELECT condicao.* FROM condicao WHERE condicao.regra = {$nodoFilho->id};";
					$resultCondicao = $conn->query($query);
					if($resultCondicao->num_rows > 0) {
						while($row = $resultCondicao->fetch_assoc()) {
							$condicaoFilho = new CondicaoValor();
							$condicaoFilho->id = $row['id'];
							$condicaoFilho->sistema = $this->sistema;
							$condicaoFilho->op = $row['op'];
							$condicaoFilho->variavel = unserialize($_SESSION[$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$condicaoFilho->valor = $row['valor'];
							
							$nodoFilho->condicoes[] = $condicaoFilho;
						}
					}
					
					// condicionar a aceitação do filho, por hora caminho feliz
					$this->filhos[] = $nodoFilho;
				}
			}
			
		}
	}
	
	endif;
?>