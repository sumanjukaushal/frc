{extends $layout}

{block content}
<?php
	$postid = get_the_ID();
	$postLink = get_permalink();
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
    $isMyIP = isRemoteAddress(array('112.196.125.178', '149.135.34.230'));
	$backToSearchDiv = "";
	$siteURL = site_url();
	$frcReferrer = $_SERVER['HTTP_REFERER'];	//$pos = strpos($frcReferrer, $siteURL);
	$pos = !empty($frcReferrer) && strpos($frcReferrer, "iframe");
	$currUserID = get_current_user_id();
	if ($pos !== false || !empty($frcReferrer)) {
		if(get_current_user_id()){
			
			$backToSearchDiv = "<div style='float: right;' class='clearfix'><a href='$frcReferrer' id='frc_bk_2_search' class='contact-owner button' style='text-decoration: none;'>Back to Search Results</a></div>";
		}
	}else{
	    if($currUserID){
	        global $wpdb;
            $userMetaQry = "SELECT `id`, `meta_value` FROM `frc_user_metas` WHERE `user_id` = $currUserID AND `meta_key` = 'search_url' LIMIT 1";
    	    $metaRes = $wpdb->get_results( $userMetaQry );
    	    if(count($metaRes)){
    	        $frcReferrer = $metaRes[0]->meta_value;
    	        $backToSearchDiv = "<div style='float: right;' class='clearfix'><a href='$frcReferrer' id='frc_bk_2_search' class='contact-owner button' style='text-decoration: none;'>Back to Search Results</a></div>";
    	    }
	    }
	}
	if($isMyIP){
	    //echo "<pre>";print_r($_SERVER);echo "</pre>";
	}
?>

<style type="text/css">
dl.item-address dt {
    clear: both;
	width:auto;
}
dl.item-address dd {
    padding-left: 120px;
}
</style>
<?php
	$themeURL = get_template_directory_uri();
	$themeURL.='/design/img/rsz_apps-google-maps-icon.png';
	if('27.255.219.100' == $_SERVER['REMOTE_ADDR']){}
	$locFound = $catFound = $nswRA = false;//New South wales with Rest Area
	
	//finding location
	$locTerms = get_the_terms($postid,'ait-dir-item-location');
	if(is_array($locTerms)){
		foreach($locTerms as $locID => $locObj){
			if(trim($locObj->name) == 'New South Wales' || $locObj->slug == 'nsw'){
				$locFound = true;
				break;
			}
		}
	}
	
	//finding category
	$catTerms = get_the_terms($postid,'ait-dir-item-category');
	foreach($catTerms as $catID => $catObj){
		if(trim($catObj->name) == 'Rest Areas' || $catObj->slug == 'rest-areas'){
			$catFound = true;
			break;
		}
	}
	
	//----------------
	if($catFound && $locFound){
		$nswRA = true;//echo "Found!";
	}
?>

<article id="post-{$post->id}" class="{$post->htmlClasses}">

	<header class="entry-header">
		<?php echo $backToSearchDiv;?>
		<div style='float: right; display:none;' class="clearfix"><a href='#-1' id='toggleMap' url='{$themeURL}' style='text-decoration: none;'>Show/Hide Map <img height='40' width='40' src='{$themeURL}' alt='View Map'/></a></div>
		<h1 class="entry-title">
			<a href="{$post->permalink}" title="Permalink to {$post->title}" rel="bookmark">{$post->title}</a>
			{if $rating}
			<span class="rating">
				{for $i = 1; $i <= $rating['max']; $i++}
					<span class="star{if $i <= $rating['val']} active{/if}"></span>
				{/for}
			</span>
			{/if}
		</h1>
		
		<div class="category-breadcrumb clearfix">
			<span class="here">{__ 'You are here'}</span>
			<span class="home"><a href="{!$homeUrl}">{__ 'Home'}</a>&nbsp;&nbsp;&gt;</span>
			{foreach $ancestors as $anc}
				{first}<span class="ancestors">{/first}
				<a href="{!$anc->link}">{!$anc->name}</a>&nbsp;&nbsp;&gt;
				{last}</span>{/last}
			{/foreach}
			{ifset $term}<span class="name"><a href="{!$term->link}">{!$term->name}</a></span>{/ifset}
			<span class="title"> >&nbsp;&nbsp;{$post->title}</span>
		</div>
		
	</header>
