/*==========================================================================================================
 * 									Javacript functions for iSheet updating
 ==========================================================================================================*/

//==================================================================
// Define global constants
//==================================================================
var fiche_sauvegardee = false;
//==================================================================

/*==================================================================
 * Modify step content by ajax
 * @param id_etape : Number of step to update
 ====================================================================*/
function editer_etape(id_etape)
{
	if(document.getElementById('conteneur_tiny_edition').innerHTML != '')
	{
		cancel_vimofy_lien();
		
		// Close information bar
		iknow_panel_reduire();
		
		// Hide help text
		unset_text_help();
		
		// Display wait message
		generer_msgbox('',get_lib(186),'','wait');
		
		//==================================================================
		// Preapre iSheet
		//==================================================================

		// Button step save
		boutons = '<div class="valider" onclick="javascript:save_step('+id_etape+');" onmouseover="ikdoc(\'id_aide\');set_text_help(121);" onmouseout="ikdoc();unset_text_help();"></div>';
		
		// Button cancel edit
		boutons += '<div class="annuler" onclick="javascript:annuler_edition_etape('+id_etape+');" onmouseover="ikdoc(\'id_aide\');set_text_help(122);" onmouseout="ikdoc();unset_text_help();"></div>';
		
		// Hide step action button
		masquer_boutons_etapes(id_etape,boutons);
		//==================================================================
		
		//==================================================================
		// Prepare TinyMCE
		//==================================================================

		// Récupération de la textarea d'édition d'étape
		var tiny_edition_etape = document.getElementById('conteneur_tiny_edition').innerHTML;
		
		// Suppression de la textarea d'édition d'étape de son conteneur d'origine
		document.getElementById('conteneur_tiny_edition').innerHTML = '';
		
		// Mise en place de la textarea d'édition d'étape dans l'étape en cours d'édition
		document.getElementById('tdetape'+id_etape).innerHTML = tiny_edition_etape;
		//==================================================================
		
		//==================================================================
		// Recover step content to update
		//==================================================================
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 5000;
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = 'ssid='+ssid+'&action=27&id_etape='+id_etape;
		configuration['fonction_a_executer_reponse'] = 'set_content_tiny_step';
		configuration['param_fonction_a_executer_reponse'] = id_etape;
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
		
		ajax_call(configuration);
		//==================================================================

		var texte_action = get_lib(318).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
		iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:editer_etape('+id_etape+');a_tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
	}
	else
	{
		ctrl_utilisation_tiny(id_etape);
	}
}
/*===================================================================*/


/*==================================================================
 * Set step tiny content 
 * p_id_etape : Number of step to update
 * p_content : Content of step
 ====================================================================*/
function set_content_tiny_step(p_id_etape,p_content)
{
	// Hide to user the textarea
	document.getElementById('edit_etape').style.visibility = 'hidden';

	// Display the textarea for browser
	document.getElementById('edit_etape').style.display = '';

	// Set the content of the step into the tiny
	document.getElementById('edit_etape').value = p_content;

	// Init the TinyMCE
	initmce_step(p_id_etape);

	// Test if the tinyMCE is available (in 10 ms)
	setTimeout(function(){ctrl_dispo_tiny(p_id_etape,p_content);},10);
}
/*===================================================================*/


/*==================================================================
 * Set main descrition ( head ) tiny content
 * p_content : Content of description
 ====================================================================*/
function set_content_tiny_description(p_content)
{
	// Hide to user the textarea
	document.getElementById('edit_etape').style.visibility = 'hidden';
	
	// Display the textarea for browser
	document.getElementById('edit_etape').style.display = '';

	// Set the content of the step into the tiny
	document.getElementById('edit_etape').value = p_content;
	
	// Init the TinyMCE
	initmce_description();
	
	// Test if the tinyMCe is available (in 10 ms)
	setTimeout(function(){ctrl_dispo_tiny(0,p_content);},10);
}
/*===================================================================*/


/*==================================================================
 * Set main prerequisite ( head ) tiny content
 * p_content : Content of prerequisite
 ====================================================================*/
function set_content_tiny_prerequis(p_content)
{
	// Hide to user the textarea
	document.getElementById('edit_etape').style.visibility = 'hidden';
	
	// Display the textarea for browser
	document.getElementById('edit_etape').style.display = '';

	// Set the content of the step into the tiny
	document.getElementById('edit_etape').value = p_content;
	
	// Init the TinyMCE
	initmce_prerequis();
	
	// Test if the tinyMCe is available (in 10 ms)
	setTimeout(function(){ctrl_dispo_tiny(0,p_content);},10);
}
/*===================================================================*/


/*==================================================================
 * Check if TinyMCE object is available
 * p_id_etape : Number of step to update ( tips : For description and prerequisite, p_id_etape = 0 )
 * p_content : Content of step
 ====================================================================*/
function ctrl_dispo_tiny(p_id_etape,p_content)
{
	try 
	{
		// Focus into tinyMCE
		tinyMCE.execCommand('mceFocus', true, 'edit_etape');
		
		// Go to active step
		window.location='#'+p_id_etape;
		
		// Display the tiny
		document.getElementById('edit_etape').style.visibility = 'visible';
		
		// Hide wait window
		close_msgbox();	
	} 
	catch(e)
	{
		setTimeout(function(){ctrl_dispo_tiny(p_id_etape,p_content);},10);
	}
}
/*===================================================================*/


/*==================================================================
 * Save step content
 * id_etape : Number of step to save
 * type_retour : 
 * reponse :
 ====================================================================*/
function save_step(id_etape,type_retour,reponse) 
{
	if(typeof(type_retour) == 'undefined')
	{
		// Display waiting message
		generer_msgbox('',get_lib(184),'','wait');
		
		//==================================================================
		// Recover content of TinyMCE
		// Protect special digit & and +
		// Test max limit size of step content in database
		//==================================================================
		contenu_etape = encodeURIComponent(tinyMCE.get('edit_etape').getContent());	
		if(contenu_etape.length > conf[44]) // MAX_STEP_LENGTH_CAPACITY_CONF
		{
			end_load_ajax();
			generer_msgbox(get_lib(358),get_lib(449).replace('$x',contenu_etape.length).replace('$max',conf[44]),'warning','msg');
			return false;
		}
		//==================================================================
		
		//==================================================================
		// Save step by ajax call
		//==================================================================
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		//configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		//configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 180000;	// 180 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=5&etape="+id_etape+"&contenu="+contenu_etape+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'save_step';
		configuration['param_fonction_a_executer_reponse'] = id_etape+',true';
		configuration['fonction_a_executer_cas_non_reponse'] = 'sauvegarder_etape';	
		configuration['param_fonction_a_executer_cas_non_reponse'] = id_etape+',false';	
		
		ajax_call(configuration);
		//==================================================================
	}
	else
	{
		if(type_retour == false)
		{
			// No server answer
			// Delete waiting message
			end_load_ajax();
		}
		else
		{
			// Answer ok
			disable_tinymce('tdetape'+id_etape);
			
			// Dispay steps
			document.getElementById('mesetapes').innerHTML = reponse;
			
			
			// Delete waiting message
			end_load_ajax();
			
			// Display last action done
			var texte_action = get_lib(319).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:editer_etape('+id_etape+');a_tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
			cancel_vimofy_lien();
			
			window.location='#'+id_etape;
		}
	}
}
/*===================================================================*/

/*==================================================================
 * Cancel step modification
 * id_etape : Number of step to save
 * type_retour : 
 * reponse :
 ====================================================================*/
function annuler_edition_etape(id_etape,reponse,reponse_ajax)
{
	if(typeof(reponse_ajax) == 'undefined')
	{
		if(typeof(reponse) == 'undefined')
		{
			aff_btn = new Array([get_lib(182),get_lib(181)],["annuler_edition_etape("+id_etape+",true);","close_msgbox();"]);
	    	generer_msgbox(get_lib(58),get_lib(136),'question','msg',aff_btn);
		}
		else
		{
			// On affiche le message d'attente
			generer_msgbox('',get_lib(185),'','wait');
		
			/**==================================================================
			 * PREPARATION DE LA TINYMCE
			 ====================================================================*/	
			
			// On desactive la tiny avant déplacement de la textarea dans son conteneur d'origine
			tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');
		
			// Récupération de la textarea d'édition d'étape
			var tiny_edition_etape = document.getElementById('tdetape'+id_etape).innerHTML;
			
			// Suppression de la textarea d'édition d'étape dans le td de l'étape
			document.getElementById('tdetape'+id_etape).innerHTML = '';
			
			// Mise en place de la textarea d'édition d'étape dans son conteneur d'origine
			document.getElementById('conteneur_tiny_edition').innerHTML = tiny_edition_etape;
			document.getElementById('conteneur_tiny_edition').style.display = 'none';
			
			/**==================================================================*/		
			
			/**==================================================================
			 * Annule l'edition de l'étape
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_a_modifier'] = 'mesetapes';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 10000;	// 10 secondes
			configuration['max_tentative'] = 10;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "ssid="+ssid+"&id_etape="+id_etape+'&action=24';
			configuration['fonction_a_executer_reponse'] = 'annuler_edition_etape';
			configuration['param_fonction_a_executer_reponse'] = id_etape+','+reponse;
			configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';		
			
			ajax_call(configuration);
			/**==================================================================*/		
			
			// Affichage de la dernère action
			var texte_action = get_lib(320).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:editer_etape('+id_etape+');a_tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
			
		}
	}
	else
	{
		end_load_ajax();
		window.location='#'+id_etape;
	}
}
/*===================================================================*/

	
	
