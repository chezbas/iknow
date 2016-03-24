<?php 
$id_arbre = "mytab";
?>
<html>
	<head>
		<link href="arbo.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="ajax_generique.js"></script>
		<script type="text/javascript" src="infobulle.js"></script>
		<script type="text/javascript">

		var selection_active = false;

		var id_arbre = '<?php echo $id_arbre;?>';
		
		// Mouse Up
		function mouseup(id)
		{
			if (selection_active) 
			{
				// Over an existing item
				if ( selection_id_source != "" && selection_id_source != id )
				{
					// Regenerate tree
					var configuration = new Array();	

					configuration['type_retour'] = false;		// Don't touch !! --> Reponse Text

					configuration['page'] = 'ajax_tree.php';
					configuration['param'] = 'id_source='+selection_id_source+'&id_target='+id;

					//configuration['div_wait'] = 'ajax_load_etape'+id_etape;
					//configuration['div_wait_nbr_tentative'] = 'ajax_load_etape_nbr_tentative'+id_etape;

					configuration['delai_tentative'] = 5000; 	// ms
					configuration['max_tentative'] = 4;			// Nombre d'essais

					configuration['div_a_modifier'] = id_arbre;
					//configuration['fonction_a_executer_reponse'] = 'message';
					//configuration['param_fonction_a_executer_reponse'] = variable+',33';

					//configuration['fonction_a_executer_cas_non_reponse'] = 'end_load_ajax';

					appel_ajax(configuration);	
				}

			var tableau = new Array();
			tableau = get_branch(id);
			if ( tableau[1] == 'P' )
			{
				expand_reduce(tableau[0]+'_EXPAND_'+tableau[2],'E');
			}
				// reset all
			document.getElementById(selection_id_source).style.color = "";
			selection_active = false;
			document.body.style.cursor = "pointer";
			selection_id_source = "";
			document.getElementById(id).style.color = "";
			hideddrivetip();
			}
		}

		function get_branch(id) {
			var tableau = new Array();
			tableau = id.split("_");

			//alert(tableau[6]);
			return tableau;
			}

		
		// Mouse down
		function mousedown(id,item_title) {
			ddrivetip(item_title, '', '300');

			selection_active = true;

			var tableau = new Array();
			tableau = get_branch(id);

			if ( tableau[1] == 'P' )
			{
				expand_reduce(tableau[0]+'_EXPAND_'+tableau[2],'R');
			}
			
			selection_id_source = id;
			
			document.getElementById(id).style.color = "red";
			
			document.getElementById('affiche').innerHTML = "->"+id;
			
			//alert(tableau);
			}

		// Mouse Over
		// item = true : Over item
		// item = false : Empty row
		function mouseover(id,item)
		{
			if ( item )
			{
				document.getElementById(id).style.color = "red";
			}
			else
			{
				document.getElementById(id).bgColor = "red";
			}	
			//document.body.style.cursor = "pointer";
			//document.body.style.cursor = "not-allowed";
		}
		
		// Mouse Out
		function mouseout(id,item)
		{
			if ( item )
			{
				document.getElementById(id).style.color = "";
			}
			else
			{
				document.getElementById(id).bgColor = "";
			}
		}
		

		function mouseup_global()
		{
			selection_active = false;
			hideddrivetip();
		}
		
		function message(retour)
		{
			//alert(retour);
		}
		// mode : E : Force Expand
		// mode : R :  Force Reduce
		// mode : empty : Auto switch
		function expand_reduce(chaine,mode)
		{

			chaine_img = chaine + '_IMG';
			//alert(chaine_img);
			if ( document.getElementById(chaine).style.display == '' || (mode == 'R' && mode != '' ) )
			{
				document.getElementById(chaine).style.display = 'none';

				document.getElementById(chaine_img).setAttribute("src", "images/expand.png")
			}
			else
			{
				document.getElementById(chaine).style.display = '';

				document.getElementById(chaine_img).setAttribute("src", "images/reduce.png")
			}
		}
		
		</script> 
	</head>
 
	<body>
			<!-- DIV Info bull advanced -->
			<div id="dhtmltooltip" style=" visibility:hidden; position:absolute; background-color:#FFFF88; border: solid; border-width:2px;"></div>
			<?php
			
			error_reporting(-1);
			
			require 'class_arbo_array.php';
			
			$heure_debut =  microtime(true);
			$arbo = new class_arbo_array('0.1',$id_arbre);
			
			// En paramètre le nombre de tabulation de décalage pour le code source
			echo '<div	
						onmouseup="javascript:mouseup_global();"
						onselectstart="return false;" style="height: 95%; width: 800px; overflow:auto;" 
						>
							<table class="tableau_arbre">
								<tr class="line">
									<td class="_cell_empty" 
										id="'.$id_arbre.'_E_0_Order_-1"
										onmouseover="javascript:mouseover(this.id,false)" 
										onmouseout="javascript:mouseout(this.id,false)"		
										onmouseup="javascript:mouseup(this.id);"				
										colspan=2
										>
										&nbsp;
									</td>
								</tr>
								<tr>
									<td id="'.$id_arbre.'" valign="top">'.$arbo->draw_tree(0).'</td>
									<td class="_cell_description" onmouseout="javascript:mouseup_global();" >&nbsp;</td>
									<td class="_cell_description" valign="top" id="mytab_description"></td>
								</tr>
							</table>
					</div>';

			$heure_fin = microtime(true);
			echo "Temps : ".round($heure_fin - $heure_debut,3).'s';
			?>
			<div id="affiche"></div>
		
		<script language="javascript">document.getElementById(id_arbre).oncontextmenu = function(){return false;};
	
		//************************************************************************************************************
		// FENETRE VOLANTE DDRIVETIP
		//************************************************************************************************************			
		var enabletip=false;
		var offsetxpoint=-10; //Customize x offset of tooltip
		var offsetypoint=10; //Customize y offset of tooltip
		var ie=document.all;
		var ns6=document.getElementById && !document.all;
		if (ie||ns6)
			var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : "";
		
		var tipobj=document.getElementById("dhtmltooltip");
		
		document.onmousemove=positiontip;
		//************************************************************************************************************	
	
	</script>
	</body>
</html>
