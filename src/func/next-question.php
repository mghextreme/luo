<?php
	if (!isset($_POST['system'])){ die(); }

	if (!isset($_SESSION)){ session_start(); }
	
	include(__DIR__.'/question.php');

	// pegando o sistema
	$sistema = $_POST['system'];
	global $conn;
	
	if (isset($_POST['variable'])){
		$val = isset($_POST['val']) ? $_POST['val'] : NULL;
		setVariable($sistema, $_POST['variable'], $val);
		checkTrees($sistema);
	}

	$result = getNextQuestion($sistema);
	
//	print_r($_SESSION['s1']['variaveis']);
//	print_r(unserialize($_SESSION['s1']['arvores'][1]));
	
	die(json_encode($result));
?>