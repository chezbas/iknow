/**
 * Verification de base, verifie le pole ainsi que la version de la fiche.
 * Si l'un et l'autre sont ok alors on met un message d'informations.
 * Cette fonction est executée au lancement de la page.
 */
function verification_lancement(reponse)
{		
	if(typeof(reponse) == 'undefined')
	{
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
		
		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 1000;		// 4 secondes
		configuration['max_tentative'] = 2;
		configuration['type_retour'] = false;			// ReponseText
		configuration['param'] = "action=19&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'verification_lancement';
		ajax_call(configuration);
		/**==================================================================*/	
	}
	else
	{
		//ctrl_free_cookie('lib_erreur',true);
		// Retour ajax
		
		// Transformation du retour XML en JSON
		var reponse_json = get_json(reponse); 
		
		// Génération du bandeau d'informations
		if(typeof(reponse_json.parent.titre_controle) == 'string')
		{
			iknow_panel_set_cts(decodeURIComponent(reponse_json.parent.message_controle),decodeURIComponent(reponse_json.parent.titre_controle));
			
			if(reponse_json.parent.niveau_erreur > 0)
			{
				// Affichage de la fenêtre d'informations
				iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
			}
		}
	}
}	

function maj_nbr_param(p_id,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * CALCUL DU NOMBRE DE PARAM
		 ====================================================================*/	
		var configuration = new Array();
		
		configuration['page'] = 'ajax/icode/actions.php';
		configuration['delai_tentative'] = 5000;	// 5 secondes
		configuration['max_tentative'] = 3;
		configuration['type_retour'] = false;		// ReponseText
		configuration['param'] = "action=23&objet="+p_id+"&ssid="+ssid;
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
				document.getElementById('onglet_nbr_varin').innerHTML = ajax_return;
				break;
			case 'vimofy_lst_tag_objassoc':
				document.getElementById('nbr_tag').innerHTML = ajax_return;
				break;	
			case 'vimofy_infos_recuperees':
				document.getElementById('onglet_nbr_varout').innerHTML = ajax_return;
				break;	
			default:
				return 0;
			break;
		}
	}
}

function cancel_modif(url,reponse)
{
	
    if(typeof(reponse) == 'undefined')
    {
		aff_btn = new Array([get_lib(182),get_lib(181)],["cancel_modif('"+url+"','yes');","close_msgbox();"]);
    	generer_msgbox(get_lib(58),get_lib(57),'question','msg',aff_btn);
    }
    else
    {
    	if (reponse == 'yes')
	    {
			tuer_session();
			window.location.replace(url);
	    }
    }		
}


/**
 * Appelée uniquement en modif, met à jour le préfixe du code lorsque l'utilisateur le modifie
 * 
 **/
function maj_prefixe(prefixe)
{
	// Si il n'y a pas de préfixe alors on en fixe un à &
	if(prefixe == '')
	{
		prefixe = '&';
		document.getElementById('prefixe').value = prefixe;
	}
	
	prefixe = encodeURIComponent(prefixe);
	
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/icode/actions.php";
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=14&ssid="+ssid+"&prefixe="+prefixe;
	   		
	ajax_call(configuration);
	/**==================================================================*/	

	document.getElementById('rappel_format_var').innerHTML = '<span class="prefixe">'+document.getElementById('prefixe').value+'</span>xxxx<span class="postfixe">'+document.getElementById('postfixe').value+'</span>';
	iknow_panel_set_action(decodeURIComponent(libelle[106]));
}

/**
 * Appelée uniquement en modif, met à jour le postfixe du code lorsque l'utilisateur le modifie
 * 
 **/
function maj_postfixe(postfixe)
{
	postfixe = encodeURIComponent(document.getElementById('postfixe').value);
	
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/icode/actions.php";
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=15&ssid="+ssid+"&postfixe="+postfixe;
	   		
	ajax_call(configuration);
	/**==================================================================*/	
   	
	document.getElementById('rappel_format_var').innerHTML = '<span class="prefixe">'+document.getElementById('prefixe').value+'</span>xxxx<span class="postfixe">'+document.getElementById('postfixe').value+'</span>';
	iknow_panel_set_action(decodeURIComponent(libelle[107]));
}
/** 
 * Sauvegarde de l'icode
 */
