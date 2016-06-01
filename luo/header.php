<?php
	session_start();
	include(__DIR__.'/func/base.php');
	connectDatabase();
?>
<!DOCTYPE html>
<html lang="pt">
	<head>
		<!-- info -->
		<meta charset="utf-8" />
		<base href="<?=$website['link'];?>/" target="_self" />
		<meta name="author" content="EvandroMS & MatiasGH" />
		<meta name="viewport" content="initial-scale=1.0" />
		<link rel="shortcut icon" href="favicon.ico" />
		
		<!-- CSS -->
		<link href="style.css" rel="stylesheet" type="text/css" />
		<link href="js/overbox/overbox.css" rel="stylesheet" type="text/css" />
		<link href="font-awesome/css/font-awesome.font-only.css" rel="stylesheet" type="text/css" />
		
		<!-- JS -->
		<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.easing-1.3.js"></script>
		<script type="text/javascript" src="js/overbox/overbox.js"></script>
		<script type="text/javascript" src="js/base.js"></script>
		<script type="text/javascript" charset="utf-8">var adm = '<?=$website['link'];?>/';</script>
		
		<!-- Google WebFonts -->
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,300' rel='stylesheet' type='text/css'>