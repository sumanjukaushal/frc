<?php

// init
add_action( 'admin_init', 'aitDirCreateRoles');

// admin customization
add_filter( 'pre_get_posts', 'aitDirShowOnlyMyItems');
add_filter( 'views_edit-ait-dir-item', 'aitDirItemsTableViews');
add_filter( 'admin_notices', 'aitDirShowNotices' );
add_action( 'add_meta_boxes', 'aitDirItemDetailedCapabilities');
add_action( 'init', 'aitDirItemRemoveFeatures');
add_action( 'pre_get_posts', 'aitDirShowOnlyOwnAttachments');
add_filter( 'add_menu_classes', 'aitDirRemoveMediaAdminButton');
add_filter( 'manage_users_columns', 'aitDirUsersShowDetails');
add_filter( 'manage_users_custom_column', 'aitDirShowDetailsData', 10, 3);

// registrations functionality
require_once dirname(__FILE__) . '/class-paypal.php';
require_once dirname(__FILE__) . '/accounts-reg.php';

function aitDirCreateRoles() {

	global $pagenow, $typenow, $current_user, $aitThemeOptions;

	// directory items capability
	$capabilityType = 'ait-dir-item';
	$capabilitiesAdmin = array(
		"edit_{$capabilityType}" => true,
		"read_{$capabilityType}" => true,
		"delete_{$capabilityType}" => true,
		"edit_{$capabilityType}s" => true,
		"edit_others_{$capabilityType}s" => true,
		"publish_{$capabilityType}s" => true,
		"read_private_{$capabilityType}s" => true,
		"delete_{$capabilityType}s" => true,
		"delete_private_{$capabilityType}s" => true,
		"delete_published_{$capabilityType}s" => true,
		"delete_others_{$capabilityType}s" => true,
		"edit_private_{$capabilityType}s" => true,
		"edit_published_{$capabilityType}s" => true,
		"assign_dir_category" => true,
		"assign_dir_location" => true
	);

	// set admin capability
	$adminRole = get_role( 'administrator' );
	foreach ($capabilitiesAdmin as $key => $value) {
		$adminRole->add_cap( $key );
	}

	$subscriberRole = get_role( 'subscriber' );
	$subscriberRole->add_cap( 'directory_account_update' );

	// update user roles from admin
	if(isset($_POST['action']) && $_POST['action'] == 'update' && strpos($_SERVER['PHP_SELF'],'options.php') !== false){
		$optionName = $_POST['option_page'];
		if(isset($_POST[$optionName]) && isset($_POST[$optionName]['members'])){

			$prefixName = '';
			$options = $_POST[$optionName]['members'];
			$roles = $GLOBALS['wp_roles']->role_names;

			$capabilitiesDirectory = array(
				"edit_{$capabilityType}s" => true,
				"read_private_{$capabilityType}s" => false,
				"edit_published_{$capabilityType}s" => true,
				"delete_{$capabilityType}s" => true,
				"delete_published_{$capabilityType}s" => true,
				"assign_dir_category" => true,
				"assign_dir_location" => true,
				"read" => true,
				"upload_files" => true,
				"directory_account_update" => true
			);

			if(isset($options['role1Enable'])){
				remove_role( 'directory_1' );
				$caps = $capabilitiesDirectory;
				if(!isset($options['role1Approve'])){
					$caps["publish_{$capabilityType}s"] = true;
				}
				add_role( 'directory_1', $prefixName . $options['role1Name'], $caps);
			} else {
				remove_role( 'directory_1' );
			}

			if(isset($options['role2Enable'])){
				remove_role( 'directory_2' );
				$caps = $capabilitiesDirectory;
				if(!isset($options['role2Approve'])){
					$caps["publish_{$capabilityType}s"] = true;
				}
				add_role( 'directory_2', $prefixName . $options['role2Name'], $caps);
			} else {
				remove_role( 'directory_2' );
			}

			if(isset($options['role3Enable'])){
				remove_role( 'directory_3' );
				$caps = $capabilitiesDirectory;
				if(!isset($options['role3Approve'])){
					$caps["publish_{$capabilityType}s"] = true;
				}
				add_role( 'directory_3', $prefixName . $options['role3Name'], $caps);
			} else {
				remove_role( 'directory_3' );
			}

			if(isset($options['role4Enable'])){
				remove_role( 'directory_4' );
				$caps = $capabilitiesDirectory;
				if(!isset($options['role4Approve'])){
					$caps["publish_{$capabilityType}s"] = true;
				}
				add_role( 'directory_4', $prefixName . $options['role4Name'], $caps);
			} else {
				remove_role( 'directory_4' );
			}

			if(isset($options['role5Enable'])){
				remove_role( 'directory_5' );
				$caps = $capabilitiesDirectory;
				if(!isset($options['role5Approve'])){
					$caps["publish_{$capabilityType}s"] = true;
				}
				add_role( 'directory_5', $prefixName . $options['role5Name'], $caps);
			} else {
				remove_role( 'directory_5' );
			}

		}
	}

	// check number of posts for directory users
	$usrRoles = $current_user->roles;
	if (isDirectoryUser()) {
		// Untrash
		if($pagenow == 'post.php' && !empty($_REQUEST['post']) && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'untrash') {
			$post = get_post($_REQUEST['post']);
			$typenow = (isset($post)) ? $post->post_type : '';
			return;
		}
		// Add new item or untrash
		if($typenow == 'ait-dir-item' && ($pagenow == 'post-new.php' || ($pagenow == 'post.php' && isset($post)))) {

			$params = array(
				'post_type'			=> 'ait-dir-item',
				'post_status' => array('publish', 'pending', 'draft', 'trash'),
				'author'			=> $current_user->ID
			);
			$itemsQuery = new WP_Query();
			$items = $itemsQuery->query($params);

			$backUrl = admin_url('edit.php?post_type=ait-dir-item&dir_notification=1');

			if(in_array('directory_1', $usrRoles)){
				if(count($items) >= intval($aitThemeOptions->members->role1Items)){
					header('Location: ' . $backUrl);
					exit();
				}
			} elseif (in_array('directory_2', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role2Items)){
					header('Location: ' . $backUrl);
					exit();
				}
			} elseif (in_array('directory_3', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role3Items)){
					header('Location: ' . $backUrl);
					exit();
				}
			} elseif (in_array('directory_4', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role4Items)){
					header('Location: ' . $backUrl);
					exit();
				}
			} elseif (in_array('directory_5', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role5Items)){
					header('Location: ' . $backUrl);
					exit();
				}
			}
		}
	}
}

