<?php
/**==================================================================
 * Database methodes connexion and extend features
 ====================================================================*/		
class class_bdd
{
	//==================================================================
	// Public class attributs
	//==================================================================
	public $link;
	public $link_password;
	//==================================================================
	
	//==================================================================
	// Private class attributs
	//==================================================================
	private $c_ssid;
	private $c_id;
	private $c_id_temp;
	private $c_version;
	private $c_objet;
	//==================================================================
	
	
	/**==================================================================
	 * Constructor of class_bdd
	 * @param p_ssid :
	 * @param p_id :
	 * @param p_id_temp :
	 * @param p_version : iObject version
	 * @param p_objet : Kind of iObject
	====================================================================*/		
	public function __construct($p_ssid = 0,$p_id = 0,$p_id_temp = 0,$p_version = 0,$p_objet = 0)
	{
		$this->c_ssid = $p_ssid;
		$this->c_id = $p_id;
		$this->c_id_temp = $p_id_temp;
		$this->c_version = $p_version;
		$this->c_objet = $p_objet;
	}
	/*===================================================================*/	
	

	/**==================================================================
	 * Create an active connexion to database
	 * @protected
	====================================================================*/		
	protected function db_connexion()
	{
		//==================================================================
		// Main connexion to iKnow schema
		//==================================================================
		$this->link = mysql_connect($_SESSION['iknow'][$this->c_ssid]['serveur_bdd'],$_SESSION['iknow'][$this->c_ssid]['user_iknow'],$_SESSION['iknow'][$this->c_ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$this->c_ssid]['schema_iknow'],$this->link) or die('dbconn: mysql_select_db: ' + mysql_error());
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		//==================================================================
				
		//==================================================================
		// Connexion to secure iKnow schema
		//==================================================================
		$this->link_password = mysql_connect($_SESSION['iknow'][$this->c_ssid]['acces_serveur_bdd'],$_SESSION['iknow'][$this->c_ssid]['acces_user_iknow'],$_SESSION['iknow'][$this->c_ssid]['acces_password_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());;
		mysql_select_db($_SESSION['iknow'][$this->c_ssid]['acces_schema_iknow'],$this->link_password) or die('dbconn: mysql_select_db: ' + mysql_error());
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		//==================================================================
	}
	/*===================================================================*/	
		
	
	/**==================================================================
	 * Add extra features to MySQL connexion
	 * Here you can trace each query og and execution time if needed
	 * @param sql : SQL query executed
	 * @param line : line number in the .php file where sql is requested
	 * @param file : File name used the query
	 * @param fonction : Fonction name called the query
	 * @param class : Class name used this query
	 * @param link : database active connexion
	====================================================================*/		
	public function exec_sql($sql,$line,$file,$function,$class,$link)
	{
		//$start_hour =  microtime(true); // Record start time
		$result = mysql_query($sql,$link) or $this->die_sql($sql,$line,$file,$function,$class,$link);
		
		/*
		//==================================================================
		// write down each query requested in $_SESSION['iknow'][$ssid]['struct']['tb_zztrace_sql']['name'] table
		// Only usefull for tunnig performance
		// !! Caution : Heavy load to proceed !!
		// !! Don't set configuration parameters 40 = true on production system !!
		//==================================================================
		$end_hour =  microtime(true); // Record long time on each query
		if(isset($_SESSION[$this->c_ssid]['configuration'][40]) && $_SESSION[$this->c_ssid]['configuration'][40] == 'true')
		{
			$this->debug_log_sql($sql,$line,$file,$function,$class,round($end_hour - $start_hour,6)*1000);
		}
		//==================================================================
		*/
		return $result;
	}
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Extend MySQL error execution
	 * @private
	 * trace SQL error in database and display error message for user
	 * @param sql : SQL query executed and raised error
	 * @param line : line number in the .php file to find SQL definition
	 * @param file : File name used the query
	 * @param fonction : Fonction name called the query
	 * @param class : Class name used this query
	 * @param link : database active connexion
	====================================================================*/		
	private function die_sql($sql,$line,$file,$function,$class,$link)
	{
		$err_id = $this->insert_error($sql,$link,$line,$file,$function,$class);
		echo $this->generate_MySQL_error_message($sql,$line,$file,$err_id);
		die();
	}
	/*===================================================================*/	

	
	/**==================================================================
	 * Generate cutom message then sql raised an error
	 * @protected
	 * @param sql : query was write in php error_log file
	 * @param ligne : Line where query was called
	 * @return HTML text to display error on screen
	====================================================================*/		
	protected function generate_MySQL_error_message($sql,$line,$file = __FILE__,$err_id)
	{
		// Write query which cause sql error on php error log file
		error_log($file.' : L '.$line.'\n'.$sql);
		
		//==================================================================
		// Build error message to display on the GUI
		//==================================================================
		$message = '</script><div class="err_sql_background"><div class="err_sql_container">';
		$message .= '<div class="err_sql_title"><b style="color:#DD2233;">'.$_SESSION[$this->c_ssid]['message']['iknow'][20].'</div>';
		$message .= '<div>'.$_SESSION[$this->c_ssid]['message']['iknow'][21].$err_id.'</div></div></div>';
		//==================================================================
		
		return $message;
	}	
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Record explain plan of each query in ikn_log_sql
	 * Performances protection : Works only if configuration parameters 40 is set on true
	 * @private
	 * @param sql : Query to execute with explain plan
	 * @param line : Line where query was called
	 * @param file : Line where query was called
	 * @param fonction : Fonction name called the query
	 * @param class : Class name used this query
	 * @param tps : Time of execution
	====================================================================*/		
	private function debug_log_sql($sql,$line,$file,$fonction,$class,$time)
	{
		if(is_null($this->link))
		{
			$this->db_connexion();
		}
		
		$sql_explain = 'EXPLAIN '.$sql;
		$resultat = mysql_query($sql_explain,$this->link);
		
		if($resultat != false)
		{
			$rows_explain = mysql_result($resultat,0,'rows');
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['rows'] > $rows_explain)
				{
					$rows_explain = $row['rows'];
				}
			}
		}
		else 
		{
			$rows_explain = -1;
		}
		
		//==================================================================
		// Build query to insert query features in database
		//==================================================================
		$query = 'INSERT
					INTO 
						`'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_zztrace_sql']['name'].'`
							  (	`ID_OBJET`,
							  	`VERSION_OBJET`,
							  	`OBJET`,
							  	`REQUETE`,
							  	`ssid`,
							  	`ROWS_EXPLAIN`,
							  	`FILE`,
							  	`LINE`,
							  	`FONCTION`,
							  	`CLASS`,
							  	`EXEC_TIME`
							  )
						VALUES(	"'.addslashes($this->c_id).'",
								"'.addslashes($this->c_version).'",
								"'.addslashes($this->c_objet).'",
								"'.addslashes($sql).'",
								"'.addslashes($this->c_ssid).'",
								'.$rows_explain.',
								"'.addslashes($file).'",
								"'.addslashes($line).'",
								"'.addslashes($fonction).'",
								"'.addslashes($class).'",
								'.$time.'
							  )
					';
		//==================================================================
		
		mysql_query($query,$this->link);
	}
	/*===================================================================*/	
	
	
	private function insert_error($sql,$link,$line,$file,$fonction,$class)
	{
		$sql = 'INSERT INTO `'.$_SESSION['iknow'][$this->c_ssid]['struct']['tb_zztrace_err_sql']['name'].'`(`OBJET_TYPE`,`LIB_ERR`,`NUM_ERR`,`sql`,`FILE`,`LINE`,`FONCTION`,`CLASS`,`V_APPLICATIF`) VALUES("'.$this->c_objet.'","'.addslashes(mysql_error($link)).'","'.addslashes(mysql_errno($link)).'","'.addslashes($sql).'","'.addslashes($file).'","'.addslashes($line).'","'.addslashes($fonction).'","'.addslashes($class).'","'.addslashes($_SESSION['iknow']['version_soft']).'")';
		mysql_query($sql,$link) or die('Erreur dans la methode insert_error '.mysql_error().'<hr />'.$sql);
		
		return mysql_insert_id($link);
	}
	
	protected function set_id_temp($p_id_temp)
	{
		$this->c_id_temp = $p_id_temp;
	}
}
/*===================================================================*/	
?>