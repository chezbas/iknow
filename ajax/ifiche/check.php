<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	require('../../class/common/class_bdd.php');
	require('../../class/ifiche/class_cartridge.php');
	require('../../class/ifiche/class_fiche.php');
	require('../../class/ifiche/class_etape.php');
	require('../../class/ifiche/class_step.php');
	require('../../class/ifiche/class_check.php');
	require('../../class/ifiche/class_header.php');
	require('../../class/common/class_lock.php');

	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	
	
	
	require('../../outils/coherent_check/class_coherent_check.php');
	
	$_SESSION['coherence_check'] = new coherent_check($ssid,$_POST['object'],$_POST['id']);
	$obj_coherent_check = &$_SESSION['coherence_check'];
	if($obj_coherent_check->init())
	{
		$child = $obj_coherent_check->check_child_object();
	}
	else
	{
		// ID doesn't exist
		echo 'var ajax_json = '.json_encode(Array('total' => 0,'cursor' => 0,'error' => true)).';';
		return false;
	}
	
	while(!$obj_coherent_check->is_last()) 
	{
		$obj_coherent_check->check_next_child();
	}
	
	$qtt_err = $obj_coherent_check->get_qtt_error();
	
	$_SESSION[$_POST['ssid']]['objet_fiche']->set_global_coherent_check_end(true,$qtt_err,$ssid);
?>