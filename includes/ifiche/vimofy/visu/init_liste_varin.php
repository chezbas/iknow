<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet in display mode : List of global iSheet input varible ( header )
 ====================================================================*/

	$vimofy_id = 'vimofy2_varin';

	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varin = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	// Recover Varin
	// SIBY_VARIN_LISHA_THEME
	//==================================================================
		
	switch($_SESSION[$ssid]['objet_fiche']->get_ik_valmod())
	{
		case 0:
			// No automatic default or neutral valorization set
			$resultat = 'CONCAT("[b][color=#FF0000]",`RESULTAT`,"[/color][/b]")';
			
			$valorised = 'IF(
   				length('.$resultat.') > 30 -- Ugly SIBY result from strlen of [b][color=#FF0000] + [/color][/b] // ISHEET_VARIN_VALORIZATION_FLAG
   				,"[img]images/1valorised.png[/img]"
   				,"[img]images/0unvalorised.png[/img]"
   				)';
			
			
			$defaut = "IF(
							`resultat` = `DEFAUT`,
							(CONCAT('<a class=\"lien_val_vimofy_val\" href=\"#\" onclick=\"set_default_neutre_value(\'DEFAUT\',',IDP,');\">',DEFAUT,'</a>')),
							(CONCAT('<a class=\"lien_val_vimofy_non_val\" href=\"#\" onclick=\"set_default_neutre_value(\'DEFAUT\',',IDP,');\">',DEFAUT,'</a>'))
						 ) as defaut";
			 
			$neutre = 'IF(
 							(
 								(
 								IFNULL(`resultat`,"123_NULL_123") <> "123_NULL_123" 
		 						AND IFNULL(length(`resultat`),0) > 0 
		 						AND `resultat` = `neutre`)
		 					)
		 					,'.value_param_vim('lien_val_vimofy_val','neutre').'
		 					,'.value_param_vim('lien_val_vimofy_non_val','neutre').') as neutre';
						
			break;
		case 1:
			// Only default value included in final result
			$resultat = 'IF (
					( LENGTH(IFNULL(`DEFAUT`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
					,CONCAT("[i][color=#FF0000]",`DEFAUT`,"[/color][/i]")
					,CONCAT("[b][color=#FF0000]",`RESULTAT`,"[/color][/b]")
				)';
			

			$valorised = 'IF(
   				length('.$resultat.') > 30 -- Ugly SIBY result from strlen of [b][color=#FF0000] + [/color][/b] // ISHEET_VARIN_VALORIZATION_FLAG
   				,"[img]images/1valorised.png[/img]"
   				,"[img]images/0unvalorised.png[/img]"
   				)';
			
 			$defaut = 'IF(
 						(IFNULL(defaut,"123_NULL_123") <> "123_NULL_123" 
 						AND defaut <> "" 
 						AND (IFNULL(resultat,"123_NULL_123") = "123_NULL_123" OR IFNULL(length(resultat),0) = 0)) 
 						OR ((IFNULL(resultat,"123_NULL_123") <> "123_NULL_123" 
 						AND IFNULL(length(resultat),0) > 0 
 						AND resultat = defaut))
 						,'.value_param_vim('lien_val_vimofy_val','defaut').'
 						,'.value_param_vim('lien_val_vimofy_non_val','defaut').') AS defaut';
  			$neutre = 'IF(IFNULL(resultat,"123_NULL_123") <> "123_NULL_123" 
 						AND IFNULL(length(resultat),0) > 0 
 						AND resultat = neutre
 						OR neutre = defaut
 						,'.value_param_vim('lien_val_vimofy_val','neutre').'
 						,'.value_param_vim('lien_val_vimofy_non_val','neutre').') AS neutre';

  			break;
		case 2:
			// Only neutral value included in final result
			$resultat = 'IF (
								( LENGTH(IFNULL(`NEUTRE`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
								,CONCAT("[i][color=#FF0000]",`NEUTRE`,"[/color][/i]")
								,CONCAT("[b][color=#FF0000]",`RESULTAT`,"[/color][/b]")
							)';
			
			$valorised = 'IF(
   				length('.$resultat.') > 30 -- Ugly SIBY result from strlen of [b][color=#FF0000] + [/color][/b] // ISHEET_VARIN_VALORIZATION_FLAG
   				,"[img]images/1valorised.png[/img]"
   				,"[img]images/0unvalorised.png[/img]"
   				)';
			
			$defaut = 'IF(IFNULL(resultat,"123_NULL_123") <> "123_NULL_123" 
 						AND IFNULL(length(resultat),0) > 0 
 						AND resultat = defaut
 						OR neutre = defaut
 						,'.value_param_vim('lien_val_vimofy_val','defaut').'
 						,'.value_param_vim('lien_val_vimofy_non_val','defaut').') as defaut';
   			$neutre = 'IF(
 						(IFNULL(neutre,"123_NULL_123") <> "123_NULL_123" 
 						AND neutre <> "" 
 						AND (IFNULL(resultat,"123_NULL_123") = "123_NULL_123" OR IFNULL(length(resultat),0) = 0)) 
 						OR ((IFNULL(resultat,"123_NULL_123") <> "123_NULL_123" 
 						AND IFNULL(length(resultat),0) > 0
 						AND resultat = neutre))
 						,'.value_param_vim('lien_val_vimofy_val','neutre').'
 						,'.value_param_vim('lien_val_vimofy_non_val','neutre').') as neutre '; 
   			
  			break;
		case 3:
			// Both automatic valorization : Neutral then default
			$resultat = 'IF (
								( LENGTH(IFNULL(`NEUTRE`,"")) > 0 OR LENGTH(IFNULL(`DEFAUT`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
								,IF(LENGTH(IFNULL(`NEUTRE`,"")) > 0
									,CONCAT("[i][color=#FF0000]",`NEUTRE`,"[/color][/i]")
									,CONCAT("[i][color=#FF0000]",`DEFAUT`,"[/color][/i]")
								   )
								,CONCAT("[b][color=#FF0000]",`RESULTAT`,"[/color][/b]")
							)';
			
			$valorised = 'IF(
   				LENGTH('.$resultat.') > 30 -- Ugly SIBY result from strlen of [b][color=#FF0000] + [/color][/b] // ISHEET_VARIN_VALORIZATION_FLAG
   				,"[img]images/1valorised.png[/img]"
   				,"[img]images/0unvalorised.png[/img]"
   				)';
			
			$defaut = 'IF((defaut = resultat) OR (IFNULL(neutre,"123_NULL_123") = "123_NULL_123" OR neutre = "" AND defaut <> "" AND (IFNULL(length(resultat),0) = 0 OR IFNULL(resultat,"123_NULL_123") = "123_NULL_123")) OR DEFAUT = NEUTRE,'.value_param_vim('lien_val_vimofy_val','defaut').'
 						,'.value_param_vim('lien_val_vimofy_non_val','defaut').') as defaut';
			
   			$neutre = 'IF(
 						(IFNULL(neutre,"123_NULL_123") <> "123_NULL_123" 
 						AND neutre <> "" 
 						AND (IFNULL(resultat,"123_NULL_123") = "123_NULL_123" OR IFNULL(length(resultat),0) = 0)) 
 						OR ((IFNULL(resultat,"123_NULL_123") <> "123_NULL_123" 
 						AND IFNULL(length(resultat),0) > 0
 						AND resultat = neutre))
 						,'.value_param_vim('lien_val_vimofy_val','neutre').'
 						,'.value_param_vim('lien_val_vimofy_non_val','neutre').') as neutre ';   			
			break;
	}
	
		
		$query = "	SELECT (
							SELECT
								CASE
									LENGTH(CONCAT(IFNULL(`DEFAUT`,\"\"),IFNULL(`NEUTRE`,\"\"))) 
									WHEN 0
									THEN \"<img src=\\\"images/obligatoire.png\\\"/>\" 
									ELSE ''
								END
							) AS 'obligatoire',
			(SELECT CASE IFNULL(length(DEFAUT),0)+IFNULL(length(NEUTRE),0) WHEN 0 then (CONCAT('<span class=\"BBVarInvimb\">',nom,'</span>')) else (CONCAT('<span class=\"BBVarInvim\">',nom,'</span>')) END) as nom,
			description,
			".$resultat." AS 'resultat',
			`commentaire`,
			".$defaut.",
			".$neutre.",IDP,id_fiche,num_version,TYPE,".$valorised." AS 'valorised'
			FROM
				`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`
			WHERE 1 = 1
				AND `TYPE`		= 'IN' 
				AND `temp`		= 0 
				AND id_fiche	= ".$_SESSION[$ssid]['id_temp']." 
			ORDER BY `idp`";

	//==================================================================
		
	$obj_vimofy_varin->define_query($query);
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varin->define_size(100,'%',100,'%');											
	$obj_vimofy_varin->define_nb_line(50);													
	$obj_vimofy_varin->define_readonly(__RW__);												// Read & Write
	$obj_vimofy_varin->define_theme('red');													// Define default style
	$obj_vimofy_varin->define_title($_SESSION[$ssid]['message'][59]);						// Define title
	$obj_vimofy_varin->define_background_logo('images/back_varin.png','repeat');			// Define background logo
	$obj_vimofy_varin->define_sep_col_row(true,false);
	$obj_vimofy_varin->define_title_display(false);
	$obj_vimofy_varin->define_page_selection_display(false,true);
	$obj_vimofy_varin->define_toolbar_delete_button(false);
	$obj_vimofy_varin->define_toolbar_add_button(false);
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
		// define column : valorised
		//==================================================================
		$obj_vimofy_varin->define_column('valorised',$_SESSION[$ssid]['message'][392],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
				
		//==================================================================
		// define column : name
		//==================================================================
		$obj_vimofy_varin->define_column('nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================

		//==================================================================
		// define column : description
		//==================================================================
		$obj_vimofy_varin->define_column('description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : result
		//==================================================================
		if($_SESSION[$ssid]['objet_fiche']->get_type() == 2)
		{
			// COLUMN	
			$obj_vimofy_varin->define_column('resultat',$_SESSION[$ssid]['message'][311],__BBCODE__,__WRAP__,__LEFT__);						
		}
		//==================================================================
		
		//==================================================================
		// define column : neutral
		//==================================================================
		$obj_vimofy_varin->define_column('neutre','<div style="height:20px;width:20px;float:left;margin-right:5px;" class="icn_neutre"></div>'.$_SESSION[$ssid]['message'][400],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : default
		//==================================================================
		$obj_vimofy_varin->define_column('defaut','<div style="height:20px;width:20px;float:left;margin-right:5px;" class="icn_defaut"></div>'.$_SESSION[$ssid]['message'][399],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
		
		//==================================================================
		// define column : comment
		//==================================================================
		$obj_vimofy_varin->define_column('commentaire',$_SESSION[$ssid]['message'][386],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
			
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	// Update table
	$obj_vimofy_varin->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']);
	
	// Columns attribut
	$obj_vimofy_varin->define_rw_flag_column('obligatoire',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('valorised',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('nom',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('description',__FORBIDEN__);
	if($_SESSION[$ssid]['objet_fiche']->get_type() == 2)
	{
		$obj_vimofy_varin->define_rw_flag_column('resultat',__REQUIRED__);
	}
	$obj_vimofy_varin->define_rw_flag_column('defaut',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('neutre',__FORBIDEN__);
	$obj_vimofy_varin->define_rw_flag_column('commentaire',__FORBIDEN__);
	
	// Table key
	$obj_vimofy_varin->define_key(Array('IDP','id_fiche','num_version','TYPE'));
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_varin->define_vimofy_action(__ON_UPDATE__,__AFTER__,$vimofy_id,Array('maj_vimofy_param=true;'));		
	//==================================================================
		
	//==================================================================
	// Define default input focus
	//==================================================================
	if($_SESSION[$ssid]['objet_fiche']->get_type() == 2)
	{
		$obj_vimofy_varin->define_input_focus('resultat');
	}
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
	
	/**==================================================================
	 * Add link to set value from neutral or default pool
	 ====================================================================*/	
	function value_param_vim($p_class,$p_param,$p_lien = true)
	{
		if($p_lien)
		{
			return "CONCAT('<a class=\"".$p_class."\" href=\"#\" onclick=\"set_default_neutre_value(\'".$p_param."\',',IDP,');\">',".$p_param.",'</a>')";
		}
		else
		{
			return "CONCAT('<span class=\"".$p_class."\">',".$p_param.",'</span>')";
		}
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Add link to set value from neutral or default pool
	 ====================================================================*/	
	function value_param($class,$param,$lien = true)
	{
		if($lien)
		{
			return "CONCAT('<a class=\"".$class."\" href=\"#\" onclick=\"set_default_neutre_value(\'".$param."\',',IDP,');\">',".$param.",'</a>')";
		}
		else
		{
			return "CONCAT('<span class=\"".$class."\">',".$param.",'</span>')";
		}
	}
	/*===================================================================*/	
?>