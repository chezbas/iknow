<?php
	/**==================================================================
	 * MagicTree configuration
	 ====================================================================*/
	$path_root_framework = './';	// MagicTree and this file in the same level
	require('includes/MTSetup/setup.php');
	/*===================================================================*/		
	
	
	/**==================================================================
	 * MagicTree framework include
	 ====================================================================*/	
	require($path_root_magictree.'/page_includes.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Lisha configuration
	 ====================================================================*/
	require('includes/lishaSetup/main_configuration.php');
	/*===================================================================*/		
	
	
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('includes/common/ssid_session_start.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('includes/common/global_functions.php');
	/*===================================================================*/	


	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('includes/common/load_conf_session.php');
	/*===================================================================*/

	
	/**==================================================================
	 * MT configuration
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require($path_root_magictree.'/includes/common/load_conf_session.php');
	/*===================================================================*/

	
	/**==================================================================
	* Recover language from URL or Database
	====================================================================*/	
	require('includes/common/language.php');
	/*===================================================================*/

	
	// Define edit mode for each tree in page
	if(!isset($_SESSION['iknow'][$ssid]['identified_level']))
	{
		$_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"] = false;
	}
	else
	{
		if(!$_SESSION['iknow'][$ssid]['identified_level'])
		{
			$_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"] = false;
		}
		else
		{
			$_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"] = true;
		}
	}

	//==================================================================
	// Get ID of page
	//==================================================================
	if(isset($_GET["id"]))
	{
		if(!is_numeric($_GET["id"]))
		{
			error_log_details('fatal','you need an numeric id');
			die();
		}
		else
		{
			if(!isset($_SESSION[$ssid]['current_read_page']))
			{
				$id_page= $_GET["id"];
			}
			else
			{
				$id_page = $_SESSION[$ssid]['current_read_page'];
			}
		}
	}
	else
	{
		// No default page, force 1
		$id_page = '1';
	}
	//==================================================================	
	
	/**==================================================================
	 * Setup page timeout
	 ====================================================================*/	
	require('./includes/common/page_timeout.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('./includes/common/html_doctype.php');
	/*===================================================================*/	

	$_SESSION[$ssid]['MT']['langue'] = $_SESSION[$ssid]['langue'];
	$_SESSION[$ssid]['MT']['langue_TinyMCE'] = $_SESSION[$ssid]['langue_TinyMCE'];
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<script type="text/javascript">
			var ssid = '<?php echo $ssid; ?>';
			var language = '<?php echo $_SESSION[$ssid]['langue']; ?>';
		</script>
		<link rel="stylesheet" href="css/home/index.css" type="text/css"> <!-- * load custom page style * -->
		<link rel="stylesheet" href="css/home/tiny_details.css" type="text/css"> <!-- * load custom tiny page style * -->

		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">

		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		<script type="text/javascript" src="js/home/tiny/tiny_mce.js"></script> <!-- TinyMCE for documentation -->

		<?php 
			//==================================================================
			// Common tree HTML header generation
			//==================================================================
			itree::generate_common_html_header(	$link_mt,
												$ssid,
												__MAGICTREE_TABLE_TEXT__,
												__MAGICTREE_TABLE_SETUP__,
												__MAGICTREE_APPLICATION_RELEASE__,
												$_SESSION[$ssid]['MT']['langue'],
												$path_root_framework
												);
			//==================================================================
		
			/**==================================================================
			 * MagicTree definition of each tree in page
			 ====================================================================*/	
			include ('./includes/MTSetup/define/home.php');
			$_SESSION[$ssid]['MT'][$mt_home]->generate_html_header();
			/*===================================================================*/
		?>
		<script type="text/javascript" src="js/common/ClassVar.js"></script> <!-- * Include javascript main var class definition * -->
		<script type="text/javascript" src="js/common/ClassQuartz.js"></script> <!-- * Include timer Class * -->
		<script type="text/javascript" src="js/common/ClassOpacity.js"></script> <!-- * Include javascript Opacity class definition * -->
		<script type="text/javascript" src="js/common/cookie.js"></script>
		<script type="text/javascript" src="js/common/informations.js"></script>	
		<script type="text/javascript" src="js/common/time.js"></script>	
		<script  type="text/javascript" src="js/home/index.js"></script> <!-- Specific javascript of current page -->
		<script type="text/javascript">
			var ssid = '<?php echo $ssid; ?>';

			var MainTimer = new Class_timer();

			var contributor_offset_init = 800;
			var contributor_offset = contributor_offset_init;
			
			MainTimer.init(30,"T1");

			MainTimer.add_event(100,"count_me()");
			MainTimer.add_event(50,"blink()");
			MainTimer.add_event(1,"move_contributor()");

			<?php 
			if(!$_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"])
			{
				//echo 'MainTimer.add_event(2,"bounce_tool_bar(20)");';
			}
			?>
			
			MainTimer.start();		

			var libelle_common = Array();
			<?php 
				/**==================================================================
				* Recover text
				====================================================================*/	
				$type_soft = 'ihome'; // Type of screen
				require('includes/common/textes.php');
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
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
		<title><?php echo str_replace('&1',$_SESSION['iknow']['version_soft'],$_SESSION[$ssid]['message'][1]);?> </title>
	</head>
	<body onload="init_load('<?php echo $_SESSION[$ssid]['langue_TinyMCE'];?>');<?php echo 'MagicTree_'.$mt_home.'();'?>;">
		<!-- ================================================== MSGBOX ================================================= -->	
		<div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<!-- ===============================================  END MSGBOX ==============================================  -->
		<div id="ikdoc" style="width:500px;"></div><!-- SAME IN INDEX.CSS AND INDEX.JS CONFIGURATION  -->
		<div id="gauche" style="float: left; width:30%; height:100%; display:block;"></div>
		<div id="main_details" style="float: right;">
			<div id="headdetails" class="headdetails" <?php if(!$_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"]) echo 'onclick=\'MainTimer.add_event(1,"reduce_tool_bar()");\'';?> style="height: 0px;">
				<?php
				echo '<div class="welcome">'.str_replace('&1',$_SESSION['iknow']['version_soft'],js_protect($_SESSION[$ssid]['message'][12])).'</div>';
				echo '<div id="boutton_setup" class="boutton_setup" onClick="jump_screen(\'setup.php\')" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][55]).'\')"></div>';
				if($_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"])
				{
				echo '
				<div class="boutton_security_off" onClick="checklogin(\''.$_SESSION[$ssid]['langue'].'\',false,\''.$id_page.'\');" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message']['iknow'][492]).'\')"></div>
				<div id="tool_button_edit" class="tool_button_edit">
				<div id="boutton_edit" class="boutton_edit" onClick="show_tiny(\''.$_SESSION[$ssid]['langue'].'\')" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][57]).'\')"></div>
					<div id="boutton_back" class="boutton_back" onClick="html_detail_display(\'ikdoc\')" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][56]).'\')"></div>
					</div>
					';	
				}
				else
				{
					echo '
					<div class="boutton_security" onmouseout="lib_hover(\'\')" onmouseover="lib_hover(\''.js_protect($_SESSION[$ssid]['message'][9]).'\')" onClick="checklogin(\''.$_SESSION[$ssid]['MT']['langue'].'\',true,\''.$id_page.'\');"></div>
					';
				}
				?>
			</div>
			<div id="slideh" onclick="active_expand_tools_bar()"></div>
			<div id="details"></div><!-- HTML RESULT -->
			<form method="post" action="javascript:sauvegarder(ssid,'<?php echo $_SESSION[$ssid]['MT']['langue'];?>');">
			<div id="details_tiny" style="width: 100%; height: 100%;">
					<textarea id="elm1" name="elm1" rows="40" cols="90" style="width: 100%;">XX</textarea>
			</div>
			</form>
		</div>
		<div id="slidev" style="left:500px;" onclick="active_expand_navigation_tree()"></div>
		<!-- ========================================== END BARRE INFORMATIONS ===================================== -->
			<div id="footer" style="z-Index:500;"></div>
		<!-- ============================================= END FOOTER ================================================= -->
		<script type="text/javascript">
		read_details('ikdoc','<?php echo $id_page; ?>','U',''); // First load
		<?php 
		if($_SESSION[$ssid]['MT']['tree']['id']['ikdoc']["edit_mode"])
		{
			echo 'document.getElementById(\'headdetails\').style.height = "20px";';
			//echo 'resize_details();';			
		}
		?>
		var footer = new iknow_footer('../../js/common/iknow/');
		footer.add_element('<div id="automatic"></div>',__FOOTER_LEFT__);
		footer.add_element('<div id="txt_help"></div>',__FOOTER_LEFT__);
		footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
		footer.generate();
		</script>
	</body>
</html>