<?php
/**==========================================================================================================
 * 									Main class for iSheet controls
 ==========================================================================================================*/
class check extends class_bdd
{
	
	// Déclarations des attributs
	var $id;						// Contient l'id de la fiche en cours.
	var $version;					// Contient la version de la fiche en cours.
	var $niveau_informations;		// Contient le niveau d'informations, peut être: 0(information), 1(warning), 2(erreur), 3(question)
	var $language;					// Contient la langue du système
	var $ssid;						// window identifier to define each window
	var $id_temp;					// temporary id to store session data

	
	/**==================================================================
	 * Class constructor
	 * $p_id : iSheet ID identifier
	 * $p_version : iSheet version
	 * $p_language : Language identifier
	 * $p_ssid : Session identifier
	 * $p_id_temp : temporary id
	 * @access public
	 ====================================================================*/
	public function __construct($p_id,$p_version,$p_language,$p_ssid,$p_id_temp) 
	{
		parent::__construct($p_ssid,$p_id,$p_id_temp,$p_version,__FICHE_MODIF__);
		
		$this->db_connexion();
		$this->id = $p_id;
		$this->version = $p_version;
		$this->language = $p_language;
		$this->ssid = $p_ssid;
		$this->id_temp = $p_id_temp;
		$this->niveau_informations = 0;
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
	 * Is it the last version ?
	 * @return true if current version is the last, false if not
	 * @access public
	 ====================================================================*/
	public function is_last_version() 
	{
		
		if ($this->id == 'new')
		{
			return true;
		}
		else
		{
			// Build query
			$sql =	"SELECT
							`num_version` AS 'max_version' 
						FROM
							`".$_SESSION['iknow'][$this->ssid]['struct']['tb_max_fiches']['name']."` 
						WHERE 1 = 1
							AND `id_fiche` = ".$this->id.";
					";
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				
				$this->maxversion = $row['max_version'];
				
			}
			
			if ($this->maxversion == $this->version) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}	
	}
	/*===================================================================*/
	

	/**==================================================================
	 * Is it the last version ?
	 * Set number version in internal attribut
	 * &p_version : Version number of iSheet
	 * @access public
	 ====================================================================*/
	public function set_version($p_version)
	{
		$this->version = $p_version;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Control if current area is the same than the preview version area
	 * &p_pole : Current area code
	 * @return true if area is the same, false if not
	 * @access private
	 ====================================================================*/
	private function verif_pole($pole)
	{
		if ($this->id == 'new')
		{
			return true;
		}
		else
		{
			// Build query
			$sql = "	SELECT
							`id_pole` 
						FROM 
							`".$_SESSION['iknow'][$this->ssid]['struct']['tb_fiches']['name']."` 
						WHERE 1 = 1
							AND `id_fiche` = ".$this->id."
							AND `num_version` = ".$this->version;
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				
				$pole_sql = $row['id_pole'];
				
			}
			
			if($pole != $pole_sql)
			{		
				return false;	
			}
			else
			{
				return true;
			}
		}
	}
	/*===================================================================*/
	

