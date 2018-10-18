<?php

/**
 * MULTI-RATING SYSTEM WITH AJAX
 */
add_action('init', 'aitRatingCustomInit');
add_action('admin_head', 'aitRatingRemoveAddButton');
add_filter('pre_get_posts', 'aitRatingTableDontShowOthersRatings');
add_filter('views_edit-ait-rating', 'aitRatingShowCorrectTableNumbers');
add_action('edit_post', 'aitSaveRatingMeanToDB', 10, 2);
add_filter('manage_ait-rating_posts_columns', 'aitRatingChangeColumns');
add_action('manage_posts_custom_column', 'aitRatingCustomColumns', 10, 2);
add_action('admin_head', 'aitRatingStyles');
add_action('add_meta_boxes', 'aitRatingEditShowDetails');
add_action('admin_notices','aitShowApprovedRatingNotice');

add_action('wp_ajax_nopriv_ait_rate_item', 'aitRateItem');
add_action('wp_ajax_ait_rate_item', 'aitRateItem');

function aitRatingCustomInit() {
	$args = array(
		'public' => true,
		'labels' => array(
			'name'			=> 'Ratings',
			'singular_name' => 'Rating',
			'add_new'		=> 'Add new',
			'add_new_item'	=> 'Add new rating',
			'edit_item'		=> 'Edit rating',
			'new_item'		=> 'New rating',
			'not_found'		=> 'No ratings found',
			'not_found_in_trash' => 'No ratings found in Trash',
			'menu_name'		=> 'Ratings',
		),
		'menu_position' => 50,
		'menu_icon' => THEME_IMG_URL . '/rating_star_admin.png',
		'capability_type' => 'ait-rating',
		'map_meta_cap' => true
	);
	register_post_type('ait-rating', $args);

	// add capability
	$capabilityType = 'ait-rating';
	$capabilitiesAdmin = array(
		"edit_{$capabilityType}" => true,
		"read_{$capabilityType}" => true,
		"delete_{$capabilityType}" => true,
		"edit_{$capabilityType}s" => true,
		"edit_others_{$capabilityType}s" => true,
		"publish_{$capabilityType}s" => true,
		"read_private_{$capabilityType}s" => true,
		"delete_{$capabilityType}s" => true,
		"delete_private_{$capabilityType}s" => true,
		"delete_published_{$capabilityType}s" => true,
		"delete_others_{$capabilityType}s" => true,
		"edit_private_{$capabilityType}s" => true,
		"edit_published_{$capabilityType}s" => true
	);

	// set admin capability
	$adminRole = get_role('administrator');
	foreach ($capabilitiesAdmin as $key => $value) {
		$adminRole->add_cap($key);
	}


	$capabilitiesDirRating = array(
		"edit_{$capabilityType}s" => true,
		"read_private_{$capabilityType}s" => false,
		"edit_published_{$capabilityType}s" => true,
		"delete_{$capabilityType}s" => true,
		"delete_published_{$capabilityType}s" => true,
		"publish_{$capabilityType}s" => true
	);

	global $aitThemeOptions;

	$dirRole1 = get_role('directory_1');
	if(isset($dirRole1)){
		foreach ($capabilitiesDirRating as $key => $value) {
			if( isset($aitThemeOptions->rating->disallowDirectoryUsers) ){
				$dirRole1->remove_cap($key);
			}else{
				$dirRole1->add_cap($key);
			}
		}
	}

	$dirRole2 = get_role('directory_2');
	if(isset($dirRole2)){
		foreach ($capabilitiesDirRating as $key => $value) {
			if( isset($aitThemeOptions->rating->disallowDirectoryUsers) ){
				$dirRole2->remove_cap($key);
			}else{
				$dirRole2->add_cap($key);
			}
		}
	}

	$dirRole3 = get_role('directory_3');
	if(isset($dirRole3)){
		foreach ($capabilitiesDirRating as $key => $value) {
			if( isset($aitThemeOptions->rating->disallowDirectoryUsers) ){
				$dirRole3->remove_cap($key);
			}else{
				$dirRole3->add_cap($key);
			}
		}
	}

	$dirRole4 = get_role('directory_4');
	if(isset($dirRole4)){
		foreach ($capabilitiesDirRating as $key => $value) {
			if( isset($aitThemeOptions->rating->disallowDirectoryUsers) ){
				$dirRole4->remove_cap($key);
			}else{
				$dirRole4->add_cap($key);
			}
		}
	}

	$dirRole5 = get_role('directory_5');
	if(isset($dirRole5)){
		foreach ($capabilitiesDirRating as $key => $value) {
			if( isset($aitThemeOptions->rating->disallowDirectoryUsers) ){
				$dirRole5->add_cap($key);
			}else{
				$dirRole5->remove_cap($key);
			}
		}
	}

}

