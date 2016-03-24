<?php
	
	class class_node extends class_bdd
	{
		private $c_id_iobject;
		private $c_version_ioject;
		private $c_ssid;
		
		public function __construct($p_ssid,$p_id_iobject,$p_version_ioject)
		{
			parent::__construct($p_ssid);
			$this->c_ssid = $p_ssid;
			$this->db_connexion();
			$this->c_id_iobject = $p_id_iobject;
			$this->c_version_ioject = $p_version_ioject;
			$this->c_get_child($this->c_id_iobject,$this->c_version_ioject);
		}
		
		/**
		 * Call when object is deserialized
		 */
		public function __wakeup()
		{
			// Databases reconnexion
			$this->db_connexion();	
		}
	
		public function c_get_child($p_id_iobject,$p_version_ioject)
		{
			$motif = '#<a.+href="ifiche.php\?(&amp;)??ID=([0-9]+?)("|">|&amp;)#';
			
			$sql = 'SELECT `description`,`id_etape`
					FROM `ikn_ifiche_etapes`
					WHERE  1 = 1 
					AND `id_fiche` = '.$p_id_iobject.'
					AND `num_version` = '.$p_version_ioject;
			
			$result = $this->exec_sql($sql,__LINE__,__FILE__,__FUNCTION__,__CLASS__,$this->link);
			
			while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				preg_match_all($motif,$row['description'],$out);
				
				if(isset($out[2][0]))
				{
					$this->c_get_child($out[2][0],0);
					echo '<pre>';
					print_r($out[2]);
					echo '</pre><hr />';
				}
			}
		}
	}

?>