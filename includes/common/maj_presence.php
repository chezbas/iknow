<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('buffering.php');
	/*===================================================================*/	

	
	// Load current SSID
	$ssid = $_POST["ssid"];
	
	
	/**==================================================================
	* Database connexion
	====================================================================*/	
	require('db_connect.php');
	/*===================================================================*/

	
	//==================================================================
	// Refresh the lock row of iObject ssid in use
	// Means, the iObject client window is still alive
	// Update the last_update field
	//==================================================================
	$str = 'UPDATE 
				`'.$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'].'` 
			SET `last_update` = NOW() 
			WHERE 1 = 1
				AND `id_temp` = "'.$_POST["id_temp"].'"
		   ';
	
	mysql_query($str) or die('Impossible de faire un lock de l\'objet');
	//==================================================================
		
	//==================================================================
	// Check if current id temp have still a ssid in lock table
	//==================================================================
	$str = 'SELECT
				1 
			FROM
				`'.$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'].'` 
			WHERE 1 = 1
				AND `id_temp` = "'.$_POST["id_temp"].'" 
				AND ssid = "'.$_POST["ssid"].'"
		   ';

	$result = mysql_query($str);
	//==================================================================

	$date_time  = '<date>'.date('m/d/Y').'</date>';
	$date_time .= '<time>'.date('H:i:s').'</time>';
	
	if(mysql_num_rows($result) == 0)
	{
		//==================================================================
		// The user doesn't have a line on the database
		// ssid was deleted in database
		// erreur = true means dead msgbox for fatal error
		//==================================================================
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<message_controle>...</message_controle>";
		echo "<titre_controle>....</titre_controle>";
		echo "<erreur>true</erreur>";
		echo $date_time;
		echo "</parent>";
		die();
	}
	
	function protect_xml($texte)
	{
		$texte = rawurlencode($texte);
	
		return $texte;
	}
	
	header("Content-type: text/xml");
	echo "<?xml version='1.0' encoding='UTF8'?>";
	echo "<parent>";
	echo $date_time;
	echo "</parent>";
?>