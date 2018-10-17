<?php
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/wlm_sync_premium_subscription.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    
    function wlm_script_url() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
		 $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
    if(is_plugin_active($wishlistPlugin)){
		$requestFor = 'GetLevels';//$_REQUEST['request_for'];
		$currentURL = wlm_script_url();
		
		if(strpos($currentURL, "classifieds")){
			$wlmKey = "8ce60f0f012df5bb33cacb751f8e90d5";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/classifieds/', $wlmKey);
		}elseif(strpos($currentURL, "directory")){
			$wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/directory/', $wlmKey);
            echo "testing";
            //Start: for testing
            $wlmKey = "149a69884694c7dbaaa4becf2c7031c7";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/shop/', $wlmKey);
            //End: for testing
		}elseif(strpos($currentURL, "shop")){
			$wlmKey = "149a69884694c7dbaaa4becf2c7031c7";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/shop/', $wlmKey);
		}elseif(strpos($currentURL, "premium")){
			$wlmKey = "6c05bdd8716c3da997acb95a8a801c24";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/premium/', $wlmKey);
		}
		$levels = WLMAPI::GetLevels();
		//print_r($levels);
        $query = "SELECT * FROM `wp_wlm_userlevels` WHERE `level_id` = '1406427506' AND (`modified` > ( NOW( ) - INTERVAL 6 MINUTE ))"; //Premium Level
        $levelObjs = $wpdb->get_results($query);
        if(count($levelObjs)){
            foreach($levelObjs as $levelObj){
                $wlmUserLevelID = (int)$levelObj->ID;
                $userID = (int)$levelObj->user_id;
                $optQry = "SELECT * FROM `wp_wlm_userlevel_options` WHERE `userlevel_id` = $wlmUserLevelID"; //Premium Level echo "\n<br/>".
                $optObjs = $wpdb->get_results($optQry);
                if(count($optObjs)){
                    foreach($optObjs as $optObj){
                        //Case 1: Directory installation
                        $gwrQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE `level_id` = '1406427506' and `user_id` = $userID";
                        $gwrObjs = $wpdb->get_results($gwrQry);
                        if(count($gwrObjs)){
                            $gwrUserLevelID = (int)$gwrObjs[0]->ID;
                            $optionName = $optObj->option_name;
                            $optionVal = $optObj->option_value;
                            echo $gwrOptQry = "SELECT * FROM `gwr_wlm_userlevel_options` WHERE `userlevel_id` = $gwrUserLevelID AND `option_name` = '$optionName'";
                            $gwrOptObjs = $wpdb->get_results($gwrOptQry);
                            $fieldArr = array(
                                                'userlevel_id' => $gwrUserLevelID,
                                                'option_name' => $optionName,
                                                'option_value' => $optionVal,
                                                'autoload' => 'yes',
                                            );
                            if(count($gwrOptObjs)){
                                $gwrOptID = $gwrOptObjs[0]->ID;
                                //Update
                                $wpdb->update( 
										'gwr_wlm_userlevel_options', 
										$fieldArr,
										array( 'ID' => $gwrOptID )
									);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                            }else{
                                //Insert
                                $dirStatus = $wpdb->insert( "gwr_wlm_userlevel_options", $fieldArr);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                                if($dirStatus){
                                    echo "\n<br/>Capabilities added to directory installation successfully for user $userID";
                                }
                            }
                        }
                        
                        //Case 2: shop installation
                        $shopQry = "SELECT * FROM `shop_wlm_userlevels` WHERE `level_id` = '1406427506' and `user_id` = $userID";
                        $shopObjs = $wpdb->get_results($shopQry);
                        if(count($shopObjs)){
                            $shopUserLevelID = (int)$shopObjs[0]->ID;
                            $optionName = $optObj->option_name;
                            $optionVal = $optObj->option_value;
                            echo $shopOptQry = "SELECT * FROM `shop_wlm_userlevel_options` WHERE `userlevel_id` = $shopUserLevelID AND `option_name` = '$optionName'";
                            $shopOptObjs = $wpdb->get_results($shopOptQry);
                            $fieldArr = array(
                                                'userlevel_id' => $shopUserLevelID,
                                                'option_name' => $optionName,
                                                'option_value' => $optionVal,
                                                'autoload' => 'yes',
                                            );
                            if(count($shopOptObjs)){
                                $shopOptID = $shopOptObjs[0]->ID;
                                //Update
                                $wpdb->update( 
										'shop_wlm_userlevel_options', 
										$fieldArr,
										array( 'ID' => $shopOptID )
									);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                            }else{
                                //Insert
                                $shopStatus = $wpdb->insert( "shop_wlm_userlevel_options", $fieldArr);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                                if($shopStatus){
                                    echo "\n<br/>Capabilities added to shop installation successfully for user $userID";
                                }
                            }
                        }
                        
                        //Case 3: classified installation
                        $classifiedQry = "SELECT * FROM `classified_wlm_userlevels` WHERE `level_id` = '1406427506' and `user_id` = $userID";
                        $classifiedObjs = $wpdb->get_results($classifiedQry);
                        if(count($classifiedObjs)){
                            $classifiedUserLevelID = (int)$classifiedObjs[0]->ID;
                            $optionName = $optObj->option_name;
                            $optionVal = $optObj->option_value;
                            echo $classifiedOptQry = "SELECT * FROM `classified_wlm_userlevel_options` WHERE `userlevel_id` = $classifiedUserLevelID AND `option_name` = '$optionName'";
                            $classifiedOptObjs = $wpdb->get_results($classifiedOptQry);
                            $fieldArr = array(
                                                'userlevel_id' => $classifiedUserLevelID,
                                                'option_name' => $optionName,
                                                'option_value' => $optionVal,
                                                'autoload' => 'yes',
                                            );
                            if(count($classifiedOptObjs)){
                                $classifiedOptID = $classifiedOptObjs[0]->ID;
                                //Update
                                $wpdb->update( 
										'classified_wlm_userlevel_options', 
										$fieldArr,
										array( 'ID' => $classifiedOptID )
									);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                            }else{
                                //Insert
                                $classifiedStatus = $wpdb->insert( "classified_wlm_userlevel_options", $fieldArr);
                                echo "\n<br />".$insertQry = $wpdb->last_query;
                                if($classifiedStatus){
                                    echo "\n<br/>Capabilities added to shop installation successfully for user $userID";
                                }
                            }
                        }
                    }
                    echo "\n\n------------------------------\n\n";
                    //Add same information to shop, classfified and directory installation
                }
            }
        }
    }
?>