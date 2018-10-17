<?php
	//Script URL: http://freerangecamping.co.nz/mobile/wp-content/manage_api_jobs/records2.php
	require_once("../../wp-config.php");
	require_once('../../wp-load.php');
	include('features-array.php');
	
	$tableName = 'camp_sites_1';
	$searchQry = "SELECT * FROM $tableName  LIMIT 20";
	$results = $wpdb->get_results($searchQry);
		
	foreach($results as $srno => $result){
		$site_id = $result->site_id;
		$site_name = $result->site_name;
		$features = $result->features;
		$features = unserialize($features);
		//  echo "<pre>";print_r($features);
		foreach($showfeatures as $feature_key => $feature){
			$dataArr[$site_name][] = '';
			if(array_key_exists($feature,$features)){
					$dataArr[$site_name][]= $feature;
			}
		}
	}
	echo "<pre>";print_r($dataArr);
?>
 