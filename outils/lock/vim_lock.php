<?php 
	$vimofy_id = 'vimofy_lock';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_lock = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = '	SELECT
					IF
					(
						DATE_FORMAT(TIMEDIFF(NOW(),`last_update`),\'%H:%i:%s\') > (
														SELECT `value` AS "valeur"
														FROM
															`'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension'].'`
														WHERE 1 = 1
															AND `id` = 39
															AND `application_release` = "'.__MAGICTREE_APPLICATION_RELEASE__.'"
														),
						"[img]sign_error.png[/img]",
						IF(
							DATE_FORMAT(TIMEDIFF(NOW(),`date_mod`),\'%H:%i:%s\') > (
															SELECT `value` AS "valeur"
															FROM
																`'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension'].'`
															WHERE 1 = 1
																AND `id` = 39
																AND `application_release` = "'.__MAGICTREE_APPLICATION_RELEASE__.'"
														 ),
							"[img]sign_warning.png[/img]",
							"[img]../../images/1valorised.png[/img]")
					) as "c1",
					CASE `type` 
						WHEN 1 
							THEN "[b][color=red]Fiche modif[/color][/b]" 
						WHEN 2 
							THEN "[b][color=green]Fiche visu[/color][/b]"  
						WHEN 3 
							THEN "[b][color=gray]Code modif[/color][/b]" 
						WHEN 4 
							THEN "[b][color=blue]Code visu[/color][/b]"
					END as "c2",
					`id`,
					`id_temp` ,
					`date_mod`,
					`last_update` ,
					TIMEDIFF(NOW(),date_mod) as dif,
					CASE `utilise_par`
						WHEN "127.0.0.1"
							THEN "Serveur"
						ELSE `utilise_par`
					END as "c3",
					CONCAT(\'<a href="javascript:deverouiller(\',id_temp,\',\',type,\',\',id,\')">'.$_SESSION[$ssid]['message'][8].'</a>\') as "c4",
					`version_client`,
					`ssid`
				FROM 
					`'.$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'].'` 
					WHERE 1 = 1 ';
		
	if(isset($_GET['mode']))
	{
		if($_GET['mode'] == 'die')
		{
			$query .= ' AND `type` IN(1,3) AND addtime(`date_mod`,(SELECT `value` FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension'].'` WHERE `id` = 39 AND `application_release` = "'.__MAGICTREE_APPLICATION_RELEASE__.'")) < now()';
		}
		
		if($_GET['mode'] == 'all')
		{
			$query .= '';
		}
	}
	else
	{
		$query .= ' AND `type` IN(1,3)';
	}
	
	$obj_lock->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_lock->define_size(100,'%',100,'%');											
	$obj_lock->define_nb_line(50);													
	$obj_lock->define_readonly(__R__);												// Read & Write
	$obj_lock->define_theme('grey');												// Define default style
	$obj_lock->define_sep_col_row(true,false);
	$obj_lock->define_title($_SESSION[$ssid]['message'][9]);
	$obj_lock->define_page_selection_display(false,true);
	$obj_lock->define_auto_refresh_timer(30000);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : type
		//==================================================================
		$obj_lock->define_column('c1',$_SESSION[$ssid]['message'][10],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : name
		//==================================================================
		$obj_lock->define_column('c2',$_SESSION[$ssid]['message'][11],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('id',$_SESSION[$ssid]['message'][12],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('id_temp',$_SESSION[$ssid]['message'][13],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('date_mod',$_SESSION[$ssid]['message'][14],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	

		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('last_update',$_SESSION[$ssid]['message'][15],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('dif',$_SESSION[$ssid]['message'][16],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('c3',$_SESSION[$ssid]['message'][17],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('c4',$_SESSION[$ssid]['message'][18],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		

		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('ssid',$_SESSION[$ssid]['message'][19],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Source
		 ====================================================================*/	
		// COLUMN	
		$obj_lock->define_column('version_client',$_SESSION[$ssid]['message'][20],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
	/*===================================================================*/	
	
	//==================================================================
	// Define extra events actions
	//==================================================================
	//$obj_lock->define_vimofy_action(__ON_UPDATE__,__AFTER__,$vimofy_id,Array('maj_vimofy_param=true;'));		
	//==================================================================

	//==================================================================
	// Define update / insert mode 
	//==================================================================
	$obj_lock->define_key(Array('id_temp','ssid'));
	//==================================================================
	
	//==================================================================
	// Define default input focus
	//==================================================================
	$obj_lock->define_input_focus('dif');
	//==================================================================
			
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_lock->define_order_column('dif',1,__DESC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_lock->define_color_mask("FFFFFF","CCCCCC","AAAAAA","000","FFF");
	$obj_lock->define_color_mask("EEEEEE","BBBBBB","888888","000","DDD");
	//==================================================================
?>