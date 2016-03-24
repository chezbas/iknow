/*********************************************************************************
 * Recharge la page et donc genere les varin qui ont été définies dans l'url
 * Uniquement si il y a eu des modifications
 *********************************************************************************/
/**
 * @param mode - false: supprime IK_VALMOD de l'url, true : ne supprime pas IK_VALMOD de l'URL
 */
function charger_var_dans_url(mode,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		if(maj_vimofy_param == true)
		{
			maj_vimofy_param = false;

			// On affiche le message d'attente
			generer_msgbox('',get_lib(65),'','wait');
			
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=4&mode="+mode+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'charger_var_dans_url';
			configuration['param_fonction_a_executer_reponse'] = mode;
			
			ajax_call(configuration);
			/**==================================================================*/		
		}
	}	
	else
	{
		// Retour ajax
	   	if(reponse != '')
	   	{
			window.location.replace(reponse);
		}
	   	else
	   	{
	   		close_msgbox();	
	   	}
	}
}


function ctrl_coherence_last_backup()
{
	/**==================================================================
	 * Configuration de l'appel Ajax
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/icode/check.php";
	configuration['delai_tentative'] = 200000;		// 200 secondes
	configuration['max_tentative'] = 2;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=24&ssid="+ssid+"&object=__ICODE__&id="+ID_code;
	
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
				
		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 5000;		// 200 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=25&ssid="+ssid;
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

/*********************************************************************************
 * Change les valeurs des varin par les valeurs par défaut et/ou neutre.
 *********************************************************************************/
function change_defaut_values(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(65),'','wait');
		
		if(state_change_defaut_values == false)
		{
			// MET A ON
			state_change_defaut_values = true;
		
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=7&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_defaut_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/		
		}	
		else
		{
			// MET A OFF
			state_change_defaut_values = false;
			//document.getElementById('btn_replace_defaut').className = 'btn_replace_defaut_off';		
			
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=8&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_defaut_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/		
		}
	}
	else
	{
	    //Rechargement de la page
		maj_vimofy_param = true;
		charger_var_dans_url(true);
	}	
}

/**
 * Efface les valeurs des parametres dans les varins
 * @param reload - true: recharge la page après le retour, false, ne recharge pas la page après le retour
 */
function delete_value_param(reload,reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(68),'','wait');
		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = new Array();	
				
		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 5000;		// 5 secondes
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=18&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'delete_value_param';
		configuration['param_fonction_a_executer_reponse'] = reload;
		
		ajax_call(configuration);
		/**==================================================================*/	
	}
	else
	{
	    //Rechargement de la vimofy
		maj_vimofy_param = true;	
		close_msgbox();
		if(reload == true)
		{
			maj_vimofy_param = true;
			charger_var_dans_url(false);	
		}
	}	
}

function change_neutral_values(reponse)
{
	if(typeof(reponse) == 'undefined')
	{
		// On affiche le message d'attente
		generer_msgbox('',get_lib(65),'','wait');
		
		if(state_change_neutral_values == false)
		{
			// ACTIVATION DES VALEURS NEUTRES
			state_change_neutral_values = true;
			
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=5&ssid="+ssid;
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
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=6&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'change_neutral_values';
			   		
			ajax_call(configuration);
			/**==================================================================*/	
		}
	}
	else
	{
	    //Rechargement de la vimofy
		maj_vimofy_param = true;	
		charger_var_dans_url(true);
	}
}

function changer_version(reponse)
{
	if(version_code != document.getElementById('lst_vimofy_version_code').value)
	{
		if(typeof(reponse) == 'undefined')
		{
			/**==================================================================
			 * Configuration de l'appel Ajax
			 ====================================================================*/	
			var configuration = new Array();	
					
			configuration['page'] = "ajax/icode/actions.php";
			configuration['delai_tentative'] = 5000;		// 5 secondes
			configuration['max_tentative'] = 4;
			configuration['type_retour'] = false;			// ResponseText		
			configuration['param'] = "action=1&url="+encodeURIComponent(document.location.href)+"&version="+document.getElementById('lst_vimofy_version_code').value+"&ssid="+ssid;
			configuration['fonction_a_executer_reponse'] = 'changer_version';
			   		
			ajax_call(configuration);
			/**==================================================================*/		
		}
		else
		{
			// Retour ajax
			if(reponse != '')
			{
				window.location.replace(reponse);
			}
		}
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
		// Display wait message
		generer_msgbox('',get_lib(65),'','wait');

		/**==================================================================
		 * Configuration de l'appel Ajax
		 ====================================================================*/	
		var configuration = [];

		configuration['page'] = "ajax/icode/actions.php";
		configuration['delai_tentative'] = 5000;		
		configuration['max_tentative'] = 4;
		configuration['type_retour'] = false;			// ResponseText		
		configuration['param'] = "action=22&id="+id+"&type="+type+"&ssid="+ssid;
		configuration['fonction_a_executer_reponse'] = 'set_default_neutre_value';
		configuration['param_fonction_a_executer_reponse'] = "'"+type+"',"+id;

		ajax_call(configuration);
		/**==================================================================*/		
	}	
	else
	{
		// Ajax return
		close_msgbox();

	    // Reload varin Vimofy
		vimofy_refresh_page_ajax('vimofy2_varin');

		maj_vimofy_param = true;
	}
}