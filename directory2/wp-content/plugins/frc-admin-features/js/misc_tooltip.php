<?php
    $tooltipAddress = "Place your address in this field in the following format:  Street Number (If available) Street Name, Suburb/Town and State.  Eg.  15 Smith Street, Cooroy, Qld This will allow your GPS Co-Ordinates to be automatically located by Google Maps.  In the event you do not have an exact street number, you can enter more accurate details in the GPS Section Below.";
    
    $tooltipGpsLatitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    
    $tooltipGpsLongitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    
    $tooltipTelephone = "Enter your contact number here if you wish to be contacted by our members.  Leave blank if you do not want your number to appear in your listing.";
    
    $tooltipEmail = "Enter your preferred email address here.  Anything entered in this filed will be displayed in the listing.  Leave blank if you do not wish to display your email address.";
    $tooltipContactOwner = "Place a tick in this field if you wish for a Contact Owner Button to appear in your listing.  The button will appear just below your feature photo or icon. This will activate a form that members can fill out to contact you direct.";
    $tooltipWeb = "Enter your preferred web address here.  Anything entered in this field will be displayed in the listing.  Format Eg, www.freerangecamping.com.au do not use http:\/\/ before the URL";
    
    $tooltipShowStreetView = "This field allows us to show a Street view of your property in the More Information window.  Tick this box if you wish to use this option. NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    
    $tooltipWLMCity = "Place your suburb/city in this field";
    $tooltipWLMState = "Place your state in this field";
    $tooltipWLMZip = "Place your postcode in this field";
    
    $tooltipLocation = "Location";
    $tooltipAccess = "Access";
    $tooltipResponsibilityAuthority = "Enter the full details of your Organisation or Entity in this field.  This field advises users who the Responsible entity for this site is.  Eg, Property Owner or National Parks";
    $tooltipDiscount = "Tick this box if you will be offering a Discount to FRC Members.  This will allow a Discount Icon to appear in the preview window of our maps. ";
    $tooltipDiscountDetail = "Enter details of your discount here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.  Eg, 10% Discount Storewide";
    $tooltipOffer = "Tick this box if you will be making an Offer to FRC Members.  This will allow a Offer Icon to appear in the preview window of our maps.";
    $tooltipOfferDetail = "Enter details of your Offer here.  Keep details in this section brief as you can elaborate terms and conditions in the Description area.  Eg, Buy One get One Free or Free Travellers Pack";
    $tooltipLimitDiscount = "Place a tick in this box only if you wish to limit how many times a member may use this discount or offer at your business. This will allow us to place restrictions on the member card that will monitor its usage.";
    $tooltipUsesPerMonth = "Only use this field if you have placed a tick in the Limit Discount box above.  Enter the number of times you wish to allow a member to use the discount in your business per calendar month.  Eg.  If you wish for a member to only use it 5 times in a month.  Simply enter the number &#8216;5&#8217;";
    $tooltipPricingDetail = "This field is for you to enter brief details of pricing or fees at your site.  Keep details in this field brief.  Here you should enter your best prices for users to see.  If your pricing model is detailed or complex, you can elaborate in the description area or use the booking link below to direct users direct to your pricing page or booking page. Eg, $32 Unpowered Site";
    $tooltipBookingLink = "This field is for your direct access to the booking link page of your web site if you have one.  Simply past the URL from your booking page into this field and we will embed it for you.";
    $tooltipHelpRequired = "Enter Summary details of the Help you need in here.  Information in this field will populate the preview box so should be brief.  You can elaborate in your description.  Eg, Farm Fencing, or Planting or Cleaning";
    $tooltipStartDate = "Use the field and calendar below to enter in the date you wish your applicant to start looking after your property.   This date will appear in your preview window.";
    
    $tooltipEndDate = "Use the field and calendar below to enter in the date you wish your applicant to stop looking after your property. This date will appear in your preview window. ";
    
    $tooltipDuration = "Enter the number of days you will require your property to be cared for, E.g.  6 Days";
    $tooltipGardening = "Mark this box if you have gardens, plants or yards that will require care and maintenance.";
    $tooltipOther = "Enter details in this field of any other requirements that will need attending to in your absence.";
    $tooltipPropertyType = "Enter a brief description here of your type of Property.  Eg, Farm House, Residential  or Unit";
    $tooltipPetMinding = "Mark this box if you have pets or animals on your property that will need looking after.";
    $tooltipPetMindingAddnl = "Additonal Information";
    $tooltipPoliceCheck = "Mark this box if you require that your applicants must have a current National Police Clearance certificate.";
    
    $toolTipArr = array(
                        
                        array('tooltip_address','Address', $tooltipAddress, 'label'),
                        array('tooltip_latitude','Latitude',$tooltipGpsLatitude,'label'),
                        array('tooltip_longitude','Longitude', $tooltipGpsLongitude, 'label'),
                        array('tooltip_show_streetview', 'Streetview' , $tooltipShowStreetView, 'label'),
                        array('tooltip_telephone', 'Telephone',$tooltipTelephone),
                        array('tooltip_email', 'Email', $tooltipEmail),
                        array('tooltip_contact_owner', 'Contact owner button' , $tooltipContactOwner, 'span'),
                        array('tooltip_web', 'Web' , $tooltipWeb),
                        array('tooltip_city','Suburb', $tooltipWLMCity, 'label'),
                        array('tooltip_state','State', $tooltipWLMState, 'label'),
                        array('tooltip_zip','Pin Code', $tooltipWLMZip, 'label'),
                        array('tooltip_location', 'Area/Region' , $tooltipLocation,'label'),
                        array('tooltip_access' , 'Entry/Route', $tooltipAccess, 'label'),
                        array('tooltip_res_auth', 'Responsibility Authority' , $tooltipResponsibilityAuthority, 'label'),
                        array('tooltip_discnt', 'Discount' , $tooltipDiscount,'span'),
                        array('tooltip_discnt_det', 'Discount Detail' , $tooltipDiscountDetail,'label'),
                        array('tooltip_offer', 'Offer' , $tooltipOffer,'span'),
                        array('tooltip_offer_det', 'Offer Detail' , $tooltipOfferDetail,'label'),
                        array('tooltip_lt_discnt', 'Limit_Discount / Other_Usage' , $tooltipLimitDiscount, 'span'), //Limit
                        array('tooltip_user_pm', 'Number of Uses per Month' , $tooltipUsesPerMonth,'label'),
                        array('tooltip_pricing_detail', 'Pricing Detail' , $tooltipPricingDetail,'label'),
                        array('tooltip_bookingLnk','Booking Link' , $tooltipBookingLink,'label'),
                        array('tooltip_help_required', 'Help Required' , $tooltipHelpRequired,'label'),
                        array('tooltip_st_date', 'Start Date' , $tooltipStartDate,'label'),
                        array('tooltip_en_date', 'End Date' , $tooltipEndDate,'label'),
                        array('tooltip_duration', 'Duration' , $tooltipDuration,'label'),
                        array('tooltip_gardening', 'Gardening' , $tooltipGardening,'label'),
                        array('tooltip_other', 'Other' , $tooltipOther,'label'),
                        array('tooltip_property_type', 'Property Type',$tooltipPropertyType,'label'),
                        array('tooltip_pet_minding', 'Pet Minding Required', $tooltipPetMinding, 'span'),
                        array('tooltip_pet_minding_addnl', 'Additional Information', $tooltipPetMindingAddnl, 'label', 'ait-opt-metabox-_ait-item_item-data-pet_minding_addnl-en_US'),
                        array('tooltip_police_chk', 'Police Check Required', $tooltipPoliceCheck, 'span'),
                        );
    $toottipImage = "https://www.freerangecamping.com.au/directory/wp-content/uploads/sites/3/2014/04/facility_icons/question_frame.png";
    //echo'<pre>';print_r($toolTipArr);die;
    $jsStr = "";
    foreach($toolTipArr as $key => $tooltipSng){
        $labelID = $tooltipSng[0];
        $label = $tooltipSng[1];
        $text = $tooltipSng[2];
        $searchTag = "";
        if(array_key_exists(3, $tooltipSng)){
            $searchTag = $tooltipSng[3];
        }
        
        if($label == 'Access' || $label == 'Location'){
            continue;
        }
        
        if(!empty($searchTag)){
            switch($searchTag){
                case 'label':
                        $jsStr.="\njQuery(\"label:contains('{$label}')\" ).filter(function() {return jQuery(this).text() === '$label';}).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$text' data-selector='true'><img src='{$toottipImage}' /></a>\");";
                    break;
                case 'span':
                    $jsStr.="\njQuery(\"span:contains('{$label}')\" ).filter(function() {return jQuery(this).text() === '$label';}).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$text' data-selector='true'><img src='{$toottipImage}' /></a>\");";
                    break;
            }
        }else{
                $jsStr.="\njQuery(\"label:contains('{$label}')\" ).filter(function() {return jQuery(this).text() === '$label';}).after(\"&nbsp;&nbsp;<a id='{$labelID}' title='$text' data-selector='true'><img src='{$toottipImage}' /></a>\");";
        }
    }
    
