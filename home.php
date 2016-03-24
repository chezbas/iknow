<?php 
	/**==================================================================
	 * Get/Set ssid window identifier
	 * Start unique php session with ssid name
	 ====================================================================*/
	require('includes/common/ssid_simple.php');
	/*===================================================================*/	

	
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('includes/common/buffering.php');
	/*===================================================================*/	

	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('includes/common/global_functions.php');
	/*===================================================================*/	
	
	/**==================================================================
	 * Application release
	 * Page database connexion
	 * Load configuration parameters in session
	 ====================================================================*/	
	require('includes/common/load_conf_session.php');
	/*===================================================================*/	
	
	$_SESSION['fichier_doc'] = 'index.php?ssid='.$ssid;
	
	if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
	{
		$administration = '- Administration';
	}
	else
	{
		$administration = '';
	}

	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('includes/common/html_doctype.php');
	/*===================================================================*/	
?>
<html>
	<head>
	<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" type="image/png" href="favicon.ico" />
		<link rel="stylesheet" href="css/common/iknow/iknow_msgbox.css" type="text/css">
		<link rel="stylesheet" href="css/common/icones_iknow.css" type="text/css">
		<script type="text/javascript" src="js/common/iknow/iknow_msgbox.js"></script>
		<script type="text/javascript" src="js/common/iknow/iknow_footer.js"></script>
		<script type="text/javascript" src="ajax/common/ajax_generique.js"></script>
		<script type="text/javascript" src="js/common/session_management.js"></script>
		<script type="text/javascript">

		var libelle_common = Array();
		libelle_common[3] = '$x écran(s) restant.';
		libelle_common[4] = 'Message important';
	
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
		
		var ssid = '<?php echo $ssid ?>';
		var verif_chargement;
		

	function ddrivetipimg()
	{

	
	}

	function hideddrivetip()
	{

	
	}


	function go_to_ifiche()
	{
		if(document.getElementById('input_id_ifiche').value != '')
		{
			window.open('ifiche.php?ID='+document.getElementById('input_id_ifiche').value);
		}
		else
		{
			alert('Veuillez sasir un ID');
		}
	}

	function go_to_icode()
	{
		if(document.getElementById('input_id_icode').value != '')
		{
			window.open('icode.php?ID='+document.getElementById('input_id_icode').value);
		}
		else
		{
			alert('Veuillez sasir un ID');
		}
	}	
		
	function verif_chargemement_page(id)
	{
		if(document.getElementById('matiny'))
		{
			clearInterval(verif_chargement);
			initmce_modif();
		}
	}


	function deconnexion()
	{
		if(window.XMLHttpRequest) // Firefox et autres
			xhr = new XMLHttpRequest(); 
		else if(window.ActiveXObject){ // Internet Explorer 
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
				
			} catch (e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		else { // 
			alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
			xhr = false; 
		}

	
		xhr.open("POST","doc/docs/identification/logout.php?ssid="+ssid,false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");	
		xhr.send("ssid="+ssid);

		window.location.href = '.?ssid=<?php echo $ssid; ?>';
		
		
	}

	function copier_page(id)
	{


		if(window.XMLHttpRequest) // Firefox et autres
	            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}
		
		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=0&ID="+id+"&ssid="+ssid);
		window.location.replace('doc/docs/edit.php?ID='+xhr.responseText);


	}

	function ajouter_frere(id)
	{
		if(window.XMLHttpRequest) // Firefox et autres
	            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}
		
		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=1&ID="+id+"&ssid="+ssid);
		window.location.replace('doc/docs/edit.php?ssid='+ssid+'&ID='+xhr.responseText);
	}


	function ajouter_parent(id)
	{
		if(window.XMLHttpRequest) // Firefox et autres
	            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}
		
		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=2&ID="+id+"&ssid="+ssid);
		window.location.replace('<?php echo $_SESSION['fichier_doc']; ?>&ID='+xhr.responseText);
	}


	function delete_page(id)
	{
	    Ext.MessageBox.confirm('Suppression', 'Etes vous sur de vouloir supprimer la page?',function(btn, text){
	        if (btn == 'yes'){
	    		

		if(window.XMLHttpRequest) // Firefox et autres
	            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}


		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 


		// On v�rifie si on peu supprimer la page 
		xhr.send("action=3&ID="+id+"&ssid="+ssid);
		if(xhr.responseText == 'false')
		{

			Ext.MessageBox.alert('Erreur','Vous ne pouvez pas supprimer cet page car c\'est la derni�re du parent.');
			return false;
		}
		else
		{
			xhr.open("POST","doc/docs/actions.php",false);
			xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
			xhr.send("action=4&ID="+id+"&ssid="+ssid);
			window.location.replace('<?php echo $_SESSION['fichier_doc']; ?>');

		}

	        }
        });		

	}

	function descendre(id)
	{
		
		if(window.XMLHttpRequest) // Firefox et autres
            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}
		
		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=5&ID="+id+"&ssid="+ssid);
		window.location.replace('<?php echo $_SESSION['fichier_doc']; ?>&ID='+xhr.responseText);
	

	}


	function monter(id)
	{
		if(window.XMLHttpRequest) // Firefox et autres
            xhr = new XMLHttpRequest(); 
	    else if(window.ActiveXObject){ // Internet Explorer 
	    	try {
	    		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch (e) {
	        	xhr = new ActiveXObject("Microsoft.XMLHTTP");
	        }
	    }
	    else { // XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}
		
		xhr.open("POST","doc/docs/actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=6&ID="+id+"&ssid="+ssid);
		window.location.replace('<?php echo $_SESSION['fichier_doc']; ?>&ID='+xhr.responseText);
	

	}

	function lib_hover(p_lib)
	{
		document.getElementById('txt_help').innerHTML = p_lib;
	}

	function lib_out()
	{
		document.getElementById('txt_help').innerHTML = '';
	}

		</script>
		<title>Portail IKNOW <?php echo $administration; ?></title>
		<link rel="stylesheet" type="text/css" href="css/common/icones_iknow.css" />
		<link rel="stylesheet" type="text/css" href="css/common/header.css" />
		<link rel="stylesheet" type="text/css" href="doc/resources/css/ext-all.css" />
		<link rel="stylesheet" type="text/css" href="doc/docs/resources/docs.css"/>
		<link rel="stylesheet" type="text/css" href="doc/docs/resources/style.css"/>
		<link rel="stylesheet" type="text/css" href="doc/docs/affiche.css"/>
		<link rel="stylesheet" type="text/css" href="doc/docs/accueil.css"/>
		<link rel="stylesheet" type="text/css" href="css/common/outils.css"/>
			<style type="text/css">
			body 
			{
				background-color: #EEE;
				margin:0;
				padding:0;
				font-weight: bold;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			
			#loading 
			{
				height:270px;
				width:480px;
				top:50%;
				margin-top:-135px;
				margin-left:-240px;
				position:absolute;
				left:50%;
				background: url(images/iknow_load.jpg) no-repeat center;
				box-shadow: 6px 6px 10px #000;
				-webkit-box-shadow: 6px 6px 10px #000; 
			}
			
			
			.lib_load
			{
				bottom:15px;
				left:20px;
				position:absolute;
			}
			
			.ikn_version
			{
				bottom:15px;
				right:20px;
				position:absolute;
			}
		</style>
			
	</head>
	<body id="docs">
		<div id="loading-mask" style=""></div>
		<div id="loading">
			<div class="loading-indicator"></div>
			<div class="lib_load">Chargement du portail en cours...</div>
			<div class="ikn_version"><?php echo $version_soft; ?></div>
		</div>
		<!-- include everything after the loading indicator -->
		
		<script type="text/javascript" src="doc/adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="doc/ext-all.js"></script>
		<script type="text/javascript" src="doc/docs/resources/TabCloseMenu.js"></script>
		
		<!-- <script type="text/javascript" src="../../js/icode/init_tinymce.js"></script> -->		
		<!--<script type="text/javascript" src="docs.js"></script>-->
		<script type="text/javascript">
		
		
		

		<?php 
			require 'doc/docs/docs.php';
			require 'doc/docs/construct_arbo.php';	
		?>
		
		</script>
		<!--<script type="text/javascript" src="output/tree_v2.js"></script>-->
	
		
		<div id="classes"></div>
		
		<div id="main"></div>
		
		<select id="search-options" style="display: none">
			<option>Starts with</option>
			<option>Ends with</option>
			<option>Any Match</option>
		</select>
	</body>
</html>