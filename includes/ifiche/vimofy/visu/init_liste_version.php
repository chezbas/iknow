<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : List of iSheet version ( header )
 ====================================================================*/

	$vimofy_id = 'vimofy_version_fiche';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_versions = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`num_version` 	AS 'num_version',
					`date`			AS 'date',
					`pers`			AS 'pers'
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['name']."` 
				WHERE 1 = 1
					AND `id_fiche` = ".$_GET['ID'];
				
	$obj_vimofy_versions->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_versions->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_versions->define_size(350,'px',280,'px');											
	$obj_vimofy_versions->define_nb_line($_SESSION[$ssid]['configuration'][15]);													
	$obj_vimofy_versions->define_readonly(__R__);												
	$obj_vimofy_versions->define_theme('green');													
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
		// define column : ISheet version number
		//==================================================================
		$obj_vimofy_versions->define_column('num_version',$_SESSION[$ssid]['message'][50],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
			
		//==================================================================
		// define column : Record date time
		//==================================================================
		$obj_vimofy_versions->define_column('date',$_SESSION[$ssid]['message']['iknow'][10],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
				
		//==================================================================
		// define column : Signed by
		//==================================================================
		$obj_vimofy_versions->define_column('pers',$_SESSION[$ssid]['message'][436],__TEXT__,__WRAP__,__LEFT__,__PERCENT__);
		//==================================================================
						
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_versions->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy_version_fiche',Array('changer_version();'));		
	//==================================================================
	
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_versions->define_col_return('num_version');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_versions->define_order_column('num_version',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_versions->define_color_mask("BDB","9C9","b2dc88","000","FFF");
	$obj_vimofy_versions->define_color_mask("EEE","9B9","90b966","000","DDD");
	//==================================================================
?>