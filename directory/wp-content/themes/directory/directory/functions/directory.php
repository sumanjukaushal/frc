<?php

add_filter('pre_get_posts','aitDirMainQuery');
function aitDirMainQuery($query) {
	global $aitThemeOptions;
	// only main query
	if($query->is_main_query() && !$query->is_admin){
		// empty search fix
		if (isset($_GET['s']) && empty($_GET['s'])){
			$query->is_search = true;
		}

		if (!isset($_GET['dir-search'])) {
			$_GET['dir-search'] = false;
		} else {
			$_GET['dir-search'] = true;
		}

		if($query->is_search){

			if (isset($_GET['dir-search']) && $_GET['dir-search'] == false) {
				if(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'product'){
					// is woocommerce search
					return $query;
				} else {
					//is default wp post search
					$query->set('post_type','post');
					$query->set('s',$_GET['s']);
				}
			} else {
				// is directory search
				if(!isset($_GET['categories'])) {
					$_GET['categories'] = 0;
				}
				if(!isset($_GET['locations'])) {
					$_GET['locations'] = 0;
				}

				$query->set('post_type','ait-dir-item');

				$taxquery = array();
				$taxquery['relation'] = 'AND';

				if(isset($_GET['categories']) && !empty($_GET['categories'])){
					$taxquery[] = array(
						'taxonomy' => 'ait-dir-item-category',
						'field' => 'id',
						'terms' => array($_GET['categories']),
						'include_children' => true
					);
				}
				if(isset($_GET['locations']) && !empty($_GET['locations'])){
					$taxquery[] = array(
						'taxonomy' => 'ait-dir-item-location',
						'field' => 'id',
						'terms' => array($_GET['locations']),
						'include_children' => true
					);
				}
				$query->set('tax_query',$taxquery);

				$num = (isset($GLOBALS['aitThemeOptions']->search->searchItemsPerPage)) ? $GLOBALS['aitThemeOptions']->search->searchItemsPerPage : 10;
				$query->set('posts_per_page',$num);

				// filter only items by geolocation
				if(isset($_GET['geo'])){
					$category = intval($_GET['categories']);
					$location = intval($_GET['locations']);
					$params = array(
						'post_type'			=> 'ait-dir-item',
						'nopaging'			=>	true,
						'post_status'		=> 'publish'
					);
					$taxquery = array();
					$taxquery['relation'] = 'AND';
					if($category != 0){
						$taxquery[] = array(
							'taxonomy' => 'ait-dir-item-category',
							'field' => 'id',
							'terms' => $category,
							'include_children' => true
						);
					}
					if($location != 0){
						$taxquery[] = array(
							'taxonomy' => 'ait-dir-item-location',
							'field' => 'id',
							'terms' => $location,
							'include_children' => true
						);
					}
					if($category != 0 || $location != 0){
						$params['tax_query'] = $taxquery;
					}
					if($query->get('s') != ''){
						$params['s'] = $query->get('s');
					}
					$itemsQuery = new WP_Query();
					$items = $itemsQuery->query($params);
					$notIn = array();
					// add item details
					foreach ($items as $key => $item) {
						// options
						$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
						// filter radius
						if(!isPointInRadius(intval($_GET['geo-radius']), floatval($_GET['geo-lat']), floatval($_GET['geo-lng']), $item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude'])){
							$notIn[] = $item->ID;
						}
					}
					// filter
					$query->set('post__not_in',$notIn);
				}
			}

		}

		// pagination
		if (!empty($_GET['pagination'])) {
			$query->set('posts_per_page',$_GET['pagination']);
		} else {
			if (isset($_GET['dir-search'])) {
				$num = (isset($aitThemeOptions->search->searchItemsPerPage)) ? $aitThemeOptions->search->searchItemsPerPage : 9;
				$query->set('posts_per_page',$num);
			}
			if (isset($query->query_vars["ait-dir-item-category"])) {
				$num = (isset($aitThemeOptions->directory->categoryItemsPerPage)) ? $aitThemeOptions->directory->categoryItemsPerPage : 10;
				$query->set('posts_per_page',$num);
			}
			if (isset($query->query_vars["ait-dir-item-location"])) {
				$num = (isset($aitThemeOptions->directory->locationItemsPerPage)) ? $aitThemeOptions->directory->locationItemsPerPage : 10;
				$query->set('posts_per_page',$num);
			}
		}

	}
	return $query;
}

/**
 * get items from DB
 * @param  integer $category category ID
 * @param  integer $location location ID
 * @param  string  $search   search keyword
 * @param  array   $radius   (radius in km, lat, lon)
 * @return array             items
 */
function getItems($category = 0, $location = 0, $search = '', $radius = array()) {

	$params = array(
		'post_type'			=> 'ait-dir-item',
		'nopaging'			=>	true,
		'post_status'		=> 'publish'
	);

	$taxquery = array();
	$taxquery['relation'] = 'AND';
	if($category != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-category',
			'field' => 'id',
			'terms' => $category,
			'include_children' => true
		);
	}
	if($location != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-location',
			'field' => 'id',
			'terms' => $location,
			'include_children' => true
		);
	}
	if($category != 0 || $location != 0){
		$params['tax_query'] = $taxquery;
	}

	if($search != ''){
		$params['s'] = $search;
	}

	$itemsQuery = new WP_Query();
	$items = $itemsQuery->query($params);

	// add item details
	foreach ($items as $key => $item) {
		// options
		$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
		// filter radius
		if(!empty($radius) && !isPointInRadius($radius[0], $radius[1], $radius[2], $item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude'])){
			unset($items[$key]);
		} else {
			// link
			$item->link = get_permalink($item->ID);
			// thumbnail
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID), 'full' );
			if($image !== false){
				$item->thumbnailDir = $image[0];
			} else {
				$item->thumbnailDir = $GLOBALS['aitThemeOptions']->directory->defaultItemImage;
			}
			// marker
			$terms = wp_get_post_terms($item->ID, 'ait-dir-item-category');
			$termMarker = $GLOBALS['aitThemeOptions']->directoryMap->defaultMapMarkerImage;
			if(isset($terms[0])){
				$termMarker = getCategoryMeta("marker", intval($terms[0]->term_id));
			}
			$item->marker = $termMarker;
			// excerpt
			$item->excerptDir = aitGetPostExcerpt($item->post_excerpt,$item->post_content);
			// package class
			$item->packageClass = getItemPackageClass($item->post_author);
			// rating
			$item->rating = get_post_meta( $item->ID, 'rating', true );
		}
	}

	return $items;

}

