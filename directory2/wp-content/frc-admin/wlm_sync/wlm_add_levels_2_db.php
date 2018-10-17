<?php
    define('DB_NAME', 'freerang_directory');
    define('DB_USER', 'freerang_rob');
    define('DB_PASSWORD', 'nandini123');
    define('DB_HOST', 'localhost');
    define('DB_CHARSET', 'utf8');
    
    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');
    define( 'WP_MEMORY_LIMIT', '512M' );
	require("wlmapiclass.php");
    $dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    $selected = mysql_select_db(DB_NAME,$dbhandle);
	
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
	
    require_once('../../wp-config.php');
	$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
	$tempArray = array();
	$jsonObj = json_encode($tempArray); //blank Json object
	$query = "SELECT `ID`, `user_email` FROM  `gwr_users` order by id asc LIMIT 30000 , 10000";
	$usersRS = mysql_query($query);
	
    if(is_plugin_active($wishlistPlugin)){
		$requestFor = $_REQUEST['request_for'];
		$currentURL = wlm_script_url();
		$wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
		$api = new wlmapiclass('https://www.freerangecamping.co.nz/directory/', $wlmKey);
		$api->return_format = 'php'; // <- value can also be xml or json
		
		while($userObj = mysql_fetch_object($usersRS)){
			$userID = (int)$userObj->ID;
			$userLevels = serialize(WLMAPI::GetUserLevels($userID));
			$user_info = get_userdata($userID);
			$userRoles = serialize($user_info->roles);
			$query = "SELECT `id` FROM `rasu_roles_n_levels` WHERE `user_id` = $userID";
			$rs = mysql_query($query);
			if(mysql_numrows($rs) >= 1){
				//update data
				$query = "UPDATE `rasu_roles_n_levels` SET `roles` = '$userRoles', `levels` = '$userLevels' WHERE `user_id` = $userID";
			}else{
				//add data
				$query = "INSERT INTO `rasu_roles_n_levels` SET `user_id` = $userID, `roles` = '$userRoles', `levels` = '$userLevels'";
			}
			mysql_query($query);
		}
	}
    mysql_close($dbhandle);
?>