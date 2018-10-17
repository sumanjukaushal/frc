<?php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php'); 
	//Step 1 Get params from request from where we want to copy role and to where, e.g.: source, destination, role
	$dbNamePrefix = strpos( realpath(dirname(__FILE__)), 'frcconz') ? 'frcconz_' : 'freerang_';
	$optNTbl = array(
					'shop' => array('dbPrefix'=>'shop_', 'roleOption' => 'shop_user_roles', 'database' => "{$dbNamePrefix}shop"),
					
					'premium' => array('dbPrefix'=>'wp_', 'roleOption' => 'wp_user_roles', 'database' => "{$dbNamePrefix}premium"),
					//e.g.: SELECT * FROM `wp_options` WHERE `option_name`='wp_user_roles'
					
					'directory' => array('dbPrefix'=>'gwr_', 'roleOption' => 'gwr_user_roles', 'database' => "{$dbNamePrefix}directory"),
					//e.g.: SELECT * FROM `gwr_options` WHERE `option_name`='gwr_user_roles'
					
					'classified' => array('dbPrefix'=>'classified_', 'roleOption' => 'classified_user_roles', 'database' => "{$dbNamePrefix}classified"),
					//e.g.: SELECT * FROM `classified_options` WHERE `option_name`='classified_user_roles'
					 );
	
	$sourceLoc = $_REQUEST['source'];
	$destLoc =  $_REQUEST['destination'];
	$role =  $_REQUEST['role'];
	if(($sourceLoc == $destLoc) || empty($role)){die('Either source/destination location are same or role is empty');}
	
	copy_role_n_capabilities($sourceLoc, $destLoc, $role);
	
	function copy_role_n_capabilities($source, $destination, $role){
		global $wpdb, $optNTbl;	//print_r($optNTbl);
		//Step 1: Get role and capabilites of source installation
		if( array_key_exists($source, $optNTbl) && array_key_exists($destination, $optNTbl) ){
			$dbNRoleInfo = $optNTbl[$source];
			$currDB = $dbNRoleInfo['database'];
			$roleOption = $dbNRoleInfo['roleOption'];
			$dbPrefix = $dbNRoleInfo['dbPrefix'];
			$tableName = "{$dbPrefix}options";
			$fieldName = $dbNRoleInfo['roleOption'];
			
			$query = "SELECT * FROM `$tableName` WHERE `option_name` = '$fieldName'";
			//Make connection to source database from where we first want to grab all roles
			$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
			$totRecs = $wpdb->query($query);
			$items = $wpdb->get_results( $query, OBJECT );
			echo "<pre>";
			$sourceRoles = unserialize($items[0]->option_value);
			if(array_key_exists($role, $sourceRoles)){
				$dbNRoleInfo = $optNTbl[$destination];
				$currDB = $dbNRoleInfo['database'];
				$roleOption = $dbNRoleInfo['roleOption'];
				$dbPrefix = $dbNRoleInfo['dbPrefix'];
				$tableName = "{$dbPrefix}options";
				$fieldName = $dbNRoleInfo['roleOption'];
				$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
				
				$query = "SELECT * FROM `$tableName` WHERE `option_name` = '$fieldName'";
				$totRecs = $wpdb->query($query);
				$items = $wpdb->get_results( $query, OBJECT );
				$destinationRoles = unserialize($items[0]->option_value);
				$optID = $items[0]->option_id;
				if(array_key_exists($role, $destinationRoles)){
					die("Role: $role is already present in Destination:$destination" );
				}else{
					echo "Adding role <b>$role</b> to Destination <b>$destination</b>";
					$destinationRoles[$role] = $sourceRoles[$role];
					$serializedRoles = serialize($destinationRoles);
					print_r($destinationRoles);
					$updateRes = $wpdb->update( 
								$tableName, 
								array('option_value' => $serializedRoles),
                                array( 'option_name' => $fieldName, 'option_id' => $optID )
                            );
					if($updateRes === false){
						echo "Failed to add Role: $role to Destination:$destination";
					}else{
						echo "Role: $role successfully added to Destination:$destination";
					}
					die;
				}
			}else{
				die("$role is missing in $source" );
			}
		}else{
			die("Source:$source or Destination:$destination is wrong" );
		}
	}
	
?>