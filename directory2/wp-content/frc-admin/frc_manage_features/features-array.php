<?php
    if(!function_exists('isOldTheme')){
		function isOldTheme(){
			$return = array();
			$args = array('public' => true);
			$post_types = get_post_types($args);
			foreach( $post_types as $post_type ){
				$taxonomies = get_object_taxonomies( $post_type );
				if (!empty($taxonomies)) {
					$return[$post_type] = $taxonomies;
				}
			}
			$oldTheme = false;
			if(is_array($return) && array_key_exists('ait-dir-item', $return)){
				 $oldTheme = true;
			}
			return $oldTheme;
	   }
	}
    
    $features = array(
              array(
                    'toilets',
                    'Toilets Available',
                    'toilets.png',
                    '',
                    ''
                    ),  //1
              array(
                    'disableFacility',
                    'Disabled Facility',
                    'disableFacility.png',
                    '',
                    '',
                    ),  //2
              array(
                    'showers',
                    'Showers Available',
                    'showers.png',
                    '',
                    '',
                    ),  //3
              array(
                    'drinkingWater',
                    'Drinking Water Available',
                    'drinkingWater.png',
                    '',
                    '',
                    ),    //4
              array(
                    'drinkingWaterNotSuitable',
                    'Water Not Suitable For Drinking',
                    'drinkingWaterNotSuitable.png',
                    '',
                    '',
                    ),  //5
              array(
                    'waterFillingAvailable',
                    'Water Filling Available',
                    'waterFillingAvailable.png',
                    '',
                    '',
                    ),  //6                   
              array(
                    'wasteFacilityAvailable',
                    'Rubbish Bins Available',
                    'wasteFacilityAvailable.png',
                    '',
                    '',
                    ),  //7                          
              array(
                    'dumpPointAvailable',
                    'Dump Point Available',
                    'dumpPointAvailable.png',
                    '',
                    '',
                    ),  //8
              array(
                    'poweredSites',
                    'Power Available',
                    'poweredSites.png',
                    '',
                    '',
                    ),  //9
              array(
                    'emergencyPhone',
                    'Emergency Phone',
                    'emergencyPhone.png',
                    '',
                    '',
                    ),  //10
              array(
                    'mobilePhoneReception',
                    'Mobile Phone Reception',
                    'mobilePhoneReception.png',
                    '',
                    '',
                    ),  //11
              array(
                    'petsOk',
                    'Pets Permitted',
                    'petsOk.png',
                    '',
                    '',
                    ),  //12
              array(
                    'petsNotOk',
                    'No Pets Permitted',
                    'petsNotOk.png',
                    '',
                    '',
                    ),  //13
              array(
                    'fourWheelDrive',
                    '4WD Drive Access Only',
                    'fourWheelDrive.png',
                    '',
                    '',
                    ),  //14                   
              array(
                    'dryWeatherOnlyAccess',
                    'Dry Weather Access',
                    'dryWeatherOnlyAccess.png',
                    '',
                    '',
                    ),  //15
              array(
                    'tentsOnly',
                    'Suitable for Tents',
                    'tentsOnly.png',
                    '',
                    '',
                    ),  //16
              array(
                    'noTents',
                    'Not Suitable for Tents',
                    'noTents.png',
                    '',
                    '',
                    ),  //17
              array(
                    'camperTrailers',
                    'Camper Trailers',
                    'camperTrailers.png',
                    '',
                    ''
                    ),  //18
              array(
                    'noCamperTrailers',
                    'No Camper Trailers',
                    'noCamperTrailers.png',
                    '',
                    '',
                    ),  //19
              array(
                    'caravans',
                    'Caravans',
                    'caravans.png',
                    '',
                    '',
                    ),  //20
              array(
                    'largeSizeMotohomeAccess',
                    'Campervans  & Motorhomes',
                    'largeSizeMotohomeAccess.png',
                    '',
                    '',
                    ),  //21
              array(
                    'bigrig',
                    'Big Rig Access',
                    'bigrig.png',
                    '',
                    '',
                    ), //22
              array(
                    'selfContainedVehicles',
                    'Fully Self Contained Vehicles Only',
                    'selfContainedVehicles.png',
                    '',
                    '',
                    ),  //23
              array(
                    'onSiteAccomodation',
                    'On Site Accommodation',
                    'onSiteAccomodation.png',
                    '',
                    '',
                    ),  //24
              array('seasonalRatesApply','Seasonal Rates Apply','seasonalRatesApply.png', '',''),                          
              array('discount','Discounts Apply','discount.png','',''),                   
              array('offer','Offers Apply','offer.png', '',''),
              array(
                    'rvTurnAroundArea',
                    'RV Turn Around Area',
                    'rvTurnAroundArea.png',
                    '',
                    ''
                    ),  //28
              array(
                    'rvParkingAvailable',
                    'RV Parking Available',
                    'rvParkingAvailable.png',
                    '',
                    '',
                    ),  //29
              array(
                    'boatRamp',
                    'Boat Ramp Nearby',
                    'boatRamp.png',
                    '',
                    '',
                    ),  //30
              array(
                    'timeLimitApplies',
                    'Time Limits Apply',
                    'timeLimitApplies.png',
                    '',
                    '',
                    ),  //31
              array(
                    'shadedSites',
                    'Some Shade Available',
                    'shadedSites.png',
                    '',
                    '',
                    ),  //32
              array(
                    'firePit',
                    'Fires Permitted',
                    'firePit.png',
                    '',
                    '',
                    ),  //33
              array(
                    'noFirePit',
                    'No Fires Permitted',
                    'noFirePit.png',
                    '',
                    '',
                    ),  //34
              array(
                    'picnicTable',
                    'Picnic Table Available',
                    'picnicTable.png',
                    '',
                    '',
                    ),  //35
              array(
                    'bbq',
                    'BBQ Available',
                    'bbq.png',
                    '',
                    '',
                    ),  //36
              array(
                    'shelter',
                    'Shelter Available',
                    'shelter.png',
                    '',
                    '',
                    ),  //37
              array(
                    'childrenPlayground',
                    'Childrens Playground Available',
                    'childrenPlayground.png',
                    '',
                    '',
                    ),  //38
              array(
                    'views',
                    'Nice Views From Location',
                    'views.png',
                    '',
                    '',
                    ),  //39
              array(
                    'parkFees',
                    'National Park Fees',
                    'parkFees.png',
                    '',
                    '',
                    ),  //40
              array(
                    'campKitchen',
                    'Camp Kitchen',
                    'campKitchen.png',
                    '',
                    '',
                    ),  //41
              array(
                    'laundry',
                    'Laundry',
                    'laundry.png',
                    '',
                    '',
                    ),  //42
              array(
                    'ensuiteSites',
                    'Ensuite Sites',
                    'ensuiteSites.png',
                    '',
                    '',
                    ),  //43
              array(
                    'restaurant',
                    'Restaurant',
                    'restaurant.png',
                    '',
                    '',
                    ),  //44
              array(
                    'kiosk',
                    'Kiosk',
                    'kiosk.png',
                    '',
                    '',
                    ),  //45
              array(
                    'internetAccess',
                    'Internet Access',
                    'internetAccess.png',
                    '',
                    '',
                    ),  //46
              array(
                    'swimmingPool',
                    'Swimming',
                    'swimmingPool.png',
                    '',
                    '',
                    ),  //47
              array(
                    'gamesRoom',
                    'Games Room',
                    'gamesRoom.png',
                    '',
                    '',
                    ),  //48
              array(
                    'generatorsPermitted',
                    'Generators Permitted',
                    'generatorsPermitted.png',
                    '',
                    '',
                    ),  //49
              array(
                    'generatorsNotPermitted',
                    'Generattors Not Permitted',
                    'generatorsNotPermitted.png',
                    '',
                    '',
                    ),  //50
              array(
                    'showGrounds',
                    'Show Grounds',
                    'showGrounds.png',
                    '',
                    '',
                    ),  //51
              array(
                    'nationalParknForestry',
                    'National Parks and Forestry',
                    'nationalParknForestry.png',
                    '',
                    '',
                    ),  //52
              array(
                    'pubs',
                    'Pubs',
                    'pubs.png',
                    '',
                    '',
                    ),  //53
              array(
                    'sharedWithTrucks',
                    'Shared With Trucks',
                    'sharedWithTrucks.png',
                    '',
                    '',
                    ),  //54
          );
    $showfeatures = array(
                            'waterFillingAvailable',
                            'noTents',
                            'camperTrailers',
                            'noCamperTrailers',
                            'caravans',
                            'onSiteAccomodation', 
                            'showGrounds',  																 
                            'nationalParknForestry',											 
                            'pubs', 																				 
                            'sharedWithTrucks' 			
                        );
    $newfeatures = array(
                            'waterFillingavailable_addnl' => '',
                            'noTents_addnl'=>'',
                            'camperTrailers_addnl'=>'',
                            'noCamperTrailers_addnl'=>'',
                            'caravans_addnl'=>'',
                            'onSiteAccomodation_addnl'=>'',
                            'showGrounds_addnl'=>'',  																 
                            'nationalParknForestry_addnl'=>'',											 
                            'pubs_addnl'=>'', 																				 
                            'sharedWithTrucks_addnl'=>''			
                        );
?>