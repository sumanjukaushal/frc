<?php
    //>>>>>: Data Management
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/';
		
		//1: Download offline user data
		register_rest_route( $namespace, '/'.'data/user/download/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_user_offlinedata',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/data/user/download/
		//Note: Only one zip file should be compressed not as separate photo folder and sql file
		
		//2: Download database
		register_rest_route( $namespace, '/'.'data/user/download/database', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_download_sqlite_db',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/data/user/download/database
		
		//3: Download updated images
		register_rest_route( $namespace, '/'.'data/user/download/updates', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_modified_images',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/data/user/download/database
		
		//4: Download offline map data - No Longer required
		register_rest_route( $namespace, '/'.'data/map/download/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_user_offlinemaps',
				  'args'            => array(),
			  )
		));
		
		//5: For internal purpose
		register_rest_route( $namespace, '/'.'data/user/export/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_export',
				  'args'            => array(),
			  )
		));
	});
	//<<<<<: Data Management
?>