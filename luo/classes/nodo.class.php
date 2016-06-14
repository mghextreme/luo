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
		
		// bool - marca se o nodo já teve os nodos expandidos
		public $expandido = FALSE;
		
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
					for ($i = 0; $i == 0; $i++){
						// verifica se o filho foi resolvido
						if ($this->filhos[$i]->resolvido){
							$this->verificar();
							// retira o primeiro filho
							array_shift($this->filhos);
							$i--;
						} else {
							$result = $this->filhos[$i]->proximaPergunta();
							if (!empty($result))
							{ return $result; }
						}
					}
				}
				else {
					// sem filhos
					$tempVar;
					$condicoesTrue = TRUE;
					// para cada condicao
					for ($i = 0; $i < count($this->condicoes); $i++){
						// se a variavel ainda não teve valor definido
						if ($_SESSION['s'.$this->sistema]['variaveis'][$this->condicoes[$i]->variavel->id]['valor'] === NULL){
							$tempVar = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$this->condicoes[$i]->variavel->id]['variavel']);
							if ($tempVar->questionavel)
							{ return $tempVar; }
						} else {
							if (!$this->condicoes[$i]->IsTrue())
							{ $condicoesTrue = FALSE; }
						}
					}
					
					// todas as condicoes foram atendidas
					if ($condicoesTrue){
						$this->resolvido = TRUE;
						$this->aplicarConsequencias();
					}
					
					return NULL;
				}
			}
			
			return NULL;
		}
		
		// void - gerar filhos
		public function expandir(){
			if ($this->expandido || $this->resolvido || count($this->filhos) > 0)
			{ return; }
			
			global $conn, $arvore, $_SESSION;
			
			$query = "SELECT regra FROM consequencia WHERE variavel IN (SELECT DISTINCT(variavel) FROM condicao WHERE regra = {$this->id})";
			//$query = "SELECT regra FROM consequencia WHERE variavel IN (SELECT DISTINCT(variavel) FROM condicao WHERE regra = {$this->id}) && NOT IN (-----CONDICOES JA ATENDIDADS-----)";
			$resultRegra = $conn->query($query);
			
			if ($resultRegra->num_rows > 0) {
				
				// faz a convesão
				while ($row = $resultRegra->fetch_assoc()) {
					// nodo filho
					$nodoFilho = new Nodo($row['regra']);
					$nodoFilho->sistema = $this->sistema;
					
					// pega as consequencias para aquela regra
					$query = "SELECT * FROM consequencia WHERE regra = {$row['regra']};";
					$resulConsequencia = $conn->query($query);
					
					if ($resulConsequencia->num_rows > 0){
						while ($row = $resulConsequencia->fetch_assoc()){
							$consequenciaNodo = new Consequencia();
							$consequenciaNodo->id = $row['id'];
							$consequenciaNodo->variavel = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$consequenciaNodo->valor = $row['valor'];
							$consequenciaNodo->certeza = $row['certeza'];
							
							$nodoFilho->consequencias[] = $consequenciaNodo;
						}
					}
					
					// pega as condições para aquela regra
					$query = "SELECT * FROM condicao WHERE regra = {$nodoFilho->id};";
					$resultCondicao = $conn->query($query);
					if ($resultCondicao->num_rows > 0) {
						while ($row = $resultCondicao->fetch_assoc()) {
							$condicaoFilho = new CondicaoValor();
							$condicaoFilho->id = $row['id'];
							$condicaoFilho->sistema = $this->sistema;
							$condicaoFilho->op = $row['op'];
							$condicaoFilho->variavel = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$row['variavel']]['variavel']);
							$condicaoFilho->valor = $row['valor'];
							
							$nodoFilho->condicoes[] = $condicaoFilho;
						}
					}
					
					// condicionar a aceitação do filho, por hora caminho feliz
					$this->filhos[] = $nodoFilho;
				}
			}
			
			$this->expandido = TRUE;
		}
		
		// void - verifica se as condições já não foram concluídas
		public function verificar(){
			global $_SESSION;
			
			$any;
			$tmp = " ";
			do {
				print_r(unserialize($_SESSION['s1']['arvores'][1]));
				
				$any = FALSE;
				if (count($this->filhos) > 0){
					do {
						$tmp = $this->filhos[0]->verificar() . " " . $tmp;
						if ($this->filhos[0]->resolvido){
							array_shift($this->filhos);
							$any = TRUE;
						} else { break; }
					} while (!$any);
				} else {
					$any = TRUE;
				}

				// só tenta resolver se não tem filhos ou se o primeiro filho foi resolvido
				if ($any){
					$condicoesCorretas = TRUE;
					$string = "";
					if (count($this->condicoes) > 0){
						foreach ($this->condicoes as $condicao){
							$string .= $condicao->variavel->nome . " " . $condicao->op . " " . $condicao->valor . "=" . $condicao->isTrue() . " " ."\n";
							if (!$condicao->isTrue()){
								$condicoesCorretas = FALSE;
								break;
							}
						}
						unset($condicao);
					}

					if ($condicoesCorretas){
						$this->resolvido = TRUE;
						$this->aplicarConsequencias();
						return "aplicou teoricamente";
					}
					return "nein: {$string}";
				}
			} while (!$this->resolvido && $any);
			
			return $tmp;
		}
		
		// void - aplica as consequencias
		public function aplicarConsequencias(){
			if (!isset($_SESSION))
			{ session_start(); }

			if (count($this->consequencias) > 0){
				foreach ($this->consequencias as $consequencia){
					$_SESSION['s'.$this->sistema]['variaveis'][$consequencia->variavel->id]['valor'] = $consequencia->valor;
				}
				unset($consequencia);
			}
		}
	}
	
	endif;
?>