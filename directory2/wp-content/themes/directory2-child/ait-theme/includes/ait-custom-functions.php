<?php
// allows programmers to include part in the same way as in latte syntax
function aitRenderLatteTemplate($template, $params = array())
{
    AitWpLatte::init();
    ob_start();
    WpLatte::render(aitPath('theme', $template), $params);
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
}

function aitRenderItemMarker($data)
{
	$dataSer = "";//serialized($data['item']);
	$item = $data['item'];
	$meta = $data['meta'];
	$enableTel = $data['enableTel'];
	$options = $data['options'];

	$address = $meta->map;
	$address = $address['address'];

	$imageUrl = $item->hasImage ? $item->imageUrl : $options['item']['noFeatured'];
	$imageUrl = aitResizeImage($imageUrl, array('width' => 145, 'height' => 180, 'crop' => 1));
	// var_dump($imageUrl);
	// exit;
	// $imageUrl = get_the_post_thumbnail($item->id);
    //>>>>>>>>> Start of custom code >>>>>>>>>>>>>>>
    global $wpdb;
	$contentURL = content_url();
	$imgSrcArr = $imgArr = array();
	$tableStr = "";
	$arr_query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'feature_n_icons'";
	$arr_result = $wpdb->get_results($arr_query);
	$imgSrcArr = array();
    if(count($arr_result) >= 1){
        $imgArr = unserialize($arr_result[0]->option_value);
        if(is_array($imgArr))
        foreach($imgArr as $key => $img){
            $imgSrcArr[$key] = array($img[1],$img[2]); //image title, and image tooltip
        }
    }
	$postID = $data['post_id'];
	$qry = "SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` ='_ait-item_item-extension' AND `post_id` = '$postID'";
	//SELECT * FROM `wp_postmeta` WHERE `meta_key` ='_ait-item_item-extension' AND `post_id` = '3590'
	$result = $wpdb->get_results($qry);
	$unsear = unserialize($result[0]->meta_value);
	$imgcount = 0;
	foreach($unsear['onoff'] as $imgKey => $imgSwitch){
		if($imgSwitch == 'on'){
			$imgcount++;
			if(array_key_exists($imgKey, $imgSrcArr)){
				list($imgName, $imageTitle) = $imgSrcArr[$imgKey];
				$absImgURL =  $contentURL."/feature_icon/icons/30X30/{$imgName}";
				if(!empty($imgName))
				$imgColArr[] = "<td style='padding-top:1px; padding-right:1px;'><img src='$absImgURL' title=\"$imageTitle\" alt=\"$imageTitle\" height='30px' width='30px'></td>";
			}
		}
	}
	$style = "";
	if(count($imgColArr) >=1){
		$imgChuncks = array_chunk($imgColArr, 4);
		$tableStr = "<table cellspacing='2' cellpadding='2' width='100%'>";
		foreach($imgChuncks as $imgChunck){
			$columnStr = implode("",$imgChunck);
			if(!empty($columnStr))$tableStr.= "<tr>$columnStr</tr>";
		}
		$tableStr.="</table><style>.infoBox .item-picture {padding:4px 4px 32px 0;}</style>";
		$style = "padding:4px 4px 32px 0; width:100%";
	}
	if(empty($tableStr)){
		$tableStr = "<img src='{$imageUrl}' alt='image'><style>.infoBox .item-picture {padding:4px 4px 4px 0;}</style>";
		$style = "padding:4px 4px 4px 0;";
	}
    //<<<<<<<<< End of Custom code <<<<<<<<<<<<<<<<<

	$showMore = __('Show More', 'ait');
	$template = "";
	/*
	$template = "<div class='item-data'>
		<h3>{$item->title}</h3>
		<span class='item-address'>{$address}</span>
		<a href='{$item->permalink}'>
			<span class='item-button'>{$showMore}</span>
		</a>
	</div>
	<div class='item-picture'>
		{$tableStr}";
		if ($enableTel && $meta->telephone) {
			$template .= "<a href='tel:{$meta->telephone}' class='phone'>{$meta->telephone}</a>";
		}
	$template .= "</div>";
	*/
	if ($enableTel && $meta->telephone) {
		$telStr = "<a href='tel:{$meta->telephone}' class='phone'>{$meta->telephone}</a>";
	}
	//suburb, state, post code - below address
	if( !empty($meta->wlm_city) || !empty($meta->wlm_state) || !empty($meta->wlm_zip)){
		$address2 = array();
		if(!empty($meta->wlm_city))$address2[] = $meta->wlm_city;
		if(!empty($meta->wlm_state))$address2[] = $meta->wlm_state;
		if(!empty($meta->wlm_zip))$address2[] = $meta->wlm_zip;
		$address2Str = "".implode(", ",$address2)."";
	}
	$template = "<div width='100%'>
					
							<div class='item-data'>
								<h5>{$item->title}</h5>
								<span class='item-address' style='padding-bottom:0px; height:auto;'>{$address}</span>
								<span class='item-address' style='padding-top:0px;'>{$address2Str}</span>
								<a href='{$item->permalink}' target='_blank'><span class='item-button'>{$showMore}</span></a>
							</div>
						
							<div class='item-picture' style='{$style}'>{$tableStr}{$telStr}</div>
						
				</div>";
				
	$template = "
				<div class='item-data'>
					<h5>{$item->title}</h5>
					<span class='item-address' style='padding-bottom:0px; height:auto;'>{$address}</span>
					<span class='item-address' style='padding-top:0px;'>{$address2Str}</span>
					<a href='{$item->permalink}' target='_blank'><span class='item-button'>{$showMore}</span></a>
				</div>
				<div class='item-picture' >{$tableStr}{$telStr}</div>
				";
	return $template;//style='{$style}'
}

