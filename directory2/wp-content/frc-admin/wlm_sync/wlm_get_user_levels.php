<!DOCTYPE html>
<html class="ie" lang="en">
	<head>
		<title>FRC User Role and Level Management</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage User Role and Levels:</h3>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/wlm_sync/wlm_get_user_levels.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
	require("wlmapiclass.php");
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$wishlistPlugin = 'wishlist-member/wpm.php';
	
	//Start: Block for getting roles for all subdirectories
	$FRC_Roles = array();
	$roleOptions = array(
							  'Directory' => array('gwr_options' , 'gwr_user_roles'), //Field option_name = 'gwr_user_roles'
							  'Shop' => array('shop_options' , 'shop_user_roles'),
							  'Premium' => array('wp_options' , 'wp_user_roles'),
							  'Classified' => array('classified_options' , 'classified_user_roles'),
							  );
	
	foreach($roleOptions as $subDir => $roleOption){
		$tabName = $roleOption[0];
		$fieldName = $roleOption[1];
		$query = "SELECT `option_value` FROM `$tabName` WHERE `option_name` = '$fieldName'";
		$resultObj = $wpdb->get_results($query);
        if(count($resultObj) >= 1){
			$tempArr = unserialize($resultObj[0]->option_value);
			foreach($tempArr as $roleID => $roleArr){
				$FRC_Roles[$subDir][$roleID] = $roleArr['name'];
			}
		}
	}
	
	$columns = 8;$ROLE_STR = "";
	foreach($FRC_Roles as $subDir => $frcRoles){
		$noOfRoles = count($frcRoles);
		$frcRoleChunks = array_chunk($frcRoles, $columns, true);
		$colStr = "<thead><tr bgcolor='#C6D5FD' style='line-height: 35px;min-height: 35px;margin-top:5px;'><th colspan='$columns' style='text-align:left'>&nbsp;&raquo;&nbsp;Roles for $subDir Installation are ($noOfRoles):</td></tr></thead>";
		$colStr.="<tbody>";
		$counter = 0;
		foreach($frcRoleChunks as $frcRoleChunk){
			$rowClass = $counter++ % 2 == 0 ? 'active' : 'info';
			$colStr.="<tr class='$rowClass'>";
			foreach($frcRoleChunk as $frcRoleID => $frcRoleName){
				$colStr.="<td style='width:150px;padding-left:3px;'><a title='$frcRoleID' alt='$frcRoleID'>$frcRoleName</a></td>";
				//$colStr.="<td>$frcRoleName</td>";
			}
			$colStr.="</tr>";
		}
		$colStr.="</tbody>";
		$ROLE_STR.=<<<ROLE_STR
			<table class='table-bordered table-hover' style='width:80%; padding-top:5px;'>
				<body>$colStr</body>
			</table>
			<br/>
ROLE_STR;
	}
	//End: Block for getting roles for all subdirectories
	
	//Start: Block for getting levels for all subdirectories
	$FRC_Levels = array();
	$lvlOptions = array(
							  'Directory' => array('gwr_wlm_options' , 'wpm_levels'), 
							  'Shop' => array('shop_wlm_options' , 'wpm_levels'),
							  'Premium' => array('wp_wlm_options' , 'wpm_levels'),
							  'Classified' => array('classified_wlm_options' , 'wpm_levels'),
							  );
	
	foreach($lvlOptions as $subDir => $roleOption){
		$tabName = $roleOption[0];
		$fieldName = $roleOption[1];
		$query = "SELECT `option_value` FROM `$tabName` WHERE `option_name` = '$fieldName'";
		$resultObj = $wpdb->get_results($query);
		$FRC_Levels[$subDir] = array();
        if(count($resultObj) >= 1){
			$tempArr = unserialize($resultObj[0]->option_value);
			foreach($tempArr as $levelID => $lvlArr){
				$FRC_Levels[$subDir][$levelID] = $lvlArr['name'];
			}
		}
	}
	
	$columns = 4;$LVL_STR = "";
	foreach($FRC_Levels as $subDir => $frcLevels){
		$noOfLvls = count($frcLevels);
		$frcLvlChunks = array_chunk($frcLevels, 4, true);
		$colStr = "";
		$colStr = "<thead>
						<tr bgcolor='#C6D5FD'>
							<th colspan='8' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;&raquo;&nbsp;Levels for $subDir Installation are ($noOfLvls):</td>
						</tr>
						<tr bgcolor='#D0D0D0'>
							<th style='text-align:left;padding-left:3px;'>Level ID</td>
							<th style='text-align:left;padding-left:3px;'>Level</td>
							<th style='text-align:left;padding-left:3px;'>Level ID</td>
							<th style='text-align:left;padding-left:3px;'>Level</td>
							<th style='text-align:left;padding-left:3px;'>Level ID</td>
							<th style='text-align:left;padding-left:3px;'>Level</td>
							<th style='text-align:left;padding-left:3px;'>Level ID</td>
							<th style='text-align:left;padding-left:3px;'>Level</td>
						</tr>
					</thead>";
		$colStr.="<tbody>";
		$counter = 0;
		foreach($frcLvlChunks as $frcLvlChunk){
			$rowClass = $counter++ % 2 == 0 ? 'active' : 'info';
			$colStr.="<tr class='$rowClass'>";
			foreach($frcLvlChunk as $frcLvlID => $frcLvlName){
				$colStr.="<td style='width:150px;padding-left:3px;'>$frcLvlID</td><td style='width:150px;padding-left:3px;'>$frcLvlName</td>";
			}
			$colStr.="</tr>";
		}
		
		$LVL_STR.=<<<LVL_STR
			<table class='table-bordered table-hover' style='width:80%'>
				<body>$colStr</body>
			</table>
		<br/>
LVL_STR;
	}
	//End: Block for getting levels for all subdirectories
	
	$roleNLevelBlk = $userDetailStr = $errorMsg = "";
    $userID = NULL;$recFound = false;
    $tableArr = array('gwr_wlm_userlevels', 'shop_wlm_userlevels', 'wp_wlm_userlevels', 'classified_wlm_userlevels');
    
	$searchForm =<<<SEARCH_FORM
	<div class='table-responsive' style='padding-left:20px;'>
		<br/>
		<form method='post'>
			<table class='table-bordered table-hover' style='width:80%'>
				<tr bgcolor='#C6D5FD'><th colspan='5' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;&raquo;&nbsp;Search User By:</th></tr>
				<tr style="line-height: 35px;min-height: 35px; margin-top:5px;">
					<td style="padding-left:3px;">Login: </td>
					<td style="padding-left:3px;"><input type ='text' name='login' value='' style='height:25px;'/>
					<th style="text-align:center">Or</th>
					<td style="padding-left:3px;">User ID</td>
					<td><input type ='text' name='user_id' value='' style='height:25px;'/>
				</tr>
				<tr><td colspan='5' style='text-align:center; padding-top:3px; padding-bottom:3px;'><input type='submit' name='search' value='Search User' /></td></tr>
			</table>
	   </form>
	</div>
