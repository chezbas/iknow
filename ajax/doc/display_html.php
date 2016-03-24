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
				FROM `".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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
					FROM `".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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
	// Configuration parameters <iconf:xx/>
	//==================================================================
	$motif = '#&lt;iconf:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$replace = $_SESSION[$ssid]['configuration'][$value];
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================	
		
	//==================================================================
	// Replace by node title
	// Search special pattern <ipage:xxx/>
	//==================================================================
	//$motif = '#&lt;ipage:([^/]+)[ ]*/&gt;#i';
	$motif = '#&lt;ipage:([0-9]+)([^/]*)/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[1] as $key => $value)
	{
		$query = "
				SELECT
				`MTC`.`title` AS 'value'
			FROM 
				`".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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
					`".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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
			$replace = '<span class="jump" onclick="read_details(\'ikdoc\',\''.$value.'\',\'D\',\'D\')">'.$_SESSION[$ssid]['MT']['ikdoc']->convertBBCodetoHTML($rowl['value']).'</span>';
		}
		else
		{
			$replace = '<span class="jump" onclick="read_details(\'ikdoc\',\''.$value.'\',\'D\',\'D\')">'.$out[2][$key].'</span>';
		}
		//$replace = '<span class="jump" onclick="read_details(\'ikdoc\',\''.$value.'\',\'D\',\'D\')">'.$rowl['value'].'</span>';
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================	
	
	//==================================================================
	// Current page text
	// Search special pattern <itext:xxx:value/>
	// Use &1 into text number xxx to be replace by value
	//==================================================================
	$motif = '#&lt;itext:([^/]+):([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);
	
	foreach($out[1] as $key => $value)
	{
		switch ($out[2][$key])
		{
			case '$$application_version$$':
				$out[2][$key] = $_SESSION['iknow']['version_soft'];
			break;
		}
		$replace = str_replace('&1',$out[2][$key],$_SESSION[$ssid]['message'][$value]);
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
			
	}	
	//==================================================================		

	//==================================================================
	// Common text element :
	// Search special pattern ( main text iknow ) <itextik:xxx:value/>
	// Use &1 into text number xxx to be replace by value
	//==================================================================
	$motif = '#&lt;itextik:([^/]+):([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);
	
	foreach($out[1] as $key => $value)
	{
		$replace = str_replace('&1',$out[2][$key],$_SESSION[$ssid]['message']['iknow'][$value]);
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}	
	//==================================================================		

	
	//==================================================================
	// Text with page id : Eg icode233
	// Search special pattern ( main text iknow ) <itextid:xxx:value/>
	// Use &1 into text number xxx to be replace by value
	//==================================================================
	$motif = '#&lt;itextid:([^/]+):([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);
	
	foreach($out[1] as $key => $value)
	{
		$replace = str_replace('&1',$out[2][$key],$_SESSION[$ssid]['message_idapge']['iknow'][$value]);
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}	
	//==================================================================		
	
	//==================================================================
	// reserved word <ireserved_word/>
	//==================================================================	
	$tableau_valeur = explode("|",$_SESSION[$ssid]['configuration'][19]);

	$html_reserved = '<table class="main_table">';
	$html_reserved = $html_reserved.'<tr class="main_tr">';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][14].'</td>';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][15].'</td>';
	$html_reserved = $html_reserved. '</tr>';
	foreach($tableau_valeur as $key => $value ) {
		$val = '';
		switch ($value)
		{
		case 'ID':
			$val = $_SESSION[$ssid]['message'][16];
		break;
		case 'SSID':
			$val = $_SESSION[$ssid]['message'][17];
		break;
		case 'VERSION':
			$val = $_SESSION[$ssid]['message'][18];
		break;
		case 'tab-level':
			$val = $_SESSION[$ssid]['message'][19];
		break;
		case 'IKLNG':
			$val = $_SESSION[$ssid]['message'][20];
		break;
		case 'IK_VALMOD':
			$val = $_SESSION[$ssid]['message'][21];
		break;
		case 'IK_CARTRIDGE':
			$val = $_SESSION[$ssid]['message'][22];
		break;
		case 'IKBACKUP':
			$val = $_SESSION[$ssid]['message'][23];
		break;
		}
		$html_reserved = $html_reserved.'<tr class="main_tr">';
			$html_reserved = $html_reserved. '<td class="row_table">'.$value."</td>";
			$html_reserved = $html_reserved. '<td class="row_table">'.$val.'</td>';
		$html_reserved = $html_reserved. '</tr>';
		}
	$html_reserved = $html_reserved. '</table>';

	$replace = $html_reserved;
	$row["DETAILS"] = str_replace('&lt;ireserved_word/&gt;',$replace,$row["DETAILS"]);
	//==================================================================

	//==================================================================
	// forbidden digit <ireserved_digit/>
	//==================================================================	
	$tableau_valeur = explode("|",$_SESSION[$ssid]['configuration'][14]);
	
	$html_reserved = '<table class="main_table">';
	$html_reserved = $html_reserved.'<tr class="main_tr">';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][24].'</td>';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][15].'</td>';
	$html_reserved = $html_reserved. '</tr>';
	foreach($tableau_valeur as $key => $value ) {
		$val = '';
		switch ($value)
		{
		case '(':
			$val = $_SESSION[$ssid]['message'][25];
		break;
		case ')':
			$val = $_SESSION[$ssid]['message'][26];
		break;
		case '&':
			$val = $_SESSION[$ssid]['message'][27];
		break;
		case '\\':
			$val = $_SESSION[$ssid]['message'][28];
		break;
		case '$':
			$val = $_SESSION[$ssid]['message'][29];
		break;
		}
		$html_reserved = $html_reserved.'<tr class="main_tr">';
			$html_reserved = $html_reserved. '<td class="row_table">'.$value."</td>";
			$html_reserved = $html_reserved. '<td class="row_table">'.$val.'</td>';
		$html_reserved = $html_reserved. '</tr>';
		}
	$html_reserved = $html_reserved. '</table>';

	$replace = $html_reserved;
	$row["DETAILS"] = str_replace('&lt;ireserved_digit/&gt;',$replace,$row["DETAILS"]);
	//==================================================================


	//==================================================================
	// default postfix string <ipostfix/>
	//==================================================================	
	$tableau_valeur = explode("SEP",$_SESSION[$ssid]['configuration'][27]);
	
	$html_reserved = '<table class="main_table">';
	$html_reserved = $html_reserved.'<tr class="main_tr">';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][31].'</td>';
	$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][15].'</td>';
	$html_reserved = $html_reserved. '</tr>';
	foreach($tableau_valeur as $key => $value ) {
		$val = '';
		switch ($value)
		{
		case "'":
			$val = $_SESSION[$ssid]['message'][30];
		break;
		case ' ':
			$val = $_SESSION[$ssid]['message'][32];
		break;
		case '*':
			$val = $_SESSION[$ssid]['message'][33];
		break;
		case '<br />':
			$value="&lt;br/&gt;";
			$val = $_SESSION[$ssid]['message'][34];
		break;
		case '</p>':
			$value="&lt;/p&gt;";
			$val = $_SESSION[$ssid]['message'][35];
		break;
		case '!':
			$val = $_SESSION[$ssid]['message'][36];
		break;
		case 9:
			$value="chr(9)";
			$val = $_SESSION[$ssid]['message'][37];
		break;
		case 10:
			$value="chr(10)";
			$val = $_SESSION[$ssid]['message'][38];
		break;
		case 13:
			$value="chr(13)";
			$val = $_SESSION[$ssid]['message'][39];
		break;
		case ',':
			$val = $_SESSION[$ssid]['message'][40];
		break;
		case ';':
			$val = $_SESSION[$ssid]['message'][41];
		break;
		case '(':
			$val = $_SESSION[$ssid]['message'][25];
		break;
		case ')':
			$val = $_SESSION[$ssid]['message'][26];
		break;
		case '=':
			$val = $_SESSION[$ssid]['message'][42];
		break;
		case '|':
			$val = $_SESSION[$ssid]['message'][43];
		break;
		case '[':
			$val = $_SESSION[$ssid]['message'][44];
		break;
		case ']':
			$val = $_SESSION[$ssid]['message'][45];
		break;
		case '"':
			$val = $_SESSION[$ssid]['message'][46];
		break;
		case '{':
			$val = $_SESSION[$ssid]['message'][47];
		break;
		case '}':
			$val = $_SESSION[$ssid]['message'][48];
		break;
		case '&amp;':
			$value = '&amp;amp;';
			$val = $_SESSION[$ssid]['message'][27];
		break;
		case '&':
			$val = $_SESSION[$ssid]['message'][27];
		break;
		case '%':
			$val = $_SESSION[$ssid]['message'][49];
		break;
		case '-':
			$val = $_SESSION[$ssid]['message'][50];
		break;
		case '.':
			$val = $_SESSION[$ssid]['message'][51];
		break;
		case '\\':
			$val = $_SESSION[$ssid]['message'][28];
		break;
		case '/':
			$val = $_SESSION[$ssid]['message'][52];
		break;
		case '`':
			$val = $_SESSION[$ssid]['message'][53];
		break;
		case '$':
			$val = $_SESSION[$ssid]['message'][29];
		break;
		case '@':
			$val = $_SESSION[$ssid]['message'][54];
		break;
		}
		$html_reserved = $html_reserved.'<tr class="main_tr">';
			$html_reserved = $html_reserved. '<td class="row_table">'.$value."</td>";
			$html_reserved = $html_reserved. '<td class="row_table">'.$val.'</td>';
		$html_reserved = $html_reserved. '</tr>';
		}
	$html_reserved = $html_reserved. '</table>';

	$replace = $html_reserved;
	$row["DETAILS"] = str_replace('&lt;ipostfix/&gt;',$replace,$row["DETAILS"]);
	//==================================================================
	
	
	
	//==================================================================
	// Build path string
	//==================================================================
	$id=$id_item;
	
	$tab_to_root = array();
	$current = get_parent($ssid,$language,$link,$id);
	while( $current[1] != '') // Till a parent exists
	{
		array_unshift($tab_to_root,array($current[0],$current[1],$current[2]));
		$current = get_parent($ssid,$language,$link,$current[1]);
	}

	$preference = '<div id="boutton_preference" class="boutton_preference" onClick="active_expand_tools_bar()" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][58]).'\')"></div>';
	$path_to_root = $preference.'<div class="home_path" onclick="read_details(\'ikdoc\',\'1\',\'D\',\'D\')" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message']['iknow'][29]).'\')"></div> : ';
	$separator = '';
	while(list($parent,$value) = each($tab_to_root))
	{
		//==================================================================
		// Manage BBCode is setup on
		//==================================================================
		$value[2] = htmlentities($value[2],ENT_QUOTES);
		
		if($_SESSION[$ssid]['MT']['ikdoc']->use_bbcode)
		{
			$value[2] = $_SESSION[$ssid]['MT']['ikdoc']->convertBBCodetoHTML($value[2]);
		}
		//==================================================================
		$path_to_root = $path_to_root . '<span class="mouse" onclick="read_details(\''.$internal_id.'\',\''.$value[0].'\',\''.$mode.'\',\'D\')">'.$value[2].'</span>' . $separator;
	}
	
	if($separator <> '')
	{
		$path_to_root = substr($path_to_root,0,-strlen($separator));
	}
	//==================================================================

	$html = '<div id="head_path" class="head_path"><span class="lien_arbo">'.$path_to_root.'</span></div>
	<div id="details_body" class="details_body">'.$row["DETAILS"].'</div>';
	
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
						`".$_SESSION[$ssid]['MT']['ikdoc']->tree_node."` `MTN`,
						`".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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
							`".$_SESSION[$ssid]['MT']['ikdoc']->tree_node."` `MTN`,
							`".$_SESSION[$ssid]['MT']['ikdoc']->tree_caption."` `MTC`
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