function aitListCategoriesWithIcons($terms, $taxonomy, $defaultIcon, $field_name = "map_icon"){
	$result = array();
	if(isset($terms['errors'])) return $result; // when ait-toolkit is not active and there is no ait-items taxonomy, get_categories() will return error array

	foreach($terms as $term){
		$catOption = get_option('ait-items_category_'.$term->term_id);
		$catIcon = $catOption[$field_name] != "" ? $catOption[$field_name] : $defaultIcon;

		$result[$term->term_id] = $catIcon;
		$children = get_categories(array('taxonomy' => $taxonomy, 'hide_empty' => 0, 'parent' => $term->term_id));
		if(!empty($children)){
			$result = $result + aitListCategoriesWithIcons($children, $taxonomy, $catIcon, $field_name);
		}
	}
	return $result;
}



function aitGetItemsMarkers($query, $options = array())
{
	$markers = array();
	// find element settings
	$enableTel = isset($options['enableTel']) ? $options['enableTel'] : false;

	$themeoptions = aitOptions()->getOptionsByType('theme');

	$terms = get_terms( array(
		'parent' => 0,
		'taxonomy' => 'ait-items',
		'hide_empty' => false,
	) );

	$defaultIcon = $themeoptions['items']['categoryDefaultPin'];

	$termsWithIcons = aitListCategoriesWithIcons($terms, 'ait-items', $defaultIcon);

	foreach (new WpLatteLoopIterator($query) as $item) {
		$meta = $item->meta('item-data');
		// meta might me empty or corrupted - ignore such items
		if (empty($meta) or empty($meta->map)) continue;

		// skip items with [1,1] coordinates
		if ($meta->map['latitude'] == 1 and $meta->map['longitude'] == 1) {
			continue;
		}
		$context = "";
		$context = aitRenderItemMarker(array('item'=>$item, 'meta'=>$meta, 'enableTel' => $enableTel, 'options' => $themeoptions, 'post_id' => $item->id));
		$catData = aitItemCategoriesData($item->id, $defaultIcon, $termsWithIcons);
		$marker = (object)array(
			'lat'        => $meta->map['latitude'],
			'lng'        => $meta->map['longitude'],
			'title'      => $item->rawTitle,
			'icon'       => $catData['icon'],
			'context'    => $context,
			'type'       => 'item',
			'data'       => array(),
		);
		array_push($markers, $marker);
	}

	return $markers;
}



function aitGetEventsMarkers($query)
{
    $markers = array();
    foreach (new WpLatteLoopIterator($query) as $item) {
        $address = aitEventAddress($item, true);

        $marker = (object)array(
            'lat'     => $address['latitude'],
            'lng'     => $address['longitude'],
            // 'title'   => $item->title,
            'icon'    => '',
            'context' => '',
            'type'    => 'event',
            'enableInfoWindow'    => false,
        );
        array_push($markers, $marker);
    }
    return $markers;
}


function aitItemCategoriesData($itemID, $icon, $termsWithIcons)
{
	$itemCats = get_the_terms($itemID, 'ait-items');

    if (!$itemCats) {
        return array(
	        'icon' => $icon,
		);
    }
	$cat = reset($itemCats);

	return array(
		'icon' => $termsWithIcons[$cat->term_id]
	);
}


function aitGetMapOptions($options)
{
    $result = array();
    $result['styles'] = aitGetMapStyles($options);

    // if (!isset($options['autoZoomAndFit']) || !$options['autoZoomAndFit']) {
    $result['center'] = array(
        'lat' => floatval($options['address']['latitude']),
        'lng' => floatval($options['address']['longitude']),
    );
    // }

    if (!empty($options['mousewheelZoom'])) {
        $result['scrollwheel'] = true;
    }

    if (isset($options['zoom'])) {
        $result['zoom'] = intval($options['zoom']);
    }

    return $result;
}

