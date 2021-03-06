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
		<h3 style='padding-left:20px;'>&raquo;&nbsp;FRC Utility: Export item feature in an Array</h3>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$flashMsg = "";
	$featureQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'ait_item_extension_administrator_options'";//frc_features
	$featureObj = $wpdb->get_results($featureQry);
	
	if(count($featureObj) >= 1){
		$featureArrays = unserialize($featureObj[0]->option_value);
		echo "<pre>";print_r($featureArrays);die;
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
				$image2[]  =  array($uid,$image_name.'.png',$feature)  ;
			} 
		}
	}
	if (isset($_GET['file']) && basename($_GET['file']) == $_GET['file']) {
        $filename = $_GET['file'];
    }else{
		$filename = NULL;
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
		//header('Content-Type: application/csv');
		//header('Content-Disposition: attachment; filename="'.$filename.'";');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		ob_clean();
		ob_start();
		$f = fopen('php://output', 'w');
		echo "<?php\n\t\$featureArr = array(";
		foreach($array as $key => $val){
			echo "\n\t\t\t\tarray(";
			foreach($val as $sngkey=>$sngline){
				 echo $result =  "'".$sngline."',";
			}
			 echo " ),";
		}
		echo "\n\t);\n?>";
		$out = ob_get_clean();
		ob_end_flush();
		print_r($out);die;
	}   
	
?>
<a href="feature-array2.php?file=feature-array2.php">Download file</a>
<?php
   
	ob_start();
	echo "<pre>";
	echo "&lt;?php\n\t\$featureArr = array(";
	foreach($image2 as $key => $val){
	    echo "\n\t\t\t\tarray(";
		foreach($val as $sngkey=>$sngline){
				 echo $result =  "'".$sngline."',";
		}
		 echo " ),";
	}
	echo "\n\t);\n?>";
	echo "</pre>";
	echo $str = ob_get_clean();
	ob_flush();
?>
    </body>
</html>