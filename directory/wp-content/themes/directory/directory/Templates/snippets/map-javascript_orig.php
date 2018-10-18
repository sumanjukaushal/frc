<style>
.infoBox > img {
	right:0;
	height:15px;
	width:15px;
}
</style>
<script type="text/javascript">
	function frcLoadingMessage(){
		jQuery.blockUI({ message: "Please wait whilst we load the listing page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
	}
//-------rasa------------
{var $url = content_url();}
<?php echo "var contentURL = '$url';\n"?>
var imageArray = [];
{if !empty($items)}
	{foreach $items as $item}
		var imagesContent = "";
		var dynamicImageContent = "";
		{var $url = content_url();}
		{if
		(!empty($item->optionsDir['toilets'])) ||
		(!empty($item->optionsDir['disableFacility'])) ||
		(!empty($item->optionsDir['showers'])) ||
		(!empty($item->optionsDir['drinkingWater'])) ||
		(!empty($item->optionsDir['drinkingWaterNotSuitable'])) ||
		(!empty($item->optionsDir['waterFillingAvailable'])) ||
		(!empty($item->optionsDir['wasteFacilityAvailable'])) ||
		(!empty($item->optionsDir['dumpPointAvailable'])) ||
		(!empty($item->optionsDir['poweredSites'])) ||
		(!empty($item->optionsDir['emergencyPhone'])) ||
		(!empty($item->optionsDir['mobilePhoneReception'])) ||
		(!empty($item->optionsDir['petsOk'])) ||
		(!empty($item->optionsDir['petsNotOk'])) ||
		(!empty($item->optionsDir['fourWheelDrive'])) ||
		(!empty($item->optionsDir['dryWeatherOnlyAccess'])) ||
		(!empty($item->optionsDir['tentsOnly'])) ||
		(!empty($item->optionsDir['noTents'])) ||
		(!empty($item->optionsDir['camperTrailers'])) ||
		(!empty($item->optionsDir['noCamperTrailers'])) ||
		(!empty($item->optionsDir['caravans'])) ||
		(!empty($item->optionsDir['largeSizeMotohomeAccess'])) ||
		(!empty($item->optionsDir['bigrig'])) ||
		(!empty($item->optionsDir['selfContainedVehicles'])) ||
		(!empty($item->optionsDir['onSiteAccomodation'])) ||
		(!empty($item->optionsDir['seasonalRatesApply'])) ||
		(!empty($item->optionsDir['discount'])) ||
		(!empty($item->optionsDir['Offers Apply'])) || 
		(!empty($item->optionsDir['rvTurnAroundArea'])) ||
		(!empty($item->optionsDir['rvParkingAvailable'])) ||
		(!empty($item->optionsDir['boatRamp'])) || (!empty($item->optionsDir['timeLimitApplies'])) || (!empty($item->optionsDir['shadedSites'])) ||
		(!empty($item->optionsDir['firePit'])) || (!empty($item->optionsDir['noFirePit'])) || (!empty($item->optionsDir['picnicTable'])) ||
		(!empty($item->optionsDir['bbq'])) || (!empty($item->optionsDir['shelter'])) || (!empty($item->optionsDir['childrenPlayground'])) ||
		(!empty($item->optionsDir['views'])) || (!empty($item->optionsDir['parkFees'])) || (!empty($item->optionsDir['campKitchen'])) ||
		(!empty($item->optionsDir['laundry'])) || (!empty($item->optionsDir['ensuiteSites'])) || (!empty($item->optionsDir['restaurant']))
		}
		
		{if
		(!empty($item->optionsDir['kiosk'])) ||
		(!empty($item->optionsDir['internetAccess'])) ||
		(!empty($item->optionsDir['swimmingPool']))||
		(!empty($item->optionsDir['gamesRoom']))||
		(!empty($item->optionsDir['generatorsPermitted'])) ||
		(!empty($item->optionsDir['generatorsNotPermitted'])) ||
		(!empty($item->optionsDir['showGrounds'])) ||
		(!empty($item->optionsDir['nationalParknForestry'])) ||
		(!empty($item->optionsDir['pubs'])) ||
		(!empty($item->optionsDir['sharedWithTrucks']))
		}
<?php
    $featuresArr = array(							
							1	=>	array('toilets', 'Toilets Available'),
							2	=>	array('disableFacility', 'Disabled Facility'),
							3	=>	array('showers', 'Showers Available'),
							4	=>	array('drinkingWater', 'Drinking Water Available'),
							5	=>	array('drinkingWaterNotSuitable', 'Water Not Suitable For Drinking'),
							6	=>	array('waterFillingAvailable', 'Water Filling Available'),
							7	=>	array('wasteFacilityAvailable', 'Rubbish Bins Available'),
							8	=>	array('dumpPointAvailable', 'Dump Point Available'),
							9	=>	array('poweredSites', 'Power Available'),
							10	=>	array('emergencyPhone', 'Emergency Phone'),
							11	=>	array('mobilePhoneReception', 'Mobile Phone Reception'),
							12	=>	array('petsOk', 'Pets Permitted'),
							13	=>	array('petsNotOk', 'No Pets Permitted'),
							14	=>	array('fourWheelDrive', '4WD Drive Access Only'),
							15	=>	array('dryWeatherOnlyAccess', 'Dry Weather Access'),
							16	=>	array('tentsOnly', 'Suitable for Tents'),
							17	=>	array('noTents', 'Not Suitable for Tents'),
							18	=>	array('camperTrailers', 'Camper Trailers'),
							19	=>	array('noCamperTrailers', 'No Camper Trailers'),
							20	=>	array('caravans', 'Caravans'),
							21	=>	array('largeSizeMotohomeAccess', 'Campervans  & Motorhomes'),
							22	=>	array('bigrig', 'Big Rig Access'),
							23	=>	array('selfContainedVehicles', 'Fully Self Contained Vehicles Only'),
							24	=>	array('onSiteAccomodation', 'On Site Accommodation'),
							25	=>	array('seasonalRatesApply', 'Seasonal Rates Apply'),
							26	=>	array('discount', 'Discounts Apply'),
							27	=>	array('offer', 'Offers Apply'),
							28	=>	array('rvTurnAroundArea', 'RV Turn Around Area'),
							29	=>	array('rvParkingAvailable', 'RV Parking Available'),
							30	=>	array('boatRamp', 'Boat Ramp Nearby'),
							31	=>	array('timeLimitApplies', 'Time Limits Apply'),
							32	=>	array('shadedSites', 'Some Shade Available'),
							33	=>	array('firePit', 'Fires Permitted'),
							34	=>	array('noFirePit', 'No Fires Permitted'),
							35	=>	array('picnicTable', 'Picnic Table Available'),
							36	=>	array('bbq', 'BBQ Available'),
							37	=>	array('shelter', 'Shelter Available'),
							38	=>	array('childrenPlayground', 'Childrens Playground Available'),
							39	=>	array('views', 'Nice Views From Location'),
							40	=>	array('parkFees', 'National Park Fees'),
							41	=>	array('campKitchen', 'Camp Kitchen'),
							42	=>	array('laundry', 'Laundry'),
							43	=>	array('ensuiteSites', 'Ensuite Sites'),
							44	=>	array('restaurant', 'Restaurant'),
							45	=>	array('kiosk', 'Kiosk'),
							46	=>	array('internetAccess', 'Internet Access'),
							47	=>	array('swimmingPool', 'Swimming'),
							48	=>	array('gamesRoom', 'Games Room'),
							49	=>	array('generatorsPermitted', 'Generators Permitted'),
							50	=>	array('generatorsNotPermitted', 'Generators Not Permitted'),
							51	=>	array('showGrounds', 'Show Grounds'),
							52	=>	array('nationalParknForestry', 'National Parks and Forestry'),
							53	=>	array('pubs', 'Pubs'),
							54	=>	array('sharedWithTrucks', 'Shared With Trucks'),
						);
    
    foreach($featuresArr as $featureArr){
        $imageSrc = "";
        $campgroundFeature = $featureArr[0];$campgroundText = $featureArr[1];
        if(!empty($item->optionsDir[$campgroundFeature])){
            $imageSrc = "{$url}/uploads/sites/3/2014/04/facility_icons/30x30/{$campgroundFeature}.png";
        }
    }
?>
			{if (!empty($item->optionsDir['toilets']))}
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/toilets.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Toilets Available'} alt = {__ 'Toilets Available'} />';
			{/if}
			{if (!empty($item->optionsDir['disableFacility']))}
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/disableFacility.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Disabled Facility'} alt = {__ 'Disabled Facility'} />';
			{/if}
			{if (!empty($item->optionsDir['showers']))}
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/showers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Showers Available'} alt = {__ 'Showers Available'} />';
			{/if}
			{if (!empty($item->optionsDir['drinkingWater']))}
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/drinkingWater.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Drinking Water Available'} alt = {__ 'Drinking Water Available'} />';
			{/if}
			{if (!empty($item->optionsDir['drinkingWaterNotSuitable']))}
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/drinkingWaterNotSuitable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Water Not Suitable For Drinking'} alt = {__ 'Water Not Suitable For Drinking'} />';
			{/if}
			{if (!empty($item->optionsDir['waterFillingAvailable']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/waterFillingAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Water Filling Available'} alt = {__ 'Water Filling Available'} />';
			{/if}
			{if (!empty($item->optionsDir['wasteFacilityAvailable']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/wasteFacilityAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Rubbish Bins Available'} alt = {__ 'Rubbish Bins Available'} />';
			{/if}
			{if (!empty($item->optionsDir['dumpPointAvailable']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/dumpPointAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Dump Point Available'} alt = {__ 'Dump Point Available'} />';
			{/if}
			{if (!empty($item->optionsDir['poweredSites']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/poweredSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Power Available'} alt = {__ 'Power Available'} />';
			{/if}
			{if (!empty($item->optionsDir['emergencyPhone']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/emergencyPhone.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Emergency Phone'} alt = {__ 'Emergency Phone'} />';
			{/if}
			{if (!empty($item->optionsDir['mobilePhoneReception']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/mobilePhoneReception.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Mobile Phone Reception'} alt = {__ 'Mobile Phone Reception'} />';
			{/if}
			{if (!empty($item->optionsDir['petsOk']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/petsOk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Pets Permitted'} alt = {__ 'Pets Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['petsNotOk']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/petsNotOk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Pets Permitted'} alt = {__ 'No Pets Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['fourWheelDrive']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/fourWheelDrive.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ '4WD Drive Access Only'} alt = {__ '4WD Drive Access Only'} />';
			{/if}
			{if (!empty($item->optionsDir['dryWeatherOnlyAccess']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/dryWeatherOnlyAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Dry Weather Access'} alt = {__ 'Dry Weather Access'} />';
			{/if}
			{if (!empty($item->optionsDir['tentsOnly']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/tentsOnly.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Suitable for Tents'} alt = {__ 'Suitable for Tents'} />';
			{/if}
			{if (!empty($item->optionsDir['noTents']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/noTents.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Not Suitable for Tents'} alt = {__ 'Not Suitable for Tents'} />';
			{/if}
			{if (!empty($item->optionsDir['camperTrailers']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/camperTrailers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camper Trailers'} alt = {__ 'Camper Trailers'} />';
			{/if}
			{if (!empty($item->optionsDir['noCamperTrailers']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/noCamperTrailers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Camper Trailers'} alt = {__ 'No Camper Trailers'} />';
			{/if}
			{if (!empty($item->optionsDir['caravans']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/caravans.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Caravans'} alt = {__ 'Caravans'} />';
			{/if}
			{if (!empty($item->optionsDir['largeSizeMotohomeAccess']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/largeSizeMotohomeAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Campervans  & Motorhomes'} alt = {__ 'Campervans  & Motorhomes'} />';
			{/if}
			{if (!empty($item->optionsDir['bigrig']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/bigrig.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Big Rig Access'} alt = {__ 'Big Rig Access'} />';
			{/if}
			{if (!empty($item->optionsDir['selfContainedVehicles']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/selfContainedVehicles.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fully Self Contained Vehicles Only'} alt = {__ 'Fully Self Contained Vehicles Only'} />';
			{/if}
			{if (!empty($item->optionsDir['onSiteAccomodation']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/onSiteAccomodation.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'On Site Accommodation'} alt = {__ 'On Site Accommodation'} />';
			{/if}
			{if (!empty($item->optionsDir['seasonalRatesApply']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/seasonalRatesApply.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Seasonal Rates Apply'} alt = {__ 'Seasonal Rates Apply'} />';
			{/if}
			{if (!empty($item->optionsDir['discount']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/discount.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Discounts Apply'} alt = {__ 'Discounts Apply'} />';
			{/if}
			{if (!empty($item->optionsDir['offer Offers Apply']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/offer.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Offers Apply'} alt = {__ 'Offers Apply'} />';
			{/if}
			{if (!empty($item->optionsDir['rvTurnAroundArea']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/rvTurnAroundArea.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Turn Around Area'} alt = {__ 'RV Turn Around Area'} />';
			{/if}
			{if (!empty($item->optionsDir['rvParkingAvailable']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/rvParkingAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Parking Available'} alt = {__ 'RV Parking Available'} />';
			{/if}
			{if (!empty($item->optionsDir['boatRamp']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/boatRamp.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Boat Ramp Nearby'} alt = {__ 'Boat Ramp Nearby'} />';
			{/if}
			{if (!empty($item->optionsDir['timeLimitApplies']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/timeLimitApplies.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Time Limits Apply'} alt = {__ 'Time Limits Apply'} />';
			{/if}
			{if (!empty($item->optionsDir['shadedSites']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/shadedSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Some Shade Available'} alt = {__ 'Some Shade Available'} />';
			{/if}
			{if (!empty($item->optionsDir['firePit']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/firePit.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fires Permitted'} alt = {__ 'Fires Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['noFirePit']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/noFirePit.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Fires Permitted'} alt = {__ 'No Fires Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['picnicTable']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/picnicTable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Picnic Table Available'} alt = {__ 'Picnic Table Available'} />';
			{/if}
			{if (!empty($item->optionsDir['bbq']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/bbq.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'BBQ Available'} alt = {__ 'BBQ Available'} />';
			{/if}
			{if (!empty($item->optionsDir['shelter']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/shelter.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Shelter Available'} alt = {__ 'Shelter Available'} />';
			{/if}
			{if (!empty($item->optionsDir['childrenPlayground']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/childrenPlayground.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Childrens Playground Available'} alt = {__ 'Childrens Playground Available'} />';
			{/if}
			{if (!empty($item->optionsDir['views']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/views.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Nice Views From Location'} alt = {__ 'Nice Views From Location'} />';
			{/if}
			{if (!empty($item->optionsDir['parkFees']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/parkFees.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'National Park Fees'} alt = {__ 'National Park Fees'} />';
			{/if}
			{if (!empty($item->optionsDir['campKitchen']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/campKitchen.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camp Kitchen'} alt = {__ 'Camp Kitchen'} />';
			{/if}
			{if (!empty($item->optionsDir['laundry']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/laundry.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Laundry'} alt = {__ 'Laundry'} />';
			{/if}
			{if (!empty($item->optionsDir['ensuiteSites']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/ensuiteSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Ensuite Sites'} alt = {__ 'Ensuite Sites'} />';
			{/if}
			{if (!empty($item->optionsDir['restaurant']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/restaurant.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Restaurant'} alt = {__ 'Restaurant'} />';
			{/if}
			{if (!empty($item->optionsDir['kiosk']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/kiosk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Kiosk'} alt = {__ 'Kiosk'} />';
			{/if}
			{if (!empty($item->optionsDir['internetAccess']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/internetAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Internet Access'} alt = {__ 'Internet Access'} />';
			{/if}
			{if (!empty($item->optionsDir['swimmingPool']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/swimmingPool.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Swimming'} alt = {__ 'Swimming'} />';
			{/if}
			{if (!empty($item->optionsDir['gamesRoom']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/gamesRoom.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Games Room'} alt = {__ 'Games Room'} />';
			{/if}
			{if (!empty($item->optionsDir['generatorsPermitted']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/generatorsPermitted.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Permitted'} alt = {__ 'Generators Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['generatorsNotPermitted']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/generatorsNotPermitted.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Not Permitted'} alt = {__ 'Generators Not Permitted'} />';
			{/if}
			{if (!empty($item->optionsDir['showGrounds']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/showGrounds.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Show Grounds'} alt = {__ 'Show Grounds'} />';
			{/if}
			{if (!empty($item->optionsDir['nationalParknForestry']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/nationalParknForestry.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'National Parks and Forestry'} alt = {__ 'National Parks and Forestry'} />';
			{/if}
			{if (!empty($item->optionsDir['pubs']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/pubs.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Pubs'} alt = {__ 'Pubs'} />';
			{/if}
			{if (!empty($item->optionsDir['sharedWithTrucks']))}				
				{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/sharedWithTrucks.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Shared With Trucks'} alt = {__ 'Shared With Trucks'} />';
			{/if}
			dynamicImageContent = '<div class="marker-content with-image" style="position: relative;"><div style="width:50%;float:right;padding:0;position:relative;left:0;top:0;padding:5px 0;">'+imagesContent+'</div>';
		{else}
			{var $thumbnailDir = $item->thumbnailDir}
		dynamicImageContent = '<div class="marker-content{ifset $item->thumbnailDir} with-image" style="position: relative;"><img src="{var $RealThumbnailUrl = getRealThumbnailUrl($item->thumbnailDir)}{thumbnailResize $RealThumbnailUrl, w => 120, h => 160}" alt=""></img>{else}">{/ifset}</div>';
		{/if}
		{/if}
		{if (empty(dynamicImageContent)) }
		    //#359
		    imageArray[{$item->ID}] = '<div class="marker-content"></div>';
		{else}
		    //#362
		    imageArray[{$item->ID}] = dynamicImageContent;
		{/if}
	{/foreach}
{else}
	var temp = 'items is empty';
{/if}
var mapDiv,
	map,
	infobox;
jQuery(document).ready(function($) {

	mapDiv = $("#directory-main-bar");
	clusterEnabled = true;
	mapDiv.height({!$themeOptions->directoryMap->mapHeight}).gmap3({
		map: {
			options: {
				{foreach parseMapOptions($themeOptions->directoryMap) as $key => $value}
				{if $iterator->first}{$key}: {!$value}{else},{$key}: {!$value}{/if}
				{/foreach}
				{if (isset($items)) && (count($items) == 1)}
				,center: [{ifset $items[0]->optionsDir['gpsLatitude']}{!$items[0]->optionsDir['gpsLatitude']}{else}0{/ifset},{ifset $items[0]->optionsDir['gpsLongitude']}{!$items[0]->optionsDir['gpsLongitude']}{else}0{/ifset}]
				,zoom: {!$themeOptions->directory->setZoomIfOne}
				{/if}
			}
			{if !isset($themeOptions->directoryMap->clusterDisable)}
			,events:{
				zoom_changed: function(map){
					clusterer = mapDiv.gmap3({ get: { name: "clusterer" }});
					if (map.getZoom() >= 19) {
						if (clusterEnabled) {
							clusterer.disable();
							clusterEnabled = false;
						}
					} else {
						if (!clusterEnabled) {
							clusterer.enable();
							clusterEnabled = true;
						}
					}
				}
			}
			{/if}
		}
		{if !empty($items)}
		,marker: {
			values: [
				{foreach $items as $item}
<?php					
	$previewString = "";
	$address2Str = "";
	if( !empty($item->optionsDir["wlm_city"]) || !empty($item->optionsDir["wlm_state"]) || !empty($item->optionsDir["zip"])){
		$address2 = array();
		if(!empty($item->optionsDir["wlm_city"]))$address2[] = $item->optionsDir["wlm_city"];
		if(!empty($item->optionsDir["wlm_state"]))$address2[] = $item->optionsDir["wlm_state"];
		if(!empty($item->optionsDir["wlm_zip"]))$address2[] = $item->optionsDir["wlm_zip"];
		$previewString.='<div class="address">'.implode(", ",$address2)."</div>";
	}
	if(is_array($item->optionsDir) && array_key_exists('directoryType', $item->optionsDir) && $item->optionsDir["directoryType"] == 'directory_5'){
	    if(!empty($item->optionsDir["property_type"])){
		$previewString.='<div class="property_type" style="margin-bottom:2px;padding-bottom:0px;">Property Type:'.$item->optionsDir["property_type"].'</div>';
	    }
	}
	
	if(is_array($item->optionsDir) && array_key_exists('directoryType', $item->optionsDir) && $item->optionsDir["directoryType"] == 'directory_6'){
	    if(isset($item->optionsDir["help_required"])){
		$previewString.='<div class="help_required" style="margin-bottom:2px;padding-bottom:0px;">Help Required:'.$item->optionsDir["help_required"].'</div>';
	    }
	}	
	
	if(is_array($item->optionsDir) && array_key_exists('directoryType', $item->optionsDir) && isset($item->optionsDir["location"]) && !empty($item->optionsDir["location"])){
	    if($item->optionsDir["directoryType"] == 'directory_1' || $item->optionsDir["directoryType"] == 'directory_6' || $item->optionsDir["directoryType"] == 'administrator' || $item->optionsDir["directoryType"] == ""){
		$previewString.='<div class="location" style="margin-bottom:2px;padding-bottom:0px;">Location:'.$item->optionsDir["location"].'</div>';
	    }
	}
	
	if(is_array($item->optionsDir) && array_key_exists('directoryType', $item->optionsDir) && isset($item->optionsDir["access"]) && !empty($item->optionsDir["access"])){
	    if($item->optionsDir["directoryType"] == 'directory_1' || $item->optionsDir["directoryType"] == 'administrator' || $item->optionsDir["directoryType"] == ""){
		$previewString.='<div class="access" style="margin-bottom:2px;padding-bottom:0px;">Access:'.$item->optionsDir["access"].'</div>';
	    }
	}
	
	if(
		is_array($item->optionsDir) && 
		array_key_exists('directoryType', $item->optionsDir) && (
		$item->optionsDir["directoryType"] == 'directory_4' ||
		$item->optionsDir["directoryType"] == 'directory_6' ||
		$item->optionsDir["directoryType"] == 'administrator')
	){
	    if(!empty($item->optionsDir["property_type"])){
		$previewString.='<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Property Type:'.$item->optionsDir["property_type"].'</div>';
	    }
	    if(!empty($item->optionsDir["start_date"])){
		$timestamp = strtotime($item->optionsDir["start_date"]);
		$startDate = date("j M Y", $timestamp);
		$item->optionsDir["start_date"] = $startDate;
		$previewString.='<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Start Date:'.$item->optionsDir["start_date"].'</div>';
	    }
	    if(!empty($item->optionsDir["end_date"])){
		$timestamp = strtotime($item->optionsDir["end_date"]);
		$endDate = date("j M Y", $timestamp);
		$item->optionsDir["end_date"] = $endDate;
		$previewString.='<div class="address" style="margin-bottom:2px;padding-bottom:0px;">End Date:'.$item->optionsDir["end_date"].'</div>';
	    }
	    if(!empty($item->optionsDir["duration"])){
		$previewString.='<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Duration:'.$item->optionsDir["duration"].'</div>';
	    }
	}
	$targetLnk = '_blank';
	$popuupJS = "";
	if(array_key_exists('iframe', $_REQUEST) && !empty($_REQUEST['iframe']) ){
	    $targetLnk = '_self';
		//$popuupJS = "onClick='jQuery.blockUI({ message: \"Please wait whilst we load the listing page.\" });'";
		$popuupJS = "onClick='frcLoadingMessage();'";
	}
?>
					{if isset($item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude']) && !(empty($item->optionsDir['gpsLatitude']) && empty($item->optionsDir['gpsLongitude']))}
					{
						<?php
							$pos = strpos($item->link, '?');
							$appendToURL = "";
							if($pos === false){
								//? is missing
								;//$appendToURL = "?nomap=1";
							}else{
								;//$appendToURL = "&nomap=1";
							}
						?>
						latLng: [{!$item->optionsDir['gpsLatitude']},{!$item->optionsDir['gpsLongitude']}],
						options: {
							icon: "{!$item->marker}",
							shadow: "{!$themeOptions->directoryMap->mapMarkerImageShadow}",
						},
						data: 	'<div class="marker-holder">'+
									imageArray[{$item->ID}]+
										'<div class="map-item-info" style="padding:9px;">'+
											'<div class="title">'+{ifset $item->post_title}{$item->post_title}+{/ifset}'</div>'+
											{if $item->rating}
											'<!--div class="rating">'+
												{for $i=1; $i <= $item->rating["max"]; $i++}
													'<div class="star{if $i <= $item->rating["val"]} active{/if}"></div>'+
												{/for}
											'</div-->'+
											{/if}
											'<div class="address">'+{ifset $item->optionsDir["address"]}{$item->optionsDir["address"]|nl2br}+{/ifset}'</div>'+{$previewString}+
											'<a target={$targetLnk}'+ {$popuupJS} +'href="{!$item->link}{!$appendToURL}" class="more-button">' + {__ 'VIEW MORE'} + '</a>'+
											'</div><div class="clear"></div><div class="arrow"></div>'+
										'</div>'+
									'</div>'+
								'</div>'
					}{if !($iterator->last)},{/if}
					{/if}
				{/foreach}
			],
			options:{
				draggable: false
			},
			{if !isset($themeOptions->directoryMap->clusterDisable)}
			cluster:{
				radius: {!((!empty($themeOptions->directoryMap->clusterRadius)) ? $themeOptions->directoryMap->clusterRadius : 20)},
				// This style will be used for clusters with more than 0 markers
				0: {
					content: "<div class='cluster cluster-1'>CLUSTER_COUNT</div>",
					width: 90,
					height: 80
				},
				// This style will be used for clusters with more than 20 markers
				20: {
					content: "<div class='cluster cluster-2'>CLUSTER_COUNT</div>",
					width: 90,
					height: 80
				},
				// This style will be used for clusters with more than 50 markers
				50: {
					content: "<div class='cluster cluster-3'>CLUSTER_COUNT</div>",
					width: 90,
					height: 80
				},
				events: {
					click: function(cluster) {
						map.panTo(cluster.main.getPosition());
						map.setZoom(map.getZoom() + 2);
					}
				}
			},
			{/if}
			events: {
				click: function(marker, event, context){
					var map = jQuery(this).gmap3("get");
					jQuery("#directory-main-bar").find('.infoBox').remove();
					if(context.data != "disabled"){

						var infoBoxOptions = {
							content: context.data,
							disableAutoPan: false,
							maxWidth: 150,
							pixelOffset: new google.maps.Size(-50, -65),
							boxStyle: {
								width: "290px"
							},
							closeBoxMargin: "4px",
							closeBoxURL: "{!$themeImgUrl}/map-icon/pop_up-close.png",
							// enableEventPropagation: true,
							infoBoxClearance: new google.maps.Size(1, 1),
							position: marker.position
						};
						infobox = new InfoBox(infoBoxOptions);
						//--------------------
						/*var oldDraw = infobox.draw;
						infobox.draw = function() {
							oldDraw.apply(this);
							jQuery(infobox.div_).hide();
							jQuery(infobox.div_).delay( 400 ).fadeIn('slow');
							//jQuery(infobox.div_).slideUp( 300 ).delay( 500 ).fadeIn('slow'); 
						}*/
						//--------------------
						//infobox.open(map,marker);//.delay( 800 ).fadeIn( 400 );
						infobox.open(map,marker).delay( 3800 ).fadeIn( 400 );
					}
					map.panTo(marker.getPosition());

					// if map is small
					var iWidth = 260;
					var iHeight = 300;
					if((mapDiv.width() / 2) < iWidth ){
						var offsetX = iWidth - (mapDiv.width() / 2);
						map.panBy(offsetX,0);
					}
					if((mapDiv.height() / 2) < iHeight ){
						var offsetY = -(iHeight - (mapDiv.height() / 2));
						map.panBy(0,offsetY);
					}
				}
			}
		}
		{/if} {* end empty items *}
	}{if (isset($items) && (count($items) > 1))},"autofit"{/if});

	map = mapDiv.gmap3("get");
	infobox = new InfoBox({
		pixelOffset: new google.maps.Size(-50, -65),
		closeBoxURL: "{!$themeImgUrl}/map-icon/pop_up-close.png",
		boxStyle: {
			width: "290px"
		},
		closeBoxMargin: "4px",
		enableEventPropagation: false
	});

	if (Modernizr.touch){
		{ifset $themeOptions->directoryMap->draggableForTouch}map.setOptions({ draggable : true });{else}map.setOptions({ draggable : false });{/ifset}
		{ifset $themeOptions->directoryMap->draggableToggleButton}
		var draggableClass = {ifset $themeOptions->directoryMap->draggableForTouch}'active'{else}'inactive'{/ifset};
		var draggableTitle = {ifset $themeOptions->directoryMap->draggableForTouch}{__ 'Deactivate map'}{else}{__ 'Activate map'}{/ifset};
		var draggableButton = $('<div class="draggable-toggle-button '+draggableClass+'">'+draggableTitle+'</div>').appendTo(mapDiv);
		draggableButton.click(function () {
			if($(this).hasClass('active')){
				$(this).removeClass('active').addClass('inactive').text({__ 'Activate map'});
				map.setOptions({ draggable : false });
			} else {
				$(this).removeClass('inactive').addClass('active').text({__ 'Deactivate map'});
				map.setOptions({ draggable : true });
			}
		});
		{/ifset}
	}

	{include 'ajaxfunctions-javascript.php'}

});
</script>