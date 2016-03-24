<?php
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * screen to manage lock entries
 * Level require class __SYSOP_LEVEL_ACCESS__
 ====================================================================*/

	/**==================================================================
	 * MagicTree configuration
	====================================================================*/
	define("__MAGICTREE_APPLICATION_RELEASE__","MT0.10");						// MagicTree package name in use
	/**===================================================================*/

	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../../includes/common/ssid_simple.php');
	/*===================================================================*/	

	/**==================================================================
	 * Load common constants
	 ====================================================================*/
	require('../../includes/common/constante.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	* Load global functions
	====================================================================*/	
	require('../../includes/common/global_functions.php');
	/*===================================================================*/

	
	/**==================================================================
	 * Authentification require
	 ====================================================================*/	
	$url_param = url_get_exclusion($_GET,array('mode','lng'));
	$_SESSION['iknow'][$ssid]['redirect_page'] = getenv("SCRIPT_NAME");
	$_SESSION['iknow'][$ssid]['level_require_path'] = '../../';
	$_SESSION['iknow'][$ssid]['logout_page'] = 'index.php';
	$_SESSION['iknow'][$ssid]['level_require'] = __SYSOP_LEVEL_ACCESS__;

	require('../../includes/security/check_login.php');
	/**===================================================================*/

	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
		require('../../includes/common/load_conf_session.php');
	/*===================================================================*/

	
	/**==================================================================
	* Recover language from URL or Database
	====================================================================*/	
	require('../../includes/common/language.php');
	/*===================================================================*/
	
		
	/**==================================================================
	 * Setup page timeout
	 * Set a protection if value is too short
	 ====================================================================*/	
	require('../../includes/common/page_timeout.php');
	/*===================================================================*/	


	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../../includes/common/html_doctype.php');
	/*===================================================================*/

?> 
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<link rel="stylesheet" type="text/css" href="../../css/common/outils.css"/>
		<link rel="stylesheet" href="../../css/common/icones_iknow.css" type="text/css">
		
		<script type="text/javascript" src="fonction.js"></script>
		<script type="text/javascript" src="../../ajax/common/ajax_generique.js"></script>
		
		<?php 
			$dir_ident = '../../doc/docs/';
			$dir_obj = '../../vimofy/';
			
			/**==================================================================
			* Recover text
			====================================================================*/	
			echo '<script type="text/javascript">';
			$type_soft = 9;
			require('../../includes/common/textes.php');
			echo '</script>';
			/*===================================================================*/
			
			/**==================================================================
			 * Lisha php initialization
			 ====================================================================*/	
			require $dir_obj.'vimofy_includes.php';
			require 'vim_lock.php';
			
			$obj_lock->generate_public_header();
			$obj_lock->vimofy_generate_header();
			/*===================================================================*/	
		?>
		<script type="text/javascript">
			var ssid= '<?php echo $ssid; ?>';
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][1]; ?></title>
	</head>
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();">
		<div id="header">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('<?php echo $_SESSION[$ssid]['message']['iknow'][352]; ?>');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
			<div style="position: absolute;width: 100%; text-align: center;top:70px;font-size: 0.8em"><?php echo $_SESSION[$ssid]['message'][2]; ?></div>
			<div style="position: absolute;top:60px;right:15px;">
				<input type="button" value="<?php echo $_SESSION[$ssid]['message'][3]; ?>" onclick="window.location.replace('.?<?php echo $url_param; ?>&mode=die')" onmouseover="over('<?php echo str_replace("'","\'",$_SESSION[$ssid]['message'][4]); ?>');" onmouseout="unset_text_help();"/>
				<input type="button" value="<?php echo $_SESSION[$ssid]['message'][5]; ?>" onclick="window.location.replace('.?<?php echo $url_param; ?>&mode=all')" onmouseover="over('<?php echo $_SESSION[$ssid]['message'][6]; ?>');" onmouseout="unset_text_help();"/>
			</div>
		</div>	
		<div style="width:100%;bottom:23px;top:102px;position:absolute;background-color:#999;" id="vimofy_3">
			<?php
				echo $obj_lock->generate_vimofy();
			?>
		</div>
		<div id="footer">
			<div id="help" style="position: absolute;left: 0;font-weight: bold;font-size: 11px;"></div>
			<div style="position: absolute;right: 5px;"><?php echo str_replace("&timeout",hms($_SESSION[$ssid]['configuration'][41]),$_SESSION[$ssid]['message'][7]); ?></div>
		</div>
		<?php 
			$obj_lock->vimofy_generate_js_body();
		?>
	</body>
</html>