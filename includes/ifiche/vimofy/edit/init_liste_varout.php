<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet VAROUT in update mode
 ====================================================================*/

	$vimofy_id = 'vimofy_varout';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varout = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					(
						CASE `used`
							WHEN 0
							THEN '[img]images/out.png[/img]'
							ELSE ''
						END
					 )
					 AS 'tp',
					CONCAT('[b][color=#0000FF]',`nom`,'[/color][/b]') AS 'nom', 
					`description` AS 'description', 
					`commentaire` AS 'commentaire', 
					CONCAT('<a href=\"#\" onclick=\"javascript:rac_deplacer_sur_etape(',id_action,');\">',`id_action`,'</a>') AS 'src',
					(
						CASE `id_src`
							WHEN 0
							THEN '-'
							ELSE `id_src`
						END
					) AS 'lien',
					(
						CASE `id_action_src`
							WHEN 0
							THEN '-'
							ELSE `id_action_src`
						END
					) AS 'etape_src',
					`id_action` AS 'id_action',
					`id_fiche` AS 'id_fiche',
					`IDP` AS 'IDP',
					`TYPE` AS 'TYPE',
					`num_version` AS 'num_version'
				FROM
			  		`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`  
				WHERE 1 = 1
					AND `TYPE`		= 'OUT' 
					AND `temp` 		= 0 
					AND `id_fiche`	= ".$_SESSION[$_GET['ssid']]['id_temp']."
				UNION 
				SELECT
					(
						CASE `used`
							WHEN 1
							THEN '[img]images/outexterne.png[/img]'
							ELSE ''
						END
					) AS 'tp', 
					CONCAT('[b][color=#318CE7]',`nom`,'[/color][/b]') AS 'nom', 
					`description` AS 'description',
					`commentaire` AS 'commentaire',
					CONCAT('<a href=\"#\" onclick=\"javascript:rac_deplacer_sur_etape(',`id_action`,');\">',`id_action`,'</a>') AS 'src',
					(
						CASE `id_src`
							WHEN 0
							THEN '-'
							ELSE `id_src`
						END
					) AS 'lien',
					(
						CASE `id_action_src`
							WHEN 0
						THEN (	SELECT
									CASE `id_src`
										WHEN 0
										THEN '-'
										ELSE '".mysql_protect($_SESSION[$ssid]['message'][4])."'
									END
							 )
						ELSE `id_action_src`
						END
					) AS 'etape_src',
				`id_action` AS 'id_action',
				`id_fiche` AS 'id_fiche',
				`IDP` AS 'IDP',
				`TYPE` AS 'TYPE',
				`num_version` AS 'num_version'
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`  
				WHERE 1 = 1
					AND `TYPE`		= 'EXTERNE'
					AND `temp` 		= 0
					AND `used` 		= 1
					AND `id_fiche`	= ".$_SESSION[$_GET['ssid']]['id_temp'];
	
	$obj_vimofy_varout->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varout->define_size(100,'%',100,'%');											
	$obj_vimofy_varout->define_nb_line(50);													
	$obj_vimofy_varout->define_readonly(__R__);												// Read & Write
	$obj_vimofy_varout->define_theme('blue');												// Define default style
	$obj_vimofy_varout->define_background_logo('images/back_varout.png','repeat');			// Define background logo
	$obj_vimofy_varout->define_sep_col_row(true,false);
	$obj_vimofy_varout->define_title_display(false);
	$obj_vimofy_varout->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet link type
		//==================================================================
		$obj_vimofy_varout->define_column('tp',$_SESSION[$ssid]['message'][402],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Variable name
		//==================================================================
		$obj_vimofy_varout->define_column('nom',$_SESSION[$ssid]['message'][192],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : Variable description
		//==================================================================
		$obj_vimofy_varout->define_column('description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Variable comments
		//==================================================================
		$obj_vimofy_varout->define_column('commentaire',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Call ID provide this variable
		//==================================================================
		$obj_vimofy_varout->define_column('lien',$_SESSION[$ssid]['message'][404],__BBCODE__,__WRAP__,__CENTER__,__EXACT__);						
		//==================================================================
				
		//==================================================================
		// define column : link source
		//==================================================================
		$obj_vimofy_varout->define_column('src',$_SESSION[$ssid]['message'][412],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
				
		//==================================================================
		// define column : Step link on remote source
		//==================================================================
		$obj_vimofy_varout->define_column('etape_src',$_SESSION[$ssid]['message'][549],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
				
		//==================================================================
		// define column : Step number
		//==================================================================
		$obj_vimofy_varout->define_column('id_action',$_SESSION[$ssid]['message'][69],__BBCODE__,__WRAP__,__CENTER__,__EXACT__);						
		//==================================================================
				
	//==================================================================
	
	
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_vimofy_varout->define_input_focus('nom');
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_varout->define_key(Array('IDP','id_fiche','num_version','TYPE','id_action','etape_src'));
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varout->define_order_column('nom',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varout->define_color_mask("DDDDEE","9999CC","99c3ed","AAA","FFF");
	$obj_vimofy_varout->define_color_mask("EEEEEE","AAAAAA","6690b9","999","DDD");
	//==================================================================
?>