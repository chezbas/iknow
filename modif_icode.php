<?php
	/**==================================================================
	 * Code edit
	 ====================================================================*/	
	$type_soft = 3; 	
	
	require('includes/common/init_edit.php');
			
	/**==================================================================
	* Generate object
	====================================================================*/ 	
	$_SESSION[$ssid]['objet_icode'] = new icode($_GET['ID'],$ssid,$_SERVER['REMOTE_ADDR'],3,$_SESSION['iknow']['version_soft'],$_GET['version'],$_SESSION[$ssid]['langue']);
	/*===================================================================*/   
	
	/**==================================================================
	* Generate title
	====================================================================*/ 	
	if($_GET['ID'] == 'new')
	{
		echo '<title>'.$_SESSION[$ssid]['message'][210].'</title>';					
	}
	else
	{
		echo '<title>'.$_SESSION[$ssid]['message'][211].' '.$_GET['ID'].'</title>';
	}
	/*===================================================================*/   	
	
	/**==================================================================
	* Vimofy include
	====================================================================*/   
	$dir_obj = 'vimofy/';  
	require($dir_obj.'vimofy_includes.php');
	/*===================================================================*/   
	
	/**==================================================================
	* Vimofy init
	====================================================================*/     
	require("includes/icode/vimofy/edit/vim_init_varin_ed.php");
	require("includes/icode/vimofy/edit/vim_init_varout_ed.php");	
	require("includes/icode/vimofy/edit/vim_init_tag_ed.php");	
	require("includes/icode/vimofy/edit/init_liste_poles_lmod_vim2_edit.php");
	require("includes/icode/vimofy/edit/init_liste_vers_poles_lmod_vim2_edit.php");
	require("includes/icode/vimofy/edit/init_liste_activite_lmod_vim2_edit.php");
	require("includes/icode/vimofy/edit/init_liste_moteur.php");
	require("includes/icode/vimofy/edit/init_liste_vers_moteur.php");
	/*===================================================================*/    

	/*==================================================================
	* Vimofy internal init
	====================================================================*/     
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->generate_public_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_varout']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_tags']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_moteur']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_vers_moteur']->vimofy_generate_header();
	/*===================================================================*/     
?>

<!--************************************************************************************************************
 *		GENERATION DE LA PARTIE STATIQUE COMPLETE DE L'ENTETE DE LA PAGE		
 *************************************************************************************************************-->			
<link rel="stylesheet" href="css/icode/modif_code.css" type="text/css">
<link rel="stylesheet" href="css/common/style.css" type="text/css">
<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
<link rel="stylesheet" href="css/common/iknow/iknow_onglet.css" type="text/css">
<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">

<script type="text/javascript" src="js/common/iknow/iknow_onglet.js"></script>
<script type="text/javascript" src="js/common/cookie.js"></script>
<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
<script type="text/javascript" src="js/common/informations.js"></script>	
<script type="text/javascript" src="js/common/tiny/tiny_mce.js"></script>
<script type="text/javascript" src="js/common/time.js"></script>
<script type="text/javascript" src="js/common/edit.js"></script>			
<script type="text/javascript" src="js/icode/init_tinymce.js"></script>
<script type="text/javascript" src="js/icode/fonctions.js"></script>
<script type="text/javascript" src="ajax/icode/ajax.js"></script>
<script type="text/javascript" src="ajax/icode/ajax_modif.js"></script>

<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
<script type="text/javascript" src="js/common/session_management.js"></script>
<!--**********************************************************************************************************-->
		
<script type="text/javascript">
	var bloquer_pulse_tab_actif = true;															// si true empeche surcharge ajax
	var application = 3;							// Type d'application
	var version_soft = '<?php echo $_SESSION['iknow']['version_soft']; ?>';						// Version de l'application
	var ssid = '<?php echo $ssid; ?>';															// Identifiant de session de l'objet
	initmce_onglet_general();																	// Initialisation de la tiny MCE
	//************************************************************************************************************
	// PROTECTION DE LA FERMETURE DE LA FENETRE ACCIDENTELE PAR DEMANDE DE CONFIRMATION
	//************************************************************************************************************
	function exit_fenetre()
	{
		if(icode_sauvegarde == false)
		{
			return '<?php echo $_SESSION[$ssid]['message'][32];?>';
		}
	}
	
	//Action lors de la fermeture de la fenêtre
	window.onbeforeunload = function(e)
	{
		return exit_fenetre();
	};
	
	window.onunload = function(e)
	{
		tuer_session();
	};
	/*===================================================================*/			
	
	/**==================================================================
	* Signal presence
	====================================================================*/  
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
	/*===================================================================*/			
