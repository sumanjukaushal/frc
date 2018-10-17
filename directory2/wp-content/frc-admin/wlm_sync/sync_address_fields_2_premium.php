<?php
	/*
	Calling URL: http://freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_address_fields_2_premium.php
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
	$currDB ="{$dbPrefix}premium";
	
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
	
	
	
	//1 Get capabilties of user last modified from directory installation
	$currDB ="{$dbPrefix}premium";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	//25+642+460+930+1564+1498+1028+440+286 for directory
	//2+170+638+1162+168
	$usrQry = "SELECT `ID`, `user_email` FROM `gwr_users` ORDER BY `ID` DESC LIMIT 0, 30000";
	$usrObjs = $wpdb->get_results($usrQry);
	
	$addressFlds = array('billing_company', 'billing_address_1', 'billing_address_2','billing_city', 'billing_state', 'billing_postcode', 'billing_country');
	foreach($usrObjs as $userObj){
		$userID = $userObj->ID;
		$addressFldsStr = "'".implode("','", $addressFlds)."'";
		$usrMetaQry = "SELECT * FROM `gwr_usermeta` WHERE `meta_key` IN($addressFldsStr) AND `user_id` = $userID";
		$usrMetaObjs = $wpdb->get_results($usrMetaQry);
		if(count($usrMetaObjs) >= 1){
			echo "\n<br/>".$usrMetaQry;
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
			echo "\n<br/>".$serilizedAdd = serialize($wpm_useraddress);
			echo "\n<br/><br/>".$optQry = "SELECT * FROM `wp_wlm_user_options` WHERE `user_id` = $userID AND `option_name` = 'wpm_useraddress'";
			$usrOptObj = $wpdb->get_results($optQry);
			if(count($usrOptObj) >= 1){
				$optQry[0];
			}else{
				$dataArr = array(
								 'user_id' => $userID,
								 'option_name' => 'wpm_useraddress',
								 'option_value' => $serilizedAdd,
								 );
				 $insQry = $wpdb->insert('wp_wlm_user_options', $dataArr);
				 echo "\n<br/><br/>Insert Qry($currDB):".$insQry = $wpdb->last_query;
			}
		}else{
			continue;
		}
	}
	
	die("-----");
	
	 
	die('script executed successfully');
?>