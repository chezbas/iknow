<?php
	//==================================================================
	// Contributors
	// Search special pattern <p><icontributors/></p>
	//==================================================================
	$motif = '#<p>&lt;icontributors/&gt;</p>#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	if(isset($_SESSION[$ssid]['message'][59]))
	{
		$contributs = $_SESSION[$ssid]['message'][59];
		$contributs = nl2br($contributs);
		
		foreach($out[0] as $key => $value)
		{
			$replace = '<div class="contributor_top_offet"></div>
						<div id="contributor" class="contributor">
						<div class="contributor_top"></div>
						<div id = "contributor_body" class="contributor_body">'.$contributs.'</div>
						<div class="contributor_bottom"></div> 
						</div>';
			$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
		}
	}
	//==================================================================	

	//==================================================================
	// Language available
	// Search special pattern <ilistlang/>
	//==================================================================
	$motif = '#&lt;ilistlang/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[0] as $key => $value)
	{
		$query = "
				SELECT
				`LAN`.`id` AS 'id',
				`LAN`.`label` AS 'label'
			FROM 
				`".$_SESSION['iknow'][$ssid]['struct']['tb_lang']['name']."` `LAN`
			WHERE 1 = 1
		";
		$result = mysql_query($query,$link);

		$html_reserved = '<table class="main_table">';
		$html_reserved = $html_reserved.'<tr class="main_tr">';
		//$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][14].'</td>';
		//$html_reserved = $html_reserved. '<td class="head_table">'.$_SESSION[$ssid]['message'][15].'</td>';
		$html_reserved = $html_reserved. '</tr>';

		while($rowl = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$html_reserved = $html_reserved.'<tr class="main_tr">';
				$html_reserved = $html_reserved. '<td class="row_table">'.$rowl['id']."</td>";
				$html_reserved = $html_reserved. '<td class="row_table">'.$rowl['label'].'</td>';
			$html_reserved = $html_reserved. '</tr>';
		}
		$html_reserved = $html_reserved. '</table>';

		$replace = $html_reserved;
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}
	//==================================================================	

	//==================================================================
	// Current language used
	// Search special pattern <icurlang/>
	//==================================================================
	$motif = '#&lt;icurlang/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[0] as $key => $value)
	{
		$query = "
				SELECT
				`LAN`.`label` AS 'label'
			FROM 
				`".$_SESSION['iknow'][$ssid]['struct']['tb_lang']['name']."` `LAN`
			WHERE 1 = 1
				AND `LAN`.`id` = '".$language."'
		";
		$result = mysql_query($query,$link);
		$rowl = mysql_fetch_array($result,MYSQL_ASSOC);

		$replace = $rowl['label'];
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}
	//==================================================================	

	//==================================================================
	// Display system information
	// Search special pattern <isys:$system/>
	// $system will have value below
	// 		language 	 		 : current language used
	//		language_url		 : keyword plus language currently in use
	//		keyword_id			 : Get keyword to design iobject id
	//		keyword_ssid		 : Get keyword to design session identifier
	//		keyword_version		 : Get keyword to force version in URL
	//		keyword_language	 : Get keyword to force language in URL
	//		keyword_valorization : Get keyword to force setup valorization mode
	//		php					 : php version
	//		MySQL				 : MySQL version
	//		apache				 : Get web page engine ( Apache, nginx.. etc.. )
	//		iknow				 : Iknow version
	//		mt					 : MagicTree name
	//		mtver				 : MagicTree Version
	//		lisha				 : lisha name
	//		lishaver			 : lisha Version
	//		browser				 : Browser client
	//		session_timeout		 : Session timeout
	//		ssid				 : Current ssid number
	//		...
	//==================================================================
	$motif = '#&lt;isys:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$row["DETAILS"],$out);
	
	foreach($out[1] as $key => $value)
	{
		$_local_sys = '';
		switch($value)
		{
			case 'language':
				$_local_sys = $language;
			break;
			case 'php':
				$_local_sys = phpversion();
			break;
			case 'MySQL':
				$_local_sys = mysql_get_server_info();
			break;
			case 'apache':
				//$_local_sys = apache_get_version();
				$_local_sys = $_SERVER["SERVER_SOFTWARE"];
			break;
			case 'iknow':
				$_local_sys = $_SESSION['iknow']['version_soft'];
			break;
			case 'mt':
				$_local_sys = __MAGICTREE_APPLICATION_NAME;
			break;
			case 'mtver':
				$_local_sys = __MAGICTREE_APPLICATION_RELEASE__;
			break;
			case 'lisha':
				$_local_sys = __LISHA_APPLICATION_NAME__;
			break;
			case 'lishaver':
				$_local_sys = __LISHA_APPLICATION_RELEASE__;
			break;
			case 'language_url':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[4]."=".$language;
			break;
			case 'keyword_id':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[0];
			break;
			case 'keyword_ssid':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[1];
			break;
			case 'keyword_version':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[2];
			break;
			case 'keyword_language':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[4];
			break;
			case 'keyword_valorization':
				$reserved_word = explode('|',$_SESSION[$ssid]['configuration'][19]);
				$_local_sys = $reserved_word[5];
			break;
			case 'ssid':
				$_local_sys = $ssid;
			break;
			case 'session_timeout':
				$temps = ini_get('session.gc_maxlifetime');
				$heure = floor($temps/3600);
				$minute = floor(($temps - ($heure * 3600))/60);
				$string_heure = str_pad($heure, 2, "0", STR_PAD_LEFT);
				$string_minute = str_pad($minute, 2, "0", STR_PAD_LEFT);
				$_local_sys = $string_heure.":".$string_minute;
			break;
			case 'browser':
				$ua = $_SERVER["HTTP_USER_AGENT"];
				
				// Safari
				$motif = '#Version/([^ ]+) ([^ ]+)/#i';
				if(preg_match_all($motif,$ua,$browser))
				{	
					$_local_sys = $browser[2][0]." - ".$browser[1][0];
				}
				
				// Firefox
				$motif = '#(firefox)/([^ ]+)#i';
				if(preg_match_all($motif,$ua,$browser))
				{	
					$_local_sys = $browser[1][0]." - ".$browser[2][0];
				}

				// Chrome
				$motif = '#(chrome)/([^ ]+)#i';
				if(preg_match_all($motif,$ua,$browser))
				{	
					$_local_sys = $browser[1][0]." - ".$browser[2][0];
				}
				
				// Opera
				$motif = '#(opera).+Version/([^ ]+)#i';
				if(preg_match_all($motif,$ua,$browser))
				{	
					$_local_sys = $browser[1][0]." - ".$browser[2][0];
				}
			break;
		}
		$row["DETAILS"] = str_replace($out[0][$key],$_local_sys,$row["DETAILS"]);
			
	}	
	//==================================================================	

	//==================================================================
	// Current area defined
	// Search special pattern <iarealist/>
	//==================================================================
	$motif = '#&lt;iarealist/&gt;#i';

	preg_match_all($motif,$row["DETAILS"],$out);

	foreach($out[0] as $key => $value)
	{
		$query = "
				SELECT
				`POL`.`Libelle` AS 'label'
			FROM 
				`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` `POL`
			WHERE 1 = 1
		";
		
		$result = mysql_query($query,$link);
		
		$html_reserved = '<table class="main_table">';
		$html_reserved = $html_reserved.'<tr class="main_tr">';
		$html_reserved = $html_reserved. '</tr>';
		while($rowl = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$html_reserved = $html_reserved.'<tr class="main_tr">';
				$html_reserved = $html_reserved. '<td class="row_table">'.$rowl['label'].'</td>';
			$html_reserved = $html_reserved. '</tr>';
		}
		$html_reserved = $html_reserved. '</table>';
		
		$replace = $html_reserved;
		$row["DETAILS"] = str_replace($out[0][$key],$replace,$row["DETAILS"]);
	}
	//==================================================================	
?>