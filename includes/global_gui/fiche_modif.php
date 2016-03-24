<?php
	require('includes/global_gui/init_text.php');

	
	/**==================================================================
	 * HTML declare page interpretation directive
	 ====================================================================*/	
	require('includes/common/html_doctype.php');
	/*===================================================================*/	
?>
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<script type="text/javascript">
		<?php
		/**==================================================================
		* Recover text
		====================================================================*/	
		$type_soft = 999; // Nothing : Load only general text iknow
		require('includes/common/textes.php');
		/*===================================================================*/	
		?>
		</script>
		<link rel="shortcut icon" type="image/png" href="favicon.ico" />
		<title><?php echo $_SESSION[$ssid]['message']['iknow'][520]; ?></title>
		<style type="text/css">
			body 
			{
				background-color: #A61415;
				margin:0;
				padding:0;
				font-weight: bold;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 11px;
			}
			
			#chargement 
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
				bottom:10px;
				left:15px;
				position:absolute;
			}
			
			.ikn_version
			{
				bottom:10px;
				right:15px;
				position:absolute;
			}
		</style>

	</head>
	<body onload="setTimeout('window.location.replace(chaine)',50);">
		<div id="chargement">
			<div class="lib_load"><?php echo $_SESSION[$ssid]['message']['iknow'][519]; ?></div>
			<div class="ikn_version"><?php echo $version_soft; ?></div>
		</div>
	</body>
</html>