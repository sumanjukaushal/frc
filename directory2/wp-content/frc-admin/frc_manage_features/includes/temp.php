<?php
echo'hi';die;
?>
<?php
	$featureHeading = "";
	//echo'<pre>';print_r($features);die;
	foreach($features as $key => $featureArr){
		$featureName =  $featureArr[0];
		if(in_array($featureName, $showfeatures)){
			$featureHeading .= "<th>". ucfirst($featureName)."</th>";
		}
	}
	//End: Initialise variables
	
	//Start Case: updating multiple rows data by submit
	$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;	//Page No
	$formQryArr = array();$formQryStr = "";
	if(array_key_exists('search',$_REQUEST) && !empty($_REQUEST['search'])){
		$ts = trim($_REQUEST['search']);
		$formQryArr[] = "search=$ts";
	}
	if(!empty($page)){$formQryArr[] = "cpage=$page";}
	if(count($formQryArr))$formQryStr = "?".implode("&", $formQryArr);
	
	if(isset($_POST['submit'])){
		echo'<pre>';print_r($_REQUEST);die;
		$dataArr = $new_array = array();	//echo "<pre>"; print_r($_REQUEST);echo "</pre>";
		if(array_key_exists('site_id', $_REQUEST)){
			foreach($_REQUEST['site_id'] as $req_key => $siteID){
				$new_array[$siteID] = ''; //$new_array[27632] = '';
			} //echo "<pre>"; print_r($new_array);echo "</pre>";die;
			//echo'<pre>';print_r($_REQUEST);die;
			foreach($_REQUEST as $request_key => $requestArr)
				$requestArray = $requestArr;
				echo'<pre>';print_r($requestArray);die;
				//if($request_key == 'site_id'){
				//	continue;
				//}
				//if(is_array($requestArr)){
				//	foreach($requestArr as $req1_key => $req1_value){
				//		if(array_key_exists($req1_key, $new_array)) $new_array[$req1_key][] = $req1_value;
				//	}
				//}
			}die;
			echo'<pre>';print_r($new_array);die;
			foreach($new_array as $site_ID => $itemFeatures){
				$campfeatures = getCampfeatures($site_ID);
				foreach($showfeatures as $show_value => $show){
					//features we are showing in form
					if(!array_key_exists($show_value, $itemFeatures)){
						//if features which we are showing in form is not present in the submitted feature collection (Not Ticked) of the Item
						if(array_key_exists($show_value, $campfeatures)){
							//If above unticked feature is present in the existing meta features, then we are unsetting it.
							unset($campfeatures[$show_value]);
						}
					}
				}
				
				foreach($itemFeatures as $features_key => $features_value){
					$campfeatures[$features_value] = array('enable' => 'enable');
					//we are here setting ticked features for postmeta
				}
				
				if(!empty($campfeatures)){
					$feature = serialize($campfeatures);	//echo "<pre>\n$site_ID:";print_r($campfeatures);continue;
					$postMetaTbl = $wpdb->prefix."postmeta";
					$wpdb->update( 
									$postMetaTbl, 
									array( 'meta_value' => $feature ),
									array( 'post_id' => $site_ID, 'meta_key' => $metaType)
								);
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
	if(array_key_exists('search',$_REQUEST) && !empty($_REQUEST['search'])){
		//https://codex.wordpress.org/Class_Reference/WP_Query#Properties
		$search = trim($_REQUEST['search']);
		$args['s'] = $search;
		$wpQueryRes = new WP_Query($args);
		$published_posts = $wpQueryRes->found_posts;
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
		<form role="search" method="get" id="searchform" class="searchform" action="$actionURL">
			<table width='100%'>
				<tr>
					<td>
						<div>
							<label class="screen-reader-text" for="s">Search for:</label>
							<input type='text' value="$search" name="search" id="search" />
							<input type="submit" id="searchsubmit" value="Search" />
						</div></td>
					<td align='right'>$customPagHTML</td>
				</tr>
			</table>
		</form>
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="jquery.blockUI.js"></script>
			<form name = 'f1' action='$actionURL{$formQryStr}' method = 'post'>
				<table id = 'example' border = '1'>
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
		$campfeatures = getCampfeatures1($site_id);
		//echo'<pre>';print_r($campfeatures);die;
		$formHTML.="\n\n<tr><td><a href='$siteLink' target='_blank'>$site_id</a></td><td><a href='$siteLink' target='_blank'>$site_name</a></td>";
		$hiddenCol = "<input type='hidden' name= 'site_id[]'  id = 'hidden_$site_id[$site_id]' value = '$site_id'>";
		foreach($showfeatures as $feature_key => $feature){
			$checked = array_key_exists($feature_key,$campfeatures) ? "checked" : "";
			$formHTML.="<td><input type='checkbox' name= '{$feature}[$site_id]'  id = 'check_{$site_id}[$feature]' value = '$feature' $checked /></td>";
		} 
		$formHTML.="<td>{$hiddenCol}<input type='button' name='update' id='update_$site_id' value='update'/></td></tr>"; 
	}
	
	$formHTML.="<tr><td><center><input type='submit' name='submit' id='submit'  value='Submit' /></center></td></tr>";
	$formHTML.="</table></form>";
	
	function getCampfeatures($site_id){
		global $metaType;
		$postMeta = get_post_meta($site_id, $metaType, true);
		return $postMeta;
	}
	function getCampfeatures1($site_id){
		global $wpdb;
		$postMetaTbl = $wpdb->prefix.'postmeta';
		//echo'hi'.$postMetaTbl.'bye';die;
		$featureMetaKey = "_ait-item_item-extension";
		$getFeatureQry = "SELECT * FROM $postMetaTbl WHERE `post_id`=$site_id AND `meta_key`='$featureMetaKey'";
		$featuresData = $wpdb->get_results($getFeatureQry);
		//echo'<pre>';print_r($featuresData);die;
		$featuresArray = unserialize($featuresData[0]->meta_value);
		//echo'<pre>';print_r($featuresArray);die;
		$onFeatures = array();
		foreach($featuresArray['onoff'] as $featureKey => $onOff){
			if($onOff == 'on'){
				$onFeatures[$featureKey] = $onOff;
			}
		}
		return $onFeatures;
	}
	echo $formHTML;
	echo $customPagHTML; //Pagination HTML
?>

<script>
<?php
	foreach($wpQueryRes->posts as $key => $postObj){
		$site_id = $postObj->ID;
		$site_name = $postObj->post_title;
?>
		$("#update_<?=$site_id;?>").click(function() {
			var ajaxurl = '<?php echo $actionURL;?>?url=ajax';
			ajaxurl += '&site_id='+<?=$site_id?>
			<?php foreach($showfeatures as $feature_key => $feature){?>
				//if($("check_<?=$site_id;?>[<?=$feature?>]]:checked") ){
				if (document.getElementById('check_<?=$site_id;?>[<?=$feature?>]').checked) {
					var feature = '<?=$feature?>';
					ajaxurl += '&'+feature+'=1';	
				}else{
					var feature = '<?=$feature?>';
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