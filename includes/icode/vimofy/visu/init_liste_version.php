<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in display mode : List of iCode version ( header )
 ====================================================================*/

	$vimofy_id = 'vimofy_version_code';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_versions = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT 
					`Version`			AS 'Version',
					`last_update_date`	AS 'last_update_date',
					`Last_update_user`	AS 'Last_update_user'
				FROM 
					`".$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name']."`
				WHERE 1 = 1
					AND `ID` = ".$_GET['ID'];
				
	$obj_vimofy_versions->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_versions->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_versions->define_size(400,'px',280,'px');											
	$obj_vimofy_versions->define_nb_line($_SESSION[$ssid]['configuration'][15]);													
	$obj_vimofy_versions->define_readonly(__R__);												
	$obj_vimofy_versions->define_theme('grey');													
	$obj_vimofy_versions->define_title_display(false);
	//$obj_vimofy_versions->define_title($_SESSION[$ssid]['message']['iknow'][9]);						
	$obj_vimofy_versions->define_sep_col_row(true,false);
	$obj_vimofy_versions->define_navbar_txt_activate(false);		
	$obj_vimofy_versions->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_versions->define_page_selection_display(false,true);
	$obj_vimofy_versions->define_lmod_width(50);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : version
		//==================================================================
		$obj_vimofy_versions->define_column('Version',$_SESSION[$ssid]['message'][62],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
			
		//==================================================================
		// define column : last update date
		//==================================================================
		$obj_vimofy_versions->define_column('last_update_date',$_SESSION[$ssid]['message']['iknow'][10],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
				
		//==================================================================
		// define column : last update user
		//==================================================================
		$obj_vimofy_versions->define_column('Last_update_user',$_SESSION[$ssid]['message'][2],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
				
	//==================================================================

		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_versions->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy_version_fiche',Array('changer_version();'));		
	//==================================================================

	//==================================================================
	// define LMOD column to return
	//==================================================================
	$obj_vimofy_versions->define_col_return('Version');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_versions->define_order_column('Version',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_versions->define_color_mask("DDDDEE","9999CC","88b2dc","000","FFF");
	$obj_vimofy_versions->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
?>