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
	$query = "SELECT `ID`, `user_email` FROM  `gwr_users` order by id asc LIMIT 0 , 10";
	$usersRS = mysql_query($query);
	
    if(is_plugin_active($wishlistPlugin)){
		$requestFor = $_REQUEST['request_for'];
		$currentURL = wlm_script_url();
		
		if(strpos($currentURL, "classifieds")){
			$wlmKey = "8ce60f0f012df5bb33cacb751f8e90d5";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/classifieds/', $wlmKey);
		}elseif(strpos($currentURL, "directory")){
			$wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
			$api = new wlmapiclass('https://www.freerangecamping.co.nz/directory/', $wlmKey);
		}
		
		$api->return_format = 'php'; // <- value can also be xml or json
		//echo $requestFor;die;
		switch($requestFor){
			case 'GetLevels':
				$levels = WLMAPI::GetLevels();
				$levelArr = array();
				foreach($levels as $sngLevel){
					$levelArr[$sngLevel['ID']] = $sngLevel['name'];
					//$sngLevel['role'] = directory_4
					//slug,url = house-sitting
				}
				echo json_encode($levelArr);die;
				$levelResponse = $api->get('/levels');//function get_levels
				//print_r($levelResponse);
				//die;
				//$levelResponse = unserialize($levelResponse);
				break;
			case 'GetUserLevels':
				if(isset($_REQUEST['userID']) && !empty($_REQUEST['userID'])){
					$userID = (int)$_REQUEST['userID'];
					$userLevels = WLMAPI::GetUserLevels($userID);
if(array_key_exists('getUserRoles', $_REQUEST) && $_REQUEST['getUserRoles'] == 1){
$user_info = get_userdata($userID);
echo serialize(array('roles' => $user_info->roles,'levels' => $userLevels));die;
}
					echo serialize($userLevels);
					die;
					//echo "<pre>{$userID}:User Level for $userID:\n";print_r($userLevels);echo "</pre>";
				}else{
					while($userObj = mysql_fetch_object($usersRS)){
						$userID = $userObj->ID;
						$userLevels = WLMAPI::GetUserLevels($userID);
						echo "<pre>User Level for $userID\n";print_r($userLevels);echo "</pre>";
					}
				}
				break;
			case 'GetUserRoles':
				if(isset($_REQUEST['userID']) && !empty($_REQUEST['userID'])){
					$userID = (int)$_REQUEST['userID'];
					$user_info = get_userdata($userID);
					echo serialize($user_info->roles);
					die;
				}else{
					die("---");
				}
				break;
			default:
				echo "---+++{$requestFor}___";
				$levels = WLMAPI::GetUserLevels(1);
				echo "<pre>";print_r($levels);echo "</pre>";
				$levelResponse = $api->get('/levels');//function get_levels
				print_r($levelResponse);
				die;
			
		}
		die("-----");
		echo "wlm installed";
		$levels = WLMAPI::GetUserLevels(1);
		echo "<pre>";print_r($levels);echo "</pre>";
		$member = wlmapi_get_member_by('user_email', 'dollar_25plus@freerangecamping.co.nz');
		echo "<pre>";print_r($member);echo "</pre>";
		//http://codex.wishlistproducts.com/tutorial-how-to-connect-to-the-wishlist-member-api/
		
	}
    
    mysql_close($dbhandle);
    header("Location:index.php?msg=1");
?>