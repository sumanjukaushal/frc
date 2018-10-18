<?php
/*
 * AIT WordPress Theme
 *
 * Copyright (c) 2013, Affinity Information Technology, s.r.o. (http://ait-themes.com)
 */
 // Notification of New Item Listing
// --------------------------------------------------
add_filter( 'wpseo_use_page_analysis', '__return_false' ); //By Glen
// ==================================================
// Enables theme custom post types, widgets, etc...
// --------------------------------------------------
$aitThemeCustomTypes = array('dir-item' => 32,'grid-portfolio' => 33);
$aitThemeWidgets = array('post', 'flickr', 'submenu', 'twitter', 'directory-login');
$aitEditorShortcodes = array('custom', 'columns', 'images', 'posts', 'buttons', 'boxesFrames', 'lists', 'notifications', 'modal', 'social', 'video', 'gMaps', 'gChart', 'language', 'tabs', 'gridgallery', 'econtent');
$aitThemeShortcodes = array('boxesFrames' => 2, 'buttons' => 1, 'columns'=> 1, 'custom'=> 1, 'images'=> 1, 'lists'=> 1, 'modal'=> 1, 'notifications'=> 1, 'portfolio'=> 1, 'posts'=> 1, 'sitemap'=> 1, 'social'=> 1, 'video'=> 1, 'language'=> 1, 'gMaps'=> 1, 'gChart'=> 1, 'tabs'=> 1, 'gridgallery'=> 1, 'econtent' => 1, 'directoryRegister' => 1);

// use pretty photo modal windows shortcode
$GLOBALS['aitUsePrettyModalSortcode'] = true;

// ==================================================
// Loads AIT WordPress Framework
// --------------------------------------------------
require dirname(__FILE__) . '/AIT/ait-bootstrap.php';

// ==================================================
// Loads Theme's text translations
// --------------------------------------------------
load_theme_textdomain('ait', get_template_directory() . '/languages');

// ==================================================
// Metaboxes settings for Posts and Pages
// --------------------------------------------------
$pageOptions = array(
	'header' => new WPAlchemy_MetaBox(array(
		'id' => '_ait_header_options',
		'title' => __('Header', 'ait-admin'),
		'types' => array('page','post'),
		'context' => 'normal',
		'priority' => 'core',
		'config' => dirname(__FILE__) . '/conf/page-header.neon'
	))
);

// ==================================================
// Theme's scripts and styles
// --------------------------------------------------
function aitAdminEnqueueScriptsAndStyles()
{
	$mapLanguage = get_locale();
	aitAddScripts(array(
		'ait-googlemaps-api' => array(
									  //'file' => 'https://maps.google.com/maps/api/js?key=AIzaSyC62AaIu5cD1nwSCmyO4-33o3DjkFCH4KE&sensor=false&amp;language='.$mapLanguage,
									  'file' => 'https://maps.google.com/maps/api/js?key=AIzaSyBL0QWiORKMYd585E4qvcsHcAR1R7wmdiY&sensor=false&amp;language='.$mapLanguage,
									  'deps' => array('jquery')
									  ),
		'ait-jquery-gmap3'   => array('file' => THEME_JS_URL . '/libs/gmap3.min.js', 'deps' => array('jquery', 'ait-googlemaps-api')),
	));
}
add_action('admin_enqueue_scripts', 'aitAdminEnqueueScriptsAndStyles');

