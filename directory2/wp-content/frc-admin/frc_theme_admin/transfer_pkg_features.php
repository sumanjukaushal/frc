<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC User Role and Level Management</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.1/jquery.js' /></script>
		<!--https://cdnjs.com/libraries/jquery/-->
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
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Transfer Feature 2 Pkgs (Step 2)</h3>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
	$optionsTbl = $wpdb->prefix."options";
    $allRoles = $wp_roles->roles;
	$flashMsg = $featureOptions = "";
	
	if(isset($_REQUEST['Submit'])){
		$pkgs = $_REQUEST['pkg'];
		$featureQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'frc_ait_item_extension_administrator_options'"; //frc_features
		$featureObj = $wpdb->get_results($featureQry);
		if(count($featureObj) >= 1){
			$featureOptions = $featureObj[0]->option_value;
		}
		if(!empty($featureOptions)){
			$counter = 0;
			foreach($pkgs as $pkg => $selected){
				$pkgOptionName = "ait_item_extension_cityguide_{$pkg}_options";
				if($pkg == 'administrator')$pkgOptionName = "ait_item_extension_{$pkg}_options";
				$selectQry = "SELECT * FROM `$optionsTbl` WHERE `option_name` = '$pkgOptionName'";//echo "<br/>".$selectQry
				$pkgOptsObj = $wpdb->get_results($selectQry);
				if(count($pkgOptsObj) >= 1){//update
					$updated = $wpdb->update( $optionsTbl, array('option_value' => $featureOptions), array('option_name' => $pkgOptionName) );
					if($updated)$counter++;//echo "<br/>if:". $wpdb->last_query;
				}else{//add echo "<br/>if";
					$updated = $wpdb->insert( $optionsTbl, array('option_value' => $featureOptions, 'option_name' => $pkgOptionName, 'autoload' => 'yes'));
					if($updated)$counter++;
				}
			}
			$flashMsg = "Features for $counter packages added/updated successfully!";
		}
	}
	
	$themeOptionQry = "SELECT * FROM `$optionsTbl` WHERE `option_name` = '$packageOptionName'";
	$themeOptionObj = $wpdb->get_results($themeOptionQry);
	
	if(count($themeOptionObj) >= 1){
		$themeOptArr = unserialize($themeOptionObj[0]->option_value);
		$pkgTypes = $themeOptArr['packages']['packageTypes'];
		//echo "<pre>";$pkgTypes;die;
		
		$formRow = "";
		$pkgTypes[] = array('name' => 'Admin', 'desc' => 'For Admin', 'slug' => 'administrator');
		foreach($pkgTypes as $key => $pkgType){
			extract($pkgType);
			$pkgOptionName = "ait_item_extension_cityguide_{$slug}_options";
			if($slug == 'administrator')$pkgOptionName = "ait_item_extension_{$slug}_options";
			$bgColor = $key % 2 ? 'style="background-color:#FFFFCC"' : 'style="background-color:#D0D0D0"';
			$formRow.=<<<FORM_ROW
				<tr $bgColor>
					<td>$name</td>
					<td>$desc</td>
					<td>$pkgOptionName</td>
					<td><input type='checkbox' name="pkg[$slug]" /> Transfer</td>
					<!--/hidden fields-->
				</tr>
FORM_ROW;
		}
		$headRow = "<tr><th>Pkg Name</th><th>Description</th><th>Pkg Option Name</th><th>Action [Check All <input type='checkbox' id='checkAll'/> Check all]</th></tr>";
		$submitRow = '<tr>
						<td colspan="5" style="text-align:center; padding-top:3px; padding-bottom:3px;">
							<input type="submit" name="Submit" value="Transfer Features to Pkgs">
					</td></tr>';
	}
	echo $featureForm = <<<FEA_FORM
	<div style='text-align:center;color:red'>$flashMsg</div>
	<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
		<form method='post'>
			<table class="table-bordered table-hover" style="width:80%; padding-top:5px;">
				<thead>
					<tr bgcolor="#C6D5FD">
						<th colspan="5" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;{$themeObj->Name} Packages:</th>
					</tr>
					$headRow
				</thead>
				<tbody>
					$formRow
					$submitRow
				</tbody>
			</table>
		</form>
	</div>		
    </body>
FEA_FORM;
?>
</html>
<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>
