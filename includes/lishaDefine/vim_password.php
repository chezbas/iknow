<?php 
	$_GET['lng'] = $_SESSION[$ssid]['langue'];
	$_SESSION['vimofy'][$ssid]['vimofy_password'] = new vimofy('vimofy_password',$ssid,__MYSQL__,array('user' => 'devil','password' => 'maycry4','host' => 'localhost','schema' => 'acces'),$dir_obj);
	
	if(isset($_GET['ID']))
	{
		$str_id = 'AND id = "'.$_GET['ID'].'"';
	}
	else
	{
		$str_id = '';
	}
	$query = "	SELECT
					`id`, 
					`object`,
					`user`,
					DECODE(`password`,'gfqhz__234j&-bhjq') password,
					`comment`,
					`level`,
					`theme`
					FROM
						`".$_SESSION['iknow'][$ssid]['struct']['tb_password']['name']."`
					WHERE 1 = 1
					".$str_id."
					AND `level` <= ".$_SESSION['iknow'][$ssid]['identified_level'];
	
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_query($query);
	/*===================================================================*/	
	
	//==================================================================
	// Lisha display setup
	//==================================================================
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_size(100,'%',100,'%');											// width 700px, height 500px
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_nb_line(50);														// 20 lines per page
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_readonly(__RW__);													// Read & Write
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_theme('blue');													// Define default style
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_background_logo('images/iknow.png');			// Define background logo
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_sep_col_row(true,false);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_page_selection_display(false,true);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_title_display(false);

	//==================================================================
		
	//==================================================================
	// define output columns
	//==================================================================
		
		//==================================================================
		// define column : id
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('id',$_SESSION[$ssid]['message'][496],__BBCODE__,__WRAP__,__CENTER__,__EXACT__);						
		//==================================================================
					
		//==================================================================
		// define column : object
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('object',$_SESSION[$ssid]['message'][495],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
				
		//==================================================================
		// define column : user
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('user',$_SESSION[$ssid]['message'][497],__BBCODE__,__WRAP__,__CENTER__);						
		//==================================================================
				
		//==================================================================
		// define column : password
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('password',$_SESSION[$ssid]['message'][498],__BBCODE__,__WRAP__,__CENTER__);
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_col_select_function('password','DECODE(password,"gfqhz__234j&-bhjq")');
		//==================================================================
				
		//==================================================================
		// define column : comment
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('comment',$_SESSION[$ssid]['message'][499],__BBCODE__,__WRAP__,__LEFT__);
		//==================================================================
				
		//==================================================================
		// define column : theme
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('theme',$_SESSION[$ssid]['message'][500],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
				
		//==================================================================
		// define column : Identification level
		//==================================================================
		$_SESSION['vimofy'][$ssid]['vimofy_password']->define_column('level',$_SESSION[$ssid]['message'][501],__BBCODE__,__WRAP__,__CENTER__);
		//==================================================================
		
	//==================================================================
		
	//==================================================================
	// UPDATE/INSERT setup
	//==================================================================
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_update_table($_SESSION['iknow'][$ssid]['struct']['tb_password']['name']);
	
	// Columns attribut
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('id',__FORBIDEN__);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('object',__REQUIRED__);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('user',__REQUIRED__);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('password',__REQUIRED__);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('theme',__REQUIRED__);
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_rw_flag_column('level',__REQUIRED__);
	
	// Columns update function
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_col_rw_function('password','ENCODE("__COL_VALUE__","gfqhz__234j&-bhjq")');
	
	// Table key
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_key(Array('id','object','user'));
	//==================================================================
	
	//==================================================================
	// Define sort order
	//==================================================================
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_order_column('id',1,__DESC__);					// Define column "version"
	//==================================================================
		
	//==================================================================
	// Define row color template
	//==================================================================
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_color_mask("FFF2E6","D0DCE0","c6f3fa","000","000");
	$_SESSION['vimofy'][$ssid]['vimofy_password']->define_color_mask("EEEEEE","D0DCE0","c7fac6","000","000");
	//==================================================================
		
	$_SESSION['vimofy'][$ssid]['vimofy_password']->new_graphic_vimofy();
?>