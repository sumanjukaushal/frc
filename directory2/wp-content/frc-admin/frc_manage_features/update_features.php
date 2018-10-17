<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Manage AIT ITEM Features</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="jquery.blockUI.js"></script>
	</head>
	<body>
<?php
    //Script URL: http://freerangecamping.co.nz/directory/wp-content/manage_features/update_features.php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	if(isOldTheme()){
		require_once('features-array.php');
	}else{
		require_once('new-theme-features-array.php');
	}
	
	//Start: Initialise variables
	
	
	$currentTheme = wp_get_theme();
	/*if($currentTheme != 'Directory'){
	    echo "This script is only for old Directory theme. This script should be updated for <strong>Directory Plus theme.</stron>";
	    die;
	}*/
	$postType = isOldTheme() ? 'ait-dir-item' : 'ait-item';
	$metaType = isOldTheme() ? '_ait-dir-item' : '_ait-item_item-data';
	$count_posts = wp_count_posts( $postType );
	define('SCRIPT_PATH', __FILE__);
	
	if(strpos(SCRIPT_PATH, '/public_html/directory/')){
		$actionURL = WP_CONTENT_URL.DS."frc-admin/frc_manage_features/update_features.php";
	}else{
		$actionURL = WP_CONTENT_URL.DS."frc-admin/frc_manage_features/update_features.php";
	}//echo $actionURL;
    if(isOldTheme()){
        include(WP_CONTENT_DIR.DS."frc-admin/frc_manage_features/includes/old-theme-features.php");
    }else{
        include(WP_CONTENT_DIR.DS."frc-admin/frc_manage_features/includes/plus-theme-features.php");
    }
?>

    </body>
</html>