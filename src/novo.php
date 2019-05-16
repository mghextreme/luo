<?php
	include(__DIR__.'/func/protect.php');
	include(__DIR__.'/header.php');
	$user = getUserData();
?>
		<title>Lúo</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
	</head>
	<body>
		<?php include('top-menu.php'); ?>
		<div id="create" class="center">
			<div class="header">
				<h1>Novo sistema</h1>
			</div>
			<form id="newSystem" method="post" action="javascript:$newSystem()">
				<label for="name">Nome do sistema</label>
				<input type="text" id="name" name="name" />
				<label for="desc">Descrição</label>
				<textarea id="desc" name="desc"></textarea>
				<button type="submit">Criar</button>
			</form>
			<script type="text/javascript">
				$newSystem = function(){
					var form = $('form#newSystem'),
						el, val,
						error = false;
					
					el = form.children('input#name');
					val = el.val();
					if (val.length < 5)
					{ error = true; }
					
					if (error){
						$box.alert('Digite um nome para o sistema com pelo menos 5 caracteres.');
						return;
					}
					
					$.post('func/system-new.php', form.serialize(), function(result){
						try {
							var rs = JSON.parse(result);
							if (rs.error){
								switch (rs.content){
									case 'already': // Nada para questionar
										$box.alert('Você já cadastrou um sistema com este nome.');
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