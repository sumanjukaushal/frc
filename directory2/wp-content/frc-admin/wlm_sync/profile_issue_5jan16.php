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
	
	function wlm_user_profile($user_id){
		  $old_avatars = get_user_meta( $user_id, 'basic_user_avatar', true );
		  $avatarImage = null;
		  if(array_key_exists('full', $old_avatars)){
			   $avatarImage = $old_avatars['full'];
		  }
		  $user = get_userdata($user_id);
		  preg_match('|src="(.+?)"|', get_avatar( $user->ID ), $avatar);$avatar[1];
		  //The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash, user email
		  //https://secure.gravatar.com/avatar/989bff07401b29ae2704b7163058d564?s=96&#038;d=mm&#038;r=g
		  $userInfo = array(
							  "user_id" => $user->ID,
							  "email" => $user->user_email,
							  "username" => $user->user_login,
							  "last_name" => $user->last_name,
							  "first_name" => $user->user_firstname,
							  "nickname" => $user->nickname,
							  "nicename" => $user->user_nicename,
							  "display_name" => $user->display_name,
							  "avatar" => $avatarImage,
							  "bio_info" => $user->user_description,
						 );
		return $userInfo;
	}
    $userProfile = wlm_user_profile(33841);//33669);34268 is coming in admin
	echo "<pre>";print_r($userProfile);
?>