<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/wlm_sync_capabilities_all.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $timeInterval = "500 MINUTE";
	$timeInterval = '10 MINUTE';
    $intQry = "SELECT NOW( ) - INTERVAL $timeInterval  as timeBeforeInterval";//- INTERVAL $timeInterval
    $intResObj = $wpdb->get_results($intQry);
    echo $intervalTime = $intResObj[0]->timeBeforeInterval;
    //[meta_value] => a:4:{s:13:"administrator";b:1;s:10:"admin_role";b:1;s:12:"create_pages";b:1;s:12:"create_posts";b:1;}
    /*$premMembers = array(
                            42847, 42834, 42559, 42557, 42242, 42051, 42050, 40743, 39679, 39675,
                            39674, 39068, 38841, 38839, 38838, 38828, 38748, 38292, 36686, 31903,
                            28151, 22840, 21847, 21258, 18382, 17692, 17232, 15605, 14398, 13337,
                            11819, 10810, 4614, 4613, 1173, 63, 58, 53, 48, 46
                        );*/
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` like '%_capabilities') ORDER BY `umeta_id` DESC"; //For grabbing capabilities of all installation modified in last time interval
    $resultObj = $wpdb->get_results($query);	//print_r($resultObj);die;
	
    if(count($resultObj)){
		foreach($resultObj as $userObj){
            $userID = (int)$userObj->user_id;
			$userCapabilities = $userObj->meta_value;	//serialised data
            $dataArr = array('user_id' => $userID, 'meta_key' => $userObj->meta_key, 'meta_value' => $userCapabilities, 'processed_by_script' => 1);
			//if($userObj->meta_key != 'wp_capabilities')continue;
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
        
        //a:1:{s:10:"subscriber";b:1;}
        //a:3:{s:10:"admin_role";b:1;s:12:"create_pages";b:1;s:12:"create_posts";b:1;} - 22840 [Working]
        //a:3:{s:12:"create_pages";b:1;s:12:"create_posts";b:1;s:10:"subscriber";b:1;} - 15605
        //a:1:{s:7:"premium";b:1;}
        //SELECT * FROM `gwr_usermeta` where user_id IN(13337, 14659, 15605, 22840, 36686) and meta_key like '%_capabilities' order by user_id
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

<?php
/*
    //Merging premium and directory meta_values
    if($source == 'directory' || $source == 'premium'){
        //Merge with premium
        $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'wp_capabilities'";
        $premObj = $wpdb->get_results($query);
        if(count($premObj) >= 1){
            $preCap = unserialize($premObj[0]->meta_value);
            $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'gwr_capabilities'";
            $dirObj = $wpdb->get_results($query);
            $gwrCap = unserialize($dirObj[0]->meta_value);
            if(array_key_exists('premium',$preCap )){
                print_r($gwrCap);print_r($preCap);
                $capArr = array_merge($gwrCap, $preCap);
                $dataArr['meta_value'] = serialize($capArr);
                echo "\nMerging:\n";
                print_r($dataArr);
            }
        }else{
            
            echo "\nWithout Merging:\n";
            print_r($dataArr);
        }
    }
    return;
    foreach($metaKeys as $metaKey){
        echo "\n:$metaKey\n";
        $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey'";
        $premObj = $wpdb->get_results($query);
        if(count($premObj) >= 1){
            //update record
            echo "\nUpdate Record\n";
        }else{
            //insert record
            $dataArr['meta_key'] = $metaKey;
            $dataArr['modified'] = $intervalTime;
            $capStatus = $wpdb->insert( "gwr_usermeta", $dataArr);
            echo "\n<br/>Cap Qry:".$insertQry = $wpdb->last_query;
            if($capStatus){
                echo "\n<br/>Capabilities added to $source installation successfully for user $userID";
            }
        }
    }
 
 */
?>