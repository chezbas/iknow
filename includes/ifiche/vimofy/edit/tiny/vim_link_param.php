<?php 
	// Get active step
	if(isset($_GET['id_step']))
	{
		$active_step = $_GET['id_step'];
	}
	else
	{
		if(isset($_POST['id_step']))
		{
			$active_step = $_POST['id_step'];
		}
		else
		{
			$active_step = null;
		}
	}
	$vimofy_id = 'vim_link_param_tiny';

	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),'../../../../../../vimofy/');
	
	// Create a reference to the session
	$obj_vimofy_link_param = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	/**==================================================================
	 * Create the query
	 ====================================================================*/
	$query = 'SELECT Nom,Valeur,id_temp,ID
			  FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name'].' 
			  WHERE 1 = 1 
			  AND id_temp = '.$_SESSION[$ssid]['id_temp'];
	
	$obj_vimofy_link_param->define_query($query);
	/*===================================================================*/	
	
	/**==================================================================
	 * Get the version of the object
	 ====================================================================*/
	if(isset($_POST['object_version']))
	{
		if($_POST['object_version'] == $_SESSION[$ssid]['message']['iknow'][504])
		{
			$version_object = $_SESSION[$ssid]['objet_fiche']->get_max_version_of_object($_POST['p_type'],$_POST['object_id']);
		}
		else
		{
			$version_object = $_POST['object_version'];
		}
	}
	else
	{
		$version_object = '';
	}
	/*===================================================================*/	
	
	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_link_param->define_size(100,'%',100,'%');	
	$obj_vimofy_link_param->define_nb_line(50);													
	$obj_vimofy_link_param->define_readonly(__RW__);															// Read & Write
	if(isset($_POST['p_type']) && $_POST['p_type'] == '__IFICHE__')
	{												
		$obj_vimofy_link_param->define_theme('green');
	}
	else
	{
		$obj_vimofy_link_param->define_theme('blue');
	}																
	$obj_vimofy_link_param->define_title($_SESSION[$ssid]['message'][59]);										// Define title
	$obj_vimofy_link_param->define_background_logo('../../../../../../images/back_varin.png','repeat');			// Define background logo
	$obj_vimofy_link_param->define_sep_col_row(true,false);
	$obj_vimofy_link_param->define_title_display(false);
	$obj_vimofy_link_param->define_page_selection_display(false,true);
	/*===================================================================*/	
	
	/**==================================================================
	 * Define columns
	 ====================================================================*/	
		
		/**==================================================================
		 * Parameter
		 ====================================================================*/	
		// COLUMN	
		$obj_vimofy_link_param->define_column('Nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);
		
		// LOV
		if (isset($_POST['p_type']))
		{
			// update existing iObject
			if($_POST['p_type'] == '__IFICHE__')
			{
				// iSheet
				
				// Define LOV query
				$sql_lov = "SELECT temp1.nom as nom,(SELECT CASE IFNULL(length(temp1.DEFAUT),0)+IFNULL(length(temp1.NEUTRE),0) WHEN 0 then \"[img]../../../../../../images/obligatoire.png[/img]\" else \"\" END) as \"obligatoire\",DESCRIPTION as \"Description\"  
																		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." as temp1
																		WHERE temp1.id_fiche = ".$_POST['object_id']." 
																		AND temp1.temp = 0 
																		AND temp1.num_version = ".$version_object." 
																		AND temp1.TYPE = 'IN'
																		AND temp1.nom NOT IN(SELECT temp2.Nom FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name']." as temp2 WHERE temp2.id_temp = ".$_SESSION[$ssid]['id_temp'].")";
				$obj_vimofy_link_param->define_lov($sql_lov,str_replace('$id',$_POST['object_id'],$_SESSION[$ssid]['message'][174]),'nom');
	
				// Define LOV Columns
				$obj_vimofy_link_param->define_column_lov('obligatoire',$_SESSION[$ssid]['message'][388],__BBCODE__,__WRAP__,__CENTER__);
				$obj_vimofy_link_param->define_column_lov('nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);
				$obj_vimofy_link_param->define_column_lov('Description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);
				
				$obj_vimofy_link_param->define_column_lov_order('obligatoire',1,__DESC__);
				$obj_vimofy_link_param->define_column_lov_order('nom',2,__ASC__);
			}
			else
			{
				// iCode
				
				// Define LOV query
				$sql_lov = "SELECT temp1.nom as nom,(SELECT CASE IFNULL(length(temp1.DEFAUT),0)+IFNULL(length(temp1.NEUTRE),0) WHEN 0 then \"[img]../../../../../../images/obligatoire.png[/img]\" else \"\" END) as \"obligatoire\",DESCRIPTION as \"Description\" 
																		FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']." as temp1
																		WHERE temp1.ID = ".$_POST['object_id']." 
																		AND temp1.Version = ".$version_object." 
																		AND temp1.TYPE = 'IN' 
																		AND temp1.nom NOT IN(SELECT temp2.Nom FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name']." as temp2 WHERE temp2.id_temp = ".$_SESSION[$ssid]['id_temp'].")";
				
					
				$obj_vimofy_link_param->define_lov($sql_lov,str_replace('$id',$_POST['object_id'],$_SESSION[$ssid]['message'][174]),'nom');
					
					
				// Define LOV Columns
				$obj_vimofy_link_param->define_column_lov('obligatoire',$_SESSION[$ssid]['message'][388],__BBCODE__,__WRAP__,__CENTER__);
				$obj_vimofy_link_param->define_column_lov('nom',$_SESSION[$ssid]['message'][192],__TEXT__,__WRAP__,__LEFT__);
				$obj_vimofy_link_param->define_column_lov('Description',$_SESSION[$ssid]['message'][53],__BBCODE__,__WRAP__,__LEFT__);
				
				$obj_vimofy_link_param->define_column_lov_order('obligatoire',1,__DESC__);
				$obj_vimofy_link_param->define_column_lov_order('nom',2,__ASC__);
			}
			
			/**==================================================================
			 * Value
			 ====================================================================*/	
			// COLUMN	
			$obj_vimofy_link_param->define_column('Valeur',$_SESSION[$ssid]['message'][311],__BBCODE__,__WRAP__,__LEFT__);
			
			// Define LOV query
			$sql_lov = "SELECT CONCAT((SELECT CASE `TYPE` 
						WHEN 'IN' THEN '<span class=\"BBVarIn\">'
						WHEN 'OUT' THEN (
											SELECT CASE `id_action` 
												WHEN ".$active_step." THEN '<span class=\"BBVarOut\">'
												ELSE '<span class=\"BBVarInl\">'
										END)
						ELSE (
											SELECT CASE `id_action` 
												WHEN ".$active_step." THEN '<span class=\"BBVarExt\">' 
												ELSE '<span class=\"BBVarinExt\">' 
										END)
					END),nom,'</span>') as nom,
					-- Step ----------------------------------------------------
					(CASE id_action
									   WHEN '0' then '".str_replace("'","\'",$_SESSION[$ssid]['message'][56])."' 
									   else (SELECT CASE id_src When 0 then CONCAT('".str_replace("'","\'",$_SESSION[$ssid]['message'][69])." ',id_action) else CONCAT('".str_replace("'","\'",$_SESSION[$ssid]['message'][69])." ',id_action,' (Ex ',id_src,'\\\\',id_action_src,')') END) END) as etape,
					-- Return ----------------------------------------------------
					CONCAT('$',(SELECT CASE `TYPE` 
						WHEN 'IN' THEN CONCAT(nom,'()')
						WHEN 'OUT' THEN (
											SELECT CASE `id_action` 
												WHEN ".$active_step." THEN nom
												ELSE CONCAT(nom,'(',id_action,')')
										END)
						ELSE (
											SELECT CASE `id_action` 
												WHEN ".$active_step." THEN CONCAT(nom,'(',id_src,'\\\\',id_action_src,')')
												ELSE CONCAT(nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')')
										END)
					END),'$') as sortie
					
					
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']." 
	 				WHERE 1 = 1 
	 				AND id_fiche = ".$_SESSION[$ssid]['id_temp']." 
	 				AND temp = 0 
	 				AND (type = 'IN' OR type = 'OUT' OR type = 'EXTERNE')";
					
			$obj_vimofy_link_param->define_lov($sql_lov,str_replace('$id',$_POST['object_id'],$_SESSION[$ssid]['message'][173]),'sortie');
			
			// Define LOV Columns
			$obj_vimofy_link_param->define_column_lov('etape',$_SESSION[$ssid]['message'][69],__BBCODE__,__WRAP__,__CENTER__);
			$obj_vimofy_link_param->define_column_lov('nom',$_SESSION[$ssid]['message'][192],__BBCODE__,__WRAP__,__LEFT__);
			$obj_vimofy_link_param->define_column_lov('sortie',$_SESSION[$ssid]['message'][401],__BBCODE__,__WRAP__,__LEFT__);
			/*===================================================================*/	
			
		/*===================================================================*/	
		
		/**==================================================================
		 * Define action
		 ====================================================================*/
		$obj_vimofy_link_param->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,$vimofy_id,Array("vimofy_tiny_insert_value(Vimofy.".$vimofy_id.".col_return_last_value);"));		
		/*===================================================================*/	
		
		/**==================================================================
		 * UPDATE/INSERT
		 ====================================================================*/		
		// Update table
		$obj_vimofy_link_param->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name']);
		
		// Table key
		$obj_vimofy_link_param->define_key(Array('id_temp','ID'));
		
		// Columns attribut
		$obj_vimofy_link_param->define_rw_flag_column('Nom',__REQUIRED__);
		$obj_vimofy_link_param->define_rw_flag_column('Valeur',__REQUIRED__);
		
		// Columns predefined values
		$obj_vimofy_link_param->define_col_value('id_temp',$_SESSION[$ssid]['id_temp']);
		/*===================================================================*/		
		
		/**==================================================================
		 * Define default input focus
		 ====================================================================*/
		$obj_vimofy_link_param->define_input_focus('Nom');
		/*===================================================================*/	
		
		/**==================================================================
		 * Define order
		 ====================================================================*/
		$obj_vimofy_link_param->define_order_column('Nom',1,__ASC__);
		/*===================================================================*/	
		
		/**==================================================================
		 * Define color mask
		 ====================================================================*/	
		$obj_vimofy_link_param->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
		$obj_vimofy_link_param->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
		/*===================================================================*/	
			
		}
		else
		{
			// Create a new iObject	
			// No Lisha initialization
		}		
?>