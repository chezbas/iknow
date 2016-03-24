	<?php
	die('die');
	ls_directory('D:/SQL_TOAD',0,$tableau,'.sql');


	$arr_query = array();
	$metiers = array();
	foreach($tableau as $value) 
	{
		$file_place = str_replace('D:/SQL_TOAD/','',$value);
		preg_match_all('#([^/]*)/(.*)#i',$file_place,$out);
		
		$handle = fopen($value, "r");
		$page = '';
		while (!feof($handle)) 
		{ 
			$page .= fgets($handle); 
		}
		
		fclose($handle);
		
		$icode_title = str_replace('/',' - ',$out[2][0]);
		$icode_title = substr($icode_title,0,-4);
		$arr_query[] = Array('theme' => str_replace(" ",'_',utf8_encode(strtoupper(substr($out[1][0],0,10)))),'title' => utf8_encode($icode_title),'content' => utf8_encode($page));
		
		$metiers[$out[1][0]] = $out[1][0];
	}
	
	/*echo '<pre>';
	print_r(print_r($arr_query));
	echo '</pre>';
	die();*/
	mysql_connect('10.11.40.138','import','import');
	//mysql_connect('localhost','adm_iknow','MC&hny11');
	mysql_select_db('iknow');	

	echo '<hr />';
	$i = 0;
	foreach ($arr_query as $value) 
	{
		/**==================================================================
	 	* Create iCode
	 	====================================================================*/	
		$sql = 'INSERT INTO `ikn_icodes`(`pole`, `Theme`, `VGS`, `AUTEUR`, `DATE`, `Version`, `prefixe`, `postfixe`, `typec`, `engine_version`, `corps`, `Titre`, `Commentaires`, `Last_update_user`, `last_update_date`, `obsolete`)
				VALUES("CENTR","'.$value['theme'].'","5064","DMM",NOW(),0,"&","","ORA","10g","'.str_replace('"','\"',$value['content']).'","'.str_replace('"','\"',$value['title']).'","","DMM",NOW(),0);';
		/*===================================================================*/
		//echo $value['title'].'<br />';
	//	mysql_query($sql) or die(mysql_error());
		//echo $sql.'<hr />';
	}







function ls_directory($path, $level, &$tab_ls,$extension)
{
	static $n = 0;
	$size_extend = strlen($extension);
	$fil = 1;
	if($handle = @opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if(strncmp($file, '.', 1) != 0) {
				if(is_dir($path.'/'.$file)) {
					$n++;
					//$tab_ls[$n] = array($file);
					ls_directory($path.'/'.$file, $n,$tab_ls,$extension);
				} 
				else 
				{
					if(strtolower(substr($file,-$size_extend)) == $extension)
					{
						$tab_ls[] = $path.'/'.$file;
						$fil++;
					}
				}
			}
		}
		closedir($handle);
	}	
}






/*
$directory = 'D:\SQL_TOAD';
$tab_dir = scan_dir_sql($directory);

echo '<pre>';
//print_r($tab_dir);
echo '</pre>';

function scan_dir_sql($p_directory)
{
	$dir = scandir($p_directory);

	foreach($dir as $value)
	{
		if(is_dir($p_directory.'\\'.$value) && $value != '.' && $value != '..')
		{
			$ss_dir = scan_dir_sql($p_directory.'\\'.$value);
			scan_dir_sql($p_directory.'\\'.$value);
			
			if(count($ss_dir))
			{
				//	echo $p_directory.'\\'.$value.'<br />';
			}
			else
			{
				echo '<span style="color:red;">'.$p_directory.'\\'.$value.'</span><br />';
			}
			$l_tab_dir[] = $value;
		}
	}
} 

*/
 
?>