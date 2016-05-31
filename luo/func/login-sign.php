<?php
	if (!isset($_POST['username']) || !isset($_POST['password']))
	{ die('error'); }
	
	include('base.php');
	connectDatabase();
	
	$user = cleanString($_POST['username']);
	$pass = $_POST['password'];
	
	$query = $conn->query("SELECT `id`,`password`,`email` FROM `users` WHERE `email`='{$user}'");
	while ($row = $query->fetch_assoc()){
		$dt = date('Y-m-d H:i:s');
		if (crypt($pass, $row['password']) == $row['password']) {
			session_start();
			$_SESSION['luouser'] = $row['email'];
			$_SESSION['luoid'] = $row['id'];
			die('ok');
		} else {
			die('error');
		}
	}
	
	die('error');
?>