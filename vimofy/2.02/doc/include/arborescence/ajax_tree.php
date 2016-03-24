<?php
ob_start("ob_gzhandler");
require 'class_arbo_array.php';
 
$id_source = $_POST["id_source"];
$id_target = $_POST["id_target"];

$tab_s = explode("_",$id_source);
$tab_t = explode("_",$id_target);

$id_arbre = "mytab";
$arbo = new class_arbo_array('0.1',$id_arbre);

// Connexion à la base de données
$link = mysql_connect('localhost','root','toto');
mysql_select_db('galerie_photo',$link);

/*==================================================================*/
// Recover source information
/*==================================================================*/
$sql = "SELECT parent FROM `configuration` WHERE id = '".$tab_s[2]."' AND `version` = '0.1';";
$query_sql = mysql_query($sql,$link) or die (mysql_error($link));
$row = mysql_fetch_row($query_sql);
$parent_source = $row[0];
/*==================================================================*/

if ($tab_t[1] == "E" ) { // empty line

	// Renumbering level of target
	$sql_renumbering = 'call renumbering_level('.$tab_t[2].','.($tab_t[4]+1).')';
	$query_sql_renumbering = mysql_query($sql_renumbering,$link) or die (mysql_error($link));
//	echo ($tab_t[2]);die();
	// Set new child with brother and sister
	$sql_no_child = 'UPDATE `configuration` 
							SET parent = "'.$tab_t[2].'",
								`order` = '.($tab_t[4]+1).'
							WHERE 1 = 1
							AND `id` = "'.$tab_s[2].'"
							LIMIT 1
							';
	$query_sql_no_child= mysql_query($sql_no_child,$link) or die (mysql_error($link));

	// Renumbering level of source
	$sql_renumbering = 'call renumbering_level('.$parent_source.',-1)';
	$query_sql_renumbering = mysql_query($sql_renumbering,$link) or die (mysql_error($link));
	
	}
else {	
		// Check 2 differents way for existing target
		if (get_any_child($tab_t[2],'0.1',$link)) { // Already a parent
			// Count children on target level
			$sql_max = "SELECT count(1) FROM `configuration` WHERE parent = '".$tab_t[2]."' AND `version` = '0.1';";
			$query_sql_max = mysql_query($sql_max,$link) or die (mysql_error($link));
			$row_max = mysql_fetch_row($query_sql_max);
			$max_child_order = $row_max[0];
			
			// Set new child with brother and sister
			$sql_no_child = 'UPDATE `configuration` 
									SET parent = "'.$tab_t[2].'",
										`order` = '.$max_child_order.'
									WHERE 1 = 1
									AND `id` = "'.$tab_s[2].'"
									LIMIT 1
									';
			$query_sql_no_child= mysql_query($sql_no_child,$link) or die (mysql_error($link));
			
		
			// Renumbering level of source
			$sql_renumbering = 'call renumbering_level('.$parent_source.',-1)';
			$query_sql_renumbering = mysql_query($sql_renumbering,$link) or die (mysql_error($link));
			
			}
		else { // Not yet a parent
			// Set current child as futur parent
			$sql_no_child = 'UPDATE `configuration` 
									SET parent = "'.$tab_t[2].'",
										`order` = 0
									WHERE 1 = 1
									AND `id` = "'.$tab_s[2].'"
									LIMIT 1
									';
			$query_sql_no_child= mysql_query($sql_no_child,$link) or die (mysql_error($link));
			
			// Renumbering level of source
			$sql_renumbering = 'call renumbering_level('.$parent_source.',-1)';
			$query_sql_renumbering = mysql_query($sql_renumbering,$link) or die (mysql_error($link));
			
			}
	}	

echo $arbo->draw_tree(0);

/*==================================================================*/
function get_any_child($id,$version,$link)
{
	$sql_enfants = 'SELECT 1 FROM `configuration` `level_n`,`configuration` `level_n+1`
					WHERE 1 = 1
					AND `level_n`.`id` = `level_n+1`.`parent`
					AND `level_n`.`version` = `level_n+1`.`version`
					AND `level_n`.`version` = '.$version.'
					AND `level_n`.`id` = '.$id.' LIMIT 1';
	
	$resultat_enfants = mysql_query($sql_enfants,$link);
				
	return mysql_num_rows($resultat_enfants);		
}
?>