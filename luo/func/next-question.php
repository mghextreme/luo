<?php
	
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

	if (!isset($_SESSION[$sistema])){
		iniSystem($sistema);
	}
	
	try {
		$arvores = array();
		
		foreach ($_SESSION[$sistema]['arvores'] as $aDescobrir){
			$arvores[] = unserialize($aDescobrir);
		}
		unset($aDescobrir);
		
		$variavel = NULL;
		foreach ($arvores as $item){
			if ($_SESSION[$sistema]['variaveis'][$item->objetivo->id]['valor'] === NULL){
				$item->raiz->seekFilhos($item->objetivo->id);
				print_r($item->raiz->filhos[0]);
				$variavel = $item->raiz->filhos[0]->expandir();
				break;
			}
		}
		
		if ($variavel !== NULL){
			$result['error'] = FALSE;
			$result['content'] = array(
				'id' => $variavel->id,
				'pergunta' => $variavel->pergunta
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