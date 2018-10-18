<script>
    console.log("fasdfsf");
<?php
    //http://www.tutorialspoint.com/jqueryui/jqueryui_tooltip.htm
    //http://gettopup.com/documentation
    //http://codecanyon.net/item/jquery-media-tooltip/1409069 paid
    //http://www.inabrains.com/tooltip/guide.html
    //https://css-tricks.com/play-button-youtube-and-vimeo-api/
    //http://www.classynemesis.com/projects/ytembed/ [embed code generator]
    //http://www.jqueryscript.net/lightbox/jQuery-Lightbox-Plugin-For-Youtube-Videos-Video-Lightning.html
    //http://lab.abhinayrathore.com/jquery_youtube/#DemoCode
    
    $contentURL = content_url();
    $toottipImage = $contentURL."/uploads/sites/3/2014/04/facility_icons/question_frame.png";
    
    $tooltipAddress = "Place your address in this field in the following format:  Street Number (If available) Street Name, Suburb/Town and State.  Eg.  15 Smith Street, Cooroy, Qld This will allow your GPS Co-Ordinates to be automatically located by Google Maps.  In the event you do not have an exact street number, you can enter more accurate details in the GPS Section Below.";
    
    $tooltipAddress = "Place your address in this field in the following format:  Street Number (If available) Street Name eg 54 Simpson Street or Bruce Highway etc";
    
    $tooltipBusinessAddress = "Place your address in this field in the following format:  Street Number (If available) Street Name eg 54 Simpson Street";
    
    $tooltipWLMCity = "Place your suburb/city in this field";
    $tooltipWLMState = "Place your state in this field";
    $tooltipWLMZip = "Place your postcode in this field";
    
    $tooltipPropertyType = "Enter a brief description here of your type of Property.  Eg, Farm House, Residential  or Unit";
    //$tooltipGpsLatitude = "<table><tr><td>Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.</td></tr><tr><td><iframe width='230' height='160' src='https://www.youtube.com/embed/Slm_YyYz68k' frameborder='0' allowfullscreen></iframe></td></tr></table>";
    
    $tooltipGpsLatitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    
    $tooltipGpsLongitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    
    $tooltipTelephone = "<table><tr><td>Enter your contact number here if you wish to be contacted by our members.  Leave blank if you do not want your number to appear in your listing.</td></tr></table>";
