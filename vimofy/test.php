<html>
	<head>
		<link href="2.02/doc/include/arborescence/arbo.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript">
		function expand_reduce(chaine,id) {

			chaine_img = chaine + '_IMG';

			if ( document.getElementById(chaine).style.display == '' ) {
				document.getElementById(chaine).style.display = 'none';

				document.getElementById(chaine_img).setAttribute("src", "images/expand.png")
				}
			else {
				document.getElementById(chaine).style.display = '';

				document.getElementById(chaine_img).setAttribute("src", "images/reduce.png")
				}
			}
		
		function display_description(chaine,valeur) {
			//alert(valeur);
			document.getElementById('mytab_description').innerHTML = valeur;
			}
		</script> 
	</head>
	
	<body>
		<?php
			
			require('2.02/doc/include/arborescence/class_arbo_array.php');
			
			$heure_debut =  microtime(true);
			$arbo = new class_arbo_array('2.0','mytab');
			
			// En paramètre le nombre de tabulation de décalage pour le code source
			echo '<table><tr><td valign="top">'.$arbo->draw_tree(0).'</td>
			<td>&nbsp;</td>
			<td class="_cell_description" valign="top" id="mytab_description"></td></tr></table>'.chr(10);

			$heure_fin = microtime(true);
			echo "Temps : ".round($heure_fin - $heure_debut,3).'s';
		?>
	</body>
</html>
