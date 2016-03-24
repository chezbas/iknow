<?php
	require '../../includes/common/define_db_names.php';

	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());

?>