function sauvegarder_icode(bloquer,reponse)
{
	// Affichage du message de sauvegarde
	// changer_message_msgbox(get_lib(67));
	
	if(typeof(bloquer) == "undefined")
	{	
		var bloquer = 0;
	}
	
	if(typeof(reponse) == 'undefined')
	{
		// SAUVEGARDE DU CODE		
		
		var titre = encodeURIComponent(document.getElementById('titre').value);
		var descriptif = encodeURIComponent(tinyMCE.get('Descriptif').getContent());
			
		var pole = encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);	
		var version = encodeURIComponent(document.getElementById('lst_vimofy2_vers_pole_lmod').value);	
		var activite = encodeURIComponent(document.getElementById('lst_vimofy2_activite_lmod').value);

		var auteur = encodeURIComponent(document.getElementById('auteur').value);
		var moteur = encodeURIComponent(document.getElementById('lst_vimofy_moteur').value);
		var engine_version  = encodeURIComponent(document.getElementById('lst_vimofy_vers_moteur').value);
		
		var corps = encodeURIComponent(document.getElementById('textarea_code').value);
		var prefixe = encodeURIComponent(document.getElementById('prefixe').value);
		var postfixe = encodeURIComponent(document.getElementById('postfixe').value);
		
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=9&ssid="+ssid+"&titre="+titre+"&descriptif="+descriptif+"&pole="+pole+"&version="+version+"&activite="+activite+"&auteur="+auteur+"&moteur="+moteur+"&engine_version="+engine_version+"&corps="+corps+"&prefixe="+prefixe+"&postfixe="+postfixe+'&bloquer='+bloquer;
		configuration['fonction_a_executer_reponse'] = 'sauvegarder_icode';
		configuration['param_fonction_a_executer_reponse'] = bloquer;
		
		ajax_call(configuration);
		/**==================================================================*/		
	}
	else
	{
		try
		{
			var reponse_json = get_json(reponse); 
			
			if(typeof(reponse_json.parent.url) == 'string')
			{
				icode_sauvegarde = true;
				//window.open(reponse_json.parent.url_ctrl);
				window.location.replace(reponse_json.parent.url);
			}
		}
		catch(e)
		{
			// TODO: handle exception
			document.body.innerHTML = reponse;
		}
	}
}

/** 
 * Contrôle de l'icode
 * retourne true si il n'y a pas d'erreur, sinon false.
 * @param sauvegarde / si false contrôle juste, si true contrôle puis sauvegarde
 */
