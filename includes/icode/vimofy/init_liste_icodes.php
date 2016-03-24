<?php 
/**==================================================================
 * Lisha general iCode list definition
 ====================================================================*/		

	//==================================================================
	// Clear working variables
	//==================================================================
	$Texists = '';
	$tags = '';
	$grtags = '';
	$engine = '';
	$lib_exists = '';
	$comment = '';
	$crp = '';
	$comment = '';
	$majc_value = '';
	$str_majc = '';
	//==================================================================

	//==================================================================
	// Add extra url GET parameters to each display action link
	//==================================================================
	$get_url_param = url_get_exclusion($_GET,array('ssid','lng','typec'));
	//==================================================================
 	
	//==================================================================
	// Recover POST url information
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
	if (isset($_GET["HEADER"]))
	{
		$display_header = $_GET["HEADER"];
	}
	if (isset($_GET["typec"]))
	{	
		$engine = $_GET["typec"]; 
	}
	//==================================================================
	
	//==================================================================
	// Recover POST information
	//==================================================================
	if (isset($_POST["versi"]))
	{
		$_SESSION["viewer"][$ssid]["version"] = $_POST["versi"];
	}
	if (isset($_POST["versi"]) and $_POST["versi"] == "")
	{
		unset ($_SESSION["viewer"][$ssid]["version"]);
	}
	if (isset($_POST["corps"]))
	{
		$crp = $_POST["corps"];
	}
	if (isset($_POST["corps"]) and $_POST["corps"] == "")
	{
		$crp='';
	}
	if (isset($_POST["commentaire"]))
	{
		$comment = $_POST["commentaire"];
	}
	if (isset($_POST["commentaire"]) and $_POST["commentaire"] == "")
	{
		$comment = '';
	}
	if (isset($_POST["MAJC"]))
	{
		$majc_value = $_POST["MAJC"];
	}
	if (isset($_POST["MAJC"]) and $_POST["MAJC"] == "")
	{
		$majc_value = '';
	}
	//==================================================================
	
	if(strlen($Texists) > 0)
	{
		$lib_exists = 'checked="checked"';
	}
	
	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
	mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());

	if($engine <> '')
	{
		mysql_real_escape_string($engine);
		// Check if typec existe
		$query = "	SELECT
					*
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['name']."`
					WHERE 1 = 1
						AND `id` = '".$engine."'";
		$requete = mysql_query($query) or die('ERREUR 00003');
		$typec = mysql_fetch_assoc($requete);
		$type_lib = $typec["Description"];
		
		if($type_lib == '')
		{
			// Unknown kind of engine
			?>
			<title><?php echo $_SESSION[$ssid]['message'][121];?></title>
			</head>
			<body style="background-color:#A61415;"><div id="iknow_msgbox_background"></div>
				<div id="iknow_msgbox_conteneur" style="display:none;"></div>
					<script type="text/javascript">
					generer_msgbox(decodeURIComponent(libelle_common[17]),'<?php echo str_replace("&engine",$engine,str_replace("'","\'",$_SESSION[$ssid]['message'][122])); ?>','erreur','msg',false,true);
					</script>
				</body>
			</html>
			<?php
			die();
		}
	}
	else 
	{
		$type_lib = "";
	}
		
	// Special tags
	$str1_tags = "";
	$str2_tags = "AND 1 = 1";
	$str3_tags = "";
	if(isset($_POST["tags"]) && strlen($_POST["tags"]) > 0 or isset($_POST["grtags"]) && strlen($_POST["grtags"]) > 0 or isset($_POST["Texists"]) && strlen($_POST["Texists"]) > 0)
	{
		$str1_tags = $_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']." `TAGS`,";
		$str2_tags = "	AND `TAGS`.`ID` = `REQ`.`ID`
						AND `TAGS`.`Etape` = 0
						AND `TAGS`.`objet` = 'icode'
						AND `TAGS`.`Version` = `REQ`.`Version`
						AND `TAGS`.`Tag` like '%".$tags."%' ";
		$str3_tags = "`TAGS`.`Tag`";
	}
		
	// Special tags Group	
	$str1_grtags = "";
	$str2_grtags = "AND 1 = 1";
	$str3_grtags = "";
	if(isset($_POST["grtags"]) && strlen($_POST["grtags"]) > 0 or isset($_POST["tags"]) &&  strlen($_POST["tags"]) > 0 or isset($_POST["Texists"]) && strlen($_POST["Texists"]) > 0) 
	{
		$str1_tags = $_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']." TAGS,";
		$str2_grtags = "	AND `TAGS`.`ID` = `REQ`.`ID`
							AND `TAGS`.`Etape` = 0
							AND `TAGS`.`objet` = 'icode'
							AND `TAGS`.`Version` = `REQ`.`Version`
							AND `TAGS`.`Groupe` like '%".$grtags."%' ";
		$str3_grtags = "`TAGS`.`Groupe`";
	}
	
	if($engine <> "")
	{
		$str_engine = "`REQ`.`typec` = '".$engine."'";
	}
	else
	{
		$str_engine = "1 = 1";
	}
		
	// Special query body needed
	if ($crp != '') $corps = "`REQ`.`corps` like '%".mysql_escape_string($crp)."%'";
	else $corps = " 1 = 1"; // by default : Empty
	// Special comment needed
	if ($comment != '') $commentaire = "`REQ`.`Commentaires` LIKE '%".mysql_escape_string($comment)."%'";
	else $commentaire = " 1 = 1"; // by default : Empty

	// Special updated query ?
	if ($majc_value == "OUI" ) $str_majc = ' AND ( `REQ`.`corps` like \'%update %\' or `REQ`.`corps` like \'%insert %\' or `REQ`.`corps` like \'%delete %\' ) ';
	if ($majc_value == "NON" ) $str_majc = ' AND NOT ( `REQ`.`corps` like \'%update %\' or `REQ`.`corps` like \'%insert %\' or `REQ`.`corps` like \'%delete %\' ) ';
	
	$_GET['lng'] = $_SESSION[$ssid]['langue'];

	$table_name = "`".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']."`";
	$str_force_version = "";

	//==================================================================
	// Begin of lisha object
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id'] = new lisha(
																	'lisha_icode_list_id',
																	$ssid,
																	__MYSQL__,
																	array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),
																	$path_root_lisha,
                                                                    false
                                                                    );


	//==================================================================
	// Define main query
	//==================================================================
	$query = "SELECT 
				`REQ`.`ID` AS `ID`,
				CASE
					`REQ`.`obsolete`
						WHEN '1'
						THEN CONCAT('[s][color=#888888]',`REQ`.`Titre`,'[/color][/s]')
						ELSE `REQ`.`Titre`
				END AS `Titre`,
				CONCAT('[urloff=./icode.php?ID=',`REQ`.`ID`,'".$get_url_param."',']', '".$_SESSION[$ssid]['message']['iknow'][516]."','[/url]',' ".$_SESSION[$ssid]['message']['iknow'][517]." ','[urloff=./modif_icode.php?&amp;ID=',`REQ`.`ID`,']".$_SESSION[$ssid]['message']['iknow'][518]."[/url]') AS `Action`,
				`POLE`.`Libelle` AS `Pole`,
				`REQ`.`VGS` AS `VPole`,
				`THEME`.`libelle` AS `Theme`,
				`REQ`.`Last_update_user` AS `Modif`,
				`REQ`.`last_update_date` AS `Date`,
				`REQ`.`Version` AS `Version`,
				`REQ`.`Commentaires` AS `Commentaire`,
				`MOT`.`Description` AS `Description`,
				".$str3_tags." 'TT',
				".$str3_grtags." 'GR',
				CASE `REQ`.`obsolete`
					WHEN '1' 
					THEN '[b][color=#FF0000]".$_SESSION[$ssid]['message'][179]."[/color][/b]' 
					WHEN '0'
					THEN 'Non' 
				END AS `Obsolete`,
				`REQ2`.`Last_update_user` AS `creator`
			".$_SESSION[$ssid]['lisha']['configuration'][10]."
				`".$_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['name']."` `REQ`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name']."` `REQ2`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` `POLE`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['name']."` `MOT`,".$str1_tags."
				`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` `THEME`
			WHERE
				1 = 1 
				".$str2_tags."
				".$str2_grtags."
				AND ".$str_engine."
				AND `MOT`.`id` = `REQ`.`typec`
				AND `REQ`.`ID` = `REQ2`.`ID`
				AND `REQ2`.`Version` = 0 -- Get Author
				AND `POLE`.`ID` = `REQ`.`pole`
				AND `THEME`.`ID_POLE` = `REQ`.`pole`
				AND `THEME`.`ID` = `REQ`.`Theme`
				AND ".$commentaire." 
				AND ".$corps."
				".$str_majc;

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__main_query', $query);
	//==================================================================
		
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_title', true);				// Title bar	
	if(isset($_GET['typec']))
	{
		$link_title = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']) or die('<span style="color:red;font-weight:bold;">Informations de connexion au serveur incorrecte (serveur, identifiant ou mot de passe)</span>');
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow'],$link_title) or die('Wrong iKnow schema');
		
		$sql = "SELECT
					`Description`
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['name']."`
				WHERE 1 = 1
					AND `id` = '".mysql_real_escape_string($_GET['typec'])."'
				;";
		
		$resultat = mysql_query($sql,$link_title) or die(mysql_error());
		
		$engine_title = mysql_result($resultat,0,'Description');
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__title',$_SESSION[$ssid]['message'][471].' - '.$engine_title);		// Define title
	}
	else
	{
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__title',$_SESSION[$ssid]['message'][471]);		// Define title
		$engine_title = '';
	}

	//==================================================================
	// Lisha display setup
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_size(100,'%',100,'%');											// width 700px, height 500px
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_nb_line(50);													// lines per page
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_readonly_mode', __R__);					// Read & Write	
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__id_theme','blue');								// Define style	
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__background_picture', 'images/iknow.png');

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_column_separation',true);
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_row_separation',false);

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_top_bar_page',false);
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_bottom_bar_page',true);

    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_user_doc', false);					// user documentation button
    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_tech_doc', false);					// technical documentation button
    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_ticket', false);					// Tickets link

    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__display_mode', __NMOD__);					// Display mode

    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__key_url_custom_view', 'CC');				// Defined key for quick custom view loader in url browser

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__update_table_name', $_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['name']);		// Define table to update

    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__column_name_group_of_color', "MyGroupTheme");		// ( Optional ) Define custom column color name

    $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_attribute('__active_quick_search', true);				        // Quick search mode ( Optional : default true )
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
	
		//==================================================================
		// define column : ID
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`REQ`.`ID`','ID',$_SESSION[$ssid]['message'][472],__BBCODE__,__WRAP__,__CENTER__,__EXACT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_input_focus('ID', true);					// Focused
		//==================================================================

		//==================================================================
		// define column : Link for actions
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column("CONCAT('[urloff=./icode.php?ID=',`REQ`.`ID`,'".$get_url_param."',']', '".$_SESSION[$ssid]['message']['iknow'][516]."','[/url]',' ".$_SESSION[$ssid]['message']['iknow'][517]." ','[urloff=./modif_icode.php?&amp;ID=',`REQ`.`ID`,']".$_SESSION[$ssid]['message']['iknow'][518]."[/url]')",'Action',$_SESSION[$ssid]['message'][473],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : iCode title
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column("CASE
					`REQ`.`obsolete`
						WHEN '1'
						THEN CONCAT('[s][color=#888888]',`REQ`.`Titre`,'[/color][/s]')
						ELSE `REQ`.`Titre`
				END",'Titre',$_SESSION[$ssid]['message'][40],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================
		
		//==================================================================
		// define column : Area
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column("`POLE`.`Libelle`",'Pole',$_SESSION[$ssid]['message']['iknow'][32],__TEXT__,__WRAP__,__CENTER__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_col_quick_help('Pole',true);	
		
		// Match code
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_lov("	SELECT
																	`POLE`.`ID` AS `ID`,
																	`POLE`.`Libelle` AS `Pole`
																".$_SESSION[$ssid]['lisha']['configuration'][10]."
																	`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` `POLE`
																WHERE 1 = 1",
															$_SESSION[$ssid]['message'][504],
                                                            '`POLE`.`Libelle`',
															'Pole'
														   );
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`POLE`.`Libelle`','ID',$_SESSION[$ssid]['message'][98],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`POLE`.`Libelle`','Pole',$_SESSION[$ssid]['message']['iknow'][32],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov_order('Pole',__ASC__);
		//==================================================================
					
		//==================================================================
		// define column : Area version
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column("`REQ`.`VGS`",'VPole',$_SESSION[$ssid]['message']['iknow'][33],__TEXT__,__WRAP__,__CENTER__);
		
		// Match code
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_lov("	SELECT
																	`REQ`.`ID` AS `ID`,
																	`REQ`.`VGS` AS `VPole`
																".$_SESSION[$ssid]['lisha']['configuration'][10]."
																	`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_versions']['name']."` `REQ`
																WHERE 1 = 1
																	AND `PV`.`ID` = '||TAGLOV_Pole**`POLE`.`ID`||'",
														  	$_SESSION[$ssid]['message'][499],
                                                            '`REQ`.`VGS`',
														  	'VPole'
														  );
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`REQ`.`ID`','ID',$_SESSION[$ssid]['message'][98],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`REQ`.`VGS`','VPole',$_SESSION[$ssid]['message'][62],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov_order('VPole',__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : activity means team
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column("`THEME`.`libelle`",'Theme',$_SESSION[$ssid]['message']['iknow'][51],__TEXT__,__WRAP__,__CENTER__);
		
		// LOV
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_lov("	SELECT
																	`PT`.`ID` AS 'ID',
																	`PT`.`libelle` AS 'libelle'
																".$_SESSION[$ssid]['lisha']['configuration'][10]."
																	`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` `PT`
																WHERE 1 = 1
																	AND `PT`.`ID_POLE` = '||TAGLOV_Pole**`POLE`.`ID`||'",
															$_SESSION[$ssid]['message'][502],
                                                            '`PT`.`libelle`',
															'libelle'
														  );
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`PT`.`ID`','ID',$_SESSION[$ssid]['message'][98],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov('`PT`.`libelle`','libelle',$_SESSION[$ssid]['message'][47],__TEXT__,__WRAP__,__LEFT__);
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column_lov_order('libelle',__ASC__);
		//==================================================================
				
		//==================================================================
		// define column : Last update by
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`REQ`.`Last_update_user`','Modif',$_SESSION[$ssid]['message'][2],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================
				

		//==================================================================
		// define column : Last update date
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`REQ`.`last_update_date`','date',$_SESSION[$ssid]['message'][46],__DATE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Creator of iCode
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`REQ2`.`Last_update_user`','creator',$_SESSION[$ssid]['message'][48],__TEXT__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Current max version
		//==================================================================
		$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`REQ`.`Version`','Version',$_SESSION[$ssid]['message'][62],__TEXT__,__WRAP__,__CENTER__,__EXACT__);
		//==================================================================
						
		//==================================================================
		// define column : Engine
		//==================================================================
		if ($engine_title == '')
		{
			// Only if no engine defined
			$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column('`MOT`.`Description`','Description',$_SESSION[$ssid]['message'][49],__TEXT__,__WRAP__,__CENTER__,__EXACT__);
		}
		//==================================================================
		
		
		if($tags != '' || $grtags != '' || $Texists != '')
		{
			$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column($str3_tags,'TT',"Tags",__TEXT__,__WRAP__,__CENTER__,__PERCENT__);
			$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_column($str3_grtags,'GR',"Groupe Tags",__TEXT__,__WRAP__,__CENTER__,__PERCENT__);
		}
	//==================================================================
				
	//==================================================================
	// Column order : Define in ascending priority means first line defined will be first priority column to order by and so on...
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_order_column('date',__DESC__);
	//==================================================================
		
	//==================================================================
	// Table columns primary key
	// Caution : Can't change key column name from root query column name
	// It's not required to declare column key with define_column method
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_key(Array('ID','Version'));
	//==================================================================

	//==================================================================
	// Cyclic theme lines
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_line_theme("A5BEC2","0.7em","CCC9AD","0.7em","264A59","0.7em","264A59","0.7em","333","FFF");
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->define_line_theme("FFFFFF","0.7em","D0DCE0","0.7em","7292CE","0.7em","7292CE","0.7em","000","FFF");
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->new_graphic_lisha();

	//==================================================================
	// Do not remove this bloc
	// Keep this bloc at the end
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->generate_public_header();   
	$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->generate_header();
	//==================================================================