//<tr><td><iframe width='230' height='160' src='http://www.youtube.com/embed/3ZJ5K0zo7dc?autoplay=1&fs=0' frameborder='0' allowfullscreen></iframe></td></tr>
    
    $tooltipEmail = "Enter your preferred email address here.  Anything entered in this filed will be displayed in the listing.  Leave blank if you do not wish to display your email address.";
    $tooltipContactOwner = "Place a tick in this field if you wish for a Contact Owner Button to appear in your listing.  The button will appear just below your feature photo or icon. This will activate a form that members can fill out to contact you direct.";
    $tooltipWeb = "Enter your preferred web address here.  Anything entered in this field will be displayed in the listing.  Format Eg, www.freerangecamping.com.au do not use http:// before the URL";
    $tooltipDiscount = "Tick this box if you will be offering a Discount to FRC Members.  This will allow a Discount Icon to appear in the preview window of our maps. ";
    $tooltipDiscountDetail = "Enter details of your discount here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.  Eg, 10% Discount Storewide";
    $tooltipOffer = "Tick this box if you will be making an Offer to FRC Members.  This will allow a Offer Icon to appear in the preview window of our maps.";
    $tooltipOfferDetail = "Enter details of your Offer here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.  Eg, Buy One get One Free or Free Travellers Pack";
    
    $tooltipSpecialOffer = "Tick this box if you will be making an Special Offer to FRC Members.  This will allow a Special Offer Icon to appear in the preview window of our maps."; //new
    $tooltipSpecialOfferDetail = "Enter details of your Special Offer here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.";
    
    $tooltipLimitDiscount = "Place a tick in this box only if you wish to limit how many times a member may use this discount or offer at your business. This will allow us to place restrictions on the member card that will monitor its usage.";
    $tooltipUsesPerMonth = "Only use this field if you have placed a tick in the Limit Discount box above.  Enter the number of times you wish to allow a member to use the discount in your business per calendar month.  Eg.  If you wish for a member to only use it 5 times in a month.  Simply enter the number &#8216;5&#8217;";
    
    $tooltipCaravans = "Mark this box if caravans available at your site."; //New
    $tooltipCaravansAddnl = "Enter further details of the caravans availability.";
    
    $tooltipToiletsAvailable = "Mark this box if you have toilets available at your site."; //1
    $tooltipToiletsAvailableAddnl = "Enter further details of the toilets if appropriate.  E.g. Drop Toilets or Flush Toilets";
    
    $tooltipDisabledFacility = "Mark this box if you have Disabled Facilities available at your site."; //2
    $tooltipDisabledFacilityAddnl = "Enter further details of the facilities if appropriate.  E.g. Wheel Chair Ramps,  Footpaths etc.";
    
    $tooltipShowers = "Mark this box if you have Shower Facilities available at your site."; //3
    $tooltipShowersAddnl = "Enter further details of the showers if appropriate.  E.g. Cold Water Only";
    
    $tooltipDrinkingWater = "Mark this box if you have clean Drinking Water available at your site."; //4
    $tooltipDrinkingWaterAddnl = "Enter further details if appropriate.  E.g. Limited amounts available, or Tank Water Only, or Boil First";
    
    $tooltipRVParking = "Mark this box if your site has adequate room for parking large RV or Caravans.";
    $tooltipRVParkingAddnl = "Enter further details if appropriate.  E.g. At rear of building";
    
    $tooltipRVTurnAroundArea = "Mark this box if your site has adequate room for a large RV, Big Rig or Caravan to maneuver in or turn around in";
    $tooltipRVTurnAroundAreaAddnl = "Enter further details if appropriate.  E.g. Turn around area at rear of site.";
    
    $tooltipShowStreetView = "This field allows us to show a Street view of your property in the More Information window.  Tick this box if you wish to use this option.  NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    $tooltipResponsibilityAuthority = "Enter the full details of your Organisation or Entity in this field.  This field advises users who the Responsible entity for this site is.  Eg, Property Owner or National Parks";
    
    $tooltipNiceViews = "Mark this box if you have nice views available from your site.";
    $tooltipNiceViewsAddnl = "Enter further details if appropriate.  E.g. Views over valley or Sunset Views";
    
    $tooltipNationalParkFees = "Mark this box if extra fees are required to enter a National Park, these are surplus fees to any camping fees if applicable.";
    $tooltipNationalParkFeesAddnl = "Enter details of park fee, e.g.  $7 per vehicle.";
    
    $tooltipPoliceCheck = "Mark this box if you require that your applicants must have a current National Police Clearance certificate.";
    
    $tooltipPetMinding = "Mark this box if you have pets or animals on your property that will need looking after.";
    $tooltipPetMindingAddnl = "Additonal Information";
    
    $tooltipGardening = "Mark this box if you have gardens, plants or yards that will require care and maintenance.";
    
    $tooltipOther = "Enter details in this field of any other requirements that will need attending to in your absence.";
    
    $tooltipPricingDetail = "This field is for you to enter brief details of pricing or fees at your site.  Keep details in this field brief.  Here you should enter your best prices for users to see.  If your pricing model is detailed or complex, you can elaborate in the description area or use the booking link below to direct users direct to your pricing page or booking page. Eg, $32 Unpowered Site";
    
    $tooltipStartDate = "Use the field and calendar below to enter in the date you wish your applicant to start looking after your property.   This date will appear in your preview window.";
    
    $tooltipEndDate = "Use the field and calendar below to enter in the date you wish your applicant to stop looking after your property. This date will appear in your preview window. ";
    
    $tooltipDuration = "Enter the number of days you will require your property to be cared for, E.g.  6 Days";
    
    $tooltipCampKitchen = "Mark this box if you have a camp kitchen available at your site.  This refers to a communal kitchen facility that offers cooking and dishwashing facilities.";    
    $tooltipCampKitchenAddnl = "Enter further details if appropriate.  E.g. Ovens and Cutlery available.";
    
    $tooltipLaundry = "Mark this box if you have laundry facilities available at your site and you wish to make them available.";
    $tooltipLaundryAddnl = "Enter further details if appropriate.  E.g. Free washing machine, or Coin Operated Machines";
    
    $tooltipHelpRequired = "Enter Summary details of the Help you need in here.  Information in this field will populate the preview box so should be brief.  You can elaborate in your description.  Eg, Farm Fencing, or Planting or Cleaning";
    
    $tooltipBookingLink = "This field is for your direct access to the booking link page of your web site if you have one.  Simply past the URL from your booking page into this field and we will embed it for you.";
    
    $tooltipWaterNotSuitable = "Mark this box if you have water available at your site but it is not suitable for Human Consumption.";
    $tooltipWaterNotSuitableAddnl = "Enter further details if appropriate.  E.g. Water from river, use for washing only.";
    
    $tooltipWaterFillingAvailable = "Mark this box if you have water filling available at your site."; //New
    $tooltipWaterFillingAvailableAddnl = "Enter further details if appropriate.";
    
    $tooltipWasteFacility = "Mark this box if you have waste or garbage bins available at your site.  This section is NOT for Dump Points or Gray Water.  See Dump Point Available for this.";
    $tooltipWasteFacilityAddnl = "Enter further details if appropriate.  E.g. Please do not overfill, take excess waste with you.";
    
    $tooltipDumpPoint = "Mark this box if you have a Dump Point a available at your site.";
    $tooltipDumpPointAddnl = "Enter further details if appropriate.  E.g. Good Access with vehicle, or Cassette Only";
    
    $tooltipPower = "Mark this box if you have 240V Power available at your site.";
    $tooltipPowerAddnl = "Enter further details if appropriate.  E.g. 4 Powered sites, or $5 extra for Power";
    
    $tooltipEmergencyPhone = "Mark this box if you have an Emergency Phone available at your site.";
    $tooltipEmergencyPhoneAddnl = "Enter further details if appropriate.";
    
    $tooltipMobilePhoneReception = "Mark this box if you know you have Mobile Phone Reception available at your site.";
    $tooltipMobilePhoneReceptionAddnl = "Enter further details if appropriate.  E.g. Telstra Only, or Signal Intermittent";
    
    $tooltipPets = "Mark this box if you allow Pets at your site.";
    $tooltipPetsAddnl = "Enter further details of any conditions if appropriate.  E.g. Dogs Only, or On Leash, or Not Permitted on Ovals.";
    
    $tooltipNoPets = "Mark this box if you specially do NOT allow Pets at your site, and wish for users to know this in advance. E.g. National Parks";
    $tooltipNoPetsAddnl = "Enter further details of any conditions if appropriate.  E.g. Guide Dogs permitted, or National Park";
    
    $tooltip4WD = "Mark this box if you can only access your site in a 4WD";
    $tooltip4WDAddnl = "Enter further details if appropriate.  E.g. 4WD at all times, or 4WD Recommended in Wet weather.";
    
    $tooltipDryWeather = "Mark this box if you can only access your site in Dry Weather only.";
    $tooltipDryWeatherAddnl = "Enter further details if appropriate.  E.g. Seasonal";
    
    $tooltipTentsOnly = "Mark this box if your site may only be used for Tents, i.e. No Caravans, Motorhome, Campervans or Camper Trailers.";
    $tooltipTentsOnlyAddnl = "Enter further details if appropriate.  E.g. No vehicle access due to remoteness";
    
    $tooltipNoTents = "Mark this box if no Tents are available"; //New
    $tooltipNoTentsAddnl = "Enter further details if appropriate.";
    
    $tooltipCamperTrailers = "Mark this box if camper trailers are available"; //New
    $tooltipCamperTrailersAddnl = "Enter further details if appropriate.";
    
    $tooltipNoCamperTrailers = "Mark this box if no camper trailers are available"; //New
    $tooltipNoCamperTrailersAddnl = "Enter further details if appropriate.";
    
    $largeSizeMotor = "Mark this box if you can access your site in a motorhome & caravan access.  Usually defined in size between 8 to 11 meters.";
    $largeSizeMotorAddnl = "Enter further details if appropriate.  E.g. Good Clear Access, or Access is tight.";
    
    $tooltipBigRig = "Mark this box if you can access your site in a Big Rig.  Usually for rigs that are over 11 meters long";
    $tooltipBigRigAddnl = "Enter further details if appropriate.  E.g. Good Clear Access, or Access is tight.";
    
    $tooltipFullySelfContained = "Mark this box if your site only allows vehicles that are FSC, i.e. they have their own on board Toilets, Showers and holding facilities.";
    $tooltipFullySelfContainedAddnl = "Enter further details if appropriate.";
    
    $tooltipOnSiteAccomodation = "Mark this box if on site accomodation is available at your site."; //New
    $tooltipOnSiteAccomodationAddnl = "Enter further details if appropriate.";
    
    $tooltipBoatRamp = "Mark this box if your site has a boat ramp on your site or nearby.";
    $tooltipBoatRampAddnl = "Enter further details if appropriate.  E.g. Boat Ramp across road";
    
    $tooltipTimeLimit = "Mark this box if your site has a time limit attached to its usage.  Eg, 24 Hour Limit only.";
    $tooltipTimeLimitAddnl = "If you have marked the box above, then you will need to enter details in this field as to the time restriction that applies, eg, 24 Hours, or 6 weeks";
    
    $tooltipShades = "Mark this box if you have some shaded sites available at your site.";
    $tooltipShadesAddnl = "Enter further details if appropriate.  E.g. Some trees scattered throughout site.";
    
    $tooltipFiresPermitted = "Mark this box if fires are permitted at your site.";
    $tooltipFiresPermittedAddnl = "Enter further details if appropriate.  E.g. BYO Wood or BYO Fire Pit or Only in Fire Pits";
    
    $tooltipNoFiresPermitted = "Mark this box if fires are not permitted at your site."; //New
    $tooltipNoFiresPermittedAddnl = "Enter further details if appropriate.";
    
    $tooltipPicnicTable = "Mark this box if you have picnic tables available at your site.";
    $tooltipPicnicTableAddnl = "Enter further details if appropriate.  E.g. 3 tables available";
    
    $tooltipBBQ = "Mark this box if you have some form of BBQ facility available at your site.";
    $tooltipBBQAddnl = "Enter further details if appropriate.  E.g. Wood BBQ or Coin Operated";
    
    $tooltipShelter = "Mark this box if you have some form of shelter available at your site.  This could relate to a cover over a picnic or BBQ area, or a communal area that is under cover.";
    $tooltipShelterAddnl = "Enter further details if appropriate.  E.g. Communal undercover area available.";
    
    $tooltipChildrenPlayground = "Mark this box if you have some form of Children&#8217;s Playground available at your site.";
    $tooltipChildrenPlaygroundAddnl = "Enter further details if appropriate.  E.g. Swings and Slide or Adventure Park nearby";//&#146
    
    $tooltipGeneratorsPermitted = "Mark this box if you permit the use of Generators at your site";
    $tooltipGeneratorsPermittedAddnl = "Enter further details if appropriate.  E.g. Between 10am - 4pm Only, or Not after 8pm";
    
    $tooltipGeneratorsNotPermitted = "Mark this box if you do NOT permit the use of Generators at your site at any time.";
    $tooltipGeneratorsNotPermittedAddnl = "Enter further details if appropriate.";
    
    $tooltipShowGrounds = "Show Grounds"; //New
    $tooltipShowGroundsAddnl = "Show Ground details";
    
    $tooltipNationalParknForestry = "National Parks and Forestry"; //New
    $tooltipNationalParknForestryAddnl = "National Parks and Forestry Details";
    
    $tooltipPubs = "Pubs"; //New
    $tooltipPubs = "Pubs Details";
    
    $tooltipPubs = "Pubs"; //New
    $tooltipPubs = "Pubs Details";
    
    $tooltipSharedWithTrucks = "Shared with Trucks"; //New
    $tooltipSharedWithTrucksPubs = "Shared with Trucks Details";
    
    $tooltipEnsuiteSites = "Mark this box if you have ensuites available at your site.  This refers to individual or shared ensuites that members can utilise.  They may be communal or at each individual site.";
    $tooltipEnsuiteSitesAddnl = "Enter further details if appropriate.  E.g. Shower & Toilet beside site.";
    
    $tooltipRestaurant = "Mark this box if you have a Restaurant or Eatery available at or near your site. ";
    $tooltipRestaurantAddnl = "Enter further details if appropriate.  E.g. Meals available at Pub, or Meals Fri & Sat only.";
    
    $tooltipKiosk = "Mark this box if you have a Kiosk or Store available at or near your site.   This may include things to eat or grocery items.";
    $tooltipKioskAddnl = "Enter further details if appropriate.  E.g. Some groceries available or snacks available.";
    
    $tooltipInternetAccess = "Mark this box if you have a Internet Access available at or near your site.";
    $tooltipInternetAccessAddnl = "Enter further details if appropriate.  E.g. $2 per half hour or Free Wireless";
    
    $tooltipSwimmingPool = "Mark this box if you have swimming available at your location.";
    $tooltipSwimmingPoolAddnl = "Enter further details, e.g. In ground Pool, or Natural Waterhole";
    
    $tooltipGamesRoom = "Mark this box if you have a Games Room available at or near your site.   This may include a child&#8217;s play room or adult gaming room, but please stipulate in the field below.";
    $tooltipGamesRoomAddnl = "Enter further details if appropriate.  E.g. Poker Machines or Kids Games and TV";
    
    //Miscellenous
    $tooltipItemCategories = "Place a tick in the box that relates to your Item Category.  This will determine in what category your item appears";
    $tooltipItemLocations = "This area allows you to nominate the region for your site.  It is important you select the correct region so that you appear on the Map.  In each case you will need to select your State, Region, then Sub Region or Town if applicable.  Eg, NSW, Illawarra, Kiama.  You would need to place a tick in all three boxes.";
    $tooltipDirectory1 = "Choose this Tab to enter or edit details for your Free Camp Listing. The Tab relates to Free Camps, Dump Points & Rest Areas";
    $tooltipDirectoryGallery = "This area allows you to upload images of your site to your listing.  There are a total of 5 free photos permitted per listing.  Please keep images to a Maximum size of 1024 pixels by 768 pixels and no larger than 1MB.  If possible please name your photos with titles that reflect your listing title";
    
    $tooltip_Location = "Remove this for owner Remove this for owner Remove this for owner ";
    $tooltip_Access = "Remove this for owner Remove this for owner Remove this for owner ";
    
    $toolTipArr = array(
                        //Options for Item
                        
                        array('tooltip_address','Address', $tooltipAddress, 'label'),
                        array('tooltip_city','Suburb', $tooltipWLMCity, 'label'),
                        array('tooltip_state','State', $tooltipWLMState, 'label'),
                        array('tooltip_zip','Post Code', $tooltipWLMZip, 'label'),
                        array('tooltip_latitude','GPS Latitude',$tooltipGpsLatitude,'label',null,'video'),
                        array('tooltip_longitude','GPS Longitude', $tooltipGpsLongitude, 'label'),
                        array('tooltip_show_streetview', 'Show Streetview instead of the map in detail' , $tooltipShowStreetView, 'td'),
                       /* 'StreetView Latitude' => '',
                        'StreetView Longitude' => '',
                        'StreetView Heading' => '',
                        'StreetView Pitch' => '',
                        'StreetView Zoom' => '',*/
                        array('tooltip_property_type', 'Property Type',$tooltipPropertyType),
                        array('tooltip_telephone', 'Telephone',$tooltipTelephone),
                        array('tooltipLocation', 'Location' , $tooltip_Location),
                        array('tooltipAccess' , 'Access', $tooltip_Access),
                        array('tooltip_email', 'Email', $tooltipEmail),
                        array('tooltip_police_chk', 'Police Check Required', $tooltipPoliceCheck, 'td'),
                        
                        array('tooltip_pet_minding', 'Pet Minding Required', $tooltipPetMinding, 'td'),
                        array('tooltip_pet_minding_addnl', 'Additional Information', $tooltipPetMindingAddnl, 'label', 'ait-_ait-dir-item-pet_minding_addnl'),
                        
                        array('tooltip_gardening', 'Gardening' , $tooltipGardening),
                        array('tooltip_other', 'Other' , $tooltipOther),
                        array('tooltip_contact_owner', 'Contact owner button (form)' , $tooltipContactOwner, 'td'),
                        array('tooltip_web', 'Web' , $tooltipWeb),
                        
                        
                        
                        array('tooltip_res_auth', 'Responsible Authority' , $tooltipResponsibilityAuthority),
                        array('tooltip_discnt', 'Discount' , $tooltipDiscount,'td'),
                        array('tooltip_discnt_det', 'Discount details' , $tooltipDiscountDetail),
                        array('tooltip_offer', 'Offer' , $tooltipOffer,'td'),
                        array('tooltip_offer_det', 'Offer details' , $tooltipOfferDetail),
                        array('tooltip_lt_discnt', 'Limit Discount/Other Usage' , $tooltipLimitDiscount, 'td'), 
                        array('tooltip_user_pm', 'Number of Uses per Month' , $tooltipUsesPerMonth),
                        //array('tooltip_pricing', 'Pricing' , $tooltipPricing,'td'),
                        array('tooltip_pricing_detail', 'Pricing details' , $tooltipPricingDetail,'label'),
                        array('tooltip_bookingLnk','Booking Link' , $tooltipBookingLink),
                        array('tooltip_help_required', 'Help Required' , $tooltipHelpRequired),
                        array('tooltip_st_date', 'Start Date' , $tooltipStartDate),
                        array('tooltip_en_date', 'End Date' , $tooltipEndDate),
                        array('tooltip_duration', 'Duration' , $tooltipDuration),
                        
                        //features
                        array('tooltip_toilets', 'Toilets Available' , $tooltipToiletsAvailable,'td'), //1
                        array('tooltip_toilets_addnl', 'Additional Information' , $tooltipToiletsAvailableAddnl,'label','ait-_ait-dir-item-toilets_addnl'),//1
                        
                        array('tooltip_disabled', 'Disabled Facilities' , $tooltipDisabledFacility,'td'), //2
                        array('tooltip_disabled_addnl', 'Additional Information' , $tooltipDisabledFacilityAddnl,'label', 'ait-_ait-dir-item-disableFacility_addnl'), //2
                        
                        array('tooltip_showers', 'Showers Available' , $tooltipShowers,'td'), //3
                        array('tooltip_showers_addnl', 'Additional Information' , $tooltipShowersAddnl,'label', 'ait-_ait-dir-item-showers_addnl'), //3
                        
                        array('tooltip_drkng_water', 'Drinking Water Available' , $tooltipDrinkingWater,'td'), //4
                        array('tooltip_drkng_water_addnl', 'Additional Information' , $tooltipDrinkingWaterAddnl, 'label', 'ait-_ait-dir-item-drinkingWater_addnl'),//4
                        
                        array('tooltip_water_not_suitable', 'Water Not Suitable For Drinking' , $tooltipWaterNotSuitable,'td'), //5
                        array('tooltip_water_not_suitable_addnl', 'Additional Information' , $tooltipWaterNotSuitableAddnl, 'label', 'ait-_ait-dir-item-drinkingWaterNotSuitable_addnl'), //5
                        
                        array('tooltip_water_not_suitable', 'Water Filling Available' , $tooltipWaterFillingAvailable,'td'), //6
                        array('tooltip_water_not_suitable_addnl', 'Additional Information' , $tooltipWaterFillingAvailableAddnl, 'label', 'ait-_ait-dir-item-waterFillingavailable_addnl'), //6
                        
                        array('tooltip_waste_facility', 'Rubbish Bins Available' , $tooltipWasteFacility,'td'), //7
                        array('tooltip_waste_facility_addnl', 'Additional Information' , $tooltipWasteFacilityAddnl, 'label', 'ait-_ait-dir-item-wasteFacilityAvailable_addnl'), //7
                        
                        array('tooltip_dmp_pt', 'Dump Point Available' , $tooltipDumpPoint), //8
                        array('tooltip_dmp_pt_addnl', 'Additional Information' , $tooltipDumpPointAddnl, 'label', 'ait-_ait-dir-item-dumpPointAvailable_addnl'), //8
                        
                        array('tooltip_pwer', 'Power Available' , $tooltipPower), //9
                        array('tooltip_pwer_addnl', 'Additional Information' , $tooltipPowerAddnl, 'label', 'ait-_ait-dir-item-poweredSites_addnl'),//9
                        
                        array('tooltip_emer_ph', 'Emergency Phone' , $tooltipEmergencyPhone), //10
                        array('tooltip_emer_ph_addnl', 'Additional Information' , $tooltipEmergencyPhoneAddnl, 'label', 'ait-_ait-dir-item-emergencyPhone_addnl'), //10
                        
                        array('tooltip_ph_recep', 'Mobile Phone Reception' , $tooltipMobilePhoneReception), //11
                        array('tooltip_ph_recep_addnl', 'Additional Information' , $tooltipMobilePhoneReceptionAddnl, 'label', 'ait-_ait-dir-item-mobilePhoneReception_addnl'), //11
                        
                        array('tooltip_pets_perm', 'Pets Permitted' , $tooltipPets), //12
                        array('tooltip_pets_perm_addnl', 'Additional Information' , $tooltipPetsAddnl, 'label', 'ait-_ait-dir-item-petsOk_addnl'), //12
                        
                        array('tooltip_no_pets', 'No Pets Permitted' , $tooltipNoPets), //13
                        array('tooltip_no_pets_addnl', 'Additional Information' , $tooltipNoPetsAddnl, 'label', 'ait-_ait-dir-item-petsNotOk_addnl'), //13
                        
                        array('tooltip_4wd_access', '4WD Drive Access Only' , $tooltip4WD, 'td'), //14
                        array('tooltip_4wd_access_addnl', 'Additional Information' , $tooltip4WDAddnl, 'label', 'ait-_ait-dir-item-fourWheelDrive_addnl'), //14
                        
                        array('tooltip_access_dry', 'Access Dry Weather Only' , $tooltipDryWeather, 'td'), //15
                        array('tooltip_access_dry_addnl', 'Additional Information' , $tooltipDryWeatherAddnl, 'label', 'ait-_ait-dir-item-dryWeatherOnlyAccess_addnl'), //15
                        
                        array('tooltip_tents_only', 'Suitable for Tents' , $tooltipTentsOnly,'td'), //16
                        array('tooltip_tents_only_addnl', 'Additional Information' , $tooltipTentsOnlyAddnl, 'label', 'ait-_ait-dir-item-tentsOnly_addnl'), //16
                        
                        array('tooltip_no_tents', 'Not Suitable for Tents' , $tooltipNoTents,'td'), //17
                        array('tooltip_no_tents_addnl', 'Additional Information' , $tooltipNoTentsAddnl, 'label', 'ait-_ait-dir-item-noTents_addnl'), //17
                        
                        array('tooltip_campers', 'Camper Trailers' , $tooltipCamperTrailers,'td'), //18
                        array('tooltip_campers_addnl', 'Additional Information' , $tooltipCamperTrailersAddnl, 'label', 'ait-_ait-dir-item-camperTrailers_addnl'), //18
                        
                        array('tooltip_no_campers', 'No Camper Trailers' , $tooltipNoCamperTrailers,'td'), //19
                        array('tooltip_no_tents_addnl', 'Additional Information' , $tooltipNoCamperTrailersAddnl, 'label', 'ait-_ait-dir-item-noCamperTrailers_addnl'), //19
                        
                        array('tooltip_caravans', 'Caravans' , $tooltipCaravans,'td'), //20
                        array('tooltip_caravans_addnl', 'Additional Information' , $tooltipCaravansAddnl,'label','ait-_ait-dir-item-caravans_addnl'), //20
                        
                        array('tooltip_large_mtr', 'Camper Vans & Motor Homes' , $largeSizeMotor, 'td'), //21
                        array('tooltip_large_mtr_addnl', 'Additional Information' , $largeSizeMotorAddnl, 'label', 'ait-_ait-dir-item-largeSizeMotohomeAccess_addnl'),//21
                        
                        array('tooltip_bigrig', 'Big Rig Access' , $tooltipBigRig, 'td'),//22
                        array('tooltip_bigrig_addnl', 'Additional Information' , $tooltipBigRigAddnl, 'label', 'ait-_ait-dir-item-bigrig_addnl'),//22
                        
                        array('tooltip_self_cont', 'Fully Self Contained Vehicles Only' , $tooltipFullySelfContained, 'td'), //23
                        array('tooltip_self_cont_addnl', 'Additional Information' , $tooltipFullySelfContainedAddnl, 'label', 'ait-_ait-dir-item-selfContainedVehicles_addnl'),//23
                        
                        array('tooltip_on_site_accommodation', 'On Site Accommodation' , $tooltipOnSiteAccomodation, 'td'), //24
                        array('tooltip_on_site_accommodation_addnl', 'Additional Information' , $tooltipOnSiteAccomodationAddnl, 'label', 'ait-_ait-dir-item-onSiteAccomodation-option'),//24
                        
                        array('tooltip_rv_area', 'RV Turn Around Area' , $tooltipRVTurnAroundArea, 'td'), //28
                        array('tooltip_rv_area_addnl', 'Additional Information' , $tooltipRVTurnAroundAreaAddnl, 'label', 'ait-_ait-dir-item-rvTurnAroundArea_addnl'), //28
                        
                        array('tooltip_rv_prking', 'RV Parking Available' , $tooltipRVParking, 'td'), //29
                        array('tooltip_rv_prking_addnl', 'Additional Information' , $tooltipRVParkingAddnl, 'label', 'ait-_ait-dir-item-rvParkingAvailable_addnl'), //29
                        
                        array('tooltip_bt_ramp', 'Boat Ramp Nearby' , $tooltipBoatRamp), //30
                        array('tooltip_bt_ramp_addnl', 'Additional Information' , $tooltipBoatRampAddnl, 'label', 'ait-_ait-dir-item-boatRamp_addnl'), //30
                        
                        array('tooltip_lt_time', 'Time Limit Applies' , $tooltipTimeLimit, 'td'),//31
                        array('tooltip_lt_time_addnl', 'Additional Information' , $tooltipTimeLimitAddnl, 'label', 'ait-_ait-dir-item-timeLimitApplies_addnl'),//31
                        
                        array('tooltip_av_shades', 'Some Shade Available' , $tooltipShades, 'td'), //32
                        array('tooltip_av_shades_addnl', 'Additional Information' , $tooltipShadesAddnl, 'label', 'ait-_ait-dir-item-shadedSites_addnl'), //32
                        
                        array('tooltip_per_fires', 'Fires Permitted' , $tooltipFiresPermitted, 'td'), //33
                        array('tooltip_per_fires_addnl', 'Additional Information' , $tooltipFiresPermittedAddnl, 'label', 'ait-_ait-dir-item-firePit_addnl'), //33
                        
                        array('tooltip_no_per_fires', 'No Fires Permitted' , $tooltipNoFiresPermitted, 'td'), //34
                        array('tooltip_no_per_fires_addnl', 'Additional Information' , $tooltipNoFiresPermittedAddnl, 'label', 'ait-_ait-dir-item-noFirePit_addnl'), //34
                        
                        array('tooltip_pic_tab', 'Picnic Table Available' , $tooltipPicnicTable, 'td'), //35
                        array('tooltip_pic_tab_addnl', 'Additional Information' , $tooltipPicnicTableAddnl, 'label', 'ait-_ait-dir-item-picnicTable_addnl'), //35
                        
                        array('tooltip_bbq', 'BBQ Available' , $tooltipBBQ, 'td'), //36
                        array('tooltip_bbq_addnl', 'Additional Information' , $tooltipBBQAddnl,'label', 'ait-_ait-dir-item-bbq_addnl'), //36
                        
                        array('tooltip_shelter', 'Shelter Available' , $tooltipShelter, 'td'), //37
                        array('tooltip_shelter_addnl', 'Additional Information' , $tooltipShelterAddnl,'label', 'ait-_ait-dir-item-shelter_addnl'),//37
                        
                        array('tooltip_playground', 'Childrens Playground Available' , $tooltipChildrenPlayground, 'td'),//38
                        array('tooltip_playground_addnl', 'Additional Information' , $tooltipChildrenPlaygroundAddnl,'label', 'ait-_ait-dir-item-childrenPlayground_addnl'),//38
                        
                        array('tooltip_nice_view', 'Nice Views From Location' , $tooltipNiceViews, 'td'),//39
                        array('tooltip_nice_view_addnl', 'Additional Information' , $tooltipNiceViewsAddnl,'label', 'ait-_ait-dir-item-views_addnl'),//39
                        
                        //array('tooltip_parking', 'Parking' , 'Parking'),
                        //array('tooltip_parking_addnl', 'Additional Information' , $tooltipNiceViewsAddnl,'label', 'ait-_ait-dir-item-views_addnl'),
                        
                        array('tooltip_national_park_fees', 'National Park Fees' , $tooltipNationalParkFees, 'td'), //40
                        array('tooltip_national_park_fees_addnl', 'Additional Information' , $tooltipNationalParkFeesAddnl,'label', 'ait-_ait-dir-item-parkFees_addnl'), //40
                        
                        array('tooltip_kitchen', 'Camp Kitchen' , $tooltipCampKitchen, 'td'), //41
                        array('tooltip_kitchen_addnl', 'Additional Information' , $tooltipCampKitchenAddnl,'label', 'ait-_ait-dir-item-campKitchen_addnl'), //41
                        
                        array('tooltip_laundry', 'Laundry' , $tooltipLaundry, 'td'), //42
                        array('tooltip_laundry_addnl', 'Additional Information' , $tooltipLaundryAddnl,'label', 'ait-_ait-dir-item-laundry_addnl'), //42
                        
                        array('tooltip_ensuite_sites', 'Ensuite Sites' , $tooltipEnsuiteSites, 'td'), //43
                        array('tooltip_ensuite_sites_addnl', 'Additional Information' , $tooltipEnsuiteSitesAddnl,'label', 'ait-_ait-dir-item-ensuiteSites_addnl'), //43
                        
                        array('tooltip_restaurant', 'Restaurant' , $tooltipRestaurant, 'td'), //44
                        array('tooltip_restaurant_addnl', 'Additional Information' , $tooltipRestaurantAddnl,'label', 'ait-_ait-dir-item-restaurant_addnl'), //44
                        
                        array('tooltip_kiosk', 'Kiosk' , $tooltipKiosk, 'td'), //45
                        array('tooltip_kiosk_addnl', 'Additional Information' , $tooltipKioskAddnl,'label', 'ait-_ait-dir-item-kiosk_addnl'), //45
                        
                        array('tooltip_internet', 'Internet Access' , $tooltipInternetAccess, 'td'),    //46 
                        array('tooltip_internet_addnl', 'Additional Information' , $tooltipInternetAccessAddnl,'label', 'ait-_ait-dir-item-internetAccess_addnl'),  //46
                        
                        array('tooltip_smimming', 'Swimming' , $tooltipSwimmingPool, 'td'), //47
                        array('tooltip_smimming_addnl', 'Additional Information' , $tooltipSwimmingPoolAddnl,'label', 'ait-_ait-dir-item-swimmingPool_addnl'),  //47
                        
                        array('tooltip_games_room', 'Games Room' , $tooltipGamesRoom, 'td'),    //48
                        array('tooltip_games_room_addnl', 'Additional Information' , $tooltipGamesRoomAddnl,'label', 'ait-_ait-dir-item-gamesRoom_addnl'), //48
                        
                        array('tooltip_generators_permitted', 'Generators Permitted' , $tooltipGeneratorsPermitted, 'td'),  //49
                        array('tooltip_generators_permitted_addnl', 'Additional Information' , $tooltipGeneratorsPermittedAddnl,'label', 'ait-_ait-dir-item-generatorsPermitted_addnl'),    //49
                        
                        array('tooltip_not_generators', 'Generators Not Permitted' , $tooltipGeneratorsNotPermitted, 'td'), //50
                        array('tooltip_not_generators_addnl', 'Additional Information' , $tooltipGeneratorsNotPermittedAddnl,'label', 'ait-_ait-dir-item-generatorsNotPermitted_addnl'),    //50
                        
                        array('tooltip_show_grounds', 'Show Ground' , $tooltipShowGrounds, 'td'), //51
                        array('tooltip_show_grounds_addnl', 'Additional Information' , $tooltipShowGroundsAddnl,'label', 'ait-_ait-dir-item-showGrounds_addnl'),    //51
                        
                        array('tooltip_national_forestry', 'National Park And Forestry' , $tooltipNationalParknForestry, 'td'), //52
                        array('tooltip_national_forestry_addnl', 'Additional Information' , $tooltipNationalParknForestryAddnl,'label', 'ait-_ait-dir-item-nationalParknForestry_addnl'),    //52
                        
                        array('tooltip_pubs', 'Pubs' , $tooltipPubs, 'td'), //53
                        array('tooltip_pubs_addnl', 'Additional Information' , $tooltipPubsAddnl,'label', 'ait-_ait-dir-item-pubs_addnl'),    //53
                        
                        array('tooltip_shared_trucks', 'Shared with Trucks' , $tooltipSharedWithTrucks, 'td'), //54
                        array('tooltip_shared_trucks_addnl', 'Additional Information' , $tooltipSharedWithTrucksAddnl,'label', 'ait-_ait-dir-item-sharedWithTrucks_addnl'),    //54
                        
                        array('tooltip_parking_facility', 'Parking Facility' , 'Parking Facility'),                   
                        );
    
    foreach($toolTipArr as $key => $tooltipSng){
        $labelID = $tooltipSng[0];
        $label = $tooltipSng[1];
        $searchTag = "";
        if(array_key_exists(3, $tooltipSng)){
            $searchTag = $tooltipSng[3];
        }
        
        if($label == 'Access' || $label == 'Location'){
            //continue;
        }
        
        if(!empty($searchTag)){
            switch($searchTag){
                case 'label':
                    if($tooltipSng[1] == 'Additional Information'){
                        echo "\njQuery(\"label[for='{$tooltipSng[4]}']\" ).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$label'><img src='{$toottipImage}' /></a>\");";
                    }else{
                        echo "\njQuery(\"label:contains('{$label}')\" ).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$label'><img src='{$toottipImage}' /></a>\");";
                    }
                    break;
                
                case 'td':
                    echo "\njQuery(\"td\").filter(function() {
                            return jQuery.text([this]).trim() == '$label';
                        }).append(\"<a id='{$labelID}' title='$label'><img src='{$toottipImage}' /></a>\");";
                    break;
            }
        }else{
            if($key > 28){
                echo "\njQuery(\"td\").filter(function() {
                    return jQuery.text([this]).trim() == '$label';
                }).append(\"<a id='{$labelID}' title='$label'><img src='{$toottipImage}' /></a>\");";
            }else{
                echo "\njQuery(\"label:contains('{$label}')\" ).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$label'><img src='{$toottipImage}' /></a>\");";
            }
        }
    }
    
    echo "\n\njQuery(function() {";
    $counter = 0;
    foreach($toolTipArr as $key => $tooltipSng){
        //echo "\n\n//Feature $counter: $counter\n alert('$counter');console.log('$counter');\n";$counter++;
        $labelID = $tooltipSng[0];
        $label = $tooltipSng[1];
        $tooltipText = $tooltipSng[2];
        
        $searchTag = "";
        if(array_key_exists(3, $tooltipSng)){
            $searchTag = $tooltipSng[3];
        }        
        if(!empty($searchTag)){
            switch($searchTag){
                case 'label':                    
                    if(array_key_exists(5, $tooltipSng) && $tooltipSng[5] == 'video'){
                        echo "\n\tjQuery( \"#{$labelID}\" ).tooltip({content: \"{$tooltipText}\",track:false,hide:false});";
                    }elseif($tooltipSng[1] == 'Additional Information'){
                        echo "\n\tjQuery( \"#{$labelID}\" ).tooltip({content: \"{$tooltipText}\",track:true});";
                    }else{
                        if($label == 'Access' || $label == 'Location'){
                            echo "//--rasa access";
                        }
                        echo "\n\tjQuery( \"#{$labelID}\" ).tooltip({content: \"{$tooltipText}\",track:true});";
                    }
                    break;
                
                case 'td':
                    echo "\n\tjQuery(\"td\").filter(function() {
                        return jQuery.text([this]).trim() == '$label';
                        }).tooltip({content: \"{$tooltipText}\",track:true});";
                    break;
            }
        }else{
            if($key > 28){
                echo "\n\tjQuery(\"td\").filter(function() {
                    return jQuery.text([this]).trim() == '$label';
                }).tooltip({content: \"{$tooltipText}\",track:true});";
            }else{
                echo "\n\tjQuery( \"#{$labelID}\" ).tooltip({content: \"{$tooltipText}\",track:true});";
            }
        }
    }
    $temp = <<<TEMP
    jQuery('#myBtn').mouseover(function () {
        jQuery('#tooltip-8').tooltip("open");
     });
     jQuery('#closeBtn').click(function () {
        jQuery('#tooltip-8').tooltip("close");
     });
