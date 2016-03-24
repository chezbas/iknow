<?php
	$ssid = $_GET['ssid'];
	session_name($ssid);
	session_start();
	unset($_SESSION['identifier']);
?>