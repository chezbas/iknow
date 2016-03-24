<?php
	// Get ssid
	require('ssid.php');
	$ssid = $p_ssid;
	
	/**==================================================================
	* Database connexion
	====================================================================*/	
	require('db_connect.php');
	/**===================================================================*/

	// TODO Remove hard coded purge time
	$sql = '
			SELECT
				`id_temp`,
				`type`
			FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'].'` 
			WHERE 1 = 1 
				AND TIMEDIFF(NOW(),last_update) > "00:30:00"
			';
	/*
	(
														SELECT `value` AS "valeur"
														FROM
															`'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension'].'`
														WHERE 1 = 1
															AND `id` = 39
															AND `application_release` = "'.__MAGICTREE_APPLICATION_RELEASE__.'"
														)
	 */
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
	{
		if($row['type'] == 1 || $row['type'] == 2)
		{
			// on vide la table ifiche_parametres
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name'].'` WHERE id_fiche = '.$row['id_temp'].';';
			mysql_query($sql) or die ('Error in query (1) '.mysql_error().$sql);
			
			// on vide la table tags des tags
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name'].'` WHERE ID = '.$row['id_temp'].' AND objet = "ifiche";';
			mysql_query($sql) or die ('Error in query (2): '.mysql_error().$sql);
			
			// on vide la table tags des logs
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_log_action']['name'].'` WHERE ID = '.$row['id_temp'].' AND objet = "ifiche";';
			mysql_query($sql) or die ('Error in query (3): '.mysql_error().$sql);
		}
		else
		{
			// on vide la table des codes
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name'].'` WHERE ID = '.$row['id_temp'].';';
			mysql_query($sql) or die ('Error in query (1): '.mysql_error().$sql);
							
			// on vide la table icode_parametres
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name'].'` WHERE ID = '.$row['id_temp'].';';
			mysql_query($sql) or die ('Error in query (2): '.mysql_error().$sql);
			
			// on vide la table tags des codes
			$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name'].'` WHERE ID = '.$row['id_temp'].' AND objet = "icode";';
			mysql_query($sql) or die ('Error in query (3): '.mysql_error().$sql);
		}	
		
		// on vide la table des url temp
		$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name'].'` WHERE ID_temp = '.$row['id_temp'];
		mysql_query($sql) or die('Error in query (4): '.mysql_error().$sql);
			
		// on vide la table messages système
		//$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_msg_maintenance']['name'].'` WHERE ID_TEMP = '.$row['id_temp'];
		//mysql_query($sql) or die ('Error in query (5): '.mysql_error().$sql);
			
		// on vide la table tags des lock
		$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'].'` WHERE id_temp = '.$row['id_temp'];
		mysql_query($sql) or die ('Error in query (6): '.mysql_error().$sql);
	}