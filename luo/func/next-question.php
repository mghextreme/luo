<?php
	if (!isset($_POST['system'])){ die(); }

	if (!isset($_SESSION)){ session_start(); }
	
	include(__DIR__.'/question.php');

	// pegando o sistema
	$sistema = $_POST['system'];
	$ret = "";
	global $conn;

	if (isset($_POST['variable']) && isset($_POST['val'])){
		$variable = $_POST['variable'];
		$val = $_POST['val'];
		
		$_SESSION['s'.$sistema]['variaveis'][$variable]['valor'] = $val;
		
		print_r($_SESSION['s'.$sistema]['variaveis']);
		
		$arvores = array();

		foreach ($_SESSION['s'.$sistema]['arvores'] as $aDescobrir){
			$arvore = unserialize($aDescobrir);
			if($_SESSION['s'.$sistema]['variaveis'][$arvore->objetivo->id]['valor'] === NULL){
				$arvores[] = $arvore;
			}
		}
		unset($aDescobrir);

		
		
		foreach ($arvores as $item){
			if ($_SESSION['s'.$sistema]['variaveis'][$item->objetivo->id]['valor'] === NULL){
				$ret = $item->raiz->verificar();
				$item->verificar();
			}
		}
		unset($item);
		
		foreach ($arvores as $item){
			$_SESSION['s'.$sistema]['arvores'][$item->objetivo->id] = serialize($item);
		}
		unset($item);
	}

	$result = getNextQuestion($sistema);
//	print_r($result);
	if (isset($_POST['variable']) && isset($_POST['val'])){
		$result['variavel'] = $_POST['variable'];
		$result['val'] = $_POST['val'];
		$result['ret'] = $ret;
	}
	die(json_encode($result));
?>