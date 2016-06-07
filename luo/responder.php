<?php
	if (!isset($_GET['id']))
	{ die(); }
	$system = $_GET['id'];
	
	include(__DIR__.'/header.php');
?>
		<title>Luo</title>
	</head>
	<body>
		<div id="responder" class="center">
			<div id="top">
				<h1>Nome do sistema</h1>
				<span id="reset" tabindex="0">Reiniciar</span>
			</div>
			<div id="content">
				<!-- Tela de Intro -->
<!--
				<div id="intro">
					<h2>Bem vindo ao Nome do Sistema</h2>
					<p>
						Aqui está um descrição em Latim enquanto o sistema não está pronto:<br/><br/>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eget metus egestas, porttitor odio vitae, pellentesque massa. Aenean a urna mattis tortor vestibulum hendrerit at at erat. In vel leo mollis dui suscipit dignissim.<br/><br/>
						Nunc bibendum, lorem nec feugiat gravida, libero augue dictum arcu, vel tincidunt sem neque eget nibh. Donec mollis nisi at elit malesuada, sed iaculis urna ornare. Fusce quis arcu in felis aliquam iaculis quis a urna. Praesent at augue sem.<br/><br/>
						Suspendisse potenti. Vestibulum sit amet diam aliquam, scelerisque quam quis, auctor leo. Curabitur consequat lorem ullamcorper tortor pharetra egestas.
					</p>
					<div class="info">
						<span class="author"><b>Autor do sistema:</b> Matias G H</span>
						<span class="date"><b>Data de criação:</b> 01/06/2016</span>
					</div>
					<button id="start">Iniciar</button>
				</div>
-->
				<!-- Tela de Pergunta -->
				<form id="question" name="question" method="post">
					<input type="hidden" id="variable" value="13" />
					<h2>Qual a sua operadora de telefonia?</h2>
					<p>A operadora de celular que você utiliza com mais frequência.</p>
					<ul id="field" class="options">
						<li tabindex="0"><input type="radio" name="val" value="1" />Claro</li>
						<li tabindex="0"><input type="radio" name="val" value="2" />Tim</li>
						<li tabindex="0" class="selected"><input type="radio" name="val" value="3" />Vivo</li>
						<li tabindex="0"><input type="radio" name="val" value="4" />Oi</li>
						<li tabindex="0"><input type="radio" name="val" value="5" />Outra</li>
					</ul>
					<div class="bottom">
						<button id="next">Next</button>
					</div>
				</form>
			</div>
			<div id="footer">
				<span class="desc"><strong>Lúo</strong> - Sistemas Especialistas</span>
				<span class="authors">Desenvolvido por Evandro M S e Matias G H</span>
			</div>
		</div>
		<script type="text/javascript" charset="utf-8">
			$next = function(){
				$.post('func/next-question.php', { system: <?=$system;?> }, function(result){
					alert(result);
				});
			}
			
			$(function(){
				$('div#intro > button#start').on('click keydown', function(e){
					if ($clicked(e.which)){ $next(); }
				});
				
				$('div#top > span#reset').on('click keydown', function(e){
					if ($clicked(e.which)){
						alert('REINICIAR PROGRAMA');
					}
				});
			});
		</script>
	</body>
</html>