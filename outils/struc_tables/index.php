<?php 
	$start_hour = microtime(true);
	define("__CHARSET__","utf8");	
	define("__COLLATION__","utf8_unicode_ci");	
	$nbr_erreur_total = 0;			
	require '../../includes/common/version_active.php';	
	require '../../includes/common/ssid.php';
	$ssid = $p_ssid;
	require '../../includes/common/define_db_names.php';

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../../includes/common/html_doctype.php');
	/*===================================================================*/	
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>Contrôle de la structure de la base iKnow version <?php echo $version_soft; ?></title>
		<link rel="stylesheet" href="style.css" type="text/css">
		
		<script type="text/javascript">

		function toggle(id)
		{
			if(document.getElementById(id).style.display == 'none' || document.getElementById(id).style.display == "")
			{
				document.getElementById(id).style.display = 'table-row';
			}
			else
			{
				document.getElementById(id).style.display = 'none';
			}
		}
		</script>
	</head>
	<body>
	<div class="header">
		<div class="logo"></div>
	</div>
	<div id="container">
		<?php
			/**==================================================================
			 * CONNEXION A LA BDD
			 ====================================================================*/
			$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']) or die('<span style="color:red;font-weight:bold;">Informations de connexion au serveur incorrecte (serveur, identifiant ou mot de passe)</span>');	
			mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
			mysql_select_db('information_schema',$link);
			
			$link_schema = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'],$_SESSION['iknow'][$ssid]['user_iknow'],$_SESSION['iknow'][$ssid]['password_iknow']) or die('<span style="color:red;font-weight:bold;">Informations de connexion au serveur incorrecte (serveur, identifiant ou mot de passe)</span>');
			mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
			mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow'],$link_schema) or die('Vous n\'avez pas specifiez le bon schéma pour iKnow');
			/*===================================================================*/
				
			/**==================================================================
			 * Récupération des tables définies dans define_db_names.php
			 ====================================================================*/	
			foreach($_SESSION['iknow'][$ssid]['struct'] as $value) 
			{
				if(isset($value['champs']))
				{
					foreach($value['champs'] as $champs) 
					{
						$tables_init[$value['name']]= $value['name'];
					}
				}
			}
			/*===================================================================*/
			
			/**==================================================================
			 * Récupération des champs définis dans define_db_names.php
			 ====================================================================*/		
			foreach($_SESSION['iknow'][$ssid]['struct'] as $value) 
			{
				if(isset($value['champs']))
				{
					foreach($value['champs'] as $champs) 
					{
						$champs_init[$value['name']][$champs] = $champs;
					}
				}
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Récupération des triggers définis dans define_db_names.php
			 ====================================================================*/		
			foreach($_SESSION['iknow'][$ssid]['struct']['TRIGGER'] as $key => $value) 
			{
				$triggers_init[$value] = $value;
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Récupération des procédures définies dans define_db_names.php
			 ====================================================================*/	
			foreach($_SESSION['iknow'][$ssid]['struct']['PROCEDURE'] as $key => $value) 
			{
				$procedures_init[$value]= $value;
			}
			/*===================================================================*/	
			
			/**==================================================================
			 * Récupération des fonctions définies dans define_db_names.php
			 ====================================================================*/		
			foreach($_SESSION['iknow'][$ssid]['struct']['FUNCTION'] as $key => $value) 
			{
				$fonctions_init[$value]= $value;
			}
			/*===================================================================*/
				
			/**==================================================================
			 * Analyse de la définition des tables
			 ====================================================================*/
			$sql = 'SELECT TABLE_NAME
					FROM information_schema.COLUMNS 
					WHERE TABLE_SCHEMA = "'.$_SESSION['iknow'][$ssid]['schema_iknow'].'"
					GROUP BY TABLE_NAME';
			
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$tables_base[$row['TABLE_NAME']] = $row['TABLE_NAME'];
			}
			
			$html_err_tables = '<div class="bloc_detail">';
			$html_err_tables .= '<table>';
			$nbr_err_tables=0;
			foreach($tables_base as $value)
			{
				if(!isset($tables_init[$value]))
				{
					$html_err_tables .= '<tr><td>La table <span class="table_err">'.$value.'</span> est définie dans la base mais pas dans define_db_names.php</td></tr>';
					$nbr_err_tables++;
				}
			}
				
			foreach($tables_init as $value)
			{
				if(!isset($tables_base[$value]))
				{
					$html_err_tables .= '<tr><td>La table <span class="table_err">'.$value.'</span> est définie dans define_db_names.php mais pas dans la base</td></tr>';
					$nbr_err_tables++;
				}
			}
			
			$html_err_tables .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_tables;
			/*===================================================================*/
			
			/**==================================================================
			 * Analyse de la définition des champs
			 ====================================================================*/
			$sql = 'SELECT TABLE_NAME,COLUMN_NAME
					FROM information_schema.COLUMNS 
					WHERE TABLE_SCHEMA = "'.$_SESSION['iknow'][$ssid]['schema_iknow'].'"
					ORDER BY TABLE_NAME';
			
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$champs_base[$row['TABLE_NAME']][$row['COLUMN_NAME']] = $row['COLUMN_NAME'];
			}
			
			
			$html_err_champs = '<div class="bloc_detail">';
			$html_err_champs .= '<table>';
			$nbr_err_champs=0;
			foreach($champs_base as $key => $value)
			{
				foreach($value as $value_value)
				{
					if(!isset($champs_init[$key][$value_value]))
					{
						$html_err_champs .= '<tr><td>Le champ <span class="table_err">'.$value_value.'</span> de la table <span class="table_err">'.$key.'</span> est défini dans la base mais pas dans define_db_names.php</td></tr>';
						$nbr_err_champs++;
					}
				}
			}
			
			foreach($champs_init as $key => $value)
			{
				foreach($value as $value_value)
				{
					if(!isset($champs_base[$key][$value_value]))
					{
						$html_err_champs .= '<tr><td>Le champ <span class="table_err">'.$value_value.'</span> de la table <span class="table_err">'.$key.'</span> est défini dans define_db_names.php mais pas dans la base</td></tr>';
						$nbr_err_champs++;
					}
				}
			}
			
			$html_err_champs .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_champs;
			/*===================================================================*/
			
			/**==================================================================
			 * Analyse de la définition des triggers
			 ====================================================================*/
			$sql = 'SHOW TRIGGERS;';
			
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$triggers_base[$row['Trigger']] = $row['Trigger'];
			}
		
			$html_err_triggers = '<div class="bloc_detail">';
			$html_err_triggers .= '<table>';
			$nbr_err_trigger=0;
			foreach($triggers_base as $value)
			{
				if(!isset($triggers_init[$value]))
				{
					$html_err_triggers .= '<tr><td>Le trigger <span class="table_err">'.$value.'</span> est défini dans la base mais pas dans define_db_names.php</td></tr>';
					$nbr_err_trigger++;
				}
			}
			
				
			foreach($triggers_init as $value)
			{
				if(!isset($triggers_base[$value]))
				{
					$html_err_triggers .= '<tr><td>Le trigger <span class="table_err">'.$value.'</span> est défini dans define_db_names.php mais pas dans la base</td></tr>';
					$nbr_err_trigger++;
				}
			}
			
			$html_err_triggers .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_trigger;
			/*===================================================================*/
			
			/**==================================================================
			 * Analyse de la définition des procedures
			 ====================================================================*/
			$sql = 'SHOW PROCEDURE STATUS;';
			
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$procedures_base[$row['Name']] = $row['Name'];
			}
		
			$html_err_procedures = '<div class="bloc_detail">';
			$html_err_procedures .= '<table>';
			$nbr_err_procedures=0;
			foreach($procedures_base as $value)
			{
				if(!isset($procedures_init[$value]))
				{
					$html_err_procedures .= '<tr><td>La procédure <span class="table_err">'.$value.'</span> est définie dans la base mais pas dans define_db_names.php</td></tr>';
					$nbr_err_procedures++;
				}
			}
			
				
			foreach($procedures_init as $value)
			{
				if(!isset($procedures_base[$value]))
				{
					$html_err_procedures .= '<tr><td>La procédure <span class="table_err">'.$value.'</span> est définie dans define_db_names.php mais pas dans la base</td></tr>';
					$nbr_err_procedures++;
				}
			}
			
			$html_err_procedures .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_procedures;
			/*===================================================================*/
			
			/**==================================================================
			 * Analyse de la définition des fonctions
			 ====================================================================*/
			$sql = 'SHOW FUNCTION STATUS;';
			
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				$fonctions_base[$row['Name']] = $row['Name'];
			}
			$html_err_fonctions = '<div class="bloc_detail">';
			$html_err_fonctions .= '<table>';
			$nbr_err_fonctions=0;
		
			foreach($fonctions_base as $value)
			{
				if(!isset($fonctions_init[$value]))
				{
					$html_err_fonctions .= '<tr><td>La fonction <span class="table_err">'.$value.'</span> est définie dans la base mais pas dans define_db_names.php</td></tr>';
					$nbr_err_fonctions++;
				}
			}
				
			foreach($fonctions_init as $value)
			{
				if(!isset($fonctions_base[$value]))
				{
					$html_err_fonctions .= '<tr><td>La fonction <span class="table_err">'.$value.'</span> est définie dans define_db_names.php mais pas dans la base</td></tr>';
					$nbr_err_fonctions++;
				}
			}
			
			$html_err_fonctions .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_fonctions;
			/*===================================================================*/
	
			
			/**==================================================================
			 * Analyse de l'encodage de MySQL
			 ====================================================================*/
			$nbr_err_encodage=0;
			$html_err_encodage_mysql = '<div class="bloc_detail">';
			$html_err_encodage_mysql .= '<table>';
			
			// character_set_client
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_client%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_client</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}	
			
			// character_set_connection
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_connection%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_connection</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}
			
			// character_set_database
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_database%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_database</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}		
			
			// character_set_results
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_results%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_results</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}		
			
			// character_set_server
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_server%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_server</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}			
			
			// character_set_system
			$sql = 'SHOW VARIABLES LIKE "%character\_set\_system%"';
			$resultat = mysql_query($sql,$link) or die(mysql_error());
			if(mysql_result($resultat,0,'Value') != __CHARSET__)
			{
				$html_err_encodage_mysql .= '<tr><td>L\'encodage de la variable MySQL <span class="table_err">character_set_system</span> est définie à <span class="table_err">'.mysql_result($resultat,0,'Value').'</span>. Veuillez la définir en utf8</td></tr>';
				$nbr_err_encodage++;
			}			
			
			$html_err_encodage_mysql .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_encodage;
			/*===================================================================*/
	
			
			/**==================================================================
			 * Analyse de l'encodage des tables
			 ====================================================================*/
			$nbr_err_encodage_tables=0;
			$html_err_encodage_tables = '<div class="bloc_detail">';
			$html_err_encodage_tables .= '<table>';
			
			$sql = 'SELECT `TABLE_NAME`,`TABLE_COLLATION`  FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` LIKE "iknow"';
			$resultat = mysql_query($sql,$link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['TABLE_COLLATION'] != __COLLATION__)
				{
					$html_err_encodage_tables .= 'La table <span class="table_err">'.$row['TABLE_NAME'].'</span> a un encodage <span class="table_err">'.$row['TABLE_COLLATION'].'</span>. Veuillez le changer en '.__COLLATION__.'</span><br />';
					$nbr_err_encodage_tables++;
				}
			}
			$html_err_encodage_tables .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_encodage_tables;
			/*===================================================================*/
			
		
			/**==================================================================
			 * Analyse de l'encodage des tables
			 ====================================================================*/
			$nbr_err_encodage_champs=0;
			$html_err_encodage_champs = '<div class="bloc_detail">';
			$html_err_encodage_champs .= '<table>';
			
			$sql = 'SELECT `TABLE_NAME`,`COLUMN_NAME`,COLLATION_NAME  FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` LIKE "iknow" AND COLLATION_NAME IS NOT NULL';
			$resultat = mysql_query($sql,$link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['COLLATION_NAME'] != __COLLATION__)
				{
					$html_err_encodage_champs .= 'Le champ <span class="table_err">'.$row['COLUMN_NAME'].'</span> de la table <span class="table_err">'.$row['TABLE_NAME'].'</span> a un encodage <span class="table_err">'.$row['COLLATION_NAME'].'</span>. Veuillez le changer en '.__COLLATION__.'</span><br />';
					$nbr_err_encodage_champs++;
				}
			}
			$html_err_encodage_champs .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_encodage_champs;
			/*===================================================================*/	
			
			
			/**==================================================================
			 * Analyse des droits d'execution des procédures stockées
			 ====================================================================*/
			$nbr_err_droit_procedure=0;
			$html_err_droit_procedure = '<div class="bloc_detail">';
			$html_err_droit_procedure .= '<table>';
			
			$sql = 'SHOW PROCEDURE STATUS;';
			$resultat = mysql_query($sql,$link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['Definer'] != $_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'])
				{
					$html_err_droit_procedure .= 'La procédure <span class="table_err">'.$row['Name'].'</span> a les droits d\'execution sur <span class="table_err">'.$row['Definer'].'</span>. Veuillez les définir sur <span class="table_err">'.$_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'].'</span><br />';
					$nbr_err_droit_procedure++;
				}
			}
			$html_err_droit_procedure .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_droit_procedure;
			/*===================================================================*/		
			
			
			/**==================================================================
			 * Analyse des droits d'execution des fonctions
			 ====================================================================*/
			$nbr_err_droit_fonctions=0;
			$html_err_droit_fonctions = '<div class="bloc_detail">';
			$html_err_droit_fonctions .= '<table>';
			
			$sql = 'SHOW FUNCTION STATUS;';
			$resultat = mysql_query($sql,$link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['Definer'] != $_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'])
				{
					$html_err_droit_fonctions .= 'La fonction <span class="table_err">'.$row['Name'].'</span> a les droits d\'execution sur <span class="table_err">'.$row['Definer'].'</span>. Veuillez les définir sur <span class="table_err">'.$_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'].'</span><br />';
					$nbr_err_droit_fonctions++;
				}
			}
			$html_err_droit_fonctions .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_droit_fonctions;
			/*===================================================================*/			
			
			/**==================================================================
			 * Analyse des droits d'execution des triggers
			 ====================================================================*/
			$nbr_err_droit_triggers=0;
			$html_err_droit_triggers = '<div class="bloc_detail">';
			$html_err_droit_triggers .= '<table>';
			
			$sql = 'SHOW TRIGGERS;';
			$resultat = mysql_query($sql,$link);
			
			while($row = mysql_fetch_array($resultat,MYSQL_ASSOC))
			{
				if($row['Definer'] != $_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'])
				{
					$html_err_droit_triggers .= 'Le trigger <span class="table_err">'.$row['Trigger'].'</span> a les droits d\'execution sur <span class="table_err">'.$row['Definer'].'</span>. Veuillez les définir sur <span class="table_err">'.$_SESSION['iknow'][$ssid]['user_iknow'].'@'.$_SESSION['iknow'][$ssid]['serveur_bdd'].'</span><br />';
					$nbr_err_droit_triggers++;
				}
			}
			$html_err_droit_triggers .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_droit_triggers;
			/*===================================================================*/				
	
			/**==================================================================
			 * Vérification de la présence des paramètres de configuration pour la version
			 ====================================================================*/
			$nbr_err_param_conf_version=0;
			$html_err_param_conf_version = '<div class="bloc_detail">';
			$html_err_param_conf_version .= '<table>';
			
			$sql = 'SELECT COUNT(1) FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].'` WHERE '.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['champs']['version_active'].' = "'.$version_soft.'"' ;
			$resultat = mysql_query($sql,$link);
			
	
			if(mysql_result($resultat,0,0) == 0)
			{
				$html_err_param_conf_version .= 'Il n\'y a aucun paramètres de configuration pour la version <span class="table_err">'.$version_soft.'</span>. Vérifier la table <span class="table_err">'.$_SESSION['iknow'][$ssid]['struct']['tb_configuration']['name'].'</span><br />';
				$nbr_err_param_conf_version++;
			}
			
			$html_err_param_conf_version .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_param_conf_version;
			/*===================================================================*/	
		
			/**==================================================================
			 * Vérification de la présence des textes pour la version
			 ====================================================================*/
			$nbr_err_textes_version=0;
			$html_err_textes_version = '<div class="bloc_detail">';
			$html_err_textes_version .= '<table>';
			
			$sql = 'SELECT COUNT(1) FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].'` WHERE '.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['champs']['version_active'].' = "'.$version_soft.'"' ;
			$resultat = mysql_query($sql,$link);
			
	
			if(mysql_result($resultat,0,0) == 0)
			{
				$html_err_textes_version .= 'Il n\'y a aucun texte pour la version <span class="table_err">'.$version_soft.'</span>. Vérifier la table <span class="table_err">'.$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name'].'</span><br />';
				$nbr_err_textes_version++;
			}
			
			$html_err_textes_version .= '</table></div></div>';
			$nbr_erreur_total += $nbr_err_textes_version;
			/*===================================================================*/	
			
			/**==================================================================
			 * RESUME ERREUR
			 ====================================================================*/
			echo '<h3>Contrôle de la base de données <span class="i">i</span>Know '.$version_soft.'</h3>';
			
			?>
			<table>
				<tr>
					<td class="ligne">Définition des tables</td>
					<?php 
						if($nbr_err_tables > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_tables\');">'.$nbr_err_tables.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_tables > 0)
					{
						echo '<tr class="hide" id="detail_err_tables"><td colspan=3>'.$html_err_tables.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Définition des champs</td>
					<?php 
						if($nbr_err_champs > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_champs\');">'.$nbr_err_champs.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_champs > 0)
					{
						echo '<tr class="hide" id="detail_err_champs"><td colspan=3>'.$html_err_champs.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Définition des triggers</td>
					<?php 
						if($nbr_err_trigger > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_triggers\');">'.$nbr_err_trigger.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_trigger > 0)
					{
						echo '<tr class="hide" id="detail_err_triggers"><td colspan=3>'.$html_err_triggers.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Droits d'execution des triggers</td>
					<?php 
						if($nbr_err_droit_triggers > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_droit_triggers\');">'.$nbr_err_droit_triggers.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_droit_triggers > 0)
					{
						echo '<tr class="hide" id="detail_err_droit_triggers"><td colspan=3>'.$html_err_droit_triggers.'</td></tr>';
					}
				?>	
				<tr>
					<td class="ligne">Définition des procédures stockées</td>
					<?php 
						if($nbr_err_procedures > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_procedures\');">'.$nbr_err_procedures.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<tr>
					<td class="ligne">Droits d'execution des procédures stockées</td>
					<?php 
						if($nbr_err_droit_procedure > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_droit_procedure\');">'.$nbr_err_droit_procedure.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_droit_procedure > 0)
					{
						echo '<tr class="hide" id="detail_err_droit_procedure"><td colspan=3>'.$html_err_droit_procedure.'</td></tr>';
					}
				?>			
				<?php 
					if($nbr_err_procedures > 0)
					{
						echo '<tr class="hide" id="detail_err_procedures"><td colspan=3>'.$html_err_procedures.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Définition des fonctions</td>
					<?php 
						if($nbr_err_fonctions > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_fonctions\');">'.$nbr_err_fonctions.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_fonctions > 0)
					{
						echo '<tr class="hide" id="detail_err_fonctions"><td colspan=3>'.$html_err_fonctions.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Droits d'execution des fonctions</td>
					<?php 
						if($nbr_err_droit_fonctions > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_droit_fonctions\');">'.$nbr_err_droit_fonctions.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_droit_fonctions > 0)
					{
						echo '<tr class="hide" id="detail_err_droit_fonctions"><td colspan=3>'.$html_err_droit_fonctions.'</td></tr>';
					}
				?>
				<tr>
					<td class="ligne">Encodage de MySQL</td>
					<?php 
						if($nbr_err_encodage > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_encodage\');">'.$nbr_err_encodage.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_encodage > 0)
					{
						echo '<tr class="hide" id="detail_err_encodage"><td colspan=3>'.$html_err_encodage_mysql.'</td></tr>';
					}
				?>	
				<tr>
					<td class="ligne">Encodage des tables</td>
					<?php 
						if($nbr_err_encodage_tables > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_encodage_tables\');">'.$nbr_err_encodage_tables.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_encodage_tables > 0)
					{
						echo '<tr class="hide" id="detail_err_encodage_tables"><td colspan=3>'.$html_err_encodage_tables.'</td></tr>';
					}
				?>	
				<tr>
					<td class="ligne">Encodage des champs</td>
					<?php 
						if($nbr_err_encodage_champs > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_encodage_champs\');">'.$nbr_err_encodage_champs.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_encodage_champs > 0)
					{
						echo '<tr class="hide" id="detail_err_encodage_champs"><td colspan=3>'.$html_err_encodage_champs.'</td></tr>';
					}
				?>	
				<tr>
					<td class="ligne">Présence des paramètres de configuration</td>
					<?php 
						if($nbr_err_param_conf_version > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_param_conf_version\');">'.$nbr_err_param_conf_version.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_param_conf_version > 0)
					{
						echo '<tr class="hide" id="detail_err_param_conf_version"><td colspan=3>'.$html_err_param_conf_version.'</td></tr>';
					}
				?>						
				<tr>
					<td class="ligne">Présence des textes</td>
					<?php 
						if($nbr_err_textes_version > 0)
						{
							echo '<td class="err ligne click" onclick="toggle(\'detail_err_textes_version\');">'.$nbr_err_textes_version.' erreur(s)</td>';
							echo '<td class="ligne"><div class="ko"></div></td>';
						}
						else
						{
							echo '<td class="err ligne">-</td>';
							echo '<td class="ligne"><div class="ok"></div></td>';
						}
					?>
				</tr>
				<?php 
					if($nbr_err_textes_version > 0)
					{
						echo '<tr class="hide" id="detail_err_textes_version"><td colspan=3>'.$html_err_textes_version.'</td></tr>';
					}
				?>					
				
				
				<?php 
	
						if($nbr_erreur_total == 0)
						{
							echo '<tr class="ok">';
							echo '<td class="resume">Etat</td>';
							echo '<td class="err resume">-</td>';
							echo '<td class="resume"><div class="ok"></div></td>';
						}
						else
						{
							echo '<tr class="error">';
							echo '<td class="resume">Etat</td>';
							echo '<td class="err resume">'.$nbr_erreur_total.' Erreur(s)</td>';
							echo '<td class="resume"><div class="ko"></div></td>';
						}
					?>
				</tr>
			</table>
			<?php 
				$end_hour = microtime(true);
				
			?>
		</div>
		<div class="footer"><?php echo 'Durée du contrôle : '.round($end_hour - $start_hour,3).' s'; ?></div>
	</body>
</html>