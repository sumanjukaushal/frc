<?php
	 //http://43.229.61.95/l5-swagger/index.html
	 //1: Search camping site
	 function frc_search_by_area($request){
		  //https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/site/search_by_location
		  /*
		   *lat = -21.039
		   *long = 149.190
		   *if km replace 3959 with 6371
		  SELECT site_id,(6371 * acos (cos ( radians(-21.052) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_lon` ) - radians(149.191) ) + sin ( radians(149.191) ) * sin( radians( `gps_lat` ) ))) AS distance FROM camp_sites ORDER BY distance asc

//HAVING distance < 30
LIMIT 0 , 20;

//http://dexxtr.com/post/83498801191/how-to-determine-point-inside-circle-using-mysql
SELECT site_id, SQRT(POW(`gps_lat` - (-21.039) , 2) + POW(`gps_lon` - (149.190), 2)) * 100 as distance FROM `camp_sites` order by distance asc;
//This seems more good

WHERE
    SQRT(POW(`gps_lat` - (-21.039) , 2) + POW(`center_lng` - (149.190), 2)) * 100 < `radius`
    
=================
:R = 6371 radius of earth
Select site_id, 
       acos(sin(-21.039)*sin(radians(gps_lat)) + cos(-21.039)*cos(radians(gps_lat))*cos(radians(gps_lon)-149.190)) * 6371 As D
From camp_sites
Google:
[https://developers.google.com/maps/articles/phpsqlsearch_v3#findnearsql]
SELECT id, ( 6371 * acos( cos( radians(:lat) ) * cos( radians( `gps_lat` ) ) * cos( radians( lng ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( `gps_lat` ) ) ) ) AS distance FROM `camp_sites`  ORDER BY distance asc;

SELECT site_id, ( 6371 * acos( cos( radians(-21.039) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_lon` ) - radians(149.190) ) + sin( radians(-21.039) ) * sin( radians( `gps_lat` ) ) ) ) AS distance FROM `camp_sites`  ORDER BY distance asc; [Working nicely]

SELECT site_id, ( 6371 * acos( cos( radians(-21.039) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_lon` ) - radians(149.190) ) + sin( radians(-21.039) ) * sin( radians( `gps_lat` ) ) ) ) AS distance FROM `camp_sites`  having distance < 500 ORDER BY distance asc; 
=================
		  */
		  echo json_encode(array('message' => "Under development",'status_code' => 400  ));die;
     }
	 
	 //2: Search camping site near me
	 //Route: https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/site/search_by_location
	 function frc_search_by_location($request){
		  global $wpdb;
		  //https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/site/search_by_location
		  //In query string, we can have gps_lon, gps_lat, radius, order_by, page_index, page_size
		  /*
		   Default Australian Capital Territory, Australia [Capital city of Australia]
		  */
		  $lat =  isset($request['gps_lat']) && !empty($request['gps_lat']) ? (float)trim($request['gps_lat']) : '-35.473469';
		  $long =  isset($request['gps_lon']) && !empty($request['gps_lon'])? (float)trim($request['gps_lon']): '149.012375';
		  $radius =  isset($request['radius']) && !empty($request['radius']) ? (int)$request['radius']: 50;
		  if(isset($request['order_by'])){
			   $orderBy = (int)$request['order_by'];
			   switch($orderBy){
					case 1: $orderBy = ' ORDER BY distance ASC';break;
					case 2: $orderBy = ' ORDER BY rating DESC';break;
					default: $orderBy = ' ORDER BY distance ASC';break;
			   }
		  }else{
			   $orderBy = ' ORDER BY distance ASC';
		  }
		  $pageSize = $offset = isset($request['page_size']) && $request['page_size'] ? (int)$request['page_size'] : 20;
		  $pageNo = isset($request['page_index']) && $request['page_index'] ? (int)$request['page_index'] : 1;
		  $offset = $offset * ($pageNo-1);
		  
		  //will give total records.
		  $whereClause = "( 6371 * acos( cos( radians($lat) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_lon` ) - radians($long) ) + sin( radians($lat) ) * sin( radians( `gps_lat` ) ) ) ) < $radius";
		  $countQry = "SELECT count(site_id) as tot_records FROM `camp_sites`  WHERE  $whereClause";
		  $results = $wpdb->get_results($countQry);
		  $noOfRecs = 0;
		  if(count($results) > 0){
			   $noOfRecs = $data['total_no_of_camp_sites'] = $results[0]->tot_records;
		  }
		  $siteIDs = $data['camp_sites'] = array();
		  $limitQry = "SELECT site_id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_lon` ) - radians($long) ) + sin( radians($lat) ) * sin( radians( `gps_lat` ) ) ) ) AS distance FROM `camp_sites`  having distance < {$radius}{$orderBy}";
		  //paginate bottom query
		  $limitQry .= " Limit $pageSize OFFSET $offset";
		  //echo $limitQry;
		  //$limitRS = mysql_query($limitQry);
		  $limitRS = $wpdb->get_results($limitQry);
		  $itemArr = array();
		  foreach($limitRS as $limitObj){
			   $siteIDs[] = $limitObj->site_id;
			   $postObj = get_post($limitObj->site_id);
			   $itemArr[] = frc_item_details($limitObj->site_id, $postObj, $lat, $long, $limitObj->distance);
		  }
		  
		  $data['camp_sites'] = $itemArr;
		  // The Query
		  //$args = array('post_type' => 'ait-dir-item','post__in' => $siteIDs);
		  /*$items = new WP_Query( $args );
		  $data['camp_sites'] = $siteIDs;
		  foreach ($items->posts as $key => $item) {
			   //fwrite($handle, "\nQuery for item:".$item->ID."\n");
			   $featuresStr = "";
			   $itemArr = array(
								   'site_name' => $item->post_title,
								   'site_id' => $item->ID,
								   'post_id' => $item->ID,
								   'site_description' => $item->post_content,
								   'rating' => $item->rating,
								   'status' => $item->post_status,
							  );
		  }*/
		  if($noOfRecs){
			   return new WP_REST_Response(array('status_code' => 200, 'data' => $data), 200);
		  }else{
			   echo json_encode(array('message' => "No camping site found.",'status_code' => 404  ));die;
		  }
		  
     }

	 //3: Get camping site by id
	 function frc_campsiteinfo($request){
		  $itemID =  (int)$request['id'];
		  $lat =  $request['gps_lat'];
		  $long =  $request['gps_lon'];
		  $postObj = get_post($itemID);
		  if(is_object($postObj)){
			   $itemData = frc_item_details($itemID, $postObj, $lat, $long);
			   return new WP_REST_Response(array('status_code' => 200, 'data' => $itemData), 200);
		  }else{
			   echo json_encode(array('message' => "No camping site found.",'status_code' => 404  ));die;
		  }
	 }
	 
	 //3: Get camping site by id - subpart
	 function is_old_theme(){
		  $currentWPTheme = wp_get_theme();
		  $return = array();
		  $args = array('public' => true);
		  $post_types = get_post_types($args);
		  foreach( $post_types as $post_type ){
			  $taxonomies = get_object_taxonomies( $post_type );
			  if (!empty($taxonomies)) {
				  $return[$post_type] = $taxonomies;
			  }
		  }
		  $oldTheme = false;
		  if(is_array($return) && array_key_exists('ait-dir-item', $return)){
			   $oldTheme = true;
		  }elseif($currentWPTheme->Name == 'Directory Plus' || $currentWPTheme->Name == 'Directory Plus Child'){
			   $oldTheme = false;
		  }
		  return $oldTheme;
	 }
	 
	 /*
		  Old Theme 				| 	New Theme							|	Type
		  --------------------------------------------------------------------------------------
		  "_ait-dir-item" 			| 	"_ait-item_item-data"				|	Meta Data
		  "_ait_dir_customField"	| 	"ait-item-extension-custom-field"	|	Custom Data
		  "ait-dir-item"			|	"ait-item"							|	Post Type 
		  "ait-dir-items"			|	"ait-items"			??				|	[Item Categories]
		  "ait-dir-item-location"	|	"ait-locations"						|	[Item Locations]
		  "ait-dir-item-category"	|	"ait-item-category"		??			|	[Item Categories]
		  features in old theme		|	_ait-item_item-extension			|
		  ait-rating				|										|	Item Rating
		  --------------------------------------------------------------------------------------
		  SELECT * FROM  `{$wpdb->prefix}postmeta` WHERE `meta_key` =  '_ait_dir_gallery' order by `post_id` desc
	 */
	 //3: Get camping site by id - subpart
	 function frc_item_details($itemID, $postObj, $lat = null, $lon = null, $distance = null){
		  global $partnerCatIds;
		  //Test : 27352
		  if(empty($lat))$lat = -33.865143;if(empty($lon))$lon = 151.209900; //For Sydney, NSW
		  $isOldTheme = is_old_theme();
		  global $wpdb;
		  $site_category = $photos = $openingHours = $features = $item = $ratingComments = array();
		  $item['site_name'] = $postObj->post_title;
		  $vidCount = substr_count($postObj->post_content,"[cleveryoutube");
		  for($i = 0; $i < $vidCount; $i++){
			   $pos1 = strpos($postObj->post_content, "[cleveryoutube");
			   if($pos1){
				   $pos2 = strpos($postObj->post_content, "]", $pos1);
				   $cleverUStr = substr ( $postObj->post_content , $pos1, ($pos2-$pos1)+1 );
				   $v1 = strpos($cleverUStr,'video="');
				   $v1= $v1 + strlen('video="');
				   $v2 = strpos($cleverUStr, '"', $v1);
				   $cleverVStr = substr ( $cleverUStr , $v1, ($v2-$v1) );
				   $youTubeStr = '<iframe style="width: 100%; height: 100%; left: 0;" src="http://www.youtube.com/embed/'.$cleverVStr.'?rel=0&amp;showinfo=0?ecver=2" width="640" height="360" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
				   $youTubeStr = "<div><a href='http://www.youtube.com/embed/".$cleverVStr."?rel=0&amp;showinfo=0?ecver=2'><img src='http://freerangecamping.com.au/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;'></a></div>";
				   $postObj->post_content = str_replace($cleverUStr, $youTubeStr, $postObj->post_content);
				   //position: absolute; 
			   }
		  }
		  
		  //Start: Added on 24th Aug, 2017
		  $couponCount = substr_count($postObj->post_content,'[wlm_private');
		  for($i = 0; $i < $couponCount; $i++){
			   $pos1 = strpos($postObj->post_content, "[wlm_private");
			   if($pos1){
				   $pos2 = strpos($postObj->post_content, "]", $pos1);
				   $couponStr = substr ( $postObj->post_content , $pos1, ($pos2-$pos1)+1 );
				   $postObj->post_content = str_replace($couponStr, "", $postObj->post_content);
				   $postObj->post_content = str_replace("[/wlm_private]", "", $postObj->post_content);
			   }
		  }
		  //End: Added on 24th Aug, 2017
		  
		  if(true || '27.255.185.38' == $_SERVER["REMOTE_ADDR"]){
			   $startTag = "<iframe";
			   $endTag = "</iframe>";
			   $iframeCount = substr_count($postObj->post_content,$startTag);
			   for($j = 0; $j < $iframeCount; $j++){
					$pos1 = strpos($postObj->post_content, $startTag);
					if($pos1){
						 $pos2 = strpos($postObj->post_content, $endTag, $pos1)+ strlen($endTag);
						 $iframeStr = substr ( $postObj->post_content , $pos1, ($pos2-$pos1)+1 );
						 preg_match( '/src="([^"]*)"/i', $iframeStr, $matches ) ;
						 if(count($matches) >=1){
							  $iframeSrc = $matches[1];
							  $iframeUTubeStr = "<div><a href='$iframeSrc'><img src='https://www.freerangecamping.com.au/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;'></a></div>";
							  $postObj->post_content = str_replace($iframeStr, $iframeUTubeStr, $postObj->post_content);
						 }
						 /*
						 $xml = simplexml_load_string($iframeStr);
						 $list = $xml->xpath("//@src");	//$srcAtt = $xml->attributes()->src;print_r($srcAtt);
						 if(array_key_exists(0, $list) && !empty($iframeSrc = xml_attribute($list[0], 'src'))){
							  //echo $iframeSrc;
							  //
							  $iframeUTubeStr = "<div><a href='$iframeSrc'><img src='https://www.freerangecamping.com.au/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;'></a></div>";
							  $postObj->post_content = str_replace($iframeStr, $iframeUTubeStr, $postObj->post_content);
						 }
						 */
						 /*$xmldoc = new DOMDocument();
						 $xmldoc->loadHTML($iframeStr);
						 $iframeElemnts = $xmldoc->getElementsByTagName("iframe");
						 $itms = $iframeElemnts->childNodes;
						 print_r($itms);
						 foreach($itms as $item){
							if ($item->nodeType == XML_ELEMENT_NODE) echo $item->getAttribute('src');
						 }*/
						 /*$searchNode = $xmldoc->getElementsByTagName( "iframe" );
						 print_r($searchNode);
						 foreach ($xmldoc->getElementsByTagName('iframe') as $feeditem) {
							  $nodes = $feeditem->getElementsByTagName('src');
							  print_r($feeditem);
							  //echo "----".$linkthumb;
						 }*/
					}
			   }
			   //Ref: https://beneverard.co.uk/blog/php-check-if-any-part-of-a-string-is-uppercase/
			   //Ref: https://www.safaribooksonline.com/library/view/regular-expressions-cookbook/9781449327453/ch04s09.html
			   //preg_match_all("/[A-Z ]{3,100}/", $postObj->post_content, $matches);
			   //print_r($matches);
			   //Select * from camp_sites where site_description like '%iframe%iframe%'
		  }
		  
		  
		  $postObj->post_content = str_replace("\r\r","",$postObj->post_content); //\n
		  $postObj->post_content = str_replace("\r","",$postObj->post_content);	//\n
		  $headings = array(
							'FACILITIES',
							'COST' ,
							'RESERVING A CAMP SITE',
							'CAMPFIRES AND FIREWOOD',
							'BEFORE YOU VISIT',
							'NOTE:',
							'HOW TO GET THERE',
							'SITE ACCESS',
							'FEES & BOOKINGS',
							'FAMILY RATE.',
							'RULES',
							'FEES',
							//'\n\n',
							'ABOUT US',
							'FRC CLUB OFFER',
							'SERVICE OFFERED',
							'SHORTCUT',
							);
		  $new_headings = array(
							'<strong>FACILITIES</strong>',
							'<strong>COST</strong>' ,
							'<strong>RESERVING A CAMP SITE</strong>',
							'<strong>CAMPFIRES AND FIREWOOD</strong>',
							'<strong>BEFORE YOU VISIT</strong>',
							'<strong>NOTE:</strong>',
							'<strong>HOW TO GET THERE</strong>',
							'<strong>SITE ACCESS</strong>',
							'<strong>FEES & BOOKINGS</strong>',
							'<strong>FAMILY RATE.</strong>',
							'<strong>RULES</strong>',
							'<strong>FEES</strong>',
							//'\n',
							'<strong>ABOUT US</strong>',
							'<strong>FRC CLUB OFFER</strong>',
							'<strong>SERVICE OFFERED</strong>',
							'<strong>SHORTCUT</strong>',
							);
		  $postObj->post_content = str_replace($headings,$new_headings,$postObj->post_content);
		  //$postObj->post_content = str_replace("\n\n","\n", $postObj->post_content);
		  //$postObj->post_content = str_replace(array("\n\n", "\\n"),array("\n", "\n"),$postObj->post_content);
		  $postObj->post_content = str_replace(array("\\n"),array("\n"),$postObj->post_content);
		  $item['site_description'] = do_shortcode($postObj->post_content);
		  
		  $primaryFeature = array(
								  'rating' => "",
								  'address' => "",
								  'gps_lon' => "",
								  'gps_lat' => "",
								  'post_id' => "",
								  'email' => "",
								  'phone' => "",
								  'web' => "",
								  'location' => ""
								  );
		  extract($primaryFeature);
		  $itemMetaData = array();
		  $postMeta = get_post_meta($itemID); //get_category( $c ); in loop
		  $ratingMeta = get_post_meta($itemID,'rating_mean_rounded',true);
		  
		  //Start: Get camp photos
		  $item['site_photos'] =  array();
		  if(is_old_theme()){
			   $query = "SELECT * FROM  `{$wpdb->prefix}postmeta` WHERE `post_id` = $itemID AND  `meta_key` =  '_ait_dir_gallery'";//25761
			   $galleryResult = $wpdb->get_results($query, OBJECT);
			   if(count($galleryResult) >= 1){
					$site_photos = unserialize($galleryResult->meta_value);
					$photos = array();
					foreach($galleryResult as $galleryObj){
						 $galleryArr = unserialize($galleryObj->meta_value);
						 foreach($galleryArr as $photo){
							  if(!empty($photo) && !in_array($photo, $photos)){$photos[] = $photo;}
						 }
					}
			   }
			   $item['site_photos'] =  $photos;
		  }else{
			   $plusMeta = unserialize($postMeta['_ait-item_item-data'][0]);
			   if(!is_old_theme()){
					if($plusMeta['displayGallery'] == 1){
						 $photos = array();
						 foreach($plusMeta['gallery'] as $sngGallery){
							  $photo = $sngGallery['image'];
							  if(!empty($photo) && !in_array($photo, $photos)){$photos[] = $photo;}
						 }
					}
					if($plusMeta['displayOpeningHours'] == 1){
						$openingHours['displayOpeningHours'] = $plusMeta['displayOpeningHours'];
						$openingHours['openingHoursMonday'] = $plusMeta['openingHoursMonday'];
						$openingHours['openingHoursTuesday'] = $plusMeta['openingHoursTuesday'];
						$openingHours['openingHoursWednesday'] = $plusMeta['openingHoursWednesday'];
						$openingHours['openingHoursThursday'] = $plusMeta['openingHoursThursday'];
						$openingHours['openingHoursFriday'] = $plusMeta['openingHoursFriday'];
						$openingHours['openingHoursSaturday'] = $plusMeta['openingHoursSaturday'];
						$openingHours['openingHoursSunday'] = $plusMeta['openingHoursSunday'];
						$openingHours['openingHoursNote'] = $plusMeta['openingHoursNote'];
					}
					$item['site_photos'] =  $photos;
			   }
		  }
		  //End: Get camp photos
			   
		  //>>>>:Start getting rating
		  $item['rating_and_reviews'] = array();//default
		  $ratingQuery = "SELECT P.`post_date` , P.`post_content` , P.`post_title`, `P`.`post_author` , M.`post_id` FROM  `{$wpdb->prefix}posts` AS P,  `{$wpdb->prefix}postmeta` AS M WHERE  `M`.`meta_key` =  'post_id' AND  `M`.`meta_value` = $itemID AND  `M`.`post_id` =  `P`.`ID` AND `P`.`post_status` = 'publish'";
		  $ipAddress = $_SERVER['REMOTE_ADDR'];
		  if($ipAddress == '27.255.205.251'){
			   //echo "Line #273".$ratingQuery;
		  }
		  $ratingPartialResult = $wpdb->get_results($ratingQuery, OBJECT);
		  $ratingIDs = array();
		  foreach($ratingPartialResult as $ratingSngObj){
			   $ratingIDs[] = $ratingSngObj->post_id; 
		  }
		  
		  $siteRating = array();
		  if(count($ratingIDs)){
			   $ratingQry = "SELECT * FROM `{$wpdb->prefix}postmeta` WHERE `post_id` IN('".implode("','",$ratingIDs)."') AND `meta_key` = 'rating_mean'";
			   $ratingFResult = $wpdb->get_results($ratingQry, OBJECT);
			   foreach($ratingFResult as $ratingFObj){
					$siteRating[$ratingFObj->post_id] = $ratingFObj->meta_value;
			   }
		  }
		  $ratingFullArr = array();
		  $ratingAvg = $ratingSum = 0;
		  foreach($ratingPartialResult as $ratingSngObj){
			   $ratingSum += $siteRating[$ratingSngObj->post_id];
			   $item['rating_and_reviews'][] = array(
															  'rate_date' => $ratingSngObj->post_date,
															  'review' => $ratingSngObj->post_content,
															  'rating' => $siteRating[$ratingSngObj->post_id],
															  'rate_id' => $ratingSngObj->post_id,
															  'user_name' => $ratingSngObj->post_title,
															  );
		  }
		  if(count($ratingPartialResult)){
			   $ratingAvg = $ratingSum / count($ratingPartialResult);
		  }
		  $item['rating'] = round($ratingAvg, 2);
		  //<<<<:End getting rating
		  
		  //>>>>:Start getting post comments
		  $all_comments = get_comments( array('status' => 'approve', 'post_id'=> $itemID) );
		  $commentArr = array();
		  foreach($all_comments as $sngCommentObj){
			   $comm['comment_id'] = $sngCommentObj->comment_ID;
			   $comm['comment'] = $sngCommentObj->comment_content;
			   $comm['user_name'] = $sngCommentObj->comment_author;
			   $comm['comment_date'] = $sngCommentObj->comment_date; //Other: comment_date_gmt
			   $commentArr[] = $comm;
		  }
		  $item['comments'] = $commentArr;
		  //<<<<:End getting post comments
		  
		  $item['in_collection'] = 0;//N0
		  if(is_array($postMeta) && array_key_exists('_wpb_post_bookmark_count',$postMeta )){
			   if($bookmarkCounts = (int)$postMeta['_wpb_post_bookmark_count'][0]){
					$item['in_collection'] = 1;
					$item['bookmark_counts'] = $bookmarkCounts;
			   }
		  }
		  
		  $item['opening_hours'] = $openingHours;
		  $item['features'] = $features;
		  //echo "postMeta:";
		  //print_r($postMeta);die;
		  //print_r(unserialize($postMeta['_ait-item_item-extension'][0]));
		  //print_r(unserialize($postMeta['_ait-item_item-data'][0]));
		  /*
		   $postMeta['ait-item-extension-custom-field'][0]
		   $postMeta['rating_mean_rounded'][0]
		   $postMeta['rating_count'][0]
		   $postMeta['_ait-item_item-data']
		   $postMeta['_ait-item_item-featured']
		   */
		  if(is_array($postMeta) && (
										array_key_exists('_ait-item_item-data', $postMeta) ||
										array_key_exists('_ait-dir-item', $postMeta) ||
										array_key_exists('_ait-item_item-extension', $postMeta)
									 )
		  ){
			   if( array_key_exists('_ait-item_item-extension', $postMeta) ){
					$itemData = unserialize($postMeta['_ait-item_item-extension'][0]);
			   }elseif( array_key_exists('_ait-item_item-data', $postMeta) ){
					$itemData = unserialize($postMeta['_ait-item_item-data'][0]);
			   }else{
					$itemData = unserialize($postMeta['_ait-dir-item'][0]);
			   }
			   //>>>>>: Features Code
			   //echo'<pre>';print_r($itemData);die;
			   if(array_key_exists('_ait-item_item-data', $postMeta)){ //New theme
					$itemDataPlus = unserialize($postMeta['_ait-item_item-data'][0]);
					//print_r($itemDataPlus);
					$item['state'] = $item['suburb'] = $item['post_code'] = "";
					$item['suburb'] = array_key_exists('wlm_city',$itemDataPlus) ? $itemDataPlus['wlm_city']: '';
					$item['state'] = array_key_exists('wlm_state',$itemDataPlus) ? $itemDataPlus['wlm_state']: '';
					$item['post_code'] = array_key_exists('wlm_zip',$itemDataPlus) ? $itemDataPlus['wlm_zip']: '';
					$item['authority'] = array_key_exists('responsible_authority', $itemDataPlus) ? $itemDataPlus['responsible_authority']:'';
					$item['phone'] = $itemDataPlus['telephone'];
					$item['email'] = $itemDataPlus['email'];
					$item['web'] = $itemDataPlus['web'];
					$item['price'] = array_key_exists('pricing_detail',$itemDataPlus) ? $itemDataPlus['pricing_detail']:'';
					$mapData = $itemDataPlus['map'];
					$item['address'] = array_key_exists('address',$mapData) ? $mapData['address'] : '';
					$item['gps_lat'] = array_key_exists('latitude',$mapData) ? $mapData['latitude']: '';
					$item['gps_lon'] = array_key_exists('longitude',$mapData) ? $mapData['longitude']:'';
					if($itemDataPlus['discount'] == 1){
						 $itemData['discount'] = 1;
						 $itemData['discount_detail'] = $itemDataPlus['discount_detail'];
					}
					if($itemDataPlus['offer'] == 1){
						 $itemData['offer'] = 1;
						 $itemData['offer_detail'] = $itemDataPlus['offer_detail'];
					}
					if($itemDataPlus['seasonalRatesApply'] == 1){
						 $itemData['seasonalRatesApply'] = 1;
						 $itemData['seasonalRatesApply_detail'] = $itemDataPlus['seasonalRatesApply_detail'];
					}
			   }else{
					$item['state'] = $item['suburb'] = $item['post_code'] = "";
					$item['suburb'] = array_key_exists('wlm_city',$itemData) ? $itemData['wlm_city']: '';
					$item['state'] = array_key_exists('wlm_state',$itemData) ? $itemData['wlm_state']: '';
					$item['post_code'] = array_key_exists('wlm_zip',$itemData) ? $itemData['wlm_zip']: '';
					$item['authority'] = array_key_exists('responsible_authority',$itemData) ? $itemData['responsible_authority']:'';
					$item['address'] = array_key_exists('address',$itemData) ? $itemData['address'] : $itemData['map']['address'];
					$item['gps_lat'] = array_key_exists('gpsLatitude',$itemData) ? $itemData['gpsLatitude']: $itemData['map']['latitude'];
					$item['gps_lon'] = array_key_exists('gpsLongitude',$itemData) ? $itemData['gpsLongitude']:$itemData['map']['longitude'];
					$item['price'] = array_key_exists('pricing_detail',$itemData) ? $itemData['pricing_detail']:'';
					$item['phone'] = $itemData['telephone'];
					$item['email'] = $itemData['email'];
					$item['web'] = $itemData['web'];
			   }
			   $ipAddress = $_SERVER['REMOTE_ADDR'];
			   if($ipAddress == '27.255.180.89'){
					//print_r($itemData);
			   }
			   $features = array();
			   //echo'hi';die;
			   //echo'<pre>';print_r($itemData);die;
			   $features = getFeaturesData($itemData);
			   $featureArr = array(); 
			   foreach($features as $feature => $featureData){$featureArr[] = $featureData['key'];}
			   //<<<<<: Features Code
			   $item['features'] = $features;
			   $item['discount'] = array_key_exists('discount_detail',$itemData) ? $itemData['discount_detail']:'';
			   
			   $item['post_id'] = $itemID;
			   if(!is_null($distance)){
					$item['distance'] = number_format($distance,2);
			   }elseif(!empty($item['gps_lat']) && !empty($item['gps_lon']) && !empty($lat) && !empty($lon)){
					$distance = distance($item['gps_lat'], $item['gps_lon'], $lat, $lon, 'K');
					$item['distance'] = number_format($distance,2);
			   }
			   
			   if(!isset($item['distance']))$item['distance'] = 100; //needs to be updated
			   if($isOldTheme){
					if(!empty($itemData['hoursMonday']))
						 $openingHours['Monday'] = $itemData['hoursMonday'];
					if(!empty($itemData['hoursTuesday']))
						 $openingHours['Tuesday'] = $itemData['hoursTuesday'];
					if(!empty($itemData['hoursWednesday']))
						 $openingHours['Wednesday'] = $itemData['hoursWednesday'];
					if(!empty($itemData['hoursThursday']))
						 $openingHours['Thursday'] = $itemData['hoursThursday'];
					if(!empty($itemData['hoursFriday']))
						 $openingHours['Friday'] = $itemData['hoursFriday'];
					if(!empty($itemData['hoursSaturday']))
						 $openingHours['Saturday'] = $itemData['hoursSaturday'];
					if(!empty($itemData['hoursSunday']))
						 $openingHours['Sunday'] = $itemData['hoursSunday'];
			   }else{
					if($itemData['displayOpeningHours'] == 1){//openingHoursMonday => Monday
						 if(!empty($itemData['openingHoursMonday']))$openingHours['Monday'] = $itemData['openingHoursMonday'];
						 if(!empty($itemData['openingHoursTuesday']))$openingHours['Tuesday'] = $itemData['openingHoursTuesday'];
						 if(!empty($itemData['openingHoursWednesday']))$openingHours['Wednesday'] = $itemData['openingHoursWednesday'];
						 if(!empty($itemData['openingHoursThursday']))$openingHours['Thursday'] = $itemData['openingHoursThursday'];
						 if(!empty($itemData['openingHoursFriday']))$openingHours['Friday'] = $itemData['openingHoursFriday'];
						 if(!empty($itemData['openingHoursSaturday']))$openingHours['Saturday'] = $itemData['openingHoursSaturday'];
						 if(!empty($itemData['openingHoursSunday']))$openingHours['Sunday'] = '';$itemData['openingHoursSunday'];
					}
			   }
			   if(count($openingHours)){
					$item['opening_hours'] = array($openingHours);
			   }else{
					$item['opening_hours'] = array();
			   }
			   //print_r(get_object_taxonomies('ait-item'));//get_taxonomies
		  }
		  
		  //Start: Get all Item Categories associated with item
		  if($isOldTheme){
			   $ItemCats = get_the_terms($itemID, 'ait-dir-item-category');
		  }else{
			   $ItemCats = get_the_terms($itemID, 'ait-items'); //doubtful
		  }
		  $itemCatStr = "";$itemCatArr = $itemCatNameArr = array();
		  $is_frc_partner = $is_frc_partner_cat = 0;
		  //Start: New change for allowing is_partner=1 for Campground n Caravan Park
		  $partnerCatIds['Caravan Parks'] = 155;
		  $partnerCatIds['Campgrounds'] = 14;
		  //End: New change for allowing is_partner=1 for Campground n Caravan Park
		  if(is_array($ItemCats)){
			   foreach($ItemCats as $ItemCat){
					//$itemCatArr[] = $ItemCat->name; //others are slug, term_id, term_taxonomy_id
					$itemCatArr[] = $ItemCat->term_id;
					if(in_array($ItemCat->term_id, $partnerCatIds)){
						 $is_frc_partner_cat = 1;
					}
					$itemCatNameArr[] = $ItemCat->name;
			   }
		  }
		  if($is_frc_partner_cat){
			   if( is_array($featureArr) && ( in_array('discount', $featureArr) || in_array('offer', $featureArr) )){
					$is_frc_partner = 1;
			   }
		  }
		  $item['is_frc_partner'] = $is_frc_partner;
		  
		  $item['site_category_ids'] =  implode(",", $itemCatArr);
		  $rootCat = get_frc_root_category($itemCatArr);
		  $item['site_category_id'] =  $rootCat;
		  //this code needs to be modified
		  $item['site_category'] =  implode(",", $itemCatNameArr);
		  //this code needs to be modified
		  
		  //End: Get all Item Categories associated with item
		  
		  //Start: Get all Item Locations associated with item
		  if($isOldTheme){
			   $ItemCats = get_the_terms($itemID, 'ait-dir-item-location');
		  }else{
			   $ItemCats = get_the_terms($itemID, 'ait-locations');
		  }
		  $itemCatStr = "";$itemCatArr = array();
		  if(is_array($ItemCats)){
			   foreach($ItemCats as $ItemCat){
					$itemCatArr[] = $ItemCat->name; //others are slug, term_id, term_taxonomy_id
			   }
		  }
		  $item['location'] =  implode(",", $itemCatArr); //Temporary
		  //End: Get all Item Locations associated with itema
		  
		  $item['site_id'] = $itemID;
		  return $item;
	 }
	 
	 //4: Upload receipt image of a commercial site
	 function frc_upload_receipt_image($request){
		  //Documentaion: https://github.com/verot/class.upload.php/blob/master/README.md
		  $user_id = check_if_bookmark_plugin_active();
		  $upload_dir = wp_upload_dir();
		  $campID =  (int)$request['id'];
		  if ( 'publish' == get_post_status ( $campID ) ) {
			   $campDir = $upload_dir['basedir'].DS.'camp_receipts'.DS.$campID.DS.$user_id;
			   if ( ! file_exists( $campDir ) ) {
					wp_mkdir_p( $campDir );
			   }
			   $dataArr = array('upload_dir' => $campDir);
			   $handle = new upload($_FILES['photo']);
			   //$_FILES['name'], $_FILES['type'],$_FILES['size']
			   
			   if ($handle->uploaded) {
					$handle->file_new_name_body   = time();
					$handle->image_resize         = true;
					$handle->image_x              = 900;
					$handle->image_ratio_y        = true;
					$handle->image_text           = 'https://freerangecamping.com.au/';
					$handle->image_text_x         = -5;
					$handle->image_text_y         = -5;
					$handle->image_text_padding    = 5;
					$handle->image_text_color = '#FF0000';
					$handle->file_name_body_pre = 'Image_';
					$handle->image_no_enlarging = true; //if image is small than required size
					$handle->file_max_size = '2097152'; // 2048KB
					//http://www.whatsabyte.com/P1/byteconverter.htm
					//$handle->image_no_shrinking = true; 
					$handle->image_bevel = 20;
					$handle->allowed = array('image/*');
					/*
					$foo->image_text_color      = '#000000';
					$foo->image_text_background = '#FFFFFF';
					$foo->image_text_background_opacity = 60;
					$foo->image_text_padding    = 3;
					$foo->image_text_font       = 3;
					$foo->image_text_alignment  = 'R';
					$foo->image_text_direction  = 'V';
					*/
					$handle->process($campDir);
					if ($handle->processed) {
						 $handle->clean();
						 echo json_encode(array('status_code' => 200));die;
					} else {
						 echo json_encode(array('message' => $handle->error,'status_code' => 400  ));die;
					}
			   }else{
					echo json_encode(array('message' => 'Receipt file is missing','status_code' => 400  ));die;
			   }
		  }else{
			   echo json_encode(array('message' => "Either invalid site id or site not published",'status_code' => 404  ));die;
		  }
     }
	 
	 //5: Rate a camping site
	 function frc_add_rating($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $itemID =  (int)$request['id'];
		  extract($request->get_params());
		  if(empty($price) || empty($location) || empty($facilities) || empty($service)){
			   echo json_encode(array('message' => "Price/Location/Facility/Service rating is required.",'status_code' => 404  ));die;
		  }else{
				$postObj = get_post($itemID);
				if(is_object($postObj)){
					$userObj = $user = get_userdata($user_id);
					$userEmail = $userObj->user_email;
					$postStatus = (isset($aitThemeOptions->rating->ratingMustApprove)) ? 'pending' : 'publish';
					$rating = array(
										'post_author'    => $user_id, //check this 
										'post_title'     => $user->user_login,
										'post_content'   => empty($review) ? '':$review,
										'post_status'    => $postStatus,
										'post_type'      => 'ait-rating',
										'comment_status' => 'closed',
										'ping_status'    => 'closed'
							  );
					$sum = 0;
					//print_r($rating);die;
					$ratingId = wp_insert_post($rating);
					if($ratingId){
						 update_post_meta($ratingId, 'rating_1', $price);
						 $sum += intval($price);
						 update_post_meta($ratingId, 'rating_2', $location);
						 $sum += intval($location);
						 update_post_meta($ratingId, 'rating_3', $facilities);
						 $sum += intval($facilities);
						 update_post_meta($ratingId, 'rating_4', $service);
						 $sum += intval($service);
						 $mean = $sum / 4;
						 update_post_meta($ratingId, 'rating_mean' , $mean);
						 update_post_meta($ratingId, 'rating_mean_rounded' , round($mean));
						 $status = update_post_meta($ratingId, 'post_id', $itemID); //newly added on Feb 8, 2017
						 
						 $headers = array('Content-Type: text/html; charset=UTF-8');
						 $headers[] = 'Cc: Glen Wilson <info@freerangecamping.com.au>';
						 $postTitle = get_the_title($itemID);
						 $postLink = get_permalink($itemID);
						 $user_login = "<a href='mailto:$userEmail?Subject=Re: For review submitted for Site: $postTitle'>".$userObj->user_login."</a>";
						 $emailBody = "User {$user_login} have just submitted review for site:<a href='$postLink'>{$postTitle}</a><br/><br>Site ID: <a href='$postLink'>$itemID</a><br>User ID: $user_id<br/><br>User Email: $userEmail<br/><br>Please review!";
						 wp_mail('kalyanrajiv@gmail.com', "Review Submissions from App", $emailBody, $headers);
						 
						 return new WP_REST_Response(array('status_code' => 200, 'data' => array(
																								 'rate_id' => $ratingId,
																								 'user_id' => $user_id,
																								 'price' => $price,
																								 'location' => $location,
																								 'facilities' => $facilities,
																								 'service' => $service,
																								 'review' => $review,
																								 //'status' => $status,//temporary
																								 )), 200);
					}else{
						 echo json_encode(array('message' => "Failed to rate camp site",'status_code' => 400  ));die;
					}
			   }else{
					echo json_encode(array('message' => "No camping site found.",'status_code' => 404  ));die;
			   }
		  }
	 }
	 
	 //5 Plus: Rate a camping site
	 function frc_add_rating_plus($request){
		  if(check_if_ait_review_plugin_active()){
			 $user_id = get_userid_if_valid_token();  
		  }
		  $itemID =  (int)$request['id'];
		  extract($request->get_params());
		  if(empty($price) || empty($location) || empty($facilities) || empty($service)){
			   echo json_encode(array('message' => "Price/Location/Facility/Service rating is required.",'status_code' => 404  ));die;
		  }else{
				$postObj = get_post($itemID);
				if(is_object($postObj)){
					$userObj = $user = get_userdata($user_id);
					$userEmail = $userObj->user_email;
					
					global $aitThemeOptions;
					//'guid' => https://www.freerangecamping.co.nz/frc2/?post_type=ait-review&p=4965
					$ratingArr = array(
										array('question' => 'Service', 'value' => $service),
										array('question' => 'Facilities', 'value' => $facilities),
										array('question' => 'Location', 'value' => $location),
										array('question' => 'Price', 'value' => $price),
									   );
					$postStatus = 'pending';
					$rating = array(
										'post_author'    => $user_id, //check this 
										'post_title'     => $user->user_login,
										'post_content'   => empty($review) ? '':$review,
										'post_status'    => $postStatus,
										'post_type'      => 'ait-review',
										'comment_status' => 'closed',
										'ping_status'    => 'closed'
							  );
					$review_id = $ratingId = wp_insert_post($rating);
					if($ratingId){
						 // update review post meta
						 update_post_meta($ratingId, 'post_id' , $itemID);
						 if (version_compare(PHP_VERSION, '5.4.0') >= 0){
							  update_post_meta($review_id, 'ratings' , json_encode($ratingArr, JSON_UNESCAPED_UNICODE));
						 }else{
							  update_post_meta($review_id, 'ratings' , AitItemReviews::raw_json_encode($ratingArr));
						 }
						 // rating mean
						 $rating_sum = $rating_count = 0;
						 foreach(array($price, $location, $facilities, $service) as $ratingVal){
							  $rating_sum += intval($ratingVal);
							  $rating_count += 1;
						 }
						 $rating_mean = $rating_sum / $rating_count;
						 update_post_meta($review_id, 'rating_mean' , $rating_mean);
						 update_post_meta($review_id, 'rating_mean_rounded' , round($rating_mean));
						 $postTitle = $itemTitle = get_the_title( $itemID);
						 $bulkMessage = sprintf(__('Review <a href="%s">#%d</a> for item <a href="%s">%s</a> is waiting for your moderation', 'ait-item-reviews'), admin_url('edit.php?post_type=ait-review'), $ratingId, admin_url("post.php?post=$itemID&action=edit"), $itemTitle);
							 
						//---------------------------------------
						 
						 $headers = array('Content-Type: text/html; charset=UTF-8');
						 $headers[] = 'Cc: Glen Wilson <info@freerangecamping.com.au>';
						 $postLink = get_permalink($itemID);
						 $user_login = "<a href='mailto:$userEmail?Subject=Re: For review submitted for Site: $postTitle'>".$userObj->user_login."</a>";
						 $emailBody = "$bulkMessage<br/><br/>User {$user_login} have just submitted review for site:<a href='$postLink'>{$postTitle}</a><br/><br>Site ID: <a href='$postLink'>$itemID</a><br>User ID: $user_id<br/><br>User Email: $userEmail<br/><br>Please review!";
						 $subjectOld = "Review Submissions from App";
						 $subject = "Review pending moderation";
						 
						 wp_mail('kalyanrajiv@gmail.com', "Review Submissions from App", $emailBody, $headers);
						 
						 return new WP_REST_Response(array('status_code' => 200, 'data' => array(
																								 'rate_id' => $ratingId,
																								 'user_id' => $user_id,
																								 'price' => $price,
																								 'location' => $location,
																								 'facilities' => $facilities,
																								 'service' => $service,
																								 'review' => $review,
																								 //'status' => $status,//temporary
																								 )), 200);
					}else{
						 echo json_encode(array('message' => "Failed to rate camp site",'status_code' => 400  ));die;
					}
			   }else{
					echo json_encode(array('message' => "No camping site found.",'status_code' => 404  ));die;
			   }
		  }
	 }
	 
	 //6: Add comment to camping site
	 function frc_add_comment($request){
		  $user_id = check_if_bookmark_plugin_active();
		  $itemID =  (int)$request['id'];
		  extract($request->get_params());
		  if(isset($comment) && !empty($comment)){
			   $postObj = get_post($itemID);
			   if(is_object($postObj)){
					$user = get_userdata($user_id);
					$time = current_time('mysql');
					$data = array(
						 'comment_post_ID' => $itemID,
						 'comment_author' => $user->user_login,
						 'comment_author_email' => $user->user_email,
						 'comment_author_url' => 'http://',
						 'comment_content' => $comment,
						 'comment_type' => '',
						 'comment_parent' => 0,
						 'user_id' => $user_id,
						 'comment_author_IP' => '127.0.0.1',
						 'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
						 'comment_date' => $time,
						 'comment_approved' => 0,
					);
					 
					if($commentID = wp_insert_comment($data)){
						 return new WP_REST_Response(array('status_code' => 200, 'data' => array(
																								 'comment_id' => $commentID,
																								 'user_id' => $user_id,
																								 'comment' => $comment,
																								 'approved' => false)), 200);
					}else{
						 echo json_encode(array('message' => "Failed to post comment",'status_code' => 400  ));die;
					}
			   }else{
					echo json_encode(array('message' => "No camping site found.",'status_code' => 404  ));die;
			   }
		  }else{
			   echo json_encode(array('message' => "comment is required.",'status_code' => 404  ));die;
		  }
	 }
	 
	 //7: Report error of a camping site
	 function frc_report_campsite($request){
		  global $wpdb;
		  $user_id = check_if_bookmark_plugin_active();
		  $itemID =  (int)$request['id'];
		  $errorMsg =  $request['error'];
		  $created = current_time('mysql');
		  if(empty($errorMsg)){
			   echo json_encode(array('message' => "Error is required",'status_code' => 400  ));die;
		  }elseif ( 'publish' == get_post_status ( $itemID ) ) {
			   
			   //echo json_encode(array('message' => "Failed to report error, please retry!",'status_code' => 400  ));die;
			   $status = $wpdb->insert( 'rasu_report_errors',
										array( 'error' => $errorMsg, 'post_id' => $itemID, 'user_id' => $user_id, 'created' => $created),
										array( '%s', '%d', '%d', '%s' )
							);
			   if($status){
					$lastid = $wpdb->insert_id;
					$data = array('user_id' => $user_id, 'error_id' => $lastid, 'error' => $errorMsg);
					return new WP_REST_Response(array('status_code' => 200, 'data' => $data), 200);
			   }else{
					echo json_encode(array('message' => "Failed to report error, please retry!",'status_code' => 400  ));die;
			   }
		  }else{
			    echo json_encode(array('message' => "Either invalid site id or site not published",'status_code' => 404  ));die;
		  }
     }
	 
	 function getFeaturesDataPlus($itemData){
		  global $featureArr,$additionalArr;
		  $features = array();
		  if(array_key_exists('onoff', $itemData)){
			   $onoffArr = $itemData['onoff'];
			   $txtArr = $itemData['text'];
			   reset($txtArr);
			   $counter = 0;
			   foreach($onoffArr as $feature => $switch){
					$addText = current($txtArr);
					if($switch == 'on'){
						 $featureStr = $featureArr[$feature];
						 $features[] = array($featureStr,$addText);
					}
					next($txtArr);
					$counter++;
			   }
			   $miscFeatures = array('discount', 'offer', 'seasonalRatesApply');
			   foreach($miscFeatures as $miscFeature){
					if(array_key_exists($miscFeature, $itemData) && $itemData[$miscFeature] == 1){
						 $addnl = $miscFeature."_detail";
						 $features[] = array($miscFeature, $itemData[$addnl]);
					}
			   }
		  }
		  $featureObjs = array();
		  foreach($features as $feature){
			   $object = new stdClass();
			   $key = $feature[0];
			   $object->$key = $feature[1];
			   $featureObjs[] = array('key' => $key, 'value' => $feature[1]);
		  }
		  return $featureObjs;
	 }
	 //sub function
	 function getFeaturesData($itemData){
		  if(!is_array($itemData)){ return array();}
		  if(!is_old_theme()){return getFeaturesDataPlus($itemData);}
		  //Feature 1
		  $features = array();
		  //Feature 1
        if(array_key_exists('toilets', $itemData) &&
		    ((is_array($itemData['toilets']) && array_key_exists('enable', $itemData['toilets']) && $itemData['toilets']['enable'] == 'enable') || strtolower($itemData['toilets']) == 'y')
		){
             $features[] = array('toilets',(string)$itemData['toilets_addnl']);
        }elseif(array_key_exists('toilets_addnl', $itemData) && trim($itemData['toilets_addnl']) != ""){
             $features[] = array('toilets',(string)$itemData['toilets_addnl']);
        }         
		//Feature 2
        if(array_key_exists('disableFacility', $itemData) &&
		    ((is_array($itemData['disableFacility']) && array_key_exists('enable', $itemData['disableFacility']) && $itemData['disableFacility']['enable'] == 'enable') || strtolower($itemData['disableFacility']) == 'y')
		){
             $features[] = array('disableFacility',(string)$itemData['disableFacility_addnl']);
        }elseif(array_key_exists('disableFacility_addnl', $itemData) && trim($itemData['disableFacility_addnl']) != ""){
             $features[] = array('disableFacility',(string)$itemData['disableFacility_addnl']);
        }         
		//Feature 3
        if(array_key_exists('showers', $itemData) &&
		    ((is_array($itemData['showers']) && array_key_exists('enable', $itemData['showers']) && $itemData['showers']['enable'] == 'enable') || strtolower($itemData['showers']) == 'y')
		){
             $features[] = array('showers',(string)$itemData['showers_addnl']);
        }elseif(array_key_exists('showers_addnl', $itemData) && trim($itemData['showers_addnl']) != ""){
             $features[] = array('showers',(string)$itemData['showers_addnl']);
        }         
		//Feature 4
        if(array_key_exists('drinkingWater', $itemData) &&
		    ((is_array($itemData['drinkingWater']) && array_key_exists('enable', $itemData['drinkingWater']) && $itemData['drinkingWater']['enable'] == 'enable') || strtolower($itemData['drinkingWater']) == 'y')
		){
             $features[] = array('drinkingWater',(string)$itemData['drinkingWater_addnl']);
        }elseif(array_key_exists('drinkingWater_addnl', $itemData) && trim($itemData['drinkingWater_addnl']) != ""){
             $features[] = array('drinkingWater',(string)$itemData['drinkingWater_addnl']);
        }         
		//Feature 5
        if(array_key_exists('drinkingWaterNotSuitable', $itemData) &&
		    ((is_array($itemData['drinkingWaterNotSuitable']) && array_key_exists('enable', $itemData['drinkingWaterNotSuitable']) && $itemData['drinkingWaterNotSuitable']['enable'] == 'enable') || strtolower($itemData['drinkingWaterNotSuitable']) == 'y')
		){
             $features[] = array('drinkingWaterNotSuitable',(string)$itemData['drinkingWaterNotSuitable_addnl']);
        }elseif(array_key_exists('drinkingWaterNotSuitable_addnl', $itemData) && trim($itemData['drinkingWaterNotSuitable_addnl']) != ""){
             $features[] = array('drinkingWaterNotSuitable',(string)$itemData['drinkingWaterNotSuitable_addnl']);
        }         
		//Feature 6
        if(array_key_exists('waterFillingAvailable', $itemData) &&
		    ((is_array($itemData['waterFillingAvailable']) && array_key_exists('enable', $itemData['waterFillingAvailable']) && $itemData['waterFillingAvailable']['enable'] == 'enable') || strtolower($itemData['waterFillingAvailable']) == 'y')
		){
             $features[] = array('waterFillingAvailable',(string)$itemData['waterFillingAvailable_addnl']);
        }elseif(array_key_exists('waterFillingAvailable_addnl', $itemData) && trim($itemData['waterFillingAvailable_addnl']) != ""){
             $features[] = array('waterFillingAvailable',(string)$itemData['waterFillingAvailable_addnl']);
        }         
		//Feature 7
        if(array_key_exists('wasteFacilityAvailable', $itemData) &&
		    ((is_array($itemData['wasteFacilityAvailable']) && array_key_exists('enable', $itemData['wasteFacilityAvailable']) && $itemData['wasteFacilityAvailable']['enable'] == 'enable') || strtolower($itemData['wasteFacilityAvailable']) == 'y')
		){
             $features[] = array('wasteFacilityAvailable',(string)$itemData['wasteFacilityAvailable_addnl']);
        }elseif(array_key_exists('wasteFacilityAvailable_addnl', $itemData) && trim($itemData['wasteFacilityAvailable_addnl']) != ""){
             $features[] = array('wasteFacilityAvailable',(string)$itemData['wasteFacilityAvailable_addnl']);
        }         
		//Feature 8
        if(array_key_exists('dumpPointAvailable', $itemData) &&
		    ((is_array($itemData['dumpPointAvailable']) && array_key_exists('enable', $itemData['dumpPointAvailable']) && $itemData['dumpPointAvailable']['enable'] == 'enable') || strtolower($itemData['dumpPointAvailable']) == 'y')
		){
             $features[] = array('dumpPointAvailable',(string)$itemData['dumpPointAvailable_addnl']);
        }elseif(array_key_exists('dumpPointAvailable_addnl', $itemData) && trim($itemData['dumpPointAvailable_addnl']) != ""){
             $features[] = array('dumpPointAvailable',(string)$itemData['dumpPointAvailable_addnl']);
        }         
		//Feature 9
        if(array_key_exists('poweredSites', $itemData) &&
		    ((is_array($itemData['poweredSites']) && array_key_exists('enable', $itemData['poweredSites']) && $itemData['poweredSites']['enable'] == 'enable') || strtolower($itemData['poweredSites']) == 'y')
		){
             $features[] = array('poweredSites',(string)$itemData['poweredSites_addnl']);
        }elseif(array_key_exists('poweredSites_addnl', $itemData) && trim($itemData['poweredSites_addnl']) != ""){
             $features[] = array('poweredSites',(string)$itemData['poweredSites_addnl']);
        }         
		//Feature 10
        if(array_key_exists('emergencyPhone', $itemData) &&
		    ((is_array($itemData['emergencyPhone']) && array_key_exists('enable', $itemData['emergencyPhone']) && $itemData['emergencyPhone']['enable'] == 'enable') || strtolower($itemData['emergencyPhone']) == 'y')
		){
             $features[] = array('emergencyPhone',(string)$itemData['emergencyPhone_addnl']);
        }elseif(array_key_exists('emergencyPhone_addnl', $itemData) && trim($itemData['emergencyPhone_addnl']) != ""){
             $features[] = array('emergencyPhone',(string)$itemData['emergencyPhone_addnl']);
        }         
		//Feature 11
        if(array_key_exists('mobilePhoneReception', $itemData) &&
		    ((is_array($itemData['mobilePhoneReception']) && array_key_exists('enable', $itemData['mobilePhoneReception']) && $itemData['mobilePhoneReception']['enable'] == 'enable') || strtolower($itemData['mobilePhoneReception']) == 'y')
		){
             $features[] = array('mobilePhoneReception',(string)$itemData['mobilePhoneReception_addnl']);
        }elseif(array_key_exists('mobilePhoneReception_addnl', $itemData) && trim($itemData['mobilePhoneReception_addnl']) != ""){
             $features[] = array('mobilePhoneReception',(string)$itemData['mobilePhoneReception_addnl']);
        }         
		//Feature 12
        if(array_key_exists('petsOk', $itemData) &&
		    ((is_array($itemData['petsOk']) && array_key_exists('enable', $itemData['petsOk']) && $itemData['petsOk']['enable'] == 'enable') || strtolower($itemData['petsOk']) == 'y')
		){
             $features[] = array('petsOk',(string)$itemData['petsOk_addnl']);
        }elseif(array_key_exists('petsOk_addnl', $itemData) && trim($itemData['petsOk_addnl']) != ""){
             $features[] = array('petsOk',(string)$itemData['petsOk_addnl']);
        }         
		//Feature 13
        if(array_key_exists('petsNotOk', $itemData) &&
		    ((is_array($itemData['petsNotOk']) && array_key_exists('enable', $itemData['petsNotOk']) && $itemData['petsNotOk']['enable'] == 'enable') || strtolower($itemData['petsNotOk']) == 'y')
		){
             $features[] = array('petsNotOk',(string)$itemData['petsNotOk_addnl']);
        }elseif(array_key_exists('petsNotOk_addnl', $itemData) && trim($itemData['petsNotOk_addnl']) != ""){
             $features[] = array('petsNotOk',(string)$itemData['petsNotOk_addnl']);
        }         
		//Feature 14
        if(array_key_exists('fourWheelDrive', $itemData) &&
		    ((is_array($itemData['fourWheelDrive']) && array_key_exists('enable', $itemData['fourWheelDrive']) && $itemData['fourWheelDrive']['enable'] == 'enable') || strtolower($itemData['fourWheelDrive']) == 'y')
		){
             $features[] = array('fourWheelDrive',(string)$itemData['fourWheelDrive_addnl']);
        }elseif(array_key_exists('fourWheelDrive_addnl', $itemData) && trim($itemData['fourWheelDrive_addnl']) != ""){
             $features[] = array('fourWheelDrive',(string)$itemData['fourWheelDrive_addnl']);
        }         
		//Feature 15
        if(array_key_exists('dryWeatherOnlyAccess', $itemData) &&
		    ((is_array($itemData['dryWeatherOnlyAccess']) && array_key_exists('enable', $itemData['dryWeatherOnlyAccess']) && $itemData['dryWeatherOnlyAccess']['enable'] == 'enable') || strtolower($itemData['dryWeatherOnlyAccess']) == 'y')
		){
             $features[] = array('dryWeatherOnlyAccess',(string)$itemData['dryWeatherOnlyAccess_addnl']);
        }elseif(array_key_exists('dryWeatherOnlyAccess_addnl', $itemData) && trim($itemData['dryWeatherOnlyAccess_addnl']) != ""){
             $features[] = array('dryWeatherOnlyAccess',(string)$itemData['dryWeatherOnlyAccess_addnl']);
        }         
		//Feature 16
        if(array_key_exists('tentsOnly', $itemData) &&
		    ((is_array($itemData['tentsOnly']) && array_key_exists('enable', $itemData['tentsOnly']) && $itemData['tentsOnly']['enable'] == 'enable') || strtolower($itemData['tentsOnly']) == 'y')
		){
             $features[] = array('tentsOnly',(string)$itemData['tentsOnly_addnl']);
        }elseif(array_key_exists('tentsOnly_addnl', $itemData) && trim($itemData['tentsOnly_addnl']) != ""){
             $features[] = array('tentsOnly',(string)$itemData['tentsOnly_addnl']);
        }         
		//Feature 17
        if(array_key_exists('noTents', $itemData) &&
		    ((is_array($itemData['noTents']) && array_key_exists('enable', $itemData['noTents']) && $itemData['noTents']['enable'] == 'enable') || strtolower($itemData['noTents']) == 'y')
		){
             $features[] = array('noTents',(string)$itemData['noTents_addnl']);
        }elseif(array_key_exists('noTents_addnl', $itemData) && trim($itemData['noTents_addnl']) != ""){
             $features[] = array('noTents',(string)$itemData['noTents_addnl']);
        }         
		//Feature 18
        if(array_key_exists('camperTrailers', $itemData) &&
		    ((is_array($itemData['camperTrailers']) && array_key_exists('enable', $itemData['camperTrailers']) && $itemData['camperTrailers']['enable'] == 'enable') || strtolower($itemData['camperTrailers']) == 'y')
		){
             $features[] = array('camperTrailers',(string)$itemData['camperTrailers_addnl']);
        }elseif(array_key_exists('camperTrailers_addnl', $itemData) && trim($itemData['camperTrailers_addnl']) != ""){
             $features[] = array('camperTrailers',(string)$itemData['camperTrailers_addnl']);
        }         
		//Feature 19
        if(array_key_exists('noCamperTrailers', $itemData) &&
		    ((is_array($itemData['noCamperTrailers']) && array_key_exists('enable', $itemData['noCamperTrailers']) && $itemData['noCamperTrailers']['enable'] == 'enable') || strtolower($itemData['noCamperTrailers']) == 'y')
		){
             $features[] = array('noCamperTrailers',(string)$itemData['noCamperTrailers_addnl']);
        }elseif(array_key_exists('noCamperTrailers_addnl', $itemData) && trim($itemData['noCamperTrailers_addnl']) != ""){
             $features[] = array('noCamperTrailers',(string)$itemData['noCamperTrailers_addnl']);
        }         
		//Feature 20
        if(array_key_exists('caravans', $itemData) &&
		    ((is_array($itemData['caravans']) && array_key_exists('enable', $itemData['caravans']) && $itemData['caravans']['enable'] == 'enable') || strtolower($itemData['caravans']) == 'y')
		){
             $features[] = array('caravans',(string)$itemData['caravans_addnl']);
        }elseif(array_key_exists('caravans_addnl', $itemData) && trim($itemData['caravans_addnl']) != ""){
             $features[] = array('caravans',(string)$itemData['caravans_addnl']);
        }         
		//Feature 21
        if(array_key_exists('largeSizeMotohomeAccess', $itemData) &&
		    ((is_array($itemData['largeSizeMotohomeAccess']) && array_key_exists('enable', $itemData['largeSizeMotohomeAccess']) && $itemData['largeSizeMotohomeAccess']['enable'] == 'enable') || strtolower($itemData['largeSizeMotohomeAccess']) == 'y')
		){
             $features[] = array('largeSizeMotohomeAccess',(string)$itemData['largeSizeMotohomeAccess_addnl']);
        }elseif(array_key_exists('largeSizeMotohomeAccess_addnl', $itemData) && trim($itemData['largeSizeMotohomeAccess_addnl']) != ""){
             $features[] = array('largeSizeMotohomeAccess',(string)$itemData['largeSizeMotohomeAccess_addnl']);
        }         
		//Feature 22
        if(array_key_exists('bigrig', $itemData) &&
		    ((is_array($itemData['bigrig']) && array_key_exists('enable', $itemData['bigrig']) && $itemData['bigrig']['enable'] == 'enable') || strtolower($itemData['bigrig']) == 'y')
		){
             $features[] = array('bigrig',(string)$itemData['bigrig_addnl']);
        }elseif(array_key_exists('bigrig_addnl', $itemData) && trim($itemData['bigrig_addnl']) != ""){
             $features[] = array('bigrig',(string)$itemData['bigrig_addnl']);
        }         
		//Feature 23
        if(array_key_exists('selfContainedVehicles', $itemData) &&
		    ((is_array($itemData['selfContainedVehicles']) && array_key_exists('enable', $itemData['selfContainedVehicles']) && $itemData['selfContainedVehicles']['enable'] == 'enable') || strtolower($itemData['selfContainedVehicles']) == 'y')
		){
             $features[] = array('selfContainedVehicles',(string)$itemData['selfContainedVehicles_addnl']);
        }elseif(array_key_exists('selfContainedVehicles_addnl', $itemData) && trim($itemData['selfContainedVehicles_addnl']) != ""){
             $features[] = array('selfContainedVehicles',(string)$itemData['selfContainedVehicles_addnl']);
        }         
		//Feature 24
        if(array_key_exists('onSiteAccomodation', $itemData) &&
		    ((is_array($itemData['onSiteAccomodation']) && array_key_exists('enable', $itemData['onSiteAccomodation']) && $itemData['onSiteAccomodation']['enable'] == 'enable') || strtolower($itemData['onSiteAccomodation']) == 'y')
		){
             $features[] = array('onSiteAccomodation',(string)$itemData['onSiteAccomodation_addnl']);
        }elseif(array_key_exists('onSiteAccomodation_addnl', $itemData) && trim($itemData['onSiteAccomodation_addnl']) != ""){
             $features[] = array('onSiteAccomodation',(string)$itemData['onSiteAccomodation_addnl']);
        }         
		//Feature 25
        if(array_key_exists('seasonalRatesApply', $itemData) &&
		    ((is_array($itemData['seasonalRatesApply']) && array_key_exists('enable', $itemData['seasonalRatesApply']) && $itemData['seasonalRatesApply']['enable'] == 'enable') || strtolower($itemData['seasonalRatesApply']) == 'y')
		){
             $features[] = array('seasonalRatesApply',(string)$itemData['seasonalRatesApply_addnl']);
        }elseif(array_key_exists('seasonalRatesApply_addnl', $itemData) && trim($itemData['seasonalRatesApply_addnl']) != ""){
             $features[] = array('seasonalRatesApply',(string)$itemData['seasonalRatesApply_addnl']);
        }         
		//Feature 26
        if(array_key_exists('discount', $itemData) &&
		    ((is_array($itemData['discount']) && array_key_exists('enable', $itemData['discount']) && $itemData['discount']['enable'] == 'enable') || strtolower($itemData['discount']) == 'y')
		){
             $features[] = array('discount',(string)$itemData['discount_addnl']);
        }elseif(array_key_exists('discount_addnl', $itemData) && trim($itemData['discount_addnl']) != ""){
             $features[] = array('discount',(string)$itemData['discount_addnl']);
        }         
		//Feature 27
        if(array_key_exists('offer', $itemData) &&
		    ((is_array($itemData['offer']) && array_key_exists('enable', $itemData['offer']) && $itemData['offer']['enable'] == 'enable') || strtolower($itemData['offer']) == 'y')
		){
			   if(array_key_exists('offer_detail', $itemData)){
					$features[] = array('offer',(string)$itemData['offer_detail']);
			   }else{
					$features[] = array('offer',(string)$itemData['offer_addnl']);
			   }
        }elseif(array_key_exists('offer_addnl', $itemData) && trim($itemData['offer_addnl']) != ""){
             $features[] = array('offer',(string)$itemData['offer_addnl']);
        }
		//Feature 28
        if(array_key_exists('rvTurnAroundArea', $itemData) &&
		    ((is_array($itemData['rvTurnAroundArea']) && array_key_exists('enable', $itemData['rvTurnAroundArea']) && $itemData['rvTurnAroundArea']['enable'] == 'enable') || strtolower($itemData['rvTurnAroundArea']) == 'y')
		){
             $features[] = array('rvTurnAroundArea',(string)$itemData['rvTurnAroundArea_addnl']);
        }elseif(array_key_exists('rvTurnAroundArea_addnl', $itemData) && trim($itemData['rvTurnAroundArea_addnl']) != ""){
             $features[] = array('rvTurnAroundArea',(string)$itemData['rvTurnAroundArea_addnl']);
        }         
		//Feature 29
        if(array_key_exists('rvParkingAvailable', $itemData) &&
		    ((is_array($itemData['rvParkingAvailable']) && array_key_exists('enable', $itemData['rvParkingAvailable']) && $itemData['rvParkingAvailable']['enable'] == 'enable') || strtolower($itemData['rvParkingAvailable']) == 'y')
		){
             $features[] = array('rvParkingAvailable',(string)$itemData['rvParkingAvailable_addnl']);
        }elseif(array_key_exists('rvParkingAvailable_addnl', $itemData) && trim($itemData['rvParkingAvailable_addnl']) != ""){
             $features[] = array('rvParkingAvailable',(string)$itemData['rvParkingAvailable_addnl']);
        }         
		//Feature 30
        if(array_key_exists('boatRamp', $itemData) &&
		    ((is_array($itemData['boatRamp']) && array_key_exists('enable', $itemData['boatRamp']) && $itemData['boatRamp']['enable'] == 'enable') || strtolower($itemData['boatRamp']) == 'y')
		){
             $features[] = array('boatRamp',(string)$itemData['boatRamp_addnl']);
        }elseif(array_key_exists('boatRamp_addnl', $itemData) && trim($itemData['boatRamp_addnl']) != ""){
             $features[] = array('boatRamp',(string)$itemData['boatRamp_addnl']);
        }         
		//Feature 31
        if(array_key_exists('timeLimitApplies', $itemData) &&
		    ((is_array($itemData['timeLimitApplies']) && array_key_exists('enable', $itemData['timeLimitApplies']) && $itemData['timeLimitApplies']['enable'] == 'enable') || strtolower($itemData['timeLimitApplies']) == 'y')
		){
             $features[] = array('timeLimitApplies',(string)$itemData['timeLimitApplies_addnl']);
        }elseif(array_key_exists('timeLimitApplies_addnl', $itemData) && trim($itemData['timeLimitApplies_addnl']) != ""){
             $features[] = array('timeLimitApplies',(string)$itemData['timeLimitApplies_addnl']);
        }         
		//Feature 32
        if(array_key_exists('shadedSites', $itemData) &&
		    ((is_array($itemData['shadedSites']) && array_key_exists('enable', $itemData['shadedSites']) && $itemData['shadedSites']['enable'] == 'enable') || strtolower($itemData['shadedSites']) == 'y')
		){
             $features[] = array('shadedSites',(string)$itemData['shadedSites_addnl']);
        }elseif(array_key_exists('shadedSites_addnl', $itemData) && trim($itemData['shadedSites_addnl']) != ""){
             $features[] = array('shadedSites',(string)$itemData['shadedSites_addnl']);
        }         
		//Feature 33
        if(array_key_exists('firePit', $itemData) &&
		    ((is_array($itemData['firePit']) && array_key_exists('enable', $itemData['firePit']) && $itemData['firePit']['enable'] == 'enable') || strtolower($itemData['firePit']) == 'y')
		){
             $features[] = array('firePit',(string)$itemData['firePit_addnl']);
        }elseif(array_key_exists('firePit_addnl', $itemData) && trim($itemData['firePit_addnl']) != ""){
             $features[] = array('firePit',(string)$itemData['firePit_addnl']);
        }         
		//Feature 34
        if(array_key_exists('noFirePit', $itemData) &&
		    ((is_array($itemData['noFirePit']) && array_key_exists('enable', $itemData['noFirePit']) && $itemData['noFirePit']['enable'] == 'enable') || strtolower($itemData['noFirePit']) == 'y')
		){
             $features[] = array('noFirePit',(string)$itemData['noFirePit_addnl']);
        }elseif(array_key_exists('noFirePit_addnl', $itemData) && trim($itemData['noFirePit_addnl']) != ""){
             $features[] = array('noFirePit',(string)$itemData['noFirePit_addnl']);
        }         
		//Feature 35
        if(array_key_exists('picnicTable', $itemData) &&
		    ((is_array($itemData['picnicTable']) && array_key_exists('enable', $itemData['picnicTable']) && $itemData['picnicTable']['enable'] == 'enable') || strtolower($itemData['picnicTable']) == 'y')
		){
             $features[] = array('picnicTable',(string)$itemData['picnicTable_addnl']);
        }elseif(array_key_exists('picnicTable_addnl', $itemData) && trim($itemData['picnicTable_addnl']) != ""){
             $features[] = array('picnicTable',(string)$itemData['picnicTable_addnl']);
        }         
		//Feature 36
        if(array_key_exists('bbq', $itemData) &&
		    ((is_array($itemData['bbq']) && array_key_exists('enable', $itemData['bbq']) && $itemData['bbq']['enable'] == 'enable') || strtolower($itemData['bbq']) == 'y')
		){
             $features[] = array('bbq',(string)$itemData['bbq_addnl']);
        }elseif(array_key_exists('bbq_addnl', $itemData) && trim($itemData['bbq_addnl']) != ""){
             $features[] = array('bbq',(string)$itemData['bbq_addnl']);
        }         
		//Feature 37
        if(array_key_exists('shelter', $itemData) &&
		    ((is_array($itemData['shelter']) && array_key_exists('enable', $itemData['shelter']) && $itemData['shelter']['enable'] == 'enable') || strtolower($itemData['shelter']) == 'y')
		){
             $features[] = array('shelter',(string)$itemData['shelter_addnl']);
        }elseif(array_key_exists('shelter_addnl', $itemData) && trim($itemData['shelter_addnl']) != ""){
             $features[] = array('shelter',(string)$itemData['shelter_addnl']);
        }         
		//Feature 38
        if(array_key_exists('childrenPlayground', $itemData) &&
		    ((is_array($itemData['childrenPlayground']) && array_key_exists('enable', $itemData['childrenPlayground']) && $itemData['childrenPlayground']['enable'] == 'enable') || strtolower($itemData['childrenPlayground']) == 'y')
		){
             $features[] = array('childrenPlayground',(string)$itemData['childrenPlayground_addnl']);
        }elseif(array_key_exists('childrenPlayground_addnl', $itemData) && trim($itemData['childrenPlayground_addnl']) != ""){
             $features[] = array('childrenPlayground',(string)$itemData['childrenPlayground_addnl']);
        }         
		//Feature 39
        if(array_key_exists('views', $itemData) &&
		    ((is_array($itemData['views']) && array_key_exists('enable', $itemData['views']) && $itemData['views']['enable'] == 'enable') || strtolower($itemData['views']) == 'y')
		){
             $features[] = array('views',(string)$itemData['views_addnl']);
        }elseif(array_key_exists('views_addnl', $itemData) && trim($itemData['views_addnl']) != ""){
             $features[] = array('views',(string)$itemData['views_addnl']);
        }         
		//Feature 40
        if(array_key_exists('parkFees', $itemData) &&
		    ((is_array($itemData['parkFees']) && array_key_exists('enable', $itemData['parkFees']) && $itemData['parkFees']['enable'] == 'enable') || strtolower($itemData['parkFees']) == 'y')
		){
             $features[] = array('parkFees',(string)$itemData['parkFees_addnl']);
        }elseif(array_key_exists('parkFees_addnl', $itemData) && trim($itemData['parkFees_addnl']) != ""){
             $features[] = array('parkFees',(string)$itemData['parkFees_addnl']);
        }         
		//Feature 41
        if(array_key_exists('campKitchen', $itemData) &&
		    ((is_array($itemData['campKitchen']) && array_key_exists('enable', $itemData['campKitchen']) && $itemData['campKitchen']['enable'] == 'enable') || strtolower($itemData['campKitchen']) == 'y')
		){
             $features[] = array('campKitchen',(string)$itemData['campKitchen_addnl']);
        }elseif(array_key_exists('campKitchen_addnl', $itemData) && trim($itemData['campKitchen_addnl']) != ""){
             $features[] = array('campKitchen',(string)$itemData['campKitchen_addnl']);
        }         
		//Feature 42
        if(array_key_exists('laundry', $itemData) &&
		    ((is_array($itemData['laundry']) && array_key_exists('enable', $itemData['laundry']) && $itemData['laundry']['enable'] == 'enable') || strtolower($itemData['laundry']) == 'y')
		){
             $features[] = array('laundry',(string)$itemData['laundry_addnl']);
        }elseif(array_key_exists('laundry_addnl', $itemData) && trim($itemData['laundry_addnl']) != ""){
             $features[] = array('laundry',(string)$itemData['laundry_addnl']);
        }         
		//Feature 43
        if(array_key_exists('ensuiteSites', $itemData) &&
		    ((is_array($itemData['ensuiteSites']) && array_key_exists('enable', $itemData['ensuiteSites']) && $itemData['ensuiteSites']['enable'] == 'enable') || strtolower($itemData['ensuiteSites']) == 'y')
		){
             $features[] = array('ensuiteSites',(string)$itemData['ensuiteSites_addnl']);
        }elseif(array_key_exists('ensuiteSites_addnl', $itemData) && trim($itemData['ensuiteSites_addnl']) != ""){
             $features[] = array('ensuiteSites',(string)$itemData['ensuiteSites_addnl']);
        }         
		//Feature 44
        if(array_key_exists('restaurant', $itemData) &&
		    ((is_array($itemData['restaurant']) && array_key_exists('enable', $itemData['restaurant']) && $itemData['restaurant']['enable'] == 'enable') || strtolower($itemData['restaurant']) == 'y')
		){
             $features[] = array('restaurant',(string)$itemData['restaurant_addnl']);
        }elseif(array_key_exists('restaurant_addnl', $itemData) && trim($itemData['restaurant_addnl']) != ""){
             $features[] = array('restaurant',(string)$itemData['restaurant_addnl']);
        }         
		//Feature 45
        if(array_key_exists('kiosk', $itemData) &&
		    ((is_array($itemData['kiosk']) && array_key_exists('enable', $itemData['kiosk']) && $itemData['kiosk']['enable'] == 'enable') || strtolower($itemData['kiosk']) == 'y')
		){
             $features[] = array('kiosk',(string)$itemData['kiosk_addnl']);
        }elseif(array_key_exists('kiosk_addnl', $itemData) && trim($itemData['kiosk_addnl']) != ""){
             $features[] = array('kiosk',(string)$itemData['kiosk_addnl']);
        }         
		//Feature 46
        if(array_key_exists('internetAccess', $itemData) &&
		    ((is_array($itemData['internetAccess']) && array_key_exists('enable', $itemData['internetAccess']) && $itemData['internetAccess']['enable'] == 'enable') || strtolower($itemData['internetAccess']) == 'y')
		){
             $features[] = array('internetAccess',(string)$itemData['internetAccess_addnl']);
        }elseif(array_key_exists('internetAccess_addnl', $itemData) && trim($itemData['internetAccess_addnl']) != ""){
             $features[] = array('internetAccess',(string)$itemData['internetAccess_addnl']);
        }         
		//Feature 47
        if(array_key_exists('swimmingPool', $itemData) &&
		    ((is_array($itemData['swimmingPool']) && array_key_exists('enable', $itemData['swimmingPool']) && $itemData['swimmingPool']['enable'] == 'enable') || strtolower($itemData['swimmingPool']) == 'y')
		){
             $features[] = array('swimmingPool',(string)$itemData['swimmingPool_addnl']);
        }elseif(array_key_exists('swimmingPool_addnl', $itemData) && trim($itemData['swimmingPool_addnl']) != ""){
             $features[] = array('swimmingPool',(string)$itemData['swimmingPool_addnl']);
        }         
		//Feature 48
        if(array_key_exists('gamesRoom', $itemData) &&
		    ((is_array($itemData['gamesRoom']) && array_key_exists('enable', $itemData['gamesRoom']) && $itemData['gamesRoom']['enable'] == 'enable') || strtolower($itemData['gamesRoom']) == 'y')
		){
             $features[] = array('gamesRoom',(string)$itemData['gamesRoom_addnl']);
        }elseif(array_key_exists('gamesRoom_addnl', $itemData) && trim($itemData['gamesRoom_addnl']) != ""){
             $features[] = array('gamesRoom',(string)$itemData['gamesRoom_addnl']);
        }         
		//Feature 49
        if(array_key_exists('generatorsPermitted', $itemData) &&
		    ((is_array($itemData['generatorsPermitted']) && array_key_exists('enable', $itemData['generatorsPermitted']) && $itemData['generatorsPermitted']['enable'] == 'enable') || strtolower($itemData['generatorsPermitted']) == 'y')
		){
             $features[] = array('generatorsPermitted',(string)$itemData['generatorsPermitted_addnl']);
        }elseif(array_key_exists('generatorsPermitted_addnl', $itemData) && trim($itemData['generatorsPermitted_addnl']) != ""){
             $features[] = array('generatorsPermitted',(string)$itemData['generatorsPermitted_addnl']);
        }         
		//Feature 50
        if(array_key_exists('generatorsNotPermitted', $itemData) &&
		    ((is_array($itemData['generatorsNotPermitted']) && array_key_exists('enable', $itemData['generatorsNotPermitted']) && $itemData['generatorsNotPermitted']['enable'] == 'enable') || strtolower($itemData['generatorsNotPermitted']) == 'y')
		){
             $features[] = array('generatorsNotPermitted',(string)$itemData['generatorsNotPermitted_addnl']);
        }elseif(array_key_exists('generatorsNotPermitted_addnl', $itemData) && trim($itemData['generatorsNotPermitted_addnl']) != ""){
             $features[] = array('generatorsNotPermitted',(string)$itemData['generatorsNotPermitted_addnl']);
        }         
		//Feature 51
        if(array_key_exists('showGrounds', $itemData) &&
		    ((is_array($itemData['showGrounds']) && array_key_exists('enable', $itemData['showGrounds']) && $itemData['showGrounds']['enable'] == 'enable') || strtolower($itemData['showGrounds']) == 'y')
		){
             $features[] = array('showGrounds',(string)$itemData['showGrounds_addnl']);
        }elseif(array_key_exists('showGrounds_addnl', $itemData) && trim($itemData['showGrounds_addnl']) != ""){
             $features[] = array('showGrounds',(string)$itemData['showGrounds_addnl']);
        }         
		//Feature 52
        if(array_key_exists('nationalParknForestry', $itemData) &&
		    ((is_array($itemData['nationalParknForestry']) && array_key_exists('enable', $itemData['nationalParknForestry']) && $itemData['nationalParknForestry']['enable'] == 'enable') || strtolower($itemData['nationalParknForestry']) == 'y')
		){
             $features[] = array('nationalParknForestry',(string)$itemData['nationalParknForestry_addnl']);
        }elseif(array_key_exists('nationalParknForestry_addnl', $itemData) && trim($itemData['nationalParknForestry_addnl']) != ""){
             $features[] = array('nationalParknForestry',(string)$itemData['nationalParknForestry_addnl']);
        }         
		//Feature 53
        if(array_key_exists('pubs', $itemData) &&
		    ((is_array($itemData['pubs']) && array_key_exists('enable', $itemData['pubs']) && $itemData['pubs']['enable'] == 'enable') || strtolower($itemData['pubs']) == 'y')
		){
             $features[] = array('pubs',(string)$itemData['pubs_addnl']);
        }elseif(array_key_exists('pubs_addnl', $itemData) && trim($itemData['pubs_addnl']) != ""){
             $features[] = array('pubs',(string)$itemData['pubs_addnl']);
        }         
		//Feature 54
        if(array_key_exists('sharedWithTrucks', $itemData) &&
		    ((is_array($itemData['sharedWithTrucks']) && array_key_exists('enable', $itemData['sharedWithTrucks']) && $itemData['sharedWithTrucks']['enable'] == 'enable') || strtolower($itemData['sharedWithTrucks']) == 'y')
		){
             $features[] = array('sharedWithTrucks',(string)$itemData['sharedWithTrucks_addnl']);
        }elseif(array_key_exists('sharedWithTrucks_addnl', $itemData) && trim($itemData['sharedWithTrucks_addnl']) != ""){
             $features[] = array('sharedWithTrucks',(string)$itemData['sharedWithTrucks_addnl']);
        }
		  
		  $featureObjs = array();
		  foreach($features as $feature){
			   $object = new stdClass();
			   $key = $feature[0];
			   $object->$key = $feature[1];
			   $featureObjs[] = array('key' => $key, 'value' => $feature[1]);
			   /*$featureObjs[] = $object;
			   $object = null;*/
		  }
		  return $featureObjs;
	 }
	 
	 function random_posts(){
		  $args = array(
			   'posts_per_page'   => 50,
			   'offset'           => 0,
			   'category'         => '',
			   'category_name'    => '',
			   'orderby'          => 'rand',
			   'order'            => 'ASC',
			   'include'          => '',
			   'exclude'          => '',
			   'meta_key'         => '',
			   'meta_value'       => '',
			   'post_type'        => 'ait-dir-item', //ait-item
			   'post_mime_type'   => '',
			   'post_parent'      => '',
			   'author'	   => '',
			   'author_name'	   => '',
			   'post_status'      => 'publish',
			   'suppress_filters' => true 
		  );
		  $posts = get_posts( $args );
		  $postsArr = array();
		  foreach($posts as $post){
			   $postsArr[] = array(id => $post->ID, 'post_title' => $post->post_title);
		  }
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $postsArr), 200);
	 }
	 
	 //Get root category of camp sites
	 function get_frc_root_category($campCategories = array()){
		  global $campCats, $partnerCatIds;
		  $rootCats = array();
		  $rootCat = 9;
		  if(count($campCategories) >= 1){$rootCat = $campCategories[0];} //Added on Mar 7, 2017
		  //------------------------------
		  $partnerTCatId = $partnerCatIds = array(
													   "Accommodation" => 2096,
													   "Camping Accessories" => 5,
													   "Entertainment" => 7,
													   "Food & Wine" => 8,
													   "Fuel" => 210,
													   "Groceries" => 10,
													   "Markets" => 106,
													   "Medical" => 15,
													   "Personal Health" => 17,
													   "Repairs" => 4,
													   "Services" => 107,
													   "Information Centre" => 2088,
													   "IGA Stores" => 13,
												  );
		  foreach($partnerTCatId as $partnerCatId){
			  $childArr = get_term_children( $partnerCatId, 'ait-dir-item-category' );
			  foreach($childArr as $childCatID){
				  $partnerCatIds[] = $childCatID;
			  }
		  }
          //------------------------------
		  $bussinessCatArr = array_flip($partnerTCatId);//array_flip($partnerCatIds); this was updated on Mar 7, 2017
		  //Only root business category should be returned to api
		  $campCatArr = array_flip($campCats);
		
		  foreach($campCategories as $cat_key => $cat_value){
			   if(array_key_exists($cat_value,$bussinessCatArr)){
				   $rootCats[] = $cat_value;
				   break;
			   }elseif(array_key_exists($cat_value,$campCatArr)){
				   $rootCats[] = $cat_value;
				   break;
			   }
		  }
		  if(count($rootCats)){$rootCat = $rootCats[0];}
		  return $rootCat;
	 }
	 
	 function xml_attribute($object, $attribute)
	 {
		 if(isset($object[$attribute]))
			 return (string) $object[$attribute];
	 }
?>