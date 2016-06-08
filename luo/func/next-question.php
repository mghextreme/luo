<?php
	
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


	if(!isset($_POST['system'])){
		die();
	}
	
	include(__DIR__.'/base.php');
	connectDatabase();

	// pegando o sistema
	$sistema = $_POST['system'];
	
	// array de retorno
	$result = array(
		'error' => TRUE,
		'content' => 'unknown'
	);
	
//	chamar o nodo inicializar ele e fazer a expanção para fazer as perguntas

	if (!isset($_SESSION['s'.$sistema])){
		iniSystem($sistema);
	}
	
	try {
		$arvores = array();
		
		foreach ($_SESSION['s'.$sistema]['arvores'] as $aDescobrir){
			$galho = unserialize($aDescobrir);
			if(!$galho->resolvido){
				$arvores[] = $galho;
			}
		}
		unset($aDescobrir);
		
		$variavel = NULL;
		foreach ($arvores as $item){
			if ($_SESSION['s'.$sistema]['variaveis'][$item->objetivo->id]['valor'] === NULL){
				$item->raiz->seekFilhos($item->objetivo->id);
				$variavel = $item->raiz->filhos[0]->expandir();
				break;
			}
		}
		
		if ($variavel !== NULL){
			$opcoes = getOpcoes($variavel);
			$result['error'] = FALSE;
			$result['content'] = array(
				'resolvido' => FALSE,
				'variavel' => $variavel,
				'opcoes' => $opcoes
			);
		} elseif(count($arvores) == 0){
			$opcoes = getOpcoes($variavel);
			$result['error'] = FALSE;
			$result['content'] = array(
				'resolvido' => TRUE
			);
		} else {
			// não possui uma variavel para questionar
			$result['content'] = 'null';
		}

		
	} catch(Exception $e){
		// deu ruim
		$result['content'] = $e->getMessage();
	}
	

	die(json_encode($result));
?>