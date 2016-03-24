<?php 
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../includes/common/ssid_simple.php');
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
	 * Setup page timeout
	 * Set a protection if value is too short
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
		<script type="text/javascript" src="search/search.js"></script>	
		<link rel="stylesheet" href="../../css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="../css/common/outils.css" type="text/css">
		<link rel="stylesheet" href="../css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="../css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="../js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="../js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="../js/common/session_management.js"></script>
		<script type="text/javascript" src="../ajax/common/ajax_generique.js"></script>

		<script type="text/javascript">
			var ssid = '<?php echo $ssid; ?>';
			var version_soft = '';

			var libelle_common = Array();
			<?php 
				/**==================================================================
				 * Recover text
				 ====================================================================*/	
				$type_soft = 5;
				require('../includes/common/textes.php');
				/*===================================================================*/
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
		</script>
		<title><?php echo $_SESSION[$ssid]['message']['475']?></title>
		<?php 		
			/**==================================================================
			 * Lisha init
			 ====================================================================*/	
			$dir_obj = '../vimofy/';
			require 'search/init_lst_search.php';
			/*===================================================================*/

			
			/**==================================================================
			 * Lisha internal init
			 ====================================================================*/	
			$obj_vimofy_search->generate_public_header();   
			$obj_vimofy_search->vimofy_generate_header();
			/*===================================================================*/  
		?>
	</head>
	<body>
		<!-- ===================================================================
		// Define div to display message box
		=================================================================== -->	
		<div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<!-- ===================================================================  -->
		<div id="header">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('Portail iKnow');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
		</div>	
		<div id="search_bar">
			<form action="javascript:search_iObject();" name="form_iobject">
				<input type="radio" name="iobject" id="rd_icode" checked="checked"><?php echo $_SESSION[$ssid]['message']['476']?> <input type="radio" name="iobject" id="rd_ifiche"/><?php echo $_SESSION[$ssid]['message']['477']?> <input type="text" id="input_search" class="gradient search" value="<?php echo $_SESSION[$ssid]['message']['479']?>" onclick="input_focus();"/><input type="button" id="button_search" value="<?php echo $_SESSION[$ssid]['message']['478']?>" onclick="search_iObject();"/><div style="top:50px;position:absolute;left:50%;" id="wait"></div>
			</form>
		</div>
		<div id="content"></div>
		<div id="footer"></div>
		<script type="text/javascript">
		var footer = new iknow_footer('../../js/common/iknow/');
		footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
		footer.add_element('<div id="txt_help"></div>',__FOOTER_LEFT__);		
		footer.generate();
		</script>
	</body>
</html>