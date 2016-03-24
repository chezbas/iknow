<?php
	require 'header_ajax.php';
	$vimofy_type = $_POST['vimofy_type'];
	
	switch($_POST['vimofy_type'])
	{
		case __ADV_FILTER__:
			// Advanced filter on a colmun
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_internal_adv_filter();
			break;
		case __POSSIBLE_VALUES__:
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_lov($_POST['column']);
			break;
		case __LOAD_FILTER__:
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_load_filter_lov();
			break;
		case __HIDE_DISPLAY_COLUMN__:
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->vimofy_hide_display_col_lov($_POST['column']);
			break;
	}

?>