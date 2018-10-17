<?php
require_once("../../wp-config.php");
require_once('../../wp-load.php');
global $wpdb;
$tblPosts = $wpdb->prefix.'posts';
if(isset($_REQUEST['title'])){
    if(!empty($_REQUEST['title'])){
        $title = $_REQUEST['title'];
        $itemQuery = "SELECT * FROM `$tblPosts` WHERE `post_title` LIKE '%$title%'";
        $itemResult = $wpdb->get_results($itemQuery);
        //echo'<pre>';print_r($itemResult);die;
        if(count($itemResult)){
            $srNo = 0;
            $block1 = "";
            foreach($itemResult as $key => $itemData){
                $srNo++;
                $postId = $itemData->ID;
                $postTitle = $itemData->post_title;
                $postStatus = $itemData->post_status;
                $block1.= "<tr>
                                <td>$srNo</td>
                                <td>$postId</td>
                                <td>$postTitle</td>
                                <td>$postStatus</td>
                                <td><input type=checkbox name=item_checked[$postId] class='case' checked='checked'></td>
                            </tr>";
            }
            $tableBlock = "<table class='table-striped' width='70%'>
                                    <tr bgcolor='#C6D5FD'>
                                        <th>SR No.</th>
                                        <th>Item ID</th>
                                        <th>Item Title</th>
                                        <th>Item Status</th>
                                        <th>Apply To<input type=checkbox class='selectall' id='selectall' checked='checked'></th>
                                    </tr>
                                    $block1
                                </table>";
            $jsonArray = array('html'=>$tableBlock);
            echo json_encode($jsonArray);
        }
    }else{
        
    }
}
?>