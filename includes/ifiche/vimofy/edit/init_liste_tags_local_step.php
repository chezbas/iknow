<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet updating mode local tags in step
 ====================================================================*/

	$vimofy_id = 'vimofy_tags_local_step';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_tags = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag` AS 'Tag',
					`Groupe` AS 'Groupe',
					`ID` AS 'ID',
					`Etape` AS 'Etape',
					`IdTag` AS 'IdTag',
					`Version` AS 'Version',
					`temp` AS 'temp'
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."` 
					WHERE 1 = 1
						AND `Version` = ".$_SESSION[$ssid]['objet_fiche']->get_version()." 
						AND `temp` = 0 
						AND `ID` = ".$_SESSION[$ssid]['id_temp']." 
						AND `Etape` = ".$_SESSION[$ssid]['etape_active']." 
						AND `id_src` IS NULL
			";

	$obj_vimofy_tags->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tags->define_size(100,'%',100,'%');											
	$obj_vimofy_tags->define_nb_line(50);													
	$obj_vimofy_tags->define_readonly(__RW__);											// Read & Write
	$obj_vimofy_tags->define_theme('grey');												// Define default style
	$obj_vimofy_tags->define_title($_SESSION[$ssid]['message'][73]);
	$obj_vimofy_tags->define_background_logo('images/back_tags.png','repeat');			// Define background logo
	$obj_vimofy_tags->define_sep_col_row(true,false);
	$obj_vimofy_tags->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Tag Group
		//==================================================================
		$obj_vimofy_tags->define_column('Groupe',$_SESSION[$ssid]['message'][410],__BBCODE__,__WRAP__,__LEFT__);
		$obj_vimofy_tags->define_col_quick_help('Groupe',true);	

		$obj_vimofy_tags->define_lov("	SELECT
											DISTINCT
												`Groupe`
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['name']."`
										WHERE 1 = 1",
									$_SESSION[$ssid]['message'][548],
									'Groupe'
									);
		$obj_vimofy_tags->define_column_lov('Groupe',$_SESSION[$ssid]['message'][410],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : Tag
		//==================================================================
		$obj_vimofy_tags->define_column('Tag',$_SESSION[$ssid]['message'][73],__BBCODE__,__WRAP__,__CENTER__);

		$obj_vimofy_tags->define_lov("	SELECT
											DISTINCT
												`Tag`
										FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['name']."`
										WHERE 1 = 1
											AND `Groupe` = '||TAGLOV_Groupe**Groupe||'",
										$_SESSION[$ssid]['message'][61],
										'Tag'
									);
		$obj_vimofy_tags->define_column_lov('Tag',$_SESSION[$ssid]['message'][61],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
		
	//==================================================================
			
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_tags->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']);
	
	// Columns attribut
	$obj_vimofy_tags->define_rw_flag_column('Tag',__REQUIRED__);
	$obj_vimofy_tags->define_rw_flag_column('Groupe',__REQUIRED__);
	
	// Table key
	$obj_vimofy_tags->define_key(Array('ID','Etape','IdTag','Version','temp'));
	
	// Columns predefined values
	$obj_vimofy_tags->define_col_value('ID',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_tags->define_col_value('Etape',$_SESSION[$ssid]['etape_active']);
	$obj_vimofy_tags->define_col_value('Version',$_SESSION[$ssid]['objet_fiche']->get_version());
	$obj_vimofy_tags->define_col_value('temp',0);
	//==================================================================
		
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_tags->define_input_focus('Groupe');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_tags->define_order_column('Tag',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_tags->define_color_mask("FFFFFF","CCCCCC","AAAAAA","000","FFF");
	$obj_vimofy_tags->define_color_mask("EEEEEE","BBBBBB","888888","000","DDD");
	//==================================================================
?>