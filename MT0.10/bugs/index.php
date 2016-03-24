<?php 
	/**==================================================================
	 * MagicTree configuration
	 ====================================================================*/
	$path_root_framework = './';
	require('../../includes/MTSetup/setup.php');
	/*===================================================================*/		
	
	
	/**==================================================================
	 * Automatic detection of current MagicTree version
	 ====================================================================*/
	require('../includes/common/version_active.php');
	/*===================================================================*/		

	$application_release = $version_soft;
	
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../includes/common/ssid_session_start.php');
	/*===================================================================*/		
	$_SESSION[$ssid]['MT']['application_release'] = $application_release;
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../includes/common/buffering.php');
	/*===================================================================*/	
		
	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../includes/common/global_functions.php');
	/*===================================================================*/	
		

	/**==================================================================
	 * Lisha configuration ( Need active php session and ssid definition )
	 ====================================================================*/
	require('../includes/lishaSetup/configuration.php');
	/*===================================================================*/		
	
	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('../includes/common/load_conf_session.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Recover language from URL or Database
	 ====================================================================*/	
	require('../includes/common/language.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Setup page max timeout
	 ====================================================================*/	
	require('../includes/common/page_timeout.php');
	/*===================================================================*/	

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../includes/common/html_doctype.php');
	/*===================================================================*/	

	// Page language equal magic tree language
	$_SESSION[$ssid]['langue'] = $_SESSION[$ssid]['MT']['langue']
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script type="text/javascript">
			var ssid = '<?php echo $ssid; ?>';
		<?php
			/**==================================================================
			 * Load text in both php session and javascript
			 * Warning : Must be in <head> html bloc 
			 ====================================================================*/	
			$text_group = 'bug';
			require('../includes/common/textes_extra_screen.php');
			echo chr(10);
			/*===================================================================*/


			/**==================================================================
			 * Lisha framework include
			 ====================================================================*/	
			$path_root_lisha = '../'.__LISHA_APPLICATION_RELEASE__; // Mandatory for Lisha
			include ('../'.__LISHA_APPLICATION_RELEASE__.'/lisha_includes.php');
			/*===================================================================*/	
			?>
		</script>
		<?php 
			/**==================================================================
			 * Lisha init
			 ====================================================================*/	
			include ('init_liste_bugs.php');
			/*===================================================================*/

			//==================================================================
			// Lisha transactional init
			//==================================================================
			$_SESSION[$ssid]['lisha'][$lisha_mt_id]->generate_public_header();   
			$_SESSION[$ssid]['lisha'][$lisha_mt_id]->generate_header();
			//==================================================================
		?>
		<title><?php echo $_SESSION[$ssid]['page_text'][1]['LT'];?></title>
	</head>
	<body onmousemove="lisha_move_cur(event);" onmouseup="lisha_mouseup();">
		
		<div style="width:100%;bottom:0;top:0;position:absolute;">
			<?php echo $_SESSION[$ssid]['lisha'][$lisha_mt_id]->generate_lisha(); ?>
		</div>
		
		<?php $_SESSION[$ssid]['lisha'][$lisha_mt_id]->lisha_generate_js_body();?>
	</body>
</html>