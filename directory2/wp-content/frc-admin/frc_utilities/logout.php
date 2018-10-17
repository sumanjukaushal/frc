<?php
	require_once("../../../wp-config.php");
	require_once('../../../wp-load.php');
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
//Start: commented by yamini----------------
	//	function logout_this_session() {
	//        //Logout Now
	//        wp_logout();
	//        wp_die();
	//    }
	//	logout_this_session();
	//	wp_logout();
	//	$loginURL = wp_login_url( $redirect );
	//echo "<script>location.href='$loginURL';</script>";
//End: commented by yamini----------------
	
//Start: Added by yamini------------------
	$yourSession= WP_Session_Tokens::get_instance(get_current_user_id());
	$yourSession->destroy_all();
	$loginURL = WP_CONTENT_URL.DS."users".DS."login.php";
	wp_redirect($loginURL);die;
//End: Added by yamini------------------
?>