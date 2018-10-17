<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/wlm_sync_capabilities_once.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
   
	//1. From Directory to all other installations (shop, classified, premium)
	$query = "SELECT * FROM `gwr_usermeta` WHERE (`modified` > ( NOW( ) - INTERVAL 50 MINUTE )) AND (`meta_key` = 'gwr_capabilities' || `meta_key` = 'wp_capabilities') ORDER BY `umeta_id` DESC";//`umeta_id` DESC
	//Total = 780778, capablity records = 167032, after script = 167152
	$resultObj = $wpdb->get_results($query);
	//SELECT * FROM `gwr_usermeta` WHERE `user_id`=21417 and meta_key like '%_capabilities' order by `umeta_id` desc
	//SELECT * FROM `wp_wlm_userlevels` WHERE `user_id`=42557
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			$userID = (int)$userObj->user_id;
			//if($userID != 42242)continue;
			$userCapabilities = $userObj->meta_value;	//serialised data
			$dataArr = array('user_id' => $userID, 'meta_key' => 'gwr_capabilities', 'meta_value' => $userCapabilities, 'processed_by_script' => 1);
			$formatArr = array('%d', '%s', '%s', '%d');
			
			//3. Premium installation
			echo $query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'wp_capabilities'";
			$premObj = $wpdb->get_results($query);
			if(count($premObj) >= 1){
				//Case: For Premium case we need to merge premium capabilities with directory capabilities and than merged value should be added to all other installation.
				$gwrCap = unserialize($userObj->meta_value);
				$preCap = unserialize($premObj[0]->meta_value);
				if(array_key_exists('premium',$preCap )){
					$capArr = array_merge($gwrCap, $preCap);
					$fieldArr = array('meta_value' => serialize($capArr), 'processed_by_script' => 1);
					$dirMetaID = $userObj->umeta_id;
					
					//1. updating capabilities for directory after merging
					//Case: check if record exist in directory
					$query = "SELECT * FROM `gwr_usermeta` WHERE `meta_key` = 'gwr_capabilities' AND `user_id` = $userID";
					$dirObj = $wpdb->get_results($query);
					if(count($dirObj) >= 1){
						$dirMetaID = $dirObj[0]->umeta_id;
						$wpdb->update( 
										'gwr_usermeta', 
										$fieldArr,
										array( 'umeta_id' => $dirMetaID, 'user_id' => $userID )
									);
					}else{
						//insert record
						$fieldArr['meta_key'] = 'gwr_capabilities';
						$fieldArr['user_id'] = $userID;
						//add data
						$dirStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
						$insertQry = $wpdb->last_query;
						if($dirStatus){
							echo "\n<br/>Capabilities added to directory installation successfully for user $userID";
						}
					}
					
					//2. updating capabilities for premium after merging
					$premMetaID = $premObj[0]->umeta_id;
					$wpdb->update( 
									'gwr_usermeta', 
									$fieldArr,
									array( 'umeta_id' => $premMetaID, 'user_id' => $userID )
								  );
					
					//3. updating capabilities for shop after merging
					$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'shop_capabilities'";
					$shopObj = $wpdb->get_results($query);
					
					if(count($shopObj) >= 1){
						$shopMetaID = $shopObj[0]->umeta_id;
						$wpdb->update( 
									   'gwr_usermeta', 
									   $fieldArr,
									   array( 'umeta_id' => $shopMetaID, 'user_id' => $userID )
									);
					}else{
						$fieldArr['meta_key'] = 'shop_capabilities';
						$fieldArr['user_id'] = $userID;
						//add data
						$shopStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
						$insertQry = $wpdb->last_query;
						if($shopStatus){
							echo "\n<br/>Capabilities added to shop successfully for user $userID";
						}
					}
					
					//4. updating capabilities for classified after merging
					$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'classified_capabilities'";
					$classifiedObj = $wpdb->get_results($query);
					if(count($classifiedObj) >= 1){
						$classifiedMetaID = $classifiedObj[0]->umeta_id;
						$wpdb->update( 
									   'gwr_usermeta', 
									   $fieldArr,
									   array( 'umeta_id' => $classifiedMetaID, 'user_id' => $userID )
									);
						echo $insertQry = $wpdb->last_query;
					}else{
						$fieldArr['meta_key'] = 'classified_capabilities';
						$fieldArr['user_id'] = $userID;
						//add data
						$classifiedStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
						echo "\n<br/>Shop Qry:".$insertQry = $wpdb->last_query;
						if($classifiedStatus){
							echo "\n<br/>Capabilities added to shop successfully for user $userID";
						}
					}
				}else{
					//do nothing - if user for premium installation do not have Premium capabilities
					echo "do nothing";
				}
			}
			
			//add caapabilities to premium, shop n classified
			$userCapabilities = $userObj->meta_value;	//serialised data from directory installation
			$fieldArr = array(
								'meta_value' => $userCapabilities,
								'user_id' => $userID,
								'meta_key' => 'wp_capabilities',
								'processed_by_script' => 1
							  );
			$updateArr = array('meta_value' => $userCapabilities);
			
			//1. updating capabilities for premium after merging
			echo "<br/><br/>\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'wp_capabilities'";
			$premObj = $wpdb->get_results($query);
			if(count($premObj) >= 1){
				//update record
				$premMetaID = $premObj[0]->umeta_id;
				$wpdb->update( 
								'gwr_usermeta', 
								$updateArr,
								array( 'umeta_id' => $premMetaID, 'user_id' => $userID )
							  );
				echo "\n<br/>Premium Qry:".$updateQry = $wpdb->last_query;
			}else{
				//Insert record
				$fieldArr['meta_key'] = 'wp_capabilities';
				$premiumStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
				echo "\n<br/>Premium Qry:".$insertQry = $wpdb->last_query;
				if($premiumStatus){
					echo "\n<br/>Capabilities added to premium installation successfully for user $userID";
				}
			}
			
			//2. updating capabilities for shop after merging
			echo "<br/><br/>\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'shop_capabilities'";
			$shopObj = $wpdb->get_results($query);
			if(count($shopObj) >= 1){
				//update record
				$shopMetaID = $shopObj[0]->umeta_id;
				$wpdb->update( 
								'gwr_usermeta', 
								$updateArr,
								array( 'umeta_id' => $shopMetaID, 'user_id' => $userID )
							  );
				echo "\n<br/>Shop Qry:".$updateQry = $wpdb->last_query;
			}else{
				//Insert record
				$fieldArr['meta_key'] = 'shop_capabilities';
				$shopStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
				echo "\n<br/>Shop Qry:".$insertQry = $wpdb->last_query;
				if($shopStatus){
					echo "\n<br/>Capabilities added to shop installation successfully for user $userID";
				}
			}
			
			//3. updating capabilities for classified after merging
			echo "<br/><br/>\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = $userID AND `meta_key` = 'classified_capabilities'";
			$classifiedObj = $wpdb->get_results($query);
			if(count($classifiedObj) >= 1){
				//update record
				$classifiedMetaID = $classifiedObj[0]->umeta_id;
				$wpdb->update( 
								'gwr_usermeta', 
								$updateArr,
								array( 'umeta_id' => $classifiedMetaID, 'user_id' => $userID )
							  );
				echo "\n<br/>Classified Qry:".$updateQry = $wpdb->last_query;
			}else{
				//Insert record
				$fieldArr['meta_key'] = 'classified_capabilities';
				$classifiedStatus = $wpdb->insert( "gwr_usermeta", $fieldArr);
				echo "\n<br/>Classified Qry:".$insertQry = $wpdb->last_query;
				if($classifiedStatus){
					echo "\n<br/>Capabilities added to Classified installation successfully for user $userID";
				}
			}
		}
	}
	
	
    die("---completed---");
    $to      = 'kalyanrajiv@gmail.com';
    $subject = 'Cron sync_classified_shop: ';
    $message = 'cron executed successfully';
    $headers = 'From: webmaster@freerangecamping.co.nz' . "\r\n" .
    'Reply-To: webmaster@freerangecamping.co.nz' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	//mail($to, $subject, $message, $headers);
	//php -q /home/frcconz/public_html/directory/wp-content/wlm_sync/cron_sync_wlm.php
?>