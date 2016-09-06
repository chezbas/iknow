<?php
	/**==================================================================
	* iSheet view
	====================================================================*/
    $type_soft = 2;

    /**==================================================================
	* Global initialization
	====================================================================*/ 
	require('includes/common/init_display.php');	
	/**=================================================================*/
	
	/**==================================================================
	* Generate iSheet object
	====================================================================*/ 
	if(isset($_GET['ID']) && !isset($_SESSION[$ssid]['objet_fiche']))
	{
		$_SESSION[$ssid]['objet_fiche'] = new fiche($_GET['ID'],$ssid,$_SERVER['REMOTE_ADDR'],2,$_SESSION['iknow']['version_soft'],$_GET['version'],$_SESSION[$ssid]['langue']);
		$instance_iobject = &$_SESSION[$ssid]['objet_fiche'];
	}	
	else
	{
		if(!isset($_GET['ID']))
		{
		// No ID parameter defined in browser url, then display error
			echo '</head>
					<title>'.$_SESSION[$ssid]['message']['iknow'][17].'</title>
					<body style="background-color:#A61415;">
					<div id="iknow_msgbox_background"></div>
					<div id="iknow_msgbox_conteneur" style="display:none;"></div>
					<script type="text/javascript">
						generer_msgbox(decodeURIComponent(libelle_common[17]),decodeURIComponent(libelle_common[484]).replace("\'","\\\'").replace(\'$iobject\',libelle[58]),\'erreur\',\'msg\');
					</script>';
			die();
		}
		else
		{
		// Isheet ID defined, ok
			$instance_iobject = &$_SESSION[$ssid]['objet_fiche'];
			if(isset($_GET['version']) && $_GET['version'] != null)$instance_iobject->reload_isheet($_GET['version']);
		}
	}
	/**=================================================================*/
	
	//==================================================================
	// Generate title
	//==================================================================
	echo '<title>'.$_SESSION[$ssid]['message'][438].' : '.$_GET['ID'].'</title>';

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
	/**=================================================================*/
	
	/**==================================================================
	* Lisha init
	====================================================================*/     
	require('includes/ifiche/vimofy/visu/init_liste_varin.php');
	require('includes/ifiche/vimofy/visu/init_liste_varout.php');
	require('includes/ifiche/vimofy/visu/init_liste_tags.php');
	require('includes/ifiche/vimofy/visu/init_liste_version.php');
	/**===================================================================*/

	/**==================================================================
	* Lisha internal init
	====================================================================*/
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->generate_public_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_varout']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_tags']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_version_fiche']->vimofy_generate_header();
	/**===================================================================*/
?>
		<!--================================================================================
		 -	GENERATE STATIC HEAD HTML PAGE
		 ================================================================================-->	
		<link rel="stylesheet" href="css/ifiche/visu_fiche.css" type="text/css">
		<link rel="stylesheet" href="css/ifiche/common_fiche.css" type="text/css">
		<link rel="stylesheet" href="css/common/style.css" type="text/css">
		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_onglet.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
		
		<script type="text/javascript" src="js/common/iknow/iknow_onglet.js"></script>
		<script type="text/javascript" src="js/common/cookie.js"></script>
		<script type="text/javascript" src="js/ifiche/fonctions.js"></script>
		<script type="text/javascript" src="js/ifiche/fonctions_visu.js"></script>
		<script type="text/javascript" src="js/common/informations.js"></script>
		<script type="text/javascript" src="js/common/time.js"></script>
		<script type="text/javascript" src="js/common/copy_url.js"></script>	
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="ajax/ifiche/ajax.js"></script>
		<script type="text/javascript" src="ajax/ifiche/ajax_visu.js"></script>
		
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		<!--================================================================================-->

		<script type="text/javascript">
			var bloquer_pulse_tab_actif = true;		// true means avoid overload ajax call on tab click ( usefull on iSheet loading page sequence )
			var application = '<?php echo $instance_iobject->get_type(); ?>';
			var version_soft = '<?php echo $_SESSION['iknow']['version_soft']; ?>';
			var ssid = '<?php echo $ssid; ?>';
			var start_visu = '<?php echo $_SESSION[$ssid]['start_visu']; ?>';
			<?php 
				$gc_lifetime = ini_get('session.gc_maxlifetime'); 
				$end_visu_date  = time() + $gc_lifetime;
				$end_visu_time = $end_visu_date;
				$end_visu_date = date('m/d/Y',$end_visu_date);
				$end_visu_time = date('H:i:s',$end_visu_time);
			?>
			var end_visu_date = '<?php echo $end_visu_date; ?>';
			var end_visu_time = '<?php echo $end_visu_time; ?>';
			 /*================================================================================ */
			 /* SIGNAL PRESENCE																	*/
			 /*=================================================================================*/
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
			/*================================================================================*/		
		</script>
		<!-- BUTTON TO PAST URL STRING INTO CLIPBOARD SYSTEM--><script type="text/javascript" src="js/common/ZeroClipboard.js"></script>
	</head>	
	<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();" onKeydown="javascript:fleches(event);" onmousedown="vimofy_mousedown(event);">
		<?php 
			require('includes/ifiche/cache.php');
			$_SESSION['vimofy'][$ssid]['vimofy_version_fiche']->generate_lmod_header();
			?>
		<!-- Div object to display info bubble (ddrivetip)--><div id="dhtmltooltip"></div>
		<!-- ================================================== START MSGBOX ================================================= -->
		<div id="iknow_msgbox_background"></div>
		<div id="iknow_msgbox_conteneur" style="display:none;"></div>
		<!-- ===============================================  END MSGBOX ==============================================  -->
		
		<!-- ================================================== START TOOLBAR ================================================= -->
		<div class="header_ifiche_visu_toolbar">
			<div class="iknow_toolbar">
				<ul id="header_list_menu">
					<?php
						if($instance_iobject->get_flag_obsolete() == false)
						{
							echo '<li><div class="boutton_outils_edit boutton_outils" onclick="tuer_session();window.location.replace(\'./modif_fiche.php?&amp;ID='.$_GET['ID'].'&amp;version='.$instance_iobject->get_version().'\');" onmouseover="over(false,57,\'\',\'\');" onmouseout="unset_text_help();"></div></li>';
						}
					?>
					<li><div class="boutton_url_copy boutton_outils" id="d_clip_button" onclick="javascript:copier_url();"></div></li>
					<li><div class="boutton_url_delete boutton_outils" onclick="javascript:effacer_param_url();" onmouseover="over(false,23,'-','X');" onmouseout="unset_text_help();"></div></li>
					<?php 
						$ik_valmod = $instance_iobject->get_ik_valmod();
						
						if($ik_valmod == 0 || $ik_valmod == 1)
						{
							// VarIn : Don't use neutrals values
							echo '<li><div id="btn_replace_neutre" class="boutton_varin_neutre_off boutton_outils" onclick="javascript: change_neutral_values();" onmouseover="over(false,421,this);" onmouseout="unset_text_help();"></div></li>';
						}
						else
						{
							// VarIn : Enable to use neutrals values
							echo '<li><div id="btn_replace_neutre" class="boutton_varin_neutre_on boutton_outils" onclick="javascript:change_neutral_values();"  onmouseover="over(false,27,\'\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}

						if($ik_valmod == 0 || $ik_valmod == 2)
						{
							// VarIn : Don't use defaults values
							echo '<li><div id="btn_replace_defaut" class="boutton_varin_default_off boutton_outils" onclick="javascript: change_defaut_values();" onmouseover="over(false,419,this);" onmouseout="unset_text_help();"></div></li>';
						}
						else
						{
							// VarIn : Enable to use defaults values
							echo '<li><div id="btn_replace_defaut" class="boutton_varin_default_on boutton_outils" onclick="javascript: change_defaut_values();" onmouseover="over(false,\'22\',\'-\',\'X\');" onmouseout="unset_text_help();"></div></li>';
						}
					?>
					<li><div class="boutton_url_home boutton_outils" onclick="tuer_session();window.location.replace('./liste_fiches.php');" onmouseover="over(false,408,'','');" onmouseout="unset_text_help();"></div></li>
				</ul>
			</div>
			<?php
				$max_version = $instance_iobject->get_max_version();
				if($max_version == $instance_iobject->get_version())
				{
					$div_icn_max = '<div class="ok pointer" onmouseover="over(false,30,\'\',\'X\');" onmouseout="unset_text_help();" onclick="document.getElementById(\'lst_vimofy_version_fiche\').value = '.$max_version.';changer_version();"></div>';
				}
				else
				{
					$div_icn_max = '<div class="warning pointer" onmouseover="over(false,31,\'\',\'X\');" onmouseout="unset_text_help();" onclick="document.getElementById(\'lst_vimofy_version_fiche\').value = '.$max_version.';changer_version();"></div>';
				}
				
				echo generate_logo_header('<div style="height:30px;line-height:30px;">'.$_SESSION[$ssid]['message'][409].' <span class="bold">'.$_GET['ID'].'</span></div><div style="height:20px;"><table summary=""><tr><td>'.$_SESSION[$ssid]['message'][48].'</td><td>'.$instance_iobject->genere_liste_version().'</td><td>'.$div_icn_max.'</td></tr></table></div>');
			?>
			<div id="ifiche_title" class="header_title">
				<?php echo $instance_iobject->get_titre_sans_bbcode(); ?>
			</div>
		</div>
		<?php 
		
			if($_SESSION[$ssid]['configuration'][42] != '')
			{
				echo '<div style="background:url(images/env/'.$_SESSION[$ssid]['configuration'][42].'.png) repeat-x;height:65px;position:absolute;top:0;left:0;right:0;"></div>';
			}
		
		?>
		<!-- =============================================  END TOOLBAR ================================================= -->

		<!-- BEGIN tabs -->
			<?php require 'includes/common/onglets.php'; ?>
		<!-- END tabs -->

		<!-- ================================================== BEGIN FOOTER ================================================= -->	
		<div id="iknow_menu_footer_container" class="iknow_el_container div_menu_footer">
			<div id="iknow_menu_footer_internal_container" class="iknow_el_internal_container iknow_menu_footer_internal_container">
				<div style="border-bottom: 1px solid #8B92B1;padding-bottom:5px;margin-bottom:5px;">
					<table summary="">
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][348]; ?> : </td><td class="menu_valeur"><?php echo $_SESSION['iknow']['version_soft']; ?></td></tr>
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][349]; ?> : </td><td class="menu_valeur"><?php echo $_SESSION[$ssid]['id_temp']; ?></td></tr>
						<tr><td><?php echo $_SESSION[$ssid]['message']['iknow'][350]; ?> : </td><td class="menu_valeur"><?php echo date('d/m/Y H:i'); ;?></td></tr>
						<tr><td>Load : </td><td class="menu_valeur"><div id="footer_debug_load"> Mo</div></td></tr>
					</table>
				</div>
				<table id="div_menu_footer_icones" summary="">
					<tr>
						<!-- Liste des raccourcis clavier --><td><div class="shortcut pointer" onclick="javascript:lst_rac();" onmouseover="ikdoc();set_text_help(160,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--        Liste des bugs        --><td><div class="bug pointer" onclick="javascript:window.open('bugs');" onmouseover="ikdoc();set_text_help(161,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--         Portail iKnow        --><td><div class="maison pointer" onclick="javascript:window.open('.');" onmouseover="ikdoc();set_text_help(352,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
						<!--       Liste des fiches       --><td><div class="liste pointer" onclick="javascript:window.open('liste_fiches.php');" onmouseover="ikdoc();set_text_help(408);" onmouseout="ikdoc('');unset_text_help();"></div></td>
					</tr>
				</table>
			</div>
		</div>
		<!-- ============================================== BEGIN LOGS AREA ============================================== -->
		<div id="iknow_log_container" class="iknow_el_container iknow_ctrl_el">
			<div id="iknow_log_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container">
			</div>
		</div>
		<!-- ============================================== END LOGS AREA ============================================= -->

		<!-- ============================================ BEGIN INFORMATION AREA ====================================== -->
		<div id="iknow_ctrl_container" class="iknow_el_container iknow_ctrl_el" style="float: left;">
			<div id="iknow_ctrl_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container">
			<?php
				if($_SESSION[$ssid]['from_backup'] != false && $instance_iobject->get_global_coherent_check_end() != true)
				{
					echo '<table id="informations" summary="">'; 
					echo '<tr><td><a href="#" class="ok"></a></td><td class="iknow_titre_controle">'.str_replace('$hour',$_SESSION[$ssid]['from_backup']['date'],$_SESSION[$ssid]['message'][453]).'</td></tr>';
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
		<!-- ========================================== END INFORMATION AREA ===================================== -->

		<div id="footer"></div>
		<!-- ================================================== END FOOTER ================================================= -->

		<script type="text/javascript">
			eval('g_timer_iknow_menu_footer_container_toggle = null;');
			eval('g_height_iknow_menu_footer_internal_container = null;');
			eval('g_height_iknow_menu_footer_max = null;');
			eval('g_iknow_menu_footer_timer_increment = null;');
			eval('g_timer_iknow_ctrl_container_toggle = null;');
			<?php

				$instance_iobject->generer_fiche();
				$end_hour = microtime(true);	// Compute load cpu time to build ISheet
				//==================================================================
				// Keep focus on current tab during loading iSheet
				//==================================================================
				if(isset($_GET['tab-level']))
				{
					echo $instance_iobject->retourne_tab_level($_GET['tab-level']);
				}
				else
				{
					echo 'a_tabbar.setTabActive(\''.$instance_iobject->get_tab_actif_haut().'\');';
					echo 'head_tabbar.setTabActive(\''.$instance_iobject->get_tab_actif_entete().'\');';
					echo 'tabbar_step.setTabActive(\''.$instance_iobject->get_tab_actif_etapes().'\');';
					echo 'step_tabbar_sep.setTabActive(\''.$instance_iobject->get_tab_actif_etapes_sep().'\');';
				}
				//==================================================================
								
				// Clean cookies
				echo $instance_iobject->purge_cookie();

				$time_load = round($end_hour - $start_hour,3);
			?>
			
			document.getElementById('footer_debug_load').innerHTML = '<?php echo $time_load.' s / '.round(memory_get_peak_usage()/1000000,2); ?> Mo';
			
			var bloquer_pulse_tab_actif = false;
			
			//==================================================================
			// Build ZeroClipboard ( Clipboard )
			//==================================================================
            var clip = new ZeroClipboard.Client();
            clip.setText(url_remove('ssid'));
            clip.glue('d_clip_button');
			//==================================================================

			//==================================================================
			// Setup main static variables
			//==================================================================
            var ID_fiche = '<?php echo $_GET['ID']; ?>';
            var version_fiche = <?php echo $instance_iobject->get_version();	?>;
			var ID_temp = <?php echo $instance_iobject->get_id_temp(); ?>;
			<?php 
					
				switch ($ik_valmod) 
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
			//==================================================================

			//==================================================================
			// Div for ddrivetip window
			//==================================================================
			var enabletip=false;
			var offsetxpoint=-10; 	
			var offsetypoint=10; 	
			var ie=document.all;
			var ns6=document.getElementById && !document.all;
			if (ie||ns6)
				var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : "";
			
			var tipobj=document.getElementById("dhtmltooltip");
			
			document.onmousemove=positiontip;
			//==================================================================
		
			//==================================================================
			// Keyboard Shortcut
			//==================================================================
			var isCtrl = false;

			document.onkeyup = function(e)
			{
			    if(e.which == 17)
				{
				     isCtrl=false;
			    }
			}
			document.onkeydown = function(e){
				// Jump to iSheet step (F12)
			    if(e.which == 123) 
				{
			         rac_deplacer_sur_etape();
			    }
			    // Jump to tab HEAD -> MAIN (F11)
			    if(e.which == 122) 
				{
					 a_tabbar.setTabActive('tab-level1');
					 head_tabbar.setTabActive('tab-level1_1');
					 window.location  = '#'+id_etape_dest;
			         return false;
			    }
				// Key CTRL pressed ?
			    if(e.which == 17)
				{
				     isCtrl=true;
			    }
				// HELP - (CTRL+F1)
		        if(e.which == 112 && isCtrl == true)
			    {
		        	if((viewer_aide) && (viewer_aide!=''))
		        	{
		        		window.open("index.php?ID="+viewer_aide);
		        	} 
		        	else
			        {  // Default help page
		        		window.open("index.php?ID=");  		
		        	}    	
		        } 
			};	// End onkeydown	
			//==================================================================
			function event_click_onglet(idd)
			{

			}
			var qtt_step = <?php echo $instance_iobject->compter_etapes(); ?>;
			// Check cookies
			//ctrl_free_cookie('lib_erreur',true);
			//signal_presence();
			document.getElementById('lst_vimofy_version_fiche').value = "<?php echo $instance_iobject->get_version(); ?>";
			document.getElementById('ZeroClipboardMovie_1').onmouseover = function(){over(false,86,'-','X');document.getElementById('d_clip_button').style.backgroundPosition = '-175px -35px';}; 
		    document.getElementById('ZeroClipboardMovie_1').onmouseout =  function(){unset_text_help();document.getElementById('d_clip_button').style.backgroundPosition = '-175px 0';};
		    <?php 
				if($_SESSION[$ssid]['from_backup'] != false && $instance_iobject->get_global_coherent_check_end() != true)
				{
					echo "iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
					      ctrl_coherence_last_backup();
					      var ctrl_coherence_last_backup_timer = setInterval('check_global_coherence_end()',800);";
				}
			?>
			
			var footer = new iknow_footer('js/common/iknow/','includes/common/maj_presence.php',"ssid="+ssid+"&id="+ID_fiche+"&start_visu="+start_visu+"&id_temp="+ID_temp+"&type_action="+application,interval_presence);

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
							$footer_progress .= str_replace('$x',$qtt_err,$_SESSION[$ssid]['message']['iknow'][13]).' <a href="outils/coherent_check/detail.php?ssid='.$ssid.'&iobject=__ICODE__&id='.$_GET['ID'].'" target="_blank">Detail</a>';
						}
						$footer_progress .= '</div>';
					}
					else
					{
						$footer_progress = '';
					}
				}
			

			?>
			footer.add_element('<div style="height:22px;"><div id="iknow_ctrl_arrow" style="<?php echo $display_ctrl_bar; ?>" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(417);" onmouseout="ikdoc(\'\');unset_text_help();"></div></div>',__FOOTER_LEFT__);
			footer.add_element('<?php echo str_replace("'","\'",$footer_progress); ?>',__FOOTER_LEFT__);
			footer.add_element('<div class="iknow_lib_hover" id="iknow_header_ss_titre">&nbsp;</div>',__FOOTER_LEFT__);
			footer.add_element('<div class="lib_btn_menu_footer"><?php echo $_SESSION[$ssid]['configuration'][36]; ?></div><div class="btn_menu_footer" onMouseDown="return false;" onclick="iknow_toggle_el(\'iknow_menu_footer_container\',\'iknow_menu_footer_internal_container\');"></div>',__FOOTER_RIGHT__);
			footer.add_element('<div id="lib_gestion_date" onmouseover="ikdoc(335);set_text_help(385);" onmouseout="ikdoc(\'\');unset_text_help();"><?php echo $instance_iobject->get_statut_lib(); ?></div>',__FOOTER_RIGHT__);
			footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
			if(strstr(navigator.userAgent,'MSIE'))
			{
				footer.add_element('<div id="conseil_navigateur"><?php echo str_replace("'","\'",$_SESSION[$ssid]['message']['iknow'][16]); ?></div>',__FOOTER_RIGHT__);
			}
			footer.generate();
			if(strstr(navigator.userAgent,'MSIE'))
			{
				blink_error_msg_start('conseil_navigateur');
			}
			//type_gestion_date();
		</script>
		<?php
			$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_varout']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_tags']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_version_fiche']->vimofy_generate_js_body();
		?>
	</body>
</html>
<?php 
	if(isset($_GET['IKN_INT_TIR_PERF']) &&  $_GET['IKN_INT_TIR_PERF'] == 1)
	{
		$sql = "INSERT INTO `iknow`.`ikn_tir_perf` (`ID_tir`, `ID_iobjet`, `Type_iobjet`, `Version_iobjet`, `load_time`, `tir_date`)
				VALUES (NULL, '".$_GET['ID']."',2, '".$instance_iobject->get_version()."', '".round( microtime(true) - $start_hour,3)."', CURRENT_TIMESTAMP);";
		
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow'],$link) or die('dbconn: mysql_select_db: ' + mysql_error());
		mysql_query($sql,$link) or die('erreur');
	}