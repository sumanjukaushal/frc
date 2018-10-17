<?php
	 //These functions depends upon wp-bookmark plugins. This plugins should be active for this functionality
	 
	 //1: List all bookmarks of the user - Verified [This function is removed]
	 function getAllFavouriteList(){
		  $user_id = check_if_bookmark_plugin_active();
		  $bookMarArr = frc_getUserBookmarks($user_id);
		  if(count($bookMarArr)){
			   return new WP_REST_Response(array('status_code' => 200, 'data' => $bookMarArr), 200);
		  }else{
			   echo json_encode(array('message' => "no bookmark found",'status_code' => 400  ));die;
			   //return new WP_Error( 'bookmarks_not_found', 'no bookmark found', array( 'status_code' => 404 ) );
		  }
     }
	 
	 //1: List all bookmarks of the user - subpart 
	 function frc_getUserBookmarks($user_id, $post_id = 0){
		  $wpb = new wpb_api;
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  $collections = $wpb->get_collections( $user_id );
		  $bookMarArr = $bookMarSngArr = array();
		  foreach($bookmarks as $itemID => $temp){
			   if($site_id && $itemID != $site_id) continue;
			   $postObj = get_post($itemID);
			   if($itemID){
					$campSite = frc_item_details($itemID, $postObj);
					$bookMarSngArr['collection'] = array();
					foreach($collections as $collectionID => $collectionArr){
						 if(is_array($collectionArr) && array_key_exists($itemID, $collectionArr)){
							  if($collectionID == 0){
								   $bookMarSngArr['collection'][] = array(
																				'collection_name' => 'default',
																				'view_option' => 'public',
																				'collection_id' => $collectionID,
																				//$temp
																				);
							  }else{
								   $bookMarSngArr['collection'][] = array(
																				'collection_name' => $collectionArr['label'],
																				'view_option' => $collectionArr['privacy'],
																				'collection_id' => $collectionID,
																				//$temp
																				);
							  }
						 }
					}
					$bookMarArr[] = array('bookmark_id' => $itemID, 'site' => $campSite, 'collections' => $bookMarSngArr['collection']);
			   }
		  }
		  return $bookMarArr;
	 }
	 
	 function frc_getBookmarks($user_id, $site_id = 0){
		  $wpb = new wpb_api;
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  $collections = $wpb->get_collections( $user_id );
		  $bookMarArr = $bookMarSngArr = array();
		  foreach($bookmarks as $itemID => $temp){
			   if($site_id && $itemID != $site_id) continue;
			   $postObj = get_post($itemID);
			   if($itemID){
					$campSite = frc_item_details($itemID, $postObj);
					$bookMarSngArr['collection'] = array();
					foreach($collections as $collectionID => $collectionArr){
						 if(is_array($collectionArr) && array_key_exists($itemID, $collectionArr)){
							  if($collectionID == 0){
								   $bookMarSngArr['collection'][] = array(
																				'collection_name' => 'default',
																				'view_option' => 'public',
																				'collection_id' => $collectionID,
																				//$temp
																				);
							  }else{
								   $bookMarSngArr['collection'][] = array(
																				'collection_name' => $collectionArr['label'],
																				'view_option' => $collectionArr['privacy'],
																				'collection_id' => $collectionID,
																				//$temp
																				);
							  }
						 }
					}
					$bookMarArr = array('site' => $campSite, 'collections' => $bookMarSngArr['collection']);
			   }
		  }
		  return $bookMarArr;
	 }
	 
	 //1: List all bookmarks of the user - subpart not in use
	 function getFavoriteListByCategory(){
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  if(!array_key_exists("collection_id",$_GET)){
			   echo json_encode(array('message' => "collection id missing",'status_code' => 400  ));die;
			   return new WP_Error( 'error', 'collection id missing', array( 'status_code' => 404 ) );
		  }
		  $collection_id = $_GET["collection_id"];
		  if(!empty($collection_id)){
			   $post = array();
			   if(!empty($bookmarks)){
					foreach($bookmarks as $postid => $col_id){
						 if($col_id == $collection_id){
							  $post[]= $postid;
						 }
					}
			   }else{
					echo json_encode(array('message' => "collection list is empty",'status_code' => 404  ));die;
					return new WP_Error( 'message', 'collection list is empty', array( 'status_code' => 404 ) );
			   }
			   $result_arr = array();
			   if(!empty($post)){
					foreach($post as $key => $value){
						 $postType = get_post_type($post[$key]);
						 if('ait-item' == $postType || 'ait-dir-item' == $postType){
							  $post_data = get_post( $post[$key] );
							  $result_arr[] = array('id' => $post_data->ID,'title' => $post_data->post_title,'collection_id' =>$collection_id);
						 }
					}
			   }
			   if(!empty($result_arr)){
					return new WP_REST_Response($result_arr, 200);
			   }else{
					echo json_encode(array('message' => "no item found",'status_code' => 404  ));die;
					return new WP_Error( 'message', 'no item found', array( 'status_code' => 404 ) );
			   }
		  }else{
			   echo json_encode(array('message' => "no item found",'status_code' => 404  ));die;
			   return new WP_Error( 'broke', 'collection id is missing', array( 'status_code' => 404 ) );
		  }
     }
	 
	 //2: Add bookmark - Verified
	 function add_2_collections($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $collection_ids = $request['collection_ids'];
		  $post_id = $request['site_id'];
		  $pos = strpos("---".$collection_ids, ",");
		  $collectionIDArr = array();
		  if($pos){
			   $collectionIDArr = explode(",", $collection_ids);
		  }else{
			   if((int)$collection_ids >= 0)$collectionIDArr[] = $collection_ids;
		  }
		  if(!empty($post_id) && count($collectionIDArr) >= 1){
			   $campExist = false;//37952
			   if ( 'publish' == get_post_status ( $post_id ) ) {
					//pending, draft, auto-draft, future, private, inherit, trash
					$campExist = true;
			   }elseif(
					   'pending' == get_post_status ( $post_id ) ||
					   'draft' == get_post_status ( $post_id ) ||
					   'auto-draft' == get_post_status ( $post_id ) ||
					   'future' == get_post_status ( $post_id ) ||
					   'private' == get_post_status ( $post_id ) ||
					   'inherit' == get_post_status ( $post_id ) ||
					   'trash' == get_post_status ( $post_id )
					   ){
					echo json_encode(array('message' => "Camp site status is not published",'status_code' => 404  ));die;
			   }
			   $collectionExists = false;
			   if(all_collection_exist($user_id, $collectionIDArr)){
					$collectionExists = true;
			   }
			   /*Alternate way
			   global $wpdb;
			   $post_exists = $wpdb->get_row("SELECT `ID` FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');
			   */
			   //Note:code need to be added for checking if post exist
			   if($campExist && $collectionExists){
					$statusArr = array();
					foreach($collectionIDArr as $sngCollectionID){
						 if($status = add_2_collection($user_id, $post_id, $sngCollectionID)){
							  $statusArr[] = $status;
						 }
					}
					if(count($statusArr)){
						 $bookMarArr = frc_getBookmarks($user_id, $post_id);
						 if(count($bookMarArr)){
							  return new WP_REST_Response(array('status_code' => 200, 'data' => $bookMarArr), 200);
						 }else{
							  echo json_encode(array('message' => "no bookmark found",'status_code' => 404  ));die;
							  return new WP_Error( 'bookmarks_not_found', 'no bookmark found', array( 'status_code' => 404 ) );
						 }
						 //return new WP_REST_Response(json_encode(array(''status_code' => true, 'msg' => implode(",", $statusArr). " is(are) added to collection(s)")), 200);
					}else{
						 echo json_encode(array('status_code' => "Failed to add item from collection",'status_code' => 404  ));die;
						 return new WP_Error( 'collection_error', 'Failed to add item from collection', array( 'status_code' => 404 ) );
					}
			   }else{
					//Error: Site/Collection not found
					if(!$campExist){
						 echo json_encode(array('message' => "collection not found",'status_code' => 404  ));die;
						 return new WP_Error( 'site_not_found', 'site_id not found!', array( 'status_code' => 404 ) );
					}else{
						 echo json_encode(array('message' => "collection not found",'status_code' => 404  ));die;
						 return new WP_Error( 'collection_not_found', "collection not found!", array( 'status_code' => 404 ) );
					}
			   }
		  }else{
			   echo json_encode(array('message' => "site_id and collection_ids are required",'status_code' => 400  ));die;
			   return new WP_Error( 'message', 'site_id and collection_ids are required', array( 'status_code' => 400 ) );
		  }
	 }
	 
	 //2: Add bookmark - subpart
	 function add_2_collection($user_id, $post_id, $sngCollectionID){
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  /* add collection (post id relation) */
		  if (!isset($collections[$sngCollectionID])){
			   $collections[$sngCollectionID] = array();
		  }
		  $collections[$sngCollectionID][$post_id] = 1;
		 
		  
		  /* add bookmark with collection id */
		  if(!isset($bookmarks[$post_id])){
			   $bookmarks[$post_id] = $sngCollectionID;
		  }else{
			   $prev_collection_id = $bookmarks[$post_id];
			   if( !wpb_get_option('allow_multiple_bookmarks') ){
					unset($collections[$prev_collection_id][$post_id]);
					// remove from prev collection
			   }
			   $bookmarks[$post_id] = $sngCollectionID; // update collection
		  }
		  $output['collection_id'] = $sngCollectionID; // update active collection
				  
		  update_user_meta($user_id, '_wpb_collections', $collections);
		  update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		  update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)+1);
		  $bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' , true);
		  if($bookmarked_by == false) {
			  $bookmarked_by = array($user_id);
		  }else{
			  $bookmarked_by[] = $user_id;
		  }
		  if(update_post_meta($post_id , 'wpb_bookmarked_by' , $bookmarked_by)){
			   return $post_id;
		  }else{
			   return 0;
		  }
	 }
	 
	 //2: Add bookmark - subpart
	 function all_collection_exist($user_id, $collectionIDs){
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  foreach($collectionIDs as $collectionID){
			   if(!array_key_exists($collectionID, $collections)){
					return false;
			   }
		  }
		  return true;
	 }
	 
	 //3: Delete bookmark
	 function remove_from_collection($request){
		  global $wpdb;
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  //print_r($collections);die;
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  $bookmark_categories = $wpb->get_category_bookmarks( $user_id );
		  $collection_ids = $request['collection_id'];
		  $post_id = (int)$request['site_id'];
		  $collectIDs = explode(",", $collection_ids);
		  $key = 0;
		  if($request['collection_id'] == "" || empty($post_id)){
			   echo json_encode(array('message' => "site_id and collection_id are required!",'status_code' => 400  ));die;
		  }
		  foreach($collectIDs as $collectionID){
			   $collectionID = (int)$collectionID;
			   //removing requested ids of collections which are not present in user collection list
			   if(!array_key_exists($collectionID, $collections)){
					unset($collectIDs[$key]);
			   }
		  }
		  if(!count($collectIDs)){
			   echo json_encode(array('message' => "Collection(s) not found.",'status_code' => 404  ));die;
		  }
		  $siteExistInCollection = false;
		  $removedFromCollections = array();
		  foreach($collections as $collection_id => $collectionArr){
			   if(in_array($collection_id, $collectIDs)){
					$requestedCollection = $collections[$collection_id];
					if(array_key_exists($post_id, $requestedCollection)){
						 //Remove campid from collection
						 //print_r($requestedCollection);
						 $siteExistInCollection = true;
						 //echo "remove_bookmark($user_id, $post_id, $collection_id)";
						 remove_bookmark($user_id, $post_id, $collection_id);
						 $removedFromCollections[] = $collection_id;
					}
			   }
		  }
		  
		  if(!$siteExistInCollection){
			   echo json_encode(array('message' => "Site not found in any of requested collection(s)",'status_code' => 404  ));die;
		  }
		  
		  /*unset($collections[$collection_id][$post_id]);
		  update_user_meta($user_id, '_wpb_collections', $collections);
		  update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);*/
		  
		  $bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' , true);
		  if($bookmarked_by == false) {
			   $bookmarked_by = array();
		  }else{
			   if(($key = array_search($user_id, $bookmarked_by)) !== false) {
					unset($bookmarked_by[$key]);
			   }
		  }
		  $status = update_post_meta($post_id , 'wpb_bookmarked_by' , $bookmarked_by);
		  return new WP_REST_Response(array('status_code' => 200, 'message' => 'success for collection id(s): '.implode(",",$removedFromCollections)), 200);
		  /*if($status){
			   return new WP_REST_Response(array('status_code' => 200, 'message' => 'success for collection id(s): '.implode(",",$removedFromCollections)), 200);
		  }else{
			   echo json_encode(array('message' => "Failed to remove item from collection",'status_code' => 404  ));die;
		  }*/
	 }
	 
	 function remove_bookmark($user_id, $post_id, $rCollectID){
		  global $wpdb;
		  //Start:Testing---------------------------------
		  if($_SERVER['REMOTE_ADDR'] == '122.173.46.89'){
			   $bookmarkQry = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_bookmarks' and user_id = '$user_id'";
			   $bkmarkMeta = $wpdb->get_results($bookmarkQry, OBJECT);
			   /*
			    Note: If any camp_id is already in bookmark list then it is not getting added again to bookmark list, when we are
			    bookmarking any camp_id which is already in another collection then collection_id is getting updated for camp_id
			    [15122] => 3 {camp_id => collection_id} now suppose I add item 15122 to collection 5, then this data is getting updated
			    as  [15122] => 5
			    **/
			   if(count($bkmarkMeta) >= 1){
					$metaValue = $bkmarkMeta[0]->meta_value;
					$uMetaID = $bkmarkMeta[0]->umeta_id;
					$bkmarkMetaData = unserialize($metaValue);
					//print_r($bkmarkMetaData);
			   }
			   //structure: Array([0] => '', ['site_id'] => collection_id);
			   $collectionQry = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_collections' and user_id = '$user_id'";
			   $collectMeta = $wpdb->get_results($collectionQry, OBJECT);
			   if(count($collectMeta) >= 1){
					$metaValue = $collectMeta[0]->meta_value;
					$uMetaID = $collectMeta[0]->umeta_id;
					$collectMetaData = unserialize($metaValue);
					//print_r($collectMetaData);
			   }
		  }
		  //structure: Array([0] => Array(), [1] => Array([label] => Low Cost,[privacy] => private,[16373] => 1,[16296] => 1));
		  //End:Testing---------------------------------
		  
		  //Start: logic for deleting site from collection
		  $query = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_collections' and user_id = '$user_id'";
		  $collectionMeta = $wpdb->get_results($query, OBJECT);
		  if(is_array($collectionMeta) and count($collectionMeta) >= 1){
			   $metaValue = $collectionMeta[0]->meta_value;
			   $uMetaID = $collectionMeta[0]->umeta_id;
			   $collectionMetaData = unserialize($metaValue);
			   $sitePresentInOtherCollections = false;
			   $collectionResult =  false;
			   foreach($collectionMetaData as $collectID => $siteIDsArr){
					//echo "\n$collectID == $rCollectID";
					if($collectID == $rCollectID){
						 //Remove site id from collection if camp exist in that collection.
						 unset($collectionMetaData[$collectID][$post_id]);
						 $collectSerialized = serialize($collectionMetaData);
						 //print_r($collectSerialized);
						 $collectQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$collectSerialized' WHERE `umeta_id` = '$uMetaID'";
						 $collectionResult = $wpdb->update(
										"{wpdb->prefix}usermeta",
										array('meta_value' => $collectSerialized),
										array('umeta_id' => $uMetaID),
										array('%s'),
										array('%d')
									   );
						 //echo $wpdb->last_query;
					}else{
						 if(is_array($siteIDsArr) && array_key_exists($post_id, $siteIDsArr)){
							  $sitePresentInOtherCollections = true;
						 }
					}
			   }//foreach
			   
			   if($sitePresentInOtherCollections){
					;//then do not remove it from bookmarks
					/*$query = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_bookmarks' and user_id = '$user_id'";
					$bookmarkMeta = $wpdb->get_results($query, OBJECT);
					$collectionQry = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_collections' and user_id = '$user_id'";
					$collectionMeta = $wpdb->get_results($collectionQry, OBJECT);
					unserialize($collectionMeta[0]->meta_value);
					if(is_array($bookmarkMeta) && count($bookmarkMeta) >= 1){
						 $unserializedData = $bookmarkMeta[0]->meta_value;
						 $dataArr = unserialize($unserializedData);
						 $uMetaID = $bookmarkMeta[0]->umeta_id;
						 $targetCollection = $dataArr[$rCollectID];
						 if(array_key_exists($post_id, $targetCollection)){
							  echo "I am unsetting";
							  unset($dataArr[$rCollectID][$post_id]);
						 }
						 $bookmarkSerialized = serialize($dataArr);
						 print_r($dataArr);
						 $bookmarkQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$bookmarkSerialized' WHERE `umeta_id` = '$uMetaID'";
						 $bookmarkResult = $wpdb->update(
								   "{wpdb->prefix}usermeta",
								   array('meta_value' => $bookmarkSerialized),
								   array('umeta_id' => $uMetaID),
								   array('%s'),
								   array('%d')
								  );
						 //print_r($bookmarkResult);
						 echo $wpdb->last_query;
						 update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);
						 $bookmarkQry = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_collections' and user_id = '$user_id'";
						 $bookmarkMeta = $wpdb->get_results($bookmarkQry, OBJECT);
						 $unserializedData = $bookmarkMeta[0]->meta_value;
						 $dataArr = unserialize($unserializedData);
						 foreach($dataArr as $tSiteId => $tcollectionID){
							  if($tSiteId == $post_id && $tcollectionID == $rCollectID){
								   unset($dataArr[$tSiteId]);
							  }
						 }
						 $bookmarkData = serialize($dataArr);
						 $bookmarkKey = $bookmarkMeta[0]->meta_key;
						 echo $bookmarkQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$bookmarkData' WHERE `umeta_id` = '$bookmarkKey'";
						 $bookmarkResult = $wpdb->update(
															"{wpdb->prefix}usermeta",
															array('meta_value' => $bookmarkData),
															array('umeta_id' => $bookmarkKey),
															array('%s'),
															array('%d')
													   );
						 
					}*/
			   }else{
					//Remove it from bookmarks
					$query = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_bookmarks' and user_id = '$user_id'";
					$bookmarkMeta = $wpdb->get_results($query, OBJECT);
					if(is_array($bookmarkMeta) && count($bookmarkMeta) >= 1){
						 $metaValue = $bookmarkMeta[0]->meta_value;
						 $uMetaID = $bookmarkMeta[0]->umeta_id;
						 $bookmarkMetaData = unserialize($metaValue);
						 if($collectionResult){
							  //if record updated for collections
							  unset($bookmarkMetaData[$post_id]);
							  $bookmarkSerialized = serialize($bookmarkMetaData);
							  $bookmarkQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$bookmarkSerialized' WHERE `umeta_id` = '$uMetaID'";
							  $bookmarkResult = $wpdb->update(
										"{wpdb->prefix}usermeta",
										array('meta_value' => $collectSerialized),
										array('umeta_id' => $uMetaID),
										array('%s'),
										array('%d')
									   );
						 }
						 update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);
					}
			   }
		  }
		  //End:
	 }
	 
	 function remove_from_collection_old($request){
		  global $wpdb;
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  $bookmark_categories = $wpb->get_category_bookmarks( $user_id );
		  $collection_id = (int)$request['collection_id'];
		  $post_id = (int)$request['site_id'];
		  $category_id = $_GET['category_id'];
		  $siteExistInCollection = false;
		  if($collection_id >= 0 && !empty($post_id)){
			   if(array_key_exists($collection_id, $collections)){
					$requestedCollection = $collections[$collection_id];
					if(is_array($requestedCollection) && array_key_exists($post_id, $requestedCollection)){
						 $siteExistInCollection = true;
					}
			   }
		  }
		  if($request['collection_id'] == "" || empty($post_id)){
			   echo json_encode(array('message' => "site_id and collection_id are required!",'status_code' => 400  ));die;
			   return new WP_Error( 'message', 'site_id and collection_ids are required!', array( 'status_code' => 400 ) );
		  }elseif(!array_key_exists($collection_id, $collections)){
			   echo json_encode(array('message' => "Collection not found.",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_not_found', 'Collection not found.', array( 'status_code' => 404 ) );
		  }elseif(!$siteExistInCollection){
			   echo json_encode(array('message' => "Site not found in requested collection",'status_code' => 404  ));die;
			   return new WP_Error( 'site_not_found_in_collection', 'Site not found in requested collection-'.$request['collection_id'], array( 'status_code' => 404 ) );
		  }
		  //Start: logic for deleting site from collection
		  $query = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_collections' and user_id = '$user_id'";
		  $collectionMeta = $wpdb->get_results($query, OBJECT);
		  if(is_array($collectionMeta) and count($collectionMeta) >= 1){
			   $metaValue = $collectionMeta[0]->meta_value;
			   $uMetaID = $collectionMeta[0]->umeta_id;
			   $collectionMetaData = unserialize($metaValue);
			   $sitePresentInOtherCollections = false;
			   $collectionResult =  false;
			   foreach($collectionMetaData as $collectID => $siteIDsArr){
					if($collection_id == $collectID){
						 //Remove site id from collection
						 unset($collectionMetaData[$collectID][$post_id]);
						 $collectSerialized = serialize($collectionMetaData);
						 $collectQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$collectSerialized' WHERE `umeta_id` = '$uMetaID'";
						 $collectionResult = $wpdb->update(
										"{wpdb->prefix}usermeta",
										array('meta_value' => $collectSerialized),
										array('umeta_id' => $uMetaID),
										array('%s'),
										array('%d')
									   );
					}else{
						 if(is_array($siteIDsArr) && array_key_exists($post_id, $siteIDsArr)){
							  $sitePresentInOtherCollections = true;
						 }
					}
			   }
			   if($sitePresentInOtherCollections){
					;//then do not remove it from bookmarks
			   }else{
					//Remove it from bookmarks
					$query = "SELECT * FROM `{wpdb->prefix}usermeta` WHERE `meta_key`='_wpb_bookmarks' and user_id = '$user_id'";
					$bookmarkMeta = $wpdb->get_results($query, OBJECT);
					if(is_array($bookmarkMeta) && count($bookmarkMeta) >= 1){
						 $metaValue = $bookmarkMeta[0]->meta_value;
						 $uMetaID = $bookmarkMeta[0]->umeta_id;
						 $bookmarkMetaData = unserialize($metaValue);
						 if($collectionResult){
							  //if record updated for collections
							  unset($bookmarkMetaData[$post_id]);
							  $bookmarkSerialized = serialize($bookmarkMetaData);
							  $bookmarkQuery = "UPDATE `{wpdb->prefix}usermeta` SET `meta_value` = '$bookmarkSerialized' WHERE `umeta_id` = '$uMetaID'";
							  $bookmarkResult = $wpdb->update(
										"{wpdb->prefix}usermeta",
										array('meta_value' => $collectSerialized),
										array('umeta_id' => $uMetaID),
										array('%s'),
										array('%d')
									   );
						 }
						 update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);
					}
			   }
		  }
		  
		  //End:
		  /*unset($collections[$collection_id][$post_id]);
		  update_user_meta($user_id, '_wpb_collections', $collections);
		  update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);*/
		  
		  $bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' , true);
		  if($bookmarked_by == false) {
			   $bookmarked_by = array();
		  }else{
			   if(($key = array_search($user_id, $bookmarked_by)) !== false) {
					unset($bookmarked_by[$key]);
			   }
		  }
		  $status = update_post_meta($post_id , 'wpb_bookmarked_by' , $bookmarked_by);
		  if($status){
			   return new WP_REST_Response(array('status_code' => 200, 'message' => 'success'), 200);
		  }else{
			   echo json_encode(array('message' => "Failed to remove item from collection",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_error', 'Failed to remove item from collection', array( 'status_code' => 404 ) );
		  }
	 }
	 
	 //4: Get user bookmark - [This function is removed]
	 function user_bookmark($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $post_id = (int)$request['site_id'];
		  $bookMarArr = frc_getUserBookmarks($user_id, $post_id);
		  if(count($bookMarArr)){
			   return new WP_REST_Response(array('status_code' => 200, 'data' => $bookMarArr), 200);
		  }else{
			   echo json_encode(array('message' => "no bookmark found",'status_code' => 404  ));die;
			   return new WP_Error( 'bookmarks_not_found', 'no bookmark found', array( 'status_code' => 404 ) );
		  }
	 }
	 
	 //5: List all collections of the user
	 function getFavouriteCategoryList(){
		  global $wpdb;
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  if($_SERVER['REMOTE_ADDR'] == '122.173.46.89'){
			   //print_r($collections);
			   //die;
		  }
		  $result_array = array();
		  if(!empty($collections)){
			   $result_array = getUserCollections($collections);
			   //$result_array['user_id'] = $user_id;
			   return new WP_REST_Response(array('data' => $result_array, 'status_code' => 200), 200);
		  }else{
			   echo json_encode(array('message' => "no collection found",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_not_found', 'no collection found', array( 'status_code' => 404 ) );
		  }
	 }
	 
	 function frc_getFavouriteCategoryList($user_id){
		  global $wpdb;
		  $wpb = new wpb_api;
		  $collections = $wpb->get_collections( $user_id );
		  return $result_array = getUserCollections($collections);
	 }
	 
	 //5: List all collections of the user - sub function
	 function getUserCollections($collections, $collection_id = NULL){
		  $campSiteData = array();
		  foreach($collections as $collectionID => $collection){
			   if(isset($collection_id) && $collectionID != $collection_id){
					continue;
			   }
			   $collection_name = "";
			   if($collectionID == 0){
					$collection_name = 'default';
			   }else{
					$collection_name = $collection['label'];
			   }
			   if(is_array($collection) && array_key_exists('label', $collection))unset($collection['label']);
			   if(is_array($collection) && array_key_exists('privacy', $collection))unset($collection['privacy']);
			   $campSites = array();
			   
			   if(is_array($collection)){
					foreach($collection as $siteID => $bookmarkCount){
						 $postObj = get_post($siteID);
						 if($siteID){
							  if(array_key_exists($siteID, $campSiteData)){
								   //$campSites[] = $campSiteData[$siteID];
								   $campSites[] = array('bookmark_id' => $siteID, 'site' => $campSiteData[$siteID]);
							  }else{
								   $campSiteData[$siteID]  = frc_item_details($siteID, $postObj);
								   $campSites[] = array('bookmark_id' => $siteID, 'site' => $campSiteData[$siteID]);
							  }
						 }
					}
			   }
			   $result_array[] = array(
									   'collection_id' => $collectionID,
									   'collection_name' => str_replace('default', 'Default List', $collection_name),
									   'bookmarks' => $campSites,
									   );
		  }
		  return $result_array;
	 }
	 
	 //6: Add collection - Verified
	 function create_collection(WP_REST_Request  $request){  // add collection
		  $user_id = check_if_bookmark_plugin_active();
		  extract($request->get_params());
		  $wpapi = new wpb_api;
		  echo 'hi'.$collection_name.'bye';die;
		  if(!empty($collection_name)){
			   $collections = $wpapi->get_collections($user_id);
			   if(!isset($privacy))$privacy = 'private'; //asked by mobile team to set it private by default
			   $collections[] = array('label' => $collection_name,'privacy' => $privacy);
			   $status = update_user_meta($user_id, '_wpb_collections', $collections);
			   
			   if($privacy == "public"){
					$privacycollections = get_option("wp_collections");
					if(!is_array($privacycollections)) $privacycollections = array();
					$privacycollections[] = array('userid' => $user_id);
					if(!in_array($user_id,$privacycollections)) update_option("wp_collections",$privacycollections);
			   }
			   if($status){
					$collections = $wpapi->get_collections($user_id);
					foreach($collections as $collectionID => $temp){}
					$data = array('collection_id' => $collectionID, 'collection_name' => $collection_name);
					return new WP_REST_Response(array('status_code' => 200, 'data' => $data), 200);
			   }else{
					echo json_encode(array('message' => "Failed to create new collection",'status_code' => 400  ));die;
					return new WP_Error( 'collection_error', 'Failed to create new collection', array( 'status_code' => 400 ) );
			   }
		  }else{
			   echo json_encode(array('message' => "'Collection name is required",'status_code' => 400  ));die;
			   return new WP_Error( 'collection_error', 'Collection name is required', array( 'status_code' => 404 ) );
		  }
	 }
	 
	 //7: Delete collection
	 function hard_remove_collection($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $id =  (int)$request['id'];
		  $collections = $wpb->get_collections($user_id);
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		  
		  // remove bookmarks
		  foreach($collections[$id] as $k => $arr) {
			   if ($k != 'label') {
				  if(isset($bookmarks[$k])){unset($bookmarks[$k]);}
			   }
		  }
		
		  // remove collection
		  if ($id > 0){
			   if(array_key_exists($id, $collections)){
					unset($collections[$id]);
					$status1 = update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
					$status2 = update_user_meta($user_id, '_wpb_collections', $collections);
					if($status1 || $status2){
						 return new WP_REST_Response(array('status_code' => 200, 'message' => 'success'), 200);
					}else{
						 echo json_encode(array('message' => "Failed to remove collection with id: $id",'status_code' => 404  ));die;
						 return new WP_Error( 'collection_error', "Failed to remove collection with id: $id", array( 'status_code' => 404 ) );
					}
			   }else{
					echo json_encode(array('message' => "Collection not found with id: $id",'status_code' => 404  ));die;
					return new WP_Error( 'collection_error', "Collection not found with id: $id", array( 'status_code' => 404 ) );
			   }
		  }else{
			   echo json_encode(array('message' => "Collection not found with id: $id",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_error', "Collection not found with id: $id", array( 'status_code' => 404 ) );
		  }
	 }
	 
	 //7: Delete collection - not in use
	 function soft_remove_collection(){
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $id = $_GET['id'];
		  $collections = $wpb->get_collections($user_id);
		  $bookmarks = $wpb->get_bookmarks( $user_id );
		 
		  // transfer bookmarks to default collection
		  foreach($collections[$id] as $k => $arr) {
			  if ($k != 'label') {$collections[0][$k] = 1;}
		  }
		  
		  // remove collection
		  if ($id > 0){unset($collections[$id]);}
		  $status1 = update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		  $status2 = update_user_meta($user_id, '_wpb_collections', $collections);
					
		  if($status1 || $status2){
			   return new WP_REST_Response(array('status_code' => 200, 'msg' => 'Removed from collection'), 200);
		  }else{
			   echo json_encode(array('message' => "Failed to remove collection",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_error', 'Failed to remove collection', array( 'status_code' => 404 ) );
		  }
	 }
	 
	 //8: Get user collection
	 function user_collection($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $wpb = new wpb_api;
		  $id =  (int)$request['id'];
		  $collections = $wpb->get_collections($user_id);
		  if(array_key_exists($id, $collections)){
			   $bookmarks = $wpb->get_bookmarks( $user_id );
			   $result_array = getUserCollections($collections, $id);
			   return new WP_REST_Response(array('data' => $result_array, 'status_code' => 200), 200);
		  }else{
			   echo json_encode(array('message' => "Collection not found.",'status_code' => 404  ));die;
			   return new WP_Error( 'collection_error', 'Collection not found.', array( 'status_code' => 404 ) );
		  }
	 }
?>