/**
 * Don't show others items for directory roles
 */
function aitDirShowOnlyMyItems($query) {
	if (isDirectoryUser()) {
		if((strpos($_SERVER['PHP_SELF'],'edit.php') !== false) && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-dir-item')){
			$query->set('author',$GLOBALS['current_user']->ID);
		}
	}
	return $query;
}

/**
 * Correct number of items for directory users
 */
function aitDirItemsTableViews($views) {
	if (isDirectoryUser()) {
		global $wpdb;

		$user = wp_get_current_user();
		$type = 'ait-dir-item';

		//$cache_key = $type;

		$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d";
		$query .= ' GROUP BY post_status';

		//$count = wp_cache_get($cache_key, 'counts');
		//if ( false !== $count )
		//	return $count;

		$count = $wpdb->get_results( $wpdb->prepare( $query, $type, $user->ID ), ARRAY_A );

		$stats = array();
		foreach ( get_post_stati() as $state )
			$stats[$state] = 0;

		foreach ( (array) $count as $row )
			$stats[$row['post_status']] = $row['num_posts'];

		$stats = (object) $stats;
		//wp_cache_set($cache_key, $stats, 'counts');

		global $locked_post_status, $avail_post_stati;

		$post_type = $type;

		if ( !empty($locked_post_status) )
			return array();

		$status_links = array();
		$num_posts = $stats;
		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();

		$total_posts = array_sum( (array) $num_posts );

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' , 'ait'), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}
		return $status_links;
	} else {
		return $views;
	}
}

