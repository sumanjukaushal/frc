<?php
//URL: http://freerangecamping.co.nz/directory/wp-content/wlm_sync/script_execution_time.php
echo $memLt = ini_get('memory_limit');
ini_set('memory_limit','2048M');
echo $memLt = ini_get('memory_limit');
$max_time = ini_get("max_execution_time");
echo $max_time;
ini_set('max_execution_time', 7200);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
echo "<br/>".$max_time = ini_get("max_execution_time");
?>