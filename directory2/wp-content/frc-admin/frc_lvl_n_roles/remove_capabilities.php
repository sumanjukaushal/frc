<?php require('config.php');ob_start();?>
<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Remove Role Capabilities</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
<?php require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');?>
	<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Role Capabilities</h3>
	<div style="margin-left: 20px;font-size: 15px;">
		<a href="<?php echo WP_CONTENT_URL;?>/frc-admin/frc_lvl_n_roles/manage_role_capabilities.php">Manage Role n Capabilities</a> &raquo; Remove Role Capablities
	</div>
<?php
    //require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
    $edited_role = isset($_REQUEST['role']) ? $_REQUEST['role'] : '';
    $query = "SELECT * FROM `$tableName` WHERE `option_name` = '$fieldName'";
    $totRecs = $wpdb->query($query);
    $items = $wpdb->get_results( $query, OBJECT );
    $roleArr = $roles = unserialize($items[0]->option_value);
    if(!empty($roles) && array_key_exists($edited_role, $roleArr)){
        if(isset($_REQUEST['submit'])){
            foreach($_REQUEST as $key => $value){
                if($key == 'role' || $key == 'submit' || $key == 'role_edited'){
                    continue;
                }
                unset($roleArr[$edited_role]['capabilities'][$key]);
            }
            $modifiedCaps = serialize($roleArr);
			$status = $wpdb->update($tableName, array( 'option_value' => $modifiedCaps), array( 'option_name' => $fieldName), array( '%s' ));
			ob_end_clean();
			if($status == false){
				$roleRemoved = $_REQUEST['role_edited'];
				wp_redirect("manage_role_capabilities.php?removed_status=0&role=$roleRemoved");
			}else{
				$roleRemoved = $_REQUEST['role_edited'];
				wp_redirect("manage_role_capabilities.php?removed_status=1&role=$roleRemoved");
			}
            //echo'<pre>';print_r($_REQUEST);die;
        }else{
            $rowStr = "";
			if(array_key_exists($edited_role, $roles)){
				$roleCaps = $roles[$edited_role]['capabilities'];
				$roleTitle = $roles[$edited_role]['name'];
				$counter=0;
				foreach($roleCaps as $capebilty_name => $capebility_value){
					//$checked = $capebility_value == 1 ? "checked='checked'" : '';
					$rowCss = ($counter++ % 2) ? 'info':'active';
					$rowStr.="<tr class='$rowCss'><td>$capebilty_name</td><td><input type='checkbox' name='{$capebilty_name}' value='1' /></td></tr>";
				}
				echo $htmlForm =<<<HTML_FORM
				<div class='table' style='padding-left:20px;padding-top:5px;'>
					<form action="" method="post">
						<div style='padding-top:5px;'>&raquo;You are removing capabilities from <strong>$roleTitle</strong> Role [Table: <b>$tableName</b>, Field: <b>$fieldName</b>]</div>
						<table class='table' style='width:80%'>
							<thead>
								<tr><th>Capability</th><th></th></tr>
							</thead>
							<tbody>
								<tr><td colspan='2' align='center'><input style='color:#333; background-color:#dcdcdc;' type='submit' name='submit' value='Update Capabilities' />&nbsp;&nbsp; <button style='color:#333; background-color:#dcdcdc;' type="cancel" onclick="window.history.go(-1); return false;">Cancel</button></td></tr>
								{$rowStr}
								<tr><td colspan='2' align='center'><input style='color:#333; background-color:#dcdcdc;'type='submit' name='submit' value='Update Capabilities' />&nbsp;&nbsp; <button style='color:#333; background-color:#dcdcdc;' type="cancel" onclick="window.history.go(-1); return false;">Cancel</button></td></tr>
								<tr><td colspan='2'>&nbsp;&nbsp;</td></tr>
							</tbody>
						</table>
						<input type='hidden' name='role_edited' value='$edited_role' />
					</form>
				</div>
HTML_FORM;
			}else{
				ob_end_clean();
				wp_redirect("manage_role_capabilities.php?cap_status=-1");
			}
        }
    }else{
        echo $html =<<<HTML
        <div class='table' style='padding-left:20px;padding-top:5px;'>
        <table class='table' style='width:80%'>
            <thead>
                <tr bgcolor='#C6D5FD' style='line-height: 35px;min-height: 35px;margin-top:5px;'>
                    <th style='text-align:left'>&nbsp;&raquo;&nbsp;Manage Roles n Capabilities:</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style='color:red; padding-left:20px;'>Either table $tableName missing or no value found for $fieldName!!!</td></tr>
            </tbody>
        </table>
        </div>
HTML;
    }

?>