function aitEnqueueScriptsAndStyles()
{

	$mapLanguage = get_locale();
	// just shortcuts
	$s = THEME_CSS_URL;
	$j = THEME_JS_URL;

	aitAddStyles(array(
		'ait-jquery-prettyPhoto'  => array('file' => "$s/prettyPhoto.css"),
		'ait-jquery-fancybox'     => array('file' => "$s/fancybox/jquery.fancybox-1.3.4.css"),
		'ait-jquery-hover-zoom'   => array('file' => "$s/hoverZoom.css"),
		'ait-jquery-fancycheckbox'=> array('file' => "$s/jquery.fancycheckbox.min.css"),
		'jquery-ui-css'           => array('file' => "$s/jquery-ui-1.10.1.custom.min.css"),
		'bootstrap-css'           => array('file' => "$s/bootstrap-3.3.2.min.css"), //rasa
		'bootstrap-multiselect-css' => array('file' => "$s/bootstrap-multiselect.css"), //rasa
	));

	aitAddScripts(array(
		'jquery-ui-tabs'              => true,
		'jquery-ui-accordion'         => true,
		'jquery-ui-autocomplete'      => true,
		'jquery-ui-slider'            => true,
		'ait-jquery-fancycheckbox'    => array('file' => "$j/libs/jquery.fancycheckbox.min.js", 'deps' => array('jquery')),
		'ait-jquery-html5placeholder' => array('file' => "$j/libs/jquery.html5-placeholder-shim.js", 'deps' => array('jquery')),
		
		//'ait-googlemaps-api'          => array('file' => 'https://maps.google.com/maps/api/js?key=AIzaSyC62AaIu5cD1nwSCmyO4-33o3DjkFCH4KE&sensor=false&amp;language='.$mapLanguage, 'deps' => array('jquery')),
		
		'ait-googlemaps-api'          => array('file' => 'https://maps.google.com/maps/api/js?key=AIzaSyBL0QWiORKMYd585E4qvcsHcAR1R7wmdiY&sensor=false&amp;language='.$mapLanguage, 'deps' => array('jquery')),
		
		'ait-jquery-gmap3-label'      => array('file' => "$j/libs/gmap3.infobox.js", 'deps' => array('jquery')),
		'ait-jquery-gmap3'            => array('file' => "$j/libs/gmap3.min.js", 'deps' => array('jquery')),
		'ait-jquery-infieldlabel'     => array('file' => "$j/libs/jquery.infieldlabel.js", 'deps' => array('jquery')),
		'ait-jquery-prettyPhoto'      => array('file' => "$j/libs/jquery.prettyPhoto.js", 'deps' => array('jquery')),
		'ait-jquery-fancybox'         => array('file' => "$j/libs/jquery.fancybox-1.3.4.js", 'deps' => array('jquery')),
		'ait-jquery-easing'           => array('file' => "$j/libs/jquery.easing-1.3.min.js", 'deps' => array('jquery')),
		'ait-jquery-nicescroll'       => array('file' => "$j/libs/jquery.nicescroll.min.js", 'deps' => array('jquery')),
		'ait-jquery-quicksand'        => array('file' => "$j/libs/jquery.quicksand.js", 'deps' => array('jquery')),
		'ait-jquery-hover-zoom'       => array('file' => "$j/libs/hover.zoom.js", 'deps' => array('jquery')),
		'ait-jquery-finished-typing'  => array('file' => "$j/libs/jquery.finishedTyping.js", 'deps' => array('jquery')),
		'ait-spin-ajax-loader'        => array('file' => "$j/libs/spin.min.js"),
		'ait-modernizr-touch'         => array('file' => "$j/libs/modernizr.touch.js"),
		'ait-gridgallery'             => array('file' => "$j/gridgallery.js", 'deps' => array('jquery')),
		'ait-rating'                  => array('file' => "$j/rating.js", 'deps' => array('jquery')),
		'ait-script'                  => array('file' => "$j/script.js", 'deps' => array('jquery')),
		'ait-bootstrap-script'        => array('file' => "$j/bootstrap-3.3.2.min.js", 'deps' => array('jquery')), //rasa
		'ait-bootstrap-multiselect'   => array('file' => "$j/bootstrap-multiselect.js", 'deps' => array('jquery')), //rasa
		'ait-jquery-ui'               => array('file' => "$j/libs/jquery_ui.js", 'deps' => array('jquery')), //rasa
		'ait-block-ui'                => array('file' => "$j/libs/jquery.blockUI.js", 'deps' => array('jquery')), //rasa
		'ait-handlebars'              => array('file' => "$j/libs/handlebars-v3.0.3.js", 'deps' => array('jquery')), //rasa
		'ait-typeahead'               => array('file' => "$j/libs/typeahead.js", 'deps' => array('jquery')), //rasa
		//'ait-addresspicker'           => array('file' => "$j/libs/typeahead-addresspicker.js", 'deps' => array('jquery')), //rasa
		//'ait-map'                     => array('file' => "$j/libs/map.js", 'deps' => array('jquery')), //rasa
		'ait-jquery-ui'               => array('file' => "$j/libs/jquery-ui.min.js", 'deps' => array('jquery')), //rasa
	));
	wp_localize_script( 'ait-script', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajaxnonce' => wp_create_nonce('ajax-nonce') ) );
	wp_localize_script( 'ait-script', 'RasuAjax', array( 'prefurl' => content_url( 'locations/save_search.php' ), 'ajaxnonce' => wp_create_nonce('ajax-nonce') ) );
	wp_localize_script( 'ait-script', 'RasuLoadSaved', array( 'prefurl' => content_url( 'locations/load_saved_search.php' ), 'ajaxnonce' => wp_create_nonce('ajax-nonce') ) );
}
add_action('wp_enqueue_scripts', 'aitEnqueueScriptsAndStyles');


// ==================================================
// Theme setup
// --------------------------------------------------
function aitThemeSetup()
{
	add_editor_style();

	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');

	register_nav_menu('primary-menu', __('Primary Menu', 'ait-admin'));
	register_nav_menu('footer-menu', __('Footer Menu', 'ait-admin'));
}
add_action('after_setup_theme', 'aitThemeSetup');

// ==================================================
// Plugins
// --------------------------------------------------
aitAddPlugins(array(
	array(
		'name'     => 'Contact Form 7',
		'slug'     => 'contact-form-7',
		'required' => false, // only recommended
	),
	array(
		'name'     => 'Revolution Slider',
		'slug'     => 'revslider',
		'version'  => '4.6.0',
		'required' => false,
		'source'   => dirname(__FILE__) . '/plugins/revslider.zip', // pre-packed
	),
));

function aitWidgetsAreasInit()
{
	aitRegisterWidgetAreas(array(
		'sidebar-1'      => array('name' => __('Main Sidebar', 'ait-admin')),
		'sidebar-home'   => array('name' => __('Homepage Sidebar', 'ait-admin')),
		'sidebar-item'   => array('name' => __('Items Sidebar', 'ait-admin')),
		'footer-widgets' => array(
			'name' => __('Footer Widget Area', 'ait-admin'),
			'before_widget' => '<div id="%1$s" class="box widget-container %2$s"><div class="box-wrapper">',
			'after_widget' => "</div></div>",
			'before_title' => '<div class="title-border-bottom"><div class="title-border-top"><div class="title-decoration"></div><h2 class="widget-title">',
			'after_title' => '</h2></div></div>',
		),
	), array(
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => "</aside>",
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	));
}
add_action('widgets_init', 'aitWidgetsAreasInit');



// ==================================================
// Loads Direcotory Theme's specific functions
// --------------------------------------------------
require_once dirname(__FILE__) . '/functions/load.php';

// ==================================================
// Some helper functions and filters for theme
// --------------------------------------------------
function default_menu(){
	wp_nav_menu(array('menu' => 'Main Menu', 'fallback_cb' => 'default_page_menu', 'container' => 'nav', 'container_class' => 'mainmenu', 'menu_class' => 'menu clear'));
}

function default_page_menu(){
	echo '<nav class="mainmenu">';
	wp_page_menu(array('menu_class' => 'menu clear'));
	echo '</nav>';
}

function default_footer_menu(){
	wp_nav_menu(array('menu' => 'Main Menu', 'container' => 'nav', 'container_class' => 'footer-menu', 'menu_class' => 'menu clear', 'depth' => 1));
}

remove_action('wp_head', 'wp_generator'); // do not show generator meta element

add_filter('widget_title', 'do_shortcode');
add_filter('widget_text', 'do_shortcode'); // do shortcode in text widget

if(!isset($content_width)) $content_width = 1000;

// ==================================================
// Custom styling of admin interface of Revolution slider
// --------------------------------------------------
if(isset($revSliderVersion)){
	// Some custom styles for slides in Revolution Slider admin
	function aitRevSliderAdminStyles(){ wp_enqueue_style('ait-revolution-slider-admin-css', THEME_URL . '/design/admin-plugins/revslider.css'); }
	function aitRevSliderAdminScripts(){ wp_enqueue_script('ait-revolution-slider-admin-js', THEME_URL . '/design/admin-plugins/revslider.js'); }

	add_action('admin_print_styles', 'aitRevSliderAdminStyles');
	add_action('admin_print_scripts', 'aitRevSliderAdminScripts');
}

function my_custom_admin_head() {
	global $current_user;
	$ipAddress = $_SERVER['REMOTE_ADDR'];
	$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
	$currentIP = '27.255.200.201';
	if(true){//$ipAddress == '124.253.182.132'
        //print_r($ipAddress);
		if(is_plugin_active($wishlistPlugin)){
			$levels = WLMAPI::GetUserLevels($current_user->ID);
            //echo "<pre>";print_r($levels);echo "</pre>";
            //echo 'User level: ' . $current_user->user_level . "\n";
			
            if($current_user->user_level != 10){
				//if($ipAddress == '124.253.144.125'){
				//	//$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'author' ) );
				//	echo "<pre>144.125:";print_r($levels);echo "</pre>";die;
				//}
				//update role of user to author only if user is not admin and have signed in for 
				//any of 6 listing other other than free listing.
				//echo "<pre>";print_r($current_user->roles);echo "</pre>";
				//echo $current_user->ID;
				$currentURL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ;
				
				if(
					$current_user->roles[0] == 'subscriber' &&
						(
							in_array('Businesses Listings',$levels) || //directory_2
							in_array('Low Cost Camps',$levels) || //directory_3 Changed to Camp Grounds
							in_array('Campgrounds',$levels) || //directory_3
							in_array('House Sitting',$levels) || //directory_4
							in_array('Park Overs',$levels) || //directory_5
							in_array('Help Outs',$levels) || //directory_6
							in_array('$25 +',$levels) || //directory_7	changed to Caravan Parks
                            in_array('25plus',$levels) || //directory_7
							in_array('Caravan Parks',$levels) //directory_7
						)
				){
					if(in_array('Businesses Listings',$levels)){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_2' ) );
					}elseif(in_array('Campgrounds',$levels) || in_array('Low Cost Camps',$levels)){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_3' ) );
					}elseif(in_array('House Sitting',$levels)){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_4' ) );
					}elseif(in_array('Park Overs',$levels)){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_5' ) );//flaw here
						if($ipAddress == $currentIP){
							echo "<pre>Park Overs:=>";print_r($levels);echo "</pre>";
						}
					}elseif(in_array('Help Outs',$levels)){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_6' ) );//flaw here
						if(!$result){
							if($ipAddress == $currentIP){
								echo "<pre>Help Out 0:=>";print_r($levels);echo "</pre>";
							}
							$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'author' ) );
							if($result){
								$result1 = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_6' ) );
								if($ipAddress == '124.253.144.125'){
									echo "<pre>Help Out 1:=>";print_r($levels);echo "</pre>";
								}
							}
						}
						if($ipAddress == $currentIP){
							$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_6' ) );
							echo "<pre>Help Out 2:=>";print_r($levels);echo "</pre>";
						}
					}elseif(
							in_array('Caravan Parks',$levels) ||
							in_array('$25 +',$levels) ||
							in_array('25 +',$levels) ||
							in_array('$25+',$levels)
							){
						$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'directory_7' ) );
						if(!$result){
							$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'author' ) );
						}
					}
					if($ipAddress == $currentIP){
					   //$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'author' ) );
					   echo "<pre>144.125:";print_r($levels);echo "</pre>";die;
					}
					if($result){
						$pos = strpos($currentURL, 'freerangecamping.com.au');
						if($pos){
							$currentURL = "https://www.freerangecamping.com.au/directory/wp-admin/profile.php";
						}
						header("Location: $currentURL");
					}
					//$result = wp_update_user( array( 'ID' => $current_user->ID, 'role' => 'author' ) );
				}
            }else{
               ;//echo "<pre>";print_r($current_user->roles);echo "</pre>";
            }
        }
    }
}




