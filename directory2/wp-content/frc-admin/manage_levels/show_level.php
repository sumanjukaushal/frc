<?php
    require_once("../../wp-config.php");
	//$link = mysql_connect('localhost', DB_USER, DB_PASSWORD);
	//mysql_select_db(DB_NAME, $link) or die('Could not select database.');
    require_once('../../wp-load.php');
    require_once('../../wp-settings.php');
    require_once("../../wp-includes/registration.php");
	require_once('../../wp-blog-header.php');
    //----------------------------------------------
    $wildcard = FALSE; // Set $wildcard to TRUE if you do not plan to check or limit the domains
    $credentials = FALSE; // Set $credentials to TRUE if expects credential requests (Cookies, Authentication, SSL certificates)
    $allowedOrigins = array('https://www.freerangecamping.co.nz');
    /*if (!in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins) && !$wildcard) {
        // Origin is not allowed
        die("Origin is not allowed");
    }*/
    $origin = $wildcard && !$credentials ? '*' : $_SERVER['HTTP_ORIGIN'];
    //Access-Control-Max-Age: 1728000
    //header('Access-Control-Allow-Methods: GET, POST');
    //http://stackoverflow.com/questions/8719276/cors-with-php-headers
    header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Origin: " . $origin);
    /*if ($credentials) {
        header("Access-Control-Allow-Credentials: true");
    }*/
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Origin");
    header('P3P: CP="CAO PSA OUR"'); // Makes IE to support cookies
    
    // Handling the Preflight
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { 
        exit;
    }
    header("Content-Type: text/plain");
    //----------------------------------------------
    $userID = $tokenID = 17051;//(int)$_REQUEST['token_id'];
    $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
    if(is_plugin_active($wishlistPlugin)){
        $wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
        $api = new wlmapiclass('https://www.freerangecamping.co.nz/directory/', $wlmKey);
        $api->return_format = 'php'; // <- value can also be xml or json
        $userLevels = WLMAPI::GetUserLevels($userID);
        echo "<pre>UserInfo:\n";print_r($userLevels);echo "</pre>";
        die;
    }
?>