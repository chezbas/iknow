<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of engine version
 ====================================================================*/

	$vimofy_id = 'vimofy_vers_moteur';

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
	// Recover relative call information about engine
	//==================================================================
	if(!isset($_POST['engine']))
	{
		$vim_engine = $_SESSION[$ssid]['objet_icode']->get_engine();   
	}
	else
	{
		$vim_engine = $_POST['engine'];
	}
	//==================================================================
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_vers_moteur = &$_SESSION['vimofy'][$ssid][$vimofy_id];

	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT 	
					`version`	AS 'version',
					`order`		AS 'order'
				FROM 
					`".$_SESSION['iknow'][$ssid]['struct']['tb_version_moteur']['name']."`  
				WHERE 1 = 1
					AND `id` = '".$vim_engine."' 
					AND `actif` = 'Y'";
	
	$obj_vimofy_vers_moteur->define_query($query);
	//==================================================================
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_vers_moteur->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_vers_moteur->define_size(350,'px',280,'px');											
	$obj_vimofy_vers_moteur->define_nb_line(50);													
	$obj_vimofy_vers_moteur->define_readonly(__R__);												
	$obj_vimofy_vers_moteur->define_theme('grey');													
	$obj_vimofy_vers_moteur->define_title($_SESSION[$ssid]['message'][500]);						
	$obj_vimofy_vers_moteur->define_sep_col_row(true,false);
	$obj_vimofy_vers_moteur->define_navbar_txt_activate(false);		
	$obj_vimofy_vers_moteur->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_vers_moteur->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : version
		//==================================================================
		$obj_vimofy_vers_moteur->define_column('version',$_SESSION[$ssid]['message'][62],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
		
		//==================================================================
		// define column : ordre
		//==================================================================
		$obj_vimofy_vers_moteur->define_column('order',$_SESSION[$ssid]['message'][507],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
				
	//==================================================================
		
	$obj_vimofy_vers_moteur->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_activite_lmod',Array('vimofy_set_innerHTML(\'engine_version_lib\',document.getElementById(\'lst_vimofy_vers_moteur\').value);iknow_panel_set_action(decodeURIComponent(libelle[105]));'));		
		
	//==================================================================
	// define LMOD column to return
	//==================================================================
	$obj_vimofy_vers_moteur->define_col_return('version');
	//==================================================================
			
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_vers_moteur->define_order_column('order',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_vers_moteur->define_color_mask("DDDDEE","9999CC","88b2dc","000","FFF");
	$obj_vimofy_vers_moteur->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
	
	if(isset($_POST['ssid']))
	{
		echo $obj_vimofy_vers_moteur->generate_lmod_form();
	}
?>