/*==================================================================
 * Duplicate current step and add the new one juste by down
 * id_etape : Number of step to duplicate
 * reponse :
 ====================================================================*/
function copie_etape(id_etape,reponse)
{
	if(typeof(reponse) == "undefined")
	{
		generer_msgbox('',get_lib(176),'','wait');
		
		/**==================================================================
		 * COPIE DE L'ETAPE
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		configuration['div_a_modifier'] = 'mesetapes';
		configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 15000;	// 15 secondes
		configuration['max_tentative'] = 5;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=15&ssid="+ssid+"&id_etape="+id_etape;
		configuration['fonction_a_executer_reponse'] = 'copie_etape';
		configuration['param_fonction_a_executer_reponse'] = id_etape;
		configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
		
		ajax_call(configuration);
		/**==================================================================*/
	}
	else
	{
		// On masque la fenêtre d'attente
		close_msgbox();
		
		// Décrémentation du nombre d'étape de 1
		document.getElementById('onglet_nbr_etape').innerHTML = parseInt(document.getElementById('onglet_nbr_etape').innerHTML) + 1;
		
		
		// Affichage de la dernère action
		var texte_action = get_lib(324).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
		texte_action = texte_action.replace('$k','<a href="#'+(id_etape + 1)+'">'+(id_etape + 1)+'</a>');
		iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		
		// Déplacement sur l'étape remplacante
		window.location  = '#'+id_etape;
	}
}
/*===================================================================*/

	
/*==================================================================
 * Try to delete current step
 * id_etape : Number of step to duplicate
 * reponse :
 ====================================================================*/
