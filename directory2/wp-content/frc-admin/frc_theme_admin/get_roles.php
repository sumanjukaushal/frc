<?php
require_once("../../../wp-config.php");
require_once('../../../wp-load.php');
$contentURL = str_replace('directory', 'cron',WP_CONTENT_URL);
if(array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])){
    $user_id = $_REQUEST['user_id'];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "$contentURL/curl/curl.php?user_id=$user_id");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    print $result;
}
?>  