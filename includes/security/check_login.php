<?php
	if(!isset($_SESSION['iknow'][$ssid]['identified_level']) || !$_SESSION['iknow'][$ssid]['identified_level'])
	{
		// No identification
		header('Location: '.$_SESSION['iknow'][$ssid]['level_require_path'].'identification_page.php?'.$url_param);
		die();
	}
	else
	{
		// Login ok, continue
	}