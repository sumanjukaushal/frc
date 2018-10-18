<?php

global $aitThemeOptions;

// main functionality
require_once dirname(__FILE__) . '/directory.php';

require_once dirname(__FILE__) . '/sorting.php';
require_once dirname(__FILE__) . '/accounts.php';

if ( isset($aitThemeOptions->members->easyAdminEnable) ) {
	require_once dirname(__FILE__) . '/easy-admin/easy-admin.php';
}

if ( isset($aitThemeOptions->rating->enableRating) ) {
	require_once dirname(__FILE__) . '/rating.php';
}

if ( isset($aitThemeOptions->directory->enableClaimListing) ) {
	require_once dirname(__FILE__) . '/claim-listing.php';
}