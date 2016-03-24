<?php

	$dir_obj = '../../vimofy/';

	$ssid = $_POST['ssid'];
	session_name($ssid);
	session_start();
	require('init_lst_search.php');
	
	$style = $obj_vimofy_search->vimofy_generate_header(true);

	$vim = $obj_vimofy_search->generate_vimofy();
	$js = $obj_vimofy_search->vimofy_generate_js_body(true);
	
	header("Content-type: text/xml");
	echo "<?xml version='1.0' encoding='UTF8'?>";
	echo "<parent>";
	echo "<vimofy>".rawurlencode($vim)."</vimofy>";
	echo "<json>".rawurlencode($js)."</json>";
	echo "<css>".rawurlencode($style)."</css>";
	echo "</parent>";
	
?>