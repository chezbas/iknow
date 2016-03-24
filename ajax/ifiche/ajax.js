/*==========================================================================================================
 * 									Javacript ajax functions for iSheet in display and update mode
 ==========================================================================================================*/
var contenu_etape_tag;								// Store step content before diplaying tag's Lisha
var tag_edit_on = false;							// ???
var emplacement_vimofy_cartouche_param = false;		// Store current id element with the active Lisha
var emplacement_cartouche_param = false; 			// Store current id element with the active Lisha of input parameters
var emplacement_vimofy_cartouche_infos= false;		// Store current id element with the active Lisha
var emplacement_cartouche_infos = false;			// Store current id element with the active Lisha of information

/**==================================================================
 * Display step tag's lisha into current updating step
 * @param id_etape : Current step identifier
 * @param type_visu : 	true : inline display
 * 						false: step display
 * @param tag_sauvegarde : Internal use
 * @return
 ====================================================================*/		
function vimofy_tag_etape(id_etape,type_visu,tag_sauvegarde,vimofy_tags)
{
	if(tag_edit_on == true)return false;
	if(typeof(tag_sauvegarde) == "undefined")
	{
		// Hide float tags div
		hideddrivetip();
		
		// Display waiting message on screen
		generer_msgbox('',get_lib(183),'','wait');
		
		//==================================================================
		// Save step content : Cache mode
		//==================================================================
		if(type_visu)
		{
			contenu_etape_tag =  document.getElementById('mesetapes').innerHTML;
		}
		else
		{
			contenu_etape_tag =  document.getElementById(id_etape+'l').innerHTML;
		}
		//==================================================================
		
		//==================================================================
		// Prepare isheet
		//==================================================================
		// Action button of step
		if(application == 1)
		{
			// Button valid tags
			boutons = '<div class="valider" onclick="javascript:valider_modifications_tag('+id_etape+');" onmouseover="ikdoc(\'id_aide\');set_text_help(121);" onmouseout="ikdoc();unset_text_help();"></div>';
			
			// Button cancel tags modification
			boutons += '<div class="annuler" onclick="javascript:annuler_modifications_tag('+id_etape+');" onmouseover="ikdoc(\'id_aide\');set_text_help(135);" onmouseout="ikdoc();unset_text_help();"></div>';
		}
		else
		{
			// Button valid tags
			boutons = '<div class="valider" onclick="javascript:valider_modifications_tag('+id_etape+','+type_visu+');" onmouseover="ikdoc(\'id_aide\');set_text_help(204);" onmouseout="ikdoc();unset_text_help();"></div>';

		}
		
		masquer_boutons_etapes(id_etape,boutons,type_visu);
		//==================================================================
		
		if(application == 1)
		{
			// Edit mode
			//==================================================================
			// Save tags step before updating : Usefull if i want to cancel action
			//==================================================================
			var configuration = new Array();	
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			if(type_visu)
			{
				configuration['div_a_modifier'] = 'tdetape'+id_etape;
			}
			else
			{
				configuration['div_a_modifier'] = 'tdetape'+id_etape+'l';
			}
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 3000;		// 3 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "ssid="+ssid+"&id_etape="+id_etape+"&action=25";
			configuration['fonction_a_executer_reponse'] = 'vimofy_tag_etape';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+type_visu;
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
		
			ajax_call(configuration);
			//==================================================================
		}
		else
		{
			// Visu
			vimofy_tag_etape(id_etape,type_visu,true);
		}
	}
	else
	{
		if(typeof(vimofy_tags) == 'undefined')
		{
			//==================================================================
			// Recover lisha
			//==================================================================
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 3000;		// 3 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=9&id_etape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'vimofy_tag_etape';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+type_visu+','+true;
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';

			ajax_call(configuration);
			//==================================================================
		}
		else
		{
			var reponse_json = get_json(vimofy_tags); 
			if(type_visu)
			{
				document.getElementById('tdetape'+id_etape).innerHTML = decodeURIComponent(reponse_json.parent.vimofy);
			}
			else
			{
				document.getElementById('tdetape'+id_etape+'l').innerHTML = decodeURIComponent(reponse_json.parent.vimofy);
			}
			
			addCss(decodeURIComponent(reponse_json.parent.css));
			eval(decodeURIComponent(reponse_json.parent.json));
			
			end_load_ajax(true,true);
			window.location='#'+id_etape;
			tag_edit_on = true;
		}
	}
}
/**===================================================================*/

