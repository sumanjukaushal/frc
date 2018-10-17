<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC User Role and Level Management</title>
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
	        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	        $themeObj = wp_get_theme();
	        if(!in_array(trim($themeObj->Name), $themeAllowed)){
	            echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
	            die;
	        }
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Merge role from old theme to new theme</h3>
<?php
	$optionTbl = $wpdb->prefix."options";
	$roleOpt = $wpdb->prefix."user_roles";
	if(isset($_REQUEST['merge_role'])){
		if(is_array($_REQUEST['roles']) && !empty($_REQUEST['roles'])){
			$query = "SELECT * FROM `$optionTbl` WHERE `option_name` = '$roleOpt'";
			$roleOBJ = $wpdb->get_results($query);
			$queryOldRoles = "SELECT * FROM `frc_admin_options` WHERE `option_name`='gwr_user_roles_old_theme'";
			$resOBJ = $wpdb->get_results($queryOldRoles);
			if(count($roleOBJ) >= 1){
				$rolesArr = unserialize($roleOBJ[0]->option_value);
			}else{
				$rolesArr = array();
			}
			if(count($resOBJ) >= 1){
				$oldRolesArr = unserialize($resOBJ[0]->option_value);
				$mergedRole = array();
				foreach($oldRolesArr as $rolKey => $oldRol){
					if(array_key_exists($rolKey,$_REQUEST['roles'])){
						$mergedRole[]= $rolKey;
						if(!array_key_exists($rolKey,$rolesArr)){
							$rolesArr[$rolKey] = $oldRol;
						}
					}
				}
				$mergedRoles = implode(",",$mergedRole);
				$finalMergedRoles = serialize($rolesArr);
				$updateRoles = $wpdb->update($optionTbl, array('option_value'=>$finalMergedRoles), array('option_name'=> $roleOpt));
				if($updateRoles === false){
					echo"Error while merging.";
				}else{
					echo"Successfully Merged Old role : $mergedRoles in New roles.";
				}
			}
		}else{
			echo"Please Select Role";
		}
	}
	$query = "SELECT * FROM `$optionTbl` WHERE `option_name` = '$roleOpt'";
	$roleOBJ = $wpdb->get_results($query);
	if(count($roleOBJ) >= 1){
		$rolesArr = unserialize($roleOBJ[0]->option_value);
		$existingRole = "";
		$key = 0;
		foreach($rolesArr as $roleKey => $role){
			$bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
			$existingRole.="<tr style='background-color:$bgColor'><td>$role[name]($roleKey)</td></tr>";
			$key++;
		}
		$queryOldRoles = "SELECT * FROM `frc_admin_options` WHERE `option_name`='gwr_user_roles_old_theme'";
		$resOBJ = $wpdb->get_results($queryOldRoles);
		if(count($resOBJ) >= 1){
			$oldRolesArr = unserialize($resOBJ[0]->option_value);
			$oldRoles = "";
			$key1 = 0;
			foreach($oldRolesArr as $oldRoleKey => $oldRole){
				$bgColor = $key1 % 2 == 0 ? '#FFFFCC': '#D0D0D0';
				$oldRoles.="<tr style='background-color:$bgColor'><td>$oldRole[name]($oldRoleKey)</td><td style='font-size: 10px;'><input type='checkbox' name='roles[$oldRoleKey]' />Merge With Existing</td></tr>";
				$key1++;
			}
		}
		echo $formRow =<<<FORM_HTML
		<form>
			<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
				<tr bgcolor='#C6D5FD'><th>Existing Roles</th>
				<th>OLd Roles</th></tr>
				<tr><td>
					<table>
						$existingRole
					</table>
					</td><td>
					<table>
						$oldRoles
					</table>
				</td></tr>
				<tr><td></td><td><input type='submit' name='merge_role' value='Merge Roles' id='merge_role' /></tr>
			</table>
		</form>
FORM_HTML;
	}   
?>
    </body>
</html>
<script>
	$("#merge_role").click(function(){
		if( !confirm('Are you sure you want to merge old roles with new?')) {
			return false;
		}
	});
</script>