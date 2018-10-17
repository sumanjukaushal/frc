<?php
    //Script URL: https://www.freerangecamping.co.nz/directory/wp-content/manage_api_jobs/track_records.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    $postType = isOldTheme() ? 'ait-dir-item' : 'ait-item';
	//$appendToTable = '_19apr';//_2feb
	$appendToTable = '';//_2feb
    define('CAMP_SITE_TABLE', "camp_sites{$appendToTable}");
	define('TRACK_RECORD_TABLE', "`rasu_track_records{$appendToTable}`");
	define('SITE_PHOTOS_TABLE', "site_photos{$appendToTable}");
	
	$innerSeparator = "^"; //Replace : to ^
	$betSeparator = "##"; //Replace , to ; and I am suggesting ##
	$camp_cats = array(
						'Free Camps' => 9,
						'Rest Areas' => 30,
						'Dump Points' => 6,
						'Campgrounds' => 14,
						'Caravan Parks' => 155,
						'Help Outs' => 20,
						'House Sitting' => 12,
						'Water Points' => 2767,
						'Park Over' => 16,
					);
	
	$businessCats = array(
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
    $count_posts = wp_count_posts( $postType );
    $published_posts = $count_posts->publish; //8292
	echo $published_posts;
	if(true){
		//This should be run manually after setting this if true
		$getSkippedQry = "SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE `gp`.`ID` NOT IN (SELECT `gp`.ID FROM `gwr_posts` as `gp`, `".CAMP_SITE_TABLE."` as `cs` WHERE   `gp`.`ID` = `cs`.`site_id`) and `gp`.`post_status`='publish' and `gp`.`post_type`='$postType'";
		//echo $getSkippedQry;
		//SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE `gp`.`ID` NOT IN (SELECT `gp`.ID FROM `gwr_posts` as `gp`, `camp_sites` as `cs` WHERE   `gp`.`ID` = `cs`.`site_id`) and `gp`.`post_status`='publish' and `gp`.`post_type`='ait-dir-item'
		//SELECT `cs`.site_id FROM `camp_sites` as `cs` WHERE `cs`.`site_id` NOT IN (SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE `gp`.`post_status`='publish' and `gp`.`post_type`='ait-dir-item')
		// 17378, 17632, 17685
		//SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE `gp`.`post_status`='publish' and `gp`.`post_type`='ait-dir-item'
		$skippedObj = $wpdb->get_results($getSkippedQry);
		$skippedItemArr = array();
		foreach($skippedObj as $sngObj){
			$skippedItemArr[] = $sngObj->ID;
		}
		echo "array(".implode(",",$skippedItemArr).")";
		die;
		frcExport($offset, $getSkipped = 1, $skippedItemArr);
	}
	die("--");
	//$query = new WP_Query( array( 'post_type' => 'page', 'post__in' => array( 2, 5, 12, 14, 20 ) ) );
	//die("script executed successfully!!");
	
	//echo $published_posts;die;
    $offset = 100;
    $query = "SELECT * FROM ". TRACK_RECORD_TABLE;
	$resultObj = $wpdb->get_results($query);
	
    if(count($resultObj) >= 1){
		$recordObj = $resultObj[0];
        $offset += $recordObj->offset;
        $allRecords = 0;
        if($offset >= $published_posts){
            $offset -= 100;
            $allRecords = 1;
            if($recordObj->mail_sent == 1){
                die("Script finished - delete rows to restart this operation. or increase cron time");
            }else{
                //inform admin/developer all records are processed
                wp_mail('kalyanrajiv@gmail.com', 'Import finished', 'Please stop cron. All records are processed.');
            }
        }
        $updateQry = "UPDATE ".TRACK_RECORD_TABLE." SET `offset` = $offset, `all_records` = $allRecords";
		$wpdb->query( $updateQry);
        frcExport($offset, $getSkipped = 0, $skippedArr = array());
    }else{
        $offset = 0;
        $query = "INSERT INTO ".TRACK_RECORD_TABLE." SET `offset` = $offset, `all_records` = 0";
		$wpdb->query( $query);
        frcExport($offset, $getSkipped = 0, $skippedArr = array());
    }
    
    if(is_object($recordObj) && $recordObj->all_records == 1){
        $query = "UPDATE ".TRACK_RECORD_TABLE." SET `mail_sent` = 1";
		$wpdb->query( $query);
    }
    
    echo "Script executed successfully";
    
    function isOldTheme(){
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
        }
        return $oldTheme;
   }
	
	//This function will return category ids business categories and their childs
	function frc_partner_cat_ids(){
		global $businessCats;
		$partnerCatIds = array();
		$partnerTCatId = $partnerCatIds = $businessCats;
		foreach($partnerTCatId as $partnerCatId){
			$childArr = get_term_children( $partnerCatId, 'ait-dir-item-category' );
			foreach($childArr as $childCatID){
				$partnerCatIds[] = $childCatID;
			}
		}
		return array_merge($partnerCatIds,$partnerTCatId);
	}
	
	//Get root category of camp sites
	function get_root_category($campCategories = array()){
		//we would have only one parent business category only. This is confirmed. From Glen on Skype on Mar 7, 2017
		global $camp_cats, $businessCats;
		$rootCats = array();
		$rootCat = 9;
		if(count($campCategories) >= 1){$rootCat = $campCategories[0];} //Added on Mar 7, 2017
		$bussinessCatArr = array_flip($businessCats);
		$campCatArr = array_flip($camp_cats);
		
		foreach($campCategories as $cat_key => $cat_value){
			if(array_key_exists($cat_value, $bussinessCatArr)){
				$rootCats[] = $cat_value;
				break;
			}elseif(array_key_exists($cat_value, $campCatArr)){
				$rootCats[] = $cat_value;
				break;
			}
		}
		if(count($rootCats)){
			$rootCat = $rootCats[0];
		}
		return $rootCat;
	}
   
   function frcExport($offset = 0, $getSkipped = 0, $skippedArr = array()){
		global $innerSeparator, $betSeparator, $wpdb;
		$partnerCatIds = frc_partner_cat_ids();
        //https://www.freerangecamping.co.nz/mobile/wp-json/api/v1/data/user/export/
        $wpdb->show_errors = true;
        $isOldTheme = is_old_theme();
		$itemMeta = $isOldTheme ? '_ait-dir-item' : '_ait-item_item-data';
		$catMeta = $isOldTheme ? 'ait-dir-item-category' : 'ait-items'; //ait-items doubtful in new theme.
		$locMeta = $isOldTheme ? 'ait-dir-item-location' : 'ait-locations';
		if($getSkipped == 1){
			$items = new WP_Query( array(
										 'post_type' => 'ait-dir-item',
										 'post__in' => $skippedArr,
										 'posts_per_page' => -1
										 )
								  );
		}else{
			$params = array(
								'posts_per_page' => 100,
								'post_type' => 'ait-dir-item',
								//'nopaging' =>	true, //Display all posts by disabling pagination:
								'post_status' => 'publish',
								'offset' => $offset,
								//'paged' => 6, //Display posts from page number x:
							);
			$items = new WP_Query( $params );
		}
		/*print_r($items);foreach ($items->posts as $key => $item) echo "\n<br/>".$item->ID;die;*/
        $dataArr = array();
        //query_vars, posts, tax_query, meta_query, query(showing parameters passed), request(containing query)
        $uploadFolder = wp_upload_dir();
        //SQL file
        $filePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'campsites_rasu.sql';
        $handle = fopen($filePath, "a+");
        //Log file
        $logFilePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'error_log.txt';
        $logHandle = fopen($logFilePath, "a+");
        foreach ($items->posts as $key => $item) {
            //fwrite($handle, "\nQuery for item:".$item->ID."\n");
			$htmlTag = array(
								'<b style="font-weight: 300;">',
								'</b>',
								'<span style="font-weight: 300;">',
								'</span>',
							);
			//>>>>>>>>>>find tag to be replaced>>>>>>>>>>>
			$pos1 = 0;
			while (($pos1 = strpos($item->post_content, $htmlTag[0], $pos1)) !== false) {
				$pos1 = (int)$pos1;
				$pos2 = strpos($item->post_content, $htmlTag[1], $pos1);
				$pos2 = $pos2 + strlen($htmlTag[1]);
				if($pos2 && $pos2 > $pos1){
					$len = (int)$pos2 - (int)$pos1;
					$tgtStr1 = $tgtStr = substr($item->post_content, $pos1, $len);
					$replaceWith = str_replace(
												array($htmlTag[0], $htmlTag[1]),
												array("",""),
												$tgtStr
											);
				}
				$item->post_content = str_replace($tgtStr, $replaceWith, $item->post_content);
			}
			//<<<<<<<<<<<<<find tag to be replaced<<<<<<<<<<<<<
		   
			//>>>>>>>>>>find tag to be replaced>>>>>>>>>>>
			$pos1 = 0;
			while (($pos1 = strpos($item->post_content, $htmlTag[2], $pos1)) !== false) {
				$pos1 = (int)$pos1;
				$pos2 = strpos($item->post_content, $htmlTag[3], $pos1);
				$pos2 = $pos2 + strlen($htmlTag[3]);
				if($pos2 && $pos2 > $pos1){
					$len = (int)$pos2 - (int)$pos1;
					$tgtStr1 = $tgtStr = substr($item->post_content, $pos1, $len);
					$replaceWith = str_replace(
								array($htmlTag[2], $htmlTag[3]),
								array("",""),
								$tgtStr
								);
				}
			   $item->post_content = str_replace($tgtStr, $replaceWith, $item->post_content);
			}
			//<<<<<<<<<<<<<find tag to be replaced<<<<<<<<<<<<<
			
			$vidCount = substr_count($item->post_content,"[cleveryoutube");
			for($i = 0; $i < $vidCount; $i++){
				$pos1 = strpos($item->post_content, "[cleveryoutube");
				if($pos1){
					$pos2 = strpos($item->post_content, "]", $pos1);
					$cleverUStr = substr ( $item->post_content , $pos1, ($pos2-$pos1)+1 );
					$v1 = strpos($cleverUStr,'video="');
					$v1 = $v1 + strlen('video="');
					$v2 = strpos($cleverUStr, '"', $v1);
					$cleverVStr = substr ( $cleverUStr , $v1, ($v2-$v1) );
					$youTubeStr = '<iframe style="width: 100%; height: 100%; left: 0;" src="http://www.youtube.com/embed/'.$cleverVStr.'?rel=0&amp;showinfo=0?ecver=2" width="640" height="360" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
					$youTubeStr = "<div><a href='http://www.youtube.com/embed/{$cleverVStr}?rel=0&amp;showinfo=0?ecver=2'><img src='https://www.freerangecamping.co.nz/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;' /></a></div>";
					//removed from style position: absolute;
					$item->post_content = str_replace($cleverUStr, $youTubeStr, $item->post_content);
				}
			}
			
			//Start: code for clean formatting for Mobile Apps
			$item->post_content = str_replace("\r\r","",$item->post_content);	//\n
			$item->post_content = str_replace("\r","",$item->post_content);	//\n
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
								'<strong><strong>COST</strong>' ,
								'<strong>RESERVING A CAMP SITE</strong>',
								'<strong>CAMPFIRES AND FIREWOOD</strong>',
								'<strong>BEFORE YOU VISIT</strong>',
								'<strong>NOTE:</strong>',
								'<strong>HOW TO GET THERE</strong>',
								'<strong>SITE ACCESS</strong>',
								'<strong>FEES & BOOKINGS</strong>',
								'<strong>FAMILY RATE.</strong>',
								'<strong>RULES</strong>',
								'<strong">FEES</strong>',
								//'\n',
								'<strong>ABOUT US</strong>',
								'<strong>FRC CLUB OFFER</strong>',
								'<strong>SERVICE OFFERED</strong>',
								'<strong>SHORTCUT</strong>',
							);
			$item->post_content = str_replace($headings,$new_headings,$item->post_content);
			//$item->post_content = str_replace("\n\n","\n", $item->post_content);
			$item->post_content = str_replace("\\n","\n", $item->post_content);
			//End: code for clean formatting for Mobile Apps
			
            $featuresStr = "";
			$fullRating = '0';
			if(is_array($item->rating) && array_key_exists('full', $item->rating)){
				$fullRating = $item->rating['full'];
			}
            $itemArr = array(
								'site_name' => $item->post_title,
								'site_id' => $item->ID,
								'post_id' => $item->ID,
								'site_description' => $item->post_content,
								'rating' => $fullRating,
								'status' => $item->post_status,
                            );
			
            $item->optionsDir = get_post_meta($item->ID, $itemMeta, true);
            
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
							frc_cp_images($photo, $isMain = 0, $item->ID);
                        }
                    }
                }
            }
			//End: Get camp photos
            
            //get_the_post_thumbnail( $item->ID, 'thumbnail' ); - getting the main thumnail image in html and all different sizes are contained in "srcset" attribute.
            $mainimage = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID), 'full' );
            if(count($mainimage)){
				if(!empty($mainimage[0])){ //New
					$photos[] = array($mainimage[0],1); //$mainimage[1] = width, $mainimage[2] = height
					frc_cp_images($mainimage[0], $isMain = 1, $item->ID);
				}
            }
			$itemArr['address'] = '';
			$itemArr['gps_lon'] = '';
			$itemArr['gps_lat'] = '';
			$itemArr['suburb'] = '';
			$itemArr['state'] = '';
			$itemArr['post_code'] = '';
			$itemArr['telephone'] = '';
			$itemArr['email'] = '';
			$itemArr['web'] = '';
			$itemArr['location'] = '';
			$itemArr['authority'] = '';
			$itemArr['location'] = '';
			$itemArr['discount'] = '';
			$itemArr['price'] = '';
			
            if(is_array($item->optionsDir)){
                $itemArr['address'] = $item->optionsDir['address'];
                $itemArr['gps_lon'] = $item->optionsDir['gpsLongitude'];
                $itemArr['gps_lat'] = $item->optionsDir['gpsLatitude'];
                $itemArr['suburb'] = $item->optionsDir['wlm_city'];
                $itemArr['state'] = $item->optionsDir['wlm_state'];
                $itemArr['post_code'] = $item->optionsDir['wlm_zip'];
				$itemArr['telephone'] = $item->optionsDir['telephone'];
				$itemArr['email'] = $item->optionsDir['email'];
				$itemArr['web'] = $item->optionsDir['web'];
				$itemArr['location'] = $item->optionsDir['location'];
				$itemArr['authority'] = $item->optionsDir['responsible_authority'];
				$itemArr['location'] = $item->optionsDir['location'];
				$itemArr['discount'] = $item->optionsDir['discount_detail'];
				$itemArr['price'] = $item->optionsDir['pricing_detail'];
            }
            
			   
            $features = getFeaturesData($item->optionsDir); //Check this function
			$featureArr = array();
			$featureDetArr = array(); 
			foreach($features as $feature => $featureData){
				$featureArr[] = $featureData['key'];
				$featDet = str_replace(array('^', '##'),array(' ', ''),$featureData['value'] );
				if(strtolower($featureData['key']) == 'discount'){
					$featureDetArr[] = $featureData['key'].$innerSeparator.$item->optionsDir['discount_detail'];
				}else{
					$featureDetArr[] = $featureData['key'].$innerSeparator.$featDet;
				}
			}
			$featureDetStr = implode($betSeparator, $featureDetArr);
			
            //Start: Get all Item Categories associated with item
			$ItemCats = get_the_terms($item->ID, $catMeta);
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
			
			$itemArr['is_frc_partner'] = $is_frc_partner;
			$itemArr['is_partner'] = $is_frc_partner;
			$rootCat = get_root_category($itemCatArr);
            $itemArr['site_category_id'] =  $rootCat;//implode(",", $itemCatArr);
			$itemCatIds = implode(",", $itemCatArr);
            $itemArr['site_category'] =  implode(",", $itemCatNameArr);
            $itemArr['photos'] = $photos;
            //End: Get all Item Categories associated with item
            
            //Start: Get all Item Locations associated with item
			$ItemCats = get_the_terms($item->ID, $locMeta);
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
            if(is_array($featureArr) && count($featureArr) > 0){
                $featuresStr = implode(",", $featureArr);
            }
            $itemArr['features'] = $featuresStr;
            //>>>Save in database
            $insertQry = "";
            $fieldArr = array(
                                'site_id' => $itemArr['site_id'],
                                'site_name' => $itemArr['site_name'],
                                'site_description' => $itemArr['site_description'],
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
								'feature_n_details' => $featureDetStr,	//New
                                'location' => $itemArr['location'],
								'category_id' => $itemArr['site_category_id'],			//New
								'category_ids' => $itemCatIds,			//New
								'is_partner' => $is_frc_partner,						//New
                                'authority' => $itemArr['authority'],
                                'price' => $itemArr['price']
                            );
			//echo "<pre>";print_r($fieldArr);die;
			if(
			   (int)$itemArr['site_id'] == '27675' ||
			   (int)$itemArr['site_id'] == '27674' ||
			   (int)$itemArr['site_id'] == '27646' ||
			   (int)$itemArr['site_id'] == '27633' ||
			   (int)$itemArr['site_id'] == '27636'
			   ){
				;//print_r($fieldArr);die;
			}
			$checkIDs = array(27675, 27674, 27646, 27633, 27636);
			if(in_array($fieldArr['site_id'], $checkIDs) || in_array($item->ID, $checkIDs) ){
				;//print_r($fieldArr);die;
			}else{
				;//die("---");
			}
			$checkRatings = array(20092, 26190, 26173);
			if(!empty($fieldArr['site_name'])){
				$campStatusRes = $wpdb->insert( CAMP_SITE_TABLE, $fieldArr,
												array(
													  '%d', '%s', '%s', '%s','%s',
													  '%s', '%s', '%s','%s','%s',
													  '%s', '%s', '%s','%s','%s',
													  '%s', '%s','%s','%s', '%s',
													  '%s', '%s','%s','%s'
													)
												);
			}
            if($campStatusRes){
                $insertQry = $wpdb->last_query;
                fwrite($handle, "Query($key):\n $insertQry");
            }else{
                $lastError = "";
                echo $lastError = $wpdb->last_error;
                ob_start();
                $wpdb->print_error();
                $lastError .= ob_get_clean();
                $fieldStrArr = array();
                foreach($fieldArr as $fieldKey => $fieldVal){
                    $fieldStrArr[] = "`$fieldKey` = '".mysql_escape_mimic($fieldVal)."'";//mysql_real_escape_string($fieldVal);
                }
				if(!empty($fieldArr['site_name'])){
					$insertQry = "INSERT INTO `".CAMP_SITE_TABLE."` SET ".implode(",", $fieldStrArr);
					$wpdb->query( $insertQry);
				}
                fwrite($logHandle, "\n"."Failed to add record for site(".date('Y-m-d H:i:s')."):". $itemArr['site_id']."\n{$insertQry};\n{$lastError}\n");
            }
            //<<<Save in database
            $dataArr[] = $itemArr;
        }
        fclose($handle);
        fclose($logHandle);
        return true;
        //echo json_encode(array('message' => "Under development",'status_code' => 400  ));die;
    }
    
    function frc_cp_images($photo, $isMain = 0, $itemID){
		//return;
        global $wpdb;
        $wpdb->show_errors = true;
        $uploadFolder = wp_upload_dir();
        $uploadBaseDir = $uploadFolder['basedir'].DS;
        $filePath = $uploadFolder['basedir'].DS.'data_v1'.DS.'photos_rasu.sql';
        $logPath = $uploadFolder['basedir'].DS.'data_v1'.DS.'error_log_photos.txt';
        $handle = fopen($filePath, "a+");
        $logHandle = fopen($logPath, "a+");
        //=>/home/frcconz/public_html/mobile/wp-content/uploads
        $findStr = "/uploads";
        $position = strpos($photo, $findStr,0);
        $position = $position + strlen($findStr) + 1;
        $resStr = substr($photo, $position);
        $resArr = explode("/", $resStr);
        $imageFile = end($resArr);
        array_pop($resArr);
        $itemAbsoluteDir = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr);
		wp_mkdir_p( $itemAbsoluteDir );
		if(is_dir($itemAbsoluteDir)){
            $destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
            $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
            if(copy($sourcePath, $destPath)){
				$recordExist = $wpdb->get_results( "SELECT `site_id` FROM `".SITE_PHOTOS_TABLE."` WHERE `site_id` = $itemID AND `photo_name` = '$imageFile' AND `path` = '".DS.implode("/",$resArr)."'" );
				if(is_array($recordExist) && count($recordExist) >= 1){
					 ;//Record exist; do nothing
				}else{
					$status = $wpdb->insert( SITE_PHOTOS_TABLE,
										array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
										array( '%d', '%s', '%s', '%d' )
							);
					$photoQry = $wpdb->last_query;
					if($status){
						$insertQry = $wpdb->last_query;
						fwrite($handle, "\n".$insertQry);
					}else{
						//Log Errors
						$lastError = $wpdb->last_error;
						ob_start();
						$wpdb->print_error();
						$lastError .= ob_get_clean();
						fwrite($logHandle, "\n"."Failed to add record for site(".date('Y-m-d H:i:s')."):". $itemID."\n{$lastError}\n");
					}
					//echo "\nquery: ".$wpdb->last_query;
				}
            }else{"Failed to copy";}
        }elseif(wp_mkdir_p ( $itemAbsoluteDir)){ // $itemAbsoluteDir, '0777', true
            $destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
            $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
            if(copy($sourcePath, $destPath)){
				$recordExist = $wpdb->get_results( "SELECT `site_id` FROM `".SITE_PHOTOS_TABLE."` WHERE `site_id` = $itemID AND `photo_name` = '$imageFile' AND `path` = '".DS.implode("/",$resArr)."'" );
				if(is_array($recordExist) && count($recordExist) >= 1){
					 ;//Record exist; do nothing
				}else{
					$status = $wpdb->insert( SITE_PHOTOS_TABLE,
										array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
										array( '%d', '%s', '%s', '%d' )
							);
					$photoQry = $wpdb->last_query;
					if($status){
						$insertQry = $wpdb->last_query;
						fwrite($handle, "\n".$insertQry);
					}else{
						//Log Errors
						$lastError = $wpdb->last_error;
						ob_start();
						$wpdb->print_error();
						$lastError .= ob_get_clean();
						//fwrite($logHandle, "\n"."Failed to add record for site(".date('Y-m-d H:i:s')."):". $itemID."\n{$lastError}\n");
					}
					//echo "\nquery: ".$wpdb->last_query;
				}
            }else{"Failed1";}
        }else{
            $campDir = $uploadBaseDir.'camp_photos';
            //This logic needs to be refined.
        }
        fclose($handle);
        fclose($logHandle);
        return true;
	 }
	 
	function mysql_escape_mimic($inp) { 
		if(is_array($inp)) 
			return array_map(__METHOD__, $inp); 
	
		if(!empty($inp) && is_string($inp)) { 
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
		} 
	
		return $inp; 
	}
	
	function mres($value){
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		return str_replace($search, $replace, $value);
	}
?>