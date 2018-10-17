<html>
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
         
        <?php 
            require_once("../../wp-config.php");
            require_once('../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
            $tblName = $wpdb->prefix.'posts';
            ?>
            <body>
        <form action='assign_author.php' method='get'>
            <fieldset style='width:78%'>
                <legend>Search Items</legend>
                <input type='text' name='search_item_title' value = '<?php if(isset($_REQUEST['search_item_title'])){ echo $_REQUEST['search_item_title']; } ?>' placeholder='ITEM TITLE' style='width:200px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            </fieldset>
        </form>    
            <?php
            if(isset($_REQUEST['submit'])){
                $postTitle = $_REQUEST['search_item_title'];
                
                $postDataQry = "SELECT * FROM `$tblName` WHERE `post_title` LIKE '%$postTitle%'";
                $postDataRes = $wpdb->get_results($postDataQry);
                //echo'<pre>';print_r($postDataRes);die;
                if(!empty($postDataRes)){
                    $args = array(
                    'role__in' => ['subscriber','author'],
                    'orderby' => 'user_nicename',
                    'order' => 'ASC'
                   );
                    $Authors = get_users( [ 'role__in' => [ 'administrator' ],'number' => 10 ] );
                    //echo'<pre>';print_r($Authors);die;
                    echo"<form>";
                    echo"<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                    <tr>
                    <th>Item ID</th>
                    <th>Item Title</th>
                    <th>Check All<input type=checkbox class='select_all' checked='checked'></th>
                    </div>
                    </tr>
                    ";
                    foreach($postDataRes as $key => $postData){
                        $bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
                        echo"<tr style='background-color:$bgColor'><td>$postData->ID</td>";
                        echo"<td>$postData->post_title</td>";
                        echo"<td><input type=checkbox name=item_checked[$postData->ID] class='case' checked='checked'></td></tr>";
                    }
                    
                    echo"<tr><td colspan='2'><select name='author'>
                        <option>Select Author</option>";
                        foreach($Authors as $key => $Author){
                            $userLogin = $Author->data->user_login;
                            $userId = $Author->data->ID;
                            echo"<option name='$userId' value='$userId'>$userLogin</option>";
                        }
                    echo"</select></td>";
                    echo"<td><input type='submit' name='author_submit' value='Assign Author'/></td></tr>";
                    echo"</table>";
                    echo"</form>";
                    
                }else{
                    echo"NO Result Found!!!";
                }
            }elseif(isset($_REQUEST['author_submit'])){
                //echo'<pre>';print_r($_REQUEST);die;
                if(array_key_exists('item_checked',$_REQUEST) && is_array($_REQUEST['item_checked']) && !empty($_REQUEST['item_checked']) && $_REQUEST['author']!= 0){
                    $successIDs = array();
                    $authorID = $_REQUEST['author'];
                    foreach($_REQUEST['item_checked'] as $item_id => $val){
                        $dataArr = array('post_author' => $authorID);
                        $updatePost = $wpdb->update($tblName, $dataArr, array('ID'=> $item_id));
                        if($updatePost){
                            $successIDs[] = $item_id;
                        }else{
                            echo'error';
                        }
                    }
                    if(count($successIDs)){
                        $updatedIDs = implode(",", $successIDs);
                        echo "Author successfully assigned to item ids $updatedIDs<br/>";
                    }
                }else{
                    echo"Select author and items carefully";
                }
            }
        ?>
    </head>
        
    </body>
</html>
<script>
$(function(){
 
    $(".select_all").click(function () {
          $('.case').attr('checked', this.checked);
    });
 
    $(".case").click(function(){
 
        if($(".case").length == $(".case:checked").length) {
            $(".select_all").attr("checked", "checked");
        } else {
            $(".select_all").removeAttr("checked");
        }
 
    });
    
});
</script>