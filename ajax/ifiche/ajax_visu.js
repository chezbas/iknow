/************************************************************************************************************
*						Fonctions AJAX pour les fiches en visualisation				
*************************************************************************************************************/

/**
 * Recharge la page et donc genere les varin qui ont été définies dans l'url
 * Uniquement si il y a eu des modifications
 */
function charger_var_dans_url(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		if(maj_vimofy_param == true)
		{
			maj_vimofy_param = false;
			
			// On affiche le message d'attente
			generer_msgbox('',get_lib(203),'','wait');
			/**==================================================================
			 * CHARGEMENT DES VARIN DANS L'URL
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_fiche.php';
			configuration['delai_tentative'] = 12000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=8&url="+encodeURIComponent(document.location.href)+"&version="+document.getElementById('lst_vimofy_version_fiche').value+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'charger_var_dans_url';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
	}
	else
	{
	   	if(reponse != '')
	   	{
			// Rechargement de la page
			window.location.replace(reponse);
	
		}
	}	
}


/**
 * Recharge la page avec la nouvelle version spécifiée
 */
function changer_version(reponse)
{
	if(version_fiche != document.getElementById('lst_vimofy_version_fiche').value)
	{
		if(typeof(reponse) == 'undefined')
		{	
			/**==================================================================
			 * CHANGEMENT DE VERSION
			 ====================================================================*/	
			var configuration = new Array();	
			
			configuration['page'] = 'ajax/ifiche/actions_fiche.php';
			configuration['delai_tentative'] = 12000;
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;		// ReponseText
			configuration['param'] = "action=13&url="+encodeURIComponent(document.location.href)+"&version="+document.getElementById('lst_vimofy_version_fiche').value+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'changer_version';
			
			ajax_call(configuration);
			/**==================================================================*/	
		}
		else
		{
			if(reponse != '')
			{
				/**==================================================================
				 * Suppression du cookie de session du navigateur
				 ====================================================================*/	
				//delete_cookie(ssid);
				/**==================================================================*/	
				
				/**==================================================================
				 * Rechargement de la page
				 ====================================================================*/	
				window.location.replace(reponse);
				/**==================================================================*/	
			}
		}
	}
}



/*********************************************************************************
 * Change les valeurs des varin par les valeurs par défaut et/ou neutre.
 *********************************************************************************/
function change_defaut_values(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		if(state_change_defaut_values == false)
		{
			// turn on default values
			state_change_defaut_values = true;
		
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/ifiche/actions_fiche.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=21&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_defaut_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/	
			
			// Log action
			texte_action = get_lib(366);
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1\');" class="informations"></a></td><td>'+texte_action+'</td>');
		}	
		else
		{
			// turn off default values
			state_change_defaut_values = false;
			
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/ifiche/actions_fiche.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=22&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_defaut_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/
			
			// Log action
			texte_action = get_lib(367);
			iknow_panel_set_action(texte_action,'<td><a href="#" onclick="javascript:tabbar.setTabActive(\'tab-level1\');" class="informations"></a></td><td>'+texte_action+'</td>');
		}
	}
	else
	{
	    //Rechargement de la page
		maj_vimofy_param = true;
		charger_var_dans_url();
	}	
}


function change_neutral_values(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		if(state_change_neutral_values == false)
		{
			// ACTIVATION DES VALEURS NEUTRES
			state_change_neutral_values = true;

			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/ifiche/actions_fiche.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=23&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_neutral_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/	
		}	
		else
		{
			// SUPPRESSION DES VALEURS NEUTRES
			state_change_neutral_values = false;
	
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/ifiche/actions_fiche.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=24&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_neutral_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/	
		}
	}
	else
	{
	    //Rechargement de la page
		maj_vimofy_param = true;
		charger_var_dans_url();
	}
}

/**
 * @param id - Identifiant de la varin
 * @param type - neutre ou defaut
 */
function set_default_neutre_value(type,id,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		
		// On affiche le message d'attente
		generer_msgbox('',get_lib(65),'','wait');
			
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
					
		configuration['page'] = "ajax/ifiche/actions_fiche.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=25&id="+id+"&type="+type+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'set_default_neutre_value';
		configuration['param_fonction_a_executer_reponse'] = "'"+type+"',"+id;
		
		ajax_call(configuration);
		/**==================================================================*/		
		
	}	
	else
	{
	    //Rechargement de la page
		maj_vimofy_param = true;
		charger_var_dans_url();
	}
}

/**
 * Efface les valeurs des parametres dans les varin's
 */
function effacer_param_url(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(435),'','wait');
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/ifiche/actions_fiche.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=32&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'effacer_param_url';
		
		ajax_call(configuration);
		/**==================================================================*/	
	}
	else
	{
		maj_vimofy_param = true;
		charger_var_dans_url();	
	}	
}

function ctrl_coherence_last_backup()
{
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/ifiche/check.php";
	configuration['delai_tentative'] = 200000;		// 200 secondes
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=24&ssid="+ssid+"&object=__IFICHE__&id="+ID_fiche;
	
	ajax_call(configuration);
	/**==================================================================*/		
}



function check_global_coherence_end(ajax_return)
{
	if(typeof(ajax_return) == 'undefined')
	{
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/ifiche/actions_fiche.php";
		configuration['delai_tentative'] = 5000;		// 200 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=33&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'check_global_coherence_end';
		
		ajax_call(configuration);
		/**==================================================================*/		
	}	
	else
	{
		eval(ajax_return);
		if(ajax_json.end_check != false)
		{
			clearInterval(ctrl_coherence_last_backup_timer);
			
			
			if(ajax_json.qtt_err > 0)
			{
				if(ajax_json.qtt_err == 1)
				{
					aff_btn = new Array([get_lib(182)],["close_msgbox();"]);
					generer_msgbox(decodeURIComponent(libelle_common[15]),decodeURIComponent(libelle_common[14])+' <a href="outils/coherent_check/detail.php?ssid='+ajax_json.ssid_object_check+'&iobject='+ajax_json.type_object+'&id='+ajax_json.id_object+'" target="_blank">Detail</a>','warning','msg',aff_btn);
					document.getElementById('iknow_ctrl_in_progress').innerHTML = decodeURIComponent(libelle_common[14])+' <a href="outils/coherent_check/detail.php?ssid='+ajax_json.ssid_object_check+'&iobject='+ajax_json.type_object+'&id='+ajax_json.id_object+'" target="_blank">Detail</a>';
				}
				else
				{
					aff_btn = new Array([get_lib(182)],["close_msgbox();"]);
					generer_msgbox(decodeURIComponent(libelle_common[15]),decodeURIComponent(libelle_common[13]).replace('$x',ajax_json.qtt_err)+' <a href="outils/coherent_check/detail.php?ssid='+ajax_json.ssid_object_check+'&iobject='+ajax_json.type_object+'&id='+ajax_json.id_object+'" target="_blank">Detail</a>','warning','confirm',aff_btn);
					document.getElementById('iknow_ctrl_in_progress').innerHTML = decodeURIComponent(libelle_common[13]).replace('$x',ajax_json.qtt_err)+' <a href="outils/coherent_check/detail.php?ssid='+ajax_json.ssid_object_check+'&iobject='+ajax_json.type_object+'&id='+ajax_json.id_object+'" target="_blank">Detail</a>';
				}
				blink_error_msg_start('iknow_ctrl_in_progress');
			}
			else
			{
				document.getElementById('iknow_ctrl_in_progress').style.display = 'none';
			}
		}
	}
}