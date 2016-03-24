<?php
/**==================================================================
 * iSheet edit mode
 ====================================================================*/		

	$type_soft = 1;
	
	require('includes/common/init_edit.php');

	/**==================================================================
	* Generate object
	====================================================================*/
	$_SESSION[$ssid]['objet_fiche'] = new fiche($_GET['ID'],$ssid,$_SERVER['REMOTE_ADDR'],1,$_SESSION['iknow']['version_soft'],$_GET['version'],$_SESSION[$ssid]['langue']);
	/*===================================================================*/  

	/**==================================================================
	* Generate title
	====================================================================*/ 
	if($_GET['ID'] == 'new')
	{
		echo '<title>'.$_SESSION[$ssid]['message'][63].'</title>';
	}
	else
	{
		echo '<title>'.$_SESSION[$ssid]['message'][62].' '.$_GET['ID'].'</title>';
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
	require('includes/ifiche/vimofy/edit/init_liste_varin.php');
	require('includes/ifiche/vimofy/edit/init_liste_varout.php');
	require('includes/ifiche/vimofy/edit/init_liste_tags_local.php');
	require('includes/ifiche/vimofy/edit/init_liste_tags_ext.php');
	require("includes/ifiche/vimofy/edit/init_liste_poles_lmod_vim2_edit.php");
	require("includes/ifiche/vimofy/edit/init_liste_vers_poles_lmod_vim2_edit.php");
	require("includes/ifiche/vimofy/edit/init_liste_activite_lmod_vim2_edit.php");
	require("includes/ifiche/vimofy/edit/init_liste_module_lmod_vim2_edit.php");
	/*===================================================================*/    

	/*==================================================================
	* Vimofy internal init
	====================================================================*/  
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->generate_public_header();   
	$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_varout']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_tags']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy_tags_ext']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->vimofy_generate_header();
	$_SESSION['vimofy'][$ssid]['vimofy2_module_lmod']->vimofy_generate_header();
	/*===================================================================*/    
?>
<script type="text/javascript">
	// Définition des variables statiques globales
	var bloquer_pulse_tab_actif = true;		// si true empeche surcharge ajax des pulse ajax des onglets lors du click (uniquement lors du chargement de la fiche)	
	var application = 1;
	var version_soft = '<?php echo $_SESSION['iknow']['version_soft']; ?>';
	var ssid = '<?php echo $ssid; ?>';
</script>
<!--************************************************************************************************************
 *		GENERATION DE LA PARTIE STATIQUE COMPLETE DE L'ENTETE DE LA PAGE		
 *************************************************************************************************************-->					
<link rel="stylesheet" href="css/ifiche/modif_fiche.css" type="text/css">
<link rel="stylesheet" href="css/ifiche/common_fiche.css" type="text/css">
<link rel="stylesheet" href="css/common/style.css" type="text/css">
<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
<link rel="stylesheet" href="css/common/iknow/iknow_onglet.css" type="text/css">
<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">

<script type="text/javascript" src="js/common/iknow/iknow_onglet.js"></script>
<script type="text/javascript" src="js/common/cookie.js"></script>
<script type="text/javascript" src="js/common/tiny/tiny_mce.js"></script>
<script type="text/javascript" src="js/ifiche/init_tinymce.js"></script>		
<script type="text/javascript" src="js/ifiche/fonctions.js"></script>
<script type="text/javascript" src="js/ifiche/fonctions_modif.js"></script>	
<script type="text/javascript" src="js/common/informations.js"></script>
<script type="text/javascript" src="js/common/time.js"></script>
<script type="text/javascript" src="js/common/edit.js"></script>
<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
<script type="text/javascript" src="ajax/ifiche/ajax.js"></script>
<script type="text/javascript" src="ajax/ifiche/ajax_modif.js"></script>

<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
<script type="text/javascript" src="js/common/session_management.js"></script>
<!--**********************************************************************************************************-->
		