function aitGetMapStyles($options)
{
    $o = $options;
    $styles = array(
    	array(
    		'stylers' => array(
                array('hue'        => $o['mapHue']),
                array('saturation' => $o['mapSaturation']),
                array('lightness'  => $o['mapBrightness']),
    		),
    	),
    	array(
    		'featureType' => 'landscape',
    		'stylers' => array(
                array('visibility' => $o['landscapeShow'] == false ? 'off' : 'on'),
                array('hue'        => $o['landscapeColor']),
                array('saturation' => $o['landscapeColor'] != '' ? $o['objSaturation'] : ''),
                array('lightness'  => $o['landscapeColor'] != '' ? $o['objBrightness'] : ''),
    		),
    	),
        array(
            'featureType' => 'administrative',
            'stylers' => array(
                array('visibility' => $o['administrativeShow'] == false ? 'off' : 'on'),
                array('hue'        => $o['administrativeColor']),
                array('saturation' => $o['administrativeColor'] != '' ? $o['objSaturation'] : ''),
                array('lightness'  => $o['administrativeColor'] != '' ? $o['objBrightness'] : ''),
            ),
        ),
        array(
            'featureType' => 'road',
            'stylers' => array(
                array('visibility' => $o['roadsShow'] == false ? 'off' : 'on'),
                array('hue'        => $o['roadsColor']),
                array('saturation' => $o['roadsColor'] != '' ? $o['objSaturation'] : ''),
                array('lightness'  => $o['roadsColor'] != '' ? $o['objBrightness'] : ''),
            ),
        ),
        array(
            'featureType' => 'water',
            'stylers' => array(
                array('visibility' => $o['waterShow'] == false ? 'off' : 'on'),
                array('hue'        => $o['waterColor']),
                array('saturation' => $o['waterColor'] != '' ? $o['objSaturation'] : ''),
                array('lightness'  => $o['waterColor'] != '' ? $o['objBrightness'] : ''),
            ),
        ),
        array(
            'featureType' => 'poi',
            'stylers' => array(
                array('visibility' => $o['poiShow'] == false ? 'off' : 'on'),
                array('hue'        => $o['poiColor']),
                array('saturation' => $o['poiColor'] != '' ? $o['objSaturation'] : ''),
                array('lightness'  => $o['poiColor'] != '' ? $o['objBrightness'] : ''),
            ),
        ),
    );
    return $styles;
}



function aitMapTranslations()
{
    return array(
        'error_geolocation_failed' => __("This page has been blocked from tracking your location", "ait"),
        'error_geolocation_unsupported' => __("Your browser doesn't support geolocation", "ait"),
    );
}


/**
* this function can be called in ajax requests
* do not use any variables out of scope which might be undefined
*/
function aitBuildItemTaxQuery($queryVars, $ignorePagination = false)
{
	// if ignore pagination is true that means the request is made from header map element
	// and all items are required
	if($ignorePagination) {
		$queryVars['posts_per_page'] = -1;
		$queryVars['nopaging'] = true;
	} else {
		// global variable which contains all information necessary to build/alter global query
		// this query must be initialised in ait_alter_search_query callback or in ajax requests
		global $__ait_query_data;
		$searchFilters = $__ait_query_data['search-filters'];
		$advancedFilters = $__ait_query_data['advanced-filters'];

		// retrieve metaQuery which was modified in previous steps
		// or if this function is called from ajax
		$metaQuery = isset($queryVars['meta_query']) ? $queryVars['meta_query'] : array();

		// posts_per_page comes from ait-search-filters or default theme options
		$queryVars['posts_per_page'] = $searchFilters['selectedCount'];

		// push featured items at top position
		$metaQuery['featured_clause'] = array(
			'key'   => '_ait-item_item-featured',
			'compare' => 'EXISTS'
		);

		// apply ordering by reviews
		if (defined('AIT_REVIEWS_ENABLED') and $searchFilters['selectedOrderBy'] == 'rating') {
			$metaQuery['rating_clause'] = array(
				'key' => 'rating_mean',
				'compare' => 'EXISTS'
			);
			$searchFilters['selectedOrderBy'] = 'rating_clause';
		}

		// apply filters from Ait Advanced Filters plugin
		$metaQuery = aitFilterByAdvancedFilters( $metaQuery, $advancedFilters );

		$queryVars['meta_query'] = $metaQuery;

		$queryVars['orderby'] = array(
			'featured_clause' => 'DESC',
			$searchFilters['selectedOrderBy'] => $searchFilters['selectedOrder']
		);
	}
	return $queryVars;
}



