<?php

/**
 * AIT WordPress Theme
 *
 * Copyright (c) 2012, Affinity Information Technology, s.r.o. (http://ait-themes.club)
 */

global $aitThemeOptions, $latteParams, $wp_query, $registerErrors, $registerMessages;

// register form errors
if(isset($registerErrors)){
	$latteParams['registerErrors'] = $registerErrors->get_error_message();
}
// register form info
if(isset($registerMessages)){
	$latteParams['registerMessages'] = $registerMessages;
}

$mapCategory = 0;
$mapLocation = 0;
$mapSearch = $wp_query->query_vars['s'];
// parse tax query - only one category and location
if(isset($wp_query->tax_query)){
	$taxQueries = $wp_query->tax_query->queries;
	foreach ($taxQueries as $tax) {
		if(is_array($tax) && $tax['field'] == 'id'){
			if($tax['taxonomy'] == 'ait-dir-item-category'){
				$mapCategory = (isset($tax['terms'][0])) ? $tax['terms'][0] : 0;
			} elseif ($tax['taxonomy'] == 'ait-dir-item-location') {
				$mapLocation = (isset($tax['terms'][0])) ? $tax['terms'][0] : 0;
			}
		} elseif (is_array($tax) && $tax['field'] == 'slug') {
			if($tax['taxonomy'] == 'ait-dir-item-category'){
				$mapCategory = (isset($tax['terms'][0])) ? get_term_by( 'slug', $tax['terms'][0], 'ait-dir-item-category' )->term_id : 0;
			} elseif ($tax['taxonomy'] == 'ait-dir-item-location') {
				$mapLocation = (isset($tax['terms'][0])) ? get_term_by( 'slug', $tax['terms'][0], 'ait-dir-item-location' )->term_id : 0;
			}
		}
	}
}
$latteParams['mapCategory'] = $mapCategory;
$latteParams['mapLocation'] = $mapLocation;
$latteParams['mapSearch'] = $mapSearch;

// for search form
$categories = get_terms('ait-dir-item-category', array(
	'hide_empty'		=> false,
	'orderby'			=> 'name'
));
$latteParams['categories'] = $categories;
$locations = get_terms('ait-dir-item-location', array(
	'hide_empty'		=> false,
	'orderby'			=> 'name'
));
$latteParams['locations'] = $locations;

// hierarchical autocomplete
if (isset($aitThemeOptions->search->searchCategoriesHierarchical)) {
	$hCategories = array();
	aitSortTermsHierarchicaly($categories, $hCategories);
	$latteParams['categoriesHierarchical'] = substr(aitGenerateHirerarchicalAutocomplete($hCategories), 0, -1);
}
if (isset($aitThemeOptions->search->searchLocationsHierarchical)) {
	$hLocations = array();
	aitSortTermsHierarchicaly($locations, $hLocations);
	$latteParams['locationsHierarchical'] = substr(aitGenerateHirerarchicalAutocomplete($hLocations), 0, -1);
}

// directory search
if(isset($_GET['dir-search']) && $_GET['dir-search'] == true){
	$latteParams['searchTerm'] = $wp_query->query_vars['s'];

	$latteParams['isGeolocation'] = (isset($_GET['geo'])) ? true : null;
	$latteParams['geolocationRadius'] = (isset($_GET['geo-radius'])) ? $_GET['geo-radius'] : 100;
	$latteParams['geolocationCircle'] = (isset($aitThemeOptions->search->showAdvancedSearchRadius)) ? true : null;
	if(isset($aitThemeOptions->search->searchShowMap)){
		// map
		$latteParams['headerType'] = 'map';
		$radius = array();
		if(isset($_GET['geo'])){
			$radius[] = $_GET['geo-radius'];
			$radius[] = $_GET['geo-lat'];
			$radius[] = $_GET['geo-lng'];
		}
		$latteParams['items'] = getItems(intval($_GET['categories']),intval($_GET['locations']),$wp_query->query_vars['s'],$radius);
	} else {
		$latteParams['headerType'] = 'none';
	}
} else if(isset($latteParams['isDirTaxonomy']) || isset($latteParams['isDirSingle'])){
	// map
	$latteParams['headerType'] = 'map';
	$latteParams['isGeolocation'] = (isset($aitThemeOptions->directoryMap->enableGeolocation)) ? true : null;
	//$latteParams['geolocationOnlyInRadius'] = (isset($aitThemeOptions->directoryMap->geolocationOnlyInRadius)) ? true : null;
	$latteParams['geolocationRadius'] = (isset($aitThemeOptions->directoryMap->geolocationRadius)) ? $aitThemeOptions->directoryMap->geolocationRadius : 100;
	$latteParams['geolocationCircle'] = (isset($aitThemeOptions->directoryMap->geolocationCircle)) ? true : null;
} else {
	// LOCAL
	if(!empty($latteParams['post']) && isset($latteParams['post']->options('header')->overrideGlobal)){
		$latteParams['headerType'] = $latteParams['post']->options('header')->headerType;
		switch ($latteParams['post']->options('header')->headerType) {
			case 'map':
				$latteParams['items'] = getItems();
				// geolocation
				$latteParams['isGeolocation'] = (isset($latteParams['post']->options('header')->enableGeolocation)) ? true : null;
				//$latteParams['geolocationOnlyInRadius'] = (isset($latteParams['post']->options('header')->geolocationOnlyInRadius)) ? true : null;
				$latteParams['geolocationRadius'] = (isset($latteParams['post']->options('header')->geolocationRadius)) ? $latteParams['post']->options('header')->geolocationRadius : 100;
				$latteParams['geolocationCircle'] = (isset($latteParams['post']->options('header')->geolocationCircle)) ? true : null;
				break;
			case 'image':
				$latteParams['headerImage'] = $latteParams['post']->options('header')->image;
				$latteParams['headerImageSize'] = aitGetImageSize($latteParams['post']->options('header')->image);
				break;
			case 'slider':
				$latteParams['headerSlider'] = $latteParams['post']->options('header')->slider;
				break;
			default:
				break;
		}
	// GLOBAL
	} else {
		$latteParams['headerType'] = $aitThemeOptions->header->headerType;
		switch ($aitThemeOptions->header->headerType) {
			case 'map':
				$latteParams['items'] = getItems();
				// geolocation
				$latteParams['isGeolocation'] = (isset($aitThemeOptions->directoryMap->enableGeolocation)) ? true : null;
				//$latteParams['geolocationOnlyInRadius'] = (isset($aitThemeOptions->directoryMap->geolocationOnlyInRadius)) ? true : null;
				$latteParams['geolocationRadius'] = (isset($aitThemeOptions->directoryMap->geolocationRadius)) ? $aitThemeOptions->directoryMap->geolocationRadius : 100;
				$latteParams['geolocationCircle'] = (isset($aitThemeOptions->directoryMap->geolocationCircle)) ? true : null;
				break;
			case 'image':
				$latteParams['headerImage'] = $aitThemeOptions->header->image;
				$latteParams['headerImageSize'] = aitGetImageSize($aitThemeOptions->header->image);
				break;
			case 'slider':
				$latteParams['headerSlider'] = $aitThemeOptions->header->slider;
				break;
			default:
				break;
		}
	}
}

WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();
