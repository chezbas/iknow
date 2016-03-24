<?php 
	$vimofy_id = 'vimofy2_tags';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_tags = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$sql = '	SELECT 
					`Tag`,
					`Groupe`,
					`IdTag`,
					`ID`,
					`Version`,
					`objet`
				FROM
					`'.$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name'].'` 
				WHERE 1 = 1
					AND `ID` = '.$_SESSION[$ssid]['id_temp'].' 
					AND `objet` = "icode"
					AND `version` = '.$_SESSION[$ssid]['objet_icode']->get_version();
	$obj_vimofy_tags->define_query($sql);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tags->define_size(100,'%',100,'%');											
	$obj_vimofy_tags->define_nb_line(50);													
	$obj_vimofy_tags->define_readonly(__R__);												// Read & Write
	$obj_vimofy_tags->define_theme('grey');													// Define default style
	$obj_vimofy_tags->define_background_logo('images/back_tags.png','repeat');				// Define background logo
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
		//==================================================================
		
		//==================================================================
		// define column : tag
		//==================================================================
		$obj_vimofy_tags->define_column('Tag',$_SESSION[$ssid]['message'][39],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
		
	//==================================================================
			
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_tags->define_order_column('Tag',1,__DESC__);				
	//==================================================================
	
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_tags->define_color_mask("FFFFFF","CCCCCC","AAAAAA","000","FFF");
	$obj_vimofy_tags->define_color_mask("EEEEEE","BBBBBB","888888","000","DDD");
	//==================================================================
?>