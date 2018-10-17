<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $version = $_REQUEST['version'];
	$query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version'";
    $versions = $wpdb->get_results($query);
	if(count($versions) >= 0){
		$searlize_data = $versions[0]->option_value;
		$table = 'gwr_options';
		$status = $wpdb->update( $table, array('option_value' => $searlize_data), array('option_name' => 'ait_item_extension_administrator_options'));
		if($status === false){
			wp_redirect("item-extension-versions.php?status=0&version=$version");
		}else{
			wp_redirect("item-extension-versions.php?status=1&version=$version");
		}
	}else{
		wp_redirect("item-extension-versions.php?status=2&version=$version");
	}
?>