<?php

	$dir_obj = '';
	ob_start("ob_gzhandler");
	
	require '../../vimofy_includes.php';
	
	
	// Get vimofy id
	$vimofy_id = $_POST['vimofy_id'];
	
	// Get ssid
	$ssid = $_POST['ssid'];

	// Session start
	session_name($ssid);
	session_start(); 

	// Page encoding
	header('Content-type: text/html; charset=UTF-8');
?>