function del_step(id_etape,resulat_test,reponse)
{
	if(typeof(reponse) == "undefined")
	{

		/**==================================================================
		 * Vérification ok, suppression possible
		 ====================================================================*/	
    	// On affiche le message d'attente
		generer_msgbox('',get_lib(126).replace('id_etape', id_etape),'','wait');
		
		/**==================================================================
		 * Suppression de l'étape
		 ====================================================================*/
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['div_a_modifier'] = 'mesetapes';
		configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 15000;	// 15 secondes
		configuration['max_tentative'] = 5;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=1&id_etape="+id_etape+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'del_step';
		configuration['param_fonction_a_executer_reponse'] = id_etape+',"'+reponse_json+'"';
		configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
		
		ajax_call(configuration);
		/**==================================================================*/				
    
		/**==================================================================*/
	}
	else
	{
		// On masque la fenêtre d'attente
		close_msgbox();	
		
		// Décrémentation du nombre d'étape de 1
		document.getElementById('onglet_nbr_etape').innerHTML = parseInt(document.getElementById('onglet_nbr_etape').innerHTML) - 1;
		
		// Déplacement sur l'étape remplacante
		if(id_etape > 1)
		{
			window.location  = '#'+(id_etape-1);
		}
		else
		{
			window.location  = '#'+id_etape;
		}
		
		// Affichage de la dernère action
		var texte_action = get_lib(322).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
		iknow_panel_set_action(texte_action,'<td><a href="#'+id_etape+'" onclick="javascript:tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');

	}
}
/*===================================================================*/
	
	
	
	/**
	 * Vérifie si l'étape id_etape peut être supprimée
	 * 
	 * @param id_etape : Identifiant de l'étape à supprimer
	 */
	function verif_del_step(id_etape,reponse)
	{
		if(typeof(reponse) == 'undefined')
		{
			generer_msgbox('',get_lib(177),'','wait');
			
			/**==================================================================
			 * On vérifie si des étapes pointe sur celle ci
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 7000;		// 7 secondes 
			configuration['max_tentative'] = 3;
			configuration['type_retour'] = false;			// ResponseText 		
			configuration['param'] = "action=11&etape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'verif_del_step';
			configuration['param_fonction_a_executer_reponse'] = id_etape;
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
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
			
			if(decodeURIComponent(reponse_json.parent.erreur) == 'false')
			{
				reponse = addslashes(reponse);
				aff_btn = new Array([get_lib(182),get_lib(181)],["del_step("+id_etape+",'"+reponse+"');","close_msgbox();"]);
		    	generer_msgbox(get_lib(124),get_lib(125).replace('id_etape', id_etape),'question','msg',aff_btn);
			}
			else
			{
				/**==================================================================
				 * Suppression de l'étape impossible
				 ====================================================================*/
				 // Génération du bandeau d'informations
				if(typeof(reponse_json.parent.titre_controle) == 'string')
				{		
					iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.message_controle),decodeURIComponent(reponse_json.parent.titre_controle));
					iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
					close_msgbox();	
				}
				/**==================================================================*/
			}
		}
	}

	
	/**
	 * Check if the step can be overwrite by other step
	 * 
	 * @param id_etape : Id of the step to overwrite
	 */
	function ctrl_step_import(id_etape,reponse)
	{
		if(typeof(reponse) == 'undefined')
		{
			generer_msgbox('',get_lib(177),'','wait');
			
			/**==================================================================
			 * Ajax init
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 7000;		// 7 secondes 
			configuration['max_tentative'] = 3;
			configuration['type_retour'] = false;			// ResponseText 		
			configuration['param'] = "action=11&etape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'ctrl_step_import';
			configuration['param_fonction_a_executer_reponse'] = id_etape;
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
		{
			try 
			{
				reponse_json = get_json(reponse);
			} 
			catch(e) 
			{
				alert(reponse);
				return false;
			}
			if(decodeURIComponent(reponse_json.parent.erreur) == 'false')
			{
				clic_droit_edit(id_etape);
			}
			else
			{
				/**==================================================================
				 * Ecrasement de l'étape impossible
				 ====================================================================*/
				 // Génération du bandeau d'informations
				if(typeof(reponse_json.parent.titre_controle) == 'string')
				{		
					iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.message_controle),decodeURIComponent(reponse_json.parent.titre_controle));
					iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
					close_msgbox();	
				}
				/**==================================================================*/
			}
		}
		return false;
	}
	
	/**
	 * Déplace l'étape de id_etape_src vers id_etape_dest en mettant à jour toute les autres étapes
	 * 
	 * @param id_etape_src : Identifiant de l'étape source
	 * @param id_etape_dst : Identifiant de l'étape destination
	 * @param eviter_controle : false si l'id destination a été choisis par l'utilisateur
	 * @return
	 */
	function deplacer_etape(nbr_etape,id_etape_src,id_etape_dst,eviter_controle,reponse)
	{
		if(typeof(reponse) == 'undefined')
		{
			if(typeof(id_etape_dst) == "undefined")
			{
				// Demande de l'id de destination
				aff_btn = new Array([get_lib(182),get_lib(181)],["deplacer_etape("+nbr_etape+","+id_etape_src+",document.getElementById('iknow_msgbox_prompt_value').value,false);","close_msgbox();"]);		
				generer_msgbox(get_lib(129),get_lib(130),'question','prompt',aff_btn);
			}
			else
			{
				
				// On affiche le message d'attente
				generer_msgbox('',get_lib(128).replace('id_etape',id_etape_src),'','wait');
									
				// On vérifie si l'utilisateur a saisi une valeur numérique de type entière
				if(Isentier(id_etape_dst))
				{
					
					// On vérifie si l'utilisateur n'a pas saisi une valeur supérieure à la valeur max du nombre d'étape
					if(id_etape_dst > nbr_etape || id_etape_dst == 0)
					{
						iknow_panel_add_more_content('<table id="informations"><tr><td class="erreur"></td><td>&nbsp;'+get_lib(131)+'</td></tr></table>');
						close_msgbox();	
						iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');    			
					}
					else
					{
						// On vérifie si la source n'est pas égale à la destination
						if(id_etape_dst == id_etape_src)
						{
							iknow_panel_add_more_content('<table id="informations"><tr><td class="erreur"></td><td>&nbsp;'+get_lib(132)+'</td></tr></table>');
							close_msgbox();	
							iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');    									
						}
						else
						{	
							/**==================================================================
							 * Déplacement de l'étape de id_etape_src vers id_etape_dst
							 ====================================================================*/	
							var configuration = new Array();	
							
							configuration['page'] = 'ajax/ifiche/actions_etapes.php';
							configuration['div_a_modifier'] = 'mesetapes';
							configuration['div_wait'] = 'ajax_load_etape'+id_etape_src;
							configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape_src;
							configuration['delai_tentative'] = 15000;		// 15 secondes
							configuration['max_tentative'] = 5;
							configuration['type_retour'] = false;			// ReponseText
							configuration['param'] = "action=16&ssid="+ssid+"&id_etape_src="+id_etape_src+"&id_etape_dst="+id_etape_dst;
							configuration['fonction_a_executer_reponse'] = 'deplacer_etape';
							configuration['param_fonction_a_executer_reponse'] = nbr_etape+','+id_etape_src+','+id_etape_dst+','+eviter_controle;
							configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
							
							ajax_call(configuration);
							/**==================================================================*/	
							
							// Affichage de la dernère action
							var texte_action = get_lib(323).replace('$j', '<a href="#'+id_etape_src+'">'+id_etape_src+'</a>');
							texte_action = texte_action.replace('$k','<a href="#'+id_etape_dst+'">'+id_etape_dst+'</a>');
							iknow_panel_set_action(texte_action,'<td><a href="#'+id_etape_dst+'" onclick="javascript:tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
						}	
					}
				}
				else
				{
					iknow_panel_add_more_content('<table id="informations"><tr><td class="erreur"></td><td>&nbsp;'+get_lib(133)+'</td></tr></table>');
					close_msgbox();	
					iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');   
				}
			}
		}
		else
		{
			close_msgbox();
			window.location = '#'+id_etape_dst;
		}
	}
	
	/**
	 * Ajoute une étape id_etape dans la fiche
	 * 
	 * @param id_etape : Identifiant de l'étape à ajouter
	 */
	function ajouter_etape(id_etape,reponse) 
	{
		if(typeof(reponse) == "undefined")
		{
			// Fermeture de la barre d'informations
			iknow_panel_reduire();
			
			// On affiche le message d'attente
			generer_msgbox('',get_lib(134),'','wait');

			var id_ajout = id_etape - 1;
			
			/**==================================================================
			 * Ajout d'une étape
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_a_modifier'] = 'mesetapes';
			configuration['div_wait'] = 'ajax_load_etape'+id_ajout;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_ajout;
			configuration['delai_tentative'] = 180000;		// 180 secondes
			configuration['max_tentative'] = 5;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=4&idetape="+id_etape+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'ajouter_etape';
			configuration['param_fonction_a_executer_reponse'] = id_etape; 
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
		/**==================================================================*/	
		}
		else
		{
			// Etape ajoutée
			
			document.getElementById('onglet_nbr_etape').innerHTML = parseInt(document.getElementById('onglet_nbr_etape').innerHTML) + 1;
			close_msgbox();
			// Affichage de la dernère action
			var texte_action = get_lib(321).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:editer_etape('+id_etape+');a_tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
			
			window.location='#'+id_etape;
		}
		

	}
	
	/**
	 * Va chercher dans le contenu de l'etape si il y a un appel vers une autre fiche. 
	 * Si oui alors une requete sql est créée pour afficher les variables de sortie
	 * de cette fiche.
	 */
	function var_ext_etape() 
	{
		
		// Cette fonction permet de contacter la methode php variables_externe qui va chercher dans le contenu de l'etape si
		// il y a un appel vers une autre fiche. Si oui alors une requete sql est créer pour afficher les variables de sortie 
		// de cette fiche.
		
		contenuetape = encodeURIComponent(tinyMCE.get('edit_etape').getContent());
		
		/**==================================================================
		 * Création de la requête
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/ifiche/actions_etapes.php";
		configuration['delai_tentative'] = 6000;		// 6 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText				
		configuration['param'] = "action=10&contenu="+contenuetape+"&ssid="+ssid;

		ajax_call(configuration);
		/**==================================================================*/	
			
	}
	
	

	/**
	 * 
	 * @param id_etape
	 * @return
	 */
	function clic_droit_edit(id_etape)
	{
		// Préparation de l'import d'une étape
		
		// On affiche le message d'attente
		generer_msgbox('',get_lib(187),'','wait');

		/**==================================================================
		 * PREPARATION DE LA FICHE
		 ====================================================================*/	
		// Masquage des boutons d'action d'étapes
	
		// Bouton valider étape
		boutons = '<div class="valider" style="display:none;" id="save_alias" onclick="javascript: save_alias('+id_etape+');"></div>';
		
		// Bouton annuler modifications étape
		boutons += '<div class="annuler" onclick="javascript:annuler_alias('+id_etape+');"></div>';
		
		masquer_boutons_etapes(id_etape,boutons);

		/**==================================================================*/	
		
		//window.location = '#'+id_etape;
		document.getElementById('tdetape'+id_etape).innerHTML = '';
		
		load_vimofy_alias_id(id_etape);
		
	}
	
	function load_vimofy_alias_id(id_etape,ajax_return)
	{
		if(typeof(ajax_return) == "undefined")
		{	
			/**==================================================================
			 * 
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 5000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=19&ssid="+ssid+"&etape="+id_etape;
			configuration['fonction_a_executer_reponse'] = 'load_vimofy_alias_id';
			configuration['param_fonction_a_executer_reponse'] = id_etape; 
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/			
		}
		else
		{	// Retour ajax de la préparation de l'import d'une étape, Initialisation des vimofy's
			try 
			{
				var reponse_json = get_json(ajax_return); 
				document.getElementById('tdetape'+id_etape).innerHTML = decodeURIComponent(reponse_json.parent.header)+decodeURIComponent(reponse_json.parent.vimofy);
				addCss(decodeURIComponent(reponse_json.parent.css));
				eval(decodeURIComponent(reponse_json.parent.json));
				vimofy_open_lmod('imp_step_ID');
				close_msgbox();
			} 
			catch(e) 
			{
				alert('err 1 \n'+e.message+'\n');
			}
		}	
		return false;	
	}
	
	
	function load_vimofy_alias_version(id_etape,ajax_return)
	{
		if(typeof(ajax_return) == "undefined")
		{	
			/**==================================================================
			 * 
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 5000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=32&ssid="+ssid+"&etape="+id_etape+"&id="+document.getElementById('lst_imp_step_ID').value;
			configuration['fonction_a_executer_reponse'] = 'load_vimofy_alias_version';
			configuration['param_fonction_a_executer_reponse'] = id_etape; 
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/			
		}
		else
		{	// Retour ajax de la préparation de l'import d'une étape, Initialisation des vimofy's
			try 
			{
				var reponse_json = get_json(ajax_return); 
				document.getElementById('tdetape'+id_etape+'_alias_version').innerHTML = decodeURIComponent(reponse_json.parent.header)+decodeURIComponent(reponse_json.parent.vimofy);
				addCss(decodeURIComponent(reponse_json.parent.css));
				eval(decodeURIComponent(reponse_json.parent.json));
				vimofy_open_lmod('imp_step_Version');
				close_msgbox();
			} 
			catch(e) 
			{
				alert('err 4.1 \n'+e.message+'\n');
			}
		}	
		return false;	
	}
	
	function load_vimofy_alias_step_id(id_etape,ajax_return)
	{
		if(typeof(ajax_return) == "undefined")
		{	
			/**==================================================================
			 * 
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 5000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=33&ssid="+ssid+"&etape="+id_etape+"&id="+document.getElementById('lst_imp_step_ID').value+"&version="+document.getElementById('lst_imp_step_Version').value;
			configuration['fonction_a_executer_reponse'] = 'load_vimofy_alias_step_id';
			configuration['param_fonction_a_executer_reponse'] = id_etape; 
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/			
		}
		else
		{	// Retour ajax de la préparation de l'import d'une étape, Initialisation des vimofy's
			try 
			{
				var reponse_json = get_json(ajax_return); 
				document.getElementById('tdetape'+id_etape+'_alias_step').innerHTML = decodeURIComponent(reponse_json.parent.header)+decodeURIComponent(reponse_json.parent.vimofy);
				addCss(decodeURIComponent(reponse_json.parent.css));
				eval(decodeURIComponent(reponse_json.parent.json));
				vimofy_open_lmod('imp_step_id_step');
				close_msgbox();
			} 
			catch(e) 
			{
				alert('err 4.2 \n'+e.message+'\n');
			}
		}	
		return false;	
	}
	
	
	function get_content_step_alias(id_etape,ajax_return)
	{
		if(typeof(ajax_return) == "undefined")
		{	
			/**==================================================================
			 * 
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['div_a_modifier'] = 'visu_alias';
			configuration['div_wait'] = 'ajax_load_etape'+id_etape;
			configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
			configuration['delai_tentative'] = 5000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=20&ssid="+ssid+"&fiche="+document.getElementById('lst_imp_step_ID').value+"&version="+document.getElementById('lst_imp_step_Version').value+'&etape='+document.getElementById('lst_imp_step_id_step').value;
			configuration['fonction_a_executer_reponse'] = 'get_content_step_alias';
			configuration['param_fonction_a_executer_reponse'] = id_etape; 
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
			
			ajax_call(configuration);
			/**==================================================================*/			
		}
		else
		{	// Retour ajax de la préparation de l'import d'une étape, Initialisation des vimofy's
			try 
			{
				document.getElementById("save_alias").style.display = "block";
			} 
			catch(e) 
			{
				alert('err 4.3 \n'+e.message+'\n');
			}
		}	
	}
	
	
	function save_alias(id_etape,test,reponse)
	{
		if(typeof(reponse) == 'undefined')
		{
			if(typeof(test) == "undefined")
			{
				aff_btn = new Array([get_lib(182),get_lib(181)],["save_alias("+id_etape+",true);","close_msgbox();"]);
				generer_msgbox(get_lib(139),get_lib(140),'question','msg',aff_btn);	
			}
			else
			{
				// On affiche le message d'attente
				generer_msgbox('',get_lib(184),'','wait');
		
				/**==================================================================
				 * Sauvegarde de l'alias
				 ====================================================================*/	
				var configuration = new Array();	
						
				configuration['page'] = "ajax/ifiche/actions_etapes.php";
				configuration['delai_tentative'] = 5000;		// 5 secondes
				configuration['div_a_modifier'] = 'mesetapes';
				configuration['div_wait'] = 'ajax_load_etape'+id_etape;
				configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
				configuration['max_tentative'] = 4;
				configuration['type_retour'] = false;			// ResponseText		
				configuration['param'] = "action=21&ssid="+ssid+"&etape="+id_etape;
				configuration['fonction_a_executer_reponse'] = 'save_alias';
				configuration['param_fonction_a_executer_reponse'] = id_etape+','+test;
				configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
				
				ajax_call(configuration);
				/**==================================================================*/
				
				// Affichage de la dernère action
				var texte_action = get_lib(325).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
				texte_action = texte_action.replace('$etape', document.getElementById('lst_imp_step_id_step').value);
				texte_action = texte_action.replace('$version', document.getElementById('lst_imp_step_Version').value);
				texte_action = texte_action.replace('$id', document.getElementById('lst_imp_step_ID').value);
				
				iknow_panel_set_action(texte_action);
			}
		}
		else
		{
			end_load_ajax();
			
			// Déplacement sur l'étape
			window.location='#'+id_etape;
		}
	}

	/**
	 * Annule l'import d'une étape depuis une autre fiche
	 * @param id_etape
	 * @return
	 */
	function annuler_alias(id_etape)
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(185),'','wait');

		/**==================================================================
		 * Affichage des étapes
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['div_a_modifier'] = 'mesetapes';
		configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 10000;	// 10 secondes
		configuration['max_tentative'] = 6;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=7&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'end_load_ajax';
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
		
		ajax_call(configuration);
		/**==================================================================*/
	}
	
	
	/**
	 * Ré-affiche les étapes
	 */
	function afficher_etapes(reponse) 
	{
		if(typeof(reponse) == 'undefined')
		{
			// On affiche le message d'attente
			generer_msgbox('',get_lib(195),'','wait');
			
			/**==================================================================
			 * RECUPERATION DES ETAPES
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_etapes.php';
			configuration['delai_tentative'] = 12000;	// 12 secondes
			configuration['max_tentative'] = 3;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=7&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'afficher_etapes';
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';
		
			ajax_call(configuration);
			/**==================================================================*/
		}
		else
		{
			// Affichage des des étapes
			document.getElementById('mesetapes').innerHTML = reponse;
				
			// Masquage de la fenêtre d'attente
			close_msgbox();	
			
			// Affichage de la fenêtre d'informations
			iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
		}
	}
	
	
	/****************************************************************************************
	 ****************************************************************************************
	 ***																				  ***
	 ***								ACTIONS FICHE									  ***
	 ***																				  ***
	 ****************************************************************************************
	 ****************************************************************************************/	
	
