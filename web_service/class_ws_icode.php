<?php
/**==================================================================
 * iKnow iCode WebService
 *
 * @p_id    	:   iCode identifier
 * @p_version	:	iCode version ( default null means use last version )
 * @p_param		:	parameters to set value in iCode
====================================================================*/
	class ws_icode
	{
		private function icode_new_object($p_id,$p_version = null,$p_param = null)
		{
			//==================================================================
			// Load iCode framework
			//==================================================================
			require('../includes/common/ssid.php');	
			require('../includes/common/define_db_names.php');
			require("../class/common/class_bdd.php");
			require("../class/common/class_lock.php");
			require("../class/icode/class_code.php");
			
			//==================================================================
			// Initialize connexion to databse
			//==================================================================
			$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']);
			mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
			mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());	

			//==================================================================
			// Load main configuration
			//==================================================================
			$str = 'SELECT `id` AS "id_conf",
							`value` AS "valeur"  
					FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].'
					WHERE `version_active` = '.$this->iknow_version();
		
			$requete = mysql_query($str);
					
			while($row = mysql_fetch_array($requete,MYSQL_ASSOC))
			{
				$_SESSION[$ssid]['configuration'][$row['id_conf']] = $row['valeur'];
			}

			$_GET = $p_param;

			$obj = new icode($p_id,$ssid,$_SERVER['REMOTE_ADDR'],4,$this->iknow_version(),$p_version,'FR');																		

			return $obj;
		}
		
		/**
		 * Return iKnow version
		 */
		public function iknow_version()
		{
			error_log('ICI');
			require '../includes/common/version_active.php';
			
			return $version_soft;
		}
		
		
		/**
		 * Get iCode content
		 * @param $p_id ID of the icode
		 * @param $p_version version of the icode
		 * @param $p_param param of the icode
		 */
		public function get_icode_content($p_id,$p_version = null,$p_param = null)
		{
			$obj = $this->icode_new_object($p_id,$p_version,$p_param);
			
			return $obj->generer_icode_light();
		}
		
		/**
		 * Get iCode title
		 * @param $p_id ID of the icode
		 * @param $p_version version of the icode
		 */
		public function get_icode_title($p_id,$p_version = null)
		{
			$obj = $this->icode_new_object($p_id,$p_version);
			
			return $obj->get_titre_sans_bbcode();
		}
		
		/**
		 * Get iCode description
		 * @param $p_id ID of the icode
		 * @param $p_version version of the icode
		 */
		public function get_icode_description($p_id,$p_version = null)
		{
			$obj = $this->icode_new_object($p_id,$p_version);
			
			return $obj->get_description();
		}
				
	}
/**===================================================================*/
?>