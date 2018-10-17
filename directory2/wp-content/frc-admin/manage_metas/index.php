<?php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    require_once('../../wp-settings.php');
    require_once("../../wp-includes/registration.php");
	require_once('../../wp-blog-header.php');
	define('DB_NAME', 'freerang_directory');
	define('DB_USER', 'freerang_rob');
	define('DB_PASSWORD', 'nandini123');
	define('DB_HOST', 'localhost');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATE', '');
	define( 'WP_MEMORY_LIMIT', '512M' );
	//$dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	//$selected = mysql_select_db(DB_NAME,$dbhandle);
	$postID = 25761;
	$taxonmies = get_the_taxonomies( $postID);
	echo "<pre>";print_r($taxonmies);echo "</pre>";
	$postType = get_post_type($postID);
	echo "<pre>=>";print_r($postType);echo "</pre>";
	$args = array(
		'posts_per_page'   => 100,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'ait-dir-item',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'author_name'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$posts = get_posts( $args );
	//echo "<pre>";print_r($posts);echo "</pre>";
$counter = 0;
        //echo "<pre>posts=>";print_r($posts);echo "</pre>";
	foreach($posts as $post){
if($counter++ == 100)break;
		$postMeta = get_post_meta($post->ID);
                $postMetaData = $postMeta['_ait-dir-item'];
		echo "<pre>Post Meta::";print_r($postMetaData[0]);echo "</pre>";
		//$customMeta = unserialize($postMeta['_ait-dir-item'][0]);
		echo "<pre>";print_r($customMeta);echo "</pre>";
	}
?>