add_action( 'admin_head', 'my_custom_admin_head' );
// ==================================================
// Update processes for 2.17 version
// --------------------------------------------------
if ((!get_option( 'directory_2.17_update_process' )) && isset($GLOBALS['aitThemeOptions']->rating->enableRating)) {

	// calculate and save ratings for all items to db
	$args = array(
		'ep_integrate' => true, //elastic search
		'post_type' => 'ait-dir-item',
		'post_status' => 'any',
		'nopaging' => true
	);
	$items = new WP_Query($args);
	foreach ($items->posts as $item) {
		aitSaveRatingMeanToDB($item->ID,null);
	}

	update_option( 'directory_2.17_update_process', 'yes' );
}


add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}


add_filter('woocommerce_prevent_admin_access', '__return_false');

function add_js_2_footer() {
    echo $js = <<<CART_JS
    <script>
     jQuery( document ).ready(function() {
       jQuery('ul li a').on("click",function(){
          //jQuery.blockUI({ message: "Please Wait....Loading the Page.<br/><img src='https://www.freerangecamping.com.au/wp-content/themes/directory2-child/design/img/loading_spinner_small.gif'/>" });
       });
       jQuery('.ubermenu-target-title.ubermenu-target-text').on("click",function(){
        //jQuery.blockUI({ message: "Please Wait....Loading the Page.<br/><img src='https://www.freerangecamping.com.au/wp-content/themes/directory2-child/design/img/loading_spinner_small.gif'/>" });
    });
    jQuery('.vc_tta-icon fa.fa-map-marker').on("click",function(){
        //jQuery.blockUI({ message: "Please Wait....Loading the Page.<br/><img src='https://www.freerangecamping.com.au/wp-content/themes/directory2-child/design/img/loading_spinner_small.gif'/>" });
    });
});
    </script>
CART_JS;
}
add_action( 'wp_footer', 'add_js_2_footer', 100 );

	function woocommerce_forget_password() {
		global $wp;
		$current_url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$nounce1 = $nounce = wp_nonce_field( 'lost_password' );
		$nounce1 = str_replace("_wpnonce", "_wpnonce_rasu", $nounce1);
		
		$successMessage = "";
		if(isset($_REQUEST['reset-link-sent']) && !empty($_REQUEST['reset-link-sent'])){
			$successMessage = "jQuery('#woocommerce_message').show();";
		}
		echo $customJS = <<<CUSTOM_JS
        <style>.ui-dialog .ui-dialog-title {padding-left: 0.4em;padding-right: 0.4em;text-align: center;}</style>
        <script>
        function forget_pwd(ajax){
			var wc_reset_password = jQuery('#wc_reset_password').val();
			var wpnonce = jQuery('#_wpnonce_rasu').val();
			var reset_pwd = jQuery('#reset_pwd').val();
			var user_login = jQuery('#user_login').val();
			var targeturl = "$current_url"+ '?wc_reset_password=true'+"&wpnonce="+wpnonce+"&reset_pwd="+reset_pwd+"&user_login="+user_login;
			jQuery.blockUI({ message: "Please wait while we save your search preferences.<br/><img src='https://www.freerangecamping.com.au/directory/wp-content/themes/directory/design/img/loading2.gif'/>" });
			
			jQuery.ajax({
				type: 'post',
				url: targeturl,
				beforeSend: function(xhr) {
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				},
				success: function(response) {
					jQuery.unblockUI();
					alert(response);
				}
			});
        }
        
        jQuery(function () {
			jQuery("#forget_dialog").dialog({
				modal: true,
				autoOpen: false,
				resizable: true,
				title: "Lost your password?",
				minWidth:900,
				minHeight:500,
				width: 900,
				height: 500,
				buttons: {
						"Close": function() {
							jQuery( this ).dialog( "close" );
						}
				}
        });
        jQuery( ".selector" ).dialog( "option", "minWidth", 900 );
        jQuery("#lost_password").click(function () {
            jQuery("#dialog-form").dialog({width: 1500,height:600});
            jQuery('#forget_dialog').dialog('open');
        });
    });
    {$successMessage}
        </script>
CUSTOM_JS;

        
	echo $dialogHTML = <<<DIALOG_HTML
		<div id="forget_dialog" style="display: none" align = "center" style="width: 800px; height: 600px">
			<form method="post">
				<table>
					<tr><td colspan='2'>You will receive a link to create a new password via email.</td></tr>
					<tr>
						<td>Username or email</td>
						<td><input type="text" name="user_login" id="user_login"></td>
					</tr>
					<tr><td></td><td><input id='reset_pwd1' type="submit" value="Reset Password"></td></tr>
					<input type="hidden" name="wc_reset_password" value="true" id="wc_reset_password">
					{$nounce}{$nounce1}
				</table>
			</form>
		</div>
        <script>
        jQuery('#reset_pwd').click(function(){
                forget_pwd(true);
        });
        </script>
DIALOG_HTML;
}
add_action( 'wp_footer', 'woocommerce_forget_password' );
function add_product_to_cart() {
		  if ( ! is_admin() ) {
			   if(true){//$_SERVER["REMOTE_ADDR"] == "27.255.218.39"
					global $woocommerce;
					$curPageURL = frc_curPageURL();
					if(strpos($curPageURL,"get-premium-membership")){
						//echo $curPageURL;
						 $product_id = 28901;
						 $found = false;
						 //check if product already in cart
						 $cartArr = @$woocommerce->cart->get_cart();
						 //echo "<pre>";print_r($cartArr);echo "</pre>";
						 if ( is_array($cartArr) && sizeof( $cartArr ) > 0 ) {
							foreach ( $cartArr as $cart_item_key => $values ) {
								$_product = $values['data'];
								if ( $_product->id == $product_id )$found = true;
							}
							// if product not found, add it
							if ( ! $found )$woocommerce->cart->add_to_cart( $product_id );
						 }else{
							// if no products in cart, add it
							@$woocommerce->cart->add_to_cart( $product_id );
						 }
					}
			   }
		  }
	 }
	 
	 if(!function_exists('frc_curPageURL')){
		  function frc_curPageURL() {
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
	 }
add_action( 'init', 'add_product_to_cart' );
// Remove Password Strong Enforcement Woocommerce
add_action( 'wp_print_scripts', 'DisableStrongPW', 100 );

function DisableStrongPW() {
    if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
        wp_dequeue_script( 'wc-password-strength-meter' );
    }
}
if (!function_exists('wp_rocket_exclude_from_cache_busting')) {
    function wp_rocket_exclude_from_cache_busting( $excluded_files = array() ) {

	 /**
	 * This is a sample file URL, define your own!
	 * Duplicate below line as needed to exclude multiple files
	 */
	$excluded_files[] = 'https://www.freerangecamping.com.au/directory/wp-json/api/v1/user';
	$excluded_files[] = '/wp-json/api/v1/user';
	$excluded_files[] = 'https://www.freerangecamping.com.au/directory/wp-json/api/v1/user/frc_login';
	$excluded_files[] = '/wp-json/api/v1/user/frc_login';

	return $excluded_files;
    }
}
add_filter( 'rocket_exclude_cache_busting', 'wp_rocket_exclude_from_cache_busting' );
function extra_user_profile_fields( $user ) {
	global $wpdb;
	$userID = $user->data->ID;
	$tblMeta = $wpdb->prefix."usermeta";
	$query = "SELECT `umeta_id`,`meta_value` FROM `$tblMeta` WHERE `user_id` = $userID AND `meta_key` = 'premium_profile'";
	$resultObj = $wpdb->get_results($query);
	$fieldArr = array();
	if(count($resultObj) >= 1){
		$fieldArr = unserialize($resultObj[0]->meta_value);
	}
	echo "<!-- field arr:\n";print_r($fieldArr);echo "\n-->";
	$uDoB = "";
	if(array_key_exists('15', $fieldArr)){
		if(strpos($fieldArr[15],'-')){
			list($uDay, $uMon,$uYear) = explode("-",$fieldArr[15]);
			$uDoB = implode("-", array($uDay, $uMon, $uYear));
		}
	}
	$suDoB = "";
	if(array_key_exists('11', $fieldArr)){
		if(strpos($fieldArr[11], '-')){
			list($uDay, $uMon,$uYear) = explode("-",$fieldArr[11]);
			$suDoB = implode("-", array($uDay, $uMon, $uYear));
		}
	}
	?>
<h3><?php _e("Extra profile information", "blank"); ?></h3>


<table class="form-table">
    <tr>
        <th><label>Your Date of Birth*</label></th>
        <td><input name="input_15" id="input_1_15" type="text" value="<?php echo $uDoB;?>" class="datepicker" tabindex="8"></td>
    </tr>
	
	<tr>
		<td colspan='3'>
			<table style='width: 450px;'>
				<td><strong>Second user added to the account?*</strong><br/>ie. husband, wife, spouse</td>
				<td><input name="input_8" type="radio" value="Yes" id="choice_1_8_0" tabindex="9" onclick="jQuery('#scnd_user').show(500);" <?php
				if(array_key_exists(8,$fieldArr) && $fieldArr[8] == 'Yes')echo 'checked="checked"';?> /><label id="label_1_8_0">Yes</label></td>
				<td><input name="input_8" type="radio" value="No" id="choice_1_8_1" tabindex="10" onclick="jQuery('#scnd_user').hide(500);" <?php
				if(array_key_exists(8,$fieldArr) && $fieldArr[8] == 'No')echo 'checked="checked"';?> /><label id="label_1_8_1">No</label></td>
			</table>
		</td>
	</tr>
	
	<tr><td colspan='3'>
		<div id='scnd_user' style="margin: 0; padding: 0;">
		<table>
			<tr><th colspan='2'><h4>Second User's Details</h4></th></tr>
			<tr><td colspan='2'>Second User's Name</td></tr>
			<tr>
				<td><input type="text" name="input_9_3" id="input_1_9_3" value="<?php echo $fieldArr['9.3'];?>" aria-label="First name" tabindex="12" class="regular-text"><br/><span class="description">First</span></td>
				<td><input type="text" name="input_9_6" id="input_1_9_6" value="<?php echo $fieldArr['9.6'];?>" aria-label="Last name" tabindex="14" class="regular-text"><br/><span class="description">Last</span></td>
			</tr>
			<tr><th colspan='2'><label>Second User's Date of Birth</label></th></tr>
			<tr><td><input name="input_11" id="input_1_11" type="text" value="<?php echo $suDoB;?>" class="datepicker" tabindex="16"></td></tr>
		</table>
		</div>
	</td></tr>
	
	
	<tr><td colspan='3'>In order to help us help you, and provide better members services and products, we would love to know a little bit more about the type of camping you prefer. Please place check boxes in the below questions.</td></tr>
	<tr><th colspan='3'>Q.  What type of camping mode do you currently use?</label><br/><span class="description">(you can choose more than one)</span></th></tr>
	<tr><td colspan='3'>
		<input name="input_4_1" type="checkbox" value="Caravan" id="choice_1_4_1" <?php if(array_key_exists('4.1', $fieldArr)) echo 'checked="checked"'; ?> tabindex="18">
		<label for="choice_1_4_1" id="label_1_4_1">Caravan</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_2" type="checkbox" value="Campervan" id="choice_1_4_2" <?php if(array_key_exists('4.2', $fieldArr)) echo 'checked="checked"'; ?> tabindex="19">
		<label for="choice_1_4_2" id="label_1_4_2">Campervan</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_3" type="checkbox" value="Motorhome" id="choice_1_4_3" <?php if(array_key_exists('4.3', $fieldArr)) echo 'checked="checked"'; ?> tabindex="20">
		<label for="choice_1_4_3" id="label_1_4_3">Motorhome</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_4" type="checkbox" value="Camper Tailer" id="choice_1_4_4" <?php if(array_key_exists('4.4', $fieldArr)) echo 'checked="checked"'; ?> tabindex="21">
		<label for="choice_1_4_4" id="label_1_4_4">Camper Tailer</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_5" type="checkbox" value="Tent" id="choice_1_4_5" <?php if(array_key_exists('4.5', $fieldArr)) echo 'checked="checked"'; ?> tabindex="22">
		<label for="choice_1_4_5" id="label_1_4_5">Tent</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_6" type="checkbox" value="Backpacker" id="choice_1_4_6" <?php if(array_key_exists('4.6', $fieldArr)) echo 'checked="checked"'; ?> tabindex="23">
		<label for="choice_1_4_6" id="label_1_4_6">Backpacker</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_4_7" type="checkbox" value="Other" id="choice_1_4_7" <?php if(array_key_exists('4.7', $fieldArr)) echo 'checked="checked"'; ?>  tabindex="24">
		<label for="choice_1_4_7" id="label_1_4_7">Other</label>
	</td></tr>
	
	<tr><th colspan="3">Q. How often do you Camp/Travel?</th></tr>
	<tr><td colspan='3'>
		<input name="input_6.1" type="checkbox" value="Full Time – i.e. all year" <?php if(array_key_exists('6.1', $fieldArr)) echo 'checked="checked"'; ?> id="choice_1_6_1" tabindex="26">
		<label for="choice_1_6_1" id="label_1_6_1">Full Time – i.e. all year</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_6.2" type="checkbox" value="Part Time – 2 -6 month of the year" id="choice_1_6_2" <?php if(array_key_exists('6.2', $fieldArr)) echo 'checked="checked"'; ?> tabindex="27">
		<label for="choice_1_6_2" id="label_1_6_2">Part Time – 2 -6 month of the year</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_6.3" type="checkbox" value="Causal – Week Ends or short trips away" id="choice_1_6_3" <?php if(array_key_exists('6.3', $fieldArr)) echo 'checked="checked"'; ?> tabindex="28">
		<label for="choice_1_6_3" id="label_1_6_3">Causal – Week Ends or short trips away</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_6.4" type="checkbox" value="Holidays – Only during school or public holidays" id="choice_1_6_4" <?php if(array_key_exists('6.4', $fieldArr)) echo 'checked="checked"'; ?> tabindex="29">
		<label for="choice_1_6_4" id="label_1_6_4">Holidays – Only during school or public holidays</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_6.5" type="checkbox" value="Other" id="choice_1_6_5" <?php if(array_key_exists('6.5', $fieldArr)) echo 'checked="checked"'; ?> tabindex="30">
		<label for="choice_1_6_5" id="label_1_6_5">Other</label>
	</td></tr>
	
	<tr><th colspan="3"><label>Q. In order of preference what is your preferred type of Campground?</label><br/><span class="description">(you can choose more than one)</span></th></tr>
	<tr><td colspan='3'>
		<input name="input_7.1" type="checkbox" value="Caravan Parks" id="choice_1_7_1" <?php if(array_key_exists('7.1', $fieldArr)) echo 'checked="checked"'; ?> tabindex="32">
		<label for="choice_1_7_1" id="label_1_7_1">Caravan Parks</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_7.2" type="checkbox" value="Private Campgrounds" id="choice_1_7_2" <?php if(array_key_exists('7.2', $fieldArr)) echo 'checked="checked"'; ?> tabindex="33">
		<label for="choice_1_7_2" id="label_1_7_2">Private Campgrounds</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_7.3" type="checkbox" value="Show Grounds" id="choice_1_7_3" <?php if(array_key_exists('7.3', $fieldArr)) echo 'checked="checked"'; ?>  tabindex="34">
		<label for="choice_1_7_3" id="label_1_7_3">Show Grounds</label>
	</td></tr>
	
	<tr><td colspan='3'>
		<input name="input_7.4" type="checkbox" value="National Parks" id="choice_1_7_4" <?php if(array_key_exists('7.4', $fieldArr)) echo 'checked="checked"'; ?> tabindex="35">
		<label for="choice_1_7_4" id="label_1_7_4">National Parks</label>
	</td></tr>
	
	<tr><td colspan="3">
		<input name="input_7.5" type="checkbox" value="Free Camps" id="choice_1_7_5" <?php if(array_key_exists('7.5', $fieldArr)) echo 'checked="checked"'; ?> tabindex="36">
		<label for="choice_1_7_5" id="label_1_7_5">Free Camps</label>
	</td></tr>
	
	<tr><td colspan="3">
		<input name="input_7.6" type="checkbox" value="Help Outs" id="choice_1_7_6" <?php if(array_key_exists('7.6', $fieldArr)) echo 'checked="checked"'; ?> tabindex="37">
		<label for="choice_1_7_6" id="label_1_7_6">Help Outs</label>
	</td></tr>
	
	<tr><td colspan="3">
		<input name="input_7.7" type="checkbox" value="House Sitting" id="choice_1_7_7" <?php if(array_key_exists('7.7', $fieldArr)) echo 'checked="checked"'; ?> tabindex="38">
		<label for="choice_1_7_7" id="label_1_7_7">House Sitting</label>
	</td></tr>
</table>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	<?php
		if(!empty($suDoB)){echo "jQuery(\"#input_1_11\").val('$suDoB');";}
		if(!empty($uDoB)){echo "jQuery(\"#input_1_15\").val('$uDoB');";}
	?>
	var myRadio = jQuery('input[name=input_8]');
	var checkedValue = myRadio.filter(':checked').val();
	if (checkedValue == 'No') {
        jQuery('#scnd_user').hide(10);
    }
});
</script>
<?php }
add_action( 'show_user_profile', 'extra_user_profile_fields'); 
function save_extra_user_profile_fields( $user_id ) {
	global $wpdb;
	$tblMeta = $wpdb->prefix."usermeta";
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
	$fieldArr = array();
	$query = "SELECT `umeta_id`,`meta_value` FROM `$tblMeta` WHERE `user_id` = $user_id AND `meta_key` = 'premium_profile'";
	$resultObj = $wpdb->get_results($query);
	if(count($resultObj) >= 1){
		$fieldArr = unserialize($resultObj[0]->meta_value);
		$umetaID = $resultObj[0]->umeta_id;
	}
	
	//------------------------------
	extract($_POST);
	if(!empty($input_15)){
		$fieldArr[15] = $input_15;		//Your DoB;
	}
	$fieldArr[8] = $input_8;		//Second user added to the account?;
	$scName = "input_9_3";
	$fieldArr['9.3'] = $$scName;	//second User first name
	$scLName = "input_9_6";
	$fieldArr['9.6'] = $$scLName;	//second User first name
	if(!empty($input_11)){
		$fieldArr[11] = $input_11;		//Second User's Date of Birth
	}
	
	if(isset($input_4_1)) $fieldArr['4.1'] = $input_4_1; else unset($fieldArr['4.1']);
	if(isset($input_4_2)) $fieldArr['4.2'] = $input_4_2; else unset($fieldArr['4.2']);
	if(isset($input_4_3)) $fieldArr['4.3'] = $input_4_3; else unset($fieldArr['4.3']);
	if(isset($input_4_4)) $fieldArr['4.4'] = $input_4_4; else unset($fieldArr['4.4']);
	if(isset($input_4_5)) $fieldArr['4.5'] = $input_4_5; else unset($fieldArr['4.5']);
	if(isset($input_4_6)) $fieldArr['4.6'] = $input_4_6; else unset($fieldArr['4.6']);
	if(isset($input_4_7)) $fieldArr['4.7'] = $input_4_7; else unset($fieldArr['4.7']);
	
	if(isset($input_6_1)) $fieldArr['6.1'] = $input_6_1; else unset($fieldArr['6.1']);
	if(isset($input_6_2)) $fieldArr['6.2'] = $input_6_2; else unset($fieldArr['6.2']);
	if(isset($input_6_3)) $fieldArr['6.3'] = $input_6_3; else unset($fieldArr['6.3']);
	if(isset($input_6_4)) $fieldArr['6.4'] = $input_6_4; else unset($fieldArr['6.4']);
	if(isset($input_6_5)) $fieldArr['6.5'] = $input_6_5; else unset($fieldArr['6.5']);
	
	if(isset($input_7_1)) $fieldArr['7.1'] = $input_7_1; else unset($fieldArr['7.1']);
	if(isset($input_7_2)) $fieldArr['7.2'] = $input_7_2; else unset($fieldArr['7.2']);
	if(isset($input_7_3)) $fieldArr['7.3'] = $input_7_3; else unset($fieldArr['7.3']);
	if(isset($input_7_4)) $fieldArr['7.4'] = $input_7_4; else unset($fieldArr['7.4']);
	if(isset($input_7_5)) $fieldArr['7.5'] = $input_7_5; else unset($fieldArr['7.5']);
	if(isset($input_7_6)) $fieldArr['7.6'] = $input_7_6; else unset($fieldArr['7.6']);
	if(isset($input_7_7)) $fieldArr['7.7'] = $input_7_7; else unset($fieldArr['7.7']);
	
	if(!empty($umetaID)){
		$result = $wpdb->update( $tblMeta, array( 'meta_value' => serialize($fieldArr) ), array( 'umeta_id' => $umetaID ));
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail('kalyanrajiv@gmail.com', "updating user $user_id", json_encode($fieldArr), $headers);
	}
}
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
/*function frc_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	$referrer = $_SERVER['HTTP_REFERER'] ;
	$headers = array('Content-Type: text/html; charset=UTF-8');
	wp_mail('kalyanrajiv@gmail.com', "redirecting user ", $referrer."--".$user, $headers);
	//return $redirect_to;
}

add_filter( 'login_redirect', 'frc_login_redirect', 1, 3 );*/

