<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of tags
 ====================================================================*/

	$vimofy_id = 'vimofy2_tags';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_tags = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag` 		AS 'Tag',
					`Groupe`	AS 'Groupe',
					`IdTag`		AS 'IdTag',
					`ID`		AS 'ID',
					`Version`	AS 'Version',
					`objet`		AS 'objet',
					`Etape`		AS 'Etape'
				  FROM 
				  	`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."` 
				  WHERE 1 = 1
				  	AND `ID`	= ".$_SESSION[$ssid]['id_temp']." 
				  	AND `objet`	= 'icode'";

	$obj_vimofy_tags->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tags->define_size(100,'%',100,'%');											
	$obj_vimofy_tags->define_nb_line(50);													
	$obj_vimofy_tags->define_readonly(__RW__);										// Read & Write
	$obj_vimofy_tags->define_theme('grey');											// Define default style
	$obj_vimofy_tags->define_background_logo('images/back_tags.png','repeat');		// Define background logo
	$obj_vimofy_tags->define_sep_col_row(true,false);
	$obj_vimofy_tags->define_title_display(false);
	$obj_vimofy_tags->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
			
		//==================================================================
		// define column : group
		//==================================================================
		$obj_vimofy_tags->define_column('Groupe',$_SESSION[$ssid]['message'][97],__BBCODE__,__WRAP__,__CENTER__);	

		// Match code for column group
		$obj_vimofy_tags->define_lov('	SELECT 
											DISTINCT
												`groupe`
	                                	FROM
	                             			`'.$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['name'].'`
	                               		UNION ALL
	                              		SELECT 
	                              			"%" groupe',
									$_SESSION[$ssid]['message'][118],'Groupe');
		
		$obj_vimofy_tags->define_column_lov('groupe',$_SESSION[$ssid]['message'][97],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
		
		//==================================================================
		// define column : tag
		//==================================================================
		$obj_vimofy_tags->define_column('Tag',$_SESSION[$ssid]['message'][39],__BBCODE__,__WRAP__,__CENTER__);
		
		// Match code for column tag
		$obj_vimofy_tags->define_lov('	SELECT
											DISTINCT
												`tag`
	                                   	FROM 
	                                    	`'.$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['name'].'` 
	                                   	WHERE 1 = 1
	                                    	AND `groupe` LIKE "||TAGLOV_Groupe**groupe||"',
									$_SESSION[$ssid]['message'][119],'tag');
		
		$obj_vimofy_tags->define_column_lov('tag',$_SESSION[$ssid]['message'][39],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================

	//==================================================================

	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_tags->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']);
	
	// Table key
	$obj_vimofy_tags->define_key(Array('ID','Version','Etape','IdTag','objet'));
	
	// Columns attribut
	$obj_vimofy_tags->define_rw_flag_column('Tag',__REQUIRED__);
	$obj_vimofy_tags->define_rw_flag_column('Groupe',__REQUIRED__);
	
	// Columns predefined values
	$obj_vimofy_tags->define_col_value('ID',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_tags->define_col_value('Version',$_SESSION[$ssid]['objet_icode']->get_version());
	$obj_vimofy_tags->define_col_value('Etape','0');
	$obj_vimofy_tags->define_col_value('objet','icode');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_tags->define_order_column('Tag',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_tags->define_vimofy_action(__ON_ADD__,__AFTER__,'vimofy2_tags',Array('maj_nbr_param(\'vimofy_lst_tag_objassoc\');'));
	$obj_vimofy_tags->define_vimofy_action(__ON_DELETE__,__AFTER__,'vimofy2_tags',Array('maj_nbr_param(\'vimofy_lst_tag_objassoc\');'));
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_tags->define_color_mask("FFFFFF","CCCCCC","AAAAAA","000","FFF");
	$obj_vimofy_tags->define_color_mask("EEEEEE","BBBBBB","888888","000","DDD");
	//==================================================================
?>