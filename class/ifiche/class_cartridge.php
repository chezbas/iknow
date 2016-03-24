<?php	
class cartridge extends class_bdd 
{
	// --------------------------------------------------- DECLARATION DES ATTRIBUTS -----------------------------------------------------------//
	private $c_id;						// Id du lien
	private $c_version;					// Version du lien
	private $c_version_precisee;		// Version précisée dans le lien (si vide alors version max (voir $this->c_version))
	private $c_objet;					// Type d'objet	(ifiche.php/icode.php/idossier.php)
	private $c_parametres;				// Paramètres de l'url
	private $c_ctrl_id;					// false si l'id n'existe pas, true si oui
	private $c_ctrl_version;			// false si la version n'existe pas, true si oui
	private $c_num_lien;				// Identifiant du lien dans l'étape (1,2,3...)
	private $c_nbr_info_recuperees;		// Quantité d'informations retournées par le lien
	private $c_nbr_param_appel;			// Quantité de paramètres d'appel dans le lien hormis l'id et la version
	private $c_nbr_param_non_appele;	// Quantité de paramètres d'appel qui n'ont pas été précisés
	private $c_nbr_param_appel_objet;	// Quantité de paramètres d'entrée de l'objet appelé
	private $c_ssid;					// Identifiant de session de l'objet	
	private $c_id_temp;					// Identifiant temporaire de l'objet
	private $c_id_etape;				// Identifiant de l'étape du lien
	private $c_resultat;				// Variables de la fiche en cours
	private $c_varin_objet;				// Variable d'entrée de l'objet appelé
	private $c_type;					// Contient le type de lock, soit 1 pour la modification d'une fiche et 2 pour la visualisation.
	private $c_requete_vimofy_cartouche;// Contient les requêtes pour les vimofy des cartouches
	private $c_dataset_info_recuperees;	// Dataset qui contient les informatiosn récupérées du lien
	private $c_max_version_objet;
	private $c_array_parameters;		// parameters of the URL (Array)
	private $c_ik_valmod;				// IK_VALMOD of the link
	private $c_speed_param_name;		// Gathering param name
	private $c_type_objet;
	private $c_ik_cartridge;
	private $c_ik_valmod_iobject;
	private $c_statut;					// Statut de la fiche
	private $c_concat_step_nul_lien;
	private $c_concat_step_nul_lien_without_underscore;
	/**
	 * Constructeur de l'objet
	 * 
	 * @param string $p_objet Type d'objet appelé (ifiche,icode,idossier,password,externe)
	 * @param int $p_id Identifiant de l'objet appelé
	 * @param int $p_version Version de l'objet appelé
	 * @param int $p_version_precisee Version précisiée dans l'url de l'objet appelé (Vide ou valorisé)
	 * @param string $p_param url
	 * @param string $p_ssid Identifiant de session de l'objet
	 * @param int $p_num_lien Numéro du lien dans l'étape
	 */
	public function __construct($p_objet,$p_id,$p_version_max,$p_version,$p_version_precisee,$p_param,$p_ssid,$p_id_temp,$p_id_etape,$p_num_lien,&$resultat,&$requete_vimofy_cartouche,$p_type,&$c_link,&$c_link_password,$p_ik_valmod_iobject,$p_statut) 
	{
		parent::__construct($p_ssid,$p_id,$p_id_temp,$p_version,$p_type);
		
		/************************************************************************************************************
		 *										Initialisation des attributs
		 ************************************************************************************************************/
		$this->c_link = $c_link;
		$this->c_link_password = $c_link_password;
		$this->c_objet = $p_objet;
		$this->c_ik_valmod_iobject = $p_ik_valmod_iobject; 	// Celui de l'objet de l'instance
		$this->c_statut = $p_statut; 						// Permet de connaitre si appel Relatif ou absolue par rapport à la date
		switch($p_objet) 
		{
			case 'ifiche.php':
				$this->c_type_objet = 'ifiche';
				break;
			case 'icode.php':
				$this->c_type_objet = 'icode';
				break;
			case 'password.php':
				$this->c_type_objet = 'password';
				break;
			case 'idossier.php':
				$this->c_type_objet = 'idossier';
				break;
		}
		
		$this->c_id = $p_id;
		$this->c_version = $p_version;
		$this->c_version_precisee = $p_version_precisee;
		$this->c_max_version_objet = $p_version_max;		
		$this->c_parametres = $p_param;
		$this->c_ik_valmod = 3;
		$this->c_array_parameters = $this->decouper_url();
		$this->c_id_etape = $p_id_etape;
		$this->c_num_lien = $p_num_lien;
		$this->c_ssid = $p_ssid;
		$this->c_id_temp = $p_id_temp;
		$this->c_type = $p_type;
		$this->verif_existance_id_and_version();
		$this->get_dataset_info_recuperees();
		$this->c_resultat = $resultat;
		$this->c_requete_vimofy_cartouche = &$requete_vimofy_cartouche;
		$this->c_requete_vimofy_cartouche['in'] = '';
		$this->c_requete_vimofy_cartouche['out'] = '';
		$this->c_ik_cartridge = $this->get_ik_cartridge();
		$this->c_concat_step_nul_lien = $this->c_id_etape.'_'.$this->c_num_lien;
		$this->c_concat_step_nul_lien_without_underscore = $this->c_id_etape.$this->c_num_lien;
		/*************************************************************************************************************/
		
		// Incrémentation du nombre de lien dans l'étape
		$p_num_lien++;
	}
	
	/**
	 * Call when object is deserialized
	 */
	public function __wakeup()
	{
		// Databases reconnexion
		$this->db_connexion();	
	}
	
	/**
	 * Intègre le cartouche précédement généré dans le texte
	 * @param string $txt Texte ou il faut placer le cartouche correspondant au lien (Passage par référence !)
	 */
	public function integrer_cartouche($txt)
	{
		$ref = $txt;
		
		/**==================================================================
		 * Recherche de la position du lien dans l'étape
		====================================================================*/
		$recherche = strstr($ref,$this->c_objet.$this->c_parametres.'"');
		/*===================================================================*/	

		/**==================================================================
		 * Mise en place du cartouche
		====================================================================*/
		// Le cartouche est affiché par défaut
		$motif = '#.*</a>#';
		
		preg_match_all($motif,$recherche,$out);		
		
		if(isset($out[0][0]))
		{
			$ref = $out[0][0];
			
			// Retour chariot trouvé à la suite du lien, on place le cartouche après le retour chariot.
			$txt = str_replace($ref,$ref.$this->generer_cartouche(),$txt);
		}
		else
		{
			// Pas de Line feed trouvé
			$txt = str_replace($txt,$txt.$this->generer_cartouche(),$txt);
		}
		
		if($this->c_version_precisee == '' && $this->c_type_objet != 'password')
		{
			// On fixe la version pour les liens qui n'en ont pas (force version max absolue ou relative)
			$txt = str_replace($this->c_objet.$this->c_parametres.'"',$this->c_objet.$this->c_parametres.'&version='.$this->c_version.'"',$txt);
		}
		/*===================================================================*/		

		return $txt;
	}
	
