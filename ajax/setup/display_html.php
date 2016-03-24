<?php
	/**==================================================================
	 * Set of documentation framework includes
	 ====================================================================*/
	require('header.php');
	/*===================================================================*/		

	
	/**==================================================================
	 * Page database connexion
	 ====================================================================*/	
	require('../../includes/common/db_connect.php');
	/*===================================================================*/	

	
	//==================================================================
	// Get tree ID
	//==================================================================
	if(!isset($_POST["internal_id"]))
	{
		error_log_details('fatal','you need an tree ID');
		die();
	}
	$internal_id= $_POST["internal_id"];
	//==================================================================	

	//==================================================================
	// Get ID item node
	//==================================================================
	if(!isset($_POST["IDitem"]))
	{
		error_log_details('fatal','you need an item ID');
		die();
	}
	$id_item= $_POST["IDitem"];
	//==================================================================	

	//==================================================================
	// Get tree mode
	//==================================================================
	if(!isset($_POST["mode"]))
	{
		error_log_details('fatal','you need an mode');
		die();
	}
	$mode= $_POST["mode"];
	//==================================================================	
	
	//==================================================================
	// Get language in use
	//==================================================================
	if(!isset($_POST["language"]))
	{
		error_log_details('fatal','you need an language');
		die();
	}
	$language= $_POST["language"];
	//==================================================================	
	
	
	// Try local language if exists first
	$query = "	SELECT 
					`MTC`.`description` AS 'DETAILS'
				FROM `".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
			WHERE
				`MTC`.`application_release` = '".__MAGICTREE_APPLICATION_RELEASE__."'
				 AND `MTC`.`language` = '".$language."'
				 AND `MTC`.`id` = ".$id_item."
			";
	
	$result = mysql_query($query,$link);
	$row = mysql_fetch_array($result,MYSQL_ASSOC);	

	/**==================================================================
	 * Common dynamic page translation
	 ====================================================================*/	
	require('../../ajax/common/dynamic_doc.php');
	/*===================================================================*/	
		
	// Try local language first
	if( (!isset($row["DETAILS"]) || $row["DETAILS"] == '')  && $_SESSION[$ssid]['MT']['configuration'][1] != $language )
	{
		// No page define for this page, recover default language page
		$query = "	SELECT 
						`MTC`.`description` AS 'DETAILS'
					FROM `".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
				WHERE
					`MTC`.`application_release` = '".__MAGICTREE_APPLICATION_RELEASE__."'
					 AND `MTC`.`language` = '".$_SESSION[$ssid]['MT']['configuration'][1]."'
					 AND `MTC`.`id` = ".$id_item."
				";
		
		$result = mysql_query($query,$link);
		$row = mysql_fetch_array($result,MYSQL_ASSOC);	
		$row["DETAILS"] = '<div style="background-color: #fff; opacity:0.3;">'.$row["DETAILS"].'</div>';
	}

	//==================================================================
	// Search special pattern <isym:ixxx/>
	//==================================================================
	$motif = '#&lt;isym:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$replace = '<span style="color:red;font-weight:bold;">'.strtolower(substr($value,0,1)).'</span>'.ucfirst(substr($value,1));
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================		

	
	//==================================================================
	// Search special pattern <ifact:xxx/>
	//==================================================================
	$motif = '#&lt;ifact:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$replace = $value;
			$query = "	SELECT 
					`MTE`.`factory` AS 'factory'
				FROM `".$_SESSION[$ssid]['MT']['iksetup']->tree_extra."` `MTE`
			WHERE
				`MTE`.`application_release` = '".__MAGICTREE_APPLICATION_RELEASE__."'
				 AND `MTE`.`id` = ".$value."
			";
	
		$result_extension = mysql_query($query,$link);
		$row_extension = mysql_fetch_array($result_extension,MYSQL_ASSOC);	
		$replace = $row_extension['factory'];
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================	

	//==================================================================
	// Search special pattern <ivalue:xxx/>
	//==================================================================
	$motif = '#&lt;ivalue:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$replace = $value;
			$query = "	SELECT 
					`MTE`.`value` AS 'value'
				FROM `".$_SESSION[$ssid]['MT']['iksetup']->tree_extra."` `MTE`
			WHERE
				`MTE`.`application_release` = '".__MAGICTREE_APPLICATION_RELEASE__."'
				 AND `MTE`.`id` = ".$value."
			";
	
		$result_extension = mysql_query($query,$link);
		$row_extension = mysql_fetch_array($result_extension,MYSQL_ASSOC);	
		$replace = $row_extension['value'];
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================	

	//==================================================================
	// Replace by page
	// Search special pattern <ipage:xxx/>
	//==================================================================
	$motif = '#&lt;ipage:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$query = "
				SELECT
				`MTC`.`title` AS 'value'
			FROM 
				`".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
			WHERE 1 = 1
				AND `MTC`.`application_release`	= '".__MAGICTREE_APPLICATION_RELEASE__."'
				AND `MTC`.`language` = '".$language."'
				AND `MTC`.`id`		= ".$value."
		";
		$result = mysql_query($query,$link);
		$rowl = mysql_fetch_array($result,MYSQL_ASSOC);

		if(!isset($rowl['value']))
		{
			// No title yet defined in local language, recover title from root language
			// add red flag to show that value come from root language
			$query = "
					SELECT
					CONCAT('<span class=\"language_not_exists\">',`MTC`.`title`,'</span>') AS 'value'
				FROM 
					`".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
				WHERE 1 = 1
					AND `MTC`.`application_release`	= '".__MAGICTREE_APPLICATION_RELEASE__."'
					AND `MTC`.`language` = '".$_SESSION[$ssid]['MT']['configuration'][1]."'
					AND `MTC`.`id`		= ".$value."
			";
			$result = mysql_query($query,$link);
			$rowl = mysql_fetch_array($result,MYSQL_ASSOC);
		}
		
		if($out[2][$key] == '')
		{
			$replace = '<span class="jump" onclick="read_details(\'iksetup\',\''.$value.'\',\'D\',\'D\')">'.$_SESSION[$ssid]['MT']['iksetup']->convertBBCodetoHTML($rowl['value']).'</span>';
		}
		else
		{
			$replace = '<span class="jump" onclick="read_details(\'iksetup\',\''.$value.'\',\'D\',\'D\')">'.$out[2][$key].'</span>';
		}
		//$replace = '<span class="jump" onclick="read_details(\'iksetup\',\''.$value.'\',\'D\',\'D\')">'.$rowl['value'].'</span>';
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================	

	//==================================================================
	// Jump to idoc page
	// Search special pattern <idoc:idpage:free_description/>
	//==================================================================
	$motif = '#&lt;idoc:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);
	
	foreach($out[1] as $key => $value)
	{
		$tab = explode(":",$value);
		
		$replace = '<span class="jump"><a href="index.php?id='.$tab[0].'" target="_blank">'.$tab[1].'</a></span>';
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}	
	//==================================================================	
	
	
	$id=$id_item;
	
	$tab_to_root = array();
	$current = get_parent($ssid,$language,$link,$id);
	while( $current[1] != '') // Till a parent exists
	{
		array_unshift($tab_to_root,array($current[0],$current[1],$current[2]));
		$current = get_parent($ssid,$language,$link,$current[1]);
	}
	
	// Build path string
	//$path_to_root = $_SESSION[$ssid]['message'][13].' : ';
	$path_to_root = '<a href= "index.php?IKLNG='.$_SESSION[$ssid]['langue'].'&ssid='.$ssid.'" target="_blank"><div id="boutton_iknow" class="boutton_iknow" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][8]).'\')"></div></a>';
	$path_to_root .= '<div class="home_path" onclick="read_details(\'iksetup\',\'48\',\'D\',\'D\')" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message']['iknow'][29]).'\')"></div> : ';
	$separator = '';
	while(list($parent,$value) = each($tab_to_root))
	{
		//==================================================================
		// Manage BBCode is setup on
		//==================================================================
		$value[2] = htmlentities($value[2],ENT_QUOTES);
		
		if($_SESSION[$ssid]['MT']['iksetup']->use_bbcode)
		{
			$value[2] = $_SESSION[$ssid]['MT']['iksetup']->convertBBCodetoHTML($value[2]);
		}
		//==================================================================
		$path_to_root = $path_to_root . '<span class="mouse" onclick="read_details(\''.$internal_id.'\',\''.$value[0].'\',\''.$mode.'\',\'D\')">'.$value[2].'</span>' . $separator;
	}
	
	if($separator <> '')
	{
		$path_to_root = substr($path_to_root,0,-strlen($separator));
	}
	
	$html = '<div id="head_path" class="head_path"><span class="lien_arbo">'.$path_to_root.'</span></div>';

	//==================================================================	
	// Extra features
	//==================================================================	
	$query = "	SELECT 
					`MTE`.`value` AS 'valeur'
				FROM `".$_SESSION[$ssid]['MT']['iksetup']->tree_extra."` `MTE`
			WHERE
				`MTE`.`application_release` = '".__MAGICTREE_APPLICATION_RELEASE__."'
				 AND `MTE`.`id` = ".$id_item."
			";
	
	$result_extension = mysql_query($query,$link);
	$row_extension = mysql_fetch_array($result_extension,MYSQL_ASSOC);	
	//==================================================================	
	
	if($row_extension['valeur'] != '')
	{
		$html .= '<div id="details_value" class="details_value">'.$_SESSION[$ssid]['message'][9].' : <span class="value_highlight">'.$row_extension['valeur'].'</span>
		</div>';
	}
	$html .= '<div id="details_body" class="details_body">'.$row["DETAILS"].'</div>';
	
	// Keep current page loaded in php session
	$_SESSION[$ssid]['current_read_page'] = $id;
	
	$retour = array("HTML" => $html,"PATH" => $path_to_root);
	echo json_encode($retour);

	//==================================================================
	// return parent of $id
	//==================================================================
	function get_parent($ssid,$language,$link,$id)
	{
		$query = "	SELECT
						`MTN`.`id` AS 'id',
						`MTC`.`title` AS 'value',
						`MTN`.`parent` AS 'parent'
					FROM 
						`".$_SESSION[$ssid]['MT']['iksetup']->tree_node."` `MTN`,
						`".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
					WHERE 1 = 1
						AND `MTN`.`id` = `MTC`.`id`
						AND `MTN`.`application_release` = `MTC`.`application_release`
						AND `MTN`.`application_release`	= '".__MAGICTREE_APPLICATION_RELEASE__."'
						AND `MTC`.`language` = '".$language."'
						AND `MTN`.`id`		= ".$id."
				";
		$result = mysql_query($query,$link);
		$row = mysql_fetch_array($result,MYSQL_ASSOC);	

		// No local language define, recover root language and mark in red
		if (!isset($row['id']))
		{
			$query = "	SELECT
							`MTN`.`id` AS 'id',
							CONCAT('<span class=\"language_not_exists\">',`MTC`.`title`,'</span>') AS 'value',
							`MTN`.`parent` AS 'parent'
						FROM 
							`".$_SESSION[$ssid]['MT']['iksetup']->tree_node."` `MTN`,
							`".$_SESSION[$ssid]['MT']['iksetup']->tree_caption."` `MTC`
						WHERE 1 = 1
							AND `MTN`.`id` = `MTC`.`id`
							AND `MTN`.`application_release` = `MTC`.`application_release`
							AND `MTN`.`application_release`	= '".__MAGICTREE_APPLICATION_RELEASE__."'
							AND `MTC`.`language` = '".$_SESSION[$ssid]['MT']['configuration'][1]."'
							AND `MTN`.`id`		= ".$id."
					";
			$result = mysql_query($query,$link);
			$row = mysql_fetch_array($result,MYSQL_ASSOC);	
		}
		
		return array($row['id'],$row['parent'],$row['value']);		
	}
	//==================================================================
?>