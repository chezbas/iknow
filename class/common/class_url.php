 <?php
class class_url extends class_bdd
{
	//==================================================================
	// Attributs definition
	//==================================================================
	private $id_appelant;			// Identifiant de la fiche appelante
	private $url_appelant;			// Url de l'objet appelant (basé sur $_SERVER['HTTP_REFERER'])
	private $ssid_appelant;			// Identifiant de session de la fiche appelante
	private $ik_valmod_appelant;	// ik_valmod de la fiche appelante
	private $var_fiche_appelante;	// Paramètres de la fiche appelante avec leurs valeurs
	private $type_soft_objet;		// Type d'application de l'objet: 1 - iFiche modif, 2 - iFiche visu, 3 - iCode modif, 4 - iCode visu 
	private $version_objet;			// Version de l'objet	
	private $tab_level_objet;		// Tab level de l'objet
	private $c_ssid;
	//==================================================================
	
	
	/**
	 * Class constructor
	 * @param string $p_url_appelant Url de l'objet appelant (basé sur $_SERVER['HTTP_REFERER'])
	 * @param decimal $p_type_soft_objet Type d'application : 1 - iFiche modif, 2 - iFiche visu, 3 - iCode modif, 4 - iCode visu
	 */
	public function __construct($p_url_appelant,$p_type_soft_objet,$p_ssid)
	{
		$this->db_connexion();
		$this->url_appelant = $p_url_appelant;
		$this->type_soft_objet = $p_type_soft_objet;
		$this->c_ssid = $p_ssid;
	}

	/**
	 * Called when the object is deserialized
	 */
	public function __wakeup()
	{
		// Reconnect to databases
		$this->db_connexion();	
	}		
	
