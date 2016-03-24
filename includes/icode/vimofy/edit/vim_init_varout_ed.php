<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of ouput variables
 ====================================================================*/

	$vimofy_id = 'vimofy2_varout';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varout = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT 
					CONCAT(
							'[b][color=#0000DD]',
							`NOM`,
							'[/color][/b]'
						  )			AS NOM, 
					`DESCRIPTION`	AS 'DESCRIPTION', 
					`COMMENTAIRE`	AS 'COMMENTAIRE',
					`IDP`			AS 'IDP',
					`ID`			AS 'ID',
					`Version`		AS 'Version',
					`TYPE`			AS 'TYPE' 
			  	FROM
			  		`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 
			  	WHERE 1 = 1
			  		AND `ID` = ".$_SESSION[$ssid]['objet_icode']->get_id_temp()." 
			  		AND `TYPE` = 'OUT'";
		
	$obj_vimofy_varout->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varout->define_size(100,'%',100,'%');											
	$obj_vimofy_varout->define_nb_line(50);													
	$obj_vimofy_varout->define_readonly(__RW__);												// Read & Write
	$obj_vimofy_varout->define_theme('blue');													// Define default style
	$obj_vimofy_varout->define_background_logo('images/back_varout.png','repeat');		// Define background logo
	$obj_vimofy_varout->define_sep_col_row(true,false);
	$obj_vimofy_varout->define_title_display(false);
	$obj_vimofy_varout->define_page_selection_display(false,true);
	//==================================================================
	
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : name
		//==================================================================
		$obj_vimofy_varout->define_column('NOM',$ikn_txt[92],__BBCODE__,__WRAP__,__CENTER__);	

		// Match code for column name
		$obj_vimofy_varout->define_lov("	SELECT
												DISTINCT
													`NOM`,
													`description` 
											FROM
												`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
											WHERE 1 = 1
												AND `max` = 1
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
											UNION 
											SELECT
												DISTINCT
													`NOM`,
													`description` 
											FROM
												`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
											WHERE 1 = 1 
												AND `id` = ".$_SESSION[$ssid]['id_temp'],
										$_SESSION[$ssid]['message'][115],'NOM');
		
		$obj_vimofy_varout->define_column_lov('NOM',$ikn_txt[92],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov('description',$ikn_txt[47],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('NOM',1,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : description
		//==================================================================
		$obj_vimofy_varout->define_column('DESCRIPTION',$ikn_txt[47],__BBCODE__,__WRAP__,__LEFT__);
		
		// Match code for column description
		$obj_vimofy_varout->define_lov("	SELECT 
												DISTINCT
													`NOM`,
													`description` 
											FROM 
												`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
											WHERE 1 = 1
												AND `max` = 1
												AND `nom` LIKE '||TAGLOV_NOM**NOM||' 
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
									  			AND `nom` LIKE '||TAGLOV_NOM**NOM||' 
									  		UNION 
									  		SELECT
									  			DISTINCT
									  				`NOM`,
									  				`description` 
									 		FROM 
									 			`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
											WHERE 1 = 1 
									 			AND `id` = ".$_SESSION[$ssid]['id_temp']." 
									 			AND `nom` LIKE '||TAGLOV_NOM**NOM||'",
										$ikn_txt[47],'description');	
	
		$obj_vimofy_varout->define_column_lov('NOM',$ikn_txt[92],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varout->define_column_lov('description',$ikn_txt[47],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('description',1,__ASC__);
		//==================================================================
					
		//==================================================================
		// define column : comments
		//==================================================================
		$obj_vimofy_varout->define_column('COMMENTAIRE',$ikn_txt[96],__BBCODE__,__WRAP__,__CENTER__);

		// Match code for column comments
		$obj_vimofy_varout->define_lov("	SELECT
												DISTINCT
													`commentaire`
											FROM 
												`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 		 
											WHERE 1 = 1
												AND `max` = 1
												AND `nom` LIKE '||TAGLOV_NOM**NOM||' 
											UNION 
											SELECT
												DISTINCT
													`commentaire`
									  		FROM 
									  			`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."` 
									  		WHERE 1 = 1
									  			AND `TYPE` IN('OUT','IN') 
									  			AND `max` = 1 
									  			AND `nom` LIKE '||TAGLOV_NOM**NOM||' 
									  		UNION 
									  		SELECT
									  			DISTINCT
									  				`commentaire`
									 		FROM 
									 			`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
											WHERE 1 = 1 
									 			AND `id` = ".$_SESSION[$ssid]['id_temp']." 
									 			AND `nom` LIKE '||TAGLOV_NOM**NOM||'",
										$_SESSION[$ssid]['message'][113],'commentaire');	
	
		$obj_vimofy_varout->define_column_lov('commentaire',$ikn_txt[96],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('commentaire',1,__ASC__);
		//==================================================================
				
		$obj_vimofy_varout->define_column('Version','',__BBCODE__,__WRAP__,__CENTER__,__PERCENT__,__HIDE__);
	//==================================================================
				
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_varout->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']);
	
	// Table key
	$obj_vimofy_varout->define_key(Array('IDP','ID','TYPE'));
	
	// Columns attribut
	$obj_vimofy_varout->define_rw_flag_column('NOM',__REQUIRED__);
	$obj_vimofy_varout->define_rw_flag_column('DESCRIPTION',__REQUIRED__);
	
	// Columns predefined values
	$obj_vimofy_varout->define_col_value('ID',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_varout->define_col_value('Version',$_SESSION[$ssid]['objet_icode']->get_version());
	$obj_vimofy_varout->define_col_value('TYPE','OUT');
	//==================================================================

	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_varout->define_input_focus('NOM');
	//==================================================================
	
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_varout->define_vimofy_action(__ON_ADD__,__AFTER__,'vimofy2_varout',Array('maj_nbr_param(\'vimofy_infos_recuperees\');'));
	$obj_vimofy_varout->define_vimofy_action(__ON_DELETE__,__AFTER__,'vimofy2_varout',Array('maj_nbr_param(\'vimofy_infos_recuperees\');'));
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varout->define_order_column('NOM',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varout->define_color_mask("DDDDEE","9999CC","99c3ed","000","FFF");
	$obj_vimofy_varout->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
?>