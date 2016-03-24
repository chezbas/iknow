<?php
	/**==================================================================
	 * Setup page timeout
	 ====================================================================*/	
	require('page_timeout.php');
	/*===================================================================*/	
		
	// Get the datehour of the begin of visualisation
	$_SESSION[$ssid]['start_visu'] = date('d/m/y H:i');
	if(!isset($_GET['version'])) $_GET['version'] = null;

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('html_doctype.php');
	/*===================================================================*/	
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<link rel="shortcut icon" type="image/png" href="favicon.ico">
		
	 	<script type="text/javascript">

<?php
	/**==================================================================
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('load_conf_session_js.php');
	/*===================================================================*/

	/**==================================================================
	* Recover language
	====================================================================*/	
	require('language.php');
	/*===================================================================*/
	
	echo "var iknow_lng = '".$_SESSION[$ssid]['langue']."';";	
	echo "var iknow_lng_tinyMCE = '".$_SESSION[$ssid]['langue_TinyMCE']."';";	
		
		//==================================================================
		// Load text
		//==================================================================
		$_SESSION[$ssid]['application'] = $type_soft;
		require('includes/common/textes.php');
		//==================================================================
?>
		</script>
<!--************************************************************************************************************
 *		GENERATION DE LA PARTIE STATIQUE SIMPLIFIEE DE L'ENTETE DE LA PAGE		
 *************************************************************************************************************-->	
<link rel="stylesheet" href="css/common/err_sql.css" type="text/css">	
<link rel="stylesheet" href="css/common/iknow/iknow_panel.css" type="text/css">
<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
<script type="text/javascript" src="js/common/iknow/iknow_panel.js"></script>
<script type="text/javascript" src="js/common/iknow/iknow_effect.js"></script>
<script type="text/javascript" src="js/common/iknow/iknow_timer.js"></script>
<!--**********************************************************************************************************-->