<?php 
	require($dir_obj.'vimofy_includes.php');
	$vimofy_id = 'vimofy_search';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);

	// Create reference to current session
	$obj_vimofy_search = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	if(isset($_POST['id']) && $_POST['id'] != '')
	{
		if($_POST['iObject'] == '__ICODE__')
		{
			// iCode
			$query = " SELECT
						FIC.`id_fiche`,
						CONCAT('<a href=\"../ifiche.php?ID=',FIC.id_fiche,'&tab-level=2_2_',ACT.id_etape,'\" target=\"_blank\">',FIC.`titre`,'</a>') AS titre,
						ACT.`id_etape`
						FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']."` FIC,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_etapes']['name']."` ACT
						WHERE 1 = 1
						AND ACT.`id_fiche` = FIC.`id_fiche`
						AND ACT.`num_version` = FIC.`num_version`
						AND ACT.`description` REGEXP '(icode.php\\\\?*ID=".$_POST['id']."[^0-9])'";
		}
		else
		{
			// iSheet
			$query = " SELECT
						FIC.`id_fiche`,
						CONCAT('<a href=\"../ifiche.php?ID=',FIC.id_fiche,'&tab-level=2_2_',ACT.id_etape,'\" target=\"_blank\">',FIC.`titre`,'</a>') as titre,
						ACT.`id_etape` AS `id_etape`
						FROM `".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']."` FIC,
						`".$_SESSION['iknow'][$ssid]['struct']['tb_fiches_etapes']['name']."` ACT
						WHERE 1 = 1
						AND ACT.`id_fiche` = FIC.`id_fiche`
						AND ACT.`num_version` = FIC.`num_version`
						AND ACT.`description` REGEXP '(ifiche.php\\\\?*ID=".$_POST['id']."[^0-9])'";
		}
		
	}
	else
	{
		$query = "SELECT
					NULL AS `id_fiche`,
					NULL AS `id_etape`,
					NULL AS `titre`";
	}
	
	$obj_vimofy_search->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_search->define_size(100,'%',100,'%');
	$obj_vimofy_search->define_nb_line(50);
	$obj_vimofy_search->define_readonly(__R__);													// Read

	if(isset($_POST['iObject']) && $_POST['iObject'] == '__ICODE__')
	{
		$obj_vimofy_search->define_theme('blue');												// Define default style
	}
	else
	{
		$obj_vimofy_search->define_theme('green');												// Define default style
	}
	
	if(isset($_POST['id']) && $_POST['id'] != '')
	{
		if(isset($_POST['iObject']) && $_POST['iObject'] == '__ICODE__')
		{
			$obj_vimofy_search->define_title($_SESSION[$ssid]['message'][483].' '.$_POST['id']);
		}
		else
		{
			$obj_vimofy_search->define_title($_SESSION[$ssid]['message'][484].' '.$_POST['id']);
		}
	}
	$obj_vimofy_search->define_sep_col_row(true,false);
	$obj_vimofy_search->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
			
		//==================================================================
		// define column : iSheet id
		//==================================================================
		$obj_vimofy_search->define_column('id_fiche',$_SESSION[$ssid]['message'][480],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : iSheet title
		//==================================================================
		$obj_vimofy_search->define_column('titre',$_SESSION[$ssid]['message'][481],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : iSheet step
		//==================================================================
		$obj_vimofy_search->define_column('id_etape',$_SESSION[$ssid]['message'][482],__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
		
		
	//==================================================================
			
	//==================================================================
	// define action
	//==================================================================
		//$obj_vimofy_search->define_vimofy_action(__ON_UPDATE__,__AFTER__,$vimofy_id,Array('maj_vimofy_param=true;'));		
	//==================================================================
			
	$obj_vimofy_search->define_key(Array('id_fiche','id_etape'));
		
	//==================================================================
	// define default input focus
	//==================================================================
	$obj_vimofy_search->define_input_focus('id_fiche');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_search->define_order_column('id_fiche',1,__ASC__);
	$obj_vimofy_search->define_order_column('id_etape',2,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_search->define_color_mask("A5BEC2","CCC9AD","264A59","333","FFF");
	$obj_vimofy_search->define_color_mask("FFF","D0DCE0","7292CE","000","FFF");
	//==================================================================
?>