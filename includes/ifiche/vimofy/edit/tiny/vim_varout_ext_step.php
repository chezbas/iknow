<?php 

	$vimofy_id = 'vim_varout_tiny_ext';
	
	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),'../../../../../../vimofy/');
	
	// Create a reference to the session
	$obj_vimofy_varout = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	/**==================================================================
	 * Create the query
	 ====================================================================*/	
	$query = "SELECT (CASE `TYPE` WHEN 'OUT' THEN CONCAT('<span class=\"BBVarOut\">',nom,'</span>') ELSE CONCAT('<span class=\"BBVarExt\">',nom,'</span>') end) as Nom,description,`TYPE`,
		commentaire,(SELECT CASE `TYPE`
				When 'OUT' then 'LOCAL'
				else `TYPE` END) as tp ,(SELECT CASE `TYPE`
				When 'OUT' then ''
				else id_src END) as id_src,num_version,id_action,(CASE `TYPE` WHEN 'OUT' THEN CONCAT('<span class=\"BBVarOut\">',`Nom`,'</span>') ELSE CONCAT('<span class=\"BBVarExt\">',nom,'(',id_src,'\\\',id_action_src,')','</span>') end) as retour,num_version_src,IDP,id_fiche,id_action_src,temp
		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." 
		WHERE (`TYPE` ='EXTERNE' OR (`TYPE` ='OUT' AND id_action_src <> 0))
		AND id_fiche = ".$_SESSION[$ssid]['id_temp']." 
		AND temp = 0 AND id_action = ".$_GET['id_step'];
	
	
	$obj_vimofy_varout->define_query($query);
	/*===================================================================*/	
	
	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_varout->define_size(100,'%',100,'%');	
	$obj_vimofy_varout->define_mode(__NMOD__,__SIMPLE__);										
	$obj_vimofy_varout->define_nb_line(50);			
	$obj_vimofy_varout->define_title($_SESSION[$ssid]['message'][455]);													
	$obj_vimofy_varout->define_readonly(__R__);															// Read & Write
	$obj_vimofy_varout->define_theme('grey');																// Define default style
	$obj_vimofy_varout->define_background_logo('../../../../../../images/back_varout.png','repeat');		// Define background logo
	$obj_vimofy_varout->define_sep_col_row(true,false);
	//$obj_vimofy_varout->define_title_display(false);
	$obj_vimofy_varout->define_page_selection_display(false,true);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define columns
	 ====================================================================*/	
		/**==================================================================
		 * Nom
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varout->define_column('Nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);	

		// Define LOV query
		$sql_lov = "SELECT DISTINCT NOM,description 
						FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` fp 
						WHERE 1 = 1 
						AND fp.TYPE IN('OUT','IN') 
						AND temp = 0 
						AND `max` = 1 
						AND id_action_src = 0 
					UNION SELECT DISTINCT NOM,description 
						FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` fp 
						WHERE 1 = 1 
						AND fp.TYPE IN('OUT','IN') 
						AND `max` = 1
					UNION SELECT DISTINCT NOM,description 
						FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` fp 
						WHERE 1 = 1 
						AND fp.TYPE IN('OUT','IN') 
						AND temp = 0 
						AND id_action_src = 0 
						AND `id_fiche` = ".$_SESSION[$_GET['ssid']]['id_temp'];
		
		$obj_vimofy_varout->define_lov($sql_lov,$_SESSION[$ssid]['message'][424],'NOM');

		// Define LOV Columns
		$obj_vimofy_varout->define_column_lov('NOM','NOM',__BBCODE__,__WRAP__,__CENTER__);
		$obj_vimofy_varout->define_column_lov('description','description',__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov_order('NOM',1,__ASC__);
		/*===================================================================*/	

		/**==================================================================
		 * Description
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varout->define_column('description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);	

		// LOV
		$obj_vimofy_varout->define_lov("SELECT DISTINCT NOM,description 
										FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']." 		 
										WHERE 1 = 1
										AND `max` = 1
										AND `nom` LIKE '||TAGLOV_Nom**NOM||' 
								  UNION 
								  SELECT DISTINCT NOM,description
								  		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." 
								  		WHERE 1 = 1
								  		AND `TYPE` IN('OUT','IN') 
								  		AND `max` = 1 
								  		AND `nom` LIKE '||TAGLOV_Nom**NOM||' 
								  UNION 
								  SELECT DISTINCT NOM,description 
								 		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."
										WHERE 1 = 1 
								 		AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 		AND `nom` LIKE '||TAGLOV_Nom**NOM||'",'Description','description');	
	
		$obj_vimofy_varout->define_column_lov('NOM',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov('description',$_SESSION[$ssid]['message'][53],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov_order('description',1,__ASC__);
		/*===================================================================*/	
		
		/**==================================================================
		 * Commentaire
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varout->define_column('commentaire',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);	

		// LOV
		$obj_vimofy_varout->define_lov("SELECT DISTINCT `Commentaire`
										FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']." 		 
										WHERE 1 = 1
										AND `max` = 1
										AND `nom` LIKE '||TAGLOV_Nom**NOM||' 
								  UNION 
								  SELECT DISTINCT `Commentaire`
								  		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." 
								  		WHERE 1 = 1
								  		AND `TYPE` IN('OUT','IN') 
								  		AND `max` = 1 
								  		AND `nom` LIKE '||TAGLOV_Nom**NOM||' 
								  UNION 
								  SELECT DISTINCT `Commentaire`
								 		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."
										WHERE 1 = 1 
								 		AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 		AND `nom` LIKE '||TAGLOV_Nom**NOM||'",'Commentaire','Commentaire');	
	
		$obj_vimofy_varout->define_column_lov('Commentaire',$_SESSION[$ssid]['message'][386],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov_order('Commentaire',1,__ASC__);
		/*===================================================================*/	
		
		/**==================================================================
		 * Etape
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varout->define_column('id_action',$_SESSION[$ssid]['message'][69],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * retour
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_varout->define_column('retour',$_SESSION[$ssid]['message'][401],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
	/*===================================================================*/	
	
	/**==================================================================
	 * Define action
	 ====================================================================*/
	$obj_vimofy_varout->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,$vimofy_id,Array("vimofy_tiny_insert_value(Vimofy.".$vimofy_id.".col_return_last_value,'".$_SESSION[$ssid]['message'][69]."');"));		
	/*===================================================================*/	

	/**==================================================================
	 * UPDATE/INSERT
	 ====================================================================*/		
	// Update table
	$obj_vimofy_varout->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']);
	
	// Table key
	$obj_vimofy_varout->define_key(Array('TYPE','id_fiche','id_action','id_action_src','temp','id_src','num_version_src','IDP','num_version'));
	
	// Columns attribut
	$obj_vimofy_varout->define_rw_flag_column('id_action',__FORBIDEN__);
	$obj_vimofy_varout->define_rw_flag_column('retour',__FORBIDEN__);
	$obj_vimofy_varout->define_rw_flag_column('Nom',__REQUIRED__);
	$obj_vimofy_varout->define_rw_flag_column('description',__REQUIRED__);
	
	// Columns predefined values
	$obj_vimofy_varout->define_col_value('id_fiche',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_varout->define_col_value('id_action',$_GET['id_step']);
	$obj_vimofy_varout->define_col_value('num_version',$_SESSION[$ssid]['objet_fiche']->get_version());
	$obj_vimofy_varout->define_col_value('TYPE',"OUT");
	/*===================================================================*/		
	
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_varout->define_col_return('retour');
	/*===================================================================*/	
	
	/**==================================================================
	 * Define order
	 ====================================================================*/
	$obj_vimofy_varout->define_order_column('id_action',1,__DESC__);
	$obj_vimofy_varout->define_order_column('Nom',2,__ASC__);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define color mask
	 ====================================================================*/	
	$obj_vimofy_varout->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_varout->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	
?>