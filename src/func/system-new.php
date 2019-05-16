<?php
	include(__DIR__.'/protect.php');
	include(__DIR__.'/base.php');
	connectDatabase();
	
	if (!isset($_POST['name']) || empty($_POST['name']))
	{ header("HTTP/1.1 403 Forbidden"); exit(0); }
	
	$result = array(
		'error' => TRUE,
		'content' => 'unknown'
	);
	
	$user = getUserData();
	
	$stmt = $conn->prepare("SELECT `id` FROM `sistema` WHERE `nome`=? && `usuario`=?");
	$stmt->bind_param('si', $_POST['name'], $user['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	if ($query->num_rows > 0){
		$result['content'] = 'already';
		die(json_encode($result));
	}
	
	$time = date('Y-m-d H:i:s');
	
	$stmt = $conn->prepare("INSERT INTO `sistema`(`usuario`,`nome`,`descricao`,`datetime`) VALUES (?,?,?,?)");
	$stmt->bind_param('isss', $user['id'], $_POST['name'], $_POST['desc'], $time);
	$stmt->execute();
	
	$result['content'] = array(
		'id' => $stmt->insert_id,
		'namelink' => nameLink($_POST['name'])
	);
	$result['error'] = FALSE;
	$stmt->close();
	
	die(json_encode($result));
?>