/**
* this function can be called in ajax requests
* do not use any variables out of scope which might be undefined
* queryVars usually comes from global wp_query->query_vars
*/
function aitBuildSearchQuery($queryVars, $ignorePagination = false)
{
	// global variable which contains all information necessary to build/alter global query
	// this query must be initialised in ait_alter_search_query callback or in ajax requests
	global $__ait_query_data;
	$searchFilters = $__ait_query_data['search-filters'];
	$advancedFilters = $__ait_query_data['advanced-filters'];
	$searchParams = $__ait_query_data['search-params'];

	$metaQuery = isset($queryVars['meta_query']) ? $queryVars['meta_query'] : array();
	$taxQuery = isset($queryVars['tax_query']) ? $queryVars['tax_query'] : array();

	// if ignore pagination is true that means the request is made from header map element
	// and all items are required
	if($ignorePagination){
		// if limit is set to -1 nopaging must be true otherwise false!
		$queryVars['posts_per_page'] = (int)$__ait_query_data['ajax']['limit'];
		$queryVars['offset'] = (int)$__ait_query_data['ajax']['offset'];
		$queryVars['nopaging'] = false;
		$queryVars['no_found_rows'] = false;
	} else {
		$queryVars['posts_per_page'] = $searchFilters['selectedCount'];
	}

	// change post_type from default global query
	$queryVars['post_type'] = 'ait-item';
	$queryVars['suppress_filters'] = false;

	// apply ordering by reviews
    if (defined('AIT_REVIEWS_ENABLED') and $searchFilters['selectedOrderBy'] == 'rating') {
        $metaQuery['rating_clause'] = array(
            'key' => 'rating_mean',
            'compare' => 'EXISTS'
        );
        $searchFilters['selectedOrderBy'] = 'rating_clause';
    }

	// apply advanced filters
    $metaQuery = aitFilterByAdvancedFilters( $metaQuery, $advancedFilters );

	// push featured items at top position
	$metaQuery['featured_clause'] = array(
		'key'   => '_ait-item_item-featured',
		'compare' => 'EXISTS'
	);
	$queryVars['meta_query'] = $metaQuery;

	// add taxonomies parameters from search url to the query
	if(!empty($searchParams['category'])){
		array_push($taxQuery, array(
			'taxonomy' => 'ait-items',
			'field' => 'term_id',
			'terms' => $searchParams['category'])
		);
	}
	if(!empty($searchParams['location'])){
		array_push($taxQuery, array(
			'taxonomy' => 'ait-locations',
			'field' => 'term_id',
			'terms' => $searchParams['location'])
		);
	}
	$queryVars['tax_query'] = $taxQuery;

	// exclude item's excerpt from search by keyword
	// include item's meta in search by keyword
	if (!empty($searchParams['s'])) {
		add_filter('posts_where', 'aitExcludeExcerptFromSearch');
		add_filter( 'posts_where', 'aitIncludeMetaInSearch' );
	}

	// apply radius filter
	if (!empty($searchParams['lat']) && !empty($searchParams['lon']) and !empty($searchParams['rad'])) {
		$radiusUnits = !empty($searchParams['runits']) ? $searchParams['runits'] : 'km';
		$radiusValue = !empty($searchParams['rad']) ? $searchParams['rad'] : 100;
		$radiusValue = $radiusUnits == 'mi' ? $radiusValue * 1.609344 : $radiusValue;

		$filteredByRadiusList = aitGetItemsByRadius($searchParams['lat'], $searchParams['lon'], $radiusValue);
		if (empty($filteredByRadiusList)) {
			$filteredByRadiusList = array(0);
		}
		$queryVars['post__in'] = $filteredByRadiusList;
	}

	$queryVars['orderby'] = array(
		'featured_clause' => 'DESC',
		$searchFilters['selectedOrderBy'] => $searchFilters['selectedOrder']
	);
	//$queryVars['post__not_in'] = array(12623,16863,31040); //rasu
	return $queryVars;
}


add_filter( 'ait_alter_ajax_query', function($query){
	if ($_REQUEST['action'] == 'get-items:getHeaderMapMarkers') {
		// items queried from frontend search actions - do not modify
	} elseif ($_REQUEST['action'] == 'query-attachments') {
		// Display posts in admin for current user only
		$wp_user = wp_get_current_user();
		if(isCityguideUser($wp_user->roles)){
			$query->set('author', $wp_user->data->ID);
		}
	} else {
		// regular admin or frontend ajax query - do not modify
		// $queryString = $query->query;
		// $query->set('meta_query', $queryString['meta_query']);
	}
	return $query;
});

add_filter( 'ait_alter_search_query', function($query){
	/* SETTINGS FOR POST TYPE RELATED PAGES: */
	if($query->is_tax('ait-events-pro') || is_post_type_archive('ait-event-pro')) {
		$settings = (object)get_option('ait_events_pro_options', array());
	} else {
		$settings = aitOptions()->getOptionsByType('theme');
		$settings = (object)$settings['items'];
	}

	$filterCountsSelected = isset($_REQUEST['count']) && $_REQUEST['count'] != "" ? $_REQUEST['count'] : $settings->sortingDefaultCount;
	$filterOrderBySelected = isset($_REQUEST['orderby']) && $_REQUEST['orderby'] != "" ? $_REQUEST['orderby'] : $settings->sortingDefaultOrderBy;
	$filterOrderSelected = isset($_REQUEST['order']) && $_REQUEST['order'] != "" ? $_REQUEST['order'] : $settings->sortingDefaultOrder;

	// $metaQuery['relation'] = 'AND';
	// save all query and search information as global variable so we can use it in other functions
	// or ajax queries
	$GLOBALS['__ait_query_data'] = array();
	$GLOBALS['__ait_query_data']['search-filters'] = array(
		'selectedCount' => $filterCountsSelected,
		'selectedOrderBy' => $filterOrderBySelected,
		'selectedOrder' => $filterOrderSelected,
	);
	$GLOBALS['__ait_query_data']['advanced-filters'] = empty($_REQUEST['filters']) ? "" : $_REQUEST['filters'];

	$query->set('lang', AitLangs::getCurrentLanguageCode());

	/* IS SEARCH PAGE FOR ITEMS */
	if(isset($_REQUEST['a']) && $_REQUEST['a'] == true) {
		$GLOBALS['__ait_query_data']['search-params'] = wp_parse_args($_GET);
		$args = aitBuildSearchQuery($query->query_vars);
		foreach($args as $key => $queryVar){
			$query->set($key, $queryVar);
		}

	/* IS ITEM TAXONOMY AND ARCHIVE PAGE */
	} elseif ($query->is_tax('ait-items') || $query->is_tax('ait-locations') || is_post_type_archive('ait-item')) {
		$args = aitBuildItemTaxQuery($query->query_vars);
		foreach($args as $key => $queryVar){
			$query->set($key, $queryVar);
		}

	/* IS EVENTS PRO TAXONOMY AND ARCHIVE PAGE */
	// TODO: I didn't make any ajax queries on following pages so I didn't refactor this section yet
	} elseif ($query->is_tax('ait-events-pro') || is_post_type_archive('ait-event-pro')) {

		if ($query->is_tax('ait-events-pro')) {
			$postIn = AitEventsPro::getEventsFromDate(date('Y-m-d H:i:s'));
		} else {
			// on archive page display also events which already ended
			$postIn = AitEventsPro::getEvents();
		}
		$query->set('post__in', $postIn);
		$query->set('posts_per_page', $filterCountsSelected);
		//quick fix - this theme doesn't have eventDate option in frontend filter on search page
		if (in_array($filterOrderBySelected, array('date', 'eventDate'))) {
			$query->set('orderby', 'post__in');
		} else {
			$query->set('orderby', array(
				$filterOrderBySelected => $filterOrderSelected
			));
		}
		if ($filterOrderSelected == 'DESC') {
			$query->set('post__in', array_reverse($postIn));
		}
	}
	$postsWithFeatureFilter = frc_feature_filter();
	if(is_array($postsWithFeatureFilter)){
		$inArr = $postsWithFeatureFilter['post__in'];
		$notInArr = $postsWithFeatureFilter['post__not_in'];
		if(is_array($inArr) && count($inArr) >= 1){
			$query->query_vars['post__in'] = $inArr;
		}elseif(is_array($notInArr) && count($notInArr) >= 1){
			$query->query_vars['post__not_in'] = $notInArr;
			$query->query_vars['post__in'] = array(1);
		}
	}
	//echo "<pre>";print_r($query);echo "</pre>"; //WP_Query Object
	
	return $query;
} );

