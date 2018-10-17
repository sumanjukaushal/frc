<?php
	/*
	  Plugin Name: WP REST API - FRC API
	  Plugin URI: http://magiks.ru
	  Description: This plugin gets data for api's defined in mobile application.
	  Version: 1.0
	  Author: Mohit Kaushal
	  Author URI: http://magiks.ru/
	*/
	header('Access-Control-Allow-Origin: *');//* => editor.swagger.io
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept'); //
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$pluginPath = plugin_dir_path( __FILE__ );
	if(!defined('DS')) {define('DS',DIRECTORY_SEPARATOR);}
	define('WP_REST_API_FRC_PATH', $pluginPath);
	
	$campCats = array(
						'Free Camps' => 9,
						'Rest Areas' => 30,
						'Dump Points' => 6,
						'Campgrounds' => 14,
						'Caravan Parks' => 155,
						'Help Outs' => 20,
						'House Sitting' => 12,
						'Water Points' => 2767,
						'Park Over' => 16,
					);
	
	$partnerTCatId = $partnerCatIds = array(
							"Accommodation" => 2096,
							"Camping Accessories" => 5,
							"Entertainment" => 7,
							"Food & Wine" => 8,
							"Fuel" => 210,
							"Groceries" => 10,
							"Markets" => 106,
							"Medical" => 15,
							"Personal Health" => 17,
							"Repairs" => 4,
							"Services" => 107,
							"Information Centre" => 2088,
							"IGA Stores" => 13,
						   );
	foreach($partnerTCatId as $partnerCatId){
		$childArr = get_term_children( $partnerCatId, 'ait-dir-item-category' );
		foreach($childArr as $childCatID){
			$partnerCatIds[] = $childCatID;
		}
	}
	
	require(WP_REST_API_FRC_PATH.'library'.DS.'upload'.DS.'class.upload.php');
	
	require(WP_REST_API_FRC_PATH.'routes'.DS.'misc_routes.php');
	require(WP_REST_API_FRC_PATH.'routes'.DS.'camp_routes.php'); //SET - 1
	require(WP_REST_API_FRC_PATH.'routes'.DS.'offline_routes.php'); //SET - 2
	require(WP_REST_API_FRC_PATH.'routes'.DS.'authentication_routes.php'); //SET - 3
	require(WP_REST_API_FRC_PATH.'routes'.DS.'collection_routes.php'); //SET - 4
	require(WP_REST_API_FRC_PATH.'routes'.DS.'profile_routes.php'); //SET - 5
	
	require(WP_REST_API_FRC_PATH.'includes'.DS.'functions.php');
	require(WP_REST_API_FRC_PATH.'includes'.DS.'feature-array.php');
	require(WP_REST_API_FRC_PATH.'includes'.DS.'camp_functions.php'); //SET - 1
	require(WP_REST_API_FRC_PATH.'includes'.DS.'offline_functions.php'); //SET - 2
	require(WP_REST_API_FRC_PATH.'includes'.DS.'authentication_functions.php'); //SET - 3
	require(WP_REST_API_FRC_PATH.'includes'.DS.'collection_functions.php'); //SET - 4
	require(WP_REST_API_FRC_PATH.'includes'.DS.'profile_functions.php'); //SET - 5
	require(WP_REST_API_FRC_PATH.'includes'.DS.'misc_functions.php'); //SET - 5
	
	
	//Any business, that is attached to one of the 11 Above Business Categories, is an frc_partner - By Glen communicated on 16th Jan, 2017
	
	$frc_api_url = frc_full_url( $_SERVER );
	if(strpos($frc_api_url, '/wp-json/api/')){
		global $wpdb;
		$user_id = get_current_user_id();
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$browser = @get_browser(null, true);
		$agentArr = array('user_agent' => $userAgent, 'browser' => $browser);
		$campStatusRes = $wpdb->insert( 'mobile_app_call_log', array(
																	 'url' => $frc_api_url,
																	 'data' => json_encode($_REQUEST),
																	 'user_agent' => json_encode($agentArr),
																	 'ip_address' => $_SERVER["REMOTE_ADDR"],
																	 'user_id' => $user_id,
																	 ),
												array(
													  '%s', '%s', '%s','%s', '%d'
													)
												);
		//echo $insertQry = $wpdb->last_query;
	}
	
	
	class frc_apis{
		public function __construct(){
			$version = '2';
			$namespace = 'wp/v' . $version;
			$base = 'post-type-taxonomies';
			register_rest_route($namespace, '/' . $base, array(
				'methods' => 'GET',
				'callback' => array($this, 'fr_get_post_type_taxonomies'),
			));
			//https://www.freerangecamping.co.nz/wp-json/frc/v1/post-type-taxonomies
		}
	
		public function fr_get_post_type_taxonomies($object){
			$return = array();
			$args = array(
				'public' => true
			);
			$post_types = get_post_types($args);
			foreach( $post_types as $post_type ){
				$taxonomies = get_object_taxonomies( $post_type );
				if (!empty($taxonomies)) {
					$return[$post_type] = $taxonomies;
				}
			}
			return new WP_REST_Response($return, 200);
		}
	}//https://torquemag.io/2015/06/adding-custom-routes-wordpress-rest-api/