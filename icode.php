<?php
	//==================================================================
	// Display iCode identifier
	//==================================================================
	$type_soft = 4;
	//==================================================================

	/**==================================================================
	* Global initialization
	====================================================================*/ 
	require('includes/common/init_display.php');
	/**===================================================================*/

	//==================================================================
	// Generate iCode object
	//==================================================================
	if(isset($_GET['ID']) && !isset($_SESSION[$ssid]['objet_icode']))
	{
		$_SESSION[$ssid]['objet_icode'] = new icode($_GET['ID'],$ssid,$_SERVER['REMOTE_ADDR'],__CODE_VISU__,$_SESSION['iknow']['version_soft'],$_GET['version'],$_SESSION[$ssid]['langue']);
		$instance_iobject = &$_SESSION[$ssid]['objet_icode'];
	}
	else
	{
		if(!isset($_GET['ID']))
		{
			// None ID define
			// Display error message
			echo '</head>
					<title>'.$_SESSION[$ssid]['message']['iknow'][17].'</title>
					<body style="background-color:#1A67B7;">
					<div id="iknow_msgbox_background"></div>
					<div id="iknow_msgbox_conteneur" style="display:none;"></div>
					<script type="text/javascript">
						generer_msgbox(decodeURIComponent(libelle_common[17]),decodeURIComponent(libelle_common[484]).replace("\'","\\\'").replace(\'$iobject\',libelle[125]),\'erreur\',\'msg\');
					</script>';
			die();
		}
		else
		{
			$instance_iobject = &$_SESSION[$ssid]['objet_icode'];
			$instance_iobject->reload_icode($_GET['version']);
		}	
	}
	//==================================================================

	echo '<title>'.$_SESSION[$ssid]['message'][83].' : '.$_GET['ID'].'</title>';

	if(!isset($_SESSION[$ssid]['from_backup']))
	{
		if(isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'],'ikbackup=true') != false)
		{
			$_SESSION[$ssid]['from_backup'] = Array('date' => date('H:i:s'));
		}
		else
		{
			$_SESSION[$ssid]['from_backup'] = false;
		}
	}

	/**==================================================================
	* Lisha include
	====================================================================*/   
	$dir_obj = 'vimofy/';  
	require($dir_obj.'vimofy_includes.php');
	/**===================================================================*/

	/**==================================================================
	* Lisha init
	====================================================================*/     
	require('includes/icode/vimofy/visu/init_liste_varin_vim2.php');
	require('includes/icode/vimofy/visu/init_liste_varout_vim2.php');	
	require('includes/icode/vimofy/visu/init_liste_tags_vim2.php');	
	require('includes/icode/vimofy/visu/init_liste_version.php');
	/**===================================================================*/

	
	/**==================================================================
	 * Load Lisha framework
	====================================================================*/
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->generate_public_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_varout']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_tags']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_version_code']->vimofy_generate_header();
	/**===================================================================*/
?>
		<!--************************************************************************************************************
		 *		GENERATION DE LA PARTIE STATIQUE COMPLETE DE L'ENTETE DE LA PAGE		
		 *************************************************************************************************************-->	
		<link rel="stylesheet" href="css/icode/visu_code.css" type="text/css">
		<link rel="stylesheet" href="css/common/style.css" type="text/css">
		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_onglet.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
		
		<script type="text/javascript" src="js/common/iknow/iknow_onglet.js"></script>
		<script type="text/javascript" src="js/common/cookie.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/informations.js"></script>	
		<script type="text/javascript" src="js/common/time.js"></script>	
		<script type="text/javascript" src="js/icode/fonctions.js"></script>
		<script type="text/javascript" src="js/common/copy_url.js"></script>
		<script type="text/javascript" src="ajax/icode/ajax.js"></script>
		<script type="text/javascript" src="ajax/icode/ajax_visu.js"></script>
		
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		<!--**********************************************************************************************************-->
		
		<script type="text/javascript">
			var bloquer_pulse_tab_actif = true;	// si true empeche surcharge ajax
			var application = '<?php echo $_SESSION[$ssid]['application'];?>';
			var version_soft = '<?php echo $_SESSION['iknow']['version_soft']; ?>';
			var ssid = '<?php echo $ssid; ?>';
		
			//************************************************************************************************************
			// SIGNAL PRESENCE
			//************************************************************************************************************	
			<?php
				if(($_SESSION[$ssid]['configuration'][1] == 0 || $_SESSION[$ssid]['configuration'][1] == '' || !is_numeric($_SESSION[$ssid]['configuration'][1] || !strstr($_SESSION[$ssid]['configuration'][1],'.'))))
				{
					echo 'var interval_presence = '.__PRESENCE_TOP__.';'.chr(10);
				}
				else
				{
					echo 'var interval_presence = '.$_SESSION[$ssid]['configuration'][1] * (1000).';'.chr(10);
				}	
			?>
			//************************************************************************************************************			
		</script>
		
		<!-- BOUTON COPIER URL DANS PRESSE PAPIER--><script type="text/javascript" src="js/common/ZeroClipboard.js"></script>	
	
	</head>
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();" style="height:100%;width:100%;position:absolute;" onmousedown="vimofy_mousedown(event);">
		<?php 
			require('includes/icode/cache.php');
			$_SESSION['vimofy'][$ssid]['vimofy_version_code']->generate_lmod_header();
		?>
		<!-- ================================================== MSGBOX ================================================= -->	
		<div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<!-- ===============================================  END MSGBOX ==============================================  -->
			
		<!-- ================================================== TOOLBAR ================================================= -->	
		<div class="header_icode_visu_toolbar">
			<div class="iknow_toolbar">
				<ul id="header_list_menu">
					<?php 
						if($instance_iobject->get_flag_obsolete() == false)
						{
							echo '<li><div class="boutton_outils_edit boutton_outils" onclick="tuer_session();window.location.replace(\'./modif_icode.php?&amp;ID='.$_GET['ID'].'&amp;version='.$instance_iobject->get_version().'\');" onmouseover="over(59,211);" onmouseout="unset_text_help();"></div></li>';
						}
					?>
					<li><div class="boutton_url_copy boutton_outils" id="d_clip_button" onclick="javascript:copier_url();"></div></li>
					<li><div class="boutton_url_delete boutton_outils" onclick="javascript:delete_value_param(true);" onmouseover="over(96,23,'-','X');" onmouseout="unset_text_help();"></div></li>
					<?php 
						$ik_valmod = $instance_iobject->get_ik_valmod();
						if($ik_valmod == 0 || $ik_valmod == 1)
						{
							// Neutral value disabled
							echo '<li><div id="btn_replace_neutre" class="boutton_varin_neutre_off boutton_outils" onclick="javascript: change_neutral_values();" onmouseover="over(false,26,\'-\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}
						else
						{
							// Neutral value enabled
							echo '<li><div id="btn_replace_neutre" class="boutton_varin_neutre_on boutton_outils" onclick="javascript:change_neutral_values();"  onmouseover="over(98,27,\'-\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}

						if($ik_valmod == 0 || $ik_valmod == 2)
						{
							// Default value disabled
							echo '<li><div id="btn_replace_defaut" class="boutton_varin_default_off boutton_outils" onclick="javascript: change_defaut_values();" onmouseover="over(false,25,\'-\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}
						else
						{
							// VDefault value enabled
							echo '<li><div id="btn_replace_defaut" class="boutton_varin_default_on boutton_outils" onclick="javascript: change_defaut_values();" onmouseover="over(97,22,\'-\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}

					?>
					<li><div class="boutton_url_home boutton_outils" onclick="tuer_session();window.location.replace('liste_codes.php');" onmouseover="over(false,75,'-','X');" onmouseout="unset_text_help();"></div></li>
				</ul>
			</div>
			<?php 
				$max_version = $instance_iobject->get_max_version();
				if($max_version == $instance_iobject->get_version())
				{
					$div_icn_max = '<div class="ok pointer" onmouseover="over(99,30,\'-\',\'X\');" onmouseout="unset_text_help();" onclick="document.getElementById(\'lst_vimofy_version_code\').value = '.$max_version.';changer_version();"></div>';
				}
				else
				{
					$div_icn_max = '<div class="warning pointer" onmouseover="over(99,31,\'-\',\'X\');" onmouseout="unset_text_help();" onclick="document.getElementById(\'lst_vimofy_version_code\').value = '.$max_version.';changer_version();"></div>';
				}
				echo generate_logo_header('<div style="height:20px;line-height:20px;">'.$_SESSION[$ssid]['message'][4].' <span class="bold">'.$_GET['ID'].'</span></div><div style="height:24px;"><table summary=""><tr><td>'.$_SESSION[$ssid]['message'][62].'</td><td>'.$instance_iobject->genere_liste_version().'</td><td>'.$div_icn_max.'</td></tr></table></div><div style="height:18px;line-height:20px;">'.$instance_iobject->get_moteur().' - '.$instance_iobject->get_engine_version().'</div>');
			?>
			<div  class="header_title header_title_edit">
				<div id="icode_title" style="height:45px;line-height:45px;overflow:hidden;">
					<?php 
						echo $instance_iobject->get_titre_sans_bbcode();
					?>
				</div>
			</div>
		</div>
		<?php 
		
			if($_SESSION[$ssid]['configuration'][42] != '')
			{
				echo '<div style="background:url(images/env/'.$_SESSION[$ssid]['configuration'][42].'.png) repeat-x;height:65px;position:absolute;top:0;left:0;right:0;"></div>';
			}
		
		?>
		<!-- ============================================= END TOOLBAR ================================================= -->
		
			<!-- ============================================= BEGIN ONGLETS ================================================= -->
				<?php require('includes/common/onglets.php'); ?>	
			<!-- ============================================= END ONGLETS ================================================= -->
			
			
			<script type="text/javascript">
				<?php 
					echo $instance_iobject->generer_icode();
					
					$end_hour = microtime(true);
								
					/************************************************************************************************************
					 *	CONSERVER L'ONGLET COURANT ACTIF AU CHARGEMENT
					 *************************************************************************************************************/	
					if(isset($_GET['tab-level']))
					{
						echo 'a_tabbar.setTabActive(\'tab-level'.$_GET['tab-level'].'\');';
					}
					else
					{
						echo 'a_tabbar.setTabActive(\''.$instance_iobject->get_tab_actif().'\');';
					}
					/************************************************************************************************************/
					
					/************************************************************************************************************
					 *	PURGE DES COOKIES NR_IKNOW_10_
					 *************************************************************************************************************/	
					echo $instance_iobject->purge_cookie();
					/************************************************************************************************************/
					
				?>
					
				var bloquer_pulse_tab_actif = false;
	
				//************************************************************************************************************
				// INSTANCIATION DE LA CLASSE ZeroClipboard (Presse papier)
				//************************************************************************************************************				
	            var clip = new ZeroClipboard.Client();
	            clip.setText(url_remove('ssid'));
	            clip.glue('d_clip_button');
	            //************************************************************************************************************	
	            
				//************************************************************************************************************
				// Définition des variables statiques globales	
				//************************************************************************************************************			 
	            var ID_code = '<?php echo $_GET['ID']; ?>';
	            var version_code = <?php echo $instance_iobject->get_version(); ?>;
				var ID_temp = <?php echo $instance_iobject->get_id_temp(); ?>;
				var maj_vimofy_param = false;
	
				<?php 
					$gc_lifetime = ini_get('session.gc_maxlifetime'); 
					$end_visu_date  = time() + $gc_lifetime;
					$end_visu_time = $end_visu_date;
					$end_visu_date = date('m/d/Y',$end_visu_date);
					$end_visu_time = date('H:i:s',$end_visu_time);
				?>
	
				var end_visu_date = '<?php echo $end_visu_date; ?>';
				var end_visu_time = '<?php echo $end_visu_time; ?>';
			
				<?php 
					switch($ik_valmod) 
					{
						case 1:
							echo 'var state_change_defaut_values = true;';
							echo 'var state_change_neutral_values = false;';
							break;
						case 2:
							echo 'var state_change_defaut_values = false;';
							echo 'var state_change_neutral_values = true;';
							break;
						case 3:
							echo 'var state_change_defaut_values = true;';
							echo 'var state_change_neutral_values = true;';
							break;
						default:
							echo 'var state_change_defaut_values = false;';
							echo 'var state_change_neutral_values = false;';
							break;
					}
				?>
				//************************************************************************************************************	
			</script>
			
			<!-- ============================================= BEGIN FOOTER ================================================= -->
		<div id="iknow_menu_footer_container" class="iknow_el_container div_menu_footer">
			<div id="iknow_menu_footer_internal_container" class="iknow_el_internal_container iknow_menu_footer_internal_container">
				<div style="border-bottom: 1px solid #8B92B1;padding-bottom:5px;margin-bottom:5px;">
					<table summary="">
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][348]; ?> : </td><td class="menu_valeur"><?php echo $_SESSION['iknow']['version_soft']; ?></td></tr>
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][349]; ?> : </td><td class="menu_valeur"><?php echo $_SESSION[$ssid]['id_temp']; ?></td></tr>
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][350]; ?> : </td><td class="menu_valeur"><?php echo date('d/m/Y H:i'); ;?></td></tr>
						<?php echo '<tr><td>Load : </td><td class="menu_valeur">'.round($end_hour - $start_hour,3).' s / '.round(memory_get_peak_usage()/1000000,2).' Mo</td></tr>'.chr(10); ?>
					</table>
				</div>
				<table id="div_menu_footer_icones" summary="">
					<tr>
						<!-- Liste des raccourcis clavier --><td><div class="shortcut pointer" onclick="javascript:lst_rac();" onmouseover="ikdoc();set_text_help(160,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--        Liste des bugs        --><td><div class="bug pointer" onclick="javascript:window.open('bugs');" onmouseover="ikdoc();set_text_help(161,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--         Portail iKnow        --><td><div class="maison pointer" onclick="javascript:window.open('.');" onmouseover="ikdoc();set_text_help(352,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--       Liste des fiches       --><td><div class="liste pointer" onclick="javascript:window.open('liste_codes.php');" onmouseover="ikdoc();set_text_help(75,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
					</tr>
				</table>
			</div>
		</div>
		
		
		
		<!-- ================================================= BARRE LOGS ============================================== -->
		<div id="iknow_log_container" class="iknow_el_container iknow_ctrl_el">
			<div id="iknow_log_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container">
			</div>
		</div>
		<!-- ============================================== END BARRE LOGS ============================================= -->
		
		<!-- ============================================ BARRE INFORMATIONS ====================================== -->
		<div id="iknow_ctrl_container" class="iknow_el_container iknow_ctrl_el" style="float: left;">
			<div id="iknow_ctrl_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container">
			<?php
				if($_SESSION[$ssid]['from_backup'] != false && $instance_iobject->get_global_coherent_check_end() != true)
				{
					echo '<table id="informations">'; 
					echo '<tr><td><a href="#" class="ok"></a></td><td class="iknow_titre_controle">'.str_replace('$hour',$_SESSION[$ssid]['from_backup']['date'],$_SESSION[$ssid]['message'][217]).'</td></tr>';
					echo '</table>';
					$display_ctrl_bar = '';
				}	
				else
				{
					$display_ctrl_bar = 'display:none;';
				}				
			?>
			</div>
		</div>
		<!-- ========================================== END BARRE INFORMATIONS ===================================== -->
			<div id="footer"></div>
			<!-- ============================================= END FOOTER ================================================= -->
			<script type="text/javascript">
				eval('g_timer_iknow_menu_footer_container_toggle = null;');
				eval('g_height_iknow_menu_footer_internal_container = null;');
				eval('g_height_iknow_menu_footer_max = null;');
				eval('g_iknow_menu_footer_timer_increment = null;');

				eval('g_timer_iknow_ctrl_container_toggle = null;');
				eval('g_height_iknow_ctrl_internal_container = null;');
				eval('g_height_iknow_ctrl_info_max = null;');
				eval('g_iknow_ctrl_info_timer_increment = null;');
				
				//************************************************************************************************************
				// RACCOURCIS CLAVIER
				//************************************************************************************************************				
				var isCtrl = false;

				document.onkeyup = function(e)
				{
				    if(e.which == 17)
					{
					     isCtrl=false;
				    }
				}
				
				document.onkeydown = function(e)
				{
					e = e || event; 
					//alert(e.which);
				    // REDUCTION DE LA BARRE D'INFORMATIONS (ECHAP)
				    if(e.keyCode == 27) 
					{
				    	iknow_toggle_control();
				        return false;
				    }	
					//Detection du CTRL
				    if(e.keyCode == 17){
					    
					     isCtrl=true;
					     
				    }

			        // fleche du bas
			        if(e.keyCode == 40 && isCtrl == true) {
			        	a_tabbar.previous();
				    	set_tabbar_actif(a_tabbar.getActiveTab());
				    	charger_var_dans_url(true);
			        }
				    // fleche du haut
			        if(e.keyCode == 38 && isCtrl == true) {
				    	a_tabbar.next();
				    	set_tabbar_actif(a_tabbar.getActiveTab());
				    	charger_var_dans_url(true);
			        }

				    // AIDE - CTRL+F11
			        //if(e.keyCode == 112 && isCtrl == true)
			        if(e.keyCode == 122 && isCtrl == true)
				    {
			        	if((viewer_aide) && (viewer_aide!=''))
			        	{
			        		window.open("index.php?id="+viewer_aide+'&'+keywordlang+"="+iknow_lng);
			        	} 
			        	else
				        {  // page d'aide par défaut
			        	//	viewer_aide = 'start';
			        	//	window.open("index.php?ID=");
			        	}    	
			        }        
				};	// End onkeydown
	
			    
			    document.onkeyup = function(e) 
			    {
			    	e = e || event; 
			        if(e.keyCode == 17)
			        {
				        isCtrl = false;
			        }
			    };
	
				//************************************************************************************************************		
			    //ctrl_free_cookie('lib_erreur',true);
			    //signal_presence();
			    document.getElementById('lst_vimofy_version_code').value = "<?php echo $instance_iobject->get_version(); ?>";
			    document.getElementById('ZeroClipboardMovie_1').onmouseover = function(){over(68,'86','-','X');document.getElementById('d_clip_button').style.backgroundPosition = '-175px -35px';}; 
			    document.getElementById('ZeroClipboardMovie_1').onmouseout =  function(){unset_text_help();document.getElementById('d_clip_button').style.backgroundPosition = '-175px 0';};
			    <?php 
					if($_SESSION[$ssid]['from_backup'] != false && $instance_iobject->get_global_coherent_check_end() != true)
					{
						echo "iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
						      ctrl_coherence_last_backup();
						      var ctrl_coherence_last_backup_timer = setInterval('check_global_coherence_end()',800);";
					}
				?>

				var footer = new iknow_footer('js/common/iknow/','includes/common/maj_presence.php',"ssid="+ssid+"&id="+ID_code+"&id_temp="+ID_temp+"&type_action="+application,interval_presence);

				<?php 
				
				if($display_ctrl_bar == '')
				{
				?>
					footer.add_element('<div style="height:22px;"><div id="iknow_ctrl_arrow" style="<?php echo $display_ctrl_bar; ?>" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(90);" onmouseout="ikdoc(\'\');unset_text_help();"></div></div>',__FOOTER_LEFT__);
				<?php 
				}
				?>
				footer.add_element('<div id="iknow_var_format" onmouseover="over(118,52);" onmouseout="unset_text_help();"><?php echo $instance_iobject->get_format_var(); ?></div>',__FOOTER_LEFT__);

				<?php 
					if($_SESSION[$ssid]['from_backup'] != false && $instance_iobject->get_global_coherent_check_end() != true)
					{
						$footer_progress = '<div id="iknow_ctrl_in_progress"><div style="background:url(images/connexion.gif) no-repeat center;height:16px;margin:4px 5px 0 0;width:16px;float:left;"></div>'.$_SESSION[$ssid]['message']['iknow'][12].'</div>';
					}
					else
					{
						if($_SESSION[$ssid]['from_backup'] != false)
						{
							$footer_progress = '<div id="iknow_ctrl_in_progress">';
							$qtt_err = $instance_iobject->get_global_coherent_check_qtt_err();
							if($qtt_err == 1)
							{
								$footer_progress .= $_SESSION[$ssid]['message']['iknow'][14].' <a href="outils/coherent_check/detail.php?ssid='.$ssid.'&iobject=__ICODE__&id='.$_GET['ID'].'" target="_blank">Detail</a>';
							}
							elseif($qtt_err > 1)
							{
								$footer_progress .=  str_replace('$x',$qtt_err,$_SESSION[$ssid]['message']['iknow'][13]).' <a href="outils/coherent_check/detail.php?ssid='.$ssid.'&iobject=__ICODE__&id='.$_GET['ID'].'" target="_blank">Detail</a>';
							}
							$footer_progress .= '</div>';
						}
						else
						{
							$footer_progress = '';
						}
					}
				?>
				footer.add_element('<?php echo str_replace("'","\'",$footer_progress); ?>',__FOOTER_LEFT__);
				footer.add_element('<div class="iknow_lib_hover" id="iknow_header_ss_titre">&nbsp;</div>',__FOOTER_LEFT__);
				footer.add_element('<div class="lib_btn_menu_footer"><?php echo $_SESSION[$ssid]['configuration'][36]; ?></div><div class="btn_menu_footer" onMouseDown="return false;" onclick="iknow_toggle_el(\'iknow_menu_footer_container\',\'iknow_menu_footer_internal_container\');"></div>',__FOOTER_RIGHT__);
				footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
				if(strstr(navigator.userAgent,'MSIE'))
				{
					// Message don't' use IE
					footer.add_element('<div id="conseil_navigateur"><?php echo str_replace("'","\'",$_SESSION[$ssid]['message']['iknow'][16]); ?></div>',__FOOTER_RIGHT__);
				}
				footer.generate();
				if(strstr(navigator.userAgent,'MSIE'))
				{
					blink_error_msg_start('conseil_navigateur');
				}
			</script>		
			<?php
				$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_js_body();
				$_SESSION['vimofy'][$ssid]['vimofy2_varout']->vimofy_generate_js_body();
				$_SESSION['vimofy'][$ssid]['vimofy2_tags']->vimofy_generate_js_body();
				$_SESSION['vimofy'][$ssid]['vimofy_version_code']->vimofy_generate_js_body();
			?>
	</body>
</html>