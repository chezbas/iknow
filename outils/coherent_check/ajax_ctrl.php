<?php
	// IOBJECT_ERROR_SERIALIZATION
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	


	/**==================================================================
	 * Database connexion
	 ====================================================================*/	
	require('../../class/common/class_bdd.php');
	/*===================================================================*/	


	/**==================================================================
	 * iCheck conherence class object
	 ====================================================================*/	
	require('class_coherent_check.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Load Lisha framework
	 ====================================================================*/	
	$dir_obj = '../../vimofy/';
	require($dir_obj.'vimofy_includes.php');
	/*===================================================================*/	


	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	
	
	if(!isset($_SESSION['coherence_check']))
	{
		$_SESSION['coherence_check'] = new coherent_check($ssid,$_POST['object'],$_POST['id']);
		if($_SESSION['coherence_check']->init())
		{
			$child = $_SESSION['coherence_check']->check_child_object();
		}
		else
		{
			// ID doesn't exist
			echo 'var ajax_json = '.json_encode(Array('total' => 0,'cursor' => 0,'error' => true)).';';
			return false;
		}
	}

	$_SESSION['coherence_check']->check_next_child();

//==================================================================
// Free php memory if no more check
//==================================================================
	if($_SESSION['coherence_check']->is_last())
	{
		unset($_SESSION['coherence_check']);
	}
//==================================================================
?>