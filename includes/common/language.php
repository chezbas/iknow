<?php
	/**==================================================================
	* Recover language define in URL and check if exist in database // SIBY_READ_LANGUAGE_CONF
	====================================================================*/	

	// Get the key name of language directive
	// Id position : 4
	$a_valeur_interdite = explode('|',$_SESSION[$ssid]['configuration'][19]);

	if(!isset($_GET[$a_valeur_interdite[4]]))
	{	
		$_SESSION[$ssid]['langue'] = $_SESSION[$ssid]['configuration'][43];
	}
	else
	{
		$fi_lng = mysql_real_escape_string($_GET[$a_valeur_interdite[4]]);
		
		$sql = 'SELECT
					1  
				FROM 
					`'.$_SESSION['iknow'][$ssid]['struct']['tb_lang']['name'].'`
				WHERE 1 = 1
					AND `id` = "'.$fi_lng.'"';

		$result = mysql_query($sql);
		
		if(mysql_num_rows($result) == 1)
		{			
			$_SESSION[$ssid]['langue'] = $fi_lng;
		}
		else
		{
			$_SESSION[$ssid]['langue'] = $_SESSION[$ssid]['configuration'][43];	
		}		
	}

	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	/*===================================================================*/
	
	/**==================================================================
	* Read id on 2 digits for TinyMCE
	====================================================================*/	
	$sql = 'SELECT
				`id_tiny`  
			FROM 
				`'.$_SESSION['iknow'][$ssid]['struct']['tb_lang']['name'].'`
			WHERE 1 = 1
				AND `id` = "'.$_SESSION[$ssid]['langue'].'"';

	$result = mysql_query($sql);
	$_SESSION[$ssid]['langue_TinyMCE'] = mysql_result($result,0,0);
	/*===================================================================*/
?>