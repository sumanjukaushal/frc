<?php
    $user_ID = get_current_user_id();
	
?>
// loading spinner
var opts = {
	lines: 13, // The number of lines to draw
	length: 9, // The length of each line
	width: 9, // The line thickness
	radius: 27, // The radius of the inner circle
	corners: 1, // Corner roundness (0..1)
	rotate: 0, // The rotation offset
	color: '#FFF', // #rgb or #rrggbb
	speed: 1.8, // Rounds per second
	trail: 81, // Afterglow percentage
	shadow: true, // Whether to render a shadow
	hwaccel: false, // Whether to use hardware acceleration
	className: 'spinner', // The CSS class to assign to the spinner
	zIndex: 2e9, // The z-index (defaults to 2000000000)
	top: 'auto', // Top position relative to parent in px
	left: 'auto' // Left position relative to parent in px
};

var target = document.getElementById('directory-main-bar'),
	spinner = new Spinner(opts).spin(target),
	spinnerDiv = mapDiv.find('.spinner');

var search = $('#directory-search'),
	searchInput = $('#dir-searchinput-text'),
	searchSubmit = $('#dir-searchsubmit'),
	categoryInput = $('#dir-searchinput-category'),
	locationInput = $('#dir-searchinput-location'),
	geoInput = $('#dir-searchinput-geo'),
	geoInputRadius = $('#dir-searchinput-geo-radius'),
	geoInputLat = $('#dir-searchinput-geo-lat'),
	geoInputLng = $('#dir-searchinput-geo-lng');

function showLoading(disableSubmit) {
	var topPosition = mapDiv.height() / 2;
	//spinnerDiv.css('top',topPosition+'px').fadeIn();
	jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });
	//jQuery(spinnerDiv).append("<div class='loading'></div>").fadeIn();
	if (disableSubmit) {
		searchSubmit.attr('disabled', true);
	}
}

function hideLoading() {
        jQuery.unblockUI();
	//spinnerDiv.fadeOut();
	searchSubmit.attr('disabled', false);
}

// set interactive search
if(search.data('interactive') == 'yes'){
	searchInput.typeWatch({
		callback: function() {
			ajaxGetMarkers(true,false);
		},
		wait: 500,
		highlight: false,
		captureLength: 0
	});

	categoryInput.on("autocompleteselect", function( event, ui ) {
		ajaxGetMarkers(true,false,ui.item.value,false);
	});
	locationInput.on("autocompleteselect", function( event, ui ) {
		ajaxGetMarkers(true,false,false,ui.item.value);
	});
	categoryInput.on("autocompleteclose", function( event, ui ) {
		if($('#dir-searchinput-category-id').val() == '0'){
			ajaxGetMarkers(true,false);
		}
	});
	locationInput.on("autocompleteclose", function( event, ui ) {
		if($('#dir-searchinput-location-id').val() == '0'){
			ajaxGetMarkers(true,false);
		}
	});
}
//--------------------------------------------------------------rasa--------------------------------
function custom_ajaxGetMarkers(ajax,geoloc)
{
    //reset = undefind   
    showLoading();
    var locRadius = '';
    locationRadius = $('#all_radiuses').val()
    if(locationRadius){
        locRadius = locationRadius;
    }
    
    var location = 0;
    locationIDs = $('#all_locations').val()
    if(locationIDs){
        location = locationIDs;
    }
        
    radius = new Array();

    var category = 0;
    categoryIDs = $('#all_categories').val()
    if(categoryIDs){
        category = categoryIDs;
    }
    
    var feature = '';
    featureIDs = $('#all_features').val()
    if(featureIDs){
        feature = featureIDs;
    }
	
    var search = '';
    var locale = '<?php echo substr(get_locale(), 0, 2); ?>';
   
    if(ajax){
        //we will focus on this case for testing
        search = $('#dir-custom-searchinput-text').val();
        var ajaxGeo = $('#dir-searchinput-geo').attr("checked");
        if(ajaxGeo && !reset){
            var inputRadius = geoInputRadius.val();
            if(!isNaN(inputRadius)){
                    radius.push(parseInt(inputRadius));
            } else {
                    geoInputRadius.val(geoInputRadius.data('default-value'));
                    radius.push(parseInt(geoInputRadius.data('default-value')));
            }
            radius.push(parseFloat(geoInputLat.val()));
            radius.push(parseFloat(geoInputLng.val()));
        }
    } else {
        if(reset){
            category = parseInt(mapDiv.data('category'));
            location = parseInt(mapDiv.data('location'));
            search = mapDiv.data('search');
        } else {
            if(geoloc){
                radius.push(parseInt({ifset $geolocationRadius}{$geolocationRadius}{else}100{/ifset}));
                radius.push(geoloc.lat());
                radius.push(geoloc.lng());

                category = parseInt(mapDiv.data('category'));
                location = parseInt(mapDiv.data('location'));
                search = mapDiv.data('search');
            }
        }
    }
    // get items from ajax
    $.post(
        // MyAjax defined in functions.php
        MyAjax.ajaxurl,
        {
            action : 'get_items',
            category : category,
            loc_radius: locRadius,
            location : location,
            feature:feature,
            search : search,
            radius : radius,
            wpml_lang : locale
        },
        function( response ) {
             hideLoading();
             if(jQuery.isEmptyObject(response)){
                //alert('Sorry, we do not have anything that meets your search criteria, please refine your search and try again.');
                //--------------
                jQuery( "#notification_dialog" ).show();
                jQuery( "#notification_dialog" ).dialog({
                 resizable: false,
                    height:140,
                    modal: true,
                    buttons: {
                      "Close": function() {
                        jQuery( this ).dialog( "close" );
                      }
                   }
                });
                //--------------
            }
            // show reset ajax button
            if((!geoloc) && ($('#directory-search').data('interactive') == 'yes')){
                $('#directory-search .reset-ajax').show();
            }
            {if isset($themeOptions->search->interactiveReplaceContent) && ($themeOptions->search->interactiveReplaceContent == 'enabled') }
            if((!reset) && (!geoloc)){
                generateContent(response);
            }else{
              generateContent(response);
            }
            {/if}
            generateRASUContent(response, category, location, search, locRadius, feature);
            
            if(ajaxGeo){
                var ajaxGeoObj = new google.maps.LatLng(parseFloat(geoInputLat.val()),parseFloat(geoInputLng.val()));
                generateMarkers(response,ajaxGeoObj,true);
            } else {
                generateMarkers(response,geoloc);
            }
        }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
		hideLoading();
                
                switch (jqXHR.status) {
                   case '400':
                   case 400:
                     alert('400 status code! user error. Please try other combination (e.g.: Try by selecting few categories)');
                     break;
                   case '500':
                   case 500: 
                     alert('500 status code! server error. Please try other combination (e.g.: Try by selecting few categories)');
                     break;
                   default:
		     result_dialog();
		     console.log("AJAX ERROR", e);
                }
	});
}
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
jQuery('#btnUpdate').click(function(){
<?php
    if(!empty($user_ID)){
        echo "custom_ajaxGetMarkers(true,false);";
    }else{
        echo "search_dialog();";
    }
?>
    //custom_ajaxGetMarkers(true,false);      
    /*var request = $.ajax({
      url: "#echo $homeUrlwp-admin/admin-ajax.php",
      dataType :"json",
      contentType:"application/x-www-form-urlencoded",
      type:"GET",
      data: {
              action:"get_items",
              category: $('#all_categories').val(),
              location: $('#all_locations').val(),
              search:$('#dir-custom-searchinput-text').val(),
              wpml_lang:"en"			      
              },
      success: function(result){
        $("#div1").html(result);
      }
    });*/
});

