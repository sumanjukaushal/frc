<?php
	/*
	Calling URL: http://freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_address_fields_cron.php
	1.	SELECT * FROM `gwr_usermeta` WHERE `user_id`=43719
	2.	SELECT * FROM `gwr_wlm_user_options` WHERE `user_id`=43719
	————————————————————————————————————————————————————
	|	Table 				|	Field				|
	————————————————————————————————————————————————————
	| gwr_wlm_user_options 	|	wlm_password_hint	| 
	| gwr_wlm_user_options 	|	wpm_useraddress		| {company, address1, address2, city, state, zip, country}
	——————————————————————————————————————————————————————————————————
	| gwr_usermeta			|	billing_first_name	|				|
	| gwr_usermeta			|	billing_last_name	|				|
	——————————————————————————————————————————————————————————————————
	| gwr_usermeta			|	billing_company		|	company		|
	| gwr_usermeta			|	billing_address_1	|	address1	|
	| gwr_usermeta			|	billing_address_2	|	address2	|
	| gwr_usermeta			|	billing_city		|	city		|
	| gwr_usermeta			|	billing_state		|	state		|
	| gwr_usermeta			|	billing_postcode	|	zip			|
	| gwr_usermeta			|	billing_country		|	country		|//in title case full name for wpm_useraddress so Australia
	——————————————————————————————————————————————————————————————————
	*/
	
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
	$timeInterval = '30 MINUTE';
	$timeInterval = '5 MINUTE';
	$dbPrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
	$currDB ="{$dbPrefix}directory";
	
	if($dbPrefix == 'frcconz_'){
		$premDB = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$resultObj = $premDB->get_results($query);
	}else{
		$wpdb->select($currDB);wp_cache_flush();
		$resultObj = $wpdb->get_results($query);
	}	
	$addressFldLbls = array(
							'billing_company' => 'company',
							'billing_address_1' => 'address1',
							'billing_address_2' => 'address2',
							'billing_city' => 'city',
							'billing_state' => 'state',
							'billing_postcode' => 'zip',
							'billing_country' => 'country',
							);
	
    $intQry = "SELECT NOW( ) - INTERVAL $timeInterval  as timeBeforeInterval";//- INTERVAL $timeInterval
    $intResObj = $wpdb->get_results($intQry);
    $intervalTime = $intResObj[0]->timeBeforeInterval;
	$multipleDBs = array(
						 'directory' => 'gwr_wlm_user_options',
						 'premium' => 'wp_wlm_user_options',
						 'classified' => 'classified_wlm_user_options',
						 'shop' => 'shop_wlm_user_options'
						);
	$addressFlds = array('billing_company', 'billing_address_1', 'billing_address_2','billing_city', 'billing_state', 'billing_postcode', 'billing_country');
	
	//1 Updating address fields in /directory
	$currDB ="{$dbPrefix}directory";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	$dbTimeAgo = $timeAgo  = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-2, date("Y"));
	$dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
	$dbTimeNow = gmdate("Y-m-d H:i:s", mktime());
	echo $usrQry = "SELECT `user_id`, `modified` FROM `frc_premium_users` WHERE (`frc_premium_users`.`modified` > '$dbTimeAgo') ORDER BY `ID` DESC";
	//AND `frc_premium_users`.`modified` <= '$dbTimeNow'
	$usrObjs = $wpdb->get_results($usrQry);
	$dataArrys = array();
	foreach($usrObjs as $key => $usrObj){
		$userID = $usrObj->user_id;
		$usrObj->user_id. "--".$usrObj->modified;
		$addressFldsStr = "'".implode("','", $addressFlds)."'";
		$usrMetaQry = "SELECT * FROM `gwr_usermeta` WHERE `meta_key` IN($addressFldsStr) AND `user_id` = $userID";//echo "\n<br/>$key: ".
		$usrMetaObjs = $wpdb->get_results($usrMetaQry);
		if(count($usrMetaObjs) >= 1){
			//echo "<pre>";print_r($usrMetaObjs);echo "</pre>";
			$wpm_useraddress = array();
			foreach($usrMetaObjs as $usrMetaObj){
				$metaKey = $addressFldLbls[$usrMetaObj->meta_key];
				if($usrMetaObj->meta_key == billing_country){
					Switch($usrMetaObj->meta_value){
						Case 'AU':$usrMetaObj->meta_value = 'Australia';break;
						Case 'IN':$usrMetaObj->meta_value = 'India';break;
					}
				}
				$wpm_useraddress[$metaKey] = $usrMetaObj->meta_value;
			}
			$serilizedAdd = serialize($wpm_useraddress);//echo "\n<br/>".$serilizedAdd;
			$optQry = "SELECT * FROM `gwr_wlm_user_options` WHERE `user_id` = $userID AND `option_name` = 'wpm_useraddress'"; //echo "\n<br/><br/>".
			$usrOptObj = $wpdb->get_results($optQry);
			$dataArr = array(
								 'user_id' => $userID,
								 'option_name' => 'wpm_useraddress',
								 'option_value' => $serilizedAdd,
								 );
			$dataArrys[$userID] = $dataArr;
			if(count($usrOptObj) >= 1){
				$optQry[0];
			}else{
				 $insQry = $wpdb->insert('gwr_wlm_user_options', $dataArr);
				 echo "\n<br/><br/>Insert Qry($currDB):".$insQry = $wpdb->last_query;
			}
		}
	}
	echo "\n\n<br/>updating for /premium ----";
	//2 Updating address fields in /premium
	$currDB ="{$dbPrefix}premium";
	$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	//print_r($dataArrys);
	foreach($dataArrys as $userID => $userAddData){
		$usrMetaQry = "SELECT * FROM `gwr_usermeta` WHERE `meta_key` IN($addressFldsStr) AND `user_id` = $userID";
		$usrMetaObjs = $wpdb->get_results($usrMetaQry);
		if(count($usrMetaObjs) >= 1){
			//echo "\n<br/>".$usrMetaQry;
			$wpm_useraddress = array();
			foreach($usrMetaObjs as $usrMetaObj){
				$metaKey = $addressFldLbls[$usrMetaObj->meta_key];
				if($usrMetaObj->meta_key == billing_country){
					Switch($usrMetaObj->meta_value){
						Case 'AU':$usrMetaObj->meta_value = 'Australia';break;
						Case 'IN':$usrMetaObj->meta_value = 'India';break;
					}
				}
				$wpm_useraddress[$metaKey] = $usrMetaObj->meta_value;
			}
			$serilizedAdd = serialize($wpm_useraddress);//echo "\n<br/><br/>".
			$optQry = "SELECT * FROM `wp_wlm_user_options` WHERE `user_id` = $userID AND `option_name` = 'wpm_useraddress' LIMIT 1";
			$usrOptObj = $wpdb->get_results($optQry);
			$dataArr = array(
								 'user_id' => $userID,
								 'option_name' => 'wpm_useraddress',
								 'option_value' => $serilizedAdd,
								 );
			$dataArrys[$userID] = $dataArr;
			if(count($usrOptObj) >= 1){
				$optID = $usrOptObj[0]->ID;
				if(empty($usrOptObj[0]->option_value)){
					$updateQry = $wpdb->update('wp_wlm_user_options', $dataArr,  array( 'ID' => $optID, 'user_id' => $userID ));
					echo "\n<br/><br/>Update Qry($currDB):".$insQry = $wpdb->last_query;
				}
			}else{
				$insQry = $wpdb->insert('wp_wlm_user_options', $dataArr);
				echo "\n<br/><br/>Insert Qry($currDB):".$insQry = $wpdb->last_query;
			}
		}
	 
	}
	die('script executed successfully');
?>