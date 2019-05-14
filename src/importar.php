<?php
	include(__DIR__.'/func/protect.php');
	include(__DIR__.'/header.php');
	
	if (!isset($_GET['id']))
	{ header('Location: home'); die(); }
	
	$user = getUserData();
	$item = NULL;
	
	$stmt = $conn->prepare("SELECT `id`,`nome` FROM `sistema` WHERE `id`=? && `usuario`=?");
	$stmt->bind_param('ii', $_GET['id'], $user['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	if ($query->num_rows > 0)
	{ $item = $query->fetch_assoc(); }
?>
		<title>Importar - Lúo</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
	</head>
	<body>
		<?php include('top-menu.php'); ?>
		<div id="create" class="center">
			<div class="header">
				<h1><?=$item['nome'];?></h1>
				<h2>Importar arquivo</h2>
			</div>
			<form id="importSystem" method="post" action="javascript:$importSystem()">
				<input type="hidden" name="id" value="<?=$item['id'];?>" />
<!--				<label for="import">Arquivo para ser importado</label>-->
<!--				<input type="file" id="file" name="file" accept="text/plain" />-->
				<label for="import">Copie o texto para importar</label>
				<textarea name="import" id="import"></textarea>
				<button type="submit">Importar</button>
			</form>
			<script type="text/javascript">
				$importSystem = function(){
					var form = $('form#importSystem'),
						el, val,
						error = false;
					
					el = form.children('textarea#import');
					val = el.val();
					if (val.length < 5)
					{ error = true; }
					
					if (error){
						$box.alert('Copie o seu sistema no formato JSON para o campo de texto.');
						return;
					}
					
					$.post('func/system-import.php', form.serialize(), function(result){
						try {
							var rs = JSON.parse(result);
							if (rs.error){
								switch (rs.content){
									case 'notexists':
										$box.alert('Este sistema não existe.');
										break;
									default:
										throw rs.content;
										break;
								}
								return;
							}
							
							window.location = 'sistema/' + rs.content.id + '/' + rs.content.namelink;
						}
						catch (err){
							console.log(err.message);
							console.log(result);
						}
					});
				}
			</script>
		</div>
		<?php include('footer.php'); ?>
	</body>
</html>