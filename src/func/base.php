<?php
	if (!isset($_SESSION))
	{ session_start(); }
	
	if (!function_exists('connectDatabase')){
		date_default_timezone_set('America/Sao_Paulo');
		
		include(dirname(__FILE__).'/config.php');
		include(dirname(__FILE__).'/../classes/arvore.class.php');
		
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Basic Functions - - - - - - - -
		
		function connectDatabase(){
			global $db, $conn;
			$conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
			if (mysqli_connect_errno()) { printf("Connect failed: %s\n", mysqli_connect_error()); die(); }
			$conn->set_charset('utf8');
		}
		
		function cleanString($str){ return str_replace(array("'","`"),array("\'",""),$str); }
		
		function nameLink($str, $extra = TRUE){
			$str = strtolower($str);
			$str = str_replace(array('Á','À','Ä','Â','Ã','á','à','ä','â','ã','ª'), 'a', $str);
			$str = str_replace(array('É','È','Ë','Ê','é','è','ë','ê'), 'e', $str);
			$str = str_replace(array('Í','Ì','Ï','Î','í','ì','ï','î'), 'i', $str);
			$str = str_replace(array('Ó','Ò','Ö','Ô','Õ','ó','ò','ö','ô','õ','º'), 'o', $str);
			$str = str_replace(array('Ú','Ù','Ü','Û','ú','ù','ü','û'), 'u', $str);
			$str = str_replace(array('Ç','ç'), 'c', $str);
			if ($extra) {
				$str = str_replace(array(' ','&','?','_','(',')','[',']','{','}','=','+','*',
					'%','$','@','!','#','₢','/',"'",'"','§',':',';',',','.','\\','|','<','>'), '-', $str);
				$str = str_replace('--', '-', $str);
			}
			$str = trim($str, ' -');
			return $str;
		}
		
		function getUserData($basic = FALSE){
			global $conn;
			$result = array('id' => NULL, 'nome' => 'DESCONHECIDO');
			
			if (!isset($_SESSION))
			{ session_start(); }
			
			if (isset($_SESSION['luouser'])){
				$stmt = $conn->prepare("SELECT `id`,`nome`,`login` FROM `usuario` WHERE `login`=? && `id`=?");
				$stmt->bind_param('si', $_SESSION['luouser'], $_SESSION['luoid']);
                $stmt->execute();
                $query = $stmt->get_result();
				if ($query->num_rows > 0)
				{ $result = $query->fetch_assoc(); }
			}
			
			return $result;
		}
        
        function iniSystem($sistema){
			global $conn;
			
            if(!isset($_SESSION)){
                session_start();
            }
			
            if(isset($_SESSION['s'.$sistema])){
                $_SESSION['s'.$sistema] = null;
            }
            
            $_SESSION['s'.$sistema] = array(
                'variaveis' => array(),
                'arvores' => array(),
				'regras' => array()
            );
			
			$query = "SELECT * FROM sistema WHERE id = '{$sistema}';";
			$result = $conn->query($query);
			if($result->num_rows <= 0) { die("Sistema não existe"); }
				
			
			$query = "SELECT * FROM variavel WHERE sistema = '{$sistema}';";
			$result = $conn->query($query);
			if($result->num_rows > 0) {
				// atribui as linhas retornadas
				while($row = $result->fetch_assoc()) {
					$varivavel = new Variavel($row['id']);
					$varivavel->nome = $row['nome'];
					$varivavel->tipo = $row['tipo'];
					$varivavel->questionavel = $row['questionavel'];
					$varivavel->pergunta = $row['pergunta'];
					$varivavel->descricao = $row['descricao'];
					
					// criando um array pra cada variavel
					$_SESSION['s'.$sistema]['variaveis'][$varivavel->id] = array(
						'variavel' => serialize($varivavel),
						'valor' => NULL,
						'certeza' => 1
					);
				}
			}
			
			$query = "SELECT id FROM variavel WHERE sistema = '{$sistema}' AND objetivo = 1;";
			$result = $conn->query($query);
			if($result->num_rows > 0) {
				// atribui as linhas retornadas
				while($row = $result->fetch_assoc()) {					
					$arvore = new Arvore(unserialize($_SESSION['s'.$sistema]['variaveis'][$row['id']]['variavel']));
					$arvore->expandirRaiz();
					$_SESSION['s'.$sistema]['arvores'][$arvore->objetivo->id] = serialize($arvore);
				}
			}
			
        }
	}
?>