jQuery(document).ready(function() {
    jQuery('#dir-custom-search-form').on('reset', function() {
        /*
        jQuery('#all_radiuses').multiselect({
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Radius';
                }
            }
        });*/
        jQuery('#dir-custom-searchinput-text').val("");
        
        jQuery('#all_radiuses option:selected').each(function() {
            jQuery(this).prop('selected', false);
        });    
        jQuery('#all_radiuses').multiselect('refresh');
        //$("#select_id option:selected").removeAttr("selected");
        //$("select.multiselect").multiselect("deselectAll", false);
        
        jQuery('#all_categories option:selected').each(function() {
            jQuery(this).prop('selected', false);
        });    
        jQuery('#all_categories').multiselect('refresh');
        
        jQuery('#all_locations option:selected').each(function() {
            jQuery(this).prop('selected', false);
        });
        jQuery('#all_locations').multiselect('refresh');
        
        jQuery('#all_features option:selected').each(function() {
            jQuery(this).prop('selected', false);
        });
        jQuery('#all_features').multiselect('refresh');
    });
});

/*
jQuery('#de_select_dropdowns').on('click', function() {
    jQuery('option', jQuery('#all_categories')).each(function(element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
    
    jQuery('option', jQuery('#all_locations')).each(function(element) {
        jQuery(this).removeAttr('selected').prop('selected', false);
    });
    
    jQuery('option', jQuery('#all_features')).each(function(element) {
        $(this).removeAttr('selected').prop('selected', false);
    });
});

jQuery('#btnClearFilters').click(function(){
    jQuery('#all_radiuses').multiselect({
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Radius';
                }
            }
    });
    jQuery('#dir-custom-searchinput-text').val("");    
    jQuery('#all_categories').multiselect('refresh');   
    jQuery('#all_locations').multiselect('refresh');    
    jQuery('#all_locations').multiselect('refresh');
    jQuery('#all_radiuses').multiselect('deselectAll',true);
    jQuery('#all_radiuses').multiselect('refresh');
    //$('#all_categories').prop("checked", false);
    //$('#all_locations').removeAttr('checked');
    //$('#all_locations').prop("checked", false);
});
*/
//-------------------------------------------------------------------------------------------
$('#dir-searchinput-settings .icon').click(function() {
	$('#dir-search-advanced').toggle();
});

$('#dir-search-advanced-close').click(function() {
	$('#dir-search-advanced').hide();
});

$('#dir-search-advanced .value-slider').slider({
	value: geoInputRadius.val(),
	min: {ifset $themeOptions->search->advancedSearchMinValue}{$themeOptions->search->advancedSearchMinValue}{else}5{/ifset},
	max: {ifset $themeOptions->search->advancedSearchMaxValue}{$themeOptions->search->advancedSearchMaxValue}{else}2000{/ifset},
	step: {ifset $themeOptions->search->advancedSearchStepValue}{$themeOptions->search->advancedSearchStepValue}{else}5{/ifset},
	change: function(event, ui) {
		if(search.data('interactive') == 'yes' && geoInput.is(':checked')){
			ajaxGetMarkers(true,false);
		}
	},
	slide: function(event, ui) {
		geoInputRadius.val(ui.value);
	}
});

// Geolocation

geoInput.FancyCheckbox();

function enableGeo() {
	geoInput.attr('checked', true);
	geoInput.next().removeClass('off').addClass('on');
}

function disableGeo() {
	geoInput.attr('checked', false);
	geoInput.next().removeClass('on').addClass('off');
}

{if isset($isGeolocation, $_GET['geo-lat'], $_GET['geo-lng'], $_GET['geo-radius'])}
	generateOnlyGeo({$_GET['geo-lat']},{$_GET['geo-lng']},{$_GET['geo-radius']});
{else}
	if(geoInput.is(":checked")) {

		// enable after user allow geolocation
		disableGeo();
		showLoading(true);

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {

				geoInputLat.val(position.coords.latitude);
				geoInputLng.val(position.coords.longitude);

				enableGeo();

				ajaxGetMarkers(true,false);

			}, function(error){
				disableGeo();
				hideLoading();
			});
		} else {
			disableGeo();
			hideLoading();
		}

	}
{/if}

