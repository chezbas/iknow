var icode_sauvegarde = false;

/**
 * Met à jour la table des locks pour voir que l'utilisateur est tjr sur le code.
 * Récupère les message en base de données si il y en a.
 * @param reponse : interne, appelé lors du retour ajax de la verification
 * @return
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
		configuration['param'] = "ssid="+ssid+"&id="+ID_code+"&id_temp="+ID_temp+"&type_action="+application;
		configuration['fonction_a_executer_reponse'] = 'signal_presence';
		   		
		ajax_call(configuration);
		/**==================================================================*/		
	}
	else
	{
		//ctrl_free_cookie('lib_erreur');
		if(reponse != '')
		{
			// Transformation du retour XML en JSON
			var reponse_json = get_json(reponse); 
			
			// Génération du bandeau d'informations
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

function valide_message_maintenance()
{
	/**==================================================================
	 * Validation du message de maintenance
	 ====================================================================*/	
	var configuration = new Array();	
			
	configuration['page'] = "ajax/icode/actions.php";
	configuration['delai_tentative'] = 5000;		// 5 secondes
	configuration['max_tentative'] = 4;
	configuration['type_retour'] = false;			// ResponseText		
	configuration['param'] = "action=2&ssid="+ssid;
   		
	ajax_call(configuration);
	/**==================================================================*/	   
}

function set_tabbar_actif(tab_actif)
{
	if(bloquer_pulse_tab_actif == false)
	{
		/**==================================================================
		 * Définition de l'onglet actif
		 * ATTENTION LAISSER EN SYNCHRONE sinon le navigateur n'attend pas 
		 * le retour et recharge directement a page
		 ====================================================================*/		
		var xhr = new XMLHttpRequest();
		xhr.open("POST","ajax/icode/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		xhr.send("action=3&ssid="+ssid+"&tab_actif="+tab_actif);
		/**==================================================================*/	
	}  
}

/**
 * Tue la session en cours, en supprimant de la base tout ce qui concerne notre id temporaire
 */
function tuer_session()
{
	/**==================================================================
	 * Kill de la session 
	 * ATTENTION LAISSER EN SYNCHRONE sinon le navigateur n'attend pas 
	 * le retour et quitte la page lors d'un rechargement, et donc la fiche
	 * reste en modification.
	 ====================================================================*/		
	icode_sauvegarde = true;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST","ajax/icode/actions.php",false);
	xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xhr.send("action=17&ssid="+ssid);
	/**==================================================================*/	  	
}