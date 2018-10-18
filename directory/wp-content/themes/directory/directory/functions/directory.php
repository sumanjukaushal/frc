<?php
ini_set('memory_limit','2048M');
$ipAddress = $_SERVER['REMOTE_ADDR'];
if(isset($_REQUEST['test_query']) || '112.196.110.195' == $ipAddress || isset($_REQUEST['rasu'])){
	//error_reporting(E_ALL);
	//ini_set("display_errors", 1); 
         //error_reporting(E_ALL & ~E_PARSE & ~E_WARNING);
         ini_set("display_errors", "1");
         error_reporting(E_ERROR);
}
add_filter('pre_get_posts','aitDirMainQuery');

if( !function_exists('isRemoteAddress') ){
	function isRemoteAddress($ipAddress){
		foreach($ipAddress as $sngIPAddr){
			if(
				$_SERVER['HTTP_X_FORWARDED_FOR'] == $sngIPAddr ||
				$_SERVER['HTTP_CF_CONNECTING_IP'] == $sngIPAddr ||
				$_SERVER['REMOTE_ADDR'] == $sngIPAddr
			){
				return true;
			}
		}
		return false;
	}
}
$isMyIP = isRemoteAddress(array('125.62.104.194', '149.135.34.230'));

if( array_key_exists('iframe', $_REQUEST) && empty($_GET['categories']) ){
	$_REQUEST['categories'] = $_GET['categories'] = '9,30,14,155,12,20,6,2096,5,7,8,10,106,15,17,4,2797,107,2088';
}

function getItemIdsNotMatchingFeatures($items, $features){
	$notIn = array();
	foreach ($items as $key => $item) {
		//--------start: filtering w.r.t features - added on 12th oct, 2018
		$featuresMatching = true;
		if(isset($_REQUEST['feature'])){
			if(is_array($_REQUEST['feature'])){
				foreach($_REQUEST['feature'] as $feature){
					$featureKey = trim($feature);
					//print_r($featureKey);print_r($featureKey);die;
					$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
					if( array_key_exists($featureKey, $item->optionsDir) ){
						if(
						   is_array($item->optionsDir[$featureKey]) &&
						   $item->optionsDir[$featureKey]['enable'] == 'enable'){
							//features are matching;
							$featuresMatching = true;
							
						}elseif($item->optionsDir[$featureKey] == 'Y'){
							//features are matching;
							$featuresMatching = true;							
						}
					}else{
						$featuresMatching = false;
						if(!in_array($item->ID, $notIn))$notIn[] = $item->ID;
						break;
					}
				}
			}
		}
		if(!$featuresMatching){if(!in_array($item->ID, $notIn))$notIn[] = $item->ID;}
		//end-----end:filtering w.r.t features - added on 12th oct, 2018
	}
	//print_r($notIn);die;
	return $notIn;
}

