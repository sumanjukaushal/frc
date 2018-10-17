<?php
    //>>>>>: User Bookmarks: All routes for bookmarks and collections********
	add_action( 'rest_api_init', function () {
        $post_type_taxonomies = new frc_apis;
        $namespace = '/api/v1/user/';
	  
        //1: List all bookmarks of the user [This function is removed]
        register_rest_route( $namespace, '/' . 'bookmark', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'getAllFavouriteList',
				'args'            => array(),
			)
		) );
	  
        //2: Add Bookmark
        register_rest_route( $namespace, '/' . 'bookmark', array(
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => 'add_2_collections',
                'args'            => array(),
            )
        ) );//Ref URL: https://www.freerangecamping.co.nz/wp-json/api/v1/user/bookmark/?cookie=<cookie>, Verb: GET
	  
        //3: Delete bookmark
        //register_rest_route( $namespace, '/' . 'bookmark/(?P<site_id>\d+)/(?P<collection_id>\d+)/', array(
		register_rest_route( $namespace, '/' . 'bookmark/(?P<site_id>\d+)/(?P<collection_id>[0-9 ,]+)/', array(
			array(
				'methods'         => WP_REST_Server::DELETABLE,
				'callback'        => 'remove_from_collection',
				'args'            => array(),
			)
		) );
	  
        //4: Get user bookmark [This function is removed]
        register_rest_route( $namespace, '/' . 'bookmark/(?P<site_id>\d+)/', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'user_bookmark',
				'args'            => array(),
			)
		) );
	  
        //5: List all collections of the user
        register_rest_route( $namespace, '/' . 'collection', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'getFavouriteCategoryList',
				'args'            => array(),
			)
		) );//Ref URL: https://www.freerangecamping.co.nz/wp-json/api/v1/user/collection/?cookie=<cookie>, Verb: GET
	  
        //6: Add collection
        register_rest_route( $namespace, '/' . 'collection', array(
			array(
				'methods'         => WP_REST_Server::CREATABLE,
				'callback'        => 'create_collection',
				'args'            => array(),
			)
		) );//Ref URL: https://www.freerangecamping.co.nz/wp-json/api/v1/user/collection/?cookie=<cookie>, Verb : POST
	  
        //7: Remove collection
        register_rest_route( $namespace, '/' . 'collection/(?P<id>\d+)/', array(
			array(
				'methods'         => WP_REST_Server::DELETABLE,
				'callback'        => 'hard_remove_collection',
				'args'            => array(),
			)
		) );//Ref URL: https://www.freerangecamping.co.nz/wp-json/api/v1/user/collection/?cookie=<cookie>, Verb : DELETE
	  
        //8: Get user collection
        register_rest_route( $namespace, '/' . 'collection/(?P<id>\d+)/', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => 'user_collection',
				'args'            => array(),
			)
		) );//Ref URL: https://www.freerangecamping.co.nz/wp-json/api/v1/user/collection/<collection_id>?cookie=<cookie>, Verb : GET
	});
?>