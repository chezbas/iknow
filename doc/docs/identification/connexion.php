<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<link rel="stylesheet" href="../../css/common/password.css" type="text/css">
		<script type="text/javascript" src="../../ajax/common/ajax_generique.js"></script>
		<title>Administration portail iKnow</title>
	</head>
	<body>
		<div id="header">
			<div class="logo"></div>
		</div>	
		<div id="identification_title">
			Veuillez vous authentifier
		</div>
		<div id="content">
			<div id="authentification">
				<form action="" method="post">
					<table summary="">
						<tr><td class="lib_input">Identifiant :</td><td><input type="text" name="login" id="login" class="input_txt gradient"/></td></tr>
						<tr><td class="lib_input">Mot de passe :</td><td><input type="password" name="password" class="input_txt gradient"/></td></tr>
						<tr><td></td><td><input type="submit" value="Connexion" class="submit_btn"/><input type="button" value="Annuler" class="submit_btn" onclick="window.location.replace('../../?ssid=<?php echo $ssid; ?>');"/></td></tr>
						<tr>
							<td colspan="2">
								<div id="lib_erreur">
									<?php 
										if(isset($bad_ident) && $bad_ident == true)
										{
											echo 'Erreur d\'identification';
										}
									?>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<div id="footer"></div>
		<script type="text/javascript">
			document.getElementById('login').focus();
		</script>
	</body>
</html>