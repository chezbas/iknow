<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
	require('../vimofy/vimofy_active_version.php');
	$server_iknow = 'dtravel.iknow';
?>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<link rel="stylesheet" href="http://dtravel.iknow/css/common/error.css" type="text/css">	
		<link rel="stylesheet" href="http://dtravel.iknow/css/common/iknow/iknow_msgbox.css" type="text/css">
		<link rel="stylesheet" href="http://dtravel.iknow/vimofy/<?php echo $vimofy_active_version; ?>/css/object/vimofy_msgbox.css" type="text/css">
		<link rel="stylesheet" href="http://dtravel.iknow/css/common/icones_iknow.css" type="text/css">
		<title>iKnow - error <?php echo $_GET['ID']; ?></title>
		
		<?php 
		
			switch ($_GET['ID'])
			{
				case 403:
					$lib_err = 'Accès à la page est refusé';
					break;
				case 404:
					$lib_err = 'Document non trouvé';
					break;
				case 407:
					$lib_err = 'Accès à la ressource autorisé par identification avec le proxy';
					break;				
			}		
		?>
	</head>
	<body>
		<div class="header">
			<div class="logo"></div>
		</div>
		
		<div class="container">
			<div class="msgbox_conteneur" id="vim_msgbox_conteneur_vimofy2_varin">
				<div class="vimofy_msgbox_hg">
					<div class="vimofy_msgbox_hd">
						<div class="vimofy_msgbox_hm">
							<div class="vimofy_msgbox_c">
								<div class="title">Impossible d'afficher la page</div>
								<div class="error_detail">Erreur <span class="err_code"><?php echo $_GET['ID']; ?></span>, <?php echo $lib_err; ?>.</div>
								<div class="error_accueil" style="width: 400px;"><div class="boutton_url_home boutton_erreur" onclick="window.location.replace('http://dtravel.iknow');"></div></div>
							</div>
						</div>
					</div>
				</div>
				<div class="vimofy_msgbox_content_bg">
					<div class="vimofy_msgbox_content_bd">
						<div class="vimofy_msgbox_content_bm"></div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>