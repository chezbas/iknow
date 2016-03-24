<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	require('../../class/common/class_bdd.php');  // classe bdd
	require('../../class/icode/class_code.php');  // on donne le lien vers la classe fiche
	require('../../class/common/class_lock.php'); // on donne le lien vers la classe lock
	$dir_obj = '';
	require('../../vimofy/vimofy_includes.php');

	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	


	switch($_POST['action']) {
			
		case 1:
			// Changement de version
			echo $_SESSION[$_POST['ssid']]['objet_icode']->changer_version($_POST['url'],$_POST['version']);
			break;
		case 2:
			//echo $_SESSION[$_POST['ssid']]['objet_icode']->valide_message_maintenance();
			break;
		case 3:
			//Lors du click sur un onglet on met à jour l'onglet actif dans l'objet
			 $_SESSION[$_POST['ssid']]['objet_icode']->set_tab_actif($_POST['tab_actif']);
			break;
		case 4:
			//Uniquement en affichage: On recharge la page si il y a de nouvelles varin de definies (pour regenerer l'url)
			echo $_SESSION[$_POST['ssid']]['objet_icode']->charger_var_dans_url($_POST['mode'],$_GET);
			break;
		case 5:
			//Attribution des valeurs neutres aux varin
			echo $_SESSION[$_POST['ssid']]['objet_icode']->set_neutral_values();
			break;
		case 6:
			//Annume les valeurs neutres des varin
			echo $_SESSION[$_POST['ssid']]['objet_icode']->unset_neutral_values();
			break;
		case 7:
			//Attribution des valeurs par défaut aux varin
			echo $_SESSION[$_POST['ssid']]['objet_icode']->set_default_values();
			break;
		case 8:
			//Annule les valeurs par défaut des varin
			echo $_SESSION[$_POST['ssid']]['objet_icode']->unset_default_values();
			break;
		case 9:
			// Sauvegarde de l'iCODE 																												
			echo $_SESSION[$_POST['ssid']]['objet_icode']->sauvegarder_icode($_POST['titre'],$_POST['descriptif'],$_POST['pole'],$_POST['version'],$_POST['activite'],$_POST['auteur'],$_POST['moteur'],$_POST['engine_version'],$_POST['corps'],$_POST['prefixe'],$_POST['postfixe'],$_POST['bloquer']);
			break;
		case 10:
			// Récupère la designation d'un moteur par rapport à son ID
			echo $_SESSION[$_POST['ssid']]['objet_icode']->get_libelle_moteur($_POST['id_moteur']);
			break;
		case 11:
			// Récupère la designation d'un pole par rapport à son ID
			echo $_SESSION[$_POST['ssid']]['objet_icode']->get_libelle_pole($_POST['id_pole']);
			break;
		case 12:
			// Récupère la designation de l'activité par rapport à son ID et l'id de son pole
			echo $_SESSION[$_POST['ssid']]['objet_icode']->get_libelle_activite($_POST['id_activite'],$_POST['id_pole']);
			break;
		case 13:
			// Controle le iCode
			echo $_SESSION[$_POST['ssid']]['objet_icode']->controler_icode($_POST['titre'],$_POST['descriptif'],$_POST['pole'],$_POST['version'],$_POST['activite'],$_POST['auteur'],$_POST['moteur'],$_POST['engine_version'],$_POST['corps']);
			break;
		case 14:
			// Met à jour le préfixe (uniquement en modif)
			echo $_SESSION[$_POST['ssid']]['objet_icode']->maj_prefixe($_POST['prefixe']);
			break;
		case 15:
			// Met à jour le postfixe (uniquement en modif)
			echo $_SESSION[$_POST['ssid']]['objet_icode']->maj_postfixe($_POST['postfixe']);
			break;
		case 16:

			break;
		case 17:
			// Tue la session
			echo $_SESSION[$_POST['ssid']]['objet_icode']->cancel_modif();
			die();
			break;		
		case 18:
			// EN VISU: Supprimer les valeurs des parametres
			echo $_SESSION[$_POST['ssid']]['objet_icode']->delete_value_param();
			break;
		case 19:
			// Verification du pole du code
			echo $_SESSION[$_POST['ssid']]['objet_icode']->verif_lancement(true);
			break;
		case 20:
			// Verification du pole du code
			echo $_SESSION[$_POST['ssid']]['objet_icode']->get_level_erreur();
			break;
		case 21:
			// Verification du pole du code
			echo $_SESSION[$_POST['ssid']]['objet_icode']->dupliquer_icode();
			break;
		case 22:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$_POST['ssid']]['objet_icode']->set_default_neutre_value($_POST['type'],$_POST['id']);
			break;
		case 23:
			// Calcul du nombre de parametres (varin ou varout ou tag) d'entête
			echo $_SESSION[$_POST['ssid']]['objet_icode']->maj_nbr_param($_POST['objet']);
			break;
		case 24:
			// Check global cohrence from last backup
			echo $_SESSION[$_POST['ssid']]['objet_icode']->check_global_coherence_from_last_backup();
			break;
		case 25:
			// Check global cohrence from last backup is ended
			echo $_SESSION[$_POST['ssid']]['objet_icode']->check_global_coherence_end();
			break;
	}
?>