function frc_feature_filter(){
	global $wpdb;
	$metaTbl = $wpdb->prefix."postmeta";
	$inArr = $notInArr = $featArr = $postIDs = $taxQry = $locTax = $catTax = array();
	//$queryObj = new WP_Query( $query );
	/*echo "<pre>";print_r($query->query_vars['tax_query']);echo "</pre>"; //WP_Query Object*/
	//$itemsQuery = new WpLatteWpQuery($query);
	//$items = $wpdb->get_results( $withoutAuthorQuery, OBJECT );
	//echo "<pre>";print_r($query);echo "</pre>"; //WP_Query Object
	
	if(isset($_REQUEST['features']) && is_array($_REQUEST['features'])){
		foreach($_REQUEST['features'] as $feature){
			if(!empty($feature)) $featArr[] =  $feature;
		}
	}
	if(empty($featArr))return false;
	
	$args = array(
				  'post_type' => 'ait-item',
				  'nopaging' =>	true,
				  'post_status' => 'publish'
				  );
	
	if(isset($_REQUEST['category']) && is_array($_REQUEST['category'])){
		$catArr = array();
		foreach($_REQUEST['category'] as $cat){
			if(!empty($cat)) $catArr[] =  $cat;
		}
		if(count($catArr))$catTax = array('taxonomy' => 'ait-items','field' => 'term_id','terms' => $catArr, 'include_children' => false);
	}
	
	if(isset($_REQUEST['location']) && is_array($_REQUEST['location'])){
		$locArr = array();
		foreach($_REQUEST['location'] as $loc){
			if(!empty($loc)) $locArr[] =  $loc;
		}
		if(count($locArr))$locTax = array('taxonomy' => 'ait-locations','field' => 'term_id','terms' => $locArr, 'include_children' => false);
	}
	
	if(count($catTax) && count($locTax)){
		$taxQry = array('relation' => 'AND',$catTax, $locTax);
	}else{
		if(count($catTax)) $taxQry = array($catTax);
		if(count($locTax)) $taxQry = array($locTax);
	}
	if(count($taxQry)){
		$args['tax_query'] = $taxQry;
	}
	$args['ep_integrate'] = true; //elastic search
	$wpQueryRes = new WP_Query( $args );
	foreach($wpQueryRes->posts as $key => $postObj){
		$postID = $postObj->ID;
		$metaQry = "SELECT `meta_value` FROM $metaTbl WHERE `post_id` = $postID AND `meta_key` = '_ait-item_item-extension'";
		$metaRes = $wpdb->get_results($metaQry);
		//echo "<br/>Comparing feature for post id: $postID";
		
		if(count($metaRes) >= 1){
			$metaRSArr = unserialize($metaRes[0]->meta_value);
			$featureMatched = 0;
			$onOffArr = $metaRSArr['onoff'];	//echo "<pre>";print_r($onOffArr);echo "</pre>";
			foreach($featArr as $sngFeature){	//echo "<br/>".$sngFeature;
				if(array_key_exists($sngFeature, $onOffArr) && $onOffArr[$sngFeature] == 'on'){	//echo "<br/>Feature found";
					$featureMatched++;
					$requiredFeatures = count($featArr);
					if($featureMatched == $requiredFeatures){;/*echo "<br/>$sngFeature feature found in $postID";*/}
				}
			}
			
			if($requiredFeatures == $featureMatched){
				$inArr[] = $postID;
			}else{
				$notInArr[] = $postID;
			}
		}
	}
	/* https://generatewp.com/wp_query/  https://generatewp.com/wp_tax_query/
	1.	https://ipinfo.io/developers Access Token	9254cde846eadc [1000 requests per day]
	http://www.geonames.org/manual.html
	2.	http://ws.geonames.org/findNearbyPlaceName?lat=55.704093&lng=13.193582&username=kalyanmohit
	3.	http://maps.googleapis.com/maps/api/geocode/json?latlng=55.704093,13.193582&sensor=false&key=AIzaSyBbKQYBMWlpSYjSJG3wUt5F4D6pNNaGjrA
	
	*/
	//echo "<pre>";print_r($inArr);echo "</pre>"; echo "<pre>";print_r($notInArr);echo "</pre>"; //WP_Query Object
	return array('post__in' => $inArr, 'post__not_in' => $notInArr, 'feature_count' => count($featArr));
}