SEARCH_FORM;

    if(isset($_REQUEST['update'])){
        $roleCapabilities = array('gwr_capabilities', 'classified_capabilities', 'shop_capabilities', 'wp_capabilities');
        $userID = (int)$_REQUEST['user_id'];
        //Start: Block for updating user roles
        $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'gwr_capabilities' ";
        $resultObj = $wpdb->get_results($query);
        $usrRoles = $_REQUEST['user_roles'];
        $newUsrCap = array();
        if(count($usrRoles) >= 1){
            foreach($usrRoles as $usrRole){
                $newUsrCap[$usrRole] = 1;
            }
        }else{
            $newUsrCap['subscriber'] = 1;
        }
		
        if(count($resultObj) >= 1){
            $userCaps = unserialize($resultObj[0]->meta_value);
            $isAdministrator = false;
            if(array_key_exists('administrator', $userCaps)){
                $isAdministrator = true;
				$newUsrCap['administrator'] = 1;
            }
			echo $newUsrCapStr = serialize($newUsrCap);
			$updated = $wpdb->update( 'gwr_usermeta',
									 array('meta_value' => $newUsrCapStr),
									 array('user_id' => $userID, 'meta_key' => 'gwr_capabilities') );
			foreach($roleCapabilities as $key => $capName){
				if($key == 0)continue;
				$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$capName' ";
				$resultObj = $wpdb->get_results($query);
				if(count($resultObj) >= 1){
					//update record
					$updated = $wpdb->update( 'gwr_usermeta',
									 array('meta_value' => $newUsrCapStr),
									 array('user_id' => $userID, 'meta_key' => $capName) );
				}else{
					//insert record
					$updated = $wpdb->insert( 'gwr_usermeta',
									 array('user_id' => $userID, 'meta_value' => $newUsrCapStr, 'meta_key' => $capName)
									 );
				}
				//echo "<br/>if blk:". $wpdb->last_query;
			}
        }else{
            //create new record for root installation and others by checking if there exist or not
			foreach($roleCapabilities as $key => $capName){
				$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = '$capName' ";
				$resultObj = $wpdb->get_results($query);
				if(count($resultObj) >= 1){
					//update record
					$updated = $wpdb->update( 'gwr_usermeta',
									 array('meta_value' => $newUsrCapStr),
									 array('user_id' => $userID, 'meta_key' => $capName) );
				}else{
					//insert record
					$updated = $wpdb->insert( 'gwr_usermeta',
									 array('user_id' => $userID, 'meta_value' => $newUsrCapStr, 'meta_key' => $capName)
									 );
				}
				//echo "<br/>else:". $wpdb->last_query;
			}
        }
        //End  : Block for updating user roles
        
        $levels = WLMAPI::GetLevels();
        $userLevelIDArr = array();
        foreach($levels as $levelKey => $levelArr){
		    $userLevelIDArr[] = $levelKey;
        }
        //print_r($usrRole = $_REQUEST['user_roles']);die;
        $user_levels = $_REQUEST['user_levels'];
        $diffArr = array_diff($userLevelIDArr, $user_levels);
		
        if(!empty($diffArr)){
            $userID = (int)$_REQUEST['user_id'];
            $query = "SELECT `ID` FROM `gwr_users` WHERE `ID` = '$userID'";
			$resultObj = $wpdb->get_results($query);
			
			if(count($resultObj) >= 1){
				$userID = $resultObj[0]->ID;
                foreach($tableArr as $table){
                    $delQry = "DELETE FROM `$table` WHERE `level_id` IN('".implode("', '", $diffArr)."') AND `user_id` = $userID";
                    $wpdb->query($delQry);
                }//$deleteObj = $wpdb->delete( 'gwr_wlm_userlevels', array( 'level_id' => $diffArr, 'user_id' => $userID) );
                //echo "<pre>";print_r($userLevelIDArr);print_r($diffArr);
			}
        }
        
        foreach($user_levels as $lvl){
            foreach($tableArr as $table){
                $query = "SELECT `ID` FROM `$table` WHERE `user_id` = $userID AND `level_id` = '$lvl'";
                $lvlObj = $wpdb->get_results($query);
                if(count($lvlObj) >= 1){
                    ;//do nothing
                }else{
                    //insert
                    $wpdb->insert($table,  array('user_id' => $userID, 'level_id' => $lvl));
                    //echo "<br/>". $wpdb->last_query;
                }
            }
        }
        $errorMsg = "Record updated successfully";
	}
    
    
	if(isset($_REQUEST['search'])){
		if(!empty($_REQUEST['login'])){
			$userLogin = strtolower(trim($_REQUEST['login']));
			$query = "SELECT `ID` FROM `gwr_users` WHERE LOWER(`user_login`) = '$userLogin'";
			$resultObj = $wpdb->get_results($query);
			
			if(count($resultObj) >= 1){
				$userID = $resultObj[0]->ID;
				$recFound = true;
			}
			
		}elseif(!empty($_REQUEST['user_id'])){
			$user_id = (int)$_REQUEST['user_id'];
			$query = "SELECT `ID` FROM `gwr_users` WHERE `ID` = '$user_id'";
			$resultObj = $wpdb->get_results($query);
			if(count($resultObj) >= 1){
				$userID = $resultObj[0]->ID;
				$recFound = true;
			}
		}
	}
    
	if(is_plugin_active($wishlistPlugin) && $userID){
		//Get user WLM levels
		$query = "SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID";
		$resultObj = $wpdb->get_results($query);
		$usrLvlArr = array();
		if(count($resultObj)){
			foreach($resultObj as $capObj){
				$usrLvlArr[] = $capObj->level_id; //By Qry
			}
		}
		$userLevels = WLMAPI::GetUserLevels($userID);
		$revUserLvls = array_flip($userLevels); //By API
		// $revUserLvls = $usrLvlArr;
	
		//Get User roles
		$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` IN ('".implode("','", array($userID))."') AND  meta_key like '%_capabilities'";
		$resultObj = $wpdb->get_results($query);
		$capArr = array();
		if(count($resultObj)){
			foreach($resultObj as $capObj){
				$capArr[$capObj->meta_key] = unserialize($capObj->meta_value);
			}
		}
		
		$dirRoles = array_keys($capArr['gwr_capabilities']);
		
		$userObj = get_userdata( $userID ); 
		$userRoles = $userObj->roles; //$dirRoles
		$userDetailStr =<<<UserDet
		<div class='table-responsive' style='padding-left:20px;padding-top:5px;'>
			<table class='table-bordered table-hover' style='width:80%'>
				<tr bgcolor='#C6D5FD'><th colspan='5' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;&raquo;&nbsp;User Detail:</th></tr>
				<tr><td>Login:</td><td>{$userObj->user_login}</td><td>Email:</td><td>{$userObj->user_email}</td></tr>
				<tr><td>Name:</td><td>{$userObj->first_name} {$userObj->last_name}</td><td>User ID:</td><td>{$userObj->ID}</td></tr>
			</table>
		</div>
UserDet;

		$roles = get_editable_roles();
		$userRoleArr = $roleOptions = array();
		foreach($allRoles as $role => $roleArr){
			$userRoleArr[$role] = $roleArr['name'];
			$checked = in_array($role, $dirRoles) ? "checked ='checked'" : "";
			$disableStr = 'administrator' == $role ? 'disabled="disabled"' : '';
			$roleOptions[] = "&nbsp;<input type='checkbox' name='user_roles[]' value='$role' $checked $disableStr>&nbsp;".$roleArr['name'];
		}
	
		$roleChuncks = array_chunk($roleOptions, 6);
		$colStr = "";
		foreach($roleChuncks as $roleChunck){
			$colStr.="<tr>";
			foreach($roleChunck as $chnkStr){
				$colStr.="<td>$chnkStr</td>";
			}
			$colStr.="</tr>";
		}
	
		$levels = WLMAPI::GetLevels();
		$userLevelArr = $wlmOptions = array();
		foreach($levels as $levelKey => $levelArr){
			$userLevelArr[$levelKey] = $levelArr['name'];
			$checked = in_array($levelKey,$usrLvlArr) ? "checked='checked'" : "";
			$wlmOptions[] = "&nbsp;<input type='checkbox' name='user_levels[]' $checked value='$levelKey'>&nbsp;".$levelArr['name'];   
		}
	
		$lvlChuncks = array_chunk($wlmOptions, 6);
		$lvlStr = "";
		foreach($lvlChuncks as $lvlChunck){
			$lvlStr.="<tr>";
			foreach($lvlChunck as $levelStr){
				$lvlStr.="<td>$levelStr</td>";
			}
			$lvlStr.="</tr>";
		}
	
		$roleNLevelBlk =<<<ROLE_LVL
		<div class='table-responsive' style='padding-left:20px;padding-top:5px;'>
			<form method='post'>
				<table class='table-bordered table-hover' style='width:80%; padding-top:5px;'>
					<tr bgcolor='#C6D5FD'>
						<th colspan='6' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;&raquo;&nbsp;WLM Levels:</td>
					</tr>
					$lvlStr
					<tr>
						<td colspan='6' style='text-align:center; padding-top:3px; padding-bottom:3px;'>
							<input type='submit' name='update' value='Update Level n Roles' />
						</td>
					</tr>
				</table>
				<br />
				<table class='table-bordered table-hover' style='width:80%'>
					<tr bgcolor='#C6D5FD'>
						<th colspan='6' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;&raquo;&nbsp;User Roles:</td>
					</tr>
					$colStr
					<tr><td colspan='6' style='font-family: "Times New Roman", Georgia, Serif;'>
					Note: <span style='background-color:yellow; '>Administrator role neither can be assigned nor can be removed. *Please refer below for levels in different installations*</style></td></tr>
					<tr><td colspan='6' style='font-family: "Times New Roman", Georgia, Serif;'>
					Note: <span style='background-color:yellow; '>Roles will be updated for all sub-directories on submission</style></td></tr>
					<tr>
						<td colspan='6' style='text-align:center; padding-top:3px; padding-bottom:3px;'>
							<input type='submit' name='update' value='Update Level n Roles' />
						</td>
					</tr>
				</table>
				<br/>
				<p><strong>Below are roles for all sub-directories:</strong></p>
				$ROLE_STR
				<p><strong>Below are levels for all sub-directories:</strong></p>
				$LVL_STR
				<input type='hidden' name='user_id' value='$userID' />
			</form>
		</div>
ROLE_LVL;
	}
	echo $searchForm;
	if(isset($_REQUEST['search']) && !$recFound){
		echo "<div style='color:red; text-align:center;'><strong>No User Record Found</strong></div>";
	}
	echo "<div style='color:red; text-align:center;'><strong>$errorMsg</strong></div>";
	echo $userDetailStr;
	echo $roleNLevelBlk;
?>
</body>
</html>