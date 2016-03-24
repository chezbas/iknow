<?php
	//==================================================================
	// Begin of lisha object
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_process_list_id'] = new lisha(
		'lisha_process_list_id',
		$ssid,
		__MYSQL__,
		array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),
		$path_root_lisha,
		false
	);

	//==================================================================
	// Define main query
	//==================================================================
	$query =   "SELECT 
					MET.`libelle` AS 'Domaine',
					MODU.`libelle` AS 'Sousdomaine',
					MODU.`ID` AS 'Module'
				".$_SESSION[$ssid]['lisha']['configuration'][10]."
					`".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name']."` MET,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']."` MODU
				WHERE 1 = 1
					AND MET.`ID` = MODU.`ID_METIER`
					AND MODU.`id_POLE` = MET.`id_POLE`
					AND MODU.`id_POLE` = '".$pole."'
					AND ".$str_cond_pole;

	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__main_query', $query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_size(100,'%',100,'%');												// width 700px, height 500px
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_nb_line(50);															// 20 lines per page
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_readonly_mode', __R__);							// Read & Write
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__id_theme','blue');									// Define default style
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__background_picture', 'images/iknow.png');

	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_title', false);

	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_column_separation',true);
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_row_separation',false);
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_top_bar_page',false);
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_bottom_bar_page',true);
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_user_doc', false);				// user documentation button
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_tech_doc', false);				// technical documentation button
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_ticket', false);					// Tickets link
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__display_mode', __NMOD__);					// Display mode
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__key_url_custom_view', 'LP');				// Defined key for quick custom view loader in url browser
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__update_table_name', $_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']);		// Define table to update
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__column_name_group_of_color', "MyGroupTheme");		// ( Optional ) Define custom column color name
	
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_attribute('__active_quick_search', true);				        // Quick search mode ( Optional : default true )
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================

		//==================================================================
		// define column : work
		//==================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_column("MET.`libelle`",'domaine','Domaine',__TEXT__,__WRAP__,__CENTER__);
		$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_input_focus('domaine', true);					// Focused
		//==================================================================

		//==================================================================
		// define column : Sub work
		//==================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_column("MODU.`libelle`",'sousdomaine','Sous domaine',__TEXT__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
		// define column : Module
		//==================================================================
		// COLUMN
		$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_column("MODU.`ID`",'module','Module',__TEXT__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
				
	//==================================================================
				
	//==================================================================
	// Define update / insert mode 
	//==================================================================

	//==================================================================
	// Column order : Define in ascending priority means first line defined will be first priority column to order by and so on...
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_order_column('module',__DESC__);
	//==================================================================

	//==================================================================
	// Table columns primary key
	// Caution : Can't change key column name from root query column name
	// It's not required to declare column key with define_column method
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_key(Array('module'));
	//==================================================================

	//==================================================================
	// Cyclic theme lines
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_line_theme("A5BEC2","0.7em","CCC9AD","0.7em","264A59","0.7em","264A59","0.7em","333","FFF");
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->define_line_theme("FFFFFF","0.7em","D0DCE0","0.7em","7292CE","0.7em","7292CE","0.7em","000","FFF");
	//==================================================================

	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->new_graphic_lisha();

	//==================================================================
	// Do not remove this bloc
	// Keep this bloc at the end
	//==================================================================
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->generate_public_header();
	$_SESSION[$ssid]['lisha']['lisha_process_list_id']->generate_header();
	//==================================================================