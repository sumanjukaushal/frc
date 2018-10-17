<html>
    <head>
        <title>Frc: Item Listing By Author</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
         <script>
            $(function() {
                $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
            $(function() {
                $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
         </script>
        <?php 
            require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
            require("items-inner-navigation.php");
            require_once('post-author-navigation.php');
            $isOldTheme = is_old_theme();
            $postType =  $isOldTheme ? 'ait-dir-item' : 'ait-item';
           
            if(isset($_REQUEST)){
                if(array_key_exists("author_id",$_REQUEST) && !empty($_REQUEST['author_id'])){
                    $authorId = $_REQUEST['author_id'];
                    $authorLogin = $_REQUEST['author_login'];
                    $noOfPosts = $_REQUEST['no_of_post'];
                    $tblName = $wpdb->prefix.'posts';
                    $getTotlPostQry = $wpdb->get_var("SELECT COUNT(*) FROM `$tblName` WHERE `post_author`='$authorId' AND `post_type` = '$postType' AND `post_status` <> 'inherit'");
                    $post_per_page = 50;
                    $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
                    $offset = ( $page * $post_per_page ) - $post_per_page;
                    $getPostQry = "SELECT * FROM `$tblName` WHERE `post_author`='$authorId' AND `post_type` = '$postType' AND `post_status` <> 'inherit' ORDER BY `ID` DESC LIMIT ${offset}, ${post_per_page}";
                    $getPostRes = $wpdb->get_results($getPostQry);//echo'<pre>';print_r($getPostRes);die;
                    echo "<h3 style='padding-left:20px;'>Item Listings By Author: {$authorLogin} &raquo;</h3>";
                    if(!empty($getPostRes)){
                        echo"<table style='margin-left: 20px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr>
                            <td style='padding-left: 10px'><b>User ID</b> = {$authorId}</td>
                            <td style='padding-left: 10px'><b>User Login</b> = {$authorLogin}</td>
                            <td style='padding-left: 10px'><b>No. Of Posts</b> = {$noOfPosts}</td>
                            </table><br/>
                            ";
                        echo"<table style='margin-left: 20px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr bgcolor='#C6D5FD'>
                            <th style='padding-top: 7px;padding-bottom: 7px;'>Post ID</th>
                            <th>Post Title</th>
                            <th>Post Status</th>
                            <th>Action</th>
                            </tr>";
                        foreach($getPostRes as $key => $postData){
                            $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
                            $postID = $postData->ID;
                            $postStatus = $postData->post_status;
                            $itemTitle = get_the_title($postID);
                            $link = get_permalink($postID);
                            $permalink = '<a href='.$link.'>View</a>';
                            echo"<tr style='background:{$bgColor}'>
                                <td style='padding-top: 7px;padding-bottom: 7px;'>{$postID}</td>
                                <td>{$itemTitle}</td>
                                <td>{$postStatus}</td>
                                <td>$permalink</td>
                                </tr>";
                        }
                        echo"</table>";
                        echo '<div class="pagination">';
                        echo paginate_links( array(
                        'base' => add_query_arg( 'cpage', '%#%' ),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => ceil($getTotlPostQry / $post_per_page),
                        'current' => $page,
                        'type' => 'plain'
                        ));
                        echo '</div>';
                    }
                }
            }
            ?>
            
    </head>
</html>