function editer_prerequis()
{
	if(document.getElementById('conteneur_tiny_edition').innerHTML != '')
	{
		/**==================================================================
		 * MASQUAGE DE LA BARRE D'OUTILS DE LA FICHE
		 ====================================================================*/	
		document.getElementById('barre_outils').style.display = 'none';
		/**==================================================================*/	
		
		// Fermeture de la barre d'informations
		iknow_panel_reduire();
		
		// Masquage du texte d'aide
		unset_text_help();
		
		// On affiche le message d'attente
		generer_msgbox('',get_lib(298),'','wait');
		/**==================================================================
		 * PREPARATION DE LA FICHE
		 ====================================================================*/	
		// Bouton valider prerequis
		boutons = '<div class="valider" onclick="javascript:sauvegarder_prerequis();" onmouseover="ikdoc(\'id_aide\');set_text_help(121);" onmouseout="ikdoc();unset_text_help();"></div>';

		// Bouton annuler modifications des prérequis
		boutons += '<div class="annuler" onclick="javascript:annuler_edition_prerequis();" onmouseover="ikdoc(\'id_aide\');set_text_help(122);" onmouseout="ikdoc();unset_text_help();"></div>';

		document.getElementById('outils_prerequis').innerHTML = boutons;
		/**==================================================================*/	
		
		/**==================================================================
		 * PREPARATION DE LA TINYMCE
		 ====================================================================*/	
		// On désactive la tiny avant déplacement de la textarea edit_etape
		//tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');

		// Récupération de la textarea d'édition wysiwyg
		var tiny_edition_etape = document.getElementById('conteneur_tiny_edition').innerHTML;

		// Suppression de la textarea d'édition d'étape de son conteneur d'origine
		document.getElementById('conteneur_tiny_edition').innerHTML = '';

		// Mise en place de la textarea d'édition d'étape dans l'étape en cours d'édition
		document.getElementById('td_prerequis').innerHTML = tiny_edition_etape;

		// Effacement de la tinyMCE
		tiny_edition_etape = '';

		// Remise en place de la tinyMCE
		/*tinyMCE.execCommand('mceAddControl', true, 'edit_etape');
		tinyMCE.get('edit_etape').setParam('theme_advanced_buttons1','removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup,|,Generer_URL,');
		tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');
		tinyMCE.execCommand('mceAddControl', true, 'edit_etape');*/

		// Définition des attributs de la tinyMCE
		/*tinyMCE.get('edit_etape').setParam('id_etape',0);
		tinyMCE.get('edit_etape').setParam('onglet_general',1);
		tinyMCE.get('edit_etape').setParam('fonction_save','sauvegarder_prerequis();');*/
		/**==================================================================*/	

		/**==================================================================
		 * RECUPERATION CONTENU PREREQUIS POUR EDITION
		 ====================================================================*/	
		var configuration = new Array();	

		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 5000;
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = 'ssid='+ssid+'&action=26';
		configuration['fonction_a_executer_reponse'] = 'set_content_tiny_prerequis';
		//configuration['param_fonction_a_executer_reponse'] = 0;
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
		
		ajax_call(configuration);
		/**==================================================================*/	

		// Affichage de la dernère action
		iknow_panel_set_action(get_lib(330),'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
	}
	else
	{
		 ctrl_utilisation_tiny(-1);
	}
}



function editer_description()
{
	if(document.getElementById('conteneur_tiny_edition').innerHTML != '')
	{
		// Fermeture de la barre d'informations
		iknow_panel_reduire();
		
		// Masquage du texte d'aide
		unset_text_help();
		
		// On affiche le message d'attente
		generer_msgbox('',get_lib(298),'','wait');
		/**==================================================================
		 * PREPARATION DE LA FICHE
		 ====================================================================*/	
		// Bouton valider prerequis
		boutons = '<div class="valider" onclick="javascript:sauvegarder_description();" onmouseover="ikdoc(\'id_aide\');set_text_help(121);" onmouseout="ikdoc();unset_text_help();"></div>';
		
		// Bouton annuler modifications des prérequis
		boutons += '<div class="annuler" onclick="javascript:annuler_edition_description();" onmouseover="ikdoc(\'id_aide\');set_text_help(122);" onmouseout="ikdoc();unset_text_help();"></div>';
		
		document.getElementById('outils_description').innerHTML = boutons;
		/**==================================================================*/	
		
		/**==================================================================
		 * PREPARATION DE LA TINYMCE
		 ====================================================================*/
		// On désactive la tiny avant déplacement de la textarea edit_etape
		//tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');
		// Récupération de la textarea d'édition wysiwyg
		var tiny_edition_etape = document.getElementById('conteneur_tiny_edition').innerHTML;

		// Suppression de la textarea d'édition d'étape de son conteneur d'origine
		document.getElementById('conteneur_tiny_edition').innerHTML = '';
		
		// Mise en place de la textarea d'édition d'étape dans l'étape en cours d'édition
		document.getElementById('td_description').innerHTML = tiny_edition_etape;
		// Effacement de la tinyMCE
		tiny_edition_etape = '';

		/**==================================================================*/	
		
		/**==================================================================
		 * RECUPERATION CONTENU PREREQUIS POUR EDITION
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 5000;
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = 'ssid='+ssid+'&action=29';
		configuration['fonction_a_executer_reponse'] = 'set_content_tiny_description';
		//configuration['param_fonction_a_executer_reponse'] = 0;
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
		ajax_call(configuration);
		/**==================================================================*/	
		// Affichage de la dernère action
		iknow_panel_set_action(get_lib(329),'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1_1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		
		/**==================================================================
		 * MASQUAGE DE LA BARRE D'OUTILS DE LA FICHE
		 ====================================================================*/	
		document.getElementById('barre_outils').style.display = 'none';
		/**==================================================================*/	
	}
	else
	{
		 ctrl_utilisation_tiny(0);
	}
}


/**
 * Sauvegarde les prérequis en cours d'édition
 * 
 */
function sauvegarder_prerequis(type_retour,reponse) 
{
	if(typeof(type_retour) == 'undefined')
	{
		// Display waiting message
		generer_msgbox('',get_lib(184),'','wait');
		
		//==================================================================
		// Recover content of TinyMCE
		// Protect special digit & and +
		// Test max limit size of step content in database
		//==================================================================
		contenu_prerequis = encodeURIComponent(tinyMCE.get('edit_etape').getContent());	
		if(contenu_prerequis.length > conf[44]) //MAX_PREREQUISITE_LENGTH_CAPACITY_CONF	
		{
			end_load_ajax();
			generer_msgbox(get_lib(358),get_lib(449).replace('$x',contenu_prerequis.length).replace('$max',conf[44]),'warning','msg');
			return false;
		}
		//==================================================================
		
		//==================================================================
		// Save prerequisite by ajax call
		//==================================================================
		var configuration = new Array();
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 12000;	// 12 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=27&contenu="+contenu_prerequis+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'sauvegarder_prerequis';
		configuration['param_fonction_a_executer_reponse'] = 'true';
		configuration['fonction_a_executer_cas_non_reponse'] = 'sauvegarder_prerequis';
		configuration['param_fonction_a_executer_cas_non_reponse'] = 'false';	
	
		ajax_call(configuration);
		//==================================================================
	}
	else
	{

		if(type_retour == false)
		{
			// No server answer
			// Delete waiting message
			end_load_ajax();
		}
		else
		{
			// answer ok
			var contenu_tiny = tinyMCE.get('edit_etape').getContent();

			disable_tinymce('td_prerequis');

			// Display prerequisite
			//document.getElementById('tr_prerequis').innerHTML = reponse;
			document.getElementById('outils_prerequis').innerHTML = '<a class="editer" href="#" onclick="javascript:editer_prerequis();"></a>';
			document.getElementById('td_prerequis').innerHTML = contenu_tiny;
			
			// Delete waiting message
			end_load_ajax();

			// Display last action
			texte_action = get_lib(334);
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1_2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
			
			/**==================================================================
			 * AFFICHAGE DE LA BARRE D'OUTILS DE LA FICHE
			 ====================================================================*/	
			document.getElementById('barre_outils').style.display = 'block';
			/**==================================================================*/	

			//==================================================================
			// Try to get raw text content
			//==================================================================
			if(document.getElementById('td_prerequis').textContent != 'undefined')
			{
				contenu_raw_text_prerequis = document.getElementById('td_prerequis').textContent;
				var configuration = new Array();
				configuration['page'] = 'ajax/ifiche/actions_fiche.php';
				configuration['delai_tentative'] = 12000;	// 12 secondes
				configuration['max_tentative'] = 3;
				configuration['type_retour'] = false;		// ReponseText
				configuration['param'] = "action=35&rawtext="+contenu_raw_text_prerequis+"&ssid="+ssid;
				configuration['fonction_a_executer_reponse'] = '';
				configuration['param_fonction_a_executer_reponse'] = 'false';
				configuration['fonction_a_executer_cas_non_reponse'] = '';	
				configuration['param_fonction_a_executer_cas_non_reponse'] = 'false';	
				ajax_call(configuration);
			}
			//==================================================================
		}
	}
}



/**
 * Sauvegarde la description en cours d'édition
 * 
 */
function sauvegarder_description(type_retour,reponse) 
{
	if(typeof(type_retour) == 'undefined')
	{
		// Display waiting message
		generer_msgbox('',get_lib(184),'','wait');
		
		//==================================================================
		// Recover content of TinyMCE
		// Protect special digit & and +
		// Test max limit size of step content in database
		//==================================================================
		contenu_description = encodeURIComponent(tinyMCE.get('edit_etape').getContent());	
		if(contenu_description.length > conf[44]) //MAX_ISHEET_DESCRIPTION_LENGTH_CAPACITY_CONF	
		{
			end_load_ajax();
			generer_msgbox(get_lib(358),get_lib(449).replace('$x',contenu_description.length).replace('$max',conf[44]),'warning','msg');
			return false;
		}
		//==================================================================
		
		//==================================================================
		// Save description by ajax call
		//==================================================================
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 12000;	// 12 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=30&contenu="+contenu_description+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'sauvegarder_description';
		configuration['param_fonction_a_executer_reponse'] = 'true';
		configuration['fonction_a_executer_cas_non_reponse'] = 'sauvegarder_description';	
		configuration['param_fonction_a_executer_cas_non_reponse'] = 'false';	
	
		ajax_call(configuration);
		//==================================================================
	}
	else
	{
		if(type_retour == false)
		{
			// No server answer
			// Delete waiting message
			end_load_ajax();
		}
		else
		{
			// Ajax return ok
			var contenu_tiny = tinyMCE.get('edit_etape').getContent();
			
			disable_tinymce('td_description');

			
			// Affichage de la description
			document.getElementById('outils_description').innerHTML = '<a  class="editer" href="#" onclick="javascript:editer_description();"></a>';
			document.getElementById('td_description').innerHTML = contenu_tiny;
			
			// Suppression du message d'attente
			end_load_ajax();
			// Affichage de la dernère action
			
			iknow_panel_set_action(get_lib(332),'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1_1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		
			
			/**==================================================================
			 * AFFICHAGE DE LA BARRE D'OUTILS DE LA FICHE
			 ====================================================================*/	
			document.getElementById('barre_outils').style.display = 'block';
			/**==================================================================*/	

			//==================================================================
			// Try to get raw text content
			//==================================================================
			if(document.getElementById('td_description').textContent != 'undefined')
			{
				contenu_raw_text_descrpition = document.getElementById('td_description').textContent;
				var configuration = new Array();
				configuration['page'] = 'ajax/ifiche/actions_fiche.php';
				configuration['delai_tentative'] = 12000;	// 12 secondes
				configuration['max_tentative'] = 3;
				configuration['type_retour'] = false;		// ReponseText
				configuration['param'] = "action=34&rawtext="+contenu_raw_text_descrpition+"&ssid="+ssid;
				configuration['fonction_a_executer_reponse'] = '';
				configuration['param_fonction_a_executer_reponse'] = 'false';
				configuration['fonction_a_executer_cas_non_reponse'] = '';	
				configuration['param_fonction_a_executer_cas_non_reponse'] = 'false';	
				ajax_call(configuration);
			}
			//==================================================================
		}
	}
}

function disable_tinymce(p_id_emplacement)
{
	/**==================================================================
	 * PREPARATION DE LA TINYMCE
	 ====================================================================*/	
	// On désactive la tiny avant déplacement de la textarea dans son conteneur d'origine
	tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');

	// Récupération de la textarea d'édition d'étape
	var tiny_edition_etape = document.getElementById(p_id_emplacement).innerHTML;
	
	// Suppression de la textarea d'édition d'étape dans le td de l'étape
	document.getElementById(p_id_emplacement).innerHTML = '';
	
	// Mise en place de la textarea d'édition d'étape dans son conteneur d'origine
	document.getElementById('conteneur_tiny_edition').innerHTML = tiny_edition_etape;
	document.getElementById('conteneur_tiny_edition').style.display = 'none';
	/**==================================================================*/	
}
function annuler_edition_prerequis(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["annuler_edition_prerequis(true);","close_msgbox();"]);
    	generer_msgbox(get_lib(58),get_lib(136),'question','msg',aff_btn);
	}
	else
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(185),'','wait');
	
		/**==================================================================
		 * PREPARATION DE LA TINYMCE
		 ====================================================================*/	
		
		// On desactive la tiny avant déplacement de la textarea dans son conteneur d'origine
		tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');
	
		// Récupération de la textarea d'édition d'étape
		var tiny_edition_etape = document.getElementById('td_prerequis').innerHTML;
		
		// Suppression de la textarea d'édition d'étape dans le td de l'étape
		document.getElementById('td_prerequis').innerHTML = '';
		
		// Mise en place de la textarea d'édition d'étape dans son conteneur d'origine
		document.getElementById('conteneur_tiny_edition').innerHTML = tiny_edition_etape;
		
		/**==================================================================*/		
		
		/**==================================================================
		 * Annule l'edition des prerequis
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['div_a_modifier'] = 'tr_prerequis';
		configuration['delai_tentative'] = 6000;	// 6 secondes
		configuration['max_tentative'] = 10;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "ssid="+ssid+"&action=28";
		configuration['fonction_a_executer_reponse'] = 'end_load_ajax';
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';		
		
		ajax_call(configuration);
		/**==================================================================*/	
		
		// Affichage de la dernère action
		texte_action = get_lib(333);
		
		iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1_2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		
		/**==================================================================
		 * AFFICHAGE DE LA BARRE D'OUTILS DE LA FICHE
		 ====================================================================*/	
		document.getElementById('barre_outils').style.display = 'block';
		/**==================================================================*/	
	}
}

