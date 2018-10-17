<?php
	require_once("../../wp-config.php");
	require_once('../../wp-load.php');
    global $wpdb;
	if(isset($_REQUEST['save_preference'])){
		$category = "";
		if(isset($_REQUEST['category']) && !empty($_REQUEST['category'])){
			$category = implode(",", $_REQUEST['category']);
		}
		
		$locRadius = "";
		if(isset($_REQUEST['loc_radius']) && !empty($_REQUEST['loc_radius'])){
			$locRadius = (int)$_REQUEST['loc_radius'];
		}
		
		$location = "";
		if(isset($_REQUEST['location']) && !empty($_REQUEST['location'])){
			$location = implode(",", $_REQUEST['location']);
		}
		
		$feature = "";
		if(isset($_REQUEST['feature']) && !empty($_REQUEST['feature'])){
			$feature = implode(",", $_REQUEST['feature']);
		}
		$id = 0;
		if(isset($_REQUEST['id'])){
			$id = (int)$_REQUEST['id'];
		}
		$customLocation = "";
		if(!empty($_REQUEST['search'])){$customLocation = trim($_REQUEST['search']);}
		$wpdb->insert( 'saved_preferences',
								  array(
										'user_id' => $id,
										'locations' => $location,
										'custom_location' => $customLocation,
										'radius' => $locRadius,
										'categories' => $category,
										'features' => $feature
										),
								  array(   '%d', '%s', '%s', '%s', '%s','%s'));
		die();
	}
?>