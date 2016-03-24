<?php
	/**==================================================================
	* Database connexion
	====================================================================*/	
	require('db_connect.php');
	/*===================================================================*/

	//==================================================================
	// Load configuration data into php session
	//==================================================================
	$sql = "SELECT
				`NAM`.`id` AS `id`,
				`EXT`.`value` AS `valeur`  
			FROM
				`".$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name']."` NAM,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension']."` EXT
			WHERE 1 = 1
				AND `NAM`.`application_release` = `EXT`.`application_release`
				AND `NAM`.`id` = `EXT`.`id`
			";
	$result = mysql_query($sql,$link);
	
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{
		$_SESSION[$ssid]['configuration'][$row['id']] = $row['valeur'];
	}
	//==================================================================