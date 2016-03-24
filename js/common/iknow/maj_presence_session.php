<?php
	/**==================================================================
	 * Page buffering ( !! No output ( echo, print_r etc..) before this include !! )
	 ====================================================================*/
	require('../../../includes/common/buffering.php');
	/*===================================================================*/	

	$date_time  = '<date>'.date('m/d/Y').'</date>';
	$date_time .= '<time>'.date('H:i:s').'</time>';
	
	header("Content-type: text/xml");
	echo "<?xml version='1.0' encoding='UTF8'?>";
	echo "<parent>";
	echo $date_time;
	echo "</parent>";
?>