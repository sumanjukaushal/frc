<?php    
    //-------------------------------
     $globalDirArr = array(
                        'featured',                 //1
                        'address',                  //2
                        'gpsLatitude',              //3
                        'gpsLongitude',             //4
                        'showStreetview',           //5
                        'streetViewLatitude',       //6
                        'streetViewLongitude',      //7
                        'streetViewHeading',        //8
                        'streetViewPitch',          //9
                        'streetViewZoom',           //10
                        'property_type',            //11
                        'telephone',                //12
                        'email',                    //13
                        'police_check_required',    //14
                        'pet_minding_required',     //15
                        'gardening',                //16
                        'other',                    //17
                        'emailContactOwner',        //18
                        'web',                      //19
                        'location',                 //20
                        'access',                   //21
                        'responsible_authority',    //22
                        'seasonalRatesApply',       //23 New
                        'seasonalRatesApply_detail',//24 New
                        'discount',                 //25
                        'discount_detail',          //26
                        'offer',                    //27
                        'offer_detail',             //28
                        'pricing',                  //29
                        'pricing_detail',           //30
                        'help_required',            //31
                        'start_date',               //32
                        'end_date',                 //33
                        'duration',                 //34
                        'map',                      //35
                        'booking_link',             //36
                        'limit_discount',           //37
                        'per_month_uses',           //38
                        
                        //Features
                        'Features',
                        'toilets',                          //1
                        'toilets_addnl',                    
                        'disableFacility',                  //2
                        'disableFacility_addnl',            
                        'showers',                          //3
                        'showers_addnl',
                        'drinkingWater',                    //4        
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',         //5
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingAvailable',            //6 New
                        'waterFillingAvailable_addnl',
                        'wasteFacilityAvailable',           //7
                        'wasteFacilityAvailable_addnl',
                        'dumpPointAvailable',               //8
                        'dumpPointAvailable_addnl',
                        'poweredSites',                     //9
                        'poweredSites_addnl',               
                        'emergencyPhone',                   //10
                        'emergencyPhone_addnl',
                        'mobilePhoneReception',             //11                   
                        'mobilePhoneReception_addnl',
                        'petsOk',                           //12
                        'petsOk_addnl',                     
                        'petsNotOk',                        //13
                        'petsNotOk_addnl',                  
                        'fourWheelDrive',                   //14
                        'fourWheelDrive_addnl',
                        'dryWeatherOnlyAccess',             //15
                        'dryWeatherOnlyAccess_addnl',
                        'tentsOnly',                        //16
                        'tentsOnly_addnl',
                        'noTents',                          //17 New
                        'noTents_addnl',
                        'camperTrailers',                   //18 New
                        'camperTrailers_addnl',
                        'noCamperTrailers',                 //19 New
                        'noCamperTrailers_addnl',              
                        'caravans',                         //20 New
                        'caravans_addnl',
                        'largeSizeMotohomeAccess',          //21
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',                           //22
                        'bigrig_addnl',                     
                        'selfContainedVehicles',            //23
                        'selfContainedVehicles_addnl',
                        'onSiteAccomodation',               //24 New
                        'onSiteAccomodation_addnl',
                        //'seasonalRatesApply'              //25 New Not a Feature
                        //'discount'                        //26 New Not a Feature
                        //'offer'                           //27 New Not a Feature
                        'rvTurnAroundArea',                 //28
                        'rvTurnAroundArea_addnl',
                        'rvParkingAvailable',               //29
                        'rvParkingAvailable_addnl',
                        'boatRamp',                         //30
                        'boatRamp_addnl',
                        'timeLimitApplies',                 //31
                        'timeLimitApplies_addnl',
                        'shadedSites',                      //32
                        'shadedSites_addnl',
                        'firePit',                          //33
                        'firePit_addnl',
                        'noFirePit',                        //34
                        'noFirePit_addnl',
                        'picnicTable',                      //35
                        'picnicTable_addnl',
                        'bbq',                              //36
                        'bbq_addnl',
                        'shelter',                          //37
                        'shelter_addnl',
                        'childrenPlayground',               //38
                        'childrenPlayground_addnl',
                        'views',                            //39
                        'views_addnl',
                        'parkFees',                         //40
                        'parkFees_addnl',
                        'campKitchen',                      //41 
                        'campKitchen_addnl',
                        'laundry',                          //42
                        'laundry_addnl',
                        'ensuiteSites',                     //43
                        'ensuiteSites_addnl',
                        'restaurant',                       //44
                        'restaurant_addnl',
                        'kiosk',                            //45
                        'kiosk_addnl',
                        'internetAccess',                   //46
                        'internetAccess_addnl',
                        'swimmingPool',                     //47
                        'swimmingPool_addnl',
                        'gamesRoom',                        //48
                        'gamesRoom_addnl',
                        'generatorsPermitted',              //49
                        'generatorsPermitted_addnl',
                        'generatorsNotPermitted',           //50
                        'generatorsNotPermitted_addnl',
                        'showGrounds',                      //51
                        'showGrounds_addnl',
                        'nationalParknForestry',            //52
                        'nationalParknForestry_addnl',
                        'pubs',                             //53
                        'pubs_addnl',
                        'sharedWithTrucks',                 //54
                        'sharedWithTrucks_addnl',
                        //--------------------                        
                        
                        //Business Features
                        'Business Features',
                        'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',
                        
                        //Alternative Content
                        'Alternative Content',
                        'alternativeContent'
                    );
    //Free_camp Attributes 
    include('FreeCamp_1_Attributes.php');
    //Business Listing 
    include('Business_2_Attributes.php');
    //Campground Attributes
    include('Campground_3_Attributes.php');
    //4 HouseSitting Attributes
    include('HouseSitting_4_Attributes.php');        
    //5 ParkOver Attributes
    include('ParkOver_5_Attributes.php');     
    //6 ParkOver Attributes
    include('HelpOut_6_Attributes.php');       
    //7 ParkOver Attributes
    include('CaravanPark_7_Attributes.php');
    //echo "<pre>";print_r($dir2Arr);echo "</pre>";
    /*
        $tab1Array = $this->subtract_array($config, $dir1Arr);
        $tab2Array = $this->subtract_array($config, $dir2Arr);
        $tab3Array = $this->subtract_array($config, $dir3Arr);
        $tab4Array = $this->subtract_array($config, $dir4Arr);
        $tab5Array = $this->subtract_array($config, $dir5Arr);
        $tab6Array = $this->subtract_array($config, $dir6Arr);
        $tab7Array = $this->subtract_array($config, $dir7Arr);
    */
?>