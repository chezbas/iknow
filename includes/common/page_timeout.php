<?php
/**==================================================================
 * Setup page timeout from main configuration 
 * Protect timeout if value is too short or not numeric
 ====================================================================*/	

	if($_SESSION[$ssid]['configuration'][41] < 3600)
	{
		ini_set('session.gc_maxlifetime',3600);
	}
	else
	{
		ini_set('session.gc_maxlifetime',$_SESSION[$ssid]['configuration'][41]);
	}
?>