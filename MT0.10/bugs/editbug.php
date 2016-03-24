<?php
	/**==================================================================
	 * MagicTree configuration ( Need active php session and ssid definition )
	 ====================================================================*/
	require('../../includes/MTSetup/setup.php');
	/*===================================================================*/		


	/**==================================================================
	 * Automatic detection of current MagicTree version
	 ====================================================================*/
	require('../includes/common/version_active.php');
	/*===================================================================*/		

	
	$application_release = $version_soft;

	
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('../includes/common/ssid_session_start.php');
	/*===================================================================*/		
	$_SESSION[$ssid]['MT']['application_release'] = $application_release;
	

	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../includes/common/buffering.php');
	/*===================================================================*/	
		
	
	/**==================================================================
	 * Load global functions
	 ====================================================================*/	
	require('../includes/common/global_functions.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('../includes/common/load_conf_session.php');
	/*===================================================================*/	
	
	
	/**==================================================================
	 * Recover language from URL or Database
	 ====================================================================*/	
	require('../includes/common/language.php');
	/*===================================================================*/ 
	
	
	//==================================================================
	// Load bug ID number
	//==================================================================
	if(!isset($_GET["ID"]))
	{
		error_log_details('fatal','No bug ID define. You have to provide one');
	}
	else
	{
		$page = $_GET["ID"];
	}
	//==================================================================
	
	$query = "SELECT
				`details` AS 'details',
				`description` AS 'description',
				`solution` AS 'solution'
			FROM 
				`".__MAGICTREE_TABLE_EXTRA_TICK__."`
			WHERE 1 = 1
				AND `ID` = '".$page."'";

	$result = $link_mt->query($query);
	
	$row = $result->fetch_array(MYSQLI_ASSOC);

	$texte = htmlentities($row["details"]);
	$description = $row["description"];
	$solution =  htmlentities($row["solution"]);;
	
	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('../includes/common/html_doctype.php');
	/*===================================================================*/	
	// Page language equal magic tree language
	$_SESSION[$ssid]['langue'] = $_SESSION[$ssid]['MT']['langue'];
?>
<html>
	<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="css/editbug.css" type="text/css"> <!-- * load custom page style * -->

	<script type="text/javascript">
		var ssid = '<?php echo $ssid; ?>';
		<?php 
				/**==================================================================
				 * Load text in both php session and javascript
				 * Warning : Must be in <head> html bloc 
				 ====================================================================*/	
				$text_group = 'bug';
				require('../includes/common/textes_extra_screen.php');
				echo chr(10);
				/*===================================================================*/
		?>	
	</script>
	<script  type="text/javascript" src="../js/ajax/common/ajax_generique_dev.js"></script> <!-- * Include Ajax connexion * -->
	<script type="text/javascript" src="../js/common/tiny/tiny_mce.js"></script> <!-- TinyMCE -->
	<script  type="text/javascript" src="js/editbug.js"></script> <!-- Specific javascript of current page -->

	<title><?php echo str_replace('$id',$page,$_SESSION[$ssid]['page_text'][29]['LT']); ?> </title>
	</head>
	<body onload="init_load('<?php echo $_SESSION[$ssid]['MT']['langue_TinyMCE'];?>');">
		<form method="post" action="javascript:sauvegarder(ssid,'<?php echo $page;?>');">
			<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
			<div class="description_main">
				<div class="title_caption"><?php echo str_replace('$id',$page,$_SESSION[$ssid]['page_text'][7]['ST']).' : '.$description; ?></div>
				<div class="description_caption"><?php echo $_SESSION[$ssid]['page_text'][14]['LT']?></div>
				<textarea id="elm1" name="elm1" rows="25" cols="90" style="width: 100%;"><?php echo $texte; ?></textarea>
			</div>
			<div class="solution_main">
				<div class="solution_caption"><?php echo $_SESSION[$ssid]['page_text'][27]['LT']?></div>
				<textarea id="solution" name="solution" rows="25" cols="90" style="width: 100%;"><?php echo $solution; ?></textarea>
			</div>
		<br />
		</form>
	</body>
</html>