function aitRatingRemoveAddButton() {
	remove_submenu_page('edit.php?post_type=ait-rating','post-new.php?post_type=ait-rating');
	if((strpos($_SERVER['PHP_SELF'],'edit.php') !== false) && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-rating')){
		echo '<style type="text/css">
				a.add-new-h2 { display: none !important; }
			</style>';
	}
}

// customize rating table
function aitRatingChangeColumns($cols)	{
	if (isDirectoryUser()) {
		$cols = array(
			'title'         => __('Name', 'ait'),
			'rating-post-id'=> __('Rating for', 'ait'),
			'rating-value'  => __('Rating', 'ait'),
			'content'		=> __('Message', 'ait'),
			'date'          => __('Date', 'ait'),
			'rating-status' => __('Status', 'ait'),
		);
	} else {
		$cols = array(
			'cb'			=> '<input type="checkbox" />',
			'title'         => __('Name', 'ait'),
			'rating-post-id'=> __('Rating for', 'ait'),
			'rating-value'  => __('Rating', 'ait'),
			'content'		=> __('Message', 'ait'),
			'date'          => __('Date', 'ait'),
			'rating-status' => __('Status', 'ait'),
		);
	}
	return $cols;
}

global $enabledRatings, $enabledRatingsMax;
$enabledRatingsMax = intval($aitThemeOptions->rating->starsCount);
$enabledRatings = array();
for ($i=1; $i <= 5; $i++) {
	$eName = 'rating'.$i.'Enable';
	$tName = 'rating'.$i.'Title';
	if (isset($aitThemeOptions->rating->$eName)) {
		$enabledRatings[$i] = $aitThemeOptions->rating->$tName;
	}
}

function aitRatingCustomColumns($column, $ratingId) {
	switch($column){
		case "rating-name":
			$post = get_post($ratingId);
			echo $post->post_title;
			break;
		case "rating-post-id":
			$postId = get_post_meta($ratingId, 'post_id', true);
			$postLink = get_permalink($postId);
			$post = get_post($postId);
			echo '<strong><a href="'.$postLink.'" target="_blank">'.$post->post_title.'</a></strong>';
			break;
		case "rating-value":
			global $enabledRatings, $enabledRatingsMax;
			$meanRounded = intval(get_post_meta($ratingId, 'rating_mean_rounded', true));
			foreach ($enabledRatings as $key => $value) {
				$rating = intval(get_post_meta($ratingId, 'rating_'.$key, true));
				echo '<div class="rating clearfix">';
				for($i = 1; $i <= $enabledRatingsMax; $i++) {
					echo '<div class="star';
					if ($i <= $rating) {
						echo ' active';
					}
					echo '"></div>';
				}
				echo '<div class="rating-label">'.$value.'</div></div><div class="clearfix"></div>';
			}
			echo '<div class="rating clearfix">';
			for($i = 1; $i <= $enabledRatingsMax; $i++) {
				echo '<div class="star';
				if ($i <= $meanRounded) {
					echo ' active';
				}
				echo '"></div>';
			}
			echo '<div class="rating-label">'.__('Mean','ait').'</div></div><div class="clearfix"></div>';
			break;
		case "rating-status":
			$post = get_post($ratingId);
			if ($post->post_status == 'publish') {
				echo "<div style='color:green;'>".__("Approved","ait")."</div>";
			} elseif ($post->post_status == 'pending') {
				echo "<a href='".admin_url('edit.php?post_type=ait-rating&rating-approve=do&rating-id='.$ratingId)."' class='button'>".__("Approve","ait")."</a>";
			}
			break;
	}
}

