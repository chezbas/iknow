<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet import step ( choose step number 3/3 )
 ====================================================================*/


	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	
	$vimofy_id = 'imp_step_id_step';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_imp_step_id_step = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = '	SELECT 
					`id_etape` AS \'id_etape\',
					LEFT(SUBSTRING(description,LOCATE(\'class="BBTitre">\',description)+16),LOCATE(\'</span>\',description)-(LOCATE(\'class="BBTitre">\',description) + 16)) AS \'titre\'
				FROM
					'.$_SESSION['iknow'][$ssid]['struct']['tb_fiches_etapes']['name'] .' 
				WHERE 1 = 1
					AND `num_version` = '.$version.' 
					AND `id_fiche` = '.$id;
	
	$obj_vimofy_imp_step_id_step->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_imp_step_id_step->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_imp_step_id_step->define_size(400,'px',300,'px');											
	$obj_vimofy_imp_step_id_step->define_nb_line(50);													
	$obj_vimofy_imp_step_id_step->define_readonly(__R__);												
	$obj_vimofy_imp_step_id_step->define_theme('green');
	$title = str_replace('$id',$id,mysql_protect($_SESSION[$ssid]['message'][397]));
	$title = str_replace('$version',$version,$title);													
	$obj_vimofy_imp_step_id_step->define_title($title);						
	$obj_vimofy_imp_step_id_step->define_sep_col_row(true,false);
	$obj_vimofy_imp_step_id_step->define_navbar_txt_activate(false);		
	$obj_vimofy_imp_step_id_step->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_imp_step_id_step->define_page_selection_display(false,true);
	$obj_vimofy_imp_step_id_step->define_c_position_mode(__ABSOLUTE__);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet step ID
		//==================================================================
		$obj_vimofy_imp_step_id_step->define_column('id_etape',mysql_protect($_SESSION[$ssid]['message'][147]),__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : iSheet step title
		//==================================================================
		$obj_vimofy_imp_step_id_step->define_column('titre',mysql_protect($_SESSION[$ssid]['message'][493]),__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_imp_step_id_step->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'imp_step_Version',Array('get_content_step_alias('.$etape.');'));		
	//==================================================================
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_imp_step_id_step->define_col_return('id_etape');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_imp_step_id_step->define_order_column('id_etape',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_imp_step_id_step->define_color_mask("AADDAA","77AA77","ccc267","000","FFF");
	$obj_vimofy_imp_step_id_step->define_color_mask("CCFFCC","BBFFBB","aaeF98","000","DDD");
	//==================================================================
?>