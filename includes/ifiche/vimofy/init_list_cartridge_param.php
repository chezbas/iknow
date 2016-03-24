<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : Display step input variables cartridge
 ====================================================================*/

	$vimofy_id = 'vimofy_cartridge_param';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_cartridge_param = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query of cartridge input parameters
	//==================================================================
	$obj_vimofy_cartridge_param->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_cartridge_param->define_size(100,'%',100,'%');											
	$obj_vimofy_cartridge_param->define_nb_line(50);													
	$obj_vimofy_cartridge_param->define_readonly(__R__);					// Read & Write
	$obj_vimofy_cartridge_param->define_theme('grey');						// Define default style
	$obj_vimofy_cartridge_param->define_sep_col_row(true,false);
	$obj_vimofy_cartridge_param->define_page_selection_display(false,true);
	$obj_vimofy_cartridge_param->define_title_display(false);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
	
		//==================================================================
		// define column : Name of output parameters
		//==================================================================
		$obj_vimofy_cartridge_param->define_column('param',$_SESSION[$ssid]['message'][59],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Description
		//==================================================================
		$obj_vimofy_cartridge_param->define_column('value',$_SESSION[$ssid]['message'][193],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================

	//==================================================================
		
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_cartridge_param->define_input_focus('param');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_cartridge_param->define_order_column('param',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_cartridge_param->define_color_mask("EEDDDD","CC9999","eeb289","666","FFF");
	$obj_vimofy_cartridge_param->define_color_mask("EEEEEE","AAAAAA","ee8844","555","DDD");
	//==================================================================
?>