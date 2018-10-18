var map, mapObject, marker, streetview, streetviewObject, sLatitude, sLongitude, sHeading, sPitch, sZoom;

jQuery(document).ready(function($) {
	// map
	var latTextfield = $('#ait-_ait-dir-item-gpsLatitude');
	var lonTextfield = $('#ait-_ait-dir-item-gpsLongitude');
	// streetview
	var streetviewCheckbox = $('#ait-_ait-dir-item-showStreetview-enable');
	sLatitude = $('#ait-_ait-dir-item-streetViewLatitude');
	sLongitude = $('#ait-_ait-dir-item-streetViewLongitude');
	sHeading = $('#ait-_ait-dir-item-streetViewHeading');
	sPitch = $('#ait-_ait-dir-item-streetViewPitch');
	sZoom = $('#ait-_ait-dir-item-streetViewZoom');
	// hide these options
	$('#ait-_ait-dir-item-streetViewLatitude-option').hide();
	$('#ait-_ait-dir-item-streetViewLongitude-option').hide();
	$('#ait-_ait-dir-item-streetViewHeading-option').hide();
	$('#ait-_ait-dir-item-streetViewPitch-option').hide();
	$('#ait-_ait-dir-item-streetViewZoom-option').hide();

	var initLat = (latTextfield.val()) ? latTextfield.val() : 0;
	var initLon = (lonTextfield.val()) ? lonTextfield.val() : 0;

	var latRow = $('#ait-_ait-dir-item-gpsLatitude-option');
	var streetviewCheckboxRow = $('#ait-_ait-dir-item-showStreetview-option');
	var mapRow = $('<tr valign="top" id="ait-map-option"><td scope="row" class="ait-custom-fields-label"><label for="ait-map-select">Set position</label></td><td><div id="ait-map-select"></div></td></tr>');
	var streetviewRow = $('<tr valign="top" id="ait-streetview-option"><td scope="row" class="ait-custom-fields-label"><label for="ait-streetview-select">Set streetview position (If window is gray then sreetview is not available for this position and you must use stick figure in map above to define correct streetview position)</label></td><td><div id="ait-streetview-select"></div></td></tr>');

	latRow.before(mapRow);
	streetviewCheckboxRow.after(streetviewRow);

	map = mapRow.find('#ait-map-select');
	map.width('95%').height(500);

	streetview = streetviewRow.find('#ait-streetview-select');
	streetview.width('95%').height(500);

	//var sFirsttime = (sLatitude.val()) ? false : true;
	var initsLat = (sLatitude.val()) ? parseFloat(sLatitude.val()) : initLat;
	var initsLon = (sLongitude.val()) ? parseFloat(sLongitude.val()) : initLon;
	var initHeading = (sHeading.val()) ? parseFloat(sHeading.val()) : 0;
	var initPitch = (sPitch.val()) ? parseFloat(sPitch.val()) : 0;
	var initZoom = (sZoom.val()) ? parseInt(sZoom.val()) : 0;

	sHeading.val(initHeading);
	sPitch.val(initPitch);
	sZoom.val(initZoom);

	var streetviewOptions = {
		container: streetview,
		opts:{
			position: new google.maps.LatLng(initsLat,initsLon),
			pov: {
				heading: initHeading,
				pitch: initPitch,
				zoom: initZoom
			}
		}
	}

	map.gmap3({
		map: {
			events: {
				click:function(mapLocal, event){
					map.gmap3({
						get: {
							name: "marker",
							callback: function(marker){
								marker.setPosition(event.latLng);
								var pos = marker.getPosition();
								latTextfield.val(pos.lat());
								lonTextfield.val(pos.lng());
							}
						}
					});
				}
			},
			options: {
				center: [initLat,initLon],
				zoom: 3
			}
		},
		marker: {
			values:[
				{latLng:[initLat, initLon]}
	        ],
			options: {
				draggable: true
			},
			events: {
				dragend: function(marker){		
					var pos = marker.getPosition();
					latTextfield.val(pos.lat());
					lonTextfield.val(pos.lng());
				}
			}
		},
		streetviewpanorama:{
			options: streetviewOptions,
			events: {
				position_changed: function (obj) {
					sLatitude.val(obj.position.lat());
					sLongitude.val(obj.position.lng());
				},
				pov_changed: function (obj) {
					sHeading.val(obj.pov.heading);
					sPitch.val(obj.pov.pitch);
					sZoom.val(obj.pov.zoom);
				}
			}
		}
	});

	mapObject = map.gmap3({
		get: {
			name: "map"
		}
	});

	marker = map.gmap3({
		get: {
			name: "marker"
		}
	});

	streetviewObject = map.gmap3({
		get: {
			name: "streetviewpanorama"
		}
	});

	latTextfield.keyup(function (event) {
		var value = $(this).val();
		var center = mapObject.getCenter();
		var newCenter = new google.maps.LatLng(parseFloat(value),center.lng());
		mapObject.setCenter(newCenter);
		marker.setPosition(newCenter);
	});

	lonTextfield.keyup(function (event) {
		var value = $(this).val();
		var center = mapObject.getCenter();
		var newCenter = new google.maps.LatLng(center.lat(), parseFloat(value));
		mapObject.setCenter(newCenter);
		marker.setPosition(newCenter);
	});

	if(streetviewCheckbox.is(':checked')){
		if(!sLatitude.val() || (parseFloat(sLatitude.val()) == 0 && parseFloat(sLongitude.val()) == 0)){
			var center = mapObject.getCenter();
			streetviewObject.setPosition(center);
		}
	} else {
		streetviewObject.setVisible(false);
		streetviewRow.hide();
	}

	streetviewCheckbox.change(function (obj) {
		if ($(this).is(':checked')) {
			if(!sLatitude.val() || (parseFloat(sLatitude.val()) == 0 && parseFloat(sLongitude.val()) == 0)){
				var center = mapObject.getCenter();
				streetviewObject.setPosition(center);
			}
			streetviewRow.show();
			streetviewObject.setVisible(true);
		} else {
			streetviewObject.setVisible(false);
			streetviewRow.hide();
		}
	});

	// find address functionality
	var findAddress = $('<a id="find-address-button" href="#" class="button">Find address on map</a>');
	findAddress.insertAfter('#ait-_ait-dir-item-address');
	findAddress.after('<span id="find-address-info-status" style="margin-left: 20px;"></span>');
	findAddress.click(function (event) {
		event.preventDefault();
		var addr = $('#ait-_ait-dir-item-address').val();
		if ( !addr || !addr.length ) return;
		map.gmap3({
			getlatlng:{
				address:  addr,
				callback: function(results){
					if ( !results ) {
						$('#find-address-info-status').text('No results found!').show().fadeOut(2000);
					} else {
						$('#find-address-info-status').text('Address found!').show().fadeOut(2000);
						marker.setPosition(results[0].geometry.location);
						map.gmap3({
							map: {
								options: {
									zoom: 10,
									center: results[0].geometry.location
								}
							}
						})
						latTextfield.val(results[0].geometry.location.lat());
						lonTextfield.val(results[0].geometry.location.lng());
					}
				}
			}
		});
	});


});