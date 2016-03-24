<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Password screen by authorization level
 * Level require class __STANDARD_LEVEL_ACCESS__
 ====================================================================*/

	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('includes/common/ssid_simple.php');
	/**===================================================================*/

	/**==================================================================
	 * Load common constants
	 ====================================================================*/
	require('includes/common/constante.php');
	/**===================================================================*/
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('includes/common/buffering.php');
	/**===================================================================*/

	
	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('includes/common/global_functions.php');
	/**===================================================================*/
	
	
	/**==================================================================
	 * Authentication require
	 ====================================================================*/	
	$url_param = url_get_exclusion($_GET,array('mode','lng'));
	$_SESSION['iknow'][$ssid]['redirect_page'] = getenv("SCRIPT_NAME");
	$_SESSION['iknow'][$ssid]['logout_page'] = 'password.php';
	$_SESSION['iknow'][$ssid]['level_require_path'] = '../';
	$_SESSION['iknow'][$ssid]['level_require'] = __STANDARD_LEVEL_ACCESS__;

	require('includes/security/check_login.php');
	/**===================================================================*/


	/**==================================================================
	 * Lisha configuration and framework includes
	====================================================================*/
	// Lisha main hard coded definition
	require('./includes/lishaSetup/main_configuration.php');
	$path_root_lisha =  './'.__LISHA_APPLICATION_RELEASE__;

	// Lisha load main customized database configuration
	require($path_root_lisha.'/includes/LishaSetup/custom_configuration.php');

	// Lisha using language
	require($path_root_lisha.'/includes/common/language.php');

	// Lisha read localization features
	require($path_root_lisha.'/includes/LishaSetup/lisha_localization.php');

	// Lisha framework includes
	require($path_root_lisha.'/lisha_includes.php');
	/**===================================================================*/

	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('includes/common/load_conf_session.php');
	/**===================================================================*/

	
	/**==================================================================
	* Recover language from URL or Database
	====================================================================*/	
	require('includes/common/language.php');
	/**===================================================================*/

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('includes/common/html_doctype.php');
	/**===================================================================*/
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<link rel="stylesheet" href="css/common/password.css" type="text/css">
		<link rel="stylesheet" href="../css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="../css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="../js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="../js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="../ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="../js/common/session_management.js"></script>
		<script type="text/javascript">
			var ssid= '<?php echo $ssid; ?>';
			var version_soft = '';

			var libelle_common = Array();
			<?php 
				//==================================================================
				// Load text
				//==================================================================
				$type_soft = 7;
				require('includes/common/textes.php');
				//==================================================================
			?>
		
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
			
			function lib_hover(p_lib)
			{
				document.getElementById('help').innerHTML = p_lib;
			}

			function lib_out()
			{
				document.getElementById('help').innerHTML = '';
			}
		
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][494]; ?></title>
		<?php
		//==================================================================
		// Lisha HTML header generation
		//==================================================================
		lisha::generate_common_html_header($ssid);	// Once
		//==================================================================

		/**==================================================================
		 * Include all Lisha list setup
		====================================================================*/
		include ('./includes/lishaDefine/password.php');
		/**===================================================================*/

		?>
	</head>
	<body onmousemove="lisha_move_cur(event);" onmouseup="lisha_mouseup();">
		<div id="header">
			<div class="logo"></div>
			<div style="position:absolute;top:20px;right:20px;background-image:url(images/logout.png);width:32px;height:32px;cursor:pointer;" onmouseover="lib_hover('<?php echo $_SESSION[$ssid]['message']['iknow'][492]; ?>');" onmouseout="lib_out();" onclick="window.location.replace('includes/security/logout.php?ssid=<?php echo $_GET['ssid']; ?>');"></div>
		</div>	
		<div id="identification_title">
			<?php echo $_SESSION[$ssid]['message'][493].' '.$_SESSION['iknow'][$ssid]['identified_level']; ?>
		</div>
		<div style="width:100%;bottom:23px;top:100px;position:absolute;background-color:#999;" id="vimofy_password">
			<?php echo $obj_lisha_password->generate_lisha(); ?>
		</div>
		<div id="footer"></div>
		<?php $obj_lisha_password->lisha_generate_js_body();?>

		<?php
		//==================================================================
		// Lisha HTML bottom generation
		//==================================================================
		lisha::generate_common_html_bottom($obj_lisha_password->c_dir_obj,$_SESSION[$ssid]['lisha']['configuration'][12],$_SESSION[$ssid]['lisha']['langue']);	// Once
		//==================================================================
		?>
		<script type="text/javascript">
			var footer = new iknow_footer('../../js/common/iknow/');
			footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
			footer.add_element('<div style="float:right;margin-right:10px;"><?php echo $_SESSION[$ssid]['message'][491].$_SESSION['iknow'][$ssid]['login']; ?></div>',__FOOTER_LEFT__);
			footer.add_element('<div style="float:left;font-weight:bold;font-size: 11px;" id="help"></div>',__FOOTER_LEFT__);			
			footer.generate();
		</script>
	</body>
</html>