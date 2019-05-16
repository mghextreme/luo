<?php
	include(__DIR__.'/func/protect.php');
	
	if (!isset($_GET['id']))
	{ header('Location: home'); die(); }
	
	include(__DIR__.'/header.php');
	$user = getUserData();
	$item = NULL;
	
	function getCondicoesFilhas($regra, $idPai){
		global $conn;
		
		$ret = array();
		
		$cond = $conn->prepare("SELECT * FROM `condicao` WHERE `regra`=? && `pai`=?");
		$cond->bind_param('ii', $regra, $idPai);
		$cond->execute();
		
		$query = $cond->get_result();
		while ($row = $query->fetch_assoc()){
			$row['filhos'] = getCondicoesFilhas($regra, $row['id']);
			$ret[] = $row;
		}
		
		$cond->close();
		
		return $ret;
	}
	
	function printCondicao($cond, $regra){
		global $item;
		
		$isLogic = in_array($cond['op'], array('&&', '||', '!'));
?>
<li class="item <?=($isLogic ? 'log' : 'val');?>">
	<input type="hidden" name="condRegra[]" value="<?=$regra;?>" />
	<input type="hidden" name="condId[]" value="<?=$cond['id'];?>" />
	<input type="hidden" name="condPai[]" value="<?=$cond['pai'];?>" />
	<?php if ($isLogic) : ?>
	<div class="cond">
		<select name="condOp[]" class="logicOp">
			<option value="&&"<?=($cond['op'] == '&&' ? ' selected' : '');?>>E</option>
			<option value="||"<?=($cond['op'] == '||' ? ' selected' : '');?>>Ou</option>
			<option value="!"<?=($cond['op'] == '!' ? ' selected' : '');?>>Não</option>
		</select>
	</div>
	<?php if (count($cond['filhos']) > 0) : ?>
	<ul class="filhas">
		<?php foreach ($cond['filhos'] as $sub){
			printCondicao($sub, $regra);
		} ?>
	</ul>
	<?php endif; else : ?>
	<div class="cond">
		<select name="condVar[]" class="valueVar">
			<?php
				foreach ($item['varShow'] as $num) :
					$sub = $item['var'][$num];
			?>
			<option value="<?=$sub['id'];?>"<?=($cond['variavel'] == $num ? ' selected' : '');?>><?=$sub['nome'];?></option>
			<?php endforeach; ?>
		</select>
		<select name="condOp[]" class="valueOp"<?=($item['var'][$cond['variavel']]['tipo'] == 'OPCAO' ? ' disabled="disabled"' : '');?>>
			<option value="<"<?=($cond['op'] == '<' ? ' selected' : '');?>>&lt;</option>
			<option value="<="<?=($cond['op'] == '<=' ? ' selected' : '');?>>&lt;=</option>
			<option value="="<?=($cond['op'] == '=' ? ' selected' : '');?>>=</option>
			<option value=">="<?=($cond['op'] == '>=' ? ' selected' : '');?>>&gt;=</option>
			<option value=">"<?=($cond['op'] == '>' ? ' selected' : '');?>>&gt;</option>
		</select>
		<div class="value">
			<?php switch ($item['var'][$cond['variavel']]['tipo']) : case 'OPCAO': ?>
			<select name="condVal[]">
				<?php
					foreach ($item['var'][$cond['variavel']]['opcShow'] as $num) :
						$opc = $item['var'][$cond['variavel']]['opc'][$num];
				?>
				<option value="<?=$opc['id'];?>"<?=($opc['id'] == $cond['valor'] ? ' selected' : '');?>><?=$opc['valor'];?></option>
				<?php endforeach; ?>
			</select>
			<?php break; case 'NUMERO': ?>
			<input type="text" class="number" name="condVal[]" placeholder="Valor" value="<?=$cond['valor'];?>" />
			<?php break; case 'TEXTO': ?>
			<input type="text" class="text" name="condVal[]" placeholder="Valor" value="<?=$cond['valor'];?>" />
			<?php break; endswitch; ?>
		</div>
		<span class="remove fa">&#xf00d;</span>
	</div>
	<?php endif; ?>
</li>
<?php
	}
	
	$stmt = $conn->prepare("SELECT `id`,`nome`,`datetime`,`descricao` FROM `sistema` WHERE `id`=? && `usuario`=?");
	$stmt->bind_param('ii', $_GET['id'], $user['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	if ($query->num_rows > 0){
		$item = $query->fetch_assoc();
		$item['datetime'] = new DateTime($item['datetime']);
	}
	
	$varTypes = array(
		'OPCAO' => 'Opções',
		'NUMERO' => 'Número',
		'TEXTO' => 'Texto'
	);
	
	$item['var'] = array();
	$item['varShow'] = array();
	
	$stmt = $conn->prepare("SELECT * FROM `variavel` WHERE `sistema`=? ORDER BY `nome` ASC");
	$stmt->bind_param('i', $item['id']);
	$stmt->execute();

	$query = $stmt->get_result();
	while ($row = $query->fetch_assoc()){
		if ($row['tipo'] == 'OPCAO'){
			$row['opc'] = array();
			$row['opcShow'] = array();
		}
		$item['varShow'][] = $row['id'];
		$item['var'][$row['id']] = $row;
	}
	$stmt->close();
	
	if (count($item['var']) > 0){
		$query = $conn->query("SELECT * FROM `opcao` WHERE `variavel` IN (".implode(',', $item['varShow']).")");
		while ($row = $query->fetch_assoc()){
			$item['var'][$row['variavel']]['opcShow'][] = $row['id'];
			$item['var'][$row['variavel']]['opc'][$row['id']] = $row;
		}
		
		// Check if any var has no options
		$keys = array_keys($item['var']);
		for ($i = 0; $i < count($keys); $i++){
			$j = $keys[$i];
			if (count($item['var'][$j]['opc']) == 0){
				$item['var'][$j]['opcShow'][] = 0;
				$item['var'][$j]['opc'][0] = array(
					'id' => 0,
					'valor' => 'Não'
				);
				$item['var'][$j]['opcShow'][] = 1;
				$item['var'][$j]['opc'][1] = array(
					'id' => 1,
					'valor' => 'Sim'
				);
			}
		}
		unset($keys);
	}
?>
		<title><?=$item['nome'];?> - Lúo</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
	</head>
	<body>
		<?php include('top-menu.php'); ?>
		<?php if (!empty($item)) : ?>
		<div id="edit" class="center">
			<div class="header">
				<h1><?=$item['nome'];?></h1>
				<h3><?=$item['datetime']->format('d/m/Y');?></h3>
				<a id="import" href="importar/<?=$item['id'];?>/<?=nameLink($item['nome']);?>">Importar</a>
				<a id="export" href="exportar/<?=$item['id'];?>/<?=nameLink($item['nome']);?>" target="_blank">Exportar</a>
			</div>
			<h4>Variáveis</h4>
			<ul id="editVars">
				<?php
					foreach ($item['varShow'] as $num) :
						$row = $item['var'][$num];
				?>
				<li id="<?=$row['id'];?>"<?=($row['objetivo'] ? ' class="objetivo"' : '');?>>
					<header tabindex="0">
						<h5><?=$row['nome'];?></h5>
						<span class="type"><?=$varTypes[$row['tipo']];?></span>
					</header>
					<form class="details" name="var<?=$row['id'];?>" method="post" action="javascript:alert('atualizar variavel')">
						<input type="hidden" id="varId" name="varId" value="<?=$row['id'];?>" />
						<div class="half main">
							<label for="varName">Nome da Variável</label>
							<input type="text" name="varName" value="<?=$row['nome'];?>" />
							<label for="varType">Tipo</label>
							<select name="varType">
								<?php foreach ($varTypes as $key => $type) : ?>
								<option value="<?=$key;?>"<?=($key == $row['tipo'] ? ' selected' : '');?>><?=$type;?></option>
								<?php endforeach; ?>
							</select>
							<label for="varObjective">Objetivo</label>
							<select name="varObjective">
								<option value="0"<?=($row['objetivo'] ? '' : ' selected');?>>Não</option>
								<option value="1"<?=($row['objetivo'] ? ' selected' : '');?>>Sim</option>
							</select>
							<label for="varQuestionable">Questionável</label>
							<select name="varQuestionable">
								<option value="0"<?=($row['questionavel'] ? '' : ' selected');?>>Não</option>
								<option value="1"<?=($row['questionavel'] ? ' selected' : '');?>>Sim</option>
							</select>
							<label for="varQuestion">Pergunta</label>
							<input type="text" name="varQuestion" value="<?=$row['pergunta'];?>" />
							<label for="varDesc">Descrição</label>
							<input type="text" name="varDesc" value="<?=$row['descricao'];?>" />
							<button type="submit">Salvar</button>
						</div>
						<ul class="half options">
							<label for="varText">Opções</label>
							<?php
								foreach ($row['opcShow'] as $num) :
									$opc = $row['opc'][$num];
							?>
							<li>
								<input type="hidden" name="opcId[]" value="<?=$opc['id'];?>" />
								<input type="text" name="opcText[]" value="<?=$opc['valor'];?>" />
								<span class="remove fa" tabindex="0">&#xf00d;</span>
							</li>
							<?php endforeach; ?>
							<button class="add" type="button">Adicionar</button>
						</ul>
					</form>
				</li>
				<?php endforeach; ?>
				<script type="text/javascript" charset="utf-8">
					$varInit = function(vr){
						vr = $(vr);
						
						vr.children('header').on('click keydown', function(e){
							if ($clicked(e.which)){
								$(this).parent().toggleClass('open');
							}
						});
						
						var type = vr.find('> form.details > div.main > select[name=varType]').val();
						switch (type){
							case 'OPCAO':
								$varInitOptions(vr.find('> form.details > ul.options'));
								break;
						}
					}
					
					$varInitOptions = function(ul){
						ul = $(ul);
						
						ul.find('> li > span.remove').on('click keydown', function(e){
							if ($clicked(e.which))
							{ $varRemoveOption($(this).parent()); }
						});
						
						ul.children('button.add').on('click keydown', function(e){
							if ($clicked(e.which)){
								$varAddOption($(this).parent());
							}
						});
					}
					
					$varRemoveOption = function(li){
						alert('Remover Opção');
					}
					
					$varAddOption = function(ul){
						alert('Adicionar Opção');
					}
					
					$(function(){
						$('ul#editVars').children('li').each(function(ix, el){ $varInit(el); });
					});
				</script>
			</ul>
			<h4>Regras</h4>
			<ul id="editRegras">
				<?php
					$stmt = $conn->prepare("SELECT * FROM `regra` WHERE `sistema`=? ORDER BY `ordem` ASC");
					$stmt->bind_param('i', $item['id']);
					$stmt->execute();
					$query = $stmt->get_result();
					$stmt->close();
					
					$condicoes = $conn->prepare("SELECT * FROM `condicao` WHERE `regra`=? && `pai` IS NULL");
					$consequencias = $conn->prepare("SELECT * FROM `consequencia` WHERE `regra`=?");
					
					while ($row = $query->fetch_assoc()) :
						$row['condicoes'] = array();
						$row['consequencias'] = array();
						
						// get condicoes
						
						$condicoes->bind_param('i', $row['id']);
						$condicoes->execute();
						$subQuery = $condicoes->get_result();
						while ($subRow = $subQuery->fetch_assoc()){
							$subRow['filhos'] = getCondicoesFilhas($row['id'], $subRow['id']);
							$row['condicoes'][] = $subRow;
						}
						
						// get consequencias
						
						$consequencias->bind_param('i', $row['id']);
						$consequencias->execute();
						$subQuery = $consequencias->get_result();
						while ($subRow = $subQuery->fetch_assoc())
						{ $row['consequencias'][] = $subRow; }
				?>
				<li id="<?=$row['id'];?>">
					<div class="handle fa">&#xf142;</div>
					<header tabindex="0">
						<h5><?=$row['nome'];?></h5>
					</header>
					<form class="details" name="var<?=$row['id'];?>" method="post" action="javascript:alert('atualizar regra')">
						<label for="varName">Nome da Regra</label>
						<input type="text" name="varName" value="<?=$row['nome'];?>" />
						<input type="hidden" id="regraId" name="regraId" value="<?=$row['id'];?>" />
						<label>Se</label>
						<ul class="condic">
							<?php if (count($row['condicoes']) > 0) { foreach ($row['condicoes'] as $cond){
								printCondicao($cond, $row['id']);
							} } ?>
						</ul>
						<label>Então</label>
						<ul class="conseq">
							<?php if (count($row['consequencias']) > 0) : foreach ($row['consequencias'] as $conseq) : ?>
							<li>
								<input type="hidden" name="conseqRegra[]" value="<?=$row['id'];?>" />
								<input type="hidden" name="conseqId[]" value="<?=$row['id'];?>" />
								<select name="conseqVar[]">
									<?php
										foreach ($item['varShow'] as $num) :
											$sub = $item['var'][$num];
									?>
									<option value="<?=$sub['id'];?>"<?=($conseq['variavel'] == $num ? ' selected' : '');?>><?=$sub['nome'];?></option>
									<?php endforeach; ?>
								</select>
								<span class="eq">=</span>
								<div class="value">
									<?php switch ($item['var'][$conseq['variavel']]['tipo']) : case 'OPCAO': ?>
									<select name="conseqVal[]">
										<?php
											if (count($item['var'][$conseq['variavel']]['opc']) > 0) :
												foreach ($item['var'][$conseq['variavel']]['opcShow'] as $num) :
													$opc = $item['var'][$conseq['variavel']]['opc'][$num];
										?>
										<option value="<?=$opc['id'];?>"<?=($opc['id'] == $conseq['valor'] ? ' selected' : '');?>><?=$opc['valor'];?></option>
										<?php endforeach; endif; ?>
									</select>
									<?php break; case 'NUMERO': ?>
									<input type="text" class="number" name="conseqVal[]" placeholder="Valor" value="<?=$conseq['valor'];?>" />
									<?php break; case 'TEXTO': ?>
									<input type="text" class="text" name="conseqVal[]" placeholder="Valor" value="<?=$conseq['valor'];?>" />
									<?php break; endswitch; ?>
								</div>
								<input type="text" name="conseqCnf[]" class="cnf" placehoder="1.0" value="<?=$conseq['certeza'];?>" />
								<span class="remove fa">&#xf00d;</span>
							</li>
							<?php endforeach; endif; ?>
						</ul>
						<button type="submit">Salvar</button>
					</form>
				</li>
				<?php
					endwhile;
					$condicoes->close();
					$consequencias->close();
				?>
				<script type="text/javascript" charset="utf-8">
					$ruleInit = function(vr){
						vr = $(vr);
						
						vr.children('header').on('click keydown', function(e){
							if ($clicked(e.which)){
								$(this).parent().toggleClass('open');
							}
						});
					}
					
					$(function(){
						$('ul#editRegras').children('li').each(function(ix, el){ $ruleInit(el); });
					});
				</script>
			</ul>
		</div>
		<?php endif; ?>
		<?php include('footer.php'); ?>
	</body>
</html>