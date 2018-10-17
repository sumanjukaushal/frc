<?php
    define('DB_NAME', 'freerang_directory');
    define('DB_USER', 'freerang_rob');
    define('DB_PASSWORD', 'nandini123');
    define('DB_HOST', 'localhost');
    define('DB_CHARSET', 'utf8'); 
    
    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');
    define( 'WP_MEMORY_LIMIT', '512M' );

    $dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    $selected = mysql_select_db(DB_NAME,$dbhandle);
    require_once('../directory/wp-config.php');
    require_once('../directory/wp-load.php');
	require_once('../directory/wp-blog-header.php');
    $truncateRS = mysql_query('TRUNCATE TABLE `ait_items`');
    $result = mysql_query("SELECT * FROM `gwr_postmeta` WHERE `meta_key`='_ait-dir-item'");
    $unserlized = array();
    $counter = 0;
    //echo "<pre>";
    while ($row = mysql_fetch_assoc($result)) {
        $metaArr = array();
        $metaValue = $row['meta_value'];
        $postID = $row['post_id'];
        $tempArr = $metaArr = unserialize($metaValue);
        $postQuery = "SELECT `post_status`, `id` ,`post_title` FROM `gwr_posts` WHERE `ID` = $postID AND `post_status` = 'publish'";
        $postRS = mysql_query($postQuery) or die(mysql_error());
        $postObj = mysql_fetch_assoc($postRS);
        
        $metaArr['post_title'] = $postObj['post_title'];
        $postIDArr = array(19917, 19356,19355);
        if(in_array($postID,$postIDArr) ){
            //echo "---------$postID--------------";
            //print_r($metaArr);
            //echo "---------*******--------------";
        }
        //$location = get_post_meta( $post->ID, '_ait-dir-item', true );
        //Reference: https://wedevs.com/support/topic/need-help-with-wp_postmeta-fields/
        if(is_array($postObj) && count($postObj) > 0){
            $unserlized[$postID] = $metaArr;
        }
    }
    $select = mysql_query("SELECT now() as c_time");
    while ($row = mysql_fetch_array($select)) {
        $current_time =  $row{'c_time'};
    }

    foreach($unserlized as $post_id => $value){
        $Latitude = $value["gpsLatitude"];
        $Longitude = $value["gpsLongitude"];
        $state = $city = $zip = "";
        if(isset($value["wlm_zip"])||!empty($value["wlm_zip"])){
            $zip = str_replace("'","\"",$value["wlm_zip"]);
        }
    
        if(isset($value["wlm_city"])||!empty($value["wlm_city"])){
            $city = str_replace("'","\"",$value["wlm_city"]);
        }
    
        if(isset($value["wlm_state"])||!empty($value["wlm_state"])){
            $state = str_replace("'","\"",$value["wlm_state"]);
        }
   
        $address = str_replace("'","\"",$value["address"]);
        $Latitude = str_replace("'","\"",$Latitude);
        $Longitude = str_replace("'","\"",$Longitude);
        $check_id= "SELECT * FROM `ait_items` WHERE `post_id`='$post_id'";
    
        $check_post_id = mysql_query($check_id);
        $num_rows = mysql_num_rows($check_post_id);
        $postTitle = str_replace("'","",$value['post_title']);
        if($num_rows > 0){
            $update = "update `ait_items` set
                                                `latitude` = '$Latitude',
                                                `longitude` = '$Longitude',
                                                `str_latitude` = '$Latitude',
                                                `str_longitude` = '$Longitude',
                                                `zip` = '$zip',
                                                `city` = '$city',
                                                `state` = '$state',
                                                `post_title` = '$postTitle',
                                                `address` = '$address',
                                                `created` = '$current_time'
                                            where `post_id` = $post_id";
            $result = mysql_query($update) or die(mysql_error());
        }else{
            $insert = "INSERT INTO `ait_items` SET
                                                `post_id` = $post_id,
                                                `latitude` = '$Latitude',
                                                `longitude` = '$Longitude',
                                                `str_latitude` = '$Latitude',
                                                `str_longitude` = '$Longitude',
                                                `zip` = '$zip',
                                                `city` = '$city',
                                                `state` = '$state',
                                                `post_title` = '$postTitle',
                                                `address` = '$address',
                                                `created` = '$current_time'
                                                ";
            $result = mysql_query($insert) or die(mysql_error());
        }
    }
    mysql_close($dbhandle);
    header("Location:index.php?msg=1");
?>