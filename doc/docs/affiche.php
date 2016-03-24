<?php
	$ssid = $_GET['ssid'];
	session_name($ssid);
	session_start();
	global $ssid;
	require '../../includes/common/define_db_names.php';
	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
	mysql_query("SET NAMES 'utf8'");
	
	
	$sql = 'SELECT `DESCRIPTION`,`NAME`,`ORDER` 
		 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
			  	WHERE `ID_CHILD` = '.$_GET['ID'].' 
			  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';

	$resultat = mysql_query($sql);
	$titre = mysql_result($resultat,0,'NAME');
	$description = mysql_result($resultat,0,'DESCRIPTION');
	$order = mysql_result($resultat,0,'ORDER');
	
	// BEGIN INCLUDE 
	// On cherche si il y a des includes vers des fichiers php
	// <include "test.php" />
	$motif = '#&lt;include "([^"]+)"[ ]*/&gt;#i';
	
	preg_match_all($motif,$description,$out);

	foreach($out[1] as $key => $value)
	{
		$value = html_entity_decode($value);
		(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? $http = 'https' : $http = 'http';
		$handle = fopen($http.'://'.$_SERVER['HTTP_HOST'].'/doc/iInclude/'.$value, "r");
		$replace = stream_get_contents($handle);
		fclose($handle);
		$description = str_replace($out[0][$key],$replace,$description);
			
	}	
	// END INCLUDE
	
	
	
	/**==================================================================
	 *  BEGIN Isymbole 
	 *  On cherche si il y a des Isymboles
	 *  <isym:xxxx/>
	 ====================================================================*/
	$motif = '#&lt;isym:([^/]+)[ ]*/&gt;#i';
	
	preg_match_all($motif,$description,$out);

	foreach($out[1] as $key => $value)
	{
		
		//$replace = strtolower($value);
		$replace = '<span style="color:red;font-weight:bold;">'.strtolower(substr($value,0,1)).'</span>'.ucfirst(substr($value,1));
		$description = str_replace($out[0][$key],$replace,$description);
			
	}	
	/*===================================================================*/
	
	/**==================================================================
	 * Begin replace icon
	 * <icon:xxxx/>
	 ====================================================================*/
	//$motif = '#&lt;icon:([^/]+)[ ]*/&gt;#i';
	
/*	preg_match_all($motif,$description,$out);

	foreach($out[1] as $key => $value)
	{
		$replace = '<img class="'.$value.'" style="display:inline;border:none;"/>';
		//$replace = 'tototototo';
		$description = str_replace($out[0][$key],$replace,$description);
	}	
	/*===================================================================*/
	$description = str_ireplace('<strong>','<b>',$description);
	$description = str_ireplace('</strong>','</b>',$description);
	$description = str_ireplace('<em>','<i>',$description);
	$description = str_ireplace('</em>','</i>',$description);
	$description = str_ireplace('src="../screenshot/','src="doc/screenshot/',$description);
	$description = str_ireplace('src="../../images/','src="images/',$description);
	
	

	
	
	
	// On vérifie si la page est la premiere ou la derniere de son parent (pour placer les fleches)

	$sql = 'SELECT MIN(`ORDER`) as min_order,MAX(`ORDER`) as max_order  
		 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
			  	WHERE `ID_PARENT` = '.get_parent($_GET['ID']).' 
			  	AND version = "'.$_SESSION['iknow']['version_soft'].'"';
	
	$resultat = mysql_query($sql);
	$min_order = mysql_result($resultat,0,'min_order');
	$max_order = mysql_result($resultat,0,'max_order');	
	
	
	echo '<div id="titre">';

	echo '<div id="arbo">'.get_arbo($_GET['ID']).'</div>';
		if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
	{
		echo '<div id="barre_outils">
					<img src="doc/docs/images/page_edit.png" title="Modifier" onclick="window.location.replace(\'doc/docs/edit.php?ID='.$_GET['ID'].'&ssid='.$_GET['ssid'].'\');" style="cursor:pointer;">
					<img src="doc/docs/images/page_copy.png" title="Copier" onclick="copier_page('.$_GET['ID'].');" style="cursor:pointer;">
					<img src="doc/docs/images/add-note.png" title="Ajouter fr&egrave;re" onclick="ajouter_frere('.$_GET['ID'].');" style="cursor:pointer;">	
					<img src="doc/docs/images/add-folder.png" title="Ajouter parent" onclick="ajouter_parent('.$_GET['ID'].');" style="cursor:pointer;">						
					<img src="doc/docs/images/page_delete.png" title="Supprimer" onclick="delete_page('.$_GET['ID'].');" style="cursor:pointer;">';			
		if($order != $max_order)
		{
			echo '<img src="doc/docs/images/arrow_down.png" title="Descendre" onclick="descendre('.$_GET['ID'].');" style="cursor:pointer;">';	
		}
		if($order != $min_order)
		{		
			echo '<img src="doc/docs/images/arrow_up.png" title="Monter" onclick="monter('.$_GET['ID'].');" style="cursor:pointer;">';			
		}			
		echo '</div>';
	}
	echo '<div class="portail_titre_logo"></div>';
	echo '</div>'; // End titre
	echo '<div id="description">'.($description).'</div>';
	
	
	function get_parent($id)
	{
		global $ssid;	
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
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

	// Vérifie si l'ID_CHILD $id est père, retourne true si il est père, false dans le cas contraire
	function is_father($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
				
		$sql = 'SELECT 1 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_PARENT = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'" 
				LIMIT 1';
		
		$resultat = mysql_query($sql);
		//echo $sql.'<hr />';
		if(mysql_num_rows($resultat) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}	
	
	function get_icone($id)
	{
global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
				
		$sql = 'SELECT `icone` 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_CHILD = '.$id.' 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';

		$resultat = mysql_query($sql);
		$classe_icone = mysql_result($resultat,0,'icone');
		
		$chemin = 'doc/docs/resources/';
		switch($classe_icone)
		{
			
			case 'icon-cls':
				return $chemin.'cls.gif';
				break;
			case 'icon-event':
				return $chemin.'event.gif';
				break;				
			case 'icon-config':
				return $chemin.'config.gif';
				break;
			case 'icon-prop':
				return $chemin.'prop.gif';
				break;
			case 'icon-method':
				return $chemin.'method.gif';
				break;	
			case 'icon-cmp':
				return $chemin.'cmp.gif';
				break;
			case 'icon-pkg':
				return $chemin.'pkg.gif';
				break;
			case 'icon-fav':
				return $chemin.'fav.gif';
				break;
			case 'icon-static':
				return $chemin.'static.gif';
				break;
			case 'icon-docs':
				return $chemin.'docs.gif';
				break;
							
		}
		
		
		
	}
	
	// Récupere le premier enfant du parent $id
	function get_first_child($id)
	{
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
				
		$sql = 'SELECT ID_CHILD 
				FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				WHERE ID_PARENT = '.$id.' 
				AND `ORDER` = 0 
				AND version = "'.$_SESSION['iknow']['version_soft'].'" 
				LIMIT 1';
		//echo $sql.'<hr />';
		$resultat = mysql_query($sql);
		if(mysql_num_rows($resultat)  > 0)
		{
			return mysql_result($resultat,0,'ID_CHILD');
		}
		else
		{
			return $id;
		}
		
	
	}
	
	function get_arbo($child_original)
	{
global $ssid;
		$link_mysql = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
			
			
		$arbo = array();
		$titre = array();
		$id_child = array();
		$i = 0;
		$end = false;
		$child = $child_original;
		$link = '<table class="tableau_lien">';
		
		while(!$end)
		{
			
			$sql = 'SELECT ID_PARENT,NAME
					FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'
					WHERE ID_CHILD = '.$child.' 
					AND version = "'.$_SESSION['iknow']['version_soft'].'"';	
			
			$resultat = mysql_query($sql);
			
			if(mysql_result($resultat,0,'ID_PARENT') != false)
			{
				$id_child[$i] = $child;
				$arbo[$i] = mysql_result($resultat,0);
				$child = $arbo[$i];	
				$titre[$i] = mysql_result($resultat,0,'NAME');
				
			}
			else
			{
				
				$end = true;
				
			}
	
			$i++;
			
		}
		

		$arbo = array_reverse($arbo);
		$titre = array_reverse($titre);
		$id_child = array_reverse($id_child);

		/*print_r($id_child);
		echo '<hr />';
		print_r($titre);
		echo '<hr />';	
		*/
		$link .= '<tr><td><b>Chemin : </b></td><td><img src="'.get_icone($id_child[0]).'" class="img_lien"/><td><td><a class="texte_lien" href="index.php?ID='.get_first_child($id_child[0]).'">'.$titre[0].'</a></td>';
		foreach($arbo as $key => $value)
		{
			//echo 'is father'.$id_child[$key].'<br />';
			//echo $value.'  '.$titre[$key].'<br />';
			if($value > 1)
			{
				
				
				if(is_father($id_child[$key]))
				{
					$link .= '<td><img src="'.get_icone($id_child[$key]).'" class="img_lien"/><td><td><a class="texte_lien" href="index.php?ID='.get_first_child($id_child[$key]).'">'.$titre[$key].'</a></td>';
				}
				else
				{
					$link .= '<td><img src="'.get_icone($id_child[$key]).'" class="img_lien"/><td><td><a class="texte_lien_last" href="index.php?ID='.$id_child[$key].'">'.$titre[$key].' ('.$id_child[$key].')</a></td>';
				}	
			}
		}
		
		$link .= '</tr></table>';
		
		return $link;
	}
	
	

	
?>