<?php
	
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");
	require('connexion.php');
	$param = mysql_real_escape_string($_GET["ID"]);
	$objet = mysql_real_escape_string($_GET["objet"]);
	// ICode
	
	$sql_encodage = "SET NAMES 'utf8'";
	mysql_query($sql_encodage);
	
	$query = 'SELECT * FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].' WHERE `id` = "'.$param.'" AND THEME = "'.$objet.'" AND `version_active` = "'.$version_soft.'" LIMIT 1';
	error_log($query);
	
	$resultat = mysql_query($query);
	//echo $query;
	$valeur = mysql_result($resultat,0,"value");
	$commentaire = mysql_result($resultat,0,"designation");
	
	echo '<b style="color:red;">'.$valeur.'</b>';	


?>