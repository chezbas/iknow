<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iCode in update mode : List of input variables
 ====================================================================*/

	$vimofy_id = 'vimofy2_varin';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = 	"	SELECT
						(
							SELECT 
								CASE IFNULL(length(`DEFAUT`),0)+IFNULL(length(`NEUTRE`),0) 
									WHEN 0
									THEN (CONCAT('[b][color=#FF0000]',`nom`,'[/color][/b]')) 
									ELSE (CONCAT('[color=#FF0000]',`nom`,'[/color]'))
								END
						)				AS 'nom',
						`DESCRIPTION`	AS 'DESCRIPTION', 
						`ID`			AS 'ID',
						`Version`		AS 'Version',
						`TYPE`			AS 'TYPE',
						`DEFAUT`		AS 'DEFAUT', 
						`NEUTRE`		AS 'NEUTRE', 
						`COMMENTAIRE`	AS 'COMMENTAIRE',
						(
							SELECT
								CASE IFNULL(length(`DEFAUT`),0)+IFNULL(length(`NEUTRE`),0) 
									WHEN 0
									THEN '[img]images/obligatoire.png[/img]' 
									ELSE '' 
								END
						)				AS 'obligatoire',
						`IDP`			AS 'IDP'
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 
					WHERE 1 = 1
						AND `ID` = ".$_SESSION[$ssid]['objet_icode']->get_id_temp()." 
						AND `TYPE` = 'IN'";
	
	$obj_vimofy_varin->define_query($query);
	//==================================================================
		
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varin->define_size(100,'%',100,'%');											
	$obj_vimofy_varin->define_nb_line(50);													
	$obj_vimofy_varin->define_readonly(__RW__);												// Read & Write
	$obj_vimofy_varin->define_theme('red');													// Define default style
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
		$obj_vimofy_varin->define_column('obligatoire',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"obligatoire\"></div>".$ikn_txt[93],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
		
		//==================================================================
		// define column : name
		//==================================================================
		$obj_vimofy_varin->define_column('nom',$ikn_txt[92],__BBCODE__,__WRAP__,__LEFT__);		

		// Match code for column name
		$obj_vimofy_varin->define_lov("	SELECT 
											DISTINCT `NOM`, `description` 
										FROM
											`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`		 
										WHERE 1 = 1 
											AND	`max` = 1
									  	UNION 
									  	SELECT
									  		DISTINCT `NOM`, `description`
									  	FROM
									  		`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`
									  	WHERE 1 = 1
									  		AND `TYPE` IN('OUT','IN') 
									  		AND `max` = 1 
									  	UNION 
									  	SELECT DISTINCT `NOM`, `description` 
									 	FROM
									 		`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
										WHERE 1 = 1 
									 		AND `id` = ".$_SESSION[$ssid]['id_temp'],
									 $_SESSION[$ssid]['message'][115],'nom');
		$obj_vimofy_varin->define_column_lov('NOM',$ikn_txt[92],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov('description',$ikn_txt[47],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('NOM',1,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : description
		//==================================================================
		$obj_vimofy_varin->define_column('DESCRIPTION',$ikn_txt[47],__BBCODE__,__WRAP__,__LEFT__);
		
		// Match code for column description
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
									 $_SESSION[$ssid]['message'][114],'description');	
	
		$obj_vimofy_varin->define_column_lov('NOM',$ikn_txt[92],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov('description',$ikn_txt[47],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('description',1,__ASC__);
		//==================================================================
		
		//==================================================================
		// define column : comments
		//==================================================================
		$obj_vimofy_varin->define_column('COMMENTAIRE',$ikn_txt[96],__BBCODE__,__WRAP__,__LEFT__);
		
		// Match code for column comments
		$obj_vimofy_varin->define_lov("SELECT DISTINCT `Commentaire`
										FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."	 
										WHERE 1 = 1
										AND `max` = 1
										AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  UNION 
								  SELECT DISTINCT `Commentaire`
								  		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."
								  		WHERE 1 = 1
								  		AND `TYPE` IN('OUT','IN') 
								  		AND `max` = 1 
								  		AND `nom` LIKE '||TAGLOV_nom**NOM||' 
								  UNION 
								  SELECT DISTINCT `Commentaire`
								 		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."
										WHERE 1 = 1 
								 		AND `id` = ".$_SESSION[$ssid]['id_temp']." 
								 		AND `nom` LIKE '||TAGLOV_nom**NOM||'",
								 $_SESSION[$ssid]['message'][113],'Commentaire');	
	
		$obj_vimofy_varin->define_column_lov('Commentaire',$ikn_txt[96],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('Commentaire',1,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Neutral value
		//==================================================================
		$obj_vimofy_varin->define_column('NEUTRE',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_neutre\"></div>".$ikn_txt[95],__TEXT__,__WRAP__,__LEFT__);
		
		// Match code for column of neutral values
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
										$_SESSION[$ssid]['message'][116],'NEUTRE');	
	
		$obj_vimofy_varin->define_column_lov('NEUTRE',$ikn_txt[95],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('NEUTRE',1,__ASC__);
		//==================================================================

		//==================================================================
		// define column : Default value
		//==================================================================
		$obj_vimofy_varin->define_column('DEFAUT',"<div style=\"height:20px;width:20px;float:left;margin-right:5px;\" class=\"icn_defaut\"></div>".$ikn_txt[94],__TEXT__,__WRAP__,__LEFT__);
		
		// Match code for column of default values
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
									$_SESSION[$ssid]['message'][117],'DEFAUT');	
	
		$obj_vimofy_varin->define_column_lov('DEFAUT',$ikn_txt[94],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_varin->define_column_lov_order('DEFAUT',1,__ASC__);
		//==================================================================

	$obj_vimofy_varin->define_column('Version','',__TEXT__,__WRAP__,__LEFT__,__PERCENT__,__HIDE__);
	//==================================================================
		
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_varin->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']);
	
	// Table key
	$obj_vimofy_varin->define_key(Array('IDP','ID','TYPE'));
	
	// Columns attribut
	$obj_vimofy_varin->define_rw_flag_column('obligatoire',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('nom',__REQUIRED__);
	$obj_vimofy_varin->define_rw_flag_column('DESCRIPTION',__REQUIRED__);
	
	// Columns predefined values
	$obj_vimofy_varin->define_col_value('ID',$_SESSION[$ssid]['id_temp']);
	$obj_vimofy_varin->define_col_value('Version',$_SESSION[$ssid]['objet_icode']->get_version());
	$obj_vimofy_varin->define_col_value('TYPE','IN');
	//==================================================================
		
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_varin->define_input_focus('nom');
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_varin->define_vimofy_action(__ON_ADD__,__AFTER__,'vimofy2_varin',Array('maj_nbr_param(\'vimofy_liste_param\');'));
	$obj_vimofy_varin->define_vimofy_action(__ON_DELETE__,__AFTER__,'vimofy2_varin',Array('maj_nbr_param(\'vimofy_liste_param\');'));
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varin->define_order_column('nom',1,__DESC__);					
	//==================================================================
	
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varin->define_color_mask("EEDDDD","CC9999","eeb289","000","FFF");
	$obj_vimofy_varin->define_color_mask("EEEEEE","AAAAAA","ee8844","000","DDD");
	//==================================================================
?>