	/**==================================================================
	 * Get information level
	 * @return level information ( means error level )
	 * @access public
	 ====================================================================*/
	public function return_niveau_informations()
	{
		if($_SESSION[$this->ssid]['niveau_informations'] > $this->niveau_informations)
		{
			return $_SESSION[$this->ssid]['niveau_informations'];
		}
		else
		{
			return $this->niveau_informations;
		}
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Set information level ( means error level )
	 * @access public
	 ====================================================================*/
	public function set_niveau_informations($niveau)
	{
		$this->niveau_informations = $niveau;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Set number id
	 * $id : number to set in internal attribut id
	 * @access public
	 ====================================================================*/
	public function set_id($id)
	{
		$this->id = $id;
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Main iSheet control
	 * Title in steps
	 * Empty step
	 * Description
	 * Pre requisite
	 * Main iSheet title
	 * Mandatory trigram
	 * iSheet version
	 * Area code
	 * @access public
	 ====================================================================*/
	function sheet_control($titre,$description,$prerequis,$trigramme,$pole,$version,$activite,$niveau)
	{
		$key_array = 0;
		$erreur = false;
		$verification = array();
		$eval_js = '';
		$this->niveau_informations = 0; // Reset error level to zero
	
	
		//==================================================================
		// Check iSheet version
		//==================================================================
		if(!$this->is_last_version() && $this->id != 'new')
		{
			$this->niveau_informations = 1;
	
			$erreur = true;	
			$verification[$key_array]['criticite'] = 1; // Warning
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',10,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');');
			$key_array++;
		}
		//==================================================================
	
		//==================================================================
		// Check area ( control if no area choosen )
		//==================================================================
		if($pole == '')
		{
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2;	// Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',111,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_pole_lmod\');');
			$erreur = true;
			$erreur_pole = true;	
			$key_array++;
		}
		//==================================================================
		
		//==================================================================
		// Check version ( control if no version choosen )
		//==================================================================
		if($version == '')
		{
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2;	// Error 
			if(isset($erreur_pole))
			{
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',112,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_pole_lmod\');');
			}
			else
			{
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',112,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_vers_pole_lmod\');');	
			}
			$erreur = true;	
			$erreur_pole_version = true;	
			$key_array++;
		}
		//==================================================================
		
			
		//==================================================================
		// Check activity ( control if no activity choosen )
		//==================================================================
		if($activite == '')
		{
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			if(isset($erreur_pole))
			{
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',113,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_pole_lmod\');');
			}
			else
			{
				if(isset($erreur_pole_version))
				{
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',113,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_vers_pole_lmod\');');
				}
				else
				{
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',113,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_activite_lmod\');');
				}
			}
			
			$erreur = true;	
			$erreur_activite = true;
			$key_array++;
		}	
		//==================================================================
		
		//==================================================================
		// Check level ( control if work and subwork choosen )
		//==================================================================
		if($niveau == '' )
		{
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			if(isset($erreur_pole))
			{
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',114,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_pole_lmod\');');
			}
			else
			{
				if(isset($erreur_pole_version))
				{
					$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',114,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_vers_pole_lmod\');');
				}
				else
				{
					if(isset($erreur_activite))
					{
						$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',114,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_activite_lmod\');');
					}
					else
					{
						$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',114,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');vimofy_open_lmod(\'vimofy2_module_lmod\');');
					}
				}
					
			}
			
			$erreur = true;	
			$key_array++;
		}	
		//==================================================================
		
		//==================================================================
		// Check if current area is the same than preview area version
		//==================================================================
		if($pole != '' && $version != '' && $activite != '' && $niveau != '')
		{
			if(!$this->verif_pole($pole)){
				$this->niveau_informations = 1;
				$verification[$key_array]['criticite'] = 1; // Warning
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',11,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');');
				$erreur = true;	
				$key_array++;
			}
		}
		//==================================================================
		
		//==================================================================
		// Check iSheet title
		//==================================================================
		$title_error = false;
		if($titre == '')
		{
			// No title : Record specific error message
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',12,'document.getElementById(\'titre\').style.backgroundColor= \'#FF866A\';a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');document.getElementById(\'titre\').focus();');
			$erreur = true;
			$title_error = true;
			$key_array++;
		}
		elseif(strlen($titre) < $_SESSION[$this->ssid]['configuration'][9])
		{	
			// Check minimum length of title
			$message_titre = str_replace('$j',strlen($titre),$_SESSION[$this->ssid]['message'][13]);
			$message_titre = str_replace('$k',$_SESSION[$this->ssid]['configuration'][9],$message_titre);
			
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] =  $this->generer_ligne_erreur('erreur',$message_titre,'document.getElementById(\'titre\').style.backgroundColor= \'#FF866A\';a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');document.getElementById(\'titre\').focus();');
			$erreur = true;	
			$title_error = true;
			$key_array++;
		}
		elseif(strlen($titre) > $_SESSION[$this->ssid]['configuration'][26])
		{		
			// Title length too long
			$erreur = true;
			$title_error = true;
			$message_titre = $_SESSION[$this->ssid]['message'][109];
			$message_titre = str_replace('$j',strlen($titre),$message_titre);
			$message_titre = str_replace('$k',$_SESSION[$this->ssid]['configuration'][26],$message_titre);
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message_titre,'a_tabbar.setTabActive(\'tab-level1\');document.getElementById(\'titre\').focus();');										
			$this->niveau_informations = 2;	//mise de niveau d'information à erreur	
			$key_array++;			
		}
	
		if($title_error)
		{
			$eval_js .= 'document.getElementById(\'titre\').style.backgroundColor= \'#FF866A\';';
		}
		//==================================================================
		
		//==================================================================
		// Check iSheet description
		//==================================================================
		//error_log('r'.$this->c_description_raw_text);
		if($description == '')
		{
			// No description
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',14,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');editer_description();');		
			$erreur = true;
			$key_array++;
		}
		elseif(strlen($description) < $_SESSION[$this->ssid]['configuration'][10])
		{		
			// Description length too short ( configuration reference 10 )		
			$message_description = str_replace('$j',strlen($description),$_SESSION[$this->ssid]['message'][15]);
			$message_description = str_replace('$k',$_SESSION[$this->ssid]['configuration'][10],$message_description);
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message_description,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');editer_description();');	
			$erreur = true;	
			$key_array++;
		}
		//==================================================================
		
		//==================================================================
		// Check iSheet prerequisite
		//==================================================================
		if($prerequis == '')
		{
			// No prerequisite
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',16,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_2\');editer_prerequis();');
			$erreur = true;
			$key_array++;
		}
		elseif(strlen($prerequis) < $_SESSION[$this->ssid]['configuration'][11])
		{	
			// Prerequisite toot short ( configuration reference 11 )
			$message_prerequis = str_replace('$j',strlen($prerequis),$_SESSION[$this->ssid]['message'][17]);
			$message_prerequis = str_replace('$k',$_SESSION[$this->ssid]['configuration'][11],$message_prerequis);
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error 
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message_prerequis,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_2\');editer_prerequis();');
			$erreur = true;
			$key_array++;	
		} 
		//==================================================================
		
		//==================================================================
		// Check trigram
		//==================================================================
		$trigramme_error = false;	
		if($trigramme == '')
		{
			// No trigram
			$this->niveau_informations = 2;
			$verification[$key_array]['criticite'] = 2; // Error 
			$verification[$key_array]['message'] =  $this->generer_ligne_erreur('erreur',18,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');document.getElementById(\'modifie_par\').focus();');
			$erreur = true;
			$trigramme_error = true;
			$key_array++;
		}
		elseif(strlen($trigramme) < 3)
		{	
			// Trigram too short	
			$this->niveau_informations = 2;	//mise de niveau d'information à erreur
			$verification[$key_array]['criticite'] = 2;
			$verification[$key_array]['message'] =  $this->generer_ligne_erreur('erreur',19,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');document.getElementById(\'formulaire_entete\').focus();');
			$erreur = true;
			$trigramme_error = true;
			$key_array++;
		}
		elseif(strlen($trigramme) > 3)
		{	
			// Trigram too long	
			$this->niveau_informations = 2;	//mise de niveau d'information à erreur
			$verification[$key_array]['criticite'] = 2;
			$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',25,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');document.getElementById(\'formulaire_entete\').focus();');
			$erreur = true;
			$trigramme_error = true;
			$key_array++;	
		}
		
		if($trigramme_error)
		{
			$eval_js .= 'document.getElementById(\'modifie_par\').style.backgroundColor= \'#FF866A\';';
		}
		//==================================================================
					
		$array_verif['eval_js'] = $eval_js;
		$array_verif['verif'] = $verification;
		return $array_verif;
	}
	/*===================================================================*/

	
	/**==================================================================
	 * Generate duplicate message
	 * @access public
	 ====================================================================*/
	function generer_message_duplication()
	{	
		return '<table id="informations">'.$this->generer_ligne_erreur('ok',22,'').'</table>';	
	}
	/*===================================================================*/



