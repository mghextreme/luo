<?php
	
	include(__DIR__.'/question.php');
	
	if(!isset($_POST['system'])){
		die();
	}

	if(!isset($_SESSION)){
    	session_start();
    }

	if(isset($_POST['variable']) && isset($_POST['val'])){
		$variable = $_POST['variable'];
		$val = $_POST['val'];
		
		$_SESSION['s'.$sistema]['variaveis'][$variable]['valor'] = $val;
		
//		ver se pode, ou destroi, 
		
		$arvores = array();

		foreach ($_SESSION['s'.$sistema]['arvores'] as $aDescobrir){
			$arvore = unserialize($aDescobrir);
			if(!$arvore->resolvido){
				$arvores[] = $arvore;
			}
		}
		unset($aDescobrir);

		foreach ($arvores as $item){
			if ($_SESSION['s'.$sistema]['variaveis'][$item->objetivo->id]['valor'] === NULL){
				$item->raiz->seekFilhos($item->objetivo->id);
				$item->raiz->inferir();
			}
		}
		unset($item);
		
		foreach ($arvores as $item){
			if ($_SESSION['s'.$sistema]['variaveis'][$item->objetivo->id]['valor'] !== NULL){
				$item->$resolvido = TRUE;
				$_SESSION['s'.$sistema]['arvores'][] = serialize($item);
				break;
			}
		}
		unset($item);
	} 
	// pegando o sistema
	$sistema = $_POST['system'];

	$result = next_question($sistema);

	die(json_encode($result));	
?>