<?php
	$ssid = $_POST['ssid'];
	session_name($ssid);
	session_start();
	
	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());

	$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
			   		SET `description` = "'.mysql_escape_string(($_POST["contenu"])).'",`NAME` = "'.mysql_escape_string(($_POST["titre"])).'",icone = "'.mysql_escape_string(utf8_decode($_POST["icone_child"])).'" 
			   		WHERE `ID_CHILD` = '.$_POST["id"].' 
			   		AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	mysql_query($sql) or die(error_log(mysql_error()));
	
	// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
	$sql = 'SELECT `ID_PARENT`
				 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
					  	WHERE `ID_CHILD` = '.$_POST["id"].' 
					  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';

	$resultat = mysql_query($sql);
	$id_parent = mysql_result($resultat,0,'ID_PARENT');		
	
	
	
	
	$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
			   		SET `NAME` = "'.mysql_escape_string(($_POST["titre_parent"])).'",icone = "'.mysql_escape_string(($_POST["icone_parent"])).'" 
			   		WHERE `ID_CHILD` = '.$id_parent.' 
			   		AND version = "'.$_SESSION['iknow']['version_soft'].'"';

	mysql_query($sql) or die(mysql_error().'   '.$sql);		
?>