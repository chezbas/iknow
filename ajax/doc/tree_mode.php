<?php	
	/**==================================================================
	 * Get ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	if(!isset($_POST["ssid"]))
	{
		error_log_details('fatal','you have to define always a ssid');
		die();
	}
	$ssid = $_POST["ssid"];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	
	//==================================================================	
	// switch mode
	//==================================================================	
	if(!isset($_SESSION[$ssid]['lishadb']['doc']['tree']['user']))
	{
		$_SESSION[$ssid]['lishadb']['doc']['tree']['user'] = true;
	}
	else
	{
		if($_SESSION[$ssid]['lishadb']['doc']['tree']['user'])
		{
			$_SESSION[$ssid]['lishadb']['doc']['tree']['user'] = false;
		}
		else
		{
			$_SESSION[$ssid]['lishadb']['doc']['tree']['user'] = true;
		}
	}
	//==================================================================	
?>