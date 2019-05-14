<?php
	if (!isset($_POST['system'])){ die(); }
	if (!isset($_SESSION)){ session_start(); }
	$sistema = $_POST['system'];
	unset($_SESSION['s'.$sistema]);
	
	$result = array(
		'error' => FALSE,
		'content' => 'ok'
	);
	die(json_encode($result));
?>