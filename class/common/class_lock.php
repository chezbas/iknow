<?php
/**==================================================================
 * Manage lock of iObject
 ====================================================================*/		
class lock_iobjet extends class_bdd 
{
	//==================================================================
	// Public class attributs
	//==================================================================
	public $id;						// Id of current iObject
	public $ssid;						// SSID of currnet iObject
	public $user;						// IP adress of updating user.
	public $type;						// Kind of lock : 	1 means iSheet updating
									//					2 means iSheet display
									//					3 means iCode updating
									//					4 means iCode display
	public $id_temp;
	//==================================================================
	
	
	/**==================================================================
	 * Constructor of lock_iobjet
	 * @param p_type : 	Kind of iObject lock
	 * @param p_id :	Id of current iObject
	 * @param p_ssid :	SSID feature of current iObject
	 * @param p_user : 	IP address of user updating iObject
	====================================================================*/		
	function __construct($p_type,$p_id,$p_ssid,$p_user) 
	{
		parent::__construct($p_ssid, $p_id, 0, 0, $p_type);

		$this->db_connexion();
		$this->id = $p_id;
		$this->ssid = $p_ssid;
		$this->user = $p_user;
		$this->type = $p_type;
		$this->id_temp = 0;
	}
	/*===================================================================*/	

	
	/**==================================================================
	 * Wakup ( reconnect to database ) when iObject is unserialized
	 * public
	====================================================================*/		
	public function __wakeup()
	{
		$this->db_connexion();	
	}
	/*===================================================================*/	
	
	
	public function purge_cookie()
	/**==================================================================
	 * Clear iKnow cookies on local web browser
	 * public
	====================================================================*/		
	{
		$i = 0;
		$cookie_delete = '';
		$chaine_l = strlen(__PREFIX_URL_COOKIES__);
		foreach($_COOKIE as $ssid => $value)
		{
			// Clear cookies which starteb by IK_...
			if(substr($ssid,0,$chaine_l) == __PREFIX_URL_COOKIES__)
			{
				$sql = 'SELECT
							1
						FROM
							`'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'`
						WHERE 1 = 1
							AND `ssid` = "'.$ssid.'"
							AND TIMEDIFF(NOW(),`last_update`) < "'.$_SESSION[$this->ssid]['configuration'][39].'"
					   ';
				
				$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

				if(mysql_num_rows($result) == 0)
				{
					echo 'delete_cookie(\''.$ssid.'\');';
					$cookie_delete .= $ssid.'\n';
					$i++;
				}
			}
		}
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Check if current SSID is not alreday used by another one
	 * public
	 * ALERT DOUBLON with class_ssid.php method get_usage_ssid
	====================================================================*/		
	public function get_usage_ssid()
	{
		if($this->id != 'new')
		{
			// iObject already exists
			$sql = 'SELECT
						1
					FROM
						`'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'`
					WHERE 1 = 1
						AND `ssid` = "'.$this->ssid.'"
						AND (`utilise_par` <> "'.$this->user.'" OR `id` <> '.$this->id.')';
		}
		else
		{
			// New iObject
			$sql = 'SELECT
						1
					FROM
						`'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'`
					WHERE 1 = 1
						AND `ssid` = "'.$this->ssid.'"';
		}

		$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		if(mysql_num_rows($result) > 0)
		{
			switch ($this->type) 
			{
				case 1:
					$file = 'modif_fiche.php?';
					break;
				case 2:
					$file = 'ifiche.php?';
					break;
				case 3:
					$file = 'modif_icode.php?';
					break;
				case 4:
					$file = 'icode.php?';
					break;
			}
			
			foreach ($_GET as $key => $value)
			{
				if($key != 'ssid')
				{
					$file .= $key.'='.$value;
				} 
			}
			
			$message = $this->protect_display($_SESSION[$this->ssid]['message']['iknow'][8]);
			echo '<body>
					<div id="iknow_msgbox_background"></div>
					<div id="iknow_msgbox_conteneur" style="display:none;"></div>
					<script type="text/javascript">aff_btn = new Array([decodeURIComponent(libelle[182])],["window.location.href = \''.$file.'\';"]);generer_msgbox(decodeURIComponent(libelle[152]),\''.$message.'\',\'erreur\',\'msg\',aff_btn,true);</script></body>';
			die();
		}
		
	}
	/*===================================================================*/
	
	
	/**==================================================================
	 * Generate reserved id number
	 * public
	====================================================================*/		
	public function generer_id_temporaire() 
	{
		//==================================================================
		// First, check if SSID is not already in use
		//==================================================================
		$this->get_usage_ssid();
		//==================================================================
		
		//==================================================================
		// Build query to insert line in lock table
		//==================================================================
		if($this->id == 'new')
		{
			// New iObject
			$insert_id = "	REPLACE
							INTO `".$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name']."`
								(`type`, `id`, `date_mod`,`last_update`, `utilise_par`, `ssid`,`id_temp`,`version_client`)
								VALUES (".$this->type.",@free_id,NOW(),NOW(),'".$this->user."','".$this->ssid."',@free_id,'".getenv("HTTP_USER_AGENT")."');";
		}
		else
		{
			// Iobject already exists
			$insert_id = "	REPLACE
							INTO `".$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name']."`
								(`type`, `id`, `date_mod`,`last_update`, `utilise_par`, `ssid`,`id_temp`,`version_client`)
								VALUES (".$this->type.",".$this->id.",NOW(),NOW(),'".$this->user."','".$this->ssid."',@free_id,'".getenv("HTTP_USER_AGENT")."');";
		}
		//==================================================================
				
		//==================================================================
		// Reserve SSID number from URL to write line in the lock table
		// WARNING : We have to lock table before to have a reliable SSID number 
		//==================================================================
		$sql = 'LOCK TABLE  `'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'` WRITE, `'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'`  AS t1 WRITE,`'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'`  AS t2 WRITE;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SET @free_id = 0;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SET @rowmin = 0;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SET @rownum = 0;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SELECT MIN(ID_TEMP) -1 INTO @rowmin
				FROM `'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'` AS t1 
				WHERE id_temp >= 99999 LIMIT 1;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SELECT idtemp INTO @rownum
				FROM (
					SELECT *
					FROM (
						SELECT id_temp +1 AS idtemp 
						FROM `'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'` as t1
						WHERE 1 =1
						AND 
						(
							id_temp +1
						) 
						NOT IN 
						(
							SELECT id_temp
							FROM `'.$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name'].'` as t2
						)
						AND id_temp >=99999
						ORDER BY idtemp
						LIMIT 1
					)SEL1
					UNION
					SELECT 99999
					LIMIT 1
				) 
				AS SEL2
				ORDER BY 1 DESC;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		$sql = 'SELECT IF ( @rowmin >= 99999,@rowmin, @rownum) INTO @free_id;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		// Ne pas mettre de die !!! (ne pas utiliser exec_sql !!!)
		$result_insert = mysql_query($insert_id,$this->link);
		
		
		$sql = 'SELECT @free_id as free_id;';
		$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

		$this->id_temp = mysql_result($resultat, 0,'free_id');
		$_SESSION[$this->ssid]['id_temp'] = $this->id_temp;
		
		$sql = 'UNLOCK TABLES;';
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);					
		//==================================================================
		
		//==================================================================
		// Clean temporary ID
		//==================================================================
		// Delete fields of the table ifiche_parametres
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->ssid]['struct']['tb_fiches_param']['name']."` WHERE id_fiche = ".$this->id_temp;
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		
		// Delete fields of the table tags
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->ssid]['struct']['tb_tags']['name']."` WHERE ID = ".$this->id_temp;
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);

		// Delete fields of the table url_temp
		$sql = "DELETE FROM `".$_SESSION['iknow'][$this->ssid]['struct']['tb_url_temp']['name']."` WHERE id_temp  = ".$this->id_temp;
		$this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
		//==================================================================
				
		return $this->id_temp;
	}
