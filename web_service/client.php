<?php
	
	if(isset($_GET['ID']) && is_numeric($_GET['ID']))
	{
		$soap_options = array
						(
							'location' => 'http://iknow.dtravel/web_service/icode_service.php',
							'uri' => 'http://iknow.dtravel/web_service/icode_service.php',
						);
		
		$clientSOAP = new SoapClient(null,$soap_options);

		//$ret = $clientSOAP->__soapCall('get_icode_content',array($_GET['ID'],null,null));
		$ret = $clientSOAP->__soapCall('iknow_version',array(null));

		header('Content-type: text/html; charset=UTF-8');
		
		echo $ret;
	}
	else
	{
		echo 'Error : ID is mandatory ';
	}