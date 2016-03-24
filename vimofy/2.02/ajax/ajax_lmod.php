<?php

	require 'header_ajax.php';
	
	switch($_POST['action'])
	{
		case 1:
			// Load LMOD vimofy
			echo $_SESSION['vimofy'][$ssid][$vimofy_id]->generate_lmod_content();
			break;

	}

?>