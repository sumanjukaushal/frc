<?php
	//calling url: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_usermeta_multiple.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
	$timeInterval = '10 MINUTE';
	$timeInterval = '2 MINUTE';
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
	$dbPrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
	
	//1. Get metas of user last modified in directory installation
	$currDB ="{$dbPrefix}directory";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	echo $query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `meta_key` not like '%_capabilities' ORDER BY `umeta_id` DESC"; 
	$metaArr = array();
	$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
	foreach($resultObj as $metaObj){
		$metaArr[] = array('user_id' => $metaObj->user_id, 'meta_key' => $metaObj->meta_key, 'meta_value' => $metaObj->meta_value);
	}
	
	if(count($metaArr)){
		//1. updating metas in premium installation
		$currDB ="{$dbPrefix}premium";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}shop";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}classified";
		update_metas($metaArr, $currDB);
	}
	
	//2. Get metas of user last modified in premium installation
	$currDB ="{$dbPrefix}premium";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	echo "<br/><br/>".$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `meta_key` not like '%_capabilities' ORDER BY `umeta_id` DESC"; 
	$metaArr = array();
	$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
	foreach($resultObj as $metaObj){
		$metaArr[] = array('user_id' => $metaObj->user_id, 'meta_key' => $metaObj->meta_key, 'meta_value' => $metaObj->meta_value);
	}
	
	if(count($metaArr)){
		//1. updating metas in directory installation
		$currDB ="{$dbPrefix}directory";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}shop";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}classified";
		update_metas($metaArr, $currDB);
	}
	
	//3. Get metas of user last modified in shop installation
	$currDB ="{$dbPrefix}shop";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	echo "<br/><br/>".$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `meta_key` not like '%_capabilities' ORDER BY `umeta_id` DESC"; 
	$metaArr = array();
	$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
	foreach($resultObj as $metaObj){
		$metaArr[] = array('user_id' => $metaObj->user_id, 'meta_key' => $metaObj->meta_key, 'meta_value' => $metaObj->meta_value);
	}
	
	if(count($metaArr)){
		//1. updating metas in directory installation
		$currDB ="{$dbPrefix}directory";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}premium";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}classified";
		update_metas($metaArr, $currDB);
	}
	
	//4. Get metas of user last modified in classified installation
	$currDB ="{$dbPrefix}classified";
	if($dbPrefix == 'frcconz_'){
		$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
	}else{
		$wpdb->select($currDB);wp_cache_flush();
	}
	echo "<br/><br/>".$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL $timeInterval )) AND `meta_key` not like '%_capabilities' ORDER BY `umeta_id` DESC"; 
	$metaArr = array();
	$resultObj = $wpdb->get_results($query);	//echo "\nDirectory: \n";print_r($resultObj);
	foreach($resultObj as $metaObj){
		$metaArr[] = array('user_id' => $metaObj->user_id, 'meta_key' => $metaObj->meta_key, 'meta_value' => $metaObj->meta_value);
	}
	
	if(count($metaArr)){
		//1. updating metas in directory installation
		$currDB ="{$dbPrefix}directory";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}premium";
		update_metas($metaArr, $currDB);
		//2. updating metas in shop installation
		$currDB ="{$dbPrefix}shop";
		update_metas($metaArr, $currDB);
	}
	
	function update_metas($metaArr, $currDB){
		global $wpdb, $dbPrefix;
		if($dbPrefix == 'frcconz_'){
			$wpdb = new wpdb(DB_USER,DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		}else{
			$wpdb->select($currDB);wp_cache_flush();
		}
		
		foreach($metaArr as $sngArr){
			extract($sngArr);
			$query = "SELECT `umeta_id` FROM `gwr_usermeta` WHERE `user_id` = '$user_id' AND `meta_key` = '$meta_key'";
			$subMetaObjs = $wpdb->get_results($query);
			if(count($subMetaObjs) >= 1){
				//update record
				$fieldArr = array('meta_value' => $meta_value, 'processed_by_script' => 0,'modified' =>'0000-00-00 00:00:00');
				$wpdb->update( 
								'gwr_usermeta', 
								$fieldArr,
                                array( 'meta_key' => $meta_key, 'user_id' => $user_id )
                            );
                echo "\n<br/>Update Qry:".$updateQry = $wpdb->last_query;
			}else{
				$fieldArr = array(
								  'user_id' => $user_id,
								  'meta_key' => $meta_key,
								  'meta_value' => $meta_value,
								  'processed_by_script' => 0,
								  'modified' =>'0000-00-00 00:00:00'
								  );
				$capStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
                echo "\n<br/>Cap Qry:".$insertQry = $wpdb->last_query;
			}
		}
	}
	die('<br/><br/>script executed successfully');
?>