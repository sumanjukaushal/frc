<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
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
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Item Extension Features (Step 1)</h3>
<?php
    include('role_array.php');
    include('role_array_plus.php');
    $roleArrPlus['premium'] = $roleArr['premium'];
    $roleArrPlus['shop_manager'] = $roleArr['shop_manager'];
    $roleArrPlus['UT2_administrator'] = $roleArr['UT2_administrator'];
    $roleArrPlus['admin_role'] = $roleArr['admin_role'];
    $roleArrPlus['admin_role2'] = $roleArr['admin_role2'];
    $roleArrPlus['admin_role3'] = $roleArr['admin_role3'];
    $roleArrPlus['cqpimuploader'] = $roleArr['cqpimuploader'];
    $roleArrPlus['UT2_administrator'] = $roleArr['UT2_administrator'];
    $roleArrPlus['customer'] = $roleArr['customer'];
    $roles = array_keys($roleArr);
    $plusRols = array_keys($roleArrPlus);
    //echo "<pre>";print_r($plusRols);print_r($roles);
    $table = $wpdb->prefix.'options';
    $searlize_data = serialize($roleArrPlus);
    $status = $wpdb->update( $table, array('option_value' => $searlize_data), array('option_name' => 'gwr_user_roles'));
    if($status !== false){
        echo "Role updated successfully!";
    }else{
        echo "Failed to update records!";
    }
?>
    </body>
</html>