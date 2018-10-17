<html>
    <head>
        <title>FRC: Feature n Icons</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_misc/feature_images_conf.php');
    
    $featureArr = array(
                        array('2118905308599ff84247c456.33090579', 'Toilets Available', 'toiletsAvailable.png', 'Enter further details of the toilets if appropriate.  E.g. Drop Toilets or Flush Toilets.<hr> Mark this box if you have toilets available at your site. '),
                        array('603471443599ff84247c4e3.62317662', 'water filling Available', 'waterFillingAvailable.png', 'Enter further details of the facilities if appropriate.  E.g. Wheel Chair Ramps,  Footpaths etc.<hr> Mark this box if you have Disabled Facilities available at your site.'),
                        array('147536215359a1200c7db353.16518800', 'Disabled Facilities', 'disabledFacility.png'),
                        array('188168178159a120ec1b4bb7.28131025', 'Showers Available', 'showerAvailable.png'),
                        array('148620156659a12645e46256.51574107', 'Drinking Water Available', 'drinkingWaterAvailable.png'),
                        array('190842153859a12645e46339.94888894', 'Water Not Suitable For Drinking', 'waterNotSuitableForDrinking.png'),
                        array('39367108959a127611e9c53.40468292', 'Rubbish Bins Available', 'rubbishBinsAvailable.png'),
                        array('68136230859a127611e9d10.16193837', 'Dump Point Available', 'dumpPointAvailable.png'),
                        array('29615392559a128fb9dffa4.51040597', 'Power Available', 'powerAvailable.png'),
                        array('49519333159a128fb9e0098.57763366','Emergency Phone', 'emergencyPhone.png'),
                        array('13328266659a128fb9e00e9.74175245','Mobile Phone Reception', 'mobilePhoneReception.png'),
                        array('16040096659a128fb9e0135.03414696','Pets Permitted', 'petsPermitted.png'),
                        array('107191184659a128fb9e0180.04901523','No Pets Permitted','noPetsPermitted.png'),
                        array('170509770559a12a8077cd57.63725733', '4WD Drive Access Only', '4WD-Drive-Access-Only.png'),
                        array('144274401759a12a8077ceb2.98916116', 'Dry Weather Access', 'dryWeatherAccess.png'),
                        array('116178928259a12a8077cf25.08496002', 'Suitable for Tents', 'suitable4Tents.png'),
                        array('3896023559a12a8077cf93.09482080', 'Not Suitable for Tents', 'notSuitable4Tents.png'),
                        array('78130016459a12b5c80e4a0.15874597', 'Camper Trailers', 'camperTrailers.png'),
                        array('115463372059a12b5c80e573.50842424', 'No Camper Trailers', 'noCamperTrailers.png'),
                        array('160327751759a12cbd8e4eb2.99387079', 'Caravans', 'caravans.png'),
                        array('110690004459a12cbd8e4f47.26395654', 'Campervans  & Motorhomes', 'campervansMotorhomes.png'),
                        array('50860830159a12e4cc7c4e2.58547247', 'Big Rig Access', 'bigRigAccess.png'),
                        array('61155347359a12e4cc7c5c7.04852427', 'FSCV Only', 'fullySelfContainedVehiclesOnly.png'),
                        array('139804642359a12e4cc7c613.03585852', 'On Site Accommodation', 'onSiteAccomodation.png'),
                        array('50324633959a12e4cc7c650.85067044', 'Seasonal Rates Apply', 'seasonalRatesApply.png'),
                        array('88199375359a130bff422c8.58387692', 'Discounts Apply', 'discountsApply.png'),
                        array('211756746459a130bff423a7.89382957', 'Offers Apply', 'specialOffersApply.png'),
                        array('171017551359a130bff423e3.61654015', 'RV Turn Around Area', 'RVTurnAroundArea.png'),
                        array('138295357859a130c0000034.04181816', 'RV Parking Available', 'RVParkingAvailable.png'),
                        array('75487067159a130c0000063.47568166','Boat Ramp Nearby', 'boatRampNearby.png'),
                        array('193829933459a130c00000b2.70881197', 'Time Limits Apply', 'timeLimitsApply.png'),
                        array('174008921159a130c00000f7.06865568', 'Some Shade Available', 'someShadeAvailable.png'),
                        array('208574398559a1319dced256.54856460', 'Fires Permitted', 'firesPermitted.png'),
                        array('157519193059a1319dced324.45146973', 'No Fires Permitted', 'firesNotPermitted.png'),
                        array('50095449759a1319dced360.35719174', 'Picnic Table Available', 'picnicTableAvailable.png'),
                        array('23268569959a1328cca76e8.15076099', 'BBQ Available', 'BBQAvailable.png'),
                        array('136297802759a1328cca7895.14816330', 'Shelter Available', 'shelterAvailable.png'),
                        array('65335996059a1397ac69e61.16868042', 'Childrens Playground Available', 'childrensPlaygroundAvailable.png'),
                        array('31863726159a1397ac6a005.03158568', 'Nice Views From Location', 'niceViewsFromLocation.png'),
                        array('52677997159a13cc46452e3.37345362', 'National Park Fees', 'nationalParkFees.png'),
                        array('40375140559a13cc4645443.47962524', 'Camp Kitchen', 'campKitchen.png'),
                        array('125526885259a13fc3432fe5.09981042', 'Laundry', 'laundry.png'),
                        array('127875872459a1419114ba21.47959230', 'Ensuite Sites', 'ensuiteSites.png'),
                        array('191709818959a1419114bb22.17156129', 'Restaurant', 'restaurant.png'),
                        array('48343349759a143b902b316.86257805', 'Kiosk', 'kiosk.png'),
                        array('119912264859a143b902b4b4.95201159', 'Internet Access', 'internetAccess.png'),
                        array('19149342359a143b902b549.18456158', 'Swimming', 'swimming.png'),
                        array('146237113459a143b902b5c9.59301660', 'Games Room', 'gamesRoom.png'),
                        array('197977266559a143b902b807.38776684', 'Generators Permitted', 'generatorsPermitted.png'),
                        array('126593474259a1476c7a45f1.63218688', 'Generators Not Permitted', 'generatorsNot-Permitted.png'),
                        array('36497051859a1476c7a47a6.45322651', 'Show Grounds', 'showgrounds.png'),
                        array('23841355559a1476c7a4825.86443341', 'National Parks and Forestry', 'nationalParksForestry.png'),
                        array('116373906659a1476c7a4897.15412247', 'Pubs', 'pubs.png'),
                        array('54328455559a1476c7a4901.74601924', 'Shared With Trucks', 'sharedWithTrucks.png')
                    );
    $tooltip = array(
                     'Toilets Available' => 'Enter further details of the toilets if appropriate.  E.g. Drop Toilets or Flush Toilets.<hr> Mark this box if you have toilets available at your site. ',
                     'Disabled Facilities' => 'Enter further details of the facilities if appropriate.  E.g. Wheel Chair Ramps,  Footpaths etc.<hr> Mark this box if you have Disabled Facilities available at your site.',
                     'Showers Available' => 'Enter further details of the showers if appropriate.  E.g. Cold Water Only. <hr> Mark this box if you have Shower Facilities available at your site.',
                     'Drinking Water Available' => 'Enter further details if appropriate.  E.g. Limited amounts available, or Tank Water Only, or Boil First. <hr> Mark this box if you have clean Drinking Water available at your site.',
                     'RV Parking Available' => 'Enter further details if appropriate.  E.g. At rear of building. <hr> Mark this box if your site has adequate room for parking large RV or Caravans.',
                     'RV Turn Around Area' => 'Enter further details if appropriate.  E.g. Turn around area at rear of site. <hr> Mark this box if your site has adequate room for a large RV, Big Rig or Caravan to  maneuver in or turn around in',
                     'Nice Views From Location' => 'Enter further details if appropriate.  E.g. Views over valley or Sunset Views. <hr> Mark this box if you have nice views available from your site.',
                     'National Park Fees' => 'Enter details of park fee, e.g.  $7 per vehicle. <hr> Mark this box if extra fees are required to enter a National Park, these are surplus fees to any camping fees if applicable.',
                     'Camp Kitchen' => 'Enter further details if appropriate.  E.g. Ovens and Cutlery available. <hr> Mark this box if you have a camp kitchen available at your site.  This refers to a communal kitchen facility that offers cooking and dishwashing facilities.',
                     'Laundry' => 'Enter further details if appropriate.  E.g. Free washing machine, or Coin Operated Machines. < hr> Mark this box if you have laundry facilities available at your site and you wish to make them available.',
                     'Showers Available' => 'Enter further details of the showers if appropriate.  E.g. Cold Water Only. <hr> Mark this box if you have Shower Facilities available at your site.',
                     'Drinking Water Available' => 'Enter further details if appropriate.  E.g. Limited amounts available, or Tank Water Only, or Boil First. <hr> Mark this box if you have clean Drinking Water available at your site.',
                     'Water Not Suitable For Drinking' => 'Enter further details if appropriate.  E.g. Water from river, use for washing only. <hr>Mark this box if you have water available at your site but it is not suitable for Human Consumption.',
                     'Dump Point Available' => 'Enter further details if appropriate.  E.g. Good Access with vehicle, or Cassette Only. <hr>Mark this box if you have a Dump Point a available at your site.',
                     'Power Available' => 'Enter further details if appropriate.  E.g. 4 Powered sites, or $5 extra for Power. <hr>Mark this box if you have 240V Power available at your site.',
                     'Emergency Phone' =>'Enter further details if appropriate. <hr> Mark this box if you have an Emergency Phone available at your site.',
                     'Mobile Phone Reception' => 'Enter further details if appropriate.  E.g. Telstra Only, or Signal Intermittent. <hr> Mark this box if you know you have Mobile Phone Reception available at your site.',
                     'No Pets Permitted' => 'Enter further details of any conditions if appropriate.  E.g. Dogs Only, or On Leash, or Not Permitted on Ovals. <hr> Mark this box if you specially do NOT allow Pets at your site, and wish for users to know this in advance. E.g. National Parks',
                     '4WD Drive Access Only' => 'Enter further details if appropriate.  E.g. 4WD at all times, or 4WD Recommended in Wet weather. <hr> Mark this box if you can only access your site in a 4WD',
                     'Dry Weather Access' => 'Enter further details if appropriate.  E.g. Seasonal.<hr>Mark this box if you can only access your site in Dry Weather only.',
                     'Suitable for Tents' => 'Enter further details if appropriate.  E.g. No vehicle access due to remoteness. <hr> Mark this box if your site may only be used for Tents, i.e. No Caravans, Motorhome, Campervans or Camper Trailers.',
                     'Boat Ramp Nearby' => 'Enter further details if appropriate.  E.g. Boat Ramp across road. <hr>Mark this box if your site has a boat ramp on your site or nearby.',
                     'Time Limits Apply' => 'If you have marked the box above, then you will need to enter details in this field as to the time restriction that applies, eg, 24 Hours, or 6 weeks. <hr> Mark this box if your site has a time limit attached to its usage.  Eg, 24 Hour Limit only.',
                     'Fires Permitted' => 'Enter further details if appropriate.  E.g. BYO Wood or BYO Fire Pit or Only in Fire Pits. <hr> Mark this box if fires are permitted at your site.',
                     'BBQ Available' => 'Enter further details if appropriate.  E.g. Wood BBQ or Coin Operated. <hr> Mark this box if you have some form of BBQ facility available at your site.',
                     'Shelter Available' => 'Enter further details if appropriate.  E.g. Communal undercover area available. <hr>Mark this box if you have some form of shelter available at your site.  This could relate to a cover over a picnic or BBQ area, or a communal area that is under cover.',
                     'Childrens Playground Available' => 'Enter further details if appropriate.  E.g. Swings and Slide or Adventure Park nearby. <hr>Mark this box if you have some form of Children&#8217;s Playground available at your site.',
                     'Generators Permitted' => 'Enter further details if appropriate.  E.g. Between 10am - 4pm Only, or Not after 8pm. <hr> Mark this box if you permit the use of Generators at your site',
                     'Ensuite Sites' => 'Enter further details if appropriate.  E.g. Shower & Toilet beside site. <hr>Mark this box if you have ensuites available at your site.  This refers to individual or shared ensuites that members can utilise.  They may be communal or at each individual site.',
                     'Restaurant' => 'Enter further details if appropriate.  E.g. Meals available at Pub, or Meals Fri & Sat only. <hr> Mark this box if you have a Restaurant or Eatery available at or near your site.',
                     'Kiosk' => 'Enter further details if appropriate.  E.g. Some groceries available or snacks available. <hr>Mark this box if you have a Kiosk or Store available at or near your site.   This may include things to eat or grocery items.',
                     'Internet Access' => 'Enter further details if appropriate.  E.g. $2 per half hour or Free Wireless. <hr>Mark this box if you have a Internet Access available at or near your site.',
                     'Swimming' => 'Enter further details, e.g. In ground Pool, or Natural Waterhole. <hr>Mark this box if you have swimming available at your location.',
                     'Games Room' => 'Enter further details if appropriate.  E.g. Poker Machines or Kids Games and TV. <hr>Mark this box if you have a Games Room available at or near your site.   This may include a child&#8217;s play room or adult gaming room, but please stipulate in the field below.',
                     'Big Rig Access' => 'Enter further details if appropriate.  E.g. Good Clear Access, or Access is tight. <hr>Mark this box if you can access your site in a Big Rig.  Usually for rigs that are over 11 meters long',
                     'FSCV Only' => 'Enter further details if appropriate. <hr> Mark this box if your site only allows vehicles that are FSC, i.e. they have their own on board Toilets, Showers and holding facilities.',
                     'Offers Apply' => 'Enter details of your Offer here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.  Eg, Buy One get One Free or Free Travellers Pack <hr>Tick this box if you will be making an Offer to FRC Members.  This will allow a Offer Icon to appear in the preview window of our maps.',
                     'Generators Permitted' => 'Enter further details if appropriate.<hr> Enter further details if appropriate.  E.g. Between 10am - 4pm Only, or Not after 8pm',
                     'Generators Not Permitted' => 'Enter further details if appropriate.<hr> Mark this box if you do NOT permit the use of Generators at your site at any time.',
                     'National Park Fees' => 'Enter details of park fee, e.g.  $7 per vehicle. <hr>Mark this box if extra fees are required to enter a National Park, these are surplus fees to any camping fees if applicable.'
                    );
    $count = 0;
    $counter = 0;
    //echo "<pre>"; print_r($featureArr); die;
    foreach($featureArr as $sngArr){
        $rowCss = ($counter++ % 2) ? 'info':'active';
        $iconURL = WP_CONTENT_URL.DS."frc_misc".DS."icons".DS."30X30".DS.$sngArr[2];
        $largeIconURL = WP_CONTENT_URL.DS."frc_misc".DS."icons".DS."120X120".DS.$sngArr[2];
        $iconFeature = $sngArr[1];
        $Sr = ++$count;
        $tooltip_msg = ""; 
        foreach($tooltip as $tooltips => $value){ 
            if($tooltips == $sngArr[1]){
                $tooltip_msg = $value;
            }
        }
        $rowStr.="<tr class='$rowCss'>
                        <td>$Sr</td>
                        <td>$sngArr[0]</td>
                        <td>$iconFeature</td>
                        <td align='center' style='text-align='center'><img src='$iconURL' title='$iconFeature' alt='$iconFeature' /></td>
                        <td align='center' style='text-align='center'><img src='$largeIconURL' title='$iconFeature' alt='$iconFeature' /></td>
                        <td>$tooltip_msg</td>
                    </tr>";
    }
?>
    <h3 style='padding-left:20px;'>&raquo;&nbsp;Features N Icons</h3><br/>
    <table class='table' border='1' width='80%'>
        <tr>
            <td>Sr. No</td>
            <td>Feature Slug</td>
            <td>Feature</td>
            <td>Icon (30X30)</td>
            <td>Icon (120X120)</td>
            <td>Tooltip</td>
        </tr>
        <?php echo $rowStr;?>
    </table>
</body>
</html>