<?php

/**
 * AIT WordPress Theme
 *
 * Copyright (c) 2012, Affinity Information Technology, s.r.o. (http://ait-themes.club)
 */
$latteParams['type'] = (isset($_GET['dir-search']) && $_GET['dir-search'] == "yes") ? true : false;
if($latteParams['type']){
	$latteParams['isDirSearch'] = true;
	// show all items on map
	if(isset($aitThemeOptions->search->searchShowMap)){
		$radius = array();
		if(isset($_GET['geo'])){
			$radius[] = $_GET['geo-radius'];
			$radius[] = $_GET['geo-lat'];
			$radius[] = $_GET['geo-lng'];
		}
		$latteParams['items'] = getItems(intval($_GET['categories']),intval($_GET['locations']),$GLOBALS['wp_query']->query_vars['s'],$radius);
	}

	$posts = $wp_query->posts;
	foreach ($posts as $item) {
		$item->link = get_permalink($item->ID);
		$image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID) );
		if($image !== false){
			$item->thumbnailDir = getRealThumbnailUrl($image[0]);
		} else {
			$item->thumbnailDir = getRealThumbnailUrl($GLOBALS['aitThemeOptions']->directory->defaultItemImage);
		}
		$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
		$item->excerptDir = aitGetPostExcerpt($item->post_excerpt,$item->post_content);
		$item->packageClass = getItemPackageClass($item->post_author);

		$item->rating = get_post_meta( $item->ID, 'rating', true );
	}

} else {
	$posts = WpLatte::createPostEntity($wp_query->posts);
}

//hottfix
// this should be same otherwise map items and searchresult don't match
$latteParams['posts'] = $posts;

WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();