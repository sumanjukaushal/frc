<?php
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/user/';
	    //New route added on May 27, 18
	    register_rest_route( $namespace, '/' . 'premium_users', array(
			array(
				'methods'         => WP_REST_SERVER::EDITABLE,
				'callback'        => 'frc_premium_users',
				'args'            => array(),
			)
		));//Ref: https://www.freerangecamping.co.nz/frcdirectory/wp-json/api/v1/user/premium_users
		register_rest_route( $namespace, '/' . 'random_posts', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'random_posts',
				'args'            => array(),
			)
		));//Ref: https://www.freerangecamping.com.au/directory/wp-json/api/v1/user/random_posts/
	});
	
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/user/';
	
		register_rest_route( $namespace, '/' . 'random_posts_test', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'random_posts',
				'args'            => array(),
			)
		));//Ref: https://www.freerangecamping.com.au/directory/wp-json/api/v1/user/random_posts_test/
	});
	
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = 'wp/v2';
		register_rest_route( $namespace, '/items/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'fr_get_items'
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/items/1
		register_rest_route( $namespace, '/texonmy/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'fr_get_texonmy'
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/items/1
		
		register_rest_route( $namespace, '/search=(?P<search>[a-zA-Z0-9 !#"\'$%^&*()_ ]+)', array(
			'methods' => 'GET',
			'callback' => 'fr_getCampsiteListByFilter'
		) );
	
		register_rest_route( $namespace, '/' . 'getCampsiteListByFilter', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_search_campaigns',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/getCampsiteListByFilter?s=carribean
		
		//****************Start: Search option routes*****************
		register_rest_route( $namespace, '/' . 'list_locations', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_list_locations',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/frc/v1/list_locations
		
		register_rest_route( $namespace, '/' . 'list_all_locations', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_list_all_locations',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/frc/v1/list_locations
		
		register_rest_route( $namespace, '/' . 'list_categories', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_list_categories',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/frc/v1/list_categories
		
		register_rest_route( $namespace, '/' . 'list_all_categories', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_list_all_categories',
				'args'            => array(),
			)
		) );
		
		register_rest_route( $namespace, '/' . 'list_features', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'fr_list_features',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/direcotry/wp/v2/list_features/
		//****************End: Search option routes*****************
		
		//****************Start: User Routes*****************
		register_rest_route( $namespace, '/' . 'frc_get_nonce', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'frc_get_nonce',
				'args'            => array(),
			)
		) );
		
		register_rest_route( $namespace, '/' . 'frc_register', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'frc_register',
				'args'            => array(),
			)
		) );
		//Ref: https://www.freerangecamping.co.nz/wp-json/wp/v2/frc_register/?username=john&nonce=83cabd06a7&display_name=John&email=sample@email.com
		//****************End: User Routes*****************
		
		//*******Start: Get all modified posts in last 2/3 days **************
		register_rest_route( $namespace, '/' . 'modified_posts', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'frc_modified_items',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/modified_posts
		//*******End: Get all modified posts in last 2/3 days ****************
		
		//*******Start: Get all images uploaded from Mobile App for Items **************
		register_rest_route( $namespace, '/' . 'mobile_uploads', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'frc_mobile_uploads',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/modified_posts
		//*******End: Get all modified posts in last 2/3 days ****************
		
		//*******Start: Get all images uploaded from Mobile App for Items **************
		
		register_rest_route( $namespace, '/' . 'test_snippets', array(
			array(
				'methods'         => 'GET',
				'callback'        => 'frc_test_snippets',
				'args'            => array(),
			)
		) );
		//https://www.freerangecamping.com.au/directory/wp-json/wp/v2/test_snippets
		//*******End: Get all modified posts in last 2/3 days ****************
		
	});
?>