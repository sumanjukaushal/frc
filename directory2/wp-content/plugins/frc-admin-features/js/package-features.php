<script>
    var globalArr = [
                        'featured',                 //01
                        'Address',                  //address
                        'Suburb',                   //wlm_city
                        'State',                    //wlm_state
                        'Pin Code',                 //wlm_zip
                        'Latitude',                 //gpsLatitude
                        'Longitude',                //gpsLongitude
                        'Streetview',               //showStreetview
                        //'streetViewLatitude',     //06
                        //'streetViewLongitude',    //07
                        //'streetViewHeading',      //08
                        //'streetViewPitch',        //09
                        //'streetViewZoom',         //10
                        'Property Type',            //property_type
                        'Telephone',                //telephone + Additional telephone numbers
                        'Additional telephone numbers',
                        'Email',                    //email
                        'Show Email',
                        'Police Check Required',    //police_check_required
                        'Pet Minding Required',     //pet_minding_required
                        'pet_minding_addnl',        //pet_minding_addnl
                        'Additional telephone numbers',    //16
                        'Gardening',                //gardening
                        'Other',                    //other - 18
                        'Contact owner button',     //emailContactOwner - 19
                        'Web',                      //web - 20
                        'Web Link Label',
                        'Area/Region',               //location- 21 needs to be changed conflicting with ITEM locations (Area or Region or site)
                        'Entry/Route',                 //access - 22 needs to be changed conflicting with category - Camping Accessories, replace with Entry, Route, Approach
                        'Responsibility Authority', //responsible_authority - 23
                        'Discount',                 //discount- 24
                        'Discount Detail',          //discount_detail - 25
                        'Offer',                    //offer - 26
                        'Offer Detail',             //offer_detail - 27
                        'Season Rate Apply',        //seasonalRatesApply - New
                        'Season Rate Details',      //seasonalRatesApply_detail - New
                        //'pricing',                //28 Missing no need
                        'Pricing Detail',           //29 - pricing_detail
                        'Help Required',            //30 - help_required
                        'Start Date',               //31 - start_date
                        'End Date',                 //32 - end_date
                        'Duration',                 //33 - duration
                        //'map',                    //34 - map (Need to check)
                        'Booking Link',             //35 -booking_link
                        'Limit_Discount / Other_Usage',       //36 limit_discount confliction with Other 18 number can be replaced with extra usage
                        'Number of Uses per Month',       //37 per_month_uses
                        
                        
                        //Features
                        'Features',
                        '2118905308599ff84247c456.33090579',//toilets                               - 1
                        '373584023599ff84247c4c5.56749002',//toilets_addnl
                        '147536215359a1200c7db353.16518800',//disableFacility                       - 2
                        '113984673159a120ec1b4b16.87775211',//disableFacility_addnl
                        '188168178159a120ec1b4bb7.28131025',//showers                               - 3
                        '124299123159a120ec1b4be3.98669372',//showers_addnl
                        '148620156659a12645e46256.51574107',//drinkingWater                         - 4
                        '125877315659a12645e462f0.58533584',//drinkingWater_addnl
                        '190842153859a12645e46339.94888894',//drinkingWaterNotSuitable              - 5
                        '52292134859a12645e46355.93563211',//drinkingWaterNotSuitable_addnl
                        '603471443599ff84247c4e3.62317662',//waterFillingAvailable                  - 6
                        '189004494559a11e68aeda99.02701281',//waterFillingAvailable_addnl
                        '39367108959a127611e9c53.40468292',//wasteFacility Rubbish Bins Available   - 7
                        '153686457459a127611e9ce9.57421471',//wasteFacilityAvailable_addnl
                        '68136230859a127611e9d10.16193837',//dumpPointAvailable                     - 8
                        '28913861059a127611e9d32.89127897',//dumpPointAvailable_addnl
                        '29615392559a128fb9dffa4.51040597',//poweredSites                           - 9
                        '21656720059a128fb9e0062.02008131',//poweredSites_addnl
                        '49519333159a128fb9e0098.57763366',//emergencyPhone                         - 10
                        '1264394359a128fb9e00c7.64748028',//emergencyPhone_addnl
                        '13328266659a128fb9e00e9.74175245',//mobilePhoneReception                   - 11
                        '24856028859a128fb9e0113.38611591',//mobilePhoneReception_addnl
                        '16040096659a128fb9e0135.03414696',//petsOk                                 - 12                       
                        '31379692359a128fb9e0151.31927562',//petsOk_addnl
                        '107191184659a128fb9e0180.04901523',//petsNotOk                             - 13
                        '49968156359a128fb9e01a0.18166469',//petsNotOk_addnl
                        '170509770559a12a8077cd57.63725733',//fourWheelDrive                        - 14
                        '64647174859a12a8077ce50.23193234',//fourWheelDrive_addnl
                        '144274401759a12a8077ceb2.98916116',//dryWeatherOnlyAccess                  - 15
                        '137602637859a12a8077cee7.04884000',//dryWeatherOnlyAccess_addnl
                        '116178928259a12a8077cf25.08496002',//tentsOnly                             - 16
                        '175033111259a12a8077cf64.35811428',//tentsOnly_addnl
                        '3896023559a12a8077cf93.09482080',//noTents  New                            - 17
                        '30743723259a12a8077cfd9.84645884',//noTents_addnl
                        '78130016459a12b5c80e4a0.15874597',//camperTrailers   New                   - 18
                        '55980863759a12b5c80e543.42060245',//camperTrailers_addnl
                        '115463372059a12b5c80e573.50842424',//noCamperTrailers //New                - 19
                        '107243141459a12b5c80e591.55167459',//noCamperTrailers_addnl
                        '160327751759a12cbd8e4eb2.99387079',//caravans New                          - 20
                        '2571259859a12cbd8e4f05.91234818',//caravans_addnl
                        '110690004459a12cbd8e4f47.26395654',//largeSizeMotohomeAccess               - 21
                        '175843714459a12cbd8e4f81.95591183',//largeSizeMotohomeAccess_addnl (Campervans  & Motorhomes)
                        '50860830159a12e4cc7c4e2.58547247',//bigrig                                 - 22
                        '17003835859a12e4cc7c595.42517010',//bigrig_addnl
                        '61155347359a12e4cc7c5c7.04852427',//selfContainedVehicles FSCV             - 23
                        '5468162659a12e4cc7c5e7.29693645',//selfContainedVehicles_addnl
                        '139804642359a12e4cc7c613.03585852',//onSiteAccomodation New                - 24
                        '104896684459a12e4cc7c631.61268353',//onSiteAccomodation_addnl
                        '50324633959a12e4cc7c650.85067044',  //Seasonal Rates Apply - not a feature - 01     
                        '74889538559a12e4cc7c679.94960310',
                        '88199375359a130bff422c8.58387692', //Discounts Apply - not a feature       - 02
                        '42754214659a130bff42378.19913131',
                        '211756746459a130bff423a7.89382957', //Offers Apply - not a feature         - 03
                        '181871935159a130bff423c2.10971337',
                        '171017551359a130bff423e3.61654015',//rvTurnAroundArea                      - 25
                        '124476508959a130c0000006.28420622',//rvTurnAroundArea_addnl
                        '138295357859a130c0000034.04181816',//rvParkingAvailable                    - 26
                        '71110796459a130c0000042.82981721',
                        '75487067159a130c0000063.47568166',//boatRamp                               - 27
                        '86489403959a130c0000097.39746852',//boatRamp_addnl
                        '193829933459a130c00000b2.70881197',//timeLimitApplies                      - 28
                        '91325718659a130c00000d7.05560623',//timeLimitApplies_addnl
                        '174008921159a130c00000f7.06865568',//shadedSites                           - 29
                        '90219130559a130c0000126.15606332',//shadedSites_addnl
                        '208574398559a1319dced256.54856460',//firePit                               - 30
                        '96932479159a1319dced2f4.48839598',//firePit_addnl
                        '157519193059a1319dced324.45146973',//noFirePit                             - 31
                        '92648312159a1319dced345.18145422',//noFirePit_addnl
                        '50095449759a1319dced360.35719174',//picnicTable                            - 32
                        '208048102859a1319dced394.92664794',//picnicTable_addnl
                        '23268569959a1328cca76e8.15076099',//bbq                                    - 33
                        '155131766359a1328cca7837.02722797',//bbq_addnl
                        '136297802759a1328cca7895.14816330',//shelter                               - 34
                        '5662200259a1328cca78d0.55880470',//shelter_addnl
                        '65335996059a1397ac69e61.16868042',//childrenPlayground                     - 35
                        '70870383959a1397ac69fb9.22200738',//childrenPlayground_addnl
                        '31863726159a1397ac6a005.03158568',//views                                  - 36
                        '10736999859a1397ac6a056.62941562',//views_addnl
                        '52677997159a13cc46452e3.37345362',//parkFees                               - 37
                        '163208053459a13cc46453f2.99439357',//parkFees_addnl
                        '40375140559a13cc4645443.47962524', //campKitchen                           - 38
                        '210513671559a13cc4645471.47839174',//campKitchen_addnl
                        '125526885259a13fc3432fe5.09981042',//laundry                               - 39
                        '166530906359a13fc3433119.63771490',//laundry_addnl
                        '127875872459a1419114ba21.47959230',//ensuiteSites                          - 40
                        '166076809859a1419114bb04.88803687',//ensuiteSites_addnl
                        '191709818959a1419114bb22.17156129',//restaurant                            - 41
                        '182069099159a1419114bb56.47084308',//restaurant_addnl
                        '48343349759a143b902b316.86257805',//kiosk                                  - 42
                        '104701077159a143b902b460.48418867',//kiosk_addnl
                        '119912264859a143b902b4b4.95201159',//internetAccess                        - 43
                        '37626437259a143b902b500.08294143',//internetAccess_addnl
                        '19149342359a143b902b549.18456158',//swimmingPool                           - 44
                        '150271062859a143b902b580.66852110',//swimmingPool_addnl
                        '146237113459a143b902b5c9.59301660',//gamesRoom                             - 45
                        '73756936959a143b902b5f6.60751977',//gamesRoom_addnl
                        '197977266559a143b902b807.38776684',//generatorsPermitted                   - 46
                        '184061923659a143b902b856.23529609',//generatorsPermitted_addnl
                        '126593474259a1476c7a45f1.63218688',//generatorsNotPermitted                - 47
                        '199408683159a1476c7a4747.07506530',//generatorsNotPermitted_addnl
                        '36497051859a1476c7a47a6.45322651',//showGrounds                            - 48
                        '71006354559a1476c7a47e7.28910931',//showGrounds_addnl
                        '23841355559a1476c7a4825.86443341',//nationalParknForestry                  - 49
                        '145462151159a1476c7a4862.15834507',//nationalParknForestry_addnl
                        '116373906659a1476c7a4897.15412247',//pubs                                  - 50
                        '126351126659a1476c7a48d5.31555450',//pubs_addnl   
                        '54328455559a1476c7a4901.74601924',//sharedWithTrucks                       - 51
                        '26705362759a1476c7a4941.39155723',//sharedWithTrucks_addnl   
                        //--------------------                        
                        
                        //Business Features
                        'Business Features',
                        //Alternative Content
                        'Alternative Content',
                        'alternativeContent'
                    ];
</script>
<?php
    include('1-free-listing-features.php');
    include('2-business-listing-features.php');
    include('3-campground-features.php');
    include('4-housesitting-features.php');
    include('5-parkover-features.php');
    include('6-helpout-features.php');
    include('7-caravan-features.php');
?>