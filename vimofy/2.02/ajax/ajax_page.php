<?php

	require 'header_ajax.php';

	switch($_POST['action'])
	{
		case 1:
			// Change page
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_page_change_ajax($_POST['type'],$_POST['selected_lines']);		
			break;
		case 2:
			// Refresh page
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->refresh_page($_POST['selected_lines']);											
			break;
		case 3:
			// Move column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->move_column($_POST['c_src'],$_POST['c_dst'],$_POST['selected_lines']);					
			break;
		case 4:
			// Change order
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->change_order($_POST['column'],$_POST['order'],$_POST['mode'],$_POST['selected_lines']); 				
			break;		
		case 5:
			// Search column onkeyup
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_input_search_onkeyup($_POST['column'],$_POST['txt'],$_POST['selected_lines']);				
			break;
		case 6:
			// Define a filter on a column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->define_filter($_POST);																		
			break;
		case 7:
			// Save a new vimofy filter
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->save_filter($_POST['name']);																
			break;
		case 8:
			// Change the search mode on a column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->change_search_mode($_POST['column'],$_POST['type_search'],$_POST['selected_lines']);		
			break;
		case 9:
			// Reset the filter on all column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->reset_vimofy();							
			break;
		case 10:
			// Hide or display a column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->toggle_column($_POST['column'],$_POST['selected_lines']);																
			break;
		case 11:
			// Change the number of line per page
			$_SESSION['vimofy'][$ssid][$vimofy_id]->change_nb_line($_POST['qtt'],$_POST['selected_lines']);
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->generate_page(true);
			break;
		case 12:
			// Change the alignment mode on a column
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->change_alignment($_POST['column'],$_POST['type_alignment'],$_POST['selected_lines']);
			break;
		case 13:
			// Edit lines
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->edit_lines($_POST['lines']);
			break;
		case 14:
			// Save lines
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->save_lines($_POST['val_json']);
			break;
		case 15:
			// Delete lines
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->delete_lines($_POST['lines']);
			break;
		case 16:
			// Add lines
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->add_line($_POST['val_json']);
			break;
		case 17:
			// Load a filter
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_load_filter($_POST['filter_name']);
			break;
		case 18:
			// Generate a calendar
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_generate_calendar($_POST['column']);
			break;
		case 19:
			// Generate a calendar
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_generate_calendar($_POST['column'],$_POST['year'],$_POST['month'],$_POST['day']);
			break;
		case 20:
			// cancel edition
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_cancel_edit();
			break;
	}

?>