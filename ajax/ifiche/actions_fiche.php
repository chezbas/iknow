<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../includes/common/buffering.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Load common constants
	 ====================================================================*/
	require '../../includes/common/constante.php';
	/*===================================================================*/	

	
	require('../../class/common/class_bdd.php');  			// on donne le lien vers la classe bdd
	require('../../class/ifiche/class_cartridge.php');
	require('../../class/ifiche/class_fiche.php');  		// on donne le lien vers la classe fiche
	require('../../class/ifiche/class_etape.php');  		// on donne le lien vers la classe etape
	require('../../class/ifiche/class_step.php');  		// on donne le lien vers la classe etape
	require('../../class/ifiche/class_header.php');  		// on donne le lien vers la classe etape
	require('../../class/ifiche/class_check.php');  		// on donne le lien vers la classe verification
	require('../../class/common/class_lock.php'); 			// on donne le lien vers la classe lock
	$dir_obj = '';
	require('../../vimofy/vimofy_includes.php');

	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	
	switch ($_POST['action'])
	{
		case 1:
			//Sauvegarde de la fiche
			$tab_entete = array();
			
			$tab_entete['titre'] = $_POST['titre'];
			$tab_entete['pole'] = $_POST['pole'];
			$tab_entete['version'] = $_POST['version'];
			$tab_entete['activite'] = $_POST['activite'];
			$tab_entete['idmodule'] = $_POST['idmodule'];
			$tab_entete['statut'] = $_POST['statut'];
			$tab_entete['modifpar'] = $_POST['modifpar'];
			$tab_entete['bloquer'] = $_POST['bloquer'];
			
			$_SESSION[$ssid]['objet_fiche']->save_sheet($tab_entete);
			break;
			
		case 2:
			// Duplication de la fiche
			//NR_IKNOW_5_
			echo $_SESSION[$ssid]['objet_fiche']->dupliquer_fiche($_POST['force']);
			break;
		case 3:
			break;
		case 4:
			// on verifie si la version de la fiche est la derniere
			echo $_SESSION[$ssid]['objet_fiche']->verif_version_fiche();
			break;
		case 5:
			// on genere le bandeau d'information
			echo $_SESSION[$ssid]['objet_fiche']->generer_bandeau_informations();
			break;
		case 6:
			// Initial main control when started modify mode
			echo $_SESSION[$ssid]['objet_fiche']->sheet_control($_POST['titre'],$_POST['modifpar'],$_POST['pole'],$_POST['version'],$_POST['activite'],$_POST['idmodule'],$_POST['xml']);
			break;
		case 7:
			// Main control 
			echo $_SESSION[$ssid]['objet_fiche']->sheet_control();
			break;
		case 8:
			//Uniquement en affichage: On recharge la page si il y a de nouvelles varin de definies (pour regenerer l'url)
			echo $_SESSION[$ssid]['objet_fiche']->charger_var_dans_url($_POST['url']);
			//echo $_SESSION[$ssid]['objet_fiche']->charger_var_dans_url($_GET);
			break;
		case 9:
			//Lors du click sur un onglet on met à jour l'onglet actif dans l'objet
			 $_SESSION[$ssid]['objet_fiche']->set_tab_actif($_POST['tab_haut'],$_POST['tab_entete'],$_POST['tab_etapes'],$_POST['tab_etapes_sep']);
			break;
		case 10:
			//On recupere l'onglet actif'
			echo $_SESSION[$ssid]['objet_fiche']->get_tab_actif_haut();
			break;
		case 11:
			//Mise à jour du statut de la fiche
			echo $_SESSION[$ssid]['objet_fiche']->set_statut($_POST['id_statut']);
			break;
		case 12:
			//
			echo $_SESSION[$ssid]['objet_fiche']->cancel_modif();
			//session_unset($ssid);
			die();
			break;
		case 13:
			// Changement de version
			echo $_SESSION[$ssid]['objet_fiche']->changer_version($_POST['url'],$_POST['version']);
			break;
		case 14:
			unset($_SESSION["viewer"][$ssid][$_POST['id_vimofy']]);		
			break;
		case 15:
			//Uniquement en affichage: On recharge la page si il y a de nouvelles varin de definies (pour regenerer l'url)
			//echo $_SESSION[$ssid]['objet_fiche']->valide_message_maintenance();
			break;
		case 17:
			// Récupère la designation d'un pole par rapport à son ID
			echo $_SESSION[$ssid]['objet_fiche']->get_libelle_pole($_POST['id_pole']);
			break;
		case 18:
			// Récupère la designation de l'activité par rapport à son ID et l'id de son pole
			echo $_SESSION[$ssid]['objet_fiche']->get_libelle_activite($_POST['id_activite'],$_POST['id_pole']);
			break;
		case 19:
			// Récupère la designation du module par rapport à son ID et l'id de son pole
			echo $_SESSION[$ssid]['objet_fiche']->get_libelle_module($_POST['id_module'],$_POST['id_pole']);
			break;		
		case 20:
			// Main control when try to save or check button
			echo $_SESSION[$ssid]['objet_fiche']->copy_var_ext_and_tag();
			echo $_SESSION[$ssid]['objet_fiche']->display_step(true);
			break;
		case 21:
			//Attribution des valeurs par défaut aux varin
			echo $_SESSION[$ssid]['objet_fiche']->set_default_values();
			break;
		case 22:
			//Annule les valeurs par défaut des varin
			echo $_SESSION[$ssid]['objet_fiche']->unset_default_values();
			break;
		case 23:
			//Attribution des valeurs neutres aux varin
			echo $_SESSION[$ssid]['objet_fiche']->set_neutral_values();
			break;
		case 24:
			//Annume les valeurs neutres des varin
			echo $_SESSION[$ssid]['objet_fiche']->unset_neutral_values();
			break;
		case 25:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->set_default_neutre_value($_POST['type'],$_POST['id']);
			break;
		case 26:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->editer_prerequis();
			break;
		case 27:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->backup_prerequis($_POST['contenu']);
			break;		
		case 28:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->annuler_modif_prerequis();
			break;
		case 29:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->editer_description();
			break;
		case 30:
			// Record description HTML
			echo $_SESSION[$ssid]['objet_fiche']->backup_description($_POST['contenu']);
			break;
		case 31:
			// Met la valeur par défaut a la varin ID
			echo $_SESSION[$ssid]['objet_fiche']->annuler_modif_description();
			break;
		case 32:
			// EN VISU: Supprimer les valeurs des parametres
			echo $_SESSION[$ssid]['objet_fiche']->delete_value_param();
			break;
		case 33:
			// Check global cohrence from last backup is ended
			echo $_SESSION[$ssid]['objet_fiche']->check_global_coherence_end();
			break;
		case 34:
			// Record description raw text
			echo $_SESSION[$ssid]['objet_fiche']->backup_description_raw_text($_POST['rawtext']);
			break;
		case 35:
			// Record prerequisite raw text
			echo $_SESSION[$ssid]['objet_fiche']->backup_prerequisite_raw_text($_POST['rawtext']);
			break;
	}
?>