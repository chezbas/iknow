<?php 
	
	$_SESSION['vimofy'][$ssid]['vimofy'] = new vimofy('vimofy',$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	
	$vimofy_child = &$_SESSION['vimofy'][$ssid]['vimofy'];
	
	if(isset($_SESSION['coherence_check']))
	{
		$sql = $_SESSION['coherence_check']->get_query_child();
	}
	else
	{
		$sql = 'SELECT * FROM (
					SELECT 	"" as id_fiche,
							"" as num_version,
							"" as id_etape,
							"" as CONTROL_STATUS,
							"" as date,""
							as description,""
							as status,""
							as ICON,""
							as MESSAGE_CONTROL,
							"" as LINK
							) t 
							WHERE 1 = 2
							';
	}
	
	$vimofy_child->define_query($sql);
	
	/*===================================================================*/	
	
	/**==================================================================
	 * Vimofy visual render
	 ====================================================================*/	
	$vimofy_child->define_size(100,'%',100,'%');											// width 700px, height 500px
	$vimofy_child->define_nb_line(50);														// 20 lines per page
	$vimofy_child->define_readonly(__R__);													// Read & Write
	if(isset($_GET['iobject']))
	{
		switch ($_GET['iobject']) {
			case '__ICODE__':
				$vimofy_child->define_theme('blue');	
				break;
			case '__IFICHE__':
				$vimofy_child->define_theme('green');	
				break;
		}	
	}
	else
	{
		$vimofy_child->define_theme('green');													// Define default style
	}
	$vimofy_child->define_background_logo('../../images/iknow.png');						// Define background logo
	$vimofy_child->define_sep_col_row(true,false);												
	$vimofy_child->define_page_selection_display(false,true);
	$vimofy_child->define_title_display(false);
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining columns
	 ====================================================================*/	
	
		/**==================================================================
		 * Sheet ID
		 ====================================================================*/	
		$vimofy_child->define_column('id_fiche',$_SESSION[$ssid]['message'][526],__TEXT__,__WRAP__,__CENTER__,__EXACT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Id of the step
		 ====================================================================*/	
		$vimofy_child->define_column('id_etape',$_SESSION[$ssid]['message'][527],__TEXT__,__WRAP__,__CENTER__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Control status : ICON
		 ====================================================================*/	
		$vimofy_child->define_column('ICON',$_SESSION[$ssid]['message'][528],__TEXT__,__WRAP__,__CENTER__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Message of the control
		 ====================================================================*/	
		$vimofy_child->define_column('MESSAGE_CONTROL',$_SESSION[$ssid]['message'][529],__TEXT__,__WRAP__,__LEFT__);						
		/*===================================================================*/	
		
		/**==================================================================
		 * Link
		 ====================================================================*/	
		$vimofy_child->define_column('LINK',$_SESSION[$ssid]['message'][530],__TEXT__,__WRAP__,__CENTER__,__EXACT__);						
		/*===================================================================*/	
		
	/*===================================================================*/	
		
	// Table key
	$vimofy_child->define_key(Array('id_fiche','id_etape'));
	
	$vimofy_child->define_vimofy_action(__ON_REFRESH__,__BEFORE__,'vimofy',Array('vimofy_refreshed = false;'));		
	$vimofy_child->define_vimofy_action(__ON_REFRESH__,__AFTER__,'vimofy',Array('vimofy_refreshed = true;'));		
	
	/**==================================================================
	 * Defining order
	 ====================================================================*/
	$vimofy_child->define_order_column('id_fiche',1,__ASC__);					// Define column "version"
	$vimofy_child->define_order_column('id_etape',2,__ASC__);					// Define column "version"
	/*===================================================================*/	
	
	/**==================================================================
	 * Defining color mask
	 ====================================================================*/	
	$vimofy_child->define_color_mask("FFF2E6","D0DCE0","c6f3fa","000","000");
	$vimofy_child->define_color_mask("EEEEEE","D0DCE0","c7fac6","000","000");
	/*===================================================================*/	
	
	$vimofy_child->new_graphic_vimofy();	
?>