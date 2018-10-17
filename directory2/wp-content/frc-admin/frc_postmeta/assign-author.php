<html>
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>    
        <?php 
            require_once("../../wp-config.php");
            require_once('../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
            $tblName = $wpdb->prefix.'posts';
            $tblUsers = $wpdb->prefix.'users';
        ?>
    <body>
        <form action='assign-author.php' method='get'>
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
            
            $postDataQry = "SELECT * FROM `$tblName` WHERE `post_title` LIKE '%$postTitle%' AND `post_status` IN('draft', 'publish')";
            $postDataRes = $wpdb->get_results($postDataQry);
            //echo'<pre>';print_r($postDataRes);die;
            if(!empty($postDataRes)){
                $args = array(
                'role__in' => ['subscriber','author'],
                'orderby' => 'user_nicename',
                'order' => 'ASC'
               );
                $Authors = get_users( [ 'role__in' => [ 'administrator', 'premium' ] ] ); //,'number' => 10
                //echo'<pre>';print_r($Authors);die;
                $authDropdown = "";
                
                $authDropdown.="<select name='author'><option>Select Author</option>";
                $userCount = 0;
                $existingUserLogin = $authorIDs = array();
                foreach($postDataRes as $key => $postData){
                    if(!in_array($authorIDs))$authorIDs[] = $postData->post_author;
                }
                if(!empty($authorIDs)){
                    //get all authors
                    $query = "SELECT `ID`,`user_login` from `$tblUsers` where `ID` IN(".implode(",", $authorIDs).")";
                    $results = $wpdb->get_results($query);
                    foreach($results as $key => $result){
                        $existingUserLogin[$result->ID] = $result->user_login;
                    }
                }
                
                foreach($Authors as $key => $Author){
                    $userLogin = $Author->data->user_login;
                    $userId = $Author->data->ID;
                    $authDropdown.="<option name='$userId' value='$userId'>$userLogin</option>";
                    $userCount++;
                }
                $authDropdown.="</select>";
                
                $srNo = 0;
                $innerHTML = "";
                foreach($postDataRes as $key => $postData){
                    $srNo++;
                    $bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
                    if(array_key_exists($postData->post_author,$existingUserLogin)){
                        $existingAuthor = $existingUserLogin[$postData->post_author];
                    }else{
                        $existingAuthor = "--";
                    }
                    $innerHTML.="
                                <tr style='background-color:$bgColor'>
                                    <td>$srNo</td>
                                    <td>$postData->ID</td>
                                    <td>$postData->post_title</td>
                                    <td>$existingAuthor</td>
                                    <td><input type=checkbox name=item_checked[$postData->ID] class='case' checked='checked'></td>
                                </tr>";
                }
                
                echo $formRow =<<<FORM_HTML
                <form>
                    <table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                        <tr>
                            <td colspan='2'>$authDropdown</td>
                            <td><input type='submit' name='author_submit' value='Assign Author'/> | Total Users: $userCount</td>
                        </tr>
                        <tr>
                            <th style='width:80px;'>Sr. No</th>
                            <th>Item ID</th>
                            <th>Item Title</th>
                            <th>Existing Author</th>
                            <th>Check All<input type=checkbox class='select_all' checked='checked'></th>
                        </tr>
                        $innerHTML
                        <tr><td colspan='2'>$authDropdown</td><td><input type='submit' name='author_submit' value='Assign Author'/></td></tr>
                    </table>
                </form>
FORM_HTML;
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