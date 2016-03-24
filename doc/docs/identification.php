<?php 
	require('../../includes/common/ssid_simple.php');
	
	
	if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
	{
		if(isset($_GET['logout']))
		{
			// Logout
			require('identification/logout.php');
		}
		else
		{
			// User identified
			header('Location: ../../?ssid='.$_GET['ssid']);
		}
	}
	else
	{
		if(isset($_POST['login']) && isset($_POST['password']))
		{
			// Control identification
			require('identification/ctrl_identification.php');
		}
		else
		{
			// Require identification
			require('identification/connexion.php');
		}
	}
?>