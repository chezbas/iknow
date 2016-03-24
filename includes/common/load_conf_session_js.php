<?php
	/**==================================================================
	* Database connexion and load conf parameters in both session and javascript
	====================================================================*/	

	/**==================================================================
	* Database connexion
	====================================================================*/	
	require('load_conf_session.php');
	/*===================================================================*/

	/**==================================================================
	* Loading conf in javascript page
	====================================================================*/	
	$s_conf = 'var conf = new Array();';	

	mysql_data_seek($result,0); // Move cursor on the first line of dataset	

	while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{
		if($row['valeur'])
		{ 
			// Record only row with defined value
			$s_conf .= 'conf['.$row['id'].']=\''.rawurlencode($row['valeur']).'\';';	
		}
	}
	
	echo $s_conf;
	
	// Record Keyword for jaascript
	$keywordstring = $_SESSION[$ssid]['configuration'][19];
	$tab_keyword = explode('|',$keywordstring);
	echo "var keywordlang = '".$tab_keyword[4]."';"; // Language url Keyword used
	/*===================================================================*/
		
?>