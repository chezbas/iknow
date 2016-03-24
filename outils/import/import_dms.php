<?php
die();
	ls_directory('D:/SCRIPT_DMS',0,$tableau,'.sql');

	$arr_query = array();
	$metiers = array();

	
	foreach($tableau as $value) 
	{
		$file_place = str_replace('D:/SCRIPT_DMS/','',$value);
		preg_match_all('#([^/]*)/?(.*)#i',$file_place,$out);
		
		$icode_title = $file_place;
		if(is_numeric(substr($icode_title,0,1)))
		{
			$icode_title = substr($icode_title,3);
		}
		
		$icode_title = str_replace('/',' - ',$icode_title);
		$icode_title = substr($icode_title,0,-4);
		
		$handle = fopen($value, "r");
		
		$page = '';
		while (!feof($handle)) 
		{ 
			$page .= fgets($handle); 
		}
		//$page = '';
		fclose($handle);
		$arr_query[] = Array('theme' => 'IMPORT','title' => utf8_encode($icode_title),'content' => utf8_encode($page));
	}
	/*echo '<pre>';
	print_r($arr_query);
	echo '</pre>';
	*/

	//mysql_connect('10.11.40.138','import','import');
	//mysql_connect('localhost','adm_iknow','MC&hny11') or die('error');
	mysql_select_db('iknow');	


	echo '<hr />';
	foreach ($arr_query as $value) 
	{
		/**==================================================================
	 	* Create iCode
	 	====================================================================*/	
		$sql = 'INSERT INTO `ikn_icodes`(`pole`, `Theme`, `VGS`, `AUTEUR`, `DATE`, `Version`, `prefixe`, `postfixe`, `typec`, `engine_version`, `corps`, `Titre`, `Commentaires`, `Last_update_user`, `last_update_date`, `obsolete`)
				VALUES("CENTR","'.$value['theme'].'","5064","IKN",NOW(),0,"&","","ORA","10g","'.mysql_real_escape_string($value['content']).'","'.mysql_real_escape_string($value['title']).'","","IKN","2011-10-18 13:00:00",0);';
		/*===================================================================*/
		//echo $sql.'<br />';
		//mysql_query($sql) or die(mysql_error());
		echo 'passe<hr />';
		//die();
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
					if(substr($file,-$size_extend) == $extension)
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