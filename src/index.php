<?php
	include(__DIR__.'/func/protect.php');
	include(__DIR__.'/header.php');
	$user = getUserData();
?>
		<title>Lúo</title>
		<meta name="description" content="" />
		<meta name="keywords" content="" />
	</head>
	<body>
		<?php include('top-menu.php'); ?>
		<ul id="sistemas" class="center">
			<?php
				$query = $conn->query("SELECT `id`,`nome`,`datetime` FROM `sistema` WHERE `usuario`='{$user['id']}'");
				while ($row = $query->fetch_assoc()) :
					$row['datetime'] = new DateTime($row['datetime']);
			?>
			<li>
				<h3><?=$row['nome'];?></h3>
				<span><?=$row['datetime']->format('d/m/Y');?></span>
				<div class="actions">
					<a href="responder/<?=$row['id'];?>/<?=nameLink($row['nome']);?>" target="_blank" class="fa">&#xf04b;</a>
					<!--<a href="javascript:$openLink()" class="fa">&#xf1d8;</a>-->
					<a href="sistema/<?=$row['id'];?>/<?=nameLink($row['nome']);?>" class="fa">&#xf013;</a>
				</div>
			</li>
			<?php endwhile; ?>
		</ul>
		<?php include('footer.php'); ?>
	</body>
</html>