function controler_icode(sauvegarde,bloquer,reponse)
{
	if(typeof(reponse) =='undefined')
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(66),'','wait');
		
		var titre = encodeURIComponent(document.getElementById('titre').value);
		var descriptif = encodeURIComponent(tinyMCE.get('Descriptif').getContent());
		
		if(maj_pole == true)
		{
			var pole = encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
			if(document.getElementById('lst_vimofy2_vers_pole_lmod'))
			{
				var version = encodeURIComponent(document.getElementById('lst_vimofy2_vers_pole_lmod').value);	
			}
			else
			{
				var version = "";	
			}
	
			if(document.getElementById('lst_vimofy2_activite_lmod'))
			{
				var activite = encodeURIComponent(document.getElementById('lst_vimofy2_activite_lmod').value);
			}
			else
			{
				var activite = "";
			}
		}
		else
		{
			var pole = encodeURIComponent(document.getElementById('lst_vimofy2_pole_lmod').value);
			var version = encodeURIComponent(document.getElementById('lst_vimofy2_vers_pole_lmod').value);	
			var activite = encodeURIComponent(document.getElementById('lst_vimofy2_activite_lmod').value);	
		}
		
		var auteur = encodeURIComponent(document.getElementById('auteur').value);
		var moteur = encodeURIComponent(document.getElementById('lst_vimofy_moteur').value);
		if(document.getElementById('lst_vimofy_vers_moteur'))
		{
			var engine_version  = encodeURIComponent(document.getElementById('lst_vimofy_vers_moteur').value);
		}
		else
		{
			var engine_version  = '';
		}
		var corps = encodeURIComponent(document.getElementById('textarea_code').value);
		
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=13&ssid="+ssid+"&titre="+titre+"&descriptif="+descriptif+"&pole="+pole+"&version="+version+"&activite="+activite+"&auteur="+auteur+"&moteur="+moteur+"&engine_version="+engine_version+"&corps="+corps;
		configuration['fonction_a_executer_reponse'] = 'controler_icode';
		configuration['param_fonction_a_executer_reponse'] = sauvegarde+','+bloquer;
		
		ajax_call(configuration);
		/**==================================================================*/		
	}
	else
	{
		// Transformation du retour XML en JSON
		var reponse_json = get_json(reponse); 
		// Génération du bandeau d'informations
		if(typeof(reponse_json.parent.titre_controle) == 'string')
		{
			iknow_panel_set_cts(decodeURIComponent(get_json(reponse).parent.message_controle),decodeURIComponent(get_json(reponse).parent.titre_controle));
			//iknow_panel_set_cts(decodeURIComponent(reponse)); // DEBUG
			
			if(get_json(reponse).parent.niveau_erreur > 0)
			{
				// Affichage de la fenêtre d'informations
				iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
				// Masquage de la fenêtre d'attente
				close_msgbox();	
				if(get_json(reponse).parent.niveau_erreur > 1)
				{
					blink_error_msg_start();
					eval(get_json(reponse).parent.eval_js);
					return false;
				}
				else
				{
					// Sauvegarde du code
					if(sauvegarde == true)
					{
						if(bloquer == true)
						{
							sauvegarder_icode(true);
						}
						else
						{
							sauvegarder_icode(false);
						}
					}
					else
					{
						close_msgbox();
					}
				}
			}
			else
			{
				// Sauvegarde du code
				if(sauvegarde == true)
				{
					if(bloquer == true)
					{
						sauvegarder_icode(true);
					}
					else
					{
						sauvegarder_icode(false);
					}
				}
				else
				{
					iknow_ellapse_el('iknow_ctrl_container','iknow_ctrl_internal_container');
					close_msgbox();
				}
			}
		}	
	}
}

