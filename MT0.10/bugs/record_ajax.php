<?php
	/**==================================================================
	 * MagicTree configuration ( Need active php session and ssid definition )
	 ====================================================================*/
	require('../../includes/MTSetup/setup.php');
	/*===================================================================*/		

	
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../includes/common/ssid_session_start.php');
	/*===================================================================*/		

	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../includes/common/buffering.php');
	/*===================================================================*/	
		
	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../includes/common/global_functions.php');
	/*===================================================================*/	

	
	/**==================================================================
	* Database connexion
	====================================================================*/	
	require('../includes/common/db_connect.php');
	/*===================================================================*/
	
	
	//==================================================================
	// Load ssid identifier
	//==================================================================
	if(!isset($_POST["ssid"]))
	{
		error_log_details('fatal','No ssid define. ssid is mandatory !');
	}
	else
	{
		$_GET["ssid"] = $_POST["ssid"];
	}
	//==================================================================

	//==================================================================
	// Get Tree ID
	//==================================================================
	if(!isset($_POST["ID"]))
	{
		error_log_details('fatal','you have to define always an ID');
		die();
	}
	$ID = $_POST["ID"];
	//==================================================================

	$corps = str_replace("'","''", $_POST["corps"]);
	$solution = str_replace("'","''", $_POST["solution"]);
	
	// Build update query
	$query = "UPDATE
				`bugsreports`
			SET
				`details` = '".$corps."',
				`solution` = '".$solution."'
			WHERE 1 = 1
				AND `ID` = '".$ID."'";

	// Send query to database
	$result = $link_mt->query($query);

	$num_rows = $link_mt->affected_rows;
	if($num_rows == 0)
	{
		echo $_SESSION[$ssid]['page_text'][15]['LT'];
	}
	else
	{
		echo $_SESSION[$ssid]['page_text'][16]['LT'];
	}
?>