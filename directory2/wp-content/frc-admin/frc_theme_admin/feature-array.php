<?php ob_start();?>
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
	        $themeObj = wp_get_theme();	//print_r($themeObj);
			$parentTemplate = $themeObj->Template; //directory2
			$parentTheme = "";
			if(!empty($parentTemplate)){
				$style_parent_theme = wp_get_theme(get_template());
				$parentTheme = $style_parent_theme->get( 'Name' ); //Directory Plus
			}
			
	        if( !in_array(trim($themeObj->Name), $themeAllowed) || (!empty($parentTheme) && !in_array(trim($parentTheme), $themeAllowed)) ){
	            echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
	            die;
	        }
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;FRC Utility: Export item feature in an Array</h3>
<?php
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
	$filename = NULL;
	if (isset($_GET['file']) && basename($_GET['file']) == $_GET['file']) {
        $filename = $_GET['file'];
    }
    
	if (!$filename) {
		echo $err = 'Please Download file.';
    }else{
	    $path = getcwd();
        $path = $path."/".$filename;
        if (file_exists($path) ) {
			array_to_csv_download($image2,$filename);
		}else{
			echo $err = 'Sorry, the file you are requesting is unavailable.';
        }
    }
    
	function array_to_csv_download($array, $filename , $delimiter=";") {
		ob_end_clean();
		ob_end_flush(); // this does a clean as well
		flush();
		ob_start();
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
<a href="feature-array-download.php?file=feature-array.php" target='_blank'>Download file</a>
<?php
	ob_start();
	echo "<pre>";
	echo "&lt;?php\n\t\$featureArr = array(";
	foreach($image2 as $key => $val){
	    echo "\n\t\t'$key' => '$val',";
	}
	echo "\n\t);\n?>";
	echo "</pre>";
	echo $str = ob_get_clean();
	ob_flush();
?>
    </body>
</html>