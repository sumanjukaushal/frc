<?php
	 require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	 // for getting function defination for is_plugin_active
	 /*
	 Sample calls:
	 echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
	 echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
	 echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
	 Ref: http://www.geodatasource.com/developers/php
	 Ref: http://stackoverflow.com/questions/7672759/how-to-calculate-distance-from-lat-long-in-php
	  **/
	 function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
		  //Result should be returned in km, integer for mobile app
		  $theta = $lon1 - $lon2;
		  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		  $dist = acos($dist);
		  $dist = rad2deg($dist);
		  $miles = $dist * 60 * 1.1515;
		  $unit = strtoupper($unit);
		  
		  if ($unit == "K") {
			   return ($miles * 1.609344);
		  } else if ($unit == "N") {
			   return ($miles * 0.8684);
		  } else {
			   return $miles;
		  }
	 }
	 
	 function check_if_ait_review_plugin_active(){
		  $aitReviewPlugin = 'ait-item-reviews/ait-item-reviews.php';
		  if(!is_plugin_active($aitReviewPlugin)){
			   echo json_encode(array('message' => "AIT review plugin either inactive or not installed",'status_code' => 404  ));die;
		  }
		  return true;
	 }
	 
	 function get_userid_if_valid_token(){
		  global $json_api;
		  $tokenPresent = false;
		  foreach (getallheaders() as $name => $value) {
			  // echo "$name => $value";
			   if($name == 'token' || $name == 'Token'){
					$json_api->query->cookie = $_GET['cookie'] = base64_decode($value);
					$tokenPresent = true;
			   }
		  }//headers_list another function for gettting headers
		  
		  if(!$tokenPresent){
			   echo json_encode(array('message' => "Securtiy token is missing#review",'status_code' => 404  ));die;
		  }else{
			   if (!$json_api->query->cookie) {
					echo json_encode(array('message' => "You must include a 'token' in request header.",'status_code' => 404  ));die;
			   }
			   $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
			   if (!$user_id) {
					$errorArr = array(
								 'status_code' => 404,
								 'data' => array('message' => 'Invalid security token or token expired'));
					echo json_encode(array('message' => "Invalid security token or token expired",'status_code' => 404  ));die;
			   }else{
					return $user_id;
			   }
		  }
		  die;
	 }
	 
	 function check_if_bookmark_plugin_active(){
		  global $json_api;
		  $wishlistPlugin = 'wp-bookmarks/index.php';//User Bookmarks
		  if(!is_plugin_active($wishlistPlugin)){
			   echo json_encode(array('message' => "wp-bookmark plugin either inactive or not installed",'status_code' => 404  ));die;
		  }
		  $tokenPresent = false;
		  foreach (getallheaders() as $name => $value) {
			  // echo "$name => $value";
			   if($name == 'token' || $name == 'Token'){
					$json_api->query->cookie = $_GET['cookie'] = base64_decode($value);
					$tokenPresent = true;
			   }
		  }//headers_list another function for gettting headers
		  
		  if(!$tokenPresent){
			   echo json_encode(array('message' => "Securtiy token is missing123",'status_code' => 404  ));die;
		  }else{
			   if (!$json_api->query->cookie) {
					echo json_encode(array('message' => "You must include a 'token' in request header.",'status_code' => 404  ));die;
			   }
			   $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
			   if (!$user_id) {
					$errorArr = array(
								 'status_code' => 404,
								 'data' => array('message' => 'Invalid security token or token expired'));
					echo json_encode(array('message' => "Invalid security token or token expired",'status_code' => 404  ));die;
			   }else{
					return $user_id;
			   }
		  }
		  die;
	 }
	 
	 function frc_pim_draw_notice_json_api() {
		echo '<div id="message" class="error fade"><p style="line-height: 150%">';
		_e('<strong>File Location: /plugins/wp-rest-api-frc/includes/functions.php<br/>JSON API User</strong></a> requires the JSON API plugin to be activated. Please <a href="wordpress.org/plugins/json-api/‎">install / activate JSON API</a> first.', 'json-api-user');
	
		echo '</p></div>';
	 }
	
	 if (!is_plugin_active('json-api/json-api.php')) {
		  add_action('admin_notices', 'frc_pim_draw_notice_json_api');
		  return;
	 }
?>