function aitFindStrPos($needle, $haystack) {
	$fnd = array();
	$pos = 0;

	while ($pos <= strlen($haystack)) {
		$pos = strpos($haystack, $needle, $pos);
		if ($pos > -1) {
			$fnd[] = $pos++;
			continue;
		}
		break;
	}
	return $fnd;
}

function aitExcludeExcerptFromSearch($where) {
	global $wpdb;
	$toFind = "/OR \(\s*".$wpdb->posts."\.post_excerpt LIKE '%.*?(%')\s*\)/m";
	$where = preg_replace(
		$toFind,
		"",
		$where);

	return $where;
}

function aitIncludeMetaInSearch($where) {
	remove_filter('posts_where', 'aitIncludeMetaInSearch');

	global $wpdb;
	// this filter callbeck can be used in ajax requests
	// search string doesn't exist in ajax requests
	// therefore search keyword must be passed as a global query from other scope
	global $__ait_query_data;
	$searchKeyword = $__ait_query_data['search-params']['s'];

	// list of meta keys where we are searching
	$metaKeys = array('subtitle', 'features_search_string');
	$metaKeys = "'" . implode( "', '", esc_sql( $metaKeys ) ) . "'";

	$toFind = "(".$wpdb->posts . ".post_title";

	$likeClause = $wpdb->esc_like( $searchKeyword );
	$likeClause = '%' . $likeClause . '%';

	// search in postmeta values and return array of post ids
	$subQuery = $wpdb->prepare(
		"SELECT group_concat(post_id) as ids FROM {$wpdb->postmeta} AS postmeta
			WHERE postmeta.meta_value LIKE %s
			AND postmeta.meta_key IN ({$metaKeys})",
		$likeClause
	);
	$subQuery = "(FIND_IN_SET(".$wpdb->posts.".ID, (".$subQuery."))) OR ";

	$subqueryLength = strlen($subQuery);

	$positions = aitFindStrPos($toFind, $where);

	$newWhere = $where;
	for ($i = 0; $i < sizeof($positions); $i++) {
		// insert subquery on each position where $toFind occured
		// consider that each next position is moved by the length of previously inserted subquery
		$newWhere = substr_replace($newWhere, $subQuery, $positions[$i] + $i * $subqueryLength, 0);
	}

	// Return revised WHERE clause
	return $newWhere;
}




function aitFilterByAdvancedFilters($metaQuery, $filters)
{
	if(empty($filters)) return $metaQuery;
    $filters = explode(";", $filters);
    foreach ($filters as $key => $value) {
        array_push($metaQuery, array(
            'key' => '_ait-item_filters-options',
            'value' => '"'.$value.'"',
            'compare' => 'LIKE',
        ));
    }

    return $metaQuery;
}

function aitIsGPSPositionOccupied($position, $args = array())
{
	global $wpdb;

	$postType = isset($args['postType']) ? $args['postType'] : 'ait-item';
	// if postId is ommited you will receive all posts from position
	$postId = isset($args['postId']) ? $args['postId'] : '';

	$sql = "
		SELECT posts.ID
			FROM $wpdb->posts posts
			INNER JOIN $wpdb->postmeta
				AS latitude
				ON posts.ID = latitude.post_id
			INNER JOIN $wpdb->postmeta
				AS longitude
				ON posts.ID = longitude.post_id
			WHERE 1=1
			AND posts.ID <> %s
			AND posts.post_status = 'publish'
			AND posts.post_type = %s
			AND	(latitude.meta_key='ait-latitude' and latitude.meta_value = %s)
			AND	(longitude.meta_key='ait-longitude' and longitude.meta_value = %s)";
	$data = $wpdb->get_col(
		$wpdb->prepare($sql, $postId, $postType, $position['lat'], $position['long'])
	);
	if (empty($data)) {
		return false;
	}

	return $data;
}


