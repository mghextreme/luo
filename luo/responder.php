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
	
	$content = NULL;
	$solved = FALSE;
	if (isset($_SESSION['s'.$system])){
		$solved = TRUE;
		foreach ($_SESSION['s'.$system]['arvores'] as $arv){
			$arv = unserialize($arv);
			if (!$arv->resolvido){
				$solved = FALSE;
				break;
			}
		}
		
		if ($solved){
			// Carregar respostas
		} else {
			// Carregar pergunta
		} 
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
				<?php if (empty($content)) : ?>
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
				<?php else : ?>
				<!-- Tela de Pergunta -->
				<form id="question" name="question" method="post" action="javascript:$next()">
					<input type="hidden" id="variable" name="variable" value="<?=$pergunta['id'];?>" />
					<?php if (empty($pergunta['pergunta'])) : ?>
					<h2>Qual o valor de <?=$pergunta['nome'];?>?</h2>
					<?php else : ?>
					<h2><?=$pergunta['pergunta'];?></h2>
					<?php if (!empty($pergunta['descricao'])) : ?>
					<p><?=$pergunta['descricao'];?></p>
					<?php endif; endif; switch (strtoupper($pergunta['tipo'])) : case 'OPCAO': ?>
					<ul id="field" class="options">
						<li tabindex="0"><input type="radio" name="val" value="1" />Opção</li>
					</ul>
					<?php break; case 'TEXTO': ?>
					<div id="field" class="string">
						<input type="text" name="val" value="" />
					</div>
					<?php break; case 'NUMERO': ?>
					<div id="field" class="number">
						<input type="text" name="val" value="" />
					</div>
					<?php break; endswitch; ?>
					<div class="bottom">
						<div id="certeza">
							<label for="certeza">Certeza (%)</label>
							<input type="text" name="certeza" value="1.0" />
						</div>
						<button id="submit">Avançar<span class="fa">&#xf054;</span></button>
					</div>
				</form>
				<?php endif; ?>
			</div>
		</div>
		<?php include('footer.php'); ?>
		<script type="text/javascript" charset="utf-8">
			$next = function(skip){
				var vars = { system: <?=$system;?> },
					form = $('div#content').children('form#question');
				
				if (form.size() > 0){
					var field = form.children('#field'),
						regex, val;
					
					if (field.hasClass('options')){
						val = field.find(':checked');
						if (val.size() == 0){
							if (skip === undefined || !skip){
								$box.ask('Nenhuma opção está selecionada.<br/>Você tem certeza de que deseja continuar?', 'Sim', 'Não', function(){ $next(true); });
								return;
							}
						}
						val = val.val();
					}
					else if (field.hasClass('string')){
						val = field.children('input').val();
						if (val.length < 1){
							if (skip === undefined || !skip){
								$box.ask('O campo de texto está vazio.<br/>Você tem certeza de que deseja continuar?', 'Sim', 'Não', function(){ $next(true); });
								return;
							}
						}
					}
					else if (field.hasClass('number')){
						val = field.children('input').val();
						if (val.length < 1){
							if (skip === undefined || !skip){
								$box.ask('O campo de texto está vazio.<br/>Você tem certeza de que deseja continuar?', 'Sim', 'Não', function(){ $next(true); });
								return;
							}
						}
						else {
							try {
								val = parseInt(val);
							}
							catch (err){
								$box.alert('O valor digitado não é um número.', 'Alterar');
								return;
							}
						}
					}
					
					vars.variable = form.children('input#variable').val();
					vars.val = val;
				}
				
				$.post('func/next-question.php', vars, function(result){
					try {
						var rs = JSON.parse(result);
						if (rs.error){
							switch (rs.content){
								case 'null': // Nada para questionar
									$box.alert('Nenhuma pergunta a ser questionada.');
									break;
								default:
									throw rs.content;
									break;
							}
							return;
						}
						
						if (!rs.content.resolvido){
							$setQuestion(rs.content);
							return;
						}
						
						$setAnswers(rs.content.respostas);
					}
					catch (err){
						console.log(err.message);
						console.log(result);
					}
				});
			}

			$setQuestion = function(cont){
				var content = $('div#content');
				
				if (content.children('form#question').size() == 0){
					var act = content.children('div#intro'),
						form;
					
					form = $('<form>').attr({ id: 'question', name: 'question', method: 'post', action: 'javascript:$next()' }).css({ display: 'none' });
					form.append($('<input>').attr({ id: 'variable', name: 'variable', type: 'hidden' }).val(cont.variavel.id));
					form.append($('<h2>').text(cont.variavel.pergunta != null ? cont.variavel.pergunta : 'Qual o valor de ' + cont.variavel.nome + '?'));
					if (cont.variavel.descricao != null)
					{ form.append($('<p>').text(cont.variavel.descricao)); }
					
					var field;
					switch (cont.variavel.tipo){
						case 'OPCAO':
							field = $('<ul>').attr({ id: 'field', class: 'options' });
							for (var i in cont.opcoes){
								field.append(
									$('<li>').attr({ tabindex: 0 })
									.append($('<input>').attr({ name: 'val', type: 'radio', value: i }))
									.append(cont.opcoes[i])
								);
							}
							$initOptions(field);
							break;
						case 'TEXTO':
							field = $('<div>').attr({ id: 'field', class: 'string' });
							field.append($('<input>').attr({ type: 'text', name: 'val' }));
							break;
						default:
							field = $('<p>').text('Desconhecido.');
							break;
					}
					form.append(field);
					
					var bottom = $('<div>').addClass('bottom');
					
					bottom.append(
						$('<div>').attr({ id: 'certeza' })
						.append($('<label>').attr({ for: 'certeza' }).text('Certeza (%)'))
						.append($('<input>').attr({ type: 'text', name: 'certeza', value: '1.0' }))
					);
					bottom.append(
						$('<button>').attr({ id: 'next', type: 'submit' })
						.append('Avançar')
						.append($('<span>').addClass('fa').html('&#xf054;'))
					);
					
					form.append(bottom);
					
					content.append(form);
					
					act.stop().slideUp({
						duration: 250,
						easing: 'easeInCubic',
						complete: function(){
							$(this).remove();
							form.stop().slideDown({
								duration: 250,
								easing: 'easeOutCubic'
							});
						}
					});
				}
				else {
					var form = content.children('form#question');
					form.stop().slideUp({
						duration: 250,
						easing: 'easeInCubic',
						complete: function(){
							var input = form.children('input#variable'),
								h2 = form.children('h2'),
								p = form.children('p'),
								field = form.children('#field'),
								footer = form.children('div.bottom');
							
							input.val(cont.id);
							h2.text(cont.variavel.pergunta != null ? cont.variavel.pergunta : 'Qual o valor de ' + cont.variavel.nome + '?');
							
							if (cont.variavel.descricao != null){
								if (p.size() == 0){
									p = $('<p>');
									h2.after(p);
								}
								p.text(cont.variavel.descricao);
							} else if (p.size() > 0) {
								p.remove();
							}
							
							field.remove();
							switch (cont.variavel.tipo){
								case 'OPCAO':
									field = $('<ul>').attr({ id: 'field', class: 'options' });
									for (var i in cont.opcoes){
										field.append(
											$('<li>').attr({ tabindex: 0 })
											.append($('<input>').attr({ name: 'val', type: 'radio', value: i }))
											.append(cont.opcoes[i])
										);
									}
									$initOptions(field);
									break;
								case 'TEXTO':
									field = $('<div>').attr({ id: 'field', class: 'string' });
									field.append($('<input>').attr({ type: 'text', name: 'val' }));
									break;
								default:
									field = $('<p>').text('Desconhecido.');
									break;
							}
							footer.before(field);
							
							footer.find('> div#certeza > input').val('1.0');
							
							form.stop().slideDown({
								duration: 250,
								easing: 'easeOutCubic'
							});
						}
					});
				}
			}
			
			$setAnswers = function(cont){
				var content = $('div#content'),
					form = content.children('form#question'),
					resp;
				
				resp = $('<div>').attr({ id: 'final' }).css({ display: 'none' });
				
				resp.append($('<h2>').text('Solução atingida'));
				
				var tbody = $('<tbody>');
				
				resp.append(
					$('<table>').append(
						$('<thead>').append(
							$('<tr>')
							.append($('<th>').attr({ id: 'var' }).text('Variável'))
							.append($('<th>').attr({ id: 'val' }).text('Valor'))
							.append($('<th>').attr({ id: 'cnf' }).text('Certeza'))
						)
					)
					.append(tbody)
				);
				
				for (var i = 0; i < cont.length; i++){
					tbody.append(
						$('<tr>')
						.append($('<td>').html(cont[i].variavel))
						.append($('<td>').html(cont[i].valor))
						.append($('<td>').html(cont[i].certeza))
					);
				}
				
				resp.append($('<p>').text('Conclusão com sucesso!'));
				
				resp.append($('<button>').attr({ id: 'restart', onclick: '$reset()' }).text('Reiniciar'));
				
				content.append(resp);
				
				form.stop().slideUp({
					duration: 250,
					easing: 'easeInCubic',
					complete: function(){
						$(this).remove();
						resp.stop().slideDown({
							duration: 250,
							easing: 'easeOutCubic'
						});
					}
				});
			}
			
			$initOptions = function(field){
				field = $(field);
				field.children('li').on('click keydown', function(e){
					if ($clicked(e.which)){
						$(this).parent().children('li').removeClass('sel');
						$(this).addClass('sel');
						$(this).children('input').prop('checked', true);
					}
				});
			}
			
			$reset = function(){
				$.post('func/reset.php', { system: <?=$system;?> }, function(result){
					try {
						var rs = JSON.parse(result);
						if (!rs.error)
						{ location.reload(); }
					}
					catch (err){
						console.log(err.message);
						console.log(result);
					}
				});
			}
			
			$(function(){
				$('div#intro > button#start').on('click keydown', function(e){
					if ($clicked(e.which)){ $next(); }
				});
				
				$('div#top > span#reset').on('click keydown', function(e){
					if ($clicked(e.which)){ $reset(); }
				});
			});
		</script>
	</body>
</html>