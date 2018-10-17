<?php
    //calling URL: https://www.freerangecamping.co.nz/cron/wp-content/wlm_sync/sync_from_wp_posts.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $dbPrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
    $currDB = "{$dbPrefix}premium";
    $wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
    $postsTable = "wp_posts";
    $timeInterval = '3 MINUTE';
    $query = "SELECT `ID` FROM `$currDB`.`$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing' AND (`modified` > ( NOW( ) - INTERVAL $timeInterval ))";
    $result = $wpdb->get_results($query);
    $idArr = array();
    foreach($result as $key => $postObj){$idArr[] = $postObj->ID;}
    $recordCounter = 0;
    echo "$query\n<br/><br/>";
    foreach($idArr as $postID){
        $metaQry = "SELECT * FROM `$currDB`.`wp_postmeta` WHERE `post_id` = $postID AND `meta_key` = '_customer_user' ORDER BY `meta_id` DESC LIMIT 0, 1 ";
        $metaResult = $wpdb->get_results($metaQry);
        $userID = 0;
        if(count($metaResult) >= 1){$userID = $metaResult[0]->meta_value;}
        if($userID){
            $userQry = "SELECT `meta_value` FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'wp_capabilities'";
            $usrResult = $wpdb->get_results($userQry);//echo'<pre>';print_r($usrResult);
            $capArr = unserialize($usrResult[0]->meta_value);
            
            if(is_array($capArr) AND count($capArr) >= 1){
                if( array_key_exists('customer', $capArr)){unset($capArr['customer']);}
                if( array_key_exists('subscriber', $capArr)){unset($capArr['subscriber']);}
                if( !array_key_exists('premium', $capArr)){$capArr['premium'] = 1;}
                $userCapabilities = serialize($capArr);
                $wpdb->update('gwr_usermeta',
                              array('meta_value' => $userCapabilities),
                              array( 'meta_key' => 'wp_capabilities', 'user_id' => $userID )
                              );
                echo "\n<br/>Update Qry($currDB):".$updateQry = $wpdb->last_query;
                $wpdb->update($postsTable, array('modified' => '0000-00-00 00:00:00'), array( 'ID' => $postID ));
                echo "\n<br/>Update Qry($currDB):".$updateQry = $wpdb->last_query;
            }else{
                //insert role
            }
        }
    }
    
    //----------if we have joined databases--------
    $currDB = "{$dbPrefix}directory";
    $wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
    echo $query = "SELECT `ID` FROM `$currDB`.`$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing' AND (`modified` > ( NOW( ) - INTERVAL $timeInterval ))";
    $result = $wpdb->get_results($query);
    $idArr = array();
    foreach($result as $key => $postObj){$idArr[] = $postObj->ID;}
    $recordCounter = 0;
    foreach($idArr as $postID){
        $metaQry = "SELECT * FROM `$currDB`.`wp_postmeta` WHERE `post_id` = $postID AND `meta_key` = '_customer_user' ORDER BY `meta_id` DESC LIMIT 0, 1 ";
        $metaResult = $wpdb->get_results($metaQry);
        $userID = 0;
        if(count($metaResult) >= 1){$userID = $metaResult[0]->meta_value;}
        if($userID){
            $userQry = "SELECT `$currDB`.`meta_value` FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'wp_capabilities'";
            $usrResult = $wpdb->get_results($userQry);//echo'<pre>';print_r($usrResult);
            $capArr = unserialize($usrResult[0]->meta_value);
            
            if(is_array($capArr) AND count($capArr) >= 1){
                if( array_key_exists('customer', $capArr)){unset($capArr['customer']);}
                if( array_key_exists('subscriber', $capArr)){unset($capArr['subscriber']);}
                if( !array_key_exists('premium', $capArr)){$capArr['premium'] = 1;}
                $userCapabilities = serialize($capArr);
                $wpdb->update('gwr_usermeta',
                              array('meta_value' => $userCapabilities),
                              array( 'meta_key' => 'wp_capabilities', 'user_id' => $userID )
                              );
                echo "\n<br/>Update Qry($currDB):".$updateQry = $wpdb->last_query;
                $wpdb->update($postsTable, array('modified' => '0000-00-00 00:00:00'), array( 'ID' => $postID ));
                echo "\n<br/>Update Qry($currDB):".$updateQry = $wpdb->last_query;
            }else{
                //insert role - for testing
                /*
                $wpdb->insert('gwr_usermeta',array('meta_value' => $userCapabilities, 'user_id' => $userID, 'meta_key' => 'wp_capabilities'));
                echo "\n<br/>Insert Qry:".$updateQry = $wpdb->last_query;
                $wpdb->update($postsTable, array('modified' => '0000-00-00 00:00:00'), array( 'ID' => $postID ));
                echo "\n<br/>Update Qry:".$updateQry = $wpdb->last_query;
                */
            }
        }
    }
    die("\n <br/>script executed successfully");
?>