// do this action before serialized ait meta data are saved in database
add_action('save_post', function($post_id, $post){
	$slug = 'ait-item';

    if ( $slug != $post->post_type ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Prevent quick edit from clearing custom fields
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

	if(!empty($_POST)){ // bulk edit check ... in this case $_POST is still empty
        if(!empty($_POST['_ait-item_item-data']['map'])){
			$location = array(
				'lat' => $_POST['_ait-item_item-data']['map']['latitude'],
				'long'  => $_POST['_ait-item_item-data']['map']['longitude']
			);

			$args = array(
				'postType' => 'ait-item',
				'postId' => $post_id
			);
			// if there is already item on the same position, move the currently saved by one meter
			// ignore if coordinates are [1, 1]
			$ignore = ($location['lat'] == '1' && $location ['long'] == '1');

			if (!$ignore && aitIsGPSPositionOccupied($location, $args)) {
				$newLocation = AitItemCpt::moveLocationByMeters($location['lat'], $location['long'], mt_rand(1,30), mt_rand(1,30));
				$_POST['_ait-item_item-data']['map']['latitude'] = $newLocation['lat'];
				$_POST['_ait-item_item-data']['map']['longitude'] = $newLocation['lng'];
			}

			// save separately meta data for lat and long
			update_post_meta($post_id, 'ait-latitude', $_POST['_ait-item_item-data']['map']['latitude']);
			update_post_meta($post_id, 'ait-longitude', $_POST['_ait-item_item-data']['map']['longitude']);
        }
    }
}, 9, 2);

add_action( 'save_post', 'aitSaveItemMeta', 13, 2 );
function aitSaveItemMeta( $post_id, $post )
{

    $slug = 'ait-item';

    if ( $slug != $post->post_type ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Prevent quick edit from clearing custom fields
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    // save separated meta data for featured
    if(!empty($_POST)){ // bulk edit check ... in this case $_POST is still empty
        if(isset($_POST['_ait-item_item-data']['featuredItem'])){
            if (intval($_POST['_ait-item_item-data']['featuredItem']) == 1) {
                update_post_meta($post_id, '_ait-item_item-featured', '1');
            }
            else {
                update_post_meta($post_id, '_ait-item_item-featured', '0');
            }
        } else {
            // item created with directory role that cannot set item as featured
            update_post_meta($post_id, '_ait-item_item-featured', '0');
        }
    }

	// save separated meta data for subTitle
    if(!empty($_POST)){ // bulk edit check ... in this case $_POST is still empty
        if(isset($_POST['_ait-item_item-data']['subtitle'])){
			update_post_meta($post_id, 'subtitle', $_POST['_ait-item_item-data']['subtitle']);
        } else {
            // item created with directory role that cannot set subTitle
			update_post_meta($post_id, 'subtitle', '');
        }
    }

	// save separated meta data for features
	// the reason is to include features in search by keyword
	// we can chain all feature labels and descriptions to one long text because we anyway search by %LIKE%
    if(!empty($_POST)){ // bulk edit check ... in this case $_POST is still empty
        if(!empty($_POST['_ait-item_item-data']['features'])){
			$result = "";
			foreach ($_POST['_ait-item_item-data']['features'] as $feature) {
				$result .= $feature['text'].';'.$feature['desc'].';';
			}
			update_post_meta($post_id, 'features_search_string', $result);
        } else {
            // we don't need meta if features are empty
			delete_post_meta($post_id, 'features_search_string');
        }
    }

    // if item hasn't been rated yet, create rating manually
    if (get_post_meta( $post_id, 'rating_mean', true ) == '') {
        update_post_meta($post_id, 'rating_mean', '0');
    }
}



// save custom meta for items created via CSV importer
add_action('ait_csv_post_imported', function($post_type, $post_id){
    // quit if post isn't ait-item
    if ( $post_type != 'ait-item' ) {
        return;
    }

    $meta = get_post_meta($post_id, '_ait-item_item-data', true);
    // quit if post doesn't have meta for some reason
    if(empty($meta)){
        $return;
    }

    if(!empty($meta['subtitle'])){
        update_post_meta($post_id, 'subtitle', $meta['subtitle']);
    } else {
		delete_post_meta($post_id, 'subtitle');
	}

	if(!empty($meta['features'])){
		$result = "";
		foreach ($meta['features'] as $feature) {
			$result .= $feature['text'].';'.$feature['desc'].';';
		}
        update_post_meta($post_id, 'features_search_string', $result);
    } else {
		delete_post_meta($post_id, 'features_search_string');
	}

	$location = array(
		'lat' => $meta['map']['latitude'],
		'long'  => $meta['map']['longitude']
	);

	$args = array(
		'postType' => 'ait-item',
		'postId' => $post_id
	);
	// if there is already item on the same position, move the currently saved by one meter
	// ignore if coordinates are [1, 1]
	$ignore = ($location['lat'] == '1' && $location ['long'] == '1');

	if (!$ignore && aitIsGPSPositionOccupied($location, $args)) {
		$newLocation = AitItemCpt::moveLocationByMeters($location['lat'], $location['long'], mt_rand(1,30), mt_rand(1,30));
		$location['lat'] = $newLocation['lat'];
		$location['long'] = $newLocation['lng'];

		$meta['map']['latitude'] = $newLocation['lat'];
		$meta['map']['longitude'] = $newLocation['lng'];
		update_post_meta($post_id, '_ait-item_item-data', $meta);
	}

	// save separately meta data for lat and long
	update_post_meta($post_id, 'ait-latitude', $location['lat']);
	update_post_meta($post_id, 'ait-longitude', $location['long']);


	// if item hasn't been rated yet, create rating manually
    if (get_post_meta( $post_id, 'rating_mean', true ) == '') {
        update_post_meta($post_id, 'rating_mean', '0');
    }

}, 10, 2 );



add_filter( 'ait_search_filter_orderby', function($orderby, $postType = ''){
    if ($postType == 'ait-event-pro') {
		if (!aitConfig()->isMainConfigType('ait-events-pro'))
			return $orderby;
		$pluginConfig = aitConfig()->getMainConfigFiles();
		$pluginConfig = include($pluginConfig['ait-events-pro']);
		$sortingOptions = $pluginConfig['sorting']['options']['sortingDefaultOrderBy']['default'];
		foreach ($sortingOptions as $key => $value) {
            $orderby[$key] = $value;
        }
    }
    return $orderby;
}, 10, 2 );


function aitGetItemsByRadius($lat, $lng, $radius)
{
	global $wpdb, $wp_query;

	$earth_radius = 6371;

	$sql = $wpdb->prepare( "
		SELECT $wpdb->posts.ID,
			( %s * acos(
				cos( radians(%s) ) *
				cos( radians( latitude.meta_value ) ) *
				cos( radians( longitude.meta_value ) - radians(%s) ) +
				sin( radians(%s) ) *
				sin( radians( latitude.meta_value ) )
			) )
			AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
			FROM $wpdb->posts
			INNER JOIN $wpdb->postmeta
				AS latitude
				ON $wpdb->posts.ID = latitude.post_id
			INNER JOIN $wpdb->postmeta
				AS longitude
				ON $wpdb->posts.ID = longitude.post_id
			WHERE 1=1
				AND ($wpdb->posts.post_status = 'publish' )
				AND latitude.meta_key='ait-latitude'
				AND longitude.meta_key='ait-longitude'
			HAVING distance < %s
			ORDER BY $wpdb->posts.menu_order ASC, distance ASC",
		$earth_radius,
		$lat,
		$lng,
		$lat,
		$radius
	);

	$post_ids = $wpdb->get_results( $sql, OBJECT_K );

	return array_keys($post_ids);

}


/* returns array of terms filtered by featured categories setting */
function aitFilterTerms($terms = array(), $maxDisplayed = 1){
	// function takes whole set of assigned terms
	// reorders them so the featured terms are at first positions
	// returns filtered terms by count $maxDisplayed

	$maxDisplayed = intval($maxDisplayed); // because if the function is called from template, string is passed instead of integer
	$result = $terms;

	if($maxDisplayed > 0){
		if(count($terms) > 0){
			$result = array();

			$terms_all = array();       // reorder array
			$terms_featured = array();
			$terms_general = array();   // not featured terms

			// split terms to featured and not-featured arrays
			foreach($terms as $index => $term){
				$term_meta = get_option($term->taxonomy . "_category_" . $term->term_id);
				if(!empty($term_meta['category_featured'])){
					array_push($terms_featured, $term);
				} else {
					array_push($terms_general, $term);
				}
			}
			$terms_all = array_merge($terms_featured, $terms_general);  // new order of terms, first are featured terms

			foreach ($terms_all as $index => $term) {
				if(count($result) < $maxDisplayed){
					array_push($result, $term);
				}
			}
		} else {
			// item has no categories assigned, this means no categories should be displayed
			$result = array();
		}
	} /*else {
		// maxDisplayed is less or equal zero, this means no categories should be displayed
		$result = array();
	}*/

	return $result;
}

/* function will order the array of terms */
/* input is unordered array basically terms in alphabetical order */
/* output is ordered array like */
/* parent-1, parent-1-child-1, parent-1-child-2, parent-2, parent-2-child-1, etc */
function aitOrderTermsByHierarchy($terms = array()){
	$result = $terms;

	$result = $result instanceof WP_Error ? array() : $result;
	$result = isset($result['errors']) ? array() : $result;
	$result = is_array($result) ? $result : array();

	/* category hierarchy order */
	// remove the else branches ...
	$term_hierarchy = array();
	$term_parents = array();
	$term_children = array();

	if(count($result) > 0){
		foreach($result as $index => $term){
			if($term->parent == 0){
				// parent term
				array_push($term_parents, $term);
			} else {
				// child term
				array_push($term_children, $term);
			}
		}
	}

	if(count($term_parents) != 0){
		/* place to order parent terms (alphabetically, id, etc)*/
		/* place to order parent terms (alphabetically, id, etc)*/

		foreach($term_parents as $p_index => $p_term){
			array_push($term_hierarchy, $p_term);

			if(count($term_children) != 0){
				foreach ($term_children as $ch_index => $ch_term) {
					if($ch_term->parent == $p_term->term_id){
						array_push($term_hierarchy, $ch_term);
						// remove the child from the $term_children array ? .. save computing time ?
					}
				}
			} else {
				// there are no child terms, move on
			}
		}

		$result = $term_hierarchy;
	} else {
		// there are no parent terms, no hierarchy order, just return the result maybe
	}
	/* category hierarchy order */

	return $result;
}