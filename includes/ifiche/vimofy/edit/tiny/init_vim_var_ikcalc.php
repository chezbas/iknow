<?php 

	$vimofy_id = 'vim_varin_tiny';
	
	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),'../../../../../../vimofy/');
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	/**==================================================================
	 * Create the query
	 ====================================================================*/	
	$query = "SELECT CONCAT((SELECT CASE `TYPE` 
					WHEN 'IN' THEN CONCAT('<span class=\"BBVarIn\">',nom)
					WHEN 'OUT' THEN (
										SELECT CASE `id_action` 
											WHEN ".$_GET['id_step']." THEN CONCAT('<span class=\"BBVarOut\">',nom)
											ELSE CONCAT('<span class=\"BBVarInl\">',nom,'(',id_action,')') 
									END)
					ELSE (
										SELECT CASE `id_action` 
											WHEN ".$_GET['id_step']." THEN CONCAT('<span class=\"BBVarExt\">',nom,'(',id_src,'\\\\',id_action_src,')')
											ELSE CONCAT('<span class=\"BBVarinExt\">',nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')')
									END)
				END),'</span>') as nom,
				-- Step ----------------------------------------------------
				(CASE id_action
								   WHEN '0' then 'Global' 
								   else (SELECT CASE id_src When 0 then CONCAT('Etape ',id_action) else CONCAT('Etape ',id_action,' (Ex ',id_src,'\\\\',id_action_src,')') END) END) as etape,
				-- Return ----------------------------------------------------
				CONCAT('$',(SELECT CASE `TYPE` 
					WHEN 'IN' THEN CONCAT(nom,'()')
					WHEN 'OUT' THEN (
										SELECT CASE `id_action` 
											WHEN ".$_GET['id_step']." THEN nom
											ELSE CONCAT(nom,'(',id_action,')')
									END)
					ELSE (
										SELECT CASE `id_action` 
											WHEN ".$_GET['id_step']." THEN CONCAT(nom,'(',id_src,'\\\\',id_action_src,')')
											ELSE CONCAT(nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')')
									END)
				END),'$') as sortie
				
				
				FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." 
 				WHERE 1 = 1 
 				AND id_fiche = ".$_SESSION[$ssid]['id_temp']." 
 				AND temp = 0 
 				AND (type = 'IN' OR type = 'OUT' OR type = 'EXTERNE')";
	
	$obj_vimofy_varin->define_query($query);
	/*===================================================================*/	
	
	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_varin->define_size(100,'%',100,'%');	
	$obj_vimofy_varin->define_mode(__NMOD__,__SIMPLE__);										
	$obj_vimofy_varin->define_nb_line(50);													
	$obj_vimofy_varin->define_readonly(__R__);																// Read & Write
	$obj_vimofy_varin->define_theme('red');																	// Define default style
	$obj_vimofy_varin->define_title($_SESSION[$ssid]['message'][59]);										// Define title
	$obj_vimofy_varin->define_background_logo('../../../../../../images/back_varin.png','repeat');			// Define background logo
	$obj_vimofy_varin->define_sep_col_row(true,false);
	$obj_vimofy_varin->define_title_display(false);
	$obj_vimofy_varin->define_page_selection_display(false,true);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define columns
	 ====================================================================*/	
		
		/**==================================================================
		 * Nom
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	

		/**==================================================================
		 * Etape
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('etape',$_SESSION[$ssid]['message'][69],__BBCODE__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Sortie
		 ====================================================================*/	
		// COLUMN	
		//$obj_vimofy_varin->define_column('sortie',$_SESSION[$ssid]['message'][69],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
	
		
	/*===================================================================*/	
	
	/**==================================================================
	 * Define action
	 ====================================================================*/
	$obj_vimofy_varin->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,$vimofy_id,Array("insert_var(Vimofy.".$vimofy_id.".col_return_last_value);"));		
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_varin->define_col_return('nom');
	/*===================================================================*/	
	
	/**==================================================================
	 * Define order
	 ====================================================================*/
	$obj_vimofy_varin->define_order_column('etape',1,__DESC__);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define color mask
	 ====================================================================*/	
	$obj_vimofy_varin->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_varin->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	
?>