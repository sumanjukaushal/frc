<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
	$postTbl = $wpdb->prefix."posts";
	$postMetaTbl = $wpdb->prefix."postmeta";
	$postImgMetaTbl = $wpdb->prefix."item_images";
	//SELECT * FROM `gwr_postmeta` WHERE `post_id`=36998
    $postQry = "SELECT `ID` FROM $postTbl WHERE `post_type` = 'ait-item' order by ID ASC";
	//echo "\n<br/>".$postQry = "SELECT `ID` FROM $postTbl WHERE `post_type` = 'ait-item' AND `ID` = 36998 order by ID ASC";
	$postResult = $wpdb->get_results($postQry);
	if(!empty($postResult)){
		foreach($postResult as $key => $postRes){
			$postID = $postRes->ID;
			echo "\n<br/>".$postmetaQry = "SELECT * FROM `$postMetaTbl` WHERE `meta_key` ='_thumbnail_id' AND `post_id` = $postID";
			$postmetaRs = $wpdb->get_results($postmetaQry);
			if(!empty($postmetaRs)){
				$thumbImage = $postmetaRs[0]->meta_value; //39517
				echo "\n<br/>Post with id {$postID} already have thumbnail {$thumbImage}";
				//post_type = attachment, post_name
			}else{
				echo "\n<br/>".$imgMetaQry = "SELECT * FROM `$postImgMetaTbl` WHERE `meta_key` ='_thumbnail_id' AND `post_id` = $postID";
				$imgResult = $wpdb->get_results($imgMetaQry);
				if(!empty($imgResult)){
					$thumbImage = $imgResult[0]->meta_value; //39517
					$dataArr = array('post_id' => $postID, 'meta_key' => '_thumbnail_id','meta_value' => $thumbImage);
					$wpdb->insert('gwr_postmeta',$dataArr);
					//print_r($dataArr);die;
					echo "\n<br/>Missing image added for Post with id {$postID}";
				}else{
					echo "\n<br/>Post with id {$postID} do not have any image";
				}
			}
		}
	}
?>