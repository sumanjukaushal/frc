<!DOCTYPE html>
<html class="ie" lang="en">
	<head>
		<title>FRC Utility export item feature in Array</title>
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
		<h3 style='padding-left:20px;'>&raquo;&nbsp;FRC Utility: Serialize Features n Icons</h3>
		<h4 style='padding-left:20px;padding-top:5px; padding-bottom:5px;background-color:yellow; width:1000px;color:blue;'>*** This serialized data is for Google Map Marker. (Option: feature_n_icons, Table: frc_admin_options ***</h4>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$flashMsg = "";
	$featureQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'ait_item_extension_administrator_options'";//frc_features
	$featureObj = $wpdb->get_results($featureQry);
	$image2 = array();
	if(count($featureObj) >= 1){
		$featureArrays = unserialize($featureObj[0]->option_value);
		$formRow = "";
		foreach($featureArrays as $key => $featureArray){
			extract($featureArray);
			$feature = $featureArray['label']['en_US'];
			$uid =  $featureArray['uid'];
			$feature = $featureArray['label']['en_US'];
			//print_r($featureArray);
			$image[$uid] = $feature;
			if(isset($image[$uid]) && $image[$uid] !=  'Additional Information') { 
				$image_name = str_replace('Available','', $feature);
				$image_name = lcfirst($image_name);
				$image_name = str_replace (" ", "", $image_name);
				$image2[$uid]  =  array($uid,$image_name.'.png',$feature) ;
			} 
		}
	}
?>
<body>
	<table width='90%'>
		<tr><td width='90%' ><?php
	echo serialize($image2);
?></td></tr>
		<tr><td style='height:50px;'>&nbsp;</td></tr>
	</table>

    </body>
</html>