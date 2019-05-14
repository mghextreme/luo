<?php
	include(__DIR__.'/header.php');
	
	if (isset($_SESSION['luouser']))
	{ echo '<META http-equiv="refresh" content="0;URL=menu">'; die(); }
?>
		<title>Lúo</title>
		<meta name="description" content="Lúo: Sistema de criação e execução de Sistemas Expecialistas" />
		<meta name="keywords" content="" />
		
		<script type="text/javascript" charset="utf-8">
            $signIn = function(){
                var form = $('form#formSignIn'),
                    user = form.find('> div#username > input'),
                    pass = form.find('> div#password > input'),
                    error = false;
                
                form.children('input#submit').attr({ disabled : 'disabled' });
                form.children('div#feedback').stop().slideUp(300);
                
                var regex = /^([a-zA-Z0-9._-]+){6,20}$/;
                if (!regex.test(user.val())) {
                    user.parent().addClass('fail');
                    error = true;
                } else user.parent().removeClass('fail');

                if (pass.val().length < 6) {
                    pass.parent().addClass('fail');
                    error = true;
                } else pass.parent().removeClass('fail');
                
                if (error){
                    form.children('div#feedback').html('Verifique os campos .').stop().slideDown(300);
                    form.children('input#submit').removeAttr('disabled');
                    return;
                }
                
                $.post('func/login-sign.php', form.serialize(), function(result){
                    if (result == 'ok')
                    { window.location = 'start'; }
                    else {
                        form.children('div#feedback').html('Login incorreto.').stop().slideDown(300);
                        form.children('input#submit').removeAttr('disabled');
                        console.log(result);
                    }
                });
            }
            
			$(function(){
				$('form#formSignIn > div#username.field > input').focus();
			});
		</script>
	</head>
	<body id="login">
		<div id="box">
			<h2>Lúo</h2>
			<form id="formSignIn" method="post" name="formSignIn" action="javascript:$signIn()" class="on">
				<div class="field" id="username">
					<label for="username">Login</label>
					<input type="text" name="username" />
				</div>
				<div class="field" id="password">
					<label for="password">Password</label>
					<input type="password" name="password" />
				</div>
				<div id="feedback"></div>
				<input type="submit" id="submit" value="Entrar" />
			</form>
		</div>
	</body>
</html>