// allow ajax
add_action( 'wp_ajax_get_items', 'getItemsAjax' );
add_action( 'wp_ajax_nopriv_get_items', 'getItemsAjax' );

function getItemsAjax() {
	global $aitThemeOptions;
	global $sitepress;

	// wpml 3.1.5+ hotfix
	// force to change language if ajax request
	if ($sitepress) {
		$sitepress->switch_lang($_POST['wpml_lang'], true);
	}

	// all items
	if(isset($_POST['radius'])){
		$items = getItems($_POST['category'],$_POST['location'],$_POST['search'],$_POST['radius']);
	} else {
		$items = getItems($_POST['category'],$_POST['location'],$_POST['search']);
	}

	foreach ($items as $item) {
		$item->timthumbUrl = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 120, 'h' => 160), "", "&amp;") : '';
		$item->timthumbUrlContent = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 100, 'h' => 100), "", "&amp;") : '';
	}

	$output = json_encode($items);
	// response output
	header( "Content-Type: application/json" );
	echo $output;
	exit;
}

$indentationChars = (isset($GLOBALS['aitThemeOptions']->search->hierarchicalIndentation) && ($GLOBALS['aitThemeOptions']->search->hierarchicalIndentation == 'space') ) ? '&nbsp;&nbsp;' : '-&nbsp;';
function aitGenerateHirerarchicalAutocomplete($categories, $in = 0) {
	global $indentationChars;
	$return = '';
	foreach ($categories as $cat) {
		$return .= '{ value: "' . $cat->term_id . '" , label: "';
		$indentation = '';
		for ($i=0; $i < $in; $i++) {
			$indentation .= $indentationChars;
		}
		$return .= $indentation . htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8', false) . '" },';
		if (!empty($cat->children)) {
			$return .= aitGenerateHirerarchicalAutocomplete($cat->children, $in + 1);
		}
	}
	return $return;
}

function aitSortTermsHierarchicaly(Array &$cats, Array &$into, $parentId = 0) {
	foreach ($cats as $i => $cat) {
		if ($cat->parent == $parentId) {
			$into[$cat->term_id] = $cat;
			unset($cats[$i]);
		}
	}
	foreach ($into as $topCat) {
		$topCat->children = array();
		aitSortTermsHierarchicaly($cats, $topCat->children, $topCat->term_id);
	}
}

