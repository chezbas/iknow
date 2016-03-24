<?php
/************************************************************************************************************
 *						Class to set up iSheet's steps														*
 ************************************************************************************************************/
class sheet_steps extends class_bdd 
{
	// --------------------------------------------------- DECLARATION DES ATTRIBUTS -----------------------------------------------------------//
	private $c_version;							// Contient la version de la fiche
	private $c_id;								// Contient l'id de la fiche
	private $c_Obj_etapes;						// Contient un tableau d'objets etapes, chaque indice du tableau contient un objet qui contient lui meme une etape.
	private $c_ssid;							// ssid de l'instance
	private $c_nbr_etapes;						// Contient le nombre d'étape de la fiche en cours
	private $c_id_temp;							// Id temporaire de l'instance
	private $c_version_app;						// Repertoire de base (par rapport à la version active)
	private $c_requete_var_externe;				// Contient la requete sql pour afficher les variables externe d'une etape
	private $c_variables_fiche;					// Contient les variables de la fiche array(0 => nom,1 => valeur,2 => type, 3 => id_etape,4 => utilisee,5 => defaut,6 => neutre,7 => in url);
	private $c_motif_expr_reg;					// Tableau qui contient les motifs des principales expression régulière (comme les span des varin, varout...)
	private $c_date;							// Date de la sauvegarde de la fiche
	private $c_statut;							// Statut de la fiche
	private $c_requete_vimofy_cartouche;		// Tableau qui contient les requetes des vimofy des cartouches des étapes
	private $c_tab_champ_speciaux;				// Contient les statistiques pour les champs spéciaux.
	private $type;								// Contient le type de lock, soit 1 pour la modification d'une fiche et 2 pour la visualsiation.
	private $c_ik_valmod;						// Type de valorisation, DEFAUT, NEUTRE, DEFAUT ET NEUTRE, NORMAL		
	private $c_commentaire_varinext_message;
	private $c_commentaire_varinext_replace;
	private $c_commentaire_varinlocal_message;
	private	$c_commentaire_varinlocal_replace;
	private $c_commentaire_varin_message;
	private	$c_commentaire_varin_replace;
	private $c_ikcalc_error;
	/**
	 * Constructeur de la classe étape
	 * 
	 * @param $p_id	Id de la fiche
	 * @param $p_version Version de la fiche
	 * @param $p_ssid Identifiant de session de la fiche
	 * @param $p_temp Identifiant temporaire
	 * @param $p_includes Repertoire d'inclusion (version)
	 * @param $p_date Date de la modification de la fiche
	 * @param $p_statut Statut de la fiche
	 */
	public function __construct($p_id,$p_version,$p_ssid,$p_id_temp,$p_version_app,$p_date,$p_statut,$p_type,&$p_ik_valmod) 
	{	
		parent::__construct($p_ssid,$p_id,$p_id_temp,$p_version,$p_type);
		/************************************************************************************************************
		 *										Initialisation des attributs
		 ************************************************************************************************************/
		// Instance de la totalité des étapes
		$this->db_connexion();
		$this->c_id = $p_id;
		$this->c_version = $p_version;
		$this->c_ik_valmod = $p_ik_valmod;
		$this->c_ssid = $p_ssid;
		$this->c_id_temp = $p_id_temp;
		$this->c_version_app = $p_version_app;
		$this->type = $p_type;
		$this->c_motif_expr_reg = $this->generer_motif_expreg();
		$this->c_date = $p_date;
		$this->c_statut = $p_statut;
		$this->c_ikcalc_error = false;
		//$this->init_value_champ_speciaux();
		$this->c_requete_vimofy_cartouche = array();
		/*************************************************************************************************************/
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
	 * Initilisation des valeurs des champs spéciaux.
	 */
	public function init_value_champ_speciaux()
	{
		$this->c_tab_champ_speciaux['__TOTAL_VARIN__'] = $this->get_total_var('"IN"');
		$this->c_tab_champ_speciaux['__TOTAL_VAROUT__'] = $this->get_total_var('"OUT","EXTERNE"');
		$this->c_tab_champ_speciaux['__TOTAL_TAG__'] = $this->get_total_tag();
	}
	
	
	/**
	 * Retourne un tableau qui contient les principales expression regulières utilisées
	 * @return array
	 */
	private function generer_motif_expreg()
	{
		$array_motif_expreg['BBVarIn'] = '#class="BBVarIn">([^<]+)#';
		$array_motif_expreg['BBVarInl'] = '#<span class="BBVarInl">([^(]+)(\(|</span>)#';
		$array_motif_expreg['BBVarOut'] = '#class="BBVarOut">([^<]+)#';
		$array_motif_expreg['BBVarInExt'] = '#class="BBVarInExt">([^>]+)\(([0-9]*)([0-9\\\\]*)\)([^<]*)</span>#i';
		$array_motif_expreg['BBVarExt'] = '#class="BBVarExt">([^>]+)\(([0-9]*)\\\([0-9]*)\)</span>#i';			
		$array_motif_expreg['Etape'] = '#<a.+href="\#([0-9]+)">Etape ([0-9]+)</a>#';
		$array_motif_expreg['lien_objet'] = '#<a.+href="((ifiche|icode|idossier).php([^"]+))"#';
		$array_motif_expreg['lien'] = '#href="([^"]+)"#';
		$array_motif_expreg['num_etape'] = '#<a.+href="\#([0-9]+)">#';
		$array_motif_expreg['fiches'] = '#<a.+href="ifiche.php\?(&amp;)?ID=([0-9]+)("|">|&amp;)#';
		$array_motif_expreg['codes'] = '#<a.+href="icode.php\?(&amp;)?ID=([0-9]+)("|">|&amp;)#';
		$array_motif_expreg['param_url_ext'] = '#\$([^$]+\([0-9]+[\\\\]+[0-9]+[\\\\]+[0-9]+\)+)\$#';
		
		/* On recherche toutes les chaînes qui commencent par href=" --> (href=") 
		 * Jusqu'à la prochaine double quote --> ([^"]+) 
		 * Sur chaque chaîne identifiée, on exclut celles contenant ifiche.php ou icode.php ou idossier.php --> (?<!ifiche\.php|icode\.php|idossier\.php) <- Assertion d'exclusion à gauche
		 */
		$array_motif_expreg['lien_non_objet'] = '#href="(.+(?<!ifiche\.php|icode\.php|idossier\.php)\?)([^"]+)#'; 

		return $array_motif_expreg;
	}
	
	public function set_ik_valmod($p_value)
	{
		$this->c_ik_valmod = $p_value;
	}
	
	/**
	 * Methode qui permet de generer les etapes depuis la base de données.
	 */
	public function generer_etapes() 
	{	
		if ($this->c_id != 'new') 
		{			
			// Comptabilisation du nombre d'étapes que comporte la fiche
			$this->c_nbr_etapes = $this->compter_etapes();
			
			// Stockage des étapes sous forme de dataset
			$dataset = $this->get_contenu_etape();
			
			// Création pour chaques étapes d'une instance de la classe_etape
			for($j = 0; $j < $this->c_nbr_etapes;$j = $j + 1)
			{
				// Instanciation de l'objet steps pour l'étape $j
				$this->c_Obj_etapes[$j] = new step_alone();
				
				// Stockage du numéro de l'étape
				$this->c_Obj_etapes[$j]->numero = $j + 1;
				
				// Stockage du contenu de l'étape
				$this->c_Obj_etapes[$j]->contenu = $dataset[$j]['description'];
			}
		}
		else
		{
			// Stockage du nombre d'étape de la fiche
			$this->c_nbr_etapes = 1;
			
			// Instanciation de l'objet steps pour l'étape $j
			$this->c_Obj_etapes[0] = new step_alone();
			
			// Stockage du numero de l'étape
			$this->c_Obj_etapes[0]->numero = 1;
			
			// Stockage du contenu de l'étape
			$this->c_Obj_etapes[0]->contenu = '';	
		}
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
	
	
	/**	
	 * Methode qui permet de generer le contenu html des etapes et de l'afficher
	 * Elle recupere le contenu des etapes depuis $this->c_Obj_etapes[$j]->contenu
	 *
	 * @param $appel_ajax false ou vide si demandé par le serveur, true si demandé par appel ajax
	 * @return texte
	 */
	public function display_step($appel_ajax = false) 
	{
		/**==================================================================
		 * ANALYSE DES LIENS VERS DES iOBJETS ET STOCKAGE DES VARIN EXTERNES
		 ====================================================================*/
		foreach($this->c_Obj_etapes as $obj_etape)
		{
			$this->c_Obj_etapes[($obj_etape->numero - 1)]->lien_iobjet_etape = null;
			
			$this->analyse_lien($obj_etape->contenu,$obj_etape->numero,'ifiche.php');
			$this->analyse_lien($obj_etape->contenu,$obj_etape->numero,'icode.php');
			$this->analyse_lien($obj_etape->contenu,$obj_etape->numero,'idossier.php');	
			
			$this->analyse_password($obj_etape->contenu,$obj_etape->numero);
			
			$this->c_Obj_etapes[$obj_etape->numero - 1]->tab_tag = 0;
			
			if(is_array($this->c_Obj_etapes[($obj_etape->numero - 1)]->lien_iobjet_etape))
			{
				$this->c_Obj_etapes[($obj_etape->numero - 1)]->lien_iobjet_etape = array_unique($this->c_Obj_etapes[($obj_etape->numero - 1)]->lien_iobjet_etape,SORT_REGULAR);
			}
		}
		/*===================================================================*/
		
		/**==================================================================
		 * MISE EN CACHE DE TOUTES LES VARIABLES DE LA FICHE
		 * Tableau $this->c_variables_fiche
		 ====================================================================*/
		$this->set_cache_variable();
		/*===================================================================*/
		
		/**==================================================================
		 * Tableau général
		 ====================================================================*/	
		if(!$appel_ajax)
		{
			$content_etape = '<div id="mesetapes" class="tab_cont"><table class="wfull">';
		}
		else
		{
			$content_etape = '<table class="wfull">';
		}
		/*===================================================================*/
		
		/**==================================================================
		 * Entête des étapes
		 ====================================================================*/		
		//$content_etape .= '<tr><td class=haut width="3%">'.$_SESSION[$this->c_ssid]['message'][69].'</td><td class=haut width="2%"></td><td class=haut width="95%">'.$_SESSION[$this->c_ssid]['message'][70].'</td></tr>';
		/*===================================================================*/
		
		/**==================================================================
		 * Première ligne de séparation
		 ====================================================================*/
		if($this->type == __FICHE_MODIF__)	
		{
			$content_etape .= '<tr class="sep_lp">';
			$content_etape .= '<td id="ajouter1">';
			$content_etape .= '<table width="20px" style="margin-top:0; margin-right:auto; margin-bottom:0; margin-left:auto;">';
			$content_etape .= '<tr>';
			$content_etape .= '<td>';
			$content_etape .= '<div name="1" '.$this->generer_texte_aide(71,'id_aide').' onclick="ajouter_etape(1);" class="ajouter"></div>';
			$content_etape .= '</td>';
			$content_etape .= '</tr>';
			$content_etape .= '</table>';
			$content_etape .= '</td>';
			$content_etape .= '<td>';
			$content_etape .= '<div id="ajax_load_etape0"></div>';
			$content_etape .= '</td>';
			$content_etape .= '<td>';
			$content_etape .= '<div class="ajax_nbr_tentative" id="ajax_step_qtt_retrieve0"></div>';
			$content_etape .= '</td>';
			$content_etape .= '</tr>';
		}
		else
		{
			$content_etape .= '<tr class="sep_lp"><td COLSPAN=3 >&nbsp;</td></tr>';
		}
		/*===================================================================*/
						
		/**==================================================================
		 *Définition des constantes pour le traitement d'affichage des étapes	
		 ====================================================================*/								 
		// Recherche des tags pour les étapes 
		$sql = 'SELECT COUNT(1) as nb_tags,Etape 
					 FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
					 WHERE ID = '.$this->c_id_temp.' 
					 AND Etape > 0 
					 GROUP BY Etape';
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
		{
			$this->c_Obj_etapes[($row['Etape'] - 1)]->tab_tag = $row['nb_tags'];
		}
		
		unset($resultat);
		/*===================================================================*/
		if($this->type == __FICHE_VISU__)
		{
			$this->replace_var_by_value();
			$this->remplacer_var_lien_non_iobjet($obj_etape->contenu,$obj_etape->numero);	
		}
		/**==================================================================
		 * LIBELLE VARINEXT
		 ====================================================================*/
		$sql = 'SELECT CONCAT(nom,\'(\',id_action,\'\\\\\',id_src,\'\\\\\',id_action_src,\')\') as variable,id_action,id_src,id_action_src,nom  
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND TYPE = "EXTERNE"
				and used = 1';

		$resultat_commentaire_externe = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
		
		/**==================================================================
		 * LIBELLE VARINLOCAL
		 ====================================================================*/
		$sql = 'SELECT CONCAT(nom,\'(\',id_action,\')\') as variable,id_action,nom  
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND TYPE = "OUT"';

		$resultat_commentaire_varinlocal= $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
		
		/**==================================================================
		 * LIBELLE VARINLOCAL
		 ====================================================================*/
		$sql = 'SELECT CONCAT(nom,\'()\') as variable,id_action,nom  
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND TYPE = "IN"';

		$resultat_commentaire_varin= $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
		
		
		/**==================================================================
		 * Remplacement des variables dans les liens // SIBY
		 ====================================================================*/		
		$sql = "SELECT DISTINCT `NOM`, 
								`TYPE`,
								`id_action`,
								`resultat`,
								(IF
									(`resultat` is null
									,\"X\"
									,\"\")
								) AS \"resultatnull\",
								`DEFAUT`,
								`NEUTRE`,
								(SELECT (CASE id_action 
										 WHEN '0' THEN CONCAT(nom,'()') 
					                     ELSE (SELECT CASE id_src 
								                      WHEN 0 THEN CONCAT(nom,'(',id_action,')') 
								                      ELSE CONCAT(nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')') END)
					                     END)) as type_externe1,
					                     (SELECT (CASE id_action 
										 WHEN '0' THEN CONCAT('$',nom,'()$') 
					                     ELSE (SELECT CASE id_src 
								                      WHEN 0 THEN CONCAT('$',nom,'(',id_action,')$') 
								                      ELSE CONCAT('$',nom,'(',id_action,'\\\\',id_src,'\\\\',id_action_src,')$') END)
					                     END)) as cache_type_externe1,
					                     IF(TYPE = 'EXTERNE',CONCAT('$',nom,'(',id_src,'\\\\',id_action_src,')$'),CONCAT('$',nom,'$')) as cache_type_out,
								IF(TYPE = 'EXTERNE',CONCAT(nom,'(',id_src,'\\\\',id_action_src,')'),'') as type_out
				FROM  `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."` 
				WHERE id_fiche = ".$this->c_id_temp."
				AND ((`used` = 1 AND  `TYPE` =  'EXTERNE') OR (`TYPE` <> 'EXTERNE'))
				ORDER BY LENGTH(`NOM`) DESC , NOM ASC";
		$resultat_replace_var = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
		
		$this->commentaire_varinext($resultat_commentaire_externe);
		$this->commentaire_varin_local($resultat_commentaire_varinlocal);
		$this->commentaire_varin($resultat_commentaire_varin);
		
		// Pour chaques étape on genere son contenu html		
		for($j = 0; $j < $this->c_nbr_etapes; $j = $j + 1) 
		{
			/**==================================================================
			 * Préparation du rendu visuel de l'étape
			 ====================================================================*/
			if($this->type == __FICHE_VISU__ && isset($this->c_Obj_etapes[$j]->html_temp))
			{
				$this->c_Obj_etapes[$j]->html_temp = $this->generer_cartouche_lien($this->c_Obj_etapes[$j]->html_temp,$this->c_Obj_etapes[$j]->numero,$resultat_replace_var);
			}
			else
			{
				$this->c_Obj_etapes[$j]->html_temp = $this->generer_cartouche_lien($this->c_Obj_etapes[$j]->contenu,$this->c_Obj_etapes[$j]->numero,$resultat_replace_var);
			}
			$this->c_Obj_etapes[$j]->html_temp = $this->replace_champs_speciaux($this->c_Obj_etapes[$j]->html_temp,$j + 1);
			$this->c_Obj_etapes[$j]->html_temp = $this->replace_champs_calculs($this->c_Obj_etapes[$j]->html_temp,$j + 1);
			
			/*===================================================================*/	
			
			/**==================================================================
			 * LIBELLE VARINEXT
			 ====================================================================*/
			$this->c_Obj_etapes[$j]->html_temp = str_replace($this->c_commentaire_varinext_replace,$this->c_commentaire_varinext_message,$this->c_Obj_etapes[$j]->html_temp);
			$this->c_Obj_etapes[$j]->html_temp = str_replace($this->c_commentaire_varinlocal_replace,$this->c_commentaire_varinlocal_message,$this->c_Obj_etapes[$j]->html_temp);
			$this->c_Obj_etapes[$j]->html_temp = str_replace($this->c_commentaire_varin_replace,$this->c_commentaire_varin_message,$this->c_Obj_etapes[$j]->html_temp);
			/*===================================================================*/	
			
			/**==================================================================
			 * GESTION ICONE DES TAGS
			 ====================================================================*/
			if($this->c_Obj_etapes[$j]->tab_tag > 0)
			{	// Icone avec tag
				$this->c_Obj_etapes[$j]->logo_tag = 'tag';
				// Fenêtre flotante 
				$this->c_Obj_etapes[$j]->tag_hover = $this->generer_commentaire_tag($this->c_Obj_etapes[$j]->numero);
			}
			else
			{	// Icone sans tag
				$this->c_Obj_etapes[$j]->logo_tag = 'no_tag';
				$this->c_Obj_etapes[$j]->tag_hover = '';
			}
			/*===================================================================*/	

			
			/**==================================================================
			 * DEFINITION DE LA COULEUR DE FOND DE L'ETAPE
			 ====================================================================*/				
			//on verifie si l'étape est pair ou impair
			if($j&1)
			{
				$classe_a_utiliser = 'lp';			// impair
			}
			else
			{
				$classe_a_utiliser = 'limp';		// pair
			}
			/*===================================================================*/	

			/**==================================================================
			 * DEBUT DE LIGNE DE L'ETAPE
			 ====================================================================*/
			$html_etape  = '<tr id="'.($this->c_Obj_etapes[$j]->numero).'">';			
			/*===================================================================*/

			/**==================================================================
			 * 1ere COLONNE (Numéro d'étape)
			 ====================================================================*/																		
			$html_etape .= '<td VALIGN="top" class="'.$classe_a_utiliser.'numetape" width="3%"><table width="20px" class="id_step">';					
			/*===================================================================*/

			/**==================================================================
			 * AFFICHAGE BOUTON SAUT D'ETAPE
			 * Visible dans tous les cas de modification sauf si il n'y a qu'une seule étape
			 ====================================================================*/
			if($this->type == __FICHE_MODIF__ && $this->c_nbr_etapes != 1)	
			{	
				$html_etape .= '<tr><td class="center"><div class="saut_etape" '.$this->generer_texte_aide(96,240).' id="deplace_etape_num'.$this->c_Obj_etapes[$j]->numero.'" onclick="deplacer_etape('.$this->c_nbr_etapes.','.$this->c_Obj_etapes[$j]->numero.');"></div></td></tr>';
			}
			/*===================================================================*/

			/**==================================================================
			 * AFFICHAGE DU NUMERO DE L'ETAPE
			 * Toujours visible
			 ====================================================================*/
			$html_etape .= '<tr><td class="stepid">'.$this->c_Obj_etapes[$j]->numero.'</td></tr>';	
			/*===================================================================*/

			/**==================================================================
			 * AFFICHAGE BOUTON SUPPRIMER ETAPE
			 * Visible dans tous les cas de modification sauf si il n'y a qu'une seule étape
			 ====================================================================*/
			if($this->type == __FICHE_MODIF__ && $this->c_nbr_etapes != 1)	
			{	
				$html_etape .= '<tr><td><div class="delete" '.$this->generer_texte_aide(76,240).' id="del_step'.$this->c_Obj_etapes[$j]->numero.'" onclick="verif_del_step('.$this->c_Obj_etapes[$j]->numero.');"></div></td></tr>';
			}				
			/*===================================================================*/


			//==================================================================
			// Second column : Main tools bar
			//==================================================================
			$html_etape .= '</table></td><td VALIGN="top" id="outils_step'.$this->c_Obj_etapes[$j]->numero.'" class="'.$classe_a_utiliser.'" width="2%"><div id="div_outils_step'.$this->c_Obj_etapes[$j]->numero.'">';
			//==================================================================
			
			/**==================================================================
			 * AFFICHAGE BOUTON MONTER ETAPE
			 * Visible dans tous les cas de modification sauf si il n'y a qu'une seule étape 
			 * et si ce n'est pas la première étape
			 ====================================================================*/									
			if($this->type == __FICHE_MODIF__ && $this->c_nbr_etapes != 1 && $j != 0)
			{
				$html_etape .= '<div><div class="monter" '.$this->generer_texte_aide(74,240).' onclick="deplacer_etape('.$this->c_nbr_etapes.','.($j+1).','.($j).');"></div></div>';
			}
			/*===================================================================*/			

			/**==================================================================
			 * AFFICHAGE BOUTON EDITER ETAPE
			 * Visible dans tous les cas de modification
			 ====================================================================*/									
			if($this->type == __FICHE_MODIF__)
			{
				$html_etape .= '<div><div '.$this->generer_texte_aide(72,240).' class="editer" onclick="editer_etape('.$this->c_Obj_etapes[$j]->numero.');"></div></div>';
			}
			/*===================================================================*/		

			/**==================================================================
			 * AFFICHAGE BOUTON TAG
			 * Visible dans tous les cas 
			 ====================================================================*/									
			if(($this->type == __FICHE_MODIF__) || ($this->c_Obj_etapes[$j]->tab_tag > 0))
			{
				$html_etape .= '<div id="a_tag_etape-'.$this->c_Obj_etapes[$j]->numero.'"><div class="'.$this->c_Obj_etapes[$j]->logo_tag.'" '.$this->c_Obj_etapes[$j]->tag_hover.' '.$this->generer_texte_aide(61,240).' onclick="vimofy_tag_etape('.$this->c_Obj_etapes[$j]->numero.',true);"></div></div>';
			}
			else
			{
				if($this->c_Obj_etapes[$j]->tab_tag > 0)
				{
					$html_etape .= '<div id="a_tag_etape-'.$this->c_Obj_etapes[$j]->numero.'"><div class="'.$this->c_Obj_etapes[$j]->logo_tag.'" '.$this->c_Obj_etapes[$j]->tag_hover.' onclick="vimofy_tag_etape('.$this->c_Obj_etapes[$j]->numero.',true);"></div></div>';
				}
				else
				{
					$html_etape .= '<div id="a_tag_etape-'.$this->c_Obj_etapes[$j]->numero.'"><div class="'.$this->c_Obj_etapes[$j]->logo_tag.'" '.$this->c_Obj_etapes[$j]->tag_hover.'></div></div>';
				}
			}
			/*===================================================================*/	

			/**==================================================================
			 * AFFICHAGE BOUTON COPIER/IMPORTER ETAPE
			 * Visible dans tous les cas de modification
			 ====================================================================*/									
			if($this->type == __FICHE_MODIF__)
			{
				$html_etape .= '<div><div class="dupliquer" '.$this->generer_texte_aide(89,240).' onclick="copie_etape('.$this->c_Obj_etapes[$j]->numero.'); " onContextMenu="return ctrl_step_import('.$this->c_Obj_etapes[$j]->numero.');"></div></div>';
			}
			/*===================================================================*/					

			/**==================================================================
			 * AFFICHAGE BOUTON DESCENDRE ETAPE
			 * Visible dans tous les cas de modification sauf si il n'y a qu'une seule étape 
			 * et si ce n'est pas la dernière étape
			 ====================================================================*/									
			if($this->type == __FICHE_MODIF__ && $this->c_nbr_etapes != 1 && ($j != $this->c_nbr_etapes - 1))
			{
				$html_etape .= '<div><div class="descendre" '.$this->generer_texte_aide(75,240).' onclick="deplacer_etape('.$this->c_nbr_etapes.','.($j+1).','.($j+2).');"></div></div>';
			}
			/*===================================================================*/					

			/**==================================================================
			 * Fermeture de la 2eme colonne
			 ====================================================================*/				
			$html_etape .= '</div></td>';							
			/*===================================================================*/								

			/**==================================================================
			 * 3eme COLONNE (CORPS DE L'ETAPE)
			 ====================================================================*/																		
			$html_etape .= '<td class="'.$classe_a_utiliser.'"  width="95%"><div style="position:relative;" id="tdetape'.($this->c_Obj_etapes[$j]->numero).'">'.$this->c_Obj_etapes[$j]->html_temp.'</div></td></tr>';	
			/*===================================================================*/							


			/**==================================================================
			 * DEBUT DE LA LIGNE DE SEPARATION DES ETAPES
			 ====================================================================*/
			$html_etape .= '<tr class="sep_'.$classe_a_utiliser.'">';			
			/*===================================================================*/				

			/**==================================================================
			 * 1ere COLONNE (Numéro d'étape)
			 ====================================================================*/		
			if($this->type == __FICHE_MODIF__)
			{	
				$html_etape .= '<td id="ajouter'.($this->c_Obj_etapes[$j]->numero + 1).'"><table width="20px" class="id_step"><tr><td><a NAME="'.($this->c_Obj_etapes[$j]->numero + 1).'"></a><div '.$this->generer_texte_aide(71,240).' onclick="ajouter_etape('.($this->c_Obj_etapes[$j]->numero + 1).');" class="ajouter"></div></td></tr></table></td><td><div id="ajax_load_etape'.$this->c_Obj_etapes[$j]->numero.'"></div></td><td><div class="ajax_nbr_tentative" id="ajax_step_qtt_retrieve'.$this->c_Obj_etapes[$j]->numero.'"></div></td></tr>';
			}	
			else
			{
				$html_etape .= '<td>&nbsp;</td><td><div id="ajax_load_etape'.$this->c_Obj_etapes[$j]->numero.'"></div></td><td><div class="ajax_nbr_tentative" id="ajax_step_qtt_retrieve'.$this->c_Obj_etapes[$j]->numero.'"></div></td></tr>';
			}	
			/*===================================================================*/	

			$content_etape .= $html_etape;
		}
		
		/**==================================================================
		 * LIBERE LA MEMOIRE UTILISEE PAR LES RECORDSETS DE MySQL
		 ====================================================================*/	
		mysql_free_result($resultat_replace_var);
		mysql_free_result($resultat_commentaire_externe);
		/*===================================================================*/	

		return $content_etape;
	}
	
	/**
	 * Identifie les liens pointant sur des iObjets dans l'étape $id_etape
	 * 
	 * @param unknown_type $txt	Texte à analyser
	 * @param unknown_type $id_etape Identifiant de l'étape
	 * @param unknown_type $iobjet Type d'objet
	 */
	private function analyse_lien($txt,$id_etape,$iobjet)
	{
		$ref = $txt;	
		$max_version_url = 0;
		while ( strlen($ref) > 0 ) 
		{
			$ref = strstr($ref,'<a');
			$l1 = $ref;
			$l2 = strstr($l1,'</a');
			$l3 = strstr($l1,$iobjet);
			
			if(strlen($l2) < strlen($l3))
			{	
				$ref = strstr($ref,$iobjet);
				$parametres = strstr($ref,'?');	
	
				$l_index = strpos($parametres,'"');
				$parametres = substr($parametres,0,$l_index);	
		
				$id_iobjet = '';
				$version_iobjet_lue = '';
				
				if($parametres != $txt)
				{
					preg_match('#ID=([0-9]+)#',$parametres,$temp);
	
					if(isset($temp[1]))
					{
						$id_iobjet = $temp[1];
					}
					
					preg_match('#&amp;version=([0-9]+)#',$parametres,$temp);
				}
				
				if($id_iobjet != '')
				{
					if(isset($temp[1]))
					{
						// Version précisée
						$version_iobjet_lue = $temp[1];
						$version_iobjet_finale = $version_iobjet_lue;
						$max_version_url = 0;
						$type_objet = str_replace('.php','',$iobjet);
					}
					else
					{
						$max_version_url = 1;
						// Dans le cas où la version n'a pas été précisée
						
						/**==================================================================
						 *	CONDITION SUR LA DATE Différent suivant mode d'affichage
						 *	Affichage: Se réfère à la date de la sauvegarde de la fiche
						 *	Modification: Se réfère à la date courante
						 ====================================================================*/	
						if($this->type == __FICHE_MODIF__)
						{
							// Modification
							$cond_date = 'NOW()';
						}
						else
						{
							// Visualisation
							$cond_date = '"'.$this->c_date.'"';
						}
						/*===================================================================*/
						
						switch($iobjet)
						{
							case 'icode.php':
								if($this->c_statut < $_SESSION[$this->c_ssid]['configuration'][32])
								{
									$sql_version_iobjet_finale = 'SELECT Version as version 
																	FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' 
																	WHERE ID = '.$id_iobjet;
								}
								else
								{
									$sql_version_iobjet_finale = 'SELECT Version as version 
																	FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` 
																	WHERE ID = '.$id_iobjet.' 
																	AND last_update_date < '.$cond_date.'
																	ORDER BY Version DESC  
																	LIMIT 1';
								}	
								$type_objet = "icode";
								break;
							
							case 'ifiche.php':
								if($this->c_statut < $_SESSION[$this->c_ssid]['configuration'][32])
								{
									$sql_version_iobjet_finale = 'SELECT num_version as version 
																	FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].' 
																	WHERE id_fiche = '.$id_iobjet;
								}
								else
								{
								$sql_version_iobjet_finale = 'SELECT num_version as version 
																FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'  
																WHERE id_fiche = '.$id_iobjet.' 
																AND date < '.$cond_date.' 
																ORDER BY num_version DESC  
																LIMIT 1';
								}
								$type_objet = "ifiche";
								break;
								
							case 'idossier.php':	// A FINIR
								// $sql_version_iobjet_finale = '(SELECT num_version FROM trr_idossier_max WHERE id_fiche = '.$id_iobjet.')';
								$sql_version_iobjet_finale = 'SELECT 1 as version';
								$type_objet = "idossier";
								break;	
						}
						
						$resultat_max = $this->exec_sql($sql_version_iobjet_finale,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
						
						if(mysql_num_rows($resultat_max) > 0)
						{
							$version_iobjet_finale = mysql_result($resultat_max,0,'version');
						}
						else
						{
							// Pas de version, l'objet n'existe pas.
							$version_iobjet_finale = '';
						}
					}
					
					// On stocke le lien complet vers l'objet (partie href)
					$this->c_Obj_etapes[($id_etape - 1)]->lien_iobjet_etape[] = array(0 => $parametres,1 => $id_iobjet,2 => $version_iobjet_finale,3 => $version_iobjet_lue,4 => $iobjet,5 => $this->get_max_version_objet($type_objet,$id_iobjet));
					
					if($version_iobjet_finale == '')
					{
						// On sort de la boucle, l'objet n'existe pas.
						break;
					}		
				}
				
				// On décalle pour analyser le lien suivant
				$ref = substr($ref,3,strlen($ref)-3);	
	
				if($id_iobjet != '')
				{
					// *************************************************************************************
					// ****************** COPIE DES VARIABLES DE SORTIE DE L'OBJET APPELE ******************
					// *************************************************************************************
					
					switch($iobjet)
					{
						case 'ifiche.php':
							$sql_insert_source = 'REPLACE INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`(`TYPE`, `id_fiche`, `num_version`, `id_action`, `id_src`,`num_version_src`, `id_action_src`,`type_src`,`max_version_src`,`NOM`, `DESCRIPTION`, `DEFAUT`, `NEUTRE`, `RESULTAT`, `used`, `COMMENTAIRE`, `temp`)
				
												  SELECT "EXTERNE",
												  '.$this->c_id_temp.',
												  '.$this->c_version.',
												  '.$id_etape.',
												  '.$id_iobjet.',
												  '.$version_iobjet_finale.',
												  `id_action`,
												  "__IFICHE__",
												  '.$max_version_url.',
												  `NOM`,
												  `DESCRIPTION`,
												  `DEFAUT`, 
												  `NEUTRE`, 
												  `RESULTAT`,  
												  0, 
												  `COMMENTAIRE`, 
												  `temp` 
												  FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' as param1
												  WHERE param1.id_fiche = '.$id_iobjet.' 
												  AND param1.NOM NOT IN (SELECT NOM 
												  						 FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
												  						 WHERE id_fiche = '.$this->c_id_temp.' 
												  						 AND TYPE = "EXTERNE"
												  						 AND id_action_src = param1.id_action 
												  						 AND id_src = param1.id_fiche 
												  						 AND id_action = '.$id_etape.') 
												  AND param1.num_version = '.$version_iobjet_finale.' 
												  AND param1.TYPE = "OUT"
												  
												  UNION
												  
												  SELECT "EXTERNE",
												  '.$this->c_id_temp.',
												  '.$this->c_version.',
												  '.$id_etape.',
												  '.$id_iobjet.',
												  '.$version_iobjet_finale.',
												  `id_action`,
												  "__IFICHE__",
												  '.$max_version_url.',
												  `NOM`,
												  `DESCRIPTION`,
												  `DEFAUT`, 
												  `NEUTRE`, 
												  `RESULTAT`, 
												  0, 
												  `COMMENTAIRE`, 
												  `temp` 
												  FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' as param1
												  WHERE param1.id_fiche = '.$id_iobjet.' 
												  AND param1.NOM NOT IN (SELECT NOM 
												  						 FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
												  						 WHERE id_fiche = '.$this->c_id_temp.' 
												  						 AND TYPE = "EXTERNE"
												  						 AND id_action_src = param1.id_action
												  						 AND id_src = param1.id_fiche 
												  						 AND id_action = '.$id_etape.') 
												  AND param1.num_version = '.$version_iobjet_finale.' 
												  AND param1.TYPE = "EXTERNE"
												  AND param1.used = 1';	
							break;
						case 'icode.php':
							$sql_insert_source = 'REPLACE INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`(`IDP`,`TYPE`, `id_fiche`, `num_version`, `id_action`, `id_src`, `num_version_src`, `id_action_src`,`type_src`,`max_version_src`,`NOM`, `DESCRIPTION`, `DEFAUT`, `NEUTRE`, `RESULTAT`, `used`, `COMMENTAIRE`, `temp`)
				
												  SELECT `IDP`,"EXTERNE",
												  '.$this->c_id_temp.',
												  '.$this->c_version.',
												  '.$id_etape.',
												  '.$id_iobjet.',
												  '.$version_iobjet_finale.',
												  0, 
												  "__ICODE__",
												  '.$max_version_url.',
												  `NOM`,
												  `DESCRIPTION`,
												  `DEFAUT`, 
												  `NEUTRE`, 
												  `RESULTAT`,  
												  0, 
												  `COMMENTAIRE`, 
												  0 
												  FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' as param1
												  WHERE param1.id = '.$id_iobjet.'
												  AND param1.NOM NOT IN (SELECT param2.NOM 
												  						 FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' as param2
												  						 WHERE param2.id_fiche = '.$this->c_id_temp.' 
												  						 AND param2.TYPE = "EXTERNE"
												  						 AND param2.id_action = '.$id_etape.'
												  						 AND param2.id_src = param1.id)  
												  AND version = '.$version_iobjet_finale.' 
												  AND TYPE = "OUT"

												  UNION 

												 SELECT `IDP`,"EXTERNE",
												  '.$this->c_id_temp.',
												  '.$this->c_version.',
												  '.$id_etape.',
												  '.$id_iobjet.',
												  '.$version_iobjet_finale.',
												  0,
												  "__ICODE__",
												  '.$max_version_url.',
												  `NOM`,
												  `DESCRIPTION`,
												  `DEFAUT`, 
												  `NEUTRE`, 
												  `RESULTAT`, 
												  0, 
												  `COMMENTAIRE`, 
												  0 
												  FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' as param1
												  WHERE param1.id = '.$id_iobjet.'
												  AND param1.NOM NOT IN (SELECT param2.NOM 
												  						 FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' as param2
												  						 WHERE param2.id_fiche = '.$this->c_id_temp.' 
												  						 AND param2.TYPE = "EXTERNE"
												  						 AND param2.id_action = '.$id_etape.' 
												  						 AND param2.id_src = param1.id)  
												  AND version = '.$version_iobjet_finale.' 
												  AND TYPE = "EXTERNE"';
							break;
						case 'idossier.php':	// A FINIR
							$sql_insert_source = 'SELECT 1';
							break;	
					}				
	
					if($this->type == __FICHE_MODIF__)
					{
						$this->exec_sql($sql_insert_source,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					}
					//*************************************************************************************
				}
			}
			else
			{
				// On décalle pour analyser le lien suivant
				$ref = substr($ref,3,strlen($ref)-3);	
			}
		}
	}
	
	private function replace_champs_calculs($txt,$num)
	{
		// Check if an iKCalc exist into the step
		if(strstr($txt,'<span class="ikcalc">'))
		{
			$err_reporting = error_reporting();
			// Get the ik_calc on the step
			$ik_calc = $this->get_ikcalc($txt);

			// Browse all ikcalc of the step
			foreach($ik_calc as $key => $value) 
			{
				// Clear the Math span class
				$ik_calc_result[$key] = str_replace('<span class="ikcalc">','',$value);
				//$ik_calc_result[$key] = str_replace('(','',$ik_calc_result[$key]);
				//$ik_calc_result[$key] = str_replace(')','',$ik_calc_result[$key]);
				$ik_calc_result[$key] = substr($ik_calc_result[$key],0,-7);

                preg_match_all('`<span onmouseover=\"ikdoc.*?\">(.*?)</span>`i',$ik_calc_result[$key],$out);

				foreach($out[0] as $key_out => &$value_out)
				{
                    $ik_calc_result[$key] = str_replace($value_out,$out[1][$key_out],$ik_calc_result[$key]);
				}
			}

			foreach($ik_calc_result as $key => $value)
			{
				/**==================================================================
				 * Manage error
				 ====================================================================*/				
				$this->c_ikcalc_error = false;
				set_error_handler(array($this, 'handleError_ikcalc'));
				/*===================================================================*/
				// Execute the calcul
				error_reporting(0);					// Set the error reporting to no error
				$ikcal_result = eval('/*error_reporting(0);*/return '.str_replace('&nbsp;',' ',$value).';');
                //error_log('xxxx'.$value);
				error_reporting($err_reporting); 	// Set the error reporting to default value

				if(!$this->c_ikcalc_error && $ikcal_result != false)
				{
                    // Math formula is ok
					$txt = str_replace($ik_calc[$key],'<span class="ikcalc" '.$this->generer_texte_aide(442,0).' onclick="document.getElementById(\'ik_calc_math_'.$num.'_'.$key.'\').style.display=\'\';this.style.display=\'none\';" id="ik_calc_result_'.$num.'_'.$key.'">'.$ikcal_result.'</span>'.'<span '.$this->generer_texte_aide(443,0).' onclick="document.getElementById(\'ik_calc_result_'.$num.'_'.$key.'\').style.display=\'\';this.style.display=\'none\';" style="display:none;" id="ik_calc_math_'.$num.'_'.$key.'">'.$ik_calc[$key].'</span>',$txt);
				}
				else
				{
					// Error in formula
					$replace= str_replace('<span class="ikcalc">','<span class="ikcalc ikcalc_err" id="ik_calc_'.$num.'_'.$key.'" '.$this->generer_texte_aide(441,0).'>',$ik_calc[$key]);
					$txt = str_replace($ik_calc[$key],$replace,$txt);
				}
			}
			
			return $txt;
		}
		else
		{
			return $txt;
		}
	}
	
	
	private	function get_ikcalc($string)
	{
		$end = 0;
		$string_span = $string;
		$a_ik_calc = Array();
		while(strstr(substr($string_span,$end),'<span class="ikcalc">') != false)
		{
			$string_span = strstr(substr($string_span,$end),'<span class="ikcalc">');
			$end = $this->get_complete_span($string_span)+1*(strlen('</span>'));
			$ikcalc =  substr($string_span,0,$end);
			$a_ik_calc[] = $ikcalc;
		}
		
		return $a_ik_calc;
	}
	
	private function get_complete_span($string,$index_span=0)
	{
		$length = 0;
		$string = substr($string,1);

		// Get the position of the next span end </span>
		$pos_next_span_end = strpos($string,'</span>');
	
		// Get the position of the next start <span (if exist)
		$pos_next_span_start = strpos($string,'<span ');
		
		// Control if the next open span is before the next close span
		if($pos_next_span_start < $pos_next_span_end && $pos_next_span_start != false)
		{
			// The next open span is before the next close span (ex : <span>..<span></span> - </span>)
			$index_span++;
			$next_str = substr($string,$pos_next_span_start);
			$length = $this->get_complete_span($next_str,$index_span)+$pos_next_span_start+1;
		}
		else
		{
			// No next open span or the next open span is after the next close span (ex : <span>..</span> <span> - </span>)
			if($index_span > 0)
			{
				$index_span--;
				$next_str = substr($string,$pos_next_span_end);
				$length = $this->get_complete_span($next_str,$index_span)+$pos_next_span_end+1;
			}
			else
			{
				$length = $pos_next_span_end+1;
			}
		}
		
		return $length;
	}	
	
	private function handleError_ikcalc($errno, $errstr,$error_file,$error_line)
	{
		$this->c_ikcalc_error = true;
	}
	
	/**
	 * Remplace les champs spéciaux de $txt par leurs valeurs
	 * 
	 * @param unknown_type $txt Texte à modifier
	 * @param unknown_type $num Numéro de l'étape
	 */
	private function replace_champs_speciaux($txt,$num)
	{
		// Liste des champs spéciaux:
		$champs[] = '__TOTAL_ETAPE__';
		$champs[] = '__NUM_ETAPE__';
		$champs[] = '__TOTAL_VARIN__';
		$champs[] = '__TOTAL_VAROUT__';
		$champs[] = '__VERSION_FICHE__';
		$champs[] = '__ID_FICHE__';
		$champs[] = '__TOTAL_TAG__';
		
		$replace[] = $this->c_nbr_etapes;
		$replace[] = $num;
		$replace[] = $this->c_tab_champ_speciaux['__TOTAL_VARIN__'];
		$replace[] = $this->c_tab_champ_speciaux['__TOTAL_VAROUT__'];
		$replace[] = $this->c_version;
		$replace[] = $this->c_id;
		$replace[] = $this->c_tab_champ_speciaux['__TOTAL_TAG__'];
		
		$txt = str_replace($champs,$replace,$txt);

		// Gestion de la date
		
		// Dans le contenu des étapes
		preg_match_all('`__DATE\(([^_]+)\)__`',$txt,$out);
		
		foreach($out[1] as $key => $value)
		{
			$out[1][$key] = '<span class="BBField">'.date($value).'</span>';
		}

		$txt = str_replace($out[0],$out[1],$txt);

		// Dans les URLs
		preg_match_all('`__DATE%28([a-zA-Z0-9/\\\\:-_*=%]+)%29__`',$txt,$out);
		
		foreach($out[0] as $key_expreg => $value_expreg)
		{
			$out[1][$key_expreg] = date(urldecode($out[1][$key_expreg]));
		}	
		
		$txt = str_replace($out[0],$out[1],$txt);

		return $txt;
	}
	
	
	/**
	 * Retourne le nombre de variable de type $type. si $etape n'est pas précisé, le retourne pour toute la fiche
	 * 
	 * @param $type Type de variable (IN,OUT,EXTERNE)
	 * @param $etape facultatif: Identifiant de l'étape où compter les variables
	 */
	private function get_total_var($type,$etape = '')
	{
		if(is_numeric($etape))
		{
			$etape = 'AND type IN("OUT","EXTERNE") AND id_action = '.$etape;	
		}
		else
		{
			$etape = 'AND type IN('.$type.')';
		}
		$sql = 'SELECT COUNT(1) as total
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				'.$etape;
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		return mysql_result($resultat,0,'total');
	}
	
	
	/**
	 * Retourne le nombre de tag. Si $etape n'est pas précisé, le retourne pour toute la fiche
	 * 
	 * @param $etape facultatif: Identifiant de l'étape où compter les tags
	 */
	private function get_total_tag($etape = '')
	{
		if(is_numeric($etape))
		{
			$etape = 'AND Etape = '.$etape;	
		}
		else
		{
			$etape = '';
		}
		$sql = 'SELECT COUNT(1) as total
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE ID = '.$this->c_id_temp.' 
				AND objet = "ifiche" 
				'.$etape;
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return mysql_result($resultat,0,'total');
	}
	
	
	/**
	 * Identifie les liens pointant sur des mots de passe dans l'étape $id_etape
	 * 
	 * @param text $txt
	 * @param decimal $id_etape
	 */
	private function analyse_password($txt,$id_etape)
	{
		$iobjet = 'password.php';
		
		$ref = $txt;	
		while (strlen($ref) > 0 ) 
		{
			$ref = strstr($ref,'"'.$iobjet);
				
			$parametres = strstr($ref,'?');	

			$l_index = strpos($parametres,'"');
			$parametres = substr($parametres,0,$l_index);	
	
			$id_iobjet = '';
	
			if($parametres != $txt)
			{
				
				preg_match('#ID=([0-9]+)#',$parametres,$temp);

				if(isset($temp[1]))
				{
					$id_iobjet = $temp[1];
					
				}
				
				preg_match('#&amp;version=([0-9]+)#',$parametres,$temp);
				
			}
			if($id_iobjet != '')
			{				
				// On stocke le lien complet vers l'objet (partie href)
				/* 	0 => lien trigger du cartouche
					1 => Id de l'objet appelé
					2 => version exacte de l'objet appelé
					3 => valorié si la version est précisée dans le lien d'appel
					4 => Type d'objet(ifiche.php/icode.php/idossier.php)	
				*/
				$this->c_Obj_etapes[($id_etape - 1)]->lien_iobjet_etape[] = array(0 => $parametres,1 => $id_iobjet,2 => 0,3 => 0,4 => $iobjet);
				

				$var_password = array();
				$var_password[0]['nom'] = 'ikspace_'.$id_iobjet.'_';
				$var_password[0]['description'] = $_SESSION[$this->c_ssid]['message'][225];
				
				$var_password[1]['nom'] = 'ikuser_'.$id_iobjet.'_';
				$var_password[1]['description'] = $_SESSION[$this->c_ssid]['message'][226];
				
				$var_password[2]['nom'] = 'ikpassword_'.$id_iobjet.'_';
				$var_password[2]['description'] = $_SESSION[$this->c_ssid]['message'][227];
				
				
				foreach ($var_password as $value)
				{
					$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
							 WHERE id_fiche = '.$this->c_id_temp.' 
							 AND TYPE = "OUT" 
			  				 AND id_action = '.$id_etape.' 
			  				 AND num_version_src = 0 
			  				 AND id_src = 0 
			  				 AND nom = "'.$value['nom'].'" 
			  				 AND id_action_src = '.$id_iobjet;
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
					if($this->existe_id_objet($id_iobjet,'password'))
					{
						// Création des variables de sortie
						$sql_insert_source = 'INSERT INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`(`TYPE`, `id_fiche`, `id_action_src`,`num_version`, `id_action`, `NOM`, `DESCRIPTION`, `used`)
											  SELECT 
												  "OUT",
												  '.$this->c_id_temp.',
												  '.$id_iobjet.',
												  '.$this->c_version.',
												  '.$id_etape.',
												  "'.$value['nom'].'",
												  "'.$value['description'].'",
												  0';
						
						$this->exec_sql($sql_insert_source,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					}
				}
			}
			// On décalle pour analyser le lien suivant
			$ref = substr($ref,3,strlen($ref)-3);	
		}
	}
	
	
	/**
	 * Recherche les blocs définis par l'utilisateur et ajoute une entête
	 * 
	 * @param string $txt
	 */
	private function generer_entete_bloc($txt)
	{
		// XXX ON DEBRANCHE POUR LE MOMENT !!!
		return $txt;
		// !!!!!!!!!!!!
		
		// Recherche des blocs dans $txt
		$motif = '#<div id="appercu" class="(.*)">#';
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
	
	/**
	 * Génère le cartouche des liens pour l'étape $id_etape dont le texte est passé par $txt
	 * 
	 * @param $txt Texte de l'étape où placer les cartouches
	 * @param $id_etape Identifiant de l'étape où placer les cartouches
	 */
	private function generer_cartouche_lien($txt,$id_etape,&$resultat)
	{
		// On vérifie si l'étape comporte des liens vers des iobjets
		if(isset($this->c_Obj_etapes[($id_etape - 1)]->lien_iobjet_etape))
		{
			// Nombre de lien d'appel vers un iobjet dans une étape
			$nbr_cartouche_par_etape = 0;
			
			// Parcours des liens
			foreach($this->c_Obj_etapes[($id_etape - 1)]->lien_iobjet_etape as $value_lien_iobjet_etape)
			{
				// Instanciation de la classe cartouche
				if($value_lien_iobjet_etape[4] == 'password.php')
				{
					// Password, no version
					$value_lien_iobjet_etape[5] = 0;
				}
				$cartouche = new cartridge($value_lien_iobjet_etape[4],$value_lien_iobjet_etape[1],$value_lien_iobjet_etape[5],$value_lien_iobjet_etape[2],$value_lien_iobjet_etape[3],$value_lien_iobjet_etape[0],$this->c_ssid,$this->c_id_temp,$id_etape,$nbr_cartouche_par_etape,$resultat,$this->c_requete_vimofy_cartouche[$id_etape - 1][$nbr_cartouche_par_etape],$this->type,$this->link,$this->link_password,$this->c_ik_valmod,$this->c_statut);

				// Integration du cartouche
				$txt = $cartouche->integrer_cartouche($txt);
				$nbr_cartouche_par_etape = $nbr_cartouche_par_etape + 1; 
			}
		}

		return $txt;
	}
	

	/**
	 * Génération commentaire lors du passage sur les tags d'une étape
	 * 
	 * @param decimal $etape
	 * @param unknown_type $avec_attributs
	 * @return unknown_type
	 */
	public function generer_commentaire_tag($etape,$avec_attributs = true)
	{
		/**==================================================================
		 * Récupération des tags en base de donnée pour l'étape $etape
		 ====================================================================*/	
		$sql = 'SELECT Tag,Groupe 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE ID = '.$this->c_id_temp.' 
				AND temp = 0 
				AND Etape = '.$etape." 
				LIMIT ".$_SESSION[$this->c_ssid]['configuration'][29];

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$contenu = '<table class=\\\'tableau_tag\\\'><tr><th class=\\\'tableau_tag\\\'>Tag</th><th class=\\\'tableau_tag\\\'>Groupe</th></tr>';
		
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
		{
			$contenu .= '<tr><td class=\\\'tableau_tag\\\' WIDTH=\\\'300px\\\'>'.$row['Tag'].'</td><td class=\\\'tableau_tag\\\' WIDTH=\\\'300px\\\'>'.$row['Groupe'].'</td></tr>';
		}	
		/*===================================================================*/	
		
		$contenu .= '</table>';

		if($avec_attributs == true)
		{
			return 'onMouseover="ddrivetip(\''.$contenu.'\',\'\',\'600\');" onMouseout="hideddrivetip();"';	
		}
		else
		{
			return 'ddrivetip(\''.$contenu.'\',\'\',\'600\')';	
		}
		
	}
	
	
	
	/**
	 * Compte le nombre d'étapes sur une fiche en base de données.
	 */
	function compter_etapes() 
	{
		$sql = 'SELECT COUNT(1) as nbretape
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name'].' 
				WHERE id_fiche = '.$this->c_id.' 
				AND num_version = '.$this->c_version;
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		return mysql_result($resultat,0,'nbretape');
	}
	

	private function get_url_etape($id_etape)
	{
		// Récupération des liens vers des iObjets
		//preg_match_all($this->c_motif_expr_reg['lien_objet'],$this->c_Obj_etapes[$id_etape - 1]->contenu,$out);
		
		preg_match_all('#<a.+href="((ifiche|icode|idossier).php([^"]+))"#',$this->c_Obj_etapes[$id_etape - 1]->contenu,$out);
		
		
		$i = 0;
		foreach ($out[1] as $value)
		{
			// Découpage de l'URL
			$objet = explode('?',$value);
			
			// Récupération de l'objet
			$tableau_liens[$i]['123objet456'] = $objet[0];		//ifiche.php ou icode.php
			
			// Découpage de l'URL
			$param_url = explode('&',$objet[1]);
			
			foreach($param_url as $value_param_url)
			{
				$valeurs = explode('=',$value_param_url);
				if(isset($valeurs[0]) && isset($valeurs[1]))
				{
					$valeurs[0] = str_replace('amp;','',$valeurs[0]);
					$tableau_liens[$i][$valeurs[0]] = $valeurs[1];
				}
			}
			$i = $i + 1;
		}

		if($i > 0)
		{
			return $tableau_liens;
		}
		else
		{
			return false;
		}
	}
	

	
	/**
	 * Methode qui permet de supprimer une étape.
	 * 
	 * @access public
	 * @param decimal $p_id_etape indique le numero d'étape a supprimer
	 */
	public function del_step($p_id_etape) 
	{
		
		/**==================================================================
		 * RECUPERATION DU CONTENU DE L'ETAPE POUR LE LOG
		 ====================================================================*/			
		$contenu_etape = $this->c_Obj_etapes[$p_id_etape - 1]->contenu;
		/*===================================================================*/
		
		$this->maj_ajoute_supprime_etape($p_id_etape,false);
	
		/**==================================================================
		 * On supprime les variables de sortie de l'étape
		 ====================================================================*/	
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_action = '.$p_id_etape.' 
				AND id_fiche = '.$this->c_id_temp.' 
				AND id_fiche >= 99999';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
		
		/**==================================================================
		 * On supprime les tags de l'étape
		 ====================================================================*/	
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE Etape = '.$p_id_etape.' 
				AND ID = '.$this->c_id_temp.' 
				AND ID >= 99999';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/

		// Redéfinition du nombre d'étape
		$this->c_nbr_etapes--;
		
		/**==================================================================
		 * Suppression de l'étape
		 ====================================================================*/	
		$tab1 = array_slice ($this->c_Obj_etapes, 0, $p_id_etape-1);  	//on coupe le tableau de l'indice 0 à la position $position.
		$tab2 = array_slice ($this->c_Obj_etapes,($p_id_etape)); 		//on coupe le tableau de $position à la fin.
		$this->c_Obj_etapes = array_merge ($tab1, $tab2); 				//Fusionne les deux tableaux
		/*===================================================================*/
		
		$j = 0;
		
		/**==================================================================
		 * Réafection des numéros d'étape
		 ====================================================================*/	
		foreach ($this->c_Obj_etapes as $array_obj) 
		{
			
			if ($this->c_Obj_etapes[$j]->numero > $p_id_etape) 
			{
				
				$this->c_Obj_etapes[$j]->numero = $this->c_Obj_etapes[$j]->numero - 1;
				
				// Déplace les numéros d'etape des variables de chaques étapes.
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						SET id_action = '.$this->c_Obj_etapes[$j]->numero.' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND id_action = '.($this->c_Obj_etapes[$j]->numero + 1);
				
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				// Déplace les numéros d'etape des tags de chaques étapes.
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = '.$this->c_Obj_etapes[$j]->numero.'
						WHERE ID = '.$this->c_id_temp.' 
						AND Etape = '.($this->c_Obj_etapes[$j]->numero + 1);
				
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
			}
			
			$j = $j +1;
			
		}
		/*===================================================================*/
		
		// Log action
		$this->insert_log('supprimer',$p_id_etape,$p_id_etape,$contenu_etape);
		
		// Regeneration des étapes
		return $this->display_step(true);
		
	}
	
	/**
	 * Methode qui permet de deplacer une étape vers un id de destination.
	 * @access public
	 * 
	 * @param decimal $id_etape_src numero de l'étape source.
	 * @param decimal $id_etape_dest numero de l'étape de destination.
	 */
	public function deplacer_etape($p_id_src,$p_id_dst) 
	{
		// On décale les id_src et id_dst de -1 car les id des etapes commencent réellement à 0
		$id_src = $p_id_src - 1;
		$id_dst = $p_id_dst - 1;
		
		// Copie des contenus des étapes src et dst
		$temp_src = $this->c_Obj_etapes[$id_src]->contenu;
		$temp_dst = $this->c_Obj_etapes[$id_dst]->contenu;
		
		
		// Pour toutes les étapes comprisent entre $id_src et $id_dst on les deplacent
		
		if($p_id_src > $p_id_dst)
		{
			$i = $id_src; 
			while($i  > $id_dst){
				$this->c_Obj_etapes[$i]->contenu = $this->c_Obj_etapes[$i - 1]->contenu;
				$i--;
			}
		}
		else
		{
			$i = $id_src; 
			while($i  < $id_dst){
				$this->c_Obj_etapes[$i]->contenu = $this->c_Obj_etapes[$i + 1]->contenu;
				$i = $i + 1;
			}	
		}
		
		// Copie de l'étape source dans l'étape de destination
		$this->c_Obj_etapes[$id_dst]->contenu = $temp_src;
		
		$this->maj_deplacer_etape($p_id_src,$p_id_dst);
		
		// Log action
		$this->insert_log('deplacer',$p_id_src,$p_id_dst,$this->c_Obj_etapes[$p_id_dst - 1]->contenu);
		
		// on réaffiche maintenant les étapes.
		echo $this->display_step(true);
	}	
	
	/**
	 * Cette methode permet de mettre à jour les liens, les variables et les tags d'une étape suite à un déplacement 
	 */
	private function maj_deplacer_etape($p_id_src,$p_id_dst)
	{
		// On decale les id_src et id_dst de -1 car les id des etapes commencent techniquement à 0
		$id_src = $p_id_src - 1;
		$id_dst = $p_id_dst - 1;

		// ------------------------ Remplacement des LIENS qui pointent sur des étapes ------------------------

		$tab_origine = array();
		$tab_replace = array();
		
		$i = $this->c_nbr_etapes;
		
		
		// Parcours des étapes (à l'envers)
		while($i > 0)
		{
			//$tab_origine[$i] = '<a href="#'.$i.'">Etape '.$i.'</a>';
			$tab_origine_motif[$i] = '<\<a href="\#('.$i.')"\>(.*) '.$i.'\</a\>>i';
			$tab_replace_temporaire[$i] = '==--=='.$i.'==-$2-==';
			$tab_replace_temporaire_motif[$i] = '<\==--=='.$i.'==-(.*)-==>';
			
			// Pour les liens compris entre l'id source et l'id destination
			if(($i > $p_id_src && $i < $p_id_dst) || ($i < $p_id_src && $i > $p_id_dst))
			{
				// On fait le remplacement du lien et du libellé
				if($p_id_src > $p_id_dst)
				{
					// MONTER
					$tab_replace[$i] = '<a href="#'.($i + 1).'">$1 '.($i + 1).'</a>';
				}
				else
				{
					// DESCENDRE
					$tab_replace[$i] = '<a href="#'.($i - 1).'">$1 '.($i - 1).'</a>';
				}
			}
			else
			{
				if($i == $p_id_src)
				{
					// c'est la source alors on remplace par la destination
					
					// On fait le remplacement du lien et du libellé
					$tab_replace[$i] = '<a href="#'.($p_id_dst).'">$1 '.($p_id_dst).'</a>';
				}
				else
				{
					if($i == $p_id_dst)
					{
						if($p_id_src > $p_id_dst)
						{
							// MONTER
							// C'est la destination alors on remplace par la source
							// On fait le remplacement du lien et du libellé
							$tab_replace[$i] = '<a href="#'.($p_id_dst + 1).'">$1 '.($p_id_dst + 1).'</a>';
						}
						else
						{
							// DESCENDRE
							// C'est la destination alors on remplace par la source
							// On fait le remplacement du lien et du libellé
							$tab_replace[$i] = '<a href="#'.($p_id_dst - 1).'">$1 '.($p_id_dst - 1).'</a>';
						}			
					}
					else
					{
						$tab_replace[$i] = '<a href="#'.($i).'">$1 '.($i).'</a>';
					}	
				}	
			}		
			
			$i--;
		}

		foreach($this->c_Obj_etapes as $value_etape)
		{	
			$value_etape->contenu = preg_replace($tab_origine_motif,$tab_replace_temporaire,$value_etape->contenu);
		}
		
		foreach($this->c_Obj_etapes as $value_etape)
		{	
			$value_etape->contenu = preg_replace($tab_replace_temporaire_motif,$tab_replace,$value_etape->contenu);
		}

	
		// -------- Mise à jour des varin locales provenant des varout des étapes qu'on a déplacé --------
		
		
		//On récupére les variables de l'étape que l'on parcourt
		if($p_id_src > $p_id_dst)
		{
			
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\')\') as variable,id_action,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "OUT" 
					AND id_action <= '.$p_id_src.' 
					AND id_action >= '.$p_id_dst;
		}
		else
		{
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\')\') as variable,id_action,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "OUT" 
					AND id_action >= '.$p_id_src.' 
					AND id_action <= '.$p_id_dst;	
		}
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$tab_var = array();
		$tab_var_url = array();
		$tab_replace = array();
		$tab_replace_insans_z = array();
		$tab_replace_url = array();
		$tab_replace_insans_z_url = array();
		$i = 0;
		while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{
			$tab_var[$i] = $row['variable'];
			$tab_var_url[$i] =rawurlencode($row['variable']);
			
			if(($row['id_action'] > $p_id_src && $row['id_action'] < $p_id_dst) || ($row['id_action'] < $p_id_src && $row['id_action'] > $p_id_dst))
			{	
				if($p_id_src > $p_id_dst)
				{
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] + 1).')';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] + 1).')');
					$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] + 1).'z)';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] + 1).'z)');
				}
				else
				{
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] - 1).')';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] - 1).')');
					$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] - 1).'z)';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] - 1).'z)');
				}
			}
			else
			{
				
				if($row['id_action'] == $p_id_src)
				{
					// c'est la source alors on remplace par la destination
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst).')';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst).')');
					$tab_replace[$i] =  $row['nom'].'('.($p_id_dst).'z)';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst).'z)');
					
				}
				else
				{
					if($row['id_action'] == $p_id_dst)
					{
						// c'est la destination alors on remplace par la source
						if($p_id_src > $p_id_dst)
						{
							$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst + 1).')';
							$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst + 1).')');
							$tab_replace[$i] =  $row['nom'].'('.($p_id_dst + 1).'z)';
							$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst + 1).'z)');
						}
						else
						{
							$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst - 1).')';
							$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst - 1).')');
							$tab_replace[$i] =  $row['nom'].'('.($p_id_dst - 1).'z)';
							$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst - 1).'z)');
						}
						
					}	
					else
					{
						$tab_replace[$i] = 'erreur';
						$tab_replace_url[$i] = 'erreur';
						$tab_replace_insans_z[$i] = 'erreur';
						$tab_replace_insans_z_url[$i] = 'erreur';
					}
				}	
			}					
			$i = $i + 1;
		}

		foreach ( $this->c_Obj_etapes as $value_etape )
		{
			$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
			$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
		}
		
		foreach ( $this->c_Obj_etapes as $value_etape ) 
		{
			$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
			$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);
		}
		
		// ----------------------------- Mise à jour des varin externes -----------------------------
		//On récupère les variables de l'étape que l'on parcourt
		if($p_id_src > $p_id_dst)
		{
			
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\'\\\\\',id_src,\'\\\\\',id_action_src,\')\') as variable,id_action,id_src,id_action_src,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "EXTERNE" 
					AND id_action <= '.$p_id_src.' 
					AND id_action >= '.$p_id_dst.'
					ORDER BY id_action DESC';
		}
		else
		{
			
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\'\\\\\',id_src,\'\\\\\',id_action_src,\')\') as variable,id_action,id_src,id_action_src,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "EXTERNE" 
					AND id_action >= '.$p_id_src.' 
					AND id_action <= '.$p_id_dst.'
					ORDER BY id_action ASC';	
		}
		
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
		$tab_var = array();
		$tab_var_url = array();
		$tab_replace = array();
		$tab_replace_insans_z = array();
		$tab_replace_url = array();
		$tab_replace_insans_z_url = array();
		$i = 0;
		while($row = mysql_fetch_array($requete,MYSQL_ASSOC))
		{
			$tab_var[$i] = $row['variable'].'</span>';
			$tab_var_url[$i] =rawurlencode($row['variable']);
			
			if(($row['id_action'] > $p_id_src && $row['id_action'] < $p_id_dst) || ($row['id_action'] < $p_id_src && $row['id_action'] > $p_id_dst))
			{	
				// id_action est compris entre l'id source et l'id destination
				if($p_id_src > $p_id_dst)
				{
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
					$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z</span>';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z');
				}
				else
				{
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
					$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z</span>';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z');
				}
			}
			else
			{
				
				if($row['id_action'] == $p_id_src)
				{
					// c'est la source alors on remplace par la destination
					$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>';
					$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');	// Probleme???
					$tab_replace[$i] =  $row['nom'].'('.($p_id_dst).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z</span>';
					$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z');
				}
				else
				{
					if($row['id_action'] == $p_id_dst)
					{
						// c'est la destination alors on remplace par la source
						if($p_id_src > $p_id_dst)
						{
							$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>';
							$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
							$tab_replace[$i] =  $row['nom'].'('.($p_id_dst + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z</span>';
							$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z');

						}
						else
						{
							$tab_replace_insans_z[$i] =  $row['nom'].'('.($p_id_dst - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>';
							$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
							$tab_replace[$i] =  $row['nom'].'('.($p_id_dst - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z</span>';
							$tab_replace_url[$i] = rawurlencode($row['nom'].'('.($p_id_dst - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z');
						}
					}	
					else
					{
						$tab_replace[$i] =  'erreur';
						$tab_replace_url[$i] =  'erreur';
						$tab_replace_insans_z[$i] =  'erreur';
						$tab_replace_insans_z_url[$i] =  'erreur';
					}
				}	
			}							
			$i = $i + 1;
		}
		
	
		foreach ($this->c_Obj_etapes as $value_etape) 
		{
			$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
			$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
		}
		
		foreach ( $this->c_Obj_etapes as $value_etape ) 
		{
			$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
			$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);
		}			
		
		// -------- Mise à jour des variables et des tags de la fiche --------
		
		if($p_id_src > $p_id_dst)
		{
			//MONTER
			
			//On met en ID temporaire l'ID source
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					SET id_action = 9999 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND id_action = '.$p_id_src.';';
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			//On met en ID temporaire l'ID source
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
					SET Etape = 9999 
					WHERE ID = '.$this->c_id_temp.' 
					AND Etape = '.$p_id_src;
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$i = $id_src; 
			while($i  > $id_dst)
			{
				
				if(($i > $p_id_src && $i <= $p_id_dst) || ($i < $p_id_src && $i >= $p_id_dst))
				{
					
					$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
							SET id_action = '.($i + 1).' 
							WHERE id_fiche = '.$this->c_id_temp.' 
							AND id_action = '.($i).';';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
					$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
							SET Etape = '.($i + 1).' 
							WHERE ID = '.$this->c_id_temp.' 
							AND Etape = '.$i;
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}				
				//On deplace les numero d'etape des variables de chaques étapes.
				$i--;
			}
			
			//On met l'ID temporaire en id de destination
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET id_action = '.$p_id_dst.' WHERE id_fiche = '.$this->c_id_temp.' AND id_action = 9999;';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = '.$p_id_dst.' WHERE ID = '.$this->c_id_temp.' AND Etape = 9999';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		else
		{
			//DESCENDRE
			
			//On met en ID temporaire l'ID source
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET id_action = 9999 WHERE id_fiche = '.$this->c_id_temp.' AND id_action = '.$p_id_src.';';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = 9999 WHERE ID = '.$this->c_id_temp.' AND Etape = '.$p_id_src;
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
							
			
			$i = $id_src; 
			
			while($i  <= $id_dst)
			{
				
				if(($i >= $p_id_src && $i <= $p_id_dst) || ($i <= $p_id_src && $i >= $p_id_dst))
				{
					
					$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET id_action = '.($i).' WHERE id_fiche = '.$this->c_id_temp.' AND id_action = '.($i +1).';';	
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
					$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = '.($i).' WHERE ID = '.$this->c_id_temp.' AND Etape = '.($i +1);	
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
				
				$i = $i + 1;
			}	
			
			//On met l'ID temporaire en id de destination
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET id_action = '.$p_id_dst.' WHERE id_fiche = '.$this->c_id_temp.' AND id_action = 9999;';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = '.$p_id_dst.' WHERE ID = '.$this->c_id_temp.' AND Etape = 9999';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);		 	
		}	
		
	}
	
	
	/**
	 * Cette methode permet de mettre à jour les liens, les variables et les tags d'une étape suite à un ajout ou une supression d'étape
	 * 
	 * @param decimal $p_id_etape id de l'étape à ajouter ou supprimer
	 * @param booleen $p_type 0=supprimer 1=ajouter
	 */
	private function maj_ajoute_supprime_etape($p_id_etape,$p_type)
	{
		if($p_type == false)
		{
			//SUPPRESSION D'UNE ETAPE	
			// -------- Mise à jour des varin locales provenant des varout des étapes qu'on a déplacé (ainsi que pour les liens)--------	
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\')\') as variable,id_action,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "OUT" 
					AND id_action >= '.$p_id_etape;	
			
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$tab_var = array();
			$tab_var_url = array();
			$tab_replace = array();
			$tab_replace_insans_z = array();
			$tab_replace_url = array();
			$tab_replace_insans_z_url = array();
			$i = 0;
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$tab_var[$i] = $row['variable'];
				$tab_var_url[$i] =rawurlencode($row['variable']);
				$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] - 1).')';
				$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] - 1).')');
			//	$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] - 1).'z)';
				$tab_replace[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37].'zzzz';
				$tab_replace_url[$i] =  $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37];							
				$i = $i + 1;
			}

			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
			}
			
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);
			}			
			
			// -------- Mise à jour des varin externes provenant des liens
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\'\\\\\',id_src,\'\\\\\',id_action_src,\')\') as variable,id_action,id_src,id_action_src,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "EXTERNE" 
					AND id_action >= '.$p_id_etape;	

			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$tab_var = array();
			$tab_var_url = array();
			$tab_replace = array();
			$tab_replace_insans_z = array();
			$tab_replace_url = array();
			$tab_replace_insans_z_url = array();
			$i = 0;
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$tab_var[$i] = $row['variable'];
				$tab_var_url[$i] =rawurlencode($row['variable']);
				$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')';
				$tab_replace_insans_z_url[$i] =rawurlencode($row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
				//$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] - 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z';
				$tab_replace[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37].'zzzz';
				$tab_replace_url[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37];						
				$i = $i + 1;
			}
			
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
			}
			
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);
			}				
			
			// ------------------------ Remplacement des LIENS des étapes ------------------------
			
			foreach($this->c_Obj_etapes as $value_etape)
			{
				//----------------------------------- Modification des liens ------------------------------------------------
			
				preg_match_all($this->c_motif_expr_reg['num_etape'],$value_etape->contenu,$out);
				
				// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
				$array_expr = array_values(array_unique($out[1]));
				
				sort($out[1]);
				$out[1] = array_reverse($out[1]);
				$contenu = '';
				foreach ( $out[1] as $value ) 
				{
					if($value >= $p_id_etape)
					{
						//On fait le remplacement du lien et du libellé
						$recherche = '<\<a href="\#('.$value.')"\>(.*) '.$value.'\</a\>>i';
						$replace = '<a href="#'.($value - 1).'">$2 '.($value - 1).'</a>';
						$value_etape->contenu = preg_replace($recherche,$replace,$value_etape->contenu);
					}
				}
			}	
		}
		else
		{
			//AJOUT D'UNE ETAPE		
			// -------- Mise à jour des varin locales provenant des varout des étapes qu'on a déplacé (ainsi que pour les liens) --------	
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\')\') as variable,id_action,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "OUT" 
					AND id_action >= '.$p_id_etape;	
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
	
			$tab_var = array();
			$tab_var_url = array();
			$tab_replace = array();
			$tab_replace_insans_z = array();
			$tab_replace_url = array();
			$tab_replace_insans_z_url = array();
			$i = 0;
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$tab_var[$i] = $row['variable'];
				$tab_var_url[$i] =rawurlencode($row['variable']);
				$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] + 1).')';
				$tab_replace_insans_z_url[$i] = rawurlencode($row['nom'].'('.($row['id_action'] + 1).')');
				//$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] + 1).'z)';		
				$tab_replace[$i] =  $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37].'zzzz';			
				$tab_replace_url[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37];			
				$i = $i + 1;
			}
			
			
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
			}
			
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);
			}			
			
			// -------- Mise à jour des varin externes provenant des liens
			$sql = 'SELECT CONCAT(nom,\'(\',id_action,\'\\\\\',id_src,\'\\\\\',id_action_src,\')\') as variable,id_action,id_src,id_action_src,nom 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND TYPE = "EXTERNE" 
					AND id_action >= '.$p_id_etape;	

			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$tab_var = array();
			$tab_var_url = array();
			$tab_replace = array();
			$tab_replace_insans_z = array();
			$tab_replace_url = array();
			$tab_replace_insans_z_url = array();
			$i = 0;
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$tab_var[$i] = $row['variable'];
				$tab_var_url[$i] =rawurlencode($row['variable']);
				$tab_replace_insans_z[$i] =  $row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')';
				$tab_replace_insans_z_url[$i] =rawurlencode($row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')');
				//$tab_replace[$i] =  $row['nom'].'('.($row['id_action'] + 1).'\\'.$row['id_src'].'\\'.$row['id_action_src'].')z';
				$tab_replace[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37].'zzzz';
				$tab_replace_url[$i] = $_SESSION[$this->c_ssid]['configuration'][37].$i.$_SESSION[$this->c_ssid]['configuration'][37];
				$i = $i + 1;
			}

			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_var,$tab_replace,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_var_url,$tab_replace_url,$value_etape->contenu);
			}

			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				$value_etape->contenu = str_replace($tab_replace,$tab_replace_insans_z,$value_etape->contenu);
				$value_etape->contenu = str_replace($tab_replace_url,$tab_replace_insans_z_url,$value_etape->contenu);     
			}				
			

			// --------------------------------------------------------------------------------------------------------------
			foreach ( $this->c_Obj_etapes as $value_etape ) 
			{
				//----------------------------------- Modification des liens ------------------------------------------------
			
				preg_match_all($this->c_motif_expr_reg['num_etape'],$value_etape->contenu,$out);
				
				
				// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
				$array_expr = array_values(array_unique($out[1]));
				
				sort($out[1]);
				$out[1] = array_reverse($out[1]);
				$contenu = '';
				foreach ( $out[1] as $value ) 
				{
					if($value >= $p_id_etape)
					{
						//On fait le remplacement du lien et du libellé
						//$recherche = '<\<a href="\#('.$value.')"\>Etape '.$value.'\</a\>>i';
						$recherche = '<\<a href="\#('.$value.')"\>(.*) '.$value.'\</a\>>i';
						//$replace = '<a href="#'.($value + 1).'">Etape '.($value + 1).'</a>';
						$replace = '<a href="#'.($value + 1).'">$2 '.($value + 1).'</a>';
						$value_etape->contenu = preg_replace($recherche,$replace,$value_etape->contenu);
					}
				}
			}
		}	
	}	
	
	
	
	/**
	 * 
	 * Methode qui permet d'ajouter une étape.
	 * @access public
	 * @param decimal $p_id_etape indique le numero de l'étape à ajouter.
	 * 
	 */
	public function ajouter_etape($p_id_etape)
	{
		$this->maj_ajoute_supprime_etape($p_id_etape,true);
		
		$tab1 = array_slice($this->c_Obj_etapes, 0, $p_id_etape - 1);	//on coupe le tableau de l'indice 0 à la position $position.
		$tab2 = array_slice($this->c_Obj_etapes, $p_id_etape -1); 		//on coupe le tableau de $position à la fin.
		
		$temp = new step_alone();
		$temp->numero = $p_id_etape;
		
		array_push($tab1, $temp); 										//on ajoute a la fin du premier tableau la variable $temp qui contient l'objet instancié
		
		foreach ( $tab2 as $value ) 
		{
			// on change les numeros des etapes suivant celle qui a été ajoutée.
			$value->numero = $value->numero + 1;
		}
		
		$tableau_inverse = array_reverse($tab2);
		
		
		//On deplace les numero d'etape des variables de chaques étapes.
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' SET id_action = id_action + 1 WHERE id_fiche = '.$this->c_id_temp.' AND id_action >= '.$p_id_etape.' ORDER BY id_action DESC';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		//Idem pour les tags.
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' SET Etape = Etape + 1 WHERE ID = '.$this->c_id_temp.' AND Etape >= '.$p_id_etape.' ORDER BY Etape DESC';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		
		$this->c_Obj_etapes = array_merge($tab1, $tab2); //Fusionne les deux tableaux	
		
		$this->c_nbr_etapes = $this->c_nbr_etapes + 1;
		
		// Log action
		$this->insert_log('ajouter',$p_id_etape,$p_id_etape,'');

		return $this->display_step(true);
		
	}
	
	
	
	
	/**
	 * Methode qui permet de recuperer le contenu de l'etape d'une fiche en base de données.
	 *	
	 * @return dataset retour d'un dataset
	 */
	private function get_contenu_etape() 
	{
		
		$sql = 'SELECT description 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name'].' 
				WHERE id_fiche = '.$this->c_id.'
				AND num_version = '.$this->c_version.' 
				ORDER BY id_etape ASC';
	
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$i = 0;
		
		while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{
			$dataset[$i] = $row;
			$i = $i+1;
		}
		
		mysql_free_result($requete);
		
		return $dataset;
		
	}
	
	
	/**
	 * Save the content of the step
	 * 
	 * @param $p_id idof the step to save
	 * @param $p_content Content of the step
	 */
	public function save_step($p_id,$p_content)
	{
		/**==================================================================
		 * Clear temporary variables
		 ====================================================================*/	
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_action = '.$p_id.' 
				AND id_fiche = '.$this->c_id_temp.' 
				AND temp = 1';
							
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/

		/**==================================================================
		 * Clear password variables
		 ====================================================================*/	
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
							 WHERE id_fiche = '.$this->c_id_temp.' 
							 AND TYPE = "OUT" 
			  				 AND id_action = '.$p_id.' 
			  				 AND num_version_src = 0 
			  				 AND id_src = 0 
			  				 AND (nom LIKE "ikpassword_%_" OR nom LIKE "ikspace_%_" OR nom LIKE "ikuser_%_")';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/

		/**==================================================================
		 * Clean title
		 ====================================================================*/		
		$title = strstr($p_content,chr(10),true);
		if($title == false)
		{
			$title = $p_content;
		}
		$new_title = str_replace('<p>','',$title);
		$new_title = str_replace('</p>','',$new_title);
		$new_title = str_replace('<br />','',$new_title);		// Firefox bug (1087): clear <br /> into title
		$p_content = str_replace($title,'<p><span class="BBTitre">'.$new_title.'</span></p>',$p_content);
		/*===================================================================*/

		/**==================================================================
		 * Clean empty span
		 ====================================================================*/	
		$p_content = $this->nettoyer_span_imbrique($p_content);
		$p_content = $this->nettoyer_span_vide($p_content);
		
		$p_content = preg_replace('`<span class="([^>]+)><br /></span>`', '', $p_content);
		$p_content = preg_replace('`<span class="([^>]+)>&nbsp;</span>`', '', $p_content);
		$p_content = preg_replace('`<span class="([^>]+)></span>`', '', $p_content);	
		$p_content = preg_replace('`<span class="([^>]+)>([^ <]+) </span>`', '<span class="$1>$2</span>', $p_content);	
		
		
		// Delete <br /> before a span
		$p_content = str_replace('<br /></span>','</span>',$p_content);
		
		// Replace var of type <span class="BBVarIn"><strong>mavar</strong></span> by <span class="BBVarIn">mavar</span>
		$p_content = preg_replace('`<span class="([^>]+)"><strong>([^>]+)</strong></span>`', '<span class="$1">$2</span>', $p_content);
		/*===================================================================*/

		/**==================================================================
		 * Clean empty link
		 ====================================================================*/			
		$p_content = preg_replace('`<a href="([^>]+)></a>`','',$p_content);
		/*===================================================================*/
		
		/**==================================================================
		 * Clean pasted link to iObject
		 ====================================================================*/			
		// $p_content = preg_replace('`<a href="([^>]+)></a>`','',$p_content);
		$patern = '#(href="|)(http://|)'.$_SERVER['SERVER_NAME'].'/((ifiche|icode|idossier).php([^" \n\r]*))#';

		preg_match_all($patern,$p_content,$out);
		foreach($out[0] as $key => $value) 
		{
			// Search ssid
			preg_match_all('#&(amp;|)ssid=[^&]*#',$out[3][$key],$out_ssid);
			// Search if a href is set
			if($out[1][$key] == '')
			{
				// No href set
				$p_content = str_replace($value,'<a href="'.str_replace($out_ssid[0],'',$out[3][$key]).'">'.$out[4][$key].'</a>',$p_content);
			}
			else
			{
				// href set
				$p_content = str_replace($value,str_replace($out_ssid[0],'',$out[3][$key]),$p_content);
			}
		}
		
		preg_match_all('#&(amp;|)ssid=[^&"]*#',$p_content,$out_ssid);
		foreach($out_ssid[0] as $value) 
		{
			$p_content = str_replace($value,'',$p_content);
		}
		
		
		/*===================================================================*/
		
		/**==================================================================
		 * Check link to other step and update the anchor value if it's not the same
		 ====================================================================*/	 
		preg_match_all($this->c_motif_expr_reg['Etape'],$p_content,$out);
		
		$array_expr_ancre = array_values(array_unique($out[1]));
		$array_expr_lien = array_values(array_unique($out[2]));
		
		$j = 0;
		foreach ($array_expr_ancre as $value)
		{
			$p_content = str_replace('<a href="#'.$value.'">Etape '.$array_expr_lien[$j].'</a>','<a href="#'.$array_expr_lien[$j].'">Etape '.$array_expr_lien[$j].'</a>',$p_content);
			$j = $j + 1;
		}
		/*===================================================================*/

		/**==================================================================
		 * Clean <a> tag 
		 ====================================================================*/	 		
		$p_content = $this->delete_attribut_balise($p_content,'a','onmouseout');
		$p_content = $this->delete_attribut_balise($p_content,'a','onmouseover');
		$p_content = $this->add_attribut_target($p_content);
		/*===================================================================*/
		
		/**==================================================================
		 * Backup the step into the object
		 ====================================================================*/	 
		$this->c_Obj_etapes[($p_id - 1)]->contenu = $p_content;
		/*===================================================================*/

		
		/**==================================================================
		 * Get external variables and tags
		 ====================================================================*/	 		
		$this->copy_var_ext_and_tag($p_id - 1);
		/*===================================================================*/
		
		/**==================================================================
		 * Backup into the log action
		 ====================================================================*/	 		
		$this->insert_log('modifier',$p_id,$p_id,$p_content);
		/*===================================================================*/

		/**==================================================================
		 * Update variables and tags of other step
		 ====================================================================*/	 	
		$this->copy_var_ext_and_tag();
		/*===================================================================*/
		
		/**==================================================================
		 * Generate all step
		 ====================================================================*/	 	
		echo $this->display_step(true);
		/*===================================================================*/	
	}
	
	private function nettoyer_span_imbrique($corps)
	{
		$tab_var_ok = $this->get_balise($corps,'span');
		
		$tab_var_masque = array();
		
		
		foreach($tab_var_ok as $key => $value)
		{
			$tab_var_masque[$key] = 'xyxy'.$key.'xyxy';
		}
		
		
		$corps = str_replace($tab_var_ok,$tab_var_masque,$corps);
		
		$ref = $corps;	
		$final = '';
		$pos = 0;
		while(strlen($ref) > 0 ) 
		{
			/* --------------------------*/
			/* BEGIN bloc début à purger */
			/* --------------------------*/
	
			// Recheche de la position du prochain span de type BB
			$pos_first = strpos($ref,'<span class="BB');
			
			if(!is_numeric($pos_first)) break; // Si il n'y a plus de span ouvrant de type BB -> Fin de la boucle
			
			// Récupération de ce qu'il y a avant le premier span ouvrant de type BB
			$final .= $this->strstrb($ref,'<span class="BB');
			
			// Décallage sur le prochain span ouvrant de type BB 
			$ref = strstr($ref,'<span class="BB');
			
			// Recherche de la fermeture du span ouvrant
			$span_ouvrant = strpos($ref,'>');
			
			// $ref se décale sur la fermeture du span ouvrant
			$ref = substr($ref,$span_ouvrant + 1);
			
			/* ------------------------*/
			/* END bloc debut à purger */
			/* ------------------------*/
			
			
			/* ------------------------*/
			/* BEGIN bloc fin à purger */
			/* ------------------------*/ 
	
			$flag_recherche = true;
			$pos_ouvrant = 0;
			$pos_fermant = 0;
			while($flag_recherche)
			{
				// Recherche de la position du prochain span ouvrant
				$pos_ouvrant = strpos($ref,'<span',$pos_ouvrant);
				// Recherche de la position du prochain span fermant
				$pos_fermant = strpos($ref,'</span>',$pos_fermant + 1);
				
				if($pos_fermant > $pos_ouvrant && is_numeric($pos_ouvrant))
				{
					// Il reste un span ouvrant entre
					$pos_ouvrant = $pos_ouvrant + 1;
				}
				else
				{
					// Le prochain span ouvrant est après le prochain span fermant
					$flag_recherche = false;
				}
			}
			

			if($pos_ouvrant > $pos_fermant)
			{
				$pos_fermant = strpos($ref,'</span>');
			}
			
			if($pos_fermant != false)
			{
				$ref = substr_replace($ref,'',$pos_fermant,7);
			}
			
			/* ----------------------*/
			/* END bloc fin à purger */
			/* ----------------------*/
			
		}
	
		$final .= $ref;
		
		$final = str_replace($tab_var_masque,$tab_var_ok,$final);
		
		return $final;
	}
	
	
	private function nettoyer_span_vide($corps)
	{
		$ref = $corps;	
		$final = '';
		$pos = 0;
		while(strlen($ref) > 0 ) 
		{
			/* --------------------------*/
			/* BEGIN bloc début à purger */
			/* --------------------------*/
	
			// Recheche de la position du prochain span de type BB
			$pos_first = strpos($ref,'<span>');
			
			if(!is_numeric($pos_first)) break; // Si il n'y a plus de span ouvrant de type BB -> Fin de la boucle
			
			// Récupération de ce qu'il y a avant le premier span ouvrant de type BB
			$final .= $this->strstrb($ref,'<span>');
			
			// Décallage sur le prochain span ouvrant de type BB 
			$ref = strstr($ref,'<span>');
			
			// Recherche de la fermeture du span ouvrant
			$span_ouvrant = strpos($ref,'>');
			
			// $ref se décale sur la fermeture du span ouvrant
			$ref = substr($ref,$span_ouvrant + 1);
			
			/* ------------------------*/
			/* END bloc debut à purger */
			/* ------------------------*/
			
			
			/* ------------------------*/
			/* BEGIN bloc fin à purger */
			/* ------------------------*/ 
	
			
			$flag_recherche = true;
			$pos_ouvrant = 0;
			$pos_fermant = 0;
			while($flag_recherche)
			{
				// Recherche de la position du prochain span ouvrant
				$pos_ouvrant = strpos($ref,'<span>',$pos_ouvrant);
				// Recherche de la position du prochain span fermant
				$pos_fermant = strpos($ref,'</span>',$pos_fermant + 1);
				
				if($pos_fermant > $pos_ouvrant && is_numeric($pos_ouvrant))
				{
					// Il reste un span ouvrant entre
					$pos_ouvrant = $pos_ouvrant + 1;
				}
				else
				{
					// Le prochain span ouvrant est après le prochain span fermant
					$flag_recherche = false;
				}
			}
			

			if($pos_ouvrant > $pos_fermant)
			{
				$pos_fermant = strpos($ref,'</span>');
			}
			
			if($pos_fermant != false)
			{
				$ref = substr_replace($ref,'',$pos_fermant,7);
			}
			
			/* ---------------------- */
			/* END bloc fin à purger  */
			/* ---------------------- */
			
		}
	
		$final .= $ref;
		
		return $final;
	}
	
	/**
	 * Methode qui ecrit toute les etapes dans la base de données.
	 */
	public function ecrire_etape_bdd() 
	{
		for ( $j = 0; $j < $this->c_nbr_etapes; $j = $j + 1) 
		{
			// Création de la requête
			$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name'].'(id_fiche,num_version,id_etape,description) 
					VALUES('.$this->c_id.','.$this->c_version.','.$this->c_Obj_etapes[$j]->numero.',"'.$this->protect_save($this->c_Obj_etapes[$j]->contenu).'");';				
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
	}
	
	
	/**
	 * Methode qui est executée lors de la duplication d'une fiche
	 * Elle met tout les id à new
	 * NR_IKNOW_5_
	 */
	public function dupliquer_etapes()
	{
		foreach ($this->c_Obj_etapes as $array_obj)
		{
			$array_obj->c_id = 'new';
			$array_obj->c_version = 0;
		}
	}
	
	
	/**
	 * NR_IKNOW_7_
	 */
	public function get_vimofy_tag_etape($etape)
	{
		$_SESSION[$this->c_ssid]['etape_active'] = $etape;
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		 
		if($this->type == __FICHE_VISU__)
		{
			// Visu
			require('../../includes/ifiche/vimofy/visu/init_liste_tags_local_step.php');
		}
		else
		{
			// Modif
			require('../../includes/ifiche/vimofy/edit/init_liste_tags_local_step.php');
			require('../../includes/ifiche/vimofy/edit/init_liste_tags_ext_step.php');
		}
		
		$style = $obj_vimofy_tags->vimofy_generate_header(true);
		$vim = '<div style="width:100%;height:270px;position:relative;">'.$obj_vimofy_tags->generate_vimofy().'</div>';
		$js = $obj_vimofy_tags->vimofy_generate_js_body(true);
		
		if($this->type == __FICHE_MODIF__)
		{
			$style .= $obj_vimofy_tags_ext->vimofy_generate_header(true);
			$vim .= '<div style="width:100%;height:270px;position:relative;margin-top:10px;">'.$obj_vimofy_tags_ext->generate_vimofy().'</div>';
			$js .= $obj_vimofy_tags_ext->vimofy_generate_js_body(true);
		}
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($vim)."</vimofy>";
		echo "<json>".rawurlencode($js)."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";
	}
	
	private function get_tag_etape_externe($etape)
	{
		
		$sql = "SELECT Tag,
				Groupe,IF(id_src IS NULL, 'Local', CONCAT(objet,' ',id_src,'/',version_src)) as source
				FROM ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."
				WHERE Version = ".$this->c_version." 
				AND temp = 0 
				AND ID = ".$this->c_id_temp." 
				AND Etape = ".$etape." 
				AND id_src IS NOT NULL";
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$html = '<div class="viewer_grey_titre">'.$_SESSION[$this->c_ssid]['message'][370].' ('.mysql_num_rows($requete).')</div><table class="tag_externe">';
		$html .= '<tr class="viewer_grey_header"><th class="tag_externe">Tag</th><th>Groupe</th><th>Source</th></tr>';
		$i = 1;
		while($row = mysql_fetch_array($requete,MYSQL_ASSOC))
		{
			/**==================================================================
			 * DEFINITION DE LA COULEUR DE FOND DE LA LIGNE
			 ====================================================================*/				
			if ($i&1) 
			{
				$classe_a_utiliser = 'style="background-color:#F3F3F3;"';		//impair
			}
			else
			{
				$classe_a_utiliser = 'style="background-color:#DEDEDE;"';		//pair
			}
			/*===================================================================*/	
			$html .= '<tr '.$classe_a_utiliser.'><td style="padding-left:18px;font-size: 8pt;">'.$row['Tag'].'</td>';
			$html .= '<td style="padding-left:18px;font-size: 8pt;">'.$row['Groupe'].'</td>';
			$html .= '<td style="padding-left:18px;font-size: 8pt;">'.$row['source'].'</td></tr>';
			$i = $i + 1;
		}
		
		$html .= '</table>';
		if(mysql_num_rows($requete) == 0)
		{
			return '';
		}
		else
		{
			return $html;
		}
		
	}
	
	
	public function vimofy_alias_step_id($etape)
	{
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		require('../../includes/ifiche/vimofy/edit/import_step_id_iFiche.php');
		$style = $obj_vimofy_imp_step_id->vimofy_generate_header(true);
	
		$html = '<div>
					<table>
						<tr>
							<td><div>'.($obj_vimofy_imp_step_id->generate_lmod_form()).'</div></td>
							<td><div id="tdetape'.$etape.'_alias_version"></div></td>
							<td><div id="tdetape'.$etape.'_alias_step"></div></td>
						</tr>
					</table>
				</div><div id="visu_alias"></div>';
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($html)."</vimofy>";
		echo "<header>".rawurlencode($obj_vimofy_imp_step_id->generate_lmod_header(false))."</header>";
		echo "<json>".rawurlencode($obj_vimofy_imp_step_id->vimofy_generate_js_body(true))."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";	
	}

	
	
	
	public function vimofy_alias_step_version($id,$etape)
	{
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		require('../../includes/ifiche/vimofy/edit/import_step_version_iFiche.php');
		$style = $obj_vimofy_imp_step_version->vimofy_generate_header(true);
	
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($obj_vimofy_imp_step_version->generate_lmod_form())."</vimofy>";
		echo "<header>".rawurlencode($obj_vimofy_imp_step_version->generate_lmod_header(false))."</header>";
		echo "<json>".rawurlencode($obj_vimofy_imp_step_version->vimofy_generate_js_body(true))."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";	
	}	
	

	public function vimofy_alias_step_id_step($id,$version,$etape)
	{
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		require('../../includes/ifiche/vimofy/edit/import_step_id_step_iFiche.php');
		$style = $obj_vimofy_imp_step_id_step->vimofy_generate_header(true);
	
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($obj_vimofy_imp_step_id_step->generate_lmod_form())."</vimofy>";
		echo "<header>".rawurlencode($obj_vimofy_imp_step_id_step->generate_lmod_header(false))."</header>";
		echo "<json>".rawurlencode($obj_vimofy_imp_step_id_step->vimofy_generate_js_body(true))."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";	
	}	
	
	
	
	
	
	/**
	 * Cette methode verifie si l'etape comporte un lien vers un iObjet
	 * Elle retourne le complement de requete SQL pour charger les variables de sortie de l'iObjet appelé
	 */
	public function variables_externe($contenu)
	{
		$this->c_requete_var_externe = $contenu;
	}
	
	
	public function copy_var_ext_and_tag($p_etape = 'all')
	{
		if(!is_numeric($p_etape))
		{
			$fin = $this->c_nbr_etapes;
		}
		else
		{
			$etape = $p_etape;
			$fin =  1;
		}
		
		for($i = 0;$i < $fin;$i = $i + 1)
		{
			if(!is_numeric($p_etape))
			{
				$etape = $i;
			}
			
			// Clear var and tag on step
			$sql = 'DELETE
					FROM 
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
					WHERE 1 = 1
						AND `id_fiche` = '.$this->c_id_temp.' 
						AND `TYPE` = "EXTERNE" 
						AND `id_action` = '.($etape + 1);
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			

			$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
					WHERE ID =  '.$this->c_id_temp.' 
					AND Etape = '.($etape + 1).' 
					AND id_src IS NOT NULL';
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			//##############################################################################################################################
			//######################################################## FICHES ##############################################################
			//##############################################################################################################################
			//---------------------------------------- GET ID ----------------------------------------

			preg_match_all($this->c_motif_expr_reg['fiches'],$this->c_Obj_etapes[$etape]->contenu,$out);
			
			// Delete duplicate value of the array
			$array_expr_id = array_values(array_unique($out[2]));
				
			//------------------------------------- GET Versions ----------------------------------------
			$j = 0;
			foreach($array_expr_id as $value) 
			{
				$motif = '#<a href="ifiche.php\?(&amp;)?ID='.$value.'.+version=([0-9]+)#';
				
				preg_match_all($motif,$this->c_Obj_etapes[$etape]->contenu,$out);
				
				// Delete duplicate value of the array
				$array_expr_versions = array_values(array_unique($out[2]));
	
				
				if(!isset($array_expr_versions[0]))
				{
					// Undefined version (max)
					$sql = 'INSERT INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'`(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`,`id_src`, `Version_src`) 
							SELECT NULL,'.$this->c_id_temp.','.($etape + 1).','.$this->c_version.',`Tag`, `Groupe`, `objet`, `temp`,`ID`,`Version` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'` 
							WHERE `ID` = '.$value.' 
							AND `Version` = (	SELECT `num_version` 
												FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
												WHERE `ID_fiche` = '.$value.'
											) 
							AND  `Etape` = 0 
							AND `objet` = "ifiche"';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
				else
				{
					// Version defined
					$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`,`id_src`, `Version_src`) 
							SELECT NULL,'.$this->c_id_temp.','.($etape + 1).','.$this->c_version.',`Tag`, `Groupe`, `objet`, `temp`,`ID`,`Version` 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
							WHERE ID = '.$value.' 
							AND Version = '.$array_expr_versions[0].' 
							AND  `Etape` = 0 
							AND objet = "ifiche"';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}	
			}	
			
			//##############################################################################################################################
			//######################################################## CODES ###############################################################
			//##############################################################################################################################	
			//---------------------------------------- GET ID ----------------------------------------
	
			
			preg_match_all($this->c_motif_expr_reg['codes'],$this->c_Obj_etapes[$etape]->contenu,$out);
			
			// Delete duplicate value of the array
			$array_expr_id = array_values(array_unique($out[2]));
			
			
			
			//------------------------------------- GET Versions ----------------------------------------
			// On crée le motif pour l'execution regulière.
			$j = 0;
			foreach ( $array_expr_id as $value ) 
			{
				$motif = '#href="icode.php\?(&amp;)?ID='.$value.'.+version=([0-9]+)#';
				
				preg_match_all($motif,$this->c_Obj_etapes[$etape]->contenu,$out);
				
				// Delete duplicate value of the array
				$array_expr_versions = array_values(array_unique($out[2]));
	
				if(!isset($array_expr_versions[0]))
				{
					// Version undefined (max)
					
					$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`,`id_src`, `Version_src`) 
							SELECT NULL,'.$this->c_id_temp.','.($etape + 1).','.$this->c_version.',`Tag`, `Groupe`, `objet`, `temp`,`ID`,`Version` 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
							WHERE ID = '.$value.' 
							AND Version = (SELECT version FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' WHERE ID = '.$value.') 
							AND  `Etape` = 0 
							AND objet = "icode"';
				
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);				
				}
				else
				{
					// Version defined
				
					$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`,`id_src`, `Version_src`) 
							SELECT NULL,'.$this->c_id_temp.','.($etape + 1).','.$this->c_version.',`Tag`, `Groupe`, `objet`, `temp`,`ID`,`Version` 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
							WHERE ID = '.$value.' 
							AND Version = '.$array_expr_versions[0].' 
							AND  `Etape` = 0 
							AND objet = "icode"';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}		
			}
		}	
	}
	
	
	/**==================================================================
	* Method to build list of step to create a link in tinyMCE framework
	* Recover step title and step number
	* @param decimal step number currently edited
	* @return sql
	====================================================================*/				
	public function generer_liste_etapes($etape_en_cours)
	{
		$sql = '';																				
		$i = 0;
		
		//==================================================================
		// Extract Title (expreg pattern)
		//==================================================================
		$motif = '#<span class="BBTitre">(.*?)</span>#';							
		//==================================================================
		
		//==================================================================
		// Build SQL query
		//==================================================================
		foreach($this->c_Obj_etapes as $array_obj) 
		{
			if($array_obj->numero != $etape_en_cours)
			{
				preg_match_all($motif,$array_obj->contenu,$out);
				
				// Delete duplicate value in array
				$titre = array_values(array_unique($out[1]));
				
				if($i == 0)
				{
					if(isset($titre[0]))
					{
						$sql .= ' SELECT "'.$_SESSION[$this->c_ssid]['message'][69].' '.$array_obj->numero.'" as Etape,"'.$this->protect_display($titre[0]).'" as title,'.$array_obj->numero.' as step';
					}
					else
					{
						$sql .= ' SELECT "'.$_SESSION[$this->c_ssid]['message'][69].' '.$array_obj->numero.'" as Etape,"[b][color=#FF0000]'.$_SESSION[$this->c_ssid]['message'][51].'[/color][/b]" as title,'.$array_obj->numero.' as step';
					}
				}
				else
				{
					if(isset($titre[0]))
					{
						$sql .= ' UNION SELECT "'.$_SESSION[$this->c_ssid]['message'][69].' '.$array_obj->numero.'" as Etape,"'.$this->protect_display($titre[0]).'" as title,'.$array_obj->numero.' as step';
					}
					else
					{
						$sql .= ' UNION SELECT "'.$_SESSION[$this->c_ssid]['message'][69].' '.$array_obj->numero.'" as Etape,"[b][color=#FF0000]'.$_SESSION[$this->c_ssid]['message'][51].'[/color][/b]" as title,'.$array_obj->numero.' as step';
					}
				}
				$i = $i + 1;
			}
		}
		//==================================================================

		return $sql;
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Method name : replace_var_by_value
	 * Replace global input variable (Varin) in steps description
	 * If a variable is defined in URL with no value, then null will set in this variable ( force no value mode )
	 ====================================================================*/	
	private function replace_var_by_value()
	{
		// Pour chaque varin de l'etape qui comporte une valeur on la modifie dans le corps des étapes.
		switch($this->c_ik_valmod) 
		{
			// Only custom value is used to build final result
			case 0:
				$sql = 'SELECT nom,resultat 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND TYPE = "IN" 
						AND LENGTH(IFNULL(`RESULTAT`,"")) > 0'; // ISHEET_INCLUDE_BLANK_REPLACE
				break;
			// Only default value is used with custom value to build final result
			case 1:
				$resultat = 'IF(
								( LENGTH(IFNULL(`DEFAUT`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
								,`DEFAUT`
								,`RESULTAT`
							  ) AS resultat'; // ISHEET_INCLUDE_BLANK_REPLACE
				
				$sql = 'SELECT nom,'.$resultat.' 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND ( LENGTH(IFNULL(`DEFAUT`,"")) > 0 OR LENGTH(IFNULL(`RESULTAT`,"")) > 0)
						AND TYPE = "IN"';
				break;
			// Only neutral value is used with custom value to build final result	
			case 2:
				$resultat = 'IF(
								( LENGTH(IFNULL(`NEUTRE`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
								,`NEUTRE`
								,`RESULTAT`
							  ) AS resultat'; // ISHEET_INCLUDE_BLANK_REPLACE
								
				$sql = 'SELECT nom,'.$resultat.' 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND ( LENGTH(IFNULL(`NEUTRE`,"")) > 0 OR LENGTH(IFNULL(`RESULTAT`,"")) > 0)
						AND TYPE = "IN"';
				break;
			// Both default and neutral values are included to build final result	
			case 3:
				$resultat = 'IF(
								( LENGTH(IFNULL(`NEUTRE`,"")) > 0 OR LENGTH(IFNULL(`DEFAUT`,"")) > 0 ) AND (`RESULTAT` IS NOT NULL AND LENGTH(IFNULL(`RESULTAT`,"")) = 0 )
								,IF	(
										LENGTH(IFNULL(`NEUTRE`,"")) > 0
										,`NEUTRE`
										,`DEFAUT`
									)
								,`RESULTAT`
							) AS resultat'; // ISHEET_INCLUDE_BLANK_REPLACE
								
				$sql = 'SELECT nom,'.$resultat.' 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND ( LENGTH(IFNULL(`DEFAUT`,"")) > 0 OR LENGTH(IFNULL(`NEUTRE`,"")) > 0 OR LENGTH(IFNULL(`RESULTAT`,"")) > 0)
						AND `TYPE` = "IN"';
				break;
		}
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$i = 0;
		while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{
			foreach($this->c_Obj_etapes as $value)
			{
				if($i == 0)
				{	
					$value->html_temp = $value->contenu;
				}
				
				if(!is_null($row['resultat']))
				{
				// Specific because string with only space digit is not visible in css span :(
				// So, do a raw replace space by &nbsp;
				if(trim($row['resultat']) == "") // ADD_VARIN_VISIBLE_BLANK
				{
					$row['resultat'] = str_replace(" ","&nbsp",$row['resultat']);	
				}
					// Varin SIBY
					//$value->html_temp = str_replace('<span class="BBVarIn">'.htmlentities($row['nom'],ENT_QUOTES,'UTF-8').'</span>','<span class="BBVarIn">'.$row['resultat'].'</span>',$value->html_temp);
					$value->html_temp = str_replace('<span class="BBVarIn">'.htmlentities($row['nom'],ENT_QUOTES,'UTF-8').'</span>','<span onmouseover="ikdoc(\'id_aide\');set_text_help(461,\'\',\'\',\'<span class=\\\'BBVarInInfo\\\'>'.$row['nom'].'</span>\');" onmouseout="ikdoc();unset_text_help();" class="ikvalorised ikvalorised_varin BBVarIn">'.$row['resultat'].'</span>',$value->html_temp); // REMIND_ORIGINAL_VARIN_NAME
				}
			}
			$i = $i + 1;	
		}	
	}
	/*===================================================================*/	
	
	
	private function remplacer_var_lien_non_iobjet($txt,$id_etape)
	{
		// Pour chaque varin de l'etape qui comporte une valeur on la modifie dans le corps des étapes.
		$sql = '	SELECT
						nom AS "nom",
						resultat AS "Rresultat" 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE 1 = 1
				AND id_fiche = '.$this->c_id_temp.' 
				AND TYPE = "IN"
				AND resultat <> ""
				';		

		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$i = 0;
		while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{

			foreach($this->c_Obj_etapes as $obj_etape)
			{
				preg_match_all($this->c_motif_expr_reg['lien_non_objet'],$txt,$out);

				$test = array();
				foreach ($out[2] as $key => $value)
				{
					$test[$key] = explode('&',$value);
				}
	
				$explode_param = array();
				foreach ($test as $key => $value)
				{
					foreach ($value as $key_param => $value_param)
					{
						$explode_param[$key][$key_param] = explode('=',$value_param);
					}
				}

				$url2 = array();
				$i = 0;

				foreach ($explode_param as $value)
				{
					$url2[$i] = $out[1][$i];
					foreach ($value as $key_param => $value_param)
					{
						if(strstr($value_param[1],$row['nom']) != false)
						{
							$url2[$i]  .= '&'.$value_param[0].'='.$row['resultat'];
						}
						else
						{
							$url2[$i]  .= '&'.$value_param[0].'=';
						}
					}
					$i = $i + 1;
				}
				
				foreach ($out[1] as $key => $value)
				{
					$obj_etape->html_temp = str_replace($value.$out[2][$key],$url2[$key],$obj_etape->html_temp);
				}
			}	
			$i = $i + 1;
		}	
	}
	
	private function protect_display($texte,$appel_ajax = false)
	{
		if(!$appel_ajax)		// Si appel ajax alors on ne protège pas les quotes
		{
			$texte = str_replace('\\','\\\\',$texte);
			$texte = str_replace('"','\\"',$texte);
		}
		
		$texte = str_replace(chr(10),"",$texte);
		$texte = str_replace(chr(13),"<br />",$texte);
		
		$texte = $this->update_balises($texte);

		return $texte;	
	}
	
	
	/**
	 * Cette methode permet de mettre à jour les balises des 
	 * tiny en balise html compatible avec dhtmlx
	 */
	public function update_balises($texte)
	{
		
		$texte = str_replace('<strong>',"<b>",$texte);
		$texte = str_replace('</strong>',"</b>",$texte);
		$texte = str_replace('<em>',"<i>",$texte);
		$texte = str_replace('</em>',"</i>",$texte);	
		return $texte;
		
	}
	
	private function protect_save($texte)
	{
		$texte = addslashes($texte);
		return $texte;
	}

	/**
	 * @method string protege les bornes < et > 
	 * @param string $texte texte à proteger
	 * @return string texte protegé
	 * @access private
	 */
	private function protect_xml($texte)
	{
		$texte = rawurlencode($texte);
		
		return $texte;
	}	
	
	public function contenu_tab_etapes()
	{
		$content_head_main = '<div id="tabbar_step" style="overflow:hidden;top:0;bottom:0;left:0;right:0;background-color:#CCC;position:absolute;"></div>';
		
		return $content_head_main;
	}
	
	private function generer_etapes_separee()
	{
		return '<div id="step_tabbar_sep" style="overflow:hidden;top:0;bottom:0;left:0;right:0;background-color:#CCC;position:absolute;"></div>';
	}
	
	public function generer_tab_etapes()
	{
		$onglet =  "var tabbar_step = new iknow_tab('tabbar_step');";
		$onglet .= 'tabbar_step.addTab("tab-level2_1","<div class=\"onglet_icn_inline\">'.$_SESSION[$this->c_ssid]['message'][81].'</div>","'.rawurlencode($this->display_step()).'","set_tabbar_actif(a_tabbar.getActiveTab(),retourne_tab_entete_actif(),\'tab-level2_1\',retourne_tab_etape_ligne_actif());charger_var_dans_url();");';

		if($this->type == __FICHE_VISU__)
		{
			$onglet .= 'tabbar_step.addTab("tab-level2_2","<div class=\"onglet_icn_step_solo\">'.rawurlencode('<span '.$this->generer_texte_aide(437,0).'>'.$_SESSION[$this->c_ssid]['message'][82].' - <input style="height:15px;font-size:10px;" onKeypress="goto_step_input(event,'.$this->c_nbr_etapes.');" id="etape_goto" type="text" value="1" size="3"></input> / '.$this->c_nbr_etapes.'</span>').'</div>","'.rawurlencode($this->generer_etapes_separee()).'","set_tabbar_actif(a_tabbar.getActiveTab(),retourne_tab_entete_actif(),\'tab-level2_2\',retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			
			$onglet .= $this->afficher_etapes_separee();
		}	
		
		return $onglet;
	}
	
	public function set_id($p_id)
	{
		$this->c_id = $p_id;
	}
	
	public function set_version($p_version)
	{
		$this->c_version = $p_version;
	}
	
	public function copier_etape($id_etape)
	{
		// Add a step	
		$this->ajouter_etape($id_etape + 1);
		
		// Copy the content of the step $id_etape into the new step
		$this->c_Obj_etapes[$id_etape]->contenu = $this->c_Obj_etapes[$id_etape - 1]->contenu;
		
		// Copy tags
		$str = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' (Idtag,ID,Etape,Version,Tag,Groupe,temp,id_src,version_src,objet) 
				SELECT Idtag,ID,(Etape + 1) as ETAPE,Version,Tag,Groupe,temp,id_src,version_src,objet 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE ID = '.$this->c_id_temp.' 
				AND Etape = '.$id_etape;
		$requete = $this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		// Copy varout
		$str = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'(IDP,`TYPE`,id_fiche,num_version,id_action,id_src,num_version_src,id_action_src,`type_src`,`max_version_src`,NOM,DESCRIPTION,DEFAUT,NEUTRE,RESULTAT,used,COMMENTAIRE) 
				SELECT IDP,`TYPE`,id_fiche,num_version,id_action + 1,id_src,num_version_src,id_action_src,`type_src`,`max_version_src`,NOM,DESCRIPTION,DEFAUT,NEUTRE,RESULTAT,used,COMMENTAIRE 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND id_action = '.$id_etape.' 
				AND `TYPE` = \'OUT\'';
		
		$requete = $this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);	
		
		// Log action
		$this->insert_log('copier',$id_etape,$id_etape + 1,$this->c_Obj_etapes[$id_etape]->contenu);
		
		echo $this->display_step(true);
	}
	
	
	/**
	 * @param unknown_type enum('modifier','deplacer','copier','ajouter','supprimer')
	 * @param unknown_type $source
	 * @param unknown_type $cible
	 * @param unknown_type $contenu
	 */
	private function insert_log($action,$source,$cible,$contenu)
	{
		$requete = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_log_action']['name'].'(`date_action`, `objet`, `ID`, `version`, `action`, `source`, `cible`, `contenu`) 
									VALUES("'.(microtime(true)*10000).'","ifiche",'.$this->c_id_temp.',0,"'.$action.'",'.$source.','.$cible.',"'.$this->protect_save($contenu).'")';
		
		$this->exec_sql($requete,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	
	
	public function recup_contenu_etape($fiche,$version,$etape)
	{
		$requete = 'SELECT description FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name'].' WHERE id_fiche = '.$fiche.' AND num_version = '.$version.' AND id_etape = '.$etape;
		
		$resultat = $this->exec_sql($requete,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$description = '';
		while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
		{		
			$description = $row['description'];
		}
		
		$this->alias[0] = $fiche;
		$this->alias[1] = $version;
		$this->alias[2] = $etape;

		$description = str_replace(chr(13),"<br />",$description); 
		$description = str_replace(chr(10),"",$description);
		$description = str_replace('<strong>','<b>',$description);
		$description = str_replace('</strong>','</b>',$description);
		
		return $description;	
	}
	
	
	public function save_alias($num_etape)
	{
		$sql = 'SELECT description FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name'].' WHERE id_fiche = '.$this->alias[0].' AND num_version = '.$this->alias[1].' AND id_etape = '.$this->alias[2];
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$description = mysql_result($resultat,0,'description');
		
		// Copy content
		$this->c_Obj_etapes[$num_etape - 1]->contenu = $description;
		
		// Delete tags
		$str = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->c_id_temp.' AND Etape = '.$num_etape.' AND Version = '.$this->c_version;		
		$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);	
		
		// Delete vars
		$str = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' WHERE id_fiche = '.$this->c_id_temp.' AND id_action = '.$num_etape.' AND num_version = '.$this->c_version;		
		$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);		
		
		// Copy tags
		$str = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(IdTag,ID,Etape,Version,Tag,Groupe,temp,id_src,version_src,objet)
			SELECT NULL, '.$this->c_id_temp.' As ID,'.$num_etape.','.$this->c_version.',Tag,Groupe,temp,id_src,version_src,objet FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'
			WHERE ID = '.$this->alias[0].' AND version = '.$this->alias[1].' AND Etape = '.$this->alias[2].' AND id_src IS NULL';
			
		$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);	
		
		// Copy vars		
		$str = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'(IDP,id_fiche,`TYPE`,num_version,id_action,id_src,num_version_src,id_action_src,NOM,DESCRIPTION,DEFAUT,NEUTRE,RESULTAT,used,COMMENTAIRE)
				SELECT NULL,'.$this->c_id_temp.' AS id_fiche,`TYPE`,'.$this->c_version.','.$num_etape.',id_src,num_version_src,id_action_src,NOM,DESCRIPTION,DEFAUT,NEUTRE,RESULTAT,used,COMMENTAIRE 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE 1 = 1
				AND id_fiche = '.$this->alias[0].' 
				AND num_version = '.$this->alias[1].' 
				AND id_action = '.$this->alias[2].' 
				AND `TYPE` = "OUT"';
			
		$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		// Log action
		$this->insert_log('modifier',$num_etape,$num_etape,$description);
		
		return $this->display_step(true);
		
	}	
	
	public function sauvegarder_variables_etape($id_etape)
	{
		$str = "INSERT INTO ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."( `IDP` ,
					`TYPE` ,
					`id_fiche` ,
					`num_version` ,
					`id_action` ,
					`id_src` ,
					`num_version_src` ,
					`id_action_src` ,
					`type_src`,
					`max_version_src`,
					`NOM` ,
					`DESCRIPTION` ,
					`DEFAUT` ,
					`NEUTRE` ,
					`RESULTAT` ,
					`used` ,
					`COMMENTAIRE`,`temp` )

					SELECT  `IDP` ,
					`TYPE` ,
					`id_fiche` ,
					`num_version` ,
					`id_action` ,
					`id_src` ,
					`num_version_src` ,
					`id_action_src` ,
					`type_src`,
					`max_version_src`,
					`NOM` ,
					`DESCRIPTION` ,
					`DEFAUT` ,
					`NEUTRE` ,
					`RESULTAT` ,
					`used` ,
					`COMMENTAIRE`,1 
					FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."` 
					WHERE id_fiche = ".$this->c_id_temp." 
					AND `TYPE` <> 'IN' 
					AND `id_action` = ".$id_etape;
				
	 	$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
	}
	
	public function sauvegarder_tags_etape($id_etape)
	{
		$str = "INSERT INTO ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`,id_src,version_src)
				SELECT  `IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, 1,id_src,version_src from ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']." WHERE ID = ".$this->c_id_temp." AND Etape = ".$id_etape;
		
		$this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	
	
	public function cancel_modif_etape($id_etape)
	{
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.'
				AND temp = 0 
				AND id_action = '.$id_etape;
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				SET temp = 0 
				WHERE id_action = '.$id_etape.' 
				AND id_fiche = '.$this->c_id_temp.' 
				AND temp = 1';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		echo $this->display_step(true);
	}
	
	public function cancel_modif_tags($id_etape)
	{
		// On purge les tags validés car on va rappatrier les tags temporaires en tags validés
		$this->purge_tag(0);
		
		// On transfert les tags temporaires en tags validés
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				SET temp = 0 
				WHERE Etape = '.$id_etape.' 
				AND ID = '.$this->c_id_temp.' 
				AND temp = 1';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return $this->display_step(true);	
	}
	
	public function delete_tags_temp($id_etape)
	{
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE ID = '.$this->c_id_temp.' 
				AND temp = 1 
				AND Etape = '.$id_etape;
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	
	/*
	 * $corps: corps dans lequel il faut récuperer les attributs
	 * $attribut: nom de l'attribut HTML (onmouseover,href,onmouseout...)
	 */
	private function get_attribut($corps_html,$balise,$attribut)
	{
		$tab_balises = $this->get_balise($corps_html,$balise);
		$attribut = $attribut.'="';
		$longeur_attribut = strlen($attribut);
	
		// Tableau qui contiendra les valeurs des attributs
		$tab_attribut = array();
		$key_tab_attribut = 0;
		$t = 0;		

		// On parcours toute les balises
		foreach($tab_balises as $corps)
		{
			$original = $corps;
			// Tant que tout le corps n'est pas consommé
			while(strlen($corps) > 0)
			{							
				$trouve_fin_attribut = false;							// Initialisation de la variable qui définie si la fin de l'attribut a été trouvé
				$corps = strstr($corps,$attribut);						// On cherche l'attribut
				$corps = substr($corps,$longeur_attribut);				// On se décale pour ne plus avoir le nom de l'attribut (par ex: onmouseover=")
				$fin_attribut_non_trouve = '';							// Stocke le contenu de l'attribut si la fin n'a pas été trouvé du premier coup (dans le cas ou une " est protégée dans l'attribut onmouseover="alert('\"');"
				
				// Tant que la fin de l'attribut n'a pas été trouvé
				while(!$trouve_fin_attribut)
				{
					$text_avant = $this->strstrb($corps,'"');			// Contenu avant la double quote
					$text_apres = strstr($corps,'"');					// Contenu après la double quote (prend la double quote)
		
					if(substr($text_avant,-1) == '\\')					// On vérifie si le caractère avant la double quote n'est pas un antislash
					{
						$corps = substr($corps,strlen($text_avant)+1);	// On décale
		
						if($fin_attribut_non_trouve == '')				// Dans le cas où c'est la premiere fois que l'on passe dans le if
						{
							$fin_attribut_non_trouve = $text_avant;
						}
						else
						{
							$fin_attribut_non_trouve = $fin_attribut_non_trouve.'"'.$text_avant;
						}
						$t = $t + 1;
					}
					else
					{
						$trouve_fin_attribut = true;					// La fin de l'attribut a été trouvé
						if($text_avant != '' || $fin_attribut_non_trouve != '')
						{
							
							if($fin_attribut_non_trouve == '')
							{
								$tab_attribut[$key_tab_attribut]['attribut'] = $text_avant;
								$tab_attribut[$key_tab_attribut]['balise'] = $original;
							}
							else
							{
								$tab_attribut[$key_tab_attribut]['attribut'] = $fin_attribut_non_trouve.'"'.$text_avant;
								$tab_attribut[$key_tab_attribut]['balise'] = $original;
							}
							$key_tab_attribut = $key_tab_attribut + 1;
						}
					}	
				}
			}			
		}
		return $tab_attribut;
	}
	
	/* Fonctionne uniquement avec les balises du type <xxxx attribut1="" attribut2="">yyyyyy</xxxx>
	 * $corps: corps du code HTML dont il faut rechercher les balises
	 * $balise: type de balise à extraire (a,img...) 
	 */
	private function get_balise($corps,$balise)
	{
		$motif = '#<'.$balise.'([^>]+)>([^<]*)</'.$balise.'>#';
		
		preg_match_all($motif,$corps,$out);
		
		return $out[0];
	}
	
	
	/*
	 * Supprime l'attribut $attribut des balises $balise dans $corps_html
	 * */
	private function delete_attribut_balise($corps_html,$balise,$attribut)
	{
		$array_attributs = $this->get_attribut($corps_html,$balise,$attribut);	// Récupère la liste des attributs $attribut pour la balise $balise
		$array_replace = array();										
		
		foreach($array_attributs as $value)
		{
			$array_replace[] = str_replace($attribut.'="'.$value['attribut'].'"','',$value['balise']);
		}
		
		$i = 0;
		foreach($array_attributs as $value)
		{
			$corps_html = str_replace($value['balise'],$array_replace[$i],$corps_html);
			
			$i = $i + 1;
		}
		
		return $corps_html;
	}
	
	/*
	 * Ajoute l'attribut target="_blank" dans $corps_html
	 * */
	private function add_attribut_target($corps_html)
	{
		$array_attributs = $this->get_attribut($corps_html,'a','href');	// Récupère la liste des attributs $attribut pour la balise a
		$array_replace = array();										
		
		foreach($array_attributs as $value)
		{
			if(substr($value['attribut'],0,1) != '#')
			{
				$array_replace[] = str_replace('href="'.$value['attribut'].'"','href="'.$value['attribut'].'" target="_blank"',$value['balise']);
			}
			else
			{
				$array_replace[] = $value['balise'];
			}
		}
		
		$i = 0;
		foreach($array_attributs as $value)
		{
			$corps_html = str_replace($value['balise'],$array_replace[$i],$corps_html);
			
			$i = $i + 1;
		}
		
		return $corps_html;
	}

	
	private function get_max_version_objet($iobjet,$id)
	{
		/**==================================================================
		 * PREPARATION DE LA REQUETE
		 ====================================================================*/			
		switch ($iobjet) 
		{
			case 'ifiche':
				$sql = 'SELECT `num_version` as max_version  
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`  
						WHERE `id_fiche` = '.$id;
				break;
			case 'icode':
				$sql = 'SELECT `version` as max_version  
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'`  
						WHERE `id` = '.$id;
				break;
			case 'idossier':
				// TODO
				$sql = 'SELECT 1 as max_version'; 
				break;
		}		
		/*===================================================================*/
		
		/**==================================================================
		 * EXECUTION DE LA REQUETE
		 ====================================================================*/		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		return mysql_result($resultat,0,'max_version');
		/*===================================================================*/
	}
	
	
	private function get_titre_objet($iobjet,$id,$version)
	{
		/**==================================================================
		 * PREPARATION DE LA REQUETE
		 ====================================================================*/			
		switch ($iobjet) 
		{
			case 'ifiche':
				if($version == null)
				{
					// Version MAX
					$sql = 'SELECT `titre` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
							WHERE `id_fiche` = '.$id;
				}
				else
				{
					// Version < MAX
					$sql = 'SELECT `titre` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'` 
							WHERE `id_fiche` = '.$id.' 
							AND `num_version` = '.$version;	
				}
				
				break;
			case 'icode':
				if($version == null)
				{
					// Version MAX
					$sql = 'SELECT `titre` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
							WHERE `id` = '.$id;				
					
				}
				else
				{
					// Version < MAX
					$sql = 'SELECT `titre` 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` 
							WHERE `id` = '.$id.' 
							AND `version` = '.$version;
				}
				break;
			case 'idossier':
				// TODO
				$sql = 'SELECT 1 as titre';
				break;
		}		
		/*===================================================================*/
		
		/**==================================================================
		 * EXECUTION DE LA REQUETE
		 ====================================================================*/		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$titre = mysql_result($resultat,0,'titre');
		
		
		return $this->convertBBCodetoHTML($titre);
		/*===================================================================*/
	}	
	
	public function editer_etape($id_etape)
	{
		// Backup the var of the step
		$this->sauvegarder_variables_etape($id_etape);
		
		// Récupération du contenu de l'étape
		return $this->c_Obj_etapes[($id_etape - 1)]->contenu;
	}
	
	/**
	 * 
	 * @param decimal $temp 
	 * 						0: tag qui est validé dans l'étape
	 * 						1: tag temporaire pendant l'edition de l'étape
	 * @return unknown_type
	 */
	public function purge_tag($temp)
	{
		/**==================================================================
		 * EXECUTION DE LA REQUETE DE PURGE DES TAGS TEMPORAIRES
		 ====================================================================*/		
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->c_id_temp.' AND temp = '.$temp;
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/		
	}

	
	private function existe_id_objet($id,$iobjet)
	{
		/**==================================================================
		 * PREPARATION DE LA REQUETE
		 ====================================================================*/			
		if($iobjet == 'ifiche' || $iobjet == 'ifiche.php')
		{
			$sql = 'SELECT `id_fiche`  
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
					WHERE `id_fiche` = '.$id;
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
		}
		elseif($iobjet == 'icode' || $iobjet == 'icode.php')
		{
			$sql = 'SELECT `ID` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
					WHERE `ID` = '.$id;	
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		elseif($iobjet == 'password' || $iobjet == 'password.php')
		{
			$sql = 'SELECT `ID` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_password']['name'].'` 
					WHERE `ID` = '.$id;
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link_password);
		}
		else
		{
			// TODO
			$sql = 'SELECT 1 as id';
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		/*===================================================================*/
		
		/**==================================================================
		 * EXECUTION DE LA REQUETE
		 ====================================================================*/		
		if(mysql_num_rows($resultat) == 0)
		{	
			return false;	// L'ID n'existe pas
		}
		else
		{
			return true;	// L'ID existe
		}	
		/*===================================================================*/	
	}
	
	private function existe_version_objet($id,$version,$iobjet)
	{
		/**==================================================================
		 * PREPARATION DE LA REQUETE
		 ====================================================================*/			
		switch ($iobjet) 
		{
				
			case 'ifiche':

				$sql = 'SELECT `num_version`  
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'` 
						WHERE `id_fiche` = '.$id.' 
						AND `num_version` = '.$version;
				break;	
			case 'icode':
				
				$sql = 'SELECT `version` 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'`  
						WHERE `ID` = '.$id.' 
						AND `version` = '.$version;				
				break;
			case 'password':
				// No version for password, always true
				return true;
				break;
			case 'idossier':
				// TODO
				$sql = 'SELECT 1 as id';
				break;
					
		}		
		/*===================================================================*/

		/**==================================================================
		 * EXECUTION DE LA REQUETE
		 ====================================================================*/		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		if(mysql_num_rows($resultat) == 0)
		{	
			return false;	// La version n'existe pas
		}
		else
		{
			return true;	// La version existe
		}	
		/*===================================================================*/	
	}

	
	private function generer_ligne_erreur($class_erreur,$id_message_erreur,$action_click,$ancre = null)
	{
		if(is_numeric($id_message_erreur))
		{
			$message = $_SESSION[$this->c_ssid]['message'][$id_message_erreur];
		}
		else
		{
			$message = $id_message_erreur;
		}
		
		if(is_null($ancre))
		{
			$href = 'href="#" onclick="'.$action_click.'iknow_panel_reduire();"';
		}
		else
		{
			$href = 'href="#'.$ancre.'" onclick="'.$action_click.'iknow_panel_reduire();"';
		}
		
		return '<tr><td><a '.$href.' class="'.$class_erreur.'"></a></td><td>&nbsp;'.$message.'</td></tr>';
	}
	
	private function strstrb($h,$n)
	{
	    return array_shift(explode($n,$h,2));
	}
	
	
	private function commentaire_varinext(&$resultat)
	{
		if($resultat != false && mysql_num_rows($resultat) > 0)
		{
			// On déplace le pointeur interne de résultat au début recordset
			mysql_data_seek($resultat,0);
		
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
			{	
				if($row['id_action_src'] == 0)
				{
					// iCode
					$message[] = $row['nom'].' '.str_replace('$id_src',$row['id_src'],str_replace('$id_action',$row['id_action'],$_SESSION[$this->c_ssid]['message'][188]));
				}
				else
				{
					// iFiche
					$message[] = $row['nom'].' '.str_replace('$action_src',$row['id_action_src'],str_replace('$id_src',$row['id_src'],str_replace('$id_action',$row['id_action'],$_SESSION[$this->c_ssid]['message'][189]))); 
				}
				$replace[] = $row['variable'];
			}
			
			$this->c_commentaire_varinext_message = $message;
			$this->c_commentaire_varinext_replace = $replace;
		}
	}
	
	private function commentaire_varin_local(&$resultat)
	{
		if($resultat != false && mysql_num_rows($resultat) > 0)
		{
			// On déplace le pointeur interne de résultat au début recordset
			mysql_data_seek($resultat,0);
		
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
			{	
					$message[] = $row['nom'].' '.str_replace('$id_action',$row['id_action'],$_SESSION[$this->c_ssid]['message'][460]);
					$replace[] = $row['variable'];
			}
			
			$this->c_commentaire_varinlocal_message = $message;
			$this->c_commentaire_varinlocal_replace = $replace;
		}
	
	}
	
	
	private function commentaire_varin(&$resultat)
	{
		if($resultat != false && mysql_num_rows($resultat) > 0)
		{
			// On déplace le pointeur interne de résultat au début recordset
			mysql_data_seek($resultat,0);
		
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
			{	
					$message[] = $row['nom'];
					$replace[] = $row['variable'];
			}
			
			$this->c_commentaire_varin_message = $message;
			$this->c_commentaire_varin_replace = $replace;
		}
	
	}
	
	/**
	 * Vérifie si $nom est utilisé dans des étapes (s'arrête à la première occurence trouvée)
	 * 
	 * @return boolean
	 * NR_IKNOW_4_
	 */
	private function get_varinext_utilise($nom_varinext,$nom_varext)
	{
		/**==================================================================
		 * Recherche dans les spans
		 ====================================================================*/		
		foreach($this->c_Obj_etapes as $obj_etape)
		{
			$recherche = stristr($obj_etape->contenu,'<span class="BBVarInExt">'.$nom_varinext.'</span>');

			if($recherche != false)
			{
				return true;			// Trouvé la varinext, sortie de la fonction
			}
			else
			{
				$recherche = strstr($obj_etape->contenu,'<span class="BBVarExt">'.$nom_varext.'</span>');
				
				if($recherche != false)
				{
					return true;		// Trouvé la varext sortie de la fonction
				}
			}
		}
		/*===================================================================*/

		
		/**==================================================================
		 * La variable n'a pas été trouvée dans spans, recherche dans les liens
		 ====================================================================*/			
		foreach($this->c_Obj_etapes as $obj_etape)
		{
			$recherche = strstr($obj_etape->contenu,rawurlencode('$'.$nom_varinext.'$'));

			if($recherche != false)
			{
				return true;		// Trouvé la varinext, sortie de la fonction
			}
			else
			{
				$recherche = strstr($obj_etape->contenu,'$'.rawurlencode($nom_varinext).'$');
				
				if($recherche != false)
				{
					return true;		// Trouvé la varinext, sortie de la fonction
				}
				else
				{
					$recherche = strstr($obj_etape->contenu,rawurlencode('$'.$nom_varext.'$'));
				
					if($recherche != false)
					{
						return true;		// Trouvé la varext sortie de la fonction
					}	
					else
					{
						$recherche = strstr($obj_etape->contenu,'$'.rawurlencode($nom_varext).'$');
						if($recherche != false)
						{
							return true;		// Trouvé la varext sortie de la fonction
						}	
					}
				}
			}
		}		
		/*===================================================================*/

		return false;				// Variable non trouvée dans la fiche
		
	}
	
	/**
	 * Mise à jour du statut de la fiche lors du changement du statut (en modif)
	 * @param entier $id_statut
	 */
	public function set_statut($id_statut)
	{
		$this->c_statut = $id_statut;
	}
	
	private function set_cache_variable()
	{
		/**==================================================================
		 * MISE EN CACHE DE TOUTES LES VARIABLES DE LA FICHE
		 * Tableau $this->c_variables_fiche
		 ====================================================================*/
				
		$sql = '	SELECT
						`IDP`,
						`NOM`,
						`resultat`,
						`type`,
						`id_action`,
						`id_src`,
						`id_action_src`,
						`used`,
						`defaut`,
						`neutre` 
					FROM 
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
					WHERE 1 = 1
						AND `id_fiche` = "'.$this->c_id_temp.'"
			  ';

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

		$this->c_variables_fiche = null;
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
		{
			switch($row['type'])
			{
				case 'IN':
					$nom = $row['NOM'].'()';
					$utilisee = true;
					break;
				case 'OUT':
					$nom = $row['NOM'].'('.$row['id_action'].')';
					$utilisee = true;
					break;
				case 'EXTERNE':
					$nom = $row['NOM'].'('.$row['id_action'].'\\'.$row['id_src'].'\\'.$row['id_action_src'].')';
					$nom_varext = $row['NOM'].'('.$row['id_src'].'\\'.$row['id_action_src'].')';
					// Vérification de l'utilisation de la variable dans les étapes
					($this->get_varinext_utilise($nom,$nom_varext) == true ? $used = 1 : $used = 0);

					$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
							SET `used` = '.$used.'
							WHERE `id_fiche` = '.$this->c_id_temp.' 
							AND `IDP` = '.$row['IDP'].' 
							AND `type` = "EXTERNE" 
							AND `id_action` = '.$row['id_action'].' 
							AND `id_src` = '.$row['id_src'].' 
							AND `id_action_src` = '.$row['id_action_src'].' 
							AND `NOM` = "'.$row['NOM'].'"';
					
					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
					if($used == 1)
					{
						$utilisee = true;
					}
					else
					{
						$utilisee = false;
					}
						
					break;
			}
			if(isset($_GET[$row['NOM']]))
			{
				$in_url = true;
			}
			else
			{
				$in_url = false;
			}
			$this->c_variables_fiche[] = array(0 => $nom,1 => $row['resultat'],2 => $row['type'], 3 => $row['id_action'],4 => $utilisee,5 => $row['defaut'],6 => $row['neutre'],7 => $in_url);
		}
		/*===================================================================*/	
	}

	private function afficher_etapes_separee()
	{
		/**==================================================================
		 * GENERATION DE L'ONGLET ETAPE SEPAREES
		 ====================================================================*/	
		$onglets = "var step_tabbar_sep = new iknow_tab('step_tabbar_sep');";
		/*===================================================================*/
			
		$motif_titre = '#class="BBTitre">([^<]+)</span>#i';
		$motif_etape= '#="\#([0-9]+)"#i';
		foreach ($this->c_Obj_etapes as $value) 
		{
			/**==================================================================
			 * ADAPTATION DU CONTENU DES ETAPES
			 ====================================================================*/
			$contenu_etape = str_replace('class="icon"','',$this->c_Obj_etapes[($value->numero - 1)]->html_temp);								
			$contenu_etape = str_replace('onmouseover','fv',$contenu_etape);
			$contenu_etape = str_replace('onmouseout','fo',$contenu_etape);
			$contenu_etape = str_replace('_entete_','_par_etape_',$contenu_etape);
			/*===================================================================*/

			/**==================================================================
			 * ADAPTATION DES LIENS VERS LES ETAPES
			 ====================================================================*/
			preg_match_all($motif_etape,$contenu_etape,$out);
			
			$array_expr = array_values(array_unique($out[1]));
			foreach($array_expr as $array_etape) 
			{
				$contenu_etape = str_replace('href="#'.$array_etape.'"','href="#4" onclick="step_tabbar_sep.setTabActive(\'tab-level2_2_'.$array_etape.'\');";"',$contenu_etape);
			}		
			/*===================================================================*/

			$content_etape = '<div class="tab_cont lp"><table class="wfull">';
			
			if($this->c_Obj_etapes[($value->numero - 1)]->tab_tag > 0)
			{
				$content_etape .= '<tr id="'.$value->numero.'l"><td style="width:25px;" id="outils_step'.$value->numero.'l" class="lp"><div id="div_outils_step'.$value->numero.'l"><div id="a_tag_etape-'.$value->numero.'"><div class="'.$this->c_Obj_etapes[($value->numero - 1)]->logo_tag.'" '.$this->c_Obj_etapes[($value->numero - 1)]->tag_hover.' onclick="vimofy_tag_etape('.$this->c_Obj_etapes[($value->numero - 1)]->numero.',false);"></div></div></div></td><td class="lp" id="tdetape'.$value->numero.'l">'.$contenu_etape.'</td></tr>';
			}
			else
			{
				$content_etape .= '<tr id="'.$value->numero.'l"><td style="width:25px;" id="outils_step'.$value->numero.'l" class="lp"><div id="div_outils_step'.$value->numero.'l"><div id="a_tag_etape-'.$value->numero.'"><div class="'.$this->c_Obj_etapes[($value->numero - 1)]->logo_tag.'" '.$this->c_Obj_etapes[($value->numero - 1)]->tag_hover.'></div></div></div></td><td class="lp" id="tdetape'.$value->numero.'l">'.$contenu_etape.'</td></tr>';
			}
			
			$content_etape .= '</table></div>';
			
			/**==================================================================
			 * CREATION DES ONGLETS (Un onglet par étape)
			 ====================================================================*/
			preg_match($motif_titre,$value->contenu,$out);

			if(isset($out[1]))
			{
				// Title found
				$titre = str_replace("'","\\'",$out[1]);
				$onglets .= 'step_tabbar_sep.addTab(\'tab-level2_2_'.$value->numero.'\',"'.rawurlencode('<span onmouseover="set_text_help(01,\''.$titre.'\');" onmouseout="unset_text_help();">'.$value->numero.'</span>').'","'.rawurlencode($content_etape).'","set_tabbar_actif(a_tabbar.getActiveTab(),retourne_tab_entete_actif(),retourne_tab_etape_actif(),\'tab-level2_2_'.$value->numero.'\');charger_var_dans_url();maj_input_etapes(\'tab-level2_2_'.$value->numero.'\');");';
			}
			else
			{
				// No title
				$onglets .= 'step_tabbar_sep.addTab("tab-level2_2_'.$value->numero.'","'.$value->numero.'","'.rawurlencode($content_etape).'","set_tabbar_actif(a_tabbar.getActiveTab(),retourne_tab_entete_actif(),retourne_tab_etape_actif(),\'tab-level2_2_'.$value->numero.'\');charger_var_dans_url();maj_input_etapes(\'tab-level2_2_'.$value->numero.'\');");';
			}
			/*===================================================================*/
		
		}
		return $onglets;
	}
	
	private function convertBBCodetoHTML($txt)
	{
		$txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','<b><u><span class="bbtitre">\\1</span></u></b>',$txt);
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
		$txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<span style="color:\\1;">\\2</span>',$txt);
		$txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','<font style="background-color: \\1;">\\2</font>',$txt);
		$txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','<font size="\\1">\\2</font>',$txt);
		$txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','<font face="\\1">\\2</font>',$txt);

		return $txt;
	}	
	//------------------------------------------------------------------------------------------------------------------------
	//-------------------------------------------------------- CONTROLES -----------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------
	
	/**
	 * Vérifie si dans les url il y a des doublons dans les paramètres
	 */
	public function check_duplicate_param_url()
	{
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$erreur = false;
		
		// parcours des étapes
		foreach($this->c_Obj_etapes as $key_etape => $value_etape)
		{
			if(isset($this->c_Obj_etapes[$key_etape]->lien_iobjet_etape))
			{
				// parcours des liens de l'étape
				foreach($this->c_Obj_etapes[$key_etape]->lien_iobjet_etape as $value_lien_iobjet_etape)
				{
					// Découpage de l'url de l'étape
					/**==================================================================
					 * ANALYSE DE L'URL
					 ====================================================================*/
					$parametre_url_get = html_entity_decode($value_lien_iobjet_etape[0],ENT_NOQUOTES,'UTF-8');
					$parametre_url_get = substr($parametre_url_get,1);	// Supprime le ? au début de l'URL		
					$parametre_url_get = explode('&',$parametre_url_get);
					
					$valorisation_variable = array();
					
					foreach($parametre_url_get as $value)
					{
						$valorisation_variable[] = explode('=',$value);
					}	
					
					foreach ($valorisation_variable as $key_url => $value_url)
					{
						$trouve = 0;
						foreach ($valorisation_variable as $key_url_verif => $value_url_verif)
						{
							if($value_url[0] == $value_url_verif[0])
							{
								$trouve = $trouve + 1;
							}
						}
						
						if($trouve > 1)
						{
							$erreur = true;
							// On efface la variable du tableau pour ne pas générer 2 fois le message d'erreur
							foreach ($valorisation_variable as $key_url_verif => $value_url_verif)
							{
								if($value_url[0] == $value_url_verif[0])
								{
									$valorisation_variable[$key_url_verif][0] = '';
								}
							}
							
							switch ($value_lien_iobjet_etape[4]) 
							{
								case 'ifiche.php':
									$iobjet = 'l\'fiche';
									break;
								case 'icode.php':
									$iobjet = 'le icode';
									break;
								case 'password.php':
									$iobjet = 'l\'accès';
									break;
								case 'idossier.php':
									$iobjet = 'le idossier';
									break;
							}
							/**
							 * Etape $j : Vous passez $x fois le paramètre $param dans le lien vers $objet $id. 
							 */
							$libelle = $_SESSION[$this->c_ssid]['message'][368];
							$libelle = str_replace('$x',$trouve,$libelle);
							$libelle = str_replace('$j',$value_etape->numero,$libelle);
							$libelle = str_replace('$param','<b>'.$value_url[0].'</b>',$libelle);
							$libelle = str_replace('$objet',$iobjet,$libelle);
							$libelle = str_replace('$id',$value_lien_iobjet_etape[1],$libelle);
							
							$verification[$key_array]['criticite'] = $niveau_erreur;
							$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_etape->numero).'" onclick="editer_etape('.$value_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
							$key_array = $key_array + 1;
					
						}
					}
					/*===================================================================*/
				}
			}
		}
				
		if($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < 1)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}		
		}
		return $verification;
	}
	
	
	
	/**
	 * Vérifie si les liens vers les iobjets sont corrects
	 */
	public function check_link_iobject()
	{
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$erreur = false;
		$erreur_verif_existance_param = false;
		
		foreach($this->c_Obj_etapes as $key_obj_etape => $value_obj_etape)
		{
			if(isset($this->c_Obj_etapes[($key_obj_etape)]->lien_iobjet_etape))
			{
				foreach($this->c_Obj_etapes[($key_obj_etape)]->lien_iobjet_etape as $key => $value_lien_iobjet_etape)
				{
					/**==================================================================
					 * VERIFICATION DE L'EXISTANCE DE L'OBJET
					 ====================================================================*/		
					// Liste des paramètres d'appel en rendu d'affichage pour permettre un classement des variables dans le cartouche
					$array_final = array();
					$valorisation_variable = array();
					/**==================================================================
					 * AFFECTATION DE L'OBJET
					 ====================================================================*/			
					switch ($value_lien_iobjet_etape[4]) 
					{
						case 'ifiche.php':
							$iobjet = 'ifiche';
							$param_exclus = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
							break;
						case 'icode.php':
							$iobjet = 'icode';
							$param_exclus = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
							break;
						case 'password.php':
							$iobjet = 'password';
							$param_exclus = array(0 => 'ID');
							break;
						case 'idossier.php':
							$iobjet = 'idossier';
							$param_exclus = array(0 => 'ID',1=> 'version');
							break;
					}
					
					/**==================================================================
					 * VERIFICATION DE L'EXISTANCE DE L'ID
					 ====================================================================*/				
					if($this->existe_id_objet($value_lien_iobjet_etape[1],$iobjet) == false)
					{
		       			// Le lien vers l'iobjet n'existe pas
		       			$libelle = $_SESSION[$this->c_ssid]['message'][69].' '.$value_obj_etape->numero.' : '.str_replace('$id',$value_lien_iobjet_etape[1],$_SESSION[$this->c_ssid]['message'][150]);
						$verification[$key_array]['criticite'] = $niveau_erreur;
						$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
						$key_array = $key_array + 1;
						$erreur = true;    
					}
					else
					{
						if($this->existe_version_objet($value_lien_iobjet_etape[1],$value_lien_iobjet_etape[2],$iobjet) == false)
						{
							// LA VERSION N'EXISTE PAS
							$libelle = $_SESSION[$this->c_ssid]['message'][69].' '.$value_obj_etape->numero.' : ';
							$libelle .= str_replace('$id',$value_lien_iobjet_etape[1],$_SESSION[$this->c_ssid]['message'][151]);
							$libelle = str_replace('$version',$value_lien_iobjet_etape[2],$libelle);
							$verification[$key_array]['criticite'] = $niveau_erreur;
							$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
							$key_array = $key_array + 1;
							$erreur = true;  
						}
					}
					/**==================================================================
					 *  FIN VERIFICATION DE L'EXISTANCE DE L'OBJET
					 ====================================================================*/

					
					/**==================================================================
					 * VERIFICATION DE L'EXISTANCE DES PARAMETRES D'APPEL EN ENTREE DE L'OBJET
					 ====================================================================*/	
					$var_appel = $this->decouper_url($value_lien_iobjet_etape[0]);
					$select = '';

					foreach($var_appel as $value_var_appel) 
					{
						if(!$this->in_arrayi($value_var_appel[0], $param_exclus))
						{
							if($select == '')
							{
								$select .= 'SELECT MD5("'.urldecode($value_var_appel[0]).'") as nom_md5,"'.urldecode($value_var_appel[0]).'" as nom';
							}
							else
							{
								$select .= ' UNION SELECT MD5("'.urldecode($value_var_appel[0]).'") as nom_md5,"'.urldecode($value_var_appel[0]).'" as nom';
							}
						}
					}
					
					if($select != '')
					{
						switch ($value_lien_iobjet_etape[4]) 
						{
							case 'ifiche.php':
								$sql = 'SELECT t.nom 
										FROM ('.$select.') t
										WHERE t.nom_md5 NOT IN (SELECT MD5(i.nom)
													FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' i
													WHERE i.id_fiche = "'.$value_lien_iobjet_etape[1].'" 
													AND i.num_version  = "'.$value_lien_iobjet_etape[2].'"
													AND i.`TYPE` = "IN")';
								$objet = $_SESSION[$this->c_ssid]['message'][382];
								break;
							case 'icode.php':
								$sql = 'SELECT t.nom 
										FROM ('.$select.') t
										WHERE t.nom_md5 NOT IN (SELECT MD5(i.nom)
													FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' i
													WHERE i.ID = "'.$value_lien_iobjet_etape[1].'"
													AND i.Version  = "'.$value_lien_iobjet_etape[2].'" 
													AND i.`TYPE` = "IN")';
								$objet = $_SESSION[$this->c_ssid]['message'][387];
								break;
							case 'password.php':
								$sql = 'SELECT 1 as "nom"';
								break;
							case 'idossier.php':
								$sql = 'SELECT 1 as "nom"';
								break;
							default:
								$sql = 'SELECT 1 as "nom"';
								break;
						}
						
						$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
						$niveau_erreur = 2;
						
						if(mysql_num_rows($resultat) > 0)
						{
							while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
							{
								if($row['nom'] != '')
								{
					       			$libelle = str_replace('$j',$value_obj_etape->numero,$_SESSION[$this->c_ssid]['message'][381]);
					       			$libelle = str_replace('$param','<b>'.$row['nom'].'</b>',$libelle);
					       			$libelle = str_replace('$objet',$objet,$libelle);
					       			$libelle = str_replace('$id',$value_lien_iobjet_etape[1],$libelle);
					       			
									$verification[$key_array]['criticite'] = $niveau_erreur;
									$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
									$key_array = $key_array + 1;
									$erreur = true; 
								}
							}
						}
					}

					/**==================================================================
					 * FIN VERIFICATION DE L'EXISTANCE DES PARAMETRES D'APPEL EN ENTREE DE L'OBJET
					 ====================================================================*/	
					
					/**==================================================================
					 * VERIFICATION DE L'EXISTANCE DES PARAMETRES PASSE A L'OBJET
					 ====================================================================*/
					foreach($var_appel as $value_var_appel)
					{
						if(isset($value_var_appel[1]))
						{
							// Serch for varinext passed in the URL
							preg_match_all($this->c_motif_expr_reg['param_url_ext'],urldecode($value_var_appel[1]),$out_var_lien);
							if(isset($out_var_lien[1][0]))
							{
								foreach($out_var_lien[1] as $value)
							 	{
							 		$trouve = false;

							 		foreach($this->c_variables_fiche as $c_variables_fiche)
									{
										if($c_variables_fiche[0] == $value)
										{
											$trouve = true;
										}
									}

									if($trouve == false)
									{
						       			$libelle = str_replace('$j',$value_obj_etape->numero,$_SESSION[$this->c_ssid]['message'][395]);
						       			$libelle = str_replace('$value','<b>'.$value.'</b>',$libelle);
						       			$libelle = str_replace('$objet',$objet,$libelle);
						       			$libelle = str_replace('$id',$value_lien_iobjet_etape[1],$libelle);

										$verification[$key_array]['criticite'] = 1;
										$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="warning"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
										$key_array = $key_array + 1;
										$erreur_verif_existance_param = true;
									}
								}
							}
							else
							{
								
								// Search for varin passed in the URL
								$motif_in = '#\$([^$]+\([0-9]*\)+)\$#';
								preg_match_all($motif_in,urldecode($value_var_appel[1]),$out_var_lien);
								if(isset($out_var_lien[1][0]))
								{
									foreach($out_var_lien[1] as $value)
								 	{
								 		$trouve = false;
	
								 		foreach($this->c_variables_fiche as $c_variables_fiche)
										{
											if($c_variables_fiche[0] == $value)
											{
												$trouve = true;
											}
										}
										
										if($trouve == false)
										{
							       			$libelle = str_replace('$j',$value_obj_etape->numero,$_SESSION[$this->c_ssid]['message'][395]);
							       			$libelle = str_replace('$value','<b>'.$value.'</b>',$libelle);
							       			$libelle = str_replace('$objet',$objet,$libelle);
							       			$libelle = str_replace('$id',$value_lien_iobjet_etape[1],$libelle);
							       			
											$verification[$key_array]['criticite'] = 1;
											$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="warning"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
											$key_array = $key_array + 1;
											$erreur_verif_existance_param = true; 
										}
									}
								}
							}
						}
					}
					/**==================================================================
					 * FIN VERIFICATION DE L'EXISTANCE DES PARAMETRES PASSE A L'OBJET
					 ====================================================================*/	
				}
			}
			
		}
		
		if($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($erreur_verif_existance_param)
			{
				if($_SESSION[$this->c_ssid]['niveau_informations'] < 1)
				{
					$_SESSION[$this->c_ssid]['niveau_informations'] = 1;
				}
				return $verification;
			}
			
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}		
		}
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
	private function decouper_url($parametres)
	{
		/**==================================================================
		 * ANALYSE DE L'URL
		 ====================================================================*/
		$parametre_url_get = html_entity_decode($parametres,ENT_NOQUOTES,'UTF-8');
		$parametre_url_get = substr($parametre_url_get,1);	// Supprime le ? au début de l'URL		
		$parametre_url_get = explode('&',$parametre_url_get);
		foreach($parametre_url_get as $value)
		{
			$valorisation_variable[] = explode('=',$value);
		}		
		/*===================================================================*/

		return $valorisation_variable;
	}
	/**
	 * Vérifie si les liens vers les iobjets contiennent un ssid
	 */
	public function check_ssid_presence_url()
	{
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$erreur = false;
		
		foreach ($this->c_Obj_etapes as $key_obj_etape => $value_obj_etape)
		{
			
			if(isset($this->c_Obj_etapes[($key_obj_etape)]->lien_iobjet_etape))
			{
				
				foreach($this->c_Obj_etapes[($key_obj_etape)]->lien_iobjet_etape as $key => $value_lien_iobjet_etape)
				{
					
					// Liste des paramètres d'appel en rendu d'affichage pour permettre un classement des variables dans le cartouche
					$array_final = array();
					$valorisation_variable = array();
					/**==================================================================
					 * AFFECTATION DE L'OBJET
					 ====================================================================*/			
					switch ($value_lien_iobjet_etape[4]) 
					{
						case 'ifiche.php':
							$iobjet = 'ifiche';
							$iobjet_libelle = $_SESSION[$this->c_ssid]['message'][313];
							break;
						case 'icode.php':
							$iobjet = 'icode';
							$iobjet_libelle = $_SESSION[$this->c_ssid]['message'][314];
							break;
						case 'password.php':
							$iobjet = 'password';
							$iobjet_libelle = $_SESSION[$this->c_ssid]['message'][315];
							break;
						case 'idossier.php':
							$iobjet = 'idossier';
							$iobjet_libelle = $_SESSION[$this->c_ssid]['message'][316];
							break;
					}
					
					$ssid_trouve = false;

					if(strstr($value_lien_iobjet_etape[0],'&amp;ssid='))
					{
						// Un ssid est présent dans l'URL
						$ssid_trouve = true;
					}
					
					/**==================================================================
					 * VERIFICATION DE L'EXISTANCE DE L'ID
					 ====================================================================*/				
					if($ssid_trouve)
					{
		       			// Le lien vers l'iobjet n'existe pas
		       			$libelle = $_SESSION[$this->c_ssid]['message'][69].' '.$value_obj_etape->numero.' : '.str_replace('$iobjet',$iobjet_libelle,$_SESSION[$this->c_ssid]['message'][312]);
		       			$libelle = str_replace('$numero',$value_lien_iobjet_etape[1],$libelle);
		       			$verification[$key_array]['criticite'] = $niveau_erreur;
						$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_obj_etape->numero).'" onclick="editer_etape('.$value_obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
						$key_array = $key_array + 1;
						$erreur = true;   
					}
				}
			}
		}
		
		if ($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < 1)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}		
		}
	}
					
	/**
	 * Vérifie qu'il n'y est qu'un lien vers un mot de passe par étape 
	 * 
	 * @param text $txt
	 * @param decimal $id_etape
	 */
	public function verif_lien_password()
	{
		
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$erreur = false;
		
		foreach ($this->c_Obj_etapes as $key_etape => $obj_etape) 
		{
			// Identifie le nombre de lien trouvé dans l'étape
			$nbr_lien_in_step = 0;
			
			
			$ref = $obj_etape->contenu;	
			while (strlen($ref) > 0 ) 
			{
				/**==================================================================
				 * Recherche d'un lien vers un mot de passe
				 ====================================================================*/
				$ref = strstr($ref,'"password.php');
					
				$parametres = strstr($ref,'?');	
	
				$l_index = strpos($parametres,'"');
				$parametres = substr($parametres,0,$l_index);	
		
				$id_iobjet = '';
		
				if($parametres != $obj_etape->contenu)
				{
					
					preg_match('#ID=([0-9]+)#i',$parametres,$temp);
	
					if(isset($temp[1]))
					{
						$id_iobjet = $temp[1];
						
					}
					
					preg_match('#&amp;version=([0-9]+)#i',$parametres,$temp);
					
				}
				
				if($id_iobjet != '')
				{	
					// Un lien vers un mot de passe a été trouvé
					$nbr_lien_in_step = $nbr_lien_in_step + 1;
				}
				// On décalle pour analyser le lien suivant
				$ref = substr($ref,3,strlen($ref)-3);	
			}
			
			if($nbr_lien_in_step > 1)
			{
       			// Il y a plus d'un lien vers un mot de passe dans l'étape
       			$libelle = str_replace('$j',$obj_etape->numero,$_SESSION[$this->c_ssid]['message'][228]);
       			$libelle = str_replace('$nbr_lien',$nbr_lien_in_step,$libelle);
       			$verification[$key_array]['criticite'] = $niveau_erreur;
				$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($obj_etape->numero).'" onclick="editer_etape('.$obj_etape->numero.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
				$key_array = $key_array + 1;
				$erreur = true;      				
			}
		}
		
		if ($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}		
		}
	}
	
	
	
	
	/**
	 * Cette methode permet de verifier si les variables locales sont correctes.
	 */
	public function check_local_varout() 
	{
		$erreur = false;
		$key_array = 0;
		$verification = array();
		$j = 1;
		foreach ($this->c_Obj_etapes as $array_obj)
		{
						
			// On récupère le nom des variables locale utilisées
			preg_match_all($this->c_motif_expr_reg['BBVarInl'],$array_obj->contenu,$out_nom);
			$array_expr_nom = $out_nom[1];
		
				
			//On recupere le numero d'etape des variables locale utilisées'
			//On crée le motif pour l'expression regulière.
			$motif = '#<span class="BBVarInl">[^(]*\(([^)]+)\)</span>#i';
			
			
			// On execute l'expression regulière pour chercher les variables.
			preg_match_all($motif,$array_obj->contenu,$out_id);
			
			$array_expr_valeur = $out_id[1];
			
	
			
			// Verification si une variable est définie sans les parenthèses
			$motif = '#<span class="BBVarInl">[^(]*\(([^)]*)\)</span>#i';
			
			// On execute l'expression regulière pour chercher les variables.
			preg_match_all($motif,$array_obj->contenu,$out_vide);	
			$array_expr_vide = $out_vide[1];		

			if(!empty($array_expr_vide))
			{		
				// ------ On va verifier si l'etape existe bien
				foreach($array_expr_vide as $key => $value)
				{
					if(!is_numeric($value) or $value == '')
					{						
						$message = str_replace('$nom',$out_vide[0][$key],$_SESSION[$this->c_ssid]['message'][99]);	// ['message'][99]: Etape 3: Format de la variable externe UserInv(1) incorrect.
						$message = str_replace('$j',($j),$message);
						$erreur = true;
						$verification[$key_array]['criticite'] = 2;
						$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.$j.'" onclick="editer_etape('.$j.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
						$key_array = $key_array + 1;      					
					}
				}
				
				// ------ On verifie si les variables locales existent bien en variable de sortie sur l'etape definie entre parenthese

				
				foreach ($array_expr_valeur as $key => $value)
				{
					
					// On récupère les variables de sortie dont l'etape est definie pour la variable locale
					$str = 'SELECT nom 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
							WHERE id_fiche = '.$this->c_id_temp.' 
							AND type="OUT" 
							AND id_action = '.$value.' 
							AND nom = "'.mysql_real_escape_string($array_expr_nom[$key]).'"';
					
					
					if(is_numeric($value))
					{
						$requete = $this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
						
						if(mysql_num_rows($requete) == 0)
						{	
							$message = str_replace('$varlocal',$array_expr_nom[$key],$_SESSION[$this->c_ssid]['message'][32]);	// ['message'][32]: Etape 3: la variable locale UserInv(19) n'existe pas dans l'etape 19.
							$message = str_replace('$j',($j),$message);
							$message = str_replace('$etap_dest',$value,$message);
							$erreur = true;
							$verification[$key_array]['criticite'] = 2;
							$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($j).'" onclick="editer_etape('.($j).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
							$key_array = $key_array + 1;	
						}	
					}
					else
					{
						
						$message = str_replace('$varlocal',$array_expr_nom[$key],$_SESSION[$this->c_ssid]['message'][33]); 		// ['message'][33]: Veuillez entrer un numero d'étape dans les parenthèses de la variable locale AdrRes(k).
						$message = str_replace('$j',($j),$message);
						$message = str_replace('$etap_dest',$value,$message);
						$erreur = true;
						$verification[$key_array]['criticite'] = 2;
						$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($j).'" onclick="editer_etape('.$j.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
						$key_array = $key_array + 1;
					}		
				}			
			}						
			$j = $j +1;
		}

		if ($erreur)
		{
			$_SESSION[$this->c_ssid]['niveau_informations'] = 2;
		}
		
		if($erreur == '')
		{	
			return '';	
		}
		else
		{
			return $verification;
		}
	}
	
	
	
	/**
	 * On vérifie si aucune des etapes n'est vide et également si un titre est présent en première ligne de l'étape
	 * NR_IKNOW_3_
	 */
	public function check_step_not_null() 
	{
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$j = 1;
		$erreur = false;

		/**==================================================================
		* Parcours des étapes
		====================================================================*/			
		foreach($this->c_Obj_etapes as $array_obj) 
		{
			$first_line = $this->strstrb($array_obj->contenu,chr(10));
			
			if(!$first_line) $first_line = $array_obj->contenu; // Pas de chr(10) trouvé
			
			if(!preg_match_all('<span class="BBTitre">',$first_line,$out))
			{
				if(!preg_match('<span class="BBTitre">',$array_obj->contenu))
				{
					$verification[$key_array]['criticite'] = $niveau_erreur;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',str_replace('$j',$j,$_SESSION[$this->c_ssid]['message'][21]),'editer_etape('.$j.','.$this->c_nbr_etapes.',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');');
					$erreur = true;
					$key_array = $key_array + 1;
				}
				else
				{
					$verification[$key_array]['criticite'] = $niveau_erreur;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',str_replace('$j',$j,$_SESSION[$this->c_ssid]['message'][378]),'editer_etape('.$j.','.$this->c_nbr_etapes.',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');');
					$erreur = true;
					$key_array = $key_array + 1;	
				}

			}
			else
			{
				// On vérifie si il n'y a qu'un titre
				preg_match_all('<span class="BBTitre">',$array_obj->contenu,$out);
				
				if(count($out[0]) > 1)
				{
					$verification[$key_array]['criticite'] = $niveau_erreur;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',str_replace('$j',$j,$_SESSION[$this->c_ssid]['message'][396]),'editer_etape('.$j.','.$this->c_nbr_etapes.',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');');
					$erreur = true;
					$key_array = $key_array + 1;
				}
			}
			$j = $j +1;
		}
		/*===================================================================*/
		
		if($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}
		}
	}
	
	/**
	 * Cette méthode vérifie si les liens vers des étapes pointent sur des étapes existantes
	 */
	public function check_link_step()
	{
		
		$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
		$verification = array();
		$key_array = 0;
		$erreur = false;
			
		foreach($this->c_Obj_etapes as $key_etape => $value_etape)
		{
       		// On recherche les liens vers des étapes dans l'étape $value	
			preg_match_all($this->c_motif_expr_reg['num_etape'],$value_etape->contenu,$out);			
       		      		
       		foreach ($out[1] as $key => $value) 
       		{
       			if($value > $this->c_nbr_etapes || $value < 1)
       			{
       				// L'étape n'existe pas	
       				
       				$libelle = str_replace('$j',$value_etape->numero,$_SESSION[$this->c_ssid]['message'][100]);
       				$libelle = str_replace('$k',$value,$libelle);
       				$verification[$key_array]['criticite'] = $niveau_erreur;
					$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($key_etape).'" onclick="editer_etape('.($key_etape + 1).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$libelle.'</td></tr>';
					$key_array = $key_array + 1;
					$erreur = true;      				
       			}
			}
		}

		if ($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = $niveau_erreur;
			}
			return $verification;
		}
		else
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < $niveau_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}		
		}		
	}
	
	
	
	/**==================================================================
	 * Check out that varin / varout are used
	 ====================================================================*/
	public function check_var_step($prerequis)
	{
		$verification = array();
		$key_array = 0;		
		$warning = false;
		$erreur = false;
		
		//==================================================================
		// Double varout check 
		//	1. varout defined in databse is in use
		//	2. varout found in description is defined in database 
		//==================================================================
		foreach($this->c_Obj_etapes as $array_obj)
		{
			// Récupération des variables de sortie de l'étape
			$query = '	SELECT
							`nom` 
						FROM
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
						WHERE 1 = 1
							AND `id_fiche` = '.$this->c_id_temp.' 
							AND `type` = "OUT" 
							AND `id_action` = '.$array_obj->numero.' 
							AND `id_action_src` = 0
				   '; 

			$result = $this->exec_sql($query,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			//==================================================================
			// Load each step's varout in $valeurs_bdd array
			//==================================================================
			$k = 0;
			$valeurs_bdd = null;
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				$valeurs_bdd[$k] = htmlentities($row['nom'],ENT_QUOTES,'UTF-8');
				$k = $k + 1;
			}
			//==================================================================
			
			// Proceed regular expression to find all varout defined in step description
			preg_match_all($this->c_motif_expr_reg['BBVarOut'],$array_obj->contenu,$out);
			// Remove duplicate entry on several use of same varout
			$array_expr_varout = array_values(array_unique($out[1]));

			//==================================================================
			// 1. Check if varout defined in database is used once in description
			//==================================================================
			if($valeurs_bdd != null)
			{
				// Browse each varout defined in this current step
				foreach($valeurs_bdd as $array_bdd)
				{
					// Flag de recherche de la variable
					$trouve = false;
					
					//==================================================================
					// Check out if varout is in use in body of current step
					//==================================================================
					foreach($array_expr_varout as $array_etape) 
					{	
						if($array_bdd == trim($array_etape))
						{
							// Ok varout found in step body
							$trouve = true;
							break;
						}	
					}	
					//==================================================================
					
					if(!$trouve)
					{
						// Varout not used in step description

						//==================================================================
						// Check if varout is used in a link call of current step (SRX)
						// Check with regular expression to find varout in link
						//==================================================================
						preg_match_all($this->c_motif_expr_reg['lien'],$array_obj->contenu,$out);
						
						foreach ($out[1] as $lien)
						{
							// Check into each link
							if(strstr(htmlentities(urldecode($lien),ENT_QUOTES,'UTF-8'),'$'.$array_bdd.'$',rawurlencode('()')))
							{
								$trouve = true;	
								break;
							}	
						}	
						//==================================================================
						
						if(!$trouve)
						{
							// Varout is not in use at all in this step : Add a waring message
							$message = str_replace('$array_bdd',$array_bdd,$_SESSION[$this->c_ssid]['message'][23]);
							$message = str_replace('$j',$array_obj->numero,$message);
							$warning = true;
							$verification[$key_array]['criticite'] = 1;
							$verification[$key_array]['message'] = $verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',$message,'editer_etape('.$array_obj->numero.');a_tabbar.setTabActive(\'tab-level2\');',$array_obj->numero);
							$key_array = $key_array + 1; 
						}
					}	
				}
			}
			//==================================================================
						
			//==================================================================
			// 2. Check if varout found in step description is defined in databse
			//==================================================================
			if($valeurs_bdd == null )
			{
				$valeurs_bdd[0] = '';	
			}
			
			// Parcours des variables de sortie du corps de l'étape
			foreach ($array_expr_varout as $array_etape)
			{
				// Flag de recherche de la variable
				$trouve = false;
				
				// Parcours des variables de sortie de la base de données
				foreach ($valeurs_bdd as $array_bdd)
				{	
					if($array_bdd == trim($array_etape))
					{
						$trouve = true;	
						break;
					}
				}

				
				if ($trouve == false)
				{
					// Varout used in description but not define in database : Error message
					$message = str_replace('$array_expr',$array_etape,$_SESSION[$this->c_ssid]['message'][26]);
					$message = str_replace('$j',$array_obj->numero,$message);
					
					$erreur = true;
					
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.$array_obj->numero.');a_tabbar.setTabActive(\'tab-level2\');',$array_obj->numero);					
					
					$key_array = $key_array + 1;
				}	
				
			}
			//==================================================================
						
		}	
		//==================================================================
				
		
		
		/**==================================================================
		* 							CONTROLE DES VARIN
		====================================================================*/				

		// Récupération des VARIN de la fiche
		$str = 'SELECT nom 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE id_fiche = '.$this->c_id_temp.' 
				AND type="IN"';
		
		$requete_varin = $this->exec_sql($str,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);	

		
		/**==================================================================
		* 	 On vérifie que les varin ne comportent pas de nom interdit
		====================================================================*/	
		/////////////////// noms interdits ////////////////////////	
		$valeur_interdite = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
		

		//////////////// caracteres interdits //////////////////////		
		$caractere_interdit = explode('|',$_SESSION[$this->c_ssid]['configuration'][14]);
		
		$k = 0;
		$valeurs_bdd = null;
		while ($row = mysql_fetch_array($requete_varin,MYSQL_ASSOC))
		{
			// On récupère les variables pour le contrôle de l'utilisation des varin (voir contrôle ci-après)
			$valeurs_bdd[$k] = htmlentities($row['nom'],ENT_QUOTES,'UTF-8');
			
			
			// Parcours des valeurs interdites
			foreach ($valeur_interdite as $value_valeur_interdite) 
			{
				// On vérifie si le nom de la variable comporte une valeur interdite (insensible à la casse)
				if(strcasecmp($value_valeur_interdite,$row['nom']) == 0)
				{	// Le nom de la variable est interdit
					
					$message = str_replace('&1','<span class="BBVarIn">'.$row['nom'].'</span>',$_SESSION[$this->c_ssid]['message']['iknow'][385]);			
					$erreur = true;
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($j+1).'" onclick="editer_etape('.($j+1).');a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_3\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
					$key_array = $key_array + 1;			
				}
				
			}
			
			// Parcours des caractères interdits
			foreach ($caractere_interdit as $value)
			{
				// On vérifie si le nom de la variable comporte un caractère interdit (insensible à la casse)
				if(strstr($row['nom'],$value) != false)
				{	// Le nom de la variable comporte un caractère interdit
					
					$message = str_replace('$nom','<span class="BBVarIn">'.$row['nom'].'</span>',$_SESSION[$this->c_ssid]['message']['iknow'][24]);
					$message = str_replace('$car',$value,$message);		
					$erreur = true;
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($j+1).'" onclick="editer_etape('.($j+1).');a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_3\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
					$key_array = $key_array + 1; 
				}	
			}
			$k = $k + 1;	
		}		
		/*===================================================================*/
		
		
		/**==================================================================
		* 	 On verifie que les variables d'entrée utilisées dans les étapes existent bien dans la base de données.
		====================================================================*/		
		foreach ($this->c_Obj_etapes as $array_obj)
		{
			// On execute l'expression regulière pour chercher les variables.
			preg_match_all($this->c_motif_expr_reg['BBVarIn'],$array_obj->contenu,$out);
			
			// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
			$array_expr_varin = array_values(array_unique($out[1]));
			
			// On verifie que toute les variables de l'etape soit dans la base de données
			foreach ($array_expr_varin as $array_etape) 
			{
				// On supprime les espaces au début et à la fin du nom de la variable
				$array_etape = trim($array_etape);
				
				// Flag de recherche de la variable
				$trouve = false;
				
				if($valeurs_bdd == null)
				{	// Il n'y a pas de varin dans la base de données
						
					$trouve = false;
				}
				else
				{	// Parcours des Varin de la base de données	
					foreach ($valeurs_bdd as $array_bdd)
					{
						if($array_etape != '')
						{
							if($array_bdd == $array_etape)
							{	// Variable trouvée
								$trouve = true;
								break;
							}
						}
						else
						{
							// Variable trouvée
							$trouve = true;
							break;
						}
					}	
				}
				
				if ($trouve == false)
				{	// Variable non trouvée
					$message = str_replace('$array_expr',$array_etape,$_SESSION[$this->c_ssid]['message'][28]);
					$message = str_replace('$j',$array_obj->numero,$message);			
					$erreur = true;
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.$array_obj->numero.');a_tabbar.setTabActive(\'tab-level2\');',$array_obj->numero);
					$key_array = $key_array + 1;
				}
			}
		}	
		/*===================================================================*/
		
		/**==================================================================
		* On verifie que les variables d'entrée utilisées dans les prérequis existent bien dans la base de données.
		====================================================================*/		

		// On execute l'expression regulière pour chercher les variables.
		preg_match_all($this->c_motif_expr_reg['BBVarIn'],$prerequis,$out);
		
		
		// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
		$array_expr = array_values(array_unique($out[1]));
		
		
		// On vérifie que toute les variables des prerequis soient dans la base de données	
		foreach ($array_expr as $array_etape)
		{
			// Flag de recherche de la variable
			$trouve = false;
			
			if($valeurs_bdd == null)
			{
				
				$trouve = false;
				break;
				
			}
			else
			{
				foreach($valeurs_bdd as $array_bdd)
				{
					if($array_etape != '')
					{
						if($array_bdd == trim($array_etape))
						{
							$trouve = true;	
							break;
						}
					}
					else
					{
						$trouve = true;
						break;
					}
				}	
			}
			
			if ($trouve == false)
			{	// Variable non trouvée
				$message = str_replace('$array_expr',$array_etape,$_SESSION[$this->c_ssid]['message'][29]);
				$erreur = true;
				$verification[$key_array]['criticite'] = 2;
				$verification[$key_array]['message'] = '<tr><td><a href="#" onclick="tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
				$key_array = $key_array + 1;	
				
			}		 
		}
		/*===================================================================*/
		
		
		/**==================================================================
		* On verifie que toute les variables d'entrée de la base de données soient utilisées.
		====================================================================*/			
		if($valeurs_bdd != null)
		{
			foreach($valeurs_bdd as $array_bdd)
			{
				// Flag de recherche de la variable
				$trouve = false;
				
				foreach($this->c_Obj_etapes as $array_obj)
				{
					// On execute l'expression regulière pour chercher les variables.
					preg_match_all($this->c_motif_expr_reg['BBVarIn'],$array_obj->contenu,$out);
					
					// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
					$array_expr_varin = array_values(array_unique($out[1]));
					
					// Recherche de la variable
					foreach($array_expr_varin as $varin_etape)
					{			
						if($array_bdd == trim($varin_etape))
						{  					
							$trouve = true;	
							break;
						}
					}
				}

				
				if(!$trouve)
				{	// Variable non trouvé dans les étapes
				
					// On verifie si dans le prerequis la variable existe
					
					// On execute l'expression regulière pour chercher les variables.
					preg_match_all($this->c_motif_expr_reg['BBVarIn'],$prerequis,$out_prerequis);
					
					// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
					$array_expr_prerequis = array_values(array_unique($out_prerequis[1]));
					
					
					foreach($array_expr_prerequis as $array_description)
					{
						if($array_bdd == trim($array_description))
						{  
							$trouve = true;	
							break;
						}
					}		
				}
				
				
				// On vérifie si une variable n'est pas présente dans une url.
				if(!$trouve)
				{
					foreach($this->c_Obj_etapes as $array_obj) 
					{
						// On execute l'expression regulière pour chercher les variables.
						preg_match_all($this->c_motif_expr_reg['lien'],$array_obj->contenu,$out);
						
						foreach ($out[1] as $lien)
						{
							if(strstr(htmlentities(urldecode($lien),ENT_QUOTES,'UTF-8'),'$'.$array_bdd.'()$',rawurlencode('()')))
							{
								$trouve = true;	
								break;
							}	
						}		
					}	
				}
				
				
				if($trouve == false)
				{
					$message = str_replace('$array_bdd',$array_bdd,$_SESSION[$this->c_ssid]['message'][30]);
					$warning = true;
					$verification[$key_array]['criticite'] = 1;
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',$message,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_3\');');
					$key_array = $key_array + 1;
				}
			}
		}
		/*===================================================================*/
		/*====================== END CONTROLE VARIN =====================*/
		
		
		
				
		/**==================================================================
		* CONTROLE DES VARINEXT
		====================================================================*/		
		foreach($this->c_Obj_etapes as $value_etape) 
		{
			// Recherche des variables de type BBVarInExt
			preg_match_all($this->c_motif_expr_reg['BBVarInExt'],$value_etape->contenu,$out);
			// Parcours des variables trouvées
			foreach ($out[2] as $key => $value)
			{
				// Initialisation du flag de recherche
				$trouve = false;

				// Contruction de la variable
				$var = $out[1][$key].'('.$value.$out[3][$key].')';
				
				if(is_array($this->c_variables_fiche))
				{
					foreach($this->c_variables_fiche as $key_variables_fiche => $value_variables_fiche)
					{
						if($value_variables_fiche[2] == 'EXTERNE' && strstr($value_variables_fiche[0],$var))
						{
							$trouve = true;					
						}		
					}
				}
				
				if($trouve == false)
				{
					$message = str_replace('$j',$value_etape->numero,$_SESSION[$this->c_ssid]['message'][98]);
					$message = str_replace('$nom','<span '.$out[0][$key].'<span>',$message);
					$message = str_replace('$etape',$value,$message);
					$erreur = true;
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_etape->numero).'" onclick="editer_etape('.($value_etape->numero).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
					$key_array = $key_array + 1;
				}	

				if($out[4][$key] != '')
				{
					$message = str_replace('$j',$value_etape->numero,$_SESSION[$this->c_ssid]['message'][471]);
					$message = str_replace('$nom','<span '.$out[0][$key].'<span>',$message);
					$erreur = true;
					$verification[$key_array]['criticite'] = 2;
					$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_etape->numero).'" onclick="editer_etape('.($value_etape->numero).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
					$key_array = $key_array + 1;
				}
			}
		}
		/*====================== END CONTROLE VARINEXT =====================*/
		
		/**==================================================================
		* CONTROLE DES VAROUTEXT
		====================================================================*/		
		if(isset($_SESSION[$this->c_ssid]['message'][394]))
		{
			foreach($this->c_Obj_etapes as $value_etape) 
			{
				// Recherche des variables de type BBVarExt
				preg_match_all($this->c_motif_expr_reg['BBVarExt'],$value_etape->contenu,$out);
				
				// Parcours des variables trouvées
				foreach ($out[2] as $key => $value)
				{
					// Initialisation du flag de recherche
					$trouve = false;
	
					// Contruction de la variable
					$var = $out[1][$key].'('.$value_etape->numero.'\\'.$value.'\\'.$out[3][$key].')';
					if(is_array($this->c_variables_fiche))
					{
						foreach($this->c_variables_fiche as $key_variables_fiche => $value_variables_fiche)
						{
							if($value_variables_fiche[2] == 'EXTERNE' && strstr($value_variables_fiche[0],$var))
							{
								$trouve = true;					
							}		
						}
					}
					
					if($trouve == false)
					{
						$message = str_replace('$j',$value_etape->numero,$_SESSION[$this->c_ssid]['message'][394]);
						$message = str_replace('$nom','<span '.$out[0][$key].'<span>',$message);
						$erreur = true;
						$verification[$key_array]['criticite'] = 2;
						$verification[$key_array]['message'] = '<tr><td><a href="#ancre'.($value_etape->numero).'" onclick="editer_etape('.($value_etape->numero).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
						$key_array = $key_array + 1;																																									
					}				
				}
			}
		}
		/*====================== END CONTROLE VARINEXT =====================*/
		
		
		if($erreur)
		{
			if($_SESSION[$this->c_ssid]['niveau_informations'] < 2)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 2;
			}
			return $verification;
		}
		else
		{
			if($warning)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 1;
				return $verification;
			}
			else
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
				return '';
			}
		}
	}
	/*===================================================================*/
	
	
	
	/**
	 * Verification des paramètres obligatoire dans les url des étapes vers des icodes ou des ifiches
	 * @return unknown_type
	 * NR_IKNOW_1_
	 */
	public function check_mandatory_param_url()
	{
		$key_array = 0;
		$verification = array();		
		$erreur = false;
		// Parcours des étapes
		foreach($this->c_Obj_etapes as $value_obj_etapes)
		{
			// Récupération des liens de l'étape
			$param_etape = $this->get_url_etape($value_obj_etapes->numero);

			// Contrôle de l'existance de lien vers un iObjet dans l'étape
			if(isset($param_etape[0]))
			{
				// Parcours des liens de l'étape
				foreach($param_etape as $value_param_etape)
				{
			 		// Contrôle de l'existance d'un ID
					if(isset($value_param_etape['ID']) && $this->existe_id_objet($value_param_etape['ID'],$value_param_etape['123objet456']))
					{
						/**==================================================================
						 * Définition du type d'objet
						 ====================================================================*/		
						if($value_param_etape['123objet456'] == 'ifiche.php')
						{
							$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'];
							$champ_version = 'num_version';
							$champ_id = 'id_fiche';
							$table_objet = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'];
							$table_objet_max = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'];
							$libelle_objet = 'la fiche';
						}
						else
						{
							$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'];
							$champ_version = 'version';
							$champ_id = 'id';
							$table_objet = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'];
							$table_objet_max = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'];
							$libelle_objet = 'le code';
						}
						/*===================================================================*/
						
						/**==================================================================
						 * Récupération de la version
						 ====================================================================*/								
						if(isset($value_param_etape['version']) && $value_param_etape['version'] != $_SESSION[$this->c_ssid]['message']['iknow'][504])
						{
							// Version précisée
							$version = $value_param_etape['version'];
							$libelle_version = 'version '.$version;
						}
						else
						{
							// Last iSheet release
							$sql_version = '(SELECT `'.$champ_version.'` as version  
											FROM `'.$table_objet_max.'` 
											WHERE `'.$champ_id.'` = '.$value_param_etape['ID'].')';
							$resultat = $this->exec_sql($sql_version,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
							$version = mysql_result($resultat,0,'version');						
							$libelle_version = 'version MAX ('.$version.')';
						}
						/*===================================================================*/
						
						// On récupère les varin de la fiche ou du code qui N'ONT PAS DE VALEURS PAR DEFAUT.
						$sql = 'SELECT `Nom` 
								FROM `'.$table.'` 
								WHERE `'.$champ_id.'` = '.$value_param_etape['ID'].' 
								AND `'.$champ_version.'` = '.$version.' 
								AND IFNULL(length(`DEFAUT`),0) = 0
								AND IFNULL(length(`NEUTRE`),0) = 0
								AND `TYPE` = "IN"';
				
						$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
						while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
						{
							// On va vérifier chaques parametres
							$trouve = false;
		
							foreach($value_param_etape as $key => $value)
							{
								if($row['Nom'] == urldecode($key))
								{
									$trouve = true;
								}
							}
							if(!$trouve)
							{
								$message = str_replace('$j',$value_obj_etapes->numero,$_SESSION[$this->c_ssid]['message'][115]);
								$message = str_replace('$objet',$libelle_objet,$message);
								$message = str_replace('$id',$value_param_etape['ID'],$message);
								$message = str_replace('$version',$libelle_version,$message);
								$message = str_replace('$param','<span class="BBVarin">'.$row['Nom'].'</span>',$message);
								$verification[$key_array]['criticite'] = 2;
								$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.$value_obj_etapes->numero.');a_tabbar.setTabActive(\'tab-level2\');');
								$key_array = $key_array + 1;	
								$erreur = true;				
							}
						}
					}								
				}
			}	
		}	
		
		if($erreur)
		{	
			if($_SESSION[$this->c_ssid]['niveau_informations'] < 2)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 2;
			}		
		}
		return $verification;
	}	
	
	
	/**
	 * Cette methode verifie que aucune étape n'a un lien ou des variables vers l'etape que l'on va supprimer
	 * Elle genere un message d'erreur dans la barre d'informations si des liens sont présents.
	 */
	public function verif_del_step($p_etape_a_supprimer)
	{
		$erreur = false;
		$message_final = '<table id="informations">';
		
		foreach ( $this->c_Obj_etapes as $value_etape ) 
		{
			/**==================================================================
			* VERIFICATION DES VARIABLES POINTANT SUR CETTE ETAPE
			====================================================================*/	
			$verif_utilisation_variable_contenu_etape = $this->verif_utilisation_variable_contenu_etape($p_etape_a_supprimer,$value_etape->numero);
				
			if($verif_utilisation_variable_contenu_etape != false)
			{
				// L'étape $value_etape->numero utilise des variable de sortie de l'étape que l'on souhaite supprimer
				$message_final .= $verif_utilisation_variable_contenu_etape;
				$erreur = true;
			}
			
			$verif_utilisation_variable_lien_etape = $this->verif_utilisation_variable_lien_etape($p_etape_a_supprimer,$value_etape->numero);
			
			if($verif_utilisation_variable_lien_etape != false)
			{
				// L'étape $value_etape->numero utilise des variable de l'étape que l'on souhaite supprimer dans des liens
				$message_final .= $verif_utilisation_variable_lien_etape;
				$erreur = true;				
			}
			/*===================================================================*/			
		
				
			//On cherche les liens vers les etapes
			$motif = '#<a href="\#('.$p_etape_a_supprimer.')">#i';
			
			// On execute l'expression regulière pour chercher les variables.
			preg_match_all($motif,$value_etape->contenu,$out);
			
			
			// On stocke le resultat de l'expression regulière dans un tableau en supprimant les doublons.
			$array_expr = array_values(array_unique($out[1]));
			
			sort($out[1]);
			
			
			foreach ( $out[1] as $value ) 
			{
				if($value == $p_etape_a_supprimer)
				{
					$message = str_replace('$k',$value_etape->numero,$_SESSION[$this->c_ssid]['message'][35]);
					$message = str_replace('$j',$p_etape_a_supprimer,$message);
					$message_final .= '<tr><td><a href="#ancre'.($value_etape->numero).'" onclick="editer_etape('.($value_etape->numero).');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message.'</td></tr>';
					$erreur = true;
				}
			}	
		}
		
		if(!$erreur)
		{
			// XML return
			header("Content-type: text/xml");
			echo "<?xml version='1.0' encoding='UTF8'?>";
			echo "<parent>";
			echo "<erreur>false</erreur>";
			echo "</parent>";	
		}
		else
		{
			// XML return	
			header("Content-type: text/xml");
			echo "<?xml version='1.0' encoding='UTF8'?>";
			echo "<parent>";
			echo "<erreur>true</erreur>";
			echo "<message_controle>".$this->protect_xml($message_final)."</message_controle>";
			echo "<titre_controle>".$this->protect_xml('<table><tr><td><a href="#" class="erreur"></a></td><td>'.str_replace('$id_etape',$p_etape_a_supprimer,$_SESSION[$this->c_ssid]['message'][178]).'</td></tr></table>')."</titre_controle>";
			echo "</parent>";
			die();
		}		
	}
	
	
	/**
	 * Vérifie si l'étape $id_etape_a_verifier utilise des variable de l'étape $p_etape_a_supprimer, retourne la liste des messages d'erreur si elle en utilise sinon false
	 * @param decimal $p_etape_a_supprimer
	 * @param decimal $id_etape_a_verifier
	 * @return booleen
	 */
	private function verif_utilisation_variable_contenu_etape($p_etape_a_supprimer,$id_etape_a_verifier)
	{
		if($id_etape_a_verifier != $p_etape_a_supprimer)
		{	
			
			// On récupère les variables de sortie de l'étape à supprimer
			$sql = 'SELECT nom,`type`,id_src,id_action_src 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND (`type` = "OUT" 
					OR `type` = "EXTERNE") 
					AND id_action = '.$p_etape_a_supprimer;
			
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$array_varout_etape = array();
			$i = 0;
			$message = '';
			
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				if($row['type'] == 'OUT')
				{
					if(strstr($this->c_Obj_etapes[$id_etape_a_verifier - 1]->contenu,'<span class="BBVarInl">'.$row['nom'].'('.$p_etape_a_supprimer.')</span>'))
					{
						// Variable de type VarInLocale trouvée dans le corps
						$message_base = str_replace('$k',$id_etape_a_verifier,$_SESSION[$this->c_ssid]['message'][105]);
						$message_base = str_replace('$var','<span class="BBVarInl">'.$row['nom'].'</span>',$message_base);
						$message .= $this->generer_ligne_erreur('erreur',$message_base,'editer_etape('.$id_etape_a_verifier.');a_tabbar.setTabActive(\'tab-level2\');',$id_etape_a_verifier);
					}
				}
				else
				{
					if(strstr($this->c_Obj_etapes[$id_etape_a_verifier - 1]->contenu,'<span class="BBVarinExt">'.$row['nom'].'('.$p_etape_a_supprimer.'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>'))
					{
						// Variable de type VarInExterne trouvée dans le corps
						$message_base = str_replace('$k',$id_etape_a_verifier,$_SESSION[$this->c_ssid]['message'][143]);
						$message_base = str_replace('$var','<span class="BBVarinExt">'.$row['nom'].'('.$p_etape_a_supprimer.'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>',$message_base);
						
						$message .= '<tr><td><a href="#'.$id_etape_a_verifier.'" onclick="editer_etape('.$id_etape_a_verifier.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message_base.'</td></tr>';
					}
				}
			}		
			
			if($message == '')
			{
				return false;
			}
			else
			{
				return $message;
			}
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Vérifie si l'étape $id_etape_a_verifier utilise des variable de l'étape $p_etape_a_supprimer dans des liens, retourne la liste des messages d'erreur si elle en utilise sinon false
	 * @param decimal $p_etape_a_supprimer
	 * @param decimal $id_etape_a_verifier
	 * @return booleen
	 */
	private function verif_utilisation_variable_lien_etape($p_etape_a_supprimer,$id_etape_a_verifier)
	{
		if($id_etape_a_verifier != $p_etape_a_supprimer)
		{
			// On récupère les variables de sortie de l'étape à supprimer
			$sql = 'SELECT nom,`type`,id_src,id_action_src 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
					WHERE id_fiche = '.$this->c_id_temp.' 
					AND `type` <> "IN" 
					AND id_action = '.$p_etape_a_supprimer;
			
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$array_varout_etape = array();
			$i = 0;
			$message = '';
	
			while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				if($row['type'] == 'OUT')
				{
					
					if(strstr($this->c_Obj_etapes[$id_etape_a_verifier - 1]->contenu,$row['nom'].'%28'.$p_etape_a_supprimer.'%29'))
					{
						// Variable de type VarInLocale trouvée dans le corps
						$message_base = str_replace('$k',$id_etape_a_verifier,$_SESSION[$this->c_ssid]['message'][155]);
						$message_base = str_replace('$var','<span class="BBVarInl">'.$row['nom'].'</span>',$message_base);
						$message .= '<tr><td><a href="#'.$id_etape_a_verifier.'" onclick="editer_etape('.$id_etape_a_verifier.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message_base.'</td></tr>';
					}
				}
				else
				{
					if(strstr($this->c_Obj_etapes[$id_etape_a_verifier - 1]->contenu,$row['nom'].'%28'.$p_etape_a_supprimer.'%5C'.$row['id_src'].'%5C'.$row['id_action_src'].'%29'))
					{
						// Variable de type VarInExterne trouvée dans le corps
						$message_base = str_replace('$k',$id_etape_a_verifier,$_SESSION[$this->c_ssid]['message'][156]);
						$message_base = str_replace('$var','<span class="BBVarinExt">'.$row['nom'].'('.$p_etape_a_supprimer.'\\'.$row['id_src'].'\\'.$row['id_action_src'].')</span>',$message_base);
						$message .= '<tr><td><a href="#'.$id_etape_a_verifier.'" onclick="editer_etape('.$id_etape_a_verifier.');a_tabbar.setTabActive(\'tab-level2\');" class="erreur"></a></td><td>&nbsp;'.$message_base.'</td></tr>';
					}
				}
			}		
			
			if($message == '')
			{
				return false;
			}
			else
			{
				return $message;
			}
		}
		else
		{
			return false;
		}
	}
	
		
	//------------------------------------------------------ END CONTROLES ---------------------------------------------------
		
	//------------------------------------------------------------------------------------------------------------------------
	//-------------------------------------------------------- ACCESSEURS ----------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------
	
	/**==================================================================
	 * get_nbr_etapes
	 * Add shortcut function to get numbers of step in current iSheet
	 ====================================================================*/
	public function get_nbr_etapes()
	{
		return $this->c_nbr_etapes;
	}
	/*===================================================================*/    
	
	public function get_requete_var_externe()
	{
		return $this->c_requete_var_externe;
	}	
	
	private function get_niveau_password($id)
	{
		$sql = 'SELECT
					`level` 
				FROM
					`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_password']['name'].'`
				WHERE 1 = 1
					AND `id` = '.mysql_escape_string($id);

		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		if(mysql_num_rows($resultat) > 0)
		{
			$niveau = mysql_result($resultat,0,'level');
			$niveau =  $_SESSION[$this->c_ssid]['message'][224].' '.$niveau;
			
			return $niveau;
		}
		else
		{
			return false;
		}
		
		mysql_close($link);
	}
	
	
		
	public function init_vimofy_cartouche_param($id_cartouche,$id_etape)
	{
		return $this->c_requete_vimofy_cartouche[$id_etape - 1][$id_cartouche]["in"];
	}
	
	public function init_vimofy_cartouche_infos($id_cartouche,$id_etape)
	{
		return $this->c_requete_vimofy_cartouche[$id_etape - 1][$id_cartouche]["out"];
	}
		
	
	//------------------------------------------------------ END ACCESSEURS --------------------------------------------------
	
    private function in_arrayi($needle, $haystack) 
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}
?>