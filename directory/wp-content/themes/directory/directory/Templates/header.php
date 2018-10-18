<?php
if (is_user_logged_in() && is_front_page() ) {
    $referrer = $_SERVER['HTTP_REFERER'] ;
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $urlPos = strpos($referrer, 'register-to-submit-a-listing');
    if($urlPos){
        remove_all_actions( 'wp_redirect');
        //wp_mail('kalyanrajiv@gmail.com', "redirecting user from header.php112", $referrer."--", $headers);
        $redirectURL = WP_SITEURL.'/submit-a-listing/?frc=9';
        /*echo $js =<<<JS
        <script type='text/javascript'>window.location = '$redirectURL';</script>
JS;
*/
        //wp_redirect($redirectURL);//17Aug 2017
        die;
    }
    wp_redirect('https://www.freerangecamping.com.au/directory/?s=&categories=2761&locations=1691&dir-search=yes');
    exit;
}
if( !function_exists('isRemoteAddress') ){
  function isRemoteAddress($ipAddress){
    foreach($ipAddress as $sngIPAddr){
      if(
        $_SERVER['HTTP_X_FORWARDED_FOR'] == $sngIPAddr ||
        $_SERVER['HTTP_CF_CONNECTING_IP'] == $sngIPAddr ||
        $_SERVER['REMOTE_ADDR'] == $sngIPAddr
      ){
        return true;
      }
    }
    return false;
  }
}
$isMyIP = isRemoteAddress(array('124.253.55.126', '149.135.34.230'));
?>
<!doctype html>

<!--[if IE 8]><html class="no-js oldie ie8 ie" lang="{$site->language}"><![endif]-->
<!--[if gte IE 9]><!--><html class="no-js" lang="{$site->language}"><!--<![endif]--><head>
		<meta charset="{$site->charset}">
		{mobileDetectionScript}
		

	<meta name="Author" content="Free Range Camping, https://www.freerangecamping.com.au">

		<title>{title}</title>
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="{$site->pingbackUrl}">

		{if $themeOptions->fonts->fancyFont->type == 'google'}
		<link href="https://fonts.googleapis.com/css?family={$themeOptions->fonts->fancyFont->font}" rel="stylesheet" type="text/css">
		{/if}

		<link id="ait-style" rel="stylesheet" type="text/css" media="all" href="{less}">

		{head}

		<script>
		  'article aside footer header nav section time'.replace(/\w+/g,function(n){ document.createElement(n) })
		</script>

        
<?php  
  $currentTheme = wp_get_theme();
  $featureOptions = "";
  if("Directory" == $currentTheme){
    if(!defined(DS)) {
      define('DS',DIRECTORY_SEPARATOR);
    }
    $templateDir = get_template_directory();
    $neonLib = $templateDir.DS.'AIT'.DS.'Framework'.DS.'Libs'.DS.'Nette'.DS.'nette.min.inc';
    $neonFile = $templateDir.DS.'AIT'.DS.'Framework'.DS.'CustomTypes'.DS.'dir-item'.DS.'dir-item.neon';
    $featureArr = array();
    if(file_exists($neonLib)){
      $neonData = NNeon::decode(file_get_contents($neonFile, true));
      $isFeautre = false;
      
      foreach($neonData as $key => $fieldArr){
	if(!is_array($key) && trim($key) == 'Features'){
	  $isFeautre = true;
	  continue;
	}
	if(!$isFeautre)continue;
	if(!is_array($fieldArr) && trim($fieldArr) == 'section')break;
	if(!is_array($fieldArr))continue;	
	if(is_array($fieldArr) && array_key_exists('label',$fieldArr)){
	  if($pos = strpos($key, "_addnl")){continue;}
	  $featureArr[$key] = $fieldArr['label'];
	  $featureOptions.="<option name='".$featureArr[$key]."'>".$fieldArr['label']."</option>";
	}elseif(trim($fieldArr) == 'section'){
	  $isFeautre = false;
	  break;
	}	
      }
    }
  }
