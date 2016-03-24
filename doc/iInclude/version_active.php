<?php
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");

	echo '<b style="color:red;">'.$version_soft.'</b>';	


?>