function aitRatingStyles() {
	echo '<style> .clearfix { clear:both; } #the-list .rating { margin: 0px; padding: 0px; } #the-list .rating-value .rating-label { float: right; padding-right: 10px; } #the-list .rating-value .star { float: right; margin: 0px; padding: 0px; width: 16px; height: 16px; background: url("'.THEME_IMG_URL.'/star-big-default.png") no-repeat; } #the-list .rating-value .star.active { background: url("'.THEME_IMG_URL.'/star-big-active.png") no-repeat ; } </style>';
}

// Don't show others ratings for directory roles
function aitRatingTableDontShowOthersRatings($query) {
	if (isDirectoryUser()) {
		if((strpos($_SERVER['PHP_SELF'],'edit.php') !== false) && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-rating')){
			$query->set('author',$GLOBALS['current_user']->ID);
		}
	}
	return $query;
}

function aitRatingShowCorrectTableNumbers($views) {
	if (isDirectoryUser()) {
		global $wpdb;

		$user = wp_get_current_user();
		$type = 'ait-rating';

		//$cache_key = $type;

		$query = "SELECT post_status, COUNT(*) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d";
		$query .= ' GROUP BY post_status';

		//$count = wp_cache_get($cache_key, 'counts');
		//if (false !== $count)
		//	return $count;

		$count = $wpdb->get_results($wpdb->prepare($query, $type, $user->ID), ARRAY_A);

		$stats = array();
		foreach (get_post_stati() as $state)
			$stats[$state] = 0;

		foreach ((array) $count as $row)
			$stats[$row['post_status']] = $row['num_posts'];

		$stats = (object) $stats;
		//wp_cache_set($cache_key, $stats, 'counts');

		global $locked_post_status, $avail_post_stati;

		$post_type = $type;

		if (!empty($locked_post_status))
			return array();

		$status_links = array();
		$num_posts = $stats;
		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();

		$total_posts = array_sum((array) $num_posts);

		// Subtract post types that are not included in the admin all list.
		foreach (get_post_stati(array('show_in_admin_all_list' => false)) as $state)
			$total_posts -= $num_posts->$state;

		$class = empty($class) && empty($_REQUEST['post_status']) && empty($_REQUEST['show_sticky']) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf(_nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' , 'ait'), number_format_i18n($total_posts)) . '</a>';

		foreach (get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status) {
			$class = '';

			$status_name = $status->name;

			if (!in_array($status_name, $avail_post_stati))
				continue;

			if (empty($num_posts->$status_name))
				continue;

			if (isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'])
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf(translate_nooped_plural($status->label_count, $num_posts->$status_name), number_format_i18n($num_posts->$status_name)) . '</a>';
		}
		return $status_links;
	} else {
		return $views;
	}
}

// Ratings meta boxes
function aitRatingEditShowDetails() {
	add_meta_box(
		'rating_details',
		__('Rating Details', 'ait'),
		'aitRatingDetailsHtml',
		'ait-rating',
		'side'
	);
}
function aitRatingDetailsHtml($rating) {
	$postId = intval(get_post_meta($rating->ID, 'post_id', true));
	$postLink = get_permalink($postId);
	$post = get_post($postId);
	echo __('Rating for: ','ait') . '<a href="'.$postLink.'">'.$post->post_title.'</a>';
}

function aitRateItem() {
	if(isset($_POST['post_id']) && isset($_POST['rating_name']) && isset($_POST['rating_values'])){
		global $aitThemeOptions;

		// Check for nonce security  
		$nonce = $_POST['nonce'];  

		if (! wp_verify_nonce($nonce, 'ajax-nonce')) {
			echo "nonce";
			exit();
		} 

		$ip = $_SERVER['REMOTE_ADDR'];
		$postId = $_POST['post_id'];
		$ratedValues = $_POST['rating_values'];

		$metaIp = get_post_meta($postId, "_ait_rating_ip");
		$votedIp = (isset($metaIp[0]) && is_array($metaIp[0])) ? $metaIp[0] : array();

		if(!aitHasAlreadyRated($postId)) {

			$votedIp[$ip] = time();
			update_post_meta($postId, "_ait_rating_ip", $votedIp);
			
			// $ratingCount = get_post_meta($postId, "_ait_rating_count", true);
			// $ratingCount = ($ratingCount == "") ? 0 : intval($ratingCount);

			// $ratingSum = get_post_meta($postId, "_ait_rating_sum", true);
			// $ratingSum = ($ratingSum == "") ? 0 : intval($ratingSum);

			// $sum = 0;
			// foreach ($ratedValues as $key => $value) {
			// 	// calculate mean
			// 	$rSum = get_post_meta($postId, '_ait_rating_'.$key.'_sum', true);
			// 	$rSum = ($rSum == "") ? 0 : intval($rSum);

			// 	$newSum = $rSum + intval($value);
			// 	$newCount = $ratingCount + 1;
			// 	$calculatedMean = ($newSum) / $newCount;

			// 	update_post_meta($postId, '_ait_rating_'.$key.'_sum', $newSum);
			// 	update_post_meta($postId, '_ait_rating_'.$key.'_mean', $calculatedMean);

			// 	$sum += $calculatedMean;
			// }
			// $mean = $sum / count($ratedValues);
			// update_post_meta($postId, '_ait_rating_mean', $mean);
			// update_post_meta($postId, '_ait_rating_count', $ratingCount + 1);
			
			$post = get_post($postId);

			$data = new StdClass;
			$data->postId = $postId;
			$data->author = $post->post_author;
			$data->name = strip_tags($_POST['rating_name']);
			$data->description = strip_tags($_POST['rating_description']);
			$data->values = $_POST['rating_values'];

			aitAddRatingPost($data);

			echo getAitRatingElement($postId, true);

		} else {
			echo "already";
		}
	} else {
		echo "missing";
	}
	exit();
}

function aitAddRatingPost($data) {

	if(defined('AIT_SERVER')) { return 0; }

	global $aitThemeOptions;

	$postStatus = (isset($aitThemeOptions->rating->ratingMustApprove)) ? 'pending' : 'publish';

	$rating = array(
		'post_author'    => $data->author,
		'post_title'     => $data->name,
		'post_content'   => $data->description,
		'post_status'    => $postStatus,
		'post_type'      => 'ait-rating',
		'comment_status' => 'closed',
		'ping_status'    => 'closed'
	);
	$ratingId = wp_insert_post($rating);
	if($ratingId == 0) return 0;

	update_post_meta($ratingId, 'post_id' , $data->postId);

	$sum = 0;
	foreach ($data->values as $key => $value) {
		update_post_meta($ratingId, 'rating_' . $key , $value);
		$sum += intval($value);
	}
	$mean = $sum / count($data->values);
	update_post_meta($ratingId, 'rating_mean' , $mean);
	update_post_meta($ratingId, 'rating_mean_rounded' , round($mean));

	// calculate new rating value for item
	aitSaveRatingMeanToDB($ratingId, null);

	return $ratingId;
}

function aitHasAlreadyRated($postId) {
	// time to check duplicity rating in minutes
	global $aitThemeOptions;
	$timeToCheck = (isset($aitThemeOptions->rating->timeToCheck)) ? intval($aitThemeOptions->rating->timeToCheck) : 60;

	$metaIp = get_post_meta($postId, "_ait_rating_ip");
	$votedIp = (isset($metaIp[0]) && is_array($metaIp[0])) ? $metaIp[0] : array();
	$ip = $_SERVER['REMOTE_ADDR'];
	if(in_array($ip, array_keys($votedIp))) {
		$time = $votedIp[$ip];
		$now = time();
		if(round(($now - $time) / 60) > $timeToCheck) {
			return false;
		}		
		return true;
	}
	return false;
}

function getAitRatingElement($postId, $success = false) {

	global $aitThemeOptions;

	if(isset($aitThemeOptions->rating->enableRating)){

		$args = array(
			'post_type' => 'ait-rating',
			'post_status' => 'publish',
			'nopaging' => true,
			'meta_query' => array(
				array(
					'key' => 'post_id',
					'value' => $postId
				)
			)
		);
		$ratings = new WP_Query($args);

		// count default is 5
		$starsCount = (isset($aitThemeOptions->rating->starsCount) && intval($aitThemeOptions->rating->starsCount) > 1) ? intval($aitThemeOptions->rating->starsCount) : 5;

		$metaIp = get_post_meta($postId, "_ait_rating_ip");
		$votedIp = (isset($metaIp[0]) && is_array($metaIp[0])) ? $metaIp[0] : array();

		?>
		<div id="ait-rating-system" class="rating-system" data-post-id="<?php echo $postId ?>">
			<h3><?php _e('Leave a review','ait'); ?></h3>
			<?php if($success) {
				echo '<div class="rating-success">'.__('Your rating has been successfully sent','ait').'</div>';
			} else if(aitHasAlreadyRated($postId)) {
				echo '<div class="rating-already">'.__('You have already rated','ait').'</div>';
			} else { ?>
			<div class="rating-send-form">
				<div class="rating-ipnuts">
					<div class="rating-details">
						<div class="detail"><label for="rating-name"><?php _e('Your Name','ait'); ?></label><input id="rating-name" name="rating-name" type="text"></div>
						<div class="detail"><label for="rating-description"><?php _e('Description','ait'); ?></label><textarea id="rating-description" name="rating-description" rows="4"></textarea></div>
						<button class="send-rating"><?php _e('Send rating','ait'); ?></button>
						<div class="message error" style="display: none;"><?php _e('Please fill out all fields!','ait'); ?></div>
						<div class="message success"<?php if(!$success) echo ' style="display: none;"'; ?>><?php _e('Your rating has been successfully sent','ait'); ?></div>
					</div>
					<div class="ratings">
					<?php for($i = 1; $i <= 5; $i++){
						$nameEnable = 'rating'.$i.'Enable';
						if(isset($aitThemeOptions->rating->$nameEnable)){
							$nameTitle = 'rating'.$i.'Title'; ?>
							<div class="rating clearfix" data-rating-id="<?php echo $i; ?>" data-rated-value="0"><div class="rating-title"><?php echo $aitThemeOptions->rating->$nameTitle; ?></div>
								<div class="stars clearfix">
								<?php for($j = 1; $j <= $starsCount; $j++) { ?>
									<div class="star" data-star-id="<?php echo $j; ?>"></div>
								<?php } ?>
								</div>
							</div>
						<?php 
						}
					}
					?>
					</div><!-- .ratings -->
				</div><!-- .rating-inputs -->
			</div><!-- .rating-send-form -->
			<?php } // if already 
			if(count($ratings->posts) > 0) { ?>
			<div class="user-ratings">
				<?php foreach ($ratings->posts as $rating) { ?>
				<div class="user-rating">
					<div class="user-values" style="display: none;">
						<?php
						$sum = 0;
						$count = 0;
						for($i = 1; $i <= 5; $i++){
							$nameEnable = 'rating'.$i.'Enable';
							if(isset($aitThemeOptions->rating->$nameEnable)){
								$nameTitle = 'rating'.$i.'Title';
								$stars = get_post_meta($rating->ID, 'rating_'.$i, true);
								$stars = (!empty($stars)) ? intval($stars) : 0;
								$sum += $stars;
								$count++;
								?>
								<div class="rating clearfix">
									<div class="rating-title"><?php echo $aitThemeOptions->rating->$nameTitle; ?></div>
									<div class="user-stars clearfix">
										<?php for($j = 1; $j <= $starsCount; $j++) { ?>
											<div class="star<?php if($j <= $stars) echo ' active'; ?>" data-star-id="<?php echo $j; ?>"></div>
										<?php } ?>
									</div>
								</div>
						<?php } } ?>
					</div>
					<div class="user-details">
						<div class="name"><?php echo $rating->post_title; ?></div>
						<div class="date"><?php echo $rating->post_date; ?></div>
						<div class="value">
						<?php $mean = round($sum / $count);
						for($j = 1; $j <= $starsCount; $j++) { ?>
							<div class="star<?php if($j <= $mean) echo ' active'; ?>" data-star-id="<?php echo $j; ?>"></div>
						<?php }	?>
						</div>
						<div class="description"><?php echo $rating->post_content; ?></div>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<hr>
		<?php
	}
}

function aitCalculateMeanForPost($postId) {
	$max = (isset($GLOBALS['aitThemeOptions']->rating->starsCount)) ? intval($GLOBALS['aitThemeOptions']->rating->starsCount) : 5;
	// get all ratings for this post
	$args = array(
		'post_type' => 'ait-rating',
		'post_status' => 'publish',
		'nopaging' => true,
		'meta_query' => array(
			array(
				'key' => 'post_id',
				'value' => $postId
			)
		)
	);
	$ratings = new WP_Query($args);

	if(count($ratings->posts) > 0) {
		$sum = 0;
		foreach ($ratings->posts as $rating) {
			$sum += floatval(get_post_meta($rating->ID,'rating_mean',true));
		}
		$rounded = round($sum / count($ratings->posts));
		$full = floatval($sum / count($ratings->posts));
		return array('max' => $max, 'val' => $rounded, 'full' => $full, 'count' => count($ratings->posts));
	} else {
		return false;
	}
		
}

// Save rating mean to items custom type
function aitSaveRatingMeanToDB($id, $post) {
	if (!isset($post)) {
		$post = get_post(intval($id));
	}
	if ($post->post_type == 'ait-rating') {
		$itemId = get_post_meta($id, 'post_id', true);
		$rating = aitCalculateMeanForPost($itemId, false);
		// for sorting
		update_post_meta($itemId, 'rating_rounded', $rating['val']);
		update_post_meta($itemId, 'rating_full', $rating['full']);
		update_post_meta($itemId, 'rating_max', $rating['max']);
		update_post_meta($itemId, 'rating_count', $rating['count']);
		// also as associated array
		update_post_meta($itemId, 'rating', $rating);
	}
}


if (isset($_GET['rating-approve']) && !empty($_GET['rating-id'])) {
	$ratingId = intval($_GET['rating-id']);
	// admin can approve all ratings
	if (current_user_can('manage_options')) {
		aitRatingApprove($ratingId);
	} else {
		global $current_user;
		$itemId = intval(get_post_meta($ratingId, 'post_id', true));
		$item = get_post($itemId);
		if (isset($current_user) && ($current_user->ID == intval($item->post_author))) {
			aitRatingApprove($ratingId);
		}
	}
}

function aitRatingApprove($ratingId) {
	global $ratingMessages;
	$rating = get_post($ratingId,'ARRAY_A');
	$rating['post_status'] = 'publish';
	$chStatus = wp_insert_post($rating, true);
	if(is_wp_error($chStatus)){
		$ratingMessages = $chStatus->get_error_message();
	} else {
		$ratingMessages = __('Rating was approved!','ait');
	}
}

function aitShowApprovedRatingNotice() {
	global $ratingMessages;
	if(isset($ratingMessages)){
		echo '<div class="updated"><p>'.$ratingMessages.'</p></div>';
	}
}


