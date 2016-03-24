<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of area
 ====================================================================*/

	$vimofy_id = 'vimofy2_pole_lmod';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_poles_lmod = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Libelle`	AS 'Libelle',
					`id`		AS 'id' 
				FROM 
					`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."`";  

	$obj_vimofy_poles_lmod->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_poles_lmod->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_poles_lmod->define_size(350,'px',280,'px');											
	$obj_vimofy_poles_lmod->define_nb_line(50);													
	$obj_vimofy_poles_lmod->define_readonly(__R__);												
	$obj_vimofy_poles_lmod->define_theme('grey');													
	$obj_vimofy_poles_lmod->define_title($_SESSION[$ssid]['message'][504]);						
	$obj_vimofy_poles_lmod->define_sep_col_row(true,false);
	$obj_vimofy_poles_lmod->define_navbar_txt_activate(false);		
	$obj_vimofy_poles_lmod->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_poles_lmod->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
			
		//==================================================================
		// define column : tag
		//==================================================================
		$obj_vimofy_poles_lmod->define_column('id',$_SESSION[$ssid]['message'][98],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : group
		//==================================================================
		$obj_vimofy_poles_lmod->define_column('Libelle',$_SESSION[$ssid]['message']['iknow'][36],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_poles_lmod->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_vers_pole_lmod',Array('get_libelle_pole();iknow_panel_set_action(decodeURIComponent(libelle[101]));vimofy_clear_value(\'lst_vimofy2_vers_pole_lmod\');vimofy_clear_value(\'lst_vimofy2_activite_lmod\');load_vimofy_versions_poles(\'vimofy2_vers_pole_lmod\',null,true,true);'));		
	//==================================================================
	
	//==================================================================
	// define LMOD column to return
	//==================================================================
	$obj_vimofy_poles_lmod->define_col_return('id');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_poles_lmod->define_order_column('Libelle',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_poles_lmod->define_color_mask("DDDDEE","9999CC","88b2dc","000","FFF");
	$obj_vimofy_poles_lmod->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
?>