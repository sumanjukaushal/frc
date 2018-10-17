<?php

function default_comments_on( $data ) {
    if( $data['post_type'] == 'page' || $data['post_type'] == 'ait-item' ) {
        $data['comment_status'] = 1;
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'default_comments_on', '', 2 );

function pc_permute($items, $perms = array( )) {
	//return $items;
	if (empty($items)) { 
		print join(' ', $perms) . "\n";
	}else{
		for ($i = count($items) - 1; $i >= 0; --$i) {
			$newitems = $items;
			$newperms = $perms;
			list($foo) = array_splice($newitems, $i, 1);
			array_unshift($newperms, $foo);
			pc_permute($newitems, $newperms);
		}
	}
}
add_action('wp_ajax_save_filter', 'save_search_filters');
add_action('wp_ajax_nopriv_save_filter', 'save_search_filters');
add_action('wp_ajax_loc_suggestions', 'get_loc_suggestion');
add_action('wp_ajax_nopriv_loc_suggestions', 'get_loc_suggestion');
add_action('wp_ajax_fetch_search_filters', 'fetch_filters');
add_action('wp_ajax_nopriv_fetch_search_filters', 'fetch_filters');
add_action('wp_ajax_prefetch_locations', 'prefetch_locations_fn');
add_action('wp_ajax_nopriv_prefetch_locations', 'prefetch_locations_fn');

function get_loc_suggestion(){
	global $wpdb;
	//Reference: https://www.youtube.com/watch?v=R7OK-TtNuEc
	if(isset($_REQUEST['s'])) $location = trim($_REQUEST['s']);
	$locationArr = $searchArray = array();
    $search = str_replace(",", " ", strtolower($_REQUEST['s']));
    $splittedArr = explode(" ", $search);

    ob_start();
    pc_permute($splittedArr);
    $permutation = ob_get_clean();
    $wordArray = explode("\n", $permutation);
	foreach($wordArray as $value){
		if(empty($value)) continue;
		$searchArray[] = "LOWER(`rasu_locations`.`output`) like '%".str_replace(" ","%",$value)."%'";
     }
    $searchStr = implode(' OR ', $searchArray);
    if(!empty($searchStr)){
		$query = "SELECT `output` FROM `rasu_locations` WHERE $searchStr";
		$dbObjs = $wpdb->get_results($query);
		if(count($dbObjs) >= 1) foreach($dbObjs as $dbObj) $locationArr[] = array('location' => $dbObj->output);
	}
	echo json_encode($locationArr);
	die;
}

function prefetch_locations_fn(){
	global $wpdb;
	//check_ajax_referer('loc-sugg', 'security');
	$locationArr = array();
	$query = "SELECT `output` FROM `rasu_locations`";
	$dbObjs = $wpdb->get_results($query);
	if(count($dbObjs) >= 1) foreach($dbObjs as $dbObj) $locationArr[] = $dbObj->output;
	echo json_encode($locationArr);die;
	//https://freerangecamping.co.nz/plus/wp-admin/admin-ajax.php?action=prefetch_location&security=a5b6c7b944
}

function save_search_filters(){
	//https://www.youtube.com/watch?v=APaCwk77hCc
	//https://codex.wordpress.org/Function_Reference/check_ajax_referer
	check_ajax_referer('save-filter', 'security');
	global $wpdb;
	if(isset($_REQUEST['action'])){
		$customLocation = $feature = $location = $locRadius = $category = "";
		if(isset($_REQUEST['category']) && !empty($_REQUEST['category'])) $category = implode(",", $_REQUEST['category']);
		if(isset($_REQUEST['loc_radius']) && !empty($_REQUEST['loc_radius'])) $locRadius = (int)$_REQUEST['loc_radius'];
		if(isset($_REQUEST['location']) && !empty($_REQUEST['location'])) $location = implode(",", $_REQUEST['location']);
		if(isset($_REQUEST['feature']) && !empty($_REQUEST['feature'])) $feature = implode(",", $_REQUEST['feature']);
		if(!empty($_REQUEST['search'])) $customLocation = trim($_REQUEST['search']);
		
		$current_user = wp_get_current_user();
		$id = $current_user->ID;
		$selQry = "SELECT * FROM `saved_preferences` WHERE `user_id` = $id";
		$rsltObjs = $wpdb->get_results($selQry);
		if(count($rsltObjs) >= 1){
			$status = $wpdb->update('saved_preferences',
									array(
										  'locations' => $location,
											'custom_location' => $customLocation,
											'radius' => $locRadius,
											'categories' => $category,
											'features' => $feature
										  ), array( 'user_id' => $id));
		}else{
			$wpdb->insert( 'saved_preferences',
									  array(
											'user_id' => $id,
											'locations' => $location,
											'custom_location' => $customLocation,
											'radius' => $locRadius,
											'categories' => $category,
											'features' => $feature
											),
									  array(   '%d', '%s', '%s', '%s', '%s','%s'));
		}
		die;
	}
}

function fetch_filters(){
	global $wpdb;
	check_ajax_referer('FRCLoadFilter', 'security');
	$data = array();
	$current_user = wp_get_current_user();
	if(isset($_REQUEST['action'])){
		if($userID = $current_user->ID){
			$query = "SELECT * FROM `saved_preferences` WHERE `user_id` = $userID ORDER BY `id` DESC LIMIT 1";
			$rsltObjs = $wpdb->get_results($query);
			if(count($rsltObjs) >= 1) $data = $rsltObjs[0];
		}
	}
	header('Content-Type: application/json');
	echo json_encode($data);die;
}

function custom_help() {
    global $post_ID;
    $screen = get_current_screen();
return;
    if( isset($_GET['post_type']) ) $post_type = $_GET['post_type']; else $post_type = get_post_type( $post_ID );

    if( true || $post_type == 'ait-item' ){

		$screen->add_help_tab( array(
			'id' => 'you_custom_id', // unique id for the tab
			'title' => 'Custom Help', // unique visible title for the tab
			'content' => '<h3>Help Title</h3><p>Help content</p>', //actual help text
		));
	
		$screen->add_help_tab( array(
			'id' => 'you_custom_id_2', // unique id for the second tab
			'title' => 'Custom Help 2', // unique visible title for the second tab
			'content' => '<h3>Help Title 2</h3><p>Help content</p>', //actual help text
		));

	}else{
		
	}

}

//add_action('admin_head', 'custom_help');
//add_action( 'admin_head', 'welcome_screen_credits_content' );


function welcome_screen_credits_content() {
  ?>
  <!--
  https://wpsites.net/wordpress-admin/add-content-before-editor-after-title-in-any-admin-edit-screen/ [<=important]
  https://stackoverflow.com/questions/37724384/how-to-make-a-function-wait-until-return-value
  https://medium.freecodecamp.org/javascript-from-callbacks-to-async-await-1cc090ddad99
  
  
  https://nikolaloncar.com/wordpress-add_help_tab-add-context-help-to-custom-admin-plugin-page/
  https://developer.wordpress.org/reference/classes/wp_screen/add_help_tab/
  https://codex.wordpress.org/Class_Reference/WP_Screen/add_help_tab/
  https://codex.wordpress.org/Plugin_API/Action_Reference/admin_head/
  http://www.kvcodes.com/2014/04/create-bootstrap-tabs-in-wordpress-admin/ [Tab structure]
  https://wordpress.stackexchange.com/questions/242372/a-similar-hook-as-wp-head-for-the-admin-area [admin_head, wp_head]
  https://premium.wpmudev.org/blog/tabbed-interface/ [Tab structure <=]
  https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/ [little bit tabs]
  <div class="wrap" style='padding-left:50px;'>

     <ul class="nav nav-tabs">
          <li  class="active"> <a href="#info" data-toggle="tab">Plugin Info</a></li>
          <li><a href="#css" data-toggle="tab">CSS</a></li>
          <li><a href="#js" data-toggle="tab">JS</a></li>
		  <li><a href="#php" data-toggle="tab">PHP Library</a></li>
        </ul>


  </div>--><div id="titlediv">
  
  <?php
}

function woocommerce_forget_password() {
        $current_url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $nounce1 = $nounce = wp_nonce_field( 'lost_password' );
        $nounce1 = str_replace("_wpnonce", "_wpnonce_rasu", $nounce1);
        global $wp;
        $successMessage = "";
        if(isset($_REQUEST['reset-link-sent']) && !empty($_REQUEST['reset-link-sent'])){
                $successMessage = "jQuery('#woocommerce_message').show();";
        }
        //$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
        //echo get_permalink();
    echo $customJS = <<<CUSTOM_JS
        <style>.ui-dialog .ui-dialog-title {padding-left: 0.4em;padding-right: 0.4em;text-align: center;}</style>
        <script>
        function forget_pwd(ajax){
                var wc_reset_password = jQuery('#wc_reset_password').val();
                var wpnonce = jQuery('#_wpnonce_rasu').val();
                var reset_pwd = jQuery('#reset_pwd').val();
                var user_login = jQuery('#user_login').val();
                var targeturl = "$current_url"+ '?wc_reset_password=true'+"&wpnonce="+wpnonce+"&reset_pwd="+reset_pwd+"&user_login="+user_login;
                alert(targeturl);
                jQuery.blockUI({ message: "Please wait while we save your search preferences.<br/><img src='https://www.freerangecamping.co.nz/mobile/wp-content/themes/directory/design/img/loading2.gif'/>" });
                
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
                //jQuery( ".ui-dialog" ).dialog( "option", "minHeight", 500 );
                //jQuery( ".ui-dialog" ).dialog( "option", "minWidth", 900 );
                //  $current_url
            /*
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
            */
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

//ADD PRDOUCT TO CART AUTOMATICALLY

/*
 * Add item to cart on visit
 *For adding woocommerce product in the cart without user's action on landing this page https://www.freerangecamping.com.au/directory/get-premium-membership/
*/
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
add_action( 'init', 'add_product_to_cart' );

if(!function_exists('frc_curPageURL')){
  function frc_curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    }else{
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }
}

add_action( 'wp_print_scripts', 'DisableStrongPW', 100 );
 
function DisableStrongPW() {
    if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
        wp_dequeue_script( 'wc-password-strength-meter' );
    }
}

//for adding revisions for ITEMs
function frc_items_revisions( $args, $post_type ) {
    //define('WP_POST_REVISIONS', 5);
	if ( 'ait-item' === $post_type ) { //CPT-Custom Post Type
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
//for adding revisions for ITEMs
//-----Start code blocks for postmeta revisions --------
function ca_members_fields(){
  $member_fields = get_post_custom();
  return $member_fields;
}
add_filter( '_wp_post_revision_fields','ca_member_fields', 10, 1 );

function ca_member_fields( $fields ) {
  $members_fields	=	ca_members_fields();
  foreach($members_fields as $fieldname=>$members_field){$fields['ct_'.$fieldname] = $members_field;}
  return $fields;
}

function ca_field( $value, $field_name,$post ) {
  $members_fields=ca_members_fields(); 
  foreach($members_fields as $fieldname=>$members_field){ 
    if($field_name==$fieldname){$value= get_metadata( 'post',$post->ID, 'ct_'.$fieldname , true );} 
  } 
  return $value; 
}

function ca_custom_admin_head() {
  $members_fields=ca_members_fields();
  $post = _wp_post_revision_fields($post,true );
  foreach($members_fields as $fieldname => $members_field){
    $revisionFieldname = 'ct_'.$fieldname;
    add_filter( "wp_post_revision_field_{$revisionFieldname}", 'ca_field', 10, 3 ); 
  }
}
add_action( 'admin_head', 'ca_custom_admin_head' );

function ca_save_member_revision( $post_id, $post ) {
  //echo $post_id;die;
  if(wp_is_post_revision( $post_id ) == true){
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
      $parent = get_post( $parent_id );
      if($parent->post_type='ait-item'){
        $members_fields=ca_members_fields();
        foreach($members_fields as $fieldname=>$members_field){      
          $meta = get_post_meta( $parent->ID, $fieldname, true );
          if ( false !== $meta ){ add_metadata( 'post', $post_id, 'ct_'.$fieldname, $meta );}
        }
      }     
    }
  }
}
add_action( 'save_post','ca_save_member_revision',10,2);

function ca_restore_revision( $post_id, $revision_id ) {
  $post = get_post( $post_id );
	$revision = get_post( $revision_id );        
  if($post->post_type='ait-item'){
    $members_fields=ca_members_fields();
    foreach($members_fields as $fieldname=>$members_field){         
      $meta = get_metadata( 'post', $revision->ID, 'ct_'.$fieldname, true );
      if ( false !== $meta ){update_post_meta( $post_id, $fieldname, $meta );}
    }   
  }    
}
add_action( 'wp_restore_post_revision',    'ca_restore_revision', 10, 2 );
//-----End code blocks for postmeta revisions --------
//deleting old revisions
function ca_limit_revisions( $num ) { 
	$num = 5;
	return $num;
}
add_filter( 'wp_revisions_to_keep', 'ca_limit_revisions', 10, 2 );

//----------------------------Rajju's Code-------------------

 add_action( 'transition_post_status', 'wwm_transition_post_status', 10, 3 );

    function wwm_transition_post_status( $new_status, $old_status, $post ) {
		//print_r($post);die;
	if ( 'trash' == $new_status ) {
      // $uid = get_current_user_id();
	   $current_user = wp_get_current_user();
	   $name = $current_user->user_login;
	   $to = "rajjukaura@gmail.com";
 		$subject = 'Move to Trash Item';
		$blogtime = current_time( 'mysql' ); 
 		$body = "User ".$name." have moved ITEM <b>".$post->post_title."</b> to trash at time ".$blogtime;
 		$headers = array('Content-Type: text/html; charset=UTF-8');
 		 
 		wp_mail( $to, $subject, $body, $headers );
      //somehow or another log the $uid with the $post->ID
     }
  }
  
  add_action( 'before_delete_post', 'my_func' );
	function my_func( $postid ){
	//echo $postid;die;
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM gwr_posts WHERE ID = {$postid}");
		
			 // print_r($result);die;
		    foreach($results as $result){
				$dataArr = array(
									'ID' =>  $result->ID,
									'post_author' => $result->post_author,
									'post_date' => $result->post_date,
									'post_date_gmt' => $result->post_date_gmt,
									'post_content' => $result->post_content,
									'post_title' => $result->post_title,
									'post_excerpt' => $result->post_excerpt,
									'post_status' => $result->post_status,
									'comment_status' => $result->comment_status,
									'ping_status' => $result->ping_status,
									'post_password' => $result->post_password,
									'post_name' => $result->post_name,
									'to_ping' => $result->to_ping,
									'pinged' => $result->pinged,
									'post_modified' => $result->post_modified,
									'post_modified_gmt' => $result->post_modified_gmt,
									'post_content_filtered' => $result->post_content_filtered,
									'post_parent' => $result->post_parent,
									'guid' => $result->guid,
									'menu_order' => $result->menu_order,
									'post_type' => $result->post_type,
									'post_mime_type' => $result->post_mime_type,
									'comment_count' => $result->comment_count
								 );
					// print_r($dataArr);die;
					if ($result->post_type == 'ait-item' || $result->post_type == 'ait_dir_itme'){
						$wpdb->insert( 'trash_posts',
												$dataArr,
												array(
													'%d', '%d', '%s', '%s','%s',
													'%s', '%s', '%s','%s','%s',
													'%s', '%s', '%s','%s','%s',
													'%s', '%s','%s','%s', '%d',
													'%s', '%d'
												)
									  );
					}
			}
			$post_meta_results = $wpdb->get_results("SELECT * FROM gwr_postmeta WHERE post_id = {$postid}");
			foreach($post_meta_results as $post_meta_result){
			   $postmetaArr = array(
						   'meta_id' =>  $post_meta_result->meta_id,
						   'post_id' => $post_meta_result->post_id,
						   'meta_key' => $post_meta_result->meta_key,
						   'meta_value' => $post_meta_result->meta_value,
						   'modified' => $post_meta_result->modified 
			  
							);
			   // print_r($dataArr);die;
			  // if ($post_meta_result->meta_key == '_ait-item_item-data'){
					$wpdb->insert( 'frc_trash_meta',
								   $postmetaArr,
								   array(
									   '%d', '%d', '%s', '%s','%d'
								  )
						 );
			 //  }
			   
			}
		 
		 
	
		// My custom stuff for deleting my custom post type here
	}
  
  add_action('delete_post', 'custom_delete_function',10, 3);
  
    function custom_delete_function($post ) {
		//   print_r($post);die;
			global $wpdb;
			$current_user = wp_get_current_user();
			$name = $current_user->user_login;
			$curenttime = current_time( 'mysql' );
			 
			$trashresults = $wpdb->get_results("SELECT * FROM trash_posts WHERE ID = {$post}");
			foreach ($trashresults as $trashresult ){
				$body = "User ".$name." have permanently deleted ITEM <b>".$trashresult->post_title."</b> at time ".$curenttime;
				if ($trashresult->post_type == 'ait-item' || $trashresult->post_type == 'ait_dir_itme'){
					$insertqry = $wpdb->insert('frc_trash_log',
											array('message' => $body),
											 array('%s')
								);
					$to = "rajjukaura@gmail.com";
					$subject = 'Permanently Delete item';
					$headers = array('Content-Type: text/html; charset=UTF-8');
					 
					wp_mail( $to, $subject, $body, $headers );
				}
			}
			
 
      
    }
	
	
 
 
//-----------------------------------------------------------

