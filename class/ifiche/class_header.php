<?php

class sheet_header extends class_bdd
{
	/**==================================================================
	 * 						Sheet Header class
	 ====================================================================*/
	
	/**==================================================================
	 * Attribute declarations
	 ====================================================================*/
	private $c_dataset;						// Dataset of the sheet header
	private $c_version;						// Sheet version
	private $c_id;							// Sheet ID
	private $c_ssid;						// Object SSID
	private $c_id_temp;						// Object temporary ID
	private $c_type;						// Sheet mode : 1 modification, 2 visualization 
	private $c_version_app;					// iKnow version
	private $c_language;					// User language
	private $c_content_tabbar_requis;		// Content of prerequisites tab
	private $c_ik_valmod;					// Level of ik_valmod (none, default, neutral, default + neutral) 
	private $c_prerequis;					// Prerequisites content
	private $c_description;					// Description content
	private $c_description_raw_text;		// Description raw text ( no HTML usefull to compute real text length for control )
	private $c_prerequisite_raw_text;		// Prerequisite oraw text ( no HTML usefull to compute real text length for control )
	/*===================================================================*/
	

	/**==================================================================
	 * Class constructor
	 * @param array $p_content
	 * @param integer $p_id
	 * @param integer $p_version
	 * @param string $p_ssid
	 * @param integer $p_id_temp
	 * @param integer $p_type
	 * @param string $p_version_app
	 * @param string $p_language
	 * @param integer $p_ik_valmod
	====================================================================*/
	public function __construct($p_content,$p_id,$p_version,$p_ssid,$p_id_temp,$p_type,$p_version_app,$p_language,&$p_ik_valmod)
	{
		// Call the constructor of extended class
		parent::__construct($p_ssid,$p_id,$p_id_temp,$p_version,$p_type);

		//==================================================================
		// Attributes initialisation
		//==================================================================
		$this->db_connexion();
		$this->c_dataset = $p_content;
		$this->c_prerequis = $p_content['prerequis'];
		// Caution : We load raw text field with html content on initialisation..
		// It's just to avoid empty message cause we don't have yet real text content
		$this->c_prerequisite_raw_text = $p_content['prerequis'];
		$this->c_description = $p_content['description'];
		// Caution : We load raw text field with html content on initialisation..
		// It's just to avoid empty message cause we don't have yet real text content
		$this->c_description_raw_text = $p_content['description'];
		$this->c_id = $p_id;
		$this->c_version = $p_version;
		$this->c_ssid = $p_ssid;
		$this->c_id_temp = $p_id_temp;
		$this->c_type = $p_type;
		$this->c_version_app = $p_version_app;
		$this->c_language = $p_language;
		$this->c_ik_valmod = $p_ik_valmod;
		//==================================================================
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Called when the object is deserialized
	====================================================================*/
	public function __wakeup()
	{
		// Reconnect to databases
		$this->db_connexion();
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Set the dataset content
	 * @param array $p_content
	====================================================================*/
	public function set_dataset($p_content)
	{
		$this->c_dataset = $p_content;
		$this->c_prerequis = $p_content['prerequis'];
		$this->c_description = $p_content['description'];
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Copy var and tag on the temporary ID
	====================================================================*/
	public function copy_var_and_tag()
	{
		if($this->c_id != "new" && $_SESSION[$this->c_ssid]['reload'] == false)
		{
			//==================================================================
			// Copy tags on IDTemp
			//==================================================================
			$sql = 'INSERT 
					INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'`
						(	`IdTag`,
							`ID`,
							`Etape`,
							`Version`,
							`Tag`,
							`Groupe`,
							`objet`,
							`temp`,
							`id_src`,
							`version_src`
						)
					 	SELECT 
							`IdTag`,
							'.$this->c_id_temp.' As ID,
							`Etape`,
							`Version`,
							`Tag`,
							`Groupe`,
							`objet`,
							`temp`,
							`id_src`,
							`version_src` 
						FROM
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'` 
						WHERE 1 = 1
							AND `ID` = '.$this->c_id.' 
							AND `Version` = '.$this->c_version.'
							AND (`objet` = "ifiche" OR `id_src` != 0)
				  ';
				
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			//==================================================================

			//==================================================================
			// Copy variables on IDTemp
			//==================================================================
			$sql = 'INSERT 
					INTO 
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
							(
								`IDP`,
								`id_fiche`,
								`TYPE`,
								`num_version`,
								`id_action`,
								`id_src`,
								`num_version_src`,
								`type_src`,
								`max_version_src`,
								`id_action_src`,
								`NOM`,
								`DESCRIPTION`,
								`DEFAUT`,
								`NEUTRE`,
								`RESULTAT`,
								`used`,
								`COMMENTAIRE`,
								`max`
							)
							SELECT
								`IDP`,
								'.($this->c_id_temp).' AS id_fiche,
								`TYPE`,
								`num_version`,
								`id_action`,
								`id_src`,
								`num_version_src`,
								`type_src`,
								`max_version_src`,
								`id_action_src`,
								`NOM`,
								`DESCRIPTION`,
								`DEFAUT`,
								`NEUTRE`,
								`RESULTAT`,
								`used`,
								`COMMENTAIRE`,
								`max`
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
							WHERE 1 = 1
								AND `id_fiche` = '.$this->c_id.' 
								AND `num_version` = '.$this->c_version.';
					';

			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			//==================================================================
			
			//==================================================================
			// ???
			//==================================================================
			if($this->c_dataset['id_statut'] < $_SESSION[$this->c_ssid]['configuration'][32])
			{
				// Update Varin version only is the status of the sheet is absolut
				$sql = 'CALL update_var_version_src('.$this->c_id_temp.','.$this->c_type.');';

				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			}
			//==================================================================
		}
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Generate the header of the sheet
	====================================================================*/
	public function generer_entete()
	{
		//==================================================================
		// Count Tags
		//==================================================================
		$sql = 'SELECT COUNT(1) as total
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'` 
					WHERE ID = '.$this->c_id_temp.' 
					AND Version = '.$this->c_version.' 
					AND (objet = "ifiche" OR (id_src IS NOT NULL))';
			
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$nbr_tag = mysql_result($requete,0,0);
			
		$tag = 'Tag (<span id="nbr_tag">'.$nbr_tag.'</span>)';
		//==================================================================

		//==================================================================
		// Count Varin
		//==================================================================
		$sql = 'SELECT COUNT(IDP)
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND num_version = '.$this->c_version.' 
					AND TYPE="IN"';

		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$nbr_in = mysql_result($requete,0,0);

		$nbr_varin = $_SESSION[$this->c_ssid]['message'][59].' (<span id="onglet_nbr_varin">'.$nbr_in.'</span>)';
		//==================================================================
		
		//==================================================================
		// Count Varout + VarinExt
		//==================================================================
		$sql = 'SELECT COUNT(IDP)
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND num_version = '.$this->c_version.' 
					AND TYPE IN ("OUT","EXTERNE")';
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$nbr_out = mysql_result($requete,0);

		$nbr_varout = $_SESSION[$this->c_ssid]['message'][45].' (<span id="onglet_nbr_varout">'.$nbr_out.'</span>)';
		//==================================================================

		//==================================================================
		// Generate header tab
		//==================================================================
		if($this->c_type == __FICHE_VISU__)
		{
			// Display mode
			$entete = 'a_tabbar.addTab("tab-level1","<div class=\"onglet_icn_header\">'.$_SESSION[$this->c_ssid]['message'][41].'</div>","'.rawurlencode($this->generer_onglet_entete()).'","event_click_onglet(\'tab-level1\');");';
		}
		else
		{
			// Update mode
			$entete = 'a_tabbar.addTab("tab-level1","<div class=\"onglet_icn_header\">'.$_SESSION[$this->c_ssid]['message'][41].'</div>","'.rawurlencode($this->generer_onglet_entete()).'","event_click_onglet(\'tab-level1\');maj_nbr_param(\'vimofy_liste_param\');maj_nbr_param(\'vimofy_lst_tag_objassoc\');maj_nbr_param(\'vimofy_infos_recuperees\');document.getElementById(\'modifie_par\').focus();");';
		}
		//==================================================================
		
		//==================================================================
		// Generate header child tab
		//==================================================================
		$entete .= 'var head_tabbar = new iknow_tab(\'head_tabbar\');';
		if($this->c_type == __FICHE_VISU__)
		{
			// Display mode
			$entete .= 'head_tabbar.addTab("tab-level1_1","<div class=\"onglet_icn_general\"></div>'.rawurlencode($_SESSION[$this->c_ssid]['message'][42]).'","'.rawurlencode($this->generate_tab_general()).'","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_1\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			$entete .= 'head_tabbar.addTab("tab-level1_2","<div class=\"onglet_icn_required\">'.rawurlencode($_SESSION[$this->c_ssid]['message'][43]).'","'.rawurlencode($this->generer_onglet_prerequis()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_2\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			$entete .= 'head_tabbar.addTab("tab-level1_3","<div class=\"onglet_icn_varin\">'.rawurlencode($nbr_varin).'","'.rawurlencode($this->generate_tab_varin()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_3\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			$entete .= 'head_tabbar.addTab("tab-level1_4","<div class=\"onglet_icn_varout\">'.rawurlencode($nbr_varout).'","'.rawurlencode($this->generate_tab_varout()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_4\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			$entete .= 'head_tabbar.addTab("tab-level1_5","<div class=\"onglet_icn_tag\">'.rawurlencode($tag).'","'.rawurlencode($this->generer_onglet_tag()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_5\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
		}
		else
		{
			// Update mode
			$entete .= 'head_tabbar.addTab("tab-level1_1","<div class=\"onglet_icn_general\"></div>'.rawurlencode($_SESSION[$this->c_ssid]['message'][42]).'","'.rawurlencode($this->generate_tab_general()).'","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_1\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());");';
			$entete .= 'head_tabbar.addTab("tab-level1_2","<div class=\"onglet_icn_required\">'.rawurlencode($_SESSION[$this->c_ssid]['message'][43]).'</div>","'.rawurlencode($this->generer_onglet_prerequis()).'","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_2\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());");';
			$entete .= 'head_tabbar.addTab("tab-level1_3","<div class=\"onglet_icn_varin\">'.rawurlencode($nbr_varin).'","'.rawurlencode($this->generate_tab_varin()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_3\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());");';
			$entete .= 'head_tabbar.addTab("tab-level1_4","<div class=\"onglet_icn_varout\">'.rawurlencode($nbr_varout).'","'.rawurlencode($this->generate_tab_varout()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_4\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());");';
			$entete .= 'head_tabbar.addTab("tab-level1_5","<div class=\"onglet_icn_tag\">'.rawurlencode($tag).'","'.rawurlencode($this->generer_onglet_tag()).'</div>","set_tabbar_actif(a_tabbar.getActiveTab(),\'tab-level1_5\',retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());");';
		}
		//==================================================================
		
		return $entete;
	}
	/*===================================================================*/
	

	/**==================================================================
	 * Duplicate iSheet
	====================================================================*/
	public function dupliquer_entete()
	{
		$this->c_id = 'new';
		$this->c_version = 0;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Generate message bloc ( help, information, causion and so on... 
	 * @param string $txt
	 * @param integer $id_etape
	 * ( Deprecated function )
	====================================================================*/
	private function generer_entete_bloc($txt)
	{
		// Looking for bloc into $txt
		$motif = '#<div id="appercu" class="(.*)">#i';
		preg_match_all($motif,$txt,$out);

		foreach ($out[0] as $key => $value)
		{
			switch ($out[1][$key])
			{
				case 'bloc_aide':
					$titre = $_SESSION[$this->c_ssid]['message'][291];
					$icone = 'bloc_icone_aide';
					break;
				case 'bloc_information':
					$titre = $_SESSION[$this->c_ssid]['message'][292];
					$icone = 'bloc_icone_information';
					break;
				case 'bloc_attention':
					$titre = $_SESSION[$this->c_ssid]['message'][293];
					$icone = 'bloc_icone_attention';
					break;
				case 'bloc_erreur':
					$titre = $_SESSION[$this->c_ssid]['message'][294];
					$icone = 'bloc_icone_erreur';
					break;
				case 'bloc_code':
					$titre = $_SESSION[$this->c_ssid]['message'][296];
					$icone = 'bloc_icone_code';
					break;
			}
				
			$txt = str_replace($value,'<div class="'.$out[1][$key].'_entete" id="appercu_entete">'.$titre.'<div class="'.$icone.'" id="icone_entete"></div></div>'.$value,$txt);
		}

		return $txt;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Build head HTML content
	 * @return html
	====================================================================*/
	private function generer_onglet_entete()
	{
		return '<div id="head_tabbar" style="overflow:hidden;top:0;bottom:0;left:0;right:0;background-color:#CCC;position:absolute;"></div>';
	}
	/*===================================================================*/
	

	/**==================================================================
	 * Build tab Head -> Main
	 * @return html
	====================================================================*/
	private function generate_tab_general()
	{
		if($this->c_type == __FICHE_MODIF__)
		{
			// Update mode

			//==================================================================
			// Prepare description 
			//==================================================================
			$affiche_description = $this->c_description;
			$edit = '<div class="editer" onclick="javascript:editer_description();" onmouseover="ikdoc();set_text_help(302);" onmouseout="ikdoc(\'\');unset_text_help();"></div>';
			//==================================================================
						 	 	
			//==================================================================
			// Prepare status
			//==================================================================
			$sql = '	SELECT 
							`texte`,
							`id_texte`
						FROM
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_libelles']['name'].'` 
						WHERE 1 = 1 
							AND `type` = "statut" 
							AND `id_LANG` = "'.$this->c_language.'" 
							AND `version_active` = '.$this->c_version_app.' 
							AND `objet` = "ifiche"
							ORDER BY id_texte
					';
				
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			$opt_statut = '<table><tr><td><select id="statut" onmouseover="popup_statut();" onmouseout="hideddrivetip()" onchange="set_statut(this.value);type_gestion_date();">';
			
			// Fill in combo box
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				// Selection de la ligne
				($this->c_dataset['texte'] == $row["texte"]) ? $str_sel = 'selected="selected"' : $str_sel = '';

				$opt_statut .= '<option value="'.$row["id_texte"].'"'.$str_sel.'>'.$row["texte"].'</option>';

			}
				
			$opt_statut .= '</select></td><td><div class="voir" onclick="javascript:target_statut();"></div></td></tr></table>';
			//==================================================================
							
				
			$opt_activite = '';
			$opt_modules = '';
				
			$opt_modif = '<input class="gradient text" type="text" id="modifie_par" onkeyup="check_trigramme(this);" value="" size=4></input>';
				

			/**==================================================================
			 * Préparation du titre
			 ====================================================================*/
			$titre = '<input class="gradient text" type="text" onkeyup="document.getElementById(\'ifiche_title\').innerHTML = this.value;check_title(this);" id="titre" value="'.$this->c_dataset['titre'].'" style="width:99%;"></input>';
			/*===================================================================*/

		}
		else
		{
			// Affichage
			$edit = '';
			/**==================================================================
			 * Préparation du titre
			 ====================================================================*/
			$titre = $this->convertBBCodetoHTML($this->c_dataset['titre']);
			/*===================================================================*/

			/**==================================================================
			 * Préparation de la description
			 ====================================================================*/
			$affiche_description = $this->generer_entete_bloc($this->clean_html($this->c_description));
			/*===================================================================*/

 	
			/**==================================================================
			 * Préparation de l'image du statut de la fiche
			 ====================================================================*/
			switch($this->c_dataset['id_statut'])
			{
				case 1:
					$image = 'flux_processus_fiches1.jpg';
					break;
				case 2:
					$image = 'flux_processus_fiches2.jpg';
					break;
				case 3:
					$image = 'flux_processus_fiches3.jpg';
					break;
				case 4:
					$image = 'flux_processus_fiches4.jpg';
					break;
				case 5:
					$image = 'flux_processus_fiches5.jpg';
					break;
				case 6:
					$image = 'flux_processus_fiches6.jpg';
					break;
			}
				
			$opt_statut = '<table><tr><td><p id="statut" value="1">'.$this->c_dataset['texte'].'</p></td><td><div class="voir" onclick="javascript:target_statut();" onmouseover="ddrivetipimg(\'images/'.$image.'\',404,131,404);" onmouseout="hideddrivetip()"></div></td></tr></table>';
			/*===================================================================*/
				
			$opt_modif = $this->c_dataset['pers'];
				
		}
			

		/**==================================================================
		 * Construction de l'onglet Général
		 ====================================================================*/
		$content_tabbar_general = '<div class="contenu_onglet"><table class="wfull">';
		$content_tabbar_general .= '<tr>';																								// Titre
		$content_tabbar_general .= '<td width="14%" class=limp><b>'.$_SESSION[$this->c_ssid]['message'][47].' : </b></td>';
		$content_tabbar_general .= '<td class=limp>'.$titre.'</td>';
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Pôle
		$content_tabbar_general .= '<td width="14%" class=lp><b>'.$_SESSION[$this->c_ssid]['message']['iknow'][32].' : </b></td>';
		if($this->c_type == __FICHE_VISU__)
		{
			$content_tabbar_general .= '<td class="lp"><div class="vimofy_pole" id ="vimofy_pole" style="float:left;"></div><div id="pole_lib" style="text-indent:3px;">'.$this->get_libelle_pole($this->c_dataset['id_POLE']).'</div></td>';
		}
		else
		{
			$content_tabbar_general .= '<td class="lp"><div style="float: left;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_pole_lmod']->generate_lmod_form().'</div><div id="pole_lib" class="lib_lmod">'.$this->get_libelle_pole($this->c_dataset['id_POLE']).'</div></td>';	
		}
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Version Pole
		$content_tabbar_general .= '<td width="14%" class="limp"><b>'.$_SESSION[$this->c_ssid]['message']['iknow'][33].' : </b></td>';
		if($this->c_type == __FICHE_VISU__)
		{
			$content_tabbar_general .= '<td class="limp"><div id ="vimofy_version_emplacement" style="float:left;"></div><div id="id_version" style="display:none;">'.$this->c_dataset['vers_goldstock'].'</div><div id="version_lib" style="text-indent:3px;">'.html_entity_decode($this->c_dataset['vers_goldstock']).'</div></td>';
		}
		else
		{
			$content_tabbar_general .= '<td class="limp"><div id ="vimofy_version_emplacement" style="float:left;"></div><div id="version_lib" class="lib_lmod">'.$this->c_dataset['vers_goldstock'].'</div></td>';
		}
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Activité
		$content_tabbar_general .= '<td width="14%" class=lp><b>'.$_SESSION[$this->c_ssid]['message']['iknow'][51].' : </b></td>';
		if($this->c_type == __FICHE_VISU__)
		{
			$content_tabbar_general .= '<td class="lp"><div id ="vimofy_activite_emplacement" style="float:left;"></div><div id="id_activite" style="display:none;">'.$this->c_dataset['theme'].'</div><div id="activite_lib" style="text-indent:3px;">'.$this->get_libelle_activite($this->c_dataset['theme'],$this->c_dataset['id_POLE']).'</div></td>';
		}
		else
		{
			$content_tabbar_general .= '<td class="lp"><div id ="vimofy_activite_emplacement" style="float:left;"></div><div id="activite_lib" class="lib_lmod">'.$this->get_libelle_activite($this->c_dataset['theme'],$this->c_dataset['id_POLE']).'</div></td>';
		}
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Niveau
		$content_tabbar_general .= '<td width="14%" class="limp"><b>'.$_SESSION[$this->c_ssid]['message'][52].' : </b></td>';
		if($this->c_type == __FICHE_VISU__)
		{
			$content_tabbar_general .= '<td class="limp"><div id ="vimofy_module_emplacement" style="float:left;"></div><div id="id_module" style="display:none;">'.$this->c_dataset['id_module'].'</div><div id="module_lib" style="text-indent:3px;">'.$this->get_libelle_module($this->c_dataset['id_module'],$this->c_dataset['id_POLE']).'</div></td>';
		}
		else
		{
			$content_tabbar_general .= '<td class="limp"><div id ="vimofy_module_emplacement" style="float:left;"></div><div id="module_lib" class="lib_lmod">'.$this->get_libelle_module($this->c_dataset['id_module'],$this->c_dataset['id_POLE']).'</div></td>';
		}
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Vide
		$content_tabbar_general .= '<td width="14%" class=lp>&nbsp;</td>';
		$content_tabbar_general .= '<td class="lp"> </td>';
		$content_tabbar_general .= '</tr>';
		$content_tabbar_general .= '<tr>';																								// Statut
		$content_tabbar_general .= '<td width="14%" class="limp"><b><a onmouseover="ddrivetipimg(\'images/flux_processus_fiches.jpg\',404,131,404);" onmouseout="hideddrivetip()" href="images/flux_processus_fiches.jpg" target="_blank">'.$_SESSION[$this->c_ssid]['message'][54].' : </a></b></td>';
		$content_tabbar_general .= '<td class="limp" valign="center">'.$opt_statut.'</td>';
		$content_tabbar_general .= '</tr>';

			
		if($this->c_type == __FICHE_VISU__)
		{
			// Affichage
			$content_tabbar_general .= '<tr>';																				// Information sur la sauvegarde
			$content_tabbar_general .= '<td width="14%" id="modifdate" class="lp"><b>'.$_SESSION[$this->c_ssid]['message'][84].' :</b></td>';
			$content_tabbar_general .= '<td class="lp">'.$this->c_dataset['date_modif'].' '.$_SESSION[$this->c_ssid]['message'][120].' '.$this->c_dataset['heure_modif'].' '.$_SESSION[$this->c_ssid]['message'][83].' <b>'.$opt_modif.'</b></td>';
			$content_tabbar_general .= '</tr>';
		}
		else
		{
			// Modification
			$content_tabbar_general .= '<tr>';																				// Trigramme
			$content_tabbar_general .= '<td width="14%" id="modifby" class="lp"><b>'.$_SESSION[$this->c_ssid]['message'][55].' :</b></td>';
			$content_tabbar_general .= '<td class="lp">'.$opt_modif.'</td>';
			$content_tabbar_general .= '</tr>';
		}

		$content_tabbar_general .= '<tr style="height:100%;">';																								// Description
		$content_tabbar_general .= '<td width="14%" class="limp"><b>'.$_SESSION[$this->c_ssid]['message'][53].' : </b></td>';
		if($this->c_type == __FICHE_VISU__)
		{
			$content_tabbar_general .= '<td class="limp"><table class="wfull" style="height:100%;"><tr style="height:100%;" id="tr_description"><td id="td_description" style="height:100%;vertical-align: top;">'.$affiche_description.'</td></tr></table></td>';
		}
		else
		{
			$content_tabbar_general .= '<td class="limp"><table class="wfull" style="height:100%;"><tr style="height:100%;" id="tr_description"><td id="outils_description" style="vertical-align: top;">'.$edit.'</td><td id="td_description" style="height:100%;vertical-align: top;">'.$affiche_description.'</td></tr></table></td>';	
		}
		$content_tabbar_general .= '</tr>';

		$content_tabbar_general .= '</table></div>';
		/*===================================================================*/


		return $content_tabbar_general;

	}
	/*===================================================================*/
	

	/**
	 * Génération du contenu de l'onglet prérequis
	 * @return html
	 */
	private function generer_onglet_prerequis()
	{

		if($this->c_type == __FICHE_VISU__)
		{
			// Affichage
			$prerequis = $this->generer_entete_bloc($this->clean_html($this->c_prerequis));
			$edit = '';
		}
		else
		{
			// Modification
			$prerequis = $this->generer_entete_bloc($this->clean_html($this->c_prerequis));
			$edit = '<div class="editer" onclick="javascript:editer_prerequis();" onmouseover="ikdoc();set_text_help(301);" onmouseout="ikdoc(\'\');unset_text_help();"></div>';
		}
			
			
		$this->c_content_tabbar_requis = '<div class="contenu_onglet"><table style="width:100%;">';
		$this->c_content_tabbar_requis .=	'<tr id="tr_prerequis">';
		$this->c_content_tabbar_requis .=	'<td class="limp" id="outils_prerequis">'.$edit.'</td>';
		$this->c_content_tabbar_requis .=	'<td width="100%" class="limp" id="td_prerequis">'.$prerequis.'</td>';
		$this->c_content_tabbar_requis .=	'</tr>';
		$this->c_content_tabbar_requis .=	'</table></div>';

		if($this->c_type == __FICHE_VISU__)
		{
			return $this->replace_variable();
		}
		else
		{
			return $this->c_content_tabbar_requis;
		}


	}

	public function annuler_modif_prerequis()
	{
		$html = '<td class="lp" id="outils_prerequis"><a  class="editer" href="#" onclick="javascript:editer_prerequis();"></a></td>'
		.'<td width="100%" class="lp" id="td_prerequis">'.$this->clean_html($this->c_prerequis).'</td>';

		return $html;
	}

	public function annuler_modif_description()
	{
		$html = '<td id="outils_description"><div class="editer" onclick="javascript:editer_description();" onmouseover="ikdoc();set_text_help(302);" onmouseout="ikdoc(\'\');unset_text_help();"></div></td><td id="td_description">'.$this->c_description.'</td>';

		return $html;
	}

	/**
	 * Get the prerequired
	 */
	public function get_prerequis()
	{
		return $this->c_prerequis;
	}
	public function get_prerequisite_raw()
	{
		return $this->c_prerequisite_raw_text;
	}
	
	/**
	 * Get the description
	 */
	public function get_description()
	{
		return $this->c_description;
	}
	public function get_description_raw()
	{
		return $this->c_description_raw_text;
	}
	
	/**
	 * Backup the prerequired
	 * @param string $p_content
	 */
	public function backup_prerequis($p_content)
	{
		$this->c_prerequis = $p_content;
	}
	public function backup_prerequisite_raw_text($p_content)
	{
		$this->c_prerequisite_raw_text = $p_content;
	}
	
	/**
	 * Backup the header description 
	 * @param string $p_content
	 */
	public function backup_description($p_content)
	{
		$this->c_description = $p_content;
	}

	
	public function backup_description_raw_text($p_content)
	{
		$this->c_description_raw_text = $p_content;
	}
	
	
	/**
	 * Generate varin parameters tab
	 * @return html
	 */
	private function generate_tab_varin()
	{
		return  '<div style="height:100%;overflow:auto;">
					<div style="width:100%;bottom:0;top:0;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varin']->generate_vimofy().'</div>
				</div>';
	}

	public function set_version($p_version)
	{
		$this->c_version = $p_version;
	}
	
	/**
	 * Set type of value (default, neutral, default and neutral, none
	 * @param id - Identifiant de la varin
	 * @param type - neutre ou defaut
	 */
	public function set_default_neutre_value($p_type,$p_id)
	{
		$sql = 'SELECT 1
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE ID_fiche = '.$this->c_id_temp.' 
				AND TYPE = "IN" 
				AND IDP = '.$p_id.' 
				AND resultat = '.$p_type;

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

		if(mysql_num_rows($resultat) == 0)
		{
			// Valorise variable
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET resultat = '.$p_type.' WHERE ID_fiche = '.$this->c_id_temp.' AND TYPE = "IN" AND IDP = '.$p_id;
		}
		else
		{
			// Devalues ​​the variable
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET resultat = "" WHERE ID_fiche = '.$this->c_id_temp.' AND TYPE = "IN" AND IDP = '.$p_id;
		}

		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	

	/**
	 * Define ik_valmod value
	 * @param integer $p_value
	 */
	public function set_ik_valmod($p_value)
	{
		$this->c_ik_valmod = $p_value;
	}

	
	/**
	 * Get lib of date type
	 */
	public function get_type_gestion_date()
	{
		if($this->c_dataset['id_statut'] < $_SESSION[$this->c_ssid]['configuration'][32])
		{
			return $_SESSION[$this->c_ssid]['message'][354];
		}
		else
		{
			return $_SESSION[$this->c_ssid]['message'][355];
		}
	}

	/**
	 * Generate varout tab
	 * @return html
	 */
	private function generate_tab_varout()
	{
		return  '<div style="height:100%;overflow:auto;"><div style="width:100%;bottom:0;top:0;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_varout']->generate_vimofy().'</div></div>';
	}

	/**
	 * Generate tags tab
	 * @return html
	 */
	private function generer_onglet_tag()
	{
		if($this->c_type == __FICHE_VISU__)
		{
			$html  = '<div style="height:100%;overflow:auto;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_tags']->generate_vimofy().'</div>';
		}
		else
		{
			$html = '<div style="top:0;bottom:50%;left:0;right:0;position:absolute;overflow:auto;"><div style="height:100%;overflow:auto;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_tags']->generate_vimofy().'</div></div>';
			$html .= '<div style="top:50%;bottom:0;left:0;right:0;position:absolute;overflow:auto;"><div style="height:100%;overflow:auto;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_tags_ext']->generate_vimofy().'</div></div>';
		}

		return $html;
	}


	/**
	 * Remplace dans le contenu des prérequis les varin par leurs valeurs (si une valeur a été définie)
	 * @return html
	 */
	private function replace_variable()
	{
		/**==================================================================
		 * Remplacement des variables dans les prerequis
		 ====================================================================*/
		$sql = 'SELECT nom,resultat
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND TYPE = "IN" 
				AND resultat <> ""';

		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		while($row = mysql_fetch_array($requete,MYSQL_ASSOC))
		{
			// Varin
			$this->c_content_tabbar_requis = str_replace('<span class="BBVarIn">'.htmlentities($row['nom'],ENT_QUOTES,'UTF-8').'</span>','<span class="BBVarIn">'.$row['resultat'].'</span>',$this->c_content_tabbar_requis);
		}
		/*===================================================================*/

		return $this->c_content_tabbar_requis;
	}


	/**==================================================================
	 * ACCESSEURS
	 ====================================================================*/
	public function get_pole_version()
	{
		return $this->c_dataset['vers_goldstock'];
	}

	public function get_ik_valmod()
	{
		return $this->c_ik_valmod;
	}

	
	public function get_id_activite()
	{
		return $this->c_dataset['theme'];
	}

	public function get_module()
	{
		return $this->c_dataset['id_module'];
	}

	public function get_contenu($id_contenu)
	{
		return $this->c_dataset[$id_contenu];
	}

	public function get_id_pole()
	{
		if($this->c_id != 'new')
		{
			return $this->c_dataset['id_POLE'];
		}
		else
		{
			return '';
		}
	}

	private function get_libelle_activite($id_activite,$id_pole)
	{
			
		$sql = 'SELECT `Libelle`
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles_themes']['name'].'` 
				WHERE `ID` = "'.$id_activite.'" 
				AND ID_POLE = "'.$id_pole.'"';

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
		if(mysql_num_rows($resultat) > 0 )
		{
			return mysql_result($resultat,0,'Libelle');
		}
	}



	private function get_libelle_pole($id_pole)
	{
			
		$sql = 'SELECT `Libelle`
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles']['name'].'`
				WHERE `ID` = "'.$id_pole.'"';

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		if(mysql_num_rows($resultat) > 0 )
		{
			return mysql_result($resultat,0,'Libelle');
		}
	}


	private function get_libelle_module($id_module,$id_pole)
	{


		$sql = 'SELECT CONCAT('.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'.id," - ",'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_metiers']['name'].'.libelle," - ",'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'.libelle)
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].','.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_metiers']['name'].' 
				WHERE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'.id = "'.$id_module.'" 
				AND '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'.id_pole = "'.$id_pole.'" 
				AND '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_metiers']['name'].'.id_pole = "'.$id_pole.'" 
				AND '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'.ID_METIER = '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_metiers']['name'].'.ID';

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		if(mysql_num_rows($resultat) > 0 )
		{
			return mysql_result($resultat,0,0);
		}

	}
	/*===================================================================*/


	/**==================================================================
	 * SETTER
	 ====================================================================*/
	/**
	 * Set the value of the variables in database
	 */
	public function set_varin_values()
	{
		if($this->c_type == __FICHE_VISU__)
		{
			/**==================================================================
			 * Get varin values
			 ====================================================================*/
			$sql = 'SELECT
						`nom`,
						`IDP`
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
					WHERE 1 = 1
						AND `id_fiche` = '.$this->c_id_temp.' 
						AND `TYPE` = "IN"
					';

			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			while($row = mysql_fetch_array($requete,MYSQL_ASSOC))
			{
				/**==================================================================
				 * Check if varin was defined in the URL
				 ====================================================================*/
				if(isset($_GET[$row['nom']]))
				{
					// The varin was defined in the URL, set the value into the database
					if($_GET[$row['nom']] == '')
					{
						$resultat = 'NULL';
					}
					else
					{
						$resultat = '"'.$this->protect_display((($_GET[$row['nom']]))).'"';
					}

					$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
							SET resultat = '.$resultat.'
							WHERE id_fiche = '.$this->c_id_temp.' 
							AND IDP = '.$row['IDP'].' 
							AND `TYPE` = "IN" 
							AND id_fiche >= 99999';
				}
				else
				{
					if($this->c_ik_valmod == 0)
					{
						// No default or neutral values, set resultat to NULL
						$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
								SET resultat = "" WHERE 
								id_fiche = '.$this->c_id_temp.' 
								AND IDP = '.$row['IDP'].' 
								AND nom="'.$row['nom'].'" 
								AND `TYPE` = "IN" 
								AND id_fiche >= 99999';
					}
					else
					{
						switch ($this->c_ik_valmod)
						{
							case 1:
								// Default value
								$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
										SET resultat = ""
										WHERE id_fiche = '.$this->c_id_temp.' 
										AND IDP = '.$row['IDP'].' 
										AND `TYPE` = "IN" 
										AND id_fiche >= 99999';
								break;
							case 2:
								// Neutral value
								$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
										SET resultat = ""
										WHERE id_fiche = '.$this->c_id_temp.' 
										AND IDP = '.$row['IDP'].' 
										AND `TYPE` = "IN" 
										AND id_fiche >= 99999';
								break;
							case 3:
								// Default value, and next neutral value.
								$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
										SET resultat = ""
										WHERE id_fiche = '.$this->c_id_temp.' 
										AND IDP = '.$row['IDP'].' 
										AND `TYPE` = "IN" 
										AND id_fiche >= 99999';
								break;
						}

					}
				}
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				/*===================================================================*/
			}
			/*===================================================================*/
		}
	}
	/*===================================================================*/

	/**==================================================================
	 * METHODES UTILITAIRE
	 ====================================================================*/
	private function protect_display($texte)
	{
		$texte = str_replace('\\','\\\\',$texte);
		$texte = str_replace('"','\\"',$texte);
		$texte = str_replace(chr(13),"<br />",$texte);
		$texte = str_replace(chr(10),"",$texte);
		return $texte;
	}


	public function get_titre_sans_bbcode()
	{

		$titre = str_replace('[b]','<b>',$this->c_dataset['titre']);
		$titre = str_replace('[/b]','</b>',$titre);
		$titre = str_replace('[i]','<i>',$titre);
		$titre = str_replace('[/i]','</i>',$titre);
		$titre = str_replace('[u]','<u>',$titre);
		$titre = str_replace('[/u]','</u>',$titre);
		return $titre;

	}


	private function clean_html($txt)
	{

		$txt = str_replace('<strong>','<b>',$txt);
		$txt = str_replace('</strong>','</b>',$txt);
		$txt = str_replace('<em>','<i>',$txt);
		$txt = str_replace('</em>','</i>',$txt);

		return $txt;
	}


	public function convertBBCodetoHTML($txt)
	{

		$remplacement=true;
		while($remplacement)
		{
			$remplacement=false;
			$oldtxt=$txt;
			$txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','<b><u><font class="bbtitre">\\1</font></u></b>',$txt);
			$txt = preg_replace('`\[EMAIL\]([^\[]*)\[/EMAIL\]`i','<a href="mailto:\\1">\\1</a>',$txt);
			$txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','<b>\\1</b>',$txt);
			$txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','<i>\\1</i>',$txt);
			$txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','<u>\\1</u>',$txt);
			$txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','<s>\\1</s>',$txt);
			$txt = preg_replace('`\[br\]`','<br>',$txt);
			$txt = preg_replace('`\[center\]([^\[]*)\[/center\]`','<div style="text-align: center;">\\1</div>',$txt);
			$txt = preg_replace('`\[left\]([^\[]*)\[/left\]`i','<div style="text-align: left;">\\1</div>',$txt);
			$txt = preg_replace('`\[right\]([^\[]*)\[/right\]`i','<div style="text-align: right;">\\1</div>',$txt);
			$txt = preg_replace('`\[img\]([^\[]*)\[/img\]`i','<img src="\\1" />',$txt);
			$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<font color="\\1">\\2</font>',$txt);
			$txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','<font style="background-color: \\1;">\\2</font>',$txt);
			$txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','<font size="\\1">\\2</font>',$txt);
			$txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','<font face="\\1">\\2</font>',$txt);
			
			if ($oldtxt<>$txt)
			{
				$remplacement=true;
			}
		}
		return $txt;
		
	}	
	/*===================================================================*/

}
?>