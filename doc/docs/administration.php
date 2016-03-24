<?php

	/* Pour le css modifier le fichier accueil.css présent dans version/doc/docs/ */

	echo '<div id="libelle_phpmyadmin" class="libelle"><a href="../mysql/" target="_blank">phpMyAdmin</div>';
	echo '<div id="libelle_bugs" class="libelle"><a href="bugs/" target="_blank">Liste des bugs</a></div>';
	echo '<div id="libelle_lock" class="libelle"><a href="outils/lock/?ssid='.$ssid.'" target="_blank">Gestion des locks</a></div>';
	echo '<div id="libelle_message" class="libelle"><a href="outils/message/?ssid='.$ssid.'" target="_blank">Messages système</a></div>';
	
?>