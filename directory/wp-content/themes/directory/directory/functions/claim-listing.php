<?php

// register custom type
add_action('init', 'aitClaimListingInit');

// Table & admin changes
add_action('admin_head', 'aitRemoveAddClaim');
add_filter('manage_ait-claim_posts_columns', 'aitClaimChangeColumns');
add_action('manage_posts_custom_column', 'aitClaimCustomColumns', 10, 2);

// Adding claims
add_action('wp_ajax_nopriv_ait_new_claim', 'aitAddNewClaim');
add_action('wp_ajax_ait_new_claim', 'aitAddNewClaim');

// Approving claim = register new user
// add_action('admin_init','aitApproveClaim');
add_action('admin_notices','aitShowApprovedClaimNotice');

function aitClaimListingInit() {
	$args = array( 
		'public' => true,
		'labels' => array(
			'name'			=> 'Claims',
			'singular_name' => 'Claim',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new claim',
			'edit_item'		=> 'Edit claim',
			'new_item'		=> 'New claim',
			'not_found'		=> 'No claims found',
			'not_found_in_trash' => 'No claims found in Trash',
			'menu_name'		=> 'Claims',
		),
		'menu_position' => 51,
		// 'menu_icon' => THEME_IMG_URL . '/rating_star_admin.png',
		'capability_type' => 'ait-claim',
		'map_meta_cap' => true,
		'supports'		=> array('')
	);
	register_post_type( 'ait-claim', $args );

	// add capability
	$capability_type = 'ait-claim';
	$capabilitiesAdmin = array(
		"edit_{$capability_type}" => true,
		"read_{$capability_type}" => true,
		"delete_{$capability_type}" => true,
		"edit_{$capability_type}s" => true,
		"edit_others_{$capability_type}s" => true,
		"publish_{$capability_type}s" => true,
		"read_private_{$capability_type}s" => true,
		"delete_{$capability_type}s" => true,
		"delete_private_{$capability_type}s" => true,
		"delete_published_{$capability_type}s" => true,
		"delete_others_{$capability_type}s" => true,
		"edit_private_{$capability_type}s" => true,
		"edit_published_{$capability_type}s" => true
	);

	// set admin capability
	$adminRole = get_role( 'administrator' );
	foreach ($capabilitiesAdmin as $key => $value) {
		$adminRole->add_cap( $key );
	}
}

// hide add new rating button
function aitRemoveAddClaim() {
	global $pagenow;
	remove_submenu_page('edit.php?post_type=ait-claim','post-new.php?post_type=ait-claim');
	if(isset($pagenow) && ($pagenow == 'edit.php') && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-claim')){
		echo '<style type="text/css">
				a.add-new-h2 { display: none !important; }
				ul.subsubsub li.publish { display: none !important; }
			</style>';
	}
}

function aitAddNewClaim() {

	if( defined('AIT_SERVER') ) { return 0; }

	if( (!empty($_POST['itemId'])) && (!empty($_POST['username'])) && (!empty($_POST['name'])) && (!empty($_POST['email'])) ){

		// check username and email if exist
		if (username_exists( $_POST['username'] )) {
			_e("This username is already registered. Please choose another one.","ait");
			exit();
		}
		if (email_exists( $_POST['email'] )) {
			_e("This email is already registered, please choose another one.","ait");
			exit();
		}

		global $aitThemeOptions;

		// Check for nonce security
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			_e( 'Bad nonce', 'ait' );
			exit();
		}

		$claim = array(
			// 'post_author'    => $data->author,
			'post_title'     => $_POST['username'],
			'post_content'   => $_POST['message'],
			// 'post_status'    => $postStatus,
			'post_type'      => 'ait-claim',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed'
		);
		$claimId = wp_insert_post( $claim );
		if($claimId == 0) return 0;

		update_post_meta( $claimId, 'item_id' , $_POST['itemId'] );

		update_post_meta( $claimId, 'username' , $_POST['username'] );
		update_post_meta( $claimId, 'name' , $_POST['name'] );
		update_post_meta( $claimId, 'email' , $_POST['email'] );
		update_post_meta( $claimId, 'number' , $_POST['number'] );

		update_post_meta( $claimId, 'status' , 'new' );

		// send email to admin
		if (isset($aitThemeOptions->directory->claimAdminEmail)) {
			
			$to = get_option('admin_email');
			$subject = strip_tags($aitThemeOptions->directory->claimAdminEmailSubject);

			$postLink = get_permalink( intval($_POST['itemId']) );
			$post = get_post( intval($_POST['itemId']) );

			$bodyHtml = $aitThemeOptions->directory->claimAdminEmailBody;
			$bodyHtml = str_replace('[item]', '<a href="'.$postLink.'" target="_blank">'.$post->post_title.'</a>', $bodyHtml);
			$bodyHtml = str_replace('[name]', $_POST['name'], $bodyHtml);
			$bodyHtml = str_replace('[username]', $_POST['username'], $bodyHtml);
			$bodyHtml = str_replace('[email]', $_POST['email'], $bodyHtml);
			$bodyHtml = str_replace('[phone]', $_POST['number'], $bodyHtml);
			$bodyHtml = str_replace('[message]', $_POST['message'], $bodyHtml);
			$bodyHtml = str_replace('[link]', admin_url('/edit.php?post_type=ait-claim'), $bodyHtml);

			$headers = 'From: ' . $aitThemeOptions->directory->claimAdminEmailFrom . "\r\n";
			add_filter( 'wp_mail_content_type', 'aitSetHtmlMail' );
			wp_mail($to, $subject, $bodyHtml, $headers );
			remove_filter( 'wp_mail_content_type', 'aitSetHtmlMail' );

		}

		echo "success";

	} else {
		_e("Please fill out inputs","ait");
	}
	exit();

}

