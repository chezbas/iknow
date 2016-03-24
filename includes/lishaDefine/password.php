<?php 
	$lisha_id = 'lisha_password';

	// Use framework connexion information from framework
	$_SESSION[$ssid]['lisha'][$lisha_id] = new lisha(
														$lisha_id,
														$ssid,
														__MYSQL__,
														array('user' => $_SESSION['iknow'][$ssid]['acces_user_iknow'],'password' => $_SESSION['iknow'][$ssid]['acces_password_iknow'],'host' => $_SESSION['iknow'][$ssid]['acces_serveur_bdd'],'schema' => $_SESSION['iknow'][$ssid]['acces_schema_iknow']),
														$path_root_lisha,
														false);	// Type of internal lisha ( false by default )

	// Create a reference to the session
	$obj_lisha_password = &$_SESSION[$ssid]['lisha'][$lisha_id];


	//==================================================================
	// Define main query
	//==================================================================

	// Get ID directive
	// Use Key ID to focus on require row identification
	if(isset($_GET['ID']))
	{
		$str_id = 'AND `'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`id` = "'.$_GET['ID'].'"';
	}
	else
	{
		$str_id = '';
	}

	$query = "
			SELECT
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`id`			AS `id`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`object`		AS `object`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`user`			AS `user`,
				AES_DECRYPT(`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`password`,'FhX*24é\"3_--é0Fz.')	AS `password`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`comment`			AS `comment`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`level`				AS `level`,
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`theme`				AS `theme`
				-- `demo_table`.`status`			AS `MyGroupTheme`
			".$_SESSION[$ssid]['lisha']['configuration'][10]."
				`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`
				WHERE 1 = 1
				AND `".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`level` <= ".$_SESSION['iknow'][$ssid]['identified_level']."
				$str_id"
				;
	$obj_lisha_password->define_attribute('__main_query', $query);
	//==================================================================

	//==================================================================
	// Lisha display setup
	//==================================================================
	$obj_lisha_password->define_nb_line(20);											// Row by page								
	$obj_lisha_password->define_size(100,'%',100,'%');									// Size of object
	$obj_lisha_password->define_attribute('__active_readonly_mode', __RW__);			// Read & Write	
	$obj_lisha_password->define_attribute('__id_theme','grey');							// Define style	

	$obj_lisha_password->define_attribute('__active_title', false);						// Title bar

	$obj_lisha_password->define_attribute('__max_lines_by_page', 80);					// Limit rows by page

	$obj_lisha_password->define_attribute('__active_column_separation',false);
	$obj_lisha_password->define_attribute('__active_row_separation',false);

	$obj_lisha_password->define_attribute('__active_top_bar_page',false);
	$obj_lisha_password->define_attribute('__active_bottom_bar_page',true);

	$obj_lisha_password->define_attribute('__active_user_doc', false);					// user documentation button
	$obj_lisha_password->define_attribute('__active_tech_doc', false);					// technical documentation button
	$obj_lisha_password->define_attribute('__active_ticket', false);						// Tickets link

	$obj_lisha_password->define_attribute('__background_picture', 'images/iknow.png');	// Define background logo

	$obj_lisha_password->define_attribute('__display_mode', __NMOD__);					// Display mode

	$obj_lisha_password->define_attribute('__key_url_custom_view', 'f1');				// Defined key for quick custom view loader in url browser

	$obj_lisha_password->define_attribute('__update_table_name', $_SESSION['iknow'][$ssid]['struct']['tb_password']['name']);		// Define table to update

	$obj_lisha_password->define_attribute('__active_user_cells_update', false);			// User cell update ( Optional : default true )

	//$obj_lisha_password->define_attribute('__column_name_group_of_color', "MyGroupTheme");		// ( Optional ) Define csutom column color name
	//==================================================================

	//==================================================================
	// define columns
	//==================================================================

		//==================================================================
		// define column : id
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`id`','id','Identification',__TEXT__,__WRAP__,__CENTER__,__PERCENT__,__DISPLAY__);
		$obj_lisha_password->define_attribute('__column_input_check_update', __FORBIDDEN__,'id');
		//==================================================================


		//==================================================================
		// define column : Object
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`object`','object','Objet',__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_password->define_attribute('__column_input_check_update', __REQUIRED__,'object');
		//==================================================================

		//==================================================================
		// define column : user
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`user`','user','Utilisateur',__TEXT__,__WRAP__,__LEFT__);
		//$obj_lisha_password->define_attribute('__column_input_check_update', __REQUIRED__,'user');
		$obj_lisha_password->define_input_focus('user', true);					// Focused
		//==================================================================

		//==================================================================
		// define column : password
		//==================================================================
		$obj_lisha_password->define_column("AES_DECRYPT(`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`password`,'FhX*24é\"3_--é0Fz.')",'password','mot de passe',__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_password->define_col_rw_function('password',"AES_ENCRYPT('__COL_VALUE__','FhX*24é\"3_--é0Fz.')");
		$obj_lisha_password->define_col_select_function('password',"AES_DECRYPT(`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`.`password`,'FhX*24é\"3_--é0Fz.')");
		//$obj_lisha_password->define_attribute('__column_display_mode',false,'amount');
		//==================================================================

		//==================================================================
		// define column : comment
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`comment`','comment','Commentaire',__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================

		//==================================================================
		// define column : Level
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`level`','level','Niveau',__BBCODE__,__WRAP__,__LEFT__);
		$obj_lisha_password->define_attribute('__column_input_check_update', __LISTED__,'level');

		// Match code

		// Build level lov list
		$i = $_SESSION['iknow'][$ssid]['identified_level'];
		$temp_query = '';
		while($i > 0)
		{
			$temp_query .= 'SELECT \''.$i.'\' AS `niv`';
			$i = $i -1;
			if($i != 0)
			{
				$temp_query .= ' UNION ';
			}
		}

		$obj_lisha_password->define_lov("	SELECT
											`main`.`niv` AS `level`
											".$_SESSION[$ssid]['lisha']['configuration'][10]."
											(
											".$temp_query."
											) `main`
											WHERE 1 = 1
											",
			$_SESSION[$ssid]['message']['iknow']['37'],
			'`main`.`niv`',
			'level'
		);
		$obj_lisha_password->define_column_lov("`main`.`niv`",'level','Niveau',__TEXT__,__WRAP__,__LEFT__);
		$obj_lisha_password->define_column_lov_order('level',__ASC__);
		//==================================================================

		//==================================================================
		// define column : theme
		//==================================================================
		$obj_lisha_password->define_column('`'.$_SESSION['iknow'][$ssid]['struct']['tb_password']['name'].'`.`theme`','theme','Groupe',__TEXT__,__WRAP__,__CENTER__);
		//==================================================================

		//==================================================================
		// define column : SetOfColor
		//==================================================================
		/*$obj_lisha_password->define_column('`demo_table`.`status`','MyGroupTheme','MyGroupTheme',__INT__,__WRAP__,__CENTER__);
		$obj_lisha_password->define_attribute('__column_display_mode',false,'MyGroupTheme');
		$obj_lisha_password->define_attribute('__column_input_check_update', __FORBIDDEN__,'MyGroupTheme');
		*/
		//==================================================================

	//==================================================================



	// Table columns primary key
	// Caution : Can't change key column name from origine query column name
	// It's not required to declare column key with define_column method
	$obj_lisha_password->define_key(Array('id','object','user'));

	//==================================================================
	// Define extra events actions 
	//==================================================================
	//$obj_lisha_password->define_lisha_action(__ON_ADD__,__AFTER__,'lisha_transaction',Array('rebuild_account();'));
	//==================================================================

	//==================================================================
	// Column order : Define in ascending priority means first line defined will be first priority column to order by and so on...
	//==================================================================
	$obj_lisha_password->define_order_column('id',__DESC__);
	//$obj_lisha_password->define_order_column('description',__DESC__);
	//$obj_lisha_password->define_order_column('amount',__ASC__);
	//==================================================================

	//==================================================================
	// Line theme mask
	//==================================================================
	// Default group
	$obj_lisha_password->define_line_theme("EEEEEE","0.8em","EECCCC","0.8em","AA8888","0.8em","BB7878","0.8em","555","000");
	$obj_lisha_password->define_line_theme("FFFFFF","0.8em","FFCDCD","0.8em","EECCCC","0.8em","DDC8C8","0.8em","000","000");

	/*
	// Group 2
	$obj_lisha_password->define_line_theme("DDEEDD","0.7em","CCEECC","0.7em","68B7E0","0.7em","68B7E0","0.7em","000","000",1);
	$obj_lisha_password->define_line_theme("EEFFEE","0.7em","D0E0DC","0.7em","AEE068","0.7em","AEE068","0.7em","000","000",1);

	// Group 3
	$obj_lisha_password->define_line_theme("DDDDEE","0.7em","CCCCEE","0.7em","68B7E0","0.7em","68B7E0","0.7em","028","000",2);
	$obj_lisha_password->define_line_theme("EEEEFF","0.7em","D0DCE0","0.7em","AEE068","0.7em","AEE068","0.7em","006","000",2);
	//==================================================================			
	*/

	//==================================================================
	// Do not remove this bloc
	// Keep this bloc at the end
	//==================================================================
	$obj_lisha_password->generate_public_header();   
	$obj_lisha_password->generate_header();
	//==================================================================