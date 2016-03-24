<?php

	$param = array();
	foreach ($_GET as $cle => $valeur )
	{
		if($valeur != "") $param += array($cle => $valeur);
	}
	//-------------------------------------------------
	// On r�cup�re tous les param�tres pour les ajouter dans l'URL de visualisation des requetes	
	//-------------------------------------------------
	$par="";
	while($cur = each($param))
	{
		if ($cur["key"] <> 'V' AND $cur["key"] <> 'ssid' AND $cur["key"] <> 'f1' AND $cur["key"] <> 'f2')
		{
			$par = $par . "&" . $cur["key"] . "=" . $cur["value"];
		}
	}

	$add_url = '';
	(count($param) == 0) ? $add_url = '?' : $add_url .= '&';
	if(!isset($_GET["ssid"]))
	{
		$p_ssid = sha1(mt_rand().microtime()).mt_rand();
		echo '<script language="javascript" >';
		echo 'chaine = document.location.href + "'.$add_url.'ssid=" + "'.$p_ssid.'";';
	    echo 'window.location.href = chaine;';
		echo '</script>';
		die();
	}

	$ssid = $_GET["ssid"];
?>