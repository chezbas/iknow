<?php
	//==================================================================
	// Extract lisha version from URL
	//==================================================================	
	preg_match_all("#[\\\\|/]([a-z0-9.]*)#i",$_SERVER["SCRIPT_NAME"],$w_output);
	$version_soft = $w_output[1][0];
	//==================================================================
?>