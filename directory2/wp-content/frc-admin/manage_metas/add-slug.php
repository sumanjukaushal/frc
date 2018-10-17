<?php
	require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    require_once('../../wp-settings.php');
    require_once("../../wp-includes/registration.php");
	require_once('../../wp-blog-header.php');
	echo $query = "SELECT `id`,`post_id` FROM `user_items_metas` ORDER BY `id` desc";
	$rs = mysql_query($query);
	//echo mysql_numrows($rs);
	while($rowObj = mysql_fetch_object($rs)){
		$postID = $rowObj->post_id;
                $autoID = $rowObj->id;
		$slug = basename( get_permalink($postID) );
                $pos = strpos("Temporary".$slug, '?');
                if($pos){$slug="";}
		echo "<br/><br/>".$query = "UPDATE `user_items_metas` SET `slug` = '$slug' WHERE `id` = '$autoID'";
                mysql_query($query);
	}
?>