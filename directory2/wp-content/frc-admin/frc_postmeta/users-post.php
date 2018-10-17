<html>
    <head>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
         <script>
            $(function() {
                $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
            $(function() {
                $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
         </script>
        <?php 
            require_once("../../wp-config.php");
            require_once('../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
            if(isset($_REQUEST)){
                if(array_key_exists("author_id",$_REQUEST) && !empty($_REQUEST['author_id'])){
                    $authorId = $_REQUEST['author_id'];
                    $authorLogin = $_REQUEST['author_login'];
                    $noOfPosts = $_REQUEST['no_of_post'];
                    $tblName = $wpdb->prefix.'posts';
                    $getTotlPostQry = $wpdb->get_var("SELECT COUNT(*) FROM `$tblName` WHERE `post_author`='$authorId'");
                    $post_per_page = 50;
                    $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
                    $offset = ( $page * $post_per_page ) - $post_per_page;
                    $getPostQry = "SELECT * FROM `$tblName` WHERE `post_author`='$authorId' LIMIT ${offset}, ${post_per_page}";
                    $getPostRes = $wpdb->get_results($getPostQry);//echo'<pre>';print_r($getPostRes);die;
                    if(!empty($getPostRes)){
                        echo"<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr>
                            <th>User ID = {$authorId}</th><th></th><th></th><th></th>
                            <th>User Login = {$authorLogin}</th><th></th><th></th><th></th>
                            <th>No. Of Posts = {$noOfPosts}</th>
                            </table>
                            ";
                        echo"<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr bgcolor='#C6D5FD'>
                            <th>Post ID</th>
                            <th>Post Title</th>
                            <th>Post Status</th>
                            <th>Action</th>
                            </tr>";
                        foreach($getPostRes as $key => $postData){
                            $postID = $postData->ID;
                            $postStatus = $postData->post_status;
                            $itemTitle = get_the_title($postID);
                            $link = get_permalink($postID);
                            $permalink = '<a href='.$link.'>View</a>';
                            echo"<tr>
                                <td>{$postID}</td>
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