	/**
	 * Retourne l'url valorisée
	 */
	public function get_url()
	{
		// Récupération de l'id temporaire de la fiche appelante
		$this->id_appelant = $this->get_id_appelant();

		// Récupération de l'ik_valmod de la fiche appelante
		$this->ik_valmod_appelant = $this->get_ik_valmod_appelant();
		
		// Récupération de la version d'appel de l'objet
		$this->version_objet = $this->get_version_objet();
		
		// Récupération du tab-level de l'objet
		$this->tab_level_objet = $this->get_tab_level_objet();
		
		// Récupération des variables de la fiche appelante
		$this->get_var_appelant();
		
		// Génération de l'URL
		return $this->generer_url();
	}
	
	
	/**
	 * Retourne le ssid contenu dans l'url de la fiche appelante
	 */
	private function get_ssid_appelant()
	{
		$motif = '#ifiche\.php\?&?.*ssid=([^&]+)&?#';
		preg_match_all($motif,$this->url_appelant,$out);
		
		return $out[1][0];	// Retourne le ssid
	}
	
	
	/**
	 * Retourne la version d'appel de l'objet, si il n'y en a pas retourne null
	 */
	private function get_version_objet()
	{
		if(isset($_GET['version']))
			return $_GET['version'];
		else
			return null;
	}
	
	
	/**
	 * Retourne le tab-level de l'objet, si il n'y en a pas retourne null
	 */
	private function get_tab_level_objet()
	{
		if(isset($_GET['tab-level']))
			return $_GET['tab-level'];
		else
			return null;
	}	
	
	
	/**
	 * Retourne la valeur de ik_valmod de la fiche appelante
	 */
	private function get_ik_valmod_appelant()
	{
		$motif = '#ifiche\.php\?&?.*IK_VALMOD=([^&]+)&?#';
		preg_match_all($motif,$this->url_appelant,$out);
		
		
		// Retourne la valeur de IK_VALMOD
		if(isset($out[1][0]))
			return $out[1][0];	// IK_VALMOD défini dans l'URL
		else
			return 3;			// Pas de IK_VALMOD présent dans l'URL
	}
	
	
	/**==================================================================
	 * Recover temporary id session of iSheet that did the call ( parent iSheet )
	====================================================================*/
	private function get_id_appelant()
	{
		//==================================================================
		// Recover ssid of iSheet that did the call
		//==================================================================
		$ssid_appelant = $this->get_ssid_appelant();
		//==================================================================
		
		$sql = '	SELECT
						id_temp 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name'].'` 
					WHERE ssid = "'.$ssid_appelant.'"';
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$id = mysql_result($resultat,0,'id_temp') or die($sql);
		
		return $id;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Recover each varin from iSheet that did the call
	 * SIBY_VARIN_LINK_CALL_001
	====================================================================*/
		private function get_var_appelant()
		{
			$this->var_fiche_appelante = array();
			
			$sql = 'SELECT
						`nom` AS "nom",
						`resultat` AS "resultat",
						(IF
								(`resultat` is null
								,"X"
								,"")
						) AS "resultatnull", -- Manage specific value null
						`type` AS "type",
						`id_action` AS "id_action",
						`id_src` AS "id_src",
						`id_action_src` AS "id_action_src",
						`defaut` AS "defaut",
						`neutre` AS "neutre" 
					FROM
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
					WHERE 1 = 1
						AND `id_fiche` = '.$this->id_appelant;
	
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				switch($row['type'])
				{
					case 'IN':
						$this->var_fiche_appelante[$row['nom'].'()'] = $this->get_valorisation($row['resultat'],$row['defaut'],$row['neutre'],$row['resultatnull']);
						break;
					case 'OUT':
						$this->var_fiche_appelante[$row['nom'].'('.$row['id_action'].')'] = $this->get_valorisation($row['resultat'],$row['defaut'],$row['neutre']);
						break;
					case 'EXTERNE':
						$this->var_fiche_appelante[$row['nom'].'('.$row['id_action'].'\\'.$row['id_src'].'\\'.$row['id_action_src'].')'] = $this->get_valorisation($row['resultat'],$row['defaut'],$row['neutre']);
						break;
				}
			}
		}
	/*===================================================================*/
	

	/**==================================================================
	 * Return variable valorisation depend on ik_valmod mode ( SIBY_VARIN_LINK_CALL_001 )
	 * @param string $valeur : resultat field
	 * @param string $defaut : defaut field
	 * @param string $neutre : neutre field
	 * @param string $valeur_null = X if $valeur is null
	====================================================================*/
	private function get_valorisation($valeur,$defaut,$neutre,$valeur_null=null)
	{
		switch ($this->ik_valmod_appelant) 
		{
			case 1:	// DEFAUT
				if($valeur == '' && $valeur_null <> 'X' && $defaut != '')
					return $defaut;
				else
					return $valeur;
				break;
			case 2:	// NEUTRE
				if($valeur == '' && $valeur_null <> 'X' && $neutre != '')
					return $neutre;
				else
					return $valeur;
				break;
			case 3:	// DEFAUT & NEUTRE
				if($valeur == '' && $valeur_null <> 'X' && $neutre != '')
					return $neutre;
				else
				{
					if($valeur == '' && $valeur_null <> 'X' && $defaut != '')
						return $defaut;
					else
						return $valeur;
				}	
				break;
			default:
				// No IK_VALMOD define, just return resultat field
				return $valeur;
				break;	
		}
	}
	/*===================================================================*/


	/**
	 * Retourne le nom de la page pour l'objet appelé
	 */
	private function get_page()
	{
		if($this->type_soft_objet == __FICHE_VISU__)
			return "ifiche.php";
		else
			return "icode.php";
	}
	
	
	/**
	 * Génère l'URL de l'objet avec la valorisation des variables
	 */
	private function generer_url()
	{
		/**==================================================================
		 * Remplacement des variables dans les liens
		 ====================================================================*/		
		$sql = "SELECT 
					DISTINCT
						`NOM`, 
						`TYPE`,
						`id_action`,
						`resultat`,
						`DEFAUT`,
						`NEUTRE`,
						(
							SELECT (
									CASE `id_action` 
								 	WHEN '0'
								 	THEN CONCAT(nom,'()') 
			                     	ELSE (	SELECT
			                     				CASE `id_src` 
						                      	WHEN 0
						                      	THEN CONCAT(`nom`,'(',`id_action`,')') 
						                      	ELSE CONCAT(`nom`,'(',`id_action`,'\\\\',`id_src`,'\\\\',`id_action_src`,')')
						                      	END
						            	 )
			                     	END
			                     	)
			            ) as type_externe1,
						IF(
							`TYPE` = 'EXTERNE',
							CONCAT(`nom`,'(',`id_src`,'\\\\',`id_action_src`,')'),''
						  ) as type_out
				FROM
					`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."` 
				WHERE 1 = 1
					AND `id_fiche` = ".$this->id_appelant."
					AND ((`used` = 1 AND  `TYPE` =  'EXTERNE') OR (`TYPE` <>  'EXTERNE'))
				ORDER BY LENGTH(  `NOM` ) DESC , `NOM` ASC";
		
		$resultat_replace_var = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		/*===================================================================*/
	
		$param_url = '';

		// Parcours des variables de l'url
		foreach($_GET as $get_key => $get_val)
		{
			if($get_key != 'IK_CARTRIDGE')
			{
				// Initialisation des flags de recherche
				$trouve_var = false;
				$valeur_var_vide = false;
				
				// Exclusion des variable ID et version de la recherche
				if($get_key != 'ID' && $get_key != 'version')
				{
					// Parcours des variables de la fiche appelante
					foreach($this->var_fiche_appelante as $var_key => $var_val)
					{
						if(strstr($get_val,'$'.$var_key.'$'))
						{
							//echo 'CORRESPONDANCE TROUVEE ENTRE '.$get_val.'   ET  $'.$var_key.'$   <br />';
							// Correspondance entre une variable de l'url et une variable de la fiche appelante trouvée
							$trouve_var = true;
							
							if($var_val == '')
							{
								// Valeur de la variable non renseignée
								$valeur_var_vide = true;
								break;
							}
						}
					}
				}
				
				if($valeur_var_vide != true)
				{
					// Tout les paramètres sont renseignés, valorisation des variables
					//error_log($get_key);
					$param_url .= $get_key.'='.$this->get_type_valorisation($get_val,$resultat_replace_var).'&';
				}
				else
				{
					//$param_url .= $get_key.'=%26'.$get_key.'&';
					$param_url .= $get_key.'=&';
				}
			}	
		}
		// Nom de la page
		return $this->get_page().'?'.$param_url;
	}
	
	
	/**
	 * Retourne la variable avec le bon type de valorisation
	 * @param string $valorisation Valeur du paramètre dans le lien
	 */
	private function get_type_valorisation(&$valorisation,&$resultat)
	{
		// On décode la variable pour ne plus qu'elle est le format URL encodé.
		//$valorisation = urldecode($valorisation);
		
		/**==================================================================
		 * RECHERCHE DU TYPE DE VALORISATION 
		 ====================================================================*/		
		if(mysql_num_rows($resultat) > 0)mysql_data_seek($resultat,0);
		
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
		{
			if($row['TYPE'] == 'EXTERNE')
			{
				/*if($this->id_etape != $row['id_action'])
				{
					*/
					$valorisation = str_replace('$'.$row['type_externe1'].'$',$this->get_valorisation($row['resultat'],$row['DEFAUT'],$row['NEUTRE'],$row['type_externe1'],true),$valorisation);
				/*}	
				else
				{
					$valorisation = str_replace('$'.$row['type_out'].'$',$this->get_valorisation($row['resultat'],$row['DEFAUT'],$row['NEUTRE'],$row['type_out'],true),$valorisation);
				}*/
			}
			else
			{
				if ($row['TYPE'] == 'OUT')
				{
					$valorisation = str_replace('$'.$row['NOM'].'$','',$valorisation);
				}
				else
				{
					$valorisation = str_replace('$'.$row['type_externe1'].'$',$this->get_valorisation($row['resultat'],$row['DEFAUT'],$row['NEUTRE'],$row['type_externe1'],true),$valorisation);
				}
			}
		}
		/*===================================================================*/	
		
		return rawurlencode($valorisation);
	}
}
?>