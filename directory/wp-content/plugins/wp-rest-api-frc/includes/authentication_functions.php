<?php
	 //1: User Login
	 function frc_login(){
		  $ipAddress = $_SERVER['REMOTE_ADDR'];
		  global $json_api;
		  foreach($_POST as $k => $val) {
			   if (isset($_POST[$k])) {$json_api->query->$k = $val;}
		  }
		  
		  if (!$json_api->query->username && !$json_api->query->email){
			   echo json_encode(array('message' => "username and password is required.",'status_code' => 404  ));die;
			   return new WP_Error( 'username_pwd_missing', 'username and password is required.', array( 'status' => 400 ) );
		  }//$json_api->error("You must include 'username' or 'email' var in your request to generate cookie.");


		  if (!$json_api->query->password) {
			   echo json_encode(array('message' => "Password is required",'status_code' => 400  ));die;
			   return new WP_Error( 'username_pwd_missing', 'Password is required.', array( 'status' => 400 ) );
		  }//$json_api->error("You must include a 'password' var in your request.");	
		
		  if ($json_api->query->seconds)
			   $seconds = (int) $json_api->query->seconds;
		  else
			   $seconds = 1209600;//14 days

		  if ( $json_api->query->email ) {
			   if ( is_email(  $json_api->query->email ) ) {
					if( !email_exists( $json_api->query->email))  {
						 $json_api->error("email does not exist."); 
					}
			   }else
					$json_api->error("Invalid email address."); 
		   
			   $user_obj = get_user_by( 'email', $json_api->query->email );
			   $user = wp_authenticate($user_obj->data->user_login, $json_api->query->password);
		  }else {
			   $user = wp_authenticate($json_api->query->username, $json_api->query->password);
		  }

		  if (is_wp_error($user)) {
			$error_types = array_keys($user->errors);
			//print_r($user->errors);
			//$json_api->error("Invalid username/email and/or password.", 'error', '401');
			echo json_encode(array('message' => "Invalid username/email and/or password.#",'status_code' => 400  ));
			remove_action('wp_login_failed', $json_api->query->username);
			die;
		  }
		  $retunArr = is_member_premium($user->ID);
		  $ipAddress = $_SERVER['REMOTE_ADDR'];
		  /*if($ipAddress == '117.225.120.83'){
			   echo "================== fasdfsaf  ==================";
		  }else{
			   echo "================== $ipAddress  ==================";
		  }*/
		  $foundationArr = if_foundation_level($user->ID);
		  if($ipAddress == '117.225.120.83'){
			  // print_r($foundationArr);
		  }
		  list($premiumMembership, $expired) = $retunArr;
		  list($foundationMembership, $foundationExpired) = $foundationArr;
		  if($foundationMembership || $json_api->query->username == 'frcpremuser'){
			   ;//do nothing, all good
		  }elseif(!$premiumMembership){
			   echo json_encode(array('message' => "Only Premium Members are allowed to use Mobile App",'status_code' => 400  ));
			   remove_action('wp_login_failed', $json_api->query->username);
			   die;
		  }elseif($expired){
			   echo json_encode(array('message' => "Your Premium Membership is expired. Please renew premium membership to login",'status_code' => 400  ));
			   remove_action('wp_login_failed', $json_api->query->username);
			   die;
		  }
		  $expiration = time() + apply_filters('auth_cookie_expiration', $seconds, $user->ID, true);
		  $cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
		  preg_match('|src="(.+?)"|', get_avatar( $user->ID, 512 ), $avatar);
		  $userInfo = frc_user_profile($user->ID);
		  $dataArr = array(
							  'token' => base64_encode($cookie),
							  'user' => $userInfo,
							  );
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $dataArr), 200);
		  //return array("cookie" => $cookie,"cookie_name" => LOGGED_IN_COOKIE);
	 }
?>