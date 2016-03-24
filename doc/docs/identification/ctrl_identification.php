<?php

	$link = mysql_connect('localhost', 'devil', 'maycry4');
	mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
	mysql_select_db('acces',$link) or die('dbconn: mysql_select_db: ' + mysql_error());

	$sql = 'SELECT 1 
		 		FROM `ikn_groups` 
			  	WHERE `name` = "'.mysql_escape_string($_POST['login']).'" 
			  	AND password = MD5("'.mysql_escape_string($_POST['password']).'")';
	$resultat = mysql_query($sql,$link) or die(mysql_error());
	
	if(mysql_num_rows($resultat)>0)
	{
		$_SESSION['identified'] = true;
		$_SESSION['login'] = $_POST['login'];
		$_SESSION['identifier'] = true;
		header('Location: ../../?ssid='.$_GET['ssid']);
	}
	else
	{
		$bad_ident = true;
		require('connexion.php');
	}
?>