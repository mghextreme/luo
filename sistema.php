<?php
	include(__DIR__.'/func/protect.php');
	
	if (!isset($_GET['id'])){
		header('Location: home'); die();
	}
	
	include(__DIR__.'/header.php');
	$user = getUserData();
	
	$stmt = $conn->prepare("SELECT `id`,`nome`,`datetime`,`descricao` FROM `sistema` WHERE `id`=? && `usuario`=?");
	$stmt->bind_param('ii', $_GET['id'], $user['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	$item = NULL;
	if ($query->num_rows > 0){
		$item = $query->fetch_assoc();
		$item['datetime'] = new DateTime($item['datetime']);
	}
?>
		<title>Lúo</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
	</head>
	<body>
		<?php include('top-menu.php'); ?>
		<?php if (!empty($item)) : ?>
		<div id="edit" class="center">
			<div class="header">
				<h1><?=$item['nome'];?></h1>
				<h4><?=$item['datetime']->format('d/m/Y');?></h4>
				<button id="export" onclick="$export()">Exportar</button>
			</div>
		</div>
		<?php endif; ?>
		<?php include('footer.php'); ?>
	</body>
</html>