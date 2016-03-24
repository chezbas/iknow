<?php
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('includes/common/ssid_simple.php');
	/**===================================================================*/
	
	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('includes/common/buffering.php');
	/**===================================================================*/

	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('includes/common/load_conf_session.php');
	/**===================================================================*/

	
	/**==================================================================
	* Load global functions
	====================================================================*/	
	require('includes/common/global_functions.php');
	/**===================================================================*/


	$url_param = url_get_exclusion($_GET);

	/**==================================================================
	* Recover language from URL or Database
	====================================================================*/	
	require('includes/common/language.php');
	/**===================================================================*/

	if(isset($_POST['login']) && isset($_POST['password']))
	{
		// Check identification
		$link_access = mysql_connect($_SESSION['iknow'][$ssid]['acces_serveur_bdd'], $_SESSION['iknow'][$ssid]['acces_user_iknow'], $_SESSION['iknow'][$ssid]['acces_password_iknow']);
		mysql_set_charset('utf8'); // FORCE_UTF8_CHARSET
		mysql_select_db($_SESSION['iknow'][$ssid]['acces_schema_iknow'],$link_access) or die('dbconn: mysql_select_db: ' + mysql_error());
	
		$sql = "SELECT
					`level` LEVEL 
				FROM
					`".$_SESSION['iknow'][$ssid]['struct']['tb_group']['name']."` 
				WHERE 1 = 1
					AND `name` ='".mysql_real_escape_string($_POST['login'])."' 
					AND `level` >= ".$_SESSION['iknow'][$ssid]['level_require']."
					AND `password`= AES_ENCRYPT('".mysql_real_escape_string($_POST['password'])."','FhX*24é\"3_--é0Fz.')
					-- AND `password`='".md5(mysql_real_escape_string($_POST['password']))."
					";

		$resultat = mysql_query($sql,$link_access) or die(mysql_error());

		// Add new user level 4 name = admin, password = demo
		// INSERT `ikn_groups` SET `name` ='admin',`level` = 4, `password`= AES_ENCRYPT('demo','FhX*24é"3_--é0Fz.')
	
		if(mysql_num_rows($resultat)>0)
		{
			// Login ok
			$_SESSION['iknow'][$ssid]['identified_level'] = mysql_result($resultat,0,'level');
			$_SESSION['iknow'][$ssid]['login'] = $_POST['login'];
			(isset($_GET['ID'])) ? $id = '&ID='.$_GET['ID'] : $id = '';
			header('Location: '.$_SESSION['iknow'][$ssid]['redirect_page'].'?'.$url_param);
		}
		else
		{
			// Identification error
			$_SESSION['iknow'][$ssid]['identified_level'] = false;
			$_SESSION['iknow'][$ssid]['error_message'] = $_SESSION[$ssid]['message']['iknow'][19];
		}
	}
	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('includes/common/html_doctype.php');
	/**==================================================================*/
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<link rel="stylesheet" href="css/common/admin.css" type="text/css">
		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_footer.css" type="text/css">
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		<script type="text/javascript">
			var ssid= '<?php echo $_GET["ssid"]; ?>';
			var version_soft = '';

			var libelle_common = Array();
			<?php 
				/**==================================================================
				* Recover text
				====================================================================*/	
				$type_soft = 6; // Type of screen
				require('includes/common/textes.php');
				/**==================================================================*/

			//==================================================================
			// Recover level of ID requested if exists
			//==================================================================
			if(isset($_GET['ID']) && $_SESSION['iknow'][$ssid]['identified_level'] == false)
			{
				$my_id = mysql_real_escape_string($_GET['ID']);

				$sql = "
					SELECT
						`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`level`				AS `level`
					FROM
						`".$_SESSION['iknow'][$ssid]['acces_schema_iknow']."`.`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`
						WHERE 1 = 1
						AND `".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`id` = ".$my_id;
				$resultat = mysql_query($sql) or die(mysql_error());

				if(mysql_num_rows($resultat) == 0)
				{
					$_SESSION['iknow'][$ssid]['identified_level'] = false;
					$_SESSION['iknow'][$ssid]['error_message'] .= '<br>'.str_replace("$1",$my_id,$_SESSION[$ssid]['message'][1]);
				}
				else
				{
					$row = mysql_fetch_array($resultat,MYSQL_ASSOC);
					$_SESSION['iknow'][$ssid]['identified_level'] = false;
					$_SESSION['iknow'][$ssid]['error_message'] .= '<br>'.str_replace("$2",$row['level'],str_replace("$1",$my_id,$_SESSION[$ssid]['message'][2]));
				}
			}
			//==================================================================
			?>
		
			// Variables pour l'uptime de la session
			<?php 
				$gc_lifetime = ini_get('session.gc_maxlifetime'); 
				$end_visu_date  = time() + $gc_lifetime;
				$end_visu_time = $end_visu_date;
				$end_visu_date = date('m/d/Y',$end_visu_date);
				$end_visu_time = date('H:i:s',$end_visu_time);
			?>
			var end_visu_date = '<?php echo $end_visu_date; ?>';
			var end_visu_time = '<?php echo $end_visu_time; ?>';
			function over(p_txt)
			{
				document.getElementById('help').innerHTML = p_txt;
			}
	
			function unset_text_help()
			{
				document.getElementById('help').innerHTML = '';
			}
		</script>
		<title><?php echo $_SESSION[$ssid]['message'][489]; ?></title>
	</head>
	<body>
		<div id="header">
			<div style="position:absolute;top:2px;left:5px;">
				<div class="boutton_url_home boutton_outils" onclick="window.location.replace('../../?ssid='+ssid);" onmouseover="over('Portail iKnow');" onmouseout="unset_text_help();"></div>
			</div>
			<div class="logo"></div>
		</div>	
		<div id="identification_title">
			<?php echo $_SESSION[$ssid]['message'][486]; ?>
		</div>
		<div id="content">
			<div id="authentification">
				<form action="" method="post">
					<table summary="">
						<tr><td class="lib_input"><?php echo $_SESSION[$ssid]['message'][487]; ?></td><td><input type="text" name="login" id="login" class="input_txt gradient"/></td></tr>
						<tr><td class="lib_input"><?php echo $_SESSION[$ssid]['message'][488]; ?></td><td><input type="password" name="password" class="input_txt gradient"/></td></tr>
						<tr><td></td><td><input type="submit" value="<?php echo $_SESSION[$ssid]['message'][490]; ?>" class="submit_btn"/></td></tr>
						<tr>
							<td colspan="2">
								<div id="lib_erreur">
									<?php 
										if(isset($_SESSION['iknow'][$ssid]['error_message']))
										{
											echo $_SESSION['iknow'][$ssid]['error_message'];
											unset($_SESSION['iknow'][$ssid]['error_message']); 
										}
									?>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="details">
			</div>
		</div>
		<div id="footer"></div>
		<script type="text/javascript">
			document.getElementById('login').focus();
			var footer = new iknow_footer('../../js/common/iknow/');
			footer.add_element(__COUNTER_SESSION__,__FOOTER_RIGHT__);
			footer.generate();
		</script>
	</body>
</html>