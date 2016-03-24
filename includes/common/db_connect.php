<?php
	/**==================================================================
	* Get active version
	====================================================================*/
	require('version_active.php');
	$_SESSION['iknow']['version_soft'] = $version_soft;
	/*===================================================================*/

	/**==================================================================
	* Get database tables / fields definition for iKnow
	====================================================================*/
	require('define_db_names.php');
	/*===================================================================*/
	

	//==================================================================
	// Create active database connexion
	//==================================================================
	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
	//error_log(mysql_client_encoding($link));
	mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());	
	//==================================================================