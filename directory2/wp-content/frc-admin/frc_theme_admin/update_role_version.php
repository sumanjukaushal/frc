<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    $optTable = $wpdb->prefix.'options';
    $version = $_REQUEST['version'];
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version'";
    $versions = $wpdb->get_results($query); //echo'<pre>';print_r($versions);die;
    $data = $versions[0]->option_value;
    $status = $wpdb->update($optTable, array( 'option_value' => $data), array( 'option_name' => 'gwr_user_roles'), array( '%s' ));
    
    if($status === false){
        $version = $_REQUEST['version'];
        wp_redirect("role-versions.php?status=0&version=$version");
    }else{
        $version = $_REQUEST['version'];
        wp_redirect("role-versions.php?status=1&version=$version");
    }
    die;
?>
