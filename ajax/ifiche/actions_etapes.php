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

	
	require('../../class/common/class_bdd.php');
	require('../../class/ifiche/class_cartridge.php');
	require('../../class/ifiche/class_fiche.php');
	require('../../class/ifiche/class_etape.php');
	require('../../class/ifiche/class_step.php');
	require('../../class/ifiche/class_check.php');
	require('../../class/ifiche/class_header.php');
	require('../../class/common/class_lock.php');
	$dir_obj = '';
	require('../../vimofy/vimofy_includes.php');
	
	
	/**==================================================================
	 * Active php session
	 ====================================================================*/	
	$ssid = $_POST['ssid'];
	require('../../includes/common/active_session.php');
	/*===================================================================*/	

	
	switch($_POST['action'])
	{
		case 1:
			//delete de l'etape
			$_SESSION[$ssid]['objet_fiche']->del_step($_POST['id_etape']);
			break;
		case 2:
			// on fait un up de l'etape
			$_SESSION[$ssid]['objet_fiche']->deplacer_etape('monter',$_POST['idetape']);
			break;
		case 3:
			// on fait un down de l'etape
			$_SESSION[$ssid]['objet_fiche']->deplacer_etape('descendre',$_POST['idetape']);
			break;
		case 4:
			// on fait un add etape
			$_SESSION[$ssid]['objet_fiche']->add_etape($_POST['idetape']);
			break;
		case 5:
			// on sauvegarde l'etape
			$_SESSION[$ssid]['objet_fiche']->save_step($_POST['etape'],$_POST['contenu']);
			break;
		case 6:
			// on recharge les etapes
			$_SESSION[$ssid]['objet_fiche']->purge_tag(1);
			echo $_SESSION[$ssid]['objet_fiche']->get_nbr_tag_etape($_POST['etape']);
			break;
		case 7:
			//Affichage des étapes
			$_SESSION[$ssid]['objet_fiche']->display_step(true);
			break;
		case 9:
			//on charge la vimofy pour les tags de l'etape
			$_SESSION[$ssid]['objet_fiche']->get_vimofy_tag_etape($_POST['id_etape']);
			break;
		case 10:
			//On va verifier si des iObjets sont appelés dans cette étape
			$_SESSION[$ssid]['objet_fiche']->variables_externe($_POST['contenu']);
			break;
		case 11:
			//On va verifier avant la suppression d'une étape qu'aucune autre ne pointe sur elle via un lien
			$_SESSION[$ssid]['objet_fiche']->verif_del_step($_POST['etape']);
			break;
		case 12:
			//On va faire le remplacement des valeurs des variables (uniquement pour la visu)
			$_SESSION[$ssid]['objet_fiche']->visu_load_varin_etapes();
			break;
		case 13:
			// Récupère la valeur du paramètre ik_valmod pour l'url d'une tiny
			echo $_SESSION[$ssid]['objet_fiche']->get_ik_cartridge_url_tiny();
			break;
		case 15:
			//copie l'etape id_etape avec les param et les tags
			$_SESSION[$ssid]['objet_fiche']->copier_etape($_POST['id_etape']);
			break;
		case 16:
			$_SESSION[$ssid]['objet_fiche']->deplacer_etape($_POST['id_etape_src'],$_POST['id_etape_dst']);
			break;
		case 17:
			// Récupère les paramètres pour generer une URL (generateur d'URL de la tiny) NR_IKNOW_2_
			//echo $_SESSION[$ssid]['objet_fiche']->generer_url_tiny($_POST['id_dst'],$_POST['v_dst'],$_POST['iobject'],$_POST['IK_CARTRIDGE'],$_POST['first_edit']);
			echo $_SESSION[$ssid]['objet_fiche']->generer_url_tiny($_POST['id_dst'],$_POST['v_dst'],$_POST['iobject']);
			break;
		case 18:
			// Efface les paramètres de l'URL stocké dans la base de données
			echo $_SESSION[$ssid]['objet_fiche']->supprimer_val_bdd();
			break;
		case 19:
			// Step alias : Load ID list
			$_SESSION[$ssid]['objet_fiche']->vimofy_alias_step_id($_POST['etape']);
			break;
		case 20:
			//on récupère le contenu de l'étape $_POST['etape'] de la fiche $_POST['fiche'] de la version $_POST['version']'
			echo $_SESSION[$ssid]['objet_fiche']->recup_contenu_etape($_POST['fiche'],$_POST['version'],$_POST['etape']);
			break;
		case 21:
			//on récupère le contenu de l'étape $_POST['etape'] de la fiche $_POST['fiche'] de la version $_POST['version']'
			echo $_SESSION[$ssid]['objet_fiche']->save_alias($_POST['etape']);
			break;
		case 22:
			//On rapproche le contenu des variables de 2 fiches ou de deux requetes
			echo $_SESSION[$ssid]['objet_fiche']->rapprocher_var($_POST['id_dst'],$_POST['v_dst'],$_POST['type_lien']);
			break;
		case 23:
			//Copie des parametres d'une étape pour pouvoir faire un cancel lors de la modification
			echo $_SESSION[$ssid]['objet_fiche']->sauvegarder_variables_etape($_POST['id_etape']);
			break;		
		case 24:
			//Met les variables de l'étape comme avant sa modification.
			echo $_SESSION[$ssid]['objet_fiche']->cancel_modif_etape($_POST['id_etape']);
			break;
		case 25:
			//Copie des tags d'une étape pour pouvoir faire un cancel lors de la modification
			echo $_SESSION[$ssid]['objet_fiche']->sauvegarder_tags_etape($_POST['id_etape']);
			break;
		case 26:
			//Met les tags de l'étape comme avant leurs modification.
			echo $_SESSION[$ssid]['objet_fiche']->cancel_modif_tags($_POST['id_etape']);
			break;
		case 27:
			// Retourne le contenu de l'étape dans une textarea (lors de l'edition d'une étape)
			echo $_SESSION[$ssid]['objet_fiche']->editer_etape($_POST['id_etape']);
			break;
		case 28:
			// Retourne le niveau du mot de passe id
			echo $_SESSION[$ssid]['objet_fiche']->get_niveau_password($_POST['id']);
			break;
		case 29:
			// Lisha of cartridge output parameters
			echo $_SESSION[$ssid]['objet_fiche']->init_vimofy_cartouche_infos($_POST['num_cartouche'],$_POST['id_etape']);
			break;
		case 30:
			// Lisha of cartridge input parameters
			echo $_SESSION[$ssid]['objet_fiche']->init_vimofy_cartouche_param($_POST['num_cartouche'],$_POST['id_etape']);
			break;
		case 31:
			// Calcul du nombre de parametres (varin ou varout ou tag) d'entête
			echo $_SESSION[$ssid]['objet_fiche']->maj_nbr_param($_POST['objet']);
			break;
		case 32:
			// Step alias : Load version list
			$_SESSION[$ssid]['objet_fiche']->vimofy_alias_step_version($_POST['id'],$_POST['etape']);
			break;
		case 33:
			// Step alias : Load step list
			$_SESSION[$ssid]['objet_fiche']->vimofy_alias_step_id_step($_POST['id'],$_POST['version'],$_POST['etape']);
			break;
		case 34:
			// Clean url param
			$_SESSION[$ssid]['objet_fiche']->clear_url_temp();
			break;
			
	}

?>