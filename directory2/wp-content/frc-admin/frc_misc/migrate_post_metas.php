<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
	
    $postmetaQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-dir-item' ORDER BY `post_id` DESC";
	//$postmetaQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-dir-item' AND `post_id` IN(19472) ORDER BY `post_id` DESC";
	//https://www.freerangecamping.co.nz/directory7/wp-content/frc_misc/migrate_post_metas.php
	//https://www.freerangecamping.co.nz/directory7/wp-admin/post.php?post=19472&action=edit
	/*
	Copy data step:
	mysqldump -u frcconz_camping -pnandini123 frcconz_plus | gzip > /home/frcconz/public_html/plus/wp-content/plus_19Feb.sql.gz
	
	Incase failuer happens:
	gunzip < /home/frcconz/public_html/plus/wp-content/plus_19Feb.sql.gz | mysql -u frcconz_camping -pnandini123 frcconz_plus
	
	dump taxonomy data:
	mysqldump -u freerang_rob -pnandini123 freerang_directory gwr_term_relationships gwr_term_taxonomy gwr_terms | gzip > /home/frcconz/public_html/plus/wp-content/taxonomy_19Feb.sql.gz
	copy taxonomies:
	gunzip < /home/frcconz/public_html/plus/wp-content/taxonomy_19Feb.sql.gz | mysql -u frcconz_camping -pnandini123 frcconz_plus
	
	Copy Posts:
	Step 1: SELECT * FROM `gwr_posts` WHERE `post_type` IN ('ait-dir-item', 'ait-rating', 'attachment'); //export from source site
	Step 2: Import above data
	Step 3: export post meta data
		    SELECT * FROM `gwr_postmeta` WHERE `meta_key` in('_ait_dir_gallery', '_ait_rating_ip', '_ait-dir-item', '_billing_address_1', '_billing_address_2', '_billing_city', '_billing_company', '_billing_country', '_billing_email', '_billing_first_name', '_billing_interval', '_billing_last_name', '_billing_period', '_billing_phone', '_billing_postcode' '_billing_state', '_edit_last', '_edit_lock', '_yoast_wpseo_content_score', '_yoast_wpseo_focuskw', '_yoast_wpseo_focuskw_text_input', '_yoast_wpseo_linkdex', '_yoast_wpseo_metadesc', '_yoast_wpseo_metakeywords', '_yoast_wpseo_opengraph-image', '_yoast_wpseo_opengraph-title', '_yoast_wpseo_primary_ait-dir-item-category', '_yoast_wpseo_primary_ait-dir-item-location', '_yoast_wpseo_primary_category', '_yoast_wpseo_primary_product_cat', '_yoast_wpseo_title','rating','rating_1','rating_2', 'rating_3', 'rating_4', 'rating_count', 'rating_full', 'rating_max', 'rating_mean', 'rating_mean_rounded', 'rating_rounded');
		    
	Copy Users:
	mysqldump -u freerang_rob -pnandini123 freerang_directory gwr_users_4_frc | gzip > /home/frcconz/public_html/plus/wp-content/gwr_users_19Feb.sql.gz
	mysqldump -u freerang_rob -pnandini123 freerang_directory gwr_usermeta_4_frc | gzip > /home/frcconz/public_html/plus/wp-content/gwr_usermeta_19Feb.sql.gz
	
	gunzip < /home/frcconz/public_html/plus/wp-content/gwr_users_19Feb.sql.gz | mysql -u frcconz_camping -pnandini123 frcconz_plus
	gunzip < /home/frcconz/public_html/plus/wp-content/gwr_usermeta_19Feb.sql.gz | mysql -u frcconz_camping -pnandini123 frcconz_plus
	freerangecamping.com.au/directory freerangecamping.co.nz/plus
	*/
	$result = $wpdb->get_results($postmetaQry);
    $mapAdddressArr = $finalArray = array();
	echo "<pre>";
    if(!empty($result)){
        foreach($result as $key => $postmeta){
            $postID = $postmeta->post_id;
            $metaID = $postmeta->meta_id;
            $itemData = unserialize($postmeta->meta_value);
			//echo'<pre>';print_r($itemData);die;
			
			//Block: Gallery
			$imageGalleryArr = array();
			$imgQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait_dir_gallery' AND `post_id` = $postID";
			$imgRS = $wpdb->get_results($imgQry);
			if(count($imgRS) >= 1){
				$imgCounter = 0;
				$imgRSArr = unserialize($imgRS[0]->meta_value);
				if(is_array($imgRSArr)){
					foreach($imgRSArr as $imgKey => $imgURL){
						if(!empty($imgURL)){
							$urlArr = explode("/", $imgURL);
							$imageName = end($urlArr);
							$imageGalleryArr[$imgCounter] = array('title' => $imageName, 'image' => $imgURL);
							$imgCounter++;
						}
					}
				}
			}
			$mapAdddressArr['gallery'] = array();
            $mapAdddressArr['displayGallery'] = '1';
            if(count($imageGalleryArr) >= 1){
                $mapAdddressArr['gallery'] = $imageGalleryArr;
            }
			//End Block: Gallery
            
			
			$mapAdddressArr['subtitle'] = "";
			$mapAdddressArr['featuredItem'] = 0;
			$mapAdddressArr['headerType'] = "map";
			$mapAdddressArr['headerImage'] = NULL;
			$mapAdddressArr['headerHeight'] = NULL;
			
			//Block: Map fields
			$mapAdddressArr['map']['address'] = "";
			$mapAdddressArr['map']['latitude'] = "";
			$mapAdddressArr['map']['longitude'] = "";
			$mapAdddressArr['map']['streetview'] = 0;
			$mapAdddressArr['map']['swheading'] = 90;
			$mapAdddressArr['map']['swpitch'] = 5;
			$mapAdddressArr['map']['swzoom'] = 0;
			if(is_array($itemData)){
				if(array_key_exists('address',$itemData)) $mapAdddressArr['map']['address'] = $itemData['address'];
				if(array_key_exists('gpsLatitude',$itemData)) $mapAdddressArr['map']['latitude'] = $itemData['gpsLatitude'];
				if(array_key_exists('gpsLongitude',$itemData)) $mapAdddressArr['map']['longitude'] = $itemData['gpsLongitude'];
				if(array_key_exists('showStreetview', $itemData)){
					if($itemData['showStreetview']['enable'] == 1){
						$mapAdddressArr['map']['streetview'] = 1;
					}
				}
				if(array_key_exists('streetViewHeading',$itemData)) $mapAdddressArr['map']['swheading'] = $itemData['streetViewHeading'];
				if(array_key_exists('streetViewPitch', $itemData)) $mapAdddressArr['map']['swpitch'] = $itemData['streetViewPitch'];
				if(array_key_exists('streetViewZoom', $itemData)) $mapAdddressArr['map']['swzoom'] = $itemData['streetViewZoom'];
			}
            //End Block: Map fields
			
			//Block: basic fields
            if(is_array($itemData) && array_key_exists('telephone', $itemData)){
                $mapAdddressArr['telephone'] = $itemData['telephone'];
            }
			$mapAdddressArr['telephoneAdditional'] = '';
            
            $mapAdddressArr['email'] = "";
            if(is_array($itemData) && array_key_exists('email',$itemData)){
                $mapAdddressArr['email'] = $itemData['email'];
            }
			
            $mapAdddressArr['showEmail'] = '1';
			
			$mapAdddressArr['police_check_required'] = 0;
            if(is_array($itemData) && array_key_exists('police_check_required',$itemData)){
                if(array_key_exists('enable', $itemData['police_check_required']) && $itemData['police_check_required']['enable'] == 'enable') $mapAdddressArr['police_check_required'] = 1;
            }
			
            $mapAdddressArr['contactOwnerBtn'] = 0;
            if(is_array($itemData) && array_key_exists('emailContactOwner',$itemData)){
                if(array_key_exists('enable', $itemData['emailContactOwner']) && $itemData['emailContactOwner']['enable'] == 'enable') $mapAdddressArr['contactOwnerBtn'] = 1;
            }
			
			$mapAdddressArr['webLinkLabel'] = '';
            if(is_array($itemData) && array_key_exists('web',$itemData)){ 
                $mapAdddressArr['web'] = $itemData['web'];
            }
            //End Block: basic fields
			
            //Block: Opening Hours
			$mapAdddressArr['openingHoursMonday'] = "";
            if(is_array($itemData) && array_key_exists('hoursMonday',$itemData)){
                $mapAdddressArr['openingHoursMonday'] = $itemData['hoursMonday'];
            }
			
            $mapAdddressArr['openingHoursTuesday'] = "";
            if(is_array($itemData) && array_key_exists('hoursTuesday',$itemData)){
                $mapAdddressArr['openingHoursTuesday'] = $itemData['hoursTuesday'];
            }
			
			$mapAdddressArr['openingHoursWednesday'] = "";
            if(is_array($itemData) && array_key_exists('hoursWednesday',$itemData)){
                $mapAdddressArr['openingHoursWednesday'] = $itemData['hoursWednesday'];
            }
			
			$mapAdddressArr['openingHoursThursday'] = "";
            if(is_array($itemData) && array_key_exists('hoursThursday',$itemData)){
                $mapAdddressArr['openingHoursThursday'] = $itemData['hoursThursday'];
            }
			
			$mapAdddressArr['openingHoursFriday'] = "";
            if(is_array($itemData) && array_key_exists('hoursFriday',$itemData)){
                $mapAdddressArr['openingHoursFriday'] = $itemData['hoursFriday'];
            }
			
			$mapAdddressArr['openingHoursSaturday'] = "";
            if(is_array($itemData) && array_key_exists('hoursSaturday',$itemData)){
                $mapAdddressArr['openingHoursSaturday'] = $itemData['hoursSaturday'];
            }
			
			$mapAdddressArr['openingHoursSunday'] = "";
            if(is_array($itemData) && array_key_exists('hoursSunday',$itemData)){
                $mapAdddressArr['openingHoursSunday'] = $itemData['hoursSunday'];
            }
			$mapAdddressArr['openingHoursNote'] = "";
			$mapAdddressArr['displayOpeningHours'] = 1;
			//End: Opening Hours
			
            //Block: Social Icons
            $mapAdddressArr['displaySocialIcons'] = '1';
			$mapAdddressArr['socialIconsOpenInNewWindow'] = '1';
            $mapAdddressArr['socialIcons'] = array();
			$mapAdddressArr['displayGallery'] = '1';
			//End Block: Social Icons
			
			//Block: Misc
			$mapAdddressArr['displayFeatures'] = 0;
			$mapAdddressArr['features'] = 0;
			
			$mapAdddressArr['wlm_city'] = "";
			if(is_array($itemData) && array_key_exists('wlm_city',$itemData)) $mapAdddressArr['wlm_city'] = $itemData['wlm_city'];
			
			$mapAdddressArr['wlm_state'] = "";
			if(is_array($itemData) && array_key_exists('wlm_state',$itemData)) $mapAdddressArr['wlm_state'] = $itemData['wlm_state'];
			
			$mapAdddressArr['wlm_zip'] = "";
			if(is_array($itemData) && array_key_exists('wlm_zip',$itemData)) $mapAdddressArr['wlm_zip'] = $itemData['wlm_zip'];
			
			$mapAdddressArr['property_type'] = "";
			if(is_array($itemData) && array_key_exists('property_type',$itemData)) $mapAdddressArr['property_type'] = $itemData['property_type'];
			
			$mapAdddressArr['gardening'] = "";
			if(is_array($itemData) && array_key_exists('gardening',$itemData)) $mapAdddressArr['gardening'] = $itemData['gardening'];
			
			$mapAdddressArr['other'] = "";
			if(is_array($itemData) && array_key_exists('other',$itemData)) $mapAdddressArr['other'] = $itemData['other'];
			
			$mapAdddressArr['location'] = "";
			if(is_array($itemData) && array_key_exists('location',$itemData)) $mapAdddressArr['location'] = $itemData['location'];
			
			$mapAdddressArr['access'] = "";
			if(is_array($itemData) && array_key_exists('access',$itemData)) $mapAdddressArr['access'] = $itemData['access'];
			
			$mapAdddressArr['responsible_authority'] = "";
			if(is_array($itemData) && array_key_exists('responsible_authority',$itemData)) $mapAdddressArr['responsible_authority'] = $itemData['responsible_authority'];
			
			//Start - discount
			$mapAdddressArr['discount'] = "off";
			if(is_array($itemData) && array_key_exists('discount',$itemData)){
                if(array_key_exists('enable', $itemData['discount']) && $itemData['discount']['enable'] == 'enable') $mapAdddressArr['discount'] = "on";
            }
			$mapAdddressArr['discount_detail'] = "";
			if(is_array($itemData) && array_key_exists('discount_detail',$itemData)) $mapAdddressArr['discount_detail'] = $itemData['discount_detail'];
			//End - discount
			
			//Start - offer
			$mapAdddressArr['offer'] = "off";
			if(is_array($itemData) && array_key_exists('offer', $itemData)){
                if(array_key_exists('enable', $itemData['offer']) && $itemData['offer']['enable'] == 'enable') $mapAdddressArr['offer'] = "on";
            }
			$mapAdddressArr['offer_detail'] = "";
			if(is_array($itemData) && array_key_exists('offer_detail', $itemData)) $mapAdddressArr['offer_detail'] = $itemData['offer_detail'];
			//End - offer
			
			//Start - seasonalRatesApply
			$mapAdddressArr['seasonalRatesApply'] = "off";
            if(is_array($itemData) && array_key_exists('seasonalRatesApply',$itemData)){
                if(array_key_exists('enable', $itemData['seasonalRatesApply']) && $itemData['seasonalRatesApply']['enable'] == 'enable') $mapAdddressArr['seasonalRatesApply'] = "on";
            }
			$mapAdddressArr['seasonalRatesApply_detail'] = "";
			if(is_array($itemData) && array_key_exists('seasonalRatesApply_detail',$itemData))
				$mapAdddressArr['seasonalRatesApply_detail'] = $itemData['seasonalRatesApply_detail'];
			//End - seasonalRatesApply
			
			//Start - limit_discount
			$mapAdddressArr['limit_discount'] = "off";
            if(is_array($itemData) && array_key_exists('limit_discount',$itemData)){
                if(array_key_exists('enable', $itemData['limit_discount']) &&  $itemData['limit_discount']['enable'] == 'enable') $mapAdddressArr['limit_discount'] = "on";
            }
			$mapAdddressArr['per_month_uses'] = "";
			if(is_array($itemData) && array_key_exists('per_month_uses', $itemData)) $mapAdddressArr['per_month_uses'] = $itemData['per_month_uses'];
			//End - limit_discount
			
			$mapAdddressArr['pricing_detail'] = "";
			if(is_array($itemData) && array_key_exists('pricing_detail', $itemData)) $mapAdddressArr['pricing_detail'] = $itemData['pricing_detail'];
			
			$mapAdddressArr['booking_link'] = "";
			if(is_array($itemData) && array_key_exists('booking_link', $itemData)) $mapAdddressArr['booking_link'] = $itemData['booking_link'];
			
			$mapAdddressArr['help_required'] = "";
			if(is_array($itemData) && array_key_exists('help_required', $itemData)) $mapAdddressArr['help_required'] = $itemData['help_required'];
			
			$mapAdddressArr['start_date'] = "";
			if(is_array($itemData) && array_key_exists('start_date', $itemData)) $mapAdddressArr['start_date'] = $itemData['start_date'];
			
			$mapAdddressArr['end_date'] = "";
			if(is_array($itemData) && array_key_exists('end_date', $itemData)) $mapAdddressArr['end_date'] = $itemData['end_date'];
			
			$mapAdddressArr['duration'] = "";
			if(is_array($itemData) && array_key_exists('duration', $itemData)) $mapAdddressArr['duration'] = $itemData['duration'];
			
			$mapAdddressArr['directoryType'] = "";
			if(is_array($itemData) && array_key_exists('directoryType', $itemData)) $mapAdddressArr['directoryType'] = $itemData['directoryType'];
			//End Block: Misc
			
            $mapAdddressArray = serialize($mapAdddressArr);
            //echo'<pre>';print_r($mapAdddressArr);die;
			//Block: AIT-Extension Features
			$finalArray['onoff'] = array(
											'2118905308599ff84247c456.33090579' => 'off', 	//1.	toiletes
											'188168178159a120ec1b4bb7.28131025' => 'off', 	//2.	showers
											'29615392559a128fb9dffa4.51040597' => 'off',	//3.	power
											'148620156659a12645e46256.51574107' => 'off', 	//4.	drinkingWater
											'603471443599ff84247c4e3.62317662' => 'off', 	//5.	waterFilling
											'16040096659a128fb9e0135.03414696' => 'off',	//6.	petsPermitted
											'190842153859a12645e46339.94888894' => 'off', 	//7.	waterNotSuitableForDrinking
											'107191184659a128fb9e0180.04901523' => 'off', 	//8.	noPetsPermitted
											'13328266659a128fb9e00e9.74175245' => 'off',	//9.	mobilePhoneReception
											'49519333159a128fb9e0098.57763366' => 'off', 	//10.	emergencyPhone
											
											'39367108959a127611e9c53.40468292' => 'off', 	//11.	rubbishBins
											'68136230859a127611e9d10.16193837' => 'off',	//12.	dumpPoint
											'147536215359a1200c7db353.16518800' => 'off',	//13.	disabledFacilities
											'193829933459a130c00000b2.70881197' => 'off', 	//14.	timeLimitsApply
											'160327751759a12cbd8e4eb2.99387079' => 'off', 	//15.	caravans
											'110690004459a12cbd8e4f47.26395654' => 'off',	//16.	campervans&Motorhomes
											'50860830159a12e4cc7c4e2.58547247' => 'off', 	//17.	bigRigAccess
											'116178928259a12a8077cf25.08496002' => 'off', 	//18.	suitableforTents
											'3896023559a12a8077cf93.09482080' => 'off',		//19.	notSuitableforTents
											'78130016459a12b5c80e4a0.15874597' => 'off', 	//20.	camperTrailers
											
											'115463372059a12b5c80e573.50842424' => 'off', 	//21.	noCamperTrailers
											'61155347359a12e4cc7c5c7.04852427' => 'off',	//22.	fSCVOnly
											'197977266559a143b902b807.38776684' => 'off',	//23.	generatorsPermitted
											'126593474259a1476c7a45f1.63218688' => 'off', 	//24.	generatorsNotPermitted
											'208574398559a1319dced256.54856460' => 'off', 	//25.	firesPermitted
											'157519193059a1319dced324.45146973' => 'off',	//26.	noFiresPermitted
											'174008921159a130c00000f7.06865568' => 'off', 	//27.	someShade
											'136297802759a1328cca7895.14816330' => 'off', 	//28.	shelter
											'23268569959a1328cca76e8.15076099' => 'off',	//29.	bBQ
											'50095449759a1319dced360.35719174' => 'off', 	//30.	picnicTable
											
											'40375140559a13cc4645443.47962524' => 'off', 	//31.	campKitchen
											'125526885259a13fc3432fe5.09981042' => 'off',	//32.	laundry
											'127875872459a1419114ba21.47959230' => 'off',	//33.	ensuiteSites
											'191709818959a1419114bb22.17156129' => 'off', 	//34.	restaurant
											'48343349759a143b902b316.86257805' => 'off', 	//35.	kiosk.png
											'119912264859a143b902b4b4.95201159' => 'off',	//36.	internetAccess
											'19149342359a143b902b549.18456158' => 'off', 	//37.	swimming
											'146237113459a143b902b5c9.59301660' => 'off', 	//38.	gamesRoom
											'65335996059a1397ac69e61.16868042' => 'off',	//39.	childrensPlayground
											'31863726159a1397ac6a005.03158568' => 'off', 	//40.	niceViewsFromLocation
											
											'139804642359a12e4cc7c613.03585852' => 'off', 	//41.	onSiteAccommodation
											'170509770559a12a8077cd57.63725733' => 'off',	//42.	4WDDriveAccessOnly
											'144274401759a12a8077ceb2.98916116' => 'off',	//43.	dryWeatherAccess
											'171017551359a130bff423e3.61654015' => 'off', 	//44.	rVTurnAroundArea
											'138295357859a130c0000034.04181816' => 'off', 	//45.	rVParking
											'75487067159a130c0000063.47568166' => 'off',	//46.	boatRampNearby
											'54328455559a1476c7a4901.74601924' => 'off', 	//47.	sharedWithTrucks
											'36497051859a1476c7a47a6.45322651' => 'off', 	//48.	showGrounds
											'23841355559a1476c7a4825.86443341' => 'off',	//49.	nationalParksandForestry
											'116373906659a1476c7a4897.15412247' => 'off', 	//50.	pubs
											
											'52677997159a13cc46452e3.37345362' => 'off', 	//51.	nationalParkFees
										 );
			$finalArray['text'] = array(
											'373584023599ff84247c4c5.56749002' => '', 	//1.	toiletes
											'124299123159a120ec1b4be3.98669372' => '', 	//2.	showers
											'21656720059a128fb9e0062.02008131' => '',	//3.	power
											'125877315659a12645e462f0.58533584' => '', 	//4.	drinkingWater
											'189004494559a11e68aeda99.02701281' => '', 	//5.	waterFilling
											'31379692359a128fb9e0151.31927562' => '',	//6.	petsPermitted
											'52292134859a12645e46355.93563211' => '', 	//7.	waterNotSuitableForDrinking
											'49968156359a128fb9e01a0.18166469' => '', 	//8.	noPetsPermitted
											'24856028859a128fb9e0113.38611591' => '',	//9.	mobilePhoneReception
											'1264394359a128fb9e00c7.64748028' => '', 	//10.	emergencyPhone
											
											'153686457459a127611e9ce9.57421471' => '', 	//11.	rubbishBins
											'28913861059a127611e9d32.89127897' => '',	//12.	dumpPoint
											'113984673159a120ec1b4b16.87775211' => '',	//13.	disabledFacilities
											'91325718659a130c00000d7.05560623' => '', 	//14.	timeLimitsApply
											'2571259859a12cbd8e4f05.91234818' => '', 	//15.	caravans
											'175843714459a12cbd8e4f81.95591183' => '',	//16.	campervans&Motorhomes
											'17003835859a12e4cc7c595.42517010' => '', 	//17.	bigRigAccess
											'175033111259a12a8077cf64.35811428' => '', 	//18.	suitableforTents
											'30743723259a12a8077cfd9.84645884' => '',	//19.	notSuitableforTents
											'55980863759a12b5c80e543.42060245' => '', 	//20.	camperTrailers
											
											'107243141459a12b5c80e591.55167459' => '', 	//21.	noCamperTrailers
											'5468162659a12e4cc7c5e7.29693645' => '',	//22.	fSCVOnly
											'184061923659a143b902b856.23529609' => '',	//23.	generatorsPermitted
											'199408683159a1476c7a4747.07506530' => '', 	//24.	generatorsNotPermitted
											'96932479159a1319dced2f4.48839598' => '', 	//25.	firesPermitted
											'92648312159a1319dced345.18145422' => '',	//26.	noFiresPermitted
											'90219130559a130c0000126.15606332' => '', 	//27.	someShade
											'5662200259a1328cca78d0.55880470' => '', 	//28.	shelter
											'155131766359a1328cca7837.02722797' => '',	//29.	bBQ
											'208048102859a1319dced394.92664794' => '', 	//30.	picnicTable
											
											'210513671559a13cc4645471.47839174' => '', 	//31.	campKitchen
											'166530906359a13fc3433119.63771490' => '',	//32.	laundry
											'166076809859a1419114bb04.88803687' => '',	//33.	ensuiteSites
											'182069099159a1419114bb56.47084308' => '', 	//34.	restaurant
											'104701077159a143b902b460.48418867' => '', 	//35.	kiosk.png
											'37626437259a143b902b500.08294143' => '',	//36.	internetAccess
											'150271062859a143b902b580.66852110' => '', 	//37.	swimming
											'73756936959a143b902b5f6.60751977' => '', 	//38.	gamesRoom
											'70870383959a1397ac69fb9.22200738' => '',	//39.	childrensPlayground
											'10736999859a1397ac6a056.62941562' => '', 	//40.	niceViewsFromLocation
											
											'104896684459a12e4cc7c631.61268353' => '', 	//41.	onSiteAccommodation
											'64647174859a12a8077ce50.23193234' => '',	//42.	4WDDriveAccessOnly
											'137602637859a12a8077cee7.04884000' => '',	//43.	dryWeatherAccess
											'124476508959a130c0000006.28420622' => '', 	//44.	rVTurnAroundArea
											'71110796459a130c0000042.82981721' => '', 	//45.	rVParking
											'86489403959a130c0000097.39746852' => '',	//46.	boatRampNearby
											'26705362759a1476c7a4941.39155723' => '', 	//47.	sharedWithTrucks
											'71006354559a1476c7a47e7.28910931' => '', 	//48.	showGrounds
											'145462151159a1476c7a4862.15834507' => '',	//49.	nationalParksandForestry
											'126351126659a1476c7a48d5.31555450' => '', 	//50.	pubs
											
											'163208053459a13cc46453f2.99439357' => '', 	//51.	nationalParkFees
										 );
			if(is_array($itemData)){
				//Feature 1 - toilets
				if(array_key_exists('toilets',$itemData)){
					if($itemData['toilets']['enable'] == 'enable' || strtolower($itemData['toilets']) == 'y'){
						$finalArray['onoff']['2118905308599ff84247c456.33090579'] = 'on';
					}
				}
				if(array_key_exists('toilets_addnl',$itemData) && !empty($itemData['toilets_addnl'])){
					$finalArray['text']['373584023599ff84247c4c5.56749002'] = $itemData['toilets_addnl'];
				}
				
				//Feature 2 - showers
				if(array_key_exists('showers',$itemData)){
					if($itemData['showers']['enable'] == 'enable' || strtolower($itemData['showers']) == 'y'){
						$finalArray['onoff']['188168178159a120ec1b4bb7.28131025'] = 'on';
					}
				}
				if(array_key_exists('showers_addnl', $itemData) && !empty($itemData['showers_addnl'])){
					$finalArray['text']['124299123159a120ec1b4be3.98669372'] = $itemData['showers_addnl'];
				}
				
				//Feature 3 - poweredSites
				if(array_key_exists('poweredSites',$itemData)){
					if($itemData['poweredSites']['enable'] == 'enable' || strtolower($itemData['poweredSites']) == 'y'){
						$finalArray['onoff']['29615392559a128fb9dffa4.51040597'] = 'on';
					}
				}
				if(array_key_exists('poweredSites_addnl',$itemData) && !empty($itemData['poweredSites_addnl'])){
					$finalArray['text']['21656720059a128fb9e0062.02008131'] = $itemData['poweredSites_addnl'];
				}
				
				//Feature 4 - drinkingWater
				if(array_key_exists('drinkingWater',$itemData)){
					if($itemData['drinkingWater']['enable'] == 'enable' || strtolower($itemData['drinkingWater']) == 'y'){
						$finalArray['onoff']['148620156659a12645e46256.51574107'] = 'on';
					}
				}
				if(array_key_exists('drinkingWater_addnl',$itemData) && !empty($itemData['drinkingWater_addnl'])){
					$finalArray['text']['125877315659a12645e462f0.58533584'] = $itemData['drinkingWater_addnl'];
				}
				
				//Feature 5 - waterFillingavailable
				if(array_key_exists('waterFillingavailable',$itemData)){
					if($itemData['waterFillingavailable']['enable'] == 'enable' || strtolower($itemData['waterFillingAvailable']) == 'y'){
						$finalArray['onoff']['603471443599ff84247c4e3.62317662'] = 'on';
					}
				}
				if(array_key_exists('waterFillingavailable_addnl',$itemData) && !empty($itemData['waterFillingavailable_addnl'])){
					$finalArray['text']['189004494559a11e68aeda99.02701281'] = $itemData['waterFillingavailable_addnl'];
				}
				
				//Feature 6 - petsOk
				if(array_key_exists('petsOk',$itemData)){
					if($itemData['petsOk']['enable'] == 'enable' || strtolower($itemData['petsOk']) == 'y'){
						$finalArray['onoff']['16040096659a128fb9e0135.03414696'] = 'on';
					}
				}
				if(array_key_exists('petsOk_addnl',$itemData) && !empty($itemData['petsOk_addnl'])){
						$finalArray['text']['31379692359a128fb9e0151.31927562'] = $itemData['petsOk_addnl'];
				}
				
				//Feature 7 - drinkingWaterNotSuitable
				if(array_key_exists('drinkingWaterNotSuitable',$itemData)){
					if($itemData['drinkingWaterNotSuitable']['enable'] == 'enable' || strtolower($itemData['drinkingWaterNotSuitable']) == 'y'){
						$finalArray['onoff']['190842153859a12645e46339.94888894'] = 'on';
					}
				}
				if(array_key_exists('drinkingWaterNotSuitable_addnl',$itemData) && !empty($itemData['drinkingWaterNotSuitable_addnl'])){
						$finalArray['text']['52292134859a12645e46355.93563211'] = $itemData['drinkingWaterNotSuitable_addnl'];
				}
				
				//Feature 8 - petsNotOk
				if(array_key_exists('petsNotOk',$itemData)){
					if($itemData['petsNotOk']['enable'] == 'enable' || strtolower($itemData['petsNotOk']) == 'y'){
						$finalArray['onoff']['107191184659a128fb9e0180.04901523'] = 'on';
					}
				}
				if(array_key_exists('petsNotOk_addnl',$itemData) && !empty($itemData['petsNotOk_addnl'])){
						$finalArray['text']['49968156359a128fb9e01a0.18166469'] = $itemData['petsNotOk_addnl'];
				}
				
				//Feature 9 - mobilePhoneReception
				if(array_key_exists('mobilePhoneReception',$itemData)){
					if($itemData['mobilePhoneReception']['enable'] == 'enable' || strtolower($itemData['mobilePhoneReception']) == 'y'){
						$finalArray['onoff']['13328266659a128fb9e00e9.74175245'] = 'on';
					}
				}
				if(array_key_exists('mobilePhoneReception_addnl',$itemData) && !empty($itemData['mobilePhoneReception_addnl'])){
					$finalArray['text']['24856028859a128fb9e0113.38611591'] = $itemData['mobilePhoneReception_addnl'];
				}
				
				//Feature 10 - emergencyPhone
				if(array_key_exists('emergencyPhone',$itemData)){
					if($itemData['emergencyPhone']['enable'] == 'enable' || strtolower($itemData['emergencyPhone']) == 'y'){
						$finalArray['onoff']['49519333159a128fb9e0098.57763366'] = 'on';
					}
				}
				if(array_key_exists('emergencyPhone_addnl',$itemData) && !empty($itemData['emergencyPhone_addnl'])){
						$finalArray['text']['1264394359a128fb9e00c7.64748028'] = $itemData['emergencyPhone_addnl'];
				}
				
				//Feature 11 - wasteFacilityAvailable
				if(array_key_exists('wasteFacilityAvailable',$itemData)){
					if($itemData['wasteFacilityAvailable']['enable'] == 'enable' || strtolower($itemData['wasteFacilityAvailable']) == 'y'){
						$finalArray['onoff']['39367108959a127611e9c53.40468292'] = 'on';
					}
				}
				if(array_key_exists('wasteFacilityAvailable_addnl',$itemData) && !empty($itemData['wasteFacilityAvailable_addnl'])){
					$finalArray['text']['153686457459a127611e9ce9.57421471'] = $itemData['wasteFacilityAvailable_addnl'];
				}
				
				//Feature 12 - dumpPointAvailable
				if(array_key_exists('dumpPointAvailable',$itemData)){
					if($itemData['dumpPointAvailable']['enable'] == 'enable' || strtolower($itemData['dumpPointAvailable']) == 'y'){
						$finalArray['onoff']['68136230859a127611e9d10.16193837'] = 'on';
					}
				}
				if(array_key_exists('dumpPointAvailable_addnl',$itemData) && !empty($itemData['dumpPointAvailable_addnl'])){
					$finalArray['text']['28913861059a127611e9d32.89127897'] = $itemData['dumpPointAvailable_addnl'];
				}
				
				//Feature 13 - disableFacility
				if(array_key_exists('disableFacility',$itemData)){
					if($itemData['disableFacility']['enable'] == 'enable' || strtolower($itemData['disableFacility']) == 'y'){
						$finalArray['onoff']['147536215359a1200c7db353.16518800'] = 'on';
					}
				}
				if(array_key_exists('disableFacility_addnl',$itemData) && !empty($itemData['disableFacility_addnl'])){
					$finalArray['text']['113984673159a120ec1b4b16.87775211'] = $itemData['disableFacility_addnl'];
				}
				
				//Feature 14 - timeLimitApplies
				if(array_key_exists('timeLimitApplies',$itemData)){
					if($itemData['timeLimitApplies']['enable'] == 'enable' || strtolower($itemData['timeLimitApplies']) == 'y'){
						$finalArray['onoff']['193829933459a130c00000b2.70881197'] = 'on';
					}
				}
				if(array_key_exists('timeLimitApplies_addnl',$itemData) && !empty($itemData['timeLimitApplies_addnl'])){
					$finalArray['text']['91325718659a130c00000d7.05560623'] = $itemData['timeLimitApplies_addnl'];
				}
				
				//Feature 15 - caravans
				if(array_key_exists('caravans',$itemData)){
					if($itemData['caravans']['enable'] == 'enable' || strtolower($itemData['caravans']) == 'y'){
						$finalArray['onoff']['160327751759a12cbd8e4eb2.99387079'] = 'on';
					}
				}
				if(array_key_exists('caravans_addnl',$itemData) && !empty($itemData['caravans_addnl'])){
					$finalArray['text']['2571259859a12cbd8e4f05.91234818'] = $itemData['caravans_addnl'];
				}
				
				//Feature 16 - largeSizeMotohomeAccess
				if(array_key_exists('largeSizeMotohomeAccess',$itemData)){
					if($itemData['largeSizeMotohomeAccess']['enable'] == 'enable' || strtolower($itemData['largeSizeMotohomeAccess']) == 'y'){
						$finalArray['onoff']['110690004459a12cbd8e4f47.26395654'] = 'on';
					}
				}
				if(array_key_exists('largeSizeMotohomeAccess_addnl',$itemData) & !empty($itemData['largeSizeMotohomeAccess_addnl'])){
					$finalArray['text']['175843714459a12cbd8e4f81.95591183'] = $itemData['largeSizeMotohomeAccess_addnl'];
				}
				
				//Feature 17 - bigrig
				if(array_key_exists('bigrig',$itemData)){
					if($itemData['bigrig']['enable'] == 'enable' || strtolower($itemData['bigrig']) == 'y'){
						$finalArray['onoff']['50860830159a12e4cc7c4e2.58547247'] = 'on';
					}   
				}
				if(array_key_exists('bigrig_addnl',$itemData) && !empty($itemData['bigrig_addnl'])){
					$finalArray['text']['17003835859a12e4cc7c595.42517010'] = $itemData['bigrig_addnl'];
				}
				
				//Feature 18 - tentsOnly
				if(array_key_exists('tentsOnly',$itemData)){
					if($itemData['tentsOnly']['enable'] == 'enable' || strtolower($itemData['tentsOnly']) == 'y'){
						$finalArray['onoff']['116178928259a12a8077cf25.08496002'] = 'on';
					}   
				}
				if(array_key_exists('tentsOnly_addnl',$itemData) && !empty($itemData['tentsOnly_addnl'])){
					$finalArray['text']['175033111259a12a8077cf64.35811428'] = $itemData['tentsOnly_addnl'];
				}
				
				//Feature 19 - noTents
				if(array_key_exists('noTents',$itemData)){
					if($itemData['noTents']['enable'] == 'enable' || strtolower($itemData['noTents']) == 'y'){
						$finalArray['onoff']['3896023559a12a8077cf93.09482080'] = 'on';
					}   
				}
				if(array_key_exists('noTents_addnl',$itemData) && !empty($itemData['noTents_addnl'])){
						$finalArray['text']['30743723259a12a8077cfd9.84645884'] = $itemData['noTents_addnl'];
				}
				
				//Feature 20 - camperTrailers
				if(array_key_exists('camperTrailers',$itemData)){
					if($itemData['camperTrailers']['enable'] == 'enable' || strtolower($itemData['camperTrailers']) == 'y'){
						$finalArray['onoff']['78130016459a12b5c80e4a0.15874597'] = 'on';
					}     
				}
				if(array_key_exists('camperTrailers_addnl',$itemData) && !empty($itemData['camperTrailers_addnl'])){
						$finalArray['text']['55980863759a12b5c80e543.42060245'] = $itemData['camperTrailers_addnl'];
				}
				
				//Feature 21 - noCamperTrailers
				if(array_key_exists('noCamperTrailers',$itemData)){
					if($itemData['noCamperTrailers']['enable'] == 'enable' || strtolower($itemData['noCamperTrailers']) == 'y'){
						$finalArray['onoff']['115463372059a12b5c80e573.50842424'] = 'on';
					}       
				}
				if(array_key_exists('noCamperTrailers_addnl',$itemData) && !empty($itemData['noCamperTrailers_addnl'])){
						$finalArray['text']['107243141459a12b5c80e591.55167459'] = $itemData['noCamperTrailers_addnl'];
				}
				
				//Feature 22 - selfContainedVehicles
				if(array_key_exists('selfContainedVehicles',$itemData)){
					if($itemData['selfContainedVehicles']['enable'] == 'enable' || strtolower($itemData['selfContainedVehicles']) == 'y'){
						$finalArray['onoff']['61155347359a12e4cc7c5c7.04852427'] = 'on';
					}
				}
				if(array_key_exists('selfContainedVehicles_addnl',$itemData) && !empty($itemData['selfContainedVehicles_addnl'])){
					$finalArray['text']['5468162659a12e4cc7c5e7.29693645'] = $itemData['selfContainedVehicles_addnl'];
				}
				
				//Feature 23 - generatorsPermitted
				if(array_key_exists('generatorsPermitted',$itemData)){
					if($itemData['generatorsPermitted']['enable'] == 'enable' || strtolower($itemData['generatorsPermitted']) == 'y'){
						$finalArray['onoff']['197977266559a143b902b807.38776684'] = 'on';
					}       
				}
				if(array_key_exists('generatorsPermitted_addnl',$itemData) && !empty($itemData['generatorsPermitted_addnl'])){
					$finalArray['text']['184061923659a143b902b856.23529609'] = $itemData['generatorsPermitted_addnl'];
				}
				
				//Feature 24 - generatorsNotPermitted
				if(array_key_exists('generatorsNotPermitted',$itemData)){
					if($itemData['generatorsNotPermitted']['enable'] == 'enable' || strtolower($itemData['generatorsNotPermitted']) == 'y'){
						$finalArray['onoff']['126593474259a1476c7a45f1.63218688'] = 'on';
					}
				}
				if(array_key_exists('generatorsNotPermitted_addnl',$itemData) && !empty($itemData['generatorsNotPermitted_addnl'])){
					$finalArray['text']['199408683159a1476c7a4747.07506530'] = $itemData['generatorsNotPermitted_addnl'];
				}
				
				//Feature 25 - firePit
				if(array_key_exists('firePit',$itemData)){
					if($itemData['firePit']['enable'] == 'enable' || strtolower($itemData['firePit']) == 'y'){
						$finalArray['onoff']['208574398559a1319dced256.54856460'] = 'on';
					}
				}
				if(array_key_exists('firePit_addnl',$itemData) && !empty($itemData['firePit_addnl'])){
						$finalArray['text']['96932479159a1319dced2f4.48839598'] = $itemData['firePit_addnl'];
				}
				
				//Feature 26 - noFirePit
				if(array_key_exists('noFirePit',$itemData)){
					if($itemData['noFirePit']['enable'] == 'enable' || strtolower($itemData['noFirePit']) == 'y'){
						$finalArray['onoff']['157519193059a1319dced324.45146973'] = 'on';
					}
				}
				if(array_key_exists('noFirePit_addnl',$itemData) && !empty($itemData['noFirePit_addnl'])){
					$finalArray['text']['92648312159a1319dced345.18145422'] = $itemData['noFirePit_addnl'];
				}
				
				//Feature 27 - shadedSites
				if(array_key_exists('shadedSites',$itemData) || strtolower($itemData['shadedSites']) == 'y'){
					if($itemData['shadedSites']['enable'] == 'enable'){
						$finalArray['onoff']['174008921159a130c00000f7.06865568'] = 'on';
					} 
				}
				if(array_key_exists('shadedSites_addnl',$itemData) && !empty($itemData['shadedSites_addnl'])){
					$finalArray['text']['90219130559a130c0000126.15606332'] = $itemData['shadedSites_addnl'];
				}
				
				//Feature 28 - shelter
				if(array_key_exists('shelter',$itemData)){
					if($itemData['shelter']['enable'] == 'enable' || strtolower($itemData['shelter']) == 'y'){
						$finalArray['onoff']['136297802759a1328cca7895.14816330'] = 'on';
					}     
				}
				if(array_key_exists('shelter_addnl',$itemData) && !empty($itemData['shelter_addnl'])){
					$finalArray['text']['5662200259a1328cca78d0.55880470'] = $itemData['shelter_addnl'];
				}
				
				//Feature 29 - bbq
				if(array_key_exists('bbq',$itemData)){
					if($itemData['bbq']['enable'] == 'enable'){
						$finalArray['onoff']['23268569959a1328cca76e8.15076099'] = 'on';
					}
				}
				if(array_key_exists('bbq_addnl',$itemData) && !empty($itemData['bbq_addnl'])){
					$finalArray['text']['155131766359a1328cca7837.02722797'] = $itemData['bbq_addnl'];
				}
				
				//Feature 30 - picnicTable
				if(array_key_exists('picnicTable',$itemData)){
					if($itemData['picnicTable']['enable'] == 'enable'){
						$finalArray['onoff']['50095449759a1319dced360.35719174'] = 'on';
					}     
				}
				if(array_key_exists('picnicTable_addnl',$itemData) && !empty($itemData['picnicTable_addnl'])){
					$finalArray['text']['208048102859a1319dced394.92664794'] = $itemData['picnicTable_addnl'];
				}
				
				//Feature 31 - campKitchen
				if(array_key_exists('campKitchen',$itemData)){
					if($itemData['campKitchen']['enable'] == 'enable' || strtolower($itemData['campKitchen']) == 'y'){
						$finalArray['onoff']['40375140559a13cc4645443.47962524'] = 'on';
					}    
				}
				if(array_key_exists('campKitchen_addnl',$itemData) && !empty($itemData['campKitchen_addnl'])){
					$finalArray['text']['210513671559a13cc4645471.47839174'] = $itemData['campKitchen_addnl'];
				}
				
				//Feature 32 - laundry
				if(array_key_exists('laundry',$itemData)){
					if($itemData['laundry']['enable'] == 'enable' || strtolower($itemData['laundry']) == 'y'){
						$finalArray['onoff']['125526885259a13fc3432fe5.09981042'] = 'on';
					}    
				}
				if(array_key_exists('laundry_addnl',$itemData) && !empty($itemData['laundry_addnl'])){
					$finalArray['text']['166530906359a13fc3433119.63771490'] = $itemData['laundry_addnl'];
				}
				
				//Feature 33 - ensuiteSites
				if(array_key_exists('ensuiteSites',$itemData)){
					if($itemData['ensuiteSites']['enable'] == 'enable' || strtolower($itemData['ensuiteSites']) == 'y'){
						$finalArray['onoff']['127875872459a1419114ba21.47959230'] = 'on';
					}      
				}
				if(array_key_exists('ensuiteSites_addnl',$itemData) && !empty($itemData['ensuiteSites_addnl'])){
					$finalArray['text']['166076809859a1419114bb04.88803687'] = $itemData['ensuiteSites_addnl'];
				}
				
				//Feature 34 - restaurant
				if(array_key_exists('restaurant',$itemData)){
					if($itemData['restaurant']['enable'] == 'enable' || strtolower($itemData['restaurant']) == 'y'){
						$finalArray['onoff']['191709818959a1419114bb22.17156129'] = 'on';
					}   
				};
				if(array_key_exists('restaurant_addnl',$itemData) && !empty($itemData['restaurant_addnl'])){
					$finalArray['text']['182069099159a1419114bb56.47084308'] = $itemData['restaurant_addnl'];
				}
				
				//Feature 35 - kiosk
				if(array_key_exists('kiosk',$itemData)){
					if($itemData['kiosk']['enable'] == 'enable' || strtolower($itemData['kiosk']) == 'y'){
						$finalArray['onoff']['48343349759a143b902b316.86257805'] = 'on';
					} 
				}
				if(array_key_exists('kiosk_addnl',$itemData) && !empty($itemData['kiosk_addnl'])){
					$finalArray['text']['104701077159a143b902b460.48418867'] = $itemData['kiosk_addnl'];
				}
				
				//Feature 36 - internetAccess
				if(array_key_exists('internetAccess',$itemData)){
					if($itemData['internetAccess']['enable'] == 'enable' || strtolower($itemData['internetAccess']) == 'y'){
						$finalArray['onoff']['119912264859a143b902b4b4.95201159'] = 'on';
					}  
				}
				if(array_key_exists('internetAccess_addnl',$itemData) && !empty($itemData['internetAccess_addnl'])){
					$finalArray['text']['37626437259a143b902b500.08294143'] = $itemData['internetAccess_addnl'];
				}
				
				//Feature 37 - swimmingPool
				if(array_key_exists('swimmingPool',$itemData)){
					if($itemData['swimmingPool']['enable'] == 'enable' || strtolower($itemData['swimmingPool']) == 'y'){
						$finalArray['onoff']['19149342359a143b902b549.18456158'] = 'on';
					}    
				}
				if(array_key_exists('swimmingPool_addnl',$itemData) && !empty($itemData['swimmingPool_addnl'])){
					$finalArray['text']['150271062859a143b902b580.66852110'] = $itemData['swimmingPool_addnl'];
				}
				
				//Feature 38 - gamesRoom
				if(array_key_exists('gamesRoom',$itemData)){
					if($itemData['gamesRoom']['enable'] == 'enable' || strtolower($itemData['gamesRoom']) == 'y'){
						$finalArray['onoff']['146237113459a143b902b5c9.59301660'] = 'on';
					}     
				}
				if(array_key_exists('gamesRoom_addnl',$itemData) && !empty($itemData['gamesRoom_addnl'])){
					$finalArray['text']['73756936959a143b902b5f6.60751977'] = $itemData['gamesRoom_addnl'];
				}
				
				//Feature 39 - childrenPlayground
				if(array_key_exists('childrenPlayground',$itemData)){
					if($itemData['childrenPlayground']['enable'] == 'enable' || strtolower($itemData['childrenPlayground']) == 'y'){
						$finalArray['onoff']['65335996059a1397ac69e61.16868042'] = 'on';
					}    
				}
				if(array_key_exists('childrenPlayground_addnl',$itemData) && !empty($itemData['childrenPlayground_addnl'])){
					$finalArray['text']['70870383959a1397ac69fb9.22200738'] = $itemData['childrenPlayground_addnl'];
				}
				
				//Feature 40 - views
				if(array_key_exists('views',$itemData)){
					if($itemData['views']['enable'] == 'enable' || strtolower($itemData['views']) == 'y'){
						$finalArray['onoff']['31863726159a1397ac6a005.03158568'] = 'on';
					}      
				}
				if(array_key_exists('views_addnl',$itemData) && !empty($itemData['views_addnl'])){
					$finalArray['text']['10736999859a1397ac6a056.62941562'] = $itemData['views_addnl'];
				}
				
				//Feature 41 - onSiteAccomodation
				if(array_key_exists('onSiteAccomodation',$itemData)){
					if($itemData['onSiteAccomodation']['enable'] == 'enable' || strtolower($itemData['onSiteAccomodation']) == 'y'){
						$finalArray['onoff']['139804642359a12e4cc7c613.03585852'] = 'on';
					}       
				} 
				if(array_key_exists('onSiteAccomodation_addnl',$itemData) && !empty($itemData['onSiteAccomodation_addnl'])){//wrongly used of nice view
					$finalArray['text']['104896684459a12e4cc7c631.61268353'] = $itemData['onSiteAccomodation_addnl'];
				}
				
				//Feature 42 - fourWheelDrive
				if(array_key_exists('fourWheelDrive',$itemData)){
					if($itemData['fourWheelDrive']['enable'] == 'enable' || strtolower($itemData['fourWheelDrive']) == 'y'){
						$finalArray['onoff']['170509770559a12a8077cd57.63725733'] = 'on';
					}     
				}
				if(array_key_exists('fourWheelDrive_addnl',$itemData) && !empty($itemData['fourWheelDrive_addnl'])){
					$finalArray['text']['64647174859a12a8077ce50.23193234'] = $itemData['fourWheelDrive_addnl'];
				}
				
				//Feature 43 - dryWeatherOnlyAccess
				if(array_key_exists('dryWeatherOnlyAccess',$itemData)){
					if($itemData['dryWeatherOnlyAccess']['enable'] == 'enable' || strtolower($itemData['dryWeatherOnlyAccess']) == 'y'){
						$finalArray['onoff']['144274401759a12a8077ceb2.98916116'] = 'on';
					}     
				}
				if(array_key_exists('dryWeatherOnlyAccess_addnl',$itemData) && !empty($itemData['dryWeatherOnlyAccess_addnl'])){
					$finalArray['text']['137602637859a12a8077cee7.04884000'] = $itemData['dryWeatherOnlyAccess_addnl'];
				}
				
				//Feature 44 - rvTurnAroundArea
				if(array_key_exists('rvTurnAroundArea',$itemData)){
					if($itemData['rvTurnAroundArea']['enable'] == 'enable' || strtolower($itemData['rvTurnAroundArea']) == 'y'){
						$finalArray['onoff']['171017551359a130bff423e3.61654015'] = 'on';
					}     
				}
				if(array_key_exists('rvTurnAroundArea_addnl',$itemData) && !empty($itemData['rvTurnAroundArea_addnl'])){
					$finalArray['text']['124476508959a130c0000006.28420622'] = $itemData['rvTurnAroundArea_addnl'];
				}
				
				//Feature 45 - rvParkingAvailable
				if(array_key_exists('rvParkingAvailable',$itemData)){
					if($itemData['rvParkingAvailable']['enable'] == 'enable' || strtolower($itemData['rvParkingAvailable']) == 'y'){
						$finalArray['onoff']['138295357859a130c0000034.04181816'] = 'on';
					}     
				}
				if(array_key_exists('rvParkingAvailable_addnl',$itemData)){
					$finalArray['text']['71110796459a130c0000042.82981721'] = $itemData['rvParkingAvailable_addnl'];
				}
				
				//Feature 46 - boatRamp
				if(array_key_exists('boatRamp',$itemData)){
					if($itemData['boatRamp']['enable'] == 'enable' || strtolower($itemData['boatRamp']) == 'y'){
						$finalArray['onoff']['75487067159a130c0000063.47568166'] = 'on';
					}    
				}
				if(array_key_exists('boatRamp_addnl',$itemData)){
					$finalArray['text']['86489403959a130c0000097.39746852'] = $itemData['boatRamp_addnl'];
				}
				
				//Feature 47 - sharedWithTrucks
				if(array_key_exists('sharedWithTrucks',$itemData) || strtolower($itemData['sharedWithTrucks']) == 'y'){
					if($itemData['sharedWithTrucks']['enable'] == 'enable'){
						$finalArray['onoff']['54328455559a1476c7a4901.74601924'] = 'on';
					}    
				}
				if(array_key_exists('sharedWithTrucks_addnl',$itemData)){
					$finalArray['text']['26705362759a1476c7a4941.39155723'] = $itemData['sharedWithTrucks_addnl'];
				}
				
				//Feature 48 - showGrounds
				if(array_key_exists('showGrounds',$itemData)){
					if($itemData['showGrounds']['enable'] == 'enable' || strtolower($itemData['showGrounds']) == 'y'){
						$finalArray['onoff']['36497051859a1476c7a47a6.45322651'] = 'on';
					}
				}
				if(array_key_exists('showGrounds_addnl',$itemData)){
					$finalArray['text']['71006354559a1476c7a47e7.28910931'] = $itemData['showGrounds_addnl'];
				}
				
				//Feature 49 - nationalParknForestry
				if(array_key_exists('nationalParknForestry',$itemData)){
					if($itemData['nationalParknForestry']['enable'] == 'enable' || strtolower($itemData['nationalParknForestry']) == 'y'){
						$finalArray['onoff']['23841355559a1476c7a4825.86443341'] = 'on';
					}
				}
				if(array_key_exists('nationalParknForestry_addnl',$itemData)){
					$finalArray['text']['145462151159a1476c7a4862.15834507'] = $itemData['nationalParknForestry_addnl'];
				}
				
				//Feature 50 - nationalParknForestry
				if(array_key_exists('pubs',$itemData) || strtolower($itemData['pubs']) == 'y'){
					if($itemData['pubs']['enable'] == 'enable'){
						$finalArray['onoff']['116373906659a1476c7a4897.15412247'] = 'on';
					}    
				}
				if(array_key_exists('pubs_addnl',$itemData)){
					$finalArray['text']['126351126659a1476c7a48d5.31555450'] = $itemData['pubs_addnl'];
				}
				
				//Feature 51 - parkFees
				if(array_key_exists('parkFees',$itemData)){
					if($itemData['parkFees']['enable'] == 'enable'){
						$finalArray['onoff']['52677997159a13cc46452e3.37345362'] = 'on';
					}
				}
				if(array_key_exists('parkFees_addnl',$itemData)){
					$finalArray['text']['163208053459a13cc46453f2.99439357'] = $itemData['parkFees_addnl'];
				}
			}
            //print_r($mapAdddressArray);print_r($mapAdddressArr);print_r($finalArray);
			//echo "\n<br/>--------------------------<br/>\n";continue;
            $finalArr = serialize($finalArray);
			
			//1.	_ait-item_item-author = a:1:{s:6:"author";s:5:"71379";}
			$postAuthor = 0;
			$authQry = "SELECT `post_author` FROM `gwr_posts` WHERE `ID` = $postID";
			$authRes = $wpdb->get_results($authQry);
			if(count($authRes) >= 1){
				$postAuthor = $authRes[0]->post_author;
			}
			if($postAuthor){
				$authQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-item_item-author' AND `post_id` = $postID";
				$authRes = $wpdb->get_results($authQry);
				if(count($authRes) >= 1){
					$authUpdated = $wpdb->update( 'gwr_postmeta',
									 array('meta_value' => serialize(array('author' => $postAuthor))),
									 array('meta_key' => '_ait-item_item-author', 'post_id' => $postID) );		
				}else{
					$wpdb->insert('gwr_postmeta',array(
															'post_id' => $postID,
															'meta_key' => '_ait-item_item-author',
															'meta_value' => serialize(array('author' => $postAuthor))
															)
								  );
				}
			}
			echo "<br/><br/>1. author qry".$wpdb->last_query;
			
			//2.	subtitle
			$subTitleQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='subtitle' AND `post_id` = $postID";
			$subTitleRes = $wpdb->get_results($subTitleQry);
			if(count($subTitleRes) >= 1){
				$titleUpdated = $wpdb->update( 'gwr_postmeta',
								 array('meta_value' => ""),
								 array('meta_key' => 'subtitle', 'post_id' => $postID) );
			}else{
				$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => 'subtitle',
														'meta_value' => ""
														)
							  );
			}
			echo "<br/>2. Title qry".$wpdb->last_query;
			
			//3.	_ait-item_item-featured = 1 (Main culprit)
			//dir_featured = yes in old theme or _ait-dir-item featured['enable'] = enable
			$featuredQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-item_item-featured' AND `post_id` = $postID";
			$featRes = $wpdb->get_results($featuredQry);
			if(count($featRes) >= 1){
				$featureUpdated = $wpdb->update( 'gwr_postmeta',
								 array('meta_value' => 0),
								 array('meta_key' => '_ait-item_item-featured', 'post_id' => $postID) );
			}else{
				$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => '_ait-item_item-featured',
														'meta_value' => "0"
														)
							  );
			}
			echo "<br/>3. Featured qry".$wpdb->last_query;
			
			//4.	_ait-item_item-data
			$itemMetaQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-item_item-data' AND `post_id` = $postID";
			$itemMetaRes = $wpdb->get_results($itemMetaQry);
			if(count($itemMetaRes) >= 1){
				$itemDataUpdated = $wpdb->update( 'gwr_postmeta',
								 array('meta_value' => serialize($mapAdddressArr)),
								 array('meta_key' => '_ait-item_item-data', 'post_id' => $postID) );		//echo "<br/>".$wpdb->last_query;
			}else{
				$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => '_ait-item_item-data',
														'meta_value' => serialize($mapAdddressArr)
														)
							  );
			}
			echo "<br/>4. Item Data qry".$wpdb->last_query;
			
			//5.	slide_template = default
			$templateQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='slide_template' AND `post_id` = $postID";
			$templateRes = $wpdb->get_results($templateQry);
			if(count($templateRes) >= 1){
				$tempUpdated = $wpdb->update( 'gwr_postmeta',
								 array('meta_value' => 'default'),
								 array('meta_key' => 'slide_template', 'post_id' => $postID) );		
			}else{
				$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => 'slide_template',
														'meta_value' => 'default'
														)
							  );
			}
			echo "<br/>5. $templateQry:".$wpdb->last_query;
			
			//6.	_ait-item_item-extension
			$extQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='_ait-item_item-extension' AND `post_id` = $postID";
			$extRes = $wpdb->get_results($extQry);
			if(count($extRes) >= 1){
				$extUpdated = $wpdb->update( 'gwr_postmeta',
								 array('meta_value' => serialize($finalArray)),
								 array('meta_key' => '_ait-item_item-extension', 'post_id' => $postID) );
			}else{
				$wpdb->insert('gwr_postmeta',array(
                                                    'post_id' => $postID,
                                                    'meta_key' => '_ait-item_item-extension',
                                                    'meta_value' => $finalArr
                                                    )
                          );
				
			}
			echo "<br/>6. $extQry <br/>6.".$wpdb->last_query;
			$longQry = $latQry = "";
			if(!empty($mapAdddressArr['map']['latitude']) && !empty ($mapAdddressArr['map']['longitude'])){
				//7.	ait-longitude
				$longQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='ait-longitude' AND `post_id` = $postID";
				$longRes = $wpdb->get_results($longQry);
				if(count($longRes) >= 1){
					$longUpdated = $wpdb->update( 'gwr_postmeta',
									 array('meta_value' => $mapAdddressArr['map']['longitude']),
									 array('meta_key' => 'ait-longitude', 'post_id' => $postID) );
				}else{
					$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => 'ait-longitude',
														'meta_value' => $mapAdddressArr['map']['longitude']
														)
							  );
					
				}
				echo "<br/>7. $longQry <br/>7.".$wpdb->last_query;
				
				//8.	ait-latitude
				$latQry = "SELECT * FROM `gwr_postmeta` WHERE `meta_key` ='ait-latitude' AND `post_id` = $postID";
				$latRes = $wpdb->get_results($latQry);
				if(count($latRes) >= 1){
					$latUpdated = $wpdb->update( 'gwr_postmeta',
									 array('meta_value' => $mapAdddressArr['map']['latitude']),
									 array('meta_key' => 'ait-latitude', 'post_id' => $postID) );
				}else{
					$wpdb->insert('gwr_postmeta',array(
														'post_id' => $postID,
														'meta_key' => 'ait-latitude',
														'meta_value' => $mapAdddressArr['map']['latitude']
														)
							  );
					
				}
				echo "<br/>8. $latQry <br/>8.".$wpdb->last_query;
			}
			//die("---------------------");
			//echo "<br/>Latitude Qry".$wpdb->last_query;
			//9.	_edit_last
			//10.	_edit_lock
			//echo "<br/>Extension Qry".$wpdb->last_query;
			
			//echo "<br/>Post Meta Qry".$wpdb->last_query;
        }
        
    }
    //echo'<pre>';print_r($itemData);die;
	//edit_ait-dir-item => ait_toolkit_items_edit_item
?>