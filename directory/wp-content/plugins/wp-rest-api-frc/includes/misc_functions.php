<?php
	//Get list of premium users
	function frc_premium_users($response){
		global $wpdb;
		global $json_api;
		$total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` ORDER BY `id` DESC");
		$post_per_page = 50;
		$page = isset( $_REQUEST['cpage'] ) ? abs( (int) $_REQUEST['cpage'] ) : 1;
		$offset = ( $page * $post_per_page ) - $post_per_page;
		$qry = "SELECT * FROM `frc_premium_users`";
		$rsltObjs = $wpdb->get_results( $qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
		if(count($rsltObjs)>=1){
			$premiumUserData = array();
			$arrayKey = 0;
			foreach($rsltObjs as $key => $reslt){
				$billData = unserialize($reslt->billing_shipping);
				$phpDate = strtotime( $reslt->paid_date );
				$paidDate = date( 'M j, y h:i A', $phpDate );
				extract($billData);
				$premiumUserData[$arrayKey]['user_id'] = $reslt->user_id;
				$premiumUserData[$arrayKey]['fname'] = $_billing_first_name;
				$premiumUserData[$arrayKey]['lname'] = $_billing_last_name;
				$premiumUserData[$arrayKey]['email'] = $reslt->email;
				$premiumUserData[$arrayKey]['phone'] = $_billing_phone;
				$premiumUserData[$arrayKey]['pmt_date'] = $paidDate;
				$premiumUserData[$arrayKey]['company'] = $_billing_company;
				$premiumUserData[$arrayKey]['address1'] = $_billing_address_1;
				$premiumUserData[$arrayKey]['address2'] = $_billing_address_2;
				$premiumUserData[$arrayKey]['city'] = $_billing_city;
				$premiumUserData[$arrayKey]['state'] = $_billing_state;
				$premiumUserData[$arrayKey]['postcode'] = $_billing_postcode;
				$arrayKey++;
			}
		}
		$headers = wp_remote_retrieve_headers( $response );
		//print_r($_SERVER);
		//print_r($response);
		//print_r($headers = wp_get_http_headers());
		//print_r($_REQUEST);
		foreach (getallheaders() as $name => $value) {
			//echo "\n$name => $value";
			if($name == 'token' || $name == 'Token'){
				$json_api->query->cookie = $_GET['cookie'] = base64_decode($value);
				$tokenPresent = true;
			}
		}//headers_list another function for gettting headers
		//$headers = apache_request_headers();
		
		//foreach ($headers as $header => $value) {
		  //  echo "$header: $value <br />\n";
		//}
		//PRINT_R($json_api->login);
		return new WP_REST_Response(array('status_code' => 200,'total_record'=>$total,'page_no'=>$page,'page_size'=>$post_per_page, 'data' => $premiumUserData), 200);
    }
    
	 //Get root category of camp sites
	 if(!function_exists('get_root_category')){
		  function get_root_category($campCategories = array()){
			   //we would have only one parent business category only. This is confirmed. From Glen on Skype on Mar 7, 2017
			   global $campCats, $partnerTCatId;
			   $businessCats = $partnerTCatId;
			   $camp_cats = $campCats;
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
	 }
	
	 if(!function_exists('frc_partner_cat_ids')){
		  //This function will return category ids business categories and their childs
		  function frc_partner_cat_ids(){
			   global $partnerTCatId;
			   $partnerCatIds = array();
			   foreach($partnerTCatId as $partnerCatId){
				   $childArr = get_term_children( $partnerCatId, 'ait-dir-item-category' );
				   foreach($childArr as $childCatID){
					   $partnerCatIds[] = $childCatID;
				   }
			   }
			   return array_merge($partnerCatIds,$partnerTCatId);
		  }
	 }
	 
	 function frc_mobile_uploads(){
		  //https://www.freerangecamping.com.au/directory/wp-json/wp/v2/mobile_uploads
		  set_time_limit(60);
		  ini_set('max_execution_time', 0);
		  $mobUploadDir = WP_CONTENT_DIR.DS."mobile_uploads".DS;
		  $currentTime = new DateTime('now');
		  $files = scandir($mobUploadDir);
		  $contentURL = content_url();
		  $haveRecords = false;
		  $txtCounter = $imgCounter = 0;
		  $attachments = array();
		  foreach($files as $sngFile){
			   if (!in_array($sngFile,array(".","..", '.ftpquota'))){
					$downloadURL = $contentURL.DS.'mobile_uploads'.DS.$sngFile;
					$fileModTime = new DateTime(date('Y-m-d G:i:s', filemtime(WP_CONTENT_DIR.DS."mobile_uploads".DS.$sngFile)));
					$difference = $currentTime->diff($fileModTime);
					//$purchaseTime = new DateTime('Y-m-d G:i:s');
					$splitArr = explode("_", $sngFile);
					$postID = $splitArr[0];
					$temp = $splitArr[1];
					$temp1 = $splitArr[2];
					//list($postID, $temp, $temp1) = split("_",$sngFile); //split function is removed from php version 7.0
					//New format :  7232_FRC_44408_1501129202641.txt. Format change noticed on 28th July.
					//<post_id>_FRC_<user_id>_<timestamp>.txt
					//$temp = FRC_<user_id>_<timestamp>.txt
					
					
					$postTitle = get_the_title($postID);
					$postLink = get_permalink($postID);
					//$postEditLink = get_edit_post_link($postID);
					$postEditLink = WP_SITEURL."/wp-admin/post.php?post=$postID&action=edit";
					$diffInMins = ($currentTime->getTimestamp() - $fileModTime->getTimestamp()) / 60;
					
					if($diffInMins <= 10){
						 $haveRecords = true; //echo "\ntemp:".$temp;echo "\n$downloadURL";
						 if($temp == 'FRC'){
							  $userID = $temp1;
						 }else{
							  list($t, $userID) = split("-",$temp);
						 }
						 $userObj = get_userdata($userID);
						 //echo "\n\n".$userID;echo "\n".$userObj->user_email;continue;
						 
						 $userEmail = $userObj->user_email;
						 $user_login = "<a href='mailto:$userEmail?Subject=Re: For Information submitted for Site: $postTitle'>".$userObj->user_login."</a>";
						 //$user_login = $userObj->user_login;
						 $diffInMins = number_format($diffInMins, 2);
						 
						 if('txt' == strtolower(substr($sngFile, -3))){
							  $txtCounter++;
							  $bodyMsg = <<<BODY_ARR
						 <b>Record $txtCounter</b>:<br/>
						 User {$user_login} have uploaded file <strong>{$sngFile}</strong> for site "<a href='$postEditLink'>$postTitle</a>" <strong>$diffInMins mins</strong> ago.
						 <br/><br/><a href='$downloadURL'>Click here to download file</a>
						 <br/><br/><strong>Site ID:<strong/> <a href='$postLink'>$postID</a>
						 <br/><br/><strong>User ID:<strong/> $userID
						 <br/><br/><strong>User Email:<strong/> $userEmail
						 <br/><br/><strong>Below is the attachment Detail:</strong><br/><br/>
						 Attachment Name: $sngFile
BODY_ARR;
							  $txtMailBodyArr[] = $bodyMsg;
							  $txtAttachments[] = WP_CONTENT_DIR.DS."mobile_uploads".DS.$sngFile;
						 }else{
							  $imgCounter++;
							  $bodyMsg = <<<BODY_ARR
						 <b>Record $imgCounter</b>:<br/>
						 User {$user_login} have uploaded file <strong>{$sngFile}</strong> for site "<a href='$postEditLink'>$postTitle</a>" <strong>$diffInMins mins</strong> ago.
						 <br/><br/><a href='$downloadURL'>Click here to download file</a>
						 <br/><br/><strong>Site ID:<strong/> <a href='$postLink'>$postID</a>
						 <br/><br/><strong>User ID:<strong/> $userID
						 <br/><br/><strong>User Email:<strong/> $userEmail
						 <br/><br/><strong>Below is the attachment Detail:</strong><br/><br/>
						 Attachment Name: $sngFile
BODY_ARR;
							  $imgMailBodyArr[] = $bodyMsg;
							  $imgAttachments[] = WP_CONTENT_DIR.DS."mobile_uploads".DS.$sngFile;
						 }
					}
				   //Format: {site_id}_FRC-{user_id}_{some_identifier}.fileExt
			   }
		  }
		  if($haveRecords){
			   $headers = array('Content-Type: text/html; charset=UTF-8');//text/html
			   $headers[] = 'Cc: Glen Wilson <info@freerangecamping.com.au>';
			   $headers[] = 'From: Free Range Camping <noreply@freerangecamping.com.au';
			   $randNum = rand(10000,99999);
			   if(count($imgMailBodyArr) >= 1){
					$subject = "User uploaded image from App - $randNum";
					$emailBody = implode("<br/><br/>", $imgMailBodyArr);
					//print_r($imgAttachments);echo $emailBody;die;
					wp_mail('kalyanrajiv@gmail.com', $subject, $emailBody, $headers, $imgAttachments);
			   }
			   if(count($txtMailBodyArr) >= 1){
					$subject = "Update Site info from App- $randNum";
					$emailBody = implode("<br/><br/>", $txtMailBodyArr);
					//print_r($imgAttachments);echo $emailBody;//die;
					wp_mail('kalyanrajiv@gmail.com', $subject, $emailBody, $headers, $txtAttachments);
			   }
			   
		  }
		  return new WP_REST_Response(array('status_code' => 200, 'data' => array('Script executed successfully!')), 200);
	 }
	 
	 function frc_modified_items(){
		  set_time_limit(180);
		  ini_set('max_execution_time', 0);
		  $itemsModified = $itemsUnPublished = array(); //global declaration
		  /*$output = shell_exec('ls -lart');
		  echo "<pre>$output</pre>";
		  echo exec('whoami');die("FasdF");*/
		  //Note: zip -r filename.zip file1 file2 file3 /usr/work/school
		  //Ref:https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/4/html/Step_by_Step_Guide/s1-managing-compressing-archiving.html
		  //https://www.freerangecamping.com.au/directory/wp-json/wp/v2/modified_posts
		  ///home/freerang/public_html/directory/wp-content/plugins/wp-rest-api-frc/includes
		  global $wpdb, $itemsModified, $itemsUnPublished;
		  if('203.134.198.105' == $_SERVER["REMOTE_ADDR"]){
			   $query = "SELECT * FROM  `gwr_postmeta` WHERE `meta_id` = 215419";
			   $metaResult = $wpdb->get_results($query, OBJECT);
			   //print_r($metaResult);
			   if(count($metaResult) >= 1){
					$metaData = unserialize($metaResult[0]->meta_value);
			   }
			   print_r($metaData);
			   die("fasdf");
		  }
		  //https://codex.wordpress.org/Class_Reference/WP_Query
		  $time_start = microtime(true);
		  //SELECT DATE_FORMAT(   DATE_SUB( CURRENT_TIMESTAMP( ) , INTERVAL 30 MINUTE ) , '%M %D, %Y %r') AS DD
		  //SELECT DATE_FORMAT(   DATE_SUB(  utc_timestamp(NOW( ) ), INTERVAL 30 MINUTE ) , '%M %D, %Y %r') AS DD
		  $dbTimeAgo = $timeAgo  = mktime(date("H"), date("i")-10, date("s"), date("m")  , date("d"), date("Y"));
		  //$dbTimeAgo = $timeAgo  = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-5, date("Y"));
		  //8:28 pm, there was only one listing
		  $timeAgo = gmdate("M d Y H:i:s", $timeAgo);
		  $dbTimeAgo = gmdate("Y-m-d H:i:s", $dbTimeAgo);
		  $timeNow = gmdate("M d Y H:i:s", mktime());
		  $dbTimeNow = gmdate("Y-m-d H:i:s", mktime());
		  $postsTable = $wpdb->prefix."posts";
		  
		  $dbQry = "SELECT `$postsTable`.ID FROM `$postsTable`  WHERE (`$postsTable`.`post_modified_gmt` > '$dbTimeAgo' AND `gwr_posts`.`post_modified_gmt` < '$dbTimeNow') AND `$postsTable`.`post_type` = 'ait-dir-item' AND `$postsTable`.`post_status` IN('publish','future','draft','pending','trash')  ORDER BY `$postsTable`.`post_date` DESC";
		  if('112.196.97.45' == $_SERVER["REMOTE_ADDR"]){
			   $dbQry = "SELECT `$postsTable`.ID FROM `$postsTable`  WHERE ID IN(31544) ORDER BY `$postsTable`.`post_date` DESC";
			   //$dbQry = "SELECT `$postsTable`.ID FROM `$postsTable`  WHERE (`$postsTable`.`post_modified_gmt` > '$dbTimeAgo' AND `gwr_posts`.`post_modified_gmt` < '$dbTimeNow') AND `$postsTable`.`post_type` = 'ait-dir-item' AND `$postsTable`.`post_status` IN('publish','future','draft','pending','trash')  ORDER BY `$postsTable`.`post_date` DESC";
			   //echo $dbQry;
			   //die;
			  ;
		  }
		  echo $dbQry;
		  $modifiedItems = $wpdb->get_results($dbQry, OBJECT);
		  $modifiedItemArr = array();
		  foreach($modifiedItems as $modifiedItem) $modifiedItemArr[] = $modifiedItem->ID;
		  
		  $args = array(
						 'post_type' => 'ait-dir-item',
						 'posts_per_page' => -1,
						 'date_query' => array(
												  array(
													   'column' => 'post_modified_gmt',
													   'after'  => $timeAgo
												  ),
												  array(
													   'column' => 'post_modified_gmt',
													   'before' => $timeNow //'0 minute ago',
												   ),
											 ),
						 'post_status' => array( 'pending', 'draft', 'future','publish', 'trash' )
						 );
		  $args = array(
						'post_type' => 'ait-dir-item',
						'posts_per_page' => -1, //Big mistake by me. Corrected on 18th May
						'post__in' => $modifiedItemArr,
						'post_status' => array( 'pending', 'draft', 'future','publish', 'trash' )
						); //Modified on Apr 25, 2017
		  /*
		  $skippedPosts = array(17547,19082,30963,30920,30795,30365,31053,25898,25897,25945,28203,22396,25903,21781,26228,22277);
		  $skippedPosts = array(17547,19082,30963,30920,30795,30365);
		  $skippedPosts = array(30963,30920,30795,30365);
		  $args = array( 'post_type' => 'ait-dir-item', 'post__in' => $skippedPosts ); //temporary
		  */
		  
		  if(count($modifiedItemArr) >= 1){
			   $items = $the_query = new WP_Query( $args );
		  }else{
			   $items = new stdClass();
			   $items->posts = array();
		  }
		  //echo "Last SQL-Query: {$items->request}";die;
		  $postIDArr['ID'] = array();
		  $resultsArr = [];
		  
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."frc.db";
		  
		  $haveRecords = false;
		  //foreach ($items->posts as $key => $item) {echo "\n<br/>".$item->ID;}die;
		  //alan.f@cretve.com
		  
		  foreach ($items->posts as $key => $item) {
			   $dataArr = array();
			   echo "ITEM ID:".$item->ID;
			   $postIDArr['ID'][] = $item->ID;
			   if($item->post_status == 'publish'){
					$itemsModified[$item->ID] = $item->post_title;
					$haveRecords = true;
					$fullRating = '0';
					if(is_array($item->rating) && array_key_exists('full', $item->rating)){
						 $fullRating = $item->rating['full'];
					}
					
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
										 array("<strong>","</strong>"),
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
										 array("<strong>","</strong>"),
										 $tgtStr
										 );
						 }
						$item->post_content = str_replace($tgtStr, $replaceWith, $item->post_content);
					}
					//<<<<<<<<<<<<<find tag to be replaced<<<<<<<<<<<<<
					
					//die($item->post_content);
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
							$youTubeStr = "<div><a href='http://www.youtube.com/embed/{$cleverVStr}?rel=0&amp;showinfo=0?ecver=2'><img src='https://www.freerangecamping.com.au/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;' /></a></div>";
							//removed from style position: absolute;
							$item->post_content = str_replace($cleverUStr, $youTubeStr, $item->post_content);
						}
					}
					
				    //Start: Added on 24th Aug, 2017
        		    $couponCount = substr_count($item->post_content,'[wlm_private');
        		    for($i = 0; $i < $couponCount; $i++){
        			   $pos1 = strpos($item->post_content, "[wlm_private");
        			   if($pos1){
        				   $pos2 = strpos($item->post_content, "]", $pos1);
        				   $couponStr = substr ( $item->post_content , $pos1, ($pos2-$pos1)+1 );
        				   $item->post_content = str_replace($couponStr, "", $item->post_content);
        				   $item->post_content = str_replace("[/wlm_private]", "", $item->post_content);
        			   }
        		    }
        		    //End: Added on 24th Aug, 2017
					
					//Start: Replace iframe tag
					$startTag = "<iframe";
					$endTag = "</iframe>";
					$iframeCount = substr_count($item->post_content, $startTag);
					for($j = 0; $j < $iframeCount; $j++){
						 $pos1 = strpos($item->post_content, $startTag);
						 if($pos1){
							  //http://simplehtmldom.sourceforge.net/
							  $pos2 = strpos($item->post_content, $endTag, $pos1)+ strlen($endTag);
							  $iframeStr = substr ( $item->post_content , $pos1, ($pos2-$pos1)+1 );
							  preg_match( '/src="([^"]*)"/i', $iframeStr, $matches ) ;
							  if(count($matches) >=1){
								   $iframeSrc = $matches[1];
								   $iframeUTubeStr = "<div><a href='$iframeSrc'><img src='https://www.freerangecamping.com.au/directory/wp-content/uploads/2017/03/youtube.png' height='80px;' width='80px;'></a></div>";
								   $item->post_content = str_replace($iframeStr, $iframeUTubeStr, $item->post_content);
							  }
							  /*
							  $xml = simplexml_load_string("<root>".$iframeStr."</root>");
							  $list = $xml->xpath("//@src");
							  if(array_key_exists(0, $list) && !empty($iframeSrc = xml_attribute($list[0], 'src'))){}
							  $iframe = $sxml->iframe->attributes();echo $iframe["src"];
							  */
						 }
					}
					echo "\n\n------------------------------\n\n";
					//echo $item->post_content;
					echo "\n\n------------------------------\n\n";
					//End: Replace iframe tag
					
					//Start: code for clean formatting for Mobile Apps
					//$item->post_content = str_replace("\r\r","",$item->post_content);	//	\n
					//$item->post_content = str_replace("\r","",$item->post_content);		//	\n
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
											 //'\n',
											 '<strong>ABOUT US</strong>',
											 '<strong>FRC CLUB OFFER</strong>',
											 '<strong>SERVICE OFFERED</strong>',
											 '<strong>SHORTCUT</strong>',
										);
					$item->post_content = str_replace($headings,$new_headings,$item->post_content);
					//$item->post_content = str_replace("\n\n","\n", $item->post_content);
					//$item->post_content = str_replace("\n\r","\n", $item->post_content);
					//$item->post_content = str_replace("\r\n","\n", $item->post_content);
					//$item->post_content = str_replace(array("\n\n", "\\n"),array("\n", "\n"),$item->post_content);
					$item->post_content = str_replace(array('\r\n'), array('\n'), $item->post_content);
					$item->post_content = str_replace(array('\n\r'), array('\n'), $item->post_content);
					$item->post_content = str_replace(array('\r'), array('\n'), $item->post_content);
					//$item->post_content = str_replace(array('\n\r','\r\n', '\r'), array('\n','\n','\n'), $item->post_content);
					//$item->post_content = str_replace(array('\n\n'),array('\n'), $item->post_content);
					//$item->post_content = str_replace(array("\\n"),array("\n"),$item->post_content);
					$item->post_content = preg_replace( "/\n/", '\n', $item->post_content ); //this is doing magic
					//echo $item->post_content;
					//die;
					
					//End: code for clean formatting for Mobile Apps
					$dataArr = array(
								   'site_name' => $item->post_title,
								   'site_id' => $item->ID,
								   'post_id' => $item->ID,
								   'site_description' => ($item->post_content), //do_shortcode
								   'status' => $item->post_status,
								   'slug' => $item->post_name,
								   'rating' => $fullRating,
							   );
					//echo "<pre>";print_r($dataArr);echo "</pre>";continue;
					$resultsArr[] = add_data_2_post($dataArr, $item->ID);
			   }else{
					$haveRecords = true;
					$itemsUnPublished[$item->ID] = $item->post_title;
					//delete post from camp_sites and sqlite db
					$postID = $item->ID;
					$dir = "sqlite:$mobDataDir";
					$dbh = new PDO($dir) or die("cannot open the database");
					$selQry = "SELECT `site_id` FROM `camp_sites` WHERE `site_id` = $postID";
					//echo "\n<br/>".$selQry;
					$sth = $dbh->prepare($selQry);
					$sth->execute();
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					$sth->closeCursor(); //$sth->rowCount();
					$sth = null;
					if(count($result) >=1){
						 $haveRecords = true;
						 try{
							  echo $delQry = "DELETE FROM `camp_sites` WHERE `site_id` = $postID";
							  $dbh->Query($delQry);
						 }catch(PDOException $e){
							  echo $e->getMessage();
						 }
						 try{
							  echo $delQry = "DELETE FROM `site_photos` WHERE `site_id` = $postID";
							  $dbh->Query($delQry);
						 }catch(PDOException $e){
							  echo $e->getMessage();
						 }
					}
					$wpdb->delete('camp_sites', array( 'site_id' => $postID ), array('%d'));
					$wpdb->delete('site_photos', array( 'site_id' => $postID ), array('%d'));
					$dbh->connection = null;
			   }
		  }
		  
		  $time_end = microtime(true);
		  //dividing with 60 will give the execution time in minutes other wise seconds
		  $execution_time = ($time_end - $time_start)/60;
		  //execution time of the script
		  
		  
		  //Step 2: create respective folders for offline data/zip files [Provided we have atleast one record to process]
		  if($haveRecords){
			   $dataModified = frc_create_n_compress_db();
			   $dataVer = $dataModified['new_version'];
			   $downloadURL = $dataModified['download_link'];
			   
			   $itemModifiedStr = "";
			   if(is_array($itemsModified) && count($itemsModified)){
					$counter = 0;
					foreach($itemsModified as $itemID => $itemTitle){
						 $counter++;
						 $authArr = frc_item_author_details($itemID);
						 $authName = $authArr['name'];
						 $authEmail = $authArr['email'];
						 $authID = $authArr['id'];
						 $itemModifiedStr.="<br/> {$counter}. {$itemTitle} - $itemID Last Edited by: <strong>{$authName}</strong> [Email:$authEmail, $authID]";
					}
			   }
			   
			   $unpublishedItemStr = "";
			   if(is_array($itemsUnPublished) && count($itemsUnPublished)){
					$counter = 0;
					foreach($itemsUnPublished as $itemID => $itemTitle){
						 $counter++;
						 $authArr = frc_item_author_details($itemID);
						 $authName = $authArr['name'];
						 $authEmail = $authArr['email'];
						 $authID = $authArr['id'];
						 $unpublishedItemStr.="<br/> {$counter}. {$itemTitle} - $itemID Unpublished By: <strong>{$authName}</strong> [Email:$authEmail], $authID";
					}
			   }
			   
			   //start: email body
			   $headers[] = 'Content-Type: text/html; charset=UTF-8';
			   //$headers[] = 'Cc: Rob Catania <rob@freerangecamping.com.au>';
			   $headers[] = 'Cc: Glen Wilson <info@freerangecamping.com.au>';
			   //$headers[] = 'Cc: Alan <alan.f@cretve.com>';
			   
			   $subject = "New offline db Version Released - $dataVer";
			   $emailBody = <<<EMAIL_BODY
			   Below is the link to download new offline database:
			   <br/><a href='{$downloadURL}'>Download</a><br/><br/>
			   <br/><b>List of Modified Items in this release are:</b>
			   {$itemModifiedStr}
			   <br/><br/><b>List of Items Removed/Unpublished in this release are:</b>
			   {$unpublishedItemStr}
EMAIL_BODY;
			   $created = current_time('mysql');
			   $status = $wpdb->insert( 'frc_offline_db_versions',
										array( 'version' => $dataVer, 'description' => $emailBody, 'created' => $created),
										array( '%s', '%s', '%s' )
							);
			   
			   //End: email body
			   //wp_mail('kalyanrajiv@gmail.com', $subject, $emailBody, $headers);
			   die("script executed successfully");
			   //frc_create_n_compress(); //
		  }else{
			   //wp_mail( 'kalyanrajiv@gmail.com', 'No Change in offline database version-'.rand(1111, 9999), "hi");
			   echo "Nothing to compress";
		  }
		  echo '\n<br/><b>Total Execution Time:</b> '.$execution_time.' Mins';die;
		  //Creating respective folders and compressing data
		  return new WP_REST_Response(array('status_code' => 200, 'data' => array()), 200);
	 }
	 
	 function frc_create_n_compress_db(){
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data";
		  if(!is_dir($mobDataDir)) wp_mkdir_p($mobDataDir);
		  $zipFileVer = 'data_v1.zip';	//Param 1 Default File version
		  $dataVer = '1.0';	//Param 2 - Default Data version
		  
		  $rootFolder = date("Ymd");	//Param 3
		  $rootFolder = '20170406';	//Note: This needs not to be changed.
		  
		  $yesterday = date('Ymd',strtotime("-1 days"));
		  $yesterday = '20170405';	//Note: This needs not to be changed.
		  
		  $day = date('D');
		  //Start: using statically for production purppose
		  /*
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  $defaulDBParams = array(
									 'rootDBFolder' => "20170322_db",
									 'rootFolder' => '20170322',
									 'zipFileVer' => 'data_v7.zip',
									 'dataVer' => '2.22',
									 'largeDataVer' => '2.22',
									 );
		  update_option($frcOption, $defaulDBParams);
		  */
		  //End: using statically for production purppose
		  
		  $frcOption = 'frc_offline_db_params_dev'; //for development purpose
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  //delete_option($frcOption);
		  
		  $defaulDBParams = array(
								   //1. For daily update
								   'rootDBFolder' => "{$rootFolder}_db",	//1. folder name -need not to be changed after each update
								   'dataVer' => $dataVer,					//2. (version)
								   'zipFileVer' => $zipFileVer,				//3. Zip file version
								   'zipFilePath' => '',						//4. We need this parameter if we are dynamically changing folder name otherwise not.
								   
								   //2. For large file - should be updated weekly.
								   'rootFolder' => $rootFolder,				//1. folder name -need not to be changed after each update
								   'largeDataVer' => $dataVer, 				//2. (version)
								   'zipLargeFileVer' => $zipFileVer, 		//3. For large file
								   'zipLargeFilePath' => '',				//4. We need this parameter if we are dynamically changing folder name otherwise not.
								   
								   //3. For incremental update.
								   'incFolder' => "{$rootFolder}_inc", 		//1. folder name -need not to be changed after each update
								   'incDataVer' => $dataVer, 				//2. (version)
								   'zipIncFileVer' => $zipFileVer, 			//3. For large file
								   'zipIncFilePath' => '',					//4. We need this parameter if we are dynamically changing folder name otherwise not.
								   
								   'weekly_processed_large_file' => 0, //For large file
								   //Note: we will generate weekly file only once. When cron will be running we will generate large file again and only once in Sunday and incremental updated file should be set to empty.
									 
								   );
		  
		  if($frcDBParams = get_option($frcOption, $defaulDBParams)){
			   //if existing, then update offline data params
			   
			   //1. For daily update
			   $existingZipFileVer = (int) str_replace(array('data_v', '.zip'), '', $frcDBParams['zipFileVer']);
			   $oldPhotoDataZipFileVer = $existingZipFileVer;
			   $newZipFileVer = $zipFileVer = $existingZipFileVer + 1;
			   $zipFileVer = "data_v{$zipFileVer}.zip";	//Param 1
			   $existingZipFileVer = "data_v{$existingZipFileVer}.zip";	//Param 1
			   $oldDataVer = (float) $frcDBParams['dataVer'];
			   $dataVer = $oldDataVer + 0.01;	//Param 2
			   //echo "$oldDataVer - $dataVer";
			   $dbFolderVer = $rootFolder."_db";	//Param 4
			   $incFolderVer = $rootFolder."_inc";	//Param 4
			   if($day != 'Sun') $weeklyProcessedLargeFile = 0;
			   
			   //2. For large file.
			   if(date('F j, Y') == date('F j, Y', strtotime('last mon of this month')) && $frcDBParams['weekly_processed_large_file'] != 1){
					//date('F j, Y', strtotime('first wed of last month +1 week'));
					/*
					Australia time is utc+10
					India time is UTC+05:30
					For this reason, this file can be created first with respect to india's time zone.
					Large file needs to be updated in $rootFolder by copying all content of /wp-content/uploads/camp_photos/ and should be compressed as well.
					also need to empty camp_photos_incremental
					Cron will rename large file and incremental folder cam be emptied manually.
					Manual operation can be done at 2 am australia time (Indian Time : 9:30 PM) on Sunday
					 */
					{
						 $oldPhotoDataVer = (float) $frcDBParams['largeDataVer'];
						 $newPhotosDataVer = $oldPhotoDataVer + 0.01;	//Param 2
					}//Param-2 for data version of inc file
					
					{
						 $existingLargeZipFileVer = (int) str_replace(array('data_v', '.zip'), '', $frcDBParams['zipLargeFileVer']);
						 $newLargeFileVer = $existingLargeZipFileVer + 1;
						 $existingLargeZipFile = $frcDBParams['zipLargeFileVer'];
						 //Old Large Zip file which will be manually updated. Script code will do nothing for this file.
						 $newLargeFileVer = "data_v{$newLargeFileVer}.zip";
						 //Old Large Zip File which will be renamed - Manual process
						 $newLargeFilePath = $uploadDirArr['basedir'].DS.$rootFolder.DS;	//Param - 4
						 $newLargeRootFolder = $rootFolder;
					}//Param-3 for zip file version
			   }else{
					//Nothing will be changed for large file.
					$newPhotoDataVer = '6.63';(float) $frcDBParams['largeDataVer']; //now static
					$newLargeFileVer = $frcDBParams['zipLargeFileVer'];
					$newLargeFilePath = $frcDBParams['zipLargeFilePath'];
					$newLargeRootFolder = $frcDBParams['rootFolder'];
			   }
			   
			   //3. For incremented file.
			   {
					$oldIncDataVer = (float) $frcDBParams['incDataVer'];
					$newIncDataVer = $oldIncDataVer + 0.01;	//Param 2
			   }//Param-2 for data version of inc file
			   
			   {
					$existingIncZipFileVer = (int) str_replace(array('data_v', '.zip'), '', $frcDBParams['zipIncFileVer']);
					$newIncFileVer = $existingIncZipFileVer + 1;
					$existingIncZipFile = $frcDBParams['zipIncFileVer'];	//Old Inc Zip file which will be deleted.
					//$newIncZipFile = "data_v{$newIncFileVer}.zip";	//New Inc Zip File which will be created
					$newIncZipFile = "data_inc.zip";	//New Inc Zip File which will be created
					$zipIncFilePath = $uploadDirArr['basedir'].DS.$rootFolder."_inc".DS;	//Param - 4
			   }//Param-3 for zip file version
			   
			   
			   
			   $defaulDBParams = array(
										//1. For daily update
										'rootDBFolder' => $dbFolderVer,	//Param 4 - . folder name
										'zipFileVer' => $zipFileVer,	//Param 1 - Zip file version
										'dataVer' => $dataVer,	//Param 2 - Data Version
										'zipFilePath' => $uploadDirArr['basedir'].DS.$rootFolder."_db".DS,	//Param - 4,
										
										//2. For large file - should be updated weekly.
										
										'rootFolder' => $rootFolder,	//Param 3
										'zipLargeFileVer' => $newLargeFileVer,	//Param 4
										'zipLargeFilePath' => $newLargeFilePath,
										'largeDataVer' => $newPhotoDataVer,
										
										//3. For incremental update.
										'incFolder' => "{$rootFolder}_inc", //1. folder name
										'incDataVer' => $newIncDataVer, //2. (version)
										'zipIncFileVer' => $newIncZipFile, //3. For inc zip file
										'zipIncFilePath' => $zipIncFilePath,	//4 - file path
										
										'weekly_processed_large_file' => $weeklyProcessedLargeFile, //For large file
									 );
			   
			   //Start: copying /uploads/mobile_app_data/camp_photos.zip to /uploads/<data_v<v_no>/camp_photos.zip
			   //Extracting this zip file in folder /uploads/<data_v<v_no>/
			   //After extraction, deleting zip file from /uploads/<data_v<v_no>/camp_photos.zip
			   
			   {
					$rootFolderPath = $uploadDirArr['basedir'].DS.$rootFolder;
					$rootDBFolderPath = $uploadDirArr['basedir'].DS.$dbFolderVer;
					$rootIncFolderPath = $uploadDirArr['basedir'].DS.$incFolderVer;
					$dataFolderPath = $uploadDirArr['basedir'].DS.$rootFolder.DS.'data_v'.$newZipFileVer;
					wp_mkdir_p($rootFolderPath);
					wp_mkdir_p($rootDBFolderPath);
					wp_mkdir_p($rootIncFolderPath);
					//wp_mkdir_p($dataFolderPath);
					echo "\n$rootFolderPath\n$rootDBFolderPath\n$dataFolderPath\n";
			   }//Step C-1: creating new required directories Format: YYYYMMDD and Format: YYYYMMDD_db
			   
			   {
					$sourceFile = $uploadDirArr['basedir'].DS."mobile_app_data".DS.'frc.db';
					$destination1 = $uploadDirArr['basedir'].DS.$dbFolderVer.DS.'frc.db';
					copy ($sourceFile, $destination1);
					//Copying to pure database folder
					echo $addFile = $uploadDirArr['basedir'].DS.$dbFolderVer.DS."frc.db";
					$filename = $uploadDirArr['basedir'].DS.$dbFolderVer.DS.$zipFileVer;
					$downloadURL = "https://www.freerangecamping.com.au/directory/wp-content/uploads/{$dbFolderVer}/{$zipFileVer}";
					frc_zipIt($addFile, $filename, false);
					unlink($addFile);
					$destination2 = $dataFolderPath.DS.'frc.db';
					//copy ($sourceFile, $destination2);
			   }
			   //Step C-2: Copying frc.db to database directories and compressing/unlinking[frc.db after successful compression] in /uploads/{$dbFolderVer}_db/
			   
			   {
					$day = date('D');
					if($day == 'Sun'){
						 ;
						 /*$sourceDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos".DS;
						 $photoSourceZip = $destination = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos.zip";
						 //frc_zipIt($sourceDir, $destination, true);	//Above instruction is failing.
						 echo "Compressing photos".$output = shell_exec("zip -r $photoSourceZip $sourceDir");
						 $photoSourceDes = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS.'camp_photos.zip';
						 rename (  $photoSourceZip, $photoSourceDes );
						 //After successful compression, we are moving this camp_photos to /uploads/{Ymd}/<data_v<v_no>/camp_photos.zip
						 */
					}else{
						 //copy existing zip file containing camp_photos and database in zipped format from older version to new version when day is changing other than Sunday. On Sunday, we can do it manually or need to create another script for Sunday. For other days this block will work.
						 $frcOption = 'frc_offline_db_params_dev';	//Note: _dev Needs to be replaced in production environment
						 $frcOption = 'frc_offline_db_params';	//Note: _dev Needs to be replaced in production environment
						 $paramOptions = get_option($frcOption);
						 $zipFileName = $paramOptions['zipFileVer'];
						 $zipFolder = $paramOptions['rootFolder'];
						 $absoluteDestZipFilePath = $uploadDirArr['basedir'].DS.$rootFolder.DS.$zipFileVer;
						 $absoluteSrcZipFilePath = $uploadDirArr['basedir'].DS.$zipFolder.DS.$zipFileName;
						 echo "\nabsoluteDestZipFilePath:\n"."\absoluteSrcZipFilePath:\n";
						 if(!file_exists($absoluteDestZipFilePath)){
							  if(file_exists($absoluteSrcZipFilePath)){
								   echo "fsadf";
								   //rename($absoluteSrcZipFilePath, $absoluteDestZipFilePath);
							  }
						 }
					}
					//echo $photoSourceZip;echo "\n<br/>".$photoSourceDes;die;
			   }//Step C-3:  compressing camp_photos and moving it to camp_photos to /uploads/<data_v<v_no>/camp_photos.zip
			   
			   if(true || '117.224.77.68' == $_SERVER["REMOTE_ADDR"]){
					echo "\n-----------------\n";
					print_r($defaulDBParams);
					echo "\n-----------------\n";
			   }
			   
			   {
					$incSourceDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."updates".DS.'camp_photos'.DS;
					$incUpdatesZip = $destination = $uploadDirArr['basedir'].DS."mobile_app_data".DS."updates.zip";
					/*
					if(file_exists($incUpdatesZip)){
						 unlink($incUpdatesZip);
					}
					//shell_exec("zip -r $incUpdatesZip $incSourceDir");
					frc_zipIt($incSourceDir, $incUpdatesZip, true);
					$absDestIncZipFilePath = $uploadDirArr['basedir'].DS.$rootFolder."_inc".DS.$newIncZipFile;
					$absExistIncFile = $uploadDirArr['basedir'].DS.$rootFolder."_inc".DS.$existingIncZipFile;
					rename($incUpdatesZip , $absDestIncZipFilePath);
					if($absExistIncFile){
						 //delete old inc zip file if exising
						 //unlink($absExistIncFile); no need to delete
					}*/
					//Above block needs to be fixed - Commented on Jul 14, 2017 - Rasu
			   }//compressing /wp-content/uploads/mobile_app_data/updates and moving it to "{$rootFolder}_inc"
			   
			   //update_option('frc_offline_db_params_dev', $defaulDBParams, 'no');
			   update_option('frc_offline_db_params', $defaulDBParams, 'no');
			   if(file_exists($uploadDirArr['basedir'].DS.$dbFolderVer.DS.$existingZipFileVer)){
					@unlink($uploadDirArr['basedir'].DS.$dbFolderVer.DS.$existingZipFileVer);
					//Deleting old zipped database file from pure database folder after updating all data by update_options
			   }else{
					//if file is not present in same day folder, then it must be present in prev day folder.
					//file and folder should be deleted in that case.
					; //This is pending
					//After 9th April this is not required as we are not changing folder name now.
			   }
			   
			   if(date('F j, Y') == date('F j, Y', strtotime('last mon of this month')) && $frcDBParams['weekly_processed_large_file'] != 1){
					$oldZipFileSource = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$oldPhotoDataZipFileVer}.zip";
					$newZipFileSource = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$newZipFileVer}.zip";
					if(file_exists($oldZipFileSource)){
						 @rename($oldZipFileSource, $newZipFileSource);
					}else{
						 //else it must be present in previos day folder
						 $oldZipFileSource = $uploadDirArr['basedir'].DS.$yesterday.DS."data_v{$oldPhotoDataZipFileVer}.zip";
						 if(file_exists($oldZipFileSource)){
							  @rename($oldZipFileSource, $newZipFileSource);
						 }else{
							  $dayBeforeYesterday = date('Ymd',strtotime("-2 days"));
							  $oldZipFileSource = $uploadDirArr['basedir'].DS.$dayBeforeYesterday.DS."data_v{$oldPhotoDataZipFileVer}.zip";
							  @rename($oldZipFileSource, $newZipFileSource);
						 }
					}
			   }
			   //shell_exec("rm -rf ");
			   //This should be done once zip file created with new version
		  }else{
			   //Add options to database
			   add_option($frcOption, $defaulDBParams, null, 'no');
			   //returns false if an option with the same name exists
		  }
		  //die("script executed successfully");
		  return array('new_version' => $dataVer, 'download_link' => $downloadURL);
	 }
	 
	 function frc_create_n_compress(){
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data";
		  if(!is_dir($mobDataDir)) wp_mkdir_p($mobDataDir);
		  //Step 1: update offline db
		  //Step 2: create respective folders for offline data/zip files
		  //Step 3: grab `frc_offline_db_params` for existing offline db and store in tempoarary array
		  //Step 4: update option `frc_offline_db_params` w.r.t new updated data
		  //Step 5: Delete data grabbed in tempoarary array
		  
		  $zipFileVer = 'data_v1.zip';	//Param 1
		  $dataVer = '1.0';	//Param 2
		  $rootFolder = date("Ymd");	//Param 3
		  
		  //Start: using statically for production purppose
		  $frcOption = 'frc_offline_db_params'; //for production purpose
		  $defaulDBParams = array(
									 'rootDBFolder' => "20170322_db",
									 'rootFolder' => '20170322',
									 'zipFileVer' => 'data_v7.zip',
									 'dataVer' => '2.22',
									 );
		  update_option($frcOption, $defaulDBParams);
		  //End: using statically for production purppose
		  
		  $frcOption = 'frc_offline_db_params_dev'; //for development purpose
		  //delete_option($frcOption);
		  
		  
		  $defaulDBParams = array(
									 'rootDBFolder' => "{$rootFolder}_db",
									 'rootFolder' => $rootFolder,
									 'zipFileVer' => $zipFileVer,
									 'dataVer' => $dataVer,
								   );
		  print_r($defaulDBParams);
		  if($frcDBParams = get_option($frcOption, $defaulDBParams)){
			   //if existing, then update offline data params
			   $zipFileVer = (int) str_replace(array('data_v', '.zip'),'', $frcDBParams['zipFileVer'])+1;
			   $zipFileVer = "data_v{$zipFileVer}.zip";	//Param 1
			   $dataVer = (float) $frcDBParams['dataVer'] + 0.01;	//Param 2
			   $dbFolderVer = $rootFolder."_db";	//Param 4
			   
			   $defaulDBParams = array(
									 'zipFileVer' => $zipFileVer,	//Param 1
									 'dataVer' => $dataVer,	//Param 2
									 'rootFolder' => $rootFolder,	//Param 3
									 'rootDBFolder' => $dbFolderVer,	//Param 4
									 );
			   
			   //Start: copying /uploads/mobile_app_data/camp_photos.zip to /uploads/<data_v<v_no>/camp_photos.zip
			   //Extracting this zip file in folder /uploads/<data_v<v_no>/
			   //After extraction, deleting zip file from /uploads/<data_v<v_no>/camp_photos.zip
			   
			   {
					$rootFolderPath = $uploadDirArr['basedir'].DS.$rootFolder;
					$rootDBFolderPath = $uploadDirArr['basedir'].DS.$dbFolderVer;
					$dataFolderPath = $uploadDirArr['basedir'].DS.$rootFolder.DS.'data_v'.$dataVer;
					wp_mkdir_p($rootFolderPath);
					wp_mkdir_p($rootDBFolderPath);
					wp_mkdir_p($dataFolderPath);
			   }//Step C-1: creating new required directories Format: YYYYMMDD and Format: YYYYMMDD_db
			   
			   {
					$sourceFile = $uploadDirArr['basedir'].DS."mobile_app_data".DS.'frc.db';
					$destination1 = $uploadDirArr['basedir'].DS.$dbFolderVer.DS.'frc.db';
					copy ($sourceFile, $destination1);
					$destination2 = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS.'frc.db';
					copy ($sourceFile, $destination2);
			   }
			   //Step C-2: Copying frc.db to both directories
			   
			   {
					$day = date('D');
					if($day == 'Sun'){
						 $sourceDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos".DS;
						 $photoSourceZip = $destination = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos.zip";
						 //frc_zipIt($sourceDir, $destination, true);	//Above instruction is failing.
						 echo "Compressing photos".$output = shell_exec("zip -r $photoSourceZip $sourceDir");
						 $photoSourceDes = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS.'camp_photos.zip';
						 copy (  $photoSourceZip, $photoSourceDes );
						 unlink($photoSourceZip);
					}else{
						 //copy existing zip file containing camp_photos and database in zipped format from older version to new version when day is changing other than Sunday. On Sunday, we can do it manually or need to create another script for Sunday. For other days this block will work.
						 $frcOption = 'frc_offline_db_params_dev';	//Note: _dev Needs to be replaced in production environment
						 $paramOptions = get_option($frcOption);
						 $zipFileName = $paramOptions['zipFileVer'];
						 $zipFolder = $paramOptions['rootFolder'];
						 $absoluteDestZipFilePath = $uploadDirArr['basedir'].DS.$rootFolder.DS.$zipFileVer;
						 $absoluteSrcZipFilePath = $uploadDirArr['basedir'].DS.$zipFolder.DS.$zipFileName;
						 //echo "\nabsoluteDestZipFilePath:\n"."\absoluteSrcZipFilePath:\n";
						 if(!file_exists($absoluteDestZipFilePath)){
							  if(file_exists($absoluteSrcZipFilePath)){
								   rename($absoluteSrcZipFilePath, $absoluteDestZipFilePath);
							  }
						 }
					}
					//echo $photoSourceZip;echo "\n<br/>".$photoSourceDes;die;
			   }//Step C-3:  compressing camp_photos and copying it to camp_photos to /uploads/<data_v<v_no>/camp_photos.zip
			   //and deleting from source location as well
			   
			   {
					if($day == 'Sun'){
						 $photoSourceDes = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS.'camp_photos.zip';
						 $zip = new ZipArchive;
						 if ($zip->open($photoSourceDes) === TRUE) {
							 $zip->extractTo($uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS);
							 $zip->close();
							 unlink($photoSourceDes);
						 }else{
							 die('failed fo extract');
						 }
					}
			   }//Step C-3:  uncompressing camp_photos and removing compressed file from location: /uploads/<data_v<v_no>/camp_photos.zip
			   //Note: On Sunday, we can do it manually or need to create another script for Sunday. For other days this block will work.
			   
			   {
					if($day == 'Sun'){
						 $sourceDir = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}".DS;
						 $destination = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}.zip";
						 //frc_zipIt($sourceDir, $destination, true);
						 //$output = shell_exec("zip -r $sourceDir $destination");
						 //Note: Above statement is working but taking long time. so this code needs to be run by independent script.
						 $rootFolderPath = $uploadDirArr['basedir'].DS.$rootFolder.DS."data_v{$dataVer}";
						 if(is_dir($rootFolderPath)){
							  frc_rmdir($rootFolderPath);
							  rmdir($rootFolderPath);
						 }
					}
			   }//compressing /uploads/$rootFolder/$dataVer.zip file + removing unzipped folder after compression
			   
			   {
					if($day == 'Sun'){
						 $campPhotosZip = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos.zip";
						 unlink($campPhotosZip);
					}
			   }//removing compressed file from source location. /uploads/mobile_app_data/camp_photos.zip
			   
			   //End: doing all needful steps for /uploads/mobile_app_data/camp_photos.zip
			   
			   //copying /uploads/mobile_app_data/frc.db 2 /uploads/<data_v<v_no>/frc.db
			   
			   //die(rand(1111,9999));
			   //First copy (sqlite file + camp_photos) to $rootFolder from location: $uploadDirArr['basedir'].DS."mobile_app_data"
			   $sourceFile = $uploadDirArr['basedir'].DS."mobile_app_data".DS.'frc.db';
			   $destination = $uploadDirArr['basedir'].DS.$rootFolder.DS.'frc.db';
			   copy (  $sourceFile, $destination );
			   $sourceFile = $uploadDirArr['basedir'].DS."mobile_app_data".DS.'camp_photos';
			   $destination = $uploadDirArr['basedir'].DS.$rootFolder.DS;
			   //xcopy (  $sourceFile, $destination ); //Note: On Hold
			   $sourceDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos".DS;
			   $destination = $uploadDirArr['basedir'].DS."mobile_app_data".DS."camp_photos.zip";
			   //frc_zipIt($sourceDir, $destination, true); //Note: On Hold
			   echo "\n<br/>".$filename = $uploadDirArr['basedir'].DS.$dbFolderVer.DS.$zipFileVer;
			   echo $addFile = $uploadDirArr['basedir'].DS."mobile_app_data".DS."frc.db";
			   frc_zipIt($addFile, $filename, false);
			   update_option('frc_offline_db_params_dev', $defaulDBParams, 'no');
		  }else{
			   //Add options to database
			   add_option($frcOption, $defaulDBParams, null, 'no');
			   //returns false if an option with the same name exists
		  }
		  //echo "<pre>";print_r($defaulDBParams);die;
		  //echo $uploadDir;die;
		  /*
		  @kalyanmohit For updating strategy, I recommend updating the 1st one (database only) in real-time (as often as you want); The 2nd one shouldn't be that often since you are not doing this by incremental updates, so you still can update this as you wish, but weeks interval is recommended.
		  @kalyanmohit Please assign value=1 for is_partner field that for selected Campsites by FRC’s criteria. As so, we are able to display the orange ribbon for selected Camp sites as well.
		  Re avatar sync issue, it is designed sync up across plarforms, and @kalyanmohit is investigating this.
		  Sure @kalyanmohit . Here are two tasks left for you:
1. As Rob and Glen decided to give selected camp sites a orange ribbon, this need you assign is_partner=1 for selected sites. You guys can define the rule, as long as there is a value=1, we can verify if we display the right thing.
2. It is about the avatar on web side, as the new image uploaded from web, but didn’t sync to app side, vice versa. Please keep investigating this. Thanks.
		   */
	 }
	 
	 function add_data_2_post($dataArr, $campID, $campSiteTbl = 'camp_sites'){
		  global $wpdb;
		  $campSiteTbl = "camp_sites";
		  $partnerCatIds = frc_partner_cat_ids();
		  $isOldTheme = is_old_theme();
		  $innerSeparator = "^"; //Replace : to ^
		  $betSeparator = "##"; //Replace , to ; and I am suggesting ##
		  $postMetaTbl = $wpdb->prefix.'postmeta';
		  $itemMeta = $isOldTheme ? '_ait-dir-item' : '_ait-item_item-data';
		  $catMeta = $isOldTheme ? 'ait-dir-item-category' : 'ait-items';
		  $locMeta = $isOldTheme ? 'ait-dir-item-location' : 'ait-locations';
		  //Note: _ait_dir_gallery, This is not by directory theme. This is by ait extension.
		  $item = new stdClass();
		  $item->optionsDir = get_post_meta($campID, $itemMeta, true);
		  //DB: First Query
		  //echo "<pre>Post Meta:";print_r($item->optionsDir);echo "</pre>";
		  
		  //Start: Get camp photos - getting camp images for slider
		  $photos = array();
		  $query = "SELECT * FROM  `$postMetaTbl` WHERE `post_id` = $campID AND  `meta_key` =  '_ait_dir_gallery'";//25761
		  $galleryResult = $wpdb->get_results($query, OBJECT);
		  //DB: 2nd Query
		  if(count($galleryResult) >= 1){
			   $site_photos = unserialize($galleryResult->meta_value);
			   foreach($galleryResult as $galleryObj){
					$galleryArr = unserialize($galleryObj->meta_value);
					foreach($galleryArr as $photo){
						 if(!empty($photo) && !in_array($photo, $photos)){
							  if(!in_array($photo, $photos)){
								   $photos[] = array($photo, 0);
							  }
							  frcCopyImages($photo, $isMain = 0, $campID, true);
							  //DB + 4 Queries
						 }
					}
			   }
		  }
		  //End: Get camp photos
		  
		  //get_the_post_thumbnail( $item->ID, 'thumbnail' ); - getting the main thumnail image in html and all different sizes are contained in "srcset" attribute.
		  $mainimage = wp_get_attachment_image_src( get_post_thumbnail_id($campID), 'full' );
		  if(count($mainimage)){
			   if(!empty($mainimage[0])){
					if(!in_array($mainimage[0], $photos)){
						 $photos[] = array($mainimage[0],1); //$mainimage[1] = width, $mainimage[2] = height
					}
					frcCopyImages($mainimage[0], $isMain = 1, $campID, true);
					//DB + 4 Queries
			   }
		  }
		  //Start:-----delete photos which are not in $photos array------------------
		  frcDeleteImages($photos, $campID);
		  //End:  -----delete photos which are not in $photos array------------------
		  
		  //print_r($item->ID);
		  $dataArr['address'] = '';
		  $dataArr['gps_lon'] = '';
		  $dataArr['gps_lat'] = '';
		  $dataArr['suburb'] = '';
		  $dataArr['state'] = '';
		  $dataArr['post_code'] = '';
		  $dataArr['telephone'] = '';
		  $dataArr['email'] = '';
		  $dataArr['web'] = '';
		  $dataArr['location'] = '';
		  $dataArr['authority'] = '';
		  $dataArr['location'] = '';
		  $dataArr['discount'] = '';
		  $dataArr['price'] = '';
		  if(is_array($item->optionsDir)){
			   $dataArr['address'] = $item->optionsDir['address'];
			   $dataArr['gps_lon'] = $item->optionsDir['gpsLongitude'];
			   $dataArr['gps_lat'] = $item->optionsDir['gpsLatitude'];
			   $dataArr['suburb'] = $item->optionsDir['wlm_city'];
			   $dataArr['state'] = $item->optionsDir['wlm_state'];
			   $dataArr['post_code'] = $item->optionsDir['wlm_zip'];
			   $dataArr['telephone'] = $item->optionsDir['telephone'];
			   $dataArr['email'] = $item->optionsDir['email'];
			   $dataArr['web'] = $item->optionsDir['web'];
			   $dataArr['location'] = $item->optionsDir['location'];
			   $dataArr['authority'] = $item->optionsDir['responsible_authority'];
			   $dataArr['location'] = $item->optionsDir['location'];
			   $dataArr['discount'] = $item->optionsDir['discount_detail'];
			   $dataArr['price'] = $item->optionsDir['pricing_detail'];
		  }
		  
		 //echo'<pre>';print_r($dataArr); 
		  $features = getFeaturesData($item->optionsDir);
		  
		  $featureArr = $featureDetArr = array();
		  
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
		  $dataArr['feature_n_details'] = $featureDetStr;
		  //Start: Get all Item Categories associated with item
		  $ItemCats = get_the_terms($campID, $catMeta);
		  $itemCatStr = "";$itemCatArr = $itemCatNameArr = array();
		  $is_frc_partner = $is_frc_partner_cat = 0;
		  //Start: New change for allowing is_partner=1 for Campground n Caravan Park
		  $partnerCatIds['Caravan Parks'] = 155;
		  $partnerCatIds['Campgrounds'] = 14;
		  //End: New change for allowing is_partner=1 for Campground n Caravan Park
		  if(is_array($ItemCats)){
			   foreach($ItemCats as $ItemCat){
					$itemCatArr[] = $ItemCat->term_id;
					if(in_array($ItemCat->term_id, $partnerCatIds)){
						 $is_frc_partner_cat = 1;
					}
					$itemCatNameArr[] = $ItemCat->name;
			   }
		  }
		  //print_r($partnerCatIds);print_r($ItemCats);
		  echo "is_frc_partner_cat:".$is_frc_partner_cat;
		  if($is_frc_partner_cat){
			   if( is_array($featureArr) && ( in_array('discount', $featureArr) || in_array('offer', $featureArr) )){
					$is_frc_partner = 1;
			   }
		  }
		  //echo $dataArr['site_name'];die('----');
		  //$dataArr['is_frc_partner'] = $is_frc_partner;
		  $dataArr['is_partner'] = $is_frc_partner;
		  $rootCat = get_root_category($itemCatArr);
          $dataArr['site_category_id'] =  $rootCat;//implode(",", $itemCatArr);
		  $itemCatIds = implode(",", $itemCatArr);
		  $dataArr['site_category'] =  implode(",", $itemCatNameArr);
		  $dataArr['photos'] = $photos;
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
		  if(empty($dataArr['location'])){
			  $dataArr['location'] =  implode(",", $itemCatArr); //Temporary
			  $dataArr['location_id'] =  implode(",", $itemCatIDArr); //Temporary
		  }
		  //End: Get all Item Locations associated with itema
		  
		  $openingHours = array();
		  if($isOldTheme){
			   if(!empty($item->optionsDir['hoursMonday']))
				   $openingHours['Monday'] = $item->optionsDir['hoursMonday'];
			   if(!empty($item->optionsDir['hoursTuesday']))
				   $openingHours['Tuesday'] = $item->optionsDir['hoursTuesday'];
			   if(!empty($item->optionsDir['hoursWednesday']))
				   $openingHours['Wednesday'] = $item->optionsDir['hoursWednesday'];
			   if(!empty($item->optionsDir['hoursThursday']))
				   $openingHours['Thursday'] = $item->optionsDir['hoursThursday'];
			   if(!empty($item->optionsDir['hoursFriday']))
				   $openingHours['Friday'] = $item->optionsDir['hoursFriday'];
			   if(!empty($item->optionsDir['hoursSaturday']))
				   $openingHours['Saturday'] = $item->optionsDir['hoursSaturday'];
			   if(!empty($item->optionsDir['hoursSunday']))
				   $openingHours['Sunday'] = $item->optionsDir['hoursSunday'];
		  }else{
			   if($item->optionsDir['displayOpeningHours'] == 1){//openingHoursMonday => Monday
				   if(!empty($item->optionsDir['openingHoursMonday']))$openingHours['Monday'] = $item->optionsDir['openingHoursMonday'];
				   if(!empty($item->optionsDir['openingHoursTuesday']))$openingHours['Tuesday'] = $item->optionsDir['openingHoursTuesday'];
				   if(!empty($item->optionsDir['openingHoursWednesday']))$openingHours['Wednesday'] = $item->optionsDir['openingHoursWednesday'];
				   if(!empty($item->optionsDir['openingHoursThursday']))$openingHours['Thursday'] = $item->optionsDir['openingHoursThursday'];
				   if(!empty($item->optionsDir['openingHoursFriday']))$openingHours['Friday'] = $item->optionsDir['openingHoursFriday'];
				   if(!empty($item->optionsDir['openingHoursSaturday']))$openingHours['Saturday'] = $item->optionsDir['openingHoursSaturday'];
				   if(!empty($item->optionsDir['openingHoursSunday']))$openingHours['Sunday'] = '';$item->optionsDir['openingHoursSunday'];
			   }
		  }
		  $openingHourStr = "";
		  if(count($openingHours)){
			  $dataArr['opening_hours'] = array($openingHours);
			  $openingHourStr = implode("|",$openingHours);
		  }else{
			  $dataArr['opening_hours'] = array();
		  }
		  if(is_array($featureArr) && count($featureArr) > 0){
			   $featuresStr = implode(",", $featureArr);
		  }
		  $dataArr['opening_hours'] = $openingHourStr;
		  $dataArr['features'] = $featuresStr;
		  unset($dataArr['status']);
		  unset($dataArr['slug']);
		  unset($dataArr['photos']);
		  //unset($dataArr['telephone']);
		  //unset($dataArr['site_category_id']);
		  unset($dataArr['site_category']);
		  unset($dataArr['location_id']);
		  //unset($dataArr['features']);
		  
		  $fieldArr = array(
                                'site_id' => $dataArr['site_id'],
                                'site_name' => $dataArr['site_name'],
                                'site_description' => ($dataArr['site_description']), //do_shortcode
                                'rating' => $dataArr['rating'],
                                'discount' => $dataArr['discount'],
                                'address' => $dataArr['address'], //6
                                'suburb' => $dataArr['suburb'], //7
                                'state' => $dataArr['state'], //8
                                'post_code' => $dataArr['post_code'], //9
                                'gps_lon' => $dataArr['gps_lon'], //10
                                'gps_lat' => $dataArr['gps_lat'], //11
                                'post_id' => $dataArr['site_id'],
                                'email' => $dataArr['email'],
                                'phone' => $dataArr['telephone'],
                                'web' => $dataArr['web'],
                                'opening_hours' => $openingHourStr,
                                'features' => $dataArr['features'],
								'feature_n_details' => $featureDetStr,	//New
                                'location' => $dataArr['location'],
								'category_id' => $dataArr['site_category_id'],			//New
								'category_ids' => $itemCatIds,			//New
								'is_partner' => $is_frc_partner,						//New
                                'authority' => $dataArr['authority'],
                                'price' => $dataArr['price']	//24
                            );
		  
		  //echo '<pre>';print_r($fieldArr);//echo '<pre>';print_r($dataArr);die;
		  update_sqlite_db($fieldArr, 'camp_sites');
		  //DB: +2 Queries
		  update_frc_db($fieldArr, 'camp_sites');
		  //DB: +2 Queries
		  
		  //For new theme this needs to changed
		  return $dataArr;
	 }
	 
	 function update_frc_db($fieldArr, $campSiteTbl = 'camp_sites'){
		  global $wpdb;
		  $post_id = $fieldArr['post_id'];
		  $result = $wpdb->get_results("SELECT * FROM $campSiteTbl WHERE post_id = {$post_id}");
		  //DB: First Query
		  if(!empty($result)){
					$wpdb->update( 
								   $campSiteTbl, 
								   $fieldArr,
								   array( 'post_id' => $post_id )
							  );
					//DB: 2nd Query
			   //echo $updteQry = $wpdb->last_query;
		  }else{
			   $campStatusRes = $wpdb->insert( $campSiteTbl,
                                        $fieldArr,
										array(
											 '%d', '%s', '%s', '%s','%s',
											 '%s', '%s', '%s','%s','%s',
											 '%s', '%s', '%s','%s','%s',
											 '%s', '%s','%s','%s', '%s',
											 '%s', '%s','%s','%s'
										)
							  );
			   //DB: 2nd Query
			   //-----In some case records are not getting added so below is hook to add records
			   if($campStatusRes){
					echo "\n<br/>Last Insert Qry:".$insertQry = $wpdb->last_query;
					echo "\n<br/>";
			   }else{
					$lastError = "";
					$fieldStrArr = array();
					foreach($fieldArr as $fieldKey => $fieldVal){
					   $fieldStrArr[] = "`$fieldKey` = '".mysql_escape_mimic($fieldVal)."'";//mysql_real_escape_string($fieldVal);
					}
					if(!empty($fieldArr['site_name'])){
						 $appendToTable = "";
						 define('CAMP_SITE_TABLE', "camp_sites{$appendToTable}");
						 $insertQry = "INSERT INTO `".CAMP_SITE_TABLE."` SET ".implode(",", $fieldStrArr);
						 $wpdb->query( $insertQry);
					}
			   }
			   //-------------------------------------------------------------------------------
		  }
		  echo "\n<br/>updteQry::<br/>\n";//.$updteQry = $wpdb->last_query;
		  echo "\n<br/><br/>\n";
	 }
	 
	 function update_sqlite_db($fieldArr, $tableName){
		  //echo "<pre>";print_r($fieldArr);echo "</pre>";return;
		  $fieldArr['telephone'] = $fieldArr['phone'];
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."frc.db";
		  $db = new SQLite3($mobDataDir);
		  $postID = $fieldArr['post_id'];
		  $query = "SELECT `site_id` FROM `$tableName` WHERE `site_id` = $postID";
		  $ret = $db->query($query);
		  //DB: First Query
		  $row = $ret->fetchArray(SQLITE3_ASSOC);
		  //print_r($fieldArr);
		  if(is_array($row) && count($row) >= 1){
			   $sqliteQry = "UPDATE `$tableName` SET
								   site_name = :site_name, site_description = :site_description, rating = :rating,
								   discount = :discount, address = :address, suburb = :suburb, state = :state,
								   post_code = :post_code, gps_lon = :gps_lon, gps_lat = :gps_lat, post_id = :post_id,
								   email = :email, phone = :phone, web = :web, opening_hours = :opening_hours,
								   features = :features, feature_n_details = :feature_n_details, location = :location, category_id = :category_id,
								   category_ids = :category_ids, is_partner = :is_partner, authority = :authority, price = :price
								   WHERE site_id = :site_id";
			   $stmt = $db->prepare($sqliteQry);
		  }else{
			   //echo "<pre>";print_r($fieldArr);echo "</pre>";
			   $sqliteQry = "INSERT INTO `$tableName` (
								   site_id, site_name, site_description, rating, discount, address, suburb, state,
								   post_code, gps_lon, gps_lat, post_id, email, phone, web, opening_hours,
								   features, feature_n_details, location, category_id, category_ids, is_partner, authority, price
								   ) values (
								   :site_id, :site_name, :site_description, :rating, :discount, :address, :suburb, :state,
								   :post_code, :gps_lon, :gps_lat, :post_id, :email, :phone, :web, :opening_hours,
								   :features, :feature_n_details, :location, :category_id, :category_ids, :is_partner, :authority, :price
								   )";
			   $stmt = $db->prepare($sqliteQry);
			   ;//Create new record
		  }
		  //SQLite3::escapeString($unsafe);
		  $siteDesc = $fieldArr['site_description'];
		  $stmt->bindParam(':site_id', $fieldArr['site_id'], SQLITE3_INTEGER);
		  $stmt->bindParam(':site_name', $fieldArr['site_name'], SQLITE3_TEXT);
		  $stmt->bindParam(':site_description', $siteDesc, SQLITE3_TEXT);
		  $stmt->bindParam(':rating', $fieldArr['rating']);
		  $stmt->bindParam(':discount', $fieldArr['discount']);
		  $stmt->bindParam(':address', $fieldArr['address'], SQLITE3_TEXT);
		  $stmt->bindParam(':suburb', $fieldArr['suburb']);
		  //$fieldArr['state'] = 'Himachal Pradesh';
		  $stmt->bindParam(':state', $fieldArr['state'], SQLITE3_TEXT);
		  //----------------------------------------------
		  $stmt->bindParam(':post_code', $fieldArr['post_code']);
		  $stmt->bindParam(':gps_lon', $fieldArr['gps_lon']);
		  $stmt->bindParam(':gps_lat', $fieldArr['gps_lat']);
		  $stmt->bindParam(':post_id', $fieldArr['post_id'], SQLITE3_INTEGER);
		  $stmt->bindParam(':email', $fieldArr['email']);
		  $stmt->bindParam(':phone', $fieldArr['phone']);
		  $stmt->bindParam(':web', $fieldArr['web']);
		  $stmt->bindParam(':opening_hours', $fieldArr['opening_hours']);
		  //----------------------------------------------
		  $stmt->bindParam(':features', $fieldArr['features']);
		  $stmt->bindParam(':feature_n_details', $fieldArr['feature_n_details']);
		  $stmt->bindParam(':location', $fieldArr['location']);
		  $stmt->bindParam(':category_id', $fieldArr['category_id']);
		  $stmt->bindParam(':category_ids', $fieldArr['category_ids']);
		  $stmt->bindParam(':is_partner', $fieldArr['is_partner']);
		  $stmt->bindParam(':authority', $fieldArr['authority']);
		  $stmt->bindParam(':price', $fieldArr['price']);
		  print_r($stmt);
		  $ret = $stmt->execute();
		  //echo "sqliteQry0->".$sqliteQry;
		  //echo "sqlite query:".parms($sqliteQry, $fieldArr);
		  //$stmt->debugDumpParams();
		  //DB: 2nd Query
		  if(!$ret){
			   echo "\n#662<br/>\n".$db->lastErrorMsg();
		  }else{
			   ;//echo "\n<br/>".$db->changes()." rows updated";
			   echo "affected rows: ".$stmt->affected_rows;
		  }
		  $stmt->close();
		  $db->close(); //closing the db connection
	 }
	 
	 function parms($string, $data) {
		  //echo "sqliteQry1->".$string;
		  $indexed = $data == array_values($data);
		  foreach($data as $k => $v) {
			   if(is_string($v))
					$v = "'$v'";
			   if($indexed)
				  $string = preg_replace('/\?/',$v,$string,1);
			   else
				  $string = str_replace(":$k",$v,$string);
        }
		echo "sqliteQry2->".$string;
        return $string;
	 }
	 
	 function frc_update_sqlite_photos($photoArr){
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."frc.db";
		  $db = new SQLite3($mobDataDir);
		  $postID = $photoArr['site_id'];
		  $photoPath = $photoArr['path'];
		  $photoName = $photoArr['photo_name'];
		  $query = "SELECT * FROM `site_photos` WHERE `site_id` = $postID AND `path` = '$photoPath' AND `photo_name` = '$photoName'";
		  if(empty($photoName))return;
		  //echo "<br/>\nQry1:".$query;
		  $ret = $db->query($query);
		  //DB: First Query
		  $row = $ret->fetchArray(SQLITE3_ASSOC);
		  //echo "select><br/>\n";print_r($row);
		  //ALTER TABLE `site_photos` ADD CONSTRAINT myUniqueConstraint  UNIQUE(`site_id`,`path`,`photo_name`);
		  //create unique index photo_constraint on site_photos(`site_id`,`path`,`photo_name`);  worked
		  //return;
		  if(is_array($row) && count($row) >= 1){
			   $sqliteQry = "UPDATE `site_photos` SET 
									path = :path, photo_name = :photo_name, is_main = :is_main WHERE
									site_id = :site_id";
			   $stmt = $db->prepare($sqliteQry);
			   echo "$photoName existing, updating it in sqlite db";
		  }else{
			   $sqliteQry = "INSERT INTO `site_photos`
									( site_id, path, photo_name, is_main )
									values
									(:site_id, :path, :photo_name, :is_main)";
			   $stmt = $db->prepare($sqliteQry);
			   echo "$photoName do not exist, adding it to sqlite db";
		  }
		  $stmt->bindParam(':site_id', $postID, SQLITE3_INTEGER);
		  $stmt->bindParam(':path', $photoPath, SQLITE3_TEXT);
		  $stmt->bindParam(':photo_name', $photoName, SQLITE3_TEXT);
		  $stmt->bindParam(':is_main', $photoArr['is_main'], SQLITE3_INTEGER);
		  try {
			   $ret = @$stmt->execute();
		  }catch (Exception $e) {
			   if(!$ret){
					echo $db->lastErrorMsg();
			   }else{
					;//echo $db->changes()." row(s) updated"; //$stmt->affected_rows
			   }
			   //echo 'Caught exception: ',  $e->getMessage(), "\n";
		  }
		  //echo "<br/>\nQry2:". $sqliteQry;
		  //DB: 2nd Query
		  
		  $stmt->close();
		  $db->close(); //closing the db connection
	 }
	 
	 function fr_list_features(){
		  $currentTheme = wp_get_theme();
		  $currentThemeTitle = $currentTheme->get( 'Name' ); //for directory theme its name is 'Directory Plus'
		  $currentTheme->get( 'Version' );
		  $currentTheme->get( 'AuthorURI' );
		  $childTemplate = $currentTheme->get( 'Template' );
		  //when directory child theme installed its result is directory2, The folder name of the parent theme
		  //ThemeURI, Description,
		  $featureOptions = array();
		  if(
			 $childTemplate == 'directory2' ||
			 $childTemplate == 'directory' ||
			 $currentThemeTitle == 'Directory Plus' ||
			 $currentThemeTitle == 'Directory'){
			   $templateDir = get_template_directory();
			   $neonLib = $templateDir.DS.'AIT'.DS.'Framework'.DS.'Libs'.DS.'Nette'.DS.'nette.min.inc';
			   $neonFile = $templateDir.DS.'AIT'.DS.'Framework'.DS.'CustomTypes'.DS.'dir-item'.DS.'dir-item.neon';
			   if( $childTemplate == 'directory2' || $currentThemeTitle == 'Directory'){
					$neonFile = $templateDir . '/ait-theme/@framework/CustomTypes/dir-item/dir-item.neon';
					if(is_old_theme()){
						 $neonLib = $templateDir . '/AIT/Framework/Libs/Nette/nette.min.inc';
						 $neonFile = $templateDir . '/AIT/Framework/CustomTypes/dir-item/dir-item.neon';
					}else{
						 $neonLib = $templateDir . '/ait-theme/@framework/vendor/nette.min.inc'; //Referring main theme
						 $neonFile = $templateDir . '/ait-theme/@framework/CustomTypes/dir-item/dir-item.neon';
					}
			   }
			   
			   $featureArr = array();
			   
			   if(file_exists($neonLib)){
					$neonData = NNeon::decode(file_get_contents($neonFile, true));
					$isFeautre = false;
      
					foreach($neonData as $key => $fieldArr){
						 if(!is_array($key) && trim($key) == 'Features'){
							  $isFeautre = true;
							  continue;
						 }
						 if(!$isFeautre)continue;
						 if(!is_array($fieldArr) && trim($fieldArr) == 'section')break;
						 if(!is_array($fieldArr))continue;	
						 if(is_array($fieldArr) && array_key_exists('label',$fieldArr)){
							  if($pos = strpos($key, "_addnl")){continue;}
							  $featureArr[$key] = $fieldArr['label'];
							  //$featureOptions[$featureArr[$key]] = $fieldArr['label'];
$featureOptions[$key] = $fieldArr['label'];
						 }elseif(trim($fieldArr) == 'section'){
							  $isFeautre = false;
							  break;
						 }	
					}
					return new WP_REST_Response(array('status_code' => 200, 'data' => $featureOptions), 200);
			   }else{
					return new WP_Error( 'file_missing_error', 'dir-item.neon file is missing', array( 'status' => 404 ) );
			   }
		  }else{
			   return new WP_Error( 'directory_theme_error', 'Directory theme missing', array( 'status' => 404 ) );
		  }
	 }
	 
	 function fr_list_locations(){
		  $allowedLocations = array(
									  "Australian Capital Territory" => 'act',
									  "New South Wales" => 'nsw',
									  "Victoria" => 'vic',
									  "South Australia" => 'sa',
									  "Western Australia" => 'wa',
									  "Northern Territory" => 'nt',
									  "Queensland" => 'qld',
									  "Tasmania" => 'tas'
								  );
		  $allowedLocationNames = array_flip($allowedLocations);
		  if(is_old_theme()){
			   $locations = get_terms( array(
					'taxonomy' => 'ait-dir-item-location',
					'hide_empty' => false,
				) );
		  }else{
			   $locations = get_terms( array(
					'taxonomy' => 'ait-locations',
					'hide_empty' => false,
			   ) );
		  }
		  
		  $jsonArr = array();
		  //print_r($locations);
		  foreach($locations as $locationObj){
			   if( in_array($locationObj->name, $allowedLocationNames) || in_array($locationObj->slug, $allowedLocations) ){
				   $jsonArr[$locationObj->term_id] = $locationObj->name;
			   }
		  }
		  //$jsonArr['user'] = get_current_user_id();
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $jsonArr), 200);
	 }
	 
	 function fr_list_all_locations(){
		  if(is_old_theme()){
			   $locations = get_terms( array(
					'taxonomy' => 'ait-dir-item-location',
					'hide_empty' => false,
				) );
		  }else{
			   $locations = get_terms( array(
					'taxonomy' => 'ait-locations',
					'hide_empty' => false,
			   ) );
		  }
		  
		  $jsonArr = array();
		  foreach($locations as $locationObj){$jsonArr[$locationObj->term_id] = $locationObj->name;}
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $jsonArr), 200);
	 }
	
	 function fr_list_categories(){
		  $allowedCategories = array(
									"Free Camps" => "free-camps",
									"Rest Areas" => "rest-areas",
									"Low Cost" => "low-cost",
									"25+ Per Night" => "25-per-night",
									"House Sitting" => "house-sitting",
									"Help Out" => "help-out",
									"Dump Points" => "dump-points",
								  );
		  $allowedCategoryNames = array_flip($allowedCategories);
		  if(is_old_theme()){
			   $categories = get_terms( array(
					'taxonomy' => 'ait-dir-item-category',
					'hide_empty' => false,
				) );
		  }else{
			   $categories = get_terms( array(
					'taxonomy' => 'ait-items',//array('ait-items', 'ait-dir-item-category'),
					'hide_empty' => false,
				) );
		  }
		  
		
		  //--------------------
		  $jsonArr = array();
		  foreach($categories as $catObj){
			   if( in_array($catObj->name, $allowedCategoryNames) || in_array($catObj->slug, $allowedCategories) ){
				   $jsonArr[$catObj->term_id] = $catObj->name;
			   }
		  }
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $jsonArr), 200);
	 }
	 
	 function fr_list_all_categories(){
		  if(is_old_theme()){
			   $categories = get_terms( array(
				   'taxonomy' => 'ait-dir-item-category',
				   'hide_empty' => false,
			   ) );
		  }else{
			    $categories = get_terms( array(
				   'taxonomy' => 'ait-items',
				   'hide_empty' => false,
			   ) );
		  }
		  $catArr = array();
		  foreach($categories as $catObj){
			   $catArr[] = array(
												 'name' => $catObj->name,
												 'slug' => $catObj->slug,
												 'description' => $catObj->description,
												 'cat_id' => $catObj->term_id,
												 //'parent' => $catObj->parent,
												 );
		  }
		  //--------------------A1a1a1a1@74
		  return new WP_REST_Response(array('status_code' => 200, 'data' => $catArr), 200);
	 }
	 
	 function frc_register(){
		  global $json_api;	
		  if (!get_option('users_can_register')) {
			   $json_api->error("User registration is disabled. Please enable it in Settings > Gereral.");            
		  }
		
		  if (!$json_api->query->username) {
			   $json_api->error("You must include 'username' var in your request. ");
		  }else{
			   $username = sanitize_user( $json_api->query->username );
		  }
 
		  if (!$json_api->query->email) {
			   $json_api->error("You must include 'email' var in your request. ");
		  }else{
			   $email = sanitize_email( $json_api->query->email );
		  }

		  if (!$json_api->query->nonce) {
			   $json_api->error("You must include 'nonce' var in your request. Use the 'get_nonce' Core API method. ");
		  }else{
			   $nonce =  sanitize_text_field( $json_api->query->nonce ) ;
		  }
 
		  if (!$json_api->query->display_name) {
			   $json_api->error("You must include 'display_name' var in your request. ");
		  }else{
			   $display_name = sanitize_text_field( $json_api->query->display_name );
		  }

		  $user_pass = sanitize_text_field( $_REQUEST['user_pass'] );

		  if ($json_api->query->seconds){
			   $seconds = (int) $json_api->query->seconds;
		  }else{
			   $seconds = 1209600;//14 days
		  }

		  //Add usernames we don't want used
	
		  $invalid_usernames = array( 'admin' );

		  //Do username validation
		  $nonce_id = $json_api->get_nonce_id('user', 'register');
	  
		  if( !wp_verify_nonce($json_api->query->nonce, $nonce_id) ) {
			   $json_api->error("Invalid access, unverifiable 'nonce' value. Use the 'get_nonce' Core API method. ");
		  }else{
			   if ( !validate_username( $username ) || in_array( $username, $invalid_usernames ) ) {
					$json_api->error("Username is invalid.");
			   }elseif ( username_exists( $username ) ) {
					$json_api->error("Username already exists.");
			   }else{
					if ( !is_email( $email ) ) {
						 $json_api->error("E-mail address is invalid.");
					}elseif (email_exists($email)) {
						 $json_api->error("E-mail address is already in use.");
					}else{
						 //Everything has been validated, proceed with creating the user
						 //Create the user
						 if( !isset($_REQUEST['user_pass']) ) {
							  $user_pass = wp_generate_password();
							  $_REQUEST['user_pass'] = $user_pass;
						 }
						 $_REQUEST['user_login'] = $username;
						 $_REQUEST['user_email'] = $email;

						 $allowed_params = array('user_login', 'user_email', 'user_pass', 'display_name', 'user_nicename', 'user_url', 'nickname', 'first_name',
                         'last_name', 'description', 'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim',
						 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front'
						 );
						 foreach($_REQUEST as $field => $value){
							  if( in_array($field, $allowed_params) ){
								   $user[$field] = trim(sanitize_text_field($value));
							  }
						 }
						 $user['role'] = get_option('default_role');
						 $user_id = wp_insert_user( $user );
			 
						 /*Send e-mail to admin and new user - 
						 You could create your own e-mail instead of using this function*/
			 
						 if( isset($_REQUEST['user_pass']) && $_REQUEST['notify'] == 'no') {
							  $notify = '';	
						 }elseif($_REQUEST['notify']!='no'){
							  $notify = $_REQUEST['notify'];
						 }
						 if($user_id){
							  wp_new_user_notification( $user_id, '',$notify );  
						 }
					}
			   } 
		  }
		  $expiration = time() + apply_filters('auth_cookie_expiration', $seconds, $user_id, true);
		  $cookie = wp_generate_auth_cookie($user_id, $expiration, 'logged_in');
		  return array("cookie" => $cookie,	"user_id" => $user_id);
	 }
	 
	 function check_headers($request){
		  foreach (getallheaders() as $name => $value) {
			   if($name == 'token'){
					echo "Token = ".base64_decode($value);
			   }
		  }
	 }
	 
	 function frc_get_nonce(){
		  $action = 'register';
		  $nonce = wp_create_nonce( $action );
		  return new WP_REST_Response(array('status_code' => 200, 'data' => array('nonce' => $nonce)), 200);
	 }
	 
	 function frcDeleteImages($imageArr, $campID){
		  $uploadDirArr = wp_upload_dir();
		  $mobDataDir = $uploadDirArr['basedir'].DS."mobile_app_data".DS."frc.db";
		  $imgArr = array();
		  
		  foreach($imageArr as $key => $sngImg){
			   $counter = $key + 1;
			   $imgURL = $sngImg[0];
			   $resArr = explode("/", $imgURL);
			   $imageURL = end($resArr);
			   if(!in_array($imageURL,$imgArr)){
					$imgArr[] = end($resArr);
			   }
		  }
		  if(count($imgArr)){
			   $imgStr = "'".implode("', '", $imgArr)."'";
			   $dir = "sqlite:$mobDataDir";
			   $dbh = new PDO($dir) or die("cannot open the database");
			   $selQry = "DELETE FROM `site_photos` WHERE `photo_name`  NOT IN($imgStr) AND `site_id` = $campID";
			   $sth = $dbh->prepare($selQry);
			   $sth->execute();
			   $result = $sth->fetchAll(PDO::FETCH_ASSOC);
		  }
		  if('112.196.97.45' == $_SERVER["REMOTE_ADDR"]){	//echo "\n<br/>";print_r($imageArr);echo "\n<br/>";
			   
		  }
		  
	 }
	 
	 function frcCopyImages($photo, $isMain = 0, $itemID, $mobile_app_data = false){
		  global $wpdb;
		  $wpdb->show_errors = false;
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
		  if($mobile_app_data){
			   $itemAbsoluteDir = $uploadBaseDir.'mobile_app_data'.DS.'camp_photos'.DS.implode("/",$resArr);
			   $itemAbsDirUpdates = $uploadBaseDir.'mobile_app_data'.DS.'updates'.DS.'camp_photos'.DS.implode("/",$resArr);
			   //For incremental updates
		  }else{
			   $itemAbsoluteDir = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr);
			   $itemAbsDirUpdates = $uploadBaseDir.'camp_photos'.DS.'updates'.DS.'camp_photos'.DS.implode("/",$resArr);	//For incremental updates
		  }
		  
		  if( is_dir($itemAbsoluteDir) || is_dir($itemAbsDirUpdates) ){ //Partially For incremental updates
			   echo "\n: if itemAbsDirUpdates:".$itemAbsDirUpdates;
			   if(!is_dir($itemAbsDirUpdates)){
					wp_mkdir_p( $itemAbsDirUpdates);
			   }
			   if($mobile_app_data){
					$destPath = $uploadBaseDir.'mobile_app_data'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					$destUpdatesPath = $uploadBaseDir.'mobile_app_data'.DS.'updates'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					//For incremental updates
			   }else{
					$destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					$destUpdatesPath = $uploadBaseDir.'updates'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					//For incremental updates
			   }
			   $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
			   echo "\n: if $sourcePath, $destUpdatesPath";
			   if(copy($sourcePath, $destUpdatesPath)){
					;//For incremental updates
			   }
			   
			   if(copy($sourcePath, $destPath)){
					$photoPath = DS.implode("/",$resArr);
					$recordExist = $wpdb->get_results( "SELECT *
													  FROM `site_photos`
													  WHERE `site_id` = $itemID AND `path` = '$photoPath' AND `photo_name` = '$imageFile'" );
					//DB: First Query
					$photoArr = array('site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain);
					frc_update_sqlite_photos($photoArr);
					//DB: + 2 DB Queries
					if(is_array($recordExist) && count($recordExist) >= 1){
						 ;//Record exist; do nothing
					}else{
						 $status = $wpdb->insert( 'site_photos',
										 array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
										 array( '%d', '%s', '%s', '%d' )
							 );
						 //DB: 4th Query
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
					}
                //echo "\nquery: ".$wpdb->last_query;
			   }else{
					$photoPath = DS.implode("/",$resArr);
					$photoArr = array(
									  'site_id' => $itemID,
									  'path' => DS.implode("/",$resArr),
									  'photo_name' => $imageFile,
									  'is_main' => $isMain
									  );
					print_r($photoArr);
					echo "else1 Failed to copy";
					die;
			   }
		  }elseif( wp_mkdir_p( $itemAbsoluteDir) && (wp_mkdir_p( $itemAbsDirUpdates) || true) ){ 	//Partially for incremental updates
			   echo "\n: else itemAbsDirUpdates:".$itemAbsDirUpdates;
			   //mkdir ( $itemAbsoluteDir, '0766', true)
			   if($mobile_app_data){
					$destPath = $uploadBaseDir.'mobile_app_data'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					$destUpdatesPath = $uploadBaseDir.'mobile_app_data'.DS.'updates'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					//For incremental updates
			   }else{
					$destPath = $uploadBaseDir.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					$destUpdatesPath = $uploadBaseDir.'updates'.DS.'camp_photos'.DS.implode("/",$resArr).DS.$imageFile;
					//For incremental updates
			   }
			   $sourcePath = $uploadBaseDir.implode("/",$resArr).DS.$imageFile;
			   if(copy($sourcePath, $destUpdatesPath)){
					;//For incremental updates
			   }
			   if(copy($sourcePath, $destPath)){
					$photoPath = DS.implode("/",$resArr);
					$recordExist = $wpdb->get_results( "SELECT *
													  FROM `site_photos`
													  WHERE `site_id` = $itemID AND `path` = '$photoPath' AND `photo_name` = '$imageFile'" );
					//DB: First Query
					$photoArr = array('site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain);
					frc_update_sqlite_photos($photoArr);
					//DB: + 2 DB Queries
					if(is_array($recordExist) && count($recordExist) >= 1){
						 ;//Record exist; do nothing
					}else{
						 $status = @$wpdb->insert( 'site_photos',
											array( 'site_id' => $itemID, 'path' => DS.implode("/",$resArr), 'photo_name' => $imageFile, 'is_main' => $isMain),
											array( '%d', '%s', '%s', '%d' )
								);
						 //DB: 4th Query
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
					}
				   //echo "\nquery: ".$wpdb->last_query;
			   }else{
					$photoPath = DS.implode("/",$resArr);
					$photoArr = array(
									  'site_id' => $itemID,
									  'path' => DS.implode("/",$resArr),
									  'photo_name' => $imageFile,
									  'is_main' => $isMain
									  );
					print_r($photoArr);
					echo "else2 Failed to copy";
					die;
			   }
		  }else{
			   $campDir = $uploadBaseDir.'camp_photos';
			   //This logic needs to be refined.
		  }
		  fclose($handle);
		  fclose($logHandle);
		  return true;
	 }
	 
	 function frc_url_origin( $s, $use_forwarded_host = false ){
		  $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
		  $sp       = strtolower( $s['SERVER_PROTOCOL'] );
		  $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		  $port     = $s['SERVER_PORT'];
		  $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
		  $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		  $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
		  return $protocol . '://' . $host;
	 }
	
	 function frc_full_url( $s, $use_forwarded_host = false ){
		  return frc_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
	 }
	 
	 if(!function_exists('frc_curPageURL')){
    	 function frc_curPageURL() {
    		  $pageURL = 'http';
    		  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    		  $pageURL .= "://";
    		  if ($_SERVER["SERVER_PORT"] != "80") {
    		   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    		  } else {
    		   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    		  }
    		  return $pageURL;
    	 }
	 }
	 
	 function frc_zipIt($source, $destination, $include_dir = false, $additionalIgnoreFiles = array()){
		  //http://stackoverflow.com/questions/3439661/php-ziparchive-not-adding-more-than-700-files
		  //solved this problem by increasing the ulimit: ulimit -n 8192.
		  //http://unix.stackexchange.com/questions/105603/setting-ulimit-correctly-for-php-on-linux
		  //https://www.cyberciti.biz/faq/linux-increase-the-maximum-number-of-open-files/
		  //http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
		  // Ignore "." and ".." folders by default
		  echo "\n<br/>Source:".$source; echo "\n<br/>Destination:".$destination;
		  $defaultIgnoreFiles = array('.', '..');
		  // include more files to ignore
		  $ignoreFiles = array_merge($defaultIgnoreFiles, $additionalIgnoreFiles);
  
		  if (!extension_loaded('zip') || !file_exists($source)) {
			  return false;
		  }
	  
		  if (file_exists($destination)) {
			  unlink ($destination);
		  }

		  $zip = new ZipArchive();
		  if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			  return false;
		  }
		  $source = str_replace('\\', '/', realpath($source));

		  if(is_dir($source) === true){
			   $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			   if ($include_dir) {
					$arr = explode("/",$source);
					$maindir = $arr[count($arr)- 1];
	
					$source = "";
					for ($i=0; $i < count($arr) - 1; $i++) { 
					$source .= '/' . $arr[$i];
				}

				$source = substr($source, 1);
				$zip->addEmptyDir($maindir);
			}

			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);

				// purposely ignore files that are irrelevant
				if( in_array(substr($file, strrpos($file, '/')+1), $ignoreFiles) )
					continue;

				$file = realpath($file);

				if (is_dir($file) === true){
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				}elseif(is_file($file) === true){
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		}elseif(is_file($source) === true){
			$zip->addFromString(basename($source), file_get_contents($source));
		}
		return $zip->close();
	}
	
	 function xcopy($source, $dest, $permissions = 0755){
		  // Check for symlinks
		  if (is_link($source)) {
			  return symlink(readlink($source), $dest);
		  }

		  // Simple copy for a file
		  if (is_file($source)) {
			  return copy($source, $dest);
		  }

		  // Make destination directory
		  if (!is_dir($dest)) {
			  //mkdir($dest, $permissions);
			  wp_mkdir_p($dest);
		  }

		  // Loop through the folder
		  $dir = dir($source);
		  while (false !== $entry = $dir->read()) {
			  // Skip pointers
			  if ($entry == '.' || $entry == '..') {
				  continue;
			  }
	  
			  // Deep copy directories
			  xcopy("$source/$entry", "$dest/$entry", $permissions);
		  }

		  // Clean up
		  $dir->close();
		  return true;
	 }
	 
	 function internoetics_delete_files ($dir, $type = '') {
		  /* Correct for spaces and period in type args */
		  $extensions = preg_replace('/\s+/', ' ', $type);
		  $extensions = str_replace('.', '', $extensions);
		  
		  /* Make sure we have a trailing slash */
		  $dir = (substr($dir, -1) == '/' ? '' : '/');
		  
		  /* Create glob string */
		  $extensions = explode(',', $type);
		  foreach($extensions as $ext) {
			   $glob .= '.' . strtolower($ext) . ',';
			   $glob .= '.' . strtoupper($ext) . ',';
		  }
		  
		  /* GLOB_BRACE string */
		  $glob = '*{' . rtrim($glob, ',') . '}';
		  
		  /* Delete files */
		  foreach ( (glob($dir . $glob, GLOB_BRACE)) as $filename) {
			   $return .= $filename . ' Size: ' . filesize($filename) . ' deleted.<br>';
			   unlink($filename);
		  }
		  return $return;
	 }
	 
	 function frc_rmdir($path) {
		  // Remove a dir (all files and folders in it)
		  $i = new DirectoryIterator($path);
		  foreach($i as $f) {
			   if($f->isFile()) {
					unlink($f->getRealPath());
			   }elseif(!$f->isDot() && $f->isDir()) {
					frc_rmdir($f->getRealPath());
					rmdir($f->getRealPath());
			   }
		  }
	 }
	 
	 function frc_delete_directory($dir) {
		  if (is_dir($dir)) {
			   $objects = scandir($dir);
			   foreach ($objects as $object) {
					if ($object != '.' && $object != '..') {
					 (filetype($dir . '/' . $object) == 'dir') ? frc_delete_directory($dir . '/' . $object) : unlink($dir . '/' . $object);
					}
			   }
			   reset($objects);
			   return rmdir($dir) ? true : false;
		  } 
	 }
	 
	 function frc_test_snippets(){
		  //URL: https://www.freerangecamping.com.au/directory/wp-json/wp/v2/test_snippets
		  //https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
		  //https://codex.wordpress.org/Function_Reference/wp_insert_attachment
		  //https://developer.wordpress.org/reference/functions/wp_insert_attachment/
	 }
	 
	 if(!function_exists('mysql_escape_mimic')){
		  function mysql_escape_mimic($inp) { 
			   if(is_array($inp)) return array_map(__METHOD__, $inp);
			   if(!empty($inp) && is_string($inp)) { 
					return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
			   }
			   return $inp; 
		 }
	 }
	 
	 function frc_item_author_details($post_ID){
		  $user_id = get_post_meta( $post_ID, '_edit_last', TRUE );
		  $userInfoObj = get_userdata( $user_id );
		  $userEmail = $nickname = $fName = $lName = "";
		  $fName = $userInfoObj->first_name;
		  $lName = $userInfoObj->last_name;
		  $userEmail = $userInfoObj->user_email;
		  $nickname = $userInfoObj->nickname;
		  $fullName = implode(" ", array($fName, $lName));
		  return array('id' => $user_id, 'email' => $userEmail, 'name' => $fullName, 'nickname' => $nickname);//$auth,
	 }
?>