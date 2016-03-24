<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : List of tag ( header )
 ====================================================================*/


	$vimofy_id = 'vimofy_tags';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag` AS 'Tag',
					`Groupe` AS 'Groupe',
					IF(	`id_src` IS NULL,
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][536])."',' ',`Etape`),
						IF( objet = 'icode',
							CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][4])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`),
							CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][58])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`)
					  	  )
					  )
					  		AS 'src',
					(
						CASE `Etape`
							WHEN 0
							THEN '".$_SESSION[$ssid]['message'][41]."'
							ELSE
								CONCAT('<a href=\"#\" onclick=\"javascript:rac_deplacer_sur_etape(',`Etape`,');\">',`Etape`,'</a>')
						END
						)
							AS 'link',
					`Etape` AS 'Etape'
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."` 
				WHERE 1 = 1
					AND `ID` = ".$_SESSION[$_GET['ssid']]['id_temp']." 
					AND (`objet` = 'ifiche' OR (`id_src` IS NOT NULL))
			 "; 
	
	$obj_vimofy_varin->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varin->define_size(100,'%',100,'%');											
	$obj_vimofy_varin->define_nb_line(50);													
	$obj_vimofy_varin->define_readonly(__R__);											// Read & Write
	$obj_vimofy_varin->define_theme('grey');											// Define default style
	$obj_vimofy_varin->define_background_logo('images/back_tags.png','repeat');			// Define background logo
	$obj_vimofy_varin->define_sep_col_row(true,false);
	$obj_vimofy_varin->define_title_display(false);
	$obj_vimofy_varin->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Tag group
		//==================================================================
		$obj_vimofy_varin->define_column('Groupe',$_SESSION[$ssid]['message'][410],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Tag name
		//==================================================================
		$obj_vimofy_varin->define_column('Tag',$_SESSION[$ssid]['message'][73],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Origin
		//==================================================================
		$obj_vimofy_varin->define_column('src',$_SESSION[$ssid]['message'][411],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Link on step
		//==================================================================
		$obj_vimofy_varin->define_column('link',$_SESSION[$ssid]['message'][412],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Step number
		//==================================================================
		$obj_vimofy_varin->define_column('Etape',$_SESSION[$ssid]['message'][69],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================

	//==================================================================
			
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_varin->define_input_focus('Tag');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varin->define_order_column('Etape',1,__ASC__);					
	$obj_vimofy_varin->define_order_column('Tag',2,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varin->define_color_mask("FFFFFF","CCCCCC","AAAAAA","000","FFF");
	$obj_vimofy_varin->define_color_mask("EEEEEE","BBBBBB","888888","000","DDD");
	//==================================================================
?>