<!doctype html>

<!--[if IE 8]><html class="no-js oldie ie8 ie" lang="{$site->language}"><![endif]-->
<!--[if gte IE 9]><!--><html class="no-js" lang="{$site->language}"><!--<![endif]-->

	<head>
		<meta charset="{$site->charset}">
		{mobileDetectionScript}
		<meta name="Author" content="AitThemes.club, http://www.ait-themes.club">

		<title>{title}</title>
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="{$site->pingbackUrl}">

		{if $themeOptions->fonts->fancyFont->type == 'google'}
		<link href="http://fonts.googleapis.com/css?family={$themeOptions->fonts->fancyFont->font}" rel="stylesheet" type="text/css">
		{/if}

		<link id="ait-style" rel="stylesheet" type="text/css" media="all" href="{less}">

		{head}

		<script>
		  'article aside footer header nav section time'.replace(/\w+/g,function(n){ document.createElement(n) })
		</script>

		<script type="text/javascript">
		jQuery(document).ready(function($) {

			{ifset $themeOptions->search->searchCategoriesHierarchical}
			var categories = [ {!$categoriesHierarchical} ];
			{else}
			var categories = [
			{foreach $categories as $cat}
				{ value: {$cat->term_id}, label: {$cat->name} }{if !($iterator->last)},{/if}
			{/foreach}
			];
			{/ifset}

			{ifset $themeOptions->search->searchLocationsHierarchical}
			var locations = [ {!$locationsHierarchical} ];
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

		});
		</script>

	</head>

	<body <?php body_class('ait-directory'); ?> data-themeurl="{$themeUrl}">

		<div id="page" class="hfeed {if (isset($themeOptions->general->layoutStyle) && $themeOptions->general->layoutStyle == 'narrow')} narrow{/if}" >

			{include 'snippets/branding-header.php'}

			<div id="directory-main-bar" data-category="{$mapCategory}" data-location="{$mapLocation}" data-search="{$mapSearch}" data-geolocation="{ifset $isGeolocation}true{else}false{/ifset}"{if $headerType == 'image'} class="directory-main-bar-image"{/if}>
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
					{include 'snippets/map-javascript.php'}
				{/if}
			{/ifset}

			<div id="directory-search" data-interactive="{ifset $themeOptions->search->enableInteractiveSearch}yes{else}no{/ifset}">
				<div class="defaultContentWidth clearfix">
					<form action="{$homeUrl}" id="dir-search-form" method="get" class="dir-searchform">
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
			</div>
