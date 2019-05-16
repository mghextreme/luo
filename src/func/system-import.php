<?php
	include(__DIR__.'/protect.php');
	include(__DIR__.'/base.php');
	connectDatabase();
	
	if (!isset($_POST['id']) || !isset($_POST['import']) || empty($_POST['import']))
	{ header("HTTP/1.1 403 Forbidden"); exit(0); }
	
	$result = array(
		'error' => TRUE,
		'content' => 'unknown'
	);
	
	$user = getUserData();
	$sistema = NULL;
	
	$stmt = $conn->prepare("SELECT `id`,`nome` FROM `sistema` WHERE `id`=? && `usuario`=?");
	$stmt->bind_param('ii', $_POST['id'], $user['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	if ($query->num_rows == 0){
		$result['content'] = 'notexists';
		die(json_encode($result));
	}
	$sistema = $query->fetch_assoc();
	
	$rs = json_decode($_POST['import'], TRUE, 512, JSON_UNESCAPED_UNICODE);
	
	// Reset data
	
	$stmt = $conn->prepare("DELETE FROM `regra` WHERE `sistema`=?");
	$stmt->bind_param('i', $sistema['id']);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $conn->prepare("DELETE FROM `variavel` WHERE `sistema`=?");
	$stmt->bind_param('i', $sistema['id']);
	$stmt->execute();
	$stmt->close();
	
	$vars = array();
	$opcs = array();
	
	// Nome, Desc
	
	$stmt = $conn->prepare("UPDATE `sistema` SET `nome`=?,`descricao`=? WHERE `id`=?");
	$stmt->bind_param('ssi', $rs['nome'], $rs['descricao'], $sistema['id']);
	$stmt->execute();
	$stmt->close();
	
	// Variaveis
	
	if (isset($rs['variaveis']) && count($rs['variaveis']) > 0){
		$stmt = $conn->prepare("INSERT INTO `variavel`(`sistema`,`nome`,`tipo`,`objetivo`,`questionavel`,`pergunta`,`descricao`) VALUES (?,?,?,?,?,?,?)");
		
		foreach ($rs['variaveis'] as $item){
			$var = array(
				'id' => 0,
				'nome' => $item['nome'],
				'tipo' => strtoupper($item['tipo']),
				'objetivo' => isset($item['objetivo']) ? $item['objetivo'] : 0,
				'questionavel' => isset($item['questionavel']) ? $item['questionavel'] : 0,
				'pergunta' => isset($item['pergunta']) ? $item['pergunta'] : '',
				'descricao' => isset($item['descricao']) ? $item['descricao'] : '',
				'opcoes' => NULL,
				'opcoesTemp' => isset($item['opcoes']) ? $item['opcoes'] : NULL
			);
			
			$stmt->bind_param('issiiss', $sistema['id'], $var['nome'], $var['tipo'], $var['objetivo'], $var['questionavel'], $var['pergunta'], $var['descricao']);
			$stmt->execute();
			
			$var['id'] = $stmt->insert_id;
			
			$vars[$var['nome']] = $var;
		}

		$stmt->close();
		
		// Opções
		
		$stmt = $conn->prepare("INSERT INTO `opcao`(`variavel`,`valor`) VALUES (?,?)");
		
		$varsKeys = array_keys($vars);
		for ($j = 0; $j < count($varsKeys); $j++){
			$i = $varsKeys[$j];
			if ($vars[$i]['tipo'] == 'OPCAO'){
				if (count($vars[$i]['opcoesTemp']) > 0){
					foreach ($vars[$i]['opcoesTemp'] as $opc){
						$stmt->bind_param('is', $vars[$i]['id'], $opc);
						$stmt->execute();
						
						$subOpc = array(
							'id' => $stmt->insert_id,
							'variavel' => $i,
							'valor' => $opc
						);
						
						$vars[$i]['opcoes'][$opc] = $subOpc['id'];
						$opcs[] = $subOpc;
					}
				} else {
					$vars[$i]['opcoes'][1] = 1;
					$vars[$i]['opcoes'][0] = 0;
				}
			}
			else unset($vars[$i]['opcoes']);
			
			unset($vars[$i]['opcoesTemp']);
		}
		unset($varsKeys);

		$stmt->close();
		
	}
	
	// Regras
	
	function addCondicoesFilhas($regra, $pai, $condicoes){
		global $conn, $vars;
		
		if (count($condicoes) > 0){

			$condVal = $conn->prepare("INSERT INTO `condicao`(`regra`,`variavel`,`op`,`valor`,`pai`) VALUES (?,?,?,?,?)");
			$condLog = $conn->prepare("INSERT INTO `condicao`(`regra`,`op`,`pai`) VALUES (?,?,?)");

			foreach ($condicoes as $sub){
				if (!isset($sub['op'])){ $sub['op'] = '='; }
				
				if (in_array($sub['op'], array('&&', '||', '!'))){
					$condLog->bind_param('isi', $regra, $sub['op'], $pai);
					$condLog->execute();
					$paiId = $condLog->insert_id;

					addCondicoesFilhas($regra, $paiId, $sub['sub']);
				}
				else {
					$condVal->bind_param('iissi', $regra, $vars[$sub['variavel']]['id'], $sub['op'], $vars[$sub['variavel']]['opcoes'][$sub['valor']], $pai);
					$condVal->execute();
				}
			}
			
			$condVal->close();
			$condLog->close();
		}
	}
	
	if (isset($rs['regras']) && count($rs['regras']) > 0){
		$contRegra = 0;
		
		$stmt = $conn->prepare("INSERT INTO `regra`(`sistema`,`ordem`,`nome`) VALUES (?,?,?)");
		
		foreach ($rs['regras'] as $reg){
			$contRegra++;
			
			$reg['nome'] = empty($reg['nome']) ? "Regra #{$contRegra}" : $reg['nome'];
			
			$stmt->bind_param('iis', $sistema['id'], $contRegra, $reg['nome']);
			$stmt->execute();
			$reg['id'] = $stmt->insert_id;
			
			if (isset($reg['consequencias']) && count($reg['consequencias']) > 0){
				$conseq = $conn->prepare("INSERT INTO `consequencia`(`regra`,`variavel`,`valor`,`certeza`) VALUES (?,?,?,?)");
				
				foreach ($reg['consequencias'] as $sub){
					$val = $vars[$sub['variavel']]['tipo'] == 'OPCAO' ? $vars[$sub['variavel']]['opcoes'][$sub['valor']] : $sub['valor'];
					$conseq->bind_param('iisd', $reg['id'], $vars[$sub['variavel']]['id'], $val, floatval($sub['certeza']));
					$conseq->execute();
				}
				
				$conseq->close();
			}
			
			if (isset($reg['condicoes']) && count($reg['condicoes']) > 0){
				
				$condVal = $conn->prepare("INSERT INTO `condicao`(`regra`,`variavel`,`op`,`valor`,`pai`) VALUES (?,?,?,?,NULL)");
				$condLog = $conn->prepare("INSERT INTO `condicao`(`regra`,`op`,`pai`) VALUES (?,?,NULL)");
				
				foreach ($reg['condicoes'] as $sub){
					if (!isset($sub['op'])){ $sub['op'] = '='; }
					
					if (in_array($sub['op'], array('&&', '||', '!'))){
						$condLog->bind_param('is', $reg['id'], $sub['op']);
						$condLog->execute();
						$paiId = $condLog->insert_id;
						
						addCondicoesFilhas($reg['id'], $paiId, $sub['sub']);
					}
					else {
						$condVal->bind_param('iiss', $reg['id'], $vars[$sub['variavel']]['id'], $sub['op'], $vars[$sub['variavel']]['opcoes'][$sub['valor']]);
						$condVal->execute();
					}
				}
				
				$condVal->close();
				$condLog->close();
			}
		}

		$stmt->close();
	}
	
	$result['error'] = FALSE;
	$result['content'] = array(
		'id' => $sistema['id'],
		'namelink' => nameLink($sistema['nome'])
	);
	die(json_encode($result));
?>