/**
 * Show error notice if max items was exceeded or registration messages
 */
function aitDirShowNotices() {
	global $registerMessages;

	if(isset($_GET['dir_notification']) && $_GET['dir_notification'] == '1'){
		echo '<div class="error"><p>'.__('Sorry but you have maximum items for your account type. Upgrade your account to insert new items!','ait').'</p></div>';
	}
	if(isset($registerMessages)){
		echo '<div class="updated"><p>'.$registerMessages.'</p></div>';
	}
}

function aitDirItemDetailedCapabilities($data) {

	global $aitThemeOptions, $wp_meta_boxes, $current_user;
	$usrRoles = $current_user->roles;

	// remove featured options for non administrators
	if (isset($wp_meta_boxes['ait-dir-item']) && (!in_array('administrator', $usrRoles))) {
		foreach ($wp_meta_boxes['ait-dir-item'] as $contextName => $context) {
			foreach ($context as $boxesName => $boxes) {
				foreach ($boxes as $boxName => $box) {
					if (isset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['featured'])) {
						unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['featured']);
					}
				}
			}
		}
	}

	if (isDirectoryUser()) {
		$usrRoles = $current_user->roles;
		$roleNumber = substr(reset($usrRoles), 10);

		$nameAddress = 'role'.$roleNumber.'Address';
		$nameStreetview = 'role'.$roleNumber.'Streetview';
		$nameTelephone = 'role'.$roleNumber.'Telephone';
		$nameEmail = 'role'.$roleNumber.'Email';
		$nameSendEmail = 'role'.$roleNumber.'SendEmail';
		$nameWeb = 'role'.$roleNumber.'Web';
		$nameHours = 'role'.$roleNumber.'Hours';
		$nameAlternativeContent = 'role'.$roleNumber.'AlternativeContent';
		$nameSocial = 'role'.$roleNumber.'Social';
		$nameSpecial = 'role'.$roleNumber.'Special';
		$nameGallery = 'role'.$roleNumber.'Gallery';

		if(isset($wp_meta_boxes['ait-dir-item'])){
			foreach ($wp_meta_boxes['ait-dir-item'] as $contextName => $context) {
				foreach ($context as $boxesName => $boxes) {
					foreach ($boxes as $boxName => $box) {
						// item options
						if($boxName == '_ait-dir-item_metabox'){
							//unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['address']);
							if(!isset($aitThemeOptions->members->$nameAddress)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['address']);
							}
							if(!isset($aitThemeOptions->members->$nameStreetview)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['showStreetview']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewHeading']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewLatitude']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewLongitude']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewPitch']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewZoom']);
							}
							if(!isset($aitThemeOptions->members->$nameTelephone)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['telephone']);
							}
							if(!isset($aitThemeOptions->members->$nameEmail)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['email']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['emailContactOwner']);
							}
							if(!isset($aitThemeOptions->members->$nameSendEmail)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['emailContactOwner']);
							}
							if(!isset($aitThemeOptions->members->$nameWeb)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['web']);
							}
							if(!isset($aitThemeOptions->members->$nameHours)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Opening Hours']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursMonday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursTuesday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursWednesday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursThursday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursFriday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursSaturday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursSunday']);
							}
							if(!isset($aitThemeOptions->members->$nameAlternativeContent)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Alternative Content']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['alternativeContent']);
							}
							// if(!isset($aitThemeOptions->members->$nameSocial)){
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Social Icons']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg1']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink1']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg2']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink2']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg3']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink3']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg4']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink4']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg5']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink5']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialImg6']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['socialLink6']);
							// }
							// if(!isset($aitThemeOptions->members->$nameSpecial)){
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Special Offer']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['specialActive']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['specialTitle']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['specialContent']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['specialImage']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['specialPrice']);
							// }
							// if(!isset($aitThemeOptions->members->$nameGallery)){
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Gallery']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['galleryEnable']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery1']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery2']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery3']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery4']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery5']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery6']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery7']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery8']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery9']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery10']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery11']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery12']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery13']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery14']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery15']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery16']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery17']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery18']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery19']);
							// 	unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['gallery20']);
							// }
						}
					}
				}
			}
		}
	}
}

