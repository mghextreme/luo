<?php
	if (!isset($_SESSION))
	{ session_start(); }
	
	if (!function_exists('connectDatabase')){
		include(dirname(__FILE__).'/config.php');
		
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
			$result = array('id' => NULL, 'name' => 'DESCONHECIDO');
			
			if (!isset($_SESSION))
			{ session_start(); }
			
			if (isset($_SESSION['luouser'])){
				if ($basic){
					$stmt = $conn->prepare("SELECT `id`,`name`,`email`,`picture` FROM `users` WHERE `email`=? && `id`=?");
				} else {
					$stmt = $conn->prepare("SELECT `U`.`id`,`U`.`name`,`U`.`email`,`U`.`picture`,`U`.`birthdate`,`L`.`name` AS `langname`,`L`.`abbr` AS `langabbr`,`L`.`id` AS `langid`,`C`.`name` AS `countryname`,`C`.`abbr` AS `countryabbr` FROM `users` `U` INNER JOIN `languages` `L` ON `U`.`mainlang`=`L`.`id` INNER JOIN `countries` `C` ON `U`.`country`=`C`.`id` WHERE `U`.`email`=? && `U`.`id`=?");
				}
				$stmt->bind_param('si', $_SESSION['luouser'], $_SESSION['luoid']);
                $stmt->execute();
                $query = $stmt->get_result();
				if ($query->num_rows > 0){
					$row = $query->fetch_assoc();
					$result = $row;
				}
			}
			
			return $result;
		}
        
        function iniSystem($sistema){
			global $conn;
			
            if(!isset($_SESSION)){
                session_start();
            }
            if(isset($_SESSION[$sistema])){
                $_SESSION[$sistema] = null;
            }
            
            $_SESSION[$sistema] = array(
				// variaveis
                'variaveis' => array(),
				// variaveis sendo espandidas pela arvore
                'arvores' => array(),
            );
			
			// acessar o banco pegar os objetivos e para cada objetivo criar uma arvore
//			$query = "SELECT id FROM variavel WHERE sistema = '{$sistema}' AND objetivo = 1;";
//			$result = $conn->query($query);
//			if($result->num_rows > 0) {
//				// atribui as linhas retornadas
//				while($row = $result->fetch_assoc()) {
//					$_SESSION[$sistema]['arvores'][] = new Arvore($row['id']);
//				}
//			}
			
			// acessar o banco pegar as variaveis
			$query = "SELECT * FROM variavel WHERE sistema = '{$sistema}';";
			$result = $conn->query($query);
			if($result->num_rows > 0) {
				// atribui as linhas retornadas
				while($row = $result->fetch_assoc()) {
					$varivavel = new Variavel($row['id']);
					$varivavel->id = $row['id'];
					$varivavel->nome = $row['nome'];
					$varivavel->tipo = $row['tipo'];
					$varivavel->questionavel = $row['questionavel'];
					$varivavel->pergunta = $row['pergunta'];
					$varivavel->descricao = $row['descricao'];
					
					if($row['objetivo'] == 1){
						$arvore = new Arvore();
						$arvore->objetivo = $varivavel;
						$_SESSION[$sistema]['arvores'][] = $arvore;
					}
					
					// criando um array pra cada variavel
					$_SESSION[$sistema]['variaveis'][$varivavel->id] = array(
						// chave 'variavel' para um objeto de variavel
						'variavel' => serialize($varivavel),
						// chave de 'valor' para o valor da variavel
						'valor' => NULL,
					);
				}
			}
        }
	}
?>