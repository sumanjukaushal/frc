<?php
    //>>>>>: User Profile
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/';
		//1: Get user profile
		register_rest_route( $namespace, '/'.'user', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_userinfo',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/wp-json/api/v1/user/ [Verb: Get]
		
		//2: Edit user profile
		register_rest_route( $namespace, '/'.'user', array(
			  array(
				  'methods'         => 'POST',
				  'callback'        => 'frc_edit_userinfo',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/wp-json/api/v1/user/ [Verb: POST]
	});
	//<<<<<: User Profile
?>