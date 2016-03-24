<?php

	define("__IFICHE__","__IFICHE__");	
	define("__ICODE__","__ICODE__");	
	
	class coherent_check extends class_bdd
	{
		// define attributs
		private $c_type_object;
		private $c_file_object;
		private $c_id_object;
		private $c_version_object;
		private $c_ssid;
		private $c_child_result;
		private $c_child_result_cursor;
		private $c_param_exclude;
		private $c_child_count;
		private $c_require_varin;
		private $c_lib_object;
		private $c_varout_object;
		private $c_qtt_error;
		private $c_query_result;
		/**
		 * Constructor of class coherent_check
		 * @param type_object $p_type_object
		 * @param integer $p_id_object ID of the object
		 * @param integer $p_version_object Version of the object
		 */
		public function __construct($p_ssid,$p_type_object,$p_id_object)
		{
			// Call constructor of inherited class
			parent::__construct($p_ssid);
			
			// Database connexion
			$this->db_connexion();
			
			// Init attributs
			$this->c_ssid = $p_ssid;
			$this->c_type_object = $p_type_object;
			$this->c_id_object = $p_id_object;
			$this->c_qtt_error = 0;
		}
		
		/**
		 * 
		 */
		public function init()
		{
			// Check if the ID exist
			if(!$this->verif_existance_id_and_version())
			{
				return false;
			}
			else
			{
				$this->c_version_object = $this->get_max_version_objet();
				$this->c_child_result_cursor = 0;
				$this->c_file_object = $this->get_file_object();
				 
				// Get exclude param
				$this->get_param_exclude();
				
				// Get required varin
				$this->get_required_varin();
				
				// Get varout of this object
				$this->get_varout();
				
				return true;
			}
		}
		
		
		/**
		 * Called when the object is deserialized
		 */
		public function __wakeup()
		{
			// Database reconnection
			$this->db_connexion();	
		}
	
		public function get_type_object()
		{
			return $this->c_type_object;
		}
		
		public function get_id_object()
		{
			return $this->c_id_object;
		}	
		
		
		/**
		 * Get max version if the object
		 */
		private function get_max_version_objet()
		{
			/**==================================================================
			 * PREPARATION DE LA REQUETE
			 ====================================================================*/			
			switch ($this->c_type_object)
			{
				case __IFICHE__:
					$sql = 'SELECT num_version as max_version  
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].'  
							WHERE id_fiche = '.$this->c_id_object;
					break;
				case __ICODE__:
					$sql = 'SELECT version as max_version  
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].'  
							WHERE id = '.$this->c_id_object;
					break;
			}		
			/*===================================================================*/
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			return mysql_result($resultat,0,'max_version');
		}
	
		
		/**
		 * 
		 */
		private function verif_existance_id_and_version()
		{
			/**==================================================================
			 * Verification de l'existance de l'ID
			 ====================================================================*/			
			switch($this->c_type_object) 
			{
				case __IFICHE__:
					$sql = 'SELECT id_fiche  
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'].' 
							WHERE id_fiche = '.$this->c_id_object;
					break;
				case __ICODE__:
					$sql = 'SELECT ID 
							FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'].' 
							WHERE ID = '.$this->c_id_object;
					break;
			}
			
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			if(mysql_num_rows($resultat) == 0)
			{	
				return false;
			}
			else
			{
				return true;
			}	
			/*===================================================================*/	
		}
	
		
		/**
		 * 
		 */
		private function get_varout()
		{
			switch($this->c_type_object) 
			{
				case __IFICHE__:
					$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'];
					$champ_version = 'num_version';
					$champ_id = 'id_fiche';
					$id_action = 'id_action';
					break;
				case __ICODE__:
					$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'];
					$id_action = '0 as id_action';
					$champ_version = 'version';
					$champ_id = 'id';
					break;
			}
			
			$sql = 'SELECT NOM,'.$id_action.'
					FROM '.$table.'
					WHERE '.$champ_id.' = '.$this->c_id_object.'
					AND '.$champ_version.' = '.$this->c_version_object.'
					AND TYPE <> "IN"';
			
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$this->c_varout_object[$row['id_action']][$row['NOM']] = $row['NOM'];
			}
		}
		
		/**
		 * 
		 */
		private function get_required_varin()
		{
			switch($this->c_type_object) 
			{
				case __IFICHE__:
					$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'];
					$champ_version = 'num_version';
					$champ_id = 'id_fiche';
					$table_objet = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches']['name'];
					$table_objet_max = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name'];
					$libelle_objet = 'la fiche';
					break;
				case __ICODE__:
					$table = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'];
					$champ_version = 'version';
					$champ_id = 'id';
					$table_objet = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes']['name'];
					$table_objet_max = $_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_codes']['name'];
					$libelle_objet = 'le code';
					break;
			}
			
			$sql = 'SELECT Nom 
					FROM '.$table.' 
					WHERE '.$champ_id.' = '.$this->c_id_object.' 
					AND '.$champ_version.' = '.$this->c_version_object.' 
					AND IFNULL(length(DEFAUT),0) = 0
					AND IFNULL(length(NEUTRE),0) = 0
					AND TYPE = "IN"';
	
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				$this->c_require_varin[] = $row;
			}
		}
		
		
		/**
		 * 
		 */
		private function get_file_object()
		{
			switch($this->c_type_object) 
			{
				case __IFICHE__:
					$this->c_lib_object = $_SESSION[$this->c_ssid]['message'][539];
					return 'ifiche.php';
					break;
				case __ICODE__:
					$this->c_lib_object = $_SESSION[$this->c_ssid]['message'][540];
					return 'icode.php';
					break;
			}
		}
		
		
		/**
		 * 
		 */
		private function get_param_exclude()
		{
			$this->c_param_exclude = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
		}

		
		/**
		 * 
		 */
		public function check_child_object()
		{
			$reserved_word = explode('|',$_SESSION[$this->c_ssid]['configuration'][19]);
			$tab_navigation_keyword = $reserved_word[3];
			switch($this->c_type_object) 
			{
				case __ICODE__:
					$sql = " SELECT
							FIC.`id_fiche`,
							FIC.`num_version`,
							FIC.`date`,
							FIC.`id_statut` as status,
							STEP.`id_etape`,
							STEP.`description`,
							'__WAIT__' as CONTROL_STATUS,
							'' as ICON,
							'' as MESSAGE_CONTROL,
							CONCAT('<a href=\"../../ifiche.php?ID=',FIC.`id_fiche`,'&".$tab_navigation_keyword."=2_2_',STEP.`id_etape`,'\" target=\"_blank\">".$_SESSION[$this->c_ssid]['message'][530]."</a>') as LINK
							FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name']."` FIC STRAIGHT_JOIN 
							`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name']."` STEP
							WHERE 1 = 1
							AND STEP.`id_fiche` = FIC.`id_fiche`
							AND STEP.`num_version` = FIC.`num_version`
							AND STEP.`description` REGEXP '(icode.php\\\\?*ID=".$this->c_id_object."[^0-9])'
							AND FIC.`obsolete` = 0
							ORDER BY FIC.`id_fiche` ASC, STEP.`id_etape` ASC";
					break;
				case __IFICHE__:
					$sql = " SELECT
							FIC.`id_fiche`,
							FIC.`num_version`,
							FIC.`id_statut` as status,
							FIC.`date`,
							STEP.`id_etape`,
							STEP.`description`,
							'__WAIT__' as CONTROL_STATUS,
							CONCAT('<a href=\"../../ifiche.php?ID=',FIC.`id_fiche`,'&".$tab_navigation_keyword."=2_2_',STEP.`id_etape`,'\" target=\"_blank\">".$_SESSION[$this->c_ssid]['message'][530]."</a>') as LINK,
							'' as ICON,
							'' as MESSAGE_CONTROL
							FROM `".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_max_fiches']['name']."` FIC STRAIGHT_JOIN 
							`".$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_etapes']['name']."` STEP
							WHERE 1 = 1
							AND STEP.`id_fiche` = FIC.`id_fiche`
							AND STEP.`num_version` = FIC.`num_version`
							AND STEP.`description` REGEXP '(ifiche.php\\\\?*ID=".$this->c_id_object."[^0-9])'
							AND FIC.`obsolete` = 0
							ORDER BY FIC.`id_fiche` ASC, STEP.`id_etape` ASC";
					break;
			}
			
			$begin = microtime(true);
			$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			$fin= microtime(true);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
			{
				$this->c_child_result[] = $row;
			}
			
			$this->c_child_count = count($this->c_child_result);
		}
		
		/**
		 * 
		 */
		public function get_array_child_result()
		{
			return $this->c_child_result;
		}
		
		
		/**
		 * 
		 */
		public function get_query_child()
		{
			$query = '';
			$i = 0;
			foreach($this->c_child_result as $key => $value)
			{
				if($value['CONTROL_STATUS'] == '__ERROR__')
				{
					if($i == 0)
					{
						$query = 'SELECT
									'.$value['id_fiche'].' as id_fiche,
									'.$value['num_version'].' as num_version,
									'.$value['id_etape'].' as id_etape,
									"'.str_replace('"','\"',$value['CONTROL_STATUS']).'" as CONTROL_STATUS,
									"'.$value['date'].'" as date,
									"'.str_replace('"','\"',$value['description']).'" as description,
									"'.$value['status'].'" as status,"'.str_replace('"','\"',$value['ICON']).'" as ICON,
									"'.str_replace('"','\"',$value['MESSAGE_CONTROL']).'" as MESSAGE_CONTROL,
									"'.str_replace('"','\"',$value['LINK']).'" as LINK';
					}
					else 
					{
						 $query .= ' UNION SELECT '.$value['id_fiche'].' as id_fiche,'.$value['num_version'].' as num_version, '.$value['id_etape'].' as id_etape,"'.str_replace('"','\"',$value['CONTROL_STATUS']).'" as CONTROL_STATUS,"'.$value['date'].'" as date,"'.str_replace('"','\"',$value['description']).'" as description,"'.$value['status'].'" as status,"'.str_replace('"','\"',$value['ICON']).'" as ICON,"'.str_replace('"','\"',$value['MESSAGE_CONTROL']).'" as MESSAGE_CONTROL,"'.str_replace('"','\"',$value['LINK']).'" as LINK';
					}
					$i++;
				}
			}
			if($query == '')
			{
				$query = 'SELECT * FROM (SELECT "" as id_fiche,"" as num_version,"" as id_etape,"" as CONTROL_STATUS,"" as date,"" as description,"" as status,"" as ICON,"" as MESSAGE_CONTROL,"" as LINK) t WHERE 1 = 2';
			}
			return $query;
		}
		
		
		/**
		 * 
		 */
		public function get_array_child_json()
		{
			return 'var child_json = '.json_encode($this->c_child_result).';';
		}
		
		
		/**
		 * 
		 */
		public function check_next_child()
		{
			if(count($this->c_child_result) > 0)
			{
				// Get the link
				$this->analyse_lien($this->c_child_result[$this->c_child_result_cursor]['description'],$this->c_child_result[$this->c_child_result_cursor]['id_etape'],$this->c_file_object,$this->c_child_result[$this->c_child_result_cursor]['date'],$this->c_child_result[$this->c_child_result_cursor]['status']);

				if(!isset($this->c_child_result[$this->c_child_result_cursor]['link']))
				{
					$var_call = $this->cut_url('');
				}
				else
				{
					// Get the var of the call
					$var_call = $this->cut_url($this->c_child_result[$this->c_child_result_cursor]['link'][0][0]);
				}
				
				// Control if the called varin exist
				$this->check_varin($var_call);
				
				// Control if all required were passed
				$this->check_required_varin($var_call);

			
				// Control the varout
				$this->check_varout();
				
				
				/**==================================================================
				* Control resume
				====================================================================*/
				if($this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS'] != '__ERROR__')
				{	
					$this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS'] = '__OK__';
				}
				
				switch($this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS']) 
				{
					case '__ERROR__':
						$this->c_child_result[$this->c_child_result_cursor]['ICON'] = '<a href="#" class="erreur"></a>';
						break;
					case '__OK__':
						$this->c_child_result[$this->c_child_result_cursor]['ICON'] = '<a href="#" class="ok"></a>';
						break;
				}			
				
				$this->c_child_result_cursor++;
				
				echo 'var ajax_json = '.json_encode(Array('total' => count($this->c_child_result),'cursor' => $this->c_child_result_cursor,'internal_error' => false,'qtt_error' => $this->c_qtt_error)).';';
				
				if($this->c_child_count == $this->c_child_result_cursor)
				{
					if(isset($_SESSION['vimofy'][$this->c_ssid]['vimofy']))
					{
						$_SESSION['vimofy'][$this->c_ssid]['vimofy']->define_query($_SESSION['coherence_check']->get_query_child());
					}
					else
					{
						$this->c_query_result = $this->get_query_child();
					}
				}
				/*===================================================================*/
			}
			else
			{
				echo 'var ajax_json = '.json_encode(Array('total' => 0,'cursor' => 0,'internal_error' => false,'qtt_error' => $this->c_qtt_error)).';';
			}
		}
		
		public function is_last()
		{
			if($this->c_child_count == $this->c_child_result_cursor)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		/**
		 *  Check varin
		 */
		private function check_varin(&$var_call)
		{
			$niveau_erreur = 2;		// Défini le niveau de l'erreur si il y en a une.
			$key_array = 0;
			$erreur = false;
		
			/**==================================================================
			* Check if the called varin exist into this object
			====================================================================*/	
			$select = '';

			foreach($var_call as $value_var_appel) 
			{
				if(!$this->in_arrayi($value_var_appel[0], $this->c_param_exclude))
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
				switch($this->c_type_object) 
				{
					case __IFICHE__:
						if (!isset($this->c_child_result[$this->c_child_result_cursor]['link'][0][1]))
						{
							$sql = 'SELECT "" "nom";';
						}
						else
						{
							$sql = 'SELECT t.nom 
									FROM ('.$select.') t
									WHERE t.nom_md5 NOT IN (SELECT MD5(i.nom)
												FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].' i
												WHERE i.id_fiche = "'.$this->c_id_object.'" 
												AND i.num_version  = "'.$this->c_child_result[$this->c_child_result_cursor]['link'][0][1].'"
												AND i.`TYPE` = "IN")';
						}
						break;
					case __ICODE__:
						$sql = 'SELECT t.nom 
								FROM ('.$select.') t
								WHERE t.nom_md5 NOT IN (SELECT MD5(i.nom)
											FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_codes_param']['name'].' i
											WHERE i.ID = "'.$this->c_id_object.'"
											AND i.Version  = "'.$this->c_child_result[$this->c_child_result_cursor]['link'][0][1].'" 
											AND i.`TYPE` = "IN")';
						break;
				}

				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

				$niveau_erreur = 2;
				
				if(mysql_num_rows($resultat) > 0)
				{
					while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
					{
						if(isset($row['nom']) && $row['nom'] != '')
						{
			       			$libelle = str_replace('$param','<b>'.$row['nom'].'</b>',$_SESSION[$this->c_ssid]['message'][541]);
			       			$libelle = str_replace('$objet',$this->c_lib_object,$libelle);
			       			$libelle = str_replace('$id',$this->c_id_object,$libelle);
			       			
							$this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS'] = '__ERROR__';
							$this->c_child_result[$this->c_child_result_cursor]['MESSAGE_CONTROL'] .= $libelle.'<br />';
							$key_array = $key_array + 1;
							$this->c_qtt_error++;
							$erreur = true; 
						}
					}
				}
			}
			/**==================================================================
			 * FIN VERIFICATION DE L'EXISTANCE DES PARAMETRES D'APPEL EN ENTREE DE L'OBJET
			 ====================================================================*/	
		}
		
		
		/**
		 * 
		 */
		private function check_required_varin(&$var_call)
		{
			$key_array = 0;
			$erreur = false;
			
			if(is_array($this->c_require_varin))
			{
				foreach($this->c_require_varin as $variable)
				{
					$trouve = false;
					foreach($var_call as $key => $value)
					{
						if($variable['Nom'] == urldecode($value[0]))
						{
							$trouve = true;
						}
					}
					
					if(!$trouve)
					{
						$libelle = str_replace('$j',$this->c_child_result[$this->c_child_result_cursor]['id_etape'],$_SESSION[$this->c_ssid]['message'][542]);
						$libelle = str_replace('$objet',$this->c_lib_object,$libelle);
						$libelle = str_replace('$id',$this->c_id_object,$libelle);
						$libelle = str_replace('$version','',$libelle);
						$libelle = str_replace('$param','<span class="BBVarin">'.$variable['Nom'].'</span>',$libelle);
						$this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS'] = '__ERROR__';
						$this->c_child_result[$this->c_child_result_cursor]['MESSAGE_CONTROL'] .= $libelle.'<br />';
						$key_array = $key_array + 1;
						$this->c_qtt_error++;
						$erreur = true; 
					}
				}
			}
		}
		
		
		/**
		 * 
		 */
		private function check_varout()
		{
			$key_array = 0;
			$erreur = false;
			
			// Get the varinext of the child object
			$sql = 'SELECT NOM,id_action_src 
					FROM '.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_fiches_param']['name'].'
					WHERE id_fiche = '.$this->c_child_result[$this->c_child_result_cursor]['id_fiche'].'
					AND num_version = '.$this->c_child_result[$this->c_child_result_cursor]['num_version'].'
					AND id_src = '.$this->c_id_object.'
					AND id_action = '.$this->c_child_result[$this->c_child_result_cursor]['id_etape'];
			
			$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($requete,MYSQL_ASSOC)) 
			{
				if(!isset($this->c_varout_object[$row['id_action_src']][$row['NOM']]))
				{
					switch ($this->c_type_object)
					{
						case __IFICHE__:
							$libelle = str_replace('$param','<span class="BBVarinExt">'.$row['NOM'].'</span>',$_SESSION[$this->c_ssid]['message'][537]);
							$libelle = str_replace('$step',$row['id_action_src'],$libelle);
							break;
						case __ICODE__:
							$libelle = str_replace('$param','<span class="BBVarinExt">'.$row['NOM'].'</span>',$_SESSION[$this->c_ssid]['message'][538]);
							break;
					}	
					$this->c_child_result[$this->c_child_result_cursor]['CONTROL_STATUS'] = '__ERROR__';
					$this->c_child_result[$this->c_child_result_cursor]['MESSAGE_CONTROL'] .= $libelle.'<br />';
					$key_array = $key_array + 1;
					$this->c_qtt_error++;
					$erreur = true;
				
				}
			}
		}	

		
		/**
		 * @param unknown_type $needle
		 * @param unknown_type $haystack
		 */
	    private function in_arrayi($needle, $haystack) 
	    {
	        return in_array(strtolower($needle), array_map('strtolower',$haystack));
	    }
	    
    
		/**
		 * Identifie les liens pointant sur des iObjets dans l'étape $id_etape
		 * 
		 * @param string $txt text to analyse
		 * @param integer $id_etape Step ID
		 * @param string $iobjet Object type
		 */
		private function analyse_lien($txt,$id_etape,$iobjet,$iobject_date,$iobject_status)
		{
			$ref = $txt;	
			while(strlen($ref) > 0 ) 
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
						preg_match('#ID=('.$this->c_id_object.')#',$parametres,$temp);
						
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
							// A version was defined in the URL
							$version_iobjet_lue = $temp[1];
							$version_iobjet_finale = $version_iobjet_lue;
						}
						else
						{
							// No version defined in the URL
							switch($iobjet)
							{
								case 'icode.php':
									if($iobject_status < $_SESSION[$this->c_ssid]['configuration'][32])
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
																		AND last_update_date < "'.$iobject_date.'"
																		ORDER BY Version DESC  
																		LIMIT 1';
									}	
									break;
								
								case 'ifiche.php':
									if($iobject_status < $_SESSION[$this->c_ssid]['configuration'][32])
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
																	AND date < "'.$iobject_date.'" 
																	ORDER BY num_version DESC  
																	LIMIT 1';
									}
									break;
									
								case 'idossier.php':	// TODO
									$sql_version_iobjet_finale = 'SELECT 1 as version';
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
						$this->c_child_result[$this->c_child_result_cursor]['link'][] = array(0 => $parametres,1 => $version_iobjet_finale,2 => $version_iobjet_lue);
						return false;
						if($version_iobjet_finale == '')
						{
							// On sort de la boucle, l'objet n'existe pas.
							break;
						}		
					}
					
					// On décalle pour analyser le lien suivant
					$ref = substr($ref,3,strlen($ref)-3);	
				}
				else
				{
					// On décalle pour analyser le lien suivant
					$ref = substr($ref,3,strlen($ref)-3);	
				}
			}
		}
	
		
		/**
		 * Cut the URL
		 * @param string $parametres parameters of the URL
		 */
		private function cut_url($parametres)
		{
			/**==================================================================
			 * ANALYSE DE L'URL
			 ====================================================================*/
			$parametre_url_get = html_entity_decode($parametres);					
			$parametre_url_get = substr($parametre_url_get,1);						// Supprime le ? au début de l'URL		
			$parametre_url_get = explode('&',$parametre_url_get);					
			foreach($parametre_url_get as $value)									
			{																		
				$valorisation_variable[] = explode('=',$value);						
			}																		
			/*===================================================================*/
																					
			return $valorisation_variable;											
		}
		
		public function get_qtt_error()
		{
			return $this->c_qtt_error;
		}
	}
?>