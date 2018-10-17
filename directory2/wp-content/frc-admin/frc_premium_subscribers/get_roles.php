<?php
if(array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])){
    $user_id = $_REQUEST['user_id'];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://www.freerangecamping.co.nz/cron/wp-content/curl/curl.php?user_id=$user_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    print $result;
}
?>  