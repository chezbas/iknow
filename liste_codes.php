<?php 
/**==================================================================
 * Display iCodes list
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
	 * Lisha configuration and framework includes
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
			$type_soft = 4;
			require('includes/common/textes.php');
			echo '</script>';
			/**===================================================================*/
		
			//==================================================================
			// Lisha HTML header generation
			//==================================================================
			lisha::generate_common_html_header($ssid);	// Once
			//==================================================================
			
			/**==================================================================
			 * Lisha init setup
			 ====================================================================*/	
			require('includes/icode/vimofy/init_liste_icodes.php');
			/**===================================================================*/
		?>
		<title><?php echo $_SESSION[$ssid]['message']['iknow'][75]; ?></title>
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
					document.getElementById('lisha_icode_list_id').style.top = '67px';
					document.getElementById('btn_hide').style.display = 'block';
					document.getElementById('button_toggle_search').style.display = 'none';
					document.getElementById('btn_search').style.display = 'block';
					document.getElementById('logo_iknow').style.display = 'none';
				}
				else
				{
					document.getElementById('search_option').style.display = 'none';
					document.getElementById('lst_toolbar').style.height = '39px';
					document.getElementById('lisha_icode_list_id').style.top = '41px';
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
		<form id="filtreplus" method="post" action="liste_codes.php?ssid=<?php echo $ssid;if(isset($_GET['typec']))echo '&typec='.$_GET['typec']; ?>">
			<div class="lst_toolbar" id="lst_toolbar">
				<div class="btn_toggle">
					<input type="button" id="button_toggle_search" onmouseover="over('<?php echo $_SESSION[$ssid]['message'][505]; ?>');" onmouseout="unset_text_help();" value="<?php echo $_SESSION[$ssid]['message'][505]; ?>" onclick="toggle_options();" style="float:right;"/>
				</div>
				<div class="lst_toolbar_tags">
					<div id="search_option" style="display:none;">
						<input size=10 class="search" name="search" type="text" style="display:none;"/>
						<div style="text-align: center;">
							<table style="margin: 0 auto;">
								<tr>
									<td><span class="lib"><?php echo $_SESSION[$ssid]['message'][39]; ?> </span></td><td><input class="search" name="tags" type="text" size=10 value="<?php echo $tags; ?>"/></td>
									<td><span class="lib"><?php echo $_SESSION[$ssid]['message'][97]; ?> </span></td><td><input class="search" name="grtags" type="text" size=10 value="<?php echo $grtags; ?>"/></td>
									<td><span class="lib"><?php echo $_SESSION[$ssid]['message'][509]; ?> </span></td><td><input onmouseover="over('<?php echo $_SESSION[$ssid]['message'][516]; ?>');" onmouseout="unset_text_help();" type="checkbox" name="Texists" value="ok" <?php echo $lib_exists; ?>/></td>
								</tr>
								<tr>
									<td><span class="lib"><?php echo $_SESSION[$ssid]['message']['iknow'][511]; ?> </span></td><td><input size=10 class="search" id="lst_description_input" name="commentaire" type="text" value="<?php echo $comment; ?>"/></td>
									<td><span class="lib"><?php echo $_SESSION[$ssid]['message'][36]; ?> </span></td><td><input size=10 class="search" id="lst_corps_input" name="corps" type="text" value="<?php echo $crp; ?>"/></td>
									<?php 
										if($engine == 'ORA')
										{
											echo '<td>'.$_SESSION[$ssid]['message'][512].' :</td>';
											echo '<td>';
											echo '<select style="margin-left:8px;" id="MAJC" name="MAJC">';
											$array_value = Array(Array($_SESSION[$ssid]['message'][515],'OUI'),Array($_SESSION[$ssid]['message'][513],'NON'),Array($_SESSION[$ssid]['message'][514],''));
								
											foreach($array_value as $value)
											{
												if($majc_value == $value[1])
												{
													echo '<option selected value="'.$value[1].'">'.$value[0].'</option>';
												}
												else
												{
													echo '<option value="'.$value[1].'">'.$value[0].'</option>';
												}
											}
											echo '</select>';
											echo '</td>';
										}
										else
										{
											echo '<td></td><td></td>';
										}
									?>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn_toggle">
						<input style="display:none;" id="btn_hide" type="button"  value="<?php echo $_SESSION[$ssid]['message'][506]; ?>" onmouseover="over('<?php echo $_SESSION[$ssid]['message'][506]; ?>');" onmouseout="unset_text_help();" onclick="toggle_options();" style="float:right;"/>
					</div>
				</div>
				<div id="btn_search">
					<input name="fil" type="submit" value="<?php echo $_SESSION[$ssid]['message']['iknow'][507]; ?>" onmouseover="over('<?php echo $_SESSION[$ssid]['message'][508]; ?>');" onmouseout="unset_text_help();"/>
				</div>
			</div>
		</form>
		<div style="position:absolute;top:2px;left:5px;">
			<div class="boutton_new boutton_outils" onclick="window.open('./modif_icode.php<?php if(isset($_GET['typec']))echo '?typec='.$_GET['typec']; ?>');" onmouseover="over(decodeURIComponent(libelle_common[470])+' <?php echo $engine_title; ?>');" onmouseout="unset_text_help();"></div>
			<div class="boutton_url_home boutton_outils" onclick="window.location.replace('./');" onmouseover="over(decodeURIComponent(libelle_common[352]));" onmouseout="unset_text_help();"></div>
		</div>
		<div class="logo" style="position: absolute;top:0;width:100px;left:50%;margin-left:-50px;" id="logo_iknow"></div>
		<div style="width:100%;bottom:23px;top:41px;position:absolute;background-color:#999;" id="lisha_icode_list_id">
			<?php echo $_SESSION[$ssid]['lisha']['lisha_icode_list_id']->generate_lisha(); ?>
		</div>
		<?php 
			$_SESSION[$ssid]['lisha']['lisha_icode_list_id']->lisha_generate_js_body();
		?>
	<?php
	//==================================================================
	// Lisha HTML bottom generation
	//==================================================================
	lisha::generate_common_html_bottom($_SESSION[$ssid]['lisha']['lisha_icode_list_id']->c_dir_obj,$_SESSION[$ssid]['lisha']['configuration'][12],$_SESSION[$ssid]['lisha']['langue']);	// Once
	//==================================================================
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