<?php 
	require('../../class/common/class_bdd.php');
	require('class_coherent_check.php');
	$dir_obj = '../../vimofy/';
	require($dir_obj.'vimofy_includes.php');
	
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	
	unset($_SESSION['coherence_check']);
	
?>