<?php
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_subscriber_cron/subscriber.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $dbTimeNow = gmdate("Y-m-d H:i:s", mktime());
    $dbTimeAgo = $timeAgo  = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-1, date("Y"));
    //$dbTimeAgo = $timeAgo  = mktime(date("H"), date("i")-10, date("s"), date("m")  , date("d"), date("Y")); //was not working
    //SELECT * FROM `wp_posts` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing'
    //SELECT sum(`pmt_counts`) FROM `frc_premium_users`
    $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
    $postsTable = "wp_posts";
    $dbQry = "SELECT `ID` FROM `$postsTable`  WHERE (`$postsTable`.`post_modified_gmt` > '$dbTimeAgo' AND `gwr_posts`.`post_modified_gmt` < '$dbTimeNow')";
    echo $query = "SELECT * FROM `$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing' AND (`post_modified_gmt` > '$dbTimeAgo' AND `post_modified_gmt` < '$dbTimeNow')";
    //echo $query = "SELECT * FROM `$postsTable` WHERE `post_type`='shop_order' AND `post_status` = 'wc-processing'";
    $result = $wpdb->get_results($query);//echo'<pre>';print_r($result);die;
    
    $missingData[] = $IDs = array();
    foreach($result as $key => $value){$IDs[] = $value->ID;}//echo'<pre>';print_r($IDs);die;
    $recordCounter = 0;
    $recordFound = 0;
    $strangeIDs = array(58961, 58985, 58999, 59007, 59023, 59027, 47319, 59030, 59045);
    foreach($IDs as $key1 => $value1){
        $pmtDetArr = $wpIDArr = $billing = array();
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
        //if(!in_array($user_id, $strangeIDs)){echo "<br/>$key1 UserID: ".$user_id;continue;}
        $billing_shipping = serialize($billing);
        $userQry = "SELECT * FROM `gwr_users` WHERE `ID` = $user_id";
        $usrResult = $wpdb->get_results($userQry);
        if(empty($user_id) || $user_id == 0)continue;
        if(!empty($usrResult)){
            $userObj = get_userdata( $user_id );
            echo "<br />".$userSelQry = "SELECT * FROM `frc_premium_users` WHERE `user_id` = $user_id";
            $usrSelResult = $wpdb->get_results($userSelQry);
            $dataArrays[] = $dataArr = array(
                                 'user_id' => $user_id,
                                 'paid_date' => $paid_date ,
                                 'date_paid' => $date_paid ,
                                 'order_total' => $order_total,
                                 'billing_shipping' => $billing_shipping,
                                 'login' => $userObj->user_login,
                                 'email' => $userObj->user_email,
                                );//print_r($dataArr);die;
            if(count($usrSelResult) >= 1){
                ;//do nothing
                $pmtDetArr = $postIDsArr = array();
                $postIDs = $usrSelResult[0]->post_ids;
                $frcID = $usrSelResult[0]->id;
                $pmtDetArr = unserialize($usrSelResult[0]->pmt_details);
                $totalPmt = $usrSelResult[0]->total_pmt;
                
                if(is_array($pmtDetArr) && !in_array($ID, $pmtDetArr)){
                    $pmtDetArr[$ID] = array('post_id' => $ID, 'amount' => $order_total, 'pmt_date' => $paid_date);
                    $pmtDetailStr = serialize($pmtDetArr);
                }
                if(!empty($postIDs)){
                    $wpIDArr = explode("|", $postIDs);
                    if(!in_array($ID, $wpIDArr)){
                        $wpIDArr[] = $ID;
                        $postIDs = implode("|", $wpIDArr);
                        $pmtCounts = count($wpIDArr);
                        $totalPmt = $totalPmt + $order_total;
                    }else{
                        $pmtCounts = count($wpIDArr);
                    }
                }else{
                    $postIDs = $ID;
                    $pmtCounts = 1;
                }
                $dataArr = array('pmt_counts' => $pmtCounts, 'post_ids' => $postIDs, 'pmt_details' => $pmtDetailStr, 'total_pmt' => $totalPmt);
                //echo "<pre>";print_r($dataArr);print_r($pmtDetArr);echo "</pre>";
                $updateQry = $wpdb->update('frc_premium_users', $dataArr, array('id' => $frcID));
                echo "\n<br/><br/>Update Qry($currDB):".$updateQry = $wpdb->last_query;
                $recordFound++;
            }else{
                $missingData[] = $dataArr;
                $pmtDetailArr = array();
                $pmtDetailArr[$ID] = array('post_id' => $ID,'amount' => $order_total, 'pmt_date' => $paid_date);
                $pmtDetailStr = serialize($pmtDetailArr);
                //new fields
                $dataArr['pmt_counts'] = 1;
                $wpPostID = $dataArr['post_ids'] = $ID;
                $dataArr['pmt_details'] = $pmtDetailStr;
                $dataArr['total_pmt'] = $order_total;
                $insQry = $wpdb->insert('frc_premium_users', $dataArr);
                //, array('%d', '%s', '%s', '%s' , '%s', '%s', '%s','%s', '%s','%s', '%s')
                echo "\n<br/><br/>Update Qry($currDB):".$insQry = $wpdb->last_query;
                $recordCounter++;
            }
        }else{
            ;//echo "<br/>$key1.:  ".$userQry;
        }
    }
    echo "<br/>frc_premium_users founds are: $recordFound <br/>";
    echo "<pre>";print_r($missingData);echo "</pre>";
    //echo "<pre>";print_r($dataArrays);echo "</pre>";
    echo "<br /> $recordCounter new records added";
?>