TEMP;
    //echo $temp;
    echo "\n});\n";        
?>
jQuery("span:contains('Item Categories')" ).after("&nbsp;&nbsp;<a id='tooltip_item_categories'><img src='<?=$toottipImage?>' /></a>");
jQuery('#tooltip_item_categories').tooltip({content:"<?= $tooltipItemCategories?>",track:true});

//jQuery("#directory_1").append("&nbsp;&nbsp;<a id='tooltip_directory_1'><img src='<?=$toottipImage?>' /></a>");
//jQuery('#tooltip_directory_1').tooltip({content:"<?= $tooltipDirectory1?>",track:true});

jQuery("span:contains('Directory Gallery')" ).after("&nbsp;&nbsp;<a id='tooltip_directory_gallery'><img src='<?=$toottipImage?>' /></a>");
jQuery('#tooltip_directory_gallery').tooltip({content:"<?= $tooltipDirectoryGallery?>",track:true});
jQuery("span:contains('Item Locations')" ).after("&nbsp;&nbsp;<a id='tooltip_item_location'><img src='<?=$toottipImage?>' /></a>");
jQuery("span:contains('Image for item')" ).after("&nbsp;&nbsp;<a id='tooltip_image_item'><img src='<?=$toottipImage?>' /></a>");
</script>
<script>
    jQuery('#title').attr('title', 'Enter the title of your listing here.  This is how it will appear in the directory.  Eg, Five Mile Reserve Camping Area');
    jQuery("#title").tooltip();
    
    jQuery('#content').attr('title', 'Enter a full description of your site here. It should tell the user about the site, what is available there, and any directions to the site. It should also include any special terms and conditions if applicable. You may also include information about the surrounding areas, towns or villages. You may mention any facilities here also, however there is provision for this below. Remember, the more information you give our members, the more likely they will be to visit your site. Remember also that any description you place in here will be translatable, so please avoid the use of slang if you want other nationalities to be able to comprehend your description.');
    jQuery("#content").tooltip();
    
    jQuery('#tooltip_directory_gallery').attr('title', 'This area allows you to upload images of your site to your listing.  There are a total of 5 free photos permitted per listing.  Please keep images to a Maximum size of 1024 pixels by 768 pixels and no larger than 1MB.  If possible please name your photos with titles that reflect your listing title');
    jQuery("#tooltip_directory_gallery").tooltip();
    
    jQuery('#tooltip_item_categories').attr('title', 'Place a tick in the box that relates to your Item Category.  This will determine in what category your item appears.  In this instance you will place a tick in either the Free Camps,  Rest Areas or Dump Points  box.');
    jQuery("#tooltip_item_categories").tooltip();
    
    jQuery('#tooltip_item_location').attr('title', 'This area allows you to nominate the region for your site. It is important you select the correct region so that you appear on the Map.  In each case you will need to select your State, Region, then Sub Region or Town if applicable.  Eg, NSW, Illawarra, Kiama.  You would need to place a tick in all three boxes.');
    jQuery("#tooltip_item_location").tooltip();
    
    jQuery('#tooltip_image_item').attr('title', 'This area will allow you to place a feature image or your logo in your listing.  It will appear at the top of the listing in the View More area.   To submit your image, select the \'Set featured image\' link below.   The best dimensions for this image are 152 x 212 Pixels');
    jQuery("#tooltip_image_item").tooltip();
    
    jQuery('#directory_1').attr('title', 'Choose this Tab to enter or edit details for your Free Camp Listing.  The Tab relates to Free Camps, Dump Points & Rest Areas');
    jQuery("#directory_1").tooltip();
    
    jQuery('#directory_2').attr('title', 'Choose this Tab to enter or edit details for your Business Listings.  The Tab relates to Sites where you are a Business wishing to list in the FRC Business Directory.');
    jQuery("#directory_2").tooltip();
    
    jQuery('#directory_3').attr('title', 'Choose this Tab to enter or edit details for your Low Cost Listing.  The Tab relates to Low Cost Sites where a single site is less than $25 per site per night per couple.');
    jQuery("#directory_3").tooltip();
    
    jQuery('#directory_4').attr('title', 'Choose this Tab to enter or edit details for your House or Farm SittingListing.  The Tab relates to Sites where you wish to find someone to look after your property, pets or gardens while you are away.');
    jQuery("#directory_4").tooltip();
    
    jQuery('#directory_5').attr('title', 'Choose this Tab to enter or edit details for your Park Over Listing.  The Tab relates to Sites where you wish to help out another member who is looking for short term accommodation.');
    jQuery("#directory_5").tooltip();
    
    jQuery('#directory_6').attr('title', 'Choose this Tab to enter or edit details for your Help Out Listing.  The Tab relates to Sites where you wish to find someone to Help Out or Volunteer on your property.');
    jQuery("#directory_6").tooltip();
    
    jQuery('#directory_7').attr('title', 'Choose this Tab to enter or edit details for your $25 + Listing.  The Tab relates to Sites where a single site is more than $25 per site per night per couple.');
    jQuery("#directory_7").tooltip();
    //jQuery('#ait-_ait-dir-item-end_date').attr('readonly', 'true');
    
    jQuery( "#ait-_ait-dir-item-start_date" ).change(function() {
        var dateObj1 = jQuery.datepicker.parseDate( "d M yy", jQuery('#ait-_ait-dir-item-start_date').val());
        var dateObj2 = jQuery.datepicker.parseDate( "d M yy", jQuery('#ait-_ait-dir-item-end_date').val());
        if ( jQuery.type(dateObj1) == 'date' && jQuery.type(dateObj2) == 'date' ) {                  
            diff = dateObj2 - dateObj1;
            var days = parseInt(diff / 1000 / 60 / 60 / 24);
            jQuery('#ait-_ait-dir-item-duration').val(days + ' days');
        }else{
            jQuery('#ait-_ait-dir-item-duration').val('');
        }
    });
    
    jQuery( "#ait-_ait-dir-item-end_date" ).change(function() {
        var dateObj1 = jQuery.datepicker.parseDate( "d M yy", jQuery('#ait-_ait-dir-item-start_date').val());
        var dateObj2 = jQuery.datepicker.parseDate( "d M yy", jQuery('#ait-_ait-dir-item-end_date').val());
        if ( jQuery.type(dateObj1) == 'date' && jQuery.type(dateObj2) == 'date' ) {                  
            diff = dateObj2 - dateObj1;
            var days = parseInt(diff / 1000 / 60 / 60 / 24);
            jQuery('#ait-_ait-dir-item-duration').val(days + ' days');
        }else{
            jQuery('#ait-_ait-dir-item-duration').val('');
        }
    });
    jQuery('#ait-_ait-dir-item-directoryType').attr('readonly', 'true');
    //setTimeout(function () { jQuery('#content').trigger('click'); }, 3000);
    //setTimeout(function () { jQuery('#content-html').trigger('click'); }, 3000);
    jQuery( document ).ready(function() {
        jQuery('#content-html').trigger('click');
        jQuery('#content').trigger('click');
        jQuery('#content-html').trigger('click');
        //setTimeout(function () { jQuery('#content').trigger('click'); }, 3000);
        //setTimeout(function () { jQuery('#content-html').trigger('click'); }, 3000);
    });
</script>
<?php
    //echo "rasa".$category_id = get_cat_ID('Dump Points');
    //taxonomy=ait-dir-item-category&post_type=ait-dir-item
    //echo "<pre>";
    //print_r(get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'hierarchical ' => 1, 'hide_empty' => 0)));
    //print_r(get_categories( array( 'taxonomy' => 'ait-dir-item-category', 'child_of' => 10,'hierarchical ' => 1, 'hide_empty' => 0))); //this is working
    //print_r(wp_list_categories(array( 'taxonomy' => 'ait-dir-item-category', 'child_of' => '4')));
    //echo "</pre>";
    //print_r(get_categories( array( 'taxonomy' => 'ait-dir-item-category')));
                                
    //term_taxonomy_id contains category_id
    //print_r(wp_list_categories( array('taxonomy' => 'ait-dir-item-category') ));
   // echo "rasa";;
?>