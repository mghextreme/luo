<?php
	if (!isset($_POST['username']) || !isset($_POST['password']))
	{ die('error'); }
	
	include('base.php');
	connectDatabase();
	
	$stmt = $conn->prepare("SELECT `id`,`login`,`senha` FROM `usuario` WHERE `login`=?");
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	while ($row = $query->fetch_assoc()){
		$dt = date('Y-m-d H:i:s');
		if (password_verify($_POST['password'], $row['senha'])) {
			$_SESSION['luouser'] = $row['login'];
			$_SESSION['luoid'] = $row['id'];
			die('ok');
		} else {
			die('error');
		}
	}
	
	die('error');
?>