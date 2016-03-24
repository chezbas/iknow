<?php	
	/**==================================================================
	 * Lisha configuration
	 ====================================================================*/
	require('../../includes/lishaSetup/main_configuration.php');
	/*===================================================================*/		


	/**==================================================================
	 * MagicTree configuration
	 ====================================================================*/
	$path_root_framework = './';
	require('../../includes/MTSetup/setup.php');
	/*===================================================================*/		


	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * MagicTree framework include
	 ====================================================================*/	
	$path_root_magictree = '../../'.__MAGICTREE_APPLICATION_RELEASE__;
	require($path_root_magictree.'/page_includes.php');
	/*===================================================================*/	
	
	
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

	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('../../includes/common/load_conf_session.php');
	/*===================================================================*/
	

	/**==================================================================
	 * Setup page timeout
	 ====================================================================*/	
	require('../../includes/common/page_timeout.php');
	/*===================================================================*/


	/**==================================================================
	 * IE prerequisite
	 * Force header encoding for Internet explorer ajax call
	 * Have to be removed then ie is ok
	 ====================================================================*/	
	require('../../includes/common/header_ajax_ie.php');
	/*===================================================================*/	
?>