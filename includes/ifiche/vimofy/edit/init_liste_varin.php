<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet VARIN in update mode
 ====================================================================*/

	$vimofy_id = 'vimofy2_varin';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					(SELECT
						CASE IFNULL(length(`DEFAUT`),0)+IFNULL(length(`NEUTRE`),0)
							WHEN 0
							THEN '<img src=\\\"images/obligatoire.png\\\"/>'
							ELSE '' 
						END)
					AS 'obligatoire',
					(SELECT
						CASE IFNULL(length(`DEFAUT`),0)+IFNULL(length(`NEUTRE`),0)
						WHEN 0
						THEN (CONCAT('<span class=\"BBVarInvimb\">',`nom`,'</span>'))
						ELSE (CONCAT('<span class=\"BBVarInvim\">',`nom`,'</span>'))
						END)
					AS 'nom',
					`description` AS 'description', 
					`commentaire` AS 'commentaire',
					`defaut` AS 'defaut',
					`neutre` AS 'neutre',
					`IDP` AS 'IDP',
					`id_fiche` AS 'id_fiche',
					`num_version` AS 'num_version',
					`TYPE` AS 'TYPE'
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
				WHERE 1 = 1
					AND `TYPE`		= 'IN' 
					AND `temp`		= 0 
					AND `id_fiche`	= ".$_SESSION[$ssid]['id_temp']." 
			 ";

	$obj_vimofy_varin->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varin->define_size(100,'%',100,'%');											
	$obj_vimofy_varin->define_nb_line(50);													
	$obj_vimofy_varin->define_readonly(__RW__);												// Read & Write
	$obj_vimofy_varin->define_theme('red');													// Define default style
	$obj_vimofy_varin->define_title(mysql_protect($_SESSION[$ssid]['message'][59]));						// Define title
	$obj_vimofy_varin->define_background_logo('images/back_varin.png','repeat');			// Define background logo
	$obj_vimofy_varin->define_sep_col_row(true,false);
	$obj_vimofy_varin->define_title_display(false);
	$obj_vimofy_varin->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : Mandatory
		//==================================================================
		$obj_vimofy_varin->define_column('obligatoire',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"obligatoire\"></div>".$_SESSION[$ssid]['message'][388],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Name
		//==================================================================
		$obj_vimofy_varin->define_column('nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);		
		//==================================================================
		
		// Define LOV query
		$sql_lov = "	SELECT
							DISTINCT
								`NOM`,
								`description` 
						FROM 
							`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` fp 
						WHERE 1 = 1 
							AND fp.`TYPE` IN('OUT','IN') 
							AND `temp` = 0 
							AND `max` = 1 
							AND `id_action_src` = 0 
						UNION
						SELECT
							DISTINCT
								`NOM`,
								`description` 
							FROM
								`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` fp 
							WHERE 1 = 1 
								AND fp.`TYPE` IN('OUT','IN') 
								AND `max` = 1
						UNION
						SELECT
							DISTINCT
								`NOM`,
								`description` 
						FROM
							`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` fp 
						WHERE 1 = 1 
							AND fp.`TYPE` IN('OUT','IN') 
							AND `temp` = 0 
							AND `id_action_src` = 0 
							AND `id_fiche` = ".$_SESSION[$_GET['ssid']]['id_temp'];

		$obj_vimofy_varin->define_lov($sql_lov,$_SESSION[$ssid]['message'][424],'NOM');

		// Define LOV Columns
		$obj_vimofy_varin->define_column_lov('NOM',$_SESSION[$ssid]['message'][192],__BBCODE__,__WRAP__,__CENTER__);
		$obj_vimofy_varin->define_column_lov('description',$_SESSION[$ssid]['message'][53],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('NOM',1,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Description
		//==================================================================
		$obj_vimofy_varin->define_column('description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);
		
		// Define matchcode
		$obj_vimofy_varin->define_lov("	SELECT
											DISTINCT
											`NOM`,
											`description` 
										FROM 
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
										WHERE 1 = 1
											AND `max` = 1
											AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								 	 	UNION 
										SELECT
											DISTINCT
											`NOM`,
											`description`
									  	FROM
									  		`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
									  	WHERE 1 = 1
									  		AND `TYPE` IN('OUT','IN') 
									  		AND `max` = 1 
									  		AND `nom` LIKE '||TAGLOV_nom**NOM||' 
										UNION 
										SELECT
											DISTINCT
											`NOM`,
											`description` 
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
										WHERE 1 = 1 
											AND `id` = ".$_SESSION[$ssid]['id_temp']." 
											AND `nom` LIKE '||TAGLOV_nom**NOM||'",
								 		$_SESSION[$ssid]['message'][53],'description');	
	
		$obj_vimofy_varin->define_column_lov('NOM',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov('description',$_SESSION[$ssid]['message'][53],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('description',1,__ASC__);
		//==================================================================
			
		//==================================================================
		// define column : Comment
		//==================================================================
		$obj_vimofy_varin->define_column('commentaire',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);
		
		// Define matchcode
		$obj_vimofy_varin->define_lov("	SELECT
											DISTINCT
											`Commentaire`
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
										WHERE 1 = 1
											AND `max` = 1
											AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`Commentaire`
								  		FROM
								  			`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
								  		WHERE 1 = 1
								  			AND `TYPE` IN('OUT','IN') 
								  			AND `max` = 1 
								  			AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`Commentaire`
								 		FROM
								 			`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
										WHERE 1 = 1 
								 			AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 			AND `nom` LIKE '||TAGLOV_nom**NOM||'",
								 		$_SESSION[$ssid]['message'][386],'Commentaire');	
	
		$obj_vimofy_varin->define_column_lov('Commentaire',$_SESSION[$ssid]['message'][386],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('Commentaire',1,__ASC__);
		//==================================================================
		
		//==================================================================
		// define column : Neutral value
		//==================================================================
		$obj_vimofy_varin->define_column('neutre',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_neutre\"></div>".$_SESSION[$ssid]['message'][400],__TEXT__,__WRAP__,__LEFT__);	

		// Define matchcode
		$obj_vimofy_varin->define_lov("	SELECT
											DISTINCT
											`NEUTRE`
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`		 
										WHERE 1 = 1
											AND `max` = 1
											AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`NEUTRE`
								  		FROM 
								  			`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
								  		WHERE 1 = 1
								  			AND `TYPE` IN('OUT','IN') 
								  			AND `max` = 1 
								  			AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`NEUTRE`
								 		FROM
								 			`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
										WHERE 1 = 1 
								 			AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 			AND `nom` LIKE '||TAGLOV_nom**NOM||'",
										$_SESSION[$ssid]['message'][400],'neutre');	
	
		$obj_vimofy_varin->define_column_lov('NEUTRE',$_SESSION[$ssid]['message'][400],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('NEUTRE',1,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Default value
		//==================================================================
		$obj_vimofy_varin->define_column('defaut',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_defaut\"></div>".$_SESSION[$ssid]['message'][399],__TEXT__,__WRAP__,__LEFT__);		

		// Define matchcode
		$obj_vimofy_varin->define_lov("	SELECT
											DISTINCT
											`DEFAUT`
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
										WHERE 1 = 1
											AND `max` = 1
											AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`DEFAUT`
								  		FROM
								  			`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
								  		WHERE 1 = 1
								  			AND `TYPE` IN('OUT','IN') 
								  			AND `max` = 1 
								  			AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  		UNION 
								  		SELECT
								  			DISTINCT
								  			`DEFAUT`
								 		FROM
								 			`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
										WHERE 1 = 1 
								 			AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 			AND `nom` LIKE '||TAGLOV_nom**NOM||'",
										$_SESSION[$ssid]['message'][399],'defaut');	
	
		$obj_vimofy_varin->define_column_lov('DEFAUT',$_SESSION[$ssid]['message'][399],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('DEFAUT',1,__ASC__);
		//==================================================================
				
	//==================================================================
			
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_varin->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']);
	
	// Columns attribut
	$obj_vimofy_varin->define_rw_flag_column('obligatoire',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('nom',__REQUIRED__);
	$obj_vimofy_varin->define_rw_flag_column('description',__REQUIRED__);
	
	// Table key
	$obj_vimofy_varin->define_key(Array('IDP','id_fiche','num_version','TYPE'));
	
	// Columns predefined values
	$obj_vimofy_varin->define_col_value('id_fiche',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_varin->define_col_value('num_version',$_SESSION[$ssid]['objet_fiche']->get_version());
	$obj_vimofy_varin->define_col_value('TYPE','IN');
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_varin->define_vimofy_action(__ON_UPDATE__,__AFTER__,$vimofy_id,Array('maj_vimofy_param=true;'));
	$obj_vimofy_varin->define_vimofy_action(__ON_ADD__,__AFTER__,'vimofy2_varin',Array('maj_nbr_param(\'vimofy_liste_param\');'));
	$obj_vimofy_varin->define_vimofy_action(__ON_DELETE__,__AFTER__,'vimofy2_varin',Array('maj_nbr_param(\'vimofy_liste_param\');'));
	//==================================================================
	
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_varin->define_input_focus('nom');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varin->define_order_column('nom',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varin->define_color_mask("EEDDDD","CC9999","eeb289","000","FFF");
	$obj_vimofy_varin->define_color_mask("EEEEEE","AAAAAA","ee8844","000","DDD");
	//==================================================================
?>