<?php
	//==================================================================
	// Information database connexion
	//==================================================================
	$_SESSION[$ssid]['MT']['serveur_bdd'] = 'localhost';
	$_SESSION[$ssid]['MT']['schema_MT'] = 'iknow';
	$_SESSION[$ssid]['MT']['user_MT'] = 'adm_iknow';
	$_SESSION[$ssid]['MT']['password_MT'] = 'MC&hny11';
	//==================================================================
		
	//==================================================================
	// Define table name
	//==================================================================
	$_SESSION[$ssid]['MT']['struct']['tb_configuration']['name'] 		= 'mt_conf';
	$_SESSION[$ssid]['MT']['struct']['tb_lang']['name']					= 'mt_lang';
	$_SESSION[$ssid]['MT']['struct']['tb_text']['name']					= 'mt_text';
	//==================================================================
?>