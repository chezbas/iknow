<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet updating mode head module list
 ====================================================================*/

	$vimofy_id = 'vimofy2_module_lmod';
	
	//==================================================================
	// Load Lisha framework
	//==================================================================
	if(isset($_POST['ssid']))
	{
		$ssid = $_POST['ssid'];

		/**==================================================================
		 * Load Lisha framework
		 ====================================================================*/
		$dir_obj = '../../../../vimofy/';
		require($dir_obj.'vimofy_includes.php');
		/*===================================================================*/	
		

		/**==================================================================
		 * Active php session
		 ====================================================================*/
		require('../../../../includes/common/active_session.php');
		/*===================================================================*/	

		
		unset($_SESSION['vimofy'][$ssid][$vimofy_id]);
	}
	//==================================================================
	
	//==================================================================
	// Depend on area id value
	//==================================================================
	if(!isset($_POST['pole']))
	{
		$vim_pole = $_SESSION[$ssid]['objet_fiche']->get_id_pole();   
	}
	else
	{
		$vim_pole = $_POST['pole'];
	}
	//==================================================================
		
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_modules_lmod = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "SELECT
			  	`modules`.`ID`		AS	'ID',
			  	`metiers`.`libelle` AS	'Metier',
			  	`modules`.`libelle` AS	'Module' 
			  FROM
			  	`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name'] ."` `modules`,
			  	`".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name'] ."` `metiers`
			  WHERE 1 = 1 
			  	AND `modules`.`id_POLE`		= '".$vim_pole."'
			  	AND `modules`.`ID_METIER`	= `metiers`.`ID`
  			  	AND `metiers`.`id_POLE`		= `modules`.`id_POLE`";
	
	$obj_vimofy_modules_lmod->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_modules_lmod->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_modules_lmod->define_size(550,'px',280,'px');											
	$obj_vimofy_modules_lmod->define_nb_line(50);													
	$obj_vimofy_modules_lmod->define_readonly(__R__);												
	$obj_vimofy_modules_lmod->define_theme('grey');													
	$obj_vimofy_modules_lmod->define_title_display(false);
	//$obj_vimofy_modules_lmod->define_title($_SESSION[$ssid]['message'][52]);						
	$obj_vimofy_modules_lmod->define_sep_col_row(true,false);
	$obj_vimofy_modules_lmod->define_navbar_txt_activate(false);		
	$obj_vimofy_modules_lmod->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_modules_lmod->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet area work
		//==================================================================
		$obj_vimofy_modules_lmod->define_column('Metier',$_SESSION[$ssid]['message'][550],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : iSheet area subwork
		//==================================================================
		$obj_vimofy_modules_lmod->define_column('Module',$_SESSION[$ssid]['message'][551],__TEXT__,__WRAP__,__LEFT__);						
		//==================================================================
				
		//==================================================================
		// define column : Key of work and subwork
		//==================================================================
		$obj_vimofy_modules_lmod->define_column('ID',$_SESSION[$ssid]['message'][46],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_modules_lmod->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_vers_pole_lmod',Array('get_libelle_module();iknow_panel_set_action(decodeURIComponent(libelle[338]));'));		
	//==================================================================
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_modules_lmod->define_col_return('ID');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_modules_lmod->define_order_column('Metier',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_modules_lmod->define_color_mask("FFF","CCC","ADA","444","FFF");
	$obj_vimofy_modules_lmod->define_color_mask("EEE","BBB","585","333","DDD");
	//==================================================================

	if(isset($_POST['ssid']))
	{
		echo $obj_vimofy_modules_lmod->generate_lmod_form();
	}
?>