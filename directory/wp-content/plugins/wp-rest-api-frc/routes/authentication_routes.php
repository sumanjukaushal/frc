<?php
    //>>>>>: User Authentication: All routes for user authentication
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/user/';
		
		register_rest_route( $namespace, '/' . 'frc_login/', array(
				array(
					'methods'         => 'POST',
					'callback'        => 'frc_login',
					'args'            => array(),
				)
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/user/login
		
		register_rest_route( $namespace, '/' . 'user_login/', array(
				array(
					'methods'         => 'POST',
					'callback'        => 'frc_login',
					'args'            => array(),
				)
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/user/login
		
		register_rest_route( $namespace, '/' . 'camp_login/', array(
				array(
					'methods'         => 'POST',
					'callback'        => 'frc_login',
					'args'            => array(),
				)
		));//https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/user/login
	});
	//<<<<<: User Authentication: All routes for user authentication********
?>