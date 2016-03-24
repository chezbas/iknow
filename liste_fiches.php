<?php
/**==================================================================
 * Display iSheets list
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
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" type="image/png" href="favicon.ico" />
		<link rel="stylesheet" href="css/common/outils.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="css/common/list_iobject.css" type="text/css">
		
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		
		<?php 			
			/**==================================================================
			* Recover text
			====================================================================*/	
			echo '<script type="text/javascript">';
			$type_soft = 2;
			require('includes/common/textes.php');
			echo '</script>';
			/**===================================================================*/

			//==================================================================
			// Lisha HTML header generation
			//==================================================================
			lisha::generate_common_html_header($ssid);	// Once
			//==================================================================

			/**==================================================================
			 * Lisha setup
			 ====================================================================*/	
			require('./includes/ifiche/vimofy/init_liste_fiches.php');
			/**===================================================================*/
		?>
		<title><?php echo $_SESSION[$ssid]['message'][408]; ?></title>
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

			function toggle_options()
			{
				if(document.getElementById('search_option').style.display == 'none' || document.getElementById('search_option').style.display == '')
				{
					document.getElementById('search_option').style.display = 'block';
					document.getElementById('lst_toolbar').style.height = '65px';
					document.getElementById('lisha_isheet_list_id').style.top = '67px';
					document.getElementById('btn_hide').style.display = 'block';
					document.getElementById('button_toggle_search').style.display = 'none';
					document.getElementById('btn_search').style.display = 'block';
					document.getElementById('logo_iknow').style.display = 'none';
				}
				else
				{
					document.getElementById('search_option').style.display = 'none';
					document.getElementById('lst_toolbar').style.height = '39px';
					document.getElementById('lisha_isheet_list_id').style.top = '41px';
					document.getElementById('btn_hide').style.display = 'none';
					document.getElementById('button_toggle_search').style.display = '';
					document.getElementById('btn_search').style.display = 'none';
					document.getElementById('logo_iknow').style.display = 'block';
				}
			}

			function over(p_txt)
			{
				document.getElementById('txt_help').innerHTML = p_txt;
			}

			function unset_text_help()
			{
				document.getElementById('txt_help').innerHTML = '';
			}

		</script>
	</head>
	<body onmousemove="lisha_move_cur(event);" onmouseup="lisha_mouseup();">
		<!-- =================================================  MSGBOX ================================================= -->	
		<div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<!-- ===============================================  END MSGBOX ==============================================  -->
		<div class="lst_toolbar" id="lst_toolbar">
		<form id="filtreplus" method="post" action="liste_fiches.php?ssid=<?php echo $ssid; ?>">
			<div class="btn_toggle">
				<input type="button" id="button_toggle_search" onmouseover="over('<?php echo str_replace("'","\'",$_SESSION[$ssid]['message'][467]); ?>');" onmouseout="unset_text_help();" value="<?php echo $_SESSION[$ssid]['message'][467]; ?>" onclick="toggle_options();" style="float:right;"/>
			</div>
				<div class="lst_toolbar_tags">
					<div id="search_option" style="display:none;">
						
							<div style="height:26px;padding-top: 20px;">
								<input size=10 class="search" name="search" type="text" value="ok" style="display:none;"/>
								<span class="lib" ><?php echo $_SESSION[$ssid]['message'][73]; ?> </span><input class="search" name="tags" type="text" size=10 value="<?php echo $tags; ?>"/>
								<span class="lib" ><?php echo $_SESSION[$ssid]['message'][410]; ?> </span><input class="search" name="grtags" type="text" size=10 value="<?php echo $grtags; ?>"/>
								<span class="lib" ><?php echo $_SESSION[$ssid]['message'][470]; ?> </span><input onmouseover="over('<?php echo $_SESSION[$ssid]['message'][494]; ?>');" onmouseout="unset_text_help();" type="checkbox" name="Texists" value="ok" <?php echo $lib_exists; ?>/>
							</div>
					</div>
					<div class="btn_toggle">
						<input style="display:none;" id="btn_hide" type="button"  value="<?php echo $_SESSION[$ssid]['message'][468]; ?>" onmouseover="over('<?php echo str_replace("'","\'",$_SESSION[$ssid]['message'][468]); ?>');" onmouseout="unset_text_help();" onclick="toggle_options();" style="float:right;"/>
					</div>
				</div>
				<div id="btn_search">
					<input name="fil" type="submit" value="<?php echo $_SESSION[$ssid]['message'][469]; ?>" onmouseover="over('<?php echo str_replace("'","\'",$_SESSION[$ssid]['message'][495]); ?>');" onmouseout="unset_text_help();"/>
				</div>
			</div>
		</form>
		<div style="position:absolute;top:2px;left:5px;">
			<div class="boutton_new boutton_outils" onclick="window.open('./modif_fiche.php');" onmouseover="over(decodeURIComponent(libelle[63]));" onmouseout="unset_text_help();"></div>
			<div class="boutton_url_home boutton_outils" onclick="window.location.replace('./');" onmouseover="over(decodeURIComponent(libelle_common[352]));" onmouseout="unset_text_help();"></div>
		</div>
		<div class="logo" style="position: absolute;top:0;width:100px;left:50%;margin-left:-50px;" id="logo_iknow"></div>
		<div style="width:100%;bottom:23px;top:41px;position:absolute;background-color:#999;" id="lisha_isheet_list_id">
			<?php echo $_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->generate_lisha(); ?>
		</div>
		<?php
		$_SESSION[$ssid]['lisha']['lisha_isheet_list_id']->lisha_generate_js_body();
		?>
		<div id="footer"></div>
		<script type="text/javascript">
			<?php 
				if(isset($_POST['search']))
				{
					echo 'toggle_options();';
				}
			?>
			var footer = new iknow_footer('js/common/iknow/');
			footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
			footer.add_element('<div id="txt_help"></div>',__FOOTER_LEFT__);		
			footer.generate();
		</script>
	</body>
</html>