/**==================================================================
 * Accept update tags of step modification
 * @param id_etape : Current step identifier
 * @param type_visu : 	true : inline display
 * 						false: step display
 * @return
 ====================================================================*/		
function valider_modifications_tag(id_etape,type_visu,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		if(typeof(type_visu) == 'undefined')
		{
			type_visu = true;
		}
		if(application == 1)
		{
			var messsage = get_lib(184);
		}
		else
		{
			var messsage = get_lib(195);
		}

		// On affiche le message d'attente	
		generer_msgbox('',messsage,'','wait');
		
		if(application == 1)
		{
			// Edit mode
			//==================================================================
			// Ajax call to confirm modification on tags
			//==================================================================
			var configuration = new Array();	
						
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 15000;		// 15 secondes
			configuration['max_tentative'] = 5;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=6&ssid="+ssid+"&etape="+id_etape;
			configuration['fonction_a_executer_reponse'] = 'valider_modifications_tag';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+type_visu;
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';

			ajax_call(configuration);
			//==================================================================
		}
		else
		{
			// Display mode
			valider_modifications_tag(id_etape,type_visu,true);
		}
	}
	else
	{
		//==================================================================
		// Display step again
		//==================================================================
		if(type_visu)
		{
			document.getElementById('mesetapes').innerHTML = contenu_etape_tag;
		}
		else
		{
			document.getElementById(id_etape+'l').innerHTML = contenu_etape_tag;
		}
		tag_edit_on = false;
		contenu_etape_tag = null;
		//==================================================================
		
		afficher_boutons_etapes(id_etape,type_visu);

		if(application == 1)
		{
			// Transform XML return to JSON format
			try 
			{
				reponse_json = get_json(reponse); 
			} 
			catch(e)
			{
				alert(reponse);
				return false;
			}

			// Generate information window
			if(typeof(reponse_json.parent.nbr_tag) == 'number')
			{
				// Tags icon
				if(reponse_json.parent.nbr_tag == '0')
				{
					// No tag
					if(!strstr(navigator.userAgent,'MSIE'))
					{
						document.getElementById('a_tag_etape-'+id_etape).className = 'no_tag';
					}
				}
				else
				{
					// A least one tag 
					if(!strstr(navigator.userAgent,'MSIE'))
					{
						document.getElementById('a_tag_etape-'+id_etape).className = 'tag';
					}
				}
				
				try 
				{
					document.getElementById('a_tag_etape-'+id_etape).innerHTML = decodeURIComponent(reponse_json.parent.popup);
				} 
				catch(e)
				{
					// TODO: handle exception
					//alert('catch a_tag_etape-'+id_etape);
				}
			}
	
			// Display last action
			texte_action = get_lib(327).replace('$j', id_etape);
			iknow_panel_set_action(texte_action,'<td><a href="#'+id_etape+'" onclick="javascript:tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		}
		end_load_ajax();
		window.location='#'+id_etape;
	}
}	
/**===================================================================*/

/**==================================================================
 * ??? SRX
 * @param id_etape : Current step identifier
 * @param id_html : 
 * @param num_cartouche :
 * @param type_div :
 * @param ajax_return :
 * @return
 ====================================================================*/		
function afficher_vimofy_cartouche_param(id_etape,id_html,num_cartouche,type_div,ajax_return)
{
	if(emplacement_vimofy_cartouche_param == false || emplacement_vimofy_cartouche_param != 'vimofy_cartouche_param'+type_div+id_html)
	{
		if(emplacement_vimofy_cartouche_param != false)
		{
			// Delete vimofy
			document.getElementById(emplacement_vimofy_cartouche_param).innerHTML = '';
			document.getElementById(emplacement_vimofy_cartouche_param).style.display = 'none';
			document.getElementById(emplacement_cartouche_param).style.display = '';
		}

		if(document.getElementById('conteneur_param_entete_'+id_etape+'_'+num_cartouche).style.display == 'none')
		{
			// The cartridge is closed, open before load vimofy
			toggle_div('conteneur_param_entete_'+id_etape+'_'+num_cartouche);
		}
		
		// Display vimofy
		if(typeof(ajax_return) == 'undefined')
		{
			document.getElementById('vimofy_cartouche_param'+type_div+id_html).style.display = '';
			document.getElementById('info_lien'+type_div+id_etape+'_'+num_cartouche).style.display = 'none';
			
			/**==================================================================
			 * Récupération de la vimofy
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'vimofy_cartouche_param'+type_div+id_html;
			configuration['delai_tentative'] = 5000;		// 3 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=30&num_cartouche="+num_cartouche+"&id_etape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'afficher_vimofy_cartouche_param';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+id_html+','+num_cartouche+',\''+type_div+'\'';
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
		{
			emplacement_vimofy_cartouche_param = 'vimofy_cartouche_param'+type_div+id_html;
			emplacement_cartouche_param = 'info_lien'+type_div+id_etape+'_'+num_cartouche;
			
			var reponse_json = get_json(ajax_return); 
			document.getElementById('vimofy_cartouche_param'+type_div+id_html).innerHTML = decodeURIComponent(reponse_json.parent.vimofy);
			
			addCss(decodeURIComponent(reponse_json.parent.css));
			eval(decodeURIComponent(reponse_json.parent.json));
			document.getElementById('aff_vimofy_'+id_html).onmouseover = function onmouseover(event) { ikdoc('');set_text_help(308);};
			end_load_ajax(true);
		}
	}
	else
	{
		// Delete vimofy
		document.getElementById('vimofy_cartouche_param'+type_div+id_html).innerHTML = '';
		document.getElementById('vimofy_cartouche_param'+type_div+id_html).style.display = 'none';
		document.getElementById('info_lien'+type_div+id_etape+'_'+num_cartouche).style.display = '';
		document.getElementById('aff_vimofy_'+id_html).onmouseover = function onmouseover(event) { ikdoc('');set_text_help(309);};
		emplacement_vimofy_cartouche_param = false;
		emplacement_cartouche_param = false;
	}
}
/**===================================================================*/


function toggle_cart(id_etape,id_html,num_cartouche,type_div,ajax_return)
{
	if(emplacement_vimofy_cartouche_infos == false || emplacement_vimofy_cartouche_infos != 'vimofy_cartouche_infos'+type_div+id_html)
	{
		if(emplacement_vimofy_cartouche_infos != false)
		{
			// Suppression de la vimofy
			document.getElementById(emplacement_vimofy_cartouche_infos).innerHTML = '';
			document.getElementById(emplacement_vimofy_cartouche_infos).style.display = 'none';
			document.getElementById(emplacement_cartouche_infos).style.display = '';
		}
		
		if(document.getElementById('conteneur_infos'+type_div+id_etape+'_'+num_cartouche).style.display == 'none')
		{
			// The cartridge is closed, open before load vimofy
			toggle_div('conteneur_infos'+type_div+id_etape+'_'+num_cartouche);
		}
		
		if(typeof(ajax_return) == 'undefined')
		{
			document.getElementById('vimofy_cartouche_infos'+type_div+id_html).style.display = 'block';
			document.getElementById('info_lien_param_en_colonne'+type_div+id_etape+'_'+num_cartouche).style.display = 'none';
			
			/**==================================================================
			 * Récupération de la Vimofy
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'vimofy_cartouche_infos'+type_div+id_html;
			configuration['delai_tentative'] = 5000;		// 3 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=29&num_cartouche="+num_cartouche+"&id_etape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'toggle_cart';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+id_html+','+num_cartouche+',\''+type_div+'\'';
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
		{
			emplacement_vimofy_cartouche_infos = 'vimofy_cartouche_infos'+type_div+id_html;
			emplacement_cartouche_infos = 'info_lien_param_en_colonne'+type_div+id_etape+'_'+num_cartouche;
			
			var reponse_json = get_json(ajax_return); 
			document.getElementById('vimofy_cartouche_infos'+type_div+id_html).innerHTML = decodeURIComponent(reponse_json.parent.vimofy);
			
			addCss(decodeURIComponent(reponse_json.parent.css));
			eval(decodeURIComponent(reponse_json.parent.json));
			
			document.getElementById('aff_vimofy_'+id_html).onmouseover = function onmouseover(event) { ikdoc('');set_text_help(308);};
			end_load_ajax(true);
		}
	}
	else
	{
		// Suppression de la vimofy
		document.getElementById('vimofy_cartouche_infos'+type_div+id_html).innerHTML = '';
		document.getElementById('vimofy_cartouche_infos'+type_div+id_html).style.display = 'none';
		document.getElementById('info_lien_param_en_colonne'+type_div+id_etape+'_'+num_cartouche).style.display = '';
		document.getElementById('aff_vimofy_'+id_html).onmouseover = function onmouseover(event) { ikdoc('');set_text_help(309);};
		emplacement_vimofy_cartouche_infos = false;
		emplacement_cartouche_infos = false;
	}
}
/****************************************************************************************
 ****************************************************************************************
 ***																				  ***
 ***								ACTIONS FICHE									  ***
 ***																				  ***
 ****************************************************************************************
 ****************************************************************************************/	

/**
 * Met à jour la table des locks pour voir que l'utilisateur est tjr sur la fiche.
 * Récupère les message en base de données si il y en a.
 * @param reponse : interne, appelé lors du retour ajax de la verification
 * @return
 * NR_IKNOW_8_
 */
function signal_presence(reponse)
{
	if(typeof(reponse) == "undefined")
	{	
		/**==================================================================
		 * Mise à jour du champs last_update et récupération des messages en bdd
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "includes/common/maj_presence.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "ssid="+ssid+"&id="+ID_fiche+"&start_visu="+start_visu+"&id_temp="+ID_temp+"&type_action="+application;
		configuration['fonction_a_executer_reponse'] = 'signal_presence';
		   		
		ajax_call(configuration);
		/**==================================================================*/		
	}
	else
	{
		ctrl_free_cookie('lib_erreur');
		
		if(reponse != '')
		{
			// Transformation du retour XML en JSON
			try 
			{
				reponse_json = get_json(reponse); 
			} 
			catch(e) 
			{
				alert(reponse);
				return false;
			}
			
			// Génération du bandeau d'informations
			var erreur = false;
			try 
			{
				typeof(reponse_json.parent.erreur) == 'string';
			} 
			catch(e) 
			{
				// handle exception
				alert('Probleme de communication avec le serveur : \n\n'+e+'\n\n'+reponse);
				erreur = true;
			}
			
			if(!erreur)
			{
				if(typeof(reponse_json.parent.erreur) == 'string')
				{
			        aff_btn = new Array([get_lib(182)],["close_msgbox();"]);
			        generer_msgbox(reponse_json.parent.titre_controle,reponse_json.parent.message_controle,'erreur','msg',aff_btn);
				}
				else
				{
					if(typeof(reponse_json.parent.message) == 'string')
					{
				        aff_btn = new Array([get_lib(182)],["close_msgbox();valide_message_maintenance();"]);
				        generer_msgbox(decodeURIComponent(reponse_json.parent.titre_message),decodeURIComponent(reponse_json.parent.message),'erreur','msg',aff_btn);
					}
					else
					{
						compteur_end_visu(reponse_json.parent.date,reponse_json.parent.time);
					}
				}
			}
		}
	}
}

function valide_message_maintenance()
{
	/**==================================================================
	 * Validation du message de maintenance
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/ifiche/actions_fiche.php";
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=15&ssid="+ssid;
   		
	ajax_call(configuration);
	/**==================================================================*/	   
}

/**
 * Définition de l'onglet actif, appelé à chaque clic sur un onglet (voir dhtmlxtabbar.js)
 * @param tab_haut
 * @param tab_entete
 * @param tab_etapes
 * @param tabbar_etapes_sep
 * @return
 */
function set_tabbar_actif(tab_haut,tab_entete,tab_etapes,tabbar_etapes_sep)
{	
	if(bloquer_pulse_tab_actif == false)
	{
		/**==================================================================
		 * Définition de l'onglet actif
		 * ATTENTION LAISSER EN SYNCHRONE sinon le navigateur n'attend pas 
		 * le retour et recharge directement la page
		 ====================================================================*/		
		//fiche_sauvegardee = true;
		
		var xhr = new XMLHttpRequest();
		xhr.open("POST","ajax/ifiche/actions_fiche.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xhr.send("action=9&ssid="+ssid+"&tab_haut="+tab_haut+"&tab_entete="+tab_entete+"&tab_etapes="+tab_etapes+"&tab_etapes_sep="+tabbar_etapes_sep);
		/**==================================================================*/	
	}  
}

/**
 * Tue la session en cours, en supprimant de la base tout ce qui concerne notre id temporaire
 * Supprime également le cookie de session.
 */
function tuer_session()
{
	/**==================================================================
	 * Kill de la session 
	 * ATTENTION LAISSER EN SYNCHRONE sinon le navigateur n'attend pas 
	 * le retour et quitte la page lors d'un rechargement, et donc la fiche
	 * reste en modification.
	 ====================================================================*/		
	fiche_sauvegardee = true;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST","ajax/ifiche/actions_fiche.php",false);
	xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xhr.send("action=12&ssid="+ssid);
	/**==================================================================*/	  	
	
	/**==================================================================
	 * Suppression du cookie de session du navigateur
	 ====================================================================*/	
	delete_cookie(ssid);
	/**==================================================================*/	  
}