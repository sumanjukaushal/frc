<html>
    <head>
		<title>FRC: Grab Dirctory Plus Item Extension Features</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
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
?>
    <h3 style='padding-left:20px;'>&raquo;&nbsp;Grab Item Extension Features</h3>
    <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
        <form method='post' >
            <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
                <tr bgcolor="#C6D5FD">
					<th colspan="1" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Update Item Extension Fields:</th>
					<td style='text-align: right;padding-right: 5px;'><a href='item-extension-versions.php'>View Archieve</a></td>
				</tr>
                <tr><td colspan='2' style='padding-left:20px;'><span>Do you want to import item extension fields from default <strong>Admin</strong> Package of Directory Plus theme?</span></td></tr>
				
                <tr>
                    <td colspan='2' style='padding-left:20px;'><input type="radio" name="yes" value="true" checked/> Yes &nbsp;<input type="radio" name="yes" value="false" /> No</td>
                </tr>
				<tr><td colspan='2' style='padding-left:20px;'>Memo: <textarea rows="2" cols="20" placeholder="Enter Notes" name="memo" required></textarea></td></tr>
                <tr>
					<td colspan="2" style="text-align:center; padding-top:3px; padding-bottom:3px;"><input type="submit" name="submit" value="Submit" />
					</td>
				</tr>
            </table>
        </form>
    </div>
<?php
	if(isset($_REQUEST['submit']) && isset($_REQUEST['yes'])){
		$memo = $_REQUEST['memo'];
		$fieldName = "ait_item_extension_administrator_options";
		$versionFieldName = "frc_{$fieldName}";
		$query = "SELECT * FROM `gwr_options` WHERE `option_name` = '$fieldName'";
		$results = $wpdb->get_results($query);
		if(count($results) >= 1){
			foreach($results as $result => $value){
				$data = $value->option_value;
			}
			$qry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE '{$versionFieldName}_v%' ORDER BY `id` DESC LIMIT 1";
			$featureRS = $wpdb->get_results($qry);
			if(count($featureRS) >= 1){
				$newVersion = str_replace($versionFieldName."_v","",$featureRS[0]->option_name)+1;
				$nameOptName = "{$versionFieldName}_v{$newVersion}";
			}else{
				//if there is no record with any version, create first version
				$nameOptName = "{$versionFieldName}_v1";
			}
			$wpdb->query("UPDATE `frc_admin_options` SET `option_name` = '$nameOptName' WHERE `option_name` = '$versionFieldName'");
			$wpdb->insert( 'frc_admin_options',
								  array( 'option_name' => $versionFieldName, 'option_value' => $data, 'memo' => $memo),
								  array(   '%s', '%s'));
			$selQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE '{$versionFieldName}_%' ORDER BY `id` DESC LIMIT 1";
			$selOBJ = $wpdb->get_results($selQry);
			echo "<p style='padding-left:20px;color:red'>Import Successful and old version saved here:<strong>".$selOBJ[0]->option_name."</strong></p>";
		}else{
			echo "No record found for grabbing item extension features";
		}
	}
?>
</body>
</html>