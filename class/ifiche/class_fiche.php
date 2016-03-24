<?php
/**==========================================================================================================
 * 									Main class for iSheet
 ==========================================================================================================*/
class fiche extends class_bdd 
{
	//==================================================================
	// Setup class private attribut
	//==================================================================
	private $c_Obj_etape;						// Instance de la classe etape de la fiche en cours.
	private $c_Obj_entete;						// Instance de l'entete de la fiche en cours.
	private $c_Obj_verification;				// Instance de la classe verification de la fiche en cours.
	private $c_Obj_lock;						// Instance de la classe lock
	private $c_id;								// Contient l'id de la fiche en cours.
	private $c_ssid;							// Contient le ssid de la fiche en cours.
	private $c_user;							// Contient l'adresse ip de l'utilisateur qui modifie la fiche.
	private $c_type;							// Kind of mode :	1 means updating mode
												//					2 means display mode
	private $c_version_app;						// Contient le lien relatif vers le dossier includes
	private $c_version;							// Contient la version de la fiche en cours.
	private $c_id_temp;							// Contient l'id temporaire de la fiche en cours (<=99999)
	private $c_language;						// Contient la langue du système
	private $c_titre;							// Contient le titre de la fiche
	private $c_tab_actif_haut;					// Flag de l'onglet actif
	private $c_tab_actif_bas;					// Flag de l'onglet actif
	private $c_tab_actif_etapes;				// Flag de l'onglet actif
	private $c_tab_etapes_sep;					// Flag de l'onglet actif
	private $c_obsolete;						// lock update flag :	0 means free for modification
												//						1 means iSheet is locked
	private $c_statut;							// Statut de la fiche (enc onstruction, validée...)
	private $c_ik_valmod;						// Type de valorisation, DEFAUT, NEUTRE, DEFAUT ET NEUTRE, NORMAL		
	private $c_global_coherent_check_end;
	private $c_global_coherent_check_qtt_err;
	private $c_global_coherent_check_ssid;	
	//==================================================================
		
	
	/**==================================================================
	 * Class constructor
	 * @param decimal $p_id  : iSheet ID identifier
	 * @param string $p_ssid : Session identifier
	 * @param string $p_user : User Ip address
	 * @param decimal $p_type : 1 means update mode
	 * 							2 means display mode
	 * @param string $p_version_app : Release of iknow ( eg:3.00 )
	 * @param decimal $p_version : iSheet version
	 * @param string $language : Language to use
	 * @access public
	 ====================================================================*/
	public function __construct($p_id,$p_ssid,$p_user,$p_type,$p_version_app,$p_version,$language) 
	{
		/**==================================================================
		 * Call parent constructor
		 ====================================================================*/
		parent::__construct($p_ssid,$p_id,0,$p_version,$p_type);
		/*===================================================================*/

		
		/**==================================================================
		 * Attribut initialization
		 ====================================================================*/
		$this->db_connexion();
		$this->c_language = $language;
		$this->c_id = $p_id;
		$this->c_ssid = $p_ssid;
		$this->c_user = $p_user;
		$this->c_type = $p_type;
		$this->c_version_app = $p_version_app;
		$_SESSION[$this->c_ssid]['reload'] = false;
		$this->c_global_coherent_check_end = false;
		/*===================================================================*/

		
		/**==================================================================
		 * Check if iSheet exists
		 ====================================================================*/
		if($this->c_id != 'new')
		{
			$this->check_ifiche_exist($p_version);
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * Define version of iSheet
		 ====================================================================*/
		if($p_version != '')
		{
			if($p_version == 'new')
			{
				$this->c_version = 0;	
			}
			else
			{
				$this->c_version = $p_version;
			}
		}
		else
		{
			$this->c_version = $this->get_max_version();
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * Check iSheet is locked for update
		 ====================================================================*/
		$this->is_obsolete();
		
		if($this->c_type == __FICHE_MODIF__ && $p_version != 'new' && $this->c_obsolete == 1)
		{
			// iSheet is locked for update... force display mode
			echo "
					<script language=\"javascript\">
						window.location.replace('./ifiche.php?ID=".$this->c_id."&version=".$this->c_version."');
					</script>
				</head>
				<body>
				</body>
			</html>";	
			die();	
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * What kind of valorisation from url key word IK_VALMOD
		 ====================================================================*/
		if(isset($_GET['IK_VALMOD']))
		{
			$this->set_ik_valmod($_GET['IK_VALMOD']);
		}
		else
		{
			$this->set_ik_valmod(3);
		}
		/*===================================================================*/
			
		
		// Instanciate lock class
		$this->c_Obj_lock = new lock_iobjet($this->c_type,$this->c_id,$this->c_ssid,$this->c_user);
		
		if($this->c_type == 2 && isset($_SESSION[$this->c_ssid]['id_temp']))
		{
			// Display another time the sheet with the same ssid
			$_SESSION[$this->c_ssid]['reload'] = true;
			$this->c_id_temp = $_SESSION[$this->c_ssid]['id_temp'];	
			$this->set_id_temp($this->c_id_temp);
			$this->c_Obj_lock->get_usage_ssid();
		}	
		else
		{
			// Generate ID temp
			$this->c_id_temp = $this->c_Obj_lock->generer_id_temporaire();
			$this->set_id_temp($this->c_id_temp);
			$_SESSION[$this->c_ssid]['id_temp'] = $this->c_id_temp;
		}
		
		// Get sheet content
		$dataset = $this->get_sheet_content();
		
		// Instanciate step class
		$this->c_Obj_etape = new sheet_steps($this->c_id,$this->c_version,$this->c_ssid,$this->c_id_temp,$this->c_version_app,$dataset['date_raw'],$this->c_statut,$this->c_type,$this->c_ik_valmod);
		if($this->c_type == __FICHE_MODIF__)
		{
			// Instanciate checking class
			$this->c_Obj_verification = new check($this->c_id,$this->c_version,$this->c_language,$this->c_ssid,$this->c_id_temp);
		}
		
		// Instanciate the header class
		$this->c_Obj_entete = new sheet_header($dataset,$this->c_id,$this->c_version,$this->c_ssid,$this->c_id_temp,$this->c_type,$this->c_version_app,$this->c_language,$this->c_ik_valmod);
		
		$this->c_Obj_entete->copy_var_and_tag();
		$this->c_Obj_etape->init_value_champ_speciaux();

		//Set varin values (from the URL)
		$this->c_Obj_entete->set_varin_values();
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Reconnect to database after deserialization
	 * @access public
	 ====================================================================*/
	public function __wakeup()
	{
		// Database reconnection
		$this->db_connexion();	
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Set kind of valorization to $this->c_ik_valmod
	 * @access public
	 ====================================================================*/
	public function set_ik_valmod($p_value)
	{
		$this->c_ik_valmod = $p_value;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Delete url_temp on temporary id
	 * @access public
	 ====================================================================*/
	public function clear_url_temp()
	{
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."`
				WHERE `id_temp`  = ".$this->c_id_temp;
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Delete input valorization only in display mode
	 * @access public
	 ====================================================================*/
	public function delete_value_param()
	{
		$this->set_ik_valmod_global(0);
		$sql = "UPDATE
					`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."`
				SET
					`resultat` = ''
				WHERE 1 = 1
				AND `ID_fiche` = ".$this->c_id_temp;
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Whole iSheet control method ( title, parameters and so on... )
	 * @return HTML : Return HTML information headband
	 * @param string $titre : iSheet title
	 * @param string $description : Head description
	 * @access public
	 ====================================================================*/
	public function sheet_control($titre = null,$trigramme = null,$pole = null,$version = null,$activite = null,$niveau = null,$xml = false)
	{
		$verification_lancement = false;
		$verification_generale = array();
		$eval_js = '';

		if(func_num_args() == 0)
		{
			$verification_lancement = true;
			$xml = true; // iSheet start check
		}
		
		//==================================================================
		// Delete var and tags which have not step
		//==================================================================
		$sql = '	DELETE FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'` 
					WHERE 1 = 1
						AND `ID` = '.$this->c_id_temp.' 
						AND `Etape` > '.$this->c_Obj_etape->get_nbr_etapes().';
			   ';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		
		$sql = '	DELETE FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
					WHERE 1 = 1 
						AND `id_fiche` = '.$this->c_id_temp.' 
						AND `id_action` > '.$this->c_Obj_etape->get_nbr_etapes().';
			   ';
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
		
		
		//==================================================================
		// Set error flag to zero
		//==================================================================
		$this->c_Obj_verification->set_niveau_informations(0);
		$_SESSION[$this->c_ssid]['niveau_informations'] = 0;
		//==================================================================
		
		
		//==================================================================
		// Check steps (not null)
		//==================================================================
		$verif = $this->c_Obj_etape->check_step_not_null();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}
		//==================================================================
				
		
		//==================================================================
		// Check if link to other step link to existing step
		//==================================================================
		$verif = $this->c_Obj_etape->check_link_step();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}		
		//==================================================================

		
		//==================================================================
		// Check if link to iObject are correct
		//==================================================================
		$verif = $this->c_Obj_etape->check_link_iobject();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}		
		//==================================================================

				
		//==================================================================
		// Check if link to iObject have duplicates parameters
		//==================================================================
		$verif = $this->c_Obj_etape->check_duplicate_param_url();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}		
		//==================================================================

		
		//==================================================================
		// Check if iObject links are ok
		//==================================================================
		$verif = $this->c_Obj_etape->check_ssid_presence_url();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}		
		//==================================================================

		
		//==================================================================
		// Check step variables
		//==================================================================
		if($verification_lancement == true)
		{
			// Sheet start check
			$verif = $this->c_Obj_etape->check_var_step($this->c_Obj_entete->get_contenu('prerequis'));
		}
		else
		{
			$verif = $this->c_Obj_etape->check_var_step($this->c_Obj_entete->get_prerequis());		
		}
		
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}
		//==================================================================

		
		//==================================================================
		// Check step output variables ( varout for short )
		//==================================================================
		$verif = $this->c_Obj_etape->check_local_varout();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}	
		//==================================================================
		
		
		//==================================================================
		// General iSheet checks
		//==================================================================
		if($verification_lancement == true)
		{
			// Sheet start check
			$array_control = $this->c_Obj_verification->sheet_control($this->c_Obj_entete->get_contenu('titre'),$this->c_Obj_entete->get_contenu('description'),$this->c_Obj_entete->get_contenu('prerequis'),$this->c_Obj_entete->get_contenu('pers'),$this->c_Obj_entete->get_contenu('id_POLE'),$this->c_Obj_entete->get_contenu('vers_goldstock'),$this->c_Obj_entete->get_contenu('theme'),$this->c_Obj_entete->get_contenu('id_module'));
			$verif = $array_control['verif'];
			$eval_js .= $array_control['eval_js'];
		}
		else
		{
			//$array_control = $this->c_Obj_verification->sheet_control($titre,$this->c_Obj_entete->get_description(), $this->c_Obj_entete->get_prerequis(),$trigramme,$pole,$version,$activite,$niveau);
			$array_control = $this->c_Obj_verification->sheet_control($titre,$this->c_Obj_entete->get_description_raw(), $this->c_Obj_entete->get_prerequisite_raw(),$trigramme,$pole,$version,$activite,$niveau);
			$verif=$array_control['verif'];
			$eval_js .= $array_control['eval_js'];
		}
		
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}			
		//==================================================================

		
		//==================================================================
		// Check duplicate variables
		//==================================================================
		$verif = $this->check_duplicate_var();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}	
		//==================================================================

		
		//==================================================================
		// Check tags
		//==================================================================
		$verif = $this->c_Obj_verification->check_tag();
		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}		
		//==================================================================
				
		
		//==================================================================
		// Check link to iObject (mandatory parameters)
		//==================================================================
		$verif = $this->c_Obj_etape->check_mandatory_param_url();

		if(is_array($verif))
		{
			foreach($verif as $value)
			{
				$verification_generale[] = $value;
			}
		}	
		//==================================================================

		
		//==================================================================
		// Sort errors by critical level
		// Get list of columns
		//==================================================================
		foreach($verification_generale as $key => $row) 
		{
			$criticite[$key] = $row['criticite'];

			if($row['criticite'] > $_SESSION[$this->c_ssid]['niveau_informations'])
			{
				$this->c_Obj_verification->set_niveau_informations($row['criticite']);
				$_SESSION[$this->c_ssid]['niveau_informations'] = $row['criticite'];
			}
		}
		
		if(isset($criticite) && is_array($criticite))
		{
			array_multisort($criticite,SORT_DESC,$verification_generale);
		}
		
		$message = '';
		$titre_controle = '';
		$erreur = false;
		$message .= '<table id="informations">';

		foreach($verification_generale as $value)
		{
			$erreur = true;
			$message .= $value['message'];
		}
		$message .= '</table>';
		//==================================================================

				
		if(!$erreur)
		{
			return $this->c_Obj_verification->get_no_error_msg($xml);
		}
		else
		{
			if($xml)
			{
				// XML return	
				header("Content-type: text/xml");
				echo "<?xml version='1.0' encoding='UTF8'?>";
				echo "<parent>";
				echo "<message_controle>".$this->protect_xml($message)."</message_controle>";
				echo "<debug>false</debug>";
				echo "<titre_controle>".$this->protect_xml($this->c_Obj_verification->generer_bandeau_informations($verification_lancement,$titre_controle))."</titre_controle>";
				echo "<niveau_erreur>".$this->c_Obj_verification->return_niveau_informations()."</niveau_erreur>";
				echo "<eval_js>".$eval_js."</eval_js>";
				echo "</parent>";
			}
			else
			{
				// Text return
				return $message;
			}	
		}		
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Control duplicate entries for input and output variables
	 * @return HTML : Return HTML information headband
	 * @key_arry : array : List of control message and critical level
	 * 						['criticite'] = 2 means mandatory : No record possible until issue solved
	 * @access private
	 ====================================================================*/
	private function check_duplicate_var()
	{
		$key_array = 0;
		$verification = array();
		
		$sql = '	SELECT
						COUNT(1) AS NBR_DOUBLES,
						`id_fiche`,
						`num_version`,
						`id_action`,
						`NOM`,
						`TYPE`
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
					WHERE 1 = 1
					AND `id_fiche` = '.$this->c_id_temp.'  
					AND (`TYPE` = "OUT" OR `TYPE` = "IN") 
					GROUP  BY 	`id_fiche`,
								`num_version`,
								`id_action`,
								`NOM`,
								`TYPE`
					HAVING COUNT(1) > 1;
			  ';
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		
		//==================================================================
		// Generate error message fo each duplicate VARIN / VAROUT entry
		//==================================================================
		$erreur = false;
		while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{
			$erreur = true;
			
			if($row['TYPE'] == 'OUT')
			{
				// Duplicate Varout
				$message = str_replace('$var','<span class="BBVarout">'.$row['NOM'].'</span>',$_SESSION[$this->c_ssid]['message'][190]);
				$message = str_replace('$j',$row['id_action'],$message);
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.$row['id_action'].','.$this->c_Obj_etape->get_nbr_etapes().',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');',$row['id_action']);
			}
			else
			{
				// Duplicate Varin
				$message = str_replace('$var','<span class="BBVarin">'.$row['NOM'].'</span>',$_SESSION[$this->c_ssid]['message'][104]);
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_2\');',$row['id_action']);
			}
			
			$verification[$key_array]['criticite'] = 2;
			$key_array++;
		}
		//==================================================================
		
		
		//==================================================================
		// ON STEP
		// LOCAL VAROUT HAVE SAME NAME THAN EXTERNAL VAROUT FROM LINK
		//==================================================================
				$sql = '	SELECT 
								fp1.`nom`,
								fp1.`id_action` 
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp1
							WHERE 1 = 1
								AND fp1.`TYPE` = "OUT" 
								AND fp1.`temp` = 0 
								AND fp1.`id_fiche` = '.$this->c_id_temp.'
								AND fp1.`nom` IN (
													SELECT 
														fp2.`nom` 
													FROM
														`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp2
													WHERE 1 = 1
														AND fp2.`id_action` = fp1.`id_action`
														AND fp2.`TYPE` ="EXTERNE" 
														AND fp2.`used` = 1 
														AND fp2.`id_fiche` = '.$this->c_id_temp.'
												 );
					  ';
				
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

		$erreur = false;
		while ($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
		{
			$erreur = true;
			$message = str_replace('$var','<span class="BBVarout">'.$row['nom'].'</span>',$_SESSION[$this->c_ssid]['message'][194]);
			$message = str_replace('$j',$row['id_action'],$message);
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.($row['id_action']).','.$this->c_Obj_etape->get_nbr_etapes().',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');',$row['id_action']);;
			$verification[$key_array]['criticite'] = 2;
			$key_array++;
		}
		//==================================================================
		
		
		//==================================================================
		// CONTROL DUPLICATE ENTERNAL VAROUT NAME IN USE 
		//==================================================================
		$sql = '	SELECT 
							`nom`,
							`id_action` 
					FROM
						(
							SELECT 
								DISTINCT 
										`id_src`,
										`nom`,
										`id_action`
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
							WHERE 1 = 1
								AND `TYPE` ="EXTERNE"  
								AND `used` = 1
								AND `id_fiche` = '.$this->c_id_temp.'
						) AS compte 			
					GROUP BY nom,id_action
					HAVING COUNT(1) > 1;
			  ';
				
		$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
		{
			$erreur = true;
			$message = str_replace('$var','<span class="BBVarExt">'.$row['nom'].'</span>',$_SESSION[$this->c_ssid]['message'][232]);
			$message = str_replace('$j',$row['id_action'],$message);
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'editer_etape('.($row['id_action']).','.$this->c_Obj_etape->get_nbr_etapes().',\''.$this->c_ssid.'\');a_tabbar.setTabActive(\'tab-level2\');',$row['id_action']);;
			$verification[$key_array]['criticite'] = 2;
			$key_array++;
		}
		
		if($erreur == true && $_SESSION[$this->c_ssid]['niveau_informations'] < 2)
		{
			$_SESSION[$this->c_ssid]['niveau_informations'] = 2;
		}
		//==================================================================
		
		return $verification;
	}
	/*===================================================================*/
	
	
	/**
	 * @method HTML verification genere le bandeau d'informations (titre) par rapport au statut d'erreur en cours.
	 * @return HTML retourne le code HTML du bandeau d'informations
	 * @access public
	 */
	public function generer_bandeau_informations() 
	{
		return $this->c_Obj_verification->generer_bandeau_informations();
	}
	
	public function get_max_version_of_object($type_object,$id_object)
	{
		if($type_object == '__IFICHE__')
		{
			// iFiche
			$sql = 'SELECT `num_version` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
					WHERE `id_fiche` = '.$id_object;
		}
		else
		{
			// iCode
			$sql = 'SELECT `version` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
					WHERE `id` = '.$id_object;
		}
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return mysql_result($requete,0);
	}
	
	public function get_title_of_object($type_object,$id_object,$version_object)
	{
		if($type_object == '__IFICHE__')
		{
			// iSheet
			if($version_object == $_SESSION[$ssid]['message']['iknow'][504])
			{
				$sql = 'SELECT `titre` 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
						WHERE `id_fiche` = '.$id_object;
			}
			else
			{
				$sql = 'SELECT `titre` 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'` 
						WHERE `id_fiche` = '.$id_object.' 
						AND `num_version` = '.$version_object;
			}
		}
		else
		{
			// iCode
			if($version_object == $_SESSION[$ssid]['message']['iknow'][504])
			{
				$sql = 'SELECT `Titre` 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
						WHERE `id` = '.$id_object;
			}
			else
			{
				$sql = 'SELECT `Titre` 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` 
						WHERE `id` = '.$id_object.' 
						AND `Version` = '.$version_object;
			}
		}
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return mysql_result($requete,0,0);
	}
	
	/**==================================================================
	 * @method decimal Get the last iSheet last version
	 * @return decimal last version
	 * @access private
	====================================================================*/ 
	public function get_max_version()
	{
		if ($this->c_id == 'new')
		{
			// New iSheet, force max version to 1
			return 1;
		}
		else
		{
			// iSheet already exists, get the max version using max iSheet table
			$sql = 'SELECT `num_version` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
					WHERE `id_fiche` = '.$this->c_id;
		
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			return mysql_result($requete,0);
		}	
	}
	/*===================================================================*/ 
	
		
	/**==================================================================
	 * @method array Recover head iSheet information
	 * @return array Head iSheet information ( eg : Title, version, area, work, subwork, last date update... )
	 * @access private
	====================================================================*/ 
	private function get_sheet_content() 
	{
		//==================================================================
		// Clear isheet header information
		//==================================================================
		if ($this->c_id == 'new')
		{
			$contenu['titre'] = '';
			$contenu['num_version'] = '';
			$contenu['theme'] = '';
			$contenu['id_module'] = '';
			$contenu['id_POLE'] = '';
			$contenu['vers_goldstock'] = '';
			$contenu['description'] = '';
			$contenu['pers'] = '';
			$contenu['prerequis'] = '';
			$contenu['texte'] = '';
			$contenu['date_modif'] = '';
			$contenu['heure_modif'] = '';
			$contenu['date_raw'] = '';
			$contenu['id_statut'] = '';
			$this->c_statut = 1;
		//==================================================================
			
			return $contenu;	
		}
		else
		{
			$sql = "SELECT 
						f.`titre`,
						f.`num_version`,
						f.`theme`, 
						f.`id_module`,
						f.`id_POLE`,
						f.`vers_goldstock`,
						f.`description`, 
						f.`pers`,
						f.`prerequis`, 
						t.`texte`,
						f.`id_statut`,
						DATE_FORMAT(f.`date`,'%d.%m.%Y') as date_modif,
						DATE_FORMAT(f.`date`,'%H:%i') as heure_modif,
						f.`date` as date_raw
					FROM 
						`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name']."` f,
						`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_libelles']['name']."` t 
					WHERE 1 = 1
						AND `f`.id_statut = `t`.id_texte 
						AND `f`.id_fiche = ".$this->c_id."
						AND `num_version` = ".$this->c_version.' 
						AND `id_lang` = "'.$this->c_language.'" 
						AND `type` = "statut"
						AND `version_active` = "'.$this->c_version_app.'"
						AND `objet` = "ifiche"
				   ';
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
			{
				// iSheet free for updating
				if($this->c_obsolete == 0)
				{
					$this->c_titre = $row['titre'];
				}	
				else
				{
					// iSheet is lock updated : ***** message ***** 
					$this->c_titre = $_SESSION[$this->c_ssid]['message'][103].' '.$row['titre'];
					$row['titre'] = $_SESSION[$this->c_ssid]['message'][103].' '.$row['titre'];
				}
				
				if(isset($row['id_statut']))
				{
					$this->c_statut = $row['id_statut'];
				}
				
				return $row;
			}
		}			
	}
	/*===================================================================*/ 
	
	
	/**==================================================================
	 * @method boolean Check iSheet ID and version both exists
	 * @return boolean 	true means all is alright
	 * 					if not... Display fatal error
	 * @access private
	====================================================================*/ 
	/**
	 * @method boolean Verifie l'existance de la fiche appelée
	 * @return boolean version true si elle existe, false sinon
	 * @access private
	 */
	private function check_ifiche_exist($p_version) 
	{
		if(is_numeric($this->c_id) && strpos($this->c_id,'-') === false && strpos($this->c_id,'.') === false) // SIBY_ID_NOT_POSITIF_INTEGER
		{
			$sql = 'SELECT
						1 
					FROM
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
					WHERE 1 = 1
						AND `id_fiche` = '.$this->c_id.';
				   ';
	
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			if(mysql_num_rows($resultat) == 0)
			{
				// iSheet number doesn't exist in database
				echo '<title>'.$_SESSION[$this->c_ssid]['message']['iknow'][17].'</title></head>
						<body style="background-color:#A61415;">
						<div id="iknow_msgbox_background"></div>
						<div id="iknow_msgbox_conteneur" style="display:none;"></div>
						<script type="text/javascript">generer_msgbox(decodeURIComponent(\''.str_replace("'","\'",$_SESSION[$this->c_ssid]['message']['iknow'][17]).'\'),decodeURIComponent(\''.str_replace("&id",$this->c_id,str_replace("'","\'",$_SESSION[$this->c_ssid]['message'][202])).'\'),\'erreur\',\'msg\',false,true);</script></body></html>';
				die();
			}
			else
			{
				if($p_version != '' && is_numeric($p_version))
				{
					$sql = 'SELECT
								1 
							FROM
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'`
							WHERE 1 = 1
								AND `id_fiche` = '.$this->c_id.'
								AND `num_version` = '.$p_version.';
						   ';
					
					$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
					if(mysql_num_rows($result) == 0)
					{
						// Version of iSheet doesn't exist
						echo '<title>'.$_SESSION[$this->c_ssid]['message']['iknow'][17].'</title></head>
						<body style="background-color:#A61415;">
						<div id="iknow_msgbox_background"></div>
						<div id="iknow_msgbox_conteneur" style="display:none;"></div>
						<script type="text/javascript">generer_msgbox(decodeURIComponent(\''.str_replace("'","\'",$_SESSION[$this->c_ssid]['message']['iknow'][17]).'\'),decodeURIComponent(\''.str_replace("&version",$p_version,str_replace("&id",$this->c_id,str_replace("'","\'",$_SESSION[$this->c_ssid]['message'][389]))).'\'),\'erreur\',\'msg\',false,true);</script></body>';
						die();
					}
					else
					{
				 		return true;	
					}
				}
				else
				{
					if($p_version != '' || is_numeric($p_version))
					{
						// Version is not empty and seems not to be a numeric
						echo '<title>'.$_SESSION[$this->c_ssid]['message']['iknow'][17].'</title></head>
						<body style="background-color:#A61415;">
						<div id="iknow_msgbox_background"></div>
						<div id="iknow_msgbox_conteneur" style="display:none;"></div>
						<script type="text/javascript">generer_msgbox(decodeURIComponent(\''.str_replace("'","\'",$_SESSION[$this->c_ssid]['message']['iknow'][17]).'\'),decodeURIComponent(libelle[432]).replace(\'$version\',\'<b>'.$p_version.'</b>\'),\'erreur\',\'msg\',false,true);</script></body></html>';
						die();
					}
				}
			}
		}
		else
		{
			// ID not valid form ( not numeric )
					echo '<title>'.$_SESSION[$this->c_ssid]['message']['iknow'][17].'</title></head>
							<body style="background-color:#A61415;">
							<div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div>
							<script type="text/javascript">generer_msgbox(decodeURIComponent(\''.str_replace("'","\'",$_SESSION[$this->c_ssid]['message']['iknow'][17]).'\'),decodeURIComponent(libelle[433]).replace(\'$id\',\'<b>'.$this->c_id.'</b>\'),\'erreur\',\'msg\',false,true);</script></body></html>';
							die();
		}
	}
	/*===================================================================*/ 
	
	
	 /**
	 * @method decimal retourne le nombre d'étapes de la fiche
	 * @return decimal nombre d'étapes de la fiche
	 * @access private
	 */
	public function compter_etapes() 
	{
		return $this->c_Obj_etape->get_nbr_etapes();
	}
	
	
	 /**
	 * @method HTML Genere la fiche.
	 * @return HTML Code HTML de la fiche
	 * @access public
	 */	
	public function generer_fiche() 
	{
		/**==================================================================
		 * GENERATION DU CONTENU DES ETAPES
		 ====================================================================*/	
		$this->c_Obj_etape->generer_etapes();
		/*===================================================================*/
		
		/**==================================================================
		 * CONFIGURATION DES ONGLETS
		 ====================================================================*/		
		$onglet = 'var a_tabbar = new iknow_tab(\'a_tabbar\');';
		/*===================================================================*/

		/**==================================================================
		 * GENERATION DE L'ONGLET D'ENTETE
		 ====================================================================*/
		$onglet .= $this->c_Obj_entete->generer_entete(); 
		/*===================================================================*/

		/**==================================================================
		 * GENERATION DE L'ONGLET D'ETAPES
		 ====================================================================*/	
		
		if($this->c_type == __FICHE_VISU__) 
		{
			$onglet .= 'a_tabbar.addTab("tab-level2","<div class=\"onglet_icn_step\">'.rawurlencode($_SESSION[$this->c_ssid]['message'][40].' ('.$this->c_Obj_etape->get_nbr_etapes().')</div>').'","'.rawurlencode($this->c_Obj_etape->contenu_tab_etapes()).'","set_tabbar_actif(\'tab-level2\',retourne_tab_entete_actif(),retourne_tab_etape_actif(),retourne_tab_etape_ligne_actif());charger_var_dans_url();");';
			$onglet .= $this->c_Obj_etape->generer_tab_etapes();
		}
		else
		{
			$onglet .= 'a_tabbar.addTab("tab-level2","<div class=\"onglet_icn_step\">'.rawurlencode($_SESSION[$this->c_ssid]['message'][40].' (<span id="onglet_nbr_etape">'.$this->c_Obj_etape->get_nbr_etapes().'</span>').')</div>","'.rawurlencode($this->c_Obj_etape->display_step()).'","event_click_onglet(\'tab-level2\');");';
		}
		
		/*===================================================================*/
				
		echo $onglet;
	}
	
	
	 /**
	 * @method none Sauvegarde l'étape $p_id avec le contenu $p_content
	 * @param decimal $p_id id de l'étape à sauvegarder
	 * @param string $p_content contenu de l'étape à sauvegarder
	 * @access public
	 */		
	public function save_step($p_id,$p_content) 
	{
		$this->c_Obj_etape->save_step($p_id,$p_content);
	}
	
	 /**
	 * @method none supprime l'étape $p_id
	 * @param decimal $p_id id de l'étape à supprimer
	 * @access public
	 */	
	public function del_step($p_id) 
	{
		echo $this->c_Obj_etape->del_step($p_id);
	}

	/**
	 * @method HTML Verifie que aucune étape n'a un lien vers l'etape que l'on va supprimer
	 * @param decimal $p_id id de l'étape à supprimer
	 * @return HTML Retourne le code HTML du bandeau d'informations
	 * @access public
	 */	
	public function verif_del_step($p_etape_a_supprimer)
	{
		$this->c_Obj_etape->verif_del_step($p_etape_a_supprimer);
	}
	
	
	
	/**
	 * 
	 * Methode qui permet de deplacer une étape vers un id de destination.
	 * @access public
	 * 
	 * @param decimal $id_etape_src numero de l'étape source.
	 * @param decimal $id_etape_dest numero de l'étape de destination.
	 */
	public function deplacer_etape($p_sens,$p_id)
	{
		$this->c_Obj_etape->deplacer_etape($p_sens,$p_id);
	}
	
	
	/**
	 * @method HTML Ajoute une étape
	 * @param decimal $p_id_etape indique le numero de l'étape à ajouter.
	 * @return HTML Code HTML de toute les étapes
	 * @access public
	 */	
	public function add_etape($p_id_etape)
	{
		echo $this->c_Obj_etape->ajouter_etape($p_id_etape);
	}
	
	
	/**
	 * @method string Recupère le titre de la fiche
	 * @return string Titre de la fiche
	 * @access public
	 */	
	public function get_titre()
	{
		return $this->c_titre;
	}
	
	/**
	 * @method string Recupère le titre de la fiche sans le bbcode
	 * @return string Titre de la fiche sans bbcode
	 * @access public
	 */	
	public function get_titre_sans_bbcode($protect = false)
	{
		if($protect)
		{	
			return str_replace("'","\'",$this->convertBBCodetoHTML($this->c_titre));
		}
		else
		{
			return $this->convertBBCodetoHTML($this->c_titre);
		}
	}	

	
	
	/**
	 * @method decimal Sauvegarde la fiche en base de données
	 * @param array $tab_entete contient les données de l'entête de la fiche (titre...)
	 * @return decimal ID de la fiche sauvegardée
	 * @access public
	 */	
	public function save_sheet($tab_entete) 
	{
		
		$controle = '';				// False si il n'y a pas d'erreur (niveau 2) true si il y a des erreurs de niveau 2
		$message_controle = '';		// Contient le message du controle
		$erreur_sauvegarde = ' ';	// Vide si pas d'erreur(erreur php ou mysql), contient l'erreur si il y en a.
		$redirection = '';			// Page de redirection à la fin de la sauvegarde

				
		/**==================================================================
			* GESTION DU VEROUILLAGE DE LA FICHE
		====================================================================*/				
		if($tab_entete['bloquer'] == 'true')
		{
			$this->c_obsolete = 1;
		}
		else
		{
			$this->c_obsolete = 0;
		}
		/*===================================================================*/
				
		/**==================================================================
		 * VERIFICATION GENERALE DE LA FICHE
		 ====================================================================*/	
		$message_controle = $this->sheet_control($tab_entete['titre'],$tab_entete['modifpar'],$tab_entete['pole'],$tab_entete['version'],$tab_entete['activite'],$tab_entete['idmodule']);
		/*===================================================================*/
		
		if($this->c_Obj_verification->return_niveau_informations() > 1)
		{
			$controle = 'true';		// Erreur dans la fiche
		}
		else
		{
			$controle = 'false';	// Pas d'erreur dans la fiche		
			
			/**==================================================================
		 	* DEFINITION DE L'ID ET DE LA VERRSION DE LA FICHE
		 	====================================================================*/				
			if($this->c_id == 'new')
			{
				// on créer une nouvelle fiche, on va donc créer un id final			
				/*$sql = 'SELECT max(id_fiche) as "maxid" 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].' 
						WHERE id_fiche < 99999';*/

				// Recycle hole
				$sql = 'SELECT IF ((
				SELECT 1
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
				WHERE id_fiche = 1) IS NULL,1, MIN(a.id_fiche)+1) as "maxid"
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` a
				WHERE 1 = 1
				AND NOT EXISTS (
					SELECT b.id_fiche
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` b
					WHERE 1 = 1
					AND b.id_fiche = (a.id_fiche + 1)
					)
				AND id_fiche < 99999
				LIMIT 1;
				';
						
				$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				$max_id_fiche = mysql_result($requete,0);
			
				$this->c_id = $max_id_fiche;	
				$this->c_Obj_etape->set_id($this->c_id);
				$this->c_Obj_etape->set_version(0);
				$this->c_version = 0;
			}
			else
			{
				// on va donc créer une version finale
				$sql = 'SELECT num_version as "maxversion" 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].' 
						WHERE id_fiche = '.$this->c_id;
				
				$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				$max_version_fiche = mysql_result($requete,0);
			
				// On affecte la nouvelle version à la fiche	
				$this->c_Obj_etape->set_version($max_version_fiche + 1);
				$this->c_version = $max_version_fiche + 1;
			}
			/*===================================================================*/
			
			/**==================================================================
		 	* Create the sheet in database
		 	====================================================================*/		
			$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'].'(id_POLE,id_statut,theme,id_module,id_fiche,num_version,titre,vers_goldstock,description,date,pers,prerequis,obsolete)' .
				   'VALUES ("'.($this->protect_save($tab_entete['pole'])).'","'.($this->protect_save($tab_entete['statut'])).'","'.($this->protect_save($tab_entete['activite'])).'","'.($this->protect_save($tab_entete['idmodule'])).'",'.$this->c_id.','.$this->c_version.',"'.($this->protect_save($tab_entete['titre'])).'","'.($this->protect_save($tab_entete['version'])).'","'.($this->protect_save($this->c_Obj_entete->get_description())).'",NOW(),"'.($this->protect_save(mb_convert_case($tab_entete['modifpar'], MB_CASE_UPPER, "UTF-8"))).'","'.($this->protect_save($this->c_Obj_entete->get_prerequis())).'",'.$this->c_obsolete.')';
			/*===================================================================*/
			
			if(mysql_query($sql,$this->link) == false)
			{
				$erreur_sauvegarde .= mysql_error($this->link).'<span style="color:red;font-weight:bold">erreur de sauvegarde de la fiche : </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
			}	
			else
			{
				/**==================================================================
			 	* CREATION DES ETAPES EN BASE
			 	====================================================================*/	
				$erreur_sauvegarde .= $this->c_Obj_etape->ecrire_etape_bdd();
				/*===================================================================*/

				/**==================================================================
			 	* DEPLACEMENT DES VARIABLES STOCKEES EN ID TEMPORAIRE
			 	====================================================================*/			
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						SET max = 0 
						WHERE id_fiche = '.$this->c_id.'  
						AND num_version < '.$this->c_version;
				
				if(mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de mise à jour des variables 1: </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}	
				
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						SET id_fiche = '.$this->c_id.',
						num_version = '.$this->c_version.',
						max = 1   
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND TYPE <> "EXTERNE"';
				
				
				if (mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de mise à jour des variables 2: </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}	
								
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						SET id_fiche = '.$this->c_id.',
						num_version = '.$this->c_version.',
						max = 1 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND TYPE = "EXTERNE" 
						AND used = 1';
				
				
				if (mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de mise à jour des variables 3: </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}	
				/*===================================================================*/
				
				/**==================================================================

				/**==================================================================
			 	* DEPLACEMENT DES TAGS STOCKES EN ID TEMPORAIRE
			 	====================================================================*/			
				// ATTENTION : Insert servant de déclencheur pour le trigger des tags. Ne pas mettre de REPLACE !!!
				
				
				$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`, `id_src`, `version_src`) 
						SELECT `IdTag`, '.$this->c_id.', `Etape`, '.$this->c_version.' , `Tag`, `Groupe`, `objet`, `temp`, `id_src`, `version_src`
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
						WHERE ID = '.$this->c_id_temp;
	
				if(mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de mise à jour des tags : </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}	
				
				$sql = "DELETE FROM ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_tags']['name']." WHERE ID = ".$this->c_id_temp;

				if(mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de suppression des tags temporaires : </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}	
				/*===================================================================*/
				
				/**==================================================================
			 	* GESTION DU LOG DE LA FICHE
			 	====================================================================*/				
				$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_log_action']['name'].' 
						SET ID = '.$this->c_id.', 
						version = '.$this->c_version.',
						date_action = date_action 
						WHERE ID = '.$this->c_id_temp;
				
				if(mysql_query($sql,$this->link) == false)
				{
					$erreur_sauvegarde .= '<span style="color:red;font-weight:bold">erreur de mise à jour du log de la fiche : </span>'.mysql_error().'<br />'.$sql.'<br /><br />';
				}
				/*===================================================================*/				
				
				/**==================================================================
			 	* PURGE DES ID TEMOPORAIRES
			 	====================================================================*/					
				// Vide la table ifiche_parametres
				$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."` WHERE id_fiche = ".$this->c_id_temp;
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				// Vide la table tags
				$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."` WHERE ID = ".$this->c_id_temp;
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
				// Vide la table lock
				$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name']."` WHERE id_temp  = ".$this->c_id_temp;
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				// Vide la table url_temp
				$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."` WHERE id_temp  = ".$this->c_id_temp;
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				/*===================================================================*/
			}
		}
		
		if($erreur_sauvegarde != ' ')
		{
			$controle = 'true';
		}
		
		// XML return
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<controle>".$this->protect_xml($controle)."</controle>";
		echo "<message_controle>".$this->protect_xml($message_controle)."</message_controle>";
		echo "<titre_controle>".$this->protect_xml($this->c_Obj_verification->generer_bandeau_informations())."</titre_controle>";
		echo "<erreur_sauvegarde>".$this->protect_xml($erreur_sauvegarde)."</erreur_sauvegarde>";
		echo "<id_fiche>".$this->protect_xml($this->c_id)."</id_fiche>";
		echo "</parent>";
	}
	

	 
	 /**
	 * @method HTML Duplique la fiche
	 * @return HTML Code HTML du bandeau d'informations
	 * @access public
	 * NR_IKNOW_5_
	 */	
	public function dupliquer_fiche($force = 'false')
	{
		// Controle de la version de la fiche	
		if($force == 'false' && !$this->c_Obj_verification->is_last_version())
		{	
			// XML return	
			header("Content-type: text/xml");
			echo "<?xml version='1.0' encoding='UTF8'?>";
			echo "<parent>";
			echo "<message_confirmation>".$this->protect_xml($_SESSION[$this->c_ssid]['message'][379])."</message_confirmation>";
			echo "<titre_confirmation>".$this->protect_xml($_SESSION[$this->c_ssid]['message'][380])."</titre_confirmation>";		
			echo "</parent>";
			die();
		}

		// on donne un nouvel id à la fiche
		$this->c_id = 'new';
		
		// on donne une version 0 à la fiche
		$this->c_version = 0;
		
		// on met les etapes à new
		$this->c_Obj_etape->dupliquer_etapes();
		$this->c_Obj_etape->set_version(0);
		
		$this->c_Obj_lock->set_id('new');
		
		$this->c_Obj_verification->set_id('new');
		
		
		// on remplace la version des tags par la version 0
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].
			' SET Version = 0 '.
			' WHERE ID = '.$this->c_id_temp." AND objet = \"ifiche\"";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		// on remplace la version des variables par la version 0
		$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].''.
			' SET num_version = 0 '.
			' WHERE id_fiche = '.$this->c_id_temp;
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name'].'`' .
			' SET id = 0 '.
			' WHERE id_temp = '.$this->c_id_temp;	
	
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		
		
		$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varin']->define_col_value('num_version',0);
		$_SESSION['vimofy'][$this->c_ssid]['vimofy_tags']->define_col_value('Version',0);
		 
		
		//on affiche un message comme quoi la fiche est dupliquée
		// XML return	
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<duplication_ok>".$this->protect_xml($this->c_Obj_verification->generer_message_duplication())."</duplication_ok>";
		echo "</parent>";
		die();
	}
	
	 /**
	 * @method HTML affiche la vimofy des tags des étapes
	 * @return HTML Code HTML de la vimofy
	 * @access public
	 */	
	public function get_vimofy_tag_etape($etape)
	{	
		return $this->c_Obj_etape->get_vimofy_tag_etape($etape);
	}
	
	public function vimofy_alias_step_id($etape)
	{	
		return $this->c_Obj_etape->vimofy_alias_step_id($etape);
	}	
	
	public function vimofy_alias_step_version($id,$etape)
	{	
		return $this->c_Obj_etape->vimofy_alias_step_version($id,$etape);
	}

	public function vimofy_alias_step_id_step($id,$version,$etape)
	{	
		return $this->c_Obj_etape->vimofy_alias_step_id_step($id,$version,$etape);
	}	
	
	 /**
	 * @method none Verifie si l'etape comporte un lien vers une autre fiche. Elle créer la requete SQL pour charger les variables d'entrée des fiches presente en lien dans le contenu de l'etape $contenu
	 * @param string $p_content contenu de l'étape
	 * @access public
	 */	
	public function variables_externe($p_content)
	{
		$this->c_Obj_etape->variables_externe($p_content);
	}
	
	 /**
	 * @method SQL genere la requete qui permet d'afficher les variables externe de la fiche
	 * @return SQL requete SQL des variables externe
	 * @access public
	 */	
	public function get_requete_var_externe()
	{
		return $this->c_Obj_etape->get_requete_var_externe();
	}
	
	 /**
	 * @method SQL genere la requete qui permet d'afficher les étapes de la fiche sauf celle active
	 * @param decimal $etape_en_cours etape en cours de modification
	 * @return SQL requete SQL des étapes
	 * @access public
	 */		
	public function generer_liste_etapes($etape_en_cours)
	{
		return $this->c_Obj_etape->generer_liste_etapes($etape_en_cours);
	}
	
	/**
	 * Mise à jour du statut de la fiche lors du changement du statut (en modif)
	 * @param entier $id_statut
	 */
	public function set_statut($id_statut)
	{
		$this->c_statut = $id_statut;
		$this->c_Obj_etape->set_statut($id_statut);
	}
	
	/**==================================================================
	* Get iSheet status label level
	====================================================================*/ 
	public function get_statut_lib()
	{
		if($this->c_statut < $_SESSION[$this->c_ssid]['configuration'][32])
		{
			$label_status = $_SESSION[$this->c_ssid]['message'][354];
		}
		else
		{
			$label_status = $_SESSION[$this->c_ssid]['message'][355];
		}
		return $label_status;
	}
	/*===================================================================*/  
	
	/**==================================================================
	 * charger_var_dans_url : Change varin value in URL when user change tab selection
	 * $url : containt the whole url requested
	 * $version : target version of iSheet to change or add in url
	 ====================================================================*/	
	public function charger_var_dans_url($url,$p_version = null)
	{
		//==================================================================
		// URL_REMOVE_#_CHANGE_VERSION
		//==================================================================
		$pos = strpos($url,'#');
		if($pos)
		{
			$url = substr($url,0,$pos);
		}
		//==================================================================
		
		$url = str_replace('version='.$this->c_version,'version='.$p_version,$url,$nbr_remplacement);
		if($nbr_remplacement == 0)
		{
			$url.'&version='.$p_version;
		}
		
		
		if($p_version == null)
		{
			$p_version = $this->c_version;
			$ssid = '&ssid='.$this->c_ssid;
		}
		else
		{
			$ssid = '';
		}
		
		// Recover language keyword in configuration database
		$reserved_word = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
		$language_keyword = $reserved_word[4];
		
		// Check if language key word is forced in current URL
		$pos = strrpos($url, $language_keyword);
		$language = '';
		if($pos === false)
		{
			// No specific language forced in browser url
			$str_url_language = '';
		}
		else 
		{
			$length_key = strlen($language_keyword)+1;
			$language = substr($url,$pos+$length_key,3); // 3 carried length of language id in base
			$str_url_language = '&'.$language_keyword.'='.$language;
		}
		
		$url = 'ifiche.php?&ID='.$this->c_id.'&version='.$p_version.$str_url_language;
		
		$param_url = '';
		$param_non_changer = '';		//Si une valeur n'a pas été changée mais est définie
		$changement = false;
		
		
		//On verifie si de nouvelles VARIN ont été modifiées
		$sql = '
				SELECT
					`nom` 		AS `nom`,
					`resultat`	AS `resultat`,
					DEFAUT AS `DEFAUT`,
					NEUTRE AS `NEUTRE` 
				FROM
					'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
				WHERE 1 = 1
					AND `id_fiche` = '.$this->c_id_temp.' 
					AND `TYPE` = "IN" 
					AND `num_version` = '.$this->c_version;

		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);;

		while ($row = mysql_fetch_array($requete,MYSQL_ASSOC))
		{
			if($row['resultat'] != '' || is_null($row['resultat']))
			{
				$param_url .= '&'.$row['nom'].'='.rawurlencode($row['resultat']);
			}
		}

		if($changement == false)
		{
			return $url.$param_url.'&IK_VALMOD='.$this->c_Obj_entete->get_ik_valmod().$ssid;
		}
		else
		{
			return $url.$param_url.$ssid;
		}
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * changer_version
	 * $url : containt the whole url requested
	 * $version : target version of iSheet to change or add in url
	 ====================================================================*/	
	public function changer_version($url,$version)
	{
		//==================================================================
		// URL_REMOVE_#_CHANGE_VERSION
		//==================================================================
		$pos = strpos($url,'#');
		if($pos)
		{
			$url = substr($url,0,$pos);
		}
		//==================================================================
		
		$url = str_replace('version='.$this->c_version,'version='.$version,$url,$nbr_remplacement);
		if($nbr_remplacement == 0)
		{
			return $url.'&version='.$version;
		}
		else
		{
			return $url;
		}
	}
	/*===================================================================*/	
	
	
	public function reload_isheet($p_version)
	{
		if($p_version != null && $p_version != $this->c_version)
		{
			// on vide la table ifiche_parametres
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."` WHERE id_fiche = ".$this->c_id_temp.";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			// on vide la table tags
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."` WHERE ID = ".$this->c_id_temp.";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			// Set the new version
			$this->c_version = $p_version;
			$this->c_Obj_etape->set_version($p_version);
			$this->c_Obj_entete->set_version($p_version);
			$this->c_Obj_entete->copy_var_and_tag();
			$dataset = $this->get_sheet_content();
			$this->c_Obj_entete->set_dataset($dataset);
		}
		else
		{
			/**==================================================================
			 * Set varin values (from the URL)
			 ====================================================================*/
			$this->c_Obj_entete->set_varin_values();
			/*===================================================================*/
		}
	}
	
	/** //SIBY
	 * @method HTML Genere la liste des versions de la fiche
	 * @return HTML Select avec la liste des versions de la fiche
	 * @access public
	 */		
	public function genere_liste_version()
	{
		//On recupere toute les version de la fiche et on genere le select.
		//return '<div style="background-color:red;width:25px;height:20px;">..</div>';
		return '<div onmouseover="over(false,9,\'-\',\'X\');" onmouseout="ikdoc(\'\');unset_text_help();" class="lst_change_version">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_version_fiche']->generate_lmod_form().'</div>';
	}
	
	
	
	/**
	 * @method decimal retourne la version de la fiche
	 * @return decimal version de la fiche
	 * @access public
	 */	
	public function get_version()
	{
		return $this->c_version;
	}
	
	/**
	 * @method none recupere le numero de plan de la fiche
	 * @access public
	 */		
	public function get_no_plan()
	{
		return $this->c_Obj_entete->get_no_plan();		
	}
	
	
	/**
	 * @method none definit l'onglet actif
	 * @access public
	 */		
	public function set_tab_actif($p_tab_haut,$p_tab_entete,$p_tab_etapes,$p_tab_etapes_sep)
	{
		$_SESSION[$this->c_ssid]['tab_haut'] = $p_tab_haut;
		$_SESSION[$this->c_ssid]['tab_bas'] = $p_tab_entete;
		$_SESSION[$this->c_ssid]['tab_etapes'] = $p_tab_etapes;
		$_SESSION[$this->c_ssid]['tab_etapes_sep'] = $p_tab_etapes_sep;
		
		$this->c_tab_actif_haut = $p_tab_haut;
		$this->c_tab_actif_bas = $p_tab_entete;
		$this->c_tab_actif_etapes = $p_tab_etapes;
		$this->c_tab_etapes_sep = $p_tab_etapes_sep;
	}
	
	
	/**
	 * @method string recupere l'onglet actif du tab-level0'
	 * @return string onglet actif
	 * @access public
	 */		
	public function get_tab_actif_haut()
	{
		(isset($_SESSION[$this->c_ssid]['tab_haut'])) ? $tab = $_SESSION[$this->c_ssid]['tab_haut'] : $tab = 'tab-level1';
		return $tab;
	}


	/**
	 * @method string recupere l'onglet actif du tab-level0_0'
	 * @return string onglet actif
	 * @access public
	 */			
	public function get_tab_actif_entete()
	{
		(isset($_SESSION[$this->c_ssid]['tab_bas'])) ? $tab = $_SESSION[$this->c_ssid]['tab_bas'] : $tab = 'tab-level1_1';
		return $tab;
	}


	/**
	 * @method string recupere l'onglet actif du tab-level1_0'
	 * @return string onglet actif
	 * @access public
	 */		
	public function get_tab_actif_etapes()
	{
		(isset($_SESSION[$this->c_ssid]['tab_etapes'])) ? $tab = $_SESSION[$this->c_ssid]['tab_etapes'] : $tab = 'tab-level2_1';
		return $tab;		
	}
	
	
	/**
	 * @method string recupere l'onglet actif du tab-level1_1_1'
	 * @return string onglet actif
	 * @access public
	 */			
	public function get_tab_actif_etapes_sep()
	{
		(isset($_SESSION[$this->c_ssid]['tab_etapes_sep'])) ? $tab = $_SESSION[$this->c_ssid]['tab_etapes_sep'] : $tab = 'tab-level2_2_1';
		return $tab;		
	}

	/**==================================================================
	* Active tab specified by tab-level in url
	* follow the form : X_Y_Z
	* Separator : underscore
	* X is the tab from the top bar
	* Y is the second one
	* Z is the last one
	* X, Y, Z are number from 1 to max number of tab
	* 
	* Eg: 2_2_3 : In english, Display tab Steps -> Step by step -> third tab
	* 
	* @method string : Go to good tab in display mode
	* @return string code javascript
	* @access public
	====================================================================*/ 
	public function retourne_tab_level($p_level)
	{
		$tab_level = explode('_',$p_level);
		
		//==================================================================
		// Initialize and load local level variable
		//==================================================================
		$level1 = '1';
		$level2 = $level1;
		$level3 = $level1;
		
		if (isset($tab_level[1]))
		{
			$level1 = $tab_level[0]; // first bar
		}
		if (isset($tab_level[1]))
		{
			$level2 = $tab_level[1]; // second bar
		}
		
		if (isset($tab_level[2]))
		{
			$level3 = $tab_level[2]; // step detail bar
		}
		//==================================================================
		
		
		//==================================================================
		// Protection wrong tab number by level
		// if tab number is below zero or not numeric then force to 1
		// if tab number is higher than max then force max value
		//==================================================================	
		if($level1 > 2 )
		{
			// Tab number to high
			$level1 = 2;
		}
		if ($level1 < 1 || !is_numeric($level1) )
		{
			// Tab number to low
			$level1 = 1;
		}
		
		if($level1 == 1 && $level2 > 5)
		{
			$level2 = 5;
		}
		if($level1 == 2 && $level2 > 2)
		{
			$level2 = 2;
		}
		if ($level2 < 1 || !is_numeric($level2) )
		{
			// Tab number to low
			$level2 = 1;
		}

		if($level1 == 2 && $level2 == 2 && $level3 > $this->c_Obj_etape->get_nbr_etapes())
		{
			$level3 =  $this->c_Obj_etape->get_nbr_etapes();
		}		
		if ($level3 < 1 || !is_numeric($level3) )
		{
			// Tab number to low
			$level3 = 1;
		}
		//==================================================================	
		
		
		//==================================================================
		// Generate javascript to active right tab in each bar
		//==================================================================	
		if($level1 == 1 ) // First bar : Header tab
		{
			$tab_actif = "	a_tabbar.setTabActive('tab-level1');
			 				head_tabbar.setTabActive('tab-level1_".$level2."');
							tabbar_step.setTabActive('tab-level2_1');
			 				step_tabbar_sep.setTabActive('tab-level2_2_1')
			 				";			
		}
		
		if($level1 == 2 ) // First bar : Steps(xx) tab
		{
			if($level2 == 1)
			{
				// line mode
				$tab_actif = "	a_tabbar.setTabActive('tab-level2');
				 				head_tabbar.setTabActive('tab-level1_1');
								tabbar_step.setTabActive('tab-level2_1');
				 				step_tabbar_sep.setTabActive('tab-level2_2_1')
				 				";			
			}
			else
			{
				// Step by step
				$tab_actif = "	a_tabbar.setTabActive('tab-level1');
			 					head_tabbar.setTabActive('tab-level1_1');
								tabbar_step.setTabActive('tab-level2_1');
			 					step_tabbar_sep.setTabActive('tab-level2_2_1')
			 					
			 					a_tabbar.setTabActive('tab-level2');
			 					tabbar_step.setTabActive('tab-level2_2')
			 					step_tabbar_sep.setTabActive('tab-level2_2_".$level3."')
			 					;";			

			}
		}
		//==================================================================	
		
		
		return $tab_actif;		
	}
	/*===================================================================*/  
	
	
	/**
	 * @method string protege les quotes
	 * @param string $texte texte à proteger
	 * @return string texte protegé
	 * @access private
	 */
	private function protect_save($texte)
	{
		$texte = addslashes($texte);
		return $texte;
	}
	
	/**
	 * @method string protege les bornes < et > par &lt; et &gt;
	 * @param string $texte texte à proteger
	 * @return string texte protegé
	 * @access private
	 */
	private function protect_xml($texte)
	{
		$texte = rawurlencode($texte);
	
		return $texte;
	}
	

	/**==================================================================
	* Cancel current modifications
	* Use temporary session id ( $this->c_id_temp ) to clear information
	* @access public
	====================================================================*/ 
	public function cancel_modif()
	{
		//==================================================================
		// Clear iSheet information
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name']."`
				WHERE `id_fiche` = ".$this->c_id_temp.";
			   ";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================

		//==================================================================
		// Clear iSheet parameters information
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name']."`
				WHERE `id_fiche` = ".$this->c_id_temp.";
			   ";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================

		//==================================================================
		// Clear Tag information
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."`
				WHERE `ID` = ".$this->c_id_temp." AND objet = \"ifiche\";
			   ";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
		
		//==================================================================
		// Clear logs informations
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_log_action']['name']."`
				WHERE `ID` = ".$this->c_id_temp."
				AND `objet` = 'ifiche';
				";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
		
		//==================================================================
		// Clear url_temp table
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."`
				WHERE `id_temp` = ".$this->c_id_temp.";
				";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
		
		//==================================================================
		// Clear lock 
		//==================================================================
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name']."`
				WHERE `id_temp`  = ".$this->c_id_temp.";
				";
		
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
		
		//==================================================================
		// Free php session memory 
		//==================================================================
		unset($_SESSION[$this->c_ssid]);
		unset($_SESSION["viewer"][$this->c_ssid]);
		//==================================================================
	}
	/*===================================================================*/  
	
	
	/**
	 * Accesseur de $this->c_Obj_etape->copier_etape($id_etape)
	 */
	public function copier_etape($id_etape)
	{
		$this->c_Obj_etape->copier_etape($id_etape);
	}
	
		
	/**
	 * Accesseur de $this->c_Obj_etape->display_step($appel_ajax);
	 */
	public function display_step($appel_ajax)
	{
		echo $this->c_Obj_etape->display_step($appel_ajax);
	}


	public function liberer_vimofy($id_vimofy)
	{
		unset($_SESSION["viewer"][$this->c_ssid][$id_vimofy]);	
	}
	
	/**
	 * NR_IKNOW_2_
	 * 
	 * @method string génère la requête SQL qui permet d'editer une URL dans une vimofy (calculatrice génératrice de lien)
	 * @param string $ik_cartridge Valeur de ik_cartridge dans le générateur de lien
	 * @param string $first_edit 
	 * @return string Requête SQL
	 * @access public
	 */
	public function generer_url_tiny($p_id,$p_version,$iobject,$ik_cartridge = false,$first_edit = false)
	{
		$sql = 'SELECT 	`nom`,
						`valeur` 
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'` 
				WHERE `id_temp` = '.$this->c_id_temp;
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		switch ($iobject) 
		{
			case '__IFICHE__':
				$url_param = 'ifiche.php?';
				break;
			case '__ICODE__':
				$url_param = 'icode.php?';
				break;
		}
		
		$url_param.= 'ID='.$p_id;
		if($p_version != $_SESSION[$this->c_ssid]['message']['iknow'][504]) // SRX remove max by dynamic text in db
		{
			$url_param.= '&version='.$p_version;
		}

		$ik_cartridge_exist = false;
		
		while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
		{
			$url_param .= '&'.rawurlencode($row['nom']).'='.rawurlencode($row['valeur']);
		}
		
		// XML return	
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<url>".$this->protect_xml($url_param)."</url>";
		echo "<ik_cartridge>".$this->protect_xml($ik_cartridge)."</ik_cartridge>";
		echo "</parent>";
		die();
	}
	
	public function get_ik_cartridge_url_tiny()
	{
		
		$sql = 'SELECT valeur 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].' 
					WHERE id_temp = '.$this->c_id_temp.' 
					AND nom = "IK_CARTRIDGE"';
		
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		if(mysql_num_rows($resultat) > 0)
			return mysql_result($resultat,0,0);
		else
			return 7;
		
	}	
	
	
	
	/**
	 * 
	 * 	Cette methode permet de supprimer les paramètres de l'url stockés dans la base de données
	 * 	pour la génération d'une URL	
	 * 
	 */
	public function supprimer_val_bdd()
	{
		$sql = 'DELETE FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].' WHERE id_temp = '.$this->c_id_temp;
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);		
	}
	
	
	public function recup_contenu_etape($fiche,$version,$etape)
	{
		return $this->c_Obj_etape->recup_contenu_etape($fiche,$version,$etape);
	}
	
	
	public function save_alias($num_etape)
	{
		return $this->c_Obj_etape->save_alias($num_etape);
	}
	
	/**==================================================================
	 * Match local varin without input parameters from called object
	 * $id_dst : id of called iObject
	 * $v_dst : version of called iObject
	 * $type_lien : Kind of iObject ( eg : __IFICHE__ )
	 ====================================================================*/	
	public function rapprocher_var($id_dst,$v_dst,$type_lien)
	{
		if($v_dst == $_SESSION[$this->c_ssid]['message']['iknow'][504])
		{
			// No version means last version used by default
			
			//==================================================================
			// Check if paramters in table url_temp already exist
			//==================================================================
			$sql = "SELECT 
						COUNT(`id_temp`) 
					FROM 
						`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."` 
					WHERE 1 = 1
						AND `id_temp` = ".$this->c_id_temp;
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$nbr_var_temp = mysql_result($result,0,0);
			//==================================================================
			
			if($type_lien == '__IFICHE__')
			{
				// iSheet
				if($nbr_var_temp == 0)
				{
								 
					$sql = 'INSERT 
							INTO 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(`nom`, `valeur`, `id_temp`) 
							SELECT
								fp2.`nom`,
								CONCAT("$",fp1.`nom`,"()","$"),
								'.$this->c_id_temp.'
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp1,
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp2
							WHERE 1 = 1
								AND fp1.`id_fiche` = '.$this->c_id_temp.'
								AND fp2.`id_fiche` = '.$id_dst.'
								AND fp2.`num_version` = (	SELECT 
																`num_version`
															FROM 
																`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
															WHERE 1 = 1
																AND `id_fiche` = '.$id_dst.'
														)
								AND fp1.`nom` = fp2.`nom`
								AND fp1.`type` = fp2.`type`
								AND fp1.`type` = "IN"
								AND fp1.`nom` IN (	SELECT `nom` 
													FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
													WHERE `id_fiche` = '.$id_dst.' 
													AND `num_version` = (	SELECT 
																				`num_version`
																			FROM 
																				`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
																			WHERE 1 = 1
																				AND `id_fiche` = '.$id_dst.'
																		) 
													AND `TYPE` = "IN");
							   ';

				}
				else
				{

					$sql = 'INSERT INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(`nom`, `valeur`, `id_temp`) 
							SELECT
								fp2.`nom`,
								CONCAT("$",fp1.`nom`,"()","$"),'.$this->c_id_temp.'
							FROM 
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp1,
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp2
							WHERE 1 = 1
								AND fp1.`id_fiche` = '.$this->c_id_temp.'
								AND fp2.`id_fiche` = '.$id_dst.'
								AND fp2.`num_version` = (	SELECT 
																`num_version`
															FROM 
																`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
															WHERE 1 = 1
																AND `id_fiche` = '.$id_dst.'
														)
								AND fp1.`nom` = fp2.`nom`
								AND fp1.`TYPE` = fp2.`type`
								AND fp1.`TYPE` = "IN"
								AND fp1.`nom` IN (
												SELECT
													`nom` 
												FROM
													`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
												WHERE 1 = 1
													AND `id_fiche` = '.$id_dst.' 
													AND `num_version` = (	SELECT 
																			`num_version`
																		FROM 
																			`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'`
																		WHERE 1 = 1
																			AND `id_fiche` = '.$id_dst.'
																	) 
													AND `TYPE` = "IN"
											 )
								AND fp1.`nom` NOT IN (	
														SELECT
															`nom` 
														FROM
															`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'` 
														WHERE 1 = 1
															AND `id_temp` = '.$this->c_id_temp.'
												 )
						  ';
					
					

				}
			}
			else
			{
				// iCode
				if($nbr_var_temp == 0)
				{
					// Match varin varout without temp var
					$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'(nom, valeur,id_temp) 
							SELECT
								rp1.`nom`,
								CONCAT("$",fp1.`nom`,"()","$"),
								'.$this->c_id_temp.' 
							FROM
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp1,
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` rp1,
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` maxcode
							WHERE 1 = 1 
								AND maxcode.`ID` = rp1.`ID`
								AND maxcode.`Version` = rp1.`Version`
								AND fp1.`id_fiche` = '.$this->c_id_temp.' 
								AND fp1.`num_version` = '.$this->c_version.' 
								AND rp1.`ID` = '.$id_dst.'
								AND rp1.`nom` = fp1.`nom`
								AND fp1.`TYPE` = "IN" 
								';
				}
				else
				{
					// Match varin varout with temp var
					$sql = 'INSERT 
							INTO 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(nom, valeur,id_temp) 
							SELECT 
								rp1.`nom`,
								CONCAT("$",fp1.nom,"()","$"),
								'.$this->c_id_temp.' 
							FROM
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` fp1,
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` rp1,
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` maxcode
							WHERE 1 = 1 
								AND maxcode.`ID` = rp1.`ID`
								AND maxcode.`Version` = rp1.`Version`
								AND fp1.`id_fiche` = '.$this->c_id_temp.' 
								AND rp1.`ID` = '.$id_dst.'
								AND rp1.`nom` = fp1.`nom` 
								AND fp1.`TYPE` = "IN" 
								AND fp1.`nom` NOT IN (	SELECT 
															`nom` 
														FROM 
															`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'` 
														WHERE 1 = 1
															ANd `id_temp` = '.$this->c_id_temp.')';						
			
				}
			}
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		else
		{
			// Version exists
			
			//==================================================================
			// Check if paramters in table url_temp already exist
			//==================================================================
			$sql = "SELECT 
						COUNT(`id_temp`)
					FROM
						`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."`
					WHERE 1 = 1
						AND `id_temp` = ".$this->c_id_temp.";
				   ";
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$nbr_var_temp = mysql_result($result,0);
			//==================================================================
			
			if($type_lien == '__IFICHE__')
			{
				if($nbr_var_temp == 0)
				{
					// iSheet without temporary variables
					
					$sql = 'INSERT 
							INTO 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(`nom`, `valeur`, `id_temp`) 
							SELECT 
								`nom`,
								CONCAT( "$",`nom`, \'()\',"$" ),
								'.$this->c_id_temp.'
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
							WHERE 1 = 1
								AND `id_fiche` = '.$this->c_id_temp.'
								AND `num_version` = '.$this->c_version.'
								AND `type` = "IN"
								AND `nom` IN (
												SELECT
													`nom` 
												FROM
													`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
												WHERE 1 = 1
													AND `id_fiche` = '.$id_dst.'
													AND `num_version` = '.$v_dst.'
													AND `type` = "IN"
											 )
						  ';
				}
				else
				{
					// iSheet with temporary variables
					
					$sql = 'INSERT
							INTO 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(nom, valeur,id_temp) 
							SELECT 
								`nom`,
								CONCAT( "$",nom, \'()\',"$" ),
								'.$this->c_id_temp.' 
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
							WHERE 1 = 1
								AND `id_fiche` = '.$this->c_id_temp.' 
								AND `num_version` = '.$this->c_version.' 
								AND `type` = "IN" 
								AND `nom` IN (
												SELECT 
													`nom` 
												FROM
													`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
												WHERE 1 = 1
													AND `id_fiche` = '.$id_dst.' 
													AND `num_version` = '.$v_dst.' 
													AND `type` = "IN"
											) 
								AND `nom` NOT IN (
													SELECT 
														`nom` 
													FROM
														`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'` 
													WHERE 1 = 1
														AND `id_temp` = '.$this->c_id_temp.')';

				}
			}
			else
			{
				// iCode with version number
				if($nbr_var_temp == 0)
				{
					// iCode without temporary variables
					
					$sql = 'INSERT 
							INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(nom, valeur,id_temp)
							SELECT 
								`nom`,
								CONCAT( "$",`nom`, \'()\',"$" ),
								'.$this->c_id_temp.'
								FROM
									`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'`
								WHERE 1 = 1
									AND `id_fiche` = '.$this->c_id_temp.'
									AND `num_version` = '.$this->c_version.'
									AND `type` = "IN"
									AND `nom` IN (
													SELECT
														`nom`
													FROM
														`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
													WHERE 1 = 1
														AND `ID` = '.$id_dst.'
														AND `Version` = '.$v_dst.'
														AND `TYPE` = "IN"
												 )
						 ';
									
				}
				else
				{
					// iCode with temporary variables
					
					$sql = 'INSERT 
							INTO
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'`(nom, valeur,id_temp) 
							SELECT
								`nom`,
								CONCAT("$", `nom`, \'()\' ,"$"),
								'.$this->c_id_temp.' 
							FROM 
								`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'` 
							WHERE 1 = 1
								AND `id_fiche` = '.$this->c_id_temp.' 
								AND `num_version` = '.$this->c_version.' 
								AND `type` = "IN" 
								AND `nom` IN (
												SELECT
													`nom` 
												FROM 
													`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` 
												WHERE 1 = 1
													AND `ID` = '.$id_dst.' 
													AND `Version` = '.$v_dst.' 
													AND `TYPE` = "IN"
											) 
								AND `nom` NOT IN (
													SELECT
														`nom` 
													FROM
														`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name'].'` 
													WHERE 1 = 1
														AND `id_temp` = '.$this->c_id_temp.'
												)
						';						
			
				}
			}
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
	}
	/*===================================================================*/	

	
	public function set_ik_valmod_global($p_value)
	{
		$this->c_ik_valmod = $p_value;
		$this->c_Obj_entete->set_ik_valmod($this->c_ik_valmod);
		$this->c_Obj_etape->set_ik_valmod($this->c_ik_valmod);
	}
	
	public function set_default_values()
	{
		if($this->c_ik_valmod == 2)
		{
			$this->set_ik_valmod_global(3);
		}
		else
		{
			$this->set_ik_valmod_global(1);
		}
	}
	
	public function unset_default_values()
	{
		if($this->c_ik_valmod == 3)
		{
			$this->set_ik_valmod_global(2);
		}
		else
		{
			$this->set_ik_valmod_global(0);
		}
	}
	
	public function set_neutral_values()
	{
		if($this->c_ik_valmod == 1)
		{
			$this->set_ik_valmod_global(3);
		}
		else
		{
			$this->set_ik_valmod_global(2);
		}
	}
	
	public function unset_neutral_values()
	{
		if($this->c_ik_valmod == 3)
		{
			$this->set_ik_valmod_global(1);
		}
		else
		{
			$this->set_ik_valmod_global(0);
		}
	}
	
	public function set_default_neutre_value($type,$id)
	{
		$this->c_Obj_entete->set_default_neutre_value($type,$id);
	}
	
	
	/**==================================================================
	 * Under construction
	 ====================================================================*/	
	public function valide_message_maintenance()
	{
		//$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_msg_maintenance']['name'].' SET STATUS = "READ" WHERE ID_TEMP = '.$this->c_id_temp;
		//$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	}
	/*===================================================================*/	
		
	
	/**==================================================================
	 * Method : Remove temporary tags of step
	 * @public 
	 ====================================================================*/	
	public function delete_tags_temp($id_etape)
	{
		$this->c_Obj_etape->delete_tags_temp($id_etape);
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Read the authorization level
	 * Remenber to use specific connector for password database schema
	 ====================================================================*/	
	public function get_niveau_password($id)
	{
		$sql = 'SELECT `level` 
				FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_password']['name'].'` 
				WHERE `id` = '.mysql_escape_string($id);
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link_password);
		
		if(mysql_num_rows($resultat) > 0)
		{
			$niveau = mysql_result($resultat,0,'level');
			$erreur = 'false';
			$niveau =  $this->protect_xml($_SESSION[$this->c_ssid]['message'][52].' '.$niveau);
		}
		else
		{
			$erreur = 'true';
			$niveau =  $this->protect_xml($_SESSION[$this->c_ssid]['message'][216]);
		}
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<erreur>".$erreur."</erreur>";
		echo "<niveau>".$niveau."</niveau>";
		echo "</parent>";
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Check if this version is locked for update
	 ====================================================================*/	
	public function is_obsolete()
	{
		if($this->c_version != 'new' && $this->c_id != 'new')
		{	
			$sql = 'SELECT `obsolete`
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'` 
					WHERE `id_fiche` = '.$this->c_id;
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);		
			
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
			{			
				if($row['obsolete'] == 0)
				{
					$this->c_obsolete = 0;
					return 0;
				}
				else
				{
					$this->c_obsolete = 1;
					return 1;
				}	
			}	
		}		
	}
	/*===================================================================*/	
	
	
	public function get_flag_obsolete()
	{
		return $this->c_obsolete;
	}
	
	public function get_libelle_pole($id_pole)
	{
		$sql = 'SELECT `Libelle` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles']['name'].'` WHERE `ID` = "'.$id_pole.'"';
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return mysql_result($resultat,0,'Libelle');
	}
	
	public function get_libelle_activite($id_activite,$id_pole)
	{
		$sql = 'SELECT `Libelle` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles_themes']['name'].'` WHERE `ID` = "'.$id_activite.'" AND ID_POLE = "'.$id_pole.'"';
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		return mysql_result($resultat,0,'Libelle');
	}
	
	
	/**==================================================================
	 * Get label of work and subwork module
	 ====================================================================*/	
	public function get_libelle_module($id_module,$id_pole)
	{
		$sql = 'SELECT
					MODULE.`ID` AS `ID`,
					MODULE.`libelle` AS `mod_libelle`,
					METIER.`libelle` AS `met_libelle`,
					MODULE.`id_pole` AS `id_pole`
				FROM
					`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_modules']['name'].'` as MODULE,
					`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_metiers']['name'].'` as METIER 
				WHERE 1 = 1
					AND	MODULE.`id_POLE` = "'.$id_pole.'"
					AND MODULE.`ID_METIER` = METIER.`ID`
					AND METIER.`id_POLE` = "'.$id_pole.'" 
					AND MODULE.`ID` = "'.$id_module.'" 
				ORDER BY METIER.`libelle`, MODULE.`libelle`, MODULE.`ID`';
		
		$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
			return $row["ID"].' - '.$row["met_libelle"].' - '.$row["mod_libelle"];
		}
	}
	/*===================================================================*/	
	
	
	public function sauvegarder_variables_etape($id_etape)
	{
		$this->c_Obj_etape->sauvegarder_variables_etape($id_etape);
	}

	public function cancel_modif_etape($id_etape)
	{
		$this->c_Obj_etape->cancel_modif_etape($id_etape);
	}

	public function sauvegarder_tags_etape($id_etape)
	{
		$this->c_Obj_etape->sauvegarder_tags_etape($id_etape);
	}
	
	public function cancel_modif_tags($id_etape)
	{
		return $this->c_Obj_etape->cancel_modif_tags($id_etape);
	}

	public function get_id_temp()
	{
		return $this->c_id_temp;
	}

	public function get_ik_valmod()
	{
		return $this->c_ik_valmod;
	}
		
	public function get_id_pole()
	{
		return $this->c_Obj_entete->get_id_pole();
	}
	
	public function get_pole_version()
	{
		return $this->c_Obj_entete->get_pole_version();
	}	
	
	public function get_id_activite()
	{
		return $this->c_Obj_entete->get_id_activite();
	}	

	public function get_module()
	{
		return $this->c_Obj_entete->get_module();
	}		
	
	public function editer_etape($id_etape)
	{
		return $this->c_Obj_etape->editer_etape($id_etape);
	}
	
	public function editer_prerequis()
	{
		return $this->c_Obj_entete->get_prerequis();
	}
	
	public function editer_description()
	{
		return $this->c_Obj_entete->get_description();
	}
	
	public function backup_prerequis($contenu)
	{
		return $this->c_Obj_entete->backup_prerequis($contenu);
	}
	public function backup_prerequisite_raw_text($contenu)
	{
		return $this->c_Obj_entete->backup_prerequisite_raw_text($contenu);
	}
	
	public function backup_description($contenu)
	{
		return $this->c_Obj_entete->backup_description($contenu);
	}
	
	public function backup_description_raw_text($contenu)
	{
		return $this->c_Obj_entete->backup_description_raw_text($contenu);
	}
	
	public function annuler_modif_prerequis()
	{
		return $this->c_Obj_entete->annuler_modif_prerequis();	
	}

	public function annuler_modif_description()
	{
		return $this->c_Obj_entete->annuler_modif_description();	
	}
	
	public function purge_tag($temp)
	{
		return $this->c_Obj_etape->purge_tag($temp);	
	}
	
	public function get_nbr_tag_etape($id_etape)
	{
		$sql = 'SELECT COUNT(1) 
				FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
				WHERE ID = '.$this->c_id_temp.' 
				AND Etape = '.$id_etape;
		
		$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		$nbr_tag = mysql_result($requete,0,0);
			
		if($nbr_tag > 0)
		{
			$popup = '<popup>'.$this->protect_xml('<div class="tag" '.$this->c_Obj_etape->generer_commentaire_tag($id_etape).' '.$this->c_Obj_etape->generer_texte_aide(61,240).' onclick="vimofy_tag_etape('.$id_etape.',true);"></div>').'</popup>';
		}
		else
		{
			$popup = '<popup>'.$this->protect_xml('<div class="no_tag" '.$this->c_Obj_etape->generer_texte_aide(61,240).' onclick="vimofy_tag_etape('.$id_etape.',true);"></div>').'</popup>';
		}
		
		// XML return	
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<nbr_tag>".$nbr_tag."</nbr_tag>";
		echo $popup;
		echo "</parent>";
	}
	
	public function get_type_gestion_date()
	{
		return $this->c_Obj_entete->get_type_gestion_date();
	}
	
	public function copy_var_ext_and_tag()
	{
		$this->c_Obj_etape->copy_var_ext_and_tag();
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
			$href = 'href="#" onclick="javascript:'.$action_click.'"';
		}
		else
		{
			$href = 'href="#'.$ancre.'" onclick="javascript:'.$action_click.'"';
		}
		
		return '<tr><td><a '.$href.' class="'.$class_erreur.'"></a></td><td>&nbsp;'.$message.'</td></tr>';
	}
	
	
	public function convertBBCodetoHTML($txt)
	{
		$remplacement=true;
		while($remplacement)
		{
			$remplacement=false;
			$oldtxt=$txt;
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
			
			if ($oldtxt<>$txt)
			{
				$remplacement=true;
			}
		}
		return $txt;
		
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
		
		$texte = $this->c_Obj_etape->update_balises($texte);

		return $texte;	
		
	}
	
	public function init_vimofy_cartouche_infos($id_cartouche,$id_etape)
	{
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		$query = $this->c_Obj_etape->init_vimofy_cartouche_infos($id_cartouche,$id_etape);
		require('../../includes/ifiche/vimofy/init_list_cartridge_infos.php');
		
		$style = $obj_vimofy_cartridge_infos->vimofy_generate_header(true);
		$vim = '<div style="margin:-8px;height:250px;position:relative;">'.$obj_vimofy_cartridge_infos->generate_vimofy().'</div>';
		$js = $obj_vimofy_cartridge_infos->vimofy_generate_js_body(true);
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($vim)."</vimofy>";
		echo "<json>".rawurlencode($js)."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";
	}
	
	public function init_vimofy_cartouche_param($id_cartouche,$id_etape)
	{
		$ssid = $this->c_ssid;
		$dir_obj = '../../vimofy/';
		$query = $this->c_Obj_etape->init_vimofy_cartouche_param($id_cartouche,$id_etape);
		$type_app = $this->c_type;
		require('../../includes/ifiche/vimofy/init_list_cartridge_param.php');
		
		$style = $obj_vimofy_cartridge_param->vimofy_generate_header(true);
		$vim = '<div style="margin:-8px;height:250px;position:relative;">'.$obj_vimofy_cartridge_param->generate_vimofy().'</div>';
		$js = $obj_vimofy_cartridge_param->vimofy_generate_js_body(true);
		
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<vimofy>".rawurlencode($vim)."</vimofy>";
		echo "<json>".rawurlencode($js)."</json>";
		echo "<css>".rawurlencode($style)."</css>";
		echo "</parent>";
	}

	public function maj_nbr_param($objet)
	{
		switch($objet)
		{
			case 'vimofy_liste_param':
				$sql = 'SELECT COUNT(1) as total 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND num_version = '.$this->c_version.' 
						AND TYPE = "IN"';
				break;
			case 'vimofy_lst_tag_objassoc':
				$sql = 'SELECT COUNT(1) as total FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->c_id_temp;
				break;	
			case 'vimofy_infos_recuperees':
				$sql = 'SELECT COUNT(1) as total 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' 
						WHERE id_fiche = '.$this->c_id_temp.' 
						AND num_version = '.$this->c_version.' 
						AND ((TYPE = "EXTERNE" AND used = 1) OR TYPE = "OUT") 
						';
				break;
			default:
				return 0;
			break;
		}
		
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$valeur = mysql_result($resultat, 0,'total');
		return $valeur;
		
	}
	
	public function get_type()
	{
		return $this->c_type;
	}
	
	/**
	 * NR_IKNOW_10_
	 */
	public function purge_cookie()
	{
		$this->c_Obj_lock->purge_cookie();
	}
	
	public function check_global_coherence_end()
	{
		return 'var ajax_json = '.json_encode(Array('end_check' => $this->c_global_coherent_check_end,'qtt_err' => $this->c_global_coherent_check_qtt_err,'ssid_object_check' => $this->c_global_coherent_check_ssid,'type_object' => '__IFICHE__','id_object' => $this->c_id)).';';
	}
	
	public function get_global_coherent_check_end()
	{
		return $this->c_global_coherent_check_end;
	}
	
	public function get_global_coherent_check_qtt_err()
	{
		return $this->c_global_coherent_check_qtt_err;
	}
	
	public function set_global_coherent_check_end($status,$qtt_err,$ssid_object_check)
	{
		$this->c_global_coherent_check_end = $status;
		$this->c_global_coherent_check_qtt_err = $qtt_err;
		$this->c_global_coherent_check_ssid = $ssid_object_check;
	}
	
	public function get_global_coherent_check_ssid()
	{
		return $this->c_global_coherent_check_ssid;
	}
}	
?>