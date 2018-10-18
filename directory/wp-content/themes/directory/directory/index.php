<?php

/**
 * AIT WordPress Theme
 *
 * Copyright (c) 2012, Affinity Information Technology, s.r.o. (http://ait-themes.club)
 */

// directory search
$latteParams['type'] = (isset($_GET['dir-search']) && isset($_GET['s'])) ? true : false;
if($latteParams['type']){
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

	$latteParams['posts'] = $posts;

} else {
	
	$latteParams['posts'] = WpLatte::createPostEntity($GLOBALS['wp_query']->posts);

	// if this is "Blog" page get the right template
	if($GLOBALS['wp_query']->is_home && $GLOBALS['wp_query']->is_posts_page){
		$template = get_page_template();
		if($template = apply_filters('template_include', $template)){
			if(substr($template, -8, 8) != 'page.php'){
				require_once $template;
				return; // ends executing this script
			}
		}
	}

	// no page was selected for "Posts page" from WP Admin in Settings->Reading
	$latteParams['isIndexPage'] = true;

	if(isset($GLOBALS['wp_query']->queried_object)){

		$latteParams['post'] = WpLatte::createPostEntity(
			$GLOBALS['wp_query']->queried_object,
			array(
				'meta' => $GLOBALS['pageOptions'],
		));

		$latteParams['isIndexPage'] = false;
	}

}

WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();