<script type="text/javascript">
	//************************************************************************************************************
	// PROTECTION DE LA FERMETURE DE LA FENETRE ACCIDENTELE PAR DEMANDE DE CONFIRMATION
	//************************************************************************************************************
	function exit_fenetre()
	{
		if(fiche_sauvegardee == false)
		{
			return '<?php echo str_replace('&id',$_GET['ID'],$_SESSION[$ssid]['message'][110]); ?>';
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
	//************************************************************************************************************

	//************************************************************************************************************
	// SIGNAL PRESENCE
	// NR_IKNOW_8_
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
</head>	
<body onmousemove="vimofy_move_cur(event);" onmouseup="vimofy_mouseup();" onmousedown="vimofy_mousedown(event);">
	<?php 
		require('includes/ifiche/cache.php');
		$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->generate_lmod_header();
		$_SESSION['vimofy'][$ssid]['vimofy2_module_lmod']->generate_lmod_header();
	?>
	<!-- Espace d'affichage de l'infobulle (ddrivetip)--><div id="dhtmltooltip"></div>
	<!-- =================================================  MSGBOX ================================================= -->	
	<div id="iknow_msgbox_background"></div>
	<div id="iknow_msgbox_conteneur" style="display:none;"></div>
	<!-- ===============================================  END MSGBOX ==============================================  -->
	<script type="text/javascript">
		// Définition des variables statiques globales	
		var ID_fiche = '<?php echo $_GET['ID']; ?>';
		var version_fiche = <?php echo $_SESSION[$ssid]['objet_fiche']->get_version(); ?>;	
		var ID_temp = <?php echo $_SESSION[$ssid]['objet_fiche']->get_id_temp(); ?>;
		var focus_modifie_par = false;
		
		// Variables pour l'uptime de la session
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
	</script>	
	
	<!-- ================================================== TOOLBAR ================================================= -->	
	<div class="header_ifiche_modif_toolbar">
		<div class="iknow_toolbar" id="barre_outils">
			<ul id="header_list_menu">
				<li><div class="boutton_save boutton_outils" id="toolbar_btn_backup" onclick="save_sheet(false);" onmouseover="over(false,78);"  onmouseout="unset_text_help();"></div></li>
				<li><div class="boutton_ctrl boutton_outils" id="toolbar_btn_ctrl" onclick="sheet_control(false);" onmouseover="over(false,415);"  onmouseout="unset_text_help();"></div></li>
				<?php 
					if($_GET['ID'] != 'new')
					{
						echo '<li><div class="boutton_copy boutton_outils" 		id="toolbar_btn_dupliq" onclick="dupliquer_fiche();" 																										 onmouseover="over(246,416,this);" onmouseout="unset_text_help();"></div></li>';
						echo '<li><div class="boutton_lock boutton_outils" 		id="toolbar_btn_lock" onclick="bloquer_fiche();" 		    																								 onmouseover="over(247,101,this);" onmouseout="unset_text_help();"></div></li>';
						echo '<li><div class="boutton_url_return boutton_outils"  id="toolbar_btn_cancel" onclick="cancel_modif(\'./ifiche.php?ID='.$_GET['ID'].'&amp;version='.$_SESSION[$ssid]['objet_fiche']->get_version().'\');" 		 onmouseover="over(248,135,this);" onmouseout="unset_text_help();"></div></li>';
					}
				?>
			</ul>
		</div>
		
		<?php 
			$txt_header = $_SESSION[$ssid]['message'][409].' '; 
			if($_GET['ID'] != 'new')
			{
				$txt_header .= '<span class="bold" id="id_fiche">'.$_GET['ID'].'</span>';
			}
			else
			{
				$txt_header .= '<span class="bold" id="id_fiche">'.$_SESSION[$ssid]['id_temp'].'</span>';
			} 
			echo generate_logo_header('<div style="height:30px;line-height:30px;">'.$txt_header.'</div><div style="height:30px;line-height:30px;">'.$_SESSION[$ssid]['message'][48].' <span class="bold" id="version_fiche">'.$_SESSION[$ssid]['objet_fiche']->get_version().'</span></div>');
		?>
		<div id="ifiche_title" class="header_title header_title_visu">
			<?php echo $_SESSION[$ssid]['objet_fiche']->get_titre_sans_bbcode(); ?>
		</div>
	</div>
	<?php 
		
			if($_SESSION[$ssid]['configuration'][42] != '')
			{
				echo '<div style="background:url(images/env/'.$_SESSION[$ssid]['configuration'][42].'.png) repeat-x;height:65px;position:absolute;top:0;left:0;right:0;"></div>';
			}
		
	?>
	<!-- ============================================= END TOOLBAR ================================================= -->
	
	
	
	<!-- BEGIN Onglets -->
		<?php require('includes/common/onglets.php'); ?>	
	<!-- END Onglets -->
	
	<script type="text/javascript">
		<?php 
		
		
			$_SESSION[$ssid]['objet_fiche']->generer_fiche();
			
			
			$_SESSION[$ssid]['reload'] = true;
		
			/************************************************************************************************************
			 *	PURGE DES COOKIES NR_IKNOW_10_
			 *************************************************************************************************************/	
			echo $_SESSION[$ssid]['objet_fiche']->purge_cookie();
			/************************************************************************************************************/
			
		?>
		
		//************************************************************************************************************
		// RE-AFFICHAGE DES TINY (Lors d'un clic sur un onglet)
		//************************************************************************************************************				
		function event_click_onglet(idd)
		{
			switch (idd) 
			{
				case 'tab-level1':
						vimofy_refresh('vimofy2_varin');
						vimofy_refresh('vimofy_varout');
						vimofy_refresh('vimofy_tags');
						vimofy_refresh('vimofy_tags_ext');
					break;
				default:
					break;	
			}
		}
		
		//************************************************************************************************************				
		
		//************************************************************************************************************
		// DEFINITION DE L'ONGLET SELECTIONNE PAR DEFAUT
		//************************************************************************************************************	
		a_tabbar.setTabActive('tab-level1');
		head_tabbar.setTabActive('tab-level1_1');
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
				</table>
			</div>
			<table summary="" id="div_menu_footer_icones">
				<tr>
					<!-- Liste des raccourcis clavier --><td><div class="shortcut pointer" onclick="javascript:lst_rac();" onmouseover="ikdoc();set_text_help(160,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
					<!--        Liste des bugs        --><td><div class="bug pointer" onclick="javascript:window.open('bugs');" onmouseover="ikdoc();set_text_help(161,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
					<!--         Portail iKnow        --><td><div class="maison pointer" onclick="javascript:window.open('.');" onmouseover="ikdoc();set_text_help(352,null,true);" onmouseout="ikdoc('');unset_text_help();"></div></td>
					<!--       Liste des fiches       --><td><div class="liste pointer" onclick="javascript:window.open('liste_fiches.php');" onmouseover="ikdoc();set_text_help(408);" onmouseout="ikdoc('');unset_text_help();"></div></td>
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
		<div id="iknow_ctrl_internal_container" class="iknow_el_internal_container iknow_ctrl_internal_container"></div>
	</div>
	<!-- ============================================== END BARRE INFORMATIONS ===================================== -->
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
		// FENETRE VOLANTE DDRIVETIP
		//************************************************************************************************************			
		var enabletip=false;
		var offsetxpoint=-10; //Customize x offset of tooltip
		var offsetypoint=10; //Customize y offset of tooltip
		var ie=document.all;
		var ns6=document.getElementById && !document.all;
		if (ie||ns6)
			var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : "";
		
		var tipobj=document.getElementById("dhtmltooltip");
		
		document.onmousemove=positiontip;
		//************************************************************************************************************	
		
		//************************************************************************************************************
		// RACCOURCI CLAVIER DE RECHERCHE D'UNE ETAPE
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
			e = e || event; 		// For IE only
			// DEPLACEMENT VERS UNE ETAPE (F12)
		    if(e.keyCode == 123)
			{
				rac_deplacer_sur_etape();
		    }
		    
		    // Affichage de l'historique des actions
			if(e.keyCode == 122)
			{
				iknow_toggle_histo();
		         return false;
		 	}

			if(e.keyCode == 120)
			{
				 sheet_control(false);
		         return false;
		 	}
			    
		    // REDUCTION DE LA BARRE D'INFORMATIONS (ECHAP)
		    if(e.keyCode == 27) 
			{
		    	iknow_toggle_control();
		    	return false;
		    }	
	        
			//Detection du CTRL
		    if(e.keyCode == 17)
			{
			     isCtrl = true;
		    }
		    else
		    {
			    if(focus_modifie_par == false)
			    {
		    		isCtrl = false;
			    }
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
	        		window.open("index.php");
	        	}   	
	        }    		
		};	// End onkeydown	
		//************************************************************************************************************	
		var bloquer_pulse_tab_actif = false;
					
	</script>
	<div id="conteneur_tiny_edition"><textarea name="edit_etape" class="edit_etape" id="edit_etape" style="width:100%;height:100%;position:absolute;display:none;" cols=5 rows=5></textarea></div>	
	<script type="text/javascript">

		// Message d'edition de la fiche
		texte_action = decodeURIComponent(libelle[364]).replace('$i',ID_fiche);
		iknow_panel_set_action(texte_action);
		if(ID_fiche == 'new')
		{
			// Do not display dependant Vimofy
			display_vim = false;
		}
		else
		{
			// Display dependant Vimofy
			display_vim = true;
		}

		document.getElementById('lst_vimofy2_pole_lmod').value = "<?php echo $_SESSION[$ssid]['objet_fiche']->get_id_pole(); ?>";
		load_vimofy_versions_poles('vimofy2_vers_pole_lmod','<?php echo $_SESSION[$ssid]['objet_fiche']->get_pole_version(); ?>',false,display_vim);
		load_vimofy_activites('vimofy2_activite_lmod','<?php echo $_SESSION[$ssid]['objet_fiche']->get_id_activite(); ?>',false,display_vim);
		load_vimofy_modules('vimofy2_module_lmod','<?php echo $_SESSION[$ssid]['objet_fiche']->get_module(); ?>',false,display_vim);

		// Call first signal presence.
		//signal_presence();
		
		var footer = new iknow_footer('js/common/iknow/','includes/common/maj_presence.php',"ssid="+ssid+"&id="+ID_fiche+"&start_visu="+start_visu+"&id_temp="+ID_temp+"&type_action="+application,interval_presence);
		footer.add_element('<div id="iknow_ctrl_arrow" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(417);" onmouseout="ikdoc(\'\');unset_text_help();"></div><div id="iknow_ctrl_title" onclick="iknow_toggle_control();" onmouseover="ikdoc();set_text_help(417);" onmouseout="ikdoc(\'\');unset_text_help();"></div>',__FOOTER_LEFT__);
		footer.add_element('<div class="iknow_lib_hover" id="iknow_header_ss_titre"></div>',__FOOTER_LEFT__);
		

		footer.add_element('<div class="lib_btn_menu_footer"><?php echo $_SESSION[$ssid]['configuration'][36]; ?></div><div class="btn_menu_footer" onMouseDown="return false;" onclick="iknow_toggle_el(\'iknow_menu_footer_container\',\'iknow_menu_footer_internal_container\');"></div>',__FOOTER_RIGHT__);
		footer.add_element('<div id="lib_gestion_date" onmouseover="ikdoc(335);set_text_help(385);" onmouseout="ikdoc(\'\');unset_text_help();"></div>',__FOOTER_RIGHT__);
		footer.add_element('<div style="cursor:pointer;" onclick="iknow_toggle_histo();" onmouseover="ikdoc();set_text_help(423);" onmouseout="ikdoc(\'\');unset_text_help();"><div class="icon_log" style="width:16px;height:16px;float:left;background-image:url(../../images/log.png);"></div><div id="iknow_log_btn"></div></div>',__FOOTER_RIGHT__);
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

		type_gestion_date();

		//************************************************************************************************************
		// VERIFICATION DE LA MODIFICATION DE LA DERNIERE VERSION DE LA FICHE
		//************************************************************************************************************	
		if(ID_fiche != 'new')
		{
			sheet_control(true);
		}
		//************************************************************************************************************	
	</script>
		<?php
			$_SESSION['vimofy'][$ssid]['vimofy2_varin']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_varout']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_tags']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy_tags_ext']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy2_pole_lmod']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy2_vers_pole_lmod']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy2_activite_lmod']->vimofy_generate_js_body();
			$_SESSION['vimofy'][$ssid]['vimofy2_module_lmod']->vimofy_generate_js_body();
		?>
	</body>
</html>