function annuler_edition_description(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["annuler_edition_description(true);","close_msgbox();"]);
    	generer_msgbox(get_lib(58),get_lib(136),'question','msg',aff_btn);
	}
	else
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(185),'','wait');
	
		/**==================================================================
		 * PREPARATION DE LA TINYMCE
		 ====================================================================*/	
		
		// On desactive la tiny avant déplacement de la textarea dans son conteneur d'origine
		tinyMCE.execCommand('mceRemoveControl', true, 'edit_etape');
	
		// Récupération de la textarea d'édition d'étape
		var tiny_edition_etape = document.getElementById('td_description').innerHTML;
		
		// Suppression de la textarea d'édition d'étape dans le td de l'étape
		document.getElementById('td_description').innerHTML = '';
		
		// Mise en place de la textarea d'édition d'étape dans son conteneur d'origine
		document.getElementById('conteneur_tiny_edition').innerHTML = tiny_edition_etape;
		
		/**==================================================================*/		
		
		/**==================================================================
		 * Annule l'edition de l'étape
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['div_a_modifier'] = 'tr_description';
		configuration['delai_tentative'] = 6000;	// 6 secondes
		configuration['max_tentative'] = 10;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "ssid="+ssid+"&action=31";
		configuration['fonction_a_executer_reponse'] = 'end_load_ajax';
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';		
		
		ajax_call(configuration);
		/**==================================================================*/		
		
		// Affichage de la dernère action
		texte_action = get_lib(331);
		
		iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
	
		/**==================================================================
		 * AFFICHAGE DE LA BARRE D'OUTILS DE LA FICHE
		 ====================================================================*/	
		document.getElementById('barre_outils').style.display = 'block';
		/**==================================================================*/	
	}
}

