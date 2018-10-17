<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    $optionsTbl = $wpdb->prefix."options";
    $themeObj = wp_get_theme();
    if(!isset($themeAllowed)){
        $themeAllowed = array('Directory Plus', 'Directory Plus Child Theme');
    }
    if(!in_array(trim($themeObj->Name), $themeAllowed)){
        $version = $_REQUEST['version'];
        wp_redirect("package-versions.php?status=0&version=$version");
        die;
    }else{
		switch(trim($themeObj->Name)){
			case 'Directory Plus':
				$packageOptionName = '_ait_directory2_theme_opts';
				break;
			case 'Directory Plus Child Theme':
				$packageOptionName = '_ait_directory2-child_theme_opts';
				break;
		}
	}
    $version = $_REQUEST['version'];
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE '$version%' ORDER BY `id` ASC ";
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version' ORDER BY `id` ASC "; //modified on 23rd Jan
    $versions = $wpdb->get_results($query); //echo'<pre>';print_r($versions);die;
    $data = $versions[0]->option_value;
    $dataArr = unserialize($data);
    $pkgDataStr = serialize($dataArr['packages']['packageTypes']);
    $status = $wpdb->update($optionsTbl, array( 'option_value' => $data), array( 'option_name' => $packageOptionName), array( '%s' ));
    
    if($status === false){
        $version = $_REQUEST['version'];
        wp_redirect("package-versions.php?status=0&version=$version");
    }else{
        $status = $wpdb->update('frc_admin_options', array( 'option_value' => $pkgDataStr), array( 'option_name' => 'frc_package_types'), array( '%s' ));
        $status = $wpdb->update('frc_admin_options', array( 'option_value' => $data), array( 'option_name' => 'frc_ait_directory2_theme_opts'), array( '%s' ));
        $version = $_REQUEST['version'];
        wp_redirect("package-versions.php?status=1&version=$version");
    }
    die;
?>
