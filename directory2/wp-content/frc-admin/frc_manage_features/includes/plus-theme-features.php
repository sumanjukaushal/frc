<?php
	global $wpdb;
	$postMetaTbl = $wpdb->prefix.'postmeta';
	$postTbl = $wpdb->prefix."posts";
	$featureMetaKey = "_ait-item_item-extension";
	$featureHeading = "";
	foreach($features as $key => $featureArr){
		$featureName =  $featureArr[0];
		if(in_array($featureName, $showfeatures)){
			$featureHeading .= "<th>". ucfirst($featureName)."</th>";
		}
	}
	//End: Initialise variables
	if(!function_exists('frc_pc_permute')){
		function frc_pc_permute($items, $perms = array( )) {
			if (empty($items)) { 
				print join(' ', $perms) . "\n";
			}else{
				for ($i = count($items) - 1; $i >= 0; --$i) {
					$newitems = $items;
					$newperms = $perms;
					list($foo) = array_splice($newitems, $i, 1);
					array_unshift($newperms, $foo);
					frc_pc_permute($newitems, $newperms);
				}
			}
		}
	}
	//Start Case: updating multiple rows data by submit
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;	//Page No
	$formQryArr = array();$formQryStr = "";
	if(array_key_exists('search',$_REQUEST) && !empty($_REQUEST['search'])){
		//$ts = trim($_REQUEST['search']);
		//$formQryArr[] = "search=$ts";
	}
	if(!empty($page)){$formQryArr[] = "cpage=$page";}
	if(count($formQryArr))$formQryStr = "?".implode("&", $formQryArr);
	
	if(isset($_POST['submit'])){
		$new_array1 = $dataArr = $new_array = array();	//echo "<pre>"; print_r($_REQUEST);echo "</pre>";
		if(array_key_exists('site_id', $_REQUEST)){
			foreach($_REQUEST['site_id'] as $req_key => $siteID){
				$new_array[$siteID] = ''; //$new_array[27632] = '';
			} //echo "<pre>"; print_r($new_array);echo "</pre>";
			
			foreach($_REQUEST as $request_key => $requestArr){
				if($request_key == 'site_id'){
					continue;
				}
				if(is_array($requestArr)){
					foreach($requestArr as $req1_key => $req1_value){
						if(array_key_exists($req1_key, $new_array)) $new_array1[$req1_key][] = $req1_value;
					}
				}
			}
			
			foreach($new_array1 as $site_ID => $itemFeatures){
				$campfeature = getCampfeatures($site_ID);
				$campfeatures = array();
				foreach($campfeature['onoff'] as $fetKey => $onOff){
					if($onOff == 'on'){
						$campfeatures[$fetKey] = $onOff;	
					}
				}

				foreach($showfeatures as $show_value => $show){
					//features we are showing in form
					if(!in_array($show_value, $itemFeatures)){
						//if features which we are showing in form is not present in the submitted feature collection (Not Ticked) of the Item
						if(array_key_exists($show_value, $campfeature['onoff'])){
							//If above unticked feature is present in the existing meta features, then we are unsetting it.
							//unset($campfeatures[$show_value]);
							$campfeature['onoff'][$show_value] = 'off';
						}
					}else{
						if(array_key_exists($show_value, $campfeature['onoff'])){
							//If above unticked feature is present in the existing meta features, then we are unsetting it.
							$campfeature['onoff'][$show_value] = 'on';
						}
					}
				}
				
				if(!empty($campfeature)){
					$feature = serialize($campfeature);	//echo "<pre>\n$site_ID:";print_r($campfeatures);continue;
					
					$wpdb->update( 
									$postMetaTbl, 
									array( 'meta_value' => $feature ),
									array( 'post_id' => $site_ID, 'meta_key' => $featureMetaKey)
								);
					$dbGMTNow = gmdate("Y-m-d H:i:s", time());
					$currentDBTime = Date("Y-m-d H:i:s");
                    $wpdb->query("UPDATE `$postTbl` SET `post_modified_gmt` = '{$dbGMTNow}', post_modified = '{$currentDBTime}'  WHERE ID = {$site_ID}" );
				}
			}
			echo 'Update Successfully!!';
			header("Location:$actionURL{$formQryStr}");
		}else{
			echo 'Failed to update records!!';
		}
	}
	//End Case: updating multiple rows data by submit
	
	$items_per_page = $perPage = 20;	//No of Items to be shown on page
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;	//Page No
	$offset = ( $page * $items_per_page ) - $items_per_page;	//Page offset
	$customPagHTML = "";
	$args = array(
					'post_type' => $postType,
					'post_status' => 'publish',
					'posts_per_page' => $perPage,
					'offset' => $offset,
					'orderby' => array( 'ID' => 'DESC')
				);
	$search = "";
	$postTbl = $wpdb->prefix."posts";
	if(array_key_exists('search',$_REQUEST) && !empty($_REQUEST['search'])){
		global $wp_filter;
		$wp_filter = array();
		$args['s'] = $_REQUEST['search'];
		$wpQueryRes = new WP_Query($args);	
		/*
		wp_reset_query();
		//https://codex.wordpress.org/Class_Reference/WP_Query#Properties
		$args = array();
		$search = trim($_REQUEST['search']);
		ob_start();
		preg_match('/^(?>\S+\s*){1,5}/', $search, $match);
		$search = $match[0];
		frc_pc_permute(explode(' ',$search));  //split(' ', $search)
		$permutation = ob_get_clean();
		$wordArray = explode("\n", $permutation);
		$searchArray = array();
		foreach($wordArray as $value){
			if(empty($value))continue;
			$searchArray[] = "LOWER(`post_title`) like '%".str_replace(" ","%",$value)."%'";
			$searchArray[] = "LOWER(`post_content`) like '%".str_replace(" ","%",$value)."%'";
			//removing 0 value from array which is for all
			
		}
		$condArr = implode(' OR ', $searchArray);
		$wpDBQry = "SELECT * FROM $postTbl WHERE $condArr ";die;
		*/
		$published_posts = $wpQueryRes->found_posts;
		//print_r($wpQueryRes );
	}elseif(array_key_exists('url',$_REQUEST)){
		;//ajax call
	}else{
		$published_posts = $count_posts->publish; //8524 echo "Published posts:".
	}
	$wpQueryRes = new WP_Query($args);	//echo "Last SQL-Query: {$wpQueryRes->request}";
	
	$search = array_key_exists('search',$_REQUEST) ? trim($_REQUEST['search']) : "";
	$totalPage = ceil($published_posts / $items_per_page);
	if($totalPage > 1){
		$customPagHTML =  '<div>'.paginate_links( array(
		'base' => add_query_arg( 'cpage', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $totalPage,
		'current' => $page
		))."<span>(Page $page of $totalPage ,showing $perPage records Per Page )</span></div>";
	}
	
	
	$formHTML = <<<FORM_HEADING
		<div class="table-responsive">
		<form role="search" method="get" id="searchform" class="searchform" action="$actionURL">
			<table class="table" width='100%'>
				<tr>
					<td>
						<div>
							<label class="screen-reader-text" for="s">Search for:</label>
							<input type='text' value="$search" name="search" id="search" placeholder='Search by title/content' style='width:200px;'/>
							<input type="submit" id="searchsubmit" value="Search" />
						</div></td>
					<td align='right'>$customPagHTML</td>
				</tr>
			</table>
		</form>
		</div>
		
		<div class="table-responsive">
			<form name = 'f1' action='$actionURL{$formQryStr}' method = 'post'>
				<table border = '1' class="table">
					<tr style='font-size:11px;'>
						<th>Post ID</th>
						<th>Camp Site</th>
						$featureHeading
						<th>Action</th>
					</tr>
FORM_HEADING;
//echo'<pre>';print_r($wpQueryRes);die;
	
	foreach($wpQueryRes->posts as $key => $postObj){
		$site_id = $postObj->ID;
		$siteLink = get_permalink($site_id);
		$site_name = $postObj->post_title;
		$campfeature = getCampfeatures($site_id);
		$campfeatures = array();
		foreach($campfeature['onoff'] as $fetKey => $onOff){
			if($onOff == 'on'){
				$campfeatures[$fetKey] = $onOff;	
			}
		}
		$rowCss = ($key%2) ? 'info':'active';
		$formHTML.="\n\n<tr class='$rowCss'><td><a href='$siteLink' target='_blank'>$site_id</a></td><td><a href='$siteLink' target='_blank'>$site_name</a></td>";
		$hiddenCol = "<input type='hidden' name= 'site_id[]'  id = 'hidden_$site_id[$site_id]' value = '$site_id'>";
		foreach($showfeatures as $feature_key => $feature){
			$checked = array_key_exists($feature_key,$campfeatures) ? "checked" : "";
			$formHTML.="<td><input type='checkbox' name= '{$feature}[$site_id]'  id = 'check_{$site_id}[$feature]' value = '$feature_key' $checked /></td>";
		} 
		$formHTML.="<td>{$hiddenCol}<input type='button' name='update' id='update_$site_id' value='update'/></td></tr>"; 
	}
	
	$formHTML.="<tr><td><center><input type='submit' name='submit' id='submit'  value='Submit' /></center></td></tr>";
	$formHTML.="</table></form></div>";
	
	//function getCampfeatures($site_id){
	//	global $metaType;
	//	$postMeta = get_post_meta($site_id, $metaType, true);
	//	return $postMeta;
	//}
	function getCampfeatures($site_id){
		global $wpdb;
		$postMetaTbl = $wpdb->prefix.'postmeta';
		$featureMetaKey = "_ait-item_item-extension";
		$getFeatureQry = "SELECT * FROM $postMetaTbl WHERE `post_id`=$site_id AND `meta_key`='$featureMetaKey'";
		$featuresData = $wpdb->get_results($getFeatureQry);
		$featuresArray = unserialize($featuresData[0]->meta_value);
		return $featuresArray;
	}
	echo "<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Item Features</h3>";
	echo "<p style='padding-left:300px;'>Note: With feature update, listing will be re-published for Mobile Apps for publishing these new changes</p>";
	echo $formHTML;
	echo $customPagHTML; //Pagination HTML
?>

<script>
<?php
	$ajaxUrl = WP_CONTENT_URL.DS."frc-admin/frc_manage_features/includes/plus-update-features-by-ajax.php";
	foreach($wpQueryRes->posts as $key => $postObj){
		$site_id = $postObj->ID;
		$site_name = $postObj->post_title;
?>
		$("#update_<?=$site_id;?>").click(function() {
			var ajaxurl = '<?php echo $ajaxUrl;?>?url=ajax';
			ajaxurl += '&site_id='+<?=$site_id?>;
			<?php foreach($showfeatures as $feature_key => $feature){?>
				//if($("check_<?=$site_id;?>[<?=$feature?>]]:checked") ){
				if (document.getElementById('check_<?=$site_id;?>[<?=$feature?>]').checked) {
					var feature = '<?=$feature_key?>';
					ajaxurl += '&'+feature+'=1';	
				}else{
					var feature = '<?=$feature_key?>';
					ajaxurl += '&'+feature+'=0';	
				}
			<?php  } ?>
			$.blockUI({ message: 'Just a moment...' });
			$.ajax({
				type: 'get',
				url: ajaxurl,
				beforeSend: function(xhr) {
					xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				},
				success: function(response) {
					//alert(response);
					$.unblockUI();
					var objArr = $.parseJSON(response);
					if (response) {
						if (objArr.hasOwnProperty('status')) {
							alert(objArr.status);
						}else if (objArr.hasOwnProperty('error')) {
							alert(objArr.error);
						}
					}
				},
				error: function(e) {
					$.unblockUI();
					alert("Error:"+response);
					alert("An error occurred: " + e.responseText.message);
					console.log(e);
				}
			});
			return false;
		});		
	<?php  } ?>
</script>