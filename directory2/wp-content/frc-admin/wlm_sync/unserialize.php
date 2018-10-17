<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/wlm_sync/unserialize.php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    
    $args = array(
    	'post_type' => 'ait-dir-item',
    	'tax_query' => array(
    		'relation' => 'AND',
    		array(
    			'taxonomy' => 'ait-dir-item-location',
    			//'field'    => 'term_id',
    			//'terms'    => array( 1564 ),
				'field'    => 'slug',
    			'terms'    => array( 'nsw' ),
    			'include_children' => false
    		),
    		array(
    			'taxonomy' => 'ait-dir-item-category',
    			//'field'    => 'term_id',
    			//'terms'    => array( 30 ),
				'field'    => 'slug',
    			'terms'    => array( 'rest-areas' ),
    			'include_children' => false
    		),
    	),
    );
    /*$itemsQuery = new WP_Query($args);
    $query = $itemsQuery->query( $args );
    print_r($itemsQuery);die;*/
	$timeNow = gmdate("Y-m-d H:i:s", mktime());
    $query = "SELECT  gwr_posts.ID, gwr_posts.post_title, gwr_posts.post_modified_gmt FROM gwr_posts  LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id)  LEFT JOIN gwr_term_relationships AS tt1 ON (gwr_posts.ID = tt1.object_id) WHERE (gwr_term_relationships.term_taxonomy_id IN (1594) AND tt1.term_taxonomy_id IN (30)) AND gwr_posts.post_type = 'ait-dir-item' AND (gwr_posts.post_status = 'publish' OR gwr_posts.post_status = 'private') GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_modified_gmt ASC LIMIT 0, 50";
    $limitRS = $wpdb->get_results($query);
	foreach($limitRS as $sngLt){
	    $postID = $sngLt->ID;
		$postMetas = get_post_meta($postID,'_ait-dir-item');
		if(count($postMetas) >= 0){
    		$postMeta = $postMetas[0];
    		$postMeta['telephone'] = $postMeta['email'] = '';
    		update_post_meta( $sngLt->ID, '_ait-dir-item', $postMeta );
    //echo "<pre>Post ID:$sngLt->ID\n";print_r($postMeta);echo "</pre>;die;*/
    		echo "<br/>".$updateQry = "UPDATE `gwr_posts` SET `post_modified_gmt` = '$timeNow' WHERE `ID` = $postID";
    		$wpdb->query($updateQry);
		}
	}
	die;
    print_r($limitRS);die;
    print_r(get_the_term_list( 34274));
    $terms = wp_get_post_terms( 34274, 'ait-dir-item-location');
    echo $wpdb->last_query;
     $taxonomy_names = get_post_taxonomies( 34274);
   print_r( $taxonomy_names );
    print_r($terms);
	echo "GMT Time: ".$timeNow = gmdate("Y-m-d H:i:s", mktime())."\n<br/>";
	die;
	//SELECT * FROM `gwr_postmeta` WHERE `meta_value` like '%Free 6 Month membership to KUI Parks%'
	$query = "SELECT ID FROM `gwr_posts` WHERE `post_title` LIKE '%kui parks%' AND `post_status`='publish' AND `post_type` = 'ait-dir-item'"; //31544
	$limitRS = $wpdb->get_results($query);
	
	$itemArr = array();
	foreach($limitRS as $limitObj){
		$postID = $limitObj->ID;
		$siteIDs[] = $postID;
		$subQry = "SELECT `meta_value`, `meta_id` FROM `gwr_postmeta` WHERE `meta_key` = '_ait-dir-item' AND `post_id` = '$postID'";
		$metaRS = $wpdb->get_results($subQry);
		if(count($metaRS) >= 1){
			$postMetaObj = $metaRS[0];
			$postMetaArr = unserialize($postMetaObj->meta_value);//meta_id
			//print_r($postMetaArr);
			$postMetaArr['offer_detail'] = 'Free 6 Month membership to KUI Parks';
			$postMetaArr['offer'] = array('enable' => 'enable');
			$collectSerialized = serialize($postMetaArr);
			$uMetaID = $postMetaObj->meta_id;
			$collectionResult = $wpdb->update(
										'gwr_postmeta',
										array('meta_value' => $collectSerialized),
										array('meta_id' => $uMetaID),
										array('%s'),
										array('%d')
									   );
			echo $wpdb->last_query;
			//print_r($postMetaArr);
		}
	}
	print_r($siteIDs);
	die;
	echo "---".$aitThemeOptions->members->role1Time;//$aitThemeOptions->members->role1Period
	print_r($aitThemeOptions->members);
	//print_r($aitThemeOptions);
	$formID = 1;
	$leadID = 3;
	$premMembers = array(13337, 14659, 15605, 22840, 36686, 42559);
	//43625 - rajjukaura
	$allUserRoles = get_editable_roles();
	$allUserRoleArr = array();
	foreach($allUserRoles as $roleID => $userRole){
		$allUserRoleArr[$roleID] = array($roleID, $userRole['name']);
	}
	print_r($allUserRoleArr);
	$wpUser = new WP_User( 43625 );
	$wpUser->add_role('directory_7');$wpUser->add_role('admin_role');
	//$user->set_role('subscriber');
	$user_info = get_userdata( 43625 );
	echo "43625 roles\n";
	print_r($roles = $user_info->roles);
	$wpUser->add_role();//set_role
	echo "\n".$query = "SELECT * FROM `gwr_usermeta` WHERE `user_id` = 43625 AND `meta_key` = 'gwr_capabilities'";
	$capObj = $wpdb->get_results($query);
	if(count($capObj) >= 1){
		print_r(unserialize($capObj[0]->meta_value));
	}
	die;
	 /*
	1398983986 => Free Memebrship [Classified, directory, Shop, ] 1
	1399015844 => House Sitting [Classified, directory, Shop, ] 2
	1444431834 => classifieds [Classified, directory, Shop, ] 3
	1414469692 => Free Listings [Classified, directory, Shop, ] 4
	1399011470 => Campgrounds [Classified, directory, Shop, ] 5
	1399015901 => Park Overs [Classified, directory, Shop, ] 6
	1399015933 => Help Outs [Classified, directory, Shop, ] 7
	1399015963 => Business Listings [Classified, directory, Shop, ] 8
	1415928787 => Caravan Parks [Classified, directory, Shop,  ] 9
	1406427506 => Premium Membership [Classified, directory, Shop, ] 10
	1496600364 => Foundation Membership [Classified - Missing, directory-1496600364, shop-1496600595, Premium-1496600500] 11
	*/
	//1. From Directory to all other installations (shop, classified, premium)
	echo "\n".$query = "SELECT * FROM `gwr_wlm_userlevels` WHERE (`user_id` IN(".implode(",", $premMembers).")) ORDER BY `user_id` DESC";
	//1406427506,1398983986
	//36686 : 1444431834 class, 1415928787 caravan, 1414469692 Free listing, 1406427506 prem, 1399015963 Busin, 1399015933 Help, 1399015901 Park
	$resultObj = $wpdb->get_results($query);
	echo "<pre>";
	print_r($resultObj);
	die;
	/*$userMeta = get_user_meta(15605, 'classified_capabilities');
	print_r($userMeta);
	$query = "SELECT `umeta_id`,`meta_value` FROM `gwr_usermeta` WHERE `user_id` = 15605 AND `meta_key` = 'premium_profile'";
    $resultObj = $wpdb->get_results($query);
	if(count($resultObj) >= 1){
		$fieldArr = unserialize($resultObj[0]->meta_value);
		$umetaID = $resultObj[0]->umeta_id;
		$fieldArr['5.6'] = 'Plus';
		$result = $wpdb->update( 'gwr_usermeta', array( 'meta_value' => serialize($fieldArr) ), array( 'umeta_id' => $umetaID ));
		//, array( '%s' ), array( '%d' ) 
		echo "Update qury:".$updateQry = $wpdb->last_query;
		if ( false === $result ) {
			return false;
		}
	}else{
		$fieldArr = array('5.3' => 'Dollar25');
		$dataArr = array( 'user_id' => 15605, 'meta_key' => 'premium_profile', 'meta_value' => serialize($fieldArr));
		$result = $wpdb->insert( 'gwr_usermeta', $dataArr, array( '%d', '%s', '%s' ) );
	}
	die;*/
	/*
	select * from `gwr_usermeta` where `user_id` = 15605 gwr_capabilities
	$wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
    if(is_plugin_active($wishlistPlugin)){
        $wlmKey = "68a6a021c09130bf2e9b1e6f27d0d0ab";
        $api = new wlmapiclass('https://www.freerangecamping.co.nz/directory7/', $wlmKey);
        $api->return_format = 'php'; // <- value can also be xml or json
	}
	foreach($premMembers as $premMember){
		$user_meta = get_userdata($premMember);
		$user_roles = $user_meta->roles;
		echo "\nRole of user with id: $premMember:\n";
		print_r($user_roles);
		$userLevels = WLMAPI::GetUserLevels($premMember);
		print_r($userLevels);
	}
	
	[3] => dollar_25plus [label: Your Username] Hidden
    [16] => dollar_25plus@freerangecamping.co.nz [label: Your Email] Hidden
    
		5 - Name
    [5.3] => Dollar25 [label:First]
    [5.4] => [label: Middle]
    [5.6] => Plus	[label:Last]
    
    [15] => 1974-07-30	[Your Date of Birth]
    [8] => Yes [Will there be a second user added to the account?]
    
		9 - Second User's Name
    [9.3] => Dhruv [First]
    [9.4] => Middle [Middle]
    [9.6] => Kaushal [Last]
    
    [11] => 2001-06-06	[Second User's Date of Birth]
    [13.1] => next [label: Continue]
    [17] => camp_rasu [label: Other] [adminLabel] => how often-other
    
		4 - Q.  What type of camping mode do you currently use?
    [4.1] => Caravan
    [4.2] => Campervan
    [4.3] => Motorhome
    [4.4] => Camper Tailer
    [4.5] => Tent
    [4.6] => Backpacker
    [4.7] => Other
    
    [18] => camping_mode_rasu [label: Other]
    
        6 - Q. How often do you Camp/Travel
    [6.1] => Full Time – i.e. all year
    [6.2] => Part Time – 2 -6 month of the year
    [6.3] => Causal – Week Ends or short trips away
    [6.4] => Holidays – Only during school or public holidays
    [6.5] => Other
    
		7 - Q. In order of preference what is your preferred type of Campground? {checkboxes}
    [7.1] => Caravan Parks
    [7.2] => Private Campgrounds
    [7.3] => Show Grounds
    [7.4] => National Parks
    [7.5] => Free Camps
    [7.6] => Help Outs
    [7.7] => House Sitting
	*/
    $query = "SELECT * FROM `wp_rg_form_meta` WHERE `form_id` = $formID";
    $resultObj = $wpdb->get_results($query);
	if(count($resultObj) >= 1){
		$metaObj = $resultObj[0];
		$formArr = json_decode($metaObj->display_meta); //confirmations, notifications
		$formTitle = $formArr->title;
		$formFields = $formArr->fields;
		print_r($formFields);die;
		echo $leadQry = "SELECT * FROM `wp_rg_lead_detail` WHERE `form_id` = $formID AND `lead_id` = $leadID";
		$leadObjs = $wpdb->get_results($leadQry);
		$fieldArr = array();
		if(count($leadObjs) >= 1){
			foreach($leadObjs as $leadObj){
				$fieldArr[$leadObj->field_number] = $leadObj->value;
			}
			print_r($fieldArr);
		}
	}
	echo "Permissions updated for following users: gbroderos, michael, keitht, soniafrc, Stacie"
?>