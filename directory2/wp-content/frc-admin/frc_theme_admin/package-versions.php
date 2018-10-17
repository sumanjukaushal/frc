<html>
    <head>
        <title>FRC Directory Plus Package Version</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	require("inner_package_navigation.php");
    $themeObj = wp_get_theme();
    if(!in_array(trim($themeObj->Name),$themeAllowed)){
        echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
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
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_ait_directory2_theme_opts%' ORDER BY `id` ASC ";
    $versions = $wpdb->get_results($query);     //echo'<pre>';print_r($versions);die;
    $count = 0;
    $rowStr = "";
    if(array_key_exists('status',$_REQUEST)){   //echo'<pre>';print_r($_REQUEST);die;
        $version = $_REQUEST['version'];
        $status = $_REQUEST['status'];
        if($status == 0){
            $message = "<strong>$version</strong> could not be updated on orignal version";
        }else{
            $message = "Packages are restored from version <strong>$version</strong> to Theme ($themeObj->Name) successfully";
        }
    }
    
    foreach($versions as $version => $value){
		$memo = $value->memo;
		if($memo == ''){
			$memo = "--";
		}
        $vrsn = $value->option_name;
        if(strpos($vrsn,'_v')===false){
                continue;
        }
        
        $bgColor = $version % 2 == 0 ? '#edd6be': '#dac292';
        $count++;
        $modDate = date('d M Y,g:i a', strtotime($value->modified));
        $ver = str_replace("frc_ait_directory2_theme_opts_","",$value->option_name);
        $detailLink = "view_package_version.php?version=$value->option_name";
        $rowStr .="
        <tr>
            <td style='padding-left:5px;'>$count</td>
            <td style='padding-left:5px;'>$modDate</td>
            <td style='padding-left:5px;'>Version :<a href='update_package_version.php?version=$value->option_name' alt=$vrsn title=$vrsn id='confirm_restore'>{$ver}</a>&nbsp;&nbsp;&nbsp;Memo: $memo&nbsp;&nbsp;&nbsp;<a href='$detailLink' name='view_detail' />View Details</a></td>
        </tr>";
    }
    
    echo $htmlBlock = "
    <div style='width:80%; text-align: center;color: red;'>$message</div>
    <div class='table-responsive' style='padding-left:20px;padding-top:5px;'>
        <table class='table-bordered table-hover' style='width:80%; padding-top:5px;' style='width:80%'>
            <tr bgcolor='#C6D5FD'>
                <th colspan='4' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;»&nbsp;Package Archieve:</th>
            </tr>
            <tr style='background-color:lightsteelblue'>
                <td style='line-height: 35px;min-height: 35px;margin-top:5px;'>Sr. No</td>
                <td>Version Date</td>
                <td>Restore Item features to <strong>Directory theme plus</strong> From:</td>
            </tr>
            $rowStr
        </table>
    </div><br/><br/>";
?>
</body>
</html>
<script>
	$("a#confirm_restore").click(function() {
		var version = ($(this).text());
		if (!confirm('Do you want to restore '+version+' version')) {
			return false;
		}
	});
</script>