<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<title>FRC: Grab Roles</title>
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	require("inner_navigation.php");
    $themeObj = wp_get_theme();
    if(!in_array(trim($themeObj->Name),$themeAllowed)){
        echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
        die;
    }
?>
<h3 style='padding-left:20px;'>&raquo;&nbsp;FRC Utility: Grab Roles</h3>
<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
    <form method='post' >
        <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
            <tr bgcolor="#C6D5FD">
				<th colspan="1" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Grab Roles:</th>
				<td style='text-align: right;padding-right: 5px;'><a href='role-versions.php'>View Archieve</a></th>
			</tr>
            <tr><td colspan='2' style='padding-left:20px;'><span>Do you want to import fresh roles n their attributes from <strong>Directory Plus</strong> theme? This is required only if you did any changes in roles or their attributes recently.</span></td></tr>
            <tr><td colspan='2' style='padding-left:20px;'>Note: we are saving this information in archieve. If we need to restore older state of roles and their attributes, we can use this utility.</td></tr>
            <tr>
                <td colspan='2' style='padding-left:20px;'><input type="radio" name="yes" value="true" checked/> Yes &nbsp;<input type="radio" name="yes" value="false" /> No</td>
            </tr>
			<tr><td colspan='2' style='padding-left:20px;'>Memo: <textarea rows="2" cols="20" placeholder="Enter Notes" name="memo" required></textarea></td></tr>
            <tr>
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
		$query = "SELECT * FROM `gwr_options` WHERE `option_name` = 'gwr_user_roles'";
		$results = $wpdb->get_results($query); //echo'<pre>';print_r($results);die;
		if(count($results) >= 1){
            $data = $results[0]->option_value;
			$qry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_gwr_user_roles_v%' ORDER BY `id` DESC LIMIT 1";
			$featureRS = $wpdb->get_results($qry); //echo'<pre>';print_r($featureRS);die;
            
			if(count($featureRS) >= 1){
				$newVersion = str_replace("frc_gwr_user_roles_v","",$featureRS[0]->option_name)+1;
				$nameOptName = "frc_gwr_user_roles_v{$newVersion}";
                $wpdb->query("UPDATE `frc_admin_options` SET `option_name` = '$nameOptName' WHERE `option_name` = 'frc_gwr_user_roles'");
			}else{
				//if there is no record with any version, create first version
				$nameOptName = "frc_gwr_user_roles_v1";
                $wpdb->insert( 'frc_admin_options',
								  array( 'option_name' => $nameOptName, 'option_value' => $data, 'memo' => $memo),
								  array(   '%s', '%s'));
			}
			$wpdb->insert( 'frc_admin_options',
								  array( 'option_name' => 'frc_gwr_user_roles', 'option_value' => $data, 'memo' => $memo),
								  array(   '%s', '%s'));
			$selQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_gwr_user_roles_v%' ORDER BY `id` DESC LIMIT 1";
			$selOBJ = $wpdb->get_results($selQry); //echo'<pre>';print_r($selOBJ);die;
			echo "<p style='padding-left:20px;color:red'>Roles grabbed successfully and old version is saved here:<strong>".$selOBJ[0]->option_name."</strong></p>";
            //echo "<pre>";print_r($nameOptName);die;
		}else{
            echo "<p style='padding-left:20px;color:red'>No record found for grabbing role</p>";
		}
	}
?>
</body>
</html>