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

	$nodo = array();
	
	if (!isset($_SESSION[$sistema])){
		iniSystem($sistema);
	}
	
	try{
		foreach($_SESSION[$sistema]['arvores'] as $aDescobrir){
			if($_SESSION[$sistema]['variaveis'][$aDescobrir->objetivo->id]['valor'] === NULL){
				$nodoObjetivo = new Nodo();
				$nodoObjetivo->sistema =$sistema;
				$nodoObjetivo->seekFilhos($aDescobrir);
				$nodo[] = $nodoObjetivo;
			}
		}
		unset($aDescobrir);
		
		// sempre vai expandir filho dos objetivos
		// pois se o objetivo já tiver sido encontrado, então ele vai pra outro
		$variavel = $nodo[0]->filhos[0]->expandir();
		
		if($variavel !== NULL){
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