function dupliquer_icode(reponse_ok,reponse)
{
	if(typeof(reponse_ok) == "undefined")
	{
		var aff_btn = new Array([get_lib(182),get_lib(181)],["dupliquer_icode(true);","close_msgbox();"]);
    	generer_msgbox(get_lib(25),get_lib(216),'question','msg',aff_btn); 
	}
	else
	{
		close_msgbox();
		if(typeof(reponse) == 'undefined')
		{
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 4000;		// 4 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ReponseText
			configuration['param'] = "action=21&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'dupliquer_icode';
			configuration['param_fonction_a_executer_reponse'] = reponse_ok;
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
		{
			// Retour ajax
			document.title=(get_lib(210));
			iknow_panel_set_cts(reponse);
			iknow_toggle_control();
			var ID_fiche_last = ID_code;
			ID_code = 'new';
			document.getElementById('id_code').innerHTML = ID_temp;
			document.getElementById('version_code').innerHTML = 0;
			
			iknow_panel_set_action(get_lib(108).replace('$x',ID_fiche_last));
			document.getElementById('toolbar_btn_dupliq').style.display = 'none';
			document.getElementById('toolbar_btn_lock').style.display = 'none';
		}	
	}
}

/************************************************************************************************************************************************
*
*  GETTER DE LIBELLE
* 
*************************************************************************************************************************************************/
function get_libelle_moteur()
{
	var id_moteur = document.getElementById('lst_vimofy_moteur').value;

	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
	
	configuration['page'] = "ajax/icode/actions.php";
	configuration['div_a_modifier'] = 'moteur_lib';
	configuration['delai_tentative'] = 4000;		// 4 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=10&ssid="+ssid+"&id_moteur="+id_moteur;
	
	ajax_call(configuration);
	/**==================================================================*/	
}

function get_libelle_pole()
{	
	id_pole = document.getElementById('lst_vimofy2_pole_lmod').value;
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
	
	configuration['page'] = "ajax/icode/actions.php";
	configuration['div_a_modifier'] = 'pole_lib';
	configuration['delai_tentative'] = 4000;		// 4 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=11&ssid="+ssid+"&id_pole="+id_pole;

	ajax_call(configuration);
	/**==================================================================*/		
}

function get_libelle_activite()
{
	var id_activite = document.getElementById('lst_vimofy2_activite_lmod').value;
	var id_pole = document.getElementById('lst_vimofy2_pole_lmod').value;
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
	
	configuration['page'] = "ajax/icode/actions.php";
	configuration['div_a_modifier'] = 'activite_lib';
	configuration['delai_tentative'] = 4000;		// 4 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ReponseText
	configuration['param'] = "action=12&ssid="+ssid+"&id_pole="+id_pole+"&id_activite="+id_activite;
	
	ajax_call(configuration);
	/**==================================================================*/	
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
		
		conf['page'] = "includes/icode/vimofy/edit/init_liste_vers_poles_lmod_vim2_edit.php";
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
				document.getElementById('activite_lib').innerHTML = '';
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
		
		conf['page'] = "includes/icode/vimofy/edit/init_liste_activite_lmod_vim2_edit.php";
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
			
			if(p_input_value != 'null')
			{
				document.getElementById('lst_vimofy2_activite_lmod').value = p_input_value;
				get_libelle_activite()
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
			//alert(e.message+vimofy_id);
			//vimofy_display_error(vimofy_id,e);
		}
	}
}


function load_vimofy_engine_version(vimofy_id,p_input_value,p_open_lmod,display,ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Ajax init
		 ====================================================================*/	
		var conf = new Array();	
		
		conf['page'] = "includes/icode/vimofy/edit/init_liste_vers_moteur.php";
		conf['delai_tentative'] = 2000;
		conf['max_tentative'] = 4;
		conf['type_retour'] = false;		// ReponseText
		conf['param'] = "ssid="+ssid+"&engine="+encodeURIComponent(document.getElementById('lst_vimofy_moteur').value);
		conf['fonction_a_executer_reponse'] = 'load_vimofy_engine_version';
		conf['param_fonction_a_executer_reponse'] = "'"+vimofy_id+"','"+p_input_value+"',"+p_open_lmod+","+display;
		
		ajax_call(conf);
		/**==================================================================*/
	}
	else
	{
		try 
		{
			document.getElementById('vimofy_engine_version_emplacement').innerHTML = ajax_return;
			if(display == false)
			{
				document.getElementById('vimofy_engine_version_emplacement').style.display = 'none';
			}
			else
			{
				document.getElementById('vimofy_engine_version_emplacement').style.display = 'block';
			}
			
			if(p_input_value != 'null')
			{
				document.getElementById('lst_vimofy_vers_moteur').value = p_input_value;
				document.getElementById('engine_version_lib').innerHTML = p_input_value;
			}
			else
			{
				document.getElementById('engine_version_lib').innerHTML = '';
			}
			
			if(p_open_lmod == true)
			{
				vimofy_lmod_click(vimofy_id);
			}
		} 
		catch(e) 
		{
			//alert(e.message+vimofy_id);
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
	if(p_input.value.length >= conf[22])
	{
		p_input.style.backgroundColor = '#FFF';
	}
	else
	{
		p_input.style.backgroundColor = '#FF866A';
	}
}

function check_description()
{
	var p_input = document.getElementById('Descriptif_ifr').contentWindow.tinymce;
	if(p_input.innerHTML.length >= conf[23])
	{
		p_input.style.backgroundColor = '#FFF';
	}
	else
	{
		p_input.style.backgroundColor = '#FF866A';
	}
}