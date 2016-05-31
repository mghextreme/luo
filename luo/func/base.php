<?php
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
	}
?>