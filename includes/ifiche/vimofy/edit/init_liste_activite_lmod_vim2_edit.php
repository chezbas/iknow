<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet updating mode head activity list ( depend on ikn_area choice )
 ====================================================================*/


	$vimofy_id = 'vimofy2_activite_lmod';

	//==================================================================
	// Load Lisha framework
	//==================================================================
	if(isset($_POST['ssid']))
	{
		$ssid = $_POST['ssid'];
		
		/**==================================================================
		 * Load Lisha framework
		 ====================================================================*/
		$dir_obj = '../../../../vimofy/';
		require($dir_obj.'vimofy_includes.php');
		/*===================================================================*/	

		
		/**==================================================================
		 * Active php session
		 ====================================================================*/
		require('../../../../includes/common/active_session.php');
		/*===================================================================*/	
		
		
		unset($_SESSION['vimofy'][$ssid][$vimofy_id]);
	}
	//==================================================================
	
	//==================================================================
	// Depend on area id value
	//==================================================================
	if(!isset($_POST['pole']))
	{
		$vim_pole = $_SESSION[$ssid]['objet_fiche']->get_id_pole();   
	}
	else
	{
		$vim_pole = $_POST['pole'];
	}
	//==================================================================
		
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_activite_lmod = &$_SESSION['vimofy'][$ssid][$vimofy_id];

	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`libelle` AS 'libelle',
					`ID` AS 'ID'  
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."`  
				WHERE 1 = 1
					AND `ID_POLE` = '".$vim_pole."'";
	
	$obj_vimofy_activite_lmod->define_query($query);
	//==================================================================
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_activite_lmod->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_activite_lmod->define_size(350,'px',280,'px');											
	$obj_vimofy_activite_lmod->define_nb_line(50);													
	$obj_vimofy_activite_lmod->define_readonly(__R__);												
	$obj_vimofy_activite_lmod->define_theme('grey');													
	$obj_vimofy_activite_lmod->define_title_display(false);
	//$obj_vimofy_activite_lmod->define_title($_SESSION[$ssid]['message']['iknow'][51]);						
	$obj_vimofy_activite_lmod->define_sep_col_row(true,false);
	$obj_vimofy_activite_lmod->define_navbar_txt_activate(false);		
	$obj_vimofy_activite_lmod->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_activite_lmod->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet activity description
		//==================================================================
		$obj_vimofy_activite_lmod->define_column('libelle',$_SESSION[$ssid]['message']['iknow'][51],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================

		//==================================================================
		// define column : iSheet activity ID
		//==================================================================
		$obj_vimofy_activite_lmod->define_column('ID',$_SESSION[$ssid]['message'][46],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
	//==================================================================
			
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_activite_lmod->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_activite_lmod',Array('get_libelle_activite();iknow_panel_set_action(decodeURIComponent(libelle[337]));if(document.getElementById(\'lst_vimofy2_module_lmod\').value==\'\')load_vimofy_modules(\'vimofy2_module_lmod\',null,true,true);'));		
	//==================================================================
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_activite_lmod->define_col_return('ID');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_activite_lmod->define_order_column('libelle',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_activite_lmod->define_color_mask("FFF","CCC","ADA","444","FFF");
	$obj_vimofy_activite_lmod->define_color_mask("EEE","BBB","585","333","DDD");
	//==================================================================
	
	if(isset($_POST['ssid']))
	{
		echo $obj_vimofy_activite_lmod->generate_lmod_form();
	}
?>