function aitDirItemRemoveFeatures() {
	global $aitThemeOptions, $current_user;

	if (isDirectoryUser()) {
		$usrRoles = $current_user->roles;
		$roleNumber = substr(reset($usrRoles), 10);

		$nameEditor = 'role'.$roleNumber.'Content';
		$nameImage = 'role'.$roleNumber.'Image';
		$nameExcerpt = 'role'.$roleNumber.'Excerpt';
		$nameReviews = 'role'.$roleNumber.'Reviews';

		// attributes
		remove_post_type_support( 'ait-dir-item', 'page-attributes' );
		// editor - content
		if(!isset($aitThemeOptions->members->$nameEditor)){
			remove_post_type_support( 'ait-dir-item', 'editor' );
		}
		// image - thumbnail
		if(!isset($aitThemeOptions->members->$nameImage)){
			remove_post_type_support( 'ait-dir-item', 'thumbnail' );
		}
		// excerpt
		if(!isset($aitThemeOptions->members->$nameExcerpt)){
			remove_post_type_support( 'ait-dir-item', 'excerpt' );
		}
		// reviews - comments
		if(!isset($aitThemeOptions->members->$nameReviews)){
			remove_post_type_support( 'ait-dir-item', 'comments' );
		}

	}
}

function aitDirShowOnlyOwnAttachments( $query ) {
	global $current_user, $pagenow;
	if (isDirectoryUser()) {
		if( 'upload.php' == $pagenow || 'admin-ajax.php' == $pagenow || 'media-upload.php' == $pagenow) {
			$query->set('author', $current_user->ID );
		}
	}
	return $query;
}

function aitDirRemoveMediaAdminButton($menu) {
	global $current_user;
	if (isDirectoryUser()) {
		foreach ($menu as $key => $item) {
			if($item[1] == 'upload_files'){
				unset($menu[$key]);
			}
		}
	}
	return $menu;
}

function aitDirUsersShowDetails($columns) {
	global $aitThemeOptions;
	$columns['items'] = __('Items','ait');
	if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
		$columns['recurring_profile_id'] = __('PayPal recurring payments profile ID','ait');
	} else {
		$columns['activation_time'] = __('Activation time','ait');
		$columns['days_before_expiration'] = __('Days left before expiration','ait');
		$columns['transaction_id'] = __('Last PayPal transaction ID','ait');
	}
	return $columns;
}

function aitDirShowDetailsData($empty='', $column_name, $id) {
	if( $column_name == 'items' ) {
		return getAuthorItemsCount($id);
	}
	if( $column_name == 'activation_time' ) {
		$data = get_user_meta( $id, 'dir_activation_time', true );
		if ($data) {
			$dateFormat = get_option( 'date_format', 'm/d/Y' );
			return date($dateFormat,$data['time']);
		}
	}
	if( $column_name == 'days_before_expiration' ) {
		$user = new WP_User($id);
		if (isDirectoryUser($user)) {
			return aitDirGetDaysLeft($user->ID);
		} else {
			return '';
		}
	}
	if( $column_name == 'transaction_id' ) {
		$data = get_user_meta( $id, 'dir_paypal_transaction_id', true );
		if ($data) {
			return $data;
		}
	}
	if( $column_name == 'recurring_profile_id' ) {
		$data = get_user_meta( $id, 'dir_paypal_recurring_profile_id', true );
		if ($data) {
			return $data;
		}
	}
}

function getAuthorItemsCount($id) {
	global $wpdb;

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'ait-dir-item' AND post_status = 'publish' AND post_author = ".$id );
	return $count;
}