//code for selected categories

  $allowedCategories = array(
			     "Free Camps" => "free-camps",
			     "Rest Areas" => "rest-areas",
			     "Campgrounds" => "campgrounds",
			     "Caravan Parks" => "caravan-parks",
			     "House Sitting" => "house-sitting",
			     "Help Out" => "help-out",
			     "Dump Points" => "dump-points",
			     //New
				 "Accommodation" => 'accommodation',
				 "Camping Accessories" => 'camping-accessories',
				 "Entertainment" => "entertainment",
				 "Food & Wine" => "food-wine",
				 //"Fuel" => "fuel",  //Rob on 9th Aug by skype to remove Fuel
				 "Groceries" => 'groceries',
				 "Markets" => 'markets',
				 "Medical" => "medical",
				 "Personal Health" => "personal-health",
				 "Repairs" => "rv-sales-repairs",
         "RV & Auto Accessories" => "rv-auto-accessories",
				 "Services" => "services",
				 "Information Centre" => "information-centre",
				 //"IGA Stores" => "iga-stores", //Rob on 9th Aug by skype to remove IGA
			     /*"Park Overs" => "park-overs",
			     "Groceries/Shopping" => "groceries-shopping",
			     "Markets" => "markets",
			     "Medical" => "medical",
			     "Repairs" => "repairs",
			     "Things To Do" => "things-to-do",
			     "Camping Supplies" => "camping-supplies",
			     "Food & Wine" => "food-wine",
			     "Personal Health" => "personal-health",
			     "Services" => "services",
			     "Fuel" => "fuel",*/
			    );
  
  //code for selected Locations
  $allowedLocations = array(
			     "Australian Capital Territory" => 'act',
			     "New South Wales" => 'nsw',
			     "Victoria" => 'vic',
			     "South Australia" => 'sa',
			     "Western Australia" => 'wa',
			     "Northern Territory" => 'nt',
			     "Queensland" => 'qld',
			     "Tasmania" => 'tas'
			    );
  
  $ipAddress = $_SERVER['REMOTE_ADDR'];
  if('223.239.46.143' == $ipAddress){//
    //echo "<pre>";print_r($categories);echo "</pre>";
  }
  if(true){//'171.78.70.201' == $ipAddress
    //echo "<pre>";print_r($categories);echo "</pre>";
//    $newCategories = array();
//    foreach($categories as $sngCategory){
//      if(in_array(trim($sngCategory->slug),$allowedCategories) ||
//	 array_key_exists($sngCategory->name, $allowedCategories) ){
//		$newCategories[] = $sngCategory;
//      }
//    }
	
	$newCategories = array();
	foreach($allowedCategories as $allowedCategoryKey => $allowedCategorySlug){
		foreach($categories as $sngCategory){
			if( (trim($sngCategory->slug) == $allowedCategorySlug) || ($sngCategory->name == $allowedCategoryKey) ){
				$newCategories[] = $sngCategory;
				break;
			}
		}
	}
	
	//echo "<!--rasu";print_r($newCategories);echo "-->";
    $categories = $newCategories;
    
    $newLocations = array();
    foreach($locations as $sngLocation){
      if(in_array(trim($sngLocation->slug),$allowedLocations) ||
	 array_key_exists($sngLocation->name, $allowedLocations) ){
	$newLocations[] = $sngLocation;
      }
    }
    $locations = $newLocations;
  }
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var radiuses = [
							{ value: "Radius", label: "Radius" },
							{ value: "20", label: " 20 KM" },
							{ value: "50", label: " 50 KM" },
							{ value: "100", label: " 100 KM" },
							{ value: "150", label: " 150 KM" },
							{ value: "200", label: " 200 KM" },
							{ value: "300", label: " 300 KM" },
							{ value: "500", label: " 500 KM" }
						];
				
		for(var i=0; i < radiuses.length; i++){
		  radiusVal = radiuses[i].label.replace(/&amp;/g, "&");
		  radiusVal = radiusVal.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
		  key = radiuses[i].value;
		  jQuery('#all_radiuses').append(jQuery('<option>', { value : key,text: radiusVal}));
		}
		jQuery('#all_radiuses').multiselect({
							  disableIfEmpty: true,
							  maxHeight: 200,
							  buttonWidth: '95%',
							  numberDisplayed: 1,
							  nonSelectedText: 'Radius'
							 });

			{ifset $themeOptions->search->searchCategoriesHierarchical}
			//var categories = [ {!$categoriesHierarchical} ];
			  var categories = [
			  {foreach $newCategories as $cat}
			    { value: {$cat->term_id}, label: {$cat->name} }{if !($iterator->last)},{/if}
			  {/foreach}
			  ];
			{else}
			var categories = [
			{foreach $newCategories as $cat}
				{ value: {$cat->term_id}, label: {$cat->name} }{if !($iterator->last)},{/if}
			{/foreach}
			];
			{/ifset}

			{ifset $themeOptions->search->searchLocationsHierarchical}
			//var locations = [ {!$locationsHierarchical} ];
			  var locations = [
			  {foreach $locations as $loc}
			    { value: {$loc->term_id}, label: {$loc->name} }{if !($iterator->last)},{/if}
			  {/foreach}
			  ];
			{else}
			var locations = [
			{foreach $locations as $loc}
				{ value: {$loc->term_id}, label: {$loc->name} }{if !($iterator->last)},{/if}
			{/foreach}
			];
			{/ifset}

			var catInput = $( "#dir-searchinput-category" ),
				catInputID = $( "#dir-searchinput-category-id" ),
				locInput = $( "#dir-searchinput-location" ),
				locInputID = $( "#dir-searchinput-location-id" );

			catInput.autocomplete({
				minLength: 0,
				source: categories,
				focus: function( event, ui ) {
					var val = ui.item.label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					catInput.val( val );
					return false;
				},
				select: function( event, ui ) {
					var val = ui.item.label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					catInput.val( val );
					catInputID.val( ui.item.value );
					return false;
				}
			}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};
			var catList = catInput.autocomplete( "widget" );
			catList.niceScroll({ autohidemode: false });

			catInput.click(function(){
				catInput.val('');
				catInputID.val('0');
				catInput.autocomplete( "search", "" );
			});

			locInput.autocomplete({
				minLength: 0,
				//Zero is useful for local data with just a few items, but a higher value should be used when a single character search could match a few thousand items.
				source: locations,
				focus: function( event, ui ) {
					var val = ui.item.label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					locInput.val( val );
					return false;
				},
				select: function( event, ui ) {
					var val = ui.item.label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					locInput.val( val );
					locInputID.val( ui.item.value );
					return false;
				}
			}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};
			var locList = locInput.autocomplete( "widget" );
			//Returns a jQuery object containing the menu element.
			locList.niceScroll({ autohidemode: false });

			locInput.click(function(){
				locInput.val('');
				locInputID.val('0');
				locInput.autocomplete( "search", "" );
			});


			{if isset($_GET['dir-search']) && $_GET['dir-search'] == true}
			// fill inputs with search parameters
			$('#dir-searchinput-text').val({$searchTerm});
			catInputID.val({$_GET["categories"]});
			for(var i=0;i<categories.length;i++){
				if(categories[i].value == {$_GET["categories"]}) {
					var val = categories[i].label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					catInput.val(val);
				}
			}
			locInputID.val({$_GET["locations"]});
			for(var i=0;i<locations.length;i++){
				if(locations[i].value == {$_GET["locations"]}) {
					var val = locations[i].label.replace(/&amp;/g, "&");
						val = val.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
					locInput.val(val);
				}
			}
			{/if}
			
			for(var i=0; i < locations.length; i++){
			  locVal = locations[i].label.replace(/&amp;/g, "&");
			  locVal = locVal.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
			  locKey = locations[i].value;
			  jQuery('#all_locations').append(jQuery('<option>', { value : locKey,text:locVal}));
			}
			
			jQuery('#all_locations').multiselect({
							      disableIfEmpty: true,
							      maxHeight: 200,
							      buttonWidth: '95%',
							      numberDisplayed: 1,
							      nonSelectedText: 'States'
							     });
				      
			for(var i=0; i < categories.length; i++){
			  catVal = categories[i].label.replace(/&amp;/g, "&");
			  catVal = catVal.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
			  key = categories[i].value;
			  jQuery('#all_categories').append(jQuery('<option>', { value : key,text:catVal}));
			}
			jQuery('#all_categories').multiselect({
							      buttonWidth: '95%',
							      disableIfEmpty: true,
							      maxHeight: 200,
							      numberDisplayed: 1,
							      nonSelectedText: 'All Categories'
							      //includeSelectAllOption: true,
							      //selectAllText: 'Check all!',
							      });
			<?php
				$featureStr = "var features = [";
				$featureStrArr = "";
				$featureOrderArr = array(
						  'toilets' => 'toilets',
						  'showers' => 'showers',
						  'poweredSites' => 'poweredSites',
						  'drinkingWater' => 'drinkingWater',
						  'petsOk' => 'petsOk'
						  );
				/*foreach($featureOrderArr as $featureVal){
				  if(array_key_exists($featureVal, $featureArr)){
						  $label = $featureArr[$featureVal];
					  $featureStrArr[] = "{"." value: \"$featureVal\", label: \"$label\""."}";
					  unset($featureArr[$featureVal]);
				  }
				}*/
				$featCounter = $counter = 0;
				$tempStr = "var features = [";
				$totalFeats = count($featureArr);
				foreach( $featureArr as $featureKey => $featureVal){
					$featCounter++;$counter++;
					if($counter == 7){
						/*$featureStrArr[$counter++] = "{"." value: \"discount\", label: \"Discount Available\""."}";
						$featureStrArr[$counter++] = "{"." value: \"offer\", label: \"Offer Available\""."}";*/
						//Temporarily blocked above code
						$counter++;$counter++;
					}
					//Move counter 2 step for adding discount and offer
					$tempStr.="{"." value: \"$featureKey\", label: \"$featureVal\""."}"; //new code
					if($featCounter < $totalFeats){$tempStr.=",";}
					$temp = "{"." value: \"$featureKey\", label: \"$featureVal\""."}";
					$featureStrArr[$counter] = $temp;
				}
				
				$featureStr = $tempStr."];";
				//$featureStr.=implode(",",$featureStrArr)."];"; //old code
			?>
			//for features
			<?php echo $featureStr;?>
			for(var i=0; i < features.length; i++){
			  feaVal = features[i].label.replace(/&amp;/g, "&");
			  feaVal = feaVal.replace(/&nbsp;/g, " ").replace(/&#039;/g, "'");
			  feaKey = features[i].value;
			  jQuery('#all_features').append(jQuery('<option>', { value : feaKey,text:feaVal}));
			}
			jQuery('#all_features').multiselect({
							      buttonWidth: '95%',
							      disableIfEmpty: true,
							      maxHeight: 200,
							      numberDisplayed: 1,
							      nonSelectedText: 'All Features'
							    });
		});
		</script>
        
