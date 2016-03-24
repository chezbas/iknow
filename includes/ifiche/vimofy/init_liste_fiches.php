<?php
/**==================================================================
 * Lisha general iSheet list definition
====================================================================*/

	//==================================================================
	// Clear working variables
	//==================================================================
	$Texists = '';
	$tags = '';
	$grtags = '';
	$str1_tags = '';
	$str2_tags = 'AND 1 = 1';
	$str2_grtags = 'AND 1 = 1';
	$str3_tags = '';
	$str3_grtags = '';
	//==================================================================

	//==================================================================
	// Add extra url GET parameters to each display action link
	//==================================================================
	$get_url_param = url_get_exclusion($_GET,array('ssid','lng','typec'));
	//==================================================================
	
	//==================================================================
	// Recover POST ( from relative call ) information
	//==================================================================
	if (isset($_POST["tags"]))
	{
		$tags = $_POST["tags"];
	}
	if (isset($_POST["grtags"]))
	{
		$grtags = $_POST["grtags"];
	}
	if (isset($_POST["Texists"]))
	{
		$Texists = $_POST["Texists"];
	}
	//==================================================================
	

	//==================================================================
	// Prepare sub part of tag query
	//==================================================================
	if(
		isset($_POST["tags"])
		&& strlen($_POST["tags"]) > 0 OR isset($_POST["grtags"])
		&& strlen($_POST["grtags"]) > 0 OR isset($_POST["Texists"])
		&& strlen($_POST["Texists"]) > 0
		)
	{
		$str1_tags = $_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']." TAGS,";
		$str2_tags = "	AND TAGS.`Etape` = 0
						AND TAGS.`ID` = FIC.`id_fiche`
						AND TAGS.`Version` = FIC.`num_version`
						AND TAGS.Tag like '%".$tags."%' ";
		$str3_tags = "TAGS.Tag";
	}

	if 	(
		isset($_POST["grtags"])
		&& strlen($_POST["grtags"]) > 0 OR isset($_POST["tags"])
		&& strlen($_POST["tags"]) > 0 OR isset($_POST["Texists"])
		&& strlen($_POST["Texists"]) > 0
		)
	{
		$str1_tags = $_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']." TAGS,";
		$str2_grtags = "AND TAGS.`Etape` = 0 AND TAGS.`ID` = FIC.id_fiche AND TAGS.`Version` = FIC.`num_version` AND TAGS.Groupe like '%".$grtags."%' ";
		$str3_grtags = "TAGS.Groupe";
	}
	//==================================================================

	if(strlen($Texists) > 0 )
	{
		$lib_exists = 'checked="checked"';
	}
	else 
	{
		$lib_exists = '';
	}
	
	$_GET['lng'] = $_SESSION[$ssid]['langue'];

	$table_name = "`".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']."`";
	$str_force_version = "";

	//==================================================================
	// Begin of lisha object
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id'] = new lisha(
																	'lisha_isheet_list_id',
																	$ssid,
																	__MYSQL__,
																	array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),
																	$path_root_lisha,
																	false
																);
	

	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT 
			 		`FIC`.`id_fiche` 			AS 'ID',
					`POLE`.`Libelle` 			AS 'Pole',
					FIC.`vers_goldstock`	AS 'VP',
					CONCAT(
							`METI`.`Libelle`,
							' - ',
							`MODUL`.`Libelle`
						  )					AS 'modlib',
					CONCAT(
							'[URL=processpole.php?pole=',
							METI.`id_POLE`,
							'&amp;mode=M&amp;vue=S&0=',
							FIC.`id_module` ,
							']',
							FIC.`id_module`,
							'[/URL]'
						  )					AS 'Module',
					CASE FIC.`obsolete`
							WHEN '1'
							THEN CONCAT('[s][color=#888888]',FIC.`titre`,'[/color][/s]')
							ELSE FIC.`titre` 
							END 			AS 'titre',
					CONCAT('[urloff=./ifiche.php?ID=',`FIC`.`id_fiche`,'".$get_url_param."',']', '".$_SESSION[$ssid]['message']['iknow'][516]."','[/url]',' ".$_SESSION[$ssid]['message']['iknow'][517]." ','[urloff=./modif_fiche.php?&amp;ID=',`FIC`.`id_fiche`,']".$_SESSION[$ssid]['message']['iknow'][518]."[/url]') AS `Action`,
					`THEM`.`libelle` 			AS 'Activity',
					`FIC`.`num_version` 		AS 'Version',
					`FIC`.`pers` 				AS 'Modif',
					`FIC`.`date` 				AS 'Date',
					FIC.`description` 		AS 'description',
					".$str3_tags." 		 	'TT',
					".$str3_grtags."		'GR',
					TEXTS.`texte`			AS STATUT,
					CASE FIC.`obsolete` 
							WHEN '1'
							THEN '[b][color=#FF0000]".$_SESSION[$ssid]['message'][179]."[/color][/b]' 
							WHEN '0'
							THEN '".$_SESSION[$ssid]['message'][180]."' 
					END 					AS 'Obsolete',
					`FIC2`.`pers`				AS 'creator'
				".$_SESSION[$ssid]['lisha']['configuration'][10]."
						".$table_name." FIC,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['name']."` FIC2,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` THEM,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` POLE,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name']."` METI,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']."` MODUL,".$str1_tags."
						`".$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name']."` TEXTS
					WHERE 1 = 1
						".$str2_tags."
						".$str2_grtags."
						AND METI.`id_POLE` = POLE.`ID`
						AND MODUL.`ID` = FIC.`id_module`
						AND MODUL.`id_POLE` = POLE.`ID`
						AND MODUL.`ID_METIER` = METI.`ID`
						AND THEM.`ID` = FIC.`Theme`
					    AND FIC.`id_POLE` = POLE.`ID`
						AND FIC.`id_statut` = TEXTS.`id_texte`
					    AND FIC.`id_fiche` = FIC2.`id_fiche`
						AND FIC2.`num_version` = 0 -- Get Author
						AND TEXTS.`type` = 'statut'
						AND TEXTS.`id_lang` = '".$_SESSION[$ssid]['configuration'][43]."'
						AND TEXTS.`objet` = 'ifiche'
						AND THEM.`ID_POLE` = FIC.`id_POLE`
						AND TEXTS.`version_active` = '".$version_soft."'
						".$str_force_version;
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__main_query', $query);
	//==================================================================

	//==================================================================
	// Lisha display setup
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_size(100,'%',100,'%');												// width 700px, height 500px
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_nb_line(50);															// 20 lines per page
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_readonly_mode', __R__);							// Read & Write
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__id_theme','green');									// Define default style
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__background_picture', 'images/iknow.png');
	//$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_title',$_SESSION[$ssid]['message'][462]);		// Define title	
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__title',$_SESSION[$ssid]['message'][462]);		// Define title

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_column_separation',true);
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_row_separation',false);

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_top_bar_page',false);
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_bottom_bar_page',true);

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_user_doc', false);				// user documentation button
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_tech_doc', false);				// technical documentation button
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_ticket', false);					// Tickets link

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__display_mode', __NMOD__);					// Display mode

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__key_url_custom_view', 'CS');				// Defined key for quick custom view loader in url browser

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__update_table_name', $_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']);		// Define table to update

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__column_name_group_of_color', "MyGroupTheme");		// ( Optional ) Define custom column color name

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_attribute('__active_quick_search', true);				        // Quick search mode ( Optional : default true )
	/*===================================================================*/

	//==================================================================
	// define output columns
	//==================================================================

		//==================================================================
		// define column : ID
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('`FIC`.`id_fiche`','ID',$_SESSION[$ssid]['message'][206],__BBCODE__,__WRAP__,__CENTER__,__EXACT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_input_focus('ID', true);					// Focused
		//==================================================================

		//==================================================================
		// define column : Link for actions
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("CONCAT('[urloff=./ifiche.php?ID=',`FIC`.`id_fiche`,'".$get_url_param."',']', '".$_SESSION[$ssid]['message']['iknow'][516]."','[/url]',' ".$_SESSION[$ssid]['message']['iknow'][517]." ','[urloff=./modif_fiche.php?&amp;ID=',`FIC`.`id_fiche`,']".$_SESSION[$ssid]['message']['iknow'][518]."[/url]')",'Action',$_SESSION[$ssid]['message'][463],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
		// define column : Area
		//==================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("`POLE`.`Libelle`",'Pole',$_SESSION[$ssid]['message']['iknow'][32],__TEXT__,__WRAP__,__CENTER__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_col_quick_help('Pole',true);

		// Match code
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_lov("	SELECT
																			`POLE`.`ID` AS `ID`,
																			`POLE`.`Libelle` AS `Pole`
																		".$_SESSION[$ssid]['lisha']['configuration'][10]."
																			`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` `POLE`
																		WHERE 1 = 1",
																			$_SESSION[$ssid]['message']['iknow'][32],
																			'`POLE`.`Libelle`',
																			'Pole'
																		);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`POLE`.`ID`','ID',$_SESSION[$ssid]['message'][46],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`POLE`.`Libelle`','Pole',$_SESSION[$ssid]['message']['iknow'][32],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov_order('Pole',__ASC__);
		//==================================================================

		//==================================================================
		// define column : Area version
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("FIC.`vers_goldstock`",'VP',$_SESSION[$ssid]['message']['iknow'][33],__TEXT__,__WRAP__,__CENTER__);

		// Match code
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_lov("	SELECT
																			`PV`.`ID` AS `ID`,
																			`PV`.`version` AS `version`
																		".$_SESSION[$ssid]['lisha']['configuration'][10]."
																			`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_versions']['name']."` `PV`
																		WHERE 1 = 1
																			AND `PV`.`ID` = '||TAGLOV_Pole**`POLE`.`ID`||'",
																		$_SESSION[$ssid]['message']['iknow'][33],
																		'`PV`.`version`',
																		'version'
																	);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`PV`.`ID`','ID',$_SESSION[$ssid]['message'][70],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`PV`.`version`','version',$_SESSION[$ssid]['message'][62],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov_order('version',__ASC__);
		//==================================================================

		//==================================================================
		// Level - Sub Level
		//====================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("CONCAT(`METI`.`Libelle`,' - ',`MODUL`.`Libelle`)",'modlib',$_SESSION[$ssid]['message'][52],__TEXT__,__WRAP__,__LEFT__);
		
		// LOV
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_lov("SELECT
																			CONCAT(METI.Libelle,' - ', MODUL.Libelle) AS `RESU`
																			".$_SESSION[$ssid]['lisha']['configuration'][10]."
																			`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']."` MODUL,
																			".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name']." METI
																			WHERE 1 = 1
																				AND METI.ID_pole = '||TAGLOV_Pole**`POLE`.`ID`||'
																				AND MODUL.ID_METIER = METI.ID
																				AND MODUL.ID_pole = '||TAGLOV_Pole**`POLE`.`ID`||'
																				AND MODUL.ID_pole = METI.ID_pole",
			$_SESSION[$ssid]['message'][52].' - '.$_SESSION[$ssid]['message'][3],
			'CONCAT(METI.Libelle,' - ', MODUL.Libelle)',
			'RESU'
		);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov("CONCAT(METI.Libelle,' - ', MODUL.Libelle)",'RESU','Résumé',__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov_order('RESU',__ASC__);
		//===================================================================

		//==================================================================
		// Module ( Level ID )
		//====================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("CONCAT(
							'[URL=processpole.php?pole=',
							METI.`id_POLE`,
							'&amp;mode=M&amp;vue=S&0=',
							FIC.`id_module` ,
							']',
							FIC.`id_module`,
							'[/URL]'
						  )",'Module',$_SESSION[$ssid]['message'][464],__BBCODE__,__WRAP__,__CENTER__);
		
		// LOV
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_lov("SELECT
																		`MODUL`.`ID` AS ID,
																		`MODUL`.`Libelle` AS Libelle
																		".$_SESSION[$ssid]['lisha']['configuration'][10]."
																		`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']."` `MODUL`
																		WHERE 1 = 1
																			AND `MODUL`.id_POLE like '||TAGLOV_Pole**`POLE`.`ID`||'",
			$_SESSION[$ssid]['message'][464],
			'`MODUL`.`ID`',
			'ID');
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`MODUL`.`ID`','ID','Code',__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`MODUL`.`Libelle`','Libelle','Libellé',__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov_order('Libelle',__ASC__);
		//===================================================================

		//==================================================================
		// define column : iSheet title
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("CASE FIC.`obsolete`
							WHEN '1'
							THEN CONCAT('[s][color=#888888]',FIC.`titre`,'[/color][/s]')
							ELSE FIC.`titre`
							END",'Titre',$_SESSION[$ssid]['message'][47],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================

		//==================================================================
		// define column : activity means team
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column("`THEM`.`libelle`",'Activity',$_SESSION[$ssid]['message']['iknow'][51],__TEXT__,__WRAP__,__CENTER__);

		// LOV
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_lov("	SELECT
																			`THEM`.`ID` AS 'ID',
																			`THEM`.`libelle` AS 'libelle'
																		".$_SESSION[$ssid]['lisha']['configuration'][10]."
																			`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` `THEM`
																		WHERE 1 = 1
																			AND `THEM`.`ID_POLE` = '||TAGLOV_Pole**`POLE`.`ID`||'",
																			$_SESSION[$ssid]['message'][502],
																			'`THEM`.`libelle`',
																			'libelle'
																		);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`THEM`.`ID`','ID','Code',__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov('`THEM`.`libelle`','libelle',$_SESSION[$ssid]['message'][47],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column_lov_order('libelle',__ASC__);
		//==================================================================
		
		//==================================================================
		// define column : Current max version
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('`FIC`.`num_version`','Version',$_SESSION[$ssid]['message'][50],__TEXT__,__WRAP__,__CENTER__,__EXACT__);
		//==================================================================

		//==================================================================
		// define column : Last update by
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('`FIC`.`pers`','Modif',$_SESSION[$ssid]['message'][436],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================


		//==================================================================
		// define column : Last update date
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('`FIC`.`date`','date',$_SESSION[$ssid]['message'][84],__DATE__,__WRAP__,__CENTER__);
		//$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('__column_date_format','YYYY-MM-DD');
		//==================================================================


		//==================================================================
		// define column : Creator of iSheet
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('`FIC2`.`pers`','creator',$_SESSION[$ssid]['message'][474],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================


		// ???$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('description',"Description",__TEXT__,__WRAP__,__LEFT__,null,__HIDE__);

		/*
		if($tags != '' || $grtags != '' || $Texists != '')
		{
			$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('TT',$_SESSION[$ssid]['message'][73],__TEXT__,__WRAP__,__CENTER__,__PERCENT__);
			$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('GR',$_SESSION[$ssid]['message'][410],__TEXT__,__WRAP__,__CENTER__,__PERCENT__);
		}
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('STATUT',$_SESSION[$ssid]['message'][54],__TEXT__,__WRAP__,__CENTER__);
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_column('Obsolete',$_SESSION[$ssid]['message'][466],__BBCODE__,__WRAP__,__CENTER__);
		*/
	/*===================================================================*/


	//==================================================================
	// Column order : Define in ascending priority means first line defined will be first priority column to order by and so on...
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_order_column('date',__DESC__);
	//==================================================================

	//==================================================================
	// Table columns primary key
	// Caution : Can't change key column name from root query column name
	// It's not required to declare column key with define_column method
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_key(Array('ID','Version'));
	//==================================================================

	//==================================================================
	// Cyclic theme lines
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_line_theme("FFFFFF","0.7em","D0DCE0","0.7em","7292CE","0.7em","7292CE","0.7em","333","FFF");
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->define_line_theme("D8EDB0","0.7em","D0DCE0","0.7em","264A59","0.7em","264A59","0.7em","000","FFF");
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->new_graphic_lisha();

	//==================================================================
	// Do not remove this bloc
	// Keep this bloc at the end
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->generate_public_header();
	$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->generate_header();
	//==================================================================