function ctrl_utilisation_tiny(id_step)
{
	// l'étape tinyMCE.activeEditor.getParam('id_etape')) est déjà en cours d'édition
	if(tinyMCE.activeEditor.getParam('id_etape') == 0)
	{
		if(tinyMCE.activeEditor.getParam('onglet_general') == 0)
		{
			// Description en cours de modification
			aff_btn = new Array([get_lib(182)],["window.location='#'+tinyMCE.activeEditor.getParam('id_etape');close_msgbox();"]);
			generer_msgbox(get_lib(302),get_lib(299),'info','msg',aff_btn);
		}
		else
		{
			// Prerequis en cours de modification
			aff_btn = new Array([get_lib(182)],["window.location='#'+tinyMCE.activeEditor.getParam('id_etape');close_msgbox();"]);
			generer_msgbox(get_lib(301),get_lib(300),'info','msg',aff_btn);
		}
	}
	else
	{
		// Etape en cours de modification
		window.location='#'+id_step;
		if(id_step != tinyMCE.activeEditor.getParam('id_etape'))
		{
			aff_btn = new Array([get_lib(182)],["window.location='#'+tinyMCE.activeEditor.getParam('id_etape');close_msgbox();"]);
			generer_msgbox(get_lib(212),get_lib(144).replace('$j', tinyMCE.activeEditor.getParam('id_etape')),'info','msg',aff_btn);
		}
		
	}
	
}

/**
 * Sauvegarde la fiche en cours de modification
 * 
 * @param bloquer : si à true permet de bloquer la fiche contre des modifications utltérieures
 * @param reponse : interne, appelé lors du retour ajax de la sauvegarde
 */
function save_sheet(bloquer,reponse) 
{
	if(typeof(bloquer) == "undefined")
	{	
		var bloquer = 0;
	}

	if(typeof(reponse) == "undefined")
	{	// Sauvegarde de la fiche
				
		// On affiche le message d'attente
		generer_msgbox('',get_lib(137),'','wait');
		
		/**==================================================================
		 * Sauvegarde de la fiche
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 15000;		// 15 secondes
		configuration['max_tentative'] = 5;
		configuration['type_retour'] = false;			// responseText
		configuration['param'] = get_elements_fiche(1)+'&bloquer='+bloquer;
		configuration['fonction_a_executer_reponse'] = 'save_sheet';
		configuration['param_fonction_a_executer_reponse'] = bloquer;
		   																	
		ajax_call(configuration);
		/**==================================================================*/	
	}
	else
	{	// Retour ajax de la sauvegarde, vérification de l'état de la sauvegarde
		close_msgbox();	
		
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
		if(decodeURIComponent(reponse_json.parent.controle) == 'false')
		{
			/**==================================================================
			 * Il n'y a pas d'erreur (niveau 2) dans la fiche,
			 ====================================================================*/			
			// desactive le message de confirmation de sortie de la fiche
			fiche_sauvegardee = true;

			// redirection en visualisation
			window.location.replace("./ifiche.php?&ID="+reponse_json.parent.id_fiche+"&ikbackup=true");	
			/**==================================================================*/	
		}
		else	
		{
			// Il y a des erreurs de niveau 2 dans la fiche
			/**==================================================================
			 * Affichage du message d'erreur
			 ====================================================================*/	
			iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.message_controle),decodeURIComponent(reponse_json.parent.titre_controle));
			iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
			
			blink_error_msg_start();
			/**==================================================================*/
		}

		/**==================================================================
		 * Gestion des erreurs PHP (Affichage des erreurs)
		 ====================================================================*/			
		if(decodeURIComponent(reponse_json.parent.erreur_sauvegarde) != ' ')
		{
			iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.erreur_sauvegarde),get_lib(152));
			iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
		}
		/**==================================================================*/	
	}
}
 
function get_elements_fiche(id_action)
{
	/**==================================================================
	 * Récupération des données
	 ====================================================================*/	
	if(document.getElementById('lst_vimofy2_pole_lmod'))
	{
		var pole = encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
	}
	else
	{
		var pole = '';
	}
	
	if(document.getElementById('lst_vimofy2_vers_pole_lmod'))
	{
		var version = encodeURIComponent(document.getElementById('lst_vimofy2_vers_pole_lmod').value);
	}	
	else
	{
		var version = '';
	}
	
	if(document.getElementById('lst_vimofy2_activite_lmod'))
	{
		var activite = encodeURIComponent(document.getElementById('lst_vimofy2_activite_lmod').value);
	}	
	else
	{
		var activite = '';
	}
	
	if(document.getElementById('lst_vimofy2_module_lmod'))
	{
		var module = encodeURIComponent(document.getElementById('lst_vimofy2_module_lmod').value);
	}	
	else
	{
		var module = '';
	}
	var titre = encodeURIComponent(document.getElementById('titre').value); 
	var statut = encodeURIComponent(document.getElementById('statut').value);
	var modifpar = encodeURIComponent(document.getElementById('modifie_par').value);
	/**==================================================================*/	
	
	return "action="+id_action+"&titre="+titre+"&pole="+pole+"&version="+version+"&activite="+activite+"&idmodule="+module+"&statut="+statut+"&modifpar="+modifpar+"&ssid="+ssid;	
}

