<?php
	$ssid = $_POST['ssid'];
	session_name($ssid);
	session_start();
	//require '../../includes/common/ssid.php';
	//$ssid = $p_ssid;
	require '../../includes/common/define_db_names.php';
	
	if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
	{
	
	
	
		switch($_POST['action'])
		{
				
			case 0:
				copier_page($_POST['ID']);
				break;
	
			case 1:
				ajouter_frere($_POST['ID']);
				break;
					
			case 2:
				ajouter_parent($_POST['ID']);
				break;
				
			case 3:
				verif_delete($_POST['ID']);
				break;			

			case 4:
				delete_page($_POST['ID']);
				break;	
		
			case 5:
				descendre($_POST['ID']);
				break;	

			case 6:
				monter($_POST['ID']);
				break;	
		}

	}
	
	function copier_page($id)
	{
			global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
			
		// RECUPERATION DU CONTENU ET DU TITRE DE LA PAGE
		$sql = 'SELECT `description`,`NAME`
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_CHILD` = '.$id.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$titre = mysql_result($resultat,0,'NAME').' COPIE';
		$description = mysql_result($resultat,0,'description');
			
			
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$id_parent = get_parent($id);
	
		// RECUPERATION DU DERNIER ORDER
		$sql = 'SELECT MAX(`ORDER`) as max_order
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_PARENT` = '.$id_parent.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$max_order = mysql_result($resultat,0,'max_order');
			
		// COPIE DE LA PAGE
		$sql = 'INSERT INTO `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'`(
							`version` ,
							`Type` ,
							`ID_PARENT` ,
							`ID_CHILD` ,
							`ORDER` ,
							`NAME` ,
							`description` ,
							`last_update` ,
							`who` ,
							`icone`
							)
							VALUES ( 
							"'.$_SESSION['iknow']['version_soft'].'", "S", '.$id_parent.', NULL , '.($max_order + 1).', "'.mysql_escape_string($titre).'", "'.mysql_escape_string($description).'", 
							CURRENT_TIMESTAMP , "IKN", "icon-prop"
							);';
	
		$resultat = mysql_query($sql) or die(mysql_error().'  '.$sql);
		echo mysql_insert_id();
	
	}
	
	
	function ajouter_frere($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
			
			
		
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$id_parent = get_parent($id);
	
		// RECUPERATION DU DERNIER ORDER
		$sql = 'SELECT MAX(`ORDER`) as max_order
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_PARENT` = '.$id_parent.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$max_order = mysql_result($resultat,0,'max_order');
			
		// INSERTION DU FRERE
		$sql = 'INSERT INTO `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'`(
							`version` ,
							`Type` ,
							`ID_PARENT` ,
							`ID_CHILD` ,
							`ORDER` ,
							`NAME` ,
							`description` ,
							`last_update` ,
							`who` ,
							`icone`
							)
							VALUES ( 
							"'.$_SESSION['iknow']['version_soft'].'", "S", '.$id_parent.', NULL , '.($max_order + 1).', "Nouvelle page", "", 
							CURRENT_TIMESTAMP , "IKN", "icon-prop"
							);';
	
		$resultat = mysql_query($sql) or die(mysql_error().'  '.$sql);
		echo mysql_insert_id();
			
			
	
	}
			
	function ajouter_parent($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
			
		// RECUPERATION DU CONTENU ET DU TITRE DE LA PAGE
		$sql = 'SELECT `description`,`NAME`
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_CHILD` = '.$id.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$titre = mysql_result($resultat,0,'NAME');
		$description = mysql_result($resultat,0,'description');

		
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$id_parent = get_parent($id);
	
			
		// INSERTION DU FRERE
		$sql = 'INSERT INTO `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'`(
							`version` ,
							`Type` ,
							`ID_PARENT` ,
							`ID_CHILD` ,
							`ORDER` ,
							`NAME` ,
							`description` ,
							`last_update` ,
							`who` ,
							`icone`
							)
							VALUES ( 
							"'.$_SESSION['iknow']['version_soft'].'", "S", '.$id.', NULL ,0, "'.mysql_escape_string($titre).'", "'.mysql_escape_string($description).'", 
							CURRENT_TIMESTAMP , "IKN", "icon-prop"
							);';
		
		$resultat = mysql_query($sql) or die(mysql_error().'  '.$sql);
		
		
		// On v�rifie si il y a des enfants qui ne sont pas parents pour notre parent.
		// Si il n'y en a pas alors on en cr�er un car il en faut obligatoirement un pour chaque parent (page de description)
		
		
		echo mysql_insert_id();
		
		$sql = 'SELECT `ID_CHILD` 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_PARENT = '.$id_parent.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		$resultat = mysql_query($sql);	
		$avoir_enfants = false;		// enfants qui ne sont pas parents
		while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
		{
			
			if(is_father($row['ID_CHILD']))
			{
				$avoir_enfants = false;
			}
			else
			{
				$avoir_enfants = true;
				break;
			}

		}
		
		if($avoir_enfants == false)
		{
			
			// RECUPERATION DU DERNIER ORDER
			$sql = 'SELECT MAX(`ORDER`) as max_order
						 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
							  	WHERE `ID_PARENT` = '.$id_parent.' 
							  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
			$resultat = mysql_query($sql);
			$max_order = mysql_result($resultat,0,'max_order');

		
			$sql = 'INSERT INTO `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'`(
								`version` ,
								`Type` ,
								`ID_PARENT` ,
								`ID_CHILD` ,
								`ORDER` ,
								`NAME` ,
								`description` ,
								`last_update` ,
								`who` ,
								`icone`
								)
								VALUES ( 
								"'.$_SESSION['iknow']['version_soft'].'", "S", '.$id_parent.', NULL ,'.($max_order + 1).', "defaut", "defaut", 
								CURRENT_TIMESTAMP , "IKN", "icon-prop"
								);';
			
			$resultat = mysql_query($sql) or die(mysql_error().'  '.$sql);			
			
			
		}
		
		
			
	
	}
	
	// V�rifie si l'ID_CHILD $id est p�re, retourne true si il est p�re, false dans le cas contraire
	function is_father($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
				
		$sql = 'SELECT 1 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_PARENT = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'" 
				LIMIT 1';
		
		$resultat = mysql_query($sql);
		
		if(mysql_num_rows($resultat) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
		
	
	// V�rifie si une page peut �tre supprim�e
	// Une page ne peu pas l'�tre si elle est la derniere de son parent � ne pas avoir d'enfant
	// retourne true si elle peut, false sinon
	function verif_delete($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
				
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$id_parent = get_parent($id);
		
		
		$sql = 'SELECT `ID_CHILD` 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_PARENT = '.$id_parent.' 
				AND `ID_CHILD` <> '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		$resultat = mysql_query($sql);	
		$avoir_enfants = false;		// enfants qui ne sont pas parents
		while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
		{
			
			if(is_father($row['ID_CHILD']))
			{
				$avoir_enfants = false;
			}
			else
			{
				$avoir_enfants = true;
				break;
			}

		}
		
		if($avoir_enfants)
		{
			
			echo 'true';
			
		}
		else
		{
			echo 'false';
			
		}
		
		
	}

	
	function get_parent($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
		
		
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$sql = 'SELECT `ID_PARENT`
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_CHILD` = '.$id.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$id_parent = mysql_result($resultat,0,'ID_PARENT');
		
		
		return $id_parent;
		
	}

	function get_order($id)
	{
		global $ssid;	
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
		
		
		// RECUPERATION DE l'ORDER DE NOTRE ID_CHILD
		$sql = 'SELECT `ORDER` 
					 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						  	WHERE `ID_CHILD` = '.$id.' 
						  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		$resultat = mysql_query($sql);
		$order = mysql_result($resultat,0,'ORDER');
		
		
		return $order;
		
	}
	
	function delete_page($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
		
		
		// RECUPERATION DE l'ID PARENT DE NOTRE ID_CHILD
		$sql = 'DELETE FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE `ID_CHILD` = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
		mysql_query($sql) or die(mysql_error().'  '.$sql);
		
	}
	
	function descendre($id)
	{
		global $ssid;
		
		$id_parent = get_parent($id);
		$order_orig = get_order($id);
		
		
		// On va chercher l'ID child de la page du dessous
		$sql = 'SELECT `ID_CHILD`,`ORDER`  
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE `ID_PARENT` = '.$id_parent.' 
				AND `ORDER` > '.$order_orig.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'" 
				ORDER BY `ORDER` ASC 
				LIMIT 1';
		
	
		$resultat = mysql_query($sql);

		$order_dest = mysql_result($resultat,0,'ORDER');
		$id_child_dest = mysql_result($resultat,0,'ID_CHILD');
		
	
		
		$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				SET `ORDER` = '.$order_orig.' 
				WHERE `ID_CHILD` = '.$id_child_dest.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		
		mysql_query($sql) or die(mysql_error().'  '.$sql);
		
		
		$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				SET `ORDER` = '.$order_dest.' 
				WHERE `ID_CHILD` = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		
		mysql_query($sql) or die(mysql_error().'  '.$sql);		
		
		echo $id;
		
		
	}
	
	function monter($id)
	{
		global $ssid;
		$id_parent = get_parent($id);
		$order_orig = get_order($id);
		
		
		// On va chercher l'ID child de la page du dessous
		$sql = 'SELECT `ID_CHILD`,`ORDER`  
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE `ID_PARENT` = '.$id_parent.' 
				AND `ORDER` < '.$order_orig.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"
				ORDER BY `ORDER` DESC 
				LIMIT 1';
		
	
		$resultat = mysql_query($sql);

		$order_dest = mysql_result($resultat,0,'ORDER');
		$id_child_dest = mysql_result($resultat,0,'ID_CHILD');
		
	
		
		$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				SET `ORDER` = '.$order_orig.' 
				WHERE `ID_CHILD` = '.$id_child_dest.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		
		mysql_query($sql) or die(mysql_error().'  '.$sql);
		
		
		$sql = 'UPDATE `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				SET `ORDER` = '.$order_dest.' 
				WHERE `ID_CHILD` = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
		
		mysql_query($sql) or die(mysql_error().'  '.$sql);		
		
		echo $id;
		
		
	}	
?>