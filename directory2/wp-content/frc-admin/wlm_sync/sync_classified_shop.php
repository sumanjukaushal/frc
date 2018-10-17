<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_classified_shop.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');
    define( 'WP_MEMORY_LIMIT', '512M' );
	require("wlmapiclass.php");
    /*
	1398983986 => Free Memebrship [Classified, directory, Shop, ] 1
	1399015844 => House Sitting [Classified, directory, Shop, ] 2
	1444431834 => classifieds [Classified, directory, Shop, ] 3
	1414469692 => Free Listings [Classified, directory, Shop, ] 4
	1399011470 => Campgrounds [Classified, directory, Shop, ] 5
	1399015901 => Park Overs [Classified, directory, Shop, ] 6
	1399015933 => Help Outs [Classified, directory, Shop, ] 7
	1399015963 => Business Listings [Classified, directory, Shop, ] 8
	1415928787 => Caravan Parks [Classified, directory, Shop,  ] 9
	1406427506 => Premium Membership [Classified, directory, Shop, ] 10
	1496600364 => Foundation Membership [Classified - Missing, directory-1496600364, shop-1496600595, Premium-1496600500] 11
	*/
	
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
	
	//1. From Premium to all other installations (shop, classified, directory)
	echo "\n".$query = "SELECT * FROM `wp_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 15 MINUTE )) Group BY `user_id` ORDER BY `user_id` DESC";//AND `processed_by_script` = 0
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			echo "\nPremium User ID:".$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);$wpdb->prefix
			//----------------------------------------
			$userWLMLevels = array();
			
			$wpQry = "SELECT * FROM `wp_wlm_userlevels` WHERE `user_id` = $userID";
			$wpObjs = $wpdb->get_results($wpQry);
			if(count($wpObjs) >= 1){
				foreach($wpObjs as $wpKey => $wpObj){
					if(!in_array($wpObj->level_id, $userWLMLevels)){$userWLMLevels[] = $wpObj->level_id;}
				}
			}
			
			$gwrQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID";
			$gwrObjs = $wpdb->get_results($gwrQry);
			if(count($gwrObjs) >= 1){
				foreach($gwrObjs as $gwrKey => $gwrObj){
					if(!in_array($gwrObj->level_id, $userWLMLevels)){$userWLMLevels[] = $gwrObj->level_id;}
				}
			}
			
			$classifiedQry = "SELECT * FROM `classified_wlm_userlevels` WHERE `user_id` = $userID";
			$classifiedObjs = $wpdb->get_results($classifiedQry);
			if(count($classifiedObjs) >= 1){
				foreach($classifiedObjs as $classifiedKey => $classifiedObj){
					if(!in_array($classifiedObj->level_id, $userWLMLevels)){$userWLMLevels[] = $classifiedObj->level_id;}
				}
			}
			
			$shopQry = "SELECT * FROM `shop_wlm_userlevels` WHERE `user_id` = $userID";
			$shopObjs = $wpdb->get_results($shopQry);
			if(count($shopObjs) >= 1){
				foreach($shopObjs as $shopKey => $shopObj){
					if(!in_array($shopObj->level_id, $userWLMLevels)){$userWLMLevels[] = $shopObj->level_id;}
				}
			}
			//print_r($userWLMLevels);
			foreach($userWLMLevels as $key => $userWLMLevel){
				$dataArr = array('user_id' => $userID, 'level_id' => $userWLMLevel, 'processed_by_script' => 1);
				
				//1. add/update record in shop 
				$shopQry = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$shopObj = $wpdb->get_results($shopQry);
				if(count($shopObj) >= 1){
					;//do nothing
				}else{
					$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n1:".$wpdb->last_query;
				}
				
				//2. add/update record in premium 
				$premQry = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$premObj = $wpdb->get_results($premQry);
				if(count($premObj) >= 1){
					;//do nothing
				}else{
					$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n2:".$wpdb->last_query;
				}
				
				//3. add/update record in directory 
				$dirQry = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$dirObj = $wpdb->get_results($dirQry);
				if(count($dirObj) >= 1){
					;//do nothing
				}else{
					$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n3:".$wpdb->last_query;
				}
				
				//4. add/update record in classified 
				$classQry = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$classObj = $wpdb->get_results($classQry);
				if(count($classObj) >= 1){
					;//do nothing
				}else{
					$classStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n4_2:".$wpdb->last_query;
				}
			}
		}
	}
		
		
	//2. From Directory to all other installations (shop, classified, premium)
	echo "\n".$query = "SELECT * FROM `gwr_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 15 MINUTE )) Group BY `user_id` ORDER BY `user_id` DESC";//AND `processed_by_script` = 0
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			echo "\nDirectory User ID:".$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);$wpdb->prefix
			//----------------------------------------
			$userWLMLevels = array();
			
			$wpQry = "SELECT * FROM `wp_wlm_userlevels` WHERE `user_id` = $userID";
			$wpObjs = $wpdb->get_results($wpQry);
			if(count($wpObjs) >= 1){
				foreach($wpObjs as $wpKey => $wpObj){
					if(!in_array($wpObj->level_id, $userWLMLevels)){$userWLMLevels[] = $wpObj->level_id;}
				}
			}
			
			$gwrQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID";
			$gwrObjs = $wpdb->get_results($gwrQry);
			if(count($gwrObjs) >= 1){
				foreach($gwrObjs as $gwrKey => $gwrObj){
					if(!in_array($gwrObj->level_id, $userWLMLevels)){$userWLMLevels[] = $gwrObj->level_id;}
				}
			}
			
			$classifiedQry = "SELECT * FROM `classified_wlm_userlevels` WHERE `user_id` = $userID";
			$classifiedObjs = $wpdb->get_results($classifiedQry);
			if(count($classifiedObjs) >= 1){
				foreach($classifiedObjs as $classifiedKey => $classifiedObj){
					if(!in_array($classifiedObj->level_id, $userWLMLevels)){$userWLMLevels[] = $classifiedObj->level_id;}
				}
			}
			
			$shopQry = "SELECT * FROM `shop_wlm_userlevels` WHERE `user_id` = $userID";
			$shopObjs = $wpdb->get_results($shopQry);
			if(count($shopObjs) >= 1){
				foreach($shopObjs as $shopKey => $shopObj){
					if(!in_array($shopObj->level_id, $userWLMLevels)){$userWLMLevels[] = $shopObj->level_id;}
				}
			}
			//print_r($userWLMLevels);
			foreach($userWLMLevels as $key => $userWLMLevel){
				$dataArr = array('user_id' => $userID, 'level_id' => $userWLMLevel, 'processed_by_script' => 1);
				
				//1. add/update record in shop 
				$shopQry = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$shopObj = $wpdb->get_results($shopQry);
				if(count($shopObj) >= 1){
					;//do nothing
				}else{
					$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n1:".$wpdb->last_query;
				}
				
				//2. add/update record in premium 
				$premQry = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$premObj = $wpdb->get_results($premQry);
				if(count($premObj) >= 1){
					;//do nothing
				}else{
					$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n2:".$wpdb->last_query;
				}
				
				//3. add/update record in directory 
				$dirQry = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$dirObj = $wpdb->get_results($dirQry);
				if(count($dirObj) >= 1){
					;//do nothing
				}else{
					$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n3:".$wpdb->last_query;
				}
				
				//4. add/update record in classified 
				$classQry = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$classObj = $wpdb->get_results($classQry);
				if(count($classObj) >= 1){
					;//do nothing
				}else{
					$classStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n4_2:".$wpdb->last_query;
				}
			}
		}
	}
	
	//3. From Shop to all other installations (directory, classified, premium)
	echo "\n".$query = "SELECT * FROM `shop_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 15 MINUTE )) GROUP BY `user_id` ORDER BY `user_id` DESC";//AND `processed_by_script` = 0
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			echo "\nShop User ID:".$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);$wpdb->prefix
			//----------------------------------------
			$userWLMLevels = array();
			
			$wpQry = "SELECT * FROM `wp_wlm_userlevels` WHERE `user_id` = $userID";
			$wpObjs = $wpdb->get_results($wpQry);
			if(count($wpObjs) >= 1){
				foreach($wpObjs as $wpKey => $wpObj){
					if(!in_array($wpObj->level_id, $userWLMLevels)){$userWLMLevels[] = $wpObj->level_id;}
				}
			}
			
			$gwrQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID";
			$gwrObjs = $wpdb->get_results($gwrQry);
			if(count($gwrObjs) >= 1){
				foreach($gwrObjs as $gwrKey => $gwrObj){
					if(!in_array($gwrObj->level_id, $userWLMLevels)){$userWLMLevels[] = $gwrObj->level_id;}
				}
			}
			
			$classifiedQry = "SELECT * FROM `classified_wlm_userlevels` WHERE `user_id` = $userID";
			$classifiedObjs = $wpdb->get_results($classifiedQry);
			if(count($classifiedObjs) >= 1){
				foreach($classifiedObjs as $classifiedKey => $classifiedObj){
					if(!in_array($classifiedObj->level_id, $userWLMLevels)){$userWLMLevels[] = $classifiedObj->level_id;}
				}
			}
			
			$shopQry = "SELECT * FROM `shop_wlm_userlevels` WHERE `user_id` = $userID";
			$shopObjs = $wpdb->get_results($shopQry);
			if(count($shopObjs) >= 1){
				foreach($shopObjs as $shopKey => $shopObj){
					if(!in_array($shopObj->level_id, $userWLMLevels)){$userWLMLevels[] = $shopObj->level_id;}
				}
			}
			//print_r($userWLMLevels);
			foreach($userWLMLevels as $key => $userWLMLevel){
				$dataArr = array('user_id' => $userID, 'level_id' => $userWLMLevel, 'processed_by_script' => 1);
				
				//1. add/update record in shop 
				$shopQry = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$shopObj = $wpdb->get_results($shopQry);
				if(count($shopObj) >= 1){
					;//do nothing
				}else{
					$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n1:".$wpdb->last_query;
				}
				
				//2. add/update record in premium 
				$premQry = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$premObj = $wpdb->get_results($premQry);
				if(count($premObj) >= 1){
					;//do nothing
				}else{
					$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n2:".$wpdb->last_query;
				}
				
				//3. add/update record in directory 
				$dirQry = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$dirObj = $wpdb->get_results($dirQry);
				if(count($dirObj) >= 1){
					;//do nothing
				}else{
					$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n3:".$wpdb->last_query;
				}
				
				//4. add/update record in classified 
				$classQry = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				if(43626 == $userID){
					echo "\nclass:".$classQry."\n";
				}
				$classObj = $wpdb->get_results($classQry);
				if(count($classObj) >= 1){
					;//do nothing
				}else{
					$classStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n4_2:".$wpdb->last_query;
				}
			}
		}
	}
	
	//4. From classified to all other installations (directory, shop, premium)
	echo "\n".$query = "SELECT * FROM `classified_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 15 MINUTE )) GROUP BY `user_id` ORDER BY `user_id` DESC";//AND `processed_by_script` = 0
	$resultObj = $wpdb->get_results($query);
	
	if(count($resultObj)){
		foreach($resultObj as $userObj){
			echo "\nClassified User ID:".$userID = (int)$userObj->user_id;
			$levelID = (int)$userObj->level_id;
			$dataArr = array('user_id' => $userID, 'level_id' => $levelID, 'processed_by_script' => 1);
			//print_r($resultObj);$wpdb->prefix
			//----------------------------------------
			$userWLMLevels = array();
			
			$wpQry = "SELECT * FROM `wp_wlm_userlevels` WHERE `user_id` = $userID";
			$wpObjs = $wpdb->get_results($wpQry);
			if(count($wpObjs) >= 1){
				foreach($wpObjs as $wpKey => $wpObj){
					if(!in_array($wpObj->level_id, $userWLMLevels)){$userWLMLevels[] = $wpObj->level_id;}
				}
			}
			
			$gwrQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID";
			$gwrObjs = $wpdb->get_results($gwrQry);
			if(count($gwrObjs) >= 1){
				foreach($gwrObjs as $gwrKey => $gwrObj){
					if(!in_array($gwrObj->level_id, $userWLMLevels)){$userWLMLevels[] = $gwrObj->level_id;}
				}
			}
			
			$classifiedQry = "SELECT * FROM `classified_wlm_userlevels` WHERE `user_id` = $userID";
			$classifiedObjs = $wpdb->get_results($classifiedQry);
			if(count($classifiedObjs) >= 1){
				foreach($classifiedObjs as $classifiedKey => $classifiedObj){
					if(!in_array($classifiedObj->level_id, $userWLMLevels)){$userWLMLevels[] = $classifiedObj->level_id;}
				}
			}
			
			$shopQry = "SELECT * FROM `shop_wlm_userlevels` WHERE `user_id` = $userID";
			$shopObjs = $wpdb->get_results($shopQry);
			if(count($shopObjs) >= 1){
				foreach($shopObjs as $shopKey => $shopObj){
					if(!in_array($shopObj->level_id, $userWLMLevels)){$userWLMLevels[] = $shopObj->level_id;}
				}
			}
			//print_r($userWLMLevels);
			foreach($userWLMLevels as $key => $userWLMLevel){
				$dataArr = array('user_id' => $userID, 'level_id' => $userWLMLevel, 'processed_by_script' => 1);
				
				//1. add/update record in shop 
				$shopQry = "SELECT `ID` FROM `shop_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$shopObj = $wpdb->get_results($shopQry);
				if(count($shopObj) >= 1){
					;//do nothing
				}else{
					$shopStatus = $wpdb->insert( "shop_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n1:".$wpdb->last_query;
				}
				
				//2. add/update record in premium 
				$premQry = "SELECT `ID` FROM `wp_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$premObj = $wpdb->get_results($premQry);
				if(count($premObj) >= 1){
					;//do nothing
				}else{
					$premStatus = $wpdb->insert( "wp_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n2:".$wpdb->last_query;
				}
				
				//3. add/update record in directory 
				$dirQry = "SELECT `ID` FROM `gwr_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$dirObj = $wpdb->get_results($dirQry);
				if(count($dirObj) >= 1){
					;//do nothing
				}else{
					$dirStatus = $wpdb->insert( "gwr_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n3:".$wpdb->last_query;
				}
				
				//4. add/update record in classified 
				$classQry = "SELECT `ID` FROM `classified_wlm_userlevels` WHERE `user_id` = $userID AND `level_id` = $userWLMLevel";
				$classObj = $wpdb->get_results($classQry);
				if(count($classObj) >= 1){
					;//do nothing
				}else{
					$classStatus = $wpdb->insert( "classified_wlm_userlevels", $dataArr, array('%d', '%s', '%d'));
					echo "\n<br/>\n4_2:".$wpdb->last_query;
				}
			}
		}
	}
	die("Fds");
	
	//4. From classified to all other installations (directory, shop, premium)
	$query = "SELECT * FROM  `classified_wlm_userlevels` WHERE (`modified` > ( NOW( ) - INTERVAL 15 MINUTE )) AND `processed_by_script` = 0 LIMIT 0 , 30";
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