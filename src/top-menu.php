<div id="top">
	<div class="center">
		<h3>Olá, <?=$user['nome'];?></h3>
		<a href="home">Sistemas</a>
		<a href="novo">Novo</a>
		<button id="signout" onclick="$signOutStart()" class="fa">&#xf08b;</button>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	$signOut = function(){
		$.post('func/login-out.php', function(result){
			if (result == 'ok'){
				window.location = 'login';
			} else {
				console.log(result);
			}
		});
	}
	
	$signOutStart = function(){
		$box.ask('Ter certeza de que deseja sair?', 'Sim', 'Não', $signOut);
	}
</script>