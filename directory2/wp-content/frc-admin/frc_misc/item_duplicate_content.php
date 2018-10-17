<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<meta charset="utf-8"> 
		<title>FRC Find Duplicate Content</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php'); 
    $isOldTheme = is_old_theme();
	$locCat =  $isOldTheme ? 'ait-dir-item-location' : 'ait-locations';
	$postCat =  $isOldTheme ? 'ait-dir-item-category' : 'ait-items';
	$postType =  $isOldTheme ? 'ait-dir-item' : 'ait-item';
	
    $selected_category = $selected_location = $selected_offset = 0;
    if(array_key_exists('offset',$_POST)){$selected_offset = $_POST['offset'];}
	if(array_key_exists('locations',$_POST)){$selected_location = $_POST['locations'];}
	if(array_key_exists('category',$_POST)){$selected_category = $_POST['category'];}
    
?>
    <body>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Find Duplicate Content</h3>
		<h5 style='padding-left:20px;color:blue;'>Script finds duplicate sentences inside AIT Item contents</h5>
        <form action="item_duplicate_content.php" id="search_duplicate" method="post">
        <div style="padding-left: 20px;"><br>
        <select id="offset" name="offset">
            <option value="0" selected="selected">Set offset</option>
            <?php for($i=50;$i<=4000;$i+=50){ ?>
            <option value="<?php echo $i; ?>" <?php if($i == $selected_offset){ echo "selected=selected";}  ?>><?php echo $i ?></option>
            <?php } ?>
        </select>
        <select id="locations" name="locations">
            <option value="0">States</option>
            <option value="1563" <?php if($selected_location == '1563'){echo "selected=selected";} ?>>Australian Capital Territory</option>
            <option value="1564" <?php if($selected_location == '1564'){echo "selected=selected";} ?>>New South Wales</option>
            <option value="1691" <?php if($selected_location == '1691'){echo "selected=selected";} ?>>Northern Territory</option>
            <option value="1712" <?php if($selected_location == '1712'){echo "selected=selected";} ?>>Queensland</option>
            <option value="1793" <?php if($selected_location == '1793'){echo "selected=selected";} ?>>South Australia</option>
            <option value="1355" <?php if($selected_location == '1355'){echo "selected=selected";} ?>>Tasmania</option>
            <option value="1862" <?php if($selected_location == '1862'){echo "selected=selected";} ?>>Victoria</option>
            <option value="1920" <?php if($selected_location == '1920'){echo "selected=selected";} ?>>Western Australia</option>
        </select>
        <select id="category" name="category">
            <option value="0">All Categories</option>
            <option value="9" <?php if($selected_category == '9'){echo "selected=selected";} ?>>Free Camps</option>
            <option value="30" <?php if($selected_category == '30'){echo "selected=selected";} ?>>Rest Areas</option>
            <option value="14" <?php if($selected_category == '14'){echo "selected=selected";} ?>>Campgrounds</option>
            <option value="155" <?php if($selected_category == '155'){echo "selected=selected";} ?>>Caravan Parks</option>
            <option value="12" <?php if($selected_category == '12'){echo "selected=selected";} ?>>House Sitting</option>
            <option value="20" <?php if($selected_category == '20'){echo "selected=selected";} ?>>Help Out</option>
            <option value="6" <?php if($selected_category == '6'){echo "selected=selected";} ?>>Dump Points</option>
            <option value="2096" <?php if($selected_category == '2096'){echo "selected=selected";} ?>>Accommodation</option>
            <option value="5" <?php if($selected_category == '5'){echo "selected=selected";} ?>>Camping Accessories</option>
            <option value="7" <?php if($selected_category == '7'){echo "selected=selected";} ?>>Entertainment</option>
            <option value="8" <?php if($selected_category == '8'){echo "selected=selected";} ?>>Food &amp; Wine</option>
            <option value="210" <?php if($selected_category == '210'){echo "selected=selected";} ?>>Fuel</option>
            <option value="10" <?php if($selected_category == '10'){echo "selected=selected";} ?>>Groceries</option>
            <option value="106" <?php if($selected_category == '106'){echo "selected=selected";} ?>>Markets</option>
            <option value="15" <?php if($selected_category == '15'){echo "selected=selected";} ?>>Medical</option>
            <option value="17" <?php if($selected_category == '17'){echo "selected=selected";} ?>>Personal Health</option>
            <option value="4" <?php if($selected_category == '4'){echo "selected=selected";} ?>>RV Sales &amp; Repairs</option>
            <option value="107" <?php if($selected_category == '107'){echo "selected=selected";} ?>>Services</option>
            <option value="2088" <?php if($selected_category == '2088'){echo "selected=selected";} ?>>Information Centre</option>
            <option value="13" <?php if($selected_category == '13'){echo "selected=selected";} ?>>IGA Stores</option>
            
        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input style="color:#333; background-color:#dcdcdc;"type="submit" name="submit" value="submit" />
        </form>
