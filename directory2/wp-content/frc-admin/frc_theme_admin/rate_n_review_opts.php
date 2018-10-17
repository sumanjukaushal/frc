<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC User Role and Level Management</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
	    <?php
	        require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
	        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	        $themeObj = wp_get_theme();
	        if(!in_array(trim($themeObj->Name),$themeAllowed)){
	            echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
	            die;
	        }
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Rate n Review Options</h3>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$flashMsg = "";
	$uniqID = rand(11, 99).str_replace(".", "", uniqid("", true)).".".rand(11111111, 99999999);
	
	$featureQry = "SELECT * FROM `gwr_options` WHERE `option_name` = '_ait_directory2_theme_opts'"; //frc_features
	$featureObj = $wpdb->get_results($featureQry);
	$themeOptions = unserialize($featureObj[0]->option_value);
	$itemReviews = array(
						 'notifications' => 1,
						 'maxShownReviews' => 2,
						 'question1' => array('en_US' => 'Location'),
						 'question2' => array('en_US' => 'Facilities'),
						 'question3' => array('en_US' => 'Service'),
						 'question4' => array('en_US' => 'Price'),
						 //'question5' => array('en_US' => 'Food'),
						 'onlyRegistered' => 0,
						 'onlyRegisteredMessage' => array(
														  'en_US' => 'Only registered users can add a review',
														  'de_DE' => 'Only registered users can add a review',
														  ),
					);
	$themeOptions['itemReviews'] = $itemReviews;
	$status = $wpdb->update('gwr_options',
									array( 'option_value' => serialize($themeOptions)),
									array( 'option_name' => '_ait_directory2_theme_opts'), array( '%s' )
							);
	if($status !== false){
		echo "Record updated with following configuration for review:";
		echo "<pre>";print_r($itemReviews);echo "</pre>";
	}
?>
	</body></html>