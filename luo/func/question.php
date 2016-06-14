<?php 

	include(__DIR__.'/base.php');
	connectDatabase();

	function getOpcoes($variavel){
		global $conn;
		
		$query = "SELECT * FROM opcao WHERE variavel = {$variavel->id};";
		$result = $conn->query($query);
		
		$opcoes = array();
		
		if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$opcoes[$row['id']] = $row['valor'];
			}
			return $opcoes;
		}
		return NULL;
	}
	
	function getNextQuestion($sistema){
		// array de retorno
		$result = array(
			'error' => TRUE,
			'content' => 'unknown'
		);

		//	chamar o nodo inicializar ele e fazer a expansão para fazer as perguntas
		if (!isset($_SESSION['s'.$sistema]))
		{ iniSystem($sistema); }

		try {
			$arvore = NULL;
			foreach ($_SESSION['s'.$sistema]['arvores'] as $aDescobrir){
				$aDescobrir = unserialize($aDescobrir);
				// ate resolvido mudar, to mudando isso
				if (!$aDescobrir->resolvido){
					$arvore = $aDescobrir;
					break;
				}
//				if ($_SESSION['s'.$sistema]['variaveis'][$aDescobrir->objetivo->id]['valor'] === NULL){
//					$arvore = $aDescobrir;
//					break;
//				}
			}
			unset($aDescobrir);
			
//			return $arvore;

			$variavel = NULL;
			if ($_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['valor'] === NULL){
				$variavel = $arvore->proximaPergunta();
			}
			
			if ($variavel !== NULL){
				$opcoes = getOpcoes($variavel);
				$result['error'] = FALSE;
				$result['content'] = array(
					'resolvido' => FALSE,
					'variavel' => $variavel,
					'opcoes' => $opcoes
				);
			} elseif ($arvore == NULL){
//				$opcoes = getOpcoes($variavel);
				$result['error'] = FALSE;
				$result['content'] = array(
					'resolvido' => TRUE
				);
			} else {
				// não possui uma variavel para questionar
//				$result['content'] = print_r($_SESSION['s'.$sistema]['variaveis']);
//				$result['content'] = print_r($arvore);
				$result['content'] = NULL;
			}
		} catch(Exception $e){
			$result['content'] = $e->getMessage();
		}
		return $result;
	}
?>