<?php require('config.php');ob_start();?> 
<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/frc2/wp-content/frc_lvl_n_roles/copy_role_capabilities.php -->
	<head>
		<title>FRC Copy Role Capabilities</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
<?php require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');?>
	<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Role Capabilities</h3>
	<div style="margin-left: 20px;font-size: 15px;">
		<a href="<?php echo WP_CONTENT_URL;?>/frc-admin/frc_lvl_n_roles/manage_role_capabilities.php">Manage Role n Capabilities</a> &raquo; Copy Role Capablities
	</div>
<?php
    $query = "SELECT * FROM `$tableName` WHERE `option_name` = '$fieldName'";
    $totRecs = $wpdb->query($query);
    $items = $wpdb->get_results( $query, OBJECT );
    $roles = unserialize($items[0]->option_value);
    $roleToBeModified = $_REQUEST['role'];
    $excludeArr = array('administrator','editor','author','contributor','subscriber');	//echo'<pre>';print_r($_REQUEST);DIE;
	
	if(empty($roleToBeModified) || !array_key_exists($roleToBeModified, $roles)){
		ob_end_clean();
		header("Location:manage_role_capabilities.php");
	}
	if(isset($_REQUEST['submit'])){ //echo'<pre>';print_r($_REQUEST);
		$role2BeModified = $_REQUEST['role_2_be_modified'];
		$new_role = $_REQUEST['role'];
		$roles[$new_role]['capabilities'] = $roles[$role2BeModified]['capabilities'];
		$desRole = $roles[$new_role]['name'];
		$srcRole = $roles[$role2BeModified]['name'];
		$updated_roles = serialize($roles);	//print_r($updated_roles);die;    
		$status = $wpdb->update($tableName, array('option_value' => $updated_roles), array( 'option_name' => $fieldName ), array( '%s' ));
		if($status == 0 or $status){
			ob_end_clean();
			wp_redirect("manage_role_capabilities.php?transStatus=1&destRole=$desRole&srcRole=$srcRole");    
		}else{
			wp_redirect("manage_role_capabilities.php?transStatus=0&destRole=$desRole&srcRole=$srcRole"); 
		}
	}else{
		$rowStr = "";
		$roleNameToBeModified = "";
		$counter=0;
		foreach($roles as $role => $capabilityArr){
			$rowCss = ($counter++ % 2) ? 'info':'active';
			$roleTitle = $capabilityArr['name'];
			if($role == $roleToBeModified){ $roleNameToBeModified = $capabilityArr['name'];continue;}
			if(empty($role)){continue;}
			if(!in_array($role, $excludeArr)){
				$rowStr.= "
				<tr class='$rowCss'>
					<td>$roleTitle</td>
					<td>$role</td>
					<td><input type='radio' name='role' value='$role'></td>
				</tr>";
			}
		}
?>
		<div class='table' style='padding-left:20px;padding-top:5px;'>
			<form action="" method="post">
				<table class='table' style='width:80%'>
					<tr bgcolor='#C6D5FD' style='line-height: 35px;min-height: 35px;margin-top:5px;'>
						<td style='text-align:left' colspan='3'>&nbsp;&raquo;&nbsp;Copy Capabilities of <strong><?php echo $roleNameToBeModified;?></strong> role (Code: <?php echo $roleToBeModified;?>) to: [Table:<b><?php echo $tableName;?></b>, Field: <b><?php echo $fieldName;?></b>])</td>
					</tr>
					<tr><th>Role Title</th><th>Role ID</th><th></th></tr>
					<?php echo $rowStr;?>
					<input type="hidden" name="role_2_be_modified" value="<?php echo $roleToBeModified ?>">
					<tr>
							<td colspan='3' align='center'>
									<input style='color:#333; background-color:#dcdcdc;'type='submit' name='submit' value='submit' />&nbsp;&nbsp;
									<button style='color:#333; background-color:#dcdcdc;' type="cancel" onclick="window.history.go(-1); return false;">Cancel</button>
							</td></tr>
				</table>
			</form>
		</div>
		<br/><br/><br/><br/>
    </body>
</html>
<?php } ?>