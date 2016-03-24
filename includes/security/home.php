<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Administration screen
 * Level require class __SYSOP_LEVEL_ACCESS__
 ====================================================================*/

	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../../includes/common/ssid_simple.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Load common constants
	 ====================================================================*/
	require('../../includes/common/constante.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Authentification require
	 ====================================================================*/
	$url_param = url_get_exclusion($_GET);
	$_SESSION['iknow'][$ssid]['redirect_page'] = getenv("SCRIPT_NAME");
	$_SESSION['iknow'][$ssid]['level_require_path'] = '../../';
	$_SESSION['iknow'][$ssid]['logout_page'] = 'index.php';
	$_SESSION['iknow'][$ssid]['level_require'] = __SYSOP_LEVEL_ACCESS__;

	require('../../includes/security/check_login.php');
	/*===================================================================*/	

	header('Location: '.$_SESSION['iknow'][$ssid]['level_require_path'].'index.php?'.$url_param);
	die();
?>