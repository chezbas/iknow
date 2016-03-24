<?php

	for ($index = 0; $index < 10; $index++) 
	{
		$handle = fopen('http://dtravel.iknow/ifiche.php?&ID=575&version=232&IKN_INT_TIR_PERF=1&ssid='.sha1(mt_rand().microtime()).mt_rand(), "r");
		stream_get_contents($handle);
		fclose($handle);
		sleep(1);
	}

		
?>