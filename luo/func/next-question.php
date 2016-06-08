<?php
	
	include(__DIR__.'/question.php');
	
	if(!isset($_POST['system'])){
		die();
	}

	// pegando o sistema
	$sistema = $_POST['system'];

	$result = next_question($sistema);

	die(json_encode($result));
?>