<?php 
	$vimofy_id = 'vimofy2_varout';
	
	$_SESSION['vimofy'][$ssid][$vimofy_id] = new vimofy($vimofy_id,$ssid,__MYSQL__,array('user' => $_SESSION['iknow'][$ssid]['user_iknow'],'password' => $_SESSION['iknow'][$ssid]['password_iknow'],'host' => $_SESSION['iknow'][$ssid]['serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['schema_iknow']),$dir_obj);
	
	// Create a reference to the session
	$obj_vimofy_varout = &$_SESSION['vimofy'][$ssid][$vimofy_id];
	
	//==================================================================
	// Define main query
	//==================================================================
	$query = "	SELECT
					CONCAT('[b][color=#0000DD]',`NOM`,'[/color][/b]') as NOM,
					`DESCRIPTION` as 'DESCRIPTION',
					`COMMENTAIRE`,
					`IDP`,
					`ID`,
					`Version`,
					`TYPE` as 'TYPE' 
			  	FROM
			  		`".$_SESSION['iknow'][$ssid]['struct']['tb_codes_param']['name']."` 
			  	WHERE 1 = 1
			  		AND `ID` = ".$_SESSION[$ssid]['objet_icode']->get_id_temp()." 
			  		AND `Version` = ".$_SESSION[$ssid]['objet_icode']->get_version()." 
			  		AND `TYPE` = 'OUT'
			";
		
	$obj_vimofy_varout->define_query($query);
	//==================================================================
		
	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_vimofy_varout->define_size(100,'%',100,'%');											
	$obj_vimofy_varout->define_nb_line(50);													
	$obj_vimofy_varout->define_readonly(__R__);											// Read & Write
	$obj_vimofy_varout->define_theme('blue');											// Define default style
	$obj_vimofy_varout->define_background_logo('images/back_varout.png','repeat');		// Define background logo
	$obj_vimofy_varout->define_sep_col_row(true,false);
	$obj_vimofy_varout->define_title_display(false);
	$obj_vimofy_varout->define_page_selection_display(false,true);
	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : name
		//==================================================================
		$obj_vimofy_varout->define_column('NOM',$_SESSION[$ssid]['message'][92],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
		
		//==================================================================
		// define column : description
		//==================================================================
		$obj_vimofy_varout->define_column('DESCRIPTION',$_SESSION[$ssid]['message'][47],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================
			
		//==================================================================
		// define column : comment
		//==================================================================
		$obj_vimofy_varout->define_column('COMMENTAIRE',$_SESSION[$ssid]['message'][96],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
		
	//==================================================================
		
	//==================================================================
	// Define update / insert mode 
	//==================================================================
	$obj_vimofy_varout->define_key(Array('IDP','ID','Version','TYPE'));
	//==================================================================

	//==================================================================
	// Define sort order
	//==================================================================
	$obj_vimofy_varout->define_order_column('NOM',1,__DESC__);					
	//==================================================================
	
	//==================================================================
	// Define row color template
	//==================================================================
	$obj_vimofy_varout->define_color_mask("DDDDEE","9999CC","99c3ed","000","FFF");
	$obj_vimofy_varout->define_color_mask("EEEEEE","AAAAAA","6690b9","000","DDD");
	//==================================================================
?>