/**
 * Annule les modifications apportées sur les tags de l'étape id_etape
 * 
 * @param id_etape : ident
 * @return
 */
function annuler_modifications_tag(id_etape,test)
{
	if(typeof(test) == "undefined")
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["annuler_modifications_tag("+id_etape+",true);","close_msgbox();"]);
		generer_msgbox(get_lib(135),get_lib(136),'question','msg',aff_btn);
	}
	else
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(185),'','wait');
		
		/**==================================================================
		 * Annulation des modifications sur les tags
		 ====================================================================*/	
		var configuration = new Array();	
			
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['div_a_modifier'] = 'mesetapes';
		configuration['div_wait'] = 'ajax_load_etape'+id_etape;
		configuration['div_wait_nbr_tentative'] = 'ajax_step_qtt_retrieve'+id_etape;
		configuration['delai_tentative'] = 3000;		// 3 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ReponseText
		configuration['param'] = "ssid="+ssid+"&id_etape="+id_etape+'&action=26';
		configuration['fonction_a_executer_reponse'] = 'end_load_ajax';
		configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';
			
		ajax_call(configuration);
		/**==================================================================*/	
		
		// Affichage de la dernère action
		texte_action = get_lib(328).replace('$j', '<a href="#'+id_etape+'">'+id_etape+'</a>');
		
		iknow_panel_set_action(texte_action,'<td><a href="#'+id_etape+'" onclick="javascript:tabbar.setTabActive(\'tab-level2\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
		tag_edit_on = false;
	}
}

/**
 * Duplique la fiche en cours de modification
 * 
 * @param reponse : interne, appelé lors du retour ajax de la duplication
 * NR_IKNOW_5_
 */
function dupliquer_fiche(force,reponse_ok,reponse) 
{
	if(typeof(force) == 'undefined') force = 'false';
	
	if(typeof(reponse_ok) == "undefined" && force != 'true')
	{
		aff_btn = new Array([get_lib(182),get_lib(181)],["dupliquer_fiche("+force+",true);","close_msgbox();"]);
    	generer_msgbox(get_lib(66),get_lib(407),'question','msg',aff_btn); 
	}
	else
	{
		close_msgbox();
		if(typeof(reponse) == "undefined")
		{
			/**==================================================================
			 * Duplication de la fiche
			 ====================================================================*/	
			var configuration = new Array();	
	
			configuration['page'] = 'ajax/ifiche/actions_fiche.php';
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 5;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "ssid="+ssid+"&action=2&force="+force;
			configuration['fonction_a_executer_reponse'] = 'dupliquer_fiche';
			configuration['param_fonction_a_executer_reponse'] = "'"+force+'\','+reponse_ok;
	
			ajax_call(configuration);
			/**==================================================================*/			
		}
		else
		{	
			// Retour ajax de la duplication
			// Transformation du retour XML en JSON
			reponse_json = get_json(reponse); 
			
			// Génération du bandeau d'informations
			if(typeof(reponse_json.parent.duplication_ok) == 'string')
			{
				// --- La fiche a bien été dupliquée ---
				
				// Changement de l'id de la fiche
				var ID_fiche_last = ID_fiche;
				ID_fiche = 'new';
				document.getElementById('id_fiche').innerHTML = ID_temp;
				document.getElementById('version_fiche').innerHTML = 0;
				
		        // Changement du titre de la fiche
		        document.title = get_lib(138);
		        
		        // Génération du bandeau d'informations
		        iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.duplication_ok),'');
		        iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
		        
				iknow_panel_set_action(get_lib(430).replace('$x',ID_fiche_last));
				
				document.getElementById('toolbar_btn_dupliq').style.display = 'none';
				document.getElementById('toolbar_btn_lock').style.display = 'none';
				
				vimofy_refresh_page_ajax('vimofy2_varin');
				vimofy_refresh_page_ajax('vimofy_varout');
				vimofy_refresh_page_ajax('vimofy_tags');
				vimofy_refresh_page_ajax('vimofy_tags_ext');
				
			}
			else
			{
				// La fiche n'a pas été dupliquée car un warning est remonté, on demande à l'utilisateur si il faut forcer la duplication.
				aff_btn = new Array([get_lib(182),get_lib(181)],["dupliquer_fiche('true');close_msgbox();","close_msgbox();"]);
		    	generer_msgbox(decodeURIComponent(reponse_json.parent.titre_confirmation),decodeURIComponent(reponse_json.parent.message_confirmation),'question','msg',aff_btn);
				
			}
		}
	}
}

/**
 * Met à jour le statut de la fiche dans l'objet PHP
 * 
 * @param id_statut
 */
function set_statut(id_statut) 
{
	/**==================================================================
	 * Mise à jour du statut
	 ====================================================================*/	
	var configuration = new Array();	

	configuration['page'] = 'ajax/ifiche/actions_fiche.php';
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 5;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "ssid="+ssid+"&action=11&id_statut="+id_statut;
	   		
	ajax_call(configuration);
	/**==================================================================*/		
	
	// Affichage de la dernère action
	var texte_action = get_lib(339); 
	iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1_1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');

}


/**
 * Verification de base, verifie le pole ainsi que la version de la fiche.
 * Si l'un et l'autre sont ok alors on met un message d'informations.
 * Cette fonction est executée au lancement de la page.
 * 
 * @param verification_lancement: si à true ne ré-affiches pas les étapes, sinon les ré-affiches
 * @param reponse : interne, appelé lors du retour ajax de la verification
 */
var verif_generale_reponse_etapes;
var verif_generale_pos_scroll = 0;
function sheet_control(verification_lancement,reponse_affichage,reponse_controle)
{
	if(typeof(reponse_affichage) == "undefined" && verification_lancement == false)
	{	
		verif_generale_pos_scroll = document.getElementById('mesetapes').scrollTop;
		generer_msgbox('',get_lib(195),'','wait');
		verif_generale_reponse_etapes = '';
		/**==================================================================
		 * Affichage de la fiche
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = 'ajax/ifiche/actions_fiche.php';
		configuration['delai_tentative'] = 15000;				// 15 secondes
		configuration['max_tentative'] = 5;
		configuration['type_retour'] = false;					// ResponseText
		configuration['param'] = 'action=20&ssid='+ssid;		// Vérification lancement
		configuration['fonction_a_executer_reponse'] = 'sheet_control';
		configuration['param_fonction_a_executer_reponse'] = verification_lancement;
		configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';	

		ajax_call(configuration);
		/**==================================================================*/		
	}
	else
	{	
		if(typeof(reponse_controle) == "undefined")
		{
			verif_generale_reponse_etapes = reponse_affichage;
			// Verification de la fiche	
			
			// On affiche le message d'attente
			generer_msgbox('',get_lib(137),'','wait');
			
			/**==================================================================
			 * Contrôle de la fiche
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_fiche.php';
			configuration['delai_tentative'] = 15000;							// 15 secondes
			configuration['max_tentative'] = 5;
			configuration['type_retour'] = false;								// ResponseText
			if(verification_lancement == true)
			{
				configuration['param'] = 'action=7&xml=true&ssid='+ssid;		// Vérification lancement
			}
			else
			{
				configuration['param'] = get_elements_fiche(6)+'&xml=true';		// Vérification générale
			}
			
			configuration['fonction_a_executer_reponse'] = 'sheet_control';
			configuration['param_fonction_a_executer_reponse'] = verification_lancement+",''";
			configuration['fonction_a_executer_cas_non_reponse'] = 'close_msgbox';	

			ajax_call(configuration);
			/**==================================================================*/		
		}
		else
		{
			
			// Retour ajax de la vérification
			close_msgbox();	
			
			if(verification_lancement == false)
			{
				// Affichage de la dernère action
				texte_action = get_lib(347);
				iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1\');iknow_panel_reduire();" class="informations"></a></td><td>'+texte_action+'</td>');
			}
			else
			{
				//ctrl_free_cookie('lib_erreur',true);
			}
			
			// Transformation du retour XML en JSON
			try 
			{
				reponse_json = get_json(reponse_controle); 
			} 
			catch(e) 
			{
				alert(reponse_controle);
				return false;
			}
			
			
			// Génération du bandeau d'informations
			if(typeof(reponse_json.parent.titre_controle) == 'string')
			{
				eval(reponse_json.parent.eval_js);
				if(reponse_json.parent.debug == 'false' || typeof(reponse_json.parent.debug) == 'undefined')
				{
					iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.message_controle),decodeURIComponent(reponse_json.parent.titre_controle));
				}
				else
				{
					// Debug
					iknow_panel_set_cts(decodeURIComponent(reponse_controle),'DEBUG'); 	//DEBUG
				}
				
				if(verification_lancement == false)
				{
					// Affichage des étapes
					cancel_vimofy_lien();
					
					document.getElementById('mesetapes').innerHTML = verif_generale_reponse_etapes;		
					verif_generale_reponse_etapes = '';
					iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
					document.getElementById('mesetapes').scrollTop = verif_generale_pos_scroll; 
					if(reponse_json.parent.niveau_erreur > 1)
					{
						blink_error_msg_start();
					}
				}
				else
				{		
					if(reponse_json.parent.niveau_erreur > 0)
					{
						// Affichage de la fenêtre d'informations
						iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
						if(reponse_json.parent.niveau_erreur > 1)
						{
							blink_error_msg_start();
						}
					}
				}
			}
		}						
	}
}

