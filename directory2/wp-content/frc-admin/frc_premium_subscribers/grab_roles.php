<?php
	require_once("../../wp-config.php");
	require_once('../../wp-load.php');
	global $wpdb;
	if(array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])){
		$FRC_Roles = array();
		$currDB = "freerang_directory";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$userID = (int)$_REQUEST['user_id'];
		$query = "SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' AND `user_id` = $userID";
		$prmRolesQry = $wpdb->get_results($query);
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['directory'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		$currDB = "freerang_classified";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'classified_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['classified'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		$currDB = "freerang_shop";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'shop_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['shop'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		
		$currDB = "freerang_premium";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
		$prmRolesQry = $wpdb->get_results("SELECT `meta_value` FROM `gwr_usermeta` WHERE `meta_key` = 'wp_capabilities' AND `user_id` = $userID");
		if(count($prmRolesQry) >= 1){
			$FRC_Roles['wp'] =  unserialize($prmRolesQry[0]->meta_value);
		}
		$rolesNlevels = json_encode($FRC_Roles);
		echo $rolesNlevels;
	}else{
		echo json_encode(array());
	}
?>