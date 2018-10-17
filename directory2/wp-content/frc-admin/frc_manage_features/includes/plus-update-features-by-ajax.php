<?php
	require_once("../../../../wp-config.php");
    require_once('../../../../wp-load.php');
    global $wpdb;
	$postMetaTbl = $wpdb->prefix.'postmeta';
	$postTbl = $wpdb->prefix."posts";
	$featureMetaKey = "_ait-item_item-extension";
	$site_id_ajax = '';
	$dataArr = array();
	if(array_key_exists('site_id',$_REQUEST)) $site_id_ajax = $_REQUEST['site_id'];
	
	//$campfeatures = getCampfeatures($site_id_ajax);	//echo "<pre>";print_r($campfeatures);echo "</pre>"; 
	if(!empty($site_id_ajax)){
		$getFeatureQry = "SELECT * FROM $postMetaTbl WHERE `post_id`=$site_id_ajax AND `meta_key`='$featureMetaKey'";
		$featuresData = $wpdb->get_results($getFeatureQry);
		$featuresArray = unserialize($featuresData[0]->meta_value);
		$campfeatures = array();
		foreach($featuresArray['onoff'] as $featureKey => $onOff){
			if($onOff == 'on'){
				$campfeatures[$featureKey] = $onOff;
			}
		}
		
		foreach($_REQUEST as $ajax_key=> $ajax_value){
			if($ajax_key == "url" || $ajax_key == "site_id") continue;
			$ajaxKey = str_replace("_",".",$ajax_key);
			if($ajax_value == 1){
				if(!array_key_exists($ajaxKey,$campfeatures)) $featuresArray["onoff"][$ajaxKey] = 'on';
			}else{
				if(array_key_exists($ajaxKey,$campfeatures)) $featuresArray["onoff"][$ajaxKey] = 'off';
			} 
		}
	}
		
	if(!empty($featuresArray)){
		$feature = serialize($featuresArray);
		$wpdb->update( 
						$postMetaTbl, 
						array( 'meta_value' => $feature ),
						array( 'post_id' => $site_id_ajax, 'meta_key' => $featureMetaKey)
					); //echo $insertQry = $wpdb->last_query;
		$dbGMTNow = gmdate("Y-m-d H:i:s", time());
		$currentDBTime = Date("Y-m-d H:i:s");
		$wpdb->query("UPDATE `$postTbl` SET `post_modified_gmt` = '{$dbGMTNow}', post_modified = '{$currentDBTime}'  WHERE ID = {$site_id_ajax}" );
		echo json_encode(array('status' => 'Update Successfully!!'));die;
	}else{
		echo json_encode(array('error' => 'error'));die;
	}
	
?>