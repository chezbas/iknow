<?php 
	$vimofy_id = 'vimofy_bugs';

	// Force vimofy language from framework language
	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	
	// Use framework connexion information from framework
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create a reference to the session
	$obj_vimofy_bug = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					REP.`ID` AS 'id',
					REP.`Type` AS 'type',
					-- REP.`Classe` AS 'classe',
					TEXC.`text` AS 'classe',
					REP.`Version` AS 'version',
					DATE_FORMAT(REP.`DateCrea`,'%Y-%m%-%d') AS 'DateCrea',
					(
						SELECT
							CASE TEX.`id`
								WHEN 4
								THEN CONCAT('[S]',REP.`Description`,'[/S]')
								ELSE
									REP.`Description`
								END	
					) AS 'Description',
					CONCAT('[img]',CLAS.`symbol`,'[/img]') AS 'flag',
					CONCAT(
								(
									SELECT
							 			CASE IFNULL(LENGTH(REP.`details`),0)
											WHEN 0
											THEN CONCAT('[URL=./editbug.php?ssid=".$ssid."&IKLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['message'][19]."[/URL]')
											ELSE CONCAT('[URL=./viewbug.php?ssid=".$ssid."&IKLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['message'][20]."[/URL]',' / ','[URL=./editbug.php?ssid=".$ssid."&IKLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['message'][21]."[/URL]')
										END
								)
						  ) AS 'details',
					REP.`Qui` AS 'qui',
					TEX.`text` AS 'status',
					REP.`reference` AS 'reference',
					REP.`Last_mod` AS 'last_mod'
				FROM
					`bugsreports` REP,
					`bugstexts` TEX,
					`bugsclass` CLAS,
					`bugsclass` CLASC,
					`bugstexts` TEXC
				WHERE 1 = 1
					AND CLAS.`id` = TEX.`id`
					AND CLAS.`class` = 'status'
					AND CLASC.`id` = REP.`Classe`
					AND CLASC.`class` = 'class'
					AND REP.`Classe` = TEXC.`id` 
					AND TEX.`id_lang` = TEXC.`id_lang`
					AND REP.`status` = TEX.`id`
					AND TEX.`id_lang` = '".$_GET['lng']."'
				";

	$obj_vimofy_bug->define_query($query);
	//==================================================================
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_bug->define_size(100,'%',100,'%');											
	$obj_vimofy_bug->define_nb_line(50);													
	$obj_vimofy_bug->define_readonly(__RW__);												// Read & Write
	$obj_vimofy_bug->define_theme('grey');													// Define default style
	$obj_vimofy_bug->define_title($_SESSION[$ssid]['message'][1]);							// Define Lisha title
	$obj_vimofy_bug->define_sep_col_row(true,true);
	$obj_vimofy_bug->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : ID
		//==================================================================
		$obj_vimofy_bug->define_column('id',$_SESSION[$ssid]['message'][2],__TEXT__,__WRAP__,__CENTER__,__EXACT__);						
		//==================================================================
		
		//==================================================================
		// define column : Theme
		//==================================================================
		$obj_vimofy_bug->define_column('type',$_SESSION[$ssid]['message'][3],__TEXT__,__WRAP__,__LEFT__);

		$obj_vimofy_bug->define_lov("	SELECT
											DISTINCT
											BUG.`Type` AS 'Type',
											MAX(BUG.`Last_mod`) AS 'Lastmod'
										FROM
											`bugsreports` BUG
										GROUP BY Type",
									$_SESSION[$ssid]['message'][24],
									'Type'
								   );
		$obj_vimofy_bug->define_column_lov('Type',$_SESSION[$ssid]['message'][3],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('Lastmod',$_SESSION[$ssid]['message'][12],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov_order('Lastmod',0,__DESC__);
		//==================================================================
					
		//==================================================================
		// define column : Bug class
		//==================================================================
		$obj_vimofy_bug->define_column('classe',$_SESSION[$ssid]['message'][4],__TEXT__,__WRAP__,__CENTER__);

		$obj_vimofy_bug->define_lov("	SELECT
											CLAS.`id` AS 'ID',
											TEX.`text` AS 'Libelle',
											CLAS.`order` AS 'ord'
										FROM
											`bugsclass` CLAS, `bugstexts` TEX
										WHERE 1 = 1
											AND TEX.`id` = CLAS.`id`
											AND TEX.`id_lang` = '".$_SESSION[$ssid]['langue']."'
											AND CLAS.`class` = 'class'",
									$_SESSION[$ssid]['message'][22],
									'ID'
								   );
		$obj_vimofy_bug->define_column_lov('Libelle',$_SESSION[$ssid]['message'][8],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('ord',$_SESSION[$ssid]['message'][18],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('ID','Iden',__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov_order('ord',0,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Application version involved
		//==================================================================
		$obj_vimofy_bug->define_column('version',$_SESSION[$ssid]['message'][5],__TEXT__,__WRAP__,__CENTER__,__EXACT__);
		//==================================================================
				
		//==================================================================
		// define column : Create date
		//==================================================================
		$obj_vimofy_bug->define_column('DateCrea',$_SESSION[$ssid]['message'][6],__DATE__,__WRAP__,__CENTER__);
		$obj_vimofy_bug->define_column_date_format('DateCrea','YYYY-MM-DD');
		//==================================================================
		
		//==================================================================
		// define column : Bug title
		//==================================================================
		$obj_vimofy_bug->define_column('Description',$_SESSION[$ssid]['message'][7],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : Status
		//==================================================================
		$obj_vimofy_bug->define_column('status',$_SESSION[$ssid]['message'][8],__TEXT__,__WRAP__,__CENTER__);

		$obj_vimofy_bug->define_lov("	SELECT
											CLAS.`id` AS 'ID',
											TEX.`text` AS 'Libelle',
											CLAS.`order` AS 'ord',
											CONCAT('[img]',CLAS.`symbol`,'[/img]') AS 'symbol'
										FROM
											`bugsclass` CLAS, `bugstexts` TEX
										WHERE 1 = 1
											AND TEX.`id` = CLAS.`id`
											AND TEX.`id_lang` = '".$_GET['lng']."'
											AND CLAS.`class` = 'status'",
									$_SESSION[$ssid]['message'][17],
									'ID'
								   );
		$obj_vimofy_bug->define_column_lov('Libelle',$_SESSION[$ssid]['message'][8],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('symbol',$_SESSION[$ssid]['message'][8],__BBCODE__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('ord',$_SESSION[$ssid]['message'][18],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('ID','Iden',__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov_order('ord',0,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Status symbol
		//==================================================================
				// COLUMN
		$obj_vimofy_bug->define_column('flag',$_SESSION[$ssid]['message'][11],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Action on further details
		//==================================================================
		$obj_vimofy_bug->define_column('details',$_SESSION[$ssid]['message'][9],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Who
		//==================================================================
		$obj_vimofy_bug->define_column('qui',$_SESSION[$ssid]['message'][10],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
		// define column : Dev reference
		//==================================================================
		$obj_vimofy_bug->define_column('reference',$_SESSION[$ssid]['message'][25],__BBCODE__,__WRAP__,__CENTER__);

		$obj_vimofy_bug->define_lov("	SELECT
											DISTINCT
											BUG.`reference` AS 'reference',
											MAX(BUG.`Last_mod`) AS 'Lastmod'
										FROM
											`bugsreports` BUG
										WHERE BUG.`reference` IS NOT NULL
										GROUP BY reference",
									$_SESSION[$ssid]['message'][24],
									'reference'
								   );
		$obj_vimofy_bug->define_column_lov('reference',$_SESSION[$ssid]['message'][3],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov('Lastmod',$_SESSION[$ssid]['message'][12],__TEXT__,__WRAP__,__LEFT__);
		$obj_vimofy_bug->define_column_lov_order('Lastmod',0,__DESC__);
		//==================================================================
		
		//==================================================================
		// define column : Last update
		//==================================================================
		$obj_vimofy_bug->define_column('last_mod',$_SESSION[$ssid]['message'][12],__DATE__,__WRAP__,__CENTER__);
		$obj_vimofy_bug->define_column_date_format('last_mod','YYYY-MM-DD');
		//==================================================================
				
	//==================================================================
				
	/**==================================================================
	 * UPDATE/INSERT
	 ====================================================================*/		
	// Update table
	$obj_vimofy_bug->define_update_table('bugsreports');
	
	// Columns attribut
	$obj_vimofy_bug->define_rw_flag_column('id',__FORBIDEN__);
	$obj_vimofy_bug->define_rw_flag_column('flag',__FORBIDEN__);
	$obj_vimofy_bug->define_rw_flag_column('type',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('classe',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('version',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('DateCrea',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('Description',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('details',__FORBIDEN__);
	$obj_vimofy_bug->define_rw_flag_column('qui',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('status',__REQUIRED__);
	$obj_vimofy_bug->define_rw_flag_column('last_mod',__FORBIDEN__);
	
	// Table key
	$obj_vimofy_bug->define_key(Array('id'));
	/*===================================================================*/		
	
	/**==================================================================
	 * Defining order
	 ====================================================================*/
	$obj_vimofy_bug->define_order_column('last_mod',1,__DESC__);					
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining color mask
	 ====================================================================*/	
	$obj_vimofy_bug->define_color_mask("DDDDFF","CCCCEE","68B7E0","000","000");
	$obj_vimofy_bug->define_color_mask("EEEEEE","D0DCE0","AEE068","000","000");
	/*===================================================================*/	
			
?>