<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/manage_roles_n_capabilities.php -->
	<head>
		<title>FRC Manage Role Capabilities</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
<?php
	require('config.php');
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	//echo "<pre>";print_r($_SERVER);echo "</pre>";
    $query = "SELECT * FROM `$tableName` WHERE `option_name` = '$fieldName'";
    $totRecs = $wpdb->query($query);
    $items = $wpdb->get_results( $query, OBJECT );	//echo'<pre>';print_r($items);die;
	
    if(array_key_exists('transfer',$_REQUEST)){
        $flashMsg = "<role_src> role capability are successfully transfered to role <role_target>";
    }elseif(array_key_exists('cap_status',$_REQUEST)){
		$role = $_REQUEST['role'];
		$roles = unserialize($items[0]->option_value);
		$roleName = $roles[$role]['name'];
		if($_REQUEST['cap_status'] == 0){
			$flashMsg = "Failed to update Capabilities for <strong>$roleName</strong>. Please try later!";
		}else{
			$flashMsg = "Capabilities for <strong>$roleName</strong> Role  are successfully updated";
		}
    }elseif(array_key_exists('transStatus', $_REQUEST)){
		$role = $_REQUEST['role'];
		$roles = unserialize($items[0]->option_value);
		$roleName = $roles[$role]['name'];
		extract($_REQUEST);
		if($_REQUEST['transStatus'] == 0){
			$flashMsg = "Failed to update Capabilities for <strong>$roleName</strong>. Please try later!";
		}else{
			$flashMsg = "Capabilities of Role <strong>$srcRole</strong> are successfully copied to role <strong>$destRole</strong>";
		}
	}elseif(array_key_exists('insert_status',$_REQUEST)){
		//echo'<pre>';print_r($_REQUEST);die;
		$role = $_REQUEST['role'];
		$roles = unserialize($items[0]->option_value);
		$roleName = $roles[$role]['name'];
		if($_REQUEST['insert_status'] == 0){
			$flashMsg = "Failed to add Capabilities for <strong>$roleName</strong>. Please try later!";
		}else{
			$flashMsg = "New capabilities are successfully added to role <strong>$roleName</strong>";
		}
	}elseif(array_key_exists('removed_status',$_REQUEST)){
		//echo'<pre>';print_r($_REQUEST);die;
		$role = $_REQUEST['role'];
		$roles = unserialize($items[0]->option_value);
		$roleName = $roles[$role]['name'];
		if($_REQUEST['removed_status'] == 0){
			$flashMsg = "Failed to remove Capabilities for <strong>$roleName</strong>. Please try later!";
		}else{
			$flashMsg = "Capabilities from <strong>$roleName</strong> role are successfully removed";
		}
	}
    
    $skipRoleArr = array('administrator','editor','author','contributor','subscriber');
    $roles = unserialize($items[0]->option_value);
    $rowStr = "";
	$counter=0;
    foreach($roles as $role => $capability){
        if(!in_array($role,$skipRoleArr)){
			if(empty($role))continue;
            $editCapLink = "edit_capabilities.php?role=$role";
            $transferCapLink = "copy_role_capabilities.php?role=$role";
			$addMorCapLink = "add_more_capabilities.php?role=$role";
			$removeCapLink = "remove_capabilities.php?role=$role";
			$rowCss = ($counter++ % 2) ? 'info':'active';
            $rowStr.="<tr class='$rowCss'>
						<td>$role</td><td>{$capability['name']}</td>
						<td>
							<a href='$editCapLink'>Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href='$transferCapLink' >Copy Capabilities</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href='$addMorCapLink' >Add More Capabilities</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href='$removeCapLink' >Remove Capabilities</a></td>
						</tr>";
		}
	}
?>
	<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Role Capabilities</h3>
	<div style='padding-left:20px;padding-top:5px;'>You are viewing role from table: <span style='background-color:yellow'><?php echo $tableName." [Field: <b>$fieldName</b>]";?></span></div>
	<div style='padding-left:20px;padding-top:5px;'>Roles excluded from this list are: <strong>administrator, editor, author, contributor and subscriber</strong></div>
	<div style='padding-left:20px;width:80%; text-align: center;color: red;'><?php echo $flashMsg;?></div>
    <div class='table' style='padding-left:20px;padding-top:5px;'>
        <table class='table' style='width:80%'>
            <tr><th>Role_Id</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php echo $rowStr;?>
        </table>
    </div>
	<br/><br/><br/>
    </body>
</html>