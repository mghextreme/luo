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
					for ($i = 0; $i < count($this->filhos); $i++){
						// verifica se o filho foi resolvido
						if ($this->filhos[$i]->resolvido){
							// retira o primeiro filho
							array_splice($this->filhos, $i, 1);
							
							$this->verificar();
							if ($this->resolvido)
							{ return NULL; }
							
							$i--;
						}
						else {
							$result = $this->filhos[$i]->proximaPergunta();
							if (!empty($result))
							{ return $result; }
						}
					}
				}
				
				if (count($this->filhos) == 0) {
//					$this->verificar();
					
					// sem filhos
					$tempVar;
					$condicoesTrue = TRUE;
					$condicoesConcluidas = TRUE;
					// para cada condicao
					for ($i = 0; $i < count($this->condicoes); $i++){
						// se a variavel ainda não teve valor definido
						if ($_SESSION['s'.$this->sistema]['variaveis'][$this->condicoes[$i]->variavel->id]['valor'] === NULL){
							$tempVar = unserialize($_SESSION['s'.$this->sistema]['variaveis'][$this->condicoes[$i]->variavel->id]['variavel']);
							if ($tempVar->questionavel)
							{ return $tempVar; }
						} else {
							$val = $this->condicoes[$i]->IsTrue();
							if (empty($val)){
								$condicoesTrue = FALSE;
								if ($val === NULL)
								{ $condicoesConcluidas = FALSE; }
							}
						}
					}
					
					// todas as condicoes foram atendidas
					if ($condicoesConcluidas){						
						$this->resolvido = TRUE;
						if ($condicoesTrue)
						{ $this->aplicarConsequencias(); }
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
			$done = FALSE;
			$i = 0;
			do {
				$any = FALSE;
				if ($i < count($this->filhos)){
					$this->filhos[$i]->verificar();
					if ($this->filhos[$i]->resolvido){
						array_splice($this->filhos, $i, 1);
						$any = TRUE;
					}
					else $i++;
				} else {
					$any = TRUE;
					$done = TRUE;
				}
				
				// só tenta resolver se não tem filhos ou se um filho foi resolvido
				if ($any){
					$condicoesCorretas = TRUE;
					$condicoesConcluidas = TRUE;
					if (count($this->condicoes) > 0){
						foreach ($this->condicoes as $condicao){
							$val = $condicao->isTrue();
							if (empty($val)){
								$condicoesCorretas = FALSE;
								if ($val === NULL)
								{ $condicoesConcluidas = FALSE; }
							}
						}
						unset($condicao);
					}
					
					if ($condicoesConcluidas){						
						$this->resolvido = TRUE;
						if ($condicoesCorretas)
						{ $this->aplicarConsequencias(); }
					}
				}
			} while (!$this->resolvido && $any && !$done);
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