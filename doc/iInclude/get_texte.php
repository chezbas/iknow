<?php 

	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();
	
	?>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
<?php


	require("../../includes/common/version_active.php");
	require('connexion.php');

	$param = mysql_real_escape_string($_GET["ID"]);
	$objet = mysql_real_escape_string($_GET["objet"]);
	$langue = mysql_real_escape_string($_GET["LAN"]);
	// ICode
	$sql_encodage = "SET NAMES 'utf8'";
	mysql_query($sql_encodage);
	
	$query = 'SELECT * FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].' WHERE id_texte = "'.$param.'" AND objet="'.$objet.'" AND id_lang = "'.$langue.'" AND version_active = "'.$version_soft.'" LIMIT 1';
	
	$resultat = mysql_query($query);
	//echo $query;
	$texte = mysql_result($resultat,0,"texte");
	
	
	echo $texte;	


?>
      </body>
</html>