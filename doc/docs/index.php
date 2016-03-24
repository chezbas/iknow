<?php 
	session_start();
	require '../../../includes/common/version_active.php';
	
	if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
	{
		$administration = '- Administration';
	}
	else
	{
		$administration = '';
	}
?>
<html>
	<head>
		<script type="text/javascript">
		var verif_chargement;

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
		else {
			// XMLHttpRequest not supported by browser
			alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
			xhr = false; 
		}
		xhr.open("POST","deconnexion.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");	
		xhr.send();

		window.location.replace('index.php');
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
		
		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=0&ID="+id);
		window.location.replace('edit.php?ID='+xhr.responseText);


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
		
		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=1&ID="+id);
		window.location.replace('edit.php?ID='+xhr.responseText);


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
		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=2&ID="+id);
		alert(xhr.responseText);
		window.location.replace('index.php?ID='+xhr.responseText);
	}


	function delete_page(id)
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
	    else
		{
			// XMLHttpRequest non support� par le navigateur 
	       	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	       	xhr_connecter = false; 
	   	}


		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 


		// On v�rifie si on peu supprimer la page 
		xhr.send("action=3&ID="+id);
		if(xhr.responseText == 'false')
		{
			alert('Vous ne pouvez pas supprimer cet page car c\'est la derniere du parent');
			return false;
		}
		else
		{
			xhr.open("POST","actions.php",false);
			xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
			xhr.send("action=4&ID="+id);
			window.location.replace('index.php');

		}
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
		
		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=5&ID="+id);
		window.location.replace('index.php?ID='+xhr.responseText);
	

	}


	function monter(id)
	{
		alert(ssid);
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
		
		xhr.open("POST","actions.php",false);
		xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
		xhr.send("action=6&ID="+id);
		window.location.replace('index.php?ID='+xhr.responseText);
	}



	
		</script>
		<title>Documentation IKNOW <?php echo $administration; ?></title>
		
		<link rel="stylesheet" type="text/css" href="../resources/css/ext-all.css" />
		<link rel="stylesheet" type="text/css" href="resources/docs.css"></link>
		<link rel="stylesheet" type="text/css" href="resources/style.css"></link>
		<link rel="stylesheet" type="text/css" href="affiche.css"></link>
		<link rel="stylesheet" type="text/css" href="accueil.css"></link>
	</head>
	<body scroll="no" id="docs">
	
	
		<div id="loading-mask" style=""></div>
		<div id="loading">
			<div class="loading-indicator">
				<img src="resources/extanim32.gif" width="32" height="32" style="margin-right: 8px;" align="absmiddle" />Chargement...
			</div>
		</div>
		<!-- include everything after the loading indicator -->
		
		<script type="text/javascript" src="../adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="../ext-all.js"></script>
		<script type="text/javascript" src="resources/TabCloseMenu.js"></script>
		
		<!-- <script type="text/javascript" src="../../js/icode/init_tinymce.js"></script> -->		
		<!--<script type="text/javascript" src="docs.js"></script>-->
	
		<script type="text/javascript">
		
		
		

		<?php 
			
			require 'docs.php';
			require 'construct_arbo.php';
		
		?>
		
		</script>
		<!--<script type="text/javascript" src="output/tree_v2.js"></script>-->
		<div id="header">
			<div class="api-title">IKNOW 1.19 - Documentation <?php echo $administration;?></div>
		</div>
		
		<div id="classes"></div>
		
		<div id="main"></div>
		
		<select id="search-options" style="display: none">
			<option>Starts with</option>
			<option>Ends with</option>
			<option>Any Match</option>
		</select>
		
	</body>
	
</html>
