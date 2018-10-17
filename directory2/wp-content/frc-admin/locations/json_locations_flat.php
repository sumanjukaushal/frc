<?php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $locationArr = $searchArray = array();
    $search = str_replace(",", " ", strtolower($_REQUEST['search']));
    //$splittedArr = split(' ', $search);
    $splittedArr = explode(" ", $search);
    /*
     UPDATE `rasu_locations` SET output = CONCAT_WS(',',`state`,`region`, `sub_region`);
    */

    ob_start();
    pc_permute($splittedArr);
    $permutation = ob_get_clean();
    $wordArray = explode("\n", $permutation);
    foreach($wordArray as $value){
        if(empty($value)) continue;
        $searchArray[] = "LOWER(`rasu_locations`.`output`) like '%".str_replace(" ","%",$value)."%'";
    }
    $searchStr = implode(' OR ', $searchArray);

    $query = "SELECT `output` FROM `rasu_locations`";
    $dbObjs = $wpdb->get_results($query);
    if(count($dbObjs) >= 1){
        foreach($dbObjs as $dbObj){
            $locationArr[] = $dbObj->output;
        }
    }
     //-------------------------------------------
     // http://www.example.org/ajax.php
    /* echo "origin".$_SERVER['HTTP_ORIGIN'];
if (!isset($_SERVER['HTTP_ORIGIN'])) {
    // This is not cross-domain request
    die("This is not cross-domain request");
}*/

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
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {exit;}
    //header('Content-Type" => application/json');
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($locationArr);
     
    function pc_permute($items, $perms = array( )) {
        //return $items;
        if (empty($items)) { 
            print join(' ', $perms) . "\n";
        }else{
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                pc_permute($newitems, $newperms);
            }
        }
    }
?>