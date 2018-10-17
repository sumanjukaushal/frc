<?php
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_subscriber_cron/subscriber.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $dbTimeNow = gmdate("Y-m-d H:i:s", mktime());
    $dbTimeAgo = $timeAgo  = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-11, date("Y"));
    //$dbTimeAgo = $timeAgo  = mktime(date("H"), date("i")-10, date("s"), date("m")  , date("d"), date("Y")); //was not working
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $postsTable = "wp_posts";
    $dbQry = "SELECT `ID` FROM `$postsTable`  WHERE (`$postsTable`.`post_modified_gmt` > '$dbTimeAgo' AND `gwr_posts`.`post_modified_gmt` < '$dbTimeNow')";
    echo $query = "SELECT * FROM `$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing' AND (`post_modified_gmt` > '$dbTimeAgo' AND `post_modified_gmt` < '$dbTimeNow')";
    $result = $wpdb->get_results($query);//echo'<pre>';print_r($result);die;
    
    $IDs = array();
    foreach($result as $key => $value){$IDs[] = $value->ID;}//echo'<pre>';print_r($IDs);die;57222
    $recordCounter = 0;
    foreach($IDs as $key1 => $value1){
        $billing = array();
        $ID = $value1;
        $metaQry = "SELECT * FROM `wp_postmeta` WHERE `post_id` = $ID";
        $metaResult = $wpdb->get_results($metaQry); //echo'<pre>';print_r($result1);die;
        foreach($metaResult as $metaKey => $metaVal){  //echo'<pre>';print_r($metaVal);
            $meta_key = $metaVal->meta_key;
            if($meta_key == '_customer_user') $user_id = $metaVal->meta_value;
            if($meta_key == '_date_paid') $date_paid = $metaVal->meta_value;
            if($meta_key == '_paid_date') $paid_date = $metaVal->meta_value;
            if($meta_key == '_order_total') $order_total = $metaVal->meta_value;
            
            if(strpos($meta_key,'_billing_') !== false){
                if($meta_key == '_billing_address_index') continue;
                $billing[$metaVal->meta_key] = $metaVal->meta_value;    //echo'<pre>';print_r($billing);
            }
            
            if(strpos($meta_key,'_shipping_') !== false){
                $billing[$metaVal->meta_key] = $metaVal->meta_value;
            }
        }
        $billing_shipping = serialize($billing);
        $userQry = "SELECT * FROM `gwr_users` WHERE `ID` = $user_id";
        $usrResult = $wpdb->get_results($userQry);
        if(!empty($usrResult)){
            $userObj = get_userdata( $user_id );
            $userSelQry = "SELECT * FROM `frc_premium_users` WHERE `user_id` = $user_id";
            $usrSelResult = $wpdb->get_results($userSelQry);
            if(count($usrSelResult) >= 1){
                ;//do nothing
            }else{
                $dataArr = array(
                                 'user_id' => $user_id,
                                 'paid_date' => $paid_date ,
                                 'date_paid' => $date_paid ,
                                 'order_total' => $order_total,
                                 'billing_shipping' => $billing_shipping,
                                 'login' => $userObj->user_login,
                                 'email' => $userObj->user_email,
                                );//print_r($dataArr);die;
                $insQry = $wpdb->insert('frc_premium_users', $dataArr, array('%d', '%s', '%s', '%s' , '%s', '%s', '%s'));
                $recordCounter++;
            }
        }
    }
    
    
    //>>>>>>>>>>>>> For Separate DB >>>>>>>>>>>
    $currDB = "freerang_premium";
    $wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $dbTimeNow = gmdate("Y-m-d H:i:s", mktime());
    $dbTimeAgo = $timeAgo  = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-11, date("Y"));
    //$dbTimeAgo = $timeAgo  = mktime(date("H"), date("i")-10, date("s"), date("m")  , date("d"), date("Y")); //was not working
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $postsTable = "wp_posts";
    $dbQry = "SELECT `ID` FROM `$postsTable`  WHERE (`$postsTable`.`post_modified_gmt` > '$dbTimeAgo' AND `gwr_posts`.`post_modified_gmt` < '$dbTimeNow')";
    echo $query = "SELECT * FROM `$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing' AND (`post_modified_gmt` > '$dbTimeAgo' AND `post_modified_gmt` < '$dbTimeNow')";
    $result = $wpdb->get_results($query);//echo'<pre>';print_r($result);die;
    
    $IDs = array();
    foreach($result as $key => $value){$IDs[] = $value->ID;}//echo'<pre>';print_r($IDs);die;57222
    $recordCounter = 0;
    foreach($IDs as $key1 => $value1){
        $billing = array();
        $ID = $value1;
        $metaQry = "SELECT * FROM `wp_postmeta` WHERE `post_id` = $ID";
        $metaResult = $wpdb->get_results($metaQry); //echo'<pre>';print_r($result1);die;
        foreach($metaResult as $metaKey => $metaVal){  //echo'<pre>';print_r($metaVal);
            $meta_key = $metaVal->meta_key;
            if($meta_key == '_customer_user') $user_id = $metaVal->meta_value;
            if($meta_key == '_date_paid') $date_paid = $metaVal->meta_value;
            if($meta_key == '_paid_date') $paid_date = $metaVal->meta_value;
            if($meta_key == '_order_total') $order_total = $metaVal->meta_value;
            
            if(strpos($meta_key,'_billing_') !== false){
                if($meta_key == '_billing_address_index') continue;
                $billing[$metaVal->meta_key] = $metaVal->meta_value;    //echo'<pre>';print_r($billing);
            }
            
            if(strpos($meta_key,'_shipping_') !== false){
                $billing[$metaVal->meta_key] = $metaVal->meta_value;
            }
        }
        $billing_shipping = serialize($billing);
        $userQry = "SELECT * FROM `gwr_users` WHERE `ID` = $user_id";
        $usrResult = $wpdb->get_results($userQry);
        if(!empty($usrResult)){
            $userObj = get_userdata( $user_id );
            $userSelQry = "SELECT * FROM `frc_premium_users` WHERE `user_id` = $user_id";
            $usrSelResult = $wpdb->get_results($userSelQry);
            if(count($usrSelResult) >= 1){
                ;//do nothing
            }else{
                $dataArr[] = array(
                                 'user_id' => $user_id,
                                 'paid_date' => $paid_date ,
                                 'date_paid' => $date_paid ,
                                 'order_total' => $order_total,
                                 'billing_shipping' => $billing_shipping,
                                 'login' => $userObj->user_login,
                                 'email' => $userObj->user_email,
                                );//print_r($dataArr);die;
            }
        }
    }
    print_r($dataArr);
    die;
    foreach($dataArr as $sngDataArr){
        $insQry = $wpdb->insert('frc_premium_users', $sngDataArr, array('%d', '%s', '%s', '%s' , '%s', '%s', '%s'));
    }
    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    echo "$recordCounter new records added";
?>
