<?php 
	$dir_obj = '../vimofy/';
	
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../includes/common/ssid_session_start.php');
	/*===================================================================*/		
	
	
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
			$type_soft = 'ibug';
			require('../includes/common/textes.php');
			echo chr(10);
			/*===================================================================*/
		?>
		</script>
		<?php	
			
			/**==================================================================
			 * Vimofy include
			 ====================================================================*/	
			include ('../vimofy/vimofy_includes.php');
			/*===================================================================*/	
			
			/**==================================================================
			 * Vimofy init
			 ====================================================================*/	
			include ('init_liste_bugs.php');
			/*===================================================================*/	

			/*==================================================================
			* Vimofy internal init
			====================================================================*/  
			$_SESSION['vimofy'][$ssid][$vimofy_id]->generate_public_header();   
			$_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_generate_header();
			/*===================================================================*/    
		?>
		<title><?php echo $_SESSION[$ssid]['message'][1];?></title>
	</head>
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();">
		
		<div style="width:100%;bottom:0;top:0;position:absolute;">
			<?php echo $_SESSION['vimofy'][$ssid][$vimofy_id]->generate_vimofy(); ?>
		</div>
		
		<?php 
			$_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_generate_js_body();
		?>
	</body>
</html>