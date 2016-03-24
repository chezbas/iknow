<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : List of step tag
 ====================================================================*/

	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	


	$vimofy_id = 'vimofy_tags_local_step';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_tags = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					`Tag`		AS 'Tag',
					`Groupe`	AS 'Groupe',
					IF(	`id_src` IS NULL,
						CONCAT('".mysql_protect($_SESSION[$ssid]['message'][536])."',' ',`Etape`),
						IF( objet = 'icode',
							CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][4])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`),
							CONCAT('".mysql_protect($_SESSION[$ssid]['message'][547])." ".mysql_protect($_SESSION[$ssid]['message'][58])." ',`id_src`,' ','".mysql_protect($_SESSION[$ssid]['message'][50])."',' ',`version_src`)
					  	  )
					  )
					  			AS 'source'
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."` 
				WHERE 1 = 1
					AND `Version`	= ".$_SESSION[$ssid]['objet_fiche']->get_version()." 
					AND `temp`		= 0 
					AND `ID`		= ".$_SESSION[$ssid]['id_temp']." 
					AND `Etape`		= ".$_SESSION[$ssid]['etape_active'];
	
	$obj_vimofy_tags->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_tags->define_size(100,'%',100,'%');											
	$obj_vimofy_tags->define_nb_line(50);													
	$obj_vimofy_tags->define_readonly(__R__);											// Read & Write
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
		// define column : Tag group
		//==================================================================
		$obj_vimofy_tags->define_column('Groupe',$_SESSION[$ssid]['message'][410],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Tag name
		//==================================================================
		$obj_vimofy_tags->define_column('Tag',$_SESSION[$ssid]['message'][73],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Origin
		//==================================================================
		$obj_vimofy_tags->define_column('source',$_SESSION[$ssid]['message'][411],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================

	//==================================================================
			
	//==================================================================
	// Define default input focus
	//==================================================================
		$obj_vimofy_tags->define_input_focus('Tag');
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