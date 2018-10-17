<?php
	 //1: Download offline user data
	 function frc_user_offlinedata(){
		  //https://www.freerangecamping.com.au/directory/wp-json/api/v1/data/user/download/
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  $paramOptions = get_option($frcOption);
		  if('27.255.196.7' == $_SERVER["REMOTE_ADDR"]){
			   echo $formattedDate = date('F j, Y', strtotime('last mon of this month')); //or 'last mon of this month'
			   echo $formattedDate = date('F j, Y');
			   //date('Y-m-d',strtotime('last sunday -7 days'));
			   $defaulDBParams = array(
								   //1. For daily update
								   'rootDBFolder' => "20170406_db",	//1. folder name
								   'dataVer' => '8.75',	//2. (version)
								   'zipFileVer' => 'data_v660.zip',	//3. Zip file version
								   'zipFilePath' => '/home/freerang/public_html/directory/wp-content/uploads/20170406_db/',
								   
								   //2. For large file - should be updated weekly.
								   'rootFolder' => '20170406',	//1. folder name
								   'largeDataVer' => '6.64', //2. (version)
								   'zipLargeFileVer' => 'data_v459.zip', //3. Zip file version
								   'zipLargeFilePath' => '/home/freerang/public_html/directory/wp-content/uploads/20170406/',
								   
								   //For incremental update.
								   'incFolder' => "20170406_inc", //1. folder name
								   'incDataVer' => '8.75', //2. (version)
								   'zipIncFileVer' => 'data_inc.zip', //3.zip file version
								   'zipIncFilePath' => '/home/freerang/public_html/directory/wp-content/uploads/20170406_inc/',
								   
								   'weekly_processed_large_file' => 0, //For large file
								   //Note: we will generate weekly file only once. When cron will be running we will generate large file again and only once in Sunday and incremental updated file should be set to empty.
									 
								   );
			   //update_option($frcOption, $defaulDBParams, 'no');
			   //print_r($paramOptions);
			   //die;
		  }
		  $folderName = $paramOptions["rootFolder"];
		  $dataFolder = $paramOptions["zipLargeFileVer"];
		  $data['version'] = $paramOptions["largeDataVer"];
		  
		  $uploadDirArr = wp_upload_dir();
		  $data['download_url'] = $uploadDirArr['baseurl'].DS.$folderName.DS.$dataFolder;
		  $orgFilePath = $filePath = $uploadDirArr['basedir'].DS.$folderName.DS.$dataFolder;
		  if(!file_exists($filePath)){
			   $fileList = scandir($uploadDirArr['basedir'].DS.$folderName);
			   foreach ($fileList as $key => $value){ 
					if (!in_array($value,array(".",".."))){
						 if(strpos($value,".zip")){
							 $filePath = $uploadDirArr['basedir'].DS.$folderName.DS.$value;
							 $data['download_url'] = $uploadDirArr['baseurl'].DS.$folderName.DS.$value;
							 break;
						 }
					}
			   }
			   $headers = array('Content-Type: text/html; charset=UTF-8');
			   wp_mail('kalyanrajiv@gmail.com', "Issue with large file {$orgFilePath}", "$orgFilePath", $headers);
		  }
		  $data['file_size'] = filesize ( $filePath);
		  //$data['file_size'] = FileSizeConvert(filesize ( $filePath));
		  if('27.255.171.12' == $_SERVER["REMOTE_ADDR"]){
			   $data['firstMonday'] = date('F j, Y', strtotime('first mon of this month'));
			   $data['secondMonday'] = date('F j, Y', strtotime('second mon of this month'));
			   $data['thirdMonday'] = date('F j, Y', strtotime('third mon of this month'));
			   $data['fourthMonday'] = date('F j, Y', strtotime('fourth mon of this month'));
			   $data['lastMonday'] = date('F j, Y', strtotime('last mon of this month'));
		  }
		  return new WP_REST_Response(array('status_code' => 200,'data' => $data),200);
	 }
	 
	 //2: Download offline user data
	 function frc_download_sqlite_db(){
		  //https://www.freerangecamping.com.au/directory/wp-json/api/v1/data/user/download/database
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  $paramOptions = get_option($frcOption);
		  $folderName = $paramOptions["rootDBFolder"];
		  $dataFolder = $paramOptions["zipFileVer"];
		  $data['version'] = $paramOptions["dataVer"];
		  
		  $uploadDirArr = wp_upload_dir();
		  $data['download_url'] = $uploadDirArr['baseurl'].DS.$folderName.DS.$dataFolder;
		  $filePath = $uploadDirArr['basedir'].DS.$folderName.DS.$dataFolder;
		  $data['file_size'] = filesize ( $filePath);
		  //$data['file_size'] = FileSizeConvert(filesize ( $filePath));
		  return new WP_REST_Response(array('status_code' => 200,'data' => $data),200);
	 }
	 
	 //3: Download offline user data
	 function frc_modified_images(){
		  //https://www.freerangecamping.com.au/directory/wp-json/api/v1/data/user/download/updates
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  $paramOptions = get_option($frcOption);
		  $folderName = $paramOptions["incFolder"];
		  $dataFolder = $paramOptions["zipIncFileVer"];
		  $data['version'] = $paramOptions["incDataVer"];
		  
		  $uploadDirArr = wp_upload_dir();
		  $data['download_url'] = $uploadDirArr['baseurl'].DS.$folderName.DS.$dataFolder;
		  $filePath = $uploadDirArr['basedir'].DS.$folderName.DS.$dataFolder;
		  $data['file_size'] = filesize ( $filePath);
		  //$data['file_size'] = FileSizeConvert(filesize ( $filePath));
		  return new WP_REST_Response(array('status_code' => 200,'data' => $data),200);
	 }
	 
	 function frc_export(){
		  //https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/data/user/export/
		  global $wpdb;
		  $wpdb->show_errors = false;
		  $isOldTheme = is_old_theme();
		  $params = array(
			   'posts_per_page' => 100,
			   'post_type' => 'ait-dir-item',
			   //'nopaging' =>	true, //Display all posts by disabling pagination:
			   'post_status' => 'publish',
			   'offset' => 0,
			   //'paged' => 6, //Display posts from page number x:
		  );
		  $items = new WP_Query( $params );
		  $dataArr = array();
		  //query_vars, posts, tax_query, meta_query, query(showing parameters passed), request(containing query)
		  $uploadFolder = wp_upload_dir();
		  $filePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'campsites_rasu.sql';
		  $logFilePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'error_log.txt';
		  $handle = fopen($filePath, "a+");
		  $logHandle = fopen($logFilePath, "a+");
		  
		  foreach ($items->posts as $key => $item) {
			   //fwrite($handle, "\nQuery for item:".$item->ID."\n");
			   $featuresStr = "";
			   $itemArr = array(
								   'site_name' => $item->post_title,
								   'site_id' => $item->ID,
								   'post_id' => $item->ID,
								   'site_description' => do_shortcode($item->post_content),
								   'rating' => $item->rating,
								   'status' => $item->post_status,
							  );
			   $item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
			   
			   //Start: Get camp photos - getting camp images for slider
			   $photos = array();
			   $query = "SELECT * FROM  `gwr_postmeta` WHERE `post_id` = $item->ID AND  `meta_key` =  '_ait_dir_gallery'";//25761
			   $galleryResult = $wpdb->get_results($query, OBJECT);
			   if(count($galleryResult) >= 1){
					$site_photos = unserialize($galleryResult->meta_value);
					foreach($galleryResult as $galleryObj){
						 $galleryArr = unserialize($galleryObj->meta_value);
						 foreach($galleryArr as $photo){
							  if(!empty($photo) && !in_array($photo, $photos)){
								   $photos[] = array($photo, 0);
								   frc_copy_images($photo, $isMain = 0, $item->ID);
							  }
						 }
					}
			   }
			   //End: Get camp photos
			   //get_the_post_thumbnail( $item->ID, 'thumbnail' ); - getting the main thumnail image in html and all different sizes are contained in "srcset" attribute.
			   $mainimage = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID), 'full' );
			   if(count($mainimage)){
					$photos[] = array($mainimage[0],1); //$mainimage[1] = width, $mainimage[2] = height
					frc_copy_images($mainimage[0], $isMain = 1, $item->ID);
			   }
			   if(is_array($item->optionsDir)){
					$itemArr['address'] = $item->optionsDir['address'];
					$itemArr['gps_lon'] = $item->optionsDir['gpsLongitude'];
					$itemArr['gps_lat'] = $item->optionsDir['gpsLatitude'];
					$itemArr['suburb'] = $item->optionsDir['wlm_city'];
					$itemArr['state'] = $item->optionsDir['wlm_state'];
					$itemArr['post_code'] = $item->optionsDir['wlm_zip'];
			   }
			   $itemArr['telephone'] = $item->optionsDir['telephone'];
			   $itemArr['email'] = $item->optionsDir['email'];
			   $itemArr['web'] = $item->optionsDir['web'];
			   $itemArr['location'] = $item->optionsDir['location'];
			   $itemArr['authority'] = $item->optionsDir['responsible_authority'];
			   $itemArr['location'] = $item->optionsDir['location'];
			   $itemArr['discount'] = $item->optionsDir['discount_detail'];
			   $itemArr['price'] = $item->optionsDir['pricing_detail'];
			   
			   $features = getFeaturesData($item->optionsDir);
			   
			   //Start: Get all Item Categories associated with item
			   if($isOldTheme){
					$ItemCats = get_the_terms($item->ID, 'ait-dir-item-category');
			   }else{
					$ItemCats = get_the_terms($item->ID, 'ait-items'); //doubtful
			   }
			   $itemCatStr = "";$itemCatArr = $itemCatNameArr = array();
			   if(is_array($ItemCats)){
					foreach($ItemCats as $ItemCat){
						 //$itemCatArr[] = $ItemCat->name; //others are slug, term_id, term_taxonomy_id
						 $itemCatArr[] = $ItemCat->term_id;
						 $itemCatNameArr[] = $ItemCat->name;
					}
			   }
			   $itemArr['site_category_id'] =  implode(",", $itemCatArr);
			   $itemArr['site_category'] =  implode(",", $itemCatNameArr);
			   $itemArr['photos'] = $photos;
			   //End: Get all Item Categories associated with item
			   
			   //Start: Get all Item Locations associated with item
			   if($isOldTheme){
					$ItemCats = get_the_terms($item->ID, 'ait-dir-item-location');
			   }else{
					$ItemCats = get_the_terms($item->ID, 'ait-locations');
			   }
			   $itemCatStr = "";$itemCatArr = $itemCatIDArr = array();
			   if(is_array($ItemCats)){
					foreach($ItemCats as $ItemCat){
						 $itemCatArr[] = $ItemCat->name; //others are slug, term_id, term_taxonomy_id
						 $itemCatIDArr[] = $ItemCat->term_id; 
					}
			   }
			   if(empty($itemArr['location'])){
					$itemArr['location'] =  implode(",", $itemCatArr); //Temporary
					$itemArr['location_id'] =  implode(",", $itemCatIDArr); //Temporary
			   }
			   //End: Get all Item Locations associated with itema
			   
			   //Start: Getting opening hours
			   $openingHours = array();
			   if($isOldTheme){
					if(!empty($itemArr['hoursMonday']))
						 $itemArr['Monday'] = $itemArr['hoursMonday'];
					if(!empty($itemArr['hoursTuesday']))
						 $itemArr['Tuesday'] = $itemArr['hoursTuesday'];
					if(!empty($itemArr['hoursWednesday']))
						 $openingHours['Wednesday'] = $itemArr['hoursWednesday'];
					if(!empty($itemArr['hoursThursday']))
						 $openingHours['Thursday'] = $itemArr['hoursThursday'];
					if(!empty($itemArr['hoursFriday']))
						 $openingHours['Friday'] = $itemArr['hoursFriday'];
					if(!empty($itemArr['hoursSaturday']))
						 $openingHours['Saturday'] = $itemArr['hoursSaturday'];
					if(!empty($itemArr['hoursSunday']))
						 $openingHours['Sunday'] = $itemArr['hoursSunday'];
			   }else{
					if($itemArr['displayOpeningHours'] == 1){//openingHoursMonday => Monday
						 if(!empty($itemArr['openingHoursMonday']))$openingHours['Monday'] = $itemArr['openingHoursMonday'];
						 if(!empty($itemArr['openingHoursTuesday']))$openingHours['Tuesday'] = $itemArr['openingHoursTuesday'];
						 if(!empty($itemArr['openingHoursWednesday']))$openingHours['Wednesday'] = $itemArr['openingHoursWednesday'];
						 if(!empty($itemArr['openingHoursThursday']))$openingHours['Thursday'] = $itemArr['openingHoursThursday'];
						 if(!empty($itemArr['openingHoursFriday']))$openingHours['Friday'] = $itemArr['openingHoursFriday'];
						 if(!empty($itemArr['openingHoursSaturday']))$openingHours['Saturday'] = $itemArr['openingHoursSaturday'];
						 if(!empty($itemArr['openingHoursSunday']))$openingHours['Sunday'] = '';$itemArr['openingHoursSunday'];
					}
			   }
			   $openingHourStr = "";
			   if(count($openingHours)){
					$itemArr['opening_hours'] = array($openingHours);
					$openingHourStr = implode("|",$openingHours);
			   }else{
					$itemArr['opening_hours'] = array();
			   }
			   //End:Getting opening hours
			   
			   //$itemArr['rating'] = get_post_meta( $item->ID, 'rating', true );	
			   if(is_array($features) && count($features) > 0){
					$featuresStr = implode(",", $features);
			   }
			   $itemArr['features'] = $featuresStr;
			   //>>>Save in database
			   $status = $wpdb->insert( 'camp_sites',
										array(
											  'site_id' => $itemArr['site_id'],
											  'site_name' => $itemArr['site_name'],
											  'site_description' => do_shortcode($itemArr['site_description']),
											  'rating' => $itemArr['rating'],
											  'discount' => $itemArr['discount'],
											  'address' => $itemArr['address'], //6
											  'suburb' => $itemArr['suburb'], //7
											  'state' => $itemArr['state'], //8
											  'post_code' => $itemArr['post_code'], //9
											  'gps_lon' => $itemArr['gps_lon'], //10
											  'gps_lat' => $itemArr['gps_lat'], //11
											  'post_id' => $itemArr['site_id'],
											  'email' => $itemArr['email'],
											  'phone' => $itemArr['telephone'],
											  'web' => $itemArr['web'],
											  'opening_hours' => $openingHourStr,
											  'features' => $itemArr['features'],
											  'location' => $itemArr['location'],
											  'authority' => $itemArr['authority'],
											  'price' => $itemArr['price']
											  ),
											 array(
												   '%d', '%s', '%s', '%s','%s',
												   '%s', '%s', '%s','%s','%s',
												   '%s', '%s', '%s','%s','%s',
												   '%s', '%s','%s','%s', '%s'
											 )
							);
			   if($status){
					$insertQry = $wpdb->last_query;
					fwrite($handle, "\n".$insertQry);
			   }else{
					$lastError = "";
					$lastError = $wpdb->last_error;
					ob_start();
					$wpdb->print_error();
					$lastError.= ob_get_clean();
					fwrite($logHandle, "\n"."Failed to add record for site(".date('Y-m-d H:i:s')."):". $itemArr['site_id']."\n{$lastError}\n");
			   }
			   //<<<Save in database
			   $dataArr[] = $itemArr;
		  }
		  fclose($handle);
		  fclose($logHandle);
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $dataArr), 200);
		  //echo json_encode(array('message' => "Under development",'status_code' => 400  ));die;
     }
	 
	 //2: Download offline map data
	 function frc_user_offlinemaps(){
		  echo json_encode(array('message' => "Under development",'status_code' => 400  ));die;
     }
	 
	 //3 copy
	 function frc_copy_images($photo, $isMain = 0, $itemID){
		  global $wpdb;
		  $uploadFolder = wp_upload_dir();
		  $uploadBaseDir = $uploadFolder['basedir'].DS;
		  $filePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'photos_rasu.sql';
		  $handle = fopen($filePath, "a+");
		  //=>/home/frcconz/public_html/mobile/wp-content/uploads
		  $findStr = "/uploads";
		  $position = strpos($photo, $findStr,0);
		  $position = $position + strlen($findStr) + 1;
		  $resStr = substr($photo, $position);
		  $resArr = explode("/", $resStr);
		  $imageFile = end($resArr);
		  array_pop($resArr);
		  $itemAbsoluteDir = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr);
		  if(is_dir($itemAbsoluteDir)){
			   $destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
			   $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
			   if(copy($sourcePath, $destPath)){;
					$status = $wpdb->insert( 'site_photos',
										array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
										array( '%d', '%s', '%s', '%d' )
							);
					$photoQry = $wpdb->last_query;
					if($status){
						 $insertQry = $wpdb->last_query;
						 fwrite($handle, "\n".$insertQry);
					}
					//echo "\nquery: ".$wpdb->last_query;
			   }else{"Failed to copy";}
		  }elseif(mkdir ( $itemAbsoluteDir, '0766', true)){
			   $destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
			   $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
			   if(copy($sourcePath, $destPath)){;
					$status = $wpdb->insert( 'site_photos',
										array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
										array( '%d', '%s', '%s', '%d' )
							);
					$photoQry = $wpdb->last_query;
					if($status){
						 $insertQry = $wpdb->last_query;
						 fwrite($handle, "\n".$insertQry);
					}
					//echo "\nquery: ".$wpdb->last_query;
			   }else{"Failed1";}
		  }else{
			   $campDir = $uploadBaseDir.'camp_photos';
			   //This logic needs to be refined.
			   /*
			   if(is_dir($campDir)){
					if(chmod($campDir, '0766')){
						 $absouluteDir = $campDir.DS;
						 print_r($resArr);
						 foreach($resArr as $subDir){
							  echo "\nrasu=>";
							  echo $absouluteDir.= $subDir;
							  if(is_dir($absouluteDir)){
								   ;//do nothing
								   echo "\nif->".$absouluteDir.=DS;
							  }else{
								   //else create dir
								   if(mkdir($absouluteDir)){
										chmod($absouluteDir, '0766');
										$absouluteDir.=DS;
										 echo "\nif_____";
								   }else{
										die("----\nFailed to create dir : $absouluteDir");
								   }
							  }
						 }
					}else{
						 echo "\n else";
					}
			   }elseif(mkdir($campDir)){
					chmod($campDir, '0777');
					echo $uploadBaseDir.'camp_photos created successfully';
			   }
			   echo "Failed to create directories $itemAbsoluteDir";
			   */
		  }
		  fclose($handle);
		  return true;
	 }
	 
	 function FileSizeConvert($bytes){
		  $bytes = floatval($bytes);
		  $arBytes = array(
							  0 => array(
								  "UNIT" => "TB",
								  "VALUE" => pow(1024, 4)
							  ),
							  1 => array(
								  "UNIT" => "GB",
								  "VALUE" => pow(1024, 3)
							  ),
							  2 => array(
								  "UNIT" => "MB",
								  "VALUE" => pow(1024, 2)
							  ),
							  3 => array(
								  "UNIT" => "KB",
								  "VALUE" => 1024
							  ),
							  4 => array(
								  "UNIT" => "B",
								  "VALUE" => 1
							  ),
						 );

		  foreach($arBytes as $arItem){
			   if($bytes >= $arItem["VALUE"]){
				   $result = $bytes / $arItem["VALUE"];
				   $result = str_replace(".", "." , strval(round($result, 2)))."".$arItem["UNIT"];
				   break;
			   }
		  }
		  return $result;
	 }

?>