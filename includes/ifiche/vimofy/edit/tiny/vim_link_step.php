<?php 

	$vimofy_id = 'vim_link_step_tiny';

	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),'../../../../../../vimofy/');
	
	// Create a reference to the session
	$obj_vimofy_link_step = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	
	
	/**==================================================================
	 * Create the query
	 ====================================================================*/	
	$obj_vimofy_link_step->define_query($_SESSION[$ssid]['objet_fiche']->generer_liste_etapes($_GET['id_step']));
	/*===================================================================*/	
	
	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_link_step->define_size(100,'%',100,'%');	
	$obj_vimofy_link_step->define_mode(__NMOD__,__SIMPLE__);										
	$obj_vimofy_link_step->define_nb_line(50);													
	$obj_vimofy_link_step->define_readonly(__R__);																// Read & Write
	$obj_vimofy_link_step->define_theme('green');																// Define default style
	$obj_vimofy_link_step->define_title($_SESSION[$ssid]['message'][59]);										// Define title
	$obj_vimofy_link_step->define_background_logo('../../../../../../images/back_varin.png','repeat');			// Define background logo
	$obj_vimofy_link_step->define_sep_col_row(true,false);
	$obj_vimofy_link_step->define_title_display(false);
	$obj_vimofy_link_step->define_page_selection_display(false,true);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define columns
	 ====================================================================*/	
		
		/**==================================================================
		 * URL label to insert in text
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_link_step->define_column('Etape',$_SESSION[$ssid]['message'][69],__TEXT__,__WRAP__,__LEFT__);
		/*===================================================================*/	
	
		/**==================================================================
		 * Titre
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_link_step->define_column('title',$_SESSION[$ssid]['message'][47],__BBCODE__,__WRAP__,__LEFT__);
		/*===================================================================*/	
		
		/**==================================================================
		 * step number
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_link_step->define_column('step',$_SESSION[$ssid]['message'][44],__BBCODE__,__WRAP__,__LEFT__);
		/*===================================================================*/		
	/*===================================================================*/	
	
	/**==================================================================
	 * Define action
	 ====================================================================*/
	$obj_vimofy_link_step->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,$vimofy_id,Array("vimofy_tiny_insert_value(Vimofy.".$vimofy_id.".col_return_last_value,'".$_SESSION[$ssid]['message'][69]."');"));		
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_link_step->define_col_return('step');
	/*===================================================================*/	
	
	/**==================================================================
	 * Define order
	 ====================================================================*/
	$obj_vimofy_link_step->define_order_column('step',1,__ASC__);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define color mask
	 ====================================================================*/	
	$obj_vimofy_link_step->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_link_step->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	
	
?>