<script>
	jQuery( "#toggleMap" ).click(function() {
                console.log("toggleMap");
		jQuery( "#directory-main-bar" ).toggle( "slow", function() {
		  console.log("toggleMap-108");// Animation complete.
                   //alert('4534');
		});
	});
	jQuery( document ).ready(function() {
		jQuery( "#frc_bk_2_search" ).click(function() {
			jQuery.blockUI({ message: "Please wait whilst you are taken back to the search results.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
		});
	});
	jQuery(window).load(function () {
                console.log("1234");
		jQuery( "#directory-main-bar" ).hide( 1000 );
                //alert('4534');
		/*
		jQuery( "#directory-main-bar" ).hide( "slow", function() {
			;
		});
		*/
	});
</script>
<script>
document.onreadystatechange = function(){
     if(document.readyState === 'complete'){
        //alert("fads");
        var DMB = document.getElementById("directory-main-bar");
                console.log("complete");
		jQuery( "#directory-main-bar" ).hide( 1000 );
     }
}
document.addEventListener('DOMContentLoaded', function() {
    //alert("fads");
   /*var DMB = document.getElementById("directory-main-bar");
                console.log("complete");
		jQuery( "#directory-main-bar" ).hide( 1000 );*/
}, false);
   
                

</script>
	<div class="entry-content clearfix">

		<div class="item-image">

			{if $post->thumbnailSrc}
			<img src="{thumbnailResize $post->thumbnailSrc, w => 140, h => 200}" alt="{__ 'Item image'}">
			{/if}

			{if isset($options['emailContactOwner']) && (!empty($options['email']))}
				{if $nswRA}
				<!--nothing to show-->
				{else}
				<a id="contact-owner-button" class="contact-owner button" href="#contact-owner-form-popup">{_ "Contact owner"}</a>
				{/if}
			{/if}

			{if (isset($themeOptions->directory->enableClaimListing)) && (!$hasAlreadyOwner)}
			<a id="claim-listing-button" class="claim-listing-button" href="#claim-listing-form-popup">{_ "Own this business?"}</a>
			{/if}

			{if isset($options['gpsLatitude'], $options['gpsLongitude']) && !(empty($options['gpsLatitude']) && empty($options['gpsLongitude']))}
			<div class="itemDirections">
			</div>
			<input type="text" style="display:none" id="address-input"/>
			{/if}

		</div>

		{if isset($options['emailContactOwner']) && (!empty($options['email']))}
		<!-- contact owner form -->
		<div id="contact-owner-form-popup" style="display:none;">
			<div id="contact-owner-form" data-email="{$options['email']}">

				<h3>{_ "Contact owner"}</h3>

				<div class="input name">
					<input type="text" class="cowner-name" name="cowner-name" value="" placeholder="{_ 'Your name'}">
				</div>
				<div class="input email">
					<input type="text" class="cowner-email" name="cowner-email" value="" placeholder="{_ 'Your email'}">
				</div>
				<div class="input subject">
					<input type="text" class="cowner-subject" name="cowner-subject" value="" placeholder="{_ 'Subject'}">
				</div>
				<div class="input message">
					<textarea class="cowner-message" name="cowner-message" cols="30" rows="4" placeholder="{_ 'Your message'}"></textarea>
					<input type="hidden" class='cowner-item-title' name='cowner-item-title' value='<?php echo get_the_title();?>' />
					<input type="hidden" class='cowner-item-link' name='cowner-item-link' value='<?php echo $postLink;?>' />
				</div>
				<button class="contact-owner-send">{_ "Send message"}</button>

				<div class="messages">
					<div class="success" style="display: none;">{_ "Your message has been successfully sent"}</div>
					<div class="error validator" style="display: none;">{_ "Please fill out email, subject and message"}</div>
					<div class="error server" style="display: none;"></div>
				</div>

			</div>
		</div>
		{/if}

		{if (isset($themeOptions->directory->enableClaimListing)) && (!$hasAlreadyOwner)}
		<!-- claim listing form -->
		<div id="claim-listing-form-popup" style="display:none;">
			<div id="claim-listing-form" data-item-id="{$post->id}">

				<h3>{_ "Enter your claim"}</h3>

				<div class="input name">
					<input type="text" class="claim-name" name="claim-name" value="" placeholder="{_ 'Your name'}">
				</div>
				<div class="input email">
					<input type="text" class="claim-email" name="claim-email" value="" placeholder="{_ 'Your email'}">
				</div>
				<div class="input number">
					<input type="text" class="claim-number" name="claim-number" value="" placeholder="{_ 'Your phone number'}">
				</div>
				<div class="input username">
					<input type="text" class="claim-username" name="claim-username" value="" placeholder="{_ 'Username'}">
				</div>
				<div class="input message">
					<textarea class="claim-message" name="claim-message" cols="30" rows="4" placeholder="{_ 'Your claim message'}"></textarea>
				</div>
				<button class="claim-listing-send">{_ "Submit"}</button>

				<div class="messages">
					<div class="success" style="display: none;">{_ "Your claim has been successfully sent"}</div>
					<div class="error validator" style="display: none;">{_ "Please fill out inputs!"}</div>
					<div class="error server" style="display: none;"></div>
				</div>

			</div>
		</div>
		{/if}

		{!$post->content}

	</div>
	<div style="margin-bottom:40px;">
	<h3>My Bookmarks</h3>
    <?php global $wpb; echo $wpb->bookmarks();?>
	</div>
	<div class="itemGallery"><!--new addition rasa-->
	</div>

	{ifset $themeOptions->directory->showShareButtons}
	<div class="item-share">
		<!-- facebook -->
		<div class="social-item fb">
			<iframe src="//www.facebook.com/plugins/like.php?href={$post->permalink}&amp;send=false&amp;layout=button_count&amp;width=113&amp;show_faces=true&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:113px; height:21px;" allowTransparency="true"></iframe>
		</div>
		<!-- twitter -->
		<div class="social-item">
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="{$post->permalink}" data-text="{$themeOptions->directory->shareText}" data-lang="en">Tweet</a>
			<script>!function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		<!-- google plus -->
		<!-- Place this tag where you want the +1 button to render. -->
		<div class="social-item">
			<div class="g-plusone"></div>
			<!-- Place this tag after the last +1 button tag. -->
			<script type="text/javascript">
			  (function() {
			    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			    po.src = 'https://apis.google.com/js/plusone.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script>
		</div>

	</div>
	{/ifset}

	<hr>
	<div class="item-info">

		{if 
			(!empty($options['address'])) ||
			(!empty($options['gpsLatitude'])) ||
			(!empty($options['property_type'])) ||
			(!empty($options['telephone'])) ||
			(!empty($options['email'])) ||
			(!empty($options['police_check_required'])) ||
			(!empty($options['pet_minding_required'])) ||
			(!empty($options['gardening'])) ||
			(!empty($options['other'])) ||
			(!empty($options['web'])) ||
			(!empty($options['location'])) ||
			(!empty($options['access'])) ||
			(!empty($options['responsible_authority'])) ||
			(!empty($options['discount'])) ||
			(!empty($options['discount_detail'])) ||
			(!empty($options['offer'])) ||
			(!empty($options['offer_detail'])) ||
			(!empty($options['limit_discount'])) ||
			(!empty($options['pricing_detail'])) ||
			(!empty($options['bookingLink'])) ||
			(!empty($options['help_required'])) ||
			(!empty($options['start_date'])) ||
			(!empty($options['end_date'])) ||
			(!empty($options['per_month_uses'])) ||
			(!empty($options['duration']))
		}
		<dl class="item-address" style="padding:0 0 20px 0;">

			<dt class="title"><h4 style="margin:0 0 10px 30px;">{__ 'Our address'}</h4></dt>

			{if (!empty($options['address']))}
				{if $options["directoryType"] == 'directory_5' || $options["directoryType"] == 'directory_6'}
					<dt class="address">{__ 'Suburb:'}</dt>
				{else}
					<dt class="address">{__ 'Address:'}</dt>
				{/if}
			{/if}

			{if (!empty($options['address']))}
			<!--dt class="address">{__ 'Address:'}</dt-->
			<dd class="data">{$options['address']}</dd>
			{/if}
			
			{if (!empty($options['wlm_city']))}
			<dt class="wlm_city">{__ 'Suburb:'}</dt>
			<dd class="data">{$options['wlm_city']}</dd>
			{/if}
			
			{if (!empty($options['wlm_state']))}
			<dt class="wlm_city">{__ 'State:'}</dt>
			<dd class="data">{$options['wlm_state']}</dd>
			{/if}
			
			{if (!empty($options['wlm_zip']))}
			<dt class="wlm_city">{__ 'Post Code:'}</dt>
			<dd class="data">{$options['wlm_zip']}</dd>
			{/if}
			
			{if (!empty($options['gpsLatitude']))}
			<dt class="gps">{__ 'GPS:'}</dt>
			<dd class="data">{$options['gpsLatitude']}, {$options['gpsLongitude']}</dd>
			{/if}

			{if (!empty($options['property_type']))}
			<dt class="property_type">Property Type:</dt>
			<dd class="data">{$options['property_type']}</dd>
			{/if}
			
			{if (!empty($options['telephone']))}
			<dt class="phone">{__ 'Telephone:'}</dt>
				{if $nswRA}
				<dd class="data">&nbsp;&nbsp;<!-- nothing to show-->&nbsp;</dd>
				{else}
				<dd class="data"><a href="tel:{$options['telephone']}">{$options['telephone']}</a></dd>
				{/if}
			{/if}

			{if (!empty($options['email']))}
			<dt class="email">{__ 'Email:'} </dt>
				{if $nswRA}
				<dd class="data">&nbsp;&nbsp;<!-- nothing to show-->&nbsp;</dd>
				{else}
				<dd class="data"><a href="mailto:{$options['email']}">{$options['email']}</a></dd>
				{/if}
			{/if}
			
			{if ($options['police_check_required']['enable']) == 'enable' }	
			<dt class="email">{__ 'Police Check:'} </dt>
			<dd class="data">Yes</dd>
			{/if}
			
			{if ($options['pet_minding_required']['enable']) == 'enable'}
			<dt class="pet_minding">Pet Minding*:</dt>
			<dd class="data">Yes</dd>
				{if (!empty($options['pet_minding_addnl']))}
				<dt class="email" style='line-height:12px;padding-top:2px;padding-bottom:5px;'>&nbsp;&nbsp;&nbsp;</dt>
				<dd class="data" style='line-height:12px;padding-top:2px;padding-bottom:5px;'>{$options['pet_minding_addnl']}</dd>
				{/if}
			{/if}

			{if (!empty($options['web']))}
			<dt class="web">{__ 'Web:'} </dt>
				{if $nswRA}
				<dd class="data"><a href="http://www.rms.nsw.gov.au/roads/using-roads/trip-information/rest-areas/">http://www.rms.nsw.gov.au/roads/using-roads/trip-information/rest-areas/</a></dd>
				{else}
				<dd class="data"><a href="{$options['web']}">{$options['web']}</a></dd>
				{/if}
			{/if}
			
			{if (!empty($options['location']))}
			<dt class="Location">{__ 'Location:'} </dt>
			<dd class="data"><a href="{$options['location']}">{$options['location']}</a></dd>
			{/if}
			
			{if (!empty($options['access']))}
			<dt class="access">{__ 'Access:'} </dt>
			<dd class="data"><a href="{!$options['access']}">{$options['access']}</a></dd>
			{/if}

			{if (!empty($options['responsible_authority']))}
			<dt class="responsible_authority" style='line-height:12px;padding-top:5px;padding-bottom:2px;'>{__ 'Responsible'} </dt>
			<dd class="data" style='line-height:12px;padding-top:5px;padding-bottom:2px;'>&nbsp;&nbsp;</dd>
			<dt class="responsible_authority" >{__ 'Authority:'} </dt>
			<dd class="data">{$options['responsible_authority']}</dd>
			{/if}
			
			{if (!empty($options['discount']))}
				{if ($options['offer']['enable']) == 'enable'}
				<!--<dt class="discount">{__ 'Discount:'} </dt>
				<dd class="data" style='color:red;'>Available</dd>-->
				{/if}
				{if (!empty($options['discount_detail']))}
				<dt class="discount_detail">Discount:</dt>
				<dd class="data" style='color:red;'>{$options['discount_detail']}</dd>
				{/if}				
			{/if}
			
			
			
			{if (!empty($options['offer']))}
				{if ($options['offer']['enable']) == 'enable'}
				<dt class="offer">{__ 'Offer'} </dt>
				<dd class="data" style='color:red;'>Available</dd>
				{/if}
				
			{/if}
			
			{if (!empty($options['offer_detail']))}
				<dt class="offer_detail">Offer Detail:</dt>
				<dd class="data" style='color:red;'>{$options['offer_detail']}</dd>
			{/if}
			
			{if (!empty($options['pricing']))}
				{if ($options['pricing']['enable']) == 'enable'}
				<dt class="pricing">{__ 'Pricing:'} </dt>
				<dd class="data" style='color:red;'>Available</dd>
				{/if}
			{/if}
			
			{if (!empty($options['pricing_detail']))}
			<dt class="pricing_detail">{__ 'Pricing Detail:'} </dt>
			<dd class="data">{$options['pricing_detail']}</dd>
			{/if}
			
			
			
			{if (!empty($options['help_required']))}
			<dt class="help_required">{__ 'Help:'} </dt>
			<dd class="data">{$options['help_required']}</dd>
			{/if}
			
			{if (!empty($options['start_date']))}
			<?php
				$timestamp = strtotime($options['start_date']);
				$startDate = date("j M Y", $timestamp);
				$options['start_date'] = $startDate;
			?>
			<dt class="start_date">{__ 'Start Date:'} </dt>
			<dd class="data">{$options['start_date']}</dd>
			{/if}
			
			{if (!empty($options['end_date']))}
			<?php
				$timestamp = strtotime($options['end_date']);
				$endDate = date("j M Y", $timestamp);
				$options['end_date'] = $endDate;
			?>
			<dt class="end_date">{__ 'End Date:'} </dt>
			<dd class="data">{$options['end_date']}</dd>
			{/if}
			
			{if (!empty($options['duration']))}
			<dt class="duration">{__ 'Duration:'} </dt>
			<dd class="data">{$options['duration']}</dd>
			{/if}
			
			{if (!empty($options['limit_discount']))}
			<dt class="end_date">{__ 'Limit:'} </dt>
			<dd class="data" style='color:red;'>Discount/Offer has limited uses.</dd>
			{/if}
			
			{if (!empty($options['limit_discount']) && !empty($options['per_month_uses']))}
			<dt class="per_month">{__ 'Per Month Uses:'}</dt>
			<dd class="data" style='color:#f00;'>This Discount/Offer may only be used ({$options['per_month_uses']}) times in a one month period.</dd>
						
			{/if}
			
			{if (!empty($options['gardening']))}
			<dt class="gardening">{__ 'Gardening:'}</dt>
			<dd class="data">{$options['gardening']}</dd>
			{/if}
			
			{if (!empty($options['other']))}
			<dt class="gardening">{__ 'Other:'}</dt>
			<dd class="data">{$options['other']}</dd>
			{/if}
			
			{if (!empty($options['booking_link']))}
			<dt class="booking_link">&nbsp;&nbsp;&nbsp;&nbsp;</dt>
			<?php
				$append = "";
				$bookingLink = str_replace(array('http://','https://'),'',$options['booking_link']);
				if( strpos("http","____".$bookingLink) || strpos("https","____".$bookingLink)){
					;//do nothing
				}else{
					$append = "http://";
				}
			?>
			<dd class="data"><a href='http://{$bookingLink}' target='_blank' style='color:red'>Click Here To Book</a></dd>
			{/if}

			<dt class="post_id">{__ 'Post ID:'} </dt>
			<dd class="data"><a href='{$postLink}'>{$postid}</a></dd>
		</dl>
		{/if}

		{if 
			(!empty($options['hoursMonday'])) || 
			(!empty($options['hoursTuesday'])) || 
			(!empty($options['hoursWednesday'])) || 
			(!empty($options['hoursThursday'])) || 
			(!empty($options['hoursFriday'])) || 
			(!empty($options['hoursSaturday'])) || 
			(!empty($options['hoursSunday']))
		}
		<dl class="item-hours" style="padding:0 0 20px 0;">

			<dt class="title"><h4 style="margin:0 0 10px 30px;">{__ 'Opening Hours'}</h4></dt>

			{if (!empty($options['hoursMonday']))}
		    <dt class="day">{__ 'Monday:'}</dt>
		    <dd class="data">{!$options['hoursMonday']}</dd>
		    {/if}

		    {if (!empty($options['hoursTuesday']))}
		    <dt class="day">{__ 'Tuesday:'}</dt>
		    <dd class="data">{!$options['hoursTuesday']}</dd>
		    {/if}

		    {if (!empty($options['hoursWednesday']))}
		    <dt class="day">{__ 'Wednesday:'}</dt>
		    <dd class="data">{!$options['hoursWednesday']}</dd>
		    {/if}

		    {if (!empty($options['hoursThursday']))}
		    <dt class="day">{__ 'Thursday:'}</dt>
		    <dd class="data">{!$options['hoursThursday']}</dd>
		    {/if}

		    {if (!empty($options['hoursFriday']))}
		    <dt class="day">{__ 'Friday:'}</dt>
		    <dd class="data">{!$options['hoursFriday']}</dd>
		    {/if}

		    {if (!empty($options['hoursSaturday']))}
		    <dt class="day">{__ 'Saturday:'}</dt>
		    <dd class="data">{!$options['hoursSaturday']}</dd>
		    {/if}

		    {if (!empty($options['hoursSunday']))}
		    <dt class="day">{__ 'Sunday:'}</dt>
		    <dd class="data">{!$options['hoursSunday']}</dd>
		    {/if}

		</dl>
		{/if}

	</div>



	{if isset($options['gpsLatitude'], $options['gpsLongitude']) && !(empty($options['gpsLatitude']) && empty($options['gpsLongitude']))}
	<div class="item-map clearfix">
	</div>
	{/if}


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
		(!empty($options['restaurant'])) ||
		(!empty($options['kiosk'])) ||
		(!empty($options['internetAccess'])) ||
		(!empty($options['swimmingPool'])) ||
		(!empty($options['gamesRoom'])) ||
		(!empty($options['generatorsPermitted'])) ||
		(!empty($options['generatorsNotPermitted'])) ||
		(!empty($options['showGrounds'])) ||
		(!empty($options['nationalParknForestry'])) ||
		(!empty($options['pubs'])) ||
		(!empty($options['sharedWithTrucks']))
	}
	<hr>

	<!--*****************************************************************************************-->
	<div class="clearfix">
		<div class="item-hours"><h4>{__ 'Features'}</h4></div>
		<div style="border-bottom: 1px dotted #CFCFCF;margin-bottom:25px;"></div>
			<div class="category-subcategories clearfix">
					<ul class="subcategories">							
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
							27	=>	array('offer Offers Apply', ''),
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
							foreach($featuresArr as $key => $featureArr){
								$imageExtension = ($key < 30) ? 'jpg' : 'png';
								$imageExtension = 'png';
								list($campgroundFeature,$campgroundText) = $featureArr;
								$additionalText = '';
								$additionalVar = "{$campgroundFeature}_addnl";
								if(!empty($options[$additionalVar])){
									$additionalText = $options[$additionalVar];
								}								
								if(!empty($options[$campgroundFeature])){
						?>
<li class="category" style="margin-bottom:10px;">
	<div class="category-wrap-table">
		<div class="category-wrap-row">
			<div class="icon">
				<img src='{content_url()}/uploads/sites/3/2014/04/facility_icons/111x111/jpg/<?php echo $campgroundFeature.".".$imageExtension;?>' title = '<?php echo $additionalText;?>' alt = '<?php echo $additionalText;?>' width="35px"  height="35px" />
			</div>
			<div class="description" style="padding-left:8px;">
				<b><?php echo $campgroundText;?></b>
				<br/><?php echo $additionalText;?>
			</div>
		</div><!-- category-wrap-row 21-->
	</div><!-- category-wrap-table 21-->							
</li>
						<?php
								}
							}
						?>
					</ul>
			
			</div>

			<!--
		
			<div class="clearfix">
				{if (!empty($options['views']))}
					
						<div class="day">
							<img src='{content_url()}/uploads/sites/3/2014/04/facility_icons/30x30/views.png' title = '{__ 'Campground Has Views'}' alt = '{__ 'Campground Has Views'}'  />
						</div>
						<p>
							<B>{__ 'Campground Has Views'}</B><br/>
							{if (!empty($options['views_addnl']))}
								{!$options['views_addnl']}
							{/if}
						</p>
					
				{/if}
			</div>-->
	</div>
	{/if}		
	<!--*****************************************************************************************-->
	
	<hr>

	{if isset($options['gpsLatitude'], $options['gpsLongitude']) && !(empty($options['gpsLatitude']) && empty($options['gpsLongitude']))}
	<input type="hidden" name="directionsLat"  id="directionsLat" value="{$options['gpsLatitude']}"/>
	<input type="hidden" name="directionsLong" id="directionsLong" value="{$options['gpsLongitude']}"/>

	<div id="dir-directions">
		<div id="mapcontainer" class="clearfix"></div>
		<div id="map-directions" class="clearfix" style="display:none"></div>
	</div>
	{/if}

	{if (!empty($options['alternativeContent']))}
	<div class="item-alternative-content">
		{!do_shortcode($options['alternativeContent'])}
	</div>
	{/if}

</article><!-- /#post-{$post->id} -->

<div>
   <h3>Related Listings</h3>
</div>
<div style="padding-bottom: 30px;">
<?php echo do_shortcode('[bibblio style="bib--row-4 bib--font-trebuchet bib--size-16 bib--title-only bib--split bib--shine bib--white-label bib--wide" query_string_params=e30=]'); ?>
</div>
{ifset $themeOptions->rating->enableRating}
	{!getAitRatingElement($post->id)}
{/ifset}

{include comments-dir.php, closeable => (isset($themeOptions->general->closeComments)) ? true : false, defaultState => $themeOptions->general->defaultPosition}

{ifset $themeOptions->advertising->showBox4}
<div id="advertising-box-4" class="advertising-box">
    {!$themeOptions->advertising->box4Content}
</div>

{/ifset}

{/block}
<script>
	
</script>