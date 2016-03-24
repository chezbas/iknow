<?php
	require "../../includes/common/ssid.php";	
	$ssid = $p_ssid; 
	
	session_name($ssid);
	session_start();

	require("../../includes/common/version_active.php");
	require('connexion.php');
	$param = mysql_real_escape_string($_GET["ID"]);
	// ICode
	$query = 'SELECT `value`, `designation` FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].' WHERE `id` = "'.$param.'" AND `version_active` = "'.$version_soft.'" LIMIT 1';
	
	$resultat = mysql_query($query);
	//echo $query;
	$valeur = mysql_result($resultat,0,"valeur");
	$commentaire = mysql_result($resultat,0,"designation");
	$tableau_valeur = explode("SEP",$valeur);
	$tableau_commentaire = explode("|",$commentaire);
	//while ($row = mysql_fetch_array($resultat)) {
		//echo $row["valeur"];
		//}
	echo '<table style="cursor: default; font-size: 12px; border-collapse: collapse; border: 2px solid #bbbbbb;" border="2" frame="void">';
	echo '<tr>';
	echo '<th style="cursor: text;"><strong>Nom</strong></th>';
	echo '<th style="cursor: text;"><strong>Fonction</strong></th>';
	echo '</tr>';
	foreach($tableau_valeur as $key => $value ) {
		echo '<tr>';
			echo '<td style="color:red;text-align:center;"><strong>'.htmlentities($value)."</strong></td>";
			echo '<td>'.htmlentities($tableau_commentaire[$key])."</td>";
		echo '</tr>';
		}
	echo '</table>';

?>