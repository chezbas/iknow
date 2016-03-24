<?php

sendmail();
function sendmail($slave_SQL = '',$slave_IO = '',$IO_State = '',$Log_Pos = '',$Log_File = '',$error = '')
		{
			
$headers ='From: Newsletters iKnow <noreply@globaline.local>'."\r\n";
$headers .='Reply-To: noreply@globaline.local'."\n";
$headers .='Content-Type: text/html; charset="UTF-8"'."\n";
$headers .='Content-Transfer-Encoding: 8bit';
			$title = 'iKnow - Newsletters nouveautées version 2.04';
			
			 							
$texte_body = "<html xmlns=\"http://www.w3.org/1999/xhtml\">
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<link href=\"styles.css\" rel=\"stylesheet\" type=\"text/css\" />

			<style>
			
			#texte_bas {
			
			font: normal 11px auto \"Trebuchet MS\", Verdana, Arial, Helvetica, sans-serif;
			text-align:center;
			
			
			}
			body 
			{
				font: normal 11px auto \"Trebuchet MS\", Verdana, Arial, Helvetica, sans-serif;
				color: #126499;
				background: #E6EAE9;
				margin:0;
				padding:0;
			}
			
			p
			{
				font: normal 15px auto \"Trebuchet MS\", Verdana, Arial, Helvetica, sans-serif;
				color:#126499;
				font-weight:bold;
				margin-bottom:10px;
			}

			</style>
	</head>

	<body>
		<div style=\"background:url(http://dtravel.iknow/images/err_head_back.jpg) repeat-x;height:90px;\">
			<div style=\"background:url(http://dtravel.iknow/images/iknow_70.png) no-repeat center;height:80px;width:100%;\"></div>
		</div>
		<div>
			<p>Contrôle de cohérence des iObjets enfants</p>
			<div>La nouvelle version d'iKnow intègre un contrôle de cohérence  des enfants à chaque sauvegarde de votre Objet.<br />
			Exemple : des que vous sauvegardez un iCode, un contrôle est lancé pour vérifier si il est utilisé dans des iFiches, et si la cohérence de l'appel n'est pas dégradée.</div>
			<p>Menu contextuel dans TinyMCE</p>
			<div>Maintenant le menu contextuel de la boite d'edition des étapes est dynamique !.<br />
			Exemple : Vous faites un clic droit sur une variable, vous pouvez la supprimer.</br><img src=\"http://dtravel.iknow/screenshot/newsletters/back_204-1.png\"/></div>
		</div>
	</body>
</html>
";
	
				$res = mail("admin@wiknow.org", $title, $texte_body, $headers);
		}
?>