<style type="text/css">
	#directory-search {
		/*height: 140px;*/
		max-height:100%;
	}
	.se_box {
		width: 200px;
		float: left;
	}
	.fst_box {
		width: 160px;
		float: left;
		margin-right: 1.3%;
	 }
	.or_box {
		width: 30px;
		float: left;
		line-height: 40px;
	}
	.ver-or-box {
		color:#fff; 
		text-align:center; 
		width:135px; 
		display:block;	
	}
	.snd_box {
		width: 100px;
		float: left;
	}
	.trd_box {
		width: 110px;
		float: left;
		margin-left: 2%;
	}
	.forth_box {
		width: 210px;
		float: left;
		margin-left:25px;
	}
	.fifh_box {
		width: 210px;
		float: left;
	}
	.savefilt{
		width:135px;
		float:left;
	}
	.clrflt{
		width:126px;
		float:left;
		margin:0 0 0 15px;;
	}
	
	.sxth_box {
		width: 105px;
		float: left;
		margin-right: 1.3%;
	}
	.sxth_box .btn {
		padding:10px 0!important;
		white-space: normal !important;
		word-wrap: break-word !important;
	}
	.go_box {
		width: 135px;
		float: left;
		margin-right: 1.3%;
	}
	
	.go_box .btn {
		padding: 10px 0 !important;
		white-space: normal !important;
		word-wrap: break-word !important;	
	}
	.svnth_box {
		width: 110px;
		float: left;
	}
	.form-f-row {
		width: 42%;
		float: left;
		margin-top:10px;
	}
	.form-f-row .form-search {
		color: #fff;
		font-weight: bold;
		padding-left: 10px;
		margin-bottom: 3px;
	}
	.form-s-row {
		width: 1.6%;
		float: left;
	}
	.form-t-row {
		width: 47%;
		float: left;
		margin-top:10px;
	}
	.form-t-row .form-pre {
		color: #fff;
		font-weight: bold;
		margin-bottom: 3px;
	}
	.form-fort-row {
		width: 11%;
		float: left;
	}
	.form-fifh-row {
		/*width: 24%;*/
		float: left;
		margin:30px 0 0 0;
	}
	.loadbtn{
		width:35%;
		float:left;
		margin:0 0 0 15px;
	}
	.hor-or-box{
		width:15%;
		float:left;
		padding:10px 0 0 2px;
		text-align:center;
	}
	.gobtn{
		/*width:20%;*/
		float:left;
	}
	.btn-success {
		padding:20px!important;	
	}
	.btn-n:hover {
		background:rgba(0, 0, 0, 0) linear-gradient(#bc8e02, #ffd040) repeat scroll 0 0!important;
	}
	
	@media only screen and (max-width : 780px) {
		 .se_box {
				max-width:100%!important;
				float:none!important;
				clear:both!important;
				margin:5px auto!important;
		}
		 .fst_box {
			max-width:100%!important;
			float:none!important;
			clear:both!important;
			margin:5px auto!important;
		}
		.fst_box span {
			width: 90%!important;
			display: block!important;
			margin: auto!important;
		}
		.btn-group-vertical > .btn, .btn-group > .btn {
			float: none;
		}
		.snd_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.or_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.ver-or-box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.trd_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.forth_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.fifh_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.savefilt{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.clrflt{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.sxth_box {
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.go_box {
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.svnth_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.form-f-row {
			width: 65%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-f-row .form-search {
			text-align: center;
			margin-bottom: 3px;
		}
		.form-s-row {
			display: none;
		}
		.form-t-row {
			width: 65%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-t-row .form-pre {
			text-align: center;
			margin-bottom: 3px;
		}
		.form-fort-row {
			width: 65%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-fifh-row {
			width: 65%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.loadbtn{
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.hor-or-box{
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.gobtn{
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		#directory-search {
			height: auto;
			max-height:100%;
		}
		
	}
	
	/*SMALL DEVICE*/
	 @media only screen and (max-width : 420px) {
		.se_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.fst_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.fst_box span {
			width: 90%!important;
			display: block!important;
			margin: auto!important;
		}
		.btn-group-vertical > .btn, .btn-group > .btn {
			float: none;
		}
		.snd_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.or_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.ver-or-box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.trd_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.forth_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.fifh_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.savefilt{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.clrflt{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.sxth_box {
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.go_box {
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.svnth_box {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
		}
		.form-f-row {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-f-row .form-search {
			text-align: center;
			margin-bottom: 3px;
		}
		.form-s-row {
			display: none;
		}
		.form-t-row {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-t-row .form-pre {
			text-align: center;
			margin-bottom: 3px;
		}
		.form-fort-row {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
			padding-top:0!important;
		}
		.form-fifh-row {
			width: 100%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.loadbtn{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.hor-or-box{
			width: 90%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		.gobtn{
			width: 91%!important;
			float: none!important;
			clear: both!important;
			margin: 5px auto!important;
			text-align: center!important;
		}
		#directory-search {
			height: auto;
			max-height:100%;
		}
	}
</style>
<script type="text/javascript" src="https://www.google.com/jsapi"></script><!--rasu 5th Dec 15 -->
<script type="text/javascript">
	if(google.loader.ClientLocation){
	    var loc = google.loader.ClientLocation;
	    if(loc.address){}
		if(loc.latitude){}
	}
</script>
	</head>

	<body data-themeurl="{$themeUrl}" <?php body_class('ait-directory'); ?> >
<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>
		<div id="page" class="hfeed {if (isset($themeOptions->general->layoutStyle) && $themeOptions->general->layoutStyle == 'narrow')} narrow{/if}" >
			{include 'snippets/branding-header.php'}
		<?php
			$readOnly = 1;
			$ipAddress = $_SERVER['REMOTE_ADDR'];
			if(isset($_REQUEST['readonly'])){
				$readonly = 0;
			}
		?>
{if (isset($_REQUEST['readonly']))}
<!-- rasu if header-->
{else}
<!-- rasu else header-->
{/if}
		<div id="resultDialog" title="&nbsp; Directory Search Result" style="display: none;"><p style="margin-left: 10px;" id="error_message">We could not find search result for this combination, please try any other combination!</p></div>
		<?php
			$showSearchBar = false;
			if((isset($_REQUEST['categories'])) || (isset($_REQUEST['locations']))){
				$showSearchBar = true;
			}
			//'ait-dir-item' == get_post_type()
		?>
		<div id="directory-search" <?php
			if(!is_user_logged_in())
				echo "style='display:none;'";
			elseif(!$showSearchBar)
				echo "style='display:none;'";
				?> data-interactive="{ifset $themeOptions->search->enableInteractiveSearch}yes{else}no{/ifset}">
			<div class="defaultContentWidth clearfix">
				<form action="{$homeUrl}" id="dir-custom-search-form" method="get" class="dir-searchform" style="<?php echo $style;?>">
					<div style="width:100%; margin:0 auto 0 auto;" class="form_top">
						<div style="padding:0 0;">
							<div class="form-f-row">
								<div class="form-search">Search By</div>
								<div class="trd_box"><select class="location_top" id="all_locations" style="width:100%; margin:0 2% 0 0;" name='categories' multiple="multiple"></select></div>
						  
								<div class="or_box"><strong style="color:#fff;">Or</strong></div>
								<div class="fst_box" id="prefetch"><input type="text" class="typeahead"  name="s" style="width:100%; margin:0 2% 0 0; padding-left:10px; height:41px; color:#000; border-radius:5px;" id="dir-custom-searchinput-text" placeholder="Town/Location" class="dir-searchinput search_top"{ifset $isDirSearch} value="{$site->searchQuery}"{/ifset} {if (!isset($_REQUEST['readonly']))}{/if}></div>
								<div class="snd_box"><select class="radius_top" id="all_radiuses" style="width:100%; margin:0 2% 0 0;" name='radius'></select></div>
							</div>
							<div class="form-s-row">
							  <div style="border-left:1px solid #fff; height:120px; margin:0 0;">&nbsp;</div>
							</div>
						
							<div class="form-t-row">
								<div class="form-pre">Filters</div>
								
								<div class="forth_box"><select id="all_categories" class="categories_top" style="width:100%; margin:0 2% 0 0;" name='locations' multiple="multiple"></select></div>
								
								<div class="fifh_box"><select id='all_features' class="features_top" style="width:100%; margin:0 2% 0 0;" name='features' multiple="multiple"></select></div>
								<div class="clear"></div>
								<div style="line-height:5px; height:5px;"></div>
								<div class="savefilt"><input name="btnPreference" value="Save Filters" id="btnPreference" class="btn btn-n btn-warning flatten btn-block" type="button" style="padding:4px; background:rgba(0, 0, 0, 0) linear-gradient(#ffd040, #bc8e02) repeat scroll 0 0;"></div>
								<div class="clrflt"><input name="btnClearFilters" value="Clear Filters" id="btnClearFilters" class="btn btn-n btn-warning flatten btn-block" type="reset" style="padding:4px; background:rgba(0, 0, 0, 0) linear-gradient(#ffd040, #bc8e02) repeat scroll 0 0;"></div>
                                <div class="loadbtn"><a href = '#-1' style= 'text-decoration:none;' onClick="javascript:return load_preference();"><input 
                                style = 'text-decoration:none;padding:4px; background:rgba(0, 0, 0, 0) linear-gradient(#ffd040, #bc8e02) repeat scroll 0 0;' name="btnGo" value="Load Saved Filters" class="btn btn-n btn-warning flatten btn-block" 
                                type="button"></a></div>
							</div>
							
							<div class="form-s-row"><div style="border-left:1px solid #fff; height:120px; margin:0 0;">&nbsp;</div></div>
							
							<div class="form-fifh-row">
								<div class="gobtn"><input name="btnGo" value="GO" id="btnUpdate" class="btn btn-success flatten btn-block" type="button"></div>
							</div>
						</div>
						<!-- next row rasa --> 
						<div><div><div id="div1" onClick=""></div></div></div>
					</div>
				</form>
				  
				  <form action="{$homeUrl}" id="dir-search-form" method="get" class="dir-searchform" style="display:none;">
					  <div id="dir-search-inputs">
						  <div id="dir-holder">
							  <div class="dir-holder-wrap">
							  <input type="text" name="s" id="dir-searchinput-text" placeholder="{__ 'Search keyword...'}" class="dir-searchinput"{ifset $isDirSearch} value="{$site->searchQuery}"{/ifset}>

							  {ifset $themeOptions->search->showAdvancedSearch}
							  <div id="dir-searchinput-settings" class="dir-searchinput-settings">
								  <div class="icon"></div>
								  <div id="dir-search-advanced" style="display: none;">
									  {ifset $themeOptions->search->advancedSearchText}<div class="text">{$themeOptions->search->advancedSearchText}</div>{/ifset}
									  <div class="text-geo-radius clear">
										  <div class="geo-radius">{__ 'Radius:'}</div>
										  <div class="metric">km</div>
										  <input type="text" name="geo-radius" id="dir-searchinput-geo-radius" value="{ifset $isGeolocation}{$geolocationRadius}{else}{ifset $themeOptions->search->advancedSearchDefaultValue}{$themeOptions->search->advancedSearchDefaultValue}{else}100{/ifset}{/ifset}" data-default-value="{ifset $themeOptions->search->advancedSearchDefaultValue}{$themeOptions->search->advancedSearchDefaultValue}{else}100{/ifset}">
									  </div>
									  <div class="geo-slider">
										  <div class="value-slider"></div>
									  </div>
									  <div class="geo-button">
										  <input type="checkbox" name="geo" id="dir-searchinput-geo"{ifset $isGeolocation} checked="true"{/ifset}>
									  </div>
									  <div id="dir-search-advanced-close"></div>
								  </div>
							  </div>
							  <input type="hidden" name="geo-lat" id="dir-searchinput-geo-lat" value="{empty($_GET['geo-lat']) ? 0 : $_GET['geo-lat']}">
							  <input type="hidden" name="geo-lng" id="dir-searchinput-geo-lng" value="{empty($_GET['geo-lng']) ? 0 : $_GET['geo-lng']}">
							  {/ifset}

							  <input type="text" id="dir-searchinput-category" placeholder="{__ 'All categories'}">
							  <input type="text" name="categories" id="dir-searchinput-category-id" value="{$mapCategory}" style="display: none;">

							  <input type="text" id="dir-searchinput-location" placeholder="{__ 'All locations'}">
							  <input type="text" name="locations" id="dir-searchinput-location-id" value="{$mapLocation}" style="display: none;">

							  <div class="reset-ajax"></div>
							  </div>
						  </div>
					  </div>
					  {ifset $themeOptions->search->showAdvancedSearch}

					  {/ifset}
					  <div id="dir-search-button">
						  <input type="submit" value="{__ 'Search'}" class="dir-searchsubmit">
					  </div>
					  <input type="hidden" name="dir-search" value="yes" />
					  {if !empty($_GET['lang'])}<input type="hidden" name="lang" value="{$_GET['lang']}" />{/if}
				  </form>
				</div>

			{if isset($_REQUEST['nomap']) && $_REQUEST['nomap'] == 1}
				<?php $style = 'display:none;'; ?>
			{/if}
			<div style='<?php echo $style;?>' id="directory-main-bar" data-category="{$mapCategory}" data-location="{$mapLocation}" data-search="{$mapSearch}" data-geolocation="{ifset $isGeolocation}true{else}false{/ifset}"{if $headerType == 'image'} class="directory-main-bar-image"{/if}>
			{if $headerType == 'slider'}
				{if function_exists('putRevSlider')}
					{? putRevSlider($headerSlider)}
				{/if}
			{elseif $headerType == 'image'}
				<img src="{$headerImage}" style="width: 100%; height: auto;" />
			{/if}
			</div>
			{ifset $isDirSingle}
				{include 'snippets/map-single-javascript.php'}
			{else}
				{if $headerType == 'map'}
          <?php
            if($isMyIP){
              ;//include('snippets/frc-map-javascript.php');
            }else{
              ;//include('snippets/map-javascript.php');
            }
          ?>
          {include 'snippets/map-javascript.php'}
				{/if}
			{/ifset}
  <style>
 #prefetch .tt-dropdown-menu {
  max-height: 250px;
  overflow-y: auto;
  background-color:lightgrey;
}
 #prefetch .twitter-typehead {
  max-height: 250px;
  overflow-y: auto;
}
.tt-dataset, .tt-dataset-locations {
  max-height: 250px;
  width:150px;
  overflow-y: auto;
  background-color:lightgrey;
}
.row_hover:hover{
 color:blue;
 background-color:yellow;
}
</style>
<style>
  .btn{
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    background-image: none;
    border: 0px solid transparent;
    white-space: nowrap;
    padding:10px 15px 10px 15px;
    font-size: 15px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .btn-block {
    display: block;
    width: 100%;
  }

.btn-success:hover,.btn-success:focus,.btn-success:active,.btn-success.active,.open > .dropdown-toggle.btn-success {
  color: #ffffff;
  background-color: #77bc22;
  border-color: #77bc22;
}
.btn-success:active,.btn-success.active,.open > .dropdown-toggle.btn-success {
  background-image: none;
}
.btn-success.disabled,.btn-success[disabled],fieldset[disabled] .btn-success,.btn-success.disabled:hover,.btn-success[disabled]:hover,fieldset[disabled] .btn-success:hover,.btn-success.disabled:focus,.btn-success[disabled]:focus,fieldset[disabled] .btn-success:focus,.btn-success.disabled:active,.btn-success[disabled]:active,fieldset[disabled] .btn-success:active,.btn-success.disabled.active,.btn-success[disabled].active,fieldset[disabled] .btn-success.active {
  background-color: #77bc22;
  border-color: #77bc22;
}
.btn-success .badge {
  color: #77bc22;
  background-color: #ffffff;
}
</style>

<?php
  $geoLocationRadius = 100;//default
  if(isset($isGeolocation)){
    $geolocationRadius = $geolocationRadius;
  }else{
    if(isset($themeOptions->search->advancedSearchDefaultValue)){
      $geoLocationRadius = $themeOptions->search->advancedSearchDefaultValue;
    }
  }
?>

  <div class="defaultContentWidth clearfix" style='padding-left:30px;' style='display: none;'>
    
  </div>				
			</div>
<script>
	<?php
		$current_user = wp_get_current_user();
	?>
	/*
	 <?php echo $current_user->ID;?>
	 */
jQuery( "#dir-custom-search-form" ).keypress(function(event) {
  if ( event.which == 13 ) {
     event.preventDefault();
    jQuery('#btnUpdate').trigger( "click" );
  }
  
});
jQuery('.input').keypress(function (e) {
  if (e.which == 13) {
    //$('form#login').submit();
    //return false;    //<---- Add this line
    alert('fdsfsf');
  }
});

 var locations = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: 'https://freerangecamping.com.au/directory/wp-content/locations/json_locations_flat.php'
 
});
jQuery('#prefetch .typeahead').typeahead(null, {
  name: 'locations',
  source: locations,
  minlength:3,
  limit:10
 });

//----------------
jQuery( document ).ready(function() {
    jQuery( "#link_house_sitting" ).click(function() {
       console.log( "link_house_sitting!" );
        
        /*jQuery.blockUI({ message: "Please wait while we redirect you to upgrade membership page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
        jQuery.ajax({
            type: "GET",
            url: "https://www.freerangecamping.com.au/directory/index.php?/register/help-outs",
            data: "",
            success: function(msg){
              
              window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1399015933&existing=1';
              jQuery.unblockUI();
            }
        });*/
		jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
        jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1399015933&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				alert("Membership Level Updated!")
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
              //window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1399015933&existing=1';
              //jQuery.unblockUI();
            }
        });
    });

     //Start:Free
	jQuery( "#link_free" ).click(function() {
		jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
		jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1414469692&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				jQuery.unblockUI();
				alert("Membership Level Updated!");
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
            }
        });
		/*
		jQuery.ajax({
			type: "GET",
			url: "https://www.freerangecamping.com.au/directory/index.php?/register/free-membership",
			data: "",
			success: function(msg){
			  
			  window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1414469692&existing=1';
			  jQuery.unblockUI();
			}
		});*/
	});
    //End:Free

	//Start: Low Cost
	jQuery( "#link_low_cost" ).click(function() {
	   jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
	   /*
		jQuery.ajax({
			type: "GET",
			url: "https://www.freerangecamping.com.au/directory/index.php?/register/low-cost-campgrounds",
			data: "",
			success: function(msg){
				window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1399011470&existing=1';
				jQuery.unblockUI();
			}
		});
		*/
	   jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1399011470&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				jQuery.unblockUI();
				alert("Membership Level Updated!");
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
            }
        });
	 });
	//End:Low cost

    //Start:25plus
	jQuery( "#link_25_plus" ).click(function() {
		jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
		/*
		jQuery.ajax({
			type: "GET",
			url: "https://www.freerangecamping.com.au/directory/index.php?/register/25plus-listings",
			data: "",
			success: function(msg){
			
				window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1415928787&existing=1';
				jQuery.unblockUI();
			}
		});
		*/
		jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1415928787&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				jQuery.unblockUI();
				alert("Membership Level Updated!");
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
            }
        });
	});
    //End:25plus

    //Start:House Sitting
	jQuery( "#link_housesitting" ).click(function() {
		jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
		/*
		jQuery.ajax({
			type: "GET",
			url: "https://www.freerangecamping.com.au/directory/index.php?/register/house-sitting",
			data: "",
			success: function(msg){
				window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1399015844&existing=1';
				jQuery.unblockUI();
			}
		});
		*/
		jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1399015844&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				jQuery.unblockUI();
				alert("Membership Level Updated!");
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
            }
        });
	});
    //End:House Sitting

    //Start:Business
	jQuery( "#link_business" ).click(function() {
		jQuery.blockUI({ message: "Please wait while we update your memembership.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
		/*
		jQuery.ajax({
			type: "GET",
			url: "https://www.freerangecamping.com.au/directory/index.php?/register/business-listings",
			data: "",
			success: function(msg){
				window.location.href = 'https://www.freerangecamping.com.au/directory/wishlist-member/?reg=1399015963&existing=1';
				jQuery.unblockUI();
			}
		});
		*/
		jQuery.ajax({
            type: "GET",
            url: "https://freerangecamping.com.au/directory/wp-content/manage_levels/manage_level.php?level_id=1399015963&token_id=<?php echo $current_user->ID;?>",
            data: "",
            success: function(msg){
				jQuery.unblockUI();
				alert("Membership Level Updated!");
				jQuery.blockUI({ message: "Please wait while we redirect you to welcome page.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
				window.location.href = 'https://www.freerangecamping.com.au/directory/welcome-free-range-camping-listing-members/';
            }
        });
	});
    //End:Business
    //jQuery('#all_features').prop( "disabled", false );
});
//----------------
<?php
	if(isset($_REQUEST['nomap']) && $_REQUEST['nomap'] == 1){
		echo "jQuery('#dir-custom-search-form').hide();";
	}
?>
</script>