<?php
	$ssid = $_GET['ssid'];
	session_name($ssid);
	session_start();

	unset($_SESSION['iknow'][$ssid]['identified_level']);
	header('Location: ../../'.$_SESSION['iknow'][$ssid]['logout_page']);