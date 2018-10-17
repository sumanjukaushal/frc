<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
	//post_type=attachment
    //SELECT * FROM `gwr_posts` WHERE `post_type`='attachment'
    $postmetaQry = "SELECT * FROM `gwr_postmeta_images` order by post_id ASC";
	
	$result = $wpdb->get_results($postmetaQry);
    $mapAdddressArr = $finalArray = array();
	echo "<pre>";
    if(!empty($result)){
        foreach($result as $key => $postmeta){
            $postID = $postmeta->post_id;
            $metaKey = $postmeta->meta_key;
            $metaValue = $postmeta->meta_value;
            echo "<br/>".$postQry = "SELECT * FROM `gwr_posts` WHERE `ID` = $postID AND `post_type`='attachment'";
            $postRes = $wpdb->get_results($postQry);
            if(count($postRes) >= 1){
                ;//do nothing
                /*
                $authUpdated = $wpdb->update( 'gwr_postmeta',
									 array('meta_value' => serialize(array('author' => $postAuthor))),
									 array('meta_key' => '_ait-item_item-author', 'post_id' => $postID) );
				*/
                $wpdb->insert('gwr_postmeta',array(
															'post_id' => $postID,
															'meta_key' => $metaKey,
															'meta_value' => $metaValue
															)
								  );
                echo "<br/>Post Meta Qry".$wpdb->last_query;
            }else{
                //add post meta
                
            }
            
        }
    }
    echo'<pre>script executed';
?>