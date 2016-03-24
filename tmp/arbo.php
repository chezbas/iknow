<?php 

	$ssid = 'zeekrj';
	require('../includes/common/define_db_names.php') ;
	require('class_bdd.php');
	require('class_node.php');
	
	$obj_node = new class_node($ssid,575,244);
	echo 'ok';
	
	
	
	
	
/*	$link = mysql_connect('localhost','adm_iknow','MC&hny11');
	mysql_select_db('iknow',$link) or die('dbconn: mysql_select_db: ' + mysql_error());

	$sql = 'SELECT `description`,`id_etape`
			FROM `ikn_ifiche_etapes`
			WHERE  1 = 1 
			AND `id_fiche` = 575
			AND `num_version` = 244';

	$result = mysql_query($sql,$link);
		
	$motif = '#<a.+href="ifiche.php\?(&amp;)??ID=([0-9]+?)("|">|&amp;)#';
	$tab = Array();
	
	//$test = new object();
	while($row = mysql_fetch_array($result,MYSQL_ASSOC))
	{
		preg_match_all($motif,$row['description'],$out);
		
		if(isset($out[2][0]))
		{
			/*echo '<pre>';
			print_r($out[2]);
			echo '</pre><hr />';*/
		/*	$tab[$row['id_etape']] = new class_node($out[2]);
		}
	}	
	echo '<pre>';
	print_r($tab);
*/
?>