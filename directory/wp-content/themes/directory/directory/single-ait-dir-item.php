<?php

$latteParams['post'] = WpLatte::createPostEntity(
	$GLOBALS['wp_query']->post,
	array(
		'meta' => $GLOBALS['pageOptions'],
	)
);

$latteParams['options'] = get_post_meta($latteParams['post']->id, '_ait-dir-item', true);
// check url link
if (isset($latteParams['options']['web']) && !empty($latteParams['options']['web']) && (strpos($latteParams['options']['web'],'http://') === false && strpos($latteParams['options']['web'],'https://') === false)){
    $latteParams['options']['web'] = 'http://'.$latteParams['options']['web'];
}

$thumbnailDir = wp_get_attachment_image_src( get_post_thumbnail_id($latteParams['post']->id), 'full' );
if($thumbnailDir !== false){
	$latteParams['thumbnailDir'] = getRealThumbnailUrl($thumbnailDir[0]);
} else {
	$latteParams['thumbnailDir'] = getRealThumbnailUrl($aitThemeOptions->directory->defaultItemImage);
}
// get term for this items
$terms = wp_get_post_terms($latteParams['post']->id, 'ait-dir-item-category');

// get items from current category
$latteParams['term'] = array();
$latteParams['ancestors'] = array();
$latteParams['items'] = array();

// pending preview
if($GLOBALS['wp_query']->post->post_status != 'publish'){

	$item = $GLOBALS['wp_query']->post;
	// options
	$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
	// link
	$item->link = get_permalink($item->ID);
	// thumbnail
	$image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID) );
	if($image !== false){
		$item->thumbnailDir = getRealThumbnailUrl($image[0]);
	} else {
		$item->thumbnailDir = getRealThumbnailUrl($GLOBALS['aitThemeOptions']->directory->defaultItemImage);
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
	$item->packageClass = getItemPackageClass($item->post_author);

	$latteParams['term'] = null;
	$latteParams['items'] = array($item);
	$latteParams['ancestors'] = array();

} else {
	if(isset($terms[0])){

		// term
		$terms[0]->link = get_term_link(intval($terms[0]->term_id), 'ait-dir-item-category');
		$terms[0]->icon = getRealThumbnailUrl(getCategoryMeta("icon", intval($terms[0]->term_id)));
		$terms[0]->marker = getCategoryMeta("marker", intval($terms[0]->term_id));

		$termAncestors = array_reverse(get_ancestors(intval($terms[0]->term_id), 'ait-dir-item-category'));
		$ancestors = array();
		foreach ($termAncestors as $anc) {
			$term = get_term($anc, 'ait-dir-item-category');
			$term->link = get_term_link(intval($term->term_id), 'ait-dir-item-category');
			$ancestors[] = $term;
		}

		$categoryID = intval($terms[0]->term_id);
		$location = 0;
		$search = '';
		$radiusKm = ($aitThemeOptions->directory->showDistanceInDetail) ? $aitThemeOptions->directory->showDistanceInDetail : 1000 ;
		// center and radius
		$radius = array($radiusKm,$latteParams['options']['gpsLatitude'],$latteParams['options']['gpsLongitude']);

		$items = getItems($categoryID,$location,$search,$radius);

		$latteParams['term'] = $terms[0];
		$latteParams['items'] = $items;
		$latteParams['ancestors'] = $ancestors;

	} else {
		// no category selected

		// all items
		$items = getItems();
		$thisItem;
		for($i = 0; $i < count($items); $i++) {
			if($items[$i]->ID == $latteParams['post']->id) {
				$thisItem = $items[$i];
			}
		}
		unset($items);

		$latteParams['term'] = null;
		$latteParams['items'] = array($thisItem);
		$latteParams['ancestors'] = array();
	}
}

$latteParams['isDirSingle'] = true;

$latteParams['sidebarType'] = 'item';

$latteParams['rating'] = get_post_meta( $latteParams['post']->id, 'rating', true );

// claim listing
$user = new WP_User(intval($GLOBALS['wp_query']->post->post_author));
$latteParams['hasAlreadyOwner'] = isDirectoryUser($user);

/**
 * Fire!
 */
WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();