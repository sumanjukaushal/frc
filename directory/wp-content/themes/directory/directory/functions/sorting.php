<?php

// Sorting
add_filter('posts_join', 'directorySortingJoin',10,2);
function directorySortingJoin($join, $query) {
	global $wpdb, $aitThemeOptions;
	if ($query->is_main_query() && !$query->is_admin && ((isset($_GET['dir-search'])) || (isset($query->query_vars["ait-dir-item-category"])) || (isset($query->query_vars["ait-dir-item-location"])))) {
		$sql = "";
		// default ordering
		$orderby = (isset($aitThemeOptions->directory->defaultOrderby)) ? $aitThemeOptions->directory->defaultOrderby : 'post_date';
		// get from get parameters
		if (!empty($_GET['orderby'])) {
			$orderby = $_GET['orderby'];
		}
		if ($orderby == 'rating') {
			$sql .= " LEFT JOIN {$wpdb->postmeta} rating ON ({$wpdb->posts}.ID = rating.post_id AND rating.meta_key IN ('rating_full'))";
		}
		if ($orderby == 'packages') {
			directorySaveUserPackagesToDb();
			$sql .= " LEFT JOIN {$wpdb->usermeta} packages ON ({$wpdb->posts}.post_author = packages.user_id AND packages.meta_key IN ('dir_package'))";
		}
		if (isset($aitThemeOptions->directory->showFeaturedItemsFirst)) {
			$sql .= " LEFT JOIN {$wpdb->postmeta} featured ON ({$wpdb->posts}.ID = featured.post_id AND featured.meta_key IN ('dir_featured'))";
		}
		$join .= $sql;
	}
	return $join;
}
add_filter('posts_orderby', 'directorySortingOrderby',10,2);
function directorySortingOrderby($orderby, $query) {
	global $wpdb, $aitThemeOptions;

	if ( is_front_page() && is_home() ) {
	  // Default homepage
	} elseif ( is_front_page() ) {
	  // static homepage
	} elseif ( is_home() ) {
	  // blog page
		return $orderby;
	} elseif(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'post') {
		return $orderby;
	} else {
	  //everything else
	}


	if ($query->is_main_query() && !$query->is_admin && ((isset($_GET['dir-search'])) || (isset($query->query_vars["ait-dir-item-category"])) || (isset($query->query_vars["ait-dir-item-location"])))) {
		$sql = "";
		// default ordering
		$orderby = (isset($aitThemeOptions->directory->defaultOrderby)) ? $aitThemeOptions->directory->defaultOrderby : 'post_date';
		$order = (isset($aitThemeOptions->directory->defaultOrder)) ? $aitThemeOptions->directory->defaultOrder : 'DESC';
		// get from get parameters
		if (!empty($_GET['orderby'])) {
			$orderby = $_GET['orderby'];
		}
		if (!empty($_GET['order'])) {
			$order = $_GET['order'];
		}
		if ($orderby == 'rating') {
			if (isset($aitThemeOptions->directory->showFeaturedItemsFirst)) {
				$sql = "featured.meta_value DESC, convert(rating.meta_value, decimal) {$order}";
			} else {
				$sql = "convert(rating.meta_value, decimal) {$order}";
			}
		} else if ($orderby == 'packages') {
			if (isset($aitThemeOptions->directory->showFeaturedItemsFirst)) {
				$sql = "featured.meta_value DESC, packages.meta_value {$order}";
			} else {
				$sql = "packages.meta_value {$order}";
			}
		} else {
			if (isset($aitThemeOptions->directory->showFeaturedItemsFirst)) {
				$sql = "featured.meta_value DESC, {$wpdb->posts}.{$orderby} {$order}";
			} else {
				$sql = "{$wpdb->posts}.{$orderby} {$order}";
			}
		}
		$orderby = $sql;
	}
	return $orderby;
}
// Save directory packages for sorting
function directorySaveUserPackagesToDb() {
	$users = get_users();
	// capabilities list
	$roles = array(
		'administrator' => 10,
		'directory_5' => 9,
		'directory_4' => 8,
		'directory_3' => 7,
		'directory_2' => 6,
		'directory_1' => 5,
		'editor' => 4,
		'author' => 3,
		'contributor' => 2,
		'subscriber' => 1
	);
	foreach ($users as $user) {
		if (isset($user->roles[0])) {
			if (array_key_exists($user->roles[0], $roles)) {
				update_user_meta($user->ID, 'dir_package', $roles[$user->roles[0]]);
			} else {
				update_user_meta($user->ID, 'dir_package', 0);
			}
		}
	}
}