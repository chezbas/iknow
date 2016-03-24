<?php

	require('class_ws_icode.php');
	$server = new SoapServer(null, array('uri' => 'http://iknow.dtravel/web_service/icode_service.php'));
		
	$server->setClass("ws_icode");
	$server->handle();
	//error_log(print_r($server->getFunctions(),1));