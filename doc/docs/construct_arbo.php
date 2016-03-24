<?php 
	//require 'includes/common/ssid.php';
	//$ssid = $p_ssid;
	//require 'includes/common/define_db_names.php';
	
	$expand_file_id = 8;
	$tableau_js = array();
	$index_tableau_js = 0;
	$tableau_js[$index_tableau_js++] = 'Docs.classData = '.chr(10);
	$page = 'doc/docs/affiche.php';
	
	
	get_arbre(0,0);
	
	$tableau_js[$index_tableau_js++] = ';'.chr(10).chr(10).'Docs.icons = {};';
	//print_r($tableau_js);
	//echo $contenu_js;
	
	foreach($tableau_js as $value){
		
		echo $value;
	}
	
	function get_arbre($id_child,$level){
		
		global $tableau_js;
		global $index_tableau_js;
		global $page;
		global $ssid;
		$end = true;
		
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
		mysql_query("SET NAMES 'utf8'");
		
		$contenu_js = '';
		
		$sql = 'SELECT `ID_PARENT`,`ID_CHILD`,`ORDER`,`NAME`,`ICONE` 
				   		FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
				   		WHERE `ID_PARENT` = '.$id_child.' 
				   		AND version = '.$_SESSION['iknow']['version_soft'].' 
				   		ORDER BY `ID_PARENT` ASC, `ORDER` ASC';
	
		$resultat = mysql_query($sql);
		$i = 0;
		$nbr_ligne = mysql_num_rows($resultat);
		
		while ($row = mysql_fetch_array($resultat,MYSQL_ASSOC)) 
		{
			$i++;
			$temp = '';
			
			// On vérifie si l'élément à des enfants
			$sql_type_arbo = 'SELECT 1  
							FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
							WHERE `ID_PARENT` = '.$row['ID_CHILD'].' 
							LIMIT 1';
			
			$resultat_type_arbo = mysql_query($sql_type_arbo);
			if(mysql_num_rows($resultat_type_arbo) == 0)
			{
				$type_arbo = 'FILE';
				//$row['ICONE'] = 'icon-prop';
			}
			else
			{
				$type_arbo = 'FOLDER';
			}
			
			if(isset($_SESSION['identifier']) && $_SESSION['identifier'] == true)
			{
				$row['NAME'] = $row['NAME'].' ('.$row['ID_CHILD'].')';
			}

			
			
			// On vérifie si il y a des enfants
			$sql_enfants = 'SELECT COUNT(1)  
							FROM `'.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'` 
							WHERE `ID_PARENT` = '.$row['ID_CHILD'];
			
			$resultat_enfants = mysql_query($sql_enfants);
			$nbr_enfants = mysql_result($resultat_enfants,0);			
			
			if($id_child == 0)
			{
				// ROOT
				if($type_arbo == 'FILE')
				{
					if($nbr_ligne == $i)
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '<div style="font-weight:bold;">LAST '.$row['NAME'].'</div>';
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '<div style="font-weight:bold;">LAST '.$row['NAME'].'</div>';
						}
							
					}
					else
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '<div style="font-weight:bold;">- '.$row['NAME'].'</div>';	
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '<div style="font-weight:bold;">- '.$row['NAME'].'</div>';	
						}
						
					}
					
				}
				else
				{
					
					if($nbr_ligne == $i)
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"id":"apidocs","iconCls":"'.$row['ICONE'].'","text":"'.$row['NAME'].'","singleClickExpand":true,"children":[]}'.chr(10);
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"id":"apidocs","iconCls":"'.$row['ICONE'].'","text":"'.$row['NAME'].' - '.$_SESSION['iknow']['version_soft'].'","singleClickExpand":true,"children":['.chr(10);
						}
						
					}
					else
					{	
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"id":"apidocs","iconCls":"'.$row['ICONE'].'","text":"'.$row['NAME'].'","singleClickExpand":true,"children":[]}'.chr(10);
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"id":"apidocs","iconCls":"'.$row['ICONE'].'","text":"'.$row['NAME'].'","singleClickExpand":true,"children":['.chr(10);	
						}
						
					}
				}
	
			}	
			else
			{
				if($type_arbo == 'FILE')
				{
					if($nbr_ligne == $i)
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"href":"'.$page.'?ID='.$row['ID_CHILD'].'&ssid='.$ssid.'","text":"'.$row['NAME'].'","id":"'.get_arbo($row['ID_CHILD']).'","isClass":true,"iconCls":"'.$row['ICONE'].'","cls":"cls","leaf":true}'.chr(10);
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"href":"'.$page.'?ID='.$row['ID_CHILD'].'&ssid='.$ssid.'","text":"'.$row['NAME'].'","id":"'.get_arbo($row['ID_CHILD']).'","isClass":true,"iconCls":"'.$row['ICONE'].'","cls":"cls","leaf":true}'.chr(10);
							$end = true;	
						}
						
					}
					else
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"href":"'.$page.'?ID='.$row['ID_CHILD'].'&ssid='.$ssid.'","text":"'.$row['NAME'].'","id":"'.get_arbo($row['ID_CHILD']).'","isClass":true,"iconCls":"'.$row['ICONE'].'","cls":"cls","leaf":true},'.chr(10);	
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"href":"'.$page.'?ID='.$row['ID_CHILD'].'&ssid='.$ssid.'","text":"'.$row['NAME'].'","id":"'.get_arbo($row['ID_CHILD']).'","isClass":true,"iconCls":"'.$row['ICONE'].'","cls":"cls","leaf":true},'.chr(10);
							$end = true;
						}
						
					}
				}
				else
				{
					if($nbr_ligne == $i)
					{	
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"id":"'.get_arbo($row['ID_CHILD']).'&ssid='.$ssid.'","text":"'.$row['NAME'].'","iconCls":"'.$row['ICONE'].'","cls":"package","singleClickExpand":true, children:[]}'.chr(10);	
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"id":"'.get_arbo($row['ID_CHILD']).'&ssid='.$ssid.'","text":"'.$row['NAME'].'","iconCls":"'.$row['ICONE'].'","cls":"package","singleClickExpand":true, children:['.chr(10);
							$end = true;
						}
						
					}
					else
					{
						if($nbr_enfants == 0)
						{
							$tableau_js[$index_tableau_js++] = '{"id":"'.get_arbo($row['ID_CHILD']).'&ssid='.$ssid.'","text":"'.$row['NAME'].'","iconCls":"'.$row['ICONE'].'","cls":"package","singleClickExpand":true, children:[]}'.chr(10);
							$end = false;
						}
						else
						{
							$tableau_js[$index_tableau_js++] = '{"id":"'.get_arbo($row['ID_CHILD']).'&ssid='.$ssid.'","text":"'.$row['NAME'].'","iconCls":"'.$row['ICONE'].'","cls":"package","singleClickExpand":true, children:['.chr(10);
							$end = true;	
						}
						
					}
				}
			}
			
			$contenu = get_arbre($row['ID_CHILD'],$level + 1);
			if($contenu != '')
			{
				$tableau_js[$index_tableau_js++] = $contenu;
			}
			else
			{
				if($end == true)
				{
					
					if($nbr_ligne == $i)
					{
						$tableau_js[$index_tableau_js++] = ']}';
					}
					else
					{
						$tableau_js[$index_tableau_js++] = ']},';
					}					
				}
			}		
		}
		//return $tableau_js[$index_tableau_js++];
	}
	
	
	
	
	function get_arbo($id_child)
	{
		
		global $ssid;
		$link = mysql_connect($_SESSION['iknow'][$ssid]['serveur_bdd'], $_SESSION['iknow'][$ssid]['user_iknow'], $_SESSION['iknow'][$ssid]['password_iknow']);
		mysql_select_db($_SESSION['iknow'][$ssid]['schema_iknow']) or die('dbconn: mysql_select_db: ' + mysql_error());
			
			
		$arbo = array();
		$i = 0;
		$end = false;
		$child = $id_child;
		$link = '';
		
		while(!$end)
		{
			
			$sql = 'SELECT ID_PARENT
					FROM '.$_SESSION['iknow'][$ssid]['struct']['tb_documentation']['name'].'
					WHERE ID_CHILD = '.$child;	
			
			$resultat = mysql_query($sql);
			if(mysql_result($resultat,0) != false)
			{
				$arbo[$i] = mysql_result($resultat,0);
				$child = $arbo[$i];	
				
			}
			else
			{
				
				$end = true;
				
			}
	
			$i++;
			
		}
		
		//print_r($arbo);
		$arbo = array_reverse($arbo);
		foreach($arbo as $value)
		{
			if($value > 1)
			{
				$link .= 'i'.$value.'.';
			}
		}
		
		$link .= 'i'.$id_child;
	
		return $link;	
			
			
	}
?>