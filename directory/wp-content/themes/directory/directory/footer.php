<style>
  .ui-draggable {width: 230px !important;height: 70px !important;}
  .ui-dialog .ui-dialog-content {height: auto !important;}
  .ui-dialog-titlebar-close {visibility: hidden;}
</style>
<?php
 $themeUrl = get_template_directory_uri();
 $user_ID = get_current_user_id();
 echo "<!-- userid(rasu) = $user_ID-->";
 $prefJS = "";
 $prefer_dialog = $loading_dialog = $search_dialog = $error_dialog = "";
 if(!empty($user_ID)){
  global $wpdb;
  $searchQuery = "SELECT * FROM `saved_preferences` WHERE `user_id` = $user_ID ORDER BY `id` DESC LIMIT 1";
  $searchRS = $wpdb->get_results( $searchQuery, OBJECT );
  //print_r($searchRS);
  $searchQuery = $searchRS[0];
  //print_r($searchQuery);
  
  //---------------field 1 ----------------
  $searchLocations = $searchQuery->locations;
  $locationJS = $featureJS = $catJS = $searchRadius = $customLocation = "";
  if(!empty($searchLocations)){
   $searchLocations = implode("','", explode(",",$searchLocations));
   $searchLocations = "'".$searchLocations."'";
   $locationJS =<<<LOC_JS
   jQuery('#all_locations').val([$searchLocations]);
   jQuery("#all_locations").multiselect("refresh");
LOC_JS;
  }
  
  //---------------field 2 ----------------
  $customLocation = $searchQuery->custom_location;
  $customLocationJS = "";
  if(!empty($customLocation)){
   $customLocationJS = "jQuery('#dir-custom-searchinput-text').val('$customLocation');";
  }else{
   $customLocationJS = "jQuery('#dir-custom-searchinput-text').val();";
  }
  
  //---------------field 3 ----------------
  $searchRadius = (int)$searchQuery->radius;
  $locationRadiusJS = "";
  if(!empty($searchRadius)){
   $locationRadiusJS = "locationRadius = jQuery('#all_radiuses').val($searchRadius);";
  }else{
   $locationRadiusJS =  "locationRadius = jQuery('#all_radiuses').val();";
  }
  
  //---------------field 4 ----------------
  if(!empty($searchQuery->categories)){
   $categories = $searchQuery->categories;
   $categories = implode("','", explode(",",$categories));
   $catetories = "'".$categories."'";
   $catJS =<<<LOC_JS
   jQuery('#all_categories').val([$catetories]);
   jQuery("#all_categories").multiselect("refresh");
LOC_JS;
  }
  
  //---------------field 5 ----------------
  if(!empty($searchQuery->features)){
   $searchFeatures = $searchQuery->features;
   $searchFeatures = implode("','", explode(",",$searchFeatures));
   $searchFeatures = "'".$searchFeatures."'";
   $featureJS =<<<LOC_JS
   jQuery('#all_features').val([$searchFeatures]);
   jQuery("#all_features").multiselect("refresh");
LOC_JS;
  }
  
  $prefJS = <<<PREF_JS
  \nfunction load_preference(){
  jQuery.blockUI({ message: "Please wait while we are loading search criteria.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });
  /*
   {$locationJS}
   {$customLocationJS}
   {$locationRadiusJS}
   {$catJS}
   {$featureJS}
   */
	jQuery.post(
	  // MyAjax defined in functions.php
	  RasuLoadSaved.prefurl,
	  {
		id:$user_ID,
		load_preference : 'load_preference'
	  },
	  function( response ) {
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
			//catArr = response.radius.split(',');
			//jQuery('#all_radiuses').val(response.radius);
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
	  }
	)
	/*jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });*/
	//jQuery( "#btnUpdate" ).trigger( "click" );
  }
  jQuery("#btnPreference").on("click", function(){
   save_preference(true);
  });
  //jQuery('#all_features').removeAttr('disabled');
  //jQuery('#all_features').prop( "disabled", false );
PREF_JS;

  $prefer_dialog = <<<PREF_DIALOG
   <div id="preference_dialog" title="&nbsp; Save Filters" style="display: none;">
  <p style="margin-left: 10px;">Your search preferences have been saved.<br/> To access them on your next visit press the 'Load Saved Filters' button.</p>
</div>
PREF_DIALOG;

 }else{
  //if user is not logged in
  $prefJS = <<<PREF_JS
  function load_preference(){
	jQuery( "#loading_dialog" ).show();
	jQuery( "#loading_dialog" ).dialog({
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
  jQuery("#btnPreference").on("click", function(){
   jQuery( "#preference_dialog" ).show();
   jQuery( "#preference_dialog" ).dialog({
	resizable: false,
	   height:140,
	   modal: true,
	   buttons: {
		 "Close": function() {
		   jQuery( this ).dialog( "close" );
		 }
	  }
   });
  });
PREF_JS;

  $prefer_dialog = <<<PREF_DIALOG
   <div id="preference_dialog" title="&nbsp; Save Preference" style="display: none;">
  <p style="margin-left: 10px;">Please login to save filters!</p>
</div>
PREF_DIALOG;

$loading_dialog = <<<LOAD_DIALOG
   <div id="loading_dialog" title="&nbsp; Load Filters" style="display: none;">
  <p style="margin-left: 10px;">Please login to load filters!</p>
</div>
LOAD_DIALOG;
$search_dialog = <<<SEARCH_DIALOG
   <div id="search_dialog" title="&nbsp; Directory Search" style="display: none;">
  <p style="margin-left: 10px;">Please login to load search items!</p>
</div>
SEARCH_DIALOG;
}
 $error_dialog = <<<SEARCH_DIALOG
   <div id="search_dialog" title="&nbsp; Directory Search" style="display: none;">
  <p style="margin-left: 10px;" id="error_message">Please login to load search items!</p>
</div>
SEARCH_DIALOG;
 echo $prefer_dialog;
 echo $loading_dialog;
 echo $search_dialog;
 echo $error_dialog;
 echo $notiDil = <<<NOTIFICATION_DIALOG
 <div id="notification_dialog" title="&nbsp; No Record Found" style="display: none;">
  <p style="margin-left: 10px;">Sorry, we do not have anything that meets your search criteria, please refine your search and try again.</p>
 </div>
NOTIFICATION_DIALOG;
?>
<?php

 /**
 * AIT WordPress Theme
 *
 * Copyright (c) 2012, Affinity Information Technology, s.r.o. (http://ait-themes.club)
 */

 global $latteParams;
 $ipAddress = $_SERVER['REMOTE_ADDR'];
 function rasu_curPageURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
 }
$currPageURL = rasu_curPageURL();
//if($ipAddress == '171.78.72.245'){//$ipAddress == '171.78.80.14'
//    echo $_SERVER['REQUEST_URl'];
//}
if(
   array_key_exists('reg',$_REQUEST) ||
   strpos($currPageURL,'business-listing-registration') ||
   strpos($currPageURL,'business-listings') ||
   strpos($currPageURL,'25-listing') ||
   strpos($currPageURL,'25plus')
   ){
        echo "<script>jQuery('.field_text').hide();jQuery('.field_textarea').hide();</script>";
}
WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();
$themeUrl = get_template_directory_uri();
$preferenceJS = <<<PREF_JS
function save_preference(ajax){
  //alert('testing save_preference');
  //showLoading();
  var locRadius = '';
  
  locationRadius = jQuery('#all_radiuses').val()
  if(locationRadius){locRadius = locationRadius;}
  
  var location = 0;
  locationIDs = jQuery('#all_locations').val()
  if(locationIDs){location = locationIDs;}

  var category = 0;
  categoryIDs = jQuery('#all_categories').val()
  if(categoryIDs){category = categoryIDs;}
  
  var feature = '';
  featureIDs = jQuery('#all_features').val();
  if(featureIDs){feature = featureIDs;}
  search = jQuery('#dir-custom-searchinput-text').val();
  jQuery.blockUI({ message: "Please wait while we save your search preferences.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });
  // get items from ajax
  jQuery.post(
	  // MyAjax defined in functions.php
	  RasuAjax.prefurl,
	  {
		  action : 'get_items',
		  category : category,
		  loc_radius: locRadius,
		  location : location,
		  feature:feature,
		  search : search,
		  id:$user_ID,
		  save_preference : 'save_preference'
	  },
	  function( response ) {
	   jQuery.unblockUI();
	   //window.location.reload(true);
	   //window.location.href=window.location.href;
		//location.reload(true);
		//alert(response);
		//-------------
		jQuery( "#preference_dialog" ).show();
		jQuery( "#preference_dialog" ).dialog({
		 resizable: false,
			height:140,
			modal: true,
			buttons: {
			  "Close": function() {
				jQuery( this ).dialog( "close" );
				
			  }
		   }
		});
		//-------------
	  }).fail(function(e) { console.log("AJAX ERROR", e); });
}
PREF_JS;
?>
<?php
$currPageURL = rasu_curPageURL();
$alertStatement = "";
if($ipAddress == '124.253.75.98'){
 $alertStatement = "alert(htmlString);";
}

$js = <<<JS
<script>

jQuery( "map[name='Map']" ).on( "click", function() {
	   /*
        var htmlString = jQuery(this).html().trim().toLowerCase();
        var skip = "cleveryoutubepluginhelper";
        console.log("Index pos:"+htmlString.indexOf(skip));
        console.log("htmlString.indexOf"+htmlString);
        if( htmlString.indexOf(skip) >= 0){
        	console.log("first test");
        }else{
		 jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });
		}
		*/
		console.log( "Map click!" );
	   jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='{$themeUrl}/design/img/loading2.gif'/>" });

	
});
console.log( "You clicked a paragraph!" );
jQuery('.ubermenu-target-title.ubermenu-target-text').on("click",function(){
     console.log( "ubermenu-target-title!" );
        
	var htmlString = jQuery(this).html().trim().toLowerCase();
        
        //alert("Test 2:" + htmlString);
		console.log( "Test 2:" + htmlString );
		//htmlString == "submit a $25+ camp/site" ||
	if(
		htmlString == "camping directory" || 
		htmlString == "login" || 
		htmlString == "business directory" || 
		htmlString == "blog" ||
		htmlString == "submit" ||
		htmlString == "contact" ||
		htmlString == "about us" ||
		htmlString == "register" ||
		htmlString == "admin area" ||
		htmlString == "submit a $25+ camp/site" || //working
		htmlString == "submit a low cost camp/site" || //working
		htmlString == "submit a business" || //working
		//htmlString == "submit a help out" || //working
		htmlString == "submit a free campground" || //working
		htmlString == "my profile" ||
		htmlString == "logout" ||
		htmlString == "list with us" ||
		htmlString == "shop" ||
		htmlString == "edit listing" ||
		htmlString == "add a listing" ||
		htmlString == "add/edit a listing" ||
		htmlString == "search" ||
		htmlString == "my bookmarks" ||
        htmlString == "about" //||
		//htmlString == "submit a house sit" //working
	   ){
	  console.log( "if!"+htmlString );;

	}else{
	  jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='$themeUrl/design/img/loading2.gif'/>" });
	}
});

jQuery(".wlss_add_to_levels_button").click(function(){
  console.log( "wlss_add_to_levels_button" );
  jQuery.blockUI({ message: "Please wait ....Loading the page.<br/><img src='$themeUrl/design/img/loading2.gif'/>"}); 
});

jQuery("input[class='.wlss_add_to_levels_button']").on( "click", function() {
        console.log( "input-wlss_add_to_levels_button" );
	jQuery.blockUI({ message: "Please wait ....Loading the page.<br/><img src='$themeUrl/design/img/loading2.gif'/>"});  });

jQuery("span[class='.ubermenu-target-title.ubermenu-target-text']").on( "click", function() {
        var htmlString = jQuery(this).html().trim().toLowerCase();
	jQuery.blockUI({ message: "Please wait while our system searches the required listings for your query.  There are many thousands of listings to sort through and your patience is appreciated.<br/><img src='$themeUrl/design/img/loading2.gif'/>"});
jQuery('#branding').hide();
});
</script>
JS;

echo $js;
echo "<script>{$prefJS}\n{$preferenceJS}</script>";
/* if(wp_is_mobile()){echo"<style>#sfm-sidebar {width:325px;}</style>";}
else{echo"<style>#sfm-sidebar {width:325px;}</style>";}*/
?>
<?php
 $requestedLocs = $requestedCats = $requestedRad = $requesteFeat = $requesteSearch = "";
 
 if(array_key_exists('locations',$_REQUEST) && !empty($_REQUEST['locations']))$requestedLocs = $_REQUEST['locations'];
 if(array_key_exists('location',$_REQUEST) && !empty($_REQUEST['location']))$requestedLocs = $_REQUEST['location'];
 if(array_key_exists('categories',$_REQUEST) && !empty($_REQUEST['categories'])){
  $requestedCats = $_REQUEST['categories'];
  if($requestedCats == '9,30,14,155,12,20,6,2096,5,7,8,10,106,15,17,4,2797,107,2088'){
   $requestedCats = "";
  }
 }
 if(array_key_exists('loc_radius',$_REQUEST) && !empty($_REQUEST['loc_radius']))$requestedRad = $_REQUEST['loc_radius'];
 if(array_key_exists('features',$_REQUEST) && !empty($_REQUEST['features']))$requesteFeat = $_REQUEST['features'];
 if(array_key_exists('search',$_REQUEST) && !empty($_REQUEST['search']))$requesteSearch = $_REQUEST['search'];//dir-custom-searchinput-text
 if(
    (array_key_exists('categories',$_REQUEST) && $_REQUEST['categories'] == 2761) &&
    (array_key_exists('locations',$_REQUEST) && $_REQUEST['locations'] == 1691) &&
    (array_key_exists('dir-search',$_REQUEST) && $_REQUEST['dir-search'] == 'yes')
    ){
  $requestedLocs = $requestedCats = $requestedRad = $requesteFeat = $requesteSearch = "";
 }
echo $preSelectJS = <<< PRE_JS
 <script>
  function preselect() {
   var requestedLocs = "$requestedLocs";
   var requestedCats = "$requestedCats";
   var requestedRad = "$requestedRad";
   var requesteFeat = "$requesteFeat";
   var requesteSearch = "$requesteSearch";
   
   if(!jQuery.isEmptyObject(requestedLocs)){
    catArr = requestedLocs.toString().split(",");
    //jQuery('#all_locations').val(catArr);
    jQuery('#all_locations').multiselect('select', catArr);
    jQuery("#all_locations").multiselect("refresh");
   }
   
   if(!jQuery.isEmptyObject(requesteSearch)){
    jQuery("#dir-custom-searchinput-text").val(requesteSearch);
   }
   
   if(!jQuery.isEmptyObject(requestedCats)){
    catArr = requestedCats.toString().split(",");
    jQuery('#all_categories').multiselect('select', catArr);
    jQuery("#all_categories").multiselect("refresh");
   }
   if(!jQuery.isEmptyObject(requestedRad)){
    jQuery('#all_radiuses').multiselect('select', requestedRad);
    jQuery("#all_radiuses").multiselect("refresh");
   }
     
   if(!jQuery.isEmptyObject(requesteFeat)){
     catArr = requesteFeat.toString().split(",");
     jQuery('#all_features').multiselect('select', catArr);
     jQuery("#all_features").multiselect("refresh");
    }
  }
   jQuery(document).ready(function() {
    preselect();
    });
 </script>
PRE_JS;
?>
<?php
	if(array_key_exists('iframe', $_REQUEST) && !empty($_REQUEST['iframe']) ){
		echo $viewMoreJS = <<<VIEW_MORE
			jQuery( document ).ready(function() {
				jQuery( "a.more-button" ).click(function() {
					jQuery.blockUI({ message: "Please wait whilst we load the listing page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				});
			});
VIEW_MORE;
	}
?>