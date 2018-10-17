<html>
    <head>
        <title>FRC: Item History</title>        
         <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
         <link rel="stylesheet" href="/resources/demos/style.css">
         <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
         <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
         <?php 
            require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        ?>
    </head>
    <body>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Item History</h3>
    <?php
        $post_per_page = 30;
        $tblName = "frc_post_logs";
        $start_date = $end_date = $search_data = "";
        if(isset($_GET['search'])){
            $start_date = $_GET['start'];
            $end_date = $_GET['end'];
        }
            
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        
        if(isset($_GET['submit'])){
            $search_data = $_GET['search'];
        }
    ?>
        <form action='item_history_logs.php' method='get'>
            <!--<fieldset style='width:78%'>-->
                <!--<legend>Search Items</legend>-->
                <input type='text' name='search' value = '<?php echo $search_data;?>' placeholder='ITEM ID or ITEM TITLE' style='width:200px;margin-left: 20px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            <!--</fieldset>-->
        </form>
<?php
    //URL:https://www.freerangecamping.co.nz/directory/wp-content/frc_offline_db_versions/item_history_logs.php
    
    //Search data display...
    if(isset($_GET['submit'])){
        $search_data = $_GET['search'];
        $postID = (int)$search_data;
        //echo $search_data.$start_date.$end_date; die;
        
        if(!empty($search_data)){
            $cntQry = "SELECT COUNT(*) FROM `$tblName` WHERE `post_title` LIKE '%$search_data%' OR `post_id` = $postID";
        }
        
        if(!empty($search_data)){
            $qry = "SELECT * FROM `$tblName` WHERE `post_title` LIKE '%$search_data%' OR `post_id` = $postID";
        }
    }
    
    if(!empty($qry) && !empty($cntQry)){
        $total = $wpdb->get_var($cntQry);
        $ltQry = "{$qry} ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}";
        $rsltObjs = $wpdb->get_results($ltQry);
    }else{
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $tblName");
        $qry = "SELECT * FROM `$tblName` ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}";
        $rsltObjs = $wpdb->get_results($qry );
    }
    
    if(!empty($rsltObjs)){
        echo "<span style='font-size:200%;margin-left: 20px; color:grey; text-align:center'><u>ITEM History logs</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]";
        $paginationStr = '<div class="pagination"  style="text-align:right;font-size: 150%;margin-left: 20px;">';
        $paginationStr.= paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total / $post_per_page),
            'current' => $page,
            'type' => 'plain'
        ));
        $paginationStr.='</div>';
        echo $tableBlock =<<<TBL_BLK
            <table style="margin-left: 20px;font-family:  Serif, Georgia, 'Times New Roman';font-size: 90%; BORDER='1'" width='100%'>
                <tr><td colspan='7' style='right'>$paginationStr</td></tr>
                <tr bgcolor="#C6D5FD" style='font-size: 120%;'>
                    <td><strong>ITEM ID</strong></td>
                    <td><strong>Title</strong></td>
                    <td style='width:180px;'><strong>Action Time</strong></td>
                    <td><strong>Author</strong></td>
                    <td style='width:150px;'><strong>Status</strong></td>
                    <td><strong>Editor</strong></td>
                    <td><strong>Content Modification<br/> Time (GMT)</strong></td>
                </tr>
TBL_BLK;
        $userInfo = array();
        foreach($rsltObjs as $key => $value){
            $bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
            $date = $value->created;
            $time = date('d-m-Y g:i a', strtotime($date));
            $authorLogs = explode("|", $value->author_log);
            $statusLogs = explode("|", $value->status_log);
            $editorLogs = explode("|", $value->post_edit_log);
            $contentLogs = explode("|", $value->content_log);
            
            $actionTimeStr = $authIDStr = $statusStr = $editorStr = $contentStr = "";
            foreach($authorLogs as $authorLog){
                if(!empty($authorLog)){
                    $autObj = explode("#", $authorLog);
                    $userID = $autObj[0];
                    if(!in_array($userID, $userInfo)){
                        $usrQry = "SELECT `user_login`, `user_email` FROM `gwr_users` WHERE `ID` = $userID";
                        $usrObj = $wpdb->get_results($usrQry );
                        $userLogin = $usrObj[0]->user_login;
                        $userEmail = $usrObj[0]->user_email;
                        $userInfo[$userID] = array($userLogin,$userEmail );
                        $userID = "<a href='#' title='$userEmail' alt='$userEmail'>$userLogin</a>";
                    }else{
                        $userLogin = $userInfo[$userID][0];
                        $userEmail = $userInfo[$userID][1];
                        $userID = "<a href='#' title='$userEmail' alt='$userEmail'>$userLogin</a>";
                    }
                    $actionTime = $autObj[1];
                    $actionTimeStr.="$actionTime<br/>";
                    $authIDStr.="$userID<br/>";
                }
            }
            
            foreach($statusLogs as $statusLog){
                if(!empty($statusLog)){
                    $statusObj = explode("#", $statusLog);
                    $status = $statusObj[0];
                    $temp = $statusObj[1];
                    $statusStr.="$status<br/>";
                }
            }
            
            foreach($editorLogs as $editorLog){
                if(!empty($editorLog)){
                    $editorObj = explode("#", $editorLog);
                    $editorID = $editorObj[0];
                    $temp = $editorObj[1];
                    if(!in_array($editorID, $userInfo)){
                        $usrQry = "SELECT `user_login`, `user_email` FROM `gwr_users` WHERE `ID` = $editorID";
                        $usrObj = $wpdb->get_results($usrQry );
                        $userLogin = $usrObj[0]->user_login;
                        $userEmail = $usrObj[0]->user_email;
                        $userInfo[$editorID] = array($userLogin,$userEmail );
                        $editorID = "<a href='#' title='$userEmail' alt='$userEmail'>$userLogin</a>";
                    }else{
                        $userLogin = $userInfo[$editorID][0];
                        $userEmail = $userInfo[$editorID][1];
                        $editorID = "<a href='#' title='$userEmail' alt='$userEmail'>$userLogin</a>";
                    }
                    $editorStr.="$editorID<br/>";
                }
            }
            
            foreach($contentLogs as $contentLog){
                if(!empty($contentLog)){
                    $contentObj = explode("#", $contentLog);
                    $gmtTime = $contentObj[0];
                    $temp = $contentObj[1];
                    $contentStr.="$gmtTime<br/>";
                }
            }
            
            if($value->post_title == 'Auto Draft'){
                $postQry = "SELECT `post_title` FROM `freerang_directory`.`gwr_posts` WHERE ID={$value->post_id}";
                $postObj = $wpdb->get_results($postQry);
                if(count($postObj) >= 1){
                    $postTitle = $postObj[0]->post_title;
                    $updateQry = $wpdb->update($tblName, array('post_title' => $postTitle), array('id' => $value->id));
                    $value->post_title = $postTitle;
                }
            }
            
            echo $tableRow =<<<TBL_ROW
            <tr style='background-color:$bgColor'>
                <td valign='top'>{$value->post_id}</td>
                <td valign='top'>{$value->post_title}</td>
                <td valign='top'>{$actionTimeStr}</td>
                <td valign='top'>{$authIDStr}</td>
                <td valign='top'>{$statusStr}</td>
                <td valign='top'>{$editorStr}</td>
                <td valign='top'>{$contentStr}</td>
            </tr>
TBL_ROW;
        }
        echo "<tr><td colspan='7' style='right'>$paginationStr</td></tr>";
        echo "</table>";
    }else{
        echo "Data not Found!!";
    }
?>
    </body>
</html>