<?php 
	$vimofy_id = 'vimofy_iobjet_version';
	/**==================================================================
	* Vimofy include
	====================================================================*/   
	$ssid = $_POST['ssid'];
	$dir_obj = '../../../../../../vimofy/';
	/*===================================================================*/   
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	// Create a reference to the session

	$obj_vimofy_version = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	/**==================================================================
	 * Define query
	 ====================================================================*/		
	if($_POST['p_type'] == '__IFICHE__')
	{
		$query = "SELECT 
					FIC.num_version as Version,
					FIC.`titre` as Title
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_fiches']['name']." FIC 
					WHERE 1 = 1 
					AND id_fiche = ".$_POST['object_id']." 
					UNION 
					SELECT '".$_SESSION[$ssid]['message']['iknow'][504]."',titre 
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']." FIC 
					WHERE 1 = 1 
					AND id_fiche = ".$_POST['object_id'];
	}
	else
	{
		$query = "SELECT r.Version, r.titre as Title
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_codes']['name']." r 
					WHERE r.ID = ".$_POST['object_id']." 
					UNION 
					SELECT '".$_SESSION[$ssid]['message']['iknow'][504]."',titre
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['name']." COD 
					WHERE 1 = 1 
					AND ID = ".$_POST['object_id'];
	
	}
	
	$obj_vimofy_version->define_query($query);
	/*===================================================================*/	

	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_version->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_version->define_size(775,'px',350,'px');											
	$obj_vimofy_version->define_nb_line(50);													
	$obj_vimofy_version->define_readonly(__R__);
	if($_POST['p_type'] == '__IFICHE__')
	{												
		$obj_vimofy_version->define_theme('green');
	}
	else
	{
		$obj_vimofy_version->define_theme('blue');
	}													
	$obj_vimofy_version->define_title($_SESSION[$ssid]['message'][42]);						
	$obj_vimofy_version->define_sep_col_row(true,false);
	$obj_vimofy_version->define_navbar_txt_activate(false);		
	$obj_vimofy_version->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_version->define_page_selection_display(false,true);
	$obj_vimofy_version->define_title_display(false);
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining columns
	 ====================================================================*/	
		
		/**==================================================================
		 * ID
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_version->define_column('Version',$_SESSION[$ssid]['message'][48],__TEXT__,__WRAP__,__LEFT__);
		/*===================================================================*/

		/**==================================================================
		 * Titre
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_version->define_column('Title',$_SESSION[$ssid]['message'][47],__BBCODE__,__WRAP__,__LEFT__);
		/*===================================================================*/	
		
	/*===================================================================*/	
	
	$obj_vimofy_version->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_vers_pole_lmod',Array('load_vim_parameters('.$_POST['p_type'].');/*gen_cartouche();*/enable_fields();'));
		
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_version->define_col_return('Version');
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining order
	 ====================================================================*/
	$obj_vimofy_version->define_order_column('Version',1,__DESC__);					
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining color mask
	 ====================================================================*/	
	$obj_vimofy_version->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_version->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	

?>