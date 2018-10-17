<?php
	require_once("../../../wp-config.php");
	require_once('../../../wp-load.php');
	global $wpdb;
	$pos = strpos(WP_CONTENT_DIR, 'freerang');
	$prodServer = false;
	if($pos === false){
	    //it is development server
	    $prodServer = false;
	}else{
	    //it is production server
	    $prodServer = true;
	}
	
	if(array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])){
		$FRC_Roles = array();
		$currDB = $prodServer ? "freerang_directory" : "frcconz_directory";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$userID = (int)$_REQUEST['user_id'];
		$query = "SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' AND `user_id` = $userID";
		$prmRolesQry = $wpdb->get_results($query);
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['directory'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		
		$currDB = $prodServer ? "freerang_classified" : "frcconz_classified";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'classified_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['classified'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		$currDB = $prodServer ? "freerang_shop" : "frcconz_shop";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'shop_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['shop'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		$currDB = $prodServer ? "freerang_premium" : "frcconz_premium";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'wp_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['wp'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		$rolesNlevels = serialize($FRC_Roles);
		echo $rolesNlevels;
	}else{
		echo serialize(array());
	}
?>