<?php
    global $dir_show;
    //include("tooltip.php");
?>
<script>
    var prepend = 'ait-_ait-item-';
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                        '68136230859a127611e9d10.16193837',//dumpPointAvailable
                        '28913861059a127611e9d32.89127897',//dumpPointAvailable_addnl
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl
                        '49519333159a128fb9e0098.57763366',//emergencyPhone
                        '1264394359a128fb9e00c7.64748028',//emergencyPhone_addnl
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '144274401759a12a8077ceb2.98916116',//dryWeatherOnlyAccess
                        '137602637859a12a8077cee7.04884000',//dryWeatherOnlyAccess_addnl
                        '116178928259a12a8077cf25.08496002',//tentsOnly
                        '175033111259a12a8077cf64.35811428',//tentsOnly_addnl
                        '3896023559a12a8077cf93.09482080',//noTents  //New
                        '30743723259a12a8077cfd9.84645884',//noTents_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                        '139804642359a12e4cc7c613.03585852',//onSiteAccomodation //New
                        '104896684459a12e4cc7c631.61268353',//onSiteAccomodation_addnl
                        //'rvTurnAroundArea',
                        //'rvTurnAroundArea_addnl',
                        //'rvParkingAvailable',
                        //'rvParkingAvailable_addnl',
                        '75487067159a130c0000063.47568166',//boatRamp
                        '86489403959a130c0000097.39746852',//boatRamp_addnl
                        '193829933459a130c00000b2.70881197',//timeLimitApplies
                        '91325718659a130c00000d7.05560623',//timeLimitApplies_addnl
                        '174008921159a130c00000f7.06865568',//shadedSites
                        '90219130559a130c0000126.15606332',//shadedSites_addnl
                        '208574398559a1319dced256.54856460',//firePit
                        '96932479159a1319dced2f4.48839598',//firePit_addnl
                        '157519193059a1319dced324.45146973',//noFirePit  //New
                        '92648312159a1319dced345.18145422',//noFirePit_addnl
                        '50095449759a1319dced360.35719174',//picnicTable
                        '208048102859a1319dced394.92664794',//picnicTable_addnl
                        '23268569959a1328cca76e8.15076099',//bbq
                        '155131766359a1328cca7837.02722797',//bbq_addnl
                        '136297802759a1328cca7895.14816330',//shelter
                        '5662200259a1328cca78d0.55880470',//shelter_addnl
                        '65335996059a1397ac69e61.16868042',//childrenPlayground
                        '70870383959a1397ac69fb9.22200738',//childrenPlayground_addnl
                        '31863726159a1397ac6a005.03158568',//views
                        '10736999859a1397ac6a056.62941562',//views_addnl
                        '52677997159a13cc46452e3.37345362',//parkFees
                        '163208053459a13cc46453f2.99439357',//parkFees_addnl
                        //'parking',                       
                        //'parking_addnl', //95
                        //new additions for role 7
                        '40375140559a13cc4645443.47962524', //campKitchen
                        '210513671559a13cc4645471.47839174',//campKitchen_addnl
                        '125526885259a13fc3432fe5.09981042',//laundry
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '127875872459a1419114ba21.47959230',//ensuiteSites
                        '166076809859a1419114bb04.88803687',//ensuiteSites_addnl
                        '191709818959a1419114bb22.17156129',//restaurant
                        '182069099159a1419114bb56.47084308',//restaurant_addnl
                        '48343349759a143b902b316.86257805',//kiosk
                        '104701077159a143b902b460.48418867',//kiosk_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        '146237113459a143b902b5c9.59301660',//gamesRoom
                        '73756936959a143b902b5f6.60751977',//gamesRoom_addnl
                        '197977266559a143b902b807.38776684',//generatorsPermitted
                        '184061923659a143b902b856.23529609',//generatorsPermitted_addnl
                        '126593474259a1476c7a45f1.63218688',//generatorsNotPermitted
                        '199408683159a1476c7a4747.07506530',//generatorsNotPermitted_addnl
                        '36497051859a1476c7a47a6.45322651',//showGrounds  //New
                        '71006354559a1476c7a47e7.28910931',//showGrounds_addnl
                        '23841355559a1476c7a4825.86443341',//nationalParknForestry    //New
                        '145462151159a1476c7a4862.15834507',//nationalParknForestry_addnl
                        '116373906659a1476c7a4897.15412247',//pubs //New
                        '126351126659a1476c7a48d5.31555450',//pubs_addnl   
                        '54328455559a1476c7a4901.74601924',//sharedWithTrucks //New
                        '26705362759a1476c7a4941.39155723',//sharedWithTrucks_addnl   
                        //--------------------                        
                        
                        //Business Features
                        'Business Features',
                        '138295357859a130c0000034.04181816',//rvParkingAvailable
                        '71110796459a130c0000042.82981721',//
                        '171017551359a130bff423e3.61654015',//rvTurnAroundArea
                        '124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl                       
                        
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
                            '160327751759a12cbd8e4eb2.99387079',//caravans //New
                            '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                            //'2118905308599ff84247c456.33090579',//toilets
                            //'373584023599ff84247c4c5.56749002',//toilets_addnl
                            //'147536215359a1200c7db353.16518800',//disableFacility
                            //'113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                            //'188168178159a120ec1b4bb7.28131025',//showers
                            //'124299123159a120ec1b4be3.98669372',//showers_addnl
                            //'148620156659a12645e46256.51574107',//drinkingWater
                            //'125877315659a12645e462f0.58533584',//drinkingWater_addnl
                            //'190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                            //'52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                            //'603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                            //'189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                            //'39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                            //'153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                            //'68136230859a127611e9d10.16193837',//dumpPointAvailable
                            //'28913861059a127611e9d32.89127897',//dumpPointAvailable_addnl
                            //'29615392559a128fb9dffa4.51040597',//poweredSites
                            //'21656720059a128fb9e0062.02008131',//poweredSites_addnl
                            //'49519333159a128fb9e0098.57763366',//emergencyPhone
                            //'1264394359a128fb9e00c7.64748028',//emergencyPhone_addnl
                            //'13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                            //'24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                            //'16040096659a128fb9e0135.03414696',//petsOk
                            //'31379692359a128fb9e0151.31927562',//petsOk_addnl
                            //'107191184659a128fb9e0180.04901523',//petsNotOk
                            //'49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                            //'170509770559a12a8077cd57.63725733',//fourWheelDrive
                            //'64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                            //'144274401759a12a8077ceb2.98916116',//dryWeatherOnlyAccess
                            //'137602637859a12a8077cee7.04884000',//dryWeatherOnlyAccess_addnl
                            //'116178928259a12a8077cf25.08496002',//tentsOnly
                            //'175033111259a12a8077cf64.35811428',//tentsOnly_addnl
                            //'3896023559a12a8077cf93.09482080',//noTents  //New
                            //'30743723259a12a8077cfd9.84645884',//noTents_addnl
                            //'78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                            //'55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                            //'115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                            //'107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                            //'110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                            //'175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                            //'50860830159a12e4cc7c4e2.58547247',//bigrig
                            //'17003835859a12e4cc7c595.42517010',//bigrig_addnl
                            //'61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                            //'5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                            //'138295357859a130c0000034.04181816',//rvParkingAvailable
                            //'71110796459a130c0000042.82981721',//rvParkingAvailable_addnl
                            //'171017551359a130bff423e3.61654015',//rvTurnAroundArea
                            //'124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl 
                            //'75487067159a130c0000063.47568166',//boatRamp
                            //'86489403959a130c0000097.39746852',//boatRamp_addnl
                            //'193829933459a130c00000b2.70881197',//timeLimitApplies
                            //'91325718659a130c00000d7.05560623',//timeLimitApplies_addnl
                            //'174008921159a130c00000f7.06865568',//shadedSites
                            //'90219130559a130c0000126.15606332',//shadedSites_addnl
                            //'208574398559a1319dced256.54856460',//firePit
                            //'96932479159a1319dced2f4.48839598',//firePit_addnl
                            //'157519193059a1319dced324.45146973',//noFirePit  //New
                            //'92648312159a1319dced345.18145422',//noFirePit_addnl
                            //'50095449759a1319dced360.35719174',//picnicTable
                            //'208048102859a1319dced394.92664794',//picnicTable_addnl
                            //'23268569959a1328cca76e8.15076099',//bbq
                            //'155131766359a1328cca7837.02722797',//bbq_addnl
                            //'136297802759a1328cca7895.14816330',//shelter
                            //'5662200259a1328cca78d0.55880470',//shelter_addnl
                            //'65335996059a1397ac69e61.16868042',//childrenPlayground
                            //'70870383959a1397ac69fb9.22200738',//childrenPlayground_addnl
                            //'31863726159a1397ac6a005.03158568',//views
                            //'10736999859a1397ac6a056.62941562',//views_addnl//59
                            //'19149342359a143b902b549.18456158',//swimmingPool
                            //'150271062859a143b902b580.66852110',//swimmingPool_addnl
                            //'52677997159a13cc46452e3.37345362',//parkFees
                            //'163208053459a13cc46453f2.99439357',//parkFees_addnl //61                            
                            //'197977266559a143b902b807.38776684',//generatorsPermitted
                            //'184061923659a143b902b856.23529609',//generatorsPermitted_addnl
                            //'126593474259a1476c7a45f1.63218688',//generatorsNotPermitted
                            //'199408683159a1476c7a4747.07506530',//generatorsNotPermitted_addnl
                            //'36497051859a1476c7a47a6.45322651',//showGrounds  //New
                            //'71006354559a1476c7a47e7.28910931',//showGrounds_addnl
                            //'23841355559a1476c7a4825.86443341',//nationalParknForestry    //New
                            //'145462151159a1476c7a4862.15834507',//nationalParknForestry_addnl
                            //'116373906659a1476c7a4897.15412247',//pubs //New
                            //'126351126659a1476c7a48d5.31555450',//pubs_addnl   
                            //'54328455559a1476c7a4901.74601924',//sharedWithTrucks //New
                            //'26705362759a1476c7a4941.39155723',//sharedWithTrucks_addnl 
                            
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                       // 'toilets',
                       // 'toilets_addnl',
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl //added on 23rd Feb
                        //'parking',
                        //'parking_addnl',
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '138295357859a130c0000034.04181816',//rvParkingAvailable
                        '71110796459a130c0000042.82981721',//rvParkingAvailable_addnl
                        '171017551359a130bff423e3.61654015',//rvTurnAroundArea
                        '124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl 
                        
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                        '68136230859a127611e9d10.16193837',//dumpPointAvailable
                        '28913861059a127611e9d32.89127897',//dumpPointAvailable_addnl
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl
                        '49519333159a128fb9e0098.57763366',//emergencyPhone //issue when editing record for low cost and have multiple tabs
                        '1264394359a128fb9e00c7.64748028',//emergencyPhone_addnl
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl//--Missing - [ait-mobilePhoneReception_addnl-option]
                        //ait-mobilePhoneReception_addnl-option should be ait-_ait-item-...petsNotOk...-option
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '144274401759a12a8077ceb2.98916116',//dryWeatherOnlyAccess
                        '137602637859a12a8077cee7.04884000',//dryWeatherOnlyAccess_addnl
                        '116178928259a12a8077cf25.08496002',//tentsOnly
                        '175033111259a12a8077cf64.35811428',//tentsOnly_addnl
                        '3896023559a12a8077cf93.09482080',//noTents  //New
                        '30743723259a12a8077cfd9.84645884',//noTents_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                        '139804642359a12e4cc7c613.03585852',//onSiteAccomodation //New
                        '104896684459a12e4cc7c631.61268353',//onSiteAccomodation_addnl
                        '138295357859a130c0000034.04181816',//rvParkingAvailable
                        '71110796459a130c0000042.82981721',//rvParkingAvailable_addnl
                        '171017551359a130bff423e3.61654015',//rvTurnAroundArea
                        '124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl  
                        '75487067159a130c0000063.47568166',//boatRamp
                        '86489403959a130c0000097.39746852',//boatRamp_addnl
                        '193829933459a130c00000b2.70881197',//timeLimitApplies
                        '91325718659a130c00000d7.05560623',//timeLimitApplies_addnl
                        '174008921159a130c00000f7.06865568',//shadedSites
                        '90219130559a130c0000126.15606332',//shadedSites_addnl
                        '208574398559a1319dced256.54856460',//firePit
                        '96932479159a1319dced2f4.48839598',//firePit_addnl
                        '157519193059a1319dced324.45146973',//noFirePit  //New
                        '92648312159a1319dced345.18145422',//noFirePit_addnl
                        '50095449759a1319dced360.35719174',//picnicTable
                        '208048102859a1319dced394.92664794',//picnicTable_addnl
                        '23268569959a1328cca76e8.15076099',//bbq
                        '155131766359a1328cca7837.02722797',//bbq_addnl
                        '136297802759a1328cca7895.14816330',//shelter
                        '5662200259a1328cca78d0.55880470',//shelter_addnl
                        '65335996059a1397ac69e61.16868042',//childrenPlayground
                        '70870383959a1397ac69fb9.22200738',//childrenPlayground_addnl
                        '31863726159a1397ac6a005.03158568',//views
                        '10736999859a1397ac6a056.62941562',//views_addnl
                        '52677997159a13cc46452e3.37345362',//parkFees //61
                        //new additions for role 3 added on Feb 4, 2015
                        '40375140559a13cc4645443.47962524', //campKitchen
                        '210513671559a13cc4645471.47839174',//campKitchen_addnl
                        '125526885259a13fc3432fe5.09981042',//laundry
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '127875872459a1419114ba21.47959230',//ensuiteSites
                        '166076809859a1419114bb04.88803687',//ensuiteSites_addnl
                        '191709818959a1419114bb22.17156129',//restaurant
                        '182069099159a1419114bb56.47084308',//restaurant_addnl
                        '48343349759a143b902b316.86257805',//kiosk
                        '104701077159a143b902b460.48418867',//kiosk_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        '146237113459a143b902b5c9.59301660',//gamesRoom
                        '73756936959a143b902b5f6.60751977',//gamesRoom_addnl
                        '197977266559a143b902b807.38776684',//generatorsPermitted
                        '184061923659a143b902b856.23529609',//generatorsPermitted_addnl
                        '126593474259a1476c7a45f1.63218688',//generatorsNotPermitted
                        '199408683159a1476c7a4747.07506530',//generatorsNotPermitted_addnl
                        '36497051859a1476c7a47a6.45322651',//showGrounds  //New
                        '71006354559a1476c7a47e7.28910931',//showGrounds_addnl
                        '23841355559a1476c7a4825.86443341',//nationalParknForestry    //New
                        '145462151159a1476c7a4862.15834507',//nationalParknForestry_addnl
                        '116373906659a1476c7a4897.15412247',//pubs //New
                        '126351126659a1476c7a48d5.31555450',//pubs_addnl 
                        
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl                       
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl                      
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl   //29
                        '125526885259a13fc3432fe5.09981042',//laundry
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                        
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl                      
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl                       
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        //'dryWeatherOnlyAccess',
                        //'dryWeatherOnlyAccess_addnl',     
                        //'tentsOnly',
                        //'tentsOnly_addnl',    //commented on 23rd Feb 2015
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl                       
                        '125526885259a13fc3432fe5.09981042',//laundry
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        
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
                        '160327751759a12cbd8e4eb2.99387079',//caravans //New
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '2118905308599ff84247c456.33090579',//toilets
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable    //New
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacilityAvailable
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                        '68136230859a127611e9d10.16193837',//dumpPointAvailable
                        '28913861059a127611e9d32.89127897',//dumpPointAvailable_addnl
                        '29615392559a128fb9dffa4.51040597',//poweredSites
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl
                        '49519333159a128fb9e0098.57763366',//emergencyPhone
                        '1264394359a128fb9e00c7.64748028',//emergencyPhone_addnl
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '144274401759a12a8077ceb2.98916116',//dryWeatherOnlyAccess
                        '137602637859a12a8077cee7.04884000',//dryWeatherOnlyAccess_addnl
                        '116178928259a12a8077cf25.08496002',//tentsOnly
                        '175033111259a12a8077cf64.35811428',//tentsOnly_addnl
                        '3896023559a12a8077cf93.09482080',//noTents  //New
                        '30743723259a12a8077cfd9.84645884',//noTents_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   //New
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl
                        '50860830159a12e4cc7c4e2.58547247',//bigrig
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                        '139804642359a12e4cc7c613.03585852',//onSiteAccomodation //New
                        '104896684459a12e4cc7c631.61268353',//onSiteAccomodation_addnl
                        '138295357859a130c0000034.04181816',//rvParkingAvailable
                        '71110796459a130c0000042.82981721',//rvParkingAvailable_addnl
                        '171017551359a130bff423e3.61654015',//rvTurnAroundArea
                        '124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl 
                        '75487067159a130c0000063.47568166',//boatRamp
                        '86489403959a130c0000097.39746852',//boatRamp_addnl
                        '193829933459a130c00000b2.70881197',//timeLimitApplies
                        '91325718659a130c00000d7.05560623',//timeLimitApplies_addnl
                        '174008921159a130c00000f7.06865568',//shadedSites
                        '90219130559a130c0000126.15606332',//shadedSites_addnl
                        '208574398559a1319dced256.54856460',//firePit
                        '96932479159a1319dced2f4.48839598',//firePit_addnl
                        '157519193059a1319dced324.45146973',//noFirePit  //New
                        '92648312159a1319dced345.18145422',//noFirePit_addnl
                        '50095449759a1319dced360.35719174',//picnicTable
                        '208048102859a1319dced394.92664794',//picnicTable_addnl
                        '23268569959a1328cca76e8.15076099',//bbq
                        '155131766359a1328cca7837.02722797',//bbq_addnl
                        '136297802759a1328cca7895.14816330',//shelter
                        '5662200259a1328cca78d0.55880470',//shelter_addnl
                        '65335996059a1397ac69e61.16868042',//childrenPlayground
                        '70870383959a1397ac69fb9.22200738',//childrenPlayground_addnl
                        '31863726159a1397ac6a005.03158568',//views
                        '10736999859a1397ac6a056.62941562',//views_addnl
                        '52677997159a13cc46452e3.37345362',//parkFees
                        '163208053459a13cc46453f2.99439357',//parkFees_addnl //in question
                        //new additions for role 7
                        '40375140559a13cc4645443.47962524', //campKitchen
                        '210513671559a13cc4645471.47839174',//campKitchen_addnl
                        '125526885259a13fc3432fe5.09981042',//laundry
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '127875872459a1419114ba21.47959230',//ensuiteSites
                        '166076809859a1419114bb04.88803687',//ensuiteSites_addnl
                        '191709818959a1419114bb22.17156129',//restaurant
                        '182069099159a1419114bb56.47084308',//restaurant_addnl
                        '48343349759a143b902b316.86257805',//kiosk
                        '104701077159a143b902b460.48418867',//kiosk_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        '146237113459a143b902b5c9.59301660',//gamesRoom
                        '73756936959a143b902b5f6.60751977',//gamesRoom_addnl
                        '197977266559a143b902b807.38776684',//generatorsPermitted
                        '184061923659a143b902b856.23529609',//generatorsPermitted_addnl
                        '126593474259a1476c7a45f1.63218688',//generatorsNotPermitted
                        '199408683159a1476c7a4747.07506530',//generatorsNotPermitted_addnl
                        '36497051859a1476c7a47a6.45322651',//showGrounds  //New
                        '71006354559a1476c7a47e7.28910931',//showGrounds_addnl
                        '23841355559a1476c7a4825.86443341',//nationalParknForestry    //New
                        '145462151159a1476c7a4862.15834507',//nationalParknForestry_addnl
                        '116373906659a1476c7a4897.15412247',//pubs //New
                        '126351126659a1476c7a48d5.31555450',//pubs_addnl  
                        
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
    //console.log(directory_7);
    <?php
        $freeCatParents = $businessCatParents = $lowCostParents = $houseSittingParents = array();
        $parkOverParents = $helpOutParents = $dollar25Plus = array();
    ?>
    function hideAllCat(catID){
        //console.log('hi');
        var catAppend = "ait-item-category-";
        var checkboxAppend = 'in-ait-item-category-';
        //alert('catID'+catID);
        <?php
        $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1, 'hide_empty' => 0));
        
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
        //echo'<pre>';print_r($Itemcats);die;
        foreach($Itemcats as $sngCat){
            //echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide()"; //minified
            //$hideDivs.="\n\tjQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').hide()";
            $hideDivs.="jQuery('#'+catAppend+'".$sngCat->cat_ID."').hide();";
            
            
            //get free cat parent id
            if(in_array($sngCat->name,$freeCat)){
                $freeCatParents[] = $sngCat->cat_ID;
            }
            //alert($freeCatParents);
            //get business listing parent ids
            if(in_array($sngCat->slug,$businessCat)){
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
        ?>
        //var div = <?php echo $freeCatParents ?>;
        //console.log(div);
    }
    
    //show free categories
    jQuery( "#directory_1" ).click(function() {
        //console.log('free');
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //hideAllCat('directory_1');
        //var a = <?php echo $freeCatParents; ?>;
        //console.log(a);
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            //echo'<pre>';print_r($freeCatParents);
            foreach($freeCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
                foreach($Itemcats as $sngCat){
                    echo "jQuery('#'+catAppend+'".$sngCat->cat_ID."').show();"; //minified
                    echo "jQuery('#'+checkboxAppend+'".$sngCat->cat_ID."').show();"; //minified
                }
            }
        ?>
        //console.log('free');
    });
    
    //show business categories
    jQuery( "#directory_2" ).click(function() {
        //console.log('buisness');
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //hideAllCat('directory_2');
        //in-popular-ait-item-category
        
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($businessCatParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_3');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n\n\t";
            foreach($lowCostParents as $catID){
                echo "jQuery('#'+catAppend+'".$catID."').show();"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_4');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($houseSittingParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_5');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($parkOverParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_6');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($helpOutParents as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        var catAppend = "ait-items-";
        var checkboxAppend = 'in-ait-items-';
        //in-popular-ait-item-category
        //hideAllCat('directory_7');
        <?php
            echo "\n".$showDivs;echo "\n".$hideDivs."\n";
            foreach($dollar25Plus as $catID){
                echo "\n\tjQuery('#'+catAppend+'".$catID."').show()"; //minified
                $Itemcats = get_categories( array( 'taxonomy' => 'ait-items', 'hierarchical ' => 1,'child_of' => $catID, 'hide_empty' => 0));           
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
        //console.log('hi');
        //return false;
        update_hidden_attribute(hash);
        //jQuery("h3:contains('Opening Hours')" ).hide();
        jQuery( "#find-address-button" ).show();
        switch(hash){
            case 'directory_1':
                //alert('hi1');
                filter_options(directory_1,'directory_1');
                
                jQuery("#tooltip_address" ).tooltip({content: directory_1_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_1_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_1_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_1_street_view,track:true});
                break;
            case 'directory_2':
                //alert('hi2');
                filter_options(directory_2,'directory_2');
                jQuery("h3:contains('Opening Hours')" ).show();

                jQuery("#tooltip_address" ).tooltip({content: directory_2_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_2_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_2_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_2_street_view,track:true});
                break;
            case 'directory_3':
                //alert('hi3');
                filter_options(directory_3,'directory_3');
                jQuery("#ait-_ait-item-emergencyPhone-option").show();
                jQuery("#tooltip_address" ).tooltip({content: directory_3_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_3_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_3_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_3_street_view,track:true});
                break;
            case 'directory_4':
                //alert('hi4');
                filter_options(directory_4,'directory_4');
                jQuery( "#find-address-button" ).hide();
                jQuery("#tooltip_address" ).tooltip({content: directory_4_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_4_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_4_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_4_street_view,track:true});
                break;
            case 'directory_5':
                //alert('hi5');
                filter_options(directory_5,'directory_5');
                jQuery("#ait-_ait-item-largeSizeMotohomeAccess-option").show();
                jQuery("#tooltip_address" ).tooltip({content: directory_5_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_5_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_5_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_5_street_view,track:true});
                jQuery('#ait-_ait-item-address-option').hide();
                jQuery('#ait-map-option').hide();
                jQuery('#ait-_ait-item-gpsLongitude-option').hide();
                jQuery('#ait-_ait-item-gpsLatitude-option').hide();
                jQuery('#ait-_ait-item-showStreetview-option').hide();
                jQuery('#find-address-button' ).hide();
                break;
            case 'directory_6':
                //alert('hi6');
                filter_options(directory_6,'directory_6');
                jQuery( "#find-address-button" ).hide();
                jQuery("#tooltip_address" ).tooltip({content: directory_6_address,track:true});
                jQuery("#tooltip_latitude" ).tooltip({content: directory_6_lattitude,track:true});
                jQuery("#tooltip_longitude" ).tooltip({content: directory_6_longitude,track:true});
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_6_street_view,track:true});
                break;
            case 'directory_7':
                //alert('hi7');
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
        //console.log(activeDirectory);
        jQuery("label:contains('Directory Type')" ).hide();
        //updating directory type on the basis of active tab only for new listing
        <?php
            if(true){ //$postID == 0
                //allowing user to update directory type after editing of list also
        ?>
        jQuery("input[name*='_ait-item_item-data[directoryType]']").val(activeDirectory);
        //jQuery("input[name=_ait-item_item-data[directoryType]]").val(activeDirectory);
            if (document.getElementsByName("_ait-item_item-data[directoryType]")) {
                //document.getElementsByName('_ait-item_item-data[directoryType]').setAttribute("value",activeDirectory);
                
                //document.getElementsByName('_ait-item_item-data[directoryType]').value = activeDirectory;
                //jQuery("input[name=_ait-item_item-data[directoryType]]").val(activeDirectory);
            }
             
            var directType = prepend+'directoryType';
            if (document.getElementById(directType)) {
                jQuery("#"+directType ).val(activeDirectory);
                jQuery("#"+directType ).hide();
            }
        <?php } ?>
        //return false;
    }
    //----------------------------Newly added on 31st Dec, 2014---------------
    
    //------------------------------------------------------
    function showAllOptions() {
        //console.log('hi');
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
        //console.log(temp);
    }
    
    function hideAllOptions(active_dir) {
        //console.log(active_dir);
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
                //console.log('hi22');
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+globalArr[index]+map_append;
            }else{
                temp = prepend+globalArr[index]+append;
                //console.log(temp);
            }
            console.log(temp);
            if (document.getElementById(temp)) {
                console.log('hi44');
                document.getElementById(temp).style.display = 'none';
            }
            
        }
        return false;
    }
    
    function filter_options(directory_arr, active_dir) {
        //console.log(active_dir);
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
        //console.log(t_directory);
        for(index = 0; index < t_directory.length; index++) {            
            //alert(active_dir + '')
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+t_directory[index]+map_append;
            }else{
                temp = prepend+t_directory[index]+append;
            }
            //console.log(temp);
            if (active_dir == 'directory_3' || active_dir == 'directory_7') {
                //alert(index + "<>" + temp);
                //ait-_ait-item-poweredSites-option => ait-poweredSites-option [Error]
                if (temp == 'ait-29615392559a128fb9dffa4.51040597-option') {
                    temp = 'ait-_ait-item-29615392559a128fb9dffa4.51040597-option';
                }
                console.log(index + "<>" + temp);
            }
            if ( active_dir == 'directory_5') {
                if(temp == 'ait-170509770559a12a8077cd57.63725733-option'){
                    temp = 'ait-_ait-item-170509770559a12a8077cd57.63725733-option';
                }
                if (temp == 'ait-107243141459a12b5c80e591.55167459-option') {
                    temp = 'ait-_107243141459a12b5c80e591.55167459-option';
                }
                console.log(index + "<>" + temp);
            }
            if ( active_dir == 'directory_6') {
                if(temp == 'ait-78130016459a12b5c80e4a0.15874597-option'){
                    temp = 'ait-_ait-item-78130016459a12b5c80e4a0.15874597-option';
                }
                console.log(index + "<>" + temp);
            }
            if ('24856028859a128fb9e0113.38611591' == t_directory[index]) {
                temp = prepend+t_directory[index]+append;
            }
            if ('16040096659a128fb9e0135.03414696' == t_directory[index]) {
                temp = prepend+t_directory[index]+append;
            }
            //chmod -R <permissionsettings> <dirname>
            //sudo chmod -R 755 yahoo_mail
            //console.log(temp);
            if (document.getElementById(temp)) {
                //console.log('working');
                document.getElementById(temp).style.display = 'block';
                document.getElementById(temp).style.display = '';
            }
            //console.log(temp);
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
            //alert('yd');
            //console.log(excluded_fields);
            for(index = 0; index < excluded_fields.length; index++) {
                if (excluded_fields[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    temp = map_prepend + excluded_fields[index] + map_append;
                }else{
                    temp = prepend + excluded_fields[index] + append;
                }
                //console.log(temp);
                if (document.getElementById(temp)) {
                    //console.log('hi');
                   document.getElementById(temp).style.display = '';
                }
            }
        }
        //console.log(temp);
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
        //console.log(firstTab);
        //jQuery("h3:contains('Opening Hours')" ).hide();
        var hash = window.location.hash.substring(1);
        //console.log(hash);
        if(hash == ""){hash = firstTab;}
        //console.log(hash);
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
        //return false;
    }
    getHash(<?php echo $firstTab;?>);
    jQuery( document ).ready(function() {
        if(firstTab == 'tabdirectory_4' || firstTab == 'tabdirectory_6'){
            jQuery( "#ait-map-option" ).hide();
            jQuery( "#find-address-button" ).hide();
        }  
    });
    <?php
    //echo $currentDirectory;die;
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