<?php
/*
Plugin Name: FRC feature managements
Plugin URI: https://freerangecamping.com.au
Description: Show/hide features and Categories based on Tab choosen 
Version: 1.0
Author: Mohit Kaushal 
Author URI: https://freerangecamping.com.au
*/
	define('FRC_FEATURES_PLUGIN_URL', plugin_dir_url( __FILE__ ));
	define('FRC_FEATURES_PLUGIN_PATH', plugin_dir_path(__FILE__));
	register_activation_hook(__FILE__, 'frc_fn_feature_activation');
	register_deactivation_hook(__FILE__, 'frc_fn_feature_deactivation');
	function frc_fn_feature_activation() {
		//register uninstaller
		register_uninstall_hook(__FILE__, 'frc_feature_deactivation');
	}
	
	
	function frc_fn_feature_deactivation() {    
		// actions to perform once on plugin deactivation go here	    
	}
	/*****************************************
	* Enqueue  js and css files for Pluginpress
	****************************************/
	if(!function_exists('frc_feature_assets')) {
		function frc_feature_assets() { 
			$post_type = frc_post_type();
			if($post_type == 'ait-item'){
				//wp_register_script('frc_feature_js' ,  FRC_FEATURES_PLUGIN_URL.'js/manage-features.js', array(  ), '1.01', true) ; 
				//wp_enqueue_script('frc_feature_js');
		
				/*wp_register_style('kv_bootstrap_css' , plugins_url('bootstrap.css' , __FILE__)) ; 
				wp_enqueue_style('kv_bootstrap_css');
		
				wp_register_style('kv_admin_css' , plugins_url('style.css' , __FILE__)) ; 
				wp_enqueue_style('kv_admin_css');*/
			}
		}
		add_action('admin_head' , 'frc_feature_assets');
	}
	add_action('admin_footer', 'frc_footer_function', '100');
	function frc_footer_function() {
		$post_type = frc_post_type();
		if($post_type == 'ait-item'){
			echo "if frc_footer_function $post_type";
			include(FRC_FEATURES_PLUGIN_PATH.'/js/manage-features.php');
			include(FRC_FEATURES_PLUGIN_PATH.'/includes/footer_code.php');
		}else{
			echo "else frc_footer_function $post_type";
		}
	}
	add_action( 'edit_form_top', 'frc_manage_item_tabs');
	$tabArr = array();
	function frc_post_type(){
		if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'ait-item'){
			return  'ait-item';
		}elseif(isset($_REQUEST['post'])){
			$postID = (int)$_REQUEST['post'];
			return get_post_type($postID);
		}
		return '';
	}
	function frc_manage_item_tabs(){ //edit_form_after_title
		global $tabArr;
		$post_type = frc_post_type();
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if($post_type == 'ait-item'){
			global $current_user;
			wp_get_current_user();
			$userRolesArr = $current_user->roles;
			if($ipAddress == '27.255.170.61'){
				;//echo "<pre>";print_r($userRolesArr);echo "</pre>";
				//$userRolesArr is containing roles that we are assigning to user while level is different.
				//Note:levels are membership level under which user can be registered under multiple registration and
				//Tab should appear on the basis of Level not roles
			}
			//echo "<pre>";print_r($userRolesArr);echo "</pre>";//administrator		
			if(
				in_array('administrator',$userRolesArr) 
				|| in_array('admin_role',$userRolesArr) 
				|| in_array('new_admin_role',$userRolesArr) 
				|| in_array('admin_role2', $userRolesArr)
			){
				$tabArr = array(
					'directory_1' => 'Listing Manager',
					'directory_2' => 'Business Listing',
					'directory_3' => 'Campgrounds',//'Low Cost Sites',
					'directory_4' => 'House Sitting',
					'directory_5' => 'Park Overs',
					'directory_6' => 'Help Outs',
					'directory_7' => 'Caravan Parks',//'$25 +',
				);
			}
											
			/*
			Note: In this case directory user role + the listing directory user has joined merged
			If suppose user own role is is House Sitting (directory_4) and user has also registered for
			adding listing to Park Overs, than Park Over role will also be added (in this case directory_5)
			*/
			
			if(in_array('directory_1',$userRolesArr)){
				$tabArr = array('directory_1' => 'Listing Manager');
			}
			if(in_array('directory_2',$userRolesArr)){
				$meta_key = 'role';
				$meta_value = '3';
				update_user_meta( $user_id, $meta_key, $meta_value);
				if($ipAddress == '124.253.93.91'){
					echo $current_user->ID;
				}
				$tabArr = array('directory_2' => 'Business Listing');
			}		
			if(in_array('directory_3',$userRolesArr)){
				$tabArr = array('directory_3' => 'Campgrounds'); //Low Cost Sites
			}
			if(in_array('directory_4',$userRolesArr)){
				$tabArr = array('directory_4' => 'House Sitting');
			}
			
			if(in_array('directory_5',$userRolesArr)){
				$tabArr = array('directory_5' => 'Park Overs');
			}
			if(in_array('directory_6',$userRolesArr)){
				$tabArr = array('directory_6' => 'Help Outs');
			}
			if(in_array('directory_7',$userRolesArr)){
				$tabArr = array('directory_7' => 'Caravan Parks'); //$25 +
			}
			// echo "<pre>";print_r($userRolesArr);print_r($tabArr);echo "</pre>";
	
			//start of level directory code
			$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
			if(is_plugin_active($wishlistPlugin)){
				$ipAddress = $_SERVER['REMOTE_ADDR'];
				$levels = WLMAPI::GetUserLevels($current_user->ID);
				if($ipAddress == '27.255.170.61'){
					//echo "<pre>";print_r($levels);echo "</pre>";
				}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Free Listings', $levels) ){$tabArr['directory_1'] = 'Free Listings';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Businesses Listings', $levels) ){$tabArr['directory_2'] = 'Businesses Listings';}
				if(
				   in_array('new_admin_role',$userRolesArr) ||
				   in_array('admin_role',$userRolesArr) ||
				   in_array('Low Cost Sites', $levels) ||
				   in_array('Low Cost Camps', $levels) ||
				   in_array('Campgrounds', $levels)
				   ){
					//$tabArr['directory_3'] = 'Low Cost Sites';
					$tabArr['directory_3'] = 'Campgrounds';
				}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('House Sitting', $levels) ){$tabArr['directory_4'] = 'House Sitting';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Park Overs', $levels) ){$tabArr['directory_5'] = 'Park Overs';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Help Outs', $levels) ){$tabArr['directory_6'] = 'Help Outs';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('$25+ Camps/Caravan Parks', $levels) ||
				   in_array('Caravan Parks', $levels) ||
				   in_array('$25 +', $levels) ||
				   in_array('25plus', $levels)
				){
					$tabArr['directory_7'] = 'Caravan Parks'; //$25 +
				}
				ksort($tabArr);
			}
			//end of level directory code
			//echo "<pre>";print_r($tabArr);echo "</pre>";
			echo "<style>h2 .nav-tab {font-size: 11px;padding: 3px 5px;margin-right:3px;}</style>";
			echo '<h2 class="nav-tab-wrapper">';
			$inc = 0;
			foreach($tabArr as $tab_id => $tab_name){
				if($inc == 0){
					echo "<a class='nav-tab nav-tab-active' id='$tab_id' href='#tab{$tab_id}' onClick='setHash(\"$tab_id\");' style='margin-right:3px;'>$tab_name</a>";
				}else{
					echo "<a class='nav-tab' href='#tab{$tab_id}' id='$tab_id' onClick='setHash(\"$tab_id\");' style='margin-right:3px;'>$tab_name</a>";
				}
				$inc++;
			}
			
			echo '</h2>';
		}
	}
	
	function frc_get_current_tabs(){ //edit_form_after_title
		global $post, $wpdb;
		$post_type = frc_post_type();
		$tabArr = array();
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if($post_type == 'ait-item'){
			global $current_user;
			wp_get_current_user();
			$userRolesArr = $current_user->roles;
				
			if(
				in_array('administrator',$userRolesArr) 
				|| in_array('admin_role',$userRolesArr) 
				|| in_array('new_admin_role',$userRolesArr) 
				|| in_array('admin_role2', $userRolesArr)
			){
				$tabArr = array(
					'directory_1' => 'Listing Manager',
					'directory_2' => 'Business Listing',
					'directory_3' => 'Campgrounds',//'Low Cost Sites',
					'directory_4' => 'House Sitting',
					'directory_5' => 'Park Overs',
					'directory_6' => 'Help Outs',
					'directory_7' => 'Caravan Parks',//'$25 +',
				);
			}
			
			if(in_array('directory_1',$userRolesArr)){$tabArr = array('directory_1' => 'Listing Manager');}
			if(in_array('directory_2',$userRolesArr)){$tabArr = array('directory_2' => 'Business Listing');}		
			if(in_array('directory_3',$userRolesArr)){$tabArr = array('directory_3' => 'Campgrounds');}
			if(in_array('directory_4',$userRolesArr)){$tabArr = array('directory_4' => 'House Sitting');}
			if(in_array('directory_5',$userRolesArr)){$tabArr = array('directory_5' => 'Park Overs');}
			if(in_array('directory_6',$userRolesArr)){$tabArr = array('directory_6' => 'Help Outs');}
			if(in_array('directory_7',$userRolesArr)){$tabArr = array('directory_7' => 'Caravan Parks');}
	
			//start of level directory code
			$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
			if(is_plugin_active($wishlistPlugin)){
				$ipAddress = $_SERVER['REMOTE_ADDR'];
				$levels = WLMAPI::GetUserLevels($current_user->ID);
				
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Free Listings', $levels) ){$tabArr['directory_1'] = 'Free Listings';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Businesses Listings', $levels) ){$tabArr['directory_2'] = 'Businesses Listings';}
				if(
				   in_array('new_admin_role',$userRolesArr) ||
				   in_array('admin_role',$userRolesArr) ||
				   in_array('Low Cost Sites', $levels) ||
				   in_array('Low Cost Camps', $levels) ||
				   in_array('Campgrounds', $levels)
				   ){
					$tabArr['directory_3'] = 'Campgrounds';
				}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('House Sitting', $levels) ){$tabArr['directory_4'] = 'House Sitting';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Park Overs', $levels) ){$tabArr['directory_5'] = 'Park Overs';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('Help Outs', $levels) ){$tabArr['directory_6'] = 'Help Outs';}
				if( in_array('new_admin_role',$userRolesArr) || in_array('admin_role',$userRolesArr) || in_array('$25+ Camps/Caravan Parks', $levels) ||
				   in_array('Caravan Parks', $levels) ||
				   in_array('$25 +', $levels) ||
				   in_array('25plus', $levels)
				){
					$tabArr['directory_7'] = 'Caravan Parks'; //$25 +
				}
				ksort($tabArr);
			}
		}
		$postID = $post->ID;
		$firstTab = '';
		if($postID){
			$postMetaTbl = $wpdb->prefix."postmeta";
			$itemMetaQry = "SELECT * FROM `$postMetaTbl` WHERE `meta_key` ='_ait-item_item-data' AND `post_id` = $postID";
			$itemMetaRes = $wpdb->get_results($itemMetaQry);
			if(count($itemMetaRes) >= 1){
				$metaArr = unserialize($itemMetaRes[0]->meta_value);//print_r($metaArr);
				if(array_key_exists('directoryType', $metaArr)){
					$firstTab = $metaArr['directoryType'];
				}
			}
		}
		if(count($tabArr) && (empty($firstTab) || !array_key_exists($firstTab, $tabArr))){
			$firstTab = $tabArr[0];
		}
		return array($tabArr, $firstTab);
	}
?>