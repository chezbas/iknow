<?php
	require(__DIR__.'/../common/define_db_names.php');
	//==================================================================
	// Information database connexion
	//==================================================================
	$_SESSION[$ssid]['LI']['serveur_bdd'] = $_SESSION['iknow'][$ssid]['serveur_bdd'];
	$_SESSION[$ssid]['LI']['schema_MT'] = $_SESSION['iknow'][$ssid]['schema_iknow'];
	$_SESSION[$ssid]['LI']['user_MT'] = $_SESSION['iknow'][$ssid]['user_iknow'];
	$_SESSION[$ssid]['LI']['password_MT'] = $_SESSION['iknow'][$ssid]['password_iknow'];
	//==================================================================
		
	//==================================================================
	// Define table name
	//==================================================================
	$_SESSION[$ssid]['LI']['struct']['tb_configuration']['name'] 			= 'lisha_config';
	$_SESSION[$ssid]['LI']['struct']['tb_lang']['name']						= 'lisha_language';
	$_SESSION[$ssid]['LI']['struct']['tb_text_screen']['name']				= 'lisha_screen_text';
	//==================================================================
?>