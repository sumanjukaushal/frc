<script type="text/javascript">
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
					{if isset($item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude']) && !(empty($item->optionsDir['gpsLatitude']) && empty($item->optionsDir['gpsLongitude']))}
					{
						latLng: [{!$item->optionsDir['gpsLatitude']},{!$item->optionsDir['gpsLongitude']}],
						options: {
							icon: "{!$item->marker}",
							shadow: "{!$themeOptions->directoryMap->mapMarkerImageShadow}",
						},
						data: 	'<div class="marker-holder">'+
									'<div class="marker-content{ifset $item->thumbnailDir} with-image"><img src="{var $RealThumbnailUrl = getRealThumbnailUrl($item->thumbnailDir)}{thumbnailResize $RealThumbnailUrl, w => 120, h => 160}" alt="">{else}">{/ifset}'+
										'<div class="map-item-info">'+
											'<div class="title">'+{ifset $item->post_title}{$item->post_title}+{/ifset}'</div>'+
											{if $item->rating}
											'<div class="rating">'+
												{for $i=1; $i <= $item->rating["max"]; $i++}
													'<div class="star{if $i <= $item->rating["val"]} active{/if}"></div>'+
												{/for}
											'</div>'+
											{/if}
											'<div class="address">'+{ifset $item->optionsDir["address"]}{$item->optionsDir["address"]|nl2br}+{/ifset}'</div>'+
											'<a href="{!$item->link}" class="more-button">' + {__ 'VIEW MORE'} + '</a>'+
											'</div><div class="arrow"></div>'+
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

						infobox.open(map,marker);
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