<?php
ob_start();
$gc_lifetime = ini_get('session.gc_maxlifetime'); 
echo '<b style="color:red;">'.date('H:i:s',$gc_lifetime).'</b>';	
?>