add_filter('wp_login','frc_successfull_login', 1, 3);
function frc_successfull_login(){
	$referrer = $_SERVER['HTTP_REFERER'] ;
	$headers = array('Content-Type: text/html; charset=UTF-8');
	return;
	$urlPos = strpos($referrer, 'register-to-submit-a-listing');
	//wp_mail('kalyanrajiv@gmail.com', "redirecting user 123-$urlPos", $referrer."--".$user, $headers);
	if($urlPos){
		remove_all_actions( 'wp_login');
		//add_action( 'wp_login', 'frc_redirect', 1, 3 );
		wp_mail('kalyanrajiv@gmail.com', "redirecting user 456", $referrer."--".$user, $headers);
		$redirectURL = WP_SITEURL.'/submit-a-listing/?frc=776';
		/*echo "<script>
		alert($redirectURL);
		window.location = '$redirectURL';
		</script>";*/
		//wp_redirect( $redirectURL, 302 );
		apply_filters( 'wp_redirect', $redirectURL, 302 );
		header("Location:$redirectURL");
		die;
	}
	//wp_redirect( "https://www.google.com", $status = 302 );
}
function frc_redirect(){
	$redirectURL = WP_SITEURL.'/submit-a-listing/?frc=789';
	header("Location:$redirectURL");
	die;
}

