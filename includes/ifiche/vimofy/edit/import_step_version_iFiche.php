<?php
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet import step ( choose iSheet version 2/3 )
 ====================================================================*/

	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	
	$vimofy_id = 'imp_step_Version';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_imp_step_version = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = '	SELECT 
					FIC.`num_version` AS \'num_version\',
					FIC.`titre` AS \'Titre\' 
				FROM
					`'.$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['name'].'` FIC 
				WHERE 1 = 1 
					AND `id_fiche` = '.$id;
	
	$obj_vimofy_imp_step_version->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_imp_step_version->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_imp_step_version->define_size(500,'px',300,'px');											
	$obj_vimofy_imp_step_version->define_nb_line(50);													
	$obj_vimofy_imp_step_version->define_readonly(__R__);												
	$obj_vimofy_imp_step_version->define_theme('green');													
	$obj_vimofy_imp_step_version->define_title(str_replace('$id',$id,mysql_protect($_SESSION[$ssid]['message'][398])));						
	$obj_vimofy_imp_step_version->define_sep_col_row(true,false);
	$obj_vimofy_imp_step_version->define_navbar_txt_activate(false);		
	$obj_vimofy_imp_step_version->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_imp_step_version->define_page_selection_display(false,true);
	$obj_vimofy_imp_step_version->define_c_position_mode(__ABSOLUTE__);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet version number
		//==================================================================
		$obj_vimofy_imp_step_version->define_column('num_version',mysql_protect($_SESSION[$ssid]['message'][287]),__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : iSheet title
		//==================================================================
		$obj_vimofy_imp_step_version->define_column('Titre',mysql_protect($_SESSION[$ssid]['message'][47]),__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_imp_step_version->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'imp_step_Version',Array('load_vimofy_alias_step_id('.$etape.');'));
	//==================================================================
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_imp_step_version->define_col_return('num_version');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_imp_step_version->define_order_column('num_version',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_imp_step_version->define_color_mask("AADDAA","77AA77","ccc267","000","FFF");
	$obj_vimofy_imp_step_version->define_color_mask("CCFFCC","BBFFBB","aaeF98","000","DDD");
	//==================================================================
?>