</script>		
</head>
<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup(event);" onmousedown="vimofy_mousedown(event);">
	<?php 
		require('includes/icode/cache.php');
		$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy_moteur']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy_vers_moteur']->generate_lmod_header();
	?>
	<!-- ================================================= MSGBOX ================================================== -->	
	<div id="iknow_msgbox_background"></div>
	<div id="iknow_msgbox_conteneur" style="display:none;"></div>
	<!-- ===============================================  END MSGBOX =============================================== -->
	
	<!-- ================================================= TOOLBAR ================================================= -->
	<div class="header_icode_edit_toolbar">
		<div class="iknow_toolbar">
			<ul id="header_list_menu">
				<li><div class="boutton_outils boutton_save" 		id="toolbar_btn_backup" onclick="controler_icode(true,false);" onmouseover="over(false,5,'','');"  onmouseout="unset_text_help();"></div></li>
				<li><div class="boutton_ctrl boutton_outils" 		id="toolbar_btn_ctrl" onclick="controler_icode(false,false);" onmouseover="over(false,7,'','');"  onmouseout="unset_text_help();"></div></li>
				<?php 
					if($_GET['ID'] != 'new')
					{
						echo '<li><div class="boutton_copy boutton_outils" 		  id="toolbar_btn_dupliq" onclick="dupliquer_icode();" 																										 onmouseover="over(160,99,this);" onmouseout="unset_text_help();"></div></li>';
						echo '<li><div class="boutton_lock boutton_outils" 		  id="toolbar_btn_lock" onclick="bloquer_icode();" 		    																								 onmouseover="over(247,71,this);" onmouseout="unset_text_help();"></div></li>';
						echo '<li><div class="boutton_url_return boutton_outils"  id="toolbar_btn_cancel" onclick="cancel_modif(\'./icode.php?&amp;ID='.$_GET['ID'].'&amp;version='.$_SESSION[$ssid]['objet_icode']->get_version().'\');" 			 onmouseover="over(247,58,this);" onmouseout="unset_text_help();"></div></li>';
					}
				?>
			</ul>
		</div>

		<?php 
			$txt_header = $_SESSION[$ssid]['message'][4].' '; 
			if($_GET['ID'] != 'new')
			{
				$txt_header .= '<span class="bold" id="id_code">'.$_GET['ID'].'</span>';
			}
			else
			{
				$txt_header .= '<span class="bold" id="id_code">'.$_SESSION[$ssid]['id_temp'].'</span>';
			} 
			echo generate_logo_header('<div style="height:30px;line-height:30px;">'.$txt_header.'</div><div style="height:30px;line-height:30px;">'.$_SESSION[$ssid]['message'][62].' <span class="bold" id="version_code">'.$_SESSION[$ssid]['objet_icode']->get_version().'</span></div>');
		?>
		
		<div class="header_title">
			<div id="icode_title" style="height:45px;line-height:45px;overflow:hidden;">
				<?php 
					echo $_SESSION[$ssid]['objet_icode']->get_titre_sans_bbcode();
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
	
	<!-- ================================================= ONGLETS ================================================= -->	
		<?php require('includes/common/onglets.php'); ?>
	<!-- =============================================== END ONGLETS =============================================== -->
	
	<script type="text/javascript">
		<?php 
			
			echo $_SESSION[$ssid]['objet_icode']->generer_icode();

			$end_hour = microtime(true);
		
			/************************************************************************************************************
			 *	PURGE DES COOKIES NR_IKNOW_10_
			 *************************************************************************************************************/	
			echo $_SESSION[$ssid]['objet_icode']->purge_cookie();
			/************************************************************************************************************/
		?>

		<?php
			/************************************************************************************************************
			 *	CONSERVER L'ONGLET COURANT ACTIF AU CHARGEMENT
			 *************************************************************************************************************/	
			if(isset($_GET['tab-level']))
			{
				echo $_SESSION[$ssid]['objet_icode']->retourne_tab_level($_GET['tab-level']);
			}
			else
			{
				echo "a_tabbar.setTabActive('".$_SESSION[$ssid]['objet_icode']->get_tab_actif()."');";
			}
			/************************************************************************************************************/
		?>
		
		//************************************************************************************************************
		// Définition des variables statiques globales	
		//************************************************************************************************************	
		var bloquer_pulse_tab_actif = false;
		var ID_code = '<?php echo $_GET['ID']; ?>';
		var version_code = <?php echo $_SESSION[$ssid]['objet_icode']->get_version();?>;
		var ID_temp = <?php echo $_SESSION[$ssid]['id_temp'];?>;
		var maj_vimofy_param = false;
		
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
		//************************************************************************************************************	
	</script>
						
	<!-- ================================================= BEGIN FOOTER ============================================ -->
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
					<!--       Liste des codes        --><td><div class="liste pointer" onclick="javascript:window.open('liste_codes.php');" onmouseover="ikdoc();set_text_help(75,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ================================================= BARRE LOGS ============================================== -->
	<div id="iknow_log_container" class="iknow_el_container iknow_ctrl_el">
		<div id="iknow_log_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container"></div>
	</div>
	<!-- ============================================== END BARRE LOGS ============================================= -->
	
	<!-- ================================================= BARRE INFORMATIONS ====================================== -->
	<div id="iknow_ctrl_container" class="iknow_el_container iknow_ctrl_el" style="float: left;">
		<div id="iknow_ctrl_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container">
		</div>
	</div>
	<!-- ============================================== END BARRE INFORMATIONS ===================================== -->
	<?php  /*
	<div id="footer">
		<div class="footer_left">
			<div id="iknow_ctrl_arrow" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(90);" onmouseout="ikdoc('');unset_text_help();"></div>
			<div id="iknow_ctrl_title" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(90);" onmouseout="ikdoc('');unset_text_help();"></div>
			<div id="iknow_var_format"><?php echo $_SESSION[$ssid]['objet_icode']->get_format_var(); ?></div>
			<div class="foot_sep_white"></div>
			<div class="iknow_lib_hover" id="iknow_header_ss_titre"></div>
		</div>
		<div class="footer_right">
			<div class="btn_menu_footer" onMouseDown="return false;" onclick="iknow_toggle_el('iknow_menu_footer_container','iknow_menu_footer_internal_container');"></div>
			<div class="lib_btn_menu_footer"><?php echo $_SESSION[$ssid]['configuration'][36]; ?></div>
			<div id="lifetime" onmouseover="ikdoc();set_text_help(null,decodeURIComponent(libelle_common[384]).replace('$t',this.innerHTML));" onmouseout="ikdoc('');unset_text_help();">-- : --</div>
			<!--<div style="cursor:pointer;float:right;" class="aide_footer" onclick="window.open('.?ID=334');"></div>-->
			<div class="foot_sep_grey"></div>
			<div id="lib_erreur" style="float:right;"></div>
			<div class="foot_sep_grey"></div>
			<div id="iknow_log_btn" onclick="iknow_toggle_histo();" onmouseover="ikdoc();set_text_help(91);" onmouseout="ikdoc('');unset_text_help();"></div>
		</div>
	</div>
	*/
	?>
	<div id="footer"></div>
	<!-- ================================================= END FOOTER ============================================== -->

	
	<script type="text/javascript">
		eval('g_timer_iknow_ctrl_container_toggle = null;');
		eval('g_height_iknow_ctrl_internal_container = null;');
		eval('g_height_iknow_ctrl_info_max = null;');
		eval('g_iknow_ctrl_info_timer_increment = null;');
		
		eval('g_timer_iknow_menu_footer_container_toggle = null;');
		eval('g_height_iknow_menu_footer_internal_container = null;');
		eval('g_height_iknow_menu_footer_max = null;');
		eval('g_iknow_menu_footer_timer_increment = null;');

		eval('g_timer_iknow_log_container_toggle = null;');
		eval('g_height_iknow_log_internal_container = null;');
		eval('g_height_iknow_log_info_max = null;');
		eval('g_iknow_log_info_timer_increment = null;');			
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
		
		document.onkeydown=function(e)
		{
			e = e || event; // For IE only

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

		    // Affichage de l'historique des actions
			if(e.keyCode == 122)
			{
				 iknow_toggle_histo();
		         return false;
		 	}

			if(e.keyCode == 120)
			{
				controler_icode(false,false);
		        return false;
		 	}
		 	
		    // fleche du bas
	        if(e.keyCode == 40 && isCtrl == true) 
		    {

	        	a_tabbar.previous();
	        }
		    // fleche du haut
	        if(e.keyCode == 38 && isCtrl == true) 
		    {
	        	a_tabbar.next();
	        }

			// AIDE - CTRL+F1
	        if(e.keyCode == 112 && isCtrl == true)
		    {
	        	if((viewer_aide) && (viewer_aide!=''))
	        	{
	        		window.open("index.php?ID="+viewer_aide);
	        	} 
	        	else
		        {  // page d'aide par défaut
	        		window.open("index.php?ID="); 
	        	}    	
	        }      
		};	// End onkeydown
		
	    document.onkeyup=function(e) 
	    {
	    	e = e || event; 		// Sets the event variable in IE
	        if(e.keyCode == 17) isCtrl=false;
	    };
		//************************************************************************************************************		
		
		if(ID_code == 'new')
		{
			// Do not display dependant Vimofy
			display_vim = false;
		}
		else
		{
			// Display dependant Vimofy
			display_vim = true;
		}

		// Message d'edition de la fiche
		texte_action = decodeURIComponent(libelle[100]).replace('$i',ID_code);
		iknow_panel_set_action(texte_action);
		
		document.getElementById('lst_vimofy2_pole_lmod').value = "<?php echo $_SESSION[$ssid]['objet_icode']->get_id_pole(); ?>";
		document.getElementById('lst_vimofy_moteur').value = "<?php echo $_SESSION[$ssid]['objet_icode']->get_engine(); ?>";
		load_vimofy_versions_poles('vimofy2_vers_pole_lmod','<?php echo $_SESSION[$ssid]['objet_icode']->get_pole_version(); ?>',false,display_vim);
		load_vimofy_activites('vimofy2_activite_lmod','<?php echo $_SESSION[$ssid]['objet_icode']->get_id_activite(); ?>',false,display_vim);
		<?php 
			if(isset($_GET['typec']))
			{
				?>
					load_vimofy_engine_version('vimofy_vers_moteur','<?php echo $_SESSION[$ssid]['objet_icode']->get_engine_version(); ?>',false,true);
				<?php 
			}
			else
			{
				?>
					load_vimofy_engine_version('vimofy_vers_moteur','<?php echo $_SESSION[$ssid]['objet_icode']->get_engine_version(); ?>',false,display_vim);
				<?php 
			}
		?>
		//signal_presence();

		var footer = new iknow_footer('js/common/iknow/','includes/common/maj_presence.php',"ssid="+ssid+"&id="+ID_code+"&id_temp="+ID_temp+"&type_action="+application,interval_presence);
		footer.add_element('<div id="iknow_ctrl_arrow" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(90);" onmouseout="ikdoc(\'\');unset_text_help();"></div><div id="iknow_ctrl_title" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(90);" onmouseout="ikdoc(\'\');unset_text_help();"></div>',__FOOTER_LEFT__);
		footer.add_element('<div id="iknow_var_format" onmouseover="over(118,52);" onmouseout="unset_text_help();"><?php echo $_SESSION[$ssid]['objet_icode']->get_format_var(); ?></div>',__FOOTER_LEFT__);
		footer.add_element('<div class="iknow_lib_hover" id="iknow_header_ss_titre">&nbsp;</div>',__FOOTER_LEFT__);
		footer.add_element('<div class="lib_btn_menu_footer"><?php echo $_SESSION[$ssid]['configuration'][36]; ?></div><div class="btn_menu_footer" onMouseDown="return false;" onclick="iknow_toggle_el(\'iknow_menu_footer_container\',\'iknow_menu_footer_internal_container\');"></div>',__FOOTER_RIGHT__);
		footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
		footer.add_element('<div id="iknow_log_btn" onclick="iknow_toggle_histo();" onmouseover="ikdoc();set_text_help(91);" onmouseout="ikdoc(\'\');unset_text_help();"></div>',__FOOTER_RIGHT__);
		if(strstr(navigator.userAgent,'MSIE'))
		{
			footer.add_element('<div id="conseil_navigateur"><?php echo str_replace("'","\'",$_SESSION[$ssid]['message']['iknow'][16]); ?></div>',__FOOTER_RIGHT__);
		}
		footer.generate();
		if(strstr(navigator.userAgent,'MSIE'))
		{
			blink_error_msg_start('conseil_navigateur');
		}

		<?php 
		
		
			/************************************************************************************************************
			 *  VERIFICATION DE LA MODIFICATION DE LA DERNIERE VERSION DE LA FICHE
			*************************************************************************************************************/	
			if($_GET['ID'] != 'new')
			{
				echo 'verification_lancement();';
			}
			/************************************************************************************************************/	
		?>
		
	</script>	
	<?php
	
		$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy2_varout']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy2_tags']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy_moteur']->vimofy_generate_js_body();
		$_SESSION['vimofy'][$ssid]['vimofy_vers_moteur']->vimofy_generate_js_body();
	?>	
</body>
</html>