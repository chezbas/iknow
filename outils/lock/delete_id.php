<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Security level
	 ====================================================================*/	
	$_SESSION['iknow'][$ssid]['redirect_page'] = getenv("SCRIPT_NAME");
	$_SESSION['iknow'][$ssid]['level_require_path'] = '../../';
	$_SESSION['iknow'][$ssid]['logout_page'] = 'index.php';
	$_SESSION['iknow'][$ssid]['level_require'] = 4;

	require('../../includes/security/check_login.php');
	/*===================================================================*/	
	
	if(isset($_POST['id']) && isset($_POST['objet']))
	{
		// Connexion au serveur MySQL
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
	
		if($_POST['objet'] == 1 || $_POST['objet'] == 2)
		{
			// iSheet object both display and update mode
			 
			//==================================================================
			// Clean iSheet parameters
			//==================================================================
			$sql = "DELETE 
					FROM 
						`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name']."`
					WHERE 1 = 1
						AND `id_fiche` = ".$_POST['id'].";
				   ";
			
			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
			
			
			//==================================================================
			// Clean iSheet Tags
			//==================================================================
			$sql = "DELETE
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."`
					WHERE 1 = 1
						AND ID = ".$_POST['id']."
						AND objet = 'ifiche';
				   ";

			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
			
			
			//==================================================================
			// Clean iSheet logs actions
			//==================================================================
			$sql = "DELETE 
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_log_action']['name']."`
					WHERE 1 = 1
						AND `ID` = ".$_POST['id']."
						AND objet = 'ifiche';
				   ";

			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
			
			//==================================================================
			// Clean iSheet url_temp
			//==================================================================
			$sql = "DELETE
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name']."`
					WHERE 1 = 1 
						AND `ID_temp` = ".$_POST['id'].";
				  ";

			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
		}
		if($_POST['objet'] == 3 || $_POST['objet'] == 4)
		{
			// iCode object both display and update mode
			 
			//==================================================================
			// Clean iCode head
			//==================================================================
			$sql = "DELETE 
					FROM 
						`".$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name']."`
					WHERE 1 = 1
						AND `ID` = ".$_POST['id'].";
				   ";
			
			mysql_query($sql) or die ("Error in query (1): $sql. ".mysql_error());
			//==================================================================
			
			
			//==================================================================
			// Clean iCode parameters
			//==================================================================
			$sql = "DELETE
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."`
					WHERE 1 = 1
						AND `ID` = ".$_POST['id'].";
				   ";

			mysql_query($sql) or die ("Error in query (2): $sql. ".mysql_error());
			//==================================================================
			
			
			//==================================================================
			// Clean iCode tags
			//==================================================================
			$sql = "DELETE
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name']."`
					WHERE 1 = 1
						AND `ID` = ".$_POST['id']."
						AND `objet` = 'icode';
				   ";
			
			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
			
			
			//==================================================================
			// Clean iCode url_temp
			//==================================================================
			$sql = "DELETE
					FROM 
						`".$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name']."`
					WHERE 1 = 1
						AND `ID_temp` = ".$_POST['id'].";
				   ";

			mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
			//==================================================================
		}	
		

		//==================================================================
		// Clean lock entry
		//==================================================================
		$sql = "DELETE
				FROM 
					`".$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name']."`
				WHERE 1 = 1
					AND `id_temp` = ".$_POST['id']."
					AND `type` = '".$_POST['objet']."';
			   ";
		
		mysql_query($sql) or die ("Error in query (3): $sql. ".mysql_error());
		//==================================================================
		
		echo str_replace("&id",$_POST['id'],$_SESSION[$ssid]['message'][23]);
	}
?>