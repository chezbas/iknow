<?php 

	$ssid = $_GET['ssid'];
	session_name($ssid);
	session_start();
	require '../../includes/common/define_db_names.php';
	$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
	mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
	mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());

	$sql = 'SELECT `DESCRIPTION`  
		 		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
			  	WHERE `ID_PARENT` = 0 
				AND version = "'.$_SESSION['iknow']['version_soft'].'"';

	$resultat = mysql_query($sql);
	$description = mysql_result($resultat,0,'DESCRIPTION');
	
	// On cherche si il y a des includes vers des fichiers php
	// <include "test.php" />
	$motif = '#&lt;include "([^"]+)"[ ]*/&gt;#i';
	
	preg_match_all($motif,$description,$out);

	foreach($out[1] as $key => $value)
	{
		(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? $http = 'https' : $http = 'http'; 
		$handle = fopen($http.'://localhost/homologation/doc/iInclude/'.$value, "r");
		$replace = stream_get_contents($handle);
		fclose($handle);
		$description = str_replace($out[$key],$replace,$description);
	}	
	// END INCLUDE	
	
	$description = str_ireplace('<strong>','<b>',$description);
	$description = str_ireplace('</strong>','</b>',$description);
	$description = str_ireplace('<em>','<i>',$description);
	$description = str_ireplace('</em>','</i>',$description);
	$description = str_ireplace('src="../screenshot/','src="doc/screenshot/',$description);
	$description = str_ireplace('src="../../images/','src="images/',$description);
	
?>
<div id="accueil_conteneur">
	<div id="header_iknow">
		<div class="logo_iknow">
		<div id="header_iknow_title" class="txt_shadow">
			Atteindre la dynamique propre des équipes...
		</div></div>
	</div>
	<div id="accueil_contenu">
		<div id="accueil_outils">
		<?php 
			if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
			{
				// MENU APRES IDENTIFICATION
				/* Pour le css modifier le fichier accueil.css présent dans version/doc/docs/ */
				echo '<div id="libelle_logout" class="libelle" onclick="deconnexion();">Deconnexion</div>';
				echo '<div id="libelle_edit" class="libelle" onclick="window.location.replace(\'doc/docs/edit.php?&ID=1&ssid='.$ssid.'\');">Modifier la page</div>';
				require 'administration.php';
			}
			else
			{
				// MENU NON IDENTIFIE.
				/* Pour le css modifier le fichier accueil.css présent dans version/doc/docs/ */
				echo '<div class="libelle_administration libelle" onclick="window.location.replace(\'doc/docs/identification.php?ssid='.$ssid.'\');" >Administration</div>';
				
				
			}
			echo '<div id="libelle_excel" class="libelle"><a style="text-decoration:none; color:black;" href="outils/SCREENSHOTS.xls">Utilitaire copie d\'&eacute;cran</a></div>';
		?>	
			
		</div>
		<div id="contenu">
		
		<?php echo $description; ?>
		
		</div>
	</div>
</div>