/**
 * Cette methode permet de verifier les tag d'une fiche
 * 
 * 
 */
function check_tag(){
	
	$verification = array();
	$message_final = '<table id = "informations">';		
	$key_array = 0;
	$erreur = false;	
	
	//------------------------------------------On verifie que un tag comporte au moins x caractères.------------------------------------
	
	
	//On recupere les configurations dans la base (nbr de caractère minimum strict)
	$valeur_base = $_SESSION[$this->ssid]['configuration'][12];
		
	
	
	//On va verifier les tag
									
	$sql = 'SELECT Tag,Etape,Groupe FROM '.$_SESSION['iknow'][$this->ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->id_temp." AND objet = \"ifiche\"";

	$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	
	while ($row = mysql_fetch_array($requete,MYSQL_ASSOC))
	{
		
		//On verifie le tag
		if(strlen($row['Tag']) <= $valeur_base){
			
			if($row['Etape'] == 0)
			{
				$erreur = true;
				$message = str_replace('$Emplacement',$_SESSION[$this->ssid]['message'][41],$_SESSION[$this->ssid]['message'][37]);
				$message = str_replace('$nom',$row['Tag'],$message);
				$message = str_replace('$j',$valeur_base + 1,$message);
				$verification[$key_array]['criticite'] = 2;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_4\');');
				
				$key_array++;
			}
			else
			{
				$erreur = true;
				$etape = $row['Etape'];	
				$message = str_replace('$Emplacement',$_SESSION[$this->ssid]['message'][69].' '.$etape,$_SESSION[$this->ssid]['message'][37]);
				$message = str_replace('$nom',$row['Tag'],$message);
				$message = str_replace('$j',$valeur_base + 1,$message);
				$verification[$key_array]['criticite'] = 2;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message,'a_tabbar.setTabActive(\'tab-level2\');vimofy_tag_etape('.$row['Etape'].',true);',$row['Etape']);
				$key_array++;
			}
			
		}
		
		//On verifie le groupe
		if(strlen($row['Groupe']) <= $valeur_base){
			
			if($row['Etape'] == 0)
			{
				$erreur = true;
				$message = str_replace('$Emplacement',$_SESSION[$this->ssid]['message'][41],$_SESSION[$this->ssid]['message'][38]);
				$message = str_replace('$nom',$row['Tag'],$message);
				$message = str_replace('$j',$valeur_base + 1,$message);
				$verification[$key_array]['criticite'] = 2;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message.$row['Groupe'],'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_4\');');
				$key_array++;
			}
			else
			{
				$erreur = true;
				$etape = $row['Etape'];	
				$message = str_replace('$Emplacement',$_SESSION[$this->ssid]['message'][69].' '.$etape,$_SESSION[$this->ssid]['message'][38]);
				$message = str_replace('$nom',$row['Tag'],$message);
				$message = str_replace('$j',$valeur_base + 1,$message);
				$verification[$key_array]['criticite'] = 2;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('erreur',$message.$row['Groupe'],'a_tabbar.setTabActive(\'tab-level2\');vimofy_tag_etape('.$row['Etape'].',true);',$row['Etape']);
				$key_array++;
				
			}		
		}	
	}
	
	
	if($erreur)
	{
		if($this->niveau_informations < 2){
			
			$this->set_niveau_informations(2);			
			
		}	
	}
	
	
	
	//------------------------------------------On verifie que le tag comporte au maximum 1 mot-------------------------------------------
	$sql = 'SELECT Tag,Etape FROM '.$_SESSION['iknow'][$this->ssid]['struct']['tb_tags']['name'].' WHERE ID = '.$this->id_temp." AND objet = \"ifiche\"";

	$requete = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
	while ($row = mysql_fetch_array($requete,MYSQL_ASSOC))
	{	
		$tab_mot = explode(' ',$row['Tag']);
		$message = '';

		if(count($tab_mot) > 1)
		{
			$erreur = true;
			if($row['Etape'] == 0)
			{
				$etape = 'Fiche';
				$message = str_replace('Etape $j',$etape,$_SESSION[$this->ssid]['message'][34]);
				$message = str_replace('$tag',$row['Tag'],$message);
				$verification[$key_array]['criticite'] = 1;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',$message,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_4\');');		
				$key_array++;
			}
			else
			{
				$etape = $row['Etape'];
				$message = str_replace('$j',$etape,$_SESSION[$this->ssid]['message'][34]);
				$message = str_replace('$tag',$row['Tag'],$message);
				$verification[$key_array]['criticite'] = 1;
				$verification[$key_array]['message'] = $this->generer_ligne_erreur('warning',$message,'a_tabbar.setTabActive(\'tab-level2\');',$row['Etape']);
				$key_array++;
			}																									
		}
	}	
	
	if($erreur)
	{
		if($this->niveau_informations < 1)
		{
			$this->set_niveau_informations(1);	
		}
	}
	
	return $verification;
		
}



