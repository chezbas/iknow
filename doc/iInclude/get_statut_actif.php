<?php
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");
	require('connexion.php');

	$id_conf = mysql_real_escape_string($_GET["idconf"]);
	$langue = mysql_real_escape_string($_GET["langue"]);
	$objet = mysql_real_escape_string($_GET["objet"]);

	// Statut
	$query = 'SELECT * FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].' TEX, '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].' CONF
		WHERE 1 = 1 
		AND CONF.`id` = "'.$id_conf.'"
		AND CONF.`THEME` = "'.$objet.'"
		AND CONF.`version_active` = "'.$version_soft.'"
		AND CONF.`value` = TEX.`id_texte`
		AND TEX.`id_lang` = "'.$langue.'"
		AND TEX.`type` = "statut"
		AND TEX.`objet` = "'.$objet.'" 
		AND TEX.`version_active` = "'.$version_soft.'" LIMIT 1';
		
	$sql_encodage = "SET NAMES 'utf8'";
	mysql_query($sql_encodage);
	
	$resultat = mysql_query($query);
	//echo $query;
	$niveau = mysql_result($resultat,0,"id_texte");
	$libelle = mysql_result($resultat,0,"texte");
	
	echo '<b style="color:red;">'.$niveau.'</b> - <b style="color:red;">'.$libelle.'</b>';
?>