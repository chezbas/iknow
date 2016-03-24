<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet updating mode external tags in header
 ====================================================================*/

	$vimofy_id = 'vimofy_tags_ext';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_tag = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag` AS 'Tag',
					`Groupe` AS 'Groupe',
					`ID` AS 'ID',
					`Version` AS 'Version',
					`temp` AS 'temp',
					IF( objet = 'icode',
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][4])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`),
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][58])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`)
					  ) AS 'source',
					(case Etape When 0 Then '".$_SESSION[$ssid]['message'][41]."' else CONCAT('<a href=\"#\" onclick=\"javascript:rac_deplacer_sur_etape(',Etape,');\">".$_SESSION[$ssid]['message'][69]." ',Etape,'</a>') end) link,
					`Etape` AS 'Etape',
					`IdTag` AS 'IdTag' 
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."`
				WHERE 1 = 1
					AND `ID` = ".$_SESSION[$_GET['ssid']]['id_temp']." 
					AND `id_src` IS NOT NULL 
					AND `temp` = 0"; 
		
	$obj_vimofy_tag->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tag->define_size(100,'%',100,'%');											
	$obj_vimofy_tag->define_nb_line(50);		
	$obj_vimofy_tag->define_title($_SESSION[$ssid]['message'][370]);											
	$obj_vimofy_tag->define_readonly(__R__);											// Read
	$obj_vimofy_tag->define_theme('grey');												// Define default style
	$obj_vimofy_tag->define_background_logo('images/back_tags.png','repeat');			// Define background logo
	$obj_vimofy_tag->define_sep_col_row(true,false);
	$obj_vimofy_tag->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Tag Group
		//==================================================================
		$obj_vimofy_tag->define_column('Groupe',$_SESSION[$ssid]['message'][410],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Tag
		//==================================================================
		$obj_vimofy_tag->define_column('Tag',$_SESSION[$ssid]['message'][73],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Link source
		//==================================================================
		$obj_vimofy_tag->define_column('source',$_SESSION[$ssid]['message'][411],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Link step
		//==================================================================
		$obj_vimofy_tag->define_column('link',$_SESSION[$ssid]['message'][405],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
			
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_tag->define_input_focus('Groupe');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_tag->define_order_column('Tag',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_tag->define_color_mask("FFFFFF","CCCCCC","AAAAAA","999","888");
	$obj_vimofy_tag->define_color_mask("EEEEEE","BBBBBB","888888","888","666");
	//==================================================================
?>