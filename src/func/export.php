<?php
	include(__DIR__.'/protect.php');
	include(__DIR__.'/base.php');
	connectDatabase();
	
	if (!isset($_GET['id']) || empty($_GET['id']))
	{ header("HTTP/1.1 403 Forbidden"); exit(0); }
	
	$user = getUserData();
	
	$stmt = $conn->prepare("SELECT * FROM `sistema` WHERE `id`=?");
	$stmt->bind_param('i', $_GET['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	
	if ($query->num_rows == 0)
	{ header("HTTP/1.1 404 Not Found"); exit(0); }
	
	$sistema = $query->fetch_assoc();
	if ($user['id'] != $sistema['usuario'])
	{ header("HTTP/1.1 403 Forbidden"); exit(0); }
	unset($sistema['datetime']);
	
	$varIds = array();
	$optIds = array();
	
	function getCondicoesFilhas($regra, $idPai){
		global $conn, $varIds, $optIds;
		
		$ret = array();
		
		$cond = $conn->prepare("SELECT * FROM `condicao` WHERE `regra`=? && `pai`=?");
		$cond->bind_param('ii', $regra, $idPai);
		$cond->execute();
		
		$query = $cond->get_result();
		while ($row = $query->fetch_assoc()){
			if ($varIds[$subRow['variavel']]['tipo'] == 'OPCAO')
			{ $subRow['valor'] = $optIds[$subRow['valor']]['valor']; }
			$subRow['variavel'] = $varIds[$subRow['variavel']]['nome'];
			
			$row['sub'] = getCondicoesFilhas($regra, $row['id']);
			if (empty($row['sub']))
			{ unset($row['sub']); }
			$ret[] = $row;
		}
		
		$cond->close();
		
		return $ret;
	}
	
	// get variaveis
	
	$sistema['variaveis'] = array();
	
	$stmt = $conn->prepare("SELECT * FROM `variavel` WHERE `sistema`=?");
	$stmt->bind_param('i', $sistema['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	
	$opcoes = $conn->prepare("SELECT * FROM `opcao` WHERE `variavel`=?");
	
	while ($row = $query->fetch_assoc()){
		if ($row['tipo'] == 'OPCAO'){
			$row['opcoes'] = array();
			$opcoes->bind_param('i', $row['id']);
			$opcoes->execute();
			$subQuery = $opcoes->get_result();
			while ($subRow = $subQuery->fetch_assoc()){
				$optIds[$subRow['id']] = $subRow;
				$row['opcoes'][] = $subRow['valor'];
			}
			if (empty($row['opcoes']))
			{ unset($row['opcoes']); }
		}
		
		$varIds[$row['id']] = $row;
		unset($row['id'],$row['sistema']);
		$sistema['variaveis'][] = $row;
	}
	$opcoes->close();
	
	// get regras
	
	$sistema['regras'] = array();
	
	$stmt = $conn->prepare("SELECT * FROM `regra` WHERE `sistema`=? ORDER BY `ordem` ASC");
	$stmt->bind_param('i', $sistema['id']);
	$stmt->execute();
	
	$query = $stmt->get_result();
	$stmt->close();
	
	$condicoes = $conn->prepare("SELECT * FROM `condicao` WHERE `regra`=? && `pai` IS NULL");
	$consequencias = $conn->prepare("SELECT * FROM `consequencia` WHERE `regra`=?");
	
	while ($row = $query->fetch_assoc()){
		$row['condicoes'] = array();
		$row['consequencias'] = array();
		
		// get condicoes
		
		$condicoes->bind_param('i', $row['id']);
		$condicoes->execute();
		$subQuery = $condicoes->get_result();
		while ($subRow = $subQuery->fetch_assoc()){
			if ($varIds[$subRow['variavel']]['tipo'] == 'OPCAO')
			{ $subRow['valor'] = $optIds[$subRow['valor']]['valor']; }
			$subRow['variavel'] = $varIds[$subRow['variavel']]['nome'];
			
			$subRow['sub'] = getCondicoesFilhas($row['id'], $subRow['id']);
			if (empty($subRow['sub']))
			{ unset($subRow['sub']); }
			
			unset($subRow['id'],$subRow['regra'],$subRow['pai']);
			$row['condicoes'][] = $subRow;
		}
		
		// get consequencias
		
		$consequencias->bind_param('i', $row['id']);
		$consequencias->execute();
		$subQuery = $consequencias->get_result();
		while ($subRow = $subQuery->fetch_assoc()){
			if ($varIds[$subRow['variavel']]['tipo'] == 'OPCAO'){
				$subRow['valor'] = $optIds[$subRow['valor']]['valor'];
			}
			$subRow['variavel'] = $varIds[$subRow['variavel']]['nome'];
			
			unset($subRow['id'],$subRow['regra']);
			$row['consequencias'][] = $subRow;
		}
		
		unset($row['id'],$row['ordem'],$row['sistema']);
		$sistema['regras'][] = $row;
	}
	$condicoes->close();
	$consequencias->close();
	
	unset($sistema['id'],$sistema['usuario']);
	
	//Required for IE
	if(ini_get('zlib.output_compression'))
	{ ini_set('zlib.output_compression', 'Off'); }

	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: private', false);
	//header('Content-Type: application/pdf'); // Comment to download
	header('Content-Type: application/force-download');
	header('Content-Disposition: attachment; filename="' . $sistema['nome'] .'.luo.json"');
	header('Content-Transfer-Encoding: text/plain');
	header('Connection: close');
	echo json_encode($sistema, JSON_UNESCAPED_UNICODE);
	exit(0);
?>