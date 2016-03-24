<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet updating mode external tags in step
 ====================================================================*/

	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	
	$vimofy_id = 'vimofy_tags_ext_step';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_tags_ext = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag` AS 'Tag',
					`Groupe` AS 'Groupe',
					IF( objet = 'icode',
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][4])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`),
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][58])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`)
					  ) AS 'source'
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."` 
					WHERE 1 = 1
						AND `Version` = ".$_SESSION[$ssid]['objet_fiche']->get_version()." 
						AND `temp` = 0 
						AND `ID` = ".$_SESSION[$ssid]['id_temp']." 
						AND `Etape` = ".$_SESSION[$ssid]['etape_active']." 
						AND `id_src` IS NOT NULL";

	$obj_vimofy_tags_ext->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tags_ext->define_size(100,'%',100,'%');											
	$obj_vimofy_tags_ext->define_nb_line(50);													
	$obj_vimofy_tags_ext->define_readonly(__R__);											// Read & Write
	$obj_vimofy_tags_ext->define_theme('grey');												// Define default style
	$obj_vimofy_tags_ext->define_title($_SESSION[$ssid]['message'][370]);
	$obj_vimofy_tags_ext->define_background_logo('images/back_tags.png','repeat');			// Define background logo
	$obj_vimofy_tags_ext->define_sep_col_row(true,false);
	$obj_vimofy_tags_ext->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Tag Group
		//==================================================================
		$obj_vimofy_tags_ext->define_column('Groupe',$_SESSION[$ssid]['message'][410],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Tag
		//==================================================================
		$obj_vimofy_tags_ext->define_column('Tag',$_SESSION[$ssid]['message'][73],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Link provide tag information
		//==================================================================
		$obj_vimofy_tags_ext->define_column('source',$_SESSION[$ssid]['message'][411],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
			
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_tags_ext->define_input_focus('Groupe');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_tags_ext->define_order_column('Tag',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_tags_ext->define_color_mask("FFFFFF","CCCCCC","AAAAAA","999","888");
	$obj_vimofy_tags_ext->define_color_mask("EEEEEE","BBBBBB","888888","888","666");
	//==================================================================
?>