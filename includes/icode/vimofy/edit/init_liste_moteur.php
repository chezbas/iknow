<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of iCode engine
 ====================================================================*/

	$vimofy_id = 'vimofy_moteur';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_moteur = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Description`	AS 'Description',
					`id`			AS 'id'
				FROM 
					`".$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['name']."`";
					
	$obj_vimofy_moteur->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_moteur->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_moteur->define_size(350,'px',280,'px');											
	$obj_vimofy_moteur->define_nb_line(50);													
	$obj_vimofy_moteur->define_readonly(__R__);												
	$obj_vimofy_moteur->define_theme('grey');													
	$obj_vimofy_moteur->define_title($_SESSION[$ssid]['message'][501]);						
	$obj_vimofy_moteur->define_sep_col_row(true,false);
	$obj_vimofy_moteur->define_navbar_txt_activate(false);		
	$obj_vimofy_moteur->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_moteur->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Tag ID
		//==================================================================
		$obj_vimofy_moteur->define_column('id',$_SESSION[$ssid]['message'][98],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
				
		//==================================================================
		// define column : Description
		//==================================================================
		$obj_vimofy_moteur->define_column('Description',$_SESSION[$ssid]['message'][49],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_moteur->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy_vers_moteur',Array('get_libelle_moteur();iknow_panel_set_action(decodeURIComponent(libelle[104]));load_vimofy_engine_version(\'vimofy_vers_moteur\',null,true);'));		
	//==================================================================

	//==================================================================
	// Define LMOD return column
	//==================================================================
	$obj_vimofy_moteur->define_col_return('id');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_moteur->define_order_column('Description',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_moteur->define_color_mask("DDDDEE","9999CC","88b2dc","000","FFF");
	$obj_vimofy_moteur->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
?>