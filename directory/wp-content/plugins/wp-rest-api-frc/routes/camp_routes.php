<?php
	//http://43.229.61.95/l5-swagger/index.html
    //>>>>>: Camping Site Information
	add_action( 'rest_api_init', function () {
		$post_type_taxonomies = new frc_apis;
		$namespace = '/api/v1/';
		
		//1: Search camping site
		register_rest_route( $namespace, '/'.'site/search_by_area/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_search_by_area',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.com.au/directory/wp-json/api/v1/site/search_by_area [Verb: Get]
		
		//2: Search camping site near me
		register_rest_route( $namespace, '/'.'site/search_by_location/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_search_by_location',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.com.au/directory/wp-json/api/v1/site/search_by_location [Verb: Get]
		
		//3: Get camping site by id
		register_rest_route( $namespace, '/'.'site/(?P<id>\d+)/', array(
			  array(
				  'methods'         => WP_REST_Server::READABLE,
				  'callback'        => 'frc_campsiteinfo',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.co.nz/wp-json/api/v1/user/ [Verb: Get]
		
		//4: Upload receipt image of a commercial site
		register_rest_route( $namespace, '/'.'site/uploadimage/(?P<id>\d+)/', array(
			  array(
				  'methods'         => WP_REST_Server::CREATABLE,
				  'callback'        => 'frc_upload_receipt_image',
				  'args'            => array(),
			  )
		));//https://www.freerangecamping.com.au/directory/wp-json/api/v1/site/uploadimage/10766 [Verb: POST]
		
		//5: Rate a camping site
		register_rest_route( $namespace, '/' . 'site/rate/(?P<id>\d+)/', array(
			array(
				'methods'         => 'POST',//WP_REST_Server::EDITABLE,//WP_REST_Server::CREATABLE,
				'callback'        => 'frc_add_rating',
				'args'            => array(),
			)
		));
		
		//6: Add comment to camping site
		register_rest_route( $namespace, '/' . 'site/comment/(?P<id>\d+)/', array(
			array(
				'methods'         => 'POST',//WP_REST_Server::EDITABLE,//WP_REST_Server::CREATABLE,
				'callback'        => 'frc_add_comment',
				'args'            => array(),
			)
		));//https://www.freerangecamping.com.au/directory/wp-json/api/v1/site/comment/10766
		
		//7: Report error of a camping site
		register_rest_route( $namespace, '/' . 'site/reporterror/(?P<id>\d+)/', array(
			array(
				'methods'         => 'POST',//WP_REST_Server::EDITABLE,//WP_REST_Server::CREATABLE,
				'callback'        => 'frc_report_campsite',
				'args'            => array(),
			)
		));
	});////https://www.freerangecamping.com.au/directory/wp-json/api/v1/site/reporterror/10766
	//<<<<<: Camping Site Information
?>