<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/shop_user_upgrade_capabilities.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $timeInterval = "500 MINUTE";
	$timeInterval = '5000 MINUTE';
    $intQry = "SELECT NOW( ) - INTERVAL $timeInterval  as timeBeforeInterval";//- INTERVAL $timeInterval
    $intResObj = $wpdb->get_results($intQry);
    $intervalTime = $intResObj[0]->timeBeforeInterval;
	wp_cache_flush();
	$wpdb->select('freerang_shop');
	echo $query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` = 'shop_capabilities') ORDER BY `umeta_id` DESC"; //For grabbing capabilities of all installation modified in last time interval
    $resultObj1 = $wpdb->get_results($query);
	print_r($resultObj1);die;
	$wpdb->select('freerang_premium');
	
    if(count($resultObj)){
		foreach($resultObj as $userObj){
            $userID = (int)$userObj->user_id;
			$userCapabilities = $userObj->meta_value;	//serialised data
            $dataArr = array('user_id' => $userID, 'meta_key' => $userObj->meta_key, 'meta_value' => $userCapabilities, 'processed_by_script' => 1);
            switch($userObj->meta_key){
                case 'gwr_capabilities':
                    //push capabilites to other 3 installation after merging
                    frc_sync_cap($source = 'directory', $userID, $dataArr);
                    break;
                case 'classified_capabilities':
                    frc_sync_cap($source = 'classified', $userID, $dataArr);
                    break;
                case 'shop_capabilities':
                    frc_sync_cap($source = 'shop', $userID, $dataArr);
                    break;
                case 'wp_capabilities':
                    frc_sync_cap($source = 'premium', $userID, $dataArr);
                    break;
            }
        }
    }
    
    function frc_sync_cap($source, $userID, $dataArr){
        //if($userID != 15605)return;
        global $intervalTime, $wpdb,$timeInterval;
		echo "\nuserid:".$userID;
		echo "\ntime interval:".$timeInterval;
		echo "\nSource:$source";
		
        $metaKeyArr['directory'] = array('classified_capabilities', 'shop_capabilities', 'wp_capabilities');
        $metaKeyArr['classified'] = array('shop_capabilities', 'wp_capabilities', 'gwr_capabilities');
        $metaKeyArr['shop'] = array('wp_capabilities', 'gwr_capabilities', 'classified_capabilities');
        $metaKeyArr['premium'] = array('gwr_capabilities', 'classified_capabilities', 'shop_capabilities');
        $metaKeys = $metaKeyArr[$source]; //get name of capabilities in collection for other installations other than $source
        echo "\n-------{$userID}:{$source}--------\n";
        list($metaKey_1, $metaKey_2, $metaKey_3) = $metaKeys;//get name of capabilities in other installations other than $source
        echo "\n-------{$metaKey_1}:{$metaKey_2}:{$metaKey_3}--------\n";
       // print_r($dataArr);
        
        //Start: getting permissions from all other installations and merging them
        $cap1Arr = $cap2Arr = $cap3Arr = array();
        $sourceCap = unserialize($dataArr['meta_value']);
        //print_r($sourceCap);
        foreach($metaKeys as $key => $metaKey){
			//In this loop, we are getting capabilities of other installations in collection in respective variables e.g.:$cap1Arr, $cap2Arr
            $inc = $key+1;
            echo "\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey'";
            $capObj = $wpdb->get_results($query);
            if(count($capObj) >= 1){
                $capVarArr = "cap{$inc}Arr";
                $$capVarArr = unserialize($capObj[0]->meta_value);
            }
        }//print_r($cap1Arr);print_r($cap2Arr);print_r($cap3Arr);echo "\n-----------------------------\n";
        
        $capArr = array();
        foreach($sourceCap as $capKey => $capVal){
            $capArr[] = $capKey;
        }
        foreach($cap1Arr as $capKey => $capVal){
            if(!in_array($capKey, $capArr)){
                $capArr[] = $capKey;
            }
        }
        foreach($cap2Arr as $capKey => $capVal){
            if(!in_array($capKey, $capArr)){
                $capArr[] = $capKey;
            }
        }
        foreach($cap3Arr as $capKey => $capVal){
            if(!in_array($capKey, $capArr)){
                $capArr[] = $capKey;
            }
        }
        //$capArr = array_merge($sourceCap, $cap1Arr, $cap2Arr, $cap3Arr);
        //End:   getting permissions from all other installations and merging them
        $delVal = 'subscriber';
        if(count($capArr) > 1 && ($key = array_search($delVal, $capArr)) !== false){
            unset($capArr[$key]);	//remove subscriber
        }
		
		//Case: if user have role premium + customer; than customer roles needs to be changed to premium
		//Case added by Glen (Refer doc: 13-06 Mohit Notes.txt): If user in /shop is user role of customer (they purchased product in /shop) and they upgrade to Foundation Membership or Premium Membership, we will need their user role to change to premium. So for this case, I will not keep user role customer and premium at same time. I am deleting the customer role for this case.
		if( in_array('premium', $capArr) && in_array('customer', $capArr) ){
			$delVal = 'customer';
			if($key = array_search($delVal, $capArr) !== false){
				unset($capArr[$key]);//remove customer
			}
		}
		
        $dataArr['meta_value'] = serialize($capArr);
        if($source == 'directory'){
            $metaKeys[] = 'gwr_capabilities';
        }elseif($source == 'shop'){
            $metaKeys[] = 'shop_capabilities';
        }elseif($source == 'premium'){
            $metaKeys[] = 'wp_capabilities';
        }elseif($source == 'classified'){
            $metaKeys[] = 'classified_capabilities';
        }
		//Note: $metaKeys is already containing meta name for 3 installation and in above snippet we are adding name of source installation. So we have 4 in total
        $dataArr['modified'] = $intervalTime;
        $newCapArr = array();
        foreach($capArr as $capVal){
            $newCapArr[$capVal] = 1;
        }
        $dataArr['meta_value'] = serialize($newCapArr);	//print_r($newCapArr);//print_r($metaKeys);die;
		
        foreach($metaKeys as $key => $metaKey){
            $fieldArr = $dataArr;				//echo "\nBefore:";print_r($capArr);echo "\n";
            $fieldArr['meta_key'] = $metaKey;	//echo "\After:";print_r($fieldArr);echo "\n";
            echo "\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey'";
            echo "\n";//continue;
            $capbObj = $wpdb->get_results($query);
            if(count($capbObj) >= 1){
                //update meta record
                echo "\n$key:update";
                $capMetaID = $capbObj[0]->umeta_id;
                $wpdb->update( 
                                'gwr_usermeta', 
                                $fieldArr,
                                array( 'umeta_id' => $capMetaID, 'user_id' => $userID )
                            );
                echo "\n<br/>Update Qry:".$updateQry = $wpdb->last_query;
            }else{
                //insert record
                echo "\n$key:insert";   //$dataArr['meta_key'] = $metaKey;$dataArr['modified'] = $intervalTime;
                $capStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
                echo "\n<br/>Cap Qry:".$insertQry = $wpdb->last_query;
                if($capStatus){
                    echo "\n<br/>Capabilities added for metakey: $metaKey installation successfully for user $userID";
                }
            }
        }
        return;
    }
die('script executed successfully');
?>