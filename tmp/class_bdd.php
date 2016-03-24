<?php
class class_bdd
{
	protected $link;
	private $c_ssid;
	
	protected function __construct($p_ssid)
	{
		$this->c_ssid = $p_ssid;
	}
	
	/**==================================================================
	* PROTECTED DATABASE CONNEXION
	====================================================================*/		
	protected function db_connexion($objet = 'iknow')
	{
		$this->link = mysql_connect($_SESSION['iknow'][$this->c_ssid]['serveur_bdd'],$_SESSION['iknow'][$this->c_ssid]['user_iknow'],$_SESSION['iknow'][$this->c_ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$this->c_ssid]['schema_iknow'],$this->link) or die('dbconn: mysql_select_db: ' + mysql_error());
	}
	/*===================================================================*/	
	
	protected function exec_sql($sql,$line,$file,$function,$class,$link)
	{
		$start_hour =  microtime(true);
		
		$resultat = mysql_query($sql,$link) or $this->die_sql($sql,$line,$file,$function,$class,$link,round(microtime(true) - $start_hour,6));
		
		return $resultat;
	}
	
	private function die_sql($sql,$line,$file,$function,$class,$link,$time)
	{
		die('Erreur SQL (class_bdd.php)');
	}
}
?>