<?php
    if(isset($_POST['submit'])){
        //print_r($_REQUEST);die;
		//$items = new WP_Query();
        $offset = (int)$_POST['offset'];
		$location_id = (int)$_POST['locations'];
        $category_id = (int)$_POST['category'];
        
        $locObj = get_term_by( 'id', $location_id, $locCat); 
        $locTermID = (is_object($locObj)) ? $locObj->term_taxonomy_id : '';
        
        $catObj = get_term_by( 'id', $category_id, $postCat); 
        $catTermID = (is_object($catObj)) ? $catObj->term_taxonomy_id : '';
        $plainQry = 0;
		
        if(!empty($locTermID) && !empty($catTermID)){	//echo "Case 1:";
			//Case: if both Item location and Item category choosen - Working
            $dupQry = "SELECT  gwr_posts.ID, gwr_posts.post_title, gwr_posts.post_content FROM gwr_posts LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id) LEFT JOIN gwr_term_relationships AS tt1 ON (gwr_posts.ID = tt1.object_id) WHERE (`gwr_term_relationships`.`term_taxonomy_id` IN ($locTermID) AND tt1.term_taxonomy_id IN ($catTermID) ) AND `gwr_posts`.`post_type` = '$postType' AND (`gwr_posts`.`post_status` = 'publish' OR `gwr_posts`.`post_status` = 'draft') GROUP BY `gwr_posts`.`ID` ORDER BY `gwr_posts`.`post_date` DESC";
            $plainQry = 1;
        }elseif(!empty($locTermID) || !empty($catTermID)){	
			//Case: if either Item location or Item category choosen - Working
            $plainQry = 1;
            $id = !empty($locTermID) ? $locTermID : $catTermID;
            $dupQry = "SELECT  gwr_posts.ID, gwr_posts.post_title, gwr_posts.post_content FROM gwr_posts  LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id) WHERE ( gwr_term_relationships.term_taxonomy_id IN ($id) ) AND gwr_posts.post_type = '$postType' AND (gwr_posts.post_status = 'publish' OR gwr_posts.post_status = 'draft') GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_date DESC";
        }elseif( !empty($location_id) && !empty($category_id)){	//echo "Case 3:";
            //Case: this will work only for admin side-if both Item location and Item category choosen
            $args = array(
							'post_type' => $postType,
							'perm' => 'all',   
							'tax_query' => array(
								'relation' => 'AND',
								array(
									'taxonomy' => $locCat,
									'field'    => 'term_id',
									'terms'    => array( $location_id ),
									'include_children' => false
								),
								array(
									'taxonomy' => $postCat,
									'field'    => 'term_id',
									'terms'    => array( $category_id ),
									'include_children' => false
								),
							),
						);
        }elseif( (empty($location_id) && !empty($category_id)) || (!empty($location_id) && empty($category_id))){	//echo "Case 4:";
            //Case: this will work only for admin side-if either Item location or Item category choosen
            $taxField = empty($category_id) ? $locCat: $postCat;
            $id = empty($category_id) ? $location_id: $category_id;
            $args = array(
					'post_type' => $postType,
					'perm' => 'all',   
					'tax_query' => array(
            							array(
            							    'taxonomy' => $taxField,
                							'field'    => 'term_id',
                							'terms'    => array( $id ),
                							'include_children' => false
                							)
					                    ),
				                    );
        }
		$duplicate = $items = array();
        if($plainQry){	//echo "Case 5:";
			//Case: if other than admin user - Working
            $totRec = $wpdb->query($dupQry);	//echo "<pre>";print_r($totRec);echo "</pre>";
            $dupQry = $dupQry. " LIMIT $offset, 50";
            $items = $wpdb->get_results( $dupQry, OBJECT );	//echo "<pre>";print_r($items);echo "</pre>";
			//echo $wpdb->last_query;
        }else{	echo "Case 6:";
            //Note: will be taken care
			$countQry = $dupQry = "SELECT gwr_posts.ID, gwr_posts.post_content, gwr_posts.post_title FROM gwr_posts  LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id)  LEFT JOIN gwr_term_relationships AS tt1 ON (gwr_posts.ID = tt1.object_id) WHERE ( gwr_term_relationships.term_taxonomy_id IN (30) AND tt1.term_taxonomy_id IN (1594)) AND gwr_posts.post_type = '$postType' AND ((gwr_posts.post_status = 'publish')) GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_date DESC";
			$totRec = $wpdb->query($countQry);
			//echo $totRec;die;
		    $dupQry = "SELECT gwr_posts.ID, gwr_posts.post_content, gwr_posts.post_title FROM gwr_posts  LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id)  LEFT JOIN gwr_term_relationships AS tt1 ON (gwr_posts.ID = tt1.object_id) WHERE ( gwr_term_relationships.term_taxonomy_id IN (30) AND tt1.term_taxonomy_id IN (1594)) AND gwr_posts.post_type = '$postType' AND ((gwr_posts.post_status = 'publish')) GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_date DESC LIMIT $offset, 50"; 
		    //print_r( $query->request);die("ProAvid");
			$totRecs = $wpdb->query($dupQry);
			$items = $wpdb->get_results( $dupQry, OBJECT );
			
        }
		//print_r($items);die;
		$tableHTML = "";
		if(!empty($items)){
			$tableHTML.="<div class='table-responsive'><table class='table' border='1'><tr style='color:#003d4c;'><th>IDs</th><th>Post Title</th><th>Duplicate Sentences</th></tr>";
		}
		$ids = array();
		$total = count($items);
		$count = 0;
        foreach($items as $dup => $duplicatArr){
			//echo'<pre>';print_r($duplicatArr);die;
			$ids = $duplicatArr->ID;
			//print_r($ids);die;
			$siteLink = get_permalink($duplicatArr->ID);
			$post_title = $duplicatArr->post_title;
			$post_content = $duplicatArr->post_content;
			//echo $post_content;die;
			$duplicate_sen = explode(".",$post_content);
			/*echo'<pre>';
			echo "\n=============\n";
			print_r($duplicate_sen);
			echo "\n=============\n";
			print_r(array_unique( $duplicate_sen ));
			echo "\n=============\n";
			print_r(array_diff_assoc( $duplicate_sen, array_unique( $duplicate_sen ) ));
			echo "\n=============\n";
			print_r(array_unique( array_diff_assoc( $duplicate_sen, array_unique( $duplicate_sen ) ) ));
			echo "</pre>";
			die;*/
			$duplicate_senArr = array_unique( array_diff_assoc( $duplicate_sen, array_unique( $duplicate_sen ) ) );
			//echo'<pre>';print_r($duplicate_senArr);die;
			$duplicate = implode("<br/>",$duplicate_senArr);
			$count += count($duplicate);
			$rowCss = $count%2 ? 'info' : 'active';
			if($duplicate){
				$tableHTML.="<tr class='$rowCss'><td><a href='$siteLink' target='_blank'>$ids</a></td><td><a href='$siteLink' target='_blank'>$post_title</td><td>$duplicate</td></tr>";
			}else{
				$tableHTML.="<tr class='$rowCss'>
							<td><a href='$siteLink' target='_blank'>$ids</a></td>
							<td><a href='$siteLink' target='_blank'>$post_title</td>
							<td><span style='background-color:yellow'>Might be only blank spaces or new line charachters</span></td></tr>";
			}
		}
		$tableHTML.="</table></div>";
		echo $tableHTML;
		?>
		<h3 style="color:#e32;">Total No. Of Records:<font style="background-color:yellow;color:003d4c;"><?php echo $totRec ?></font></h3><font style="background-color:yellow;color:#e32">Showing <?php echo $count ?> Out of <?php echo $totRec ?></font>

		
		<?php
		if(!$duplicate){
			echo'No Duplicate Sentences';
		}
        echo'</div>';
    }
?>
</body>
</html>