function getDirItemsDetails($items) {
	foreach ($items as $item) {
		$item->link = get_permalink($item->ID);
		$image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID), 'full' );
		if($image !== false){
			$item->thumbnailDir = $image[0];
		} else {
			$item->thumbnailDir = $GLOBALS['aitThemeOptions']->directory->defaultItemImage;
		}
		$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
		$item->excerptDir = aitGetPostExcerpt($item->post_excerpt,$item->post_content);
		$item->packageClass = getItemPackageClass($item->post_author);
		$item->rating = get_post_meta( $item->ID, 'rating', true );
	}
	return $items;
}

function isDirectoryUser($userToTest = null) {
	global $current_user;
	$user = (isset($userToTest)) ? $userToTest : $current_user;
	if( isset( $user->roles ) && is_array( $user->roles ) ) {
		if( in_array('directory_1', $user->roles) || in_array('directory_2', $user->roles) || in_array('directory_3', $user->roles) || in_array('directory_4', $user->roles) || in_array('directory_5', $user->roles) ) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * Maps functions
 **/

function isPointInRadius($radiusInKm, $cenLat, $cenLng, $lat, $lng) {
	$radiusInKm = intval($radiusInKm);
	$cenLat = floatval($cenLat);
	$cenLng = floatval($cenLng);
	$lat = floatval($lat);
	$lng = floatval($lng);
	$distance = ( 6371 * acos( cos( deg2rad($cenLat) ) * cos( deg2rad( $lat ) ) * cos( deg2rad( $lng ) - deg2rad($cenLng) ) + sin( deg2rad($cenLat) ) * sin( deg2rad( $lat ) ) ) );
	if($distance <= $radiusInKm){
		return true;
	} else {
		return false;
	}
}

function movePointByMeters($lat, $lng, $top, $left) {
	// Earth’s radius, sphere
	$R = 6378137;

	// Coordinate offsets in radians
	$dLat = floatval($top) / $R;
	$dLng = floatval($left) / ( $R * cos(pi() * floatval($lat) / 180) );

	// OffsetPosition, decimal degrees
	$nlat = floatval($lat) + ( $dLat * 180 / pi() );
	$nlng = floatval($lng) + ( $dLng * 180 / pi() );

	return array( 'lat' => $nlat, 'lng' => $nlng);
}

function parseMapOptions($adminOptions) {
	$options = array();

	$options['draggable'] = (isset($adminOptions->draggable)) ? "true" : "false";
	$options['mapTypeControl'] = (isset($adminOptions->mapTypeControl)) ? "true" : "false";
	$options['mapTypeId'] = 'google.maps.MapTypeId.'.$adminOptions->mapTypeId;
	$options['scrollwheel'] = (isset($adminOptions->scrollwheel)) ? "true" : "false";
	$options['panControl'] = (isset($adminOptions->panControl)) ? "true" : "false";
	$options['rotateControl'] = (isset($adminOptions->rotateControl)) ? "true" : "false";
	$options['scaleControl'] = (isset($adminOptions->scaleControl)) ? "true" : "false";
	$options['streetViewControl'] = (isset($adminOptions->streetViewControl)) ? "true" : "false";
	$options['zoomControl'] = (isset($adminOptions->zoomControl)) ? "true" : "false";

	return $options;
}

$aitCategoryMeta = array();
function getCategoryMeta( $what, $categoryID ) {
	global $aitCategoryMeta, $wpdb;

	// get cache = all values
	if(empty($aitCategoryMeta)){
		$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->options." WHERE option_name LIKE 'ait\_dir\_item\_category\_%'" );
		foreach ($results as $r) {
			preg_match('!\d+!', $r->option_name, $matches);
			if(isset($matches[0])) {
				$id = (int)$matches[0];
				if(!isset($aitCategoryMeta[$id])) {
					$aitCategoryMeta[$id] = array();
				}
				if(strpos($r->option_name,'icon') !== false){
					$aitCategoryMeta[$id]['icon'] = $r->option_value;
				} else if(strpos($r->option_name,'marker') !== false){
					$aitCategoryMeta[$id]['marker'] = $r->option_value;
				} else {
					$aitCategoryMeta[$id]['excerpt'] = $r->option_value;
				}
			}
		}
	}

	switch ($what) {
		case 'icon':
			$anc = get_ancestors( $categoryID, 'ait-dir-item-category' );
			$icon = isset($aitCategoryMeta[$categoryID]['icon']) ? $aitCategoryMeta[$categoryID]['icon'] : '';
			if(empty($icon)){
				foreach ($anc as $value) {
					if(!empty($aitCategoryMeta[$value]['icon'])){
						$icon = $aitCategoryMeta[$value]['icon'];
						break;
					}
				}
				if(empty($icon)){
					$icon = $GLOBALS['aitThemeOptions']->directory->defaultCategoryIcon;
				}
			}
			return $icon;
		case 'marker':
			$anc = get_ancestors( $categoryID, 'ait-dir-item-category' );
			$marker = isset($aitCategoryMeta[$categoryID]['marker']) ? $aitCategoryMeta[$categoryID]['marker'] : '';
			if(empty($marker)){
				foreach ($anc as $value) {
					if(!empty($aitCategoryMeta[$value]['marker'])){
						$marker = $aitCategoryMeta[$value]['marker'];
						break;
					}
				}
				if(empty($marker)){
					$marker = $GLOBALS['aitThemeOptions']->directoryMap->defaultMapMarkerImage;
				}
			}
			return $marker;
		case 'excerpt':
			$excerpt = '';
			if(isset($aitCategoryMeta[$categoryID]['excerpt'])){
				$excerpt = $aitCategoryMeta[$categoryID]['excerpt'];
			}
			return $excerpt;
		default:
			break;
	}
}

$aitLocationMeta = array();
function getLocationMeta( $what, $categoryID ) {
	global $aitLocationMeta, $wpdb;

	// get cache = all values
	if(empty($aitLocationMeta)){
		$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->options." WHERE option_name LIKE 'ait\_dir\_item\_location\_%'" );
		foreach ($results as $r) {
			preg_match('!\d+!', $r->option_name, $matches);
			if(isset($matches[0])) {
				$id = (int)$matches[0];
				if(!isset($aitLocationMeta[$id])) {
					$aitLocationMeta[$id] = array();
				}
				if(strpos($r->option_name,'icon') !== false){
					$aitLocationMeta[$id]['icon'] = $r->option_value;
				} else {
					$aitLocationMeta[$id]['excerpt'] = $r->option_value;
				}
			}
		}
	}

	switch ($what) {
		case 'icon':
			$anc = get_ancestors( $categoryID, 'ait-dir-item-location' );
			$icon = (isset($aitLocationMeta[$categoryID]['icon'])) ? $aitLocationMeta[$categoryID]['icon'] : '';
			if(empty($icon)){
				foreach ($anc as $value) {
					if(!empty($aitLocationMeta[$value]['icon'])){
						$icon = $aitLocationMeta[$value]['icon'];
						break;
					}
				}
				if(empty($icon)){
					$icon = (isset($GLOBALS['aitThemeOptions']->directory->defaultLocationIcon)) ? $GLOBALS['aitThemeOptions']->directory->defaultLocationIcon : '';
				}
			}
			return $icon;
		case 'excerpt':
			$excerpt = '';
			if(isset($aitLocationMeta[$categoryID]['excerpt'])){
				$excerpt = $aitLocationMeta[$categoryID]['excerpt'];
			}
			return $excerpt;
		default:
			break;
	}
}

