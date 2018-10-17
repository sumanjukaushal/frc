<?php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');
    define( 'WP_MEMORY_LIMIT', '512M' );
	require("wlmapiclass.php");
    
	
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
	$tempArray = array();
	$jsonObj = json_encode($tempArray); //blank Json object
	
	//1. From Directory to all other installations (shop, classified, premium)
	$query = "SELECT * FROM  `gwr_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 60 MINUTE )) AND `processed_by_script` = 0 LIMIT 0 , 30";
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);
			
			//1. Shop installation
			$query = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$shopObj = $wpdb->get_results($query);
			if(count($shopObj) >= 1){echo "\n<br/>NS";}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `shop_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($shopStatus){
					echo "\n<br/>Level added to shop successfully for user $userID";
				}
			}
			
			//2. Classified installation
			$query = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$classifieldObj = $wpdb->get_results($query);
			if(count($classifieldObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `classified_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$classifieldStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($classifieldStatus){
					echo "\n<br/>Level added to classified successfully for user $userID";
				}
			}
			
			//3. Premium installation
			$query = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$premObj = $wpdb->get_results($query);
			if(count($premObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `wp_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($premStatus){
					echo "\n<br/>Level added to premium successfully for user $userID";
				}
			}
		}
	}
	
	//2. From Premium to all other installations (shop, classified, directory)
	$query = "SELECT * FROM  `wp_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 60 MINUTE )) AND `processed_by_script` = 0 LIMIT 0 , 30";
	$resultObj = $wpdb->get_results($query);
	/*
	 42242 => 1406427506 (Premium)
	 SELECT * FROM  `gwr_wlm_userlevels` WHERE user_id=42242 (1406427506)
	 **/
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);
			
			//1. Shop installation
			$query = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$shopObj = $wpdb->get_results($query);
			if(count($shopObj) >= 1){
				echo "\n<br/>NS";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `shop_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($shopStatus){
					echo "\n<br/>Level added to shop successfully for user $userID";
				}
			}
			
			//2. Classified installation
			$query = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$classifieldObj = $wpdb->get_results($query);
			if(count($classifieldObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `classified_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$classifieldStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($classifieldStatus){
					echo "\n<br/>Level added to classified successfully for user $userID";
				}
			}
			
			//3. Directory installation
			$query = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID AND `processed_by_script` = 0";
			$dirObj = $wpdb->get_results($query);
			if(count($dirObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `gwr_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($dirStatus){
					echo "\n<br/>Level added to directory successfully for user $userID";
				}
			}
		}
	}
	
	//3. From shop to all other installations (directory, classified, premium)
	$query = "SELECT * FROM  `shop_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 60 MINUTE )) AND `processed_by_script` = 0 LIMIT 0 , 30";
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);
			
			//1. Directory installation
			$query = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$dirObj = $wpdb->get_results($query);
			if(count($dirObj) >= 1){
				echo "\n<br/>NS";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `gwr_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($dirStatus){
					echo "\n<br/>Level added to shop successfully for user $userID";
				}
			}
			
			//2. Classified installation
			$query = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$classifieldObj = $wpdb->get_results($query);
			if(count($classifieldObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `classified_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$classifieldStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($classifieldStatus){
					echo "\n<br/>Level added to classified successfully for user $userID";
				}
			}
			
			//3. Premium installation
			$query = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$premObj = $wpdb->get_results($query);
			if(count($premObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `wp_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($premStatus){
					echo "\n<br/>Level added to directory successfully for user $userID";
				}
			}
		}
	}
	
	//4. From classified to all other installations (directory, shop, premium)
	$query = "SELECT * FROM  `classified_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 60 MINUTE )) AND `processed_by_script` = 0 LIMIT 0 , 30";
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);
			
			//1. Directory installation
			$query = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$dirObj = $wpdb->get_results($query);
			if(count($dirObj) >= 1){
				echo "\n<br/>NS";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `gwr_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($dirStatus){
					echo "\n<br/>Level added to shop successfully for user $userID";
				}
			}
			
			//2. Shop installation
			$query = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$shopObj = $wpdb->get_results($query);
			if(count($shopObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `shop_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($shopStatus){
					echo "\n<br/>Level added to classified successfully for user $userID";
				}
			}
			
			//3. Premium installation
			$query = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $levelID";
			$premObj = $wpdb->get_results($query);
			if(count($premObj) >= 1){
				echo "\n<br/>NC";
			}else{
				//add data
				echo "\n<br/>".$query = "INSERT INTO `wp_wlm_userlevels` SET `user_id` = $userID, `level_id` = '$levelID', `processed_by_script` = 1";
				$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
				if($premStatus){
					echo "\n<br/>Level added to directory successfully for user $userID";
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