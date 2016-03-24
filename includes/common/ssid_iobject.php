<?php
$param = array();
foreach ($_GET as $cle => $valeur ) {
	if ($valeur != "" && $cle != 'ikbackup')
	{
		$param += array($cle => $valeur);
	}
}
//----------------------------------------------------
// On récupère tous les paramètres pour les ajouter dans l'URL de visualisation des requetes
//----------------------------------------------------
$par="";
while($cur = each($param))
{
	if($cur["key"] <> 'ssid' && $cur["key"] != 'ikbackup')
	{
		$par = $par."&".$cur["key"]."=".$cur["value"];
	}
}

$add_url = '';
(count($param) == 0) ? $add_url = '?' : $add_url .= '&';

	require 'includes/common/ssid.php';
	$new_ssid = __PREFIX_URL_COOKIES__.$p_ssid;
	$url = '';
	if(isset($_SERVER['HTTP_REFERER']))
	{
		$motif = '#('.$_SERVER['HTTP_HOST'].'/ifiche)\.php\?(&)?([^"]+)#i';
		preg_match_all($motif,$_SERVER['HTTP_REFERER'],$out);
	
		if(isset($out[0][0]) && $type_soft != 1)
		{
			$ssid = '';
			require 'includes/common/define_db_names.php';
			require 'class/common/class_bdd.php';
			require 'class/common/class_url.php';
			$obj_url = new class_url($_SERVER['HTTP_REFERER'],$type_soft,$ssid);
			$url = $obj_url->get_url();
			//error_log($url);die();
		}
	}

	if($url != '')
	{
		echo '<script type="text/javascript">';
		echo 'chaine = "'.$url.'ssid=" + "'.$new_ssid.'";
				//alert(chaine);
	      		//window.location.replace(chaine);'; // AVANT OK
		echo '</script>';
	}
	else
	{
		echo '<script type="text/javascript" >';
		echo "function strpos(haystack, needle, offset) {
				    var i = (haystack+'').indexOf(needle, (offset || 0));
				    return i === -1 ? false : i;
				}";
		
		echo 'if(strpos(document.location.href,"#") == false)
			  {
				  chaine = document.location.href.replace("&ikbackup=true","") + "'.$add_url.'ssid=" + "'.$new_ssid.'";
			  }
			  else
			  {
			  	  chaine = document.location.href.replace("#", "&ssid='.$new_ssid.'#"); 
			  }
	          // window.location.replace(chaine);'; // AVANT OK
		echo '</script>';
		//die();
	}
?>