add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {
    //remove generator meta tag
    remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

    //first check that woo exists to prevent fatal errors
    if ( function_exists( 'is_woocommerce' ) ) {
        //dequeue scripts and styles
        if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
            wp_dequeue_style( 'woocommerce_frontend_styles' );
            wp_dequeue_style( 'woocommerce_fancybox_styles' );
            wp_dequeue_style( 'woocommerce_chosen_styles' );
            wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
            wp_dequeue_script( 'wc_price_slider' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-add-to-cart' );
            wp_dequeue_script( 'wc-cart-fragments' );
            wp_dequeue_script( 'wc-checkout' );
            wp_dequeue_script( 'wc-add-to-cart-variation' );
            wp_dequeue_script( 'wc-single-product' );
            wp_dequeue_script( 'wc-cart' );
            wp_dequeue_script( 'wc-chosen' );
            wp_dequeue_script( 'woocommerce' );
            wp_dequeue_script( 'prettyPhoto' );
            wp_dequeue_script( 'prettyPhoto-init' );
            wp_dequeue_script( 'jquery-blockui' );
            wp_dequeue_script( 'jquery-placeholder' );
            wp_dequeue_script( 'fancybox' );
            wp_dequeue_script( 'jqueryui' );
        }
    }
 }

function change_cat_meta_postbox_css(){
   ?>
   <style type="text/css">
   .wp-tab-panel, .categorydiv div.tabs-panel,
   .customlinkdiv div.tabs-panel,
   .posttypediv div.tabs-panel,
   .taxonomydiv div.tabs-panel {
       min-height: 42px;
       max-height: 650px;/* change this to own wishes */
       overflow: auto;
       padding: 0 0.9em;
       border: solid 1px #dfdfdf;
       background-color: #fdfdfd;
   }
   </style><?php
}
add_action('admin_head', 'change_cat_meta_postbox_css');

function frc_items_revisions( $args, $post_type ) {
	if ( 'ait-dir-item' === $post_type ) { //CPT-Custom Post Type
		// https://codex.wordpress.org/Function_Reference/register_post_type
		$args['supports'] = array( 
		                            'title', 
		                            'editor', 
		                            'revisions',
		                            'author', 
		                            //'custom-fields',
		                            'excerpt',
		                            'thumbnail',
		                            );
		return $args;
	}
	return $args;
}
add_filter( 'register_post_type_args', 'frc_items_revisions',1,2);
//registered_taxonomy | register_post_type_args | muplugins_loaded | wp_enqueue_scripts

/**
 * Remove password strength check.
 */
 add_filter( 'password_hint', function( $hint )
{
  return __( 'Hint: You can use the recommended password above, or change it to your own preferred password.' );
} );
?>