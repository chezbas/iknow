<?php
require "../../includes/common/ssid.php";	
$ssid = $p_ssid; 

session_name($ssid);
session_start();

require("../../includes/common/version_active.php");
require('connexion.php');

ob_start();
phpinfo();
$config = ob_get_contents();

ob_end_clean();



$analyse = strstr($config,'PHP Version');
$phpversion = explode("+",$analyse);

if ( strlen($phpversion[0]) > 50 ) {
	$phpversion = explode("<",$analyse);
	}


$analyse = strstr($analyse,'MySQL Support');
$analyse = strstr($analyse,'API version');
$analyse = strstr($analyse,'">');
$long = strpos($analyse,'<');
$mysqlversion = substr($analyse,2,$long-3);


$mode = $_GET['mode'];

if ( $mode == 'web_service' ) {
	$temp = explode(" ",$_SERVER["SERVER_SOFTWARE"]);
	$temp2 = explode("/",$temp[0]);
	echo '<b style="color:red;">'.$temp2[0]." - ".$temp2[1].'</b>';
	}
	
if ( $mode == 'php' ) echo '<b style="color:red;">'.substr($phpversion[0],3).'</b>';

if ( $mode == 'mysql' )
	{	
	//$param = mysql_real_escape_string($_GET["ID"]);
	//$query = 'SELECT valeur, designation FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].' WHERE id = "'.$param.'" AND version_active = "'.$version_soft.'" LIMIT 1';
	
	mysql_query("SET NAMES 'utf8'");
	
	//$resultat = mysql_query($query);*/
	echo '<b style="color:red;">'.mysql_get_server_info().'</b>';
	
	//echo '<b style="color:red;">'.$mysqlversion.'</b>';
	}
?>