	/**
	 * Génération du cartouche
	 */
	private function generer_cartouche()
	{
		$titre_param_appel = '';
		// On vérifie si le lien est correct
		if($this->c_ctrl_id == false || $this->c_ctrl_version == false)
		{
			return $this->generer_cartouche_erreur();		// Lien KO, génération d'un cartouche d'erreur.
		}
		else
		{
			$cartouche = '';
			$style = $this->get_style();
			
			if(($this->c_ik_cartridge&1) == 0)
			{
				$cartouche .= '<div class="liste" '.$this->get_event("'id_aide'",107,'cartouche_'.$this->c_concat_step_nul_lien).'"></div>';
			}
			
			$cartouche  .= '<div id="cartouche_'.$this->c_concat_step_nul_lien.'" class="conteneur_cartouche" '.$this->get_style_display(1).'>';	
			
			/**==================================================================
			 * Entête du cartouche
			 ====================================================================*/
			$cartouche .= '<div class="cart_head cart_head_info_'.$style.' ikcur" '.$this->get_event("'id_aide'",107,'cartouche_info_lien_entete_conteneur_'.$this->c_concat_step_nul_lien).'>'.$this->get_titre_cartouche().'</div>';
			/*===================================================================*/
				
			$param_appel = $this->get_param_appel();
			/**==================================================================
			 * Paramètres d'appel
			 ====================================================================*/
			if($this->c_objet != 'password.php')
			{
				
				$cartouche .= '<div id="cartouche_info_lien_entete_conteneur_'.$this->c_concat_step_nul_lien.'" '.$this->get_style_display(2).'>';
				if($this->c_varin_objet != false && mysql_num_rows($this->c_varin_objet) >= 1)
				{
					$cartouche .= '<div><div class="cart_head cart_head_info_'.$style.' ikcur" style="border-top:none;"><table class="wfull"><tr><td style="width:16px;">';
					
					// On propose uniquement l'affichage de la vimofy si on passe des paramètres à l'objet
					if($this->c_nbr_param_appel > 0)
					{
						$cartouche .= '<div id="aff_vimofy_'.$this->c_concat_step_nul_lien_without_underscore.'" class="liste" onclick="afficher_vimofy_cartouche_param('.$this->c_id_etape.','.$this->c_concat_step_nul_lien_without_underscore.','.$this->c_num_lien.',\'_entete_\')" style="float:left;margin:-3px 10px 0 0;" onmouseover="ikdoc(\'id_aide\');set_text_help(371);"  onmouseout="ikdoc();unset_text_help();"></div>';
					}
				
					/**==================================================================
					 * Définition du titre
					 ====================================================================*/
					if($this->c_type == __FICHE_MODIF__)
					{
						if($this->c_nbr_param_appel == 0)
						{
							$titre_param_appel .= $_SESSION[$this->c_ssid]['message'][117].' : '.$_SESSION[$this->c_ssid]['message'][393];					//Aucun valorise
						}
						else
						{
							$titre_param_appel .= $_SESSION[$this->c_ssid]['message'][117].' : '.$_SESSION[$this->c_ssid]['message'][392].'  ('.$this->c_nbr_param_appel.')';		// Valorisés	
						}
						
						if($this->c_nbr_param_non_appele == 0)
						{
							$titre_param_appel .= ' - '.$_SESSION[$this->c_ssid]['message'][390];												// Aucun non valorise
						}
						else
						{
							$titre_param_appel .= ' - '.$_SESSION[$this->c_ssid]['message'][391].' ('.$this->c_nbr_param_non_appele.')';		// Non valorisé
						}
					}
					else
					{
						$titre_param_appel = $_SESSION[$this->c_ssid]['message'][117].' ('.$this->c_nbr_param_appel.')';
					}
					/*===================================================================*/	
					
					$ik_valmod = '<div class="value_type_'.$this->c_ik_valmod.'"></div>';
					$cartouche .= '</td><td class="fleche_parametres_lien" '.$this->get_event("'id_aide'",107,'conteneur_param_entete_'.$this->c_concat_step_nul_lien,'param').'><table><tr><td style="padding:0;"><div class="droite" style="float:left;"></div></td><td style="padding:0 5px 0 8px;">'.$titre_param_appel.'</td><td style="padding:0;">'.$ik_valmod.'</td></tr></table></td></tr></table></div><div id="conteneur_param_entete_'.$this->c_concat_step_nul_lien.'" '.$this->get_style_display(4).'><div class="info_lien" style="display:none;" id="vimofy_cartouche_param_entete_'.$this->c_concat_step_nul_lien_without_underscore.'"></div><div class="info_lien" id="info_lien_entete_'.$this->c_concat_step_nul_lien.'">'.$param_appel.'</div></div></div>';
				}
				else
				{
					$param_appel = '';
				}
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Informations récupérées
			 ====================================================================*/
			if($this->c_nbr_info_recuperees > 0)
			{
				if($this->c_type == __FICHE_MODIF__)
				{	// Modification
					$titre_informations_recuperees = $_SESSION[$this->c_ssid]['message'][211];
				}
				else
				{	// Affichage
					$titre_informations_recuperees = $_SESSION[$this->c_ssid]['message'][45];
				}

				$cartouche .= '<div>
									<div class="cart_head cart_head_info_'.$style.' ikcur" style="border-top:none;">
										<table class="wfull">
											<tr>
												<td class="aff_vim">
													<div id="aff_vimofy_'.$this->c_concat_step_nul_lien_without_underscore.'" class="liste" onclick="toggle_cart('.$this->c_id_etape.','.$this->c_concat_step_nul_lien_without_underscore.','.$this->c_num_lien.',\'_entete_\')" style="float:right;margin:-3px 10px 0 0;" onmouseover="ikdoc(\'id_aide\');set_text_help(371);"  onmouseout="ikdoc();unset_text_help();"></div>
												</td>
												<td class="arrow_varin_link" '.$this->get_event("'id_aide'",107,'conteneur_infos_entete_'.$this->c_concat_step_nul_lien,'infos').'>'.$titre_informations_recuperees.' ('.$this->c_nbr_info_recuperees.')</td>
											</tr>
										</table>
									</div>
									<div id="conteneur_infos_entete_'.$this->c_concat_step_nul_lien.'" '.$this->get_style_display(8).'>
										<div class="info_lien hide" id="vimofy_cartouche_infos_entete_'.$this->c_concat_step_nul_lien_without_underscore.'"></div>
										<div class="info_lien_recup" id="info_lien_param_en_colonne_entete_'.$this->c_concat_step_nul_lien.'">'.$this->get_info_recuperees().'</div>
									</div> 
								</div>';
			}
			/*===================================================================*/	
			$cartouche .= '</div></div>'; // Fermeture div cartouche_info_lien_entete_conteneur / Fermeture div conteneur_cartouche

			return $cartouche;
		}
		
	}
	
	/**
	 * Retourne le style a appliquer a un element html par rapport à la valeur de ik_cartridge et le bit de configuration.
	 * 
	 * @param numeric $bit_mode 1,2,4 ou 8 (1 = Cartouche, 2 = Entête, 3 = Appel, 8 = Infos récup
	 */
	private function get_style_display($bit_mode,$style_perso = '')
	{
		if($this->c_ik_cartridge&$bit_mode)
		{
			return 'style="'.$style_perso.'"';
		}
		else
		{
			return 'style="display:none;'.$style_perso.'"';
		}
	}
	
	/**
	 * Génération du cartouche d'erreur
	 */
	private function generer_cartouche_erreur()
	{
		$cartouche  = '<div class="conteneur_cartouche"><div class="cart_head cart_head_info_erreur">';
		/**==================================================================
		 * Contrôle du type d'erreur
		 ====================================================================*/	
		if($this->c_ctrl_id == false)
		{
			$cartouche .= str_replace('$id',$this->c_id,$_SESSION[$this->c_ssid]['message'][150]);
		}
		elseif($this->c_ctrl_version == false)
		{
			if($this->c_objet == 'password.php')
			{
				// iObject without version
				$message = str_replace('$id',$this->c_id,$_SESSION[$this->c_ssid]['message'][501]);
				$message = str_replace('$version',$this->c_version,$message);
			}
			else
			{
				// iObject with version
				$message = str_replace('$id',$this->c_id,$_SESSION[$this->c_ssid]['message'][151]);
				$message = str_replace('$version',$this->c_version,$message);
			}
			$cartouche .= $message;
		}
		/*===================================================================*/	
		
		$cartouche .= '</div></div>';	// Fermeture div cart_head_info_erreur, Fermeture div conteneur_cartouche
		
		return $cartouche;
	}
		
	/**
	 * Génération des evenements d'une div
	 * @param int $id_aide
	 * @param int $id_libelle
	 * @param int $id_div
	 */	
	private function get_event($id_aide = null,$id_libelle = null,$id_div = null,$type = null)
	{
		$event = ' onclick="toggle_div(\''.$id_div.'\');"';
		
		if(!is_null($id_aide) && ($this->c_nbr_info_recuperees > 0 || $this->c_nbr_param_appel > 0)) 
			$event .= ' onmouseover="ikdoc('.$id_aide.');set_text_help('.$id_libelle.');"';
		
		if(!is_null($id_aide) && ($this->c_nbr_info_recuperees > 0 || $this->c_nbr_param_appel > 0)) 
			$event .= ' onmouseout="ikdoc('.$id_aide.');unset_text_help();"';
		
		return $event;
	}
	
	/**
	 * Génération du titre du cartouche
	 */
	private function get_titre_cartouche()
	{
		/**==================================================================
		 * GESTION DE LA VERSION D'APPEL ET RECUPERATION DU TITRE DE L'OBJET APPELE
		====================================================================*/
		if($this->c_ctrl_id == true && $this->c_ctrl_version == true)
		{
			if($this->get_objet() == 'password')
			{
				return $_SESSION[$this->c_ssid]['message'][224].' '.$this->get_niveau_password();
			}	
			else
			{
				switch ($this->get_objet()) 
				{
					case 'ifiche':
						$appli = $_SESSION[$this->c_ssid]['message'][118];
						break;
					case 'icode':
						$appli = $_SESSION[$this->c_ssid]['message'][119];
						break;
					case 'idossier':
						$appli = 'IDOSSIER A FINIR';
						break;
					case 'password':
						$appli = $_SESSION[$this->c_ssid]['message'][223];
						break;
				}
				
				if($this->c_version_precisee == '')
				{
					// Pas de version de précisée
					if($this->c_statut > $_SESSION[$this->c_ssid]['configuration'][32])
					{
						$version_appel = $_SESSION[$this->c_ssid]['message'][148].' ('.$_SESSION[$this->c_ssid]['message'][457].' '.$this->c_version.'/'.$this->c_max_version_objet.')';
					}
					else
					{
						$version_appel = $_SESSION[$this->c_ssid]['message'][148].' ('.$this->c_version.')';
					}
				}
				else
				{
					// Une version est précisée
					$version_appel = $_SESSION[$this->c_ssid]['message'][149].' '.$this->c_version.'/'.$this->c_max_version_objet;
				}
				return $appli.' '.$this->c_id.' '.$version_appel.' - '.$this->get_titre_objet_varin();	
			}
		}
		/*===================================================================*/
	}
	
	/**
	 * Retourne le titre de l'iObjet pointé ainsi que ses variables d'entrée.
	 */
	private function get_titre_objet_varin()
	{
		/**==================================================================
		 * PREPARATION DE LA REQUETE
		 ====================================================================*/			
		switch ($this->get_objet()) 
		{
			case 'ifiche':
				if($this->c_version == '')
				{
					// Version MAX
					$sql = 'SELECT	fic.`titre` as titre,
									IF(
										IFNULL(length(par.`DEFAUT`),0) = 0 
										AND
										IFNULL(length(par.`NEUTRE`),0) = 0
										,1
										,0
										) as "OB",
									par.`DEFAUT`,
									par.`NEUTRE`,
									par.`nom` as nom
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` fic LEFT join `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` par 
							ON fic.`id_fiche` = par.`id_fiche`
							AND fic.`num_version` = par.`num_version`
							AND par.`TYPE` = "IN"
							WHERE 1 = 1
							AND fic.`id_fiche` = '.$this->c_id;
				}
				else
				{
					// Version < MAX
					$sql = 'SELECT	fic.`titre` as titre,
									IF(
										IFNULL(length(par.`DEFAUT`),0) = 0
										AND
										IFNULL(length(par.`NEUTRE`),0) = 0
										,1
										,0
										) as "OB",
									par.`DEFAUT`,
									par.`NEUTRE`,
									par.`nom` as nom
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'` fic LEFT join `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` par
							ON fic.`id_fiche` = par.`id_fiche`
							AND fic.`num_version` = par.`num_version`
							AND par.`TYPE` = "IN" 
							WHERE 1 = 1
							AND fic.`id_fiche` = '.$this->c_id.' 
							AND fic.`num_version` = '.$this->c_version;
				}
				
				break;
			case 'icode':
				if($this->c_version == '')
				{
					// Version MAX
					$sql = 'SELECT 	ico.`Titre` as titre,
									IF(
										IFNULL(length(par.`DEFAUT`),0) = 0 
										AND 
										IFNULL(length(par.`NEUTRE`),0) = 0
										,1 -- Mandatory
										,0 -- Not mandatory
										) as "OB",
									par.`DEFAUT`,
									par.`NEUTRE`,
									par.`nom` as nom
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` ico LEFT join `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` par
							ON ico.`ID` = par.`ID`
							AND ico.`Version` = par.`Version`
							AND par.`TYPE` = "IN" 
							WHERE 1 = 1
							AND ico.`ID` = '.$this->c_id;
					
				}
				else
				{
					// Version < MAX
					
					$sql = 'SELECT 	ico.`Titre` as titre,
									IF(
										IFNULL(length(par.`DEFAUT`),0) = 0 
										AND
										IFNULL(length(par.`NEUTRE`),0) = 0
										,1
										,0
										) as "OB",
									par.`DEFAUT`,
									par.`NEUTRE`,
									par.`nom` as nom
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` ico LEFT join `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` par
							ON ico.`ID` = par.`ID`
							AND ico.`Version` = par.`Version` 
							AND par.`TYPE` = "IN"
							WHERE 1 = 1
							AND ico.`ID` = '.$this->c_id.' 
							AND ico.`Version` = '.$this->c_version;
				}
				
				break;
			case 'idossier':
				// TODO
				$sql = 'SELECT 1 as titre,"" as OB,"" as DEFAUT, "" as NEUTRE,"" as nom';
				break;
					
		}
		/*===================================================================*/
		$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link);
		
		if(mysql_num_rows($result) > 0)
		{
			$titre = mysql_result($result,0,'titre');
			
			if(mysql_result($result,0,'nom') == '')
			{
				$this->c_varin_objet = null;
			}
			else
			{
				if(mysql_num_rows($result) > 0) mysql_data_seek($result,0);
				$this->c_varin_objet = $result;
			}
			
			return $this->convertBBCodetoHTML($titre);
		}
		else
		{
			$this->c_varin_objet = $result;
			return '';
		}
	}	
	
