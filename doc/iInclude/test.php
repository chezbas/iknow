<?php
ob_start();
phpinfo();
//echo phpversion();
//echo apache_get_version();
$config = ob_get_contents();

ob_end_clean();



$analyse = strstr($config,'<td class="e">Apache Version </td>');
$analyse = strstr($analyse,'ache/');
$long = strpos($analyse,'(');
$apacheversion = substr($analyse,5,$long-6);


$analyse = strstr($analyse,'<td class="e">PHP Version </td>');
$analyse = strstr($analyse,'</td>');
$analyse = strstr($analyse,'"');
$analyse = strstr($analyse,'>');

$long = strpos($analyse,'<');
$phpversion = substr($analyse,1,$long-2);


$analyse = strstr($analyse,'MySQL Support');
$analyse = strstr($analyse,'API version');
$analyse = strstr($analyse,'">');
$long = strpos($analyse,'<');
$mysqlversion = substr($analyse,2,$long-3);


$analyse = strstr($config,'REMOTE_ADDR');
$analyse = strstr($analyse,'">');

$long = strpos($analyse,'<');
$ipclient = substr($analyse,2,$long-3);





?>
<textarea rows="50" cols="50"><?php echo $ipclient; ?></textarea>