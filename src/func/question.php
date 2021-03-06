<?php 
	include(__DIR__.'/base.php');
	connectDatabase();

	function getOpcoes($variavel){
		global $conn;
		
		$query = "SELECT * FROM opcao WHERE variavel={$variavel->id};";
		$result = $conn->query($query);
		
		$opcoes = array();
		
		if ($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
				$opcoes[$row['id']] = $row['valor'];
			}
		}
		else {
			$opcoes[0] = 'Não';
			$opcoes[1] = 'Sim';
		}
		
		return $opcoes;
	}
	
	function setVariable($sistema, $var, $value){
		if ($value === NULL)
		{ $value = FALSE; }
		
		$_SESSION['s'.$sistema]['variaveis'][$var]['valor'] = $value;
	}
	
	function checkTrees($sistema){
		foreach ($_SESSION['s'.$sistema]['arvores'] as $key => $aDescobrir){
			$item = unserialize($aDescobrir);
			if ($_SESSION['s'.$sistema]['variaveis'][$key]['valor'] === NULL){
				$item->verificarFilhos();
				$_SESSION['s'.$sistema]['arvores'][$key] = serialize($item);
			}
		}
		unset($aDescobrir);
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
			}
			unset($aDescobrir);

			$variavel = NULL;
			
			if ($arvore !== NULL){
				if ($_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['valor'] === NULL)
				{ $variavel = $arvore->proximaPergunta(); }
				$_SESSION['s'.$sistema]['arvores'][$arvore->objetivo->id] = serialize($arvore);
			}
			
			if ($arvore === NULL || $arvore->resolvido){
				$result['error'] = FALSE;
				$result['content'] = array(
					'resolvido' => TRUE,
					'respostas' => getRespostas($sistema)
				);
			} elseif ($variavel !== NULL){
				$opcoes = getOpcoes($variavel);
				$result['error'] = FALSE;
				$result['content'] = array(
					'resolvido' => FALSE,
					'variavel' => $variavel,
					'opcoes' => $opcoes
				);
			} else {
				$arvore->verificarFilhos();
				if (!$arvore->resolvido){
					$result['content'] = NULL;
				} else {
					$result['error'] = FALSE;
					$result['content'] = array(
						'resolvido' => TRUE,
						'respostas' => getRespostas($sistema)
					);
				}
			}
		} catch(Exception $e){
			$result['content'] = $e->getMessage();
		}
		return $result;
	}
	
	function getRespostas($sistema){
		global $conn;
		
		$answer = array();
		
		foreach ($_SESSION['s'.$sistema]['arvores'] as $arvore){
			$arvore = unserialize($arvore);
			
			$row = array(
				'variavel' => unserialize($_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['variavel']),
				'valor' => $_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['valor'],
				'certeza' => $_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['certeza']
			);
			
			if ($row['valor'] === NULL){
				$row['valor'] = '<small>Deconhecido</small>';
			}
			else if ($row['variavel']->tipo == 'OPCAO'){
				$query = "SELECT `valor` FROM `opcao` WHERE `variavel`='{$row['variavel']->id}' && `id`='{$row['valor']}'";
				$result = $conn->query($query);
				if ($result->num_rows > 0){
					$tmp = $result->fetch_assoc();
					$row['valor'] = $tmp['valor'];
				} else {
					$row['valor'] = $row['valor'] ? 'Sim' : 'Não';
				}
			}
			
			$row['variavel'] = $row['variavel']->nome;
			
			$answer[] = $row;
		}
		
		return $answer;
	}
?>