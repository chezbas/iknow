<?php
	//==================================================================
	// Information database connexion
	//==================================================================
	$_SESSION['iknow'][$ssid]['serveur_bdd'] = 'localhost';
	$_SESSION['iknow'][$ssid]['schema_iknow'] = 'iknow';
	$_SESSION['iknow'][$ssid]['user_iknow'] = 'adm_iknow';
	$_SESSION['iknow'][$ssid]['password_iknow'] = 'MC&hny11';

	$_SESSION['iknow'][$ssid]['acces_serveur_bdd'] = 'localhost';
	$_SESSION['iknow'][$ssid]['acces_schema_iknow'] = 'acces';
	$_SESSION['iknow'][$ssid]['acces_user_iknow'] = 'devil';
	$_SESSION['iknow'][$ssid]['acces_password_iknow'] = 'maycry4';
	//==================================================================
	
	//==================================================================
	// Define table name iKnow
	//==================================================================
	
	//==================================================================
	// Define iKnow - Access
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_group']['name'] 				= 'ikn_groups';
	$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'] 				= 'ikn_password';
	//==================================================================
	
	//==================================================================
	// Define iKnow - Main table
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'] 		= 'ikn_conf';
	$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['caption'] 		= 'ikn_conf_caption';
	$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['extension'] 	= 'ikn_conf_feature';
	
	$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'] 		= 'ikn_doc';
	$_SESSION['iknow'][$ssid]['struct']['tb_zztrace_err_sql']['name'] 		= 'ikn_err_sql';
	$_SESSION['iknow'][$ssid]['struct']['tb_zztrace_sql']['name'] 			= 'ikn_log_sql';
	$_SESSION['iknow'][$ssid]['struct']['tb_lock']['name'] 					= 'ikn_lock';
	$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name'] 				= 'ikn_area_work';
	$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name'] 				= 'ikn_area_subwork';
	$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name'] 				= 'ikn_area';
	$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name'] 			= 'ikn_area_activity';
	$_SESSION['iknow'][$ssid]['struct']['tb_poles_versions']['name'] 		= 'ikn_area_version';	
	$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'] 				= 'ikn_text';
	$_SESSION['iknow'][$ssid]['struct']['tb_tags']['name'] 					= 'ikn_tag';
	$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['name'] 				= 'ikn_trr_tag_max';
	$_SESSION['iknow'][$ssid]['struct']['tb_bugs_reports']['name'] 			= 'bugsreports';
	$_SESSION['iknow'][$ssid]['struct']['tb_stat_version']['name'] 			= 'ikn_shoot_perf';
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_doc']['name'] 			= 'vimofy_doc';
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_filters']['name'] 		= 'vimofy_filters';
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_setup']['name'] 			= 'vimofy_setup';
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_texts']['name'] 			= 'vimofy_textes';
	$_SESSION['iknow'][$ssid]['struct']['tb_tir_perf']['name']				= 'ikn_tir_perf';
	$_SESSION['iknow'][$ssid]['struct']['tb_lang']['name']					= 'ikn_lang';
	//==================================================================
	
	//==================================================================
	// Define iKnow - iSheet table
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['name']				= 'ikn_isheet';
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches_etapes']['name'] 		= 'ikn_isheet_step';
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['name'] 			= 'ikn_isheet_parameter';
	$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['name'] 				= 'ikn_url_temp';
	$_SESSION['iknow'][$ssid]['struct']['tb_log_action']['name'] 			= 'ikn_log_action';	
	$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name'] 			= 'ikn_trr_isheet_max';
	//==================================================================
	
	
	//==================================================================
	// Define iKnow - iCode table
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name'] 				= 'ikn_icode';
	$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']			= 'ikn_icode_parameter';
	$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['name']				= 'ikn_engine';
	$_SESSION['iknow'][$ssid]['struct']['tb_version_moteur']['name'] 		= 'ikn_engine_version';
	$_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['name'] 			= 'ikn_trr_icode_max';
	//==================================================================
	
	
	//==================================================================
	// Define iKnow - MySQL procedure
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_delete_id_temp'] 			= 'delete_id_temp';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_delete_var_version_src'] 	= 'delete_var_version_src';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_icode_idp_renumbering'] 		= 'icode_idp_renumbering';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_ifiche_idp_renumbering_IN'] 	= 'ifiche_idp_renumbering_IN';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_ifiche_idp_renumbering_OUT'] = 'ifiche_idp_renumbering_OUT';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_tags_id_renumbering'] 		= 'tags_id_renumbering';
	$_SESSION['iknow'][$ssid]['struct']['PROCEDURE']['proc_update_var_version_src'] 	= 'update_var_version_src';
	//==================================================================
	
	//==================================================================
	// Define iKnow - MySQL function
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['FUNCTION']['func_get_max_version_icode'] 		= 'get_max_version_icode';
	$_SESSION['iknow'][$ssid]['struct']['FUNCTION']['func_get_max_version_ifiche'] 		= 'get_max_version_ifiche';	
	//==================================================================

	//==================================================================
	// Define iKnow - MySQL trigger
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['TRIGGER']['trig_FIC_ID_MAX'] 					= 'FIC_ID_MAX';
	$_SESSION['iknow'][$ssid]['struct']['TRIGGER']['trig_TAGS_ID_MAX'] 					= 'TAGS_ID_MAX';	
	$_SESSION['iknow'][$ssid]['struct']['TRIGGER']['trig_ICODE_ID_MAX'] 				= 'ICODE_ID_MAX';	
	//==================================================================
	
	
	
	//==================================================================
	// Define tables fields
	//==================================================================

	
	//==================================================================
	// Define main iKnow tables fields
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['champs'] 		= array('version_applicatif'=>		'version_applicatif',
																			'id' 						=> 		'id',
																			'valeur' 					=> 		'valeur',
																			'unite' 					=> 		'unite',
																			'format'					=> 		'FORMAT',
																			'theme' 					=> 		'THEME',
																			'type' 						=> 		'type',
																			'designation' 				=> 		'designation',
																			'version_active' 			=> 		'version_active');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['champs'] 		= array('version_active' 	=> 		'version',
																			'type_doc' 					=> 		'Type',
																			'parent' 					=> 		'ID_PARENT',
																			'enfant' 					=> 		'ID_CHILD',
																			'ordre' 					=> 		'ORDER',
																			'nom' 						=> 		'NAME',
																			'description' 				=> 		'description',
																			'dt_maj' 					=> 		'last_update',
																			'auteur' 					=> 		'who',
																			'icone' 					=> 		'icone');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_zztrace_err_sql']['champs']  	= array('id_erreur' 		=> 		'ID_ERR',
																			'dt_erreur' 				=> 		'DATE_ERR',
																			'libelle' 					=> 		'LIB_ERR',
																			'errno' 					=> 		'NUM_ERR',
																			'sql' 						=> 		'sql',
																			'fichier' 					=> 		'FILE',
																			'ligne' 					=> 		'LINE',
																			'fonction' 					=> 		'FONCTION',
																			'classe'					=> 		'CLASS',
																			'objet_id'					=> 		'OBJET_ID',
																			'objet_type'				=> 		'OBJET_TYPE',
																			'err_identified'			=> 		'ERR_IDENTIFIED',
																			'version_applicatif'		=>		'V_APPLICATIF');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_zztrace_sql']['champs']  		= array('id' 				=> 		'ID',
																			'id_objet' 					=> 		'ID_OBJET',
																			'version_objet' 			=> 		'VERSION_OBJET',
																			'objet' 					=> 		'OBJET',
																			'sql' 						=> 		'REQUETE',
																			'ssid' 						=> 		'ssid',
																			'ligne_explain' 			=> 		'ROWS_EXPLAIN',
																			'fichier' 					=> 		'FILE',
																			'ligne'						=> 		'LINE',
																			'fonction' 					=> 		'FONCTION',
																			'classe' 					=> 		'CLASS',
																			'duree_execution' 			=> 		'EXEC_TIME');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_lock']['champs'] 				= array('objet' 			=> 		'type',
																			'id_objet' 					=> 		'id',
																			'dt_debut' 					=> 		'date_mod',
																			'dt_maj' 					=> 		'last_update',
																			'IP' 						=> 		'utilise_par',
																			'ssid' 						=> 		'ssid',
																			'id_temp' 					=> 		'id_temp',
																			'infos_client' 				=> 		'version_client');
		
	
	$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['champs']  			= array('id_pole' 			=> 		'id_POLE',
																			'id_metier' 				=> 		'ID',
																			'libelle' 					=> 		'libelle');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_modules']['champs']  			= array('id_module' 		=> 		'ID',
																			'id_pole' 					=> 		'id_POLE',
																			'id_metier' 				=> 		'ID_METIER',
																			'libelle' 					=> 		'libelle');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_poles']['champs']  				= array('id_pole' 			=> 		'ID',
																			'libelle' 					=> 		'Libelle');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['champs']  		= array('id_theme' 			=> 		'ID',
																			'id_pole' 					=> 		'ID_POLE',
																			'libelle' 					=> 		'libelle');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_poles_versions']['champs']  	= array('id_pole' 		=> 		'ID',
																			'version' 					=> 		'version',
																			'ordre' 					=> 		'ORDRE',
																			'actif' 					=> 		'active');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['champs']  			= array('id' 				=> 		'id_texte',
																			'langue' 					=> 		'id_lang',
																			'texte' 					=> 		'texte',
																			'type_texte' 				=> 		'type',
																			'corps' 					=> 		'Corps',
																			'version_active' 			=> 		'version_active',
																			'objet' 					=> 		'objet',
																			'id_lien_aide' 				=> 		'help_link');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_tags']['champs']  				= array('id' 				=> 		'IdTag',
																			'id_objet' 					=> 		'ID',
																			'etape' 					=> 		'Etape',
																			'version_objet' 			=> 		'Version',
																			'tag' 						=> 		'Tag',
																			'groupe' 					=> 		'Groupe',
																			'objet' 					=> 		'objet',
																			'temporaire' 				=> 		'temp',
																			'id_objet_src'				=> 		'id_src',
																			'version_objet_src' 		=> 		'version_src');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_max_tags']['champs']  			= array('id' 				=> 		'IdTag',
																			'id_objet' 					=> 		'ID',
																			'etape' 					=> 		'Etape',
																			'version_objet' 			=> 		'Version',
																			'tag' 						=> 		'Tag',
																			'groupe' 					=> 		'Groupe',
																			'objet' 					=> 		'objet',
																			'temporaire' 				=> 		'temp',
																			'id_objet_src'				=> 		'id_src',
																			'version_objet_src' 		=> 		'version_src');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_bugs_reports']['champs'] 		= array('id' 				=> 		'ID',
																			'type_objet' 				=> 		'Type',
																			'type_report' 				=> 		'Classe',
																			'version_applicatif' 		=> 		'Version',
																			'dt_creation' 				=> 		'DateCrea',
																			'titre' 					=> 		'Description',
																			'detail' 					=> 		'details',
																			'trigramme' 				=> 		'Qui',
																			'status'					=> 		'Solved',
																			'dt_maj' 					=> 		'Last_mod');
	

	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_stat_version']['champs']		= array('version_applicatif'=> 		'VERSION_APPLICATION',
																			'type_objet' 				=> 		'OBJET',
																			'id_objet' 					=> 		'ID_OBJET',
																			'version_objet' 			=> 		'VERSION_OBJET',
																			'tp_chargement' 			=> 		'TEMPS',
																			'um_chargement' 			=> 		'RESSOURCE',
																			'environnement' 			=> 		'ENVIRONNEMENT',
																			'dt_tir' 					=> 		'DATE_TIR');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_doc']['champs']		= array('type' 					=> 		'Type',
																			'description' 				=> 		'description',
																			'dt_maj' 					=> 		'last_update',
																			'trigramme' 				=> 		'who',
																			'name' 						=> 		'NAME',
																			'order' 					=> 		'ORDER',
																			'id_child' 					=> 		'ID_CHILD',
																			'id_parent' 				=> 		'ID_PARENT',
																			'version' 					=> 		'version',
																			'icone' 					=> 		'icone');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_filters']['champs']	= array('name' 					=> 		'name',
																			'key' 						=> 		'key',
																			'vimofy_id' 				=> 		'vimofy_id',
																			'id_column' 				=> 		'id_column',
																			'type' 						=> 		'type',
																			'val1' 						=> 		'val1',
																			'val2' 						=> 		'val2',
																			'val3' 						=> 		'val3',
																			'date' 						=> 		'date');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_setup']['champs']	= array('version' 				=> 		'version',
																			'parent' 					=> 		'parent',
																			'id' 						=> 		'id',
																			'order' 					=> 		'order',
																			'value' 					=> 		'value',
																			'description' 				=> 		'description');	
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_vimofy_texts']['champs']	= array('id' 					=> 		'id',
																			'id_lang' 					=> 		'id_lang',
																			'texte' 					=> 		'texte',
																			'corps' 					=> 		'corps',
																			'version_active' 			=> 		'version_active');	
		
	$_SESSION['iknow'][$ssid]['struct']['tb_tir_perf']['champs']	= array('tir_date' 					=> 		'tir_date',
																			'load_time' 				=> 		'load_time',
																			'Version_iobjet' 			=> 		'Version_iobjet',
																			'Type_iobjet' 				=> 		'Type_iobjet',
																			'ID_iobjet' 				=> 		'ID_iobjet',
																			'ID_tir' 					=> 		'ID_tir');	

	$_SESSION['iknow'][$ssid]['struct']['tb_lang']['champs']		= array('id' 						=> 		'id',
																			'id_tiny' 					=> 		'id_tiny',
																			'label' 					=> 		'label');	
	//==================================================================
	

	//==================================================================
	// Define main iSheet tables fields
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['champs']				= array('cn_id_pole' 		=> 		'id_POLE',
																			'cn_id_theme' 				=> 		'Theme',
																			'id' 						=> 		'id_fiche',
																			'version' 					=> 		'num_version',
																			'titre' 					=> 		'titre',
																			'cn_version_pole' 			=> 		'vers_goldstock',
																			'cn_id_module' 				=> 		'id_module',
																			'description' 				=> 		'description',
																			'status'					=> 		'id_statut',
																			'dt_modif' 					=> 		'date',
																			'trigramme' 				=> 		'pers',
																			'prerequis' 				=> 		'prerequis',
																			'flag_obsolete' 			=> 		'obsolete');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches_etapes']['champs'] 		= array('id_fiche' 			=> 		'id_fiche',
																			'version' 					=> 		'num_version',
																			'etape' 					=> 		'id_etape',
																			'description' 				=> 		'description');

	
	$_SESSION['iknow'][$ssid]['struct']['tb_fiches_param']['champs'] 		= array('id' 				=> 		'IDP',
																			'type_param' 				=> 		'TYPE',
																			'id_fiche' 					=> 		'id_fiche',
																			'version_fiche' 			=> 		'num_version',
																			'id_etape' 					=> 		'id_action',
																			'id_src' 					=> 		'id_src',
																			'type_src' 					=> 		'type_src',
																			'version_src' 				=> 		'num_version_src',
																			'flag_max_src'				=> 		'max_version_src',
																			'etape_src' 				=> 		'id_action_src',
																			'nom' 						=> 		'NOM',
																			'description' 				=> 		'DESCRIPTION',
																			'val_defaut' 				=> 		'DEFAUT',
																			'val_neutre' 				=> 		'NEUTRE',
																			'val_resultat' 				=> 		'RESULTAT',
																			'flag_utilise' 				=> 		'used',
																			'commentaire' 				=> 		'COMMENTAIRE',
																			'flag_temporaire' 			=> 		'temp',
																			'flag_max' 					=> 		'max');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_url_temp']['champs'] 			= array('id_temporaire' 	=> 		'id_temp',
																			'id' 						=> 		'ID',
																			'nom' 						=> 		'Nom',
																			'valeur' 					=> 		'Valeur');
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_log_action']['champs'] 			= array('dt_action' 		=> 		'date_action',
																			'objet'		 				=> 		'objet',
																			'id_objet' 					=> 		'ID',
																			'ssid' 						=> 		'ssid',
																			'version_objet' 			=> 		'version',
																			'action' 					=> 		'action',
																			'etape_source' 				=> 		'source',
																			'etape_cible' 				=> 		'cible',
																			'contenu_etape'				=> 		'contenu');	
	
	
	$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['champs'] 			= array('cn_id_pole' 		=> 		'id_POLE',
																			'cn_id_theme' 				=> 		'Theme',
																			'id' 						=> 		'id_fiche',
																			'version' 					=> 		'num_version',
																			'titre' 					=> 		'titre',
																			'cn_version_pole' 			=> 		'vers_goldstock',
																			'cn_id_module' 				=> 		'id_module',
																			'description' 				=> 		'description',
																			'status'					=> 		'id_statut',
																			'dt_modif' 					=> 		'date',
																			'trigramme' 				=> 		'pers',
																			'prerequis' 				=> 		'prerequis',
																			'flag_obsolete' 			=> 		'obsolete');
	//==================================================================
	

	//==================================================================
	// Define main iCode tables fields
	//==================================================================
	$_SESSION['iknow'][$ssid]['struct']['tb_codes']['champs'] 				= array('id' 				=> 		'ID',
																			'cn_id_pole' 				=> 		'pole',
																			'cn_id_theme' 				=> 		'Theme',
																			'cn_version_pole' 			=> 		'VGS',
																			'version' 					=> 		'Version',
																			'prefixe' 					=> 		'prefixe',
																			'postfixe' 					=> 		'postfixe',
																			'cn_type_code' 				=> 		'typec',
																			'cn_version_moteur' 		=> 		'engine_version',
																			'corps_code' 				=> 		'corps',
																			'titre' 					=> 		'Titre',
																			'description' 				=> 		'Commentaires',
																			'trigramme_maj' 			=> 		'Last_update_user',
																			'dt_maj' 					=> 		'last_update_date',
																			'flag_obsolete' 			=> 		'obsolete');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['champs'] 			= array('id' 				=> 		'ID',
																			'cn_id_pole' 				=> 		'pole',
																			'cn_id_theme' 				=> 		'Theme',
																			'cn_version_pole' 			=> 		'VGS',
																			'version' 					=> 		'Version',
																			'titre' 					=> 		'Titre',
																			'description' 				=> 		'Commentaires',
																			'trigramme_maj' 			=> 		'Last_update_user',
																			'dt_maj' 					=> 		'last_update_date',
																			'cn_type_code' 				=> 		'typec',
																			'cn_version_moteur' 		=> 		'engine_version',
																			'corps_code' 				=> 		'corps',
																			'flag_obsolete' 			=> 		'obsolete');
		
	$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['champs']			= array('id' 				=> 		'IDP',
																			'type_param' 				=> 		'TYPE',
																			'id_code' 					=> 		'ID',
																			'version_code' 				=> 		'Version',
																			'nom' 						=> 		'NOM',
																			'description' 				=> 		'DESCRIPTION',
																			'val_defaut' 				=> 		'DEFAUT',
																			'val_neutre' 				=> 		'NEUTRE',
																			'commentaire' 				=> 		'COMMENTAIRE',
																			'val_resultat' 				=> 		'resultat',
																			'flag_max' 					=> 		'max');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_moteurs']['champs']				= array('id' 				=> 		'id',
																			'description' 				=> 		'Description');
	
	$_SESSION['iknow'][$ssid]['struct']['tb_version_moteur']['champs'] 		= array('id' 				=> 		'id',
																			'version' 					=> 		'version',
																			'ordre' 					=> 		'order',
																			'flag_actif' 				=> 		'actif');
	//==================================================================
?>