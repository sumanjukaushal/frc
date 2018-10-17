<?php
	/*
	Calling URL: https://freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_pwd_hint.php
	*/
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
	$timeInterval = '30 MINUTE';
	$timeInterval = '5 MINUTE';
	$dbPrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
	$currDB ="{$dbPrefix}directory";
	$premDB = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	$resultObj = $premDB->get_results($query);
	
    $intQry = "SELECT NOW( ) - INTERVAL $timeInterval  as timeBeforeInterval";//- INTERVAL $timeInterval
    $intResObj = $wpdb->get_results($intQry);
    $intervalTime = $intResObj[0]->timeBeforeInterval;
	
	$optQry = "SELECT * FROM `gwr_wlm_user_options` WHERE `option_name`='wlm_password_hint' AND `option_value` <> '' LIMIT 40000, 5000";
	$usrObjs = $wpdb->get_results($optQry);
	
	foreach($usrObjs as $userObj){
		$userID = $userObj->user_id;
		$pwdHint = $userObj->option_value;
		$metaQry = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'frc_password_hint' order by `umeta_id` DESC LIMIT 1";
		//echo "\n<br/><br/>".$metaQry;
		$metaObjs = $wpdb->get_results($metaQry);
		if(count($metaObjs) >= 1){
			//update
		}else{
				$dataArr = array(
								 'user_id' => $userID,
								 'meta_key' => 'frc_password_hint',
								 'meta_value' => $pwdHint,
								 );
				 $insQry = $wpdb->insert('gwr_usermeta', $dataArr);
				 echo "\n<br/><br/>Insert Qry($currDB):".$insQry = $wpdb->last_query;
		}
	}
	die('script executed successfully');
?>