/**
 * Get package class for item
 * @param  int $id author id
 * @return string     class
 */
function getItemPackageClass( $authorId ) {
	$user = new WP_User( $authorId );
	if(isset($user->roles[0])){
		return $user->roles[0];
	} else {
		return null;
	}
}

// Get manually excerpt or automatic excerpt for wp post
function aitGetPostExcerpt($excerpt, $content) {
	$newExcerpt = '';
	$trimExcerpt = trim($excerpt);
	if(empty($trimExcerpt)){
		$exc = substr(strip_shortcodes($content), 0, 300);
		$pos = strrpos($exc, " ");
		$newExcerpt = substr($exc, 0, ($pos ? $pos : -1)) . "...";
	} else {
		$newExcerpt = $excerpt;
	}
	return $newExcerpt;
}

// Change author custom post type ait-dir-item fix
add_filter('wp_dropdown_users', 'aitChangeAuthorForItems');
function aitChangeAuthorForItems($output) {
	global $post;
	// Doing it only for the custom post type
	if(!empty($post) && $post->post_type == 'ait-dir-item') {
		$users = array();
		$users[0] = get_users(array('role'=>'administrator'));
		$users[1] = get_users(array('role'=>'directory_1'));
		$users[2] = get_users(array('role'=>'directory_2'));
		$users[3] = get_users(array('role'=>'directory_3'));
		$users[4] = get_users(array('role'=>'directory_4'));
		$users[5] = get_users(array('role'=>'directory_5'));

		$output = "<select id='post_author_override' name='post_author_override' class=''>";
		foreach($users as $userGroup) {
			foreach ($userGroup as $user) {
				$selected = ($user->ID == intval($post->post_author)) ? " selected='selected'" : "";
				$output .= "<option".$selected." value='".$user->ID."'>".$user->user_login."</option>";
			}
		}
		$output .= "</select>";
	}
	return $output;
}

