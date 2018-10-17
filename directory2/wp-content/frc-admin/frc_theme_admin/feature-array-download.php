<?php
	require_once("../../../wp-config.php");
	require_once('../../../wp-load.php');
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	$themeObj = wp_get_theme();	//print_r($themeObj);
	$parentTemplate = $themeObj->Template; //directory2
	$parentTheme = "";
	if(!empty($parentTemplate)){
		$style_parent_theme = wp_get_theme(get_template());
		$parentTheme = $style_parent_theme->get( 'Name' ); //Directory Plus
	}
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$flashMsg = "";
	$featureQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'frc_ait_item_extension_administrator_options'";//frc_features
	$featureObj = $wpdb->get_results($featureQry);
	
	if(count($featureObj) >= 1){
		$featureArrays = unserialize($featureObj[0]->option_value);			//echo "<pre>";print_r($featureArrays);
		$formRow = "";
		foreach($featureArrays as $key => $featureArray){
			extract($featureArray);
			$feature = $featureArray['label']['en_US'];
			$uid =  $featureArray['uid'];
			$feature = $featureArray['label']['en_US'];
			//print_r($featureArray);
			$image[$uid] = $feature;
			if(isset($image[$uid]) && $image[$uid] !=  'Additional Information') { 
				$image_name = str_replace(array('&'),'', $feature);//'Available',
				$image_name = lcfirst($image_name);
				$image_name = str_replace (" ", "", $image_name);
				$image2[$uid]  =  $image_name.'.png'  ;
			} 
		}
	}
	$filename = 'feature-array.php';
	
	$path = getcwd();
	$path = $path."/".$filename;
	if (file_exists($path) ) {
		array_to_csv_download($image2,$filename);
	}
	function array_to_csv_download($array, $filename , $delimiter=";") {
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		$f = fopen('php://output', 'w');
		echo "<?php\n\t\$featureArr = array(";
		foreach($array as $key => $val){
		    echo "\n\t\t'$key' => '$val',";
		}
		echo "\n\t);\n?>";
		$out = ob_get_clean();
		ob_end_flush();
		print_r($out);die;
	}
?>