<?php
	//Original Script: wlm_sync_capabilities_all
	//a:1:{s:7:"premium";i:1;}
	//a:1:{s:11:"directory_1";b:1;}
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_capabilities_multiple.php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_capabilities_multiple.php
	// `user_id` = 10810 
	//1. SELECT * FROM `gwr_usermeta` WHERE `meta_key`='gwr_capabilities' and meta_value <> 'a:1:{s:8:"customer";b:1;}'
	//2. SELECT * FROM `gwr_usermeta` WHERE `meta_key`='wp_capabilities' and meta_value <> 'a:1:{s:8:"customer";b:1;}'
	//3. SELECT * FROM `gwr_usermeta` WHERE `meta_key`='shop_capabilities' and meta_value <> 'a:1:{s:8:"customer";b:1;}'
	//4. SELECT * FROM `gwr_usermeta` WHERE `meta_key`='classified_capabilities' and meta_value <> 'a:1:{s:10:"subscriber";b:1;}' ORDER BY RAND()
	//a:2:{s:13:"administrator";b:1;s:10:"admin_role";b:1;} a:1:{s:10:"subscriber";b:1;} 11950
	/*
	 
	SELECT * FROM `gwr_usermeta` WHERE `user_id`=4 AND meta_key like '%_capabilities';
	 
	SELECT * FROM `frcconz_premium`.`gwr_usermeta` WHERE `user_id`=11950 AND meta_key='wp_capabilities';
	SELECT * FROM `frcconz_directory`.`gwr_usermeta` WHERE `user_id`=11950 AND meta_key='gwr_capabilities';
	SELECT * FROM `frcconz_shop`.`gwr_usermeta` WHERE `user_id`=11950 AND meta_key='shop_capabilities';
	SELECT * FROM `frcconz_classified`.`gwr_usermeta` WHERE `user_id`=11950 AND meta_key='classified_capabilities';
	
	UPDATE `frcconz_classified`.`gwr_usermeta` SET `meta_key` = 'a:1:{s:11:"directory_1";b:1;}' WHERE `user_id`=4 AND meta_key='classified_capabilities';
	UPDATE `frcconz_directory`.`gwr_usermeta` SET `meta_key` ='a:1:{s:10:"subscriber";b:1;}' WHERE `user_id`=4 AND meta_key='gwr_capabilities';
	UPDATE `frcconz_shop`.`gwr_usermeta` SET `meta_key` ='a:1:{s:11:"directory_1";b:1;}' WHERE `user_id`=4 AND meta_key='shop_capabilities';
	UPDATE `frcconz_premium`.`gwr_usermeta` SET `meta_key` ='a:1:{s:8:"customer";b:1;}' WHERE `user_id`=4 AND meta_key='wp_capabilities';
	
	UPDATE `frcconz_premium`.`gwr_usermeta` SET `meta_value` = 'a:1:{s:10:"subscriber";b:1;}' WHERE `user_id`=11950 AND meta_key='wp_capabilities';
	UPDATE `frcconz_directory`.`gwr_usermeta` SET `processed_by_script` = '3' WHERE `user_id`=11950 AND meta_key='gwr_capabilities';
	UPDATE `frcconz_classified`.`gwr_usermeta` SET `meta_value` = 'a:2:{s:13:"administrator";b:1;s:10:"admin_role";b:1;}' WHERE `user_id`=11950 AND meta_key='classified_capabilities';
	UPDATE `frcconz_shop`.`gwr_usermeta` SET `meta_value` = 'a:1:{s:8:"customer";b:1;}' WHERE `user_id`=11950 AND meta_key='shop_capabilities';
	 **/
	
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
	$timeInterval = '30 MINUTE';
	$timeInterval = '1 MINUTE';
    $intQry = "SELECT NOW( ) - INTERVAL $timeInterval  as timeBeforeInterval";//- INTERVAL $timeInterval
    $intResObj = $wpdb->get_results($intQry);
    $intervalTime = $intResObj[0]->timeBeforeInterval;
	$multipleDBs = array(
						 'directory' => 'gwr_capabilities',
						 'premium' => 'wp_capabilities',
						 'classified' => 'classified_capabilities',
						 'shop' => 'shop_capabilities'
						);
	$allCaps = array();
	echo "<pre>";
	echo serialize(array('customer' => 1, 'admin_role2' => 1));
	echo "\n".serialize(array('directory_3' => 1, 'directory_5' => 1));
	$dbPrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
	$userIDs = modified_user_caps($timeInterval, $dbPrefix);	print_r($userIDs);
	$userIDStr = implode(",",$userIDs);
	//$userIDStr = 8;
	
	//1 Get capabilties of user last modified from directory installation
	$currDB ="{$dbPrefix}directory";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	if(!is_array($userIDs) || count($userIDs) == 0){die("Nothing to Sync");}
	
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `user_id` IN($userIDStr) AND (`meta_key` = 'gwr_capabilities') ORDER BY `umeta_id` DESC"; 		
	//echo "\n Directory Qry: $query\n";
	$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
	foreach($resultObj as $userObj){
		$allCaps[$userObj->user_id] = $userObj->meta_value;
		echo "\ndirectory permissions\n";
		print_r(unserialize($userObj->meta_value));
	}
	
	//2 Get capabilties of user last modified from premium installation
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `user_id` IN($userIDStr) AND (`meta_key` = 'wp_capabilities') ORDER BY `umeta_id` DESC";
	//echo "\nPremium Qry: $query\n";
	$currDB ="{$dbPrefix}premium";
	if($dbPrefix == 'frcconz_'){
		$premDB = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$resultObj = $premDB->get_results($query);
	}else{
		$wpdb->select($currDB);wp_cache_flush();
		$resultObj = $wpdb->get_results($query);
	}	//echo "\premium: \n";print_r($resultObj);
	
	foreach($resultObj as $userObj){
		$allCapbility = $allCaps[$userObj->user_id];
		if(array_key_exists($userObj->user_id, $allCaps) && ($userObj->meta_value != $allCapbility)){
			//unserialize and merge both if capabilities are different in Premium Installation.
			$allCaps[$userObj->user_id] = frc_unserialize_n_merge($userObj->meta_value, $allCapbility);
		}else{
			$allCaps[$userObj->user_id] = $userObj->meta_value;
		}
		echo "\n premium permissions\n";
		print_r(unserialize($userObj->meta_value));
	}	//print_r($allCaps);
	
	//3 Get capabilties of user last modified in shop installation
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `user_id` IN($userIDStr) AND (`meta_key` = 'shop_capabilities') ORDER BY `umeta_id` DESC";
	//echo "\n Shop Qry: $query\n";
	
	$currDB ="{$dbPrefix}shop";
	if($dbPrefix == 'frcconz_'){
		$shopDB = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$resultObj = $shopDB->get_results($query);
	}else{
		$wpdb->select($currDB);wp_cache_flush();
		$resultObj = $wpdb->get_results($query);
	}	//echo "\shop: \n";print_r($resultObj);
	
	foreach($resultObj as $userObj){
		$allCapbility = $allCaps[$userObj->user_id];
		if(array_key_exists($userObj->user_id, $allCaps) && ($userObj->meta_value != $allCapbility)){
			//unserialize and merge both if capabilities are different in Premium Installation.
			$allCaps[$userObj->user_id] = frc_unserialize_n_merge($userObj->meta_value, $allCapbility);
		}else{
			$allCaps[$userObj->user_id] = $userObj->meta_value;
		}
		echo "\n shop permissions\n";
		print_r(unserialize($userObj->meta_value));
	}
	
	//4 Get capabilties of user last modified from classified installation
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `user_id` IN($userIDStr) AND (`meta_key` = 'classified_capabilities') ORDER BY `umeta_id` DESC";
	echo "\n classified Qry: $query\n";
	
	$currDB ="{$dbPrefix}classified";
	if($dbPrefix == 'frcconz'){
		$classDBObj = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$resultObj = $wpdb->get_results($query);
	}else{
		$wpdb->select($currDB);wp_cache_flush();
		$resultObj = $wpdb->get_results($query);
	}	//echo "\nclassified: \n";print_r($resultObj);
	foreach($resultObj as $userObj){
		$allCapbility = $allCaps[$userObj->user_id];
		if(array_key_exists($userObj->user_id, $allCaps) && ($userObj->meta_value != $allCapbility)){
			//unserialize and merge both if capabilities are different in Classified Installation.
			$allCaps[$userObj->user_id] = frc_unserialize_n_merge($userObj->meta_value, $allCapbility);
		}else{
			$allCaps[$userObj->user_id] = $userObj->meta_value;
		}
		echo "\n Classified permissions\n";
		print_r(unserialize($userObj->meta_value));
	}
	
    if(count($allCaps)){
		echo "\n <strong>All permissions </strong>\n";
		print_r($allCaps);
		//Case 1: Updating capabilites in directory installation
		//echo "\n\n<strong>Queries for directory</strong>:\n\n";
		$currDB ="{$dbPrefix}directory";
		frc_sync_user_cap($allCaps, $currDB, 'gwr_capabilities'); 
		
		//Case 2: Updating capabilites in premium installation
		//echo "\n\n<strong>Queries for premium</strong>:\n\n";
		$currDB ="{$dbPrefix}premium";
		frc_sync_user_cap($allCaps, $currDB, 'wp_capabilities');
		
		//Case 3: Updating capabilites in shop installation
		//echo "\n\n<strong>Queries for shop</strong>:\n\n";
		$currDB ="{$dbPrefix}shop";
		frc_sync_user_cap($allCaps, $currDB, 'shop_capabilities');
		
		//Case 4: Updating capabilites in classified installation
		//echo "\n\n<strong>Queries for classified</strong>:\n\n";
		$currDB ="{$dbPrefix}classified";
		frc_sync_user_cap($allCaps, $currDB, 'classified_capabilities');
	}
	
	function frc_sync_user_cap($allCaps, $currDB, $metaKey){	//print_r($allCaps);die;
		global $wpdb, $dbPrefix;
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		foreach($allCaps as $userID => $userCapabilities){
			$delVal = 'subscriber';
			$capArr = unserialize($userCapabilities);
			if(count($capArr) > 1 && array_key_exists($delVal, $capArr )){
				unset($capArr[$delVal]);	//remove subscriber
			}
			
			//Case: if user have role premium + customer; than customer roles needs to be changed to premium
			//Case added by Glen (Refer doc: 13-06 Mohit Notes.txt): If user in /shop is user role of customer (they purchased product in /shop) and they upgrade to Foundation Membership or Premium Membership, we will need their user role to change to premium. So for this case, I will not keep user role customer and premium at same time. I am deleting the customer role for this case.
			if( in_array('premium', $capArr) && in_array('customer', $capArr) ){
				$delVal = 'customer';
				//if($key = array_search($delVal, $capArr) !== false){unset($capArr[$key]);}
				if(array_key_exists($delVal, $capArr )){
					unset($capArr[$delVal]);	//remove customer
				}
			}
			//print_r($capArr);die;
			$capArr = serialize($capArr);
			$fieldArr = array(
							 'user_id' => $userID,
							 'meta_key' => $metaKey,
							 'meta_value' => $userCapabilities,
							 'processed_by_script' => 1,
							 'modified' =>'0000-00-00 00:00:00'
							 );
			
			echo "\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$metaKey' LIMIT 1";
			$capObj = $wpdb->get_results($query);
            if(count($capObj) >= 1){
				$capMetaID = $capObj[0]->umeta_id;
				echo "\nupdate";
                $wpdb->update( 
								'gwr_usermeta', 
								$fieldArr,
                                array( 'meta_key' => $metaKey, 'user_id' => $userID )
                            );
                echo "\n<br/>Update Qry:".$updateQry = $wpdb->last_query;
            }else{
                //insert record
                echo "\ninsert";   //$dataArr['meta_key'] = $metaKey;$dataArr['modified'] = $intervalTime;
                $capStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
                echo "\n<br/>Cap Qry:".$insertQry = $wpdb->last_query;
                if($capStatus){
                    echo "\n<br/>Capabilities added for metakey: $metaKey installation successfully for user $userID";
                }
            }
        }
	}
	
	function modified_user_caps($timeInterval, $dbPrefix){
		global $wpdb;
		$userIDs = array();
		
		//1 Get users whose caps modified in directory installation
		$currDB ="{$dbPrefix}directory";
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		
		$query = "SELECT `user_id`,  TIMEDIFF( NOW( ) - INTERVAL $timeInterval, `modified` ) as time_intveral FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` = 'gwr_capabilities') ORDER BY `umeta_id` DESC"; //For grabbing capabilities of /directory installation modified in last time interval
		echo "\Directory: \n$query\n";
		$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
		foreach($resultObj as $userObj){
			if(!in_array($userObj->user_id, $userIDs)){
				$userIDs[] = $userObj->user_id;
			}
		}
		
		//2 Get users whose caps modified in premium installation
		$currDB ="{$dbPrefix}premium";
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		$query = "SELECT `user_id`,  TIMEDIFF( NOW( ) - INTERVAL $timeInterval, `modified` ) as time_intveral FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` = 'wp_capabilities') ORDER BY `umeta_id` DESC"; //For grabbing capabilities of /directory installation modified in last time interval
		echo "\Premium: \n$query\n";
		$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
		foreach($resultObj as $userObj){
			if(!in_array($userObj->user_id, $userIDs)){
				$userIDs[] = $userObj->user_id;
			}
		}
		
		//3 Get users whose caps modified in shop installation
		$currDB ="{$dbPrefix}shop";
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		$query = "SELECT `user_id`,  TIMEDIFF( NOW( ) - INTERVAL $timeInterval, `modified` ) as time_intveral FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` = 'shop_capabilities') ORDER BY `umeta_id` DESC";
		//For grabbing capabilities of /directory installation modified in last time interval
		echo "\nSHOP:\n $query\n";
		$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
		foreach($resultObj as $userObj){
			if(!in_array($userObj->user_id, $userIDs)){
				$userIDs[] = $userObj->user_id;
			}
		}
		
		//4 Get users whose caps modified in classified installation
		$currDB ="{$dbPrefix}classified";
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		$query = "SELECT `user_id`,  TIMEDIFF( NOW( ) - INTERVAL $timeInterval, `modified` ) as time_intveral FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND (`meta_key` = 'classified_capabilities') ORDER BY `umeta_id` DESC";
		//For grabbing capabilities of /directory installation modified in last time interval
		echo "\nCLASSIFIED: \n$query\n";
		$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
		foreach($resultObj as $userObj){
			if(!in_array($userObj->user_id, $userIDs)){
				$userIDs[] = $userObj->user_id;
			}
		}
		return $userIDs;
	}
	
	function frc_unserialize_n_merge($serStr1, $serStr2){
		$arr = array();						//echo "\n1:  $serStr1 \n $serStr2\n";
		$arr1 = unserialize($serStr1);
		$arr2 = unserialize($serStr2);		//echo "\n>>>>>>\n";print_r($arr1);print_r($arr2);echo "\n<<<<<<<";
		if(is_array($arr1) && is_array($arr2)){
			foreach($arr1 as $role => $roleVal){if(!array_key_exists($arr, $role)){$arr[$role] = $roleVal;}}
			foreach($arr2 as $role => $roleVal){if(!array_key_exists($arr, $role)){$arr[$role] = $roleVal;}}
			return serialize($arr);
		}elseif(is_array($arr1)) return $serStr1;
		elseif(is_array($arr2)) return $serStr2;
		else return serialize(array('subscriber' => 1)); //Return blank array
	}
	 
	die('script executed successfully');
?>