<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : Display step output variables cartridge
 ====================================================================*/

	$vimofy_id = 'vimofy_cartridge_infos';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_cartridge_infos = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query of cartridge output parameters
	//==================================================================
	$obj_vimofy_cartridge_infos->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_cartridge_infos->define_size(100,'%',100,'%');											
	$obj_vimofy_cartridge_infos->define_nb_line(50);													
	$obj_vimofy_cartridge_infos->define_readonly(__R__);						// Read & Write
	$obj_vimofy_cartridge_infos->define_theme('grey');							// Define default style
	$obj_vimofy_cartridge_infos->define_sep_col_row(true,false);
	$obj_vimofy_cartridge_infos->define_page_selection_display(false,true);
	$obj_vimofy_cartridge_infos->define_title_display(false);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
	
		//==================================================================
		// define column : Name of input parameters
		//==================================================================
		$obj_vimofy_cartridge_infos->define_column('1',$_SESSION[$ssid]['message'][476],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Description
		//==================================================================
		$obj_vimofy_cartridge_infos->define_column('2',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Comments
		//==================================================================
		$obj_vimofy_cartridge_infos->define_column('3',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================

	//==================================================================
		
	//==================================================================
	// Define default input focus
	//==================================================================
		$obj_vimofy_cartridge_infos->define_input_focus('1');
	//==================================================================
	
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_cartridge_infos->define_order_column('1',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_cartridge_infos->define_color_mask("DDDDEE","9999CC","99c3ed","666","FFF");
	$obj_vimofy_cartridge_infos->define_color_mask("EEEEEE","AAAAAA","6690b9","555","DDD");
	//==================================================================	
?>