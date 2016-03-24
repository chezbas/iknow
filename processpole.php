<?php
/**==================================================================
 * View Work and subWork modules list
====================================================================*/

	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	====================================================================*/
	require('includes/common/ssid_simple.php');
	/**===================================================================*/


	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	====================================================================*/
	require('includes/common/buffering.php');
	/**===================================================================*/


	/**==================================================================
	 * Load common functions
	====================================================================*/
	require('includes/common/global_functions.php');
	/**===================================================================*/


	/**==================================================================
	 * Load main iknow configuration
	====================================================================*/
	require('includes/common/load_conf_session.php');
	/**===================================================================*/


	/**==================================================================
	 * Load Lisha framework
	====================================================================*/
	// Lisha main hard coded definition
	require('includes/lishaSetup/main_configuration.php');
	$path_root_lisha = __LISHA_APPLICATION_RELEASE__;

	// Lisha load main customized database configuration
	require($path_root_lisha.'/includes/LishaSetup/custom_configuration.php');

	// Lisha using language
	require($path_root_lisha.'/includes/common/language.php');

	// Lisha read localization features
	require($path_root_lisha.'/includes/LishaSetup/lisha_localization.php');

	// Lisha framework includes
	require($path_root_lisha.'/lisha_includes.php');
	/**===================================================================*/


	$_SESSION[$ssid]['langue'] = $_SESSION[$ssid]['lisha']['langue']; // Recover main page language from lisha

	/**==================================================================
	 * Database connexion
	====================================================================*/
	require('./includes/common/db_connect.php');
	/**===================================================================*/


	/**==================================================================
	 * Setup page timeout
	====================================================================*/
	require('includes/common/page_timeout.php');
	/**===================================================================*/


	/**==================================================================
	 * HTML declare page interpretation directive
	====================================================================*/
	require('includes/common/html_doctype.php');
	/**===================================================================*/
	$param = url_get_exclusion($_GET,array('mode','vue','pole','lng','ssid'));

	$motif = '`&[^=]+=([A-Z]+)`i';
	preg_match_all($motif,$param,$out);

	$str_sql_filter = "";
	$str_cond_pole = "1 = 1";

	if(count($out[1]) > 0)
	{
		foreach( $out[1] as $key => $value )
		{
			if($key == 0 )
			{
				$str_sql_filter = " MODU.`ID` IN ( ";
			}
			$str_sql_filter .= "'".$value."',";
		}
	}

	if($str_sql_filter <> "" )
	{
		$str_cond_pole = substr($str_sql_filter,0,strlen($str_sql_filter)-1);
		$str_cond_pole .= " ) ";
	}
	?>