	/**
	 * Vérification de l'existance de l'id et de la version
	 */
	private function verif_existance_id_and_version()
	{
		if($this->c_version == '')
		{
			/**==================================================================
			 * Verification de l'existance de l'ID
			 ====================================================================*/			
			switch($this->get_objet()) 
			{
				case 'ifiche':
					$sql = 'SELECT `id_fiche`  
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
							WHERE `id_fiche` = '.$this->c_id.'
							UNION ALL
							SELECT `num_version`  
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'` 
							WHERE `id_fiche` = '.$this->c_id.' 
							AND `num_version` = "'.$this->c_version.'"';
					$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link);
					break;
				case 'icode':
					$sql = 'SELECT ID 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' 
							WHERE ID = '.$this->c_id.'
							UNION ALL
							SELECT `version` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'`  
							WHERE `ID` = '.$this->c_id.' 
							AND `version` = "'.$this->c_version.'"';
					$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link);
					break;
				case 'password':
					$sql = 'SELECT `id` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_password']['name'].'`
							WHERE `id` = '.$this->c_id.' 
							UNION ALL
							SELECT "-"'; // id password not possible : eg : -
					
					$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link_password);
					break;
				case 'idossier':
					$sql = 'SELECT 1 as id';
					$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link);
					break;
			}
			
			
			if(mysql_num_rows($resultat) < 2)
			{	
				$this->c_ctrl_id = false;			// L'ID n'existe pas
				$this->c_ctrl_version = false; 		// La version n'existe pas
			}
			else
			{
				$this->c_ctrl_id = true;			// L'ID existe
				if(mysql_result($resultat, 1) == false)
				{
					$this->c_ctrl_version = false; 		// La version n'existe pas
				}
				else 
				{
					$this->c_ctrl_version = true; 		// La version n'existe pas
				}
			}	
			/*===================================================================*/	
		}
		else
		{
			$this->c_ctrl_id = true;			
			$this->c_ctrl_version = true; 		
		}
	}
	

	/**
	 * Retourne le style css a utiliser pour le lien
	 */
	private function get_style()
	{
		/**==================================================================
		 * Définition du style du cartouche
		 ====================================================================*/		
		if($this->c_ctrl_id == false || $this->c_ctrl_version == false)
		{
			return 'erreur'; 			
		}
		else
		{
			return $this->c_type_objet;
		}
		/*===================================================================*/	
	}
	
	/**
	 * Retourne le type d'objet pointé par le lien
	 */
	private function get_objet()
	{
		return $this->c_type_objet;
	}
	
	/**
	 * Retourne le niveau d'accès minimum d'un mot de passe
	 */
	private function get_niveau_password()
	{
		$sql = 'SELECT `level` 
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_password']['name'].'`
				WHERE `id` = '.mysql_real_escape_string($this->c_id);
		
		$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link_password);
		
		if(mysql_num_rows($result) > 0)
		{
			return mysql_result($result,0,'level');
		}
		else
		{
			return false;		// Le mot de passe n'existe pas.
		}
	}
	
	
	/**
	 * Retourne la liste des paramètres d'appel 
	 */
	private function get_param_appel()
	{
		// Récupération des paramètres de l'URL
		
		// tri des paramètres

		foreach($this->c_array_parameters as $key => $value)
		{
			$order[$key] = strtolower($value[0]);
		}
		
		if(is_array($order))
		{
			array_multisort($order,SORT_ASC,$this->c_array_parameters);
		}
		
		$html = '';
		
		/**==================================================================
		 * Flag parameters valorised in display mode ( Parameters not define in database ) // SIBY
		 ====================================================================*/		
		if($this->c_type == __FICHE_VISU__)
		{
			if($this->c_varin_objet != false)
			{
				foreach($this->c_array_parameters as $key => $value)
				{				
					if (strstr($value[1],'$'))
					{		
						$this->c_array_parameters[$key][2] = 2;				
						/**==================================================================
						 * RECHERCHE DES VARIABLES DE LA FICHE 
						 ====================================================================*/								
						if(mysql_num_rows($this->c_resultat) > 0) mysql_data_seek($this->c_resultat,0);
						while($row = mysql_fetch_array($this->c_resultat,MYSQL_ASSOC))
						{
							if (strstr($value[1],$row['NOM']) )
							{						
								$this->c_array_parameters[$key][2] = 0;
								break;
							}
						}
						
					}
					else
					{
						$this->c_array_parameters[$key][2] = 2;	
					}												
				}
			}
		}
		/*===================================================================*/
				
		
		/* Récupération des paramètres d'entrée de l'objet appelé */
		if($this->c_type == __FICHE_MODIF__)
		{
			/* On affiche tous les paramètres d'entrée de l'objet appelé, même si ils ne sont pas renseignés. */
			// Tableau contenant les variables
			$tab_varin_param_appele = array();			// 0 -> nom, 1 -> valeur, 2 -> obligatoire
			$tab_varin_param_non_appele = array();		// 0 -> nom, 1 -> obligatoire
			
			// Indice du tableau
			$i_appele = 0;
			$i_non_appele = 0;
			
			// Parcours des paramètres d'entrée de l'objet appelé
			if($this->c_varin_objet != false)
			{
				while($row = mysql_fetch_array($this->c_varin_objet,MYSQL_ASSOC))
				{
					$trouve = false;				// Flag de recherche du paramètre
					foreach($this->c_array_parameters as $key => $value)
					{
						// Parcours des paramètres de l'url
						if($value[0] == $row['nom'])
						{
							// Le paramètre de l'objet appelé est passé dans l'url
							$tab_varin_param_appele[$i_appele][0] = $value[0];				// Nom
							$tab_varin_param_appele[$i_appele][1] = $value[1];				// Valeur
							$tab_varin_param_appele[$i_appele][2] = $row['OB'];				// Obligatoire (0 : non / 1 : oui)
							$i_appele = $i_appele + 1;										// Indice du tableau
							$trouve = true;
							$this->c_array_parameters[$key][2] = 1;							// Flag de recherche
							break;															// On ne continue pas la recherche.
						}
					}
											
					if(!$trouve)
					{
						switch($this->c_ik_valmod)
						{
							case 1:
								if($row['DEFAUT'] == "")
								{
									$tab_varin_param_non_appele[$i_non_appele][0] = $row['nom'];		// Nom
									$tab_varin_param_non_appele[$i_non_appele][1] = $row['OB'];			// Obligatoire (0 : non / 1 : oui)
									$i_non_appele = $i_non_appele + 1;									// Indice du tablea
								}
								break;
							case 2:
								if($row['NEUTRE'] == "")
								{
									$tab_varin_param_non_appele[$i_non_appele][0] = $row['nom'];		// Nom
									$tab_varin_param_non_appele[$i_non_appele][1] = $row['OB'];			// Obligatoire (0 : non / 1 : oui)
									$i_non_appele = $i_non_appele + 1;									// Indice du tablea
								}
								break;
							case 3:
								if($row['DEFAUT'] == "" && $row['NEUTRE'] == "")
								{
									$tab_varin_param_non_appele[$i_non_appele][0] = $row['nom'];		// Nom
									$tab_varin_param_non_appele[$i_non_appele][1] = $row['OB'];			// Obligatoire (0 : non / 1 : oui)
									$i_non_appele = $i_non_appele + 1;									// Indice du tablea
								}
								break;
						}
					}
					
				}
										//error_log(print_r($tab_varin_param_appele,true));
				
			}
			
			if($this->c_varin_objet != false) mysql_data_seek($this->c_varin_objet,0);
			
			//error_log(print_r($tab_varin_param_non_appele,true));
			// Paramètre non passé dans l'URL
			$lbl_obligatoire = $this->generer_texte_aide(377,'id_aide');				// Libelle d'aide pour le drapeau du paramètre obligatoire
			$this->c_nbr_param_non_appele = 0;
			foreach($tab_varin_param_non_appele as $value)
			{				
				if($value[1] == '1')
				{
					$icone = '<div class="obligatoire" '.$lbl_obligatoire.'></div>';	// Obligatoire
				}
				else // Pas obligatoire
				{
					$icone = '';
				}
				$html .= '<tr><td>'.$icone.'</td><td><i style="color:grey;">'.$value[0].'</i></td><td></td><td></td></tr>';
				$this->c_nbr_param_non_appele++;
			}	
		}
		/* END FICHE_MODIF MODE */
		/*===================================================================*/

		// SIBY ISHEET IKVALMOD HERITAGE HAVE TO BE COMPLETED
		// SIBY THIS IF BLOCK BELOW USEFULL ?????
		if($this->c_varin_objet != false)
		{
			// Add default or neutral parameters if needed (depending of IK_VALMOD value)
			switch($this->c_ik_valmod)
			{
				case 1:
					// Default
					while($row = mysql_fetch_array($this->c_varin_objet,MYSQL_ASSOC))
					{
						if($row["DEFAUT"] != '' && !isset($this->c_speed_param_name[$row["nom"]]))
						{
							$this->c_array_parameters[] = Array(0 => $row["nom"],1 => $row["DEFAUT"],2 => 2,3 => 1);
						}
					}
					break;
				case 2:
					// Neutral
					while($row = mysql_fetch_array($this->c_varin_objet,MYSQL_ASSOC))
					{
						if($row["NEUTRE"] != '' && !isset($this->c_speed_param_name[$row["nom"]]))
						{
							$this->c_array_parameters[] = Array(0 => $row["nom"],1 => $row["NEUTRE"],2 => 2,3 => 2);
						}
					}
					break;
				case 3:
					// Neutral & Default
					while($row = mysql_fetch_array($this->c_varin_objet,MYSQL_ASSOC))
					{
						if(!isset($this->c_speed_param_name[$row["nom"]]))
						{
							if($row["NEUTRE"] != '')
							{
								$this->c_array_parameters[] = Array(0 => $row["nom"],1 => $row["NEUTRE"],2 => 2,3 => 2);
							}
							else
							{
								if($row["DEFAUT"] != '')
								{
									$this->c_array_parameters[] = Array(0 => $row["nom"],1 => $row["DEFAUT"],2 => 2,3 => 1);
								}
							}
						}
					}
					break;
			}
		}

		foreach($this->c_array_parameters as $value)
		{
			$array_val_not_replaced[] = $value[1];
		}
		
		$array_val_not_replaced = $this->get_type_valorisation($array_val_not_replaced,$this->c_resultat);
		
		foreach($this->c_array_parameters as $key => $value)
		{
			if($value[0] != 'ID' && $value[0] != 'version' && $value[0] != 'IK_CARTRIDGE' && $value[0] != '')
			{
				$this->c_nbr_param_appel = $this->c_nbr_param_appel + 1;
				
				if(isset($value[3]))
				{
					$icone = 'value_type_'.$value[3];
				}
				else
				{
					$icone = 'no_value_type';
				}

				if($value[2] == 2 )
				{
					$html .= '<tr><td><div class="'.$icone.'"></div></td><td>'.$value[0].'</td><td class="arrow_cart">'.$_SESSION[$this->c_ssid]['configuration'][30].'</td><td class="ikvalorised fw">'.$array_val_not_replaced[$key].'</td></tr>';
				}
				else
				{
					$html .= '<tr><td><div class="'.$icone.'"></div></td><td>'.$value[0].'</td><td class="arrow_cart">'.$_SESSION[$this->c_ssid]['configuration'][30].'</td><td class="fw">'.$array_val_not_replaced[$key].'</td></tr>';
				}

				$array_val_not_replaced[$key] = str_replace('\\','\\\\',$array_val_not_replaced[$key]);
				$array_val_not_replaced[$key] = str_replace('"','\"',$array_val_not_replaced[$key]);
				
				if($this->c_requete_vimofy_cartouche['in'] == '')
					$this->c_requete_vimofy_cartouche['in'] .= 'SELECT "'.$value[0].'" as "param","'.$array_val_not_replaced[$key].'" as "value"';
				else
					$this->c_requete_vimofy_cartouche['in'] .= ' UNION ALL SELECT "'.$value[0].'" as "param","'.$array_val_not_replaced[$key].'" as "value"';
			}
		}

		if($html == '')
		{
			return false;
		}
		else
		{
			return '<table>'.$html.'</table>';
		}
	}

	/**
	 * Get ik_valmod level of the current object in the url browser
	 */
	private function get_ik_valmod()
	{
		(isset($_GET['IK_VALMOD'])) ? $ik_valmod = $_GET['IK_VALMOD'] : $ik_valmod = 0;
		
		return $ik_valmod;
	}

	
	private function get_dataset_info_recuperees()
	{
		if($this->get_objet() == 'password')
		{
			
			$sql = 'SELECT nom,id_action_src,DESCRIPTION,COMMENTAIRE
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
					WHERE id_fiche = '.$this->c_id_temp.'
					AND id_action = '.$this->c_id_etape.'
					AND id_action_src = '.$this->c_id.'
					AND num_version_src = 0
					AND id_src = 0
					AND type = "OUT"
					ORDER BY nom,id_action_src';
		}
		else
		{
			$sql = 'SELECT nom,id_action_src,DESCRIPTION,COMMENTAIRE
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
					WHERE id_fiche = '.$this->c_id_temp.'
					AND id_action = '.$this->c_id_etape.'
					AND id_src = '.$this->c_id.'
					AND type = "EXTERNE"
					ORDER BY nom,id_action_src';
		}
		
		
		$this->c_dataset_info_recuperees = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->c_link);
		$this->c_nbr_info_recuperees = mysql_num_rows($this->c_dataset_info_recuperees);
	}
	
	/**
	 * Retourne la liste des infromations récupérées
	 */
	private function get_info_recuperees()
	{
		$html = '<table>';
		while ($row = mysql_fetch_array($this->c_dataset_info_recuperees,MYSQL_ASSOC))
		{
			$html .= '<tr><td><span class="BBVarExt">'.$row['nom'].'</span></td><td class="arrow_cart">'.$this->convertBBCodetoHTML($row['DESCRIPTION']).'</td><td>'.$this->convertBBCodetoHTML($row['COMMENTAIRE']).'</td></tr>';
			if($this->c_requete_vimofy_cartouche['out'] == '')
				$this->c_requete_vimofy_cartouche['out'] .= 'SELECT "'.$this->protect_sql($row['nom']).'" as "1","'.($this->protect_sql($row['DESCRIPTION'])).'" as "2","'.$this->protect_sql($row['COMMENTAIRE']).'" as "3"';
			else
				$this->c_requete_vimofy_cartouche['out'] .= ' UNION ALL SELECT "'.$this->protect_sql($row['nom']).'" as "1","'.$this->protect_sql($row['DESCRIPTION']).'" as "2","'.$this->protect_sql($row['COMMENTAIRE']).'" as "3"';
		}
		
		return $html.'</table>';
	}
	
	
	private function protect_sql($txt)
	{
		$txt = str_replace('\\','\\\\',$txt);
		$txt = str_replace('"','\"',$txt);
		
		return $txt;
	}
	/**
	 * Retourne un tableau contenant les paramètres de l'url
	 * Array
	 * (
     * 		[0] => Array
	 *      (
	 *          [0] => ID
	 *          [1] => 831
	 *      )
	 *
	 *	    [1] => Array
	 *      (
     *	        [0] => version
     *	        [1] => 3
 	 *		)
	 *
	 * )
	 */
	private function decouper_url()
	{
		/**==================================================================
		 * ANALYSE DE L'URL
		 ====================================================================*/
		$parametre_url_get = html_entity_decode($this->c_parametres);
		$parametre_url_get = substr($parametre_url_get,1);	// Supprime le ? au début de l'URL		
		$parametre_url_get = explode('&',$parametre_url_get);
		$i = 0;
		foreach($parametre_url_get as $value)
		{
			$explode = explode('=',$value);
			
			if($explode[0] != '')
			{
				// Get the ik_valmod
				if($explode[0] == 'IK_VALMOD')
				{
					$this->c_ik_valmod = $explode[1];
				}
				else
				{
					$valorisation_variable[$i][0] = urldecode($explode[0]);
					if(isset($explode[1]))
						$valorisation_variable[$i][1] = urldecode($explode[1]);
					else
					{
						$valorisation_variable[$i][1] = null;
					}
					$this->c_speed_param_name[$valorisation_variable[$i][0]] = true;
					$valorisation_variable[$i][2] = 0;			// Flag de recherche
					
					$i = $i + 1;
				}
			}
		}		
		/*===================================================================*/
		
		return $valorisation_variable;
	}
	
	
	/**
	 * Retourne les variables stockée en base de données avec le bon type de valorisation
	 * @param string $valorisation Valeur du paramètre dans le lien
	 */
	private function get_type_valorisation($valorisation,&$resultat)
	{
		// On décode la variable pour ne plus qu'elle est le format URL encodé.
		//$valorisation = urldecode($this->c_array_parameters);
		/**==================================================================
		 * RECHERCHE DU TYPE DE VALORISATION : VARIABLES EN BASE 
		 ====================================================================*/		
		if(mysql_num_rows($resultat) > 0) mysql_data_seek($resultat,0);
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
		{
			$f_valorised = false;
			/**==================================================================
			 * Get style var
			 ====================================================================*/		
			switch($row['TYPE'])
			{
				case 'EXTERNE':
					if($row['id_action'] != $this->c_id_etape)
						$style_var = 'BBVarinExt';
					else
						$style_var = 'BBVarExt';
					break;
				case 'IN':
					$style_var = 'BBVarIn';
					break;											
				case 'OUT':
					if($row['id_action'] != $this->c_id_etape)
						$style_var = 'BBVarInl';
					else
						$style_var = 'BBVarOut';
					break;
			}
			
			/*===================================================================*/
			if($row['TYPE'] == 'EXTERNE')
			{
				if($this->c_id_etape != $row['id_action'])
				{
					/**==================================================================
					 * Get value var
					 ====================================================================*/		
					if($this->c_type == __FICHE_VISU__)
					{
						if($this->c_ik_valmod_iobject > 0)
						{
							switch($this->c_ik_valmod_iobject) 
							{
								case 3:	// DEFAUT & NEUTRE
									if($row['resultat'] == '' && $row['NEUTRE'] != '')
									{
										$val = $row['NEUTRE'];
										$f_valorised = true;
									}
									else
									{
										if($row['resultat'] == '' && $row['DEFAUT'] != '')
										{
											$val = $row['DEFAUT'];
											$f_valorised = true;
										}	
										else
										{
											if($row['resultat'] == '')
											{
												$val = $row['type_externe1'];							
											}
											else
											{
												$val = $row['resultat'];
												$f_valorised = true;
											}	
										}	
									}	
									break;
								case 1:	// DEFAUT
									if($row['resultat'] == '' && $row['DEFAUT'] != '')
									{
										$val = $row['DEFAUT'];
										$f_valorised = true;
									}	
									else
									{
										if($row['resultat'] == '')
										{
											$val = $row['type_externe1'];
										}
										else
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}
									}
									break;
								case 2:	// NEUTRE
									if($row['resultat'] == '' && $row['NEUTRE'] != '')
									{
										$val = $row['NEUTRE'];
										$f_valorised = true;
									}
									else
									{
										if($row['resultat'] == '')
										{
											$val = $row['type_externe1'];
										}
										else
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}	
									}		
									break;
							}
						}
						else
						{
							// Pas de mode par défaut ni neutre.
							if($row['resultat'] == '')
							{
								$val = $row['type_externe1'];
							}	
							else
							{
								$val = $row['resultat'];
								$f_valorised = true;
							}	
						}
					}
					else
					{
						$val = $row['type_externe1'];
					}
					/*===================================================================*/
					// Replace content of var
					$valorisation = str_replace($row['cache_type_externe1'],'<span class="'.$style_var.'">'.$val.'</span>',$valorisation);
				}	
				else
				{
					/**==================================================================
					 * Get value var
					 ====================================================================*/	
					if($this->c_type == __FICHE_VISU__)
					{
						if($this->c_ik_valmod_iobject > 0)
						{
							switch($this->c_ik_valmod_iobject) 
							{
								case 3:	// DEFAUT & NEUTRE
									if($row['resultat'] == '' && $row['NEUTRE'] != '')
									{
										$val = $row['NEUTRE'];
										$f_valorised = true;
									}	
									else
									{
										if($row['resultat'] == '' && $row['DEFAUT'] != '')
										{
											$val = $row['DEFAUT'];
											$f_valorised = true;
										}
										else
										{
											if($row['resultat'] == '')
											{
												$val = $row['type_out'];
											}
											else
											{
												$val = $row['resultat'];
												$f_valorised = true;
											}
										}
									}	
									break;
								case 1:	// DEFAUT
									if($row['resultat'] == '' && $row['DEFAUT'] != '')
									{
										$val = $row['DEFAUT'];
										$f_valorised = true;
									}	
									else
									{	
										if($row['resultat'] == '')
										{
											$val = $row['type_out'];
										}
										else
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}
									}
									break;
								case 2:	// NEUTRE
									if($row['resultat'] == '' && $row['NEUTRE'] != '')
									{
										$val = $row['NEUTRE'];
									}	
									else
									{
										if($row['resultat'] == '')
										{
											$val = $row['type_out'];
										}												
										else
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}	
									}
									break;
							}
						}
						else
						{
							// Pas de mode par défaut ni neutre.
							if($row['resultat'] == '')
							{
								$val = $row['type_out'];
							}
							else
							{
								$val = $row['resultat'];
								$f_valorised = true;
							}						
						}
					}
					else
					{
						$val = $row['type_out'];
					}
					/*===================================================================*/
					$valorisation = str_replace($row['cache_type_out'],'<span class="'.$style_var.'">'.$val.'</span>',$valorisation);
				}
			}
			else
			{
				/**==================================================================
				 * Get value var witch is not external 
				 ====================================================================*/	
				if($this->c_type == __FICHE_VISU__)
				{
					if($this->c_ik_valmod_iobject > 0)
					{
						switch($this->c_ik_valmod_iobject) 
						{
							case 3:	// DEFAUT & NEUTRE
								if($row['resultat'] == '' && $row['resultatnull'] == '' && $row['NEUTRE'] != '')
								{
									$val = $row['NEUTRE'];
									$f_valorised = true;
								}	
								else
								{
									if($row['resultat'] == '' && $row['resultatnull'] == '' && $row['DEFAUT'] != '')
									{
										$val = $row['DEFAUT'];
										$f_valorised = true;
									}
									else
									{
										if($row['resultat'] == '' && $row['resultatnull'] == '')
										{
											if($row['TYPE'] == 'OUT' && $row['id_action'] == $this->c_id_etape)
											{
												$val = $row['NOM'];
											}
											else
											{
												$val = $row['type_externe1'];
												$f_valorised = false;
											}
										}
										else
										{
											if($row['resultatnull'] == '')
											{
												$val = $row['resultat'];
												$f_valorised = true;
											}
											else
											{
												$val = $row['type_externe1'];
												$f_valorised = false;
											}
										}
									}		
								}	
								break;
							case 1:	// DEFAUT
								if($row['resultat'] == '' && $row['resultatnull'] == '' && $row['DEFAUT'] != '')
								{
									$val = $row['DEFAUT'];
									$f_valorised = true;
								}	
								else
								{
									if($row['resultat'] == '' && $row['resultatnull'] == '')
									{
										if($row['TYPE'] == 'OUT' && $row['id_action'] == $this->c_id_etape)
										{
											$val = $row['NOM'];
										}
										else
										{
											$val = $row['type_externe1'];
										}
									}
									else
									{
										if($row['resultatnull'] == '')
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}
										else
										{
											$val = $row['type_externe1'];
											$f_valorised = false;
										}
									}
								}		
								break;
							case 2:	// NEUTRE
								if($row['resultat'] == '' && $row['resultatnull'] == '' && $row['NEUTRE'] != '')
								{
									$val = $row['NEUTRE'];
									$f_valorised = true;
								}	
								else
									if($row['resultat'] == '' && $row['resultatnull'] == '')
									{
										if($row['TYPE'] == 'OUT' && $row['id_action'] == $this->c_id_etape)
										{
											$val = $row['NOM'];
										}
										else
										{
											$val = $row['type_externe1'];
										}
									}
									else
									{
										if($row['resultatnull'] == '')
										{
											$val = $row['resultat'];
											$f_valorised = true;
										}
										else
										{
											$val = $row['type_externe1'];
											$f_valorised = false;
										}
									}	
								break;
						}
					}
					else
					{
						// Pas de mode par défaut ni neutre.
						if($row['resultat'] == '')
						{
							$val = $row['type_externe1'];
							$f_valorised = false;
						}
						else
						{
							$val = $row['resultat'];
							$f_valorised = true;
						}	
					}
				}
				else
				{
					if($row['TYPE'] == 'OUT' && $row['id_action'] == $this->c_id_etape)
					{
						$val = $row['NOM'];
					}
					else
					{
						$val = $row['type_externe1'];
					}
				}
				/*===================================================================*/
				
				// Specific because string with only space digit is not visible in css span :(
				// So, do a raw replace space by &nbsp;
				if($f_valorised && trim($val) == "") // ADD_VARIN_VISIBLE_BLANK
				{
					$val = str_replace(" ","&nbsp",$val);	
				}
				
				// Replace content of var
				if($row['TYPE'] == 'OUT' && $row['id_action'] == $this->c_id_etape)
				{
					if($f_valorised)
					{
						$valorisation = str_replace($row['cache_type_out'],'<span class="ikvalorised '.$style_var.'">'.$val.'</span>',$valorisation);
					}
					else
					{
						$valorisation = str_replace($row['cache_type_out'],'<span class="'.$style_var.'">'.$val.'</span>',$valorisation);
					}
					
				}
				else
				{
					// VARIN SET SIBY
					if($f_valorised)
					{
						$valorisation = str_replace($row['cache_type_externe1'],'<span onmouseover="ikdoc(\'id_aide\');set_text_help(461,\'\',\'\',\'<span class=\\\'BBVarInInfo\\\'>'.$row['NOM'].'</span>\');" onmouseout="ikdoc();unset_text_help();" class="ikvalorised ikvalorised_varin '.$style_var.'">'.$val.'</span>',$valorisation); // REMIND_ORIGINAL_VARIN_NAME
					}
					else
					{
						$valorisation = str_replace($row['cache_type_externe1'],'<span class="'.$style_var.'">'.$val.'</span>',$valorisation);
					}
					
				}
			}
		}
		/*===================================================================*/	

		return $valorisation;
	}
	

	
	/**
	 * Récupère la valeur ik_cartridge du lien. Si la valeur n'est pas présente retourne la valeur par défaut (7)
	 */
	private function get_ik_cartridge()
	{
		/**==================================================================
		 * RECHERCHE DU PARAMETRE IK_CARTRIDGE DANS L'URL
		 ====================================================================*/	
		$motif = '#&(amp;)?IK_CARTRIDGE=([0-9]*)#i';
		preg_match_all($motif,$this->c_parametres,$out);
		/*===================================================================*/	
		
		if(isset($out[2][0]) && is_numeric($out[2][0]))
		{
			// IK_CARTRIDGE défini dans l'url
			return $out[2][0];
		}
		else
		{
			// IK_CARTRIDGE non défini dans l'url, on retourne la valeur par défaut
			return 7;
		}
	}
	
	private function convertBBCodetoHTML($txt)
	{
		$remplacement=true;
		while($remplacement)
		{
			$remplacement=false;
			$oldtxt=$txt;

			$txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','<b>\\1</b>',$txt);
			$txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','<i>\\1</i>',$txt);
			$txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','<u>\\1</u>',$txt);
			$txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','<s>\\1</s>',$txt);
			$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<font color="\\1">\\2</font>',$txt);
			$txt = preg_replace('`\[br\]`','<br>',$txt);
			
			if ($oldtxt<>$txt)
			{
				$remplacement=true;
			}
		}
		return $txt;
	}		
	
	/**
	 * Génère les evenements html pour l'aide d'un objet
	 * @param decimal $id_message	Identifiant du message en base de données
	 * @param decimal $id_aide		Identifiant de la page d'aide (ID_CHILD)
	 * @return string
	 */
	public function generer_texte_aide($id_message,$id_aide)
	{
		return 'onmouseover="ikdoc(\''.$id_aide.'\');set_text_help('.$id_message.');" onmouseout="ikdoc();unset_text_help();"';
	}
}	
?>