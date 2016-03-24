<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('includes/common/buffering.php');
	/*===================================================================*/	

	/**==================================================================
	 * Load common constants
	 ====================================================================*/
	require('includes/common/constante.php');
	/*===================================================================*/	

	/**==================================================================
	 * Load common functions
	 ====================================================================*/	
	require('global_functions.php');
	/*===================================================================*/	

	/**==================================================================
	 * Application release
	 ====================================================================*/	
	require('includes/common/version_active.php');
	/*===================================================================*/		
	
	if(!isset($_GET['ssid']) || strlen($_GET['ssid']) < 35)
	{
		switch ($type_soft) 
		{
			case __FICHE_VISU__:
				require 'includes/global_gui/fiche_affichage.php';
				die();
				break;
			case __FICHE_MODIF__:
				require 'includes/global_gui/fiche_modif.php';
				die();
				break;
			case __CODE_VISU__:
				require 'includes/global_gui/code_affichage.php';
				die();
				break;
			case __CODE_MODIF__:
				require 'includes/global_gui/code_modif.php';
				die();
				break;	
		}		
	}

	$ssid = $_GET['ssid'];
	
		
	if($type_soft == __CODE_MODIF__ || $type_soft == __CODE_VISU__)
	{
		// iCode
		require('class/common/class_bdd.php');
		require('class/common/class_lock.php');
		require('class/icode/class_code.php');
	}
	else
	{
		// iSheet
		if($type_soft == __FICHE_MODIF__ || $type_soft == __FICHE_VISU__)
		{
			require('class/common/class_bdd.php');  		
			require('class/ifiche/class_fiche.php');  		
			require('class/ifiche/class_cartridge.php');    
			require('class/ifiche/class_header.php');    
			require('class/ifiche/class_etape.php');  
			require('class/common/class_lock.php'); 
			require('class/ifiche/class_step.php');
			
			if($type_soft == __FICHE_MODIF__)
			{
				// iSheet updating only
				require('class/ifiche/class_check.php');
			}
		}
	}

	
	/************************************************************************************************************
	 *		ACTIVATION DES VARIABLES DE SESSION
	 *************************************************************************************************************/
	session_name($ssid);
	session_start();
	$_SESSION['iknow']['version_soft'] = $version_soft;
	/************************************************************************************************************/

	function generate_logo_header($p_content)
	{
		
		return '<div class="logo_iknow_header_right">
				<div style="color:#000;right:5px;top:5px;font-size:1.2em;">
					'.$p_content.'
				</div>
			</div>
			<div class="logo_iknow_header" onclick="window.open(\'./\');" onmouseover="over(65,28,\'-\',\'X\');" onmouseout="unset_text_help();"></div>';
		
	}
?>