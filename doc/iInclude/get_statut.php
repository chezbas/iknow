<?php
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");
	require('connexion.php');

	$niveau = mysql_real_escape_string($_GET["niveau"]);
	$langue = mysql_real_escape_string($_GET["langue"]);
	$objet = mysql_real_escape_string($_GET["objet"]);

	$mode = mysql_real_escape_string($_GET["mode"]);

	$sql_encodage = "SET NAMES 'utf8'";
	mysql_query($sql_encodage);

	// Mode 0
	if ( $mode == '0' ) {
		$query = 'SELECT * FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].' 
			WHERE 1 = 1 
			AND id_texte = "'.$niveau.'"
			AND `id_lang` = "'.$langue.'"
			AND `type` = "statut"
			AND objet = "'.$objet.'" 
			AND version_active = "'.$version_soft.'" LIMIT 1';
			
	
		$resultat = mysql_query($query);
		//echo $query;
		$niveau = mysql_result($resultat,0,"id_texte");
		$libelle = mysql_result($resultat,0,"texte");
		
		echo '<b style="color:red;">'.$libelle.'</b>';
		}
	
	// Mode 1 = count
	if ( $mode == '1' ) {
		$query = 'SELECT count(1) total FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].'
			WHERE 1 = 1 
			-- AND id_texte = "'.$niveau.'"
			AND `id_lang` = "'.$langue.'"
			AND `type` = "statut"
			AND objet = "'.$objet.'" 
			AND version_active = "'.$version_soft.'"';
			
	
		$resultat = mysql_query($query);
		//echo $query;
		$total = mysql_result($resultat,0,"total");
		
		echo '<b style="color:red;">'.$total.'</b>';
		}
	
?>