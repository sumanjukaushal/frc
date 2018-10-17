<html>
    <head>
        <title>FRC: Item Extension Feature Versions</title>
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
	require("inner_features_navigation.php");
    $themeObj = wp_get_theme();
    if(!in_array(trim($themeObj->Name),$themeAllowed)){
        echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
        die;
    }
    $fieldName = "ait_item_extension_administrator_options";
	$versionFieldName = "frc_{$fieldName}_";
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE '{$versionFieldName}%' ORDER BY `id` ASC ";
    $versions = $wpdb->get_results($query);
   
    $count = 0;
    $rowStr = "";
    if(isset($_REQUEST['status'])){
        $version = $_REQUEST['version'];
        $status = $_REQUEST['status'];
        if($status == 0){
            $flashMsg = "AIT Extension feature could not be restored from version <strong>$version</strong> of backup. Please try later!";
        }elseif($status == 1){
            $flashMsg = "AIT Extension feature are successfully restored from version <strong>$version</strong> of backup";
        }else{
            $flashMsg = "AIT Extension feature version <strong>$version</strong> is missing";
        }
    }
    foreach($versions as $version => $value){
		$memo = $value->memo;
		if($memo == ''){
			$memo = "--";
		}
        $bgColor = $version % 2 == 0 ? '#edd6be': '#dac292';
        $count++;
        $modDate = date('d-M-Y g:i a', strtotime($value->modified));
        $newVersion = str_replace($versionFieldName,"",$value->option_name);
        $fullVersion = $value->option_name;
        $link = "restore_feature_version.php?version=$fullVersion";
		$detailLink = "view_item_extension_version.php?version=$fullVersion";
        $rowStr .="
                    <tr>
                        <td style='padding-left:5px;'>$count</td>
                        <td style='padding-left:5px;'>$modDate</td>
                        <td style='padding-left:5px;'>Version : <a href='$link' alt='$fullVersion' title='$fullVersion' id='confirm_restore'>{$newVersion}</a>&nbsp;&nbsp;&nbsp;Memo: $memo&nbsp;&nbsp;&nbsp;<a href='$detailLink' name='view_detail' />View Details</a></td>
                    </tr>";
    }
    echo $htmlBlock = 
            "<h3 style='padding-left:20px;'>&raquo;&nbsp;FRC Utility: Restore Item Extension Features</h3>
            <div class='table-responsive' style='padding-left:20px;padding-top:5px;'>
                <div style='padding-left:20px;color:red'>$flashMsg</div>
                <table class='table-bordered table-hover' style='width:80%; padding-top:5px;margin-bottom: 30px;' style='width:80%'>
                    <tr bgcolor='#C6D5FD'>
                        <th colspan='4' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;»&nbsp;Versions Details:</th>
                    </tr>
                    <tr style='background-color:lightsteelblue'>
                        <td style='line-height: 35px;min-height: 35px;margin-top:5px;'>Sr. No</td>

                        <td>Version Date</td>
                        <td>Restore Item Extension features from feature archieve version:</td>
                    </tr>
                    $rowStr
                </table>
            </div>";
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