/*
 * Contact owner functionality
 */
add_action('wp_ajax_nopriv_ait_contact_owner', 'aitDirContactOwner');
add_action('wp_ajax_ait_contact_owner', 'aitDirContactOwner');
function aitDirContactOwner() {

	// Check for nonce security
	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
		_e( 'Bad nonce', 'ait' );
		exit();
	}

	if ((!empty($_POST['name'])) && (!empty($_POST['from'])) && (!empty($_POST['to'])) && (!empty($_POST['subject'])) && (!empty($_POST['message']))) {
		$headers = 'From: "' . $_POST['name'] . '" <' . $_POST['from'] . '>' . "\r\n";
		$result = wp_mail( strip_tags($_POST['to']), strip_tags($_POST['subject']), strip_tags($_POST['message']), $headers );
		if (!$result) {
			_e( 'Error with sending email', 'ait' );
			exit();
		}
	} else {
		_e( 'Please fill out name, email, subject and message', 'ait' );
		exit();
	}

	echo "success";
	exit();

}

function aitGetSpecialOffers($count = 5) {

	$offers = array();
	$args = array(
		'post_type' => 'ait-dir-item',
		'post_status' => 'publish',
		'nopaging' => true,
		'orderby' => 'rand'
	);
	$query = new WP_Query($args);
	$all = $query->posts;

	$i = 0;
	$j = 0;
	do {
		$options = get_post_meta($all[$i]->ID,'_ait-dir-item',true);
		if (isset($options['specialActive'])) {
			$offers[$i] = $all[$i];
			$offers[$i]->link = get_permalink($all[$i]->ID);
			$offers[$i]->options = $options;
			$j++;
		}
		$i++;
	} while (($i < count($all)) && ($j < $count));

	return $offers;

}

function aitGetBestPlaces($count = 3) {

	$items = array();
	$args = array(
		'post_type' => 'ait-dir-item',
		'post_status' => 'publish',
		'posts_per_page' => $count,
		'meta_key' => 'rating_full',
		'orderby' => 'meta_value_num',
		'order' => 'DESC'
	);
	$query = new WP_Query($args);
	$items = $query->posts;

	return getDirItemsDetails($items);

}

function aitGetRecentPlaces($count = 3) {

	$items = array();
	$args = array(
		'post_type' => 'ait-dir-item',
		'post_status' => 'publish',
		'posts_per_page' => $count,
		'orderby' => 'date',
		'order' => 'DESC'
	);
	$query = new WP_Query($args);
	$items = $query->posts;

	return getDirItemsDetails($items);

}

function aitGetPeopleRatings($count = 5) {

	$items = array();
	$args = array(
		'post_type' => 'ait-rating',
		'post_status' => 'publish',
		'posts_per_page' => $count,
		'orderby' => 'rand'
	);
	$query = new WP_Query($args);
	$items = $query->posts;

	$max = (isset($GLOBALS['aitThemeOptions']->rating->starsCount)) ? intval($GLOBALS['aitThemeOptions']->rating->starsCount) : 5;

	foreach ($items as $item) {
		$item->rating = array();
		$item->rating['val'] = get_post_meta($item->ID,'rating_mean_rounded',true);
		$item->rating['max'] = $max;

		$itemId = get_post_meta($item->ID,'post_id',true);
		$item->for = get_post($itemId);
	}

	return $items;

}

// Is User shortcode
function aitDirShortcodeIsUser( $params, $content = null) {
	extract( shortcode_atts( array(
		'role' => ''
	), $params ) );

	$current_user = wp_get_current_user();
	if (!empty($role)) {
		if (in_array($role, $current_user->roles)) {
			echo $content;
		}
	} else {
		if ( 0 != $current_user->ID ) {
			echo $content;
		}
	}

}
add_shortcode( 'is-user', 'aitDirShortcodeIsUser' );

// Is Guest shortcode
function aitDirShortcodeIsGuest( $params, $content = null) {
	extract( shortcode_atts( array(
	), $params ) );

	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
	    echo $content;
	}
}
add_shortcode( 'is-guest', 'aitDirShortcodeIsGuest' );

function getAitThemeName() {
	return 'directory';
}