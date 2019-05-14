<?php
	// Set Database Data
	$db = array();
	$db['host'] = getenv("DB_HOST");
	$db['name'] = getenv("DB_NAME");
	$db['user'] = getenv("DB_USERNAME");
	$db['pass'] = getenv("DB_PASSWORD");
	
	// Set Website Options
	$website = array();
	$website['name'] = "Lúo";
	$website['base'] = getenv("WEB_HOST");
	$website['link'] = getenv("WEB_PROTOCOL") . "://" . $website['base'];
	
	// Connection
	$conn = NULL;
?>
