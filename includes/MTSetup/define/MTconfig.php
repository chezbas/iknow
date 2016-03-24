<?php
	//==================================================================
	// Define main root id of your tree ( same in css and html div identifier !! )
	// Internal id
	//==================================================================
	$mt_setup = 'iksetup';
	//==================================================================

	
	// Instance of tree
	$_SESSION[$ssid]['MT'][$mt_setup] = new itree(
						 $ssid,
						 $mt_setup,
						 __MAGICTREE_TABLE_SETUP__,
						 __MAGICTREE_TABLE_TEXT__,
						 __MAGICTREE_TABLE_LANGUAGE__,
						 __MAGICTREE_APPLICATION_RELEASE__
						 );

						 // Create a reference to the session

	// Build reference
	$obj_setup = &$_SESSION[$ssid]['MT'][$mt_setup];

	//==================================================================
	// Define custom tree table to draw
	//==================================================================
	$obj_setup->define_attribute('__node_table_name', 'ikn_conf');
	$obj_setup->define_attribute('__caption_table_name', 'ikn_conf_caption');
	$obj_setup->define_attribute('__extra_table_name', 'ikn_conf_feature');
	//==================================================================
	
	//==================================================================
	// BBCode display node title
	//==================================================================
	$obj_setup->define_attribute('__active_bbcode', true);
	//==================================================================

	//==================================================================
	// user and technical documentation
	//==================================================================
	$obj_setup->define_attribute('__active_user_doc', false);
	$obj_setup->define_attribute('__active_tech_doc', false);
	//==================================================================
	
	
	//==================================================================
	// Define language caption
	//==================================================================
	$obj_setup->define_attribute('__language_id', $_SESSION[$ssid]['langue']);
	//==================================================================

	//==================================================================
	// __automatic_flag_manage : true or false
	// false means you have to manage properly flag in tree
	// true means flag manage in tree
	//==================================================================
	$obj_setup->define_attribute('__automatic_flag_manage', true);
	//==================================================================

	//==================================================================
	// Edit mode
	// true means you can change tree items
	// false means only display
	//==================================================================
	$obj_setup->define_attribute('__active_edit', $_SESSION[$ssid]['MT']['tree']['id']['iksetup']["edit_mode"]);
	//==================================================================
	
	//==================================================================
	// Custom event function OnCickItem
	// Define name of customer javascript
	// 3 input parameters available
	// internal_id 	: Id of tree
	// row_id		: Id of clicked node
	// mode			: U means that tree is in update mode
	// Syntaxe to define your javascript custom function
	// function my_custom_function(node_id, row_id, mode ) {....}
	//==================================================================
	$obj_setup->define_attribute('__on_click_item', 'read_details');
	//==================================================================

	//==================================================================
	// Tree theme to use
	// Have to be defined in theme/
	//==================================================================
	$obj_setup->define_attribute('__theme', 'TreeView_default');
	//==================================================================
	
	
	//==================================================================
	// Input Id item to focus ( null means no focus )
	//==================================================================
	if(!isset($_SESSION[$ssid]['current_read_page']))
	{
		$_SESSION[$ssid]['current_read_page'] = 48;
	}
	$obj_setup->define_attribute('__focus_id', $_SESSION[$ssid]['current_read_page']);
	//==================================================================
	
	//==================================================================
	// Enable or disable link to tickets list
	// true means ticket list is available by link
	//==================================================================
	$obj_setup->define_attribute('__active_ticket_link', false);
	//==================================================================

	//==================================================================
	// tab index prefixe : Mandatory for focus : Be larg to avoid issue
	//==================================================================
	$obj_setup->define_attribute('__html_base_index_focus', 100);
	//==================================================================
		
	//==================================================================
	// Define input search caption
	//==================================================================
	$obj_setup->define_attribute('__caption_search', '');
	//==================================================================
	
	//==================================================================
	// Display or hide checkbox
	// true means display
	// false means hide
	//==================================================================
	$obj_setup->define_attribute('__display_check_box', false);
	//==================================================================

	//==================================================================
	// Define list of items to check
	// Only visible if option __display_check_box is set to true
	// Only readed if mark_inside_tree = false
	//==================================================================
	//$obj_setup->define_attribute('__list_checkbox_to_mark_id', Array("2" => true,"5" => true));
	$obj_setup->define_attribute('__list_checkbox_to_mark_id', Array());
	//==================================================================

	//==================================================================
	// Define list of items to expand to root
	// if __active_expand_all is set to 1, all items are expanded
	//==================================================================
	$obj_setup->define_attribute('__list_expand_root_id',$obj_setup->read_attribute('__list_checkbox_to_mark_id'));
	//==================================================================

	//==================================================================
	// Expand all nodes
	// true means all nodes are expanded
	// false means all nodes are collapsed
	//==================================================================
	$obj_setup->define_attribute('__active_expand_all',true);
	//==================================================================
?>