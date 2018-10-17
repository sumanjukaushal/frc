<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/wlm_sync_test_capabilities.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $intervalTimeInMin = "500";
    $intQry = "SELECT NOW( ) - INTERVAL $intervalTimeInMin MINUTE as timeBeforeInterval";
    $intResObj = $wpdb->get_results($intQry);
    $intervalTime = $intResObj[0]->timeBeforeInterval;
    $premMembers = array(43625);
    $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` IN ('".implode("','", $premMembers)."') AND  meta_key like '%_capabilities' GROUP BY `user_id` ORDER BY `user_id` ASC";
    $resultObj = $wpdb->get_results($query);
	
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
        global $intervalTime, $wpdb;
        $metaKeyArr['directory'] = array('classified_capabilities', 'shop_capabilities', 'wp_capabilities');
        $metaKeyArr['classified'] = array('shop_capabilities', 'wp_capabilities', 'gwr_capabilities');
        $metaKeyArr['shop'] = array('wp_capabilities', 'gwr_capabilities', 'classified_capabilities');
        $metaKeyArr['premium'] = array('gwr_capabilities', 'classified_capabilities', 'shop_capabilities');
        //a:3:{s:10:"admin_role";b:1;s:12:"create_pages";b:1;s:12:"create_posts";b:1;} - 22840 [Working]
        //a:3:{s:10:"admin_role";b:1;s:12:"create_pages";b:1;s:12:"create_posts";b:1;} : soniafrc (working) - 22840
		
        //gbroderos (13337); michael(14659); keitht (15605); soniafrc (22840); Stacie (36686); michael.m@freerangecamping.co.nz (42559)
        //SELECT * FROM `gwr_usermeta` where user_id IN(13337, 14659, 15605, 22840, 36686) and meta_key like '%_capabilities' order by user_id
		//SELECT * FROM `gwr_usermeta` WHERE `user_id`='17692' and `meta_key` like 'dir_activation_time'
		/*
		[31/05/17, 10:45:00 AM] Glen Wilson: Hi Mohit, re users
		They all need user role 
		
		primary: Administrator
		secondary: Admin Role
		
		They must have administrator as primary.  Can you adjust your script to make sure it reflects this please
		*/
        $metaKeys = $metaKeyArr[$source];
        list($metaKey_1, $metaKey_2, $metaKey_3) = $metaKeys;
		
        
        //Start: getting permissions from all other installations and merging them
        $cap1Arr = $cap2Arr = $cap3Arr = array();
        $sourceCap = unserialize($dataArr['meta_value']);	//print_r($sourceCap);
		
        foreach($metaKeys as $key => $metaKey){
            $inc = $key+1;
            $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey'";
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
        }//$capArr = array_merge($sourceCap, $cap1Arr, $cap2Arr, $cap3Arr);
        //End:   getting permissions from all other installations and merging them
		
        
        $delVal = 'subscriber';
        if(count($capArr) > 1 && ($key = array_search($delVal, $capArr)) !== false){
            //remove subscriber
            unset($capArr[$key]);
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
        $dataArr['modified'] = $intervalTime;
        $newCapArr = array();
        foreach($capArr as $capVal){
            $newCapArr[$capVal] = 1;
        }
		//-----------------------------
		$employeePermArr = array('premium' => 1, 'customer' => 1, 'subscriber' => 1);
		$newCapArr = $employeePermArr;	//Only for employees
		//-----------------------------
		print_r($newCapArr);
		$dataArr['meta_value'] = serialize($newCapArr);
        foreach($metaKeys as $key => $metaKey){
            $fieldArr = $dataArr;
            $fieldArr['meta_key'] = $metaKey;
            $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey'";
            $capbObj = $wpdb->get_results($query);
            if(count($capbObj) >= 1){
                //update meta record
                $capMetaID = $capbObj[0]->umeta_id;
                $wpdb->update( 
                                'gwr_usermeta', 
                                $fieldArr,
                                array( 'umeta_id' => $capMetaID, 'user_id' => $userID )
                            );
                //echo "\n<br/>Update Qry:".$updateQry = $wpdb->last_query;
            }else{
                //insert record
                $capStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
                $insertQry = $wpdb->last_query; //echo "\n<br/>Cap Qry:".
                if($capStatus){
                    echo "\n<br/>Capabilities added for metakey: $metaKey installation successfully for user $userID";
                }
            }
        }
        return;
    }
	echo "Permissions updated for following users: gbroderos, michael, keitht, soniafrc, Stacie, michael.m"
?>