/************************************************************************************************************************************************
*
*  ACCESSEUR DE LIBELLE
* 
*************************************************************************************************************************************************/


function get_libelle_pole()
{
	id_pole = document.getElementById('lst_vimofy2_pole_lmod').value;
	/**==================================================================
	 * On récupère le libellé du pole id_pole
	 ====================================================================*/	
	var configuration = new Array();	
		
	configuration['page'] = 'ajax/ifiche/actions_fiche.php';
	configuration['div_a_modifier'] = 'pole_lib';
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 5;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=17&ssid="+ssid+"&id_pole="+id_pole;
	
	ajax_call(configuration);
	/**==================================================================*/			
}


function get_libelle_activite()
{
	var id_activite = document.getElementById('lst_vimofy2_activite_lmod').value;
	var id_pole = document.getElementById('lst_vimofy2_pole_lmod').value;
	/**==================================================================
	 * On récupère le libellé de l'activité
	 ====================================================================*/	
	var configuration = new Array();	
		
	configuration['page'] = 'ajax/ifiche/actions_fiche.php';
	configuration['div_a_modifier'] = 'activite_lib';
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 5;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=18&ssid="+ssid+"&id_pole="+id_pole+"&id_activite="+id_activite;
		
	ajax_call(configuration);
	/**==================================================================*/		
	
}

function get_libelle_module()
{
	var id_module = document.getElementById('lst_vimofy2_module_lmod').value;
	var id_pole = document.getElementById('lst_vimofy2_pole_lmod').value;
	
	/**==================================================================
	 * On récupère le libellé de l'activité
	 ====================================================================*/	
	var configuration = new Array();	
		
	configuration['page'] = 'ajax/ifiche/actions_fiche.php';
	configuration['div_a_modifier'] = 'module_lib';
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 5;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=19&ssid="+ssid+"&id_pole="+id_pole+"&id_module="+id_module;

	ajax_call(configuration);
	/**==================================================================*/		
	
}

function maj_nbr_param(p_id,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		/**==================================================================
		 * CALCUL DU NOMBRE DE PARAM
		 ====================================================================*/	
		var configuration = new Array();
		
		configuration['page'] = 'ajax/ifiche/actions_etapes.php';
		configuration['delai_tentative'] = 5000;	// 5 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=31&objet="+p_id+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'maj_nbr_param';
		configuration['param_fonction_a_executer_reponse'] = "'"+p_id+"'";
		
		ajax_call(configuration);
		/**==================================================================*/
	}
	else
	{
		switch(p_id) 
		{
			case 'vimofy_liste_param':
				document.getElementById('onglet_nbr_varin').innerHTML = reponse;
				break;
			case 'vimofy_lst_tag_objassoc':
				document.getElementById('nbr_tag').innerHTML = reponse;
				break;	
			case 'vimofy_infos_recuperees':
				document.getElementById('onglet_nbr_varout').innerHTML = reponse;
				break;	
			default:
				return 0;
			break;
		}
	}
}

/************************************************************************************************************************************************
*
*  VIMOFY DE LA PAGE D'ENTETE
* 
*************************************************************************************************************************************************/

function load_vimofy_versions_poles(vimofy_id,p_input_value,p_open_lmod,display,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = "includes/ifiche/vimofy/edit/init_liste_vers_poles_lmod_vim2_edit.php";
		conf['delai_tentative'] = 2000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = "ssid="+ssid+"&pole="+encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
		conf['fonction_a_executer_reponse'] = 'load_vimofy_versions_poles';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+p_input_value+"',"+p_open_lmod+","+display;
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			// Set vimofy html
			document.getElementById('vimofy_version_emplacement').innerHTML = ajax_return;
			
			if(display == false)
			{
				document.getElementById('vimofy_version_emplacement').style.display = 'none';
			}
			else
			{
				document.getElementById('vimofy_version_emplacement').style.display = 'block';
			}
			
			if(p_input_value != 'null')
			{
				if(document.getElementById('lst_vimofy2_vers_pole_lmod'))
				{
					document.getElementById('lst_vimofy2_vers_pole_lmod').value = p_input_value;
				}
				document.getElementById('version_lib').innerHTML = p_input_value;
			}
			else
			{
				document.getElementById('version_lib').innerHTML = '';
			}
			
			if(p_open_lmod == true)
			{
				vimofy_lmod_click(vimofy_id);
			}
		} 
		catch(e) 
		{
			alert(e.message+'\n'+vimofy_id);
		}
	}
}

function load_vimofy_activites(vimofy_id,p_input_value,p_open_lmod,display,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = "includes/ifiche/vimofy/edit/init_liste_activite_lmod_vim2_edit.php";
		conf['delai_tentative'] = 2000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = "ssid="+ssid+"&pole="+encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
		conf['fonction_a_executer_reponse'] = 'load_vimofy_activites';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+p_input_value+"',"+p_open_lmod+","+display;
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			document.getElementById('vimofy_activite_emplacement').innerHTML = ajax_return;
			if(display == false)
			{
				document.getElementById('vimofy_activite_emplacement').style.display = 'none';
			}
			else
			{
				document.getElementById('vimofy_activite_emplacement').style.display = 'block';
			}
			
			if(p_input_value != 'null' && p_input_value != '')
			{
				document.getElementById('lst_vimofy2_activite_lmod').value = p_input_value;
				get_libelle_activite();
			}
			else
			{
				document.getElementById('activite_lib').innerHTML = '';
			}
			
			if(p_open_lmod == true)
			{
				vimofy_lmod_click(vimofy_id);
			}
		} 
		catch(e) 
		{
			alert(e.message+vimofy_id);
			//vimofy_display_error(vimofy_id,e);
		}
	}
}


function load_vimofy_modules(vimofy_id,p_input_value,p_open_lmod,display,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		conf['page'] = "includes/ifiche/vimofy/edit/init_liste_module_lmod_vim2_edit.php";
		conf['delai_tentative'] = 2000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = "ssid="+ssid+"&pole="+encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
		conf['fonction_a_executer_reponse'] = 'load_vimofy_modules';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+p_input_value+"',"+p_open_lmod+","+display;
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			document.getElementById('vimofy_module_emplacement').innerHTML = ajax_return;
			if(display == false)
			{
				document.getElementById('vimofy_module_emplacement').style.display = 'none';
			}
			else
			{
				document.getElementById('vimofy_module_emplacement').style.display = 'block';
			}
			
			if(p_input_value != 'null')
			{
				document.getElementById('lst_vimofy2_module_lmod').value = p_input_value;
				get_libelle_module();
			}
			else
			{
				document.getElementById('module_lib').innerHTML = '';
			}
			
			if(p_open_lmod == true)
			{
				vimofy_lmod_click(vimofy_id);
			}
		} 
		catch(e) 
		{
			alert(e.message+vimofy_id);
			//vimofy_display_error(vimofy_id,e);
		}
	}
}

function check_trigramme(p_input)
{
	if(p_input.value.length == 3)
	{
		p_input.style.backgroundColor = '#FFF';
	}
	else
	{
		p_input.style.backgroundColor = '#FF866A';
	}
}

function check_title(p_input)
{
	if(p_input.value.length >= conf[9])
	{
		p_input.style.backgroundColor = '#FFF';
	}
	else
	{
		p_input.style.backgroundColor = '#FF866A';
	}
}

