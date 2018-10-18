<?php
    global $dir_show;
?>
<script>
    var prepend = 'ait-_ait-dir-item-';
    var append = '-option';
    //=== /home/freerang/public_html/directory/wp-admin
    Array.prototype.diff = function(a) {
        //return this.filter(function(i) {return a.indexOf(i) < 0;});
        return this.filter(function(i) {return !(a.indexOf(i) > -1);});
    };
    
    //[1,2,3,4,5,6].diff( [3,4,5] );  
    // => [1, 2, 6]
    //globalArr.diff(directory_1);
    
    var globalArr = [
                        'featured',             //01
                        'address',              //02
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        'gpsLatitude',          //03
                        'gpsLongitude',         //04
                        'showStreetview',       //05
                        'streetViewLatitude',   //06
                        'streetViewLongitude',  //07
                        'streetViewHeading',    //08
                        'streetViewPitch',      //09
                        'streetViewZoom',       //10
                        'property_type',        //11
                        'telephone',            //12
                        'email',                //13
                        'police_check_required',//14
                        'pet_minding_required', //15
                        'pet_minding_addnl',    //16
                        'gardening',            //17
                        'other',                //18
                        'emailContactOwner',    //19
                        'web',                  //20
                        'location',             //21
                        'access',               //22
                        'responsible_authority',//23
                        'discount',             //24
                        'discount_detail',      //25
                        'offer',                //26
                        'offer_detail',         //27
                        'seasonalRatesApply',           //New
                        'seasonalRatesApply_detail',    //New
                        'pricing',              //28
                        'pricing_detail',       //29
                        'help_required',        //30
                        'start_date',           //31
                        'end_date',             //32
                        'duration',             //33
                        'map',                  //34
                        'booking_link',         //35
                        'limit_discount',       //36
                        'per_month_uses',       //37
                        
                        //Features
                        'Features',
                        'caravans', //New
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingAvailable',    //New
                        'waterFillingAvailable_addnl',
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',
                        'dumpPointAvailable',
                        'dumpPointAvailable_addnl',
                        'poweredSites',
                        'poweredSites_addnl',
                        'emergencyPhone',
                        'emergencyPhone_addnl',
                        'mobilePhoneReception',
                        'mobilePhoneReception_addnl',
                        'petsOk',
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        'dryWeatherOnlyAccess',
                        'dryWeatherOnlyAccess_addnl',
                        'tentsOnly',
                        'tentsOnly_addnl',
                        'noTents',  //New
                        'noTents_addnl',
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'noCamperTrailers', //New
                        'noCamperTrailers_addnl',
                        'largeSizeMotohomeAccess',
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',
                        'onSiteAccomodation', //New
                        'onSiteAccomodation_addnl',
                        //'rvTurnAroundArea',
                        //'rvTurnAroundArea_addnl',
                        //'rvParkingAvailable',
                        //'rvParkingAvailable_addnl',
                        'boatRamp',
                        'boatRamp_addnl',
                        'timeLimitApplies',
                        'timeLimitApplies_addnl',
                        'shadedSites',
                        'shadedSites_addnl',
                        'firePit',
                        'firePit_addnl',
                        'noFirePit',  //New
                        'noFirePit_addnl',
                        'picnicTable',
                        'picnicTable_addnl',
                        'bbq',
                        'bbq_addnl',
                        'shelter',
                        'shelter_addnl',
                        'childrenPlayground',
                        'childrenPlayground_addnl',
                        'views',
                        'views_addnl',
                        'parkFees',
                        'parkFees_addnl',
                        //'parking',                       
                        //'parking_addnl', //95
                        //new additions for role 7
                        'campKitchen', 
                        'campKitchen_addnl',
                        'laundry',
                        'laundry_addnl',
                        'ensuiteSites',
                        'ensuiteSites_addnl',
                        'restaurant',
                        'restaurant_addnl',
                        'kiosk',
                        'kiosk_addnl',
                        'internetAccess',
                        'internetAccess_addnl',
                        'swimmingPool',
                        'swimmingPool_addnl',
                        'gamesRoom',
                        'gamesRoom_addnl',
                        'generatorsPermitted',
                        'generatorsPermitted_addnl',
                        'generatorsNotPermitted',
                        'generatorsNotPermitted_addnl',
                        'showGrounds',  //New
                        'showGrounds_addnl',
                        'nationalParknForestry',    //New
                        'nationalParknForestry_addnl',
                        'pubs', //New
                        'pubs_addnl',   
                        'sharedWithTrucks', //New
                        'sharedWithTrucks_addnl',   
                        //--------------------                        
                        
                        //Business Features
                        'Business Features',
                        'rvParkingAvailable',
                        'rvParkingAvailable_addnl',
                        'rvTurnAroundArea',
                        'rvTurnAroundArea_addnl',                       
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                        
                        //Alternative Content
                        'Alternative Content',
                        'alternativeContent'
                    ];
    
    var directory_1_show = [
                            //directory_1: free_listing_user------------
                            //----------Options for Item ---------------
                            "address",                  //1
                            'wlm_city',
                            'wlm_state',
                            'wlm_zip',
                            'gpsLatitude',              //2
                            'gpsLongitude',             //3
                            'showStreetview',           //4                        
                            'telephone',                //5
                            'email',                    //6    
                            'emailContactOwner',        //7
                            'web',                      //8
                            'location',                 //9
                            'access',                   //10
                            'responsible_authority',    //11
                            'map',                       //12
                            
                            //Features - Free Camps
                            'Features', //1
                            'caravans', //New
                            'caravans_addnl',
                            'toilets',
                            'toilets_addnl',
                            'disableFacility',
                            'disableFacility_addnl',
                            'showers',
                            'showers_addnl',
                            'drinkingWater',
                            'drinkingWater_addnl',
                            'drinkingWaterNotSuitable',
                            'drinkingWaterNotSuitable_addnl',
                            'waterFillingavailable',    //New
                            'waterFillingavailable_addnl',
                            'wasteFacilityAvailable',
                            'wasteFacilityAvailable_addnl',
                            'dumpPointAvailable',
                            'dumpPointAvailable_addnl',
                            'poweredSites',
                            'poweredSites_addnl',
                            'emergencyPhone',
                            'emergencyPhone_addnl',
                            'mobilePhoneReception',
                            'mobilePhoneReception_addnl',//--Missing
                            'petsOk',
                            'petsOk_addnl',
                            'petsNotOk',
                            'petsNotOk_addnl',
                            'fourWheelDrive',
                            'fourWheelDrive_addnl',
                            'dryWeatherOnlyAccess',
                            'dryWeatherOnlyAccess_addnl',
                            'tentsOnly',
                            'tentsOnly_addnl',
                            'noTents',  //New
                            'noTents_addnl',
                            'camperTrailers',  //New
                            'camperTrailers_addnl',
                            'noCamperTrailers',  //New
                            'noCamperTrailers_addnl',
                            'largeSizeMotohomeAccess',
                            'largeSizeMotohomeAccess_addnl',
                            'bigrig',
                            'bigrig_addnl',
                            'selfContainedVehicles',
                            'selfContainedVehicles_addnl',
                            'rvTurnAroundArea',
                            'rvTurnAroundArea_addnl',
                            'rvParkingAvailable',
                            'rvParkingAvailable_addnl',
                            'boatRamp',
                            'boatRamp_addnl',
                            'timeLimitApplies',
                            'timeLimitApplies_addnl',
                            'shadedSites',
                            'shadedSites_addnl',
                            'firePit',
                            'firePit_addnl',
                            'noFirePit',
                            'noFirePit_addnl',
                            'picnicTable',
                            'picnicTable_addnl',
                            'bbq',
                            'bbq_addnl',
                            'shelter',
                            'shelter_addnl',
                            'childrenPlayground',
                            'childrenPlayground_addnl',
                            'views',
                            'views_addnl', //59
                            'swimmingPool', //Added on 14th Apr
                            'swimmingPool_addnl',
                            'parkFees',
                            'parkFees_addnl', //61                            
                            'generatorsPermitted', //Add after Changes and Tidy Upsl.docx on 23rd Feb
                            'generatorsPermitted_addnl',
                            'generatorsNotPermitted',
                            'generatorsNotPermitted_addnl',
                            'showGrounds',  //New
                            'showGrounds_addnl',
                            'nationalParknForestry',    //New
                            'nationalParknForestry_addnl',
                            'pubs',  //New
                            'pubs_addnl',
                            'sharedWithTrucks',    //New
                            'sharedWithTrucks_addnl',
                            
                            //Opening Hours
                            'Opening Hours',
                                'hoursMonday',
                                'hoursTuesday',
                                'hoursWednesday',
                                'hoursThursday',
                                'hoursFriday',
                                'hoursSaturday',
                                'hoursSunday',
                           ];
    
    var directory_1 = globalArr.diff(directory_1_show);    
    
     var directory_2_show = [
                        //directory_2: business_listing - verified and correct
                        'address',                  //1
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        'gpsLatitude',              //2
                        'gpsLongitude',             //3
                        'showStreetview',           //4                        
                        'telephone',                //5
                        'email',                    //6    
                        'emailContactOwner',        //7
                        'web',                      //8
                        'discount',                 //9
                        'discount_detail',          //10
                        'offer',                    //11
                        'offer_detail',             //12
                        'seasonalRatesApply',       //New
                        'seasonalRatesApply_detail',//New
                        'map',                      //13,
                        'limit_discount',           //14
                        'per_month_uses',           //15
                        
                        //Features
                        'Features',
                        'caravans',
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl', //added on 23rd Feb
                        //'parking',
                        //'parking_addnl',
                        'waterFillingavailable',
                        'waterFillingavailable_addnl',
                        'rvParkingAvailable',
                        'rvParkingAvailable_addnl',
                        'rvTurnAroundArea',
                        'rvTurnAroundArea_addnl',
                        
                        //Opening Hours
                       /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                    ];
    var directory_2 = globalArr.diff(directory_2_show);
    
    
    var directory_3_show = [
                        //directory_3: low_cost_camps - verified and missing mobilePhoneReception_addnl
                        'address',                  //1
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        'gpsLatitude',              //2
                        'gpsLongitude',             //3
                        'showStreetview',           //4                        
                        'telephone',                //5
                        'email',                    //6    
                        'emailContactOwner',        //7
                        'web',                      //8
                        'pricing',                  //9
                        'pricing_detail',           //10
                        'map',                      //11
                        'booking_link',             //12
                        'discount',                 //13
                        'discount_detail',          //14
                        'offer',                    //15
                        'offer_detail',             //16
                        'seasonalRatesApply',       //New
                        'seasonalRatesApply_detail',//New
                        
                        //Features
                        'Features', //1
                        'caravans',
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingavailable',    //new
                        'waterFillingavailable_addnl', 
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',
                        'dumpPointAvailable',
                        'dumpPointAvailable_addnl',
                        'poweredSites',
                        'poweredSites_addnl',
                        'emergencyPhone', //issue when editing record for low cost and have multiple tabs
                        'emergencyPhone_addnl',
                        'mobilePhoneReception',
                        'mobilePhoneReception_addnl',//--Missing - [ait-mobilePhoneReception_addnl-option]
                        //ait-mobilePhoneReception_addnl-option should be ait-_ait-dir-item-...petsNotOk...-option
                        'petsOk',
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        'dryWeatherOnlyAccess',
                        'dryWeatherOnlyAccess_addnl',
                        'tentsOnly',
                        'tentsOnly_addnl',
                        'noTents',
                        'noTents_addnl',
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'noCamperTrailers', //New
                        'noCamperTrailers_addnl',
                        'largeSizeMotohomeAccess',
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',
                        'onSiteAccomodation',   //New
                        'onSiteAccomodation_addnl',
                        'rvTurnAroundArea',
                        'rvTurnAroundArea_addnl',
                        'rvParkingAvailable',
                        'rvParkingAvailable_addnl',
                        'boatRamp',
                        'boatRamp_addnl',
                        'timeLimitApplies',
                        'timeLimitApplies_addnl',
                        'shadedSites',
                        'shadedSites_addnl',
                        'firePit',
                        'firePit_addnl',
                        'picnicTable',
                        'picnicTable_addnl',
                        'bbq',
                        'bbq_addnl',
                        'shelter',
                        'shelter_addnl',
                        'childrenPlayground',
                        'childrenPlayground_addnl',
                        'views',
                        'views_addnl', //59
                        'parkFees',
                        'parkFees_addnl', //61
                        //new additions for role 3 added on Feb 4, 2015
                        'campKitchen', 
                        'campKitchen_addnl',
                        'laundry',
                        'laundry_addnl',
                        'ensuiteSites',
                        'ensuiteSites_addnl',
                        'restaurant',
                        'restaurant_addnl',
                        'kiosk',
                        'kiosk_addnl',
                        'internetAccess',
                        'internetAccess_addnl',
                        'swimmingPool',
                        'swimmingPool_addnl',
                        'gamesRoom',
                        'gamesRoom_addnl',
                        'generatorsPermitted',
                        'generatorsPermitted_addnl',
                        'generatorsNotPermitted',
                        'generatorsNotPermitted_addnl', //79
                        'showGrounds',  //new
                        'showGrounds_addnl',
                        'nationalParknForestry',    //New
                        'nationalParknForestry_addnl',
                        'pubs', //New
                        'pubs_addnl',
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                                               
                    ];
    var directory_3 = globalArr.diff(directory_3_show);
     
    var directory_4_show = [
                        //directory_4: house_sitting - verified mobilePhoneReception_addnl missing
                        //'address',                  //01
                        //'gpsLatitude',              //02
                        //'gpsLongitude',             //03
                        //'showStreetview',           //04
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        'telephone',                //05
                        'email',                    //06    
                        'emailContactOwner',        //07                       
                        'start_date',               //08
                        'end_date',                 //09
                        'duration',                 //10
                        "property_type",            //11
                        "police_check_required",    //12
                        "pet_minding_required",     //13
                        "pet_minding_addnl",        //14
                        "gardening",                //15
                        "other",                    //16
                        //'map',                      //17
                       
                       //Features
                        'Features', //1
                        'caravans', //New
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingavailable',
                        'waterFillingavailable_addnl',
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',                        
                        'poweredSites',
                        'poweredSites_addnl',                        
                        'mobilePhoneReception',
                        'mobilePhoneReception_addnl',   //--missing
                        'petsOk',
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'noCamperTrailers', //New
                        'noCamperTrailers_addnl',
                        'largeSizeMotohomeAccess',
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',    //29
                        'laundry',  //Added on 23rd Feb
                        'laundry_addnl',
                        'internetAccess',
                        'internetAccess_addnl',
                        'swimmingPool',
                        'swimmingPool_addnl',
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                    ];
    var directory_4 = globalArr.diff(directory_4_show);
    
    var directory_5_show = [
                        //directory_5: park_overs - verified all correct
                        //'address',                  //1
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        //'gpsLatitude',              //2
                        //'gpsLongitude',             //3
                        'showStreetview',           //4                        
                        'telephone',                //5
                        'email',                    //6    
                        'emailContactOwner',        //7
                        'property_type',            //8
                        'police_check_required',    //9
                        'map',                      //10
                       
                       //Features
                        'Features',
                        'caravans', //New
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingavailable',    //New
                        'waterFillingavailable_addnl',
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',
                        'poweredSites',
                        'poweredSites_addnl',
                        'mobilePhoneReception',
                        'mobilePhoneReception_addnl',
                        'petsOk',
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'noCamperTrailers', //New
                        'noCamperTrailers_addnl',
                        'largeSizeMotohomeAccess', //show this field conidtionally-rasa
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                    ];
    var directory_5 = globalArr.diff(directory_5_show);
    
    var directory_6_show = [
                        //directory_6: help_outs - verified petsOK missing
                        //'address',                  //1
                        'wlm_city',
                        'wlm_state',
                        //'gpsLatitude',              //2
                        //'gpsLongitude',             //3
                        //'showStreetview',           //4                        
                        'telephone',                //5
                        'email',                    //6    
                        'emailContactOwner',        //7
                        'web',                      //8
                        'location',                 //9                        
                        'responsible_authority',    //10
                        'police_check_required',    //11    //added on 23rd Feb - misssing in single list
                        'help_required',            //12
                        'start_date',               //13
                        'end_date',                 //14
                        'duration',                 //15
                        //'map',                      //16
                        
                        //Features  //1
                        'Features',
                        'caravans', //New
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingavailable',    //New
                        'waterFillingavailable_addnl',
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',                       
                        'poweredSites',
                        'poweredSites_addnl',                       
                        'mobilePhoneReception',
                        'mobilePhoneReception_addnl',
                        'petsOk',                       //missing
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        //'dryWeatherOnlyAccess',
                        //'dryWeatherOnlyAccess_addnl',     
                        //'tentsOnly',
                        //'tentsOnly_addnl',    //commented on 23rd Feb 2015
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'noCamperTrailers', //New
                        'noCamperTrailers_addnl',
                        'largeSizeMotohomeAccess',
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',                        
                        'laundry',  //Added on 23rd Feb
                        'laundry_addnl',
                        'swimmingPool',
                        'swimmingPool_addnl',
                        'internetAccess',
                        'internetAccess_addnl',
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                    ];
    var directory_6 = globalArr.diff(directory_6_show);
    
    var directory_7_show = [
                        //directory_7: dollar_25plus [Caravan Parks] - verified mobilePhoneReception_addnl missing
                        'address',                  //1
                        'wlm_city',
                        'wlm_state',
                        'wlm_zip',
                        'gpsLatitude',              //2
                        'gpsLongitude',             //3
                        'showStreetview',           //4                        
                        'telephone',                //5
                        'email',                    //6    
                        'emailContactOwner',        //7
                        'web',                      //8
                        'pricing',                  //9
                        'pricing_detail',           //10
                        'map',                      //11
                        'booking_link',             //12
                        'discount',                 //13
                        'discount_detail',          //14
                        'offer',                    //15
                        'offer_detail',             //16
                        'seasonalRatesApply',       //New
                        'seasonalRatesApply_detail', //New
                        
                        //Features
                        'Features',
                        'caravans', //New
                        'caravans_addnl',
                        'toilets',
                        'toilets_addnl',
                        'disableFacility',
                        'disableFacility_addnl',
                        'showers',
                        'showers_addnl',
                        'drinkingWater',
                        'drinkingWater_addnl',
                        'drinkingWaterNotSuitable',
                        'drinkingWaterNotSuitable_addnl',
                        'waterFillingavailable',    //New
                        'waterFillingavailable_addnl',
                        'wasteFacilityAvailable',
                        'wasteFacilityAvailable_addnl',
                        'dumpPointAvailable',
                        'dumpPointAvailable_addnl',
                        'poweredSites',
                        'poweredSites_addnl',
                        'emergencyPhone',//missing mobilePhoneReception_addnl in the case of dollar_25plus account
                        'emergencyPhone_addnl',
                        'mobilePhoneReception', 
                        'mobilePhoneReception_addnl', 
                        'petsOk',
                        'petsOk_addnl',
                        'petsNotOk',
                        'petsNotOk_addnl',
                        'fourWheelDrive',
                        'fourWheelDrive_addnl',
                        'dryWeatherOnlyAccess',
                        'dryWeatherOnlyAccess_addnl',
                        'tentsOnly',
                        'tentsOnly_addnl',
                        'noTents',  //New
                        'noTents_addnl',
                        'camperTrailers',   //New
                        'camperTrailers_addnl',
                        'largeSizeMotohomeAccess',
                        'largeSizeMotohomeAccess_addnl',
                        'bigrig',
                        'bigrig_addnl',
                        'selfContainedVehicles',
                        'selfContainedVehicles_addnl',
                        'onSiteAccomodation',   //New
                        'onSiteAccomodation_addnl',
                        'rvTurnAroundArea',
                        'rvTurnAroundArea_addnl',
                        'rvParkingAvailable',
                        'rvParkingAvailable_addnl',
                        'boatRamp',
                        'boatRamp_addnl',
                        'timeLimitApplies',
                        'timeLimitApplies_addnl',
                        'shadedSites',
                        'shadedSites_addnl',
                        'firePit',
                        'firePit_addnl',
                        'noFirePit',    //New
                        'noFirePit_addnl',
                        'picnicTable',
                        'picnicTable_addnl',
                        'bbq',
                        'bbq_addnl',
                        'shelter',
                        'shelter_addnl',
                        'childrenPlayground',
                        'childrenPlayground_addnl',
                        'views',
                        'views_addnl',
                        'parkFees', //in question
                        'parkFees_addnl', //in question
                        //new additions for role 7
                        'campKitchen', 
                        'campKitchen_addnl',
                        'laundry',
                        'laundry_addnl',
                        'ensuiteSites',
                        'ensuiteSites_addnl',
                        'restaurant',
                        'restaurant_addnl',
                        'kiosk',
                        'kiosk_addnl',
                        'internetAccess',
                        'internetAccess_addnl',
                        'swimmingPool',
                        'swimmingPool_addnl',
                        'gamesRoom',
                        'gamesRoom_addnl',
                        'generatorsPermitted',
                        'generatorsPermitted_addnl',
                        'generatorsNotPermitted',
                        'generatorsNotPermitted_addnl',
                        'showGrounds',  //New
                        'showGrounds_addnl',
                        'nationalParknForestry',    //New
                        'nationalParknForestry_addnl',
                        'pubs', //New
                        'pubs_addnl',
                        
                        //Opening Hours
                        /*'Opening Hours',
                        'hoursMonday',
                        'hoursTuesday',
                        'hoursWednesday',
                        'hoursThursday',
                        'hoursFriday',
                        'hoursSaturday',
                        'hoursSunday',*/
                    ];
    var directory_7 = globalArr.diff(directory_7_show); //= items which we want to hide
    <?php
        $freeCatParents = $businessCatParents = $lowCostParents = $houseSittingParents = array();
        $parkOverParents = $helpOutParents = $dollar25Plus = array();
    ?>
    function hideAllCat(catID){
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        alert('catID'+catID);
        <?php
        $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1, 'hide_empty' => 0));
        
        $freeCat = array('Free Camps','Dump Points','Rest Areas', 'Tourist Information Centres', 'Tourist Information Outlets');        
        $businessCat = array(
                                'camping-accessories', //5
                                'food-wine',           //8
                                'information-centre',  //2088
                                'entertainment',       //7    
                                'fuel',                //210
                                'groceries',           //10
                                'markets',             //106
                                'medical',             //15
                                'personal-health',     //17
                                'repairs',             //4
                                'services',            //107
                                'accommodation',       //2096
                                'rv-sales-repairs',     //6      //added on 13th July
                                'rv-auto-accessories',   //2797 -  RV & Auto Accessories
                                //'auto-rv-caravan',
                                //'iga-stores',
                                //'things-to-do',
                                //'united-fuels',
                                //'puma',
                             );//'free-range-camping'
        
        $lowCostCat = array('campgrounds',);//'low-cost',
        $houseSittingCat = array('house-sitting');
        $parkOverCat = array('park-overs');
        $helpOutCat = array('help-out');
        $dollar25PlusCat = array('caravan-parks');//'25-per-night'
        
        $showDivs = "\n\t";
        $hideDivs = "\n\t";
        foreach($Itemcats as $sngCat){
            //echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide()"; //minified
            $showDivs.="jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();";
            $showDivs.="jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();";
        }
        
        
        
        foreach($Itemcats as $sngCat){
            //echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide()"; //minified
            //$hideDivs.="\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').hide()";
            $hideDivs.="jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide();";
            
            
            //get free cat parent id
            if(in_array($sngCat->name,$freeCat)){
                $freeCatParents[] = $sngCat->cat_ID;
            }
            
            //get business listing parent ids
            if(in_array($sngCat->slug, $businessCat)){
                $businessCatParents[] = $sngCat->cat_ID;
            }
            
            //get low cost (Campgrounds) parent ids
            if(in_array($sngCat->slug,$lowCostCat)){
                $lowCostParents[] = $sngCat->cat_ID;
            }
            
            //get house sitting parent ids
            if(in_array($sngCat->slug,$houseSittingCat)){
                $houseSittingParents[] = $sngCat->cat_ID;
            }
            
            //get park over parent ids
            if(in_array($sngCat->slug,$parkOverCat)){
                $parkOverParents[] = $sngCat->cat_ID;
            }
            
            //get help out parent ids
            if(in_array($sngCat->slug,$helpOutCat)){
                $helpOutParents[] = $sngCat->cat_ID;
            }
            
            //get park dollar 25+ cats parent ids
            if(in_array($sngCat->slug,$dollar25PlusCat)){
                $dollar25Plus[] = $sngCat->cat_ID;
            }
        }
        //echo "/*<pre>#919"; print_r($businessCat); print_r($businessCatParents);echo "</pre>*/";
        ?>
    }
    
    //show free categories
    jQuery( "#directory_1" ).click(function() {
        //alert('free');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //hideAllCat('directory_1');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($freeCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>
    });
    
    //show business categories
    jQuery( "#directory_2" ).click(function() {
        //alert('buisness');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_2');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($businessCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>            
    });
    
    //low cost categories ('Campgrounds');
    jQuery( "#directory_3" ).click(function() {
        //alert('low cost');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_3');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($lowCostParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>            
    });
    
    //house sitting categories
    jQuery( "#directory_4" ).click(function() {
        //alert('house sitting');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_4');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($houseSittingParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Park Over categories
    jQuery( "#directory_5" ).click(function() {
        //alert('Park Over');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_5');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($parkOverParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Park Over categories
    jQuery( "#directory_6" ).click(function() {
        //alert('Help Outs');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_6');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($helpOutParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });
    
    //Dollar 25+ categories
    jQuery( "#directory_7" ).click(function() {
        //alert('Help Outs');
        var catAppend = "ait-dir-item-category-";
        var checkboxAppend = 'in-ait-dir-item-category-';
        //in-popular-ait-dir-item-category
        //hideAllCat('directory_7');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($dollar25Plus as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "\n\tjQuery('#'+catAppend+'".$sngCat->cat_ID."').show()"; //minified
                    echo "\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show()"; //minified
                }
            }
        ?>            
    });   
    
    
    var directory_1_address = "Place your address in this field in the following format:  Street Number (If available) Street Name, Suburb/Town and State.  Eg.  15 Smith Street, Cooroy, Qld This will allow your GPS Co-Ordinates to be automatically located by Google Maps.  In the event you do not have an exact street number, you can enter more accurate details in the GPS Section Below.";//free
    
    var  directory_1_lattitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    //same for business, free, 
    var  directory_1_longitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_1_street_view = "This field allows us to show a Street view of your property in the More Information window.  Tick this box if you wish to use this option. NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    //same for business
    
    var directory_2_address = directory_1_address;//business
    var directory_2_lattitude = directory_1_lattitude;
    var directory_2_longitude = directory_1_longitude;
    var directory_2_street_view = directory_1_street_view;
    
    var directory_3_address = directory_1_address; //lowcost
    var directory_3_lattitude = directory_1_lattitude;
    var directory_3_longitude = directory_1_longitude;
    var directory_3_street_view = directory_1_street_view;
    
    var directory_4_address = "For privacy reasons, it is important that you do NOT place your full address in this field.  Simply enter your Suburb/Town and State.  This will allow the GPS Co-Ordinates to be automatically located by Google Maps and show up in the center of the Suburb or Town and not disclose your private address to everyone."; //house sitting    
    var directory_4_lattitude = "Do not use this field for House Sitting Listings";
    var directory_4_longitude = "Do not use this field for House Sitting Listings";
    var directory_4_street_view = "Do not mark this box for House Sitting Overs Listings";
    
    var directory_5_address = directory_4_address; //Park Over
    var directory_5_lattitude = "Do not use this field for Park Overs Listings";
    var directory_5_longitude = "Do not use this field for Park Overs Listings";
    var directory_5_street_view = "Do not mark this box for Park Overs Listings";
    
    var directory_6_address = directory_4_address; //Helpout
    var directory_6_lattitude = "Do not use this field for Help Outs Listings";
    var directory_6_longitude = "Do not use this field for Help Outs Listings";
    var directory_6_street_view = "Do not mark this box for Help Outs Listings";
    
    var directory_7_address = directory_1_address; //25+
    var directory_7_lattitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_7_longitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_7_street_view = "This field allows us to show a Street view of your property in the More Information window. Tick this box if you wish to use this option. NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    //tinymce or content_ifr add tooltip or wp-content-editor-container for this id also.
    
    function setHash(hash){
        update_hidden_attribute(hash);
        //jQuery("h3:contains('Opening Hours')" ).hide();
        jQuery( "#find-address-button" ).show();
        switch(hash){
            case 'directory_1':
                filter_options(directory_1,'directory_1');
                
                jQuery("#tooltip_address" ).tooltip({content: directory_1_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_1_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_1_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_1_street_view,track:true});
                break;
            case 'directory_2':
                filter_options(directory_2,'directory_2');
                jQuery("h3:contains('Opening Hours')" ).show();
                
                jQuery("#tooltip_address" ).tooltip({content: directory_2_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_2_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_2_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_2_street_view,track:true});
                break;
            case 'directory_3':
                filter_options(directory_3,'directory_3');
                jQuery("#ait-_ait-dir-item-emergencyPhone-option").show();
                jQuery("#tooltip_address" ).tooltip({content: directory_3_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_3_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_3_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_3_street_view,track:true});
                break;
            case 'directory_4':
                filter_options(directory_4,'directory_4');
                jQuery( "#find-address-button" ).hide();
                jQuery("#tooltip_address" ).tooltip({content: directory_4_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_4_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_4_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_4_street_view,track:true});
                break;
            case 'directory_5':
                filter_options(directory_5,'directory_5');
                jQuery("#ait-_ait-dir-item-largeSizeMotohomeAccess-option").show();
                jQuery("#tooltip_address" ).tooltip({content: directory_5_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_5_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_5_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_5_street_view,track:true});
                jQuery('#ait-_ait-dir-item-address-option').hide();
                jQuery('#ait-map-option').hide();
                jQuery('#ait-_ait-dir-item-gpsLongitude-option').hide();
                jQuery('#ait-_ait-dir-item-gpsLatitude-option').hide();
                jQuery('#ait-_ait-dir-item-showStreetview-option').hide();
                jQuery('#find-address-button' ).hide();
                break;
            case 'directory_6':
                filter_options(directory_6,'directory_6');
                jQuery( "#find-address-button" ).hide();
                jQuery("#tooltip_address" ).tooltip({content: directory_6_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_6_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_6_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_6_street_view,track:true});
                break;
            case 'directory_7':                
                filter_options(directory_7,'directory_7');
                
                jQuery("#tooltip_address" ).tooltip({content: directory_7_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_7_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_7_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_7_street_view,track:true});
                break;
            default:
                filter_options(directory_1,'directory_1');
                jQuery("#tooltip_address" ).tooltip({content: directory_1_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_1_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_1_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_1_street_view,track:true});
        }
        //alert(hash);
    }
    
    function update_hidden_attribute(activeDirectory){
        //alert(activeDirectory);
        jQuery("label:contains('Directory Type')" ).hide();
        //updating directory type on the basis of active tab only for new listing
        <?php
            if(true){ //$postID == 0
                //allowing user to update directory type after editing of list also
        ?>
            if (document.getElementById(prepend+'Hidden Flags'+append)) {
                document.getElementById(prepend+'directoryType').value = activeDirectory;
            }
            var directType = prepend+'directoryType';
            if (document.getElementById(directType)) {
                jQuery("#"+directType ).val(activeDirectory);
                jQuery("#"+directType ).hide();
            }
        <?php } ?>        
    }
    //----------------------------Newly added on 31st Dec, 2014---------------
    
    //------------------------------------------------------
    function showAllOptions() {       
        for(index = 0; index < globalArr.length; index++){
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+globalArr[index]+map_append;
            }else{
                temp = prepend+globalArr[index]+append;
            }
            if (document.getElementById(temp)) {
                document.getElementById(temp).style.display = 'block';
            }            
        }
    }
    
    function hideAllOptions(active_dir) {       
        for(index = 0; index < globalArr.length; index++){
            if (
                active_dir == 'directory_4' ||
                active_dir == 'directory_6'
            ) {
               ; 
            }
            else if(
               globalArr[index] == 'address' || //rasa
               globalArr[index] == 'gpsLatitude' || //rasa
               globalArr[index] == 'gpsLongitude' || //rasa
               /*globalArr[index] == 'showStreetview' ||
               globalArr[index] == 'streetViewLatitude' ||
               globalArr[index] == 'streetViewLongitude' ||
               globalArr[index] == 'streetViewHeading' ||
               globalArr[index] == 'streetViewPitch' ||
               globalArr[index] == 'streetViewZoom' ||*/
               globalArr[index] == 'map'
            ){
                //do not hide map and address fields if directory is other than 4 and 6 
                continue;
            }
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+globalArr[index]+map_append;
            }else{
                temp = prepend+globalArr[index]+append;
            }
            if (document.getElementById(temp)) {
                document.getElementById(temp).style.display = 'none';
            }            
        }
    }
    
    function filter_options(directory_arr, active_dir) {
        //****Start of changing active tab onClick****
        var tabArr = ['directory_1','directory_2','directory_3','directory_4','directory_5','directory_6','directory_7'];
        for(index = 0; index < tabArr.length; index++) {
            if(document.getElementById(tabArr[index])){
                document.getElementById(tabArr[index]).className = '';
                document.getElementById(tabArr[index]).className = 'nav-tab';
            }            
        }
        
        document.getElementById(active_dir).className = '';
        document.getElementById(active_dir).className = 'nav-tab nav-tab-active';
        //****End of changing active tab onClick****
        
        //--------------------reverse show hide-----------------        
        hideAllOptions(active_dir); //with some exceptions for google map
        //alert(active_dir);
        var t_directory = globalArr.diff(directory_arr);       
        for(index = 0; index < t_directory.length; index++) {            
            //alert(active_dir + '')
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+t_directory[index]+map_append;
            }else{
                temp = prepend+t_directory[index]+append;
            }
            if (active_dir == 'directory_3' || active_dir == 'directory_7') {
                //alert(index + "<>" + temp);
                //ait-_ait-dir-item-poweredSites-option => ait-poweredSites-option [Error]
                if (temp == 'ait-poweredSites-option') {
                    temp = 'ait-_ait-dir-item-poweredSites-option';
                }
                console.log(index + "<>" + temp);
            }
            if ( active_dir == 'directory_5') {
                if(temp == 'ait-fourWheelDrive-option'){
                    temp = 'ait-_ait-dir-item-fourWheelDrive-option';
                }
                if (temp == 'ait-noCamperTrailers_addnl-option') {
                    temp = 'ait-_ait-dir-item-noCamperTrailers_addnl-option';
                }
                console.log(index + "<>" + temp);
            }
            if ( active_dir == 'directory_6') {
                if(temp == 'ait-camperTrailers-option'){
                    temp = 'ait-_ait-dir-item-camperTrailers-option';
                }
                console.log(index + "<>" + temp);
            }
            if ('mobilePhoneReception_addnl' == t_directory[index]) {
                temp = prepend+t_directory[index]+append;
            }
            if ('petsOk' == t_directory[index]) {
                temp = prepend+t_directory[index]+append;
            }
            //chmod -R <permissionsettings> <dirname>
            //sudo chmod -R 755 yahoo_mail 
            if (document.getElementById(temp)) {
                document.getElementById(temp).style.display = 'block';
                document.getElementById(temp).style.display = '';
            }
            
            //alert(temp);
            //if ('mobilePhoneReception_addnl' == t_directory[index]) {
            //    if (document.getElementById(temp)) {
            //        alert('if');
            //    }else{//ait-mobilePhoneReception_addnl-option
            //        alert('else');
            //    }
            //}
        }
        
        var excluded_fields = [
                        'address',                  //1
                        'gpsLatitude',              //2
                        'gpsLongitude',             //3
                        'map',                      //4
                        ];
        
        if (active_dir == 'directory_4' || active_dir == 'directory_6') {
            ;
        }else{
            for(index = 0; index < excluded_fields.length; index++) {
                if (excluded_fields[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    temp = map_prepend + excluded_fields[index] + map_append;
                }else{
                    temp = prepend + excluded_fields[index] + append;
                }
                
                if (document.getElementById(temp)) {
                   document.getElementById(temp).style.display = '';
                }
            }
        }
        //--------------------reverse show hide-----------------
    }
    var firstTab = 'tabdirectory_1';
<?php
    if(is_array($tabArr)){
        foreach($tabArr as $firstTab => $value){break;}
        switch($firstTab){
            case 'directory_1':
                echo "firstTab = 'tabdirectory_1';";
                break;
            case 'directory_2':
                echo "firstTab = 'tabdirectory_2';";
                break;
            case 'directory_3':
                echo "firstTab = 'tabdirectory_3';";
                break;
            case 'directory_4':
                echo "firstTab = 'tabdirectory_4';";
                break;
            case 'directory_5':
                echo "firstTab = 'tabdirectory_5';";
                break;
            case 'directory_6':
                echo "firstTab = 'tabdirectory_6';";
                break;
            case 'directory_7':
                echo "firstTab = 'tabdirectory_7';";
                break;            
        }
        //echo "alert('$firstTab');";
    }
?>
    function getHash(){
        //jQuery("h3:contains('Opening Hours')" ).hide();
        var hash = window.location.hash.substring(1);
        if(hash == ""){hash = 'tab'+firstTab;} 
        switch (hash) {          
            case 'tabdirectory_1':
                jQuery( "#directory_1" ).trigger( "click" );                
                break;
            case 'tabdirectory_2':
                jQuery("h3:contains('Opening Hours')" ).show();
                jQuery( "#directory_2" ).trigger( "click" );                
                break;
            case 'tabdirectory_3':
                jQuery( "#directory_3" ).trigger( "click" );
                break;
            case 'tabdirectory_4':
                jQuery( "#directory_4" ).trigger( "click" );
                break;
            case 'tabdirectory_5':
                jQuery( "#directory_5" ).trigger( "click" );
                break;
            case 'tabdirectory_6':
                jQuery( "#directory_6" ).trigger( "click" );
                break;
            case 'tabdirectory_7':                
                jQuery( "#directory_7" ).trigger( "click" );
                break;
            default:
                jQuery( "#directory_1" ).trigger( "click" );
        }
        //alert(hash);
    }
    getHash(<?php echo $firstTab;?>);
    jQuery( document ).ready(function() {
        if(firstTab == 'tabdirectory_4' || firstTab == 'tabdirectory_6'){
            jQuery( "#ait-map-option" ).hide();
            jQuery( "#find-address-button" ).hide();
        }  
    });
    <?php 
     if(isset($currentDirectory) && !empty($currentDirectory) ){
        //Note: We are setting this variable in Metabox.inc
        //(Path: \themes\directory\AIT\Framework\Libs\WPAlchemy) function textControl
        //echo "setHash('$currentDirectory');";
        echo "jQuery('#$currentDirectory').trigger( \"click\" );";
     }
     ?>
</script>
<?php
    $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member™'
    if(is_plugin_active($wishlistPlugin)){
        global $current_user;
        $wlmObj = new WishListMember();
        $wlmUseraAddress = $wlmObj->Get_UserMeta($current_user->ID,'wpm_useraddress');
        //print_r($wlmUseraAddress);
        $userMeta =<<<USER_META
        <script>
            jQuery('#'+prepend+'wlm_city').val('{$wlmUseraAddress['city']}');
            jQuery('#'+prepend+'wlm_state').val('{$wlmUseraAddress['state']}');
            jQuery('#'+prepend+'wlm_zip').val('{$wlmUseraAddress['zip']}');            
            jQuery('#'+prepend+'address').val('{$wlmUseraAddress['address1']}');
        </script>
USER_META;
        $postID = 0;
        if(array_key_exists('post',$_REQUEST)){
            $postID = (int)$_REQUEST['post'];
        }
        if(!$postID){echo $userMeta;}
        
    }
?>