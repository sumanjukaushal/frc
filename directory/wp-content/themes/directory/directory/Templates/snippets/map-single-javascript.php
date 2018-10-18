<style>
.infoBox > img {
	right:0;
	height:15px;
	width:15px;
}
</style>

{if isset($options['gpsLatitude'], $options['gpsLongitude']) && !(empty($options['gpsLatitude']) && empty($options['gpsLongitude']))}
<script type="text/javascript">
		//wp-content
	//alert({$themeImgUrl});
<?php
	//echo "/*rasu mamp-single";
	//print_r($items);
	//echo "*/";
	$wpContentPos = strpos($themeImgUrl,"wp-content");
	$wpContentPath = substr($themeImgUrl,0,$wpContentPos+strlen("wp-content"));	
	$contentURL = content_url();
	$imagesURL = "{$contentURL}/uploads/sites/3/2014/04/facility_icons/30x30/jpg/";
	//$imagesURL = str_replace(array(":","/"),array("%3A","%2F"),$imagesURL)
?>
	{var $url = content_url()}
	//alert({$contentURL});
	//<img src="{timthumb src => $imageSrc, w => 30, h => 30}" 12title
	//<img src="{$imageSrc}" width="30" height="30" title
	//{var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/toilets.jpg";}
	var map,
		mapDiv,
		smallMapDiv,
		infobox;

	jQuery(document).ready(function($) {
		mapDiv = $("#directory-main-bar");
		smallMapDiv = $(".item-map");

		smallMapDiv.width(300).height(300).gmap3({
			map: {
				options: {
					center: [{ifset $options['gpsLatitude']}{$options['gpsLatitude']}{else}0{/ifset}, {ifset $options['gpsLongitude']}{$options['gpsLongitude']}{else}0{/ifset}],
					zoom: 18,
					scrollwheel: false
				}
			},
			marker: {
				values: [
					{
						latLng: [{ifset $options['gpsLatitude']}{$options['gpsLatitude']}{else}0{/ifset}, {ifset $options['gpsLongitude']}{$options['gpsLongitude']}{else}0{/ifset}]
					}
				]
			}
		});

		mapDiv.height({!$themeOptions->directoryMap->mapHeight}).gmap3({
			map: {
				options: {
					{foreach parseMapOptions($themeOptions->directoryMap) as $key => $value}
					{if $iterator->first}{$key}: {!$value}{else},{$key}: {!$value}{/if}
					{/foreach}
					{if count($items) <= 1}
					,center: [{ifset $options['gpsLatitude']}{$options['gpsLatitude']}{else}0{/ifset}, {ifset $options['gpsLongitude']}{$options['gpsLongitude']}{else}0{/ifset}]
					,zoom: {!$themeOptions->directory->setZoomIfOne}
					{/if}
				}

			},
			marker: {
				values: [
					{foreach $items as $item}
<?php					
	$previewString = "";	
	if($item->optionsDir["directoryType"] == 'directory_5' || $item->optionsDir["directoryType"] == ''){
		//if(!empty($item->optionsDir["property_type"])){
			$previewString.='<div class="property_type" style="margin-bottom:2px;padding-bottom:0px;">Property Type:'.$item->optionsDir["property_type"].'</div>';
		//}
	}
	
	if($item->optionsDir["directoryType"] == 'directory_6' || $item->optionsDir["directoryType"] == ''){
		if(isset($item->optionsDir["help_required"])){
			$previewString.='<div class="help_required" style="margin-bottom:2px;padding-bottom:0px;">Help Required:'.$item->optionsDir["help_required"].'</div>';
		}
	}
	
	if(
	   $item->optionsDir["directoryType"] == 'directory_1' ||
	   $item->optionsDir["directoryType"] == 'directory_6' ||
	   $item->optionsDir["directoryType"] == 'administrator' ||
	   $item->optionsDir["directoryType"] == ''
	){
		if(isset($item->optionsDir["location"]) && !empty($item->optionsDir["location"])){
			$previewString.='<div class="location" style="margin-bottom:2px;padding-bottom:0px;">Location:'.$item->optionsDir["location"].'</div>';
		}
	}
	
	if(
	   $item->optionsDir["directoryType"] == 'directory_1' ||	  
	   $item->optionsDir["directoryType"] == 'administrator' ||
	   $item->optionsDir["directoryType"] == ''
	){
		if(isset($item->optionsDir["access"]) && !empty($item->optionsDir["access"])){
			$previewString.='<div class="access" style="margin-bottom:2px;padding-bottom:0px;">Access:'.$item->optionsDir["access"].'</div>';
		}
	}
	
	if(
		$item->optionsDir["directoryType"] == 'directory_4' ||
		$item->optionsDir["directoryType"] == 'directory_6' ||
		$item->optionsDir["directoryType"] == 'administrator' ||
		$item->optionsDir["directoryType"] == ''
	){
		if(!empty($item->optionsDir["property_type"])){
			$previewString.='<div class="property_type" style="margin-bottom:2px;padding-bottom:0px;">Property Type:'.$item->optionsDir["property_type"].'</div>';
		}
		if(!empty($item->optionsDir["start_date"])){
			$timestamp = strtotime($item->optionsDir["start_date"]);
			$startDate = date("j M Y", $timestamp);
			$item->optionsDir["start_date"] = $startDate;
			$previewString.='<div class="start_date" style="margin-bottom:2px;padding-bottom:0px;">Start Date:'.$item->optionsDir["start_date"].'</div>';
		}
		if(!empty($item->optionsDir["end_date"])){
			$timestamp = strtotime($item->optionsDir["end_date"]);
			$endDate = date("j M Y", $timestamp);
			$item->optionsDir["end_date"] = $endDate;
			$previewString.='<div class="end_date" style="margin-bottom:2px;padding-bottom:0px;">End Date:'.$item->optionsDir["end_date"].'</div>';
		}
		if(!empty($item->optionsDir["duration"])){
			$previewString.='<div class="duration" style="margin-bottom:2px;padding-bottom:0px;">Duration:'.$item->optionsDir["duration"].'</div>';
		}
	}
	$address2Str = "";
	if( !empty($item->optionsDir["wlm_city"]) || !empty($item->optionsDir["wlm_state"]) || !empty($item->optionsDir["zip"])){
		$address2 = array();
		if(!empty($item->optionsDir["wlm_city"]))$address2[] = $item->optionsDir["wlm_city"];
		if(!empty($item->optionsDir["wlm_state"]))$address2[] = $item->optionsDir["wlm_state"];
		if(!empty($item->optionsDir["wlm_zip"]))$address2[] = $item->optionsDir["wlm_zip"];
		$address2Str = '<div class="address">'.implode(", ",$address2)."</div>";
	}
	
	$pos = strpos($item->link, '?');
	$appendToURL = "";
	if($pos === false){
		//? is missing
		;//$appendToURL = "?nomap=1";
	}else{
		;//$appendToURL = "&nomap=1";
	}
?>					
						{
							latLng: [{ifset $item->optionsDir['gpsLatitude']}{!$item->optionsDir['gpsLatitude']}{else}0{/ifset},{ifset $item->optionsDir['gpsLongitude']}{!$item->optionsDir['gpsLongitude']}{else}0{/ifset}],
							options: {
								icon: "{!$item->marker}",
								shadow: "{!$themeOptions->directoryMap->mapMarkerImageShadow}",
							},
							data: 	'<div class="marker-holder">'+
										'<div class="marker-content{ifset $item->thumbnailDir} with-image" style="position: relative;"><img src="{var $RealThumbnailUrl = getRealThumbnailUrl($item->thumbnailDir)}{thumbnailResize $RealThumbnailUrl, w => 120, h => 160}" alt="">{else}">{/ifset}'+
											'<div class="map-item-info">'+
												'<div class="title">'+{ifset $item->post_title}{$item->post_title}+{/ifset}'</div>'+
												{if $item->rating}
												'<div class="rating">'+
													{for $i=1; $i <= $item->rating["max"]; $i++}
														'<div class="star{if $i <= $item->rating["val"]} active{/if}"></div>'+
													{/for}
												'</div>'+
												{/if}
												'<div class="address">'+{ifset $item->optionsDir["address"]}{$item->optionsDir["address"]|nl2br}+{/ifset}'</div>'+{$address2Str}+{$previewString}+
												'<a target="_blank" href="{!$item->link}{!$appendToURL}" class="more-button">' + {__ 'VIEW MORE'} + '</a>'+
												'</div><div class="clear"></div><div class="arrow"></div>'+
											'</div>'+
										'</div>'+
									'</div>',
							tag: "marker-{!$item->ID}"
						}
					{if !($iterator->last)},{/if}
					{/foreach}
				],
				options:{
					draggable: false
				},
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
							google.maps.event.addListener(infobox, "mouseover", function (e) {
								//alert("Mouse Over");
								//log("Mouse Over");
							});
							infobox.open(map,marker).delay( 800 ).fadeIn( 400 );
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
			{ifset $isGeolocation}
			,getgeoloc:{
				callback : function(latLng){

					$(this).gmap3({
						marker: {
							latLng: latLng,
							options: {
								animation: google.maps.Animation.DROP
							}
						}
					});

				}
			}
			{/ifset}
			{ifset $options['showStreetview']}
			,streetviewpanorama:{
				options: {
					container: mapDiv,
					opts:{
						position: [parseFloat({$options['streetViewLatitude']}),parseFloat({$options['streetViewLongitude']})],
						pov: {
							heading: parseFloat({$options['streetViewHeading']}),
							pitch: parseFloat({$options['streetViewPitch']}),
							zoom: parseInt({$options['streetViewZoom']})
						},
						scrollwheel : {ifset $themeOptions->directoryMap->scrollwheel}true{else}false{/ifset},
						enableCloseButton : true
					}
				}
			}
			{/ifset}
		}{if count($items) > 1},"autofit"{/if});

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

		var currMarker = mapDiv.gmap3({ get: { name: "marker", tag: "marker-{!$post->id}"}});
		//<!-- rasa -->
		var dynamicImageContent = "";
		{if
			(!empty($options['toilets'])) ||        
			(!empty($options['disableFacility'])) ||        
			(!empty($options['showers'])) ||        
			(!empty($options['drinkingWater'])) ||        
			(!empty($options['drinkingWaterNotSuitable'])) ||        
			(!empty($options['waterFillingAvailable'])) ||        
			(!empty($options['wasteFacilityAvailable'])) ||        
			(!empty($options['dumpPointAvailable'])) ||        
			(!empty($options['poweredSites'])) ||        
			(!empty($options['emergencyPhone'])) ||        
			(!empty($options['mobilePhoneReception'])) ||        
			(!empty($options['petsOk'])) ||        
			(!empty($options['petsNotOk'])) ||        
			(!empty($options['fourWheelDrive'])) ||        
			(!empty($options['dryWeatherOnlyAccess'])) ||        
			(!empty($options['tentsOnly'])) ||        
			(!empty($options['noTents'])) ||        
			(!empty($options['camperTrailers'])) ||        
			(!empty($options['noCamperTrailers'])) ||        
			(!empty($options['caravans'])) ||        
			(!empty($options['largeSizeMotohomeAccess'])) ||        
			(!empty($options['bigrig'])) ||        
			(!empty($options['selfContainedVehicles'])) ||        
			(!empty($options['onSiteAccomodation'])) ||        
			(!empty($options['seasonalRatesApply'])) ||        
			(!empty($options['discount'])) ||        
			(!empty($options['offer'])) ||        
			(!empty($options['rvTurnAroundArea'])) ||        
			(!empty($options['rvParkingAvailable'])) ||        
			(!empty($options['boatRamp'])) ||        
			(!empty($options['timeLimitApplies'])) ||        
			(!empty($options['shadedSites'])) ||        
			(!empty($options['firePit'])) ||        
			(!empty($options['noFirePit'])) ||        
			(!empty($options['picnicTable'])) ||        
			(!empty($options['bbq'])) ||        
			(!empty($options['shelter'])) ||        
			(!empty($options['childrenPlayground'])) ||        
			(!empty($options['views'])) ||        
			(!empty($options['parkFees'])) ||        
			(!empty($options['campKitchen'])) ||        
			(!empty($options['laundry'])) ||        
			(!empty($options['ensuiteSites'])) ||        
			(!empty($options['restaurant']))
		}
		{if
			(!empty($options['kiosk'])) ||
			(!empty($options['internetAccess'])) ||
			(!empty($options['swimmingPool']))||
			(!empty($options['gamesRoom']))||
			(!empty($options['generatorsPermitted'])) ||
			(!empty($options['generatorsNotPermitted'])) ||
			(!empty($options['showGrounds'])) ||
			(!empty($options['nationalParknForestry'])) ||
			(!empty($options['pubs'])) ||
			(!empty($options['sharedWithTrucks']))
		}
		var imagesContent = "";
			{var $url = content_url()}
			{if (!empty($options['toilets']))}				
				{var $imageSrc = "{$imagesURL}toilets.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Toilets Available'} alt = {__ 'Toilets Available'} />';
			{/if}
			//--------1---------------
		
			{if (!empty($options['disableFacility']))}				
				{var $imageSrc = "{$imagesURL}disableFacility.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Disabled Facility'} alt = {__ 'Disabled Facility'} />';
			{/if}
			//--------2---------------
		
			{if (!empty($options['showers']))}				
				{var $imageSrc = "{$imagesURL}showers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Showers Available'} alt = {__ 'Showers Available'} />';
			{/if}
			//--------3---------------
		
			{if (!empty($options['drinkingWater']))}				
				{var $imageSrc = "{$imagesURL}drinkingWater.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Drinking Water Available'} alt = {__ 'Drinking Water Available'} />';
			{/if}
			//--------4---------------
		
			{if (!empty($options['drinkingWaterNotSuitable']))}				
				{var $imageSrc = "{$imagesURL}drinkingWaterNotSuitable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Water Not Suitable For Drinking'} alt = {__ 'Water Not Suitable For Drinking'} />';
			{/if}
			//--------5---------------
		
			{if (!empty($options['waterFillingAvailable']))}				
				{var $imageSrc = "{$imagesURL}waterFillingAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Water Filling Available'} alt = {__ 'Water Filling Available'} />';
			{/if}
			//--------6---------------
		
			{if (!empty($options['wasteFacilityAvailable']))}				
				{var $imageSrc = "{$imagesURL}wasteFacilityAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Rubbish Bins Available'} alt = {__ 'Rubbish Bins Available'} />';
			{/if}
			//--------7---------------
		
			{if (!empty($options['dumpPointAvailable']))}				
				{var $imageSrc = "{$imagesURL}dumpPointAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Dump Point Available'} alt = {__ 'Dump Point Available'} />';
			{/if}
			//--------8---------------
		
			{if (!empty($options['poweredSites']))}				
				{var $imageSrc = "{$imagesURL}poweredSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Power Available'} alt = {__ 'Power Available'} />';
			{/if}
			//--------9---------------
		
			{if (!empty($options['emergencyPhone']))}				
				{var $imageSrc = "{$imagesURL}emergencyPhone.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Emergency Phone'} alt = {__ 'Emergency Phone'} />';
			{/if}
			//--------10---------------
		
			{if (!empty($options['mobilePhoneReception']))}				
				{var $imageSrc = "{$imagesURL}mobilePhoneReception.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Mobile Phone Reception'} alt = {__ 'Mobile Phone Reception'} />';
			{/if}
			//--------11---------------
		
			{if (!empty($options['petsOk']))}				
				{var $imageSrc = "{$imagesURL}petsOk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Pets Permitted'} alt = {__ 'Pets Permitted'} />';
			{/if}
			//--------12---------------
		
			{if (!empty($options['petsNotOk']))}				
				{var $imageSrc = "{$imagesURL}petsNotOk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Pets Permitted'} alt = {__ 'No Pets Permitted'} />';
			{/if}
			//--------13---------------
		
			{if (!empty($options['fourWheelDrive']))}				
				{var $imageSrc = "{$imagesURL}fourWheelDrive.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ '4WD Drive Access Only'} alt = {__ '4WD Drive Access Only'} />';
			{/if}
			//--------14---------------
		
			{if (!empty($options['dryWeatherOnlyAccess']))}				
				{var $imageSrc = "{$imagesURL}dryWeatherOnlyAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Dry Weather Access'} alt = {__ 'Dry Weather Access'} />';
			{/if}
			//--------15---------------
		
			{if (!empty($options['tentsOnly']))}				
				{var $imageSrc = "{$imagesURL}tentsOnly.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Suitable for Tents'} alt = {__ 'Suitable for Tents'} />';
			{/if}
			//--------16---------------
		
			{if (!empty($options['noTents']))}				
				{var $imageSrc = "{$imagesURL}noTents.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Not Suitable for Tents'} alt = {__ 'Not Suitable for Tents'} />';
			{/if}
			//--------17---------------
		
			{if (!empty($options['camperTrailers']))}				
				{var $imageSrc = "{$imagesURL}camperTrailers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camper Trailers'} alt = {__ 'Camper Trailers'} />';
			{/if}
			//--------18---------------
		
			{if (!empty($options['noCamperTrailers']))}				
				{var $imageSrc = "{$imagesURL}noCamperTrailers.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Camper Trailers'} alt = {__ 'No Camper Trailers'} />';
			{/if}
			//--------19---------------
		
			{if (!empty($options['caravans']))}				
				{var $imageSrc = "{$imagesURL}caravans.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Caravans'} alt = {__ 'Caravans'} />';
			{/if}
			//--------20---------------
		
			{if (!empty($options['largeSizeMotohomeAccess']))}				
				{var $imageSrc = "{$imagesURL}largeSizeMotohomeAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Campervans  & Motorhomes'} alt = {__ 'Campervans  & Motorhomes'} />';
			{/if}
			//--------21---------------
		
			{if (!empty($options['bigrig']))}				
				{var $imageSrc = "{$imagesURL}bigrig.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Big Rig Access'} alt = {__ 'Big Rig Access'} />';
			{/if}
			//--------22---------------
		
			{if (!empty($options['selfContainedVehicles']))}				
				{var $imageSrc = "{$imagesURL}selfContainedVehicles.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fully Self Contained Vehicles Only'} alt = {__ 'Fully Self Contained Vehicles Only'} />';
			{/if}
			//--------23---------------
		
			{if (!empty($options['onSiteAccomodation']))}				
				{var $imageSrc = "{$imagesURL}onSiteAccomodation.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'On Site Accommodation'} alt = {__ 'On Site Accommodation'} />';
			{/if}
			//--------24---------------
		
			{if (!empty($options['seasonalRatesApply']))}				
				{var $imageSrc = "{$imagesURL}seasonalRatesApply.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Seasonal Rates Apply'} alt = {__ 'Seasonal Rates Apply'} />';
			{/if}
			//--------25---------------
		
			{if (!empty($options['discount']))}				
				{var $imageSrc = "{$imagesURL}discount.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Discounts Apply'} alt = {__ 'Discounts Apply'} />';
			{else}
				{if ($options['discount']['enable']) == 'enable'}				
					{var $imageSrc = "{$imagesURL}discount.png";}
					imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {$options['discount_detail']} alt = {$options['discount_detail']} />';
				{/if}
			{/if}
			//--------26---------------
		
			{if (!empty($options['offer']))}				
				{var $imageSrc = "{$imagesURL}offer.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Offers Apply'} alt = {__ 'Offers Apply'} />';
			{else}
				{if ($options['offer']['enable']) == 'enable'}				
					{var $imageSrc = "{$imagesURL}offer.png";}
					imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {$options['offer_detail']} alt = {$options['offer_detail']} />';
				{/if}
			{/if}
			//--------27---------------
		
			{if (!empty($options['rvTurnAroundArea']))}				
				{var $imageSrc = "{$imagesURL}rvTurnAroundArea.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Turn Around Area'} alt = {__ 'RV Turn Around Area'} />';
			{/if}
			//--------28---------------
		
			{if (!empty($options['rvParkingAvailable']))}				
				{var $imageSrc = "{$imagesURL}rvParkingAvailable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Parking Available'} alt = {__ 'RV Parking Available'} />';
			{/if}
			//--------29---------------
		
			{if (!empty($options['boatRamp']))}				
				{var $imageSrc = "{$imagesURL}boatRamp.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Boat Ramp Nearby'} alt = {__ 'Boat Ramp Nearby'} />';
			{/if}
			//--------30---------------
		
			{if (!empty($options['timeLimitApplies']))}				
				{var $imageSrc = "{$imagesURL}timeLimitApplies.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Time Limits Apply'} alt = {__ 'Time Limits Apply'} />';
			{/if}
			//--------31---------------
		
			{if (!empty($options['shadedSites']))}				
				{var $imageSrc = "{$imagesURL}shadedSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Some Shade Available'} alt = {__ 'Some Shade Available'} />';
			{/if}
			//--------32---------------
		
			{if (!empty($options['firePit']))}				
				{var $imageSrc = "{$imagesURL}firePit.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fires Permitted'} alt = {__ 'Fires Permitted'} />';
			{/if}
			//--------33---------------
		
			{if (!empty($options['noFirePit']))}				
				{var $imageSrc = "{$imagesURL}noFirePit.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Fires Permitted'} alt = {__ 'No Fires Permitted'} />';
			{/if}
			//--------34---------------
		
			{if (!empty($options['picnicTable']))}				
				{var $imageSrc = "{$imagesURL}picnicTable.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Picnic Table Available'} alt = {__ 'Picnic Table Available'} />';
			{/if}
			//--------35---------------
		
			{if (!empty($options['bbq']))}				
				{var $imageSrc = "{$imagesURL}bbq.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'BBQ Available'} alt = {__ 'BBQ Available'} />';
			{/if}
			//--------36---------------
		
			{if (!empty($options['shelter']))}				
				{var $imageSrc = "{$imagesURL}shelter.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Shelter Available'} alt = {__ 'Shelter Available'} />';
			{/if}
			//--------37---------------
		
			{if (!empty($options['childrenPlayground']))}				
				{var $imageSrc = "{$imagesURL}childrenPlayground.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Childrens Playground Available'} alt = {__ 'Childrens Playground Available'} />';
			{/if}
			//--------38---------------
		
			{if (!empty($options['views']))}				
				{var $imageSrc = "{$imagesURL}views.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Nice Views From Location'} alt = {__ 'Nice Views From Location'} />';
			{/if}
			//--------39---------------
		
			{if (!empty($options['parkFees']))}				
				{var $imageSrc = "{$imagesURL}parkFees.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'National Park Fees'} alt = {__ 'National Park Fees'} />';
			{else}
				{if ($options['parkFees']['enable']) == 'enable'}				
					{var $imageSrc = "{$imagesURL}parkFees.png";}
					imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {$options['parkFees_addnl']} alt = {$options['parkFees_addnl']} />';
				{/if}
				var parkFees = '{$options['parkFees_addnl']} {$options['parkFees']}';
			{/if}
			//--------40---------------
		
			{if (!empty($options['campKitchen']))}				
				{var $imageSrc = "{$imagesURL}campKitchen.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camp Kitchen'} alt = {__ 'Camp Kitchen'} />';
			{/if}
			//--------41---------------
		
			{if (!empty($options['laundry']))}				
				{var $imageSrc = "{$imagesURL}laundry.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Laundry'} alt = {__ 'Laundry'} />';
			{/if}
			//--------42---------------
		
			{if (!empty($options['ensuiteSites']))}				
				{var $imageSrc = "{$imagesURL}ensuiteSites.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Ensuite Sites'} alt = {__ 'Ensuite Sites'} />';
			{/if}
			//--------43---------------
		
			{if (!empty($options['restaurant']))}				
				{var $imageSrc = "{$imagesURL}restaurant.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Restaurant'} alt = {__ 'Restaurant'} />';
			{/if}
			//--------44---------------
		
			{if (!empty($options['kiosk']))}				
				{var $imageSrc = "{$imagesURL}kiosk.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Kiosk'} alt = {__ 'Kiosk'} />';
			{/if}
			//--------45---------------
		
			{if (!empty($options['internetAccess']))}				
				{var $imageSrc = "{$imagesURL}internetAccess.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Internet Access'} alt = {__ 'Internet Access'} />';
			{/if}
			//--------46---------------
		
			{if (!empty($options['swimmingPool']))}				
				{var $imageSrc = "{$imagesURL}swimmingPool.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Swimming'} alt = {__ 'Swimming'} />';
			{/if}
			//--------47---------------
		
			{if (!empty($options['gamesRoom']))}				
				{var $imageSrc = "{$imagesURL}gamesRoom.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Games Room'} alt = {__ 'Games Room'} />';
			{/if}
			//--------48---------------
		
			{if (!empty($options['generatorsPermitted']))}				
				{var $imageSrc = "{$imagesURL}generatorsPermitted.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Permitted'} alt = {__ 'Generators Permitted'} />';
			{/if}
			//--------49---------------
		
			{if (!empty($options['generatorsNotPermitted']))}				
				{var $imageSrc = "{$imagesURL}generatorsNotPermitted.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Not Permitted'} alt = {__ 'Generators Not Permitted'} />';
			{/if}
			//--------50---------------
		
			{if (!empty($options['showGrounds']))}				
				{var $imageSrc = "{$imagesURL}showGrounds.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Show Grounds'} alt = {__ 'Show Grounds'} />';
			{/if}
			//--------51---------------
		
			{if (!empty($options['nationalParknForestry']))}				
				{var $imageSrc = "{$imagesURL}nationalParknForestry.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'National Parks and Forestry'} alt = {__ 'National Parks and Forestry'} />';
			{/if}
			//--------52---------------
		
			{if (!empty($options['pubs']))}				
				{var $imageSrc = "{$imagesURL}pubs.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Pubs'} alt = {__ 'Pubs'} />';
			{/if}
			//--------53---------------
		
			{if (!empty($options['sharedWithTrucks']))}				
				{var $imageSrc = "{$imagesURL}sharedWithTrucks.png";}
				imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Shared With Trucks'} alt = {__ 'Shared With Trucks'} />';
			{/if}
			//--------54---------------
			
			//************* end of new additions on 7th Nov*************
			
			dynamicImageContent = '<div class="marker-content with-image"  style="position: relative;"><div style="width:50%;float:right;padding:0;position:relative;left:0; top:0;padding:5px 0;">'+imagesContent+'</div>';
		{else}
				dynamicImageContent = '<div class="marker-content{if !empty($thumbnailDir)} with-image" style="position: relative;"><img src="{thumbnailResize $thumbnailDir, w => 120, h => 160}" alt="view more" title="view more">{else}">{/if}';
				/*test nest ekse*/
		{/if}
		{/if}
		<?php
		$address2Str = "";
		if( !empty($options["wlm_city"]) || !empty($options["wlm_state"]) || !empty($options["zip"])){
			$address2 = array();
			if(!empty($options["wlm_city"]))$address2[] = $options["wlm_city"];
			if(!empty($options["wlm_state"]))$address2[] = $options["wlm_state"];
			if(!empty($options["wlm_zip"]))$address2[] = $options["wlm_zip"];
			$address2Str = '<div class="address">'.implode(", ",$address2)."</div>";
		}
		?>
		//rasa map-single-javascript.php
		
		infobox.setContent(
			'<div class="marker-holder">'+
				dynamicImageContent+
					'<div class="map-item-info">'+
						'<div class="title">'+{$post->title}+'</div>'+
						{if $rating}
						'<!--div class="rating">'+
							{for $i=1; $i <= $rating["max"]; $i++}
								'<div class="star{if $i <= $rating["val"]} active{/if}"></div>'+
							{/for}
						'</div--><!--rasu--><!--/rating-->'+
						{/if}
				{if $options["directoryType"] != 'directory_4'}
					'<div class="address" style="margin-bottom:2px;padding-bottom:5px;">'+{ifset $options["address"]}{$options["address"]|nl2br}+{/ifset}'</div>'+{$address2Str}+
					{ifset $options["discount_detail"]}
						{if (!empty($options['discount_detail']))}
							//'<div class="address">'+{$options["discount"]['enable']}+{$options["discount_detail"]|nl2br}+'</div>'+
						{/if}
					{/ifset}
					{ifset $options["offer_detail"]}
						{if (!empty($options['offer_detail']))}
							//'<div class="address">'+{$options["offer_detail"]|nl2br}+'</div>'+
						{/if}
					{/ifset}
				{/if}
				{if $options["directoryType"] == 'directory_5'}
					{ifset $options["property_type"]}
						{if (!empty($options['property_type']))}						
						'<div class="property_type">Property Type:'+{$options["property_type"]}+'</div>'+
						{/if}
					{/ifset}
				{/if}
				{if $options["directoryType"] == 'directory_6'}
					{ifset $options["help_required"]}
						'<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Help Required:'+{$options["help_required"]}+'</div>'+
					{/ifset}
				{/if}
				
									
				{ifset $options["location"]}
					{if ($options["directoryType"] == 'directory_1' || $options["directoryType"] == 'directory_6' || $options["directoryType"] == 'administrator' || $options["directoryType"] == "") && !empty($options["location"])}
					
						'<div class="location" style="margin-bottom:2px;padding-bottom:0px;">Location:'+{$options["location"]}+'</div>'+
					
					{/if}
				{/ifset}
				
				{ifset $options["access"]}
					{if ($options["directoryType"] == 'directory_1' || $options["directoryType"] == 'administrator' || $options["directoryType"] == "") && !empty($options["access"])}
						'<div class="access" style="margin-bottom:2px;padding-bottom:0px;">Access:'+{$options["access"]}+'</div>'+
					{/if}
				{/ifset}
				
				{if $options["directoryType"] == 'directory_4' || $options["directoryType"] == 'directory_6' ||  $options["directoryType"] == 'administrator'}
					{ifset $options["property_type"]}
						{if (!empty($options['property_type']))}						
						'<div class="address">Property Type:'+{$options["property_type"]}+'</div>'+
						{/if}
					{/ifset}
					{ifset $options["start_date"]}
						{if (!empty($options['start_date']))}
						<?php
							$timestamp = strtotime($options["start_date"]);
							$startDate = date("j M Y", $timestamp);
							$options["start_date"] = $startDate;
							#echo "'<div class=\"address\">{$startDate}</div>'";
						?>
						'<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Start Date:'+{$options["start_date"]}+'</div>'+
						{/if}
					{/ifset}
					{ifset $options["end_date"]}
						{if (!empty($options['end_date']))}
						<?php
							$timestamp = strtotime($options["end_date"]);
							$endDate = date("j M Y", $timestamp);
							$options["end_date"] = $endDate;
							#echo "'<div class=\"address\">{$startDate}</div>'";
						?>
							'<div class="address" style="margin-bottom:2px;padding-bottom:0px;">End Date:'+{$options["end_date"]}+'</div>'+
						{/if}
					{/ifset}
					{ifset $options["duration"]}
						{if (!empty($options['duration']))}
							'<div class="address" style="margin-bottom:2px;padding-bottom:0px;">Duration:'+{$options["duration"]}+'</div>'+
						{/if}
					{/ifset}
				{/if}
				
						'<a target="_blank" href="{!$post->link}{!$appendToURL}" class="more-button">' + {__ 'VIEW MORE'} + '</a>'+
				'</div><!--/map-item-info-->'+
				"<div class='clear'></div><div class='arrow'></div><div class='close' onClick='"+'$(".infobox").dialog('+'"close");'+'</div>'+
				'</div><!--/marker-content-->'+
				'</div><!--/marker holder-->'+
			'</div>'
		);
		infobox.open(map,currMarker);

		{if count($items) > 1}
		map.panTo(new google.maps.LatLng({!$options['gpsLatitude']}, {!$options['gpsLongitude']}));
		{/if}



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
{/if}