?>
<script type="text/javascript">
    <?php echo $jsStr;?>
    jQuery('#title').attr('title', 'Enter the title of your listing here.  This is how it will appear in the directory.  Eg, Five Mile Reserve Camping Area');
    jQuery('#title').attr('data-selector', 'true');
    jQuery("#title").tooltip();
    
    jQuery('#content').attr('title', 'Enter a full description of your site here. It should tell the user about the site, what is available there, and any directions to the site. It should also include any special terms and conditions if applicable. You may also include information about the surrounding areas, towns or villages. You may mention any facilities here also, however there is provision for this below. Remember, the more information you give our members, the more likely they will be to visit your site. Remember also that any description you place in here will be translatable, so please avoid the use of slang if you want other nationalities to be able to comprehend your description.');
    jQuery('#content').attr('data-selector', 'true');
    jQuery("#content").tooltip();
    
    var img = "https://www.freerangecamping.com.au/directory/wp-content/uploads/sites/3/2014/04/facility_icons/question_frame.png";
    
    jQuery("h2 span:contains('Item Categories')").after("\n\n<a title='Place a tick in the box that relates to your Item Category.  This will determine in what category your item appears.  In this instance you will place a tick in either the Free Camps,  Rest Areas or Dump Points  box.' data-selector='true'><img src="+img+" /></a>");
    
    jQuery("h2 span:contains('Item Locations')").after("\n\n<a title='This area allows you to nominate the region for your site. It is important you select the correct region so that you appear on the Map.  In each case you will need to select your State, Region, then Sub Region or Town if applicable.  Eg, NSW, Illawarra, Kiama.  You would need to place a tick in all three boxes.' data-selector='true'><img src="+img+" /></a>");
    
    jQuery('#directory_1').attr({'title':'Choose this Tab to enter or edit details for your Free Camp Listing.  The Tab relates to Free Camps, Dump Points & Rest Areas','data-selector':'true'});
    jQuery("#directory_1").tooltip();
    
    jQuery('#directory_2').attr({'title':'Choose this Tab to enter or edit details for your Business Listings.  The Tab relates to Sites where you are a Business wishing to list in the FRC Business Directory.','data-selector':'true'});
    jQuery("#directory_2").tooltip();
    
    jQuery('#directory_3').attr({'title':'Choose this Tab to enter or edit details for your Low Cost Listing.  The Tab relates to Low Cost Sites where a single site is less than $25 per site per night per couple.','data-selector':'true'});
    jQuery("#directory_3").tooltip();
    
    jQuery('#directory_4').attr({'title':'Choose this Tab to enter or edit details for your House or Farm SittingListing.  The Tab relates to Sites where you wish to find someone to look after your property, pets or gardens while you are away.','data-selector':'true'});
    jQuery("#directory_4").tooltip();
    
    jQuery('#directory_5').attr({'title':'Choose this Tab to enter or edit details for your Park Over Listing.  The Tab relates to Sites where you wish to help out another member who is looking for short term accommodation.','data-selector':'true'});
    jQuery("#directory_5").tooltip();
    
    jQuery('#directory_6').attr({'title':'Choose this Tab to enter or edit details for your Help Out Listing.  The Tab relates to Sites where you wish to find someone to Help Out or Volunteer on your property.','data-selector':'true'});
    jQuery("#directory_6").tooltip();
    
    jQuery('#directory_7').attr({'title':'Choose this Tab to enter or edit details for your $25 + Listing.  The Tab relates to Sites where a single site is more than $25 per site per night per couple.','data-selector':'true'});
    jQuery("#directory_7").tooltip();
</script>