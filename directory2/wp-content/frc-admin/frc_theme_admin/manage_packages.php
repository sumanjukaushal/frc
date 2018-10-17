<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Manage Package for Directory Plus</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
	        }
			$currentTheme = wp_get_theme();
			if(!in_array(trim($themeObj->Name),$themeAllowed)){
				$themeAllowed = array('Directory Plus', 'Directory Plus Child Theme');
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
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Package Features for theme ( <span style='background-color: yellow;'><?php echo $currentTheme;?></span> )</h3>
		<h3 style='padding-left:20px;'>After save: This script will keep package data in safezone</h3>
		<h5 style='padding-left:20px;color:blue;'>Same moment it is adding/updating packages, for directory plus theme in the admin panel.This script is also adding/updating respectives roles for each package.</h5>
		<h6 style='padding-left:20px;color:blue;'>*Note:For saving data; please do minimium one change in any row.</h6>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
	$optionsTbl = $wpdb->prefix."options";
    $allRoles = $wp_roles->roles;
	$flashMsg = $featureOptions = "";
	
	if(isset($_REQUEST['Submit'])){
		$slugArr = $_REQUEST['slug'];
		$pkgArr = array();
		foreach($slugArr as $key => $slug){
			$itemsFeatured = isset($_REQUEST['itemsFeatured']) && array_key_exists($key, $_REQUEST['itemsFeatured']) ? 1 : 0;
			$name = isset($_REQUEST['name']) && array_key_exists($key, $_REQUEST['name']) ? $_REQUEST['name'][$key] : '';
			$adminApprove = isset($_REQUEST['adminApprove']) && array_key_exists($key, $_REQUEST['adminApprove']) ? 1 : 0;
			$slug = isset($_REQUEST['slug']) && array_key_exists($key, $_REQUEST['slug']) ? $_REQUEST['slug'][$key] : '';
			$desc = isset($_REQUEST['desc']) && array_key_exists($key, $_REQUEST['desc']) ? $_REQUEST['desc'][$key] : '';
			$maxItems = isset($_REQUEST['maxItems']) && array_key_exists($key, $_REQUEST['maxItems']) ? $_REQUEST['maxItems'][$key] : '';
			$expirationLimit = isset($_REQUEST['expirationLimit']) && array_key_exists($key, $_REQUEST['expirationLimit']) ? $_REQUEST['expirationLimit'][$key] : '';
			$price = isset($_REQUEST['price']) && array_key_exists($key, $_REQUEST['price']) ? $_REQUEST['price'][$key] : '';
			
			$capabilityEditor = isset($_REQUEST['capabilityEditor']) && array_key_exists($key, $_REQUEST['capabilityEditor']) ? 1 : 0;
			$capabilityExcerpt = isset($_REQUEST['capabilityExcerpt']) && array_key_exists($key, $_REQUEST['capabilityExcerpt']) ? 1 : 0;
			$capabilityImage = isset($_REQUEST['capabilityImage']) && array_key_exists($key, $_REQUEST['capabilityImage']) ? 1 : 0;
			$capabilityComments = isset($_REQUEST['capabilityComments']) && array_key_exists($key, $_REQUEST['capabilityComments']) ? 1 : 0;
			
			$capabilityAddress = isset($_REQUEST['capabilityAddress']) && array_key_exists($key, $_REQUEST['capabilityAddress']) ? 1 : 0;
			$capabilityTelephone = isset($_REQUEST['capabilityTelephone']) && array_key_exists($key, $_REQUEST['capabilityTelephone']) ? 1 : 0;
			$capabilityEmail = isset($_REQUEST['capabilityEmail']) && array_key_exists($key, $_REQUEST['capabilityEmail']) ? 1 : 0;
			$capabilityWeb = isset($_REQUEST['capabilityWeb']) && array_key_exists($key, $_REQUEST['capabilityWeb']) ? 1 : 0;
			
			$capabilityOpeningHours = isset($_REQUEST['capabilityOpeningHours']) && array_key_exists($key, $_REQUEST['capabilityOpeningHours']) ? 1 : 0;
			$capabilitySocialIcons = isset($_REQUEST['capabilitySocialIcons']) && array_key_exists($key, $_REQUEST['capabilitySocialIcons']) ? 1 : 0;
			$capabilityGallery = isset($_REQUEST['capabilityGallery']) && array_key_exists($key, $_REQUEST['capabilityGallery']) ? 1 : 0;
			$capabilityFeatures = isset($_REQUEST['capabilityFeatures']) && array_key_exists($key, $_REQUEST['capabilityFeatures']) ? 1 : 0;
			
			$capabilityYoastseo = isset($_REQUEST['capabilityYoastseo']) && array_key_exists($key, $_REQUEST['capabilityYoastseo']) ? 1 : 0;
			$adminApproveEdit = isset($_REQUEST['adminApproveEdit']) && array_key_exists($key, $_REQUEST['adminApproveEdit']) ? 1 : 0;
			$capabilityMedia = isset($_REQUEST['capabilityMedia']) && array_key_exists($key, $_REQUEST['capabilityMedia']) ? 1 : 0;
			$capabilityHeaderType = isset($_REQUEST['capabilityHeaderType']) && array_key_exists($key, $_REQUEST['capabilityHeaderType']) ? 1 : 0;
			
			$reqPkgArr = array(
								'itemsFeatured' => $itemsFeatured, //Missing
								'name' => $name,
								'adminApprove' => $adminApprove,
								'slug' => $slug,
								'desc' => $desc,
								'maxItems' => $maxItems,
								'expirationLimit' => $expirationLimit,
								'price' => $price,
								'capabilityEditor' => $capabilityEditor,
								'capabilityExcerpt' => $capabilityExcerpt,
								'capabilityImage' => $capabilityImage,
								'capabilityComments' => $capabilityComments,
								'capabilityAddress' => $capabilityAddress,
								'capabilityTelephone' => $capabilityTelephone,
								'capabilityEmail' => $capabilityEmail,
								'capabilityWeb' => $capabilityWeb,
								'capabilityOpeningHours' => $capabilityOpeningHours,
								'capabilitySocialIcons' => $capabilitySocialIcons,
								'capabilityGallery' => $capabilityGallery,
								'capabilityFeatures' => $capabilityFeatures,
								'capabilityYoastseo' => $capabilityYoastseo,
								'adminApproveEdit' => $adminApproveEdit,
								'capabilityMedia' => $capabilityMedia,
								'capabilityHeaderType' => $capabilityHeaderType,
							  );
			$pkgArr[] = $reqPkgArr;
		}
		$pkgStr = serialize($pkgArr);
		$updated = $wpdb->update( 'frc_admin_options',
								 array('option_value' => $pkgStr),
								 array('option_name' => 'frc_package_types') );
		if($updated){
			$themeOptionSubQry = "SELECT * FROM `$optionsTbl` WHERE `option_name` = '$packageOptionName'";
			$themeOptObj = $wpdb->get_results($themeOptionSubQry);					//echo "<br/>".$wpdb->last_query;
			if(count($themeOptObj) >= 1){
				$themeOptsArr = unserialize($themeOptObj[0]->option_value);
				$themeOptsArr['packages']['packageTypes'] = $pkgArr;
				
				$updated1 = $wpdb->update( $optionsTbl,
								 array('option_value' => serialize($themeOptsArr)),
								 array('option_name' => $packageOptionName) );		//echo "<br/>".$wpdb->last_query;
				if($updated1){
					$flashMsg1 = "(For Theme $themeObj->Name)";
				}
			}
			$flashMsg = "Packages added/updated successfully $flashMsg1!";
		}else{
		    $flashMsg = "!!!Nothing Updated!!!";
		}
	}
	
	array('general', 'header', 'footer', 'typography', 'breadcrumbs', '@widgetAreasAndSidebars', 'google','social', 'items', 'item', 'packages','payments', 'customCss', 'adminBranding', 'administrator', 'megamenu');
	
	$themeOptionQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'frc_package_types'";
	//$themeOptionQry = "SELECT * FROM `gwr_options` WHERE `option_name` = '_ait_directory2_theme_opts'";
	$themeOptionObj = $wpdb->get_results($themeOptionQry);
	
	if(count($themeOptionObj) >= 1){
		$themeOptsArr = unserialize($themeOptionObj[0]->option_value);
		//echo "---".serialize($themeOptsArr['packages']['packageTypes']);die;
		$formRowStr = "";
		foreach($themeOptsArr as $key => $themeOptArr){
			extract($themeOptArr);
			$adminApprove = $adminApprove ? "checked = 'checked'" : "";
			$itemsFeatured = $itemsFeatured ? "checked = 'checked'" : "";
			
			$capabilityEditor = $capabilityEditor ? "checked = 'checked'" : "";
			$capabilityExcerpt = $capabilityExcerpt ? "checked = 'checked'" : "";
			$capabilityImage = $capabilityImage ? "checked = 'checked'" : "";
			$capabilityComments = $capabilityComments ? "checked = 'checked'" : "";
			
			$capabilityAddress = $capabilityAddress ? "checked = 'checked'" : "";
			$capabilityTelephone = $capabilityTelephone ? "checked = 'checked'" : "";
			$capabilityEmail = $capabilityEmail ? "checked = 'checked'" : "";
			$capabilityWeb = $capabilityWeb ? "checked = 'checked'" : "";
			
			$capabilityOpeningHours = $capabilityOpeningHours ? "checked = 'checked'" : "";
			$capabilitySocialIcons = $capabilitySocialIcons ? "checked = 'checked'" : "";
			$capabilityGallery = $capabilityGallery ? "checked = 'checked'" : "";
			$capabilityYoastseo = $capabilityYoastseo ? "checked = 'checked'" : "";
			
			$adminApproveEdit = $adminApproveEdit ? "checked = 'checked'" : "";
			$capabilityMedia = $capabilityMedia ? "checked = 'checked'" : "";
			$capabilityHeaderType = $capabilityHeaderType ? "checked = 'checked'" : "";
			$capabilityFeatures = $capabilityFeatures ? "checked = 'checked'" : "";
			
			$bgColor = $key % 2 ? 'style="background-color:#FFFFCC"' : 'style="background-color:#D0D0D0"';
			$formRowStr.= <<<FORM_STR
			\n<tr $bgColor>
				<td>$name</td>
				<td><input type='checkbox' name='adminApprove[$key]' value='1' $adminApprove /></td>
				<td><input type='checkbox' name='itemsFeatured[$key]' value='1' $itemsFeatured /></td>
				
				<td><input style='width:70px;' type='text' name='maxItems[$key]' value='$maxItems' /></td>
				<td><input style='width:70px;' type='text' name='expirationLimit[$key]' value='$expirationLimit' /></td>
				<td><input style='width:70px;' type='text' name='price[$key]' value='$price' /></td>
				
				<td><input type='checkbox' name='capabilityEditor[$key]' 		value='1' $capabilityEditor /></td>
				<td><input type='checkbox' name='capabilityExcerpt[$key]' 		value='1' $capabilityExcerpt /></td>
				<td><input type='checkbox' name='capabilityImage[$key]' 		value='1' $capabilityImage /></td>
				<td><input type='checkbox' name='capabilityComments[$key]' 		value='1' $capabilityComments /></td>
				<td><input type='checkbox' name='capabilityAddress[$key]' 		value='1' $capabilityAddress /></td>
				<td><input type='checkbox' name='capabilityTelephone[$key]' 	value='1' $capabilityTelephone /></td>
				<td><input type='checkbox' name='capabilityEmail[$key]' 		value='1' $capabilityEmail /></td>
				<td><input type='checkbox' name='capabilityWeb[$key]' 			value='1' $capabilityWeb /></td>
				<td><input type='checkbox' name='capabilityOpeningHours[$key]' 	value='1' $capabilityOpeningHours /></td>
				<td><input type='checkbox' name='capabilitySocialIcons[$key]' 	value='1' $capabilitySocialIcons /></td>
				<td><input type='checkbox' name='capabilityGallery[$key]' 		value='1' $capabilityGallery /></td>
				<td><input type='checkbox' name='capabilityFeatures[$key]' 		value='1' $capabilityFeatures /></td>
				<td><input type='checkbox' name='capabilityYoastseo[$key]' 		value='1' $capabilityYoastseo /></td>
				<td><input type='checkbox' name='adminApproveEdit[$key]' 		value='1' $adminApproveEdit /></td>
				<td><input type='checkbox' name='capabilityMedia[$key]' 		value='1' $capabilityMedia /></td>
				<td><input type='checkbox' name='capabilityHeaderType[$key]' 	value='1' $capabilityHeaderType /></td>
				
				<input type='hidden' name='name[$key]' value='$name' />
				<input type='hidden' name='slug[$key]' value='$slug' />
				<input type='hidden' name='desc[$key]' value='$desc' />
			</tr>
FORM_STR;
		}
		
		$headRow = "<tr>
						<th>Pkg Name</th>
						<th>Admin Approve</th>
						<th>Featured</th>
						
						<th>Max Items</th>
						<th>Exp Lmt</th>
						<th>Price</th>
						
						<th>Cont <br/>Editor</th>
						<th>Excerpt</th>
						<th>Image</th>
						<th>Comments</th>
						<th>Addresss</th>
						<th>Phone</th>
						<th>Email</th>
						<th>Web</th>
						<th>Opening Hrs</th>
						<th>Social Icons</th>
						<th>Gallery</th>
						<th>Features</th>
						<th>SEO</th>
						<th>Admin<br/>Approve</th>
						<th>Cont <br/>Media</th>
						<th>Header Type</th>
						<!--th>Action <br/>[Check All <input type='checkbox' id='checkAll'/> Check all]</th--></tr>";
		$submitRow = '<tr>
						<td colspan="22" style="text-align:center; padding-top:3px; padding-bottom:3px;">
							<input type="submit" name="Submit" value="Save Package Attributes">
					</td></tr>';
	}else{
		$formRowStr.="<tr><td colspan='22'>Table: <b>frc_admin_options</b> might be missing or option <b>frc_package_types</b> might be absent in table <b>frc_admin_options</b>!</td></tr>";
	}
	echo $featureForm = <<<FEA_FORM
	<div style='text-align:center;color:red'>$flashMsg</div>
	<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
		<form method='post'>
			<table class="table-bordered table-hover" style="width:95%; padding-top:5px;">
				<thead>
					<tr bgcolor="#C6D5FD">
						<th colspan="22" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Packages Options</th>
					</tr>
					$headRow
				</thead>
				<tbody>
					$formRowStr
					$submitRow
				</tbody>
			</table>
		</form>
	</div>		
    </body>
FEA_FORM;
?>
</html>

<script type='text/javascript'>
$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});
</script>