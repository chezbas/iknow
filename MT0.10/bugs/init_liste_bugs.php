<?php 
	$lisha_mt_id = 'lisha_bugs_id';

	// Force vimofy language from framework language
	$_GET['lng'] = $_SESSION[$ssid]['MT']['langue'];
	
	// Use framework connexion information from framework
	$_SESSION[$ssid]['lisha'][$lisha_mt_id] = new lisha($lisha_mt_id,$ssid,__MYSQL__,array('user' => __MAGICTREE_DATABASE_USER__,'password' => __MAGICTREE_DATABASE_PASSWORD__,'host' => __MAGICTREE_DATABASE_HOST__,'schema' => __MAGICTREE_DATABASE_SCHEMA__),'../'.__LISHA_APPLICATION_RELEASE__.'/',null,false,__LISHA_APPLICATION_RELEASE__);
	
	// Create a reference to the session
	$obj_lisha_bug = &$_SESSION[$ssid]['lisha'][$lisha_mt_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					REP.`ID` AS 'id',
					TEXD.`text` AS 'business',
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
							 			CASE IFNULL( LENGTH( REP.`details` ) , 0 ) + IFNULL( LENGTH( REP.`solution` ) , 0 ) 
											WHEN 0
											THEN CONCAT('[URL=./editbug.php?ssid=".$ssid."&MTLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['page_text'][19]['MT']."[/URL]')
											ELSE CONCAT('[URL=./viewbug.php?ssid=".$ssid."&MTLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['page_text'][20]['MT']."[/URL]',' / ','[URL=./editbug.php?ssid=".$ssid."&MTLNG=".$_GET['lng']."&ID=',REP.`ID`,']".$_SESSION[$ssid]['page_text'][21]['MT']."[/URL]')
										END
								)
						  ) AS 'details',
					REP.`Qui` AS 'qui',
					TEX.`text` AS 'status',
					REP.`reference` AS 'reference',
					REP.`Last_mod` AS 'last_mod'
				FROM
					`".__MAGICTREE_TABLE_EXTRA_TICK__."` REP,
					`".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEX,
					`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLAS,
					`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLASC,
					`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLASD,
					`".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEXC,
					`".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEXD
				WHERE 1 = 1
					AND CLAS.`id` = TEX.`id`
					AND CLAS.`class` = 'status'
					AND CLASC.`id` = REP.`Classe`
					AND CLASC.`class` = 'class'
					AND CLASD.`id` = REP.`Business`
					AND CLASD.`class` = 'business'
					AND REP.`Classe` = TEXC.`id`
					AND REP.`Business` = TEXD.`id` 
					AND TEX.`id_lang` = TEXC.`id_lang`
					AND TEX.`id_lang` = TEXD.`id_lang`
					AND REP.`status` = TEX.`id`
					AND TEX.`id_lang` = '".$_GET['lng']."'
				";

	$obj_lisha_bug->define_query($query);
	//==================================================================
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_lisha_bug->define_size(100,'%',100,'%');											
	$obj_lisha_bug->define_nb_line(50);													
	$obj_lisha_bug->define_readonly(__RW__);												// Read & Write
	$obj_lisha_bug->define_theme('grey');													// Define default style
	$obj_lisha_bug->define_title($_SESSION[$ssid]['page_text'][1]['MT']);					// Define Lisha title
	$obj_lisha_bug->define_sep_col_row(true,true);
	$obj_lisha_bug->define_page_selection_display(false,true);

	$obj_lisha_bug->display_help_button(false); // Don't display help button
	$obj_lisha_bug->tickets_link_enable(false); // disable tickets link
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : ID
		//==================================================================
		$obj_lisha_bug->define_column('id',$_SESSION[$ssid]['page_text'][2]['ST'],__TEXT__,__WRAP__,__CENTER__,__EXACT__);						
		//==================================================================

		//==================================================================
		// define column : Business domain
		//==================================================================
		$obj_lisha_bug->define_column('business',$_SESSION[$ssid]['page_text'][13]['ST'],__TEXT__,__WRAP__,__CENTER__,__EXACT__);						

		$obj_lisha_bug->define_lov("	SELECT
											CLAS.`id` AS 'ID',
											TEX.`text` AS 'Libelle',
											CLAS.`order` AS 'ord'
										FROM
											`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLAS, `".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEX
										WHERE 1 = 1
											AND TEX.`id` = CLAS.`id`
											AND TEX.`id_lang` = '".$_GET['lng']."'
											AND CLAS.`class` = 'business'",
									$_SESSION[$ssid]['page_text'][13]['MT'],
									'ID'
								   );
		$obj_lisha_bug->define_column_lov('Libelle',$_SESSION[$ssid]['page_text'][28]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ord',$_SESSION[$ssid]['page_text'][18]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ID',$_SESSION[$ssid]['page_text'][2]['ST'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov_order('ord',0,__ASC__);
		//==================================================================
		
		//==================================================================
		// define column : Theme
		//==================================================================
		$obj_lisha_bug->define_column('type',$_SESSION[$ssid]['page_text'][3]['ST'],__TEXT__,__WRAP__,__LEFT__);

		$obj_lisha_bug->define_lov("	SELECT
											DISTINCT
											BUG.`Type` AS 'Type',
											MAX(BUG.`Last_mod`) AS 'Lastmod'
										FROM
											`".__MAGICTREE_TABLE_EXTRA_TICK__."` BUG
										GROUP BY Type",
									$_SESSION[$ssid]['page_text'][3]['LT'],
									'Type'
								   );
		$obj_lisha_bug->define_column_lov('Type',$_SESSION[$ssid]['page_text'][3]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('Lastmod',$_SESSION[$ssid]['page_text'][12]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov_order('Lastmod',0,__DESC__);
		//==================================================================
					
		//==================================================================
		// define column : Bug class
		//==================================================================
		$obj_lisha_bug->define_column('classe',$_SESSION[$ssid]['page_text'][4]['ST'],__TEXT__,__WRAP__,__CENTER__);

		$obj_lisha_bug->define_lov("	SELECT
											CLAS.`id` AS 'ID',
											TEX.`text` AS 'Libelle',
											CLAS.`order` AS 'ord'
										FROM
											`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLAS, `".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEX
										WHERE 1 = 1
											AND TEX.`id` = CLAS.`id`
											AND TEX.`id_lang` = '".$_GET['lng']."'
											AND CLAS.`class` = 'class'",
									$_SESSION[$ssid]['page_text'][4]['LT'],
									'ID'
								   );
		$obj_lisha_bug->define_column_lov('Libelle',$_SESSION[$ssid]['page_text'][4]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ord',$_SESSION[$ssid]['page_text'][18]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ID',$_SESSION[$ssid]['page_text'][2]['ST'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov_order('ord',0,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Application version involved
		//==================================================================
		$obj_lisha_bug->define_column('version',$_SESSION[$ssid]['page_text'][5]['MT'],__TEXT__,__WRAP__,__CENTER__,__EXACT__);
		//==================================================================
				
		//==================================================================
		// define column : Create date
		//==================================================================
		$obj_lisha_bug->define_column('DateCrea',$_SESSION[$ssid]['page_text'][6]['MT'],__DATE__,__WRAP__,__CENTER__);
		$obj_lisha_bug->define_column_date_format('DateCrea','YYYY-MM-DD');
		//==================================================================
		
		//==================================================================
		// define column : Bug title
		//==================================================================
		$obj_lisha_bug->define_column('Description',$_SESSION[$ssid]['page_text'][7]['MT'],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : Status
		//==================================================================
		$obj_lisha_bug->define_column('status',$_SESSION[$ssid]['page_text'][8]['ST'],__TEXT__,__WRAP__,__CENTER__);

		$obj_lisha_bug->define_lov("	SELECT
											CLAS.`id` AS 'ID',
											TEX.`text` AS 'Libelle',
											CLAS.`order` AS 'ord',
											CONCAT('[img]',CLAS.`symbol`,'[/img]') AS 'symbol'
										FROM
											`".__MAGICTREE_TABLE_EXTRA_TICK_CLAS__."` CLAS, `".__MAGICTREE_TABLE_EXTRA_TICK_TEXT__."` TEX
										WHERE 1 = 1
											AND TEX.`id` = CLAS.`id`
											AND TEX.`id_lang` = '".$_GET['lng']."'
											AND CLAS.`class` = 'status'",
									$_SESSION[$ssid]['page_text'][8]['LT'],
									'ID'
								   );
		$obj_lisha_bug->define_column_lov('Libelle',$_SESSION[$ssid]['page_text'][8]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('symbol',$_SESSION[$ssid]['page_text'][8]['MT'],__BBCODE__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ord',$_SESSION[$ssid]['page_text'][18]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('ID',$_SESSION[$ssid]['page_text'][2]['ST'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov_order('ord',0,__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Status symbol
		//==================================================================
		// COLUMN
		$obj_lisha_bug->define_column('flag',$_SESSION[$ssid]['page_text'][11]['MT'],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Action on further details
		//==================================================================
		$obj_lisha_bug->define_column('details',$_SESSION[$ssid]['page_text'][9]['MT'],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Who
		//==================================================================
		$obj_lisha_bug->define_column('qui',$_SESSION[$ssid]['page_text'][10]['MT'],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
		// define column : Dev reference
		//==================================================================
		$obj_lisha_bug->define_column('reference',$_SESSION[$ssid]['page_text'][25]['ST'],__BBCODE__,__WRAP__,__CENTER__);

		$obj_lisha_bug->define_lov("	SELECT
											DISTINCT
											BUG.`reference` AS 'reference',
											MAX(BUG.`Last_mod`) AS 'Lastmod'
										FROM
											`".__MAGICTREE_TABLE_EXTRA_TICK__."` BUG
										WHERE BUG.`reference` IS NOT NULL
										GROUP BY reference",
									$_SESSION[$ssid]['page_text'][25]['LT'],
									'reference'
								   );
		$obj_lisha_bug->define_column_lov('reference',$_SESSION[$ssid]['page_text'][25]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov('Lastmod',$_SESSION[$ssid]['page_text'][12]['MT'],__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_bug->define_column_lov_order('Lastmod',0,__DESC__);
		//==================================================================
		
		//==================================================================
		// define column : Last update
		//==================================================================
		$obj_lisha_bug->define_column('last_mod',$_SESSION[$ssid]['page_text'][12]['MT'],__DATE__,__WRAP__,__CENTER__);
		$obj_lisha_bug->define_column_date_format('last_mod','YYYY-MM-DD');
		//==================================================================
				
	//==================================================================
				
	/**==================================================================
	 * UPDATE/INSERT
	 ====================================================================*/		
	// Update table
	$obj_lisha_bug->define_update_table('bugsreports');
	
	// Columns attribut
	$obj_lisha_bug->define_rw_flag_column('id',__FORBIDEN__);
	$obj_lisha_bug->define_rw_flag_column('flag',__FORBIDEN__);
	$obj_lisha_bug->define_rw_flag_column('type',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('classe',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('business',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('version',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('DateCrea',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('Description',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('details',__FORBIDEN__);
	$obj_lisha_bug->define_rw_flag_column('qui',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('status',__REQUIRED__);
	$obj_lisha_bug->define_rw_flag_column('last_mod',__FORBIDEN__);
	
	// Table key
	$obj_lisha_bug->define_key(Array('id'));
	/*===================================================================*/		
	
	/**==================================================================
	 * Defining order
	 ====================================================================*/
	$obj_lisha_bug->define_order_column('last_mod',1,__DESC__);					
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining color mask
	 ====================================================================*/	
	$obj_lisha_bug->define_color_mask("DDDDFF","CCCCEE","68B7E0","000","000");
	$obj_lisha_bug->define_color_mask("EEEEEE","D0DCE0","AEE068","000","000");
	/*===================================================================*/			
?>