geoInput.on("change",function(event) {

	if(geoInput.is(":checked")) {

		// enable after user allow geolocation
		disableGeo();
		showLoading(true);

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {

				geoInputLat.val(position.coords.latitude);
				geoInputLng.val(position.coords.longitude);

				enableGeo();

				if(search.data('interactive') == 'yes'){
					ajaxGetMarkers(true,false);
				} else {
					hideLoading();
				}

			}, function(error){
				disableGeo();
				hideLoading();
			});
		} else {
			disableGeo();
			hideLoading();
		}

	} else {

		if(search.data('interactive') == 'yes'){
			ajaxGetMarkers(true,false);
		}

	}

});

function ajaxGetMarkers(ajax,geoloc,rewriteCategory,rewriteLocation,reset) {

	showLoading();

	radius = new Array();

	var category = 0;
	var location = 0;
	var search = '';
	var locale = '<?php echo substr(get_locale(), 0, 2); ?>';

	if(ajax){
		if(rewriteCategory){
			category = rewriteCategory;
		} else {
			category = $('#dir-searchinput-category-id').val();
		}
		if(rewriteLocation){
			location = rewriteLocation;
		} else {
			location = $('#dir-searchinput-location-id').val();
		}
		search = $('#dir-searchinput-text').val();

		var ajaxGeo = $('#dir-searchinput-geo').attr("checked");

		if(ajaxGeo && !reset){
			var inputRadius = geoInputRadius.val();
			if(!isNaN(inputRadius)){
				radius.push(parseInt(inputRadius));
			} else {
				geoInputRadius.val(geoInputRadius.data('default-value'));
				radius.push(parseInt(geoInputRadius.data('default-value')));
			}
			radius.push(parseFloat(geoInputLat.val()));
			radius.push(parseFloat(geoInputLng.val()));
		}
	} else {
		if(reset){
			category = parseInt(mapDiv.data('category'));
			location = parseInt(mapDiv.data('location'));
			search = mapDiv.data('search');
		} else {
			if(geoloc){
				radius.push(parseInt({ifset $geolocationRadius}{$geolocationRadius}{else}100{/ifset}));
				radius.push(geoloc.lat());
				radius.push(geoloc.lng());

				category = parseInt(mapDiv.data('category'));
				location = parseInt(mapDiv.data('location'));
				search = mapDiv.data('search');
			}
		}
	}
	// get items from ajax
	$.post(
		// MyAjax defined in functions.php
		MyAjax.ajaxurl,
	    {
	        action : 'get_items',
	        category : category,
	        location : location,
	        search : search,
	        radius : radius,
	        wpml_lang : locale
	    },
	    function( response ) {
	    	// show reset ajax button
	    	if((!reset) && (!geoloc) && ($('#directory-search').data('interactive') == 'yes')){
	    		$('#directory-search .reset-ajax').show();
	    	}
	    	{if isset($themeOptions->search->interactiveReplaceContent) && ($themeOptions->search->interactiveReplaceContent == 'enabled') }
	    	if((!reset) && (!geoloc)){
	    		generateContent(response);
	    	}
	    	{/if}
	    	if(ajaxGeo && !reset){
	    		var ajaxGeoObj = new google.maps.LatLng(parseFloat(geoInputLat.val()),parseFloat(geoInputLng.val()));
	    		generateMarkers(response,ajaxGeoObj,true);
	    	} else {
	    		generateMarkers(response,geoloc);
	    	}
	    }
	)
	.fail(function(e) { console.log("AJAX ERROR", e); });
}
{var $url = content_url()} 
{var $siteURL = site_url()}
function generateMarkers(dataRaw,geoloc,ajaxGeo) {

	// clear map
	infobox.close();
	mapDiv.gmap3({ clear: { } });

	map.setZoom({!$themeOptions->directory->setZoomIfOne});

	var len = $.map(dataRaw, function(n, i) { return i; }).length;

	var i = 0;
	// prepare data
	var data = new Array();
	for(var key in dataRaw){
            //----------------------------rasu-------------------------------
            var imagesContent = "";            
            if( dataRaw[key].optionsDir.hasOwnProperty("toilets") &&
                ( dataRaw[key].optionsDir['toilets']['enable'] == "enable" || dataRaw[key].optionsDir['toilets'] == 'Y')){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/toilets.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Toilet Facilities'}" alt = "{__ 'Toilet Facilities'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("disableFacility") &&
                (dataRaw[key].optionsDir['disableFacility']['enable'] == "enable" || dataRaw[key].optionsDir['disableFacility'] == "Y")){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/disableFacility.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Disabled Facilities Available'}" alt = "{__ 'Disabled Facilities Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("showers") &&
                ( dataRaw[key].optionsDir['showers']['enable'] == "enable" || dataRaw[key].optionsDir['showers'] == 'Y') ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/showers.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Showers Available'}" alt = "{__ 'Showers Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("drinkingWater") &&
                ( dataRaw[key].optionsDir['drinkingWater']['enable'] == "enable" || dataRaw[key].optionsDir['drinkingWater'] == "Y")){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/drinkingWater.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Drinking Water Available'}" alt = "{__ 'Drinking Water Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("drinkingWaterNotSuitable") &&
                (dataRaw[key].optionsDir['drinkingWaterNotSuitable']['enable'] == "enable" || dataRaw[key].optionsDir['drinkingWaterNotSuitable'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/drinkingWaterNotSuitable.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Water Not Suitable For Drinking'}" alt = "{__ 'Water Not Suitable For Drinking'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("wasteFacilityAvailable") &&
                (dataRaw[key].optionsDir['wasteFacilityAvailable']['enable'] == "enable" || dataRaw[key].optionsDir['wasteFacilityAvailable'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/wasteFacilityAvailable.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Rubbish Bins Available'}" alt = "{__ 'Rubbish Bins Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("dumpPointAvailable") &&
                (dataRaw[key].optionsDir['dumpPointAvailable']['enable'] == "enable" || dataRaw[key].optionsDir['dumpPointAvailable'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/dumpPointAvailable.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Dump Point Available'}" alt = "{__ 'Dump Point Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("poweredSites") &&
                (dataRaw[key].optionsDir['poweredSites']['enable'] == "enable" || dataRaw[key].optionsDir['poweredSites'] == "Y")){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/poweredSites.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Power Available'}" alt = "{__ 'Power Available'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("emergencyPhone") &&
                (dataRaw[key].optionsDir['emergencyPhone']['enable'] == "enable" || dataRaw[key].optionsDir['emergencyPhone'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/emergencyPhone.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Emergency Phone'}" alt = "{__ 'Emergency Phone'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("mobilePhoneReception") &&
                (dataRaw[key].optionsDir['mobilePhoneReception']['enable'] == "enable" || dataRaw[key].optionsDir['mobilePhoneReception'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/mobilePhoneReception.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src="{$imageSrc}" title = "{__ 'Mobile Phone Reception'}" alt = "{__ 'Mobile Phone Reception'}" />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("petsOk") &&
                (dataRaw[key].optionsDir['petsOk']['enable'] == "enable" || dataRaw[key].optionsDir['petsOk'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/petsOk.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Pets Permitted'} alt = {__ 'Pets Permitted'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("petsNotOk") &&
                (dataRaw[key].optionsDir['petsNotOk']['enable'] == "enable" && dataRaw[key].optionsDir['petsNotOk'] == "Y")){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/petsNotOk.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'No Pets Permitted'} alt = {__ 'No Pets Permitted'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("fourWheelDrive") &&
                (dataRaw[key].optionsDir['fourWheelDrive']['enable'] == "enable" || dataRaw[key].optionsDir['fourWheelDrive'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/fourWheelDrive.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ '4WD Drive Access Only'} alt = {__ '4WD Drive Access Only'} />';    
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("dryWeatherOnlyAccess") &&
                (dataRaw[key].optionsDir['dryWeatherOnlyAccess']['enable'] == "enable" || dataRaw[key].optionsDir['dryWeatherOnlyAccess'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/dryWeatherOnlyAccess.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Access Dry Weather Only'} alt = {__ 'Access Dry Weather Only'} />';    
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("tentsOnly") &&
                (dataRaw[key].optionsDir['tentsOnly']['enable'] == "enable" || dataRaw[key].optionsDir['tentsOnly'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/tentsOnly.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Suitable for Tents'} alt = {__ 'Suitable for Tents'} />';    
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("largeSizeMotohomeAccess") &&
                (dataRaw[key].optionsDir['largeSizeMotohomeAccess']['enable'] == "enable" || dataRaw[key].optionsDir['largeSizeMotohomeAccess'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/largeSizeMotohomeAccess.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camper Vans & Motor Homes'} alt = {__ 'Camper Vans & Motor Homes'} />';    
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("bigrig") &&
                (dataRaw[key].optionsDir['bigrig']['enable'] == "enable" || dataRaw[key].optionsDir['bigrig'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/bigrig.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Big Rig Access'} alt = {__ 'Big Rig Access'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("selfContainedVehicles") &&
                (dataRaw[key].optionsDir['selfContainedVehicles']['enable'] == "enable" || dataRaw[key].optionsDir['selfContainedVehicles'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/selfContainedVehicles.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fully Self Contained Vehicles Only'} alt = {__ 'Fully Self Contained Vehicles Only'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("rvTurnAroundArea")&& 
                (dataRaw[key].optionsDir['rvTurnAroundArea']['enable'] == "enable" || dataRaw[key].optionsDir['rvTurnAroundArea'] == "Y") ){
                //only enable in this case
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/rvTurnAroundArea.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Turn Around Area'} alt = {__ 'RV Turn Around Area'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("rvParkingAvailable") && 
                ( dataRaw[key].optionsDir['rvParkingAvailable']['enable'] == "enable" || dataRaw[key].optionsDir['rvParkingAvailable'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/rvParkingAvailable.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'RV Parking Available'} alt = {__ 'RV Parking Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("boatRamp") &&
                (dataRaw[key].optionsDir['boatRamp']['enable'] == "enable" || dataRaw[key].optionsDir['boatRamp'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/boatRamp.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Boat Ramp Available'} alt = {__ 'Boat Ramp Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("timeLimitApplies") &&
                (dataRaw[key].optionsDir['timeLimitApplies']['enable'] == "enable" && dataRaw[key].optionsDir['timeLimitApplies'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/timeLimitApplies.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Time Limit Applies'} alt = {__ 'Time Limit Applies'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("shadedSites") &&
                (dataRaw[key].optionsDir['shadedSites']['enable'] == "enable" || dataRaw[key].optionsDir['shadedSites'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/shadedSites.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Some Shade Available'} alt = {__ 'Some Shade Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("firePit") &&
                (dataRaw[key].optionsDir['firePit']['enable'] == "enable" || dataRaw[key].optionsDir['firePit'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/firePit.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Fires Permitted'} alt = {__ 'Fires Permitted'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("picnicTable") &&
                (dataRaw[key].optionsDir['picnicTable']['enable'] == "enable" || dataRaw[key].optionsDir['picnicTable'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/picnicTable.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Picnic Table Available'} alt = {__ 'Picnic Table Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("bbq") &&
                (dataRaw[key].optionsDir['bbq']['enable'] == "enable" || dataRaw[key].optionsDir['bbq'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/bbq.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'BBQs Available'} alt = {__ 'BBQs Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("shelter") &&
                (dataRaw[key].optionsDir['shelter']['enable'] == "enable" || dataRaw[key].optionsDir['shelter'] == "Y")){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/shelter.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Shelter Available'} alt = {__ 'Shelter Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("childrenPlayground") &&
                (dataRaw[key].optionsDir['childrenPlayground']['enable'] == "enable" || dataRaw[key].optionsDir['childrenPlayground'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/childrenPlayground.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Childrens Playground Available'} alt = {__ 'Childrens Playground Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("views") &&
                (dataRaw[key].optionsDir['views']['enable'] == "enable" || dataRaw[key].optionsDir['views'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/views.jpg";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Nice Views From Location'} alt = {__ 'Nice Views From Location'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("parking") &&
                (dataRaw[key].optionsDir['parking']['enable'] == "enable" || dataRaw[key].optionsDir['parking'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/parking.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Parking'} alt = {__ 'Parking'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("campKitchen") &&
                (dataRaw[key].optionsDir['campKitchen']['enable'] == "enable" || dataRaw[key].optionsDir['campKitchen'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/campKitchen.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Camp Kitchen'} alt = {__ 'Camp Kitchen'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("laundry") &&
                (dataRaw[key].optionsDir['laundry']['enable'] == "enable" || dataRaw[key].optionsDir['laundry'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/laundry.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Laundry'} alt = {__ 'Laundry'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("ensuiteSites") &&
                (dataRaw[key].optionsDir['ensuiteSites']['enable'] == "enable" || dataRaw[key].optionsDir['ensuiteSites'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/ensuiteSites.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Ensuite Sites'} alt = {__ 'Ensuite Sites'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("restaurant") &&
                (dataRaw[key].optionsDir['restaurant']['enable'] == "enable" || dataRaw[key].optionsDir['restaurant'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/restaurant.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Restaurant'} alt = {__ 'Restaurant'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("kiosk") &&
                (dataRaw[key].optionsDir['kiosk']['enable'] == "enable" || dataRaw[key].optionsDir['kiosk'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/kiosk.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Kiosk'} alt = {__ 'Kiosk'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("internetAccess") &&
                (dataRaw[key].optionsDir['internetAccess']['enable'] == "enable" || dataRaw[key].optionsDir['internetAccess'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/internetAccess.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Internet Access'} alt = {__ 'Internet Access'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("swimmingPool") &&
                (dataRaw[key].optionsDir['swimmingPool']['enable'] == "enable" || dataRaw[key].optionsDir['swimmingPool'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/swimmingPool.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Swimming Available'} alt = {__ 'Swimming Available'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("gamesRoom") &&
                (dataRaw[key].optionsDir['gamesRoom']['enable'] == "enable" || dataRaw[key].optionsDir['gamesRoom'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/gamesRoom.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Games Room'} alt = {__ 'Games Room'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("generatorsPermitted") &&
                (dataRaw[key].optionsDir['generatorsPermitted']['enable'] == "enable" || dataRaw[key].optionsDir['generatorsPermitted'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/generatorsPermitted.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Permitted'} alt = {__ 'Generators Permitted'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("generatorsNotPermitted") &&
                (dataRaw[key].optionsDir['generatorsNotPermitted']['enable'] == "enable" || dataRaw[key].optionsDir['generatorsNotPermitted'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/generatorsNotPermitted.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Generators Not Permitted'} alt = {__ 'Generators Not Permitted'} />';
            }
            
            if( dataRaw[key].optionsDir.hasOwnProperty("offer") &&
                (dataRaw[key].optionsDir['offer']['enable'] == "enable" || dataRaw[key].optionsDir['offer'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/offer.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Offer'} alt = {__ 'Offer'} />';
            }
    
            if( dataRaw[key].optionsDir.hasOwnProperty("discount") &&
                (dataRaw[key].optionsDir['discount']['enable'] == "enable" || dataRaw[key].optionsDir['discount'] == "Y") ){
                {var $imageSrc = "$url/uploads/sites/3/2014/04/facility_icons/30x30/jpg/discount.png";}
                imagesContent = imagesContent + '<img width="30" height="30" src={$imageSrc} title = {__ 'Discount'} alt = {__ 'Discount'} />';
            }
            
            if(imagesContent.trim() != ""){
                //alert("Link: "+dataRaw[key].link+"  ||"+imagesContent+ "||");
            }
            
            var previewString = "";
            if(dataRaw[key].hasOwnProperty("wlm_address")){
                previewString = previewString + '<div class="wlm_address" style="margin-bottom:2px;padding-bottom:0px;">'+dataRaw[key].wlm_address+'</div>';
            }
            if( dataRaw[key].optionsDir.hasOwnProperty("directoryType") ){
                
                if( dataRaw[key].optionsDir['directoryType'] == 'directory_6' &&
                    dataRaw[key].optionsDir.hasOwnProperty("help_required") &&
                    dataRaw[key].optionsDir['help_required'] != ''){                    
                    previewString = previewString + '<div class="help_required" style="margin-bottom:2px;padding-bottom:0px;">Help Required:'+dataRaw[key].optionsDir['help_required']+'</div>';

                }
                
                if( (dataRaw[key].optionsDir['directoryType'] == 'directory_1' || dataRaw[key].optionsDir['directoryType'] == 'directory_6' || dataRaw[key].optionsDir['directoryType'] == 'administrator') &&
                    dataRaw[key].optionsDir.hasOwnProperty("location") &&
                    dataRaw[key].optionsDir['location'] != ''){                    
                    previewString = previewString + '<div class="location" style="margin-bottom:2px;padding-bottom:0px;">Location:'+dataRaw[key].optionsDir['location']+'</div>';
                }
                
                if( (dataRaw[key].optionsDir['directoryType'] == 'directory_1' || dataRaw[key].optionsDir['directoryType'] == 'administrator') &&
                    dataRaw[key].optionsDir.hasOwnProperty("access") &&
                    dataRaw[key].optionsDir['access'] != ''){                    
                    previewString = previewString + '<div class="access" style="margin-bottom:2px;padding-bottom:0px;">Access:'+dataRaw[key].optionsDir['access']+'</div>';
                }
                
                if( (dataRaw[key].optionsDir['directoryType'] == 'directory_4' || dataRaw[key].optionsDir['directoryType'] == 'directory_5' || dataRaw[key].optionsDir['directoryType'] == 'directory_6' || dataRaw[key].optionsDir['directoryType'] == 'administrator') &&
                    dataRaw[key].optionsDir.hasOwnProperty("property_type") &&
                    dataRaw[key].optionsDir['property_type'] != ''){                    
                    previewString = previewString + '<div class="property_type" style="margin-bottom:2px;padding-bottom:0px;">Property Type:'+dataRaw[key].optionsDir['property_type']+'</div>';
                }
                
                if( (dataRaw[key].optionsDir['directoryType'] == 'directory_4' || dataRaw[key].optionsDir['directoryType'] == 'directory_6' || dataRaw[key].optionsDir['directoryType'] == 'administrator')){
                    if( dataRaw[key].optionsDir.hasOwnProperty("start_date") &&
                        dataRaw[key].optionsDir['start_date'] != ''){                    
                        previewString = previewString + '<div class="start_date" style="margin-bottom:2px;padding-bottom:0px;">Start Date:'+dataRaw[key].optionsDir['start_date']+'</div>';
                    }
                    if( dataRaw[key].optionsDir.hasOwnProperty("end_date") &&
                        dataRaw[key].optionsDir['end_date'] != ''){                    
                        previewString = previewString + '<div class="end_date" style="margin-bottom:2px;padding-bottom:0px;">End Date:'+dataRaw[key].optionsDir['end_date']+'</div>';
                    }
                    if( dataRaw[key].optionsDir.hasOwnProperty("duration") &&
                        dataRaw[key].optionsDir['duration'] != ''){                    
                        previewString = previewString + '<div class="duration" style="margin-bottom:2px;padding-bottom:0px;">Duration:'+dataRaw[key].optionsDir['duration']+'</div>';
                    }
                }
            }

            //----------------------------rasu-------------------------------
		if (!(dataRaw[key].optionsDir['gpsLatitude'] == "0" && dataRaw[key].optionsDir['gpsLongitude'] == "0")) {

			var rating = '';
			if(dataRaw[key].rating){
				rating += '<div class="rating">';
				for (var j = 1; j <= dataRaw[key].rating['max']; j++) {
					rating += '<div class="star';
					if(j <= dataRaw[key].rating['val']) {
						rating += ' active';
					}
					rating += '"></div>';
				}
				rating += '</div>';
			}
                        rating = "";//hiding from preview window - raise
                        viewMoreLink = "";
                        if(imagesContent != ""){
                            viewMoreLink = dataRaw[key].link;
                            if(viewMoreLink.indexOf("?") == -1){
                                noMapLink = viewMoreLink;// + "?nomap=1&rasa=1"
                            }else{
                                noMapLink = viewMoreLink;// + "&nomap=1&rasa=1"
                            }
                            
                            var thumbCode = '<div style="width:50%;float:right;padding:0;position:relative;left:0;top:5px;">'+imagesContent+'</div>';
                            previewHTML = '<div class="marker-holder"><div class="marker-content" style="position: relative;">'+thumbCode+'<div class="map-item-info" style="width:50%;float:none;padding:8px;position:relative;left:10px;"><div class="title">'+dataRaw[key].post_title+'</div>'+rating+'<div class="address">'+dataRaw[key].optionsDir["address"]+'</div>'+previewString+'<a target="_blank" href="'+ noMapLink +'" class="more-button">{__ 'VIEW MORE'}</a></div><div class="clear"></div><div class="arrow"></div></div></div></div>';

                        }else{

                            viewMoreLink = dataRaw[key].link;
                            if(viewMoreLink.indexOf("?") == -1){
                                noMapLink = viewMoreLink;// + "?nomap=1&rasu=1"
                            }else{
                                noMapLink = viewMoreLink;// + "&nomap=1&rasu=1"
                            }
                            var thumbCode = (dataRaw[key].thumbnailDir) ? ' with-image"><img width="120" height="160" src="'+dataRaw[key].thumbnailDir+'" alt="">' : '">';
                            //alert(dataRaw[key].timthumbUrl);
                            //alert(dataRaw[key].thumbnailDir);//reference in functions/directory.php [getItemsAjax]
                            var tempPreviewStr = "";
                            if(dataRaw[key].optionsDir['directoryType'] == 'directory_6'){
                              tempPreviewStr = previewString;
                            }
//alert(tempPrevStr);

previewHTML = '<div class="marker-holder"><div class="marker-content'+thumbCode+'<div class="map-item-info"><div class="title">'+dataRaw[key].post_title+'</div>'+rating+'<div class="address">'+dataRaw[key].optionsDir["address"]+'</div>'+ tempPreviewStr + '<a target="_blank" href="'+noMapLink+'" class="more-button">{__ 'VIEW MORE'}</a></div><div class="clear"></div><div class="arrow"></div></div></div></div>';
                        }
			data[i] = {
				latLng: [dataRaw[key].optionsDir['gpsLatitude'],dataRaw[key].optionsDir['gpsLongitude']],
				options: {
					icon: dataRaw[key].marker,
					shadow: "{!$themeOptions->directoryMap->mapMarkerImageShadow}",
				},
				data: previewHTML
			};
			i++;
		}
    }

    // show geoloc marker
    if(geoloc){
    	mapDiv.gmap3({
			marker: {
    			latLng: geoloc,
    			options: {
    				animation: google.maps.Animation.DROP,
    				zIndex: 1000,
    				icon: "{$themeImgUrl}/geolocation-pin.png"
    			}
    		}
    	});
	}

	// generate markers in map
	var mapObj = {
		marker: {
			values: data,
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
					map.panTo(marker.getPosition());
					//------------------------
					/*
					var oldDraw = infobox.draw;
					infobox.draw = function() {
						oldDraw.apply(this);
						jQuery(infobox.div_).hide();
						//jQuery(infobox.div_).slideUp( 800 ).delay( 500 ).fadeIn('slow');
						jQuery(infobox.div_).delay( 500 ).fadeIn('slow'); 
					}
					*/
					//------------------------
					infobox.setContent(context.data);
					//infobox.open(map,marker);//.delay( 800 ).fadeIn( 400 );
					infobox.open(map,marker).delay( 3800 ).fadeIn( 400 );

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
	};
	/*google.maps.event.addListener(infobox, "mouseover", function (e) {
		alert("Mouse Over");
		log("Mouse Over");
	});*/

	if(geoloc){
		if(ajaxGeo){
			var inputRadius = geoInputRadius.val();
			if(!isNaN(inputRadius)){
				var radiusInM = parseInt(geoInputRadius.val()) * 1000;
			} else {
				var radiusInM = parseInt(geoInputRadius.data('default-value')) * 1000;
			}
			// autofit by circle
			mapObj.circle = {
				options: {
					center: geoloc,
					radius : radiusInM,
					visible : {ifset $themeOptions->search->showAdvancedSearchRadius}true{else}false{/ifset},
					fillOpacity : 0.15,
					fillColor : "#2c82be",
					strokeColor : "#2c82be"
				}
			}
		} else {
			var radiusInM = parseInt({ifset $geolocationRadius}{$geolocationRadius}{else}100{/ifset}) * 1000;
			// autofit by circle
			mapObj.circle = {
				options: {
					center: geoloc,
					radius : radiusInM,
					visible : {ifset $geolocationCircle}true{else}false{/ifset},
					fillOpacity : 0.15,
					fillColor : "#2c82be",
					strokeColor : "#2c82be"
				}
			}
		}
	}

	hideLoading();

	mapDiv.gmap3( mapObj, "autofit" );

	if(len == 1 && !geoloc){
		map.setZoom({!$themeOptions->directory->setZoomIfOne});
	}

}

function generateOnlyGeo(lat,lng,radius) {
	var geoloc = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
	// generate geo pin
	mapDiv.gmap3({
		marker: {
			latLng: geoloc,
			options: {
				animation: google.maps.Animation.DROP,
				zIndex: 1000,
				icon: "{$themeImgUrl}/geolocation-pin.png"
			}
		}
	});
	// generate and autofit by circle
	var mapObj = {};
	var radiusInM = parseInt(radius) * 1000;
	// autofit by circle
	mapObj.circle = {
		options: {
			center: geoloc,
			radius : radiusInM,
			visible : {ifset $themeOptions->search->showAdvancedSearchRadius}true{else}false{/ifset},
			fillOpacity : 0.15,
			fillColor : "#2c82be",
			strokeColor : "#2c82be"
		}
	}
	mapDiv.gmap3( mapObj, "autofit" );
}

var contentDiv = $('#main #content');
var currContent = contentDiv.html();
var ajaxContent;

function generateRASUContent(data, category, location, search, loc_radius, feature) {


	var length = $.map(data, function(n, i) { return i; }).length;

	contentDiv.find('.ajax-content').remove();
	var title;
	var pageHTML = "";
	var PaginationLinks = "";
	var pageLinks = "";
	var limit = {ifset $themeOptions->search->interactiveContentMaxItems}{!$themeOptions->search->interactiveContentMaxItems}{else}10{/ifset};
	var limit = 10;
    var num_records = 0;
    for(var key in data){
      num_records = num_records+1;
    }
	if(length == 0){
		title = $('<header class="entry-header"><h1 class="entry-title"><span>{__ 'No result found'}</span></h1></header>');
		pageHTML = "No result found";
	} else {
		{var $firstPageLink = "$siteURL/?dir-search=yes&pagination=20&orderby=post_date";}
        {var $secondPageLink = "$siteURL/page/2/?dir-search=yes&pagination=20&orderby=post_date";}
        {var $thirdPageLink = "$siteURL/page/3/?dir-search=yes&pagination=20&orderby=post_date";}
		title = $('<header class="entry-header"><h1 class="entry-title"><span>{__ 'Search result'}</span></h1></header>');
		
		if(length > 10){
			var featureStr = "feature=";
			if(feature){
				var featureArr = feature.toString().split(",");
				var featureStr = 'feature='+featureArr.join(',');
			}
			var catStr = "categories=";
			if(category){
				var catArr = category.toString().split(",");
				var catStr = 'categories='+catArr.join(',');
				//var catStr = my_implode_js('&category[]=', catArr);//locArr.join('&location%5B%5D=');
			}
			var locArr = location.toString().split(",");
			//var locStr = my_implode_js('&location[]=', locArr);
			var locStr = 'locations='+locArr.join(',');
			if(num_records > 30){
				var pageLinks = '<div class="page_nav"><span class="page-numbers current">1</span><span class="dots">...</span><a class="page-numbers" href="{$secondPageLink}&' + catStr + '&' + locStr + '&loc_radius=' + loc_radius + '&' + featureStr + '&s=&search=' + search + '">2</a><span class="dots">...</span><a class="page-numbers" href="{$thirdPageLink}&' + catStr + '&' + locStr + '&s=&search=' + search + '">3</a><a class="page-numbers next" href="{$firstPageLink}&'+ catStr +'&'+ locStr +'&s=&search='+ search +'">Next</a></div>';
			}else if(num_records > 20){
				var pageLinks = '<div class="page_nav"><span class="page-numbers current">1</span><span class="dots">...</span><a class="page-numbers" href="{$secondPageLink}&' + catStr + '&' + locStr + '&loc_radius=' + loc_radius + '&' + featureStr + '&s=&search=' + search + '">2</a><a class="page-numbers next" href="{$firstPageLink}&'+ catStr +'&'+ locStr +'&s=&search='+ search +'">Next</a></div>';
			}else{
				var pageLinks = '<div class="page_nav"><span class="page-numbers current">1</span><span class="dots">...</span><a class="page-numbers next" href="{$firstPageLink}&'+ catStr +'&'+ locStr +'&loc_radius=' + loc_radius + '&' + featureStr + '&s=&search='+ search +'">Next</a></div>';
			}
			var PaginationLinks = '<nav class="paginate-links">'+ pageLinks +'</nav>';
		}
		pageHTML = "Showing "+ limit +" from "+ length +" Items" + PaginationLinks;
		
	}

	var html;
	if(length > 0){
		html = $('<ul class="items"></ul>');
	}
	
	if(limit > length) {
		limit = length;
	}
	var i = 0;
	for(var key in data){
		var thumbnailHtml;
		if(data[key].timthumbUrlContent){
			var thumbnailHtml = '<div class="thumbnail"><img src="'+data[key].thumbnailDir+'" alt="Item thumbnail" width="150" height="150" style="width:150px;height:150px;"><div class="comment-count">'+data[key].comment_count+'</div></div>';
		} else {
			thumbnailHtml = '';
		}
		var rating = '';
		if(data[key].rating){
			rating += '<span class="rating">';
			for (var i = 1; i <= data[key].rating['max']; i++) {
				rating += '<span class="star';
				if(i <= data[key].rating['val']) {
					rating += ' active';
				}
				rating += '"></span>';
			}
			rating += '</span>';
		}
		var descriptionHtml = '<div class="description"><h3><a target="_blank" href="'+data[key].link+'">'+data[key].post_title+'</a>'+rating+'</h3>'+data[key].excerptDir+'</div>';
		html.append('<li class="item clear">'+thumbnailHtml+descriptionHtml+'</li>');
		if(i <= limit){
			i++;
		} else {
			break;
		}
	};
	//ajaxContent = $('<div class="ajax-content"></div>').html(title).append(html);
	//contentDiv.find('>').hide();
	//contentDiv.append(ajaxContent);
    jQuery('.items').html(html);
    jQuery('.paginate-links').html(pageLinks);
    jQuery('.dir-sorting.clearfix').html(pageHTML);
}
function my_implode_js(separator,cusArr){
       var temp = '';
       for(var i=0; i < cusArr.length; i++){
           
           //if(i != cusArr.length-1){
                temp += separator  ; 
           //}
           temp +=  cusArr[i] 
       }//end of the for loop

       return temp;
}//end of the function
function generateContent(data) {

	var length = $.map(data, function(n, i) { return i; }).length;

	contentDiv.find('.ajax-content').remove();
	var title;
	if(length == 0){
		title = $('<header class="entry-header"><h1 class="entry-title"><span>{__ 'No result found'}</span></h1></header>');
	} else {
		title = $('<header class="entry-header"><h1 class="entry-title"><span>{__ 'Search result'}</span></h1></header>');
	}

	var html;
	if(length > 0){
		html = $('<ul class="items"></ul>');
	}
	var limit = {ifset $themeOptions->search->interactiveContentMaxItems}{!$themeOptions->search->interactiveContentMaxItems}{else}30{/ifset};
	if(limit > length) {
		limit = length;
	}
	var i = 0;
	for(var key in data){
		var thumbnailHtml;
		if(data[key].timthumbUrlContent){
			var thumbnailHtml = '<div class="thumbnail"><img src="'+data[key].timthumbUrlContent+'" alt="Item thumbnail"><div class="comment-count">'+data[key].comment_count+'</div></div>';
		} else {
			thumbnailHtml = '';
		}
		var rating = '';
		if(data[key].rating){
			rating += '<span class="rating">';
			for (var i = 1; i <= data[key].rating['max']; i++) {
				rating += '<span class="star';
				if(i <= data[key].rating['val']) {
					rating += ' active';
				}
				rating += '"></span>';
			}
			rating += '</span>';
		}
                rating = "";//hiding rating from preview window - raise
		var descriptionHtml = '<div class="description"><h3><a target="_blank" href="'+data[key].link+'">'+data[key].post_title+'</a>'+rating+'</h3>'+data[key].excerptDir+'</div>';
		html.append('<li class="item clear">'+thumbnailHtml+descriptionHtml+'</li>');
		if(i <= limit){
			i++;
		} else {
			break;
		}
	};
	ajaxContent = $('<div class="ajax-content"></div>').html(title).append(html);
	contentDiv.find('>').hide();
	contentDiv.append(ajaxContent);
}

// reset search ajax values
$('#directory-search .reset-ajax').click(function () {

	// get default values
	ajaxGetMarkers(false,false,false,false,true);

	{if isset($themeOptions->search->interactiveReplaceContent) && ($themeOptions->search->interactiveReplaceContent == 'enabled') }
	contentDiv.find('.ajax-content').remove();
	contentDiv.find('>').show();
	{/if}

	$('#dir-searchinput-text').val("");
	// for IE
	$('span.for-dir-searchinput-text label').show();

	$('#dir-searchinput-location').val("");
	$('#dir-searchinput-location-id').val("0");
	// for IE
	$('span.for-dir-searchinput-category label').show();

	$('#dir-searchinput-category').val("");
	$('#dir-searchinput-category-id').val("0");
	// for IE
	$('span.for-dir-searchinput-location label').show();

	$('#dir-searchinput-geo').attr('checked',false);
	$('div.iphone-style[rel=dir-searchinput-geo]').removeClass('on').addClass('off');

	//geoInputRadius.val(geoInputRadius.data('default-value'));

	// hide close icon
	$(this).hide();
});