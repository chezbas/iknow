<?php
	/**===========================================================================================================
 	* 								Main class to generate iCode object
 	============================================================================================================*/	
	class icode extends class_bdd
	{
		private $c_id;								// Contient l'id du code en cours.
		private $c_version;							// Contient la version du code en cours.
		private $c_ssid;							// Contient le ssid du code en cours.
		private $c_user;							// Contient l'adresse ip de l'utilisateur qui modifie le code.
		private $c_includes;						// Contient le lien relatif vers le dossier includes
		private $c_id_temp;							// Contient l'id temporaire du code en cours (<=99999)
		private $c_language;						// Contient la langue du système
		private $c_type;							// Type d'appli, 3 pour la modification d'un code, 4 pour la visualisation	
		private $c_reload;							// 1 pour la modification d'un code, 2 pour la visualisation
		private $c_dataset;							// Contient le dataset du code (id,version,corps...)
		private $c_Obj_lock;						// Contient l'objet de lock de la fiche
		private $c_level_erreur;					// Niveau d'erreur du code: 0 INFORMATION,1 WARNING,2 ERREUR
		private $c_ik_valmod;						// Type de valorisation, DEFAUT, NEUTRE, DEFAUT ET NEUTRE, NORMAL			
		private $c_obsolete;						// flag de bloquage de la fiche, si à 1 alors la fiche est bloquée en modification (obsolete) si à 0 la fiche c'est pas bloquée.
		private $c_global_coherent_check_end;
		private $c_global_coherent_check_qtt_err;
		private $c_global_coherent_check_ssid;
		
		/**==================================================================
	 	* iCode constructor
	 	* @param integer $p_id : iCode ID
	 	* @param string $p_ssid : Instance one identifier ( ssid )
	 	* @param string $p_user : Ip user adress
	 	* @param integer $p_type : 	"3" means update mode
	 	* 							"4" means display mode
	 	* @param string $p_includes : Current application version
	 	* @param integer $p_version : iCode version
	 	* @param string $language : language to use
	 	* @access public
	 	====================================================================*/	
		public function __construct($p_id,$p_ssid,$p_user,$p_type,$p_includes,$p_version,$language) 
		{
			// Call the parent constructor
			parent::__construct($p_ssid,$p_id,0,$p_version,$p_type);
			
			// Database connexion
			$this->db_connexion();
				
			
			/**==================================================================
		 	* Initialise attributs
		 	====================================================================*/	
			$this->c_id = $p_id;
			$this->c_ssid = $p_ssid;
			$this->c_user = $p_user;
			$this->c_type = $p_type;
			$this->c_includes = $p_includes;
			$this->c_version = $p_version;
			$this->c_language = $language;
			$this->c_reload = false;
			$this->c_level_erreur = 0;
			$this->c_global_coherent_check_end = false;
			$this->c_global_coherent_check_qtt_err = 0;
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check code existance and obsolet
		 	====================================================================*/	
			if($p_id != 'new')
			{
				$this->existe_code($p_version);
			}
			
			if($p_id != 'new')
			{
				$this->is_obsolete();
			}
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Get valorisation type
		 	====================================================================*/	
			if(isset($_GET['IK_VALMOD']))
			{
				$this->c_ik_valmod = $_GET['IK_VALMOD'];
			}
			else
			{
				if(!is_numeric($this->c_ik_valmod))
				{
					$this->c_ik_valmod = 3;
				}
			}
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Define code version
		 	====================================================================*/	
			if($p_version != '')
			{
				if($p_version == 'new')
				{
					// Nouveau code
					$this->c_version = 0;
				}
				else
				{
					// Version définie dans l'URL
					$this->c_version = $p_version;
				}
			}
			else
			{
				// No version specified, set last version to internal
				$this->set_max_version();
			}		
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check if the code is obsolet
		 	====================================================================*/	
			if($this->c_type == 3 && $this->c_id != 'new' && $this->is_obsolete() == 1)
			{
				echo "<script language=\"javascript\">window.location.replace('./icode.php?ID=".$this->c_id."&version=".$this->c_version."');</script>";	
				die();	
			}
			/*===================================================================*/
	
			
			/**==================================================================
		 	* Manage lock and temporary ID
		 	====================================================================*/	
			// Instanciation de l'objet lock
			$this->c_Obj_lock = new lock_iobjet($this->c_type,$this->c_id,$this->c_ssid,$this->c_user);
	
			// Generation d'un id temporaire
			$this->c_id_temp = $this->c_Obj_lock->generer_id_temporaire();
			
			// Mise à jour de l'id temporaire dans la classe mère
			$this->set_id_temp($this->c_id_temp);
			/*===================================================================*/
			
			
			// Purge des objets temporaire dans la base
			$this->purge_base();
			
			// Copie des tags et des paramètres sur l'ID temporaire
			$this->copy_var_and_tag();

			// Génération du contenu du icode
			$this->get_contenu_icode();	

			// Set param values
			$this->set_varin_values();
		}
		/*===================================================================*/

		
		/**==================================================================
		 * Database reconnexion after deserialization
		 ====================================================================*/
		public function __wakeup()
		{
			// Reconnexion aux bases de données
			$this->db_connexion();
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * Set the value of the variables in database
		 * @access private
		====================================================================*/
		private function set_varin_values()
		{
			/**==================================================================
			 * Get varin values
			====================================================================*/
			$sql = 'SELECT `nom`,
					`IDP` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` 
					WHERE `ID` = '.$this->c_id_temp.' 
					AND `TYPE` = "IN" 
					AND `version` = '.$this->c_version;
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			/*===================================================================*/

			/**==================================================================
			 * Check if varin was defined in the URL
			====================================================================*/
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				$row['nom'] = (html_entity_decode($row['nom'],ENT_NOQUOTES,'UTF-8'));
				
				if(isset($_GET[$row['nom']]))
				{
					if($_GET[$row['nom']] == '')
					{
						$w_result = 'NULL';
					}
					else
					{
						$w_result = '"'.$this->protect_display($_GET[$row['nom']]).'"';
					}
				
					$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
							SET `resultat` = '.$w_result.'
							WHERE `ID` = '.$this->c_id_temp.'
							AND `IDP` = '.$row['IDP'].'
							AND `TYPE` = "IN"
							AND `version` = '.$this->c_version.'
							AND `ID` >= 99999';

					$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				}
			}
			/*===================================================================*/
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * @method boolean : Check if called iCode is define
		 * @return boolean : Version : 	true means exist
		 * 								false means don't exist
		 * @access private
		====================================================================*/
		private function existe_code($p_version) 
		{
			if(is_numeric($this->c_id) && strpos($this->c_id,'-') === false && strpos($this->c_id,'.') === false) // SIBY_ID_NOT_POSITIF_INTEGER
			{
				$sql = 'SELECT
							1
						FROM
							`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` 
						WHERE 1 = 1
							AND `id` = '.$this->c_id.';
					   ';
				$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
				if(mysql_num_rows($result) == 0)
				{
					// iCode id doesn't exist
					// Display an user error message
					$l_message = str_replace("'","\'",str_replace('&1',$this->c_id,$_SESSION[$this->c_ssid]['message'][84]));
					echo '<body style="background-color: #1A67B7;">
							<div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div>
							<script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(\''.$l_message.'\'),\'erreur\',\'msg\',false,true);</script></body>';
					die();
				}
				else
				{
					if($p_version != '' && is_numeric($p_version))
					{
						$sql = 'SELECT
									`id` 
								FROM
									`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` 
								WHERE 1 = 1
									ANd `id` = '.$this->c_id.' 
									AND `version` = '.$p_version;
		
						$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
						if(mysql_num_rows($result) == 0)
						{
							// iCode version doesn't exist
							// Display an user error message
							echo '<body style="background-color: #1A67B7;">
							<div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div>
							<script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(libelle[85]),\'erreur\',\'msg\',false,true);</script></body>';
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
							echo '<body style="background-color: #1A67B7;">
								<div id="iknow_msgbox_background"></div>
								<div id="iknow_msgbox_conteneur" style="display:none;"></div>
								<script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(libelle[109]).replace(\'$version\',\'<b>'.$p_version.'</b>\'),\'erreur\',\'msg\',false,true);</script></body>';
								die();
						}
					}
				}
			}
			else
			{
				// ID not valid
					echo '<body style="background-color: #1A67B7;">
							<div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div>
							<script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(libelle[110]).replace(\'$id\',\'<b>'.$this->c_id.'</b>\'),\'erreur\',\'msg\',false,true);</script></body>';
							die();
			}
		}
		/*===================================================================*/

		
		/**==================================================================
		 * @method to get current total of input / output parameters or / tags list
		 * @ $objet : string :
		 * 						'vimofy_liste_param' means total of input parameters
		 * 						'vimofy_infos_recuperees' means total of output parameters
		 * 						'vimofy_lst_tag_objassoc' means total of tags
		 * @ Access public
		====================================================================*/
		public function maj_nbr_param($objet)
		{
			switch($objet)
			{
				case 'vimofy_liste_param':
					$sql = 'SELECT COUNT(1) as total 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` 
							WHERE `ID` = '.$this->c_id_temp.' 
							AND `Version` = '.$this->c_version.' 
							AND `TYPE` = "IN"';
					break;
				case 'vimofy_lst_tag_objassoc':
					$sql = 'SELECT COUNT(1) as total
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'`
							WHERE `ID` = '.$this->c_id_temp;
					break;	
				case 'vimofy_infos_recuperees':
					$sql = 'SELECT COUNT(1) as total 
							FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` 
							WHERE `ID` = '.$this->c_id_temp.' 
							AND `Version` = '.$this->c_version.' 
							AND `TYPE` = "OUT" 
							';
					break;
				default:
					return 0;
				break;
			}
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$valeur = mysql_result($result, 0,'total');
			
			return $valeur;
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * Main methode to generate iCode
		====================================================================*/
		public function generer_icode()
		{
			/**==================================================================
			 * Compute total of input, output parameters and tags
			====================================================================*/
			$s_nbr_varin = ' (<span id="onglet_nbr_varin">'.$this->maj_nbr_param('vimofy_liste_param').'</span>)';
			
			$s_nbr_varout = ' (<span id="onglet_nbr_varout">'.$this->maj_nbr_param('vimofy_infos_recuperees').'</span>)';
			
			$s_nbr_tags = ' (<span id="nbr_tag">'.$this->maj_nbr_param('vimofy_lst_tag_objassoc').'</span>)';
			/*===================================================================*/

			/**==================================================================
			 * Generate tab
			====================================================================*/
			if($this->c_type == 4)
			{
				// Display mode
				$s_tab = 'var a_tabbar = new iknow_tab(\'a_tabbar\');
								a_tabbar.addTab("tab-level1","<div class=\"onglet_icn_general\"></div>'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][37].'</span>').'","'.rawurlencode($this->generer_contenu_general()).'","set_tabbar_actif(\'tab-level1\');charger_var_dans_url(true);");
								a_tabbar.addTab("tab-level2","<div class=\"onglet_icn_code\"></div>'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][36].'</span>').'","'.rawurlencode($this->generer_contenu_corps()).'","set_tabbar_actif(\'tab-level2\');charger_var_dans_url(true);");
								a_tabbar.addTab("tab-level3","<div class=\"onglet_icn_varin\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][35].$s_nbr_varin.'</span>').'</div>","'.rawurlencode($this->generate_tab_varin()).'","set_tabbar_actif(\'tab-level3\');charger_var_dans_url(true);");
								a_tabbar.addTab("tab-level4","<div class=\"onglet_icn_varout\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][38].$s_nbr_varout.'</span>').'</div>","'.rawurlencode($this->generer_contenu_varout()).'","set_tabbar_actif(\'tab-level4\');charger_var_dans_url(true);");
								a_tabbar.addTab("tab-level5","<div class=\"onglet_icn_tag\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][39].$s_nbr_tags.'</span>').'</div>","'.rawurlencode($this->generer_contenu_tags()).'","set_tabbar_actif(\'tab-level5\');charger_var_dans_url(true);");';
			}
			else
			{
				// Update mode
				$action = 'maj_nbr_param(\\\'vimofy_liste_param\\\');maj_nbr_param(\\\'vimofy_lst_tag_objassoc\\\');maj_nbr_param(\\\'vimofy_infos_recuperees\\\');';
				$s_tab = 'var a_tabbar = new iknow_tab(\'a_tabbar\');
								a_tabbar.addTab("tab-level1","<div class=\"onglet_icn_general\"></div>'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][37].'</span>').'","'.rawurlencode($this->generer_contenu_general()).'",\''.$action.'\');
								a_tabbar.addTab("tab-level2","<div class=\"onglet_icn_code\"></div>'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][36].'</span>').'","'.rawurlencode($this->generer_contenu_corps()).'",\''.$action.'\');
								a_tabbar.addTab("tab-level3","<div class=\"onglet_icn_varin\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][35].$s_nbr_varin.'</span>').'</div>","'.rawurlencode($this->generate_tab_varin()).'",\''.$action.'\');
								a_tabbar.addTab("tab-level4","<div class=\"onglet_icn_varout\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][38].$s_nbr_varout.'</span>').'</div>","'.rawurlencode($this->generer_contenu_varout()).'",\''.$action.'\');
								a_tabbar.addTab("tab-level5","<div class=\"onglet_icn_tag\">'.rawurlencode('<span onmouseover="ikdoc();set_text_help(111);" onmouseout="ikdoc(\'\');unset_text_help();" >'.$_SESSION[$this->c_ssid]['message'][39].$s_nbr_tags.'</span>').'</div>","'.rawurlencode($this->generer_contenu_tags()).'",\''.$action.'\');';
			}
			/*===================================================================*/
						
			return $s_tab;
		}
		/*===================================================================*/
		
		
		/**==================================================================
		 * Generate raw text valorised body
		 * Values come from url
		====================================================================*/
		public function generer_icode_light()
		{
			$sql = 'SELECT 
						`nom`,
						`IDP`
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
					WHERE `ID` = '.$this->c_id_temp.'
					AND `TYPE` = "IN"
					AND `version` = '.$this->c_version;
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			// Browse input parameters
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				
				// Check if input parameters is valorised in url
				if(isset($_GET[$row['nom']]))
				{	
					$resultat_var = $this->protect_display($_GET[$row['nom']]);
				}
				else
				{
					$resultat_var = '';
				}
				
				$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` 
						SET `resultat` = "'.$resultat_var.'" 
						WHERE `ID` = '.$this->c_id_temp.' 
						AND `IDP` = '.$row['IDP'].' 
						AND `TYPE` = "IN" 
						AND `version` = '.$this->c_version.' 
						AND `ID` >= 99999';
				
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			}
			
			// Replace in body valorised input parameters
			$s_corps = $this->replace_var_by_value($this->c_dataset['CORPS']);
			
			/**==================================================================
			 * Clean body
			====================================================================*/
			$s_corps = str_replace('<span class="BBVarin">','',$s_corps);
			$s_corps = str_replace('</span>','',$s_corps);
			$s_corps = str_replace('<p>',' ',$s_corps);
			$s_corps = str_replace('</p>',' ',$s_corps);
			$s_corps = str_replace('<br />',' ',$s_corps);
			$s_corps = str_replace('<br/>',' ',$s_corps);
			/*===================================================================*/
						
			return $s_corps;
		}
		/*===================================================================*/
		
		
		private function get_contenu_icode()
		{
			if($this->c_id == 'new')
			{
				$this->c_dataset['ID'] = '';
				$this->c_dataset['pole'] = '';
				$this->c_dataset['Theme'] = '';
				$this->c_dataset['VGS'] = '';
				$this->c_dataset['AUTEUR'] = '';
				$this->c_dataset['DATE'] = '';
				$this->c_dataset['VERSION'] = '';
				$this->c_dataset['PREFIXE'] = $_SESSION[$this->c_ssid]['configuration'][31];			
				$this->c_dataset['POSTFIXE'] = '';
				if(isset($_GET['typec']))
				{
					$this->c_dataset['TYPEC'] = $_GET['typec'];
				}
				else
				{
					$this->c_dataset['TYPEC'] = '';
				}
				$this->c_dataset['engine_version'] = '';
				$this->c_dataset['CORPS'] = '';
				$this->c_dataset['TITRE'] = '';
				$this->c_dataset['COMMENTAIRES'] = '';
				$this->c_dataset['LAST_UPDATE_USER'] = '';
				$this->c_dataset['last_update_date'] = '';
				$this->c_dataset['date_creation_version'] = date('Y-m-d H:i:s');
			}
			else
			{
				$sql = 'SELECT
						REQ.`ID`,
						REQ.`pole`,
						REQ.`Theme`,
						REQ2.`Last_update_user` AUTEUR,
						DATE_FORMAT(REQ2.`Last_update_date`,"%d/%m/%Y - %H:%i") AS `DATE_CREATED`,
						REQ.`VGS`,
						REQ2.`Last_update_date` as DATE,
						REQ.`VERSION`,
						REQ.`PREFIXE`,
						REQ.`POSTFIXE`,
						REQ.`TYPEC`,
						REQ.`engine_version`,
						REQ.`CORPS`,
						REQ.`TITRE`,
						REQ.`COMMENTAIRES`,
						REQ.`LAST_UPDATE_USER`,
						DATE_FORMAT(REQ.`last_update_date`,"%d/%m/%Y - %H:%i") as `last_update_date`,
						REQ.`last_update_date` as date_creation_version 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` REQ,
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'` REQ2
						WHERE REQ.`ID` = '.$this->c_id.' 
						AND REQ.`ID` = REQ2.`ID`
						AND REQ2.`Version` = 0 -- Get Author
						AND REQ.`Version` = '.$this->c_version;
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
				while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
				{
					foreach ($row as $key => $value)
					{
						if($key == 'PREFIXE' && $value == '')
						{
							$this->c_dataset[$key] = $_SESSION[$this->c_ssid]['configuration'][31];
						}
						else
						{
							if($key == 'TITRE')
							{
								if($this->c_obsolete == 0)
								{
									$this->c_dataset[$key] = $value;
								}	
								else
								{
									$this->c_dataset[$key] = $_SESSION[$this->c_ssid]['message'][75].' '.$value;
								}
							}
							else
							{
								$this->c_dataset[$key] = $value;
							}
						}
					}
				}
			}
		}
	
		/**
		 * Génération de l'onglet général
		 */
		private function generer_contenu_general() 
		{
			$lib = &$_SESSION[$this->c_ssid]['message'];
			$lib_main = &$_SESSION[$this->c_ssid]['message']['iknow'];
			
			if($this->c_type == 4)
			{
				// VISUALISATION
	
				//On recupere les noms des pole, versions, activites et modules
				$sql = 'SELECT libelle 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles']['name'].' 
						WHERE ID = "'.$this->c_dataset['pole'].'"';
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				$s_opt_pole =  mysql_result($resultat,0);
				
				
				$sql = 'SELECT libelle 
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles_themes']['name'].' 
						WHERE ID = "'.$this->c_dataset['Theme'].'" 
						AND ID_POLE = "'.$this->c_dataset['pole'].'"';
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				$s_opt_activite = mysql_result($resultat,0);
	
	
				$s_content_tabbar_general= '<div class="limp icode_header"><table id="onglet_general">
						<tr>
							<td class="limp iknow_gen_title" style="white-space: nowrap;">'.$lib[40].'</td>
							<td class="limp" style="width:100%;">'.$this->convertBBCodetoHTML($this->c_dataset['TITRE']).'</td>
						</tr>
						<tr>
							<td class="lp iknow_gen_title" style="white-space: nowrap;">'.$lib_main[32].'</td>
							<td class="lp">'.$s_opt_pole.'</td>
						</tr>
						<tr>
							<td class="limp iknow_gen_title" style="white-space: nowrap;">'.$lib_main[33].'</td>
							<td class="limp">'.$this->c_dataset['VGS'].'</td>
						</tr>
						<tr>
							<td class="lp iknow_gen_title" style="white-space: nowrap;">'.$lib_main[51].'</td>
							<td class="lp">'.$s_opt_activite.'</td>
						</tr>
						<tr>
							<td class="limp iknow_gen_title" style="white-space: nowrap;">'.$lib[44].'</td>
							<td class="limp"><strong>'.$this->c_dataset['AUTEUR'].'</strong>&nbsp;<span style="font-size: 0.9em;">'.$_SESSION[$this->c_ssid]['message']['iknow'][35].'</span>&nbsp;'.$this->c_dataset['DATE_CREATED'].'</td>
						</tr>
						<tr>
							<td class="lp iknow_gen_title" style="white-space: nowrap;">'.str_replace('$1',$this->c_version,$lib[45]).'</td>
							<td class="lp"><strong>'.$this->c_dataset['LAST_UPDATE_USER'].'</strong>&nbsp;<span style="font-size: 0.9em;">'.$_SESSION[$this->c_ssid]['message']['iknow'][35].'</span>&nbsp;'.$this->c_dataset['last_update_date'].'</td>
						</tr>
						<tr>
							<td colspan=2 class="limp iknow_gen_title" style="white-space: nowrap;">'.$lib[47].'</td>
						</tr>
						<tr>
							<td colspan=2 class="limp">'.$this->c_dataset['COMMENTAIRES'].'</td>
						</tr>
					</table></div>';
	
			}
			else
			{
				// MODIFICATION
				$s_content_tabbar_general= '<div style="height:100%;overflow:auto;" class="limp"><div><table id="onglet_general" style="position:absolute;">
					<tr class="header_line">
						<td class="lp iknow_gen_title">'.$lib[40].'</td>
						<td class="lp"><input class="gradient text" type="text" id="titre" onkeyup="document.getElementById(\'icode_title\').innerHTML = this.value;check_title(this);" style="width:99%" value="'.htmlentities($this->c_dataset['TITRE'],ENT_QUOTES,'UTF-8').'"></input></td>
					</tr>
					<tr class="header_line">
						<td class="limp iknow_gen_title">'.$lib_main[32].'</td>
						<td class="limp"><div style="float: left;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_pole_lmod']->generate_lmod_form().'</div><div id="pole_lib" class="lib_lmod">'.$this->get_libelle_pole($this->c_dataset['pole']).'</div></td>
					</tr>
					<tr class="header_line">
						<td class="lp iknow_gen_title">'.$lib_main[33].'</td>
						<td class="lp"><div id ="vimofy_version_emplacement" style="float:left;"></div><div id="version_lib" class="lib_lmod">'.$this->c_dataset['VGS'].'</div></td>
					</tr>
					<tr class="header_line">
						<td class="limp iknow_gen_title">'.$lib_main[51].'</td>
						<td class="limp"><div id ="vimofy_activite_emplacement" style="float:left;"></div><div id="activite_lib" class="lib_lmod">'.$this->get_libelle_activite($this->c_dataset['Theme'],$this->c_dataset['pole']).'</div></td>
					</tr>
					<tr class="header_line">
						<td class="lp iknow_gen_title">'.$lib[48].'</td>
						<td class="lp"><input class="gradient text" type="text" id="auteur" onkeyup="check_trigramme(this);" size=3 /></td>
					</tr>
					<tr class="header_line">
						<td class="limp iknow_gen_title">'.$lib[49].'</td>
						<td class="limp"><div style="float: left;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_moteur']->generate_lmod_form().'</div><div id="moteur_lib" class="lib_lmod">'.$this->get_moteur().'</div></td>
					</tr>
					<tr class="header_line">
						<td class="lp iknow_gen_title">'.$lib[72].'</td>';
				
					$s_content_tabbar_general .= '<td class="lp"><div id ="vimofy_engine_version_emplacement" style="float: left;"></div><div id="engine_version_lib" class="lib_lmod">'.$this->get_engine_version().'</div></td>';
					
				
					$s_content_tabbar_general .= '</tr>
					<tr>
						<td class="limp iknow_gen_title">'.$lib[47].'</td>
						<td class="limp"><textarea name="Descriptif" rows="10" style="width:100%;height:100%;position: relative;" id="Descriptif" class="Descriptif">'.$this->c_dataset['COMMENTAIRES'].'</textarea></td>
					</tr>
				</table></div>';
	
			}
			
			$s_content_tabbar_general = html_entity_decode($s_content_tabbar_general,ENT_NOQUOTES,'UTF-8');
			
			return $s_content_tabbar_general;
		}
	
		/**
		 * Génère le contenu de l'onglet corps
		 */
		private function generer_contenu_corps()
		{
			if($this->c_type == 4)
			{
				// VISUALISATION
				$s_corps = $this->replace_var_by_value($this->c_dataset['CORPS']);
				$s_corps = str_replace('<p>','',$s_corps);
				$s_corps = str_replace('</p>','',$s_corps);
				$s_corps = str_replace('&','&amp;',$s_corps);
				$s_readonly = 'readonly="readonly"';
			}
			else
			{
				// MODIFICATION
				$s_corps = $this->c_dataset['CORPS'];
				$s_corps = str_replace('&','&amp;',$s_corps);
				$s_readonly = '';
			}
			
			return '<textarea id="textarea_code" class="gradient textarea_code" cols="15" rows="30" '.$s_readonly.'>'.$s_corps.'</textarea>';
		}
	
		public function get_format_var()
		{
			$html = js_protect($_SESSION[$this->c_ssid]['message'][52]).' : </div><div id="rappel_format_var">';
			
			if($this->c_dataset['PREFIXE'] != '')
			{
				$html .= '<span class="prefixe">'.htmlentities($this->c_dataset['PREFIXE']).'</span>';
			}
			$html .= '.......';
			
			if($this->c_dataset['POSTFIXE'] != '')
			{
				$html .= '<span class="postfixe">'.htmlentities($this->c_dataset['POSTFIXE']).'</span>';
			}
			
			return $html;
		}
		
		/**
		 * Génère le contenu de l'onglet paramètres en entrée
		 */
		private function generate_tab_varin() 
		{
			// on recupère toute les variables de version $this->c_version et on les copies avec l'identifiant temporaire
			if($this->c_type == 3)
			{
				$s_barre_outils =  '<div style="height:100%;overflow:auto;background-color:#ffdddd;">
										<div id="var_pre_post_fixe">'.$_SESSION[$this->c_ssid]['message'][50].'
											<input class="gradient" id="prefixe" onchange="javascript:maj_prefixe(this.value);" type="text" size=5 value="'.$this->c_dataset['PREFIXE'].'"/>
											'.$_SESSION[$this->c_ssid]['message'][51].'
											<input class="gradient" id="postfixe" type="text" onchange="javascript:maj_postfixe(this.value);" size=5 value="'.$this->c_dataset['POSTFIXE'].'"/>
										</div>
									</div><div style="width:100%;bottom:0;top:30px;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varin']->generate_vimofy().'</div></div>';
			}
			else
			{
				$s_barre_outils = '<div style="width:100%;bottom:0;top:0px;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varin']->generate_vimofy().'</div></div>';
			}
			
			return $s_barre_outils;
		}
	
		
		/**==================================================================
		 * Private access to generate tab of output values
		 * @method : string : HTML div with list of output values
		 * @return : decimal : return HTML div with list of output values
		 * @access : private
		 ====================================================================*/	
		private function generer_contenu_varout() 
		{
			return '<div style="width:100%;bottom:0;top:0;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varout']->generate_vimofy().'</div></div>';
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Private access to generate tab of tags
		 * @method : string : HTML div with list of tags
		 * @return : decimal : return HTML div with list of tags
		 * @access : private
		 ====================================================================*/	
		private function generer_contenu_tags() 
		{
			return '<div style="width:100%;bottom:0;top:0;position:absolute;">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy2_tags']->generate_vimofy().'</div></div>';
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Public access get internal iCode version
		 * @method : decimal : Get the iCode version
		 * @return : decimal : Value of iCode version
		 * @access : public
		 ====================================================================*/	
		public function get_version()
		{
			return $this->c_version;
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Private access to force internal $this->c_version to last version
		 * @method : decimal : Recover number of the last version
		 * @return : decimal : Contain last version of $this->c_id
		 * @access : private
		 ====================================================================*/	
		private function set_max_version()
		{
			if ($this->c_id == 'new')
			{
				// New iCode, force version 1
				$this->c_version = 1;
			}
			else
			{
				// Recover last version from iCode trigger table
				$sql = 'SELECT `version`
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
						WHERE `ID` = '.$this->c_id;
		
				$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				$this->c_version = mysql_result($result,0,'version');
			}
			// Return last version
			return $this->c_version ;
		}
		/*===================================================================*/	

		
		/**==================================================================
		 * Public method to get the iCode last version
		 * @method : decimal : Recover number of the last version
		 * @return : decimal : Contain last version of $this->c_id
		 * @access : public
		 ====================================================================*/	
		public function get_max_version()
		{
			if ($this->c_id == 'new')
			{
				// Nouveau code, version 1
				return 1;
			}
			else
			{
				// Récupération de la version max
				
				$sql = 'SELECT version
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' 
						WHERE ID = '.$this->c_id;
	
				$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				return mysql_result($result,0,'version');
			}
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Public method to get title in HTML
		 * @method : string : Recover iCode title without BBCODE
		 * @return : string : iCode title without BBCODE
		 * @access : public
		 ====================================================================*/	
		public function get_titre_sans_bbcode()
		{
			return $this->convertBBCodetoHTML($this->c_dataset['TITRE']);
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Public method to get head description
		 * @method : string : Recover iCode description
		 * @return : string : iCode description
		 * @access : public
		 ====================================================================*/	
		public function get_description()
		{
			return $this->c_dataset['COMMENTAIRES'];
		}
		/*===================================================================*/	
		

		/**==================================================================
		 * Public method to get one temporary id
		 * @method : decimal : Recover one temporary id
		 * @return : decimal : return temporary id
		 * @access : public
		 ====================================================================*/	
		public function get_id_temp()
		{
			return $this->c_id_temp;
		}
		/*===================================================================*/	

		
		/**==================================================================
		 * Public method to get iCode label engine
		 * @method : string : Recover iCode label engine
		 * @return : decimal : Return iCode label engine
		 * @access : public
		 ====================================================================*/	
		public function get_moteur()
		{
			if($this->c_id == 'new')
			{
				return '';
			}
			else
			{
				$sql = 'SELECT `Description`
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_moteurs']['name'].'` 
						WHERE `ID` = "'.$this->c_dataset['TYPEC'].'"';
		
				$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				return mysql_result($result,0,'Description');
			}
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Public method to get iCode label version engine
		 * @method : string : iCode label version engine
		 * @return : decimal : Return iCode label version engine
		 * @access : public
		 ====================================================================*/	
		public function get_engine_version()
		{
			return $this->c_dataset['engine_version'];
		}
		/*===================================================================*/	

		
		/**==================================================================
		 * Private method to protect special digit
		 * @return : string : Return protected text
		 * @access : private
		 * $p_texte : Containt text to be protected
		 ====================================================================*/	
		private function protect_display($p_texte)
		{
			$p_texte = str_replace('\\','\\\\',$p_texte);
			$p_texte = str_replace('"','\\"',$p_texte);
			$p_texte = str_replace(chr(13),'',$p_texte);
			$p_texte = str_replace(chr(10),'\n',$p_texte);
	
			return $p_texte;
		}
		/*===================================================================*/	


		/**==================================================================
		 * Public method to get version list
		 * @method : string : HTML list of iCode version
		 * @return : string : Return HTML list of iCode version
		 * @access : public 
		 ====================================================================*/	
		public function genere_liste_version()
		{
			return '<div onmouseover="over(false,9,\'-\',\'X\');" onmouseout="ikdoc(\'\');unset_text_help();" class="lst_change_version">'.$_SESSION['vimofy'][$this->c_ssid]['vimofy_version_code']->generate_lmod_form().'</div>';
		}
		/*===================================================================*/	
		
		
		/**==================================================================
		 * Public method to change iCode version
		 * @method : string : remove current version in url and add the new one
		 * @return : string : Return url with new version
		 * @access : public 
		 ====================================================================*/	
		public function changer_version($p_url,$p_version)
		{
			$p_url = str_replace('version='.$this->c_version,'version='.$p_version,$p_url,$nbr_remplacement);
			if($nbr_remplacement == 0)
			{
				return $p_url.'&version='.$p_version;
			}
			else
			{
				return $p_url;
			}
		}
		/*===================================================================*/	
		
		
		public function reload_icode($p_version)
		{
			if($p_version != '')
			{
				$this->c_version = $p_version;
			}
			//SRX
			// Copie des tags et des paramètres sur l'ID temporaire
			$this->copy_var_and_tag();

			// Génération du contenu du icode
			$this->get_contenu_icode();
			
			// Set param values
			$this->set_varin_values();
		}
		
		/**
		 * Copy var and tag on the temporary ID
		 */
		private function copy_var_and_tag()
		{
			if($this->c_id != 'new')
			{
				/**==================================================================
				 * Copy tags
				 ====================================================================*/	
				$sql = 'REPLACE INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(IdTag,ID,Etape,Version,Tag,Groupe,objet)
							SELECT IdTag, '.$this->c_id_temp.' As ID,Etape,Version,Tag,Groupe,"icode" 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' 
							WHERE ID = "'.$this->c_id.'" 
							AND version = '.$this->c_version." 
							AND objet = \"icode\" 
							AND id_src IS NULL";
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				/*===================================================================*/	
				
				/**==================================================================
				 * Copy vars
				 ====================================================================*/	
				$sql = 'REPLACE INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'(IDP,ID,`TYPE`,Version,NOM,DESCRIPTION,DEFAUT,NEUTRE,COMMENTAIRE,resultat)
							SELECT IDP,'.($this->c_id_temp).' AS ID,`TYPE`,Version,NOM,DESCRIPTION,DEFAUT,NEUTRE,COMMENTAIRE,"" 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
							WHERE ID = "'.$this->c_id.'" 
							AND Version = '.$this->c_version;
				$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				/*===================================================================*/	
			}
		}


		/**
		 * Purge des variables et tags de la base sur l'id temporaire
		 */
		private function purge_base()
		{
			// Suppression des lignes de la table icode_parametres
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name']."` WHERE ID = ".$this->c_id_temp.";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
			// Suppression des lignes de la table tags
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."` WHERE ID = ".$this->c_id_temp." AND objet = \"icode\";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
	
		
		/**
		 * Validation de la lecture des messsage système reçu par l'utilisateur
		 */
		public function valide_message_maintenance()
		{
			//$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_msg_maintenance']['name'].' SET STATUS = "READ" WHERE ID_TEMP = '.$this->c_id_temp;
			//$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
	
	
		/**
		 * @method none definit l'onglet actif
		 * @access public
		 */
		public function set_tab_actif($p_tab_actif)
		{
			$_SESSION[$this->c_ssid]['tab_actif'] = $p_tab_actif;
			echo $_SESSION[$this->c_ssid]['tab_actif'];
		}
		
		
		/**
		 * Récupère l'onglet actif
		 */
		public function get_tab_actif()
		{
			if(isset($_SESSION[$this->c_ssid]['tab_actif']))
			{
				return $_SESSION[$this->c_ssid]['tab_actif'];
			}
			else
			{
				if($this->c_type == 4)
				{
					return 'tab-level2';
				}
				else
				{
					return 'tab-level1';
				}
			}
		}
		
		
		/**
		 * @method string permet d'aller sur l'onglet actif (au rechargement)
		 * @return string code javascript
		 * @access public
		 */
		public function retourne_tab_level($p_level)
		{
			$s_tab_actif = "a_tabbar.setTabActive('".$p_level."');";
	
			return $s_tab_actif;
		}
		
		
		/**
		 * @method URL Permet de charger les valeurs des VARIN dans l'url
		 * @return URL URL avec les valeurs des VARIN
		 * @access public
		 */
		public function charger_var_dans_url($p_mode,$p_url,$p_version = null)
		{
			if($p_version == null)
			{
				$p_version = $this->c_version;
			}

			$p_url = 'icode.php?&ID='.$this->c_id.'&version='.$p_version;
			$s_param_url = '';
			$b_changement = false;

			// On verifie si de nouvelles VARIN ont été modifiées
			$sql = 'SELECT nom,resultat,DEFAUT,NEUTRE
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'
					WHERE ID = '.$this->c_id_temp.'
					AND TYPE = "IN"
					AND version = '.$this->c_version;

			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['resultat'] != '' || is_null($row['resultat']))
				{
					$s_param_url .= '&'.rawurlencode($row['nom']).'='.rawurlencode($row['resultat']);
				}
			}

			if($b_changement == false)
			{
				if($p_mode == 'true')
				{
					return $p_url.$s_param_url.'&IK_VALMOD='.$this->c_ik_valmod.'&ssid='.$this->c_ssid;
				}
				else
				{
					return $p_url.$s_param_url.'&ssid='.$this->c_ssid;
				}
			}
		}

		/**
		 * Mise à jour du code (remplacement des VARIN par leurs valeurs)
		 */
		private function replace_var_by_value($p_corps)
		{
			// On recupere les varin
			switch ($this->c_ik_valmod) 
			{
				case 0:
					// Pas de valorisation par défaut ni neutre
					$sql = 'SELECT nom,resultat
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'
						WHERE ID = '.$this->c_id_temp.'
						AND TYPE = "IN"
						ORDER BY LENGTH(Nom) DESC';
					break;
				case 1:
					// Valorisation par défaut
					$sql = 'SELECT nom,(CASE WHEN resultat IS NULL THEN resultat WHEN IFNULL(length(RESULTAT),0) = 0 THEN defaut ELSE resultat END) as resultat
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'
						WHERE ID = '.$this->c_id_temp.'
						AND TYPE = "IN"
						ORDER BY LENGTH(Nom) DESC';
					break;
				case 2:
					// Valorisation neutre
					$sql = 'SELECT nom,(CASE WHEN resultat IS NULL THEN resultat WHEN IFNULL(length(RESULTAT),0) = 0 THEN neutre ELSE resultat END) as resultat
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
						WHERE ID = '.$this->c_id_temp.' 
						AND TYPE = "IN" 
						ORDER BY LENGTH(Nom) DESC';
					break;
				case 3:
					// Valorisation neutre puis par défaut
					$sql = 'SELECT nom,(CASE WHEN resultat IS NULL THEN (resultat) WHEN IFNULL(length(RESULTAT),0) = 0 THEN (CASE IFNULL(length(NEUTRE),0) WHEN 0 THEN defaut ELSE neutre END) ELSE resultat END) as resultat
						FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
						WHERE ID = '.$this->c_id_temp.' 
						AND TYPE = "IN" 
						ORDER BY LENGTH(Nom) DESC';
					break;
			}
			// On recupere les varin
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
			if($this->c_dataset['POSTFIXE'] == '')
			{
				$s_prefixvar = $this->c_dataset['PREFIXE'];
				
				$a_param = array();
				
				while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
				{ 
					$row['resultat'] = html_entity_decode($row['resultat'],ENT_NOQUOTES,'UTF-8');
					$row['nom'] = html_entity_decode($row['nom'],ENT_NOQUOTES,'UTF-8');
					if($row['resultat'] == '')
					{
						$a_param[$row['nom']] = $this->c_dataset['PREFIXE'].$row['nom'].$this->c_dataset['POSTFIXE'];
					}
					else
					{
						$a_param[$row['nom']] = $row['resultat'];
					}
				}
	
				$tab_caractere_fin = explode('SEP',$_SESSION[$this->c_ssid]['configuration'][27]);
				// On parcours toute les variables
				foreach($a_param as $key => $value)
				{
					// $value = str_ireplace("\'","'",$value);
					// Liste des caratères de fin d'une variable
					
					foreach($tab_caractere_fin as $caractere_fin)
					{
						if(is_numeric($caractere_fin))	
						{
							$caractere_fin = chr($caractere_fin);
						}	

						$b_trouve = $s_prefixvar.$key.$caractere_fin;
						$p_corps = str_ireplace($b_trouve,$value.$caractere_fin,$p_corps);
					}

					// Do not remove
					// In case of end of file only
					$len_var = strlen($key)+strlen($s_prefixvar);
					if(substr($p_corps, -($len_var)) == $s_prefixvar.$key ) 
					{
						$b_trouve = $s_prefixvar.$key;
						$p_corps = substr($p_corps,0,strlen($p_corps)-($len_var)).$value;
					}
					// Do not remove
				}
			}
			else
			{
				while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
				{
					if($row['resultat'] == '')
					{
						$p_corps = str_ireplace(htmlentities($this->c_dataset['PREFIXE']).$row['nom'].htmlentities($this->c_dataset['POSTFIXE']),htmlentities($this->c_dataset['PREFIXE']).$row['nom'].htmlentities($this->c_dataset['POSTFIXE']),$p_corps);
					}
					else
					{
						$p_corps = str_ireplace(($this->c_dataset['PREFIXE']).$row['nom'].($this->c_dataset['POSTFIXE']),$row['resultat'],$p_corps);
					}
				}
			}
			return $p_corps;
		}	
		
		public function set_neutral_values()
		{
			if($this->c_ik_valmod == 1)
			{
				$this->c_ik_valmod = 3;
			}
			else
			{
				$this->c_ik_valmod = 2;
			}
		}
	
		public function unset_neutral_values()
		{
			if($this->c_ik_valmod == 3)
			{
				$this->c_ik_valmod = 1;
			}
			else
			{
				$this->c_ik_valmod = 0;
			}
		}
		
		/**
		 * Sauvegarde de l'objet en base de données, créer une nouvelle version.
		 */
		public function sauvegarder_icode($p_titre,$p_descriptif,$p_pole,$p_version,$p_activite,$p_auteur,$p_engine,$p_engine_version,$p_corps,$prefixe,$postfixe,$p_bloquer)
		{
			/**==================================================================
			* GESTION DU VEROUILLAGE DE LA FICHE (flag obsolete)
			====================================================================*/				
			if($p_bloquer == 'true')
			{
				$this->c_obsolete = 1;
			}
			else
			{
				$this->c_obsolete = 0;
			}
			/*===================================================================*/
					
			/**==================================================================
			* Définition de l'id du code et de la version
			====================================================================*/	
			if($this->c_id == 'new')
			{
				// Recycle hole
				$sql = 'SELECT IF ((
					SELECT 1
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'`
					WHERE ID = 1) IS NULL,1, MIN(a.ID)+1) as "maxid"
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` a
					WHERE NOT EXISTS (
					SELECT b.ID
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` b
					WHERE 1 = 1
					AND b.ID = (a.ID + 1))
					AND ID < 99999
					LIMIT 1;';
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				$i_max_id_code = mysql_result($resultat,0);
				
				$this->c_id = $i_max_id_code;
				$this->c_version = 0;
				$this->c_dataset['DATE'] = "NOW()";
				$this->c_dataset['AUTEUR'] = $p_auteur;
			}
			else
			{
				// Objet existant -> création d'une nouvelle version
						
				$this->c_dataset['DATE'] = '"'.$this->c_dataset['DATE'].'"';
				
				$sql = 'SELECT version as "maxversion" from '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' where ID = '.$this->c_id;
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				// Affection de la nouvelle version à l'objet
				$this->c_version = mysql_result($resultat,0) + 1;
			}
			/*===================================================================*/
	
			/**==================================================================
		 	* Sauvegarde du code
		 	====================================================================*/	
			$sql = 'INSERT INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'].'`(`ID`, `pole`, `Theme`, `VGS`, `Version`, `prefixe`, `postfixe`, `typec`, `engine_version`, `corps`, `Titre`, `Commentaires`, `Last_update_user`, `last_update_date`,`obsolete`)
					VALUES ('.$this->c_id.',"'.addslashes($p_pole).'", "'.addslashes($p_activite).'","'.addslashes($p_version).'", '.$this->c_version.', "'.addslashes($prefixe).'","'.addslashes($postfixe).'", "'.addslashes($p_engine).'", "'.addslashes($p_engine_version).'", "'.addslashes($p_corps).'","'.addslashes($p_titre).'", "'.addslashes($p_descriptif).'", "'.addslashes(strtoupper($p_auteur)).'", NOW(), "'.$this->c_obsolete.'");';
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			/*===================================================================*/
		
			/**==================================================================
		 	* Sauvegarde des variables
		 	====================================================================*/		
			$sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
					SET max = 0 
					WHERE id = '.$this->c_id.'  
					AND version < '.$this->c_version;
							
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
					
			$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
					SET `ID` = '.$this->c_id.',
						`Version` = '.$this->c_version.',
						`max` = 1 
					WHERE `ID` = '.$this->c_id_temp;
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			/*===================================================================*/
			
			/**==================================================================
		 	* Sauvegarde des tags
		 	====================================================================*/		
			// ATTENTION : Insert servant de déclencheur pour le trigger des tags. Ne pas mettre de REPLACE !!!
			$sql = 'INSERT INTO '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'(`IdTag`, `ID`, `Etape`, `Version`, `Tag`, `Groupe`, `objet`, `temp`, `id_src`, `version_src`) 
					SELECT `IdTag`, '.$this->c_id.', `Etape`, '.$this->c_version.' , `Tag`, `Groupe`, `objet`, `temp`, `id_src`, `version_src` FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'
					WHERE ID = '.$this->c_id_temp;
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$sql = "DELETE FROM ".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_tags']['name']." WHERE ID = ".$this->c_id_temp;
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			/*===================================================================*/
			
			// XML return	
			header("Content-type: text/xml");
			echo "<?xml version='1.0' encoding='UTF-8'?>";
			echo "<parent>";
			echo "<url>icode.php?ID=".$this->c_id."&ikbackup=true</url>";
			//echo "<url_ctrl>outils/coherent_check/auto.php?iobject=__ICODE__&id=".$this->c_id."</url_ctrl>";
			echo "</parent>";
			/*===================================================================*/
		}
	
		/**
		 * Vérifie si l'objet est obsolete, retourne true si oui, sinon false
		 */
		public function is_obsolete()
		{
			$sql = 'SELECT `obsolete` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'`
					WHERE `id` = '.$this->c_id;
	
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_result($resultat, 0,'obsolete') == 0)
			{
				$this->c_obsolete = false;
				return 0;
			}
			else
			{
				$this->c_obsolete = true;
				return 1;
			}			
		}
		
		/**
		 * Getter de $this->c_obsolete
		 */
		public function get_flag_obsolete()
		{
			return $this->c_obsolete;
		}
		
		public function get_ik_valmod()
		{
			return $this->c_ik_valmod;
		}
		
		/**
		 * @param type - neutre ou defaut
		 * @param id - Identifiant de la varin
		 */
		public function set_default_neutre_value($p_type,$p_id)
		{
			$sql = 'SELECT 1 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
					WHERE ID = '.$this->c_id_temp.' 
					AND TYPE = "IN" 
					AND IDP = '.$p_id.' 
					AND resultat = '.$p_type;
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_num_rows($resultat) == 0)
			{
				// On valorise la variable
				 $sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' SET resultat = '.$p_type.' WHERE ID = '.$this->c_id_temp.' AND TYPE = "IN" AND IDP = '.$p_id;	
			}
			else
			{
				// Devalorisation de la variable
				 $sql = 'UPDATE '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' SET resultat = "" WHERE ID = '.$this->c_id_temp.' AND TYPE = "IN" AND IDP = '.$p_id;
			}
	
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		
		public function set_default_values()
		{
			if($this->c_ik_valmod == 2)
			{
				$this->c_ik_valmod = 3;
			}
			else
			{
				$this->c_ik_valmod = 1;
			}
		}
	
		public function unset_default_values()
		{
			if($this->c_ik_valmod == 3)
			{
				$this->c_ik_valmod = 2;
			}
			else
			{
				$this->c_ik_valmod = 0;
			}
		}
	
		public function get_libelle_moteur($p_id_moteur)
		{
			$sql = 'SELECT `Description` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_moteurs']['name'].'` WHERE `id` = "'.$p_id_moteur.'"';
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_num_rows($resultat) > 0)
			{
				return mysql_result($resultat, 0,'Description');
			}
			else
			{
				return '';
			}
		}
	
		public function get_libelle_pole($p_id_pole)
		{
			$sql = 'SELECT `Libelle` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles']['name'].'` WHERE `ID` = "'.$p_id_pole.'"';
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_num_rows($resultat) > 0)
			{
				return mysql_result($resultat, 0,'Libelle');
			}
			else
			{
				return '';
			}
		}
		
		public function get_libelle_activite($p_id_activite,$p_id_pole)
		{
			$sql = 'SELECT `Libelle` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_poles_themes']['name'].'` WHERE `ID` = "'.$p_id_activite.'" AND ID_POLE = "'.$p_id_pole.'"';
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_num_rows($resultat) > 0)
			{
				return mysql_result($resultat, 0,'Libelle');		
			}
			else
			{
				return '';
			}
		}
		
		public function get_engine()
		{
			return $this->c_dataset['TYPEC'];
		}
		
		public function get_id_activite()
		{
			return $this->c_dataset['Theme'];
		}
		
		public function get_id_pole()
		{
			return $this->c_dataset['pole'];
		}
		
		public function get_pole_version()
		{
			return $this->c_dataset['VGS'];
		}
		
		/**
		 * iCode control
		 */
		public function controler_icode($p_titre,$p_descriptif,$p_pole,$p_version,$p_activite,$p_auteur,$p_engine,$p_engine_version,$p_corps)
		{
			$a_verification = array();
			$this->c_level_erreur = 0;
			$b_erreur = false;
			$i_key_array = 0;
			$eval_js = '';
			
			/**==================================================================
		 	* Check title
		 	====================================================================*/	
			$title_error = false;	
			if($p_titre == '')
			{
				// Empty title
				$b_erreur = true;
				$title_error = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',8,"a_tabbar.setTabActive('tab-level1');document.getElementById('titre').focus();");
				$i_key_array++;
			}
			else
			{
				if(strlen($p_titre) < $_SESSION[$this->c_ssid]['configuration'][22])
				{
					// Title too short
					$b_erreur = true;
					$title_error = true;
					$s_texte = $_SESSION[$this->c_ssid]['message'][9];
					$s_texte = str_replace('$j',strlen($p_titre),$s_texte);
					$s_texte = str_replace('$k',$_SESSION[$this->c_ssid]['configuration'][22],$s_texte);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_texte,"a_tabbar.setTabActive('tab-level1');document.getElementById('titre').focus();");
					$i_key_array++;
				}
				else
				{				
					if(strlen($p_titre) > $_SESSION[$this->c_ssid]['configuration'][25])
					{
						// Title too long
						$b_erreur = true;
						$title_error = true;
						$s_texte = $_SESSION[$this->c_ssid]['message'][30];
						$s_texte = str_replace('$j',strlen($p_titre),$s_texte);
						$s_texte = str_replace('$k',$_SESSION[$this->c_ssid]['configuration'][25],$s_texte);
						$a_verification[$i_key_array]['criticite'] = 2;
						$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_texte,"a_tabbar.setTabActive('tab-level1');document.getElementById('titre').focus();");
						$i_key_array++;
					}	
				}
			}
			if($title_error)
			{
				$eval_js .= 'document.getElementById(\'titre\').style.backgroundColor= \'#FF866A\';';
			}
			/*===================================================================*/
			
			/**==================================================================
		 	* Check description
		 	====================================================================*/	
			$descriptif_error = false;
			if($p_descriptif == '')
			{
				// Empty description
				$b_erreur = true;
				$descriptif_error = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',10,"a_tabbar.setTabActive('tab-level1');");
				$i_key_array++;
			}
			else
			{
				if(strlen($p_descriptif) < $_SESSION[$this->c_ssid]['configuration'][23])
				{
					// Description too short
					$b_erreur = true;
					$descriptif_error = true;
					$s_texte = $_SESSION[$this->c_ssid]['message'][11];
					$s_texte = str_replace('$j',strlen($p_descriptif),$s_texte);
					$s_texte = str_replace('$k',$_SESSION[$this->c_ssid]['configuration'][23],$s_texte);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_texte,"a_tabbar.setTabActive('tab-level1');");		
					$i_key_array++;
				}
			}
			
			if($descriptif_error)
			{
				$eval_js .= 'if(document.getElementById(\'Descriptif_ifr\').contentWindow.tinymce){document.getElementById(\'Descriptif_ifr\').contentWindow.tinymce.style.backgroundColor= \'#FF866A\';}';
			}
			else
			{
				$eval_js .= 'if(document.getElementById(\'Descriptif_ifr\').contentWindow.tinymce){document.getElementById(\'Descriptif_ifr\').contentWindow.tinymce.style.backgroundColor= \'#FFF\';}';
			}
			/*===================================================================*/	
			
			/**==================================================================
		 	* Check Pole
		 	====================================================================*/
			if($p_pole == '')
			{
				// Pôle undefined
				$b_erreur = true;			
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',12,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_pole_lmod');");	
				$erreur_pole = true;	
				$i_key_array++;
			}
			/*===================================================================*/	
			
			
			/**==================================================================
		 	* Check pole version
		 	====================================================================*/
			if($p_version == '')
			{
				// Pole version undefined
				$b_erreur = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				if(isset($erreur_pole))
				{
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',13,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_pole_lmod');");
				}
				else
				{
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',13,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_vers_pole_lmod');");	
				}
				$erreur_pole_version = true;	
				$i_key_array++;
			}	
			/*===================================================================*/	
			
			
			/**==================================================================
		 	* Check activity
		 	====================================================================*/
			if($p_activite == '')
			{
				// Activity undefined
				$b_erreur = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				if(isset($erreur_pole))
				{
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',14,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_pole_lmod');");
				}
				else
				{
					if(isset($erreur_pole_version))
					{
						$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',14,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_vers_pole_lmod');");
					}
					else
					{
						$a_verification[$i_key_array]['message'] =  $this->generer_ligne_erreur('erreur',14,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy2_activite_lmod');");
					}
				}
				$erreur_activite = true;	
				$i_key_array++;	
			}	
			/*===================================================================*/	
			
			
			/**==================================================================
		 	* Check trigramme
		 	====================================================================*/
			$trigramme_error = false;	
			if($p_auteur == '')
			{
				// Trigramme undefined
				$b_erreur = true;
				$trigramme_error = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',15,"a_tabbar.setTabActive('tab-level1');document.getElementById('auteur').focus();");
				$i_key_array++;
			}
			else
			{
				if((strlen($p_auteur) < 3) || (strlen($p_auteur) > 3))
				{
					// Trigramme too short or too long
					$b_erreur = true;
					$trigramme_error = true;
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',16,"a_tabbar.setTabActive('tab-level1');document.getElementById('auteur').focus();");
					$i_key_array++;
				}
			}	
			if($trigramme_error)
			{
				$eval_js .= 'document.getElementById(\'auteur\').style.backgroundColor= \'#FF866A\';';
			}
			/*===================================================================*/
	
			
			/**==================================================================
		 	* Check engine
		 	====================================================================*/
			if($p_engine == '')
			{
				// Engine undefined
				$b_erreur = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',17,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy_moteur');");
				$erreur_engine = true;	   
				$i_key_array++;
			}	
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check engine version
		 	====================================================================*/	
			if($p_engine_version == '')
			{
				// Engine version undefined
				$b_erreur = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				if(isset($erreur_engine))
				{
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',73,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy_moteur');");	
				}
				else
				{
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',73,"a_tabbar.setTabActive('tab-level1');vimofy_open_lmod('vimofy_vers_moteur');");
				}
					   
				$i_key_array++;
				
			}
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check code content
		 	====================================================================*/	
			if($p_corps == '')
			{
				// Code content empty
				$b_erreur = true;
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',18,"a_tabbar.setTabActive('tab-level2');");	 
				$i_key_array++;
			}			
			/*===================================================================*/
			
					
			/**==================================================================
		 	* Check code variables
		 	====================================================================*/	
			$a_verif = $this->controler_corps($p_corps);
			if(is_array($a_verif))
			{
				foreach($a_verif as $value)
				{
					$a_verification[] = $value;
				}
			}		
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check code version
		 	====================================================================*/	
			$a_verif = $this->verif_lancement();
			if(is_array($a_verif))
			{
				foreach($a_verif as $value)
				{
					$a_verification[] = $value;
				}
			}	
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Contrôle des doublons de variable
		 	====================================================================*/	
			$a_verif = $this->check_duplicate_var();
			if(isset($a_verif[0]))
			{
				if($this->c_level_erreur < 2)
				{
					$this->c_level_erreur = 2;
				}
			}
	
			if(is_array($a_verif))
			{
				foreach($a_verif as $value)
				{
					$a_verification[] = $value;
				}
			}	
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check tags
		 	====================================================================*/	
			$a_verif = $this->check_tag();
			if(isset($a_verif[0]))
			{
				if($this->c_level_erreur < 1)
				{
					$this->c_level_erreur = 1;
				}
			}
			if(is_array($a_verif))
			{
				foreach($a_verif as $value)
				{
					$a_verification[] = $value;
				}
			}
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Check variable name
		 	====================================================================*/	
			$a_verif = $this->verif_variables();
			if(isset($a_verif[0]))
			{
				$this->c_level_erreur = 2;
			}
			if(is_array($a_verif))
			{
				foreach($a_verif as $value)
				{
					$a_verification[] = $value;
				}
			}
			/*===================================================================*/
			
			
			/**==================================================================
		 	* Error management
		 	====================================================================*/	
			if($b_erreur)
			{
				$this->c_level_erreur = 2;
			}
			
			// Get list of column
			foreach($a_verification as $key => $row) 
			{
				$a_criticite[$key]  = $row['criticite'];
			}
			
			if(isset($a_criticite) && is_array($a_criticite)) array_multisort($a_criticite, SORT_DESC,$a_verification);
			
			$b_erreur = false;
			$s_message = '<table id="informations">';
	
			foreach($a_verification as $value)
			{
				$b_erreur = true;
				$s_message .= $value['message'];
			}
			
			if(!$b_erreur)
			{
				$s_message .= $this->generer_ligne_erreur('ok',69,''); 
			}
			$s_message .= '</table>';
	
			// XML return	
			header("Content-type: text/xml");
			echo "<?xml version='1.0' encoding='UTF-8'?>";
			echo "<parent>";
			echo "<message_controle>".$this->protect_xml($s_message)."</message_controle>";
			echo "<titre_controle>".$this->protect_xml($this->generer_titre_controle())."</titre_controle>";
			echo "<niveau_erreur>".$this->c_level_erreur."</niveau_erreur>";
			echo "<eval_js>".$eval_js."</eval_js>";
			echo "</parent>";
			/*===================================================================*/
		}
	
		
		/**
		 * Génération d'une ligne de contrôle
		 */
		private function generer_titre_controle()
		{
			switch($this->c_level_erreur ) 
			{
				case 0:
					return '<table><tr><td><a href="#" class="ok"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->c_ssid]['message'][153].'</td></tr></table>';
					break;
				case 1:
					return '<table><tr><td><a href="#" class="warning"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->c_ssid]['message'][154].'</td></tr></table>';
					break;
				case 2:
					return '<table><tr><td><a href="#" class="erreur"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->c_ssid]['message'][152].'</td></tr></table>';
					break;
			}
		}
	
		/**
		 * Vérifie les variables du code:
		 * 
		 * 	- Le format de la variable est correct (préfixe et postfixe ok)
		 *  - qu'une variable n'a pas été définie dans le corps et non dans la liste des variables.
		 *  - que les variables qui sont dans la liste des variables sont bien présentent dans le corps du code.
		 *  - que des variables sans postfixe ne soient pas collées.
		 **/
		private function controler_corps($p_corps)
		{
			$a_verification = array();
			$i_key_array = 0;

			/**==================================================================
		 	* Récupération des VARIN de la base
		 	====================================================================*/	
			$sql = 'SELECT `Nom` FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'` WHERE `ID` = "'.$this->c_id_temp.'" AND type = "IN"';
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$a_varin_base = array();
			$i = 0;
			while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$a_varin_base[$i] = html_entity_decode($row['Nom'],ENT_NOQUOTES,'UTF-8');
				$i++;
			}
			/*===================================================================*/

			/**==================================================================
		 	* Récupération des VARIN définies dans le code
		 	====================================================================*/	
			$a_varin_code = $this->var_extract($this->c_dataset['PREFIXE'],$this->c_dataset['POSTFIXE'],($p_corps));
			/*===================================================================*/

			/**==================================================================
		 	* Comparaison des VARIN du code et des VARIN de la base
		 	====================================================================*/	
			foreach($a_varin_code as $value_varin_code)
			{
				$b_trouve = false;
				foreach($a_varin_base as $value_varin_base)
				{
					if(html_entity_decode($value_varin_code,ENT_NOQUOTES,'UTF-8') == $value_varin_base)
					{
						$b_trouve = true;
						break;
					}
					else
					{
						$b_trouve = false;
					}
				}
				if(!$b_trouve)
				{
					$s_texte = $_SESSION[$this->c_ssid]['message'][19];
					$s_texte = str_replace('&id_source','><span class="BBVarin">'.($value_varin_code).'</span><',$s_texte);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur("erreur",$s_texte,"a_tabbar.setTabActive('tab-level3');");	 
					$i_key_array++;
					$this->c_level_erreur = 2;
				}
			}
			/*===================================================================*/

			/**==================================================================
		 	* Comparaison des VARIN de la base et des VARIN du code
		 	====================================================================*/	
			foreach($a_varin_base as $value_varin_base)
			{
				$b_trouve = false;
				foreach($a_varin_code as $value_varin_code)
				{
					if(html_entity_decode($value_varin_code,ENT_NOQUOTES,'UTF-8') == $value_varin_base)
					{
						$b_trouve = true;
						break;
					}
					else
					{
						$b_trouve = false;
					}
				}
	
				if(!$b_trouve)
				{
					$s_texte = $_SESSION[$this->c_ssid]['message'][20];
					$s_texte = str_replace('&id_source','><span class="BBVarin">'.htmlentities($value_varin_base,ENT_QUOTES,'UTF-8').'</span><',$s_texte);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_texte,"a_tabbar.setTabActive('tab-level2');");
					$i_key_array++;
					$this->c_level_erreur = 2;
				}
			}	
			/*===================================================================*/

			/**==================================================================
		 	* On vérifie que des varins sans postfixe ne sont pas collées dans le corps.
		 	====================================================================*/	
			if($this->c_dataset['POSTFIXE'] == '')
			{
				foreach($a_varin_base as $value_varin_base)
				{
					if(strstr($p_corps,$this->c_dataset['PREFIXE'].$value_varin_base.$this->c_dataset['PREFIXE']))
					{
						$s_texte = $_SESSION[$this->c_ssid]['message'][112];
						$s_texte = str_replace('$var','<span class="BBVarin">'.htmlentities($value_varin_base,ENT_QUOTES,'UTF-8').'</span>',$s_texte);
						$a_verification[$i_key_array]['criticite'] = 2;
						$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_texte,"a_tabbar.setTabActive('tab-level2');");
						$i_key_array++;
						$this->c_level_erreur = 2;
					}
				}
			}
			/*===================================================================*/
			return $a_verification;
		}
	
		/**
		 * Mise à jour du préfixe
		 */
		public function maj_prefixe($p_prefixe)
		{
			$this->c_dataset['PREFIXE'] = $p_prefixe;
		}
		
		/**
		 * Mise à jour du postfixe
		 */
		public function maj_postfixe($p_postfixe)
		{
			$this->c_dataset['POSTFIXE'] = $p_postfixe;
		}	
		
		/**
		 * Annule les modifications du code, supprime les lignes crées en base
		 */
		public function cancel_modif()
		{
			// Suppression des lignes de la table icode_parametres
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name']."` WHERE ID = ".$this->c_id_temp.";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
			// Suppression des lignes de la table tags
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name']."` WHERE ID = ".$this->c_id_temp." AND objet = \"icode\";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
			// Suppression des lignes de la table url_temp
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_url_temp']['name']."` WHERE ID_temp = ".$this->c_id_temp;
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
			// Suppression des lignes de la table lock
			$sql = "DELETE FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name']."` WHERE id_temp  = ".$this->c_id_temp.";";
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		
		
		/**
		 * Efface les valorisations des paramètres (uniquement en visu)
		 */
		public function delete_value_param()
		{
			$this->c_ik_valmod = 0;
			$sql = "UPDATE `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name']."` 
					SET `resultat` = ''
					WHERE `ID` = ".$this->c_id_temp;
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		}
		
		
		/**
		 * Extrait les variables du corps
		 */
		public function var_extract($p_prefixe,$p_postfixe,$p_texte) 
		{
			$a_tab_var = array();					// Contient les variables définies dans le corps du code
			$s_ref = $p_texte; 						// Copie du texte original
			$i_len_prefixe = strlen($p_prefixe); 	// Calcul de la longeur du prefixe
			$i_len_postfixe = strlen($p_postfixe);  // Calcul de la longeur du postfixe
			
			/**==================================================================
		 	* Parcours du corps du code
		 	====================================================================*/	
			while(strstr($s_ref,$p_prefixe)) 
			{
				// Cherche le prochain préfixe
				$s_ref = strstr($s_ref,$p_prefixe); 
				
				// Suppression du préfixe de la chaine extraite
				$s_ref = substr($s_ref,$i_len_prefixe); 
				
				/**==================================================================
			 	* Extraction de la variable
			 	====================================================================*/	
				if(strlen($p_postfixe) > 0)
				{
					// Postfixe définit
					
					// On cherche le prochain postfixe
					$s_end = strstr($s_ref,$p_postfixe); 
					
					if($s_end)
					{
						$s_var = substr($s_ref,0,strlen($s_ref)-strlen($s_end)); // On supprime le postfixe de la variable
					}			
				}
				else 
				{ 
					// Postfixe non définit
					$s_motif = "/([".$_SESSION[$this->c_ssid]['configuration'][38]."]*)/"; // On extrait la variable par rapport au caractère qui peuvent la composer
					preg_match($s_motif,$s_ref,$result);
					$s_var = str_ireplace('&amp;','',$result[1]);
				}
				/*===================================================================*/
				if($s_var != '')
				{
					$a_tab_var[$s_var] = $s_var; 						// On met la variable dans un tableau
				}
				
				$s_ref = substr($s_ref,strlen($s_var)+$i_len_postfixe); // On se décale pour la boucle suivante
			}
			/*===================================================================*/
			
			// On fait un distinct sur les variables
			if (isset($a_tab_var)) $a_tab_var = array_unique($a_tab_var);
			return $a_tab_var;
		}	
	
		
		/**
		 * Conversion du BBCode en HTML
		 */
		public function convertBBCodetoHTML($p_txt)
		{
			$b_remplacement = true;
			
			while($b_remplacement)
			{
				$b_remplacement = false;
				$s_oldtxt = $p_txt;
				$p_txt = preg_replace('`\[BBTITRE\]([^\[]*)\[/BBTITRE\]`i','<b><u><font class="bbtitre">\\1</font></u></b>',$p_txt);
				$p_txt = preg_replace('`\[EMAIL\]([^\[]*)\[/EMAIL\]`i','<a href="mailto:\\1">\\1</a>',$p_txt);
				$p_txt = preg_replace('`\[b\]([^\[]*)\[/b\]`i','<b>\\1</b>',$p_txt);
				$p_txt = preg_replace('`\[i\]([^\[]*)\[/i\]`i','<i>\\1</i>',$p_txt);
				$p_txt = preg_replace('`\[u\]([^\[]*)\[/u\]`i','<u>\\1</u>',$p_txt);
				$p_txt = preg_replace('`\[s\]([^\[]*)\[/s\]`i','<s>\\1</s>',$p_txt);
				$p_txt = preg_replace('`\[br\]`','<br>',$p_txt);
				$p_txt = preg_replace('`\[center\]([^\[]*)\[/center\]`','<div style="text-align: center;">\\1</div>',$p_txt);
				$p_txt = preg_replace('`\[left\]([^\[]*)\[/left\]`i','<div style="text-align: left;">\\1</div>',$p_txt);
				$p_txt = preg_replace('`\[right\]([^\[]*)\[/right\]`i','<div style="text-align: right;">\\1</div>',$p_txt);
				$p_txt = preg_replace('`\[img\]([^\[]*)\[/img\]`i','<img src="\\1" />',$p_txt);
				$p_txt = preg_replace('`\[color=([^[]*)\]([^[]*)\[/color\]`i','<font color="\\1">\\2</font>',$p_txt);
				$p_txt = preg_replace('`\[bg=([^[]*)\]([^[]*)\[/bg\]`i','<font style="background-color: \\1;">\\2</font>',$p_txt);
				$p_txt = preg_replace('`\[size=([^[]*)\]([^[]*)\[/size\]`i','<font size="\\1">\\2</font>',$p_txt);
				$p_txt = preg_replace('`\[font=([^[]*)\]([^[]*)\[/font\]`i','<font face="\\1">\\2</font>',$p_txt);
				
				if ($s_oldtxt != $p_txt)
				{
					$b_remplacement=true;
				}
			}
			return $p_txt;
		}
		
		
		/**
		 * Vérification au lancement de l'objet (en modif)
		 */
		public function verif_lancement($p_lancement = false)
		{
			$a_verification = array();
			$i_key_array = 0;
			$a_criticite = null;
			if(!$this->is_last_version())
			{
				$b_erreur = true;
				// L'objet n'est pas modifié dans sa dernière version
				$a_verification[$i_key_array]['criticite'] = 1;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('warning',23,"a_tabbar.setTabActive('tab-level1');");	
				$i_key_array++;
			}
			
			if($p_lancement)
			{
				/**==================================================================
			 	* GESTION DES ERREURS
			 	====================================================================*/	
				if(isset($b_erreur) && $b_erreur && $this->c_level_erreur < 1)
				{
					$this->c_level_erreur = 1;
				}
				
				// Obtient une liste de colonnes
				foreach($a_verification as $key => $row) 
				{
					$a_criticite[$key]  = $row['criticite'];
				}
				
				if(is_array($a_criticite)) array_multisort($a_criticite, SORT_DESC,$a_verification);
				
				$b_erreur = false;
				$s_message = '<table id="informations">';
		
				foreach($a_verification as $value)
				{
					$b_erreur = true;
					$s_message .= $value['message'];
				}
				
				if(!$b_erreur)
				{
					$s_message .= $this->generer_ligne_erreur('ok',69,''); 
				}
				$s_message .= '</table>';
		
				// XML return	
				header("Content-type: text/xml");
				echo "<?xml version='1.0' encoding='UTF-8'?>";
				echo "<parent>";
				echo "<message_controle>".$this->protect_xml($s_message)."</message_controle>";
				echo "<titre_controle>".$this->protect_xml($this->generer_titre_controle())."</titre_controle>";
				echo "<niveau_erreur>".$this->c_level_erreur."</niveau_erreur>";
				echo "</parent>";
				/*===================================================================*/
			}
			else
			{
				return $a_verification;
			}
		}
		
		/**
		 * methode qui retourne true si la fiche en cours de modification est dans sa derniere version sinon elle retourne false
		 */
		private function is_last_version() 
		{
			if ($this->c_id == 'new')
			{
				return true;
			}
			else
			{
				// Création de la requête
				$sql = 'SELECT `version` as max_version 
						FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'` 
						WHERE `ID` = '.$this->c_id;
				
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				if(mysql_result($resultat,0,'max_version') == $this->c_version) 
				{
					return true;
				}
				else
				{
					return false;
				}
			}	
		}
		
		
		/**
		 * Vérifie que les variables du code ne comportent pas de doublons
		 */
		private function check_duplicate_var()
		{
			$a_verification = array();
			$i_key_array = 0;		
			
			$sql = 'SELECT 
						`NOM`,
						`TYPE`
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
					WHERE `ID` = '.$this->c_id_temp.' 
					AND `Version` = '.$this->c_version.' 
					AND (`TYPE` = "OUT" OR `TYPE` = "IN") 
					GROUP  BY `ID`, `Version`,`NOM`,`TYPE`
					HAVING COUNT(*) > 1';
			
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$b_erreur = false;
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
			{
				if($row['TYPE'] == 'OUT')
				{
					$s_type = $_SESSION[$this->c_ssid]['message'][78];
					$s_nom = '><span class="BBVarout">'.$row['NOM'].'</span><';
					$s_emplacement = $_SESSION[$this->c_ssid]['message'][82];
				}
				else
				{
					$s_type = $_SESSION[$this->c_ssid]['message'][77];
					$s_nom = '><span class="BBVarin">'.$row['NOM'].'</span><';
					$s_emplacement = $_SESSION[$this->c_ssid]['message'][81];
				}
				
				$b_erreur = true;
				$s_message = str_replace('$type',$s_type,$_SESSION[$this->c_ssid]['message'][24]);
				$s_message = str_replace('$var',$s_nom,$s_message);
				$s_message = str_replace('$emplacement',$s_emplacement,$s_message);
				$a_verification[$i_key_array]['criticite'] = 2;
				$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,"a_tabbar.setTabActive('tab-level4');");	
				$i_key_array++;
			}
			
			if($b_erreur)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 1;
			}
			
			return $a_verification;
		}
		
		
		/**
		 * Get iCode error level
		 */
		public function get_level_erreur()
		{
			return $this->c_level_erreur;
		}
		
		
		/**
		 *  Duplication d'un iCode
		 * @return HTML Code HTML du bandeau d'informations
		 * @access public
		 */	
		public function dupliquer_icode()
		{
			// Set new ID
			$this->c_id = 'new';
			
			// Set version to 0
			$this->c_version = 0;
				
			$this->c_Obj_lock->set_id('new');
			
			// Replace tag version
			$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'`
				   SET `Version` = 0
				   WHERE `ID` = '.$this->c_id_temp.'
				   AND `objet` = "icode"';
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].'`
				   SET `version` = 0
				   WHERE `id` = '.$this->c_id_temp;
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
			$sql = 'UPDATE `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_lock']['name'].'`
				   SET `id` = 0
				   WHERE `id_temp` = '.$this->c_id_temp;	
			
			$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varout']->define_col_value('Version',0);
			$_SESSION['vimofy'][$this->c_ssid]['vimofy2_varin']->define_col_value('Version',0);
			$_SESSION['vimofy'][$this->c_ssid]['vimofy2_tags']->define_col_value('Version',0);
			
			// Display duplication message
			return '<table id="informations"><tr><td><img src="images/ok.png"></td><td>&nbsp;'.$_SESSION[$this->c_ssid]['message'][22].'</td></tr>';
		}
		
		
		
		/**
		 * Cette methode permet de vérifier les tags d'une fiche
		 */
		function check_tag()
		{
			$a_verification = array();
			$i_key_array = 0;		
			$b_erreur = false;	
				
			/**==================================================================
		 	* On vérifie qu'un tag et que le groupe du tag comporte au moins x caractères
		 	====================================================================*/	
			$i_nbr_carac_minimum = $_SESSION[$this->c_ssid]['configuration'][12];
	
			$sql = 'SELECT 
						`Tag`,
						`Groupe` 
					FROM `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].'`
					WHERE `ID` = '.$this->c_id_temp.'
					AND `objet` = "icode"';
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				/**==================================================================
			 	* Contrôle du tag
			 	====================================================================*/	
				if(strlen($row['Tag']) <= $i_nbr_carac_minimum)
				{
					// tag name is too short
					$b_erreur = true;
					$s_etape = 'Fiche';
					$s_message = str_replace('$Emplacement',$s_etape,$_SESSION[$this->c_ssid]['message'][28]);
					$s_message = str_replace('$nom',$row['Tag'],$s_message);
					$s_message = str_replace('$j',$i_nbr_carac_minimum + 1,$s_message);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,'a_tabbar.setTabActive(\'tab-level5\');'); 
					$i_key_array++;
				}
				/*===================================================================*/
				
				/**==================================================================
			 	* Contrôle du groupe du tag
			 	====================================================================*/	
				if(strlen($row['Groupe']) <= $i_nbr_carac_minimum)
				{
					// Group tag is too short
					$b_erreur = true;
					$s_etape = 'Fiche';
					$s_message = str_replace('$Emplacement',$s_etape,$_SESSION[$this->c_ssid]['message'][29]);
					$s_message = str_replace('$nom',$row['Tag'],$s_message);
					$s_message = str_replace('$j',$i_nbr_carac_minimum + 1,$s_message);
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,'a_tabbar.setTabActive(\'tab-level5\');'); 
					$i_key_array++;
				}
				/*===================================================================*/	
			}
			
			if($b_erreur && $_SESSION[$this->c_ssid]['niveau_informations'] < 2)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 2;			
			}
			/*===================================================================*/
			
			/**==================================================================
			 * Check that tag have only one word
			====================================================================*/	
			$sql = 'SELECT Tag FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->c_id_temp.' AND objet = "icode"';
		
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{	
				$tab_mot = explode(' ',$row['Tag']);
				$s_message = '';
		
				if(count($tab_mot) > 1)
				{
					// Le tag comporte plus d'un mot
					$b_erreur = true;
					$etape = 'Fiche';
					$s_message = str_replace('Etape $j',$etape,$_SESSION[$this->c_ssid]['message'][27]);
					$s_message = str_replace('$tag',$row['Tag'],$s_message);
					$a_verification[$i_key_array]['criticite'] = 1;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('warning',$s_message,'a_tabbar.setTabActive(\'tab-level5\');'); 
					$i_key_array++;																								
				}
			}	
			
			if(!isset($_SESSION[$this->c_ssid]['niveau_informations']) || ($b_erreur && $_SESSION[$this->c_ssid]['niveau_informations'] < 1))
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 1;
			}
			/*===================================================================*/
			
			return $a_verification;
		}
		
		
		/**
		 * Vérification des variables
		 */
		private function verif_variables()
		{
			$a_verification = array();
			$i_key_array = 0;		
			$b_erreur = false;	
			
			/**==================================================================
			 * Contrôle des variables - Noms interdits et caractères interdits
			====================================================================*/	
			
			// noms interdits
			$a_valeur_interdite = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);	
	
			// caracteres interdits
			$a_caractere_interdit = explode('|',$_SESSION[$this->c_ssid]['configuration'][14]);
			
			// creation de la requete
			$sql = 'SELECT nom,type
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' 
					WHERE id = '.$this->c_id_temp.' 
					AND version = '.$this->c_version;
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$row['nom'] = html_entity_decode($row['nom'],ENT_NOQUOTES,'UTF-8');
				if($row['type'] == 'OUT')
				{
					$onglet = 'tab-level4';
				}
				else
				{
					$onglet = 'tab-level3';
				}
				
				foreach($a_valeur_interdite as $value)
				{
					if(strcasecmp($value,$row['nom']) == 0)
					{
						// Le nom de la variable est composé d'un nom interdit
						$s_message = str_replace('$nom','><span class="BBVar'.$row['type'].'">'.$row['nom'].'</span><',$_SESSION[$this->c_ssid]['message'][30]);
						$b_erreur = true;
						$a_verification[$i_key_array]['criticite'] = 2;
						$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,"a_tabbar.setTabActive('".$onglet."');");	
						$i_key_array++;
					}
				}
				
				foreach($a_caractere_interdit as $value)
				{
					if(strstr($row['nom'],$value) != false)
					{
						// Le nom de la variable comporte un caractère interdit
						$s_message = str_replace('$nom','><span class="BBVar'.$row['type'].'">'.$row['nom'].'</span><',$_SESSION[$this->c_ssid]['message']['iknow'][24]);
						$s_message = str_replace('$car',$value,$s_message);		
						$b_erreur = true;
						$a_verification[$i_key_array]['criticite'] = 2;
						$a_verification[$i_key_array]['message'] =  $this->generer_ligne_erreur('erreur',$s_message,"a_tabbar.setTabActive('".$onglet."');");
						$i_key_array++;
					}
				}
				
				if(strstr($row['nom'],$this->c_dataset['PREFIXE']) != false)
				{
					// Le nom de la variable comporte le préfixe
					$s_message = str_replace('$nom','><span class="BBVar'.$row['type'].'">'.$row['nom'].'</span><',$_SESSION[$this->c_ssid]['message'][33]);	
					$s_message = str_replace('$prefixe',$this->c_dataset['PREFIXE'],$s_message);	
					$b_erreur = true;
					$a_verification[$i_key_array]['criticite'] = 2;
					$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,"a_tabbar.setTabActive('".$onglet."');");
					$i_key_array++;
				}
				
				if($this->c_dataset['POSTFIXE'] != '')
				{
					if(strstr($row['nom'],$this->c_dataset['POSTFIXE']) != false)
					{
						// Le nom de la variable comporte le postfixe
						$s_message = str_replace('$nom','><span class="BBVar'.$row['type'].'">'.$row['&1'].'</span><',$_SESSION[$this->c_ssid]['message'][385]);	
						$s_message = str_replace('$postfixe',$this->c_dataset['POSTFIXE'],$s_message);	
						$b_erreur = true;
						$a_verification[$i_key_array]['criticite'] = 2;
						$a_verification[$i_key_array]['message'] = $this->generer_ligne_erreur('erreur',$s_message,"a_tabbar.setTabActive('".$onglet."');");
						$i_key_array++;
					}		
				}	
			}	
	
			if($b_erreur && $_SESSION[$this->c_ssid]['niveau_informations'] < 2)
			{
				$_SESSION[$this->c_ssid]['niveau_informations'] = 2;
			}
			/*===================================================================*/
			return $a_verification;
		}
	
		
		/**
		 * Génère une ligne d'erreur
		 */
		private function generer_ligne_erreur($p_class_erreur,$p_id_message_erreur,$p_action_click,$p_ancre = null)
		{
			if(is_numeric($p_id_message_erreur))
			{
				$s_message = $_SESSION[$this->c_ssid]['message'][$p_id_message_erreur];
			}
			else
			{
				$s_message = $p_id_message_erreur;
			}
	
			if(is_null($p_ancre))
			{
				$href = 'href="#" onclick="javascript:'.$p_action_click.'iknow_panel_reduire();"';
			}
			else
			{
				$href = 'href="#'.$p_ancre.'" onclick="javascript:'.$p_action_click.'iknow_panel_reduire();"';
			}
	
			return '<tr><td><a '.$href.' class="'.$p_class_erreur.'"></a></td><td class="iknow_titre_controle">'.$s_message.'</td></tr>';
		}
		
		/**
		 * @method string protege les bornes < et > par &lt; et &gt;
		 * @param string $p_texte texte à proteger
		 * @return string texte protegé
		 * @access private
		 */
		private function protect_xml($p_texte)
		{
			$p_texte = rawurlencode($p_texte);
		
			return $p_texte;
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
			return 'var ajax_json = '.json_encode(Array('end_check' => $this->c_global_coherent_check_end,'qtt_err' => $this->c_global_coherent_check_qtt_err,'ssid_object_check' => $this->c_global_coherent_check_ssid,'type_object' => '__ICODE__','id_object' => $this->c_id)).';';
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