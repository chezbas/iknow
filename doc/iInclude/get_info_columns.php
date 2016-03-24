<?php
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");
	require('connexion.php');
	mysql_select_db('information_schema') or die('dbconn: mysql_select_db: ' + mysql_error());
	$type = mysql_real_escape_string($_GET["TYPE"]);
	$table = mysql_real_escape_string($_GET["TABLE"]);
	$colonne = mysql_real_escape_string($_GET["COLONNE"]);
		
	$query = 'SELECT `'.$type.'` as RETOUR 
			FROM information_schema.`COLUMNS` 
			WHERE 1 = 1
			AND `TABLE_NAME` = "'.$table.'"
			AND COLUMN_NAME = "'.$colonne.'" 
			LIMIT 1';
	
	$resultat = mysql_query($query) or die(mysql_error());
	
	$valeur = mysql_result($resultat,0,"RETOUR");
	
	echo '<b style="color:red;">'.$valeur.'</b>';

?>