<?php
	require_once("../../../../wp-config.php");
    require_once('../../../../wp-load.php');
	/*
	https://www.freerangecamping.co.nz/directory/wp-content/frc_manage_features/includes/update-features-by-ajax.php?url=ajax&site_id=51177&waterFillingAvailable=0&noTents=0&camperTrailers=0&noCamperTrailers=0&caravans=0&onSiteAccomodation=0&showGrounds=0&nationalParknForestry=0&pubs=0&sharedWithTrucks=1
	
	 https://www.freerangecamping.co.nz/directory/wp-content/frc_manage_features/includes/update_features.php?url=ajax&site_id=51177
	 &waterFillingAvailable=0
	 &noTents=0
	 &camperTrailers=0
	 &noCamperTrailers=0
	 &caravans=0
	 &onSiteAccomodation=0
	 &showGrounds=0
	 &nationalParknForestry=0
	 &pubs=0
	 &sharedWithTrucks=1
	*/
    global $wpdb;
	$postMetaTbl = $wpdb->prefix.'postmeta';
	$postTbl = $wpdb->prefix."posts";
	$site_id_ajax = '';
	$dataArr = array();
	$metaType = '_ait-dir-item';
	if(array_key_exists('site_id',$_REQUEST)) $site_id_ajax = $_REQUEST['site_id'];
	if(!function_exists('getCampfeatures')){
		function getCampfeatures($site_id){
			global $metaType;
			$postMeta = get_post_meta($site_id, $metaType, true);
			return $postMeta;
		}
	}
	$campfeatures = getCampfeatures($site_id_ajax);	//echo "<pre>";print_r($campfeatures);echo "</pre>"; 
	if(!empty($site_id_ajax)){
		foreach($_REQUEST as $ajax_key=> $ajax_value){
			if($ajax_key == "url" || $ajax_key == "site_id") continue;
			
			if($ajax_value == 1){
				if(!array_key_exists($ajax_key, $campfeatures)) $campfeatures[$ajax_key] = array('enable' => 'enable');
			}else{
				if(array_key_exists($ajax_key, $campfeatures)) {
					unset($campfeatures[$ajax_key]);
					unset($campfeatures[$ajax_key."_addnl"]);
				}
			} 
		}
	}
	//echo "<pre>";print_r($campfeatures);echo "</pre>";die;
	if(!empty($campfeatures)){ //echo "<pre>";print_r($campfeatures);echo "</pre>";
		$feature = serialize($campfeatures);
		$wpdb->update( 
						$postMetaTbl, 
						array( 'meta_value' => $feature ),
						array( 'post_id' => $site_id_ajax, 'meta_key' => $metaType)
					); //echo $insertQry = $wpdb->last_query;
		$dbGMTNow = gmdate("Y-m-d H:i:s", time());
		$currentDBTime = Date("Y-m-d H:i:s");
		$wpdb->query("UPDATE `$postTbl` SET `post_modified_gmt` = '{$dbGMTNow}', post_modified = '{$currentDBTime}'  WHERE ID = {$site_id_ajax}" );
		//echo $wpdb->last_query;;die;
		echo json_encode(array('status' => 'Update Successfully!!'));die;
	}else{
		echo json_encode(array('error' => 'error'));die;
	}
?>