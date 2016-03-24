libelle = new Array();libelle_common = new Array();<?php

	/**==================================================================
	 * Select optimised set of text label depend on $type_soft
	 * type_soft means set of screen ( eg : ifiche, icode, isearch,...) See enum value of $_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'] table field 'objet'
	 * In all case, we loaded common label text design by objet = 'iknow'
	 ====================================================================*/	
	if(isset($type_soft))
	{
		switch($type_soft) 
		{
			case 1:
				$objet = 'ifiche';
				break;
			case 2:
				$objet = 'ifiche';
				break;
			case 3:
				$objet = 'icode';
				break;
			case 4:
				$objet = 'icode';
				break;
			case 5:
				$objet = 'isearch';
				break;
			case 6:
				$objet = 'iacces';
				break;
			case 7:
				$objet = 'ipassword';
				break;
			case 8:
				$objet = 'icheck'; // Coherence check
				break;
			case 9:
				$objet = 'ilock'; // Lock session management screen
				break;
			case 10:
				$objet = 'iarea'; // Area Module liste : processpole.php
				break;
			case 'ihome':
				$objet = 'ihome'; // Special behaviour cause need to load all text for documentation
				break;
			case 'ibug':
				$objet = 'ibug'; // index.php in bugs
				break;
			case 'isetup':
				$objet = 'isetup'; // index.php in setup
				break;
				case 999: /* Load only common iknow text */
				$objet = 'blackhole';
				break;
			default:
				error_log_details("fatal","Unknown type_soft < ".$type_soft." >");
				break;
		}
	}
	else // if no type_soft define, then load only common label
	{
		$objet = "iknow";
	}
	/*===================================================================*/	
		
				
	/**==================================================================
	 * Text then no documentation page defnie : more help 
	 ====================================================================*/	
	$sql = 'SELECT texte 
			FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].'  
			WHERE id_lang = "'.$_SESSION[$ssid]['langue'].'" 
			AND objet = "iknow"
			AND version_active = '.$_SESSION['iknow']['version_soft'].' 
			AND id_texte = 205';

	$result = mysql_query($sql,$link);	
	$s_texte_plus_aide = mysql_result($result,0,0);
	/*===================================================================*/	

	
	$sql = "SELECT
				`id_texte` AS id_texte,
				texte,
				help_link,objet
			FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name']."
			WHERE 1 = 1
				AND `id_lang` = '".$_SESSION[$ssid]['langue']."' 
				AND `objet` IN ('".$objet."','iknow')
				AND `version_active` = '".$_SESSION['iknow']['version_soft']."' 
				ORDER BY 'id_texte'
			";
	$result = mysql_query($sql,$link);

	/**==================================================================
	 * Load label text into 2 area
	 * Page area :
	 * 		Label are loaded in that kind of session form $_SESSION[$ssid]['message'][id_numer]
	 * Common area :
	 * 		Design by objet = iknow
	 * 		Label are loaded in that kind of session form $_SESSION[$ssid]['message']['iknow'][id_numer]
	 ====================================================================*/	
	$w_libelle = ''; // Clear local variable for screen specific label
	$w_libelle_common = ''; // Clear local variable for common iknow label

	while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{
		// Load help only if it's defined
		if($row['help_link'] != 0)
		{
			$row['texte'] = '<table class="tableau_aide"><tr class="tableau_aide"><td class="tableau_aide">'.$row['texte'].' ( </td><td class="aide"></td><td class="tableau_aide" onclick="javascript:window.open();"> '.$s_texte_plus_aide.' <b>Ctrl+F1</b> )</td><td class="tableau_aide"></td></tr></table>';	
		}	

		// Load specific object label into javascript variable libelle and php session $_SESSION[$ssid]['message'][xxx]
		if($row['objet'] == $objet)
		{ 
			$_SESSION[$ssid]['message'][$row['id_texte']] = $row['texte']; // Load label in php session
			$w_libelle .= 'libelle['.$row['id_texte'].']=\''.rawurlencode($row['texte']).'\';'; // Load label in javascript page
		}
		else // It's not specific object label, so load into common iknow label area
		{
			$_SESSION[$ssid]['message']['iknow'][$row['id_texte']] = $row['texte']; // Load label in php session
			$w_libelle_common .= 'libelle_common['.$row['id_texte'].']=\''.rawurlencode($row['texte']).'\';'; // Load label in javascript page
		}			
	}
	/*===================================================================*/	

	if ($objet == 'ihome')
	{
		$sql = "SELECT
					`id_texte` AS id_texte,
					texte,
					help_link,
					objet
				FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name']."
				WHERE 1 = 1
					AND `id_lang` = '".$_SESSION[$ssid]['langue']."'
					AND `objet` not IN ('iknow', 'ihome') 
					AND `version_active` = '".$_SESSION['iknow']['version_soft']."' 
					ORDER BY 'id_texte'
				";
		$result = mysql_query($sql,$link);	
	
		/**==================================================================
		 * Load label only in php space
		 * 		Label are loaded in that kind of session form $_SESSION[$ssid]['message_idapge'][id_text+id_number]
		 ====================================================================*/	
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			$_SESSION[$ssid]['message_idapge']['iknow'][$row['objet'].$row['id_texte']] = $row['texte']; // Load label in php session
		}
		/*===================================================================*/	
	}
	echo $w_libelle.$w_libelle_common;
	$ikn_txt = &$_SESSION[$ssid]['message'];
?>