function aitDirMainQuery($query) {
	//Note: aitDirMainQuery function is running before getItems when we are calling pagination URL's or call by browser
	$ipAddress = $_SERVER['REMOTE_ADDR'];
	global $aitThemeOptions;
	// only main query
	global $isMyIP;
	
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
			}else{
				// is directory search
				if(!isset($_GET['categories'])) {
					$_GET['categories'] = 0;
				}
				if(!isset($_GET['locations'])) {
					$_GET['locations'] = 0;
				}

				$query->set('post_type','ait-dir-item');

				$taxquery = array();
				if(($_REQUEST['category'] == 0) && ($_REQUEST['location'] == 0) && !isset($_REQUEST['categories'])){
					$category = array(9,14,155,12,20,5,6,5,8,2087,210, 10, 17, 107, 7, 2088, 30, 2797);
					//new 17, 110, 2088: new 16 Sep: 2797
				}
				$taxquery['relation'] = 'AND';
				$termsArr = array();
				if('27.255.225.94' == $ipAddress){
					//echo "<pre>rasu";print_r($_GET['categories']);echo "</pre>";
				}
				$rasuCustomQuery = array();
				$rasuCustomQuery['relation'] = 'AND';
				
				if(isset($_REQUEST['categories']) && !empty($_REQUEST['categories'])){
					$categoryArr = array();
					$categories = trim($_GET['categories']);
					$pos = strpos("---".$categories, ",");
					if($pos){
						$categoryArr = explode(",", $categories);
					}else{
						$categoryArr[] = $categories;
					}
					$rasuCustomQuery[] = array(
						'taxonomy' => 'ait-dir-item-category',
						'field' => 'id',
						'terms' => $categoryArr,
						'include_children' => false, //updated on November 4, 2015 and set to false
					);
				}elseif(isset($_REQUEST['category']) && !empty($_REQUEST['category'])){
					$categoryArr = array();
					$categories = trim($_GET['category']);
					$pos = strpos("---".$categories, ",");
					if($pos){
						$categoryArr = explode(",", $categories);
					}else{
						$categoryArr[] = $categories;
					}
					$rasuCustomQuery[] = array(
						'taxonomy' => 'ait-dir-item-category',
						'field' => 'id',
						'terms' => $categoryArr,
						'include_children' => false, //updated on November 4, 2015 and set to false
					);
				}
				//Start: code added by rasu on Mar 9
				$search = "";
				if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
					$search = $_REQUEST['search'];
				}
				$locationArr = array();
				if(isset($_REQUEST['locations']) && !empty($_REQUEST['locations'])){
					$locations = trim($_REQUEST['locations']);
					$pos = strpos("---".$locations, ",");
					if($pos){
						$locationArr = explode(",", $locations);
					}else{
						if(!empty($locations)){
							$locationArr[] = $locations;
						}
					}
					if(count($locationArr) >= 1){
						$rasuCustomQuery[] = array(
							'taxonomy' => 'ait-dir-item-location',
							'field' => 'id',
							'terms' => $locationArr,
							'include_children' => false, //updated on November 4, 2015 and set to false
						);
						$term = get_term( $locationArr[0], 'ait-dir-item-location');
					}
				}elseif(isset($_REQUEST['location']) && !empty($_REQUEST['location'])){
					$locations = trim($_GET['location']);
					$pos = strpos("---".$locations, ",");
					if($pos){
						$locationArr = explode(",", $locations);
					}else{
						$locationArr[] = $locations;
					}
					if(count($locationArr) >= 1){
						$rasuCustomQuery[] = array(
							'taxonomy' => 'ait-dir-item-location',
							'field' => 'id',
							'terms' => $locationArr,
							'include_children' => false, //updated on November 4, 2015 and set to false
						);
						$term = get_term( $locationArr[0], 'ait-dir-item-location');
					}
				}
				if(count($rasuCustomQuery) >= 1){
					$taxquery = $rasuCustomQuery;
				}
				
					
				$radius = array();
				$locRadius = 0;
				if(isset($_REQUEST['loc_radius']) && intval($_REQUEST['loc_radius'])){
					$locRadius = $_REQUEST['loc_radius'];
				}
				
				if(isset($term) && is_object($term)){
					$locationName = $term->name;
					$address = "{$locationName}, Australia";
					if(!empty($search)){
						$address = "{$search},{$locationName}, Australia";
					}
					
					if($isMyIP && false){echo "<pre>";print_r($_REQUEST);print_r($term);echo "</pre>";die('163');}
					
					$prepAddr = str_replace(' ', '+',$address);
					$searchURL = "https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key=AIzaSyDgK8iRF7H9Prct4gY2V1inq1bXDh2A-NE";
					$geocode = file_get_contents($searchURL);
					$output= json_decode($geocode);
					$locFound = false;
					if(is_array($output) && array_key_exists('0', $output['results'])){
						$locFound = true;
						if(
							array_key_exists('geometry', $output['results'][0]) &&
							array_key_exists('location', $output['results'][0]['geometry'])
						 ){
							$lat = $output['results'][0]['geometry']['location']['lat'];
							$long = $output['results'][0]['geometry']['location']['lng'];
						}
					}else{
						$locFound = true;
						$lat = $output->results[0]->geometry->location->lat;
						$long = $output->results[0]->geometry->location->lng;
					}
					if(!empty($locRadius) && $locFound){$radius = array($locRadius, $lat,$long);}
					$locVar = "if case";
				}elseif(!empty($search)){
					$locationName = $search;
					$address = "{$locationName}, Australia";
					$prepAddr = str_replace(' ', '+',$address);
					$searchURL = "https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key=AIzaSyDgK8iRF7H9Prct4gY2V1inq1bXDh2A-NE";
					$geocode = file_get_contents($searchURL);
					$output= json_decode($geocode);
					
					$locFound = false;
					if(is_array($output) && array_key_exists('0', $output['results'])){
						$locFound = true;
						if(
							array_key_exists('geometry', $output['results'][0]) &&
							array_key_exists('location', $output['results'][0]['geometry'])
						){
							$lat = $output['results'][0]['geometry']['location']['lat'];
							$long = $output['results'][0]['geometry']['location']['lng'];
						}
					}else{
						$locFound = true;
						$lat = $output->results[0]->geometry->location->lat;
						$long = $output->results[0]->geometry->location->lng;
					}
					if($locFound){
						if(empty($locRadius))$locRadius = 50; //default
						$radius = array($locRadius, $lat,$long);
					}
					$locVar = "else case";
				}
				//End: only for getting geo location for custom search and
				
				if($isMyIP && false){echo "<pre>$locVar\n";print_r($radius);print_r($_REQUEST);print_r($term);echo "</pre>";die('163');}
				
				//End: ------------added on Feb 28------------------
				$query->set('tax_query',$taxquery);

				$num = (isset($GLOBALS['aitThemeOptions']->search->searchItemsPerPage)) ? $GLOBALS['aitThemeOptions']->search->searchItemsPerPage : 10;
				$query->set('posts_per_page',$num);
                //Start: added on 12 Oct
                if(array_key_exists('features', $_REQUEST)){
            	    $_REQUEST['feature'] = $_REQUEST['features'];
            	    $feature = $_REQUEST['feature'];
            	    if(strpos($feature, ",")){
            			$feature = explode(",", $feature);
            			$featureArr = array();
            			foreach($feature as $featureValue){
            				$featureArr[] = trim($featureValue);
            			}
            			$_REQUEST['feature'] = $featureArr;
            		}
            	}
                //End: added on 12 Oct
				// filter only items by geolocation
				if(isset($_GET['geo'])){
					$category = intval($_GET['categories']);
					$location = intval($_GET['locations']);
					$params = array(
						'ep_integrate' => true, //elastic search
						'post_type'			=> 'ait-dir-item',
						'nopaging'			=>	true,
						'post_status'		=> 'publish'
					);
					$taxquery = array();
					if(($_REQUEST['category'] == 0) && ($_REQUEST['location'] == 0) && !isset($_REQUEST['categories'])){
						$category = array(9,14,155,12,20,5,6,5,8,2087,210, 10, 17, 107, 7, 2088, 30, 2797);
						//new 17, 110, 2088: new 16 Sep: 2797
					}
					$taxquery['relation'] = 'AND';
					if($category != 0){
						$taxquery[] = array(
							'taxonomy' => 'ait-dir-item-category',
							'field' => 'id',
							'terms' => $category,
							'include_children' => true //true updated on Feb 28, 2016
						);
					}
					if($location != 0){
						$taxquery[] = array(
							'taxonomy' => 'ait-dir-item-location',
							'field' => 'id',
							'terms' => $location,
							'include_children' => false //true updated on Feb 28, 2016
						);
					}
					if($category != 0 || $location != 0){
						$params['tax_query'] = $taxquery;
					}
					if($query->get('s') != ''){
						$params['s'] = $query->get('s');
					}
					$itemsQuery = new WP_Query();
					if(!array_key_exists('ep_integrate', $params)){
						$params['ep_integrate'] = true;
					}
					$items = $itemsQuery->query($params);
					$notIn = array();
					// add item details
					$notIn = getItemIdsNotMatchingFeatures($items, $_REQUEST['feature']);
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
				}elseif(count($radius) >= 1){
					//new cases added by me to match ajax result with pagination
					$params = array(
						'ep_integrate' => true, //elastic search
						'post_type'			=> 'ait-dir-item',
						'nopaging'			=>	true,
						'post_status'		=> 'publish'
					);
					if(count($rasuCustomQuery) > 1){
						$params['tax_query'] = $rasuCustomQuery;
					}
					
					$itemsQuery = new WP_Query();
					if(!array_key_exists('ep_integrate', $params)){
						$params['ep_integrate'] = true;
					}
					
					global $wp_filter;
					$wp_filter_clone = $wp_filter;
					$wp_filter = array(); //FRC - hack by Mohit
					$itemsQuery = new WP_Query();
					$items = $itemsQuery->query($params);
					$wp_filter=$wp_filter_clone;
					
					$notIn = array();
					// add item details
					$inArray = array();
					$notIn = getItemIdsNotMatchingFeatures($items, $_REQUEST['feature']);
					foreach ($items as $key => $item) {
						$inArray[] = $item->ID;
						// options
						$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
						// filter radius
						$gpsLatitude = $gpsLongitude = '';
						if(array_key_exists('gpsLatitude', $item->optionsDir)){$gpsLatitude = $item->optionsDir['gpsLatitude'];}
						if(array_key_exists('gpsLongitude', $item->optionsDir)){$gpsLongitude = $item->optionsDir['gpsLongitude'];}
						if(!isPointInRadius($radius[0], floatval($radius[1]), floatval($radius[2]), $gpsLatitude, $gpsLongitude)){$notIn[] = $item->ID;}
					}
					//if($isMyIP){echo "<pre>$locVar\n";print_r($inArray);print_r($notIn);print_r($radius);print_r($_REQUEST);print_r($term);echo "</pre>";die('307');}
					$query->set('post__not_in',$notIn);
				}
			}
		}
        
        //Start: 14 Oct, 2018
        if(!isset($items)){
		    $itemsQuery = new WP_Query();
		    if(!isset($params) or !count($params)){
		        $params = array(
						'ep_integrate' => true, //elastic search
						'post_type'			=> 'ait-dir-item',
						'nopaging'			=>	true,
						'post_status'		=> 'publish'
					);
		    }
		    $items = $itemsQuery->query($params);
		    
			$notIn = array();
			$notIn = getItemIdsNotMatchingFeatures($items, $_REQUEST['feature']);
			$query->set('post__not_in',$notIn);
		}
        //End: 14 Oct, 2018
        
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
	ini_set('memory_limit','2048M');
	//echo "<pre>";print_r($_REQUEST);echo "</pre>";
	if(isset($_REQUEST['test_query']) ){
		//echo "getItems";
		//die;
	}
	if(isset($_REQUEST['S']) && !isset($_REQUEST['search'])){
		//added on 12th Feb
		$_REQUEST['search'] = $_REQUEST['S'];
	}
	
	if(isset($_REQUEST['s']) && !isset($_REQUEST['search'])){
		//added on 28th Feb, 2016
		$_REQUEST['search'] = $_REQUEST['s'];
	}
	
	/*
	 *
		echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
		$browser = get_browser(null, true);
		print_r($browser);
		$items = getItems($_POST['category'],$_POST['location'],$_POST['search'],$_POST['radius']);
	**/
	$args = array('orderby' => 'name',);
	/*if(!empty($location) && !is_array($location)){
	   $term = get_term( $location, 'ait-dir-item-location');
	}*/
	
	//******** code for grabbing locations received from broser call added on Feb 28, 2016 ***********
	if(isset($_REQUEST['locations']) && !empty($_REQUEST['locations']) && !is_array($_REQUEST['locations'])){
		$location = trim($_REQUEST['locations']);
	}
	if(is_string($location) && strpos($location, ",")){
		$location = explode(",",$location);	
	}
	//End: ******* code for grabbing locations received from broser call added on Feb 28, 2016 *******
	
	if(is_array($location) && count($location) >= 1){
		//Case 'States': No restriction should be for radius by default
		$term = get_term( $location[0], 'ait-dir-item-location');
	}elseif(!empty($location)){
		//Case 'custom_location': By default 20 Km
		if(empty($locRadius)){$locRadius = 50;} //Asked by Rob on 7th Feb, message_id =
		$term = get_term( $location, 'ait-dir-item-location');
	}
	
	if(isset($_REQUEST['search'])){
		$search = $_REQUEST['search'];
	}
	$locRadius = (int)$_REQUEST['loc_radius'];
	
	if(isset($term) && is_object($term)){
		$locationName = $term->name;
		$address = "{$locationName}, Australia";
		if(!empty($search)){
			$address = "{$search},{$locationName}, Australia";
		}
		$prepAddr = str_replace(' ', '+',$address);
		$searchURL = "https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key=AIzaSyBbKQYBMWlpSYjSJG3wUt5F4D6pNNaGjrA";
		$searchURL = "https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key=AIzaSyADDx4qOiFApfmQ_IYjAoOfE-W_otkLTOo";
		$geocode = file_get_contents($searchURL);
		$output= json_decode($geocode);
		$locFound = false;
		/*if(array_key_exists('0', $output->results)){
		  $locFound = true;
		  $lat = $output->results[0]->geometry->location->lat;
		  $long = $output->results[0]->geometry->location->lng;
		}*/
		if(is_array($output) && array_key_exists('0', $output['results'])){
			$locFound = true;
			if(
				array_key_exists('geometry', $output['results'][0]) &&
				array_key_exists('location', $output['results'][0]['geometry'])
			 ){
				$lat = $output['results'][0]['geometry']['location']['lat'];
				$long = $output['results'][0]['geometry']['location']['lng'];
			}
		}else{
			$locFound = true;
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;
		  
		}
		$radius = array();
		if(!empty($locRadius) && $locFound){
			$radius = array($locRadius, $lat,$long);
		}
	}elseif(!empty($search)){
		$locationName = $search;
		$address = "{$locationName}, Australia";
		$prepAddr = str_replace(' ', '+',$address);
		$searchURL = "https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key=AIzaSyBbKQYBMWlpSYjSJG3wUt5F4D6pNNaGjrA";
		$geocode = file_get_contents($searchURL);
		$output= json_decode($geocode);
		//------------added on Apr 26, 2016----------
		if(isset($_REQUEST['rasu'])){
			echo "<pre>>>>>>>>>$searchURL";
			print_r($output);
			echo "<<<<<<<</pre>";die;
		}
		//-------------------------------------------
		$locFound = false;
		if(is_array($output) && array_key_exists('0', $output['results'])){
			$locFound = true;
			if(
				array_key_exists('geometry', $output['results'][0]) &&
				array_key_exists('location', $output['results'][0]['geometry'])
			){
				$lat = $output['results'][0]['geometry']['location']['lat'];
				$long = $output['results'][0]['geometry']['location']['lng'];
			}
		}else{
			$locFound = true;
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;
		}
		$radius = array();
		if($locFound){
			if(empty($locRadius))$locRadius = 50;
			$radius = array($locRadius, $lat,$long);
		}
	}
	//------------added on Apr 26, 2016----------
	
    //-------------------------------------------
	if(empty($search)){
		if(array_key_exists('search',$_REQUEST) & !empty($_REQUEST['search'])){
			$search = $_REQUEST['search'];
		}
	}
	if( isset($_REQUEST['location']) && !empty($_REQUEST['location']) && empty($location)){
		$location = $_REQUEST['location'];
	}
	if($location == -1 || $location == '-1'){$location = 0;}//hack added for chrome
	
	//******** code for grabbing categories received from broser call added on Feb 28, 2016 ***********
	if(isset($_REQUEST['categories']) && !empty($_REQUEST['categories']) && !is_array($_REQUEST['categories'])){
		$category = trim($_REQUEST['categories']);
	}
	//End: ******* code for grabbing categories received from broser call added on Feb 28, 2016 *******
	
	//******** code for grabbing categories received from browser call added on Feb 28, 2016 ***********
	if(array_key_exists('features', $_REQUEST)){
	    $_REQUEST['feature'] = $_REQUEST['features'];
	}
	if(isset($_REQUEST['feature']) && !empty($_REQUEST['feature']) && !is_array($_REQUEST['feature'])){
		$feature = trim($_REQUEST['feature']);
		if(strpos($feature, ",")){
			$feature = explode(",", $feature);
			$featureArr = array();
			foreach($feature as $featureValue){
				$featureArr[] = trim($featureValue);
			}
			$_REQUEST['feature'] = $featureArr;
		}
	}
	//End: ******* code for grabbing categories received from browser call added on Feb 28, 2016 *******
	
	if( isset($_REQUEST['category']) && !empty($_REQUEST['category']) && empty($category)){
		$category = $_REQUEST['category'];
	}
	if($category == '111111111'){$category = 0;}//hack added for chrome
	$params = array(
		'post_type' => 'ait-dir-item',
		'nopaging' =>	true,
		'post_status' => 'publish'
	);
	
	$taxquery = array();
	//-----hack added on Feb 12 for case when we are searching only custom location--------
	$userID = get_current_user_id();
	if(($_REQUEST['category'] == 0) && ($_REQUEST['location'] == 0) && !isset($_REQUEST['categories'])){
		$category = array(9,14,155,12,20,5,6,5,8,2087,210, 10, 17, 107, 7, 2088, 30, 2797);
		//new 17, 110, 2088: new 16 Sep: 2797
	}
	//-----hack added on Feb 12 for case when we are searching only custom location--------
	$taxquery['relation'] = 'AND';
	if(!is_array($category) && strpos($category, ",")){
		$category = explode(",",$category);	
	}
	if(is_string($location) && strpos($location, ",")){
		$location = explode(",",$location);	
	}
	
	if($category != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-category',
			'field' => 'id',
			'terms' => $category,
			'include_children' => true //true updated on Feb 28, 2016
		);
	}
	
	if($location != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-location',
			'field' => 'id',
			'terms' => $location,
			'include_children' => false, //updated on May 20,2105 for not displaying cities other than searched.
			'operator' => 'IN',
		);
	}
	
	if($category != 0 || $location != 0){
		$params['tax_query'] = $taxquery;
	}

	if($search != ''){
		//$params['s'] = $search;//commented on 2nd Feb
		//Rob wants it thhis way if user is searching location, he want to show all type of items falling withing radius range, so I commented bottom line
		//$params['s'] = $search;
	}
	
	if(isset($_REQUEST['test_query'])){
		ini_set('memory_limit','2048M');
		$max_time = ini_get("max_execution_time");
		$memLimit = ini_get("memory_limit");
		echo  "Memory Usage:".memory_get_usage();
		$params['tax_query'];
		echo "<br/>Max Time:".$max_time." <br/>\n Memory Limit:".$memLimit;
		//die;
	}
	
	
	global $wpdb;
	$itemsQuery = new WP_Query();
	if($category == 0 && $location == 0 && empty($_REQUEST['search'])){
		//hack for chrome and firefox
		$query = "SELECT gwr_posts.* FROM gwr_posts WHERE 1=1 AND gwr_posts.post_type = 'ait-dir-item' AND ((gwr_posts.post_status = 'publish')) ORDER BY gwr_posts.post_date DESC ";
		$items = $wpdb->get_results( $query, OBJECT );
	}else{
		
		$params['tax_query'][0]['include_children'] = 0; //Added on July 23, 2016
		if(isset($_REQUEST['test_query'])){
			echo "<pre>---else ";print_r($params);echo "##########</pre>";die;
		}
		if(!array_key_exists('ep_integrate', $params)){
		    $params['ep_integrate'] = true;
		}
		$items = $itemsQuery->query($params);
		//-------if logged in users, replace post_auther for query
		$userID = get_current_user_id();
		if ($userID){
			$withoutAuthorQuery = str_replace("AND gwr_posts.post_author IN ($userID) ", " ", $itemsQuery->request);
			if(isset($_REQUEST['test_query'])){
				echo "<pre>---else $withoutAuthorQuery";echo "##########</pre>";die;
			}
			$items = $wpdb->get_results( $withoutAuthorQuery, OBJECT );
		}
		//-------End: if logged in users, replace post_auther for query
	}
        
	
	if(isset($_REQUEST['test_query']) || '124.455.66.222' == $ipAddress){
		//$results = $wpdb->get_results( 'SELECT * FROM wp_options WHERE option_id = 1', OBJECT );
		/*SELECT   gwr_posts.* FROM gwr_posts  INNER JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id) WHERE 1=1  AND ( gwr_term_relationships.term_taxonomy_id IN (1594,1742)) AND gwr_posts.post_author IN (53)  AND gwr_posts.post_type = 'ait-dir-item' AND ((gwr_posts.post_status = 'publish')) GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_date DESC*/
		echo "Last SQL-Query: {$itemsQuery->request}<br/>\n\n</br/>";
		echo $userID = get_current_user_id();
		echo "<pre>"; print_r($taxquery);echo "</pre>";die;
		if ($userID){
			global $wpdb;
			//$withoutAuthorQuery = str_replace("AND gwr_posts.post_author IN ($userID) ", " ", $itemsQuery->request);
			//$results = $wpdb->get_results( $withoutAuthorQuery, OBJECT );
			//echo "<pre>"; print_r($results);echo "</pre>";
		}
		//echo "<pre>"; print_r($params);echo "</pre>";
	}
	
	//WordPress Query API
	//Note: this may be getting ids of all items
	
	$counter = 0;
	if( isset($_REQUEST['search']) && !empty($_REQUEST['search']) ){
		$search = strtolower($_REQUEST['search']);
		//Rob wants it this way if user is searching location, he want to show all type of items falling withing radius range, so I commented bottom line; so I have nullified $search
		$search = "";
	}
	
	foreach ($items as $key => $item) {
		$counter++;
		// options
		$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
		
		if(!is_array($item->optionsDir) || count($item->optionsDir) == 0){
		  unset($items[$key]);
		  continue;
		}
		
		//preg_match("/{$search}/i", $item->post_title)
		$titleFound = false;
		if(property_exists($item, 'post_title') && (!empty($search))){
			$pos = strpos(strtolower($item->post_title), $search);
			if($pos !== false){
			  $titleFound = true;
			}
		}
		
		$addressFound = false;
		if(array_key_exists('address',$item->optionsDir) && (!empty($search))){
			$pos = strpos(strtolower($item->optionsDir['address']), $search);
			if($pos !== false){
			  $addressFound = true;
			}
		}
		
		$cityFound = false;
		if(array_key_exists('city',$item->optionsDir) && (!empty($search))){
			$pos = strpos(strtolower($item->optionsDir['city']), $search);
			if($pos !== false){
			  $cityFound = true;
			}
		}
		
		$stateFound = false;
		if(array_key_exists('state',$item->optionsDir) && (!empty($search))){
			$pos = strpos(strtolower($item->optionsDir['state']), $search);
			if($pos !== false){
			  $stateFound = true;
			}
		}
		
		if( !empty($search) ){
		   if ($titleFound || $addressFound || $cityFound || $stateFound) {
		      ;
		   }else{
		      unset($items[$key]);
		       continue;
		   }
		}
		// filter radius
		
		//isPointInRadius($radiusInKm, $cenLat, $cenLng, $lat, $lng)
		//----------new code addition by rasa developers----------------------
		//unset all items which do not have matching features
		$featuresMatching = true;
		if(isset($_REQUEST['feature'])){
			if(is_array($_REQUEST['feature'])){
				foreach($_REQUEST['feature'] as $feature){
					$featureKey = trim($feature);
					if( array_key_exists($featureKey, $item->optionsDir) ){
						if(
						   is_array($item->optionsDir[$featureKey]) &&
						   $item->optionsDir[$featureKey]['enable'] == 'enable'){
							//features are matching;
							$featuresMatching = true;
							
						}elseif($item->optionsDir[$featureKey] == 'Y'){
							//features are matching;
							$featuresMatching = true;							
						}
						
					}else{
						unset($items[$key]);
						$featuresMatching = false;
						break;
					}
				}
			}
		}
		
		if(!$featuresMatching){continue;}
		//--------------------------------------------------------------------
		if(
		   !empty($radius) &&
		   !empty($radius[1]) && //Lat
		   !empty($radius[2]) && //Long
		   !isPointInRadius($radius[0], $radius[1], $radius[2], $item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude'])){
			if(isset($_REQUEST['rasu'])){
			  echo "<pre>>>>>>>>>";
			  print_r($radius);
			  echo "<<<<<<<</pre>";
			}
			;unset($items[$key]);
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
		if($counter >= 20){
			unset($item->post_content);
			unset($item->post_date);
			unset($item->post_date_gmt);
			unset($item->post_status);
			unset($item->ping_status);
			unset($item->post_password);
			unset($item->to_ping);
			unset($item->post_modified);
			unset($item->post_modified_gmt);
			unset($item->post_content_filtered);
			unset($item->post_parent);
			unset($item->menu_order);
			unset($item->post_mime_type);
			//unset($item->excerptDir);
			unset($item->packageClass);
			//unset($item->comment_count);
			unset($item->post_content);
			unset($item->rating);
		}else{
			unset($item->post_modified_gmt);
			unset($item->post_content_filtered);
			unset($item->post_parent);
			unset($item->menu_order);
			unset($item->post_mime_type);
			unset($item->packageClass);
		}
		/*unset($item->post_author);
		unset($item->post_date);
		unset($item->post_date_gmt);
		unset($item->post_status);
		unset($item->ping_status);
		unset($item->post_password);
		unset($item->to_ping);
		unset($item->post_modified);
		unset($item->post_modified_gmt);
		unset($item->post_content_filtered);
		unset($item->post_parent);
		unset($item->menu_order);
		unset($item->post_mime_type);
		//unset($item->excerptDir);
		unset($item->packageClass);
		//unset($item->comment_count);
		unset($item->post_content);
                unset($item->rating);*/
	}
        
	if(isset($_REQUEST['test_query'])){
		$postIds = array();
		foreach($items as $key => $item){
			$postIds[] = $item->ID;
		}
		//echo count($items)." items are qualifying for features with ".implode(",",$postIds);//rasu
		// 	https://www.freerangecamping.com.au/directory/wp-admin/admin-ajax.php?action=get_items&category=0&location=0&feature=&search=national+park.&wpml_lang=en&test_query=1
		//clusterer is undefined
		//var ps = this.getProjection().fromLatLngToDivPixel(latLng); - gmap3.min.js
		//echo "<pre>";print_r($items);//rasu
		//die;
	}
        if(count($_REQUEST) == 0){
    $tempItems = array();
    $postID = get_the_ID ();
    $postType = get_post_type();
    foreach($items as $item){
        if($item-> ID == $postID){
           $tempItems[] = $item;
        }
        //echo "<pre>";print_r($item);echo "</pre>";
    }
    $items = $tempItems;
    //echo "<pre>";print_r($items);echo "</pre>";
    //echo count($_REQUEST);
}
	return $items;
}

// allow ajax
add_action( 'wp_ajax_get_items', 'getItemsAjax' );
add_action( 'wp_ajax_nopriv_get_items', 'getItemsAjax' );

function getItemsAjax() {
	//this function is  called only for ajax based requests
	global $aitThemeOptions;
	global $sitepress;

	// wpml 3.1.5+ hotfix
	// force to change language if ajax request
	if ($sitepress) {
		$sitepress->switch_lang($_POST['wpml_lang'], true);
	}

	// all items
	if(isset($_REQUEST['radius'])){
		$items = getItems($_REQUEST['category'],$_REQUEST['location'],$_REQUEST['search'],$_REQUEST['radius']);
	} else {
		$items = getItems($_REQUEST['category'],$_REQUEST['location'],$_REQUEST['search']);
	}

	foreach ($items as $item) {
		$item->timthumbUrl = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 120, 'h' => 160), "", "&amp;") : '';
		$item->timthumbUrlContent = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 100, 'h' => 100), "", "&amp;") : '';
		$item->wlm_address = "";
		if(
		   isset($item->optionsDir['wlm_city']) ||
		   isset($item->optionsDir['wlm_state']) ||
		   isset($item->optionsDir['wlm_zip'])
		){
			$address2 = array();
			if(!empty($item->optionsDir['wlm_city'])){
				$address2[] = $item->optionsDir['wlm_city'];
			}
			if(!empty($item->optionsDir['wlm_state'])){
				$address2[] = $item->optionsDir['wlm_state'];
			}
			if(!empty($item->optionsDir['wlm_zip'])){
				$address2[] = $item->optionsDir['wlm_zip'];
			}
			$item->wlm_address = implode(", ",$address2);			
		}
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
		if( in_array('directory_1', $user->roles) || in_array('directory_2', $user->roles) || in_array('directory_3', $user->roles) || in_array('directory_4', $user->roles) || in_array('directory_5', $user->roles) || in_array('directory_6', $user->roles) || in_array('directory_7', $user->roles) ) {
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
	//return;
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
		$users[6] = get_users(array('role'=>'directory_6'));
		$users[7] = get_users(array('role'=>'directory_7'));

		$output = "<select id='post_author_override' name='post_author_override' class=''>";
		foreach($users as $userGroup) {
			foreach ($userGroup as $user) {
				$selected = ($user->ID == intval($post->post_author)) ? " selected='selected'" : "";
				$output .= "<option".$selected." value='".$user->ID."'>".$user->user_login."</option>";
			}
		}
		$output .= "</select>";
	}
	echo "<!-- frcarray \n";print_r($post);echo "-->";
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
		$fromName = $_POST['name'];
		$fromEmail = $_POST['from'];
		$mailMessage = strip_tags($_POST['message']);
		$mailSubjectByUser = strip_tags($_POST['subject']);
		if(array_key_exists('item_title',$_POST) && !empty($_POST['item_title'])){
			$mailSubject = "You Have Received A Free Range Camping-(".$_POST['item_title'].")";
		}else{
			$mailSubject = $mailSubjectByUser;
		}
		$itemLink = $_POST['item_link'];
		$messageBody = <<<MessageBody
		Name: $fromName<br/><br/>
		Email: $fromEmail<br/><br/>
		<a href='$itemLink'>Click here to view the item</a><br/><br/>
		Subject: $mailSubjectByUser<br/><br/>
		Message: $mailMessage
MessageBody;

		$headers = 'From: "' . $fromName . '" <' . $fromEmail . '>' . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$result = wp_mail( strip_tags($_POST['to']), $mailSubject, $messageBody, $headers );
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
		'ep_integrate' => true, //elastic search
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
		'ep_integrate' => true, //elastic search
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
		'ep_integrate' => true, //elastic search
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
		'ep_integrate' => true, //elastic search
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