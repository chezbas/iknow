<?php 

	$vimofy_id = 'vim_varin_tiny';
	
	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),'../../../../../../vimofy/');
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	/**==================================================================
	 * Create the query
	 ====================================================================*/	
	if($_GET['id_step'] == 0)
	{
		// Header
		$query = "SELECT CONCAT('<span class=\"BBVarIn\">',Nom,'</span>') as Nom,
					description, 
					'".$_SESSION[$ssid]['message'][56]."' as Etape,
					`COMMENTAIRE`, 
					`DEFAUT`, 
					`NEUTRE`
			 	FROM 
			 		`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
			 	WHERE 1 = 1
			 		AND `id_fiche` = ".$_SESSION[$ssid]['id_temp']." 
			 		AND `temp` = 0 
			 		AND `type` = 'IN'"; 
	}
	else
	{
		// Step
		$query = "	SELECT CONCAT(
									(
									SELECT 
										CASE id_action
											WHEN 0
											THEN '<span class=\"BBVarIn\">'
											ELSE	(
														SELECT 
															CASE id_src 
																WHEN 0
																THEN '<span class=\"BBVarInl\">'
																ELSE '<span class=\"BBVarinExt\">'
															END
													) 
						 				END
						 			),
						`Nom`,
						'</span>') as Nom,
						`description`,
						CASE `id_action`
							WHEN '0' 
								THEN '".$_SESSION[$ssid]['message'][56]."' 
								ELSE (
										SELECT 
											CASE id_src 
												When 0 
												THEN CONCAT('".$_SESSION[$ssid]['message'][69]." ',id_action)
												ELSE CONCAT('".$_SESSION[$ssid]['message'][69]." ',id_action,' (Ex ',id_src,'\\\\',id_action_src,')') 
											END
									 ) END as Etape,
						commentaire, defaut, neutre,idp, 
						CASE `id_action`
							WHEN '0'
							THEN CONCAT('<span class=\"BBVarIn\">',nom,'</span>') else (SELECT CASE id_src When 0 then CONCAT('<span class=\"BBVarInl\">',nom,'(',id_action,')</span>') else CONCAT('<span class=\"BBVarinExt\">',nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')</span>') END)  END as sortie,
						id_action
					 FROM 
					 	`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`  
					 WHERE 1 = 1
					 	AND `id_fiche` = ".$_SESSION[$ssid]['id_temp']."
					 	AND `temp` = 0 
					 	AND `id_action` <> ".$_GET['id_step'];
	}	
	
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
		$obj_vimofy_varin->define_column('Nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	

		/**==================================================================
		 * Description
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Etape
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('Etape',$_SESSION[$ssid]['message'][69],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
	
		/**==================================================================
		 * Commentaire
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('commentaire',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Défaut
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('defaut',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_defaut\"></div>".$_SESSION[$ssid]['message'][399],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Neutre
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('neutre',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_neutre\"></div>".$_SESSION[$ssid]['message'][400],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Retour
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varin->define_column('sortie',$_SESSION[$ssid]['message'][401],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
	/*===================================================================*/	
	
	/**==================================================================
	 * Define action
	 ====================================================================*/
	$obj_vimofy_varin->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,$vimofy_id,Array("vimofy_tiny_insert_value(Vimofy.".$vimofy_id.".col_return_last_value,'".$_SESSION[$ssid]['message'][69]."');"));		
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_varin->define_col_return('sortie');
	/*===================================================================*/	
	
	/**==================================================================
	 * Define order
	 ====================================================================*/
	$obj_vimofy_varin->define_order_column('Etape',1,__DESC__);
	$obj_vimofy_varin->define_order_column('Nom',2,__ASC__);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define color mask
	 ====================================================================*/	
	$obj_vimofy_varin->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_varin->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	
?>