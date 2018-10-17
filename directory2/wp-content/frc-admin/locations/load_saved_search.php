<?php
	require_once("../../wp-config.php");
	require_once('../../wp-load.php');
    global $wpdb;
	header('Content-Type: application/json');
	if(isset($_REQUEST['load_preference'])){
		$userID = (int)$_REQUEST['id'];
		$query = "SELECT * FROM `saved_preferences` WHERE `user_id` = $userID ORDER BY `id` DESC LIMIT 1";
		$rsltObjs = $wpdb->get_results($query);
		if(count($rsltObjs) >= 1){
			echo json_encode($rsltObjs[0]);
		}else{
			echo json_encode(array());
		}
	}
	die;
?>