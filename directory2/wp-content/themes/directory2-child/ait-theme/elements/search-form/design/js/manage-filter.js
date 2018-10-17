function frc_SetGeoData() {
	if(navigator.geolocation) {
		console.log("navigator.geolocation is available");
		navigator.geolocation.getCurrentPosition(showPosition);
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			var latitude = pos.lat();var longitude = pos.lng();
			
			jQuery("#latitude-search").attr('value', latitude); jQuery("#latitude-search" ).removeAttr( "disabled" );
			jQuery("#longitude-search").attr('value', longitude); jQuery("#longitude-search" ).removeAttr( "disabled" );
            jQuery("#runits").attr('value', 'km');
		});
	}
}
function showPosition(position) { 
    var innerHTML = "Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude;//console.log(innerHTML);
}
frc_SetGeoData();
jQuery("#load_preference").on("click", function(){ load_preference(true);});

function load_preference(){
  jQuery.blockUI({ message: "Please wait while we are loading search criteria.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
  
	jQuery.post({
        type: 'POST',
        url: FilterJS.prefurl,
        data: {
                action:'fetch_search_filters',
                security:FilterJS.nonce,
                load_preference : 'load_preference'
                },
        beforeSend:function(){},
        success: function(response) {
                if(jQuery.isEmptyObject(response)){
                  jQuery.unblockUI();
                  alert("No Saved record found");
                }else{
                  if(!jQuery.isEmptyObject(response.locations)){
                    catArr = response.locations.split(',');
                    jQuery('#all_locations').val(catArr);
                    jQuery("#all_locations").multiselect("refresh");
                  }
                  if(!jQuery.isEmptyObject(response.custom_location)){
                    jQuery('#dir-custom-searchinput-text').val(response.custom_location);
                  }
                  if(!jQuery.isEmptyObject(response.radius)){
                    jQuery('#all_radiuses').multiselect('select',response.radius);
                  }
                  if(!jQuery.isEmptyObject(response.categories)){
                    catArr = response.categories.split(',');
                    jQuery('#all_categories').val(catArr);
                    jQuery("#all_categories").multiselect("refresh");
                  }
                  if(!jQuery.isEmptyObject(response.features)){
                    catArr = response.features.split(',');
                    jQuery('#all_features').val(catArr);
                    jQuery("#all_features").multiselect("refresh");
                  }
                  jQuery.unblockUI();
                }
				},error: function(xhr) { // if error occured
					jQuery.unblockUI();
					alert("Error occured.please try again");
				},dataType: 'json'
  });
}
jQuery("#btnPreference").on("click", function(){save_preference(true);});

jQuery("#btnGo").on("click", function(){
	locationIDs = jQuery('#all_locations').val();
	var searchArr = [];	//frc_SetGeoData();
	var locationCounter = 0;
	var locArr = [];
	jQuery('#all_locations option:selected').each(function() {
		locArr[locationCounter] = jQuery(this).val();
		locationCounter++;
    var locID = jQuery(this).val();
		searchArr.push("location[]="+locID);
  });
	
	jQuery('#all_categories option:selected').each(function() {
    var catID = jQuery(this).val();
		searchArr.push("category[]="+catID);
  });
	
	jQuery('#all_features option:selected').each(function() {
    var features = jQuery(this).val();
		searchArr.push("features[]="+features);
  });
	
	jQuery('#all_radiuses option:selected').each(function() {
    var radius = jQuery(this).val();
		if (radius.trim() != "Radius" && locationCounter > 1) {
			//If user have selected more than one state from dropdown, we can't take center of any state in that case. So we are taking central australia co-ordinates in this case.
			//Case : when user have selected multiple states in the dropdown
			//frc_SetGeoData();
			var latitude = jQuery("#latitude-search").val(); //jQuery("#frc_lat").val(); //	alert("lst="+latitude);
			var longitude = jQuery("#longitude-search").val(); //jQuery("#frc_lon").val(); //alert("lon="+longitude);
			latitude = "-22.755439"; longitude = "133.790845";//Central Australia
			searchArr.push("rad="+radius);
			searchArr.push("runits=km");
			searchArr.push("lat="+latitude);
			searchArr.push("lon="+longitude);
		}
  });
    
	
	if (searchArr.length >= 1) {searchArr.push("a=true");}
	var searchKW = jQuery("#dir-custom-searchinput-text").val();
	console.log("searchKW"+searchKW);
	frc_item_locs = [];jQuery('#all_locations option').each(function() {frc_item_locs[$(this).val()] = $(this).text();});
	
	if (locationCounter == 1) {
		//Case:  when user have selected single state in the dropdown; and added custom location in text box as well; priority in this case would be given to state dropdown.we will also get co-ordinates of the center of state. 
		console.log("locationCounter = 1");
		
		var state = frc_item_locs[locArr[0]];
		searchArr.push("s=");
		getLatLong(state, searchArr, locationCounter);
  }else if(!isEmpty(searchKW)) {
		if (searchArr.length == 0) {searchArr.push("a=true");}
		var address = searchKW + ", Australia";
		searchArr.push("s=");
		console.log("address 2"+address);
		getLatitudeLongitude(showResult, searchKW, searchArr);
		//lat_lon_ajax(address, searchArr); //or this one
		//searchArr.push("s="+searchKW);
	}else{
		searchArr.push("s=");
		var siteURL = AitSettings.home.url;
		var searchURL = siteURL+"/?"+searchArr.join('&'); console.log(searchURL);
		jQuery(location).attr('href', searchURL);	//console.log(searchURL);
		window.location.href = searchURL;
	}
	//jQuery( "#dir-custom-search-form" ).submit();
	//https://www.findlatitudeandlongitude.com/?loc=central+australia&id=499370#.Wp0aYJNubL8 <==
});

function getLatLong(address, searchArr, locationCounter) {
	var siteURL = AitSettings.home.url;
    loc_address = address + ", Australia";
    geocoder = new google.maps.Geocoder();
    if (geocoder) {
        geocoder.geocode({
            'address': loc_address
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
							var resultObj = results[0];
							var lat = resultObj.geometry.location.lat();
							var lon = resultObj.geometry.location.lng();
							var jsonObj = {lat: lat,lon: lon};
							var rad = 20;
							if (locationCounter == 1) rad="";
							
							jQuery('#all_radiuses option:selected').each(function() {
								var radius = jQuery(this).val();
								if (radius.trim() != "Radius") {
									rad = radius.trim();
								}
							});
							searchArr.push("rad="+rad);
							searchArr.push("runits=km");
							searchArr.push("lat="+lat);
							searchArr.push("lon="+lon);
							var searchURL = siteURL+"/?"+searchArr.join('&'); console.log("search url:"+searchURL);
							alert(searchURL);
							jQuery(location).attr('href', searchURL);	
							window.location.href = searchURL;
              //return callback(results[0]);
            }else{
							window.alert('Geocode was not successful for the following reason: ' + status);
							return {status: status};
						}
        });
    }
}
function isEmpty(value) {
  return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
}
//https://www.webbjocke.com/javascript-check-data-types/
function frc_isNull (value) {return value === null;};
function frc_isUndefined (value) {return typeof value === 'undefined';}
function frc_isBoolean (value) {return typeof value === 'boolean';};

function result_dialog(){
	jQuery( "#resultDialog" ).show();
	jQuery( "#search_dialog" ).dialog({
	  resizable: false,
	  height:140,
	  modal: true,
	  buttons: {
				  "Close": function() {
					jQuery( this ).dialog( "close" );
				  }
				}
	});
}
function search_dialog(){
	jQuery( "#search_dialog" ).show();
	jQuery( "#search_dialog" ).dialog({
	  resizable: false,
	  height:140,
	  modal: true,
	  buttons: {
				  "Close": function() {
					jQuery( this ).dialog( "close" );
				  }
				}
	});
}

function save_preference(ajax){
	var savePrefURL = FilterJS.prefurl;
	var locRadius = '';
	locationRadius = jQuery('#all_radiuses').val();
	if(locationRadius) locRadius = locationRadius;
	
	
	var location = 0;
	locationIDs = jQuery('#all_locations').val();
	if(locationIDs){
		location = locationIDs;
	}
	
	var category = 0;
	categoryIDs = jQuery('#all_categories').val();
	if(categoryIDs){
		category = categoryIDs;
	}
	
	var feature = '';
	featureIDs = jQuery('#all_features').val();
	if(featureIDs){ 
		feature = featureIDs;
	}
	
	search = jQuery('#dir-custom-searchinput-text').val();
	jQuery.blockUI({ message: "Please wait while we save your search preferences.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
	var data = {
		'action': 'save_filter',
		category : category,
		loc_radius: locRadius,
		location : location,
		feature:feature,
		search : search,
		security: FilterJS.loadingNounce
	};
	
	jQuery.post(savePrefURL, data, function(response){
		jQuery.unblockUI();
		jQuery( "#preference_dialog" ).show();
		jQuery( "#preference_dialog" ).dialog({
			resizable: false,
			height:180,
			width:500,
			modal: false,
			//stack: false,
			//zIndex: 9999,
			buttons: {
			  "Close": function() {
				jQuery( this ).dialog( "close" );
					console.log(response);
			  }
			}
		});
	}).fail(function(e) { jQuery.unblockUI();console.log("AJAX ERROR", e); });
}

function  lat_lon_ajax(address, searchArr) {
	//https://developers.google.com/maps/documentation/geocoding/intro
	//https://developers.google.com/maps/documentation/javascript/geocoding
	
  var remote_url = "https://maps.googleapis.com/maps/api/geocode/json";
	var req_params = {
		'address': 'Queanbeyan, NSW, Australia',
		'key' : "AIzaSyBbKQYBMWlpSYjSJG3wUt5F4D6pNNaGjrA",
		'sensor': false
	};
	
	jQuery.get( remote_url, req_params).done(function( data ) {
		if (data.status == 'OK') { //other can be ZERO_RESULTS |OVER_QUERY_LIMIT | INVALID_REQUEST |ERROR
			var latitude = data.results[0].geometry.location.lat;
			var longitude = data.results[0].geometry.location.lng;
			jQuery("#frc_lat").val(latitude);jQuery("#frc_lon").val(longitude);
			console.log("latitude : "+ latitude);console.log("latitude : "+ longitude);
		}else{
			//if location not found send location as search kw
		}
	}, "json");
}

function showResult(result) {
    lat = result.geometry.location.lat();
    lon = result.geometry.location.lng();
		var jsonObj = {lat: lat,lon: lon};
		return jsonObj;
}

function getLatitudeLongitude(callback, address, searchArr) {
	//https://jsfiddle.net/alvaroAV/qn8bb8q5/
  // If adress is not supplied, use default value 'Darwin, NT, Australia'
	var siteURL = AitSettings.home.url;
    loc_address = address + ", Australia";
    geocoder = new google.maps.Geocoder();	// Initialize the Geocoder
    if (geocoder) {
        geocoder.geocode({
            'address': loc_address
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
							var resultObj = results[0];
							var lat = resultObj.geometry.location.lat();
							var lon = resultObj.geometry.location.lng();
							var jsonObj = {lat: lat,lon: lon};
							var rad = 20;
							jQuery('#all_radiuses option:selected').each(function() {
								var radius = jQuery(this).val();
								if (radius.trim() != "Radius") {
									rad = radius.trim();
								}
							});
							searchArr.push("rad="+rad);
							searchArr.push("runits=km");
							searchArr.push("lat="+lat);
							searchArr.push("lon="+lon);
							searchArr.push("frc_loc="+address);
							var searchURL = siteURL+"/?"+searchArr.join('&'); console.log("search url:"+searchURL);
							jQuery(location).attr('href', searchURL);	
							window.location.href = searchURL;
											//return callback(results[0]);
						}else{
							window.alert('Geocode was not successful for the following reason: ' + status);
							return {status: status};
						}
        });
    }
}

/*
var button = document.getElementById('btn');

button.addEventListener("click", function () {
    var address = document.getElementById('address').value;
    getLatitudeLongitude(showResult, address)
});
*/


pucho