<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC User Role and Level Management</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
	    <?php
	        require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
	        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	        $themeObj = wp_get_theme();
			global $wpdb;
	        if(!in_array(trim($themeObj->Name), $themeAllowed)){
	            echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
	            die;
	        }
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Map User Roles of old theme to Package roles of Directory Plus theme</h3>
		<h5 style='padding-left:25px;'>&nbsp;&nbsp;(one time operation)</h5>
<?php
    /*
	 SELECT * FROM `gwr_options` where option_name='gwr_user_roles'
	 Free Listings :dirctory_1 => cityguide_63911c36
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_1%'
	 Business Listings: directory_2 => cityguide_26b2ed98
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_2%'
	 Campgrounds: directory_3 => cityguide_ba9d57c1
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_3%'
	 House Sitting: directory_4 => cityguide_5b656f57
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_4%'
	 Park Overs: directory_5 => cityguide_b0fc8201
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_5%'
	 Help Outs: directory_6 => cityguide_3969116f
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_6%'
	 Caravan Parks: directory_7 => cityguide_745e0aaf
	 Query: SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' and `meta_value` like '%directory_7%'
	 */
	$userMetaTable = $wpdb->prefix.'usermeta';
	$metaKey = "gwr_capabilities";
	$mapRoles = array(
						array('role_title' => 'Free Listings', 'dir_role' => 'directory_1', 'cityguide_role' => 'cityguide_63911c36'),
						array('role_title' => 'Business Listings', 'dir_role' => 'directory_2', 'cityguide_role' => 'cityguide_26b2ed98'),
						array('role_title' => 'Campgrounds', 'dir_role' => 'directory_3', 'cityguide_role' => 'cityguide_ba9d57c1'),
						array('role_title' => 'House Sitting', 'dir_role' => 'directory_4', 'cityguide_role' => 'cityguide_5b656f57'),
						
						array('role_title' => 'Park Overs', 'dir_role' => 'directory_5', 'cityguide_role' => 'cityguide_3969116f'), //cityguide_3969116f
						array('role_title' => 'Help Outs', 'dir_role' => 'directory_6', 'cityguide_role' => 'cityguide_b0fc8201'),	//cityguide_b0fc8201
						
						array('role_title' => 'Caravan Parks', 'dir_role' => 'directory_7', 'cityguide_role' => 'cityguide_745e0aaf'),
					  );
	//Start:Block for mapping directory roles to cityguide roles
	foreach($mapRoles as $roleArr){
		$roleTitle = $roleArr['role_title'];
		$dirRole = $roleArr['dir_role'];
		$cityGuidRole = $roleArr['cityguide_role'];
		$directoryRoleQry = "SELECT * FROM `$userMetaTable` WHERE `meta_key` = '$metaKey' and `meta_value` like '%$dirRole%'";
		$directoryRolesRes = $wpdb->get_results($directoryRoleQry);
		if(!empty($directoryRolesRes)){
			$errorCounts = $sucessCounts = array();
			foreach($directoryRolesRes as $keys => $directoryRole){
				$umetaId = $directoryRole->umeta_id;
				$metaValue = $directoryRole->meta_value;
				$userMetasArr = unserialize($metaValue);
				$newRoleNameArr = replaceRoles($dirRole, $cityGuidRole, $userMetasArr);
				$newUserMeta = serialize($newRoleNameArr);
				$dataArr = array("meta_value"=>$newUserMeta);
				$updateDir1Qry = $wpdb->update($userMetaTable, $dataArr, array('umeta_id'=> $umetaId, 'meta_key' => $metaKey));
				if($updateDir1Qry === false){
					$errorCounts[] = $umetaId;
				}else{
					$sucessCounts[] = $umetaId;
				}
			}
		}
		echo "Total Num of Records for <b>$roleTitle ($dirRole)</b> roles: ".sizeof($directoryRolesRes)."<br/>";
		if(count($sucessCounts)){
			$updatedIds = implode(",",$sucessCounts);
			echo "Total num of updated records for <b>$roleTitle ($dirRole)</b> :".sizeof($sucessCounts)."<br/>";
			//echo "Successfully Updated Role Name: $dirRole to $cityGuidRole of UmetaIds: $updatedIds<br/>";
		}
		if(count($errorCounts)){
			echo "Failed to update records for <b>$roleTitle ($dirRole)</b> :".sizeof($sucessCounts)."<br/>";
			$notUpdatedIds = implode(",",$errorCounts);
			echo"Error While Updating Role for meta ids: $notUpdatedIds<br/>";
		}
	}
	//END:Block for mapping directory roles to cityguide roles
	
	function replaceRoles($oldRole,$NewRole,$metaArray){
		$finalMetaArray = array();
		foreach($metaArray as $key => $metaValues){
			if($key == $oldRole){
				$newkey = $NewRole;
			}else{
				$newkey = $key;
			}
			$finalMetaArray[$newkey] = $metaValues;
		}
		return $finalMetaArray;
	}
?>
	<br/><br/><br/><br/><br/><br/>
    </body>
</html>