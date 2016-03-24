<?php 
	$vimofy_id = 'vimofy_iobjet_id';
	/**==================================================================
	* Vimofy include
	====================================================================*/   
	$ssid = $_POST['ssid'];
	$dir_obj = '../../../../../../vimofy/';
	/*===================================================================*/   
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	// Create a reference to the session

	$obj_vimofy_ID = &$_SESSION['vimofy'][$ssid][$vimofy_id];	/**==================================================================
	 * Define query
	 ====================================================================*/		
	if($_POST['p_type'] == '__IFICHE__')
	{
		$query = "SELECT 
					FIC.`id_fiche` `ID`,
					POLE.Libelle as pole_lib,
					FIC.`vers_goldstock` 'VP',
					CONCAT(METI.Libelle,' - ',MODUL.Libelle) as metmod,
					FIC.`titre` `Titre`,
					THEM.libelle Act,
					FIC.pers 'Modif'
					FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_max_fiches']['name']." FIC,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']."` THEM,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']."` POLE,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_metiers']['name']."` METI,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_modules']['name']."` MODUL,
					`".$_SESSION['iknow'][$ssid]['struct']['tb_libelles']['name']."` TEXTS
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
					AND TEXTS.version_active = '".$_SESSION['iknow']['version_soft']."'";
		
	}
	else
	{
		$query = "SELECT 
						r.ID,
						p.Libelle as pole_lib,
						r.VGS as VP,
						r.titre as Titre,
						t.libelle as Act,
						r.Last_update_user as Modif
						FROM ".$_SESSION['iknow'][$ssid]['struct']['tb_max_codes']['name']." r, ".$_SESSION['iknow'][$ssid]['struct']['tb_poles_themes']['name']." t,".$_SESSION['iknow'][$ssid]['struct']['tb_poles']['name']." p
						WHERE r.pole = t.ID_POLE
						AND r.Theme = t.ID
						AND t.ID_POLE = p.ID";
	}
	
	$obj_vimofy_ID->define_query($query);
	/*===================================================================*/	

	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$obj_vimofy_ID->define_mode(__LMOD__,__SIMPLE__);				
	$obj_vimofy_ID->define_size(775,'px',350,'px');											
	$obj_vimofy_ID->define_nb_line(50);													
	$obj_vimofy_ID->define_readonly(__R__);
	if($_POST['p_type'] == '__IFICHE__')
	{												
		$obj_vimofy_ID->define_theme('green');
	}
	else
	{
		$obj_vimofy_ID->define_theme('blue');
	}													
	$obj_vimofy_ID->define_title($_SESSION[$ssid]['message'][42]);						
	$obj_vimofy_ID->define_sep_col_row(true,false);
	$obj_vimofy_ID->define_navbar_txt_activate(false);		
	$obj_vimofy_ID->define_navbar_refresh_button_activate(false);	
	$obj_vimofy_ID->define_page_selection_display(false,true);
	$obj_vimofy_ID->define_title_display(false);
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining columns
	 ====================================================================*/	
		
		/**==================================================================
		 * ID
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_ID->define_column('ID',$_SESSION[$ssid]['message'][46],__TEXT__,__WRAP__,__LEFT__);
		/*===================================================================*/

		/**==================================================================
		 * Titre
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_ID->define_column('Titre',$_SESSION[$ssid]['message'][47],__BBCODE__,__WRAP__,__LEFT__);
		/*===================================================================*/	
		
		//==================================================================
		// Area
		//==================================================================
		$obj_vimofy_ID->define_column('pole_lib',$_SESSION[$ssid]['message']['iknow'][32],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
		
		/**==================================================================
		 * Version Pole
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_ID->define_column('VP',$_SESSION[$ssid]['message']['iknow'][33],__TEXT__,__WRAP__,__LEFT__);
		/*===================================================================*/	
		
		if($_POST['p_type'] == '__IFICHE__')
		{
			/**==================================================================
			 * Metier - Module
			 ====================================================================*/	
			// COLUMN
			$obj_vimofy_ID->define_column('metmod',$_SESSION[$ssid]['message'][208],__TEXT__,__WRAP__,__LEFT__);
			/*===================================================================*/	
		}
		
		//==================================================================
		// Activity
		//==================================================================
		$obj_vimofy_ID->define_column('Act',$_SESSION[$ssid]['message']['iknow'][51],__TEXT__,__WRAP__,__LEFT__);
		//==================================================================
	
		/**==================================================================
		 * Trigrmame
		 ====================================================================*/	
		// COLUMN
		$obj_vimofy_ID->define_column('Modif',$_SESSION[$ssid]['message'][429],__TEXT__,__WRAP__,__LEFT__);
		/*===================================================================*/			
		
	/*===================================================================*/	
	
	$obj_vimofy_ID->define_vimofy_action(__ON_LMOD_INSERT__,__AFTER__,'vimofy2_vers_pole_lmod',Array('load_vim_version('.$_POST['p_type'].')'));		
		
	/**==================================================================
	 * Defining LMOD options
	 ====================================================================*/
	$obj_vimofy_ID->define_col_return('ID');
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining order
	 ====================================================================*/
	$obj_vimofy_ID->define_order_column('ID',1,__DESC__);					
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining color mask
	 ====================================================================*/	
	$obj_vimofy_ID->define_color_mask("FFF2E6","D0DCE0","88b2dc","000","000");
	$obj_vimofy_ID->define_color_mask("EEEEEE","D0DCE0","e8b1b1","000","000");
	/*===================================================================*/	

?>