/*===================================================================*/
	
	
	/**==================================================================
	 * Setup internal id from external call
	 * public
	====================================================================*/		
	function set_id($p_id)
	{
		$this->id = $p_id;
	}
	/*===================================================================*/
	
	/**==================================================================
	 * Check if iObject number $this->id is not already updated
	 * Return 	true if iObject available to update
	 * 			else user information who is currently updating this iObect 
	 * public
	====================================================================*/		
	function verification()
	{
		if(is_numeric($this->id))
		{
			if($this->id != 'new')
			{
				// Build query
				$sql = "SELECT 
							`utilise_par`,
							`ssid`
						FROM
							`".$_SESSION['iknow'][$this->ssid]['struct']['tb_lock']['name']."`
						WHERE 1 = 1
							AND `type`=".$this->type."
							AND `id`=".$this->id.";
					   ";
	
				$resultat = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
				
				if(mysql_num_rows($resultat) > 0)
				{
				
					$result=mysql_fetch_row($resultat);
					
					if($result[0] != $_SERVER["REMOTE_ADDR"]) 			// la fiche est en modification par qqun d'autre
					{ 			
						if($this->type == __FICHE_MODIF__)
						{
							// iSheet
							return '<body style="background-color:#A61415;"><div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div><script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(libelle[198]).replace(\'$by\',\''.$result[0].'\'),\'info\',\'msg\');</script>';
						}
						else
						{
							// iCode
							return '<body style="background-color:r#3B3B3B;"><div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div><script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(libelle[199]),\'info\',\'msg\');</script>';
						}
					}
					else if($this->ssid != $result[1]) // IObject is already updated by me in another window ( based on IP address )
					{	
						if($this->type == __FICHE_MODIF__){
							// iSheet
							return '<title>'.$_SESSION[$this->ssid]['message'][500].'</title></head><body style="background-color:#A61415;"><div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div><script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(\''.str_replace("&id",$this->id,str_replace("'","\'",$_SESSION[$this->ssid]['message'][200])).'\'),\'info\',\'msg\');</script>';
						}
						else
						{
							// iCode
							return '<title>'.$_SESSION[$this->ssid]['message'][511].'</title></head><body style="background-color:#3B3B3B;"><div id="iknow_msgbox_background"></div>
							<div id="iknow_msgbox_conteneur" style="display:none;"></div><script type="text/javascript">generer_msgbox(decodeURIComponent(libelle[152]),decodeURIComponent(\''.str_replace("&id",$this->id,str_replace("'","\'",$_SESSION[$this->ssid]['message'][201])).'\'),\'info\',\'msg\');</script>';
						}
					}
					else
					{	
						// iSheet is already updated
						return false;						
					}
				}
				else
				{
					// iSheet is not currently updated
					return false;		
				}
			}
			else
			{
				return false;
			}
		}
	}	
	/*===================================================================*/
	
		
	/**==================================================================
	 * Custom protection ( TODO : Check if standard function exists or place it in globla function
	 * @param 	:	texte to protect
	 * private
	====================================================================*/		
	private function protect_display($texte)
	{
		$texte = str_replace('\\','\\\\',$texte);
		$texte = str_replace('"','\\"',$texte);
		$texte = str_replace("'","\'",$texte);

		return $texte;	
	}
	/*===================================================================*/

	
}
/*===================================================================*/	
?>