<?php
	if (!class_exists('Condicao'))
	{ include(__DIR__.'/condicao.class.php'); }
	
	class Nodo {
		// int - ID da regra a que o nodo se refere
		public $id;
		
		// int - ID do sistema ao que este nodo está relacionado;
		/*Isso tem que ser discutido*/
		public $sistema;
		
		// Nodo[] - lista de Nodos filhos
		public $filhos = array();
		
		// Condicao[] - lista de Condicao para que as consequências sejam verdadeiras
		public $codicoes = array();
		
		// Consequencia[] - lista de Condicao para que as consequências sejam verdadeiras
		public $consequencias = array();
		
		// void - construtor
		public __construct(){
			
		}
		
		// Variavel - gerar filhos
		public expandir(){
			// ids para perguntar
			$aumento = count($this->filhos);
			
			// para cada condição de condições
			foreach($codicoes as $condicao){
				// verifica se a variavel tem valor ou não
				if(!isset($_SESSION[$sistema]['variaveis'][$condicao->variavel->id]['valor'])){
					$this->seekFilhos($condicao->variavel->id);

					if(count($this->filhos) == $aumento){
						$variavel = unserialize($_SESSION[$sistema]['variaveis'][$condicao->variavel->id]['variavel']);

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
		
		// metodo pra procurar e gerar nodos filhos
		public seekFilhos($id){
			global $conn;
			
			$query = "SELECT consequencia.regra FROM consequencia WHERE consequencia.variavel = {$id};";
			$resultRegra = $conn->query($query);
			
			if($resultRegra->num_rows > 0) {
				// faz a convesão
				while($row = $resultRegra->fetch_assoc()) {
					// nodo filho
					$nodoFilho = new Nodo();
					$nodoFilho->sistema = $this->sistema;
					
					// pegas as consequencias para aquela regra
					$query = "SELECT consequencia.* FROM consequencia WHERE consequencia.regra = {$row['regra']};";
					$resulConsequencia = $conn->query($query);
					
					if($resulConsequencia->num_rows > 0){
						while($row = $resulConsequencia->fetch_assoc()){
							$consequenciaNodo = new Consequencia();
							$consequenciaNodo->id = $row['cons'];
							$consequenciaNodo->variavel = unserialize($_SESSION[$sistema]['variaveis'][$row['variavel']]['variavel']);
							$consequenciaNodo->valor = $row['valor'];
							$consequenciaNodo->certeza = $row['certeza'];
							
							$nodoFilho->consequencia[] = $consequenciaNodo;
						}
					}
					
					// pegas as condições para aquela regra
					$query = "SELECT condicao.* FROM condicao WHERE condicao.regra = {$row['regra']};";
					$resultCondicao = $conn->query($query);
					if($resultRegra->num_rows > 0) {
						while($row = $resultCondicao->fetch_assoc()) {
							$condicaoFilho = new CondicaoValor();
							$condicaoFilho->id = $row['id'];
							$condicaoFilho->sistema = $this->sistema;
							$condicaoFilho->op = $row['op'];
							$condicaoFilho->variavel = unserialize($_SESSION[$sistema]['variaveis'][$row['id']]['variavel']);
							$condicaoFilho->valor = $row['valor'];
							
							$nodoFilho->condicao[] = $condicaoFilho;
						}
					}
					
					// condicionar a aceitação do filho, por hora caminho feliz
					$this->filhos[] = $nodoFilho;
				}
			}
			
		}
	}
?>