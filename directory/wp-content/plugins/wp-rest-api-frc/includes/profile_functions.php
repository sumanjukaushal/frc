<?php
	 //1 : Get user profile
	 function frc_userinfo(){
		  global $wpdb;
		  $user_id = check_if_bookmark_plugin_active();
		  $userInfo = frc_user_profile($user_id);
		  $userInfo['suburb'] = "";
		  $userInfo['post_code'] = "";
		  if(array_key_exists('first_name', $userInfo)) $userInfo['first_name'] = str_replace('&amp;', '&', $userInfo['first_name']);
		  if(array_key_exists('last_name', $userInfo)) $userInfo['last_name'] = str_replace('&amp;', '&', $userInfo['last_name']);
		  //$userInfo['post_code_3'] = "";
		  $userMetaTbl = $wpdb->prefix."usermeta";
		  $query = "SELECT `meta_key`, `meta_value` FROM `$userMetaTbl` WHERE `user_id`=$user_id AND `meta_key` IN ('billing_postcode', 'billing_city')";
          $results = $wpdb->get_results($query);
		  $mapFields = array('billing_city' => 'suburb', 'billing_postcode' => 'post_code');
		  if(count($results) > 0){
			   foreach($results as $result){
					$fieldName = $mapFields[$result->meta_key];
					if(!empty($fieldName)){
						 $userInfo[$fieldName] = $result->meta_value;
					}
               }
		  }
		  list($premiumMembership, $expired) = is_member_premium($user_id);
		  $userInfo['premium_member'] = $premiumMembership;
		  
		  $userInfo['collections'] = frc_getFavouriteCategoryList($user_id);
		  //defined in collection_functions.php
		  
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $userInfo), 200);
	 }
	 
	 function frc_edit_userinfo($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $first_name = $request['first_name']; //required
		  $last_name = $request['last_name'];	//required
		  $description = $request['bio_info'];
		  $user_pass = $request['password'];
		  
		  if(empty($first_name)){
			   echo json_encode(array('message' => "First name is required.",'status_code' => 400  ));die;
		  }elseif(empty($last_name)){
			   echo json_encode(array('message' => "Last name is required.",'status_code' => 400  ));die;
		  }
		  $dataArr = array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name);
		  if(!empty($description))$dataArr['description'] = $description;
		  if(!empty($user_pass))$dataArr['user_pass'] = $user_pass;
		  //Other parameters user_url, user_email
		  if ( ! empty( $_FILES['avatar']['name'] ) ) {
			   $user_data = wp_update_user( $dataArr);
			   // Allowed file extensions/types
			   $mimes = array('jpg|jpeg|jpe' => 'image/jpeg','gif' => 'image/gif','png' => 'image/png',);
			   if ( ! function_exists( 'wp_handle_upload' ) )require_once ABSPATH . 'wp-admin/includes/file.php';
			   frc_avatar_delete( $user_id );
			   if ( strstr( $_FILES['avatar']['name'], '.php' ) )
				   wp_die( 'For security reasons, the extension ".php" cannot be in your file name.' );
			   //$upload_overrides = array( 'test_form' => false, 'mimes' => array('jpg' => 'image/jpeg', 'png' => 'image/png') );
			   $_REQUEST['user_id'] = $user_id;
			   $avatar = wp_handle_upload( $_FILES['avatar'], array( 'mimes' => $mimes, 'test_form' => false, 'unique_filename_callback' => function($dir, $name, $ext ){
					$user_id = $_REQUEST['user_id'];
					$user = get_userdata($user_id);
					$rand = rand ( 1000 , 9999 );
					$name = $base_name = $rand.sanitize_file_name( $user->user_login . '_avatar' );
					$number = 1;
					// /home/frcconz/public_html/mobile/wp-content/uploads/2016/12/prithakaushal_avatar.png
					while ( file_exists( $dir . "/$name$ext" ) ) {
						$name = $base_name . '_' . $number;
						$number++;
					}
					return $name . $ext;
					} ) );
			   // Handle failures
			   if ( empty( $avatar['file'] ) ) {  
					switch ( $avatar['error'] ) {
						 case 'File type does not meet security guidelines. Try another.' :
							  echo json_encode(array('message' => "File type does not meet security guidelines. Try another.",'status_code' => 400  ));die;
							 break;
						 default :
							  echo json_encode(array('message' => "There was an error uploading the avatar.".esc_attr( $avatar['error'] ),'status_code' => 400  ));die;
						 }
			   }else{
					//--------------------
					$avatarImg = $avatar['file'];
					if($avatarImg){
						 //echo "\n::AI".$avatarImg;
						 $pos = strpos($avatarImage, '/uploads/');
						 $wp_upload_dir = $avatarPathArr = wp_upload_dir();
						 $avatarPath = $avatarPathArr['basedir'];
						 $subStr = substr($avatarImg, $pos+strlen('/uploads/'));
						 // $filename should be the path to a file in the upload directory.
						 $filename = $avatarPath.DS.$subStr;
						 // The ID of the post this attachment is for.
						 $parent_post_id = 0;
						 // Check the type of file. We'll use this as the 'post_mime_type'.
						 $filetype = wp_check_filetype( basename( $filename ), null );
						 
						 // Prepare an array of post data for the attachment.
						 $attachment = array(
							 'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
							 'post_mime_type' => $filetype['type'],
							 'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
							 'post_content'   => '',
							 'post_status'    => 'inherit'
						 );
						 //print_r($attachment);
						 // Insert the attachment.
						 $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
						 //echo "\nattachment id:".$attach_id;
						 // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
						 require_once( ABSPATH . 'wp-admin/includes/image.php' );
						 
						 // Generate the metadata for the attachment, and update the database record.
						 $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
						 //print_r($attach_data);
						 wp_update_attachment_metadata( $attach_id, $attach_data );
						 //set_post_thumbnail( $parent_post_id, $attach_id );
						 /* save the new attachment instead of the url */
						 $userInfo['attach_id'] = $attach_id;
						 update_user_meta( $user_id, '_wp_attached_file', $attach_id );
					}
			   }
   
			   // Save user information (overwriting previous)
			   $status = update_user_meta( $user_id, 'basic_user_avatar', array( 'full' => $avatar['url'] ) );
			   if ( $status  ) {
					//https://hughlashbrooke.com/2014/03/20/wordpress-upload-user-submitted-files-frontend/
					//http://www.ibenic.com/wordpress-file-upload-with-ajax/
					//https://developer.wordpress.org/reference/functions/wp_unique_filename/
					$userInfo = frc_user_profile($user_id);
					return new WP_REST_Response(array('status_code' => 200, 'data' => $userInfo), 200);
			   }
		  }else{
			   $user_data = wp_update_user( $dataArr);
			   $userInfo = frc_user_profile($user_id);
			   return new WP_REST_Response(array('status_code' => 200, 'data' => $userInfo), 200);
		  }
	 }
	 
	 function frc_avatar_delete($user_id){
		  $old_avatars = get_user_meta( $user_id, 'basic_user_avatar', true );
		  $upload_path = wp_upload_dir();

		  if ( is_array( $old_avatars ) ) {
			   foreach ( $old_avatars as $old_avatar ) {
				   $old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatar );
				   @unlink( $old_avatar_path );
			   }
		  }
		  delete_user_meta( $user_id, 'basic_user_avatar' );
	 }
	
	 function frc_user_profile($user_id){
		  global $wpdb;
		  $old_avatars = get_user_meta( $user_id, 'basic_user_avatar', true );
		  $avatarImage = null;
		  if(is_array($old_avatars) && array_key_exists('full', $old_avatars)){
			   $avatarImage = $old_avatars['full'];
		  }
		  $user = get_userdata($user_id);
		  //preg_match('|src="(.+?)"|', get_avatar( $user->ID ), $avatar);$avatar[1];
		  $userAvatar = get_avatar( $user->ID );
		  $dom = new DOMDocument;
		  $avatarImg = "";
		  if($userAvatar){
			   $dom->loadHTML($userAvatar);
			   $links = $dom->getElementsByTagName('img');
			   foreach ($links as $link){    
				   $avatarImg = $link->getAttribute('src');
			   }
			   if($avatarImg){
					$pos = strpos($avatarImage, '/uploads/');
					$avatarPathArr = wp_upload_dir();
					$avatarPath = $avatarPathArr['basedir'];
					$subStr = substr($avatarImg, $pos+strlen('/uploads/'));
					$avatarPath = $avatarPath.DS.$subStr;
			   }
		  }
		  //The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash, user email
		  //https://secure.gravatar.com/avatar/989bff07401b29ae2704b7163058d564?s=96&#038;d=mm&#038;r=g
		  $membershipQry = "SELECT * FROM `gwr_wlm_userlevels` WHERE user_id=".$user->ID." AND `level_id` IN(1406427506, 1496600364, 1496600500)";
		  //SELECT * FROM `gwr_wlm_userlevels` WHERE `ID`=43370
		  $results = $wpdb->get_results($membershipQry);
		  if(count($results[0]) > 0){
			   $member_issue_date = '';
			   foreach($results as $wlmObj){
					$wlmUserLvlID = $wlmObj->ID;
					$membershipLvlQry = "SELECT * FROM `gwr_wlm_userlevel_options` WHERE `userlevel_id`='$wlmUserLvlID' AND `option_name` = 'registration_date'";
					$lvlResults = $wpdb->get_results($membershipLvlQry);
					if(count($results[0]) > 0){
						 foreach($lvlResults as $wlmLvlObj){
							  list($regDate, $regTime) = explode(" ", $wlmLvlObj->option_value);
							  list($regYear, $regMon, $regDay) = explode("-", $regDate);
							  $member_issue_date = implode("-", array($regDay,$regMon, $regYear));//date('j-n-Y', strtotime($regDate));
						 }
					}
					if(empty($member_issue_date)){
						 $member_issue_date = date('j-n-Y', strtotime($wlmObj->modified));
					}
					break;
			   }
		  }else{
			   $member_issue_date = date('j-n-Y', strtotime($user->user_registered));	//DD/MM/YYYY
		  }
		  $userInfo = array(
							  "user_id" => $user->ID,
							  "member_issue_date" => $member_issue_date,
							  "email" => $user->user_email,
							  "username" => $user->user_login,
							  "last_name" => $user->last_name,
							  "first_name" => $user->user_firstname,
							  "nickname" => $user->nickname,
							  "nicename" => $user->user_nicename,
							  "display_name" => $user->display_name,
							  "avatar" => $avatarImage,
							  "avatar_website" => $avatarImg, //new
							  "bio_info" => $user->user_description,
						 );
		  return $userInfo;
	 }
	 if(!function_exists('wlm_script_url')){
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
	 }
	 
	 function is_member_premium($user_id){
		  global $wpdb;
		  $premiumLvlID = '1406427506';
		  $premiumMembership = false;
		  $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
		  if(is_plugin_active($wishlistPlugin)){
			   $currentURL = wlm_script_url();
			   if(strpos($currentURL, "classifieds")){
				   $wlmKey = "8ce60f0f012df5bb33cacb751f8e90d5";
				   $api = new wlmapiclass('https://www.freerangecamping.com.au/classifieds/', $wlmKey);
			   }elseif(strpos($currentURL, "directory")){
				   $wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
				   $api = new wlmapiclass('https://www.freerangecamping.com.au/directory/', $wlmKey);
			   }
			   
			   $api->return_format = 'php'; // <- value can also be xml or json
			   $levels = WLMAPI::GetLevels();
			   $levelArr = array();
			   foreach($levels as $sngLevel){
				   $levelArr[$sngLevel['ID']] = $sngLevel['name'];
				   //$sngLevel['role'] = directory_4
				   //slug,url = house-sitting
			   }
			   //echo json_encode($levelArr);
			   $userLevels = WLMAPI::GetUserLevels($user_id);
			   $ipAddress = $_SERVER['REMOTE_ADDR'];
			   if(true || '27.255.165.77' == $ipAddress){
					$expVal = $levels[$premiumLvlID]['expire'];
					$expUnit = $levels[$premiumLvlID]['calendar'];
					$expTime = "$expVal $expUnit";	//print_r($userLevels);
					$userLevelTbl = $wpdb->prefix.'wlm_userlevels';
					$wlmUserOptTbl = $wpdb->prefix.'wlm_userlevel_options';
					$query = "SELECT * FROM `$userLevelTbl` WHERE `user_id` = $user_id AND `level_id` = '$premiumLvlID'";
					$expired = false;
					$results = $wpdb->get_results($query);
					
					if(count($results[0]) > 0){
						 $userLevelID = $results[0]->ID;
						 $optQry = "SELECT * FROM `$wlmUserOptTbl` WHERE `userlevel_id`= $userLevelID and `option_name` = 'registration_date'";
						 $optResults = $wpdb->get_results($optQry);
						 if(count($optResults[0]) > 0){
							  $regDate = $optResults[0]->option_value;
							  list($registrationDate) = explode(" ",$regDate);
							  $newDate = date('Y-m-d', strtotime($registrationDate. " + $expTime"));
							  $currentTime = new DateTime('now');
							  $expTime = new DateTime($newDate);
							  $diffInSeconds = $expTime->getTimestamp() - $currentTime->getTimestamp();
							  $expired = $diffInSeconds >= 0 ? false : true;
						 }
					}
			   }
			   foreach($userLevels as $levelID => $sngLevel){
					if(
					   strpos("---".strtolower($sngLevel),'premium') ||
					   strpos("---".strtolower($sngLevel),'foundation') ||
					   strpos("---".strtolower($sngLevel),'lifetime ') ||
					   strpos("---".strtolower($sngLevel),'lifetime membership')
					)
					{
						 $premiumMembership = true;
					}
			   }
			   $premiumMembership;
			   //echo json_encode($userLevels);
			   //die;
		  }
		  return array($premiumMembership, $expired);
	 }
	 
	 //----------------------------------
	 function if_foundation_level($user_id){
		  //https://www.freerangecamping.com.au/directory/wp-admin/admin.php?page=WishListMember&wl=members&level=1496600500
		  //SELECT * FROM `gwr_wlm_userlevels` WHERE `user_id` = 53
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
		  1496600500 => Foundation Membership [] 11
		  dollar_25plus => 53
		  */
		  global $wpdb;
		  $premiumLvlID = '1496600500';
		  $foundationMembership = false;
		  $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
		  if(is_plugin_active($wishlistPlugin)){
			   $currentURL = wlm_script_url();
			   if(strpos($currentURL, "classifieds")){
				   $wlmKey = "8ce60f0f012df5bb33cacb751f8e90d5";
				   $api = new wlmapiclass('https://www.freerangecamping.com.au/classifieds/', $wlmKey);
			   }elseif(strpos($currentURL, "directory")){
				   $wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
				   $api = new wlmapiclass('https://www.freerangecamping.com.au/directory/', $wlmKey);
			   }
			   
			   $api->return_format = 'php'; // <- value can also be xml or json
			   $levels = WLMAPI::GetLevels();
			   $levelArr = array();
			   foreach($levels as $sngLevel){
				   $levelArr[$sngLevel['ID']] = $sngLevel['name'];
			   }
			   //echo json_encode($levelArr);
			   $userLevels = WLMAPI::GetUserLevels($user_id);
			   $ipAddress = $_SERVER['REMOTE_ADDR'];
			   //print_r($userLevels);
			   if(true || '117.224.201.107' == $ipAddress){
					$expVal = $levels[$premiumLvlID]['expire'];
					$expUnit = $levels[$premiumLvlID]['calendar'];
					$expTime = "$expVal $expUnit";	//print_r($userLevels);
					$userLevelTbl = $wpdb->prefix.'wlm_userlevels';
					$wlmUserOptTbl = $wpdb->prefix.'wlm_userlevel_options';
					$query = "SELECT * FROM `$userLevelTbl` WHERE `user_id` = $user_id AND `level_id` = '$premiumLvlID'";
					$expired = false;
					$results = $wpdb->get_results($query);
					
					if(count($results[0]) > 0){
						 $userLevelID = $results[0]->ID;
						 $optQry = "SELECT * FROM `$wlmUserOptTbl` WHERE `userlevel_id`= $userLevelID and `option_name` = 'registration_date'";
						 $optResults = $wpdb->get_results($optQry);
						 if(count($optResults[0]) > 0){
							  $regDate = $optResults[0]->option_value;
							  list($registrationDate) = explode(" ",$regDate);
							  $newDate = date('Y-m-d', strtotime($registrationDate. " + $expTime"));
							  $currentTime = new DateTime('now');
							  $expTime = new DateTime($newDate);
							  $diffInSeconds = $expTime->getTimestamp() - $currentTime->getTimestamp();
							  $expired = $diffInSeconds >= 0 ? false : true;
							  $expired = false;//foundation members will never expire
						 }
					}
			   }
			   foreach($userLevels as $levelID => $sngLevel){
					if(
					   strpos("---".strtolower($sngLevel),'foundation') ||
					   strpos("---".strtolower($sngLevel),'lifetime ') ||
					   strpos("---".strtolower($sngLevel),'lifetime membership')
					){
						 $foundationMembership = true;
					}
			   }
			   //echo json_encode($userLevels);
			   //die;
		  }
		  return array($foundationMembership, $expired);
	 }
	 //----------------------------------
?>