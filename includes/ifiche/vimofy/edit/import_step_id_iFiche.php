<?php 
/**==================================================================
 * __FILE_COMPRESSOR_DIRECTIVE_ON__
 * Lisha iSheet import available ( choose id iSheet 1/3 )
 ====================================================================*/


	/**==================================================================
	 * Load common function
	 ====================================================================*/
	require('../../includes/common/global_functions.php');
	/*===================================================================*/	

	
	$vimofy_id = 'imp_step_ID';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_imp_step_id = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "SELECT  
				FIC.`id_fiche` AS 'ID',
				POLE.`Libelle` AS ' pole_lib' ,
				FIC.`vers_goldstock` 'Version GOLD',
				CONCAT(METI.`Libelle`,' - ',MODUL.`Libelle`),
				FIC.`titre` AS 'Titre',
				THEM.`libelle` AS 'Act',
				FIC.`pers` 'Modif'
			FROM
				`".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']."` FIC,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` THEM,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` POLE,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name'] ."` METI,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name'] ."` MODUL,
				".$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name']." TEXTS
			WHERE 1 = 1
				AND METI.id_POLE = POLE.ID 
				AND MODUL.ID = FIC.id_module
				AND MODUL.id_POLE = POLE.ID
				AND MODUL.ID_METIER = METI.ID
				AND THEM.ID = FIC.Theme
				AND FIC.id_POLE = POLE.ID
				AND FIC.id_statut = TEXTS.id_texte
				AND TEXTS.type = 'statut'
				AND TEXTS.`id_lang` = '".$_SESSION[$ssid]['langue']."'
				AND THEM.ID_POLE = FIC.id_POLE
				AND TEXTS.`objet` = 'ifiche'
				AND TEXTS.version_active = '".$_SESSION['iknow']['version_soft']."'
			";
	
	$obj_vimofy_imp_step_id->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_imp_step_id->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_imp_step_id->define_size(500,'px',300,'px');											
	$obj_vimofy_imp_step_id->define_nb_line(50);													
	$obj_vimofy_imp_step_id->define_readonly(__R__);												
	$obj_vimofy_imp_step_id->define_theme('green');													
	$obj_vimofy_imp_step_id->define_title(mysql_protect($_SESSION[$ssid]['message'][56]));						
	$obj_vimofy_imp_step_id->define_sep_col_row(true,false);
	$obj_vimofy_imp_step_id->define_navbar_txt_activate(false);		
	$obj_vimofy_imp_step_id->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_imp_step_id->define_page_selection_display(false,true);
	$obj_vimofy_imp_step_id->define_c_position_mode(__ABSOLUTE__);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : iSheet ID
		//==================================================================
		$obj_vimofy_imp_step_id->define_column('ID',mysql_protect($_SESSION[$ssid]['message'][46]),__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : iSheet title
		//==================================================================
		$obj_vimofy_imp_step_id->define_column('Titre',mysql_protect($_SESSION[$ssid]['message'][47]),__BBCODE__,__WRAP__,__LEFT__);						
		//==================================================================
				
	//==================================================================
		
	//==================================================================
	// Define extra events actions
	//==================================================================
	$obj_vimofy_imp_step_id->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'imp_step_Version',Array('load_vimofy_alias_version('.$etape.');'));		
	//==================================================================
	
	//==================================================================
	// Define column to return
	//==================================================================
	$obj_vimofy_imp_step_id->define_col_return('ID');
	//==================================================================
		
	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_imp_step_id->define_order_column('ID',1,__ASC__);					
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_imp_step_id->define_color_mask("AADDAA","77AA77","ccc267","000","FFF");
	$obj_vimofy_imp_step_id->define_color_mask("CCFFCC","BBFFBB","aaeF98","000","DDD");
	//==================================================================
?>