function get_no_error_msg($xml = false)
{
	$retour = '<table id="informations">';
	$retour .= $this->generer_ligne_erreur('ok',39,'a_tabbar.setTabActive(\'tab-level1\');head_tabbar.setTabActive(\'tab-level1_1\');');
	$retour .= '</table>';
	
	if($xml)
	{
		// XML return	
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF8'?>";
		echo "<parent>";
		echo "<message_controle>".$this->protect_xml($retour)."</message_controle>";
		echo "<titre_controle>".$this->protect_xml($this->generer_bandeau_informations())."</titre_controle>";
		echo "<niveau_erreur>".$_SESSION[$this->ssid]['niveau_informations']."</niveau_erreur>";
		echo "</parent>";	
	}
	else
	{
		return $retour;
	}
}



/**
 * Methode qui permet de generer le bandeau de la barre d'informations
 * 
 * @param text type valeurs possibles: information, warning, erreur
 * 
 */
function generer_bandeau_informations($verification_lancement = false,$titre_debug = '')
{
	if($titre_debug != '')
	{
		$titre_debug = '   @   '.$titre_debug;	
	}
	
	if(isset($_SESSION[$this->ssid]['niveau_informations']))
	{
	
		if ($_SESSION[$this->ssid]['niveau_informations'] > $this->niveau_informations)
		{
			
			$this->niveau_informations = $_SESSION[$this->ssid]['niveau_informations'];
			
		}
		
	}
	
	if($verification_lancement == true)
	{
		$titre_lancement = ' - '.$_SESSION[$this->ssid]['message'][196];
	}
	else
	{
		$titre_lancement = '';
	}
	// on genere le bandeau d'informations
	switch ( $this->niveau_informations ) 
	{
	case 0:
		return '<table><tr><td><a href="#" class="ok"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->ssid]['message'][153].$titre_lancement.$titre_debug.'</td></tr></table>';
		break;
	case 1:
		return '<table><tr><td><a href="#" class="warning"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->ssid]['message'][154].$titre_lancement.$titre_debug.'</td></tr></table>';
		break;
	case 2:
		return '<table><tr><td><a href="#" class="erreur"></a></td><td class="td_mini iknow_titre_controle">'.$_SESSION[$this->ssid]['message'][152].$titre_lancement.$titre_debug.'</td></tr></table>';
		break;

	}
	
	
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
	
	private function generer_ligne_erreur($class_erreur,$id_message_erreur,$action_click,$ancre = null)
	{
		
		if(is_numeric($id_message_erreur))
		{
			$message = $_SESSION[$this->ssid]['message'][$id_message_erreur];
		}
		else
		{
			$message = $id_message_erreur;
		}
		
		if(is_null($ancre))
		{
			$href = 'href="#" onclick="javascript:'.$action_click.'iknow_panel_reduire();"';
		}
		else
		{
			$href = 'href="#'.$ancre.'" onclick="javascript:'.$action_click.'iknow_panel_reduire();"';
		}
		
		return '<tr><td><a '.$href.' class="'.$class_erreur.'"></a></td><td>&nbsp;'.$message.'</td></tr>';
		
	}
}
?>