function aitSetHtmlMail() {
	return 'text/html';
}

function aitClaimChangeColumns($cols)	{
	$cols = array(
		'cb'			=> '<input type="checkbox" />',
		'claim-name'    => __( 'Name', 'ait'),
		'claim-username'=> __( 'Username', 'ait'),
		'claim-email'   => __( 'Email', 'ait'),
		'claim-phone'   => __( 'Phone number', 'ait'),
		'claim-item-id' => __( 'Claim for', 'ait'),
		'content'		=> __( 'Message', 'ait'),
		'date'   		=> __( 'Created', 'ait'),
		'claim-status'  => __( 'Status', 'ait')
	);
	return $cols;
}

function aitClaimCustomColumns($column, $claimId) {
	switch($column){
		case "claim-name":
			$val = get_post_meta( $claimId, 'name', true );
			echo $val;
			break;
		case "claim-username":
			$val = get_post_meta( $claimId, 'username', true );
			echo $val;
			break;
		case "claim-email":
			$val = get_post_meta( $claimId, 'email', true );
			echo $val;
			break;
		case "claim-phone":
			$val = get_post_meta( $claimId, 'number', true );
			echo $val;
			break;
		case "claim-email":
			$val = get_post_meta( $claimId, 'email', true );
			echo $val;
			break;
		case "claim-item-id":
			$postId = get_post_meta( $claimId, 'item_id', true );
			$postLink = get_permalink( $postId );
			$post = get_post( $postId );
			echo '<a href="'.$postLink.'" target="_blank">'.$post->post_title.'</a>';
			break;
		case "claim-status":
			$val = get_post_meta( $claimId, 'status', true );
			if ($val == 'approved') {
				echo "<div style='color:green;'>".__("Approved","ait")."</div>";
			} else {
				echo "<a href='".admin_url('edit.php?post_type=ait-claim&claim-approve=do&claim-id='.$claimId)."' class='button'>".__("Approve","ait")."</a>";
			}
			break;
	}
}

if (current_user_can( 'manage_options' ) && isset($_GET['claim-approve']) && !empty($_GET['claim-id'])) {
	aitApproveClaim(intval($_GET['claim-id']));
}

function aitApproveClaim($claimId) {

	global $claimMessages, $aitThemeOptions;

	$username = get_post_meta( $claimId, 'username', true );
	$email = get_post_meta( $claimId, 'email', true );
	$itemId = intval(get_post_meta( $claimId, 'item_id', true ));

	// register
	$userId = aitRegisterDirectoryUser($username,$email);

	if(is_wp_error( $userId )){
		$claimMessages = $userId->get_error_message();
	} else {
		
		// set role
		$role = (isset($aitThemeOptions->directory->claimListingRole)) ? $aitThemeOptions->directory->claimListingRole : "directory_1";
		$user = get_userdata( $userId );
		$user->set_role( $role );
		// write activation time
		aitDirWriteActivationTime( $userId, $role );

		// change item author
		$item = get_post($itemId,'ARRAY_A');
		$itemUpdated = $item;
		$itemUpdated['post_author'] = $userId;

		$chStatus = wp_insert_post( $itemUpdated, true );
		if(is_wp_error( $chStatus )){
			$claimMessages = $chStatus->get_error_message();
		} else {
			// change status
			update_post_meta($claimId, 'status', 'approved');
			// show message
			$claimMessages = __('Claim was approved! New user was registered and assignated to Item. Email with generated password was sent.','ait');
		}
		
	}

}

function aitShowApprovedClaimNotice() {
	global $claimMessages;
	if(isset($claimMessages)){
		echo '<div class="updated"><p>'.$claimMessages.'</p></div>';
	}
}