<?php
	if (!isset($_GET['id']))
	{ die(); }
	$system = $_GET['id'];
	
	include(__DIR__.'/header.php');
	
	$stmt = $conn->prepare("SELECT `S`.`id`,`S`.`nome`,`S`.`descricao`,`S`.`datetime`,`U`.`nome` AS `autor` FROM `sistema` `S` INNER JOIN `usuario` `U` ON `S`.`usuario`=`U`.`id` WHERE `S`.`id`=?");
	$stmt->bind_param('i', $system);
	$stmt->execute();
	$query = $stmt->get_result();
	
	$sistema = array();
	if ($query->num_rows > 0){
		$sistema = $query->fetch_assoc();
		$sistema['datetime'] = new DateTime($sistema['datetime']);
	} else {
		$sistema = array(
			'id' => 0,
			'nome' => 'Sistema não existente',
			'descricao' => "Este link não é válido.\n\nO sistema pode ter sido removido ou o link está quebrado."
		);
	}
?>
		<title>Luo</title>
	</head>
	<body>
		<div id="responder" class="center">
			<div id="top">
				<h1><?=$sistema['nome'];?></h1>
				<span id="reset" tabindex="0">Reiniciar</span>
			</div>
			<div id="content">
				<!-- Tela de Intro -->
				<div id="intro">
					<h2>Bem vindo</h2>
					<p><?=nl2br($sistema['descricao']);?></p>
					<?php if ($sistema['id'] > 0) : ?>
					<div class="info">
						<span class="author"><b>Autor do sistema:</b> <?=$sistema['autor'];?></span>
						<span class="date"><b>Data de criação:</b> <?=$sistema['datetime']->format('d/m/Y');?></span>
					</div>
					<button id="start">Iniciar<span class="fa">&#xf054;</span></button>
					<?php endif; ?>
				</div>
				<!-- Tela de Pergunta -->
<!--
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
					<div id="field" class="string">
						<input type="text" name="val" value="" />
					</div>
					<div class="bottom">
						<div id="certeza">
							<label for="certeza">Certeza (%)</label>
							<input type="text" name="certeza" value="1.0" />
						</div>
						<button id="next">Avançar<span class="fa">&#xf054;</span></button>
					</div>
				</form>
-->
			</div>
			<div id="footer">
				<span class="desc"><strong>Lúo</strong> - Sistemas Especialistas</span>
				<span class="authors">Desenvolvido por Evandro M S e Matias G H</span>
			</div>
		</div>
		<script type="text/javascript" charset="utf-8">
			$next = function(){
				$.post('func/next-question.php', { system: <?=$system;?> }, function(result){
					
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