<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
	<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
	<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
	<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
	<script type="text/javascript" src="js/common/session_management.js"></script>
	<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>

	<?php
	/**==================================================================
	 * Recover text
	====================================================================*/
	echo '<script type="text/javascript">';
	$type_soft = 10;
	require('includes/common/textes.php');
	echo '</script>';
	/**===================================================================*/

	$stop = false;
	$pole = mysql_real_escape_string($_GET["pole"]);

	$query = "	SELECT
					`Libelle` AS 'Libelle'
				  FROM
					  `".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."`
				  WHERE 1 = 1
					  AND `ID` = '".$pole."'
				  LIMIT 1;
			 ";

	$result = mysql_query($query) or sqlerr();
	$resultat = mysql_fetch_array($result);

	if($resultat["Libelle"] == "" )
	{
		// Unknown area
		?>
		<title><?php echo $_SESSION[$ssid]['message']['iknow'][17];?></title>
		</head>
		<body style="background-color:#A61415;"><div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<script type="text/javascript">
			generer_msgbox(decodeURIComponent(libelle_common[17]),'<?php echo str_replace("&area",$pole,str_replace("'","\'",$_SESSION[$ssid]['message'][5])); ?>','erreur','msg',false,true);
		</script>
		</body>
	</html>
	<?php
	die();
	}
	else
	{
		$pole_lib = $resultat["Libelle"];
	}
	?>
	<link rel="stylesheet" href="css/common/password.css" type="text/css">
	<script type="text/javascript">
		function lib_hover(p_lib)
		{
			document.getElementById('help').innerHTML = p_lib;
		}

		function lib_out()
		{
			document.getElementById('help').innerHTML = '';
		}
	</script>
	<?php
	//==================================================================
	// Lisha HTML header generation
	//==================================================================
	lisha::generate_common_html_header($ssid);	// Once
	//==================================================================

	/**==================================================================
	 * Lisha setup
	====================================================================*/
	require('./includes/lishaDefine/process.php');
	/**===================================================================*/
	?>

	<script type="text/javascript">
		// Variables pour l'uptime de la session
		<?php
			$gc_lifetime = ini_get('session.gc_maxlifetime');
			$end_visu_date  = time() + $gc_lifetime;
			$end_visu_time = $end_visu_date;
			$end_visu_date = date('m/d/Y',$end_visu_date);
			$end_visu_time = date('H:i:s',$end_visu_time);

		?>
		var end_visu_date = '<?php echo $end_visu_date; ?>';
		var end_visu_time = '<?php echo $end_visu_time; ?>';
	</script>

	<?php
	$stop = false;
	$pole = mysql_real_escape_string($_GET["pole"]);

	$query = "	SELECT
							`Libelle` AS 'Libelle'
					  	FROM
					  		`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` 
					  	WHERE 1 = 1
					  		AND `ID` = '".$pole."' 
					  	LIMIT 1;
					 ";

	$result = mysql_query($query) or sqlerr();
	$resultat = mysql_fetch_array($result);

	if($resultat["Libelle"] == "" )
	{
	// Unknown area
	?>
	<title><?php echo $_SESSION[$ssid]['message']['iknow'][17];?></title>
	</head>
	<body style="background-color:#A61415;"><div id="iknow_msgbox_background"></div>
	<div id="iknow_msgbox_conteneur" style="display:none;"></div>
	<script type="text/javascript">
		generer_msgbox(decodeURIComponent(libelle_common[17]),'<?php echo str_replace("&area",$pole,str_replace("'","\'",$_SESSION[$ssid]['message'][5])); ?>','erreur','msg',false,true);
	</script>
	</body>
	</html>
	<?php
	die();
	}
	else
	{
		$pole_lib = $resultat["Libelle"];
	}
	?>
	<link rel="stylesheet" href="css/common/password.css" type="text/css">
	<script type="text/javascript">
		function lib_hover(p_lib)
		{
			document.getElementById('help').innerHTML = p_lib;
		}

		function lib_out()
		{
			document.getElementById('help').innerHTML = '';
		}
	</script>

	<title><?php echo $_SESSION[$ssid]['message'][1].' '.$pole_lib; ?></title>

	<script type="text/javascript">
		<?php
			//==================================================================
			// Session uptime javascript php variable
			//==================================================================
			$gc_lifetime = ini_get('session.gc_maxlifetime');
			$end_visu_date  = time() + $gc_lifetime;
			$end_visu_time = $end_visu_date;
			$end_visu_date = date('m/d/Y',$end_visu_date);
			$end_visu_time = date('H:i:s',$end_visu_time);
			echo "var end_visu_date = '".$end_visu_date."'".chr(13);
			echo "var end_visu_time = '".$end_visu_time."'".chr(13);
		//==================================================================
		?>
	</script>
</head>
	<body onmousemove="lisha_move_cur(event);" onmouseup="lisha_mouseup();">
	<!-- =================================================  MSGBOX ================================================= -->
	<div id="iknow_msgbox_background"></div>
	<div id="iknow_msgbox_conteneur" style="display:none;"></div>
	<!-- ===============================================  END MSGBOX ==============================================  -->
	<div id="header">
		<div class="logo"></div>
	</div>
	<div id="identification_title">
		<?php echo $_SESSION[$ssid]['message'][1].' '.$pole_lib; ?>
	</div>
	<div style="width:100%;bottom:23px;top:100px;position:absolute;background-color:#999;" id="lisha_processus">
		<?php echo $_SESSION[$ssid]['lisha']['lisha_process_list_id']->generate_lisha(); ?>
	</div>

	<?php
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->lisha_generate_js_body();
	?>

	<div id="footer"></div>
	<script type="text/javascript">
		var footer = new iknow_footer('js/common/iknow/');
		footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
		footer.add_element('<div id="txt_help"></div>',__FOOTER_LEFT__);
		footer.generate();
	</script>
	</body>
</html>