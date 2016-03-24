<?php
	$ssid = $_GET['ssid'];
	session_name($ssid);
	session_start();
	$dir_ident = '';

	if(!isset($_SESSION['identifier']) && $_SESSION['identifier'] != true)
	{
		require $dir_ident.'identification.php';
		die();	
	}
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<title>Modification de l'ID <?php echo $_GET["ID"]; ?></title>
		<script type="text/javascript" src="../../js/common/tiny/tiny_mce.js"></script>	
		
		<script type="text/javascript">

			function sauvegarder_contenu(){

				icone_parent = '';
				if(document.getElementById('choix_parent'))
				{
					radio = document.getElementsByName('choix_parent');
					
					for (var i=0; i < 10;i++)
					{
				         if (radio[i].checked) {
				        	 icone_parent = radio[i].value;
				         }
				    }
				}
				radio = document.getElementsByName('choix_child');
				icone_child = '';
				for (var i=0; i < 10;i++)
				{
					
			         if (radio[i].checked) {
			        	 icone_child = radio[i].value;
			         }
			    }

				
				var id = '<?php echo $_GET["ID"]; ?>';
				var contenu = encodeURIComponent(tinyMCE.get('matiny').getContent());
				if(document.getElementById('titre_parent'))
				{
					var titre_parent = encodeURIComponent(document.getElementById('titre_parent').value);
				}
				else
				{
					var titre_parent = '';
				}
				var titre = encodeURIComponent(document.getElementById('titre').value);
				


				if(contenu == '' || titre == '')
				{
					alert('Aucun champ ne doit être vide !');
				}
				else
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
				    	xhr= false; 
				    }
				    
		
					xhr.open("POST","sauvegarde_doc.php",false);
					xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");    
				    
					xhr.send("id="+id+"&contenu="+contenu+"&titre="+titre+"&titre_parent="+titre_parent+"&icone_parent="+icone_parent+"&icone_child="+icone_child+'&ssid=<?php echo $ssid; ?>');
					window.location.replace('../../<?php echo $_SESSION['fichier_doc']; ?>&ID='+id);
					
				}
			}

			
			function initmce_modif() {
				
				tinyMCE.init({	
					mode : "textareas",
					theme : "advanced",
					readonly : false,
					plugins : "insertdatetime,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
					language : 'fr',
					editor_selector : 'matiny',

					// Theme options
					theme_advanced_buttons1 : "save,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,forecolor,backcolor,code,image,preview,cleanup,|,insertimage,",
					theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,outdent,indent,|,undo,redo,|,link,unlink,hr,removeformat,|,sub,sup,|,charmap,|,fullscreen,",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true
					
				});		
			}
			
		</script>
	</head>

	<body>

		<?php
		
					$table_icones = array();
					$table_icones[1] = 'cls';	
					$table_icones[2] = 'event';
					$table_icones[3] = 'config';
					$table_icones[4] = 'prop';
					$table_icones[5] = 'method';
					$table_icones[6] = 'cmp';
					$table_icones[7] = 'pkg';
					$table_icones[8] = 'fav';
					$table_icones[9] = 'static';
					$table_icones[10] = 'docs';	
		
			if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
			{
			
				$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
				mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());

				mysql_query("SET NAMES 'utf8'");
				$contenu_js = '';
				
				$sql = 'SELECT `description`,`NAME`,`ID_PARENT`,`icone` 
						   		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						   		WHERE `ID_CHILD` = '.$_GET["ID"].' 
						   		AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
				$resultat = mysql_query($sql);
				$texte = (mysql_result($resultat,0,'description'));
				$titre = (mysql_result($resultat,0,'NAME'));
				$icone = (mysql_result($resultat,0,'icone'));
				$id_parent = mysql_result($resultat,0,'ID_PARENT');
				
				$sql = 'SELECT `NAME`,`icone`  
						   		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
						   		WHERE `ID_CHILD` = '.$id_parent.' 
						   		AND version = "'.$_SESSION['iknow']['version_soft'].'"';
		
				$resultat = mysql_query($sql);
				if(mysql_num_rows($resultat) > 0)
				{
					$titre_parent = (mysql_result($resultat,0,'NAME'));	
					$icone_parent = (mysql_result($resultat,0,'icone'));
					$tableau_edit_titre = '<tr><td>Titre de mon parent</td><td><input type="text" id="titre_parent" value="'.$titre_parent.'" SIZE="120"/></td></tr>';	
					$tableau_edit_icone_parent = '<tr><td id="choix_parent">Icone de mon parent</td>
												<td>';


					foreach($table_icones as $value)
					{
						if($icone_parent == 'icon-'.$value)
						{
							$tableau_edit_icone_parent .= '<input type="radio" name="choix_parent" value="icon-'.$value.'" CHECKED/><img src="resources/'.$value.'.gif"/>';

						}
						else
						{
							$tableau_edit_icone_parent .= '<input type="radio" name="choix_parent" value="icon-'.$value.'"/><img src="resources/'.$value.'.gif"/>';

						}
						
						
					}
													
					$tableau_edit_icone_parent .= '</td></tr>';		

				}
				else
				{
					$tableau_edit_titre = '';
					$tableau_edit_icone_parent = '';
				}
				
				echo '<table>';
				echo $tableau_edit_titre;
				echo $tableau_edit_icone_parent;
				echo '<tr>';
				echo '<td>Titre</td>';
				echo '<td><input type="text" id="titre" value="'.$titre.'" SIZE="120"/></td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>Contenu</td>';
				echo '<td><form method="post" action="javascript:sauvegarder_contenu();">';
				//echo '<textarea id="content_docedit.php?ID='.$_GET["ID"].'" style="height:400px;" name="content_docedit.php?ID='.$_GET["ID"].'" class="content_docedit.php?ID='.$_GET["ID"].'">'.$texte.'</textarea>';
				echo '<textarea id="matiny" style="height:400px;" name="matiny" class="matiny">'.htmlentities($texte).'</textarea>';
				echo '</form></td>';
				echo '</tr>';
				
				$tableau_edit_icone = '<tr><td>Mon icone</td>
												<td>';


					foreach($table_icones as $value)
					{
						if($icone == 'icon-'.$value)
						{
							$tableau_edit_icone .= '<input type="radio" name="choix_child" value="icon-'.$value.'" CHECKED/><img src="resources/'.$value.'.gif"/>';

						}
						else
						{
							$tableau_edit_icone .= '<input type="radio" name="choix_child" value="icon-'.$value.'"/><img src="resources/'.$value.'.gif"/>';

						}
						
						
					}
													
					$tableau_edit_icone .= '</td></tr>';						
				
				echo $tableau_edit_icone;
				echo '<tr>';
				echo '<td></td>';
				echo '<td><input type="button" id="annuler" value="Annuler" onclick="window.location.replace(\'../../'.$_SESSION['fichier_doc'].'?ID='.$_GET["ID"].'&ssid='.$_GET["ssid"].'\');"/></td>';
				echo '</tr>';
				echo '</table>';								
			}
		
		?>
		<script type="text/javascript">
			initmce_modif();
		</script>
	</body>
</html>