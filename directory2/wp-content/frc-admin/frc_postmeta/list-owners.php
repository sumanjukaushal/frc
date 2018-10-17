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
         </head>
        <?php 
            require_once("../../wp-config.php");
            require_once('../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
            $tblName = $wpdb->prefix.'posts';
            $queryTotl = $wpdb->get_results("SELECT `post_author`,COUNT(*) AS post_count FROM `$tblName` GROUP BY `post_author`");
            $totalRes = count($queryTotl);
            $post_per_page = 25;
            $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
            $offset = ( $page * $post_per_page ) - $post_per_page;
            $query = "SELECT `post_author`,COUNT(*) AS post_count FROM `$tblName` GROUP BY `post_author` LIMIT ${offset}, ${post_per_page}";
            $results = $wpdb->get_results($query);//echo'<pre>';print_r($result);die;
            if(!empty($results)){
                echo"<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr bgcolor='#C6D5FD'>
                <th>User Id</th>
                <th>User Login</th>
                <th>Post Counts</th>
                <th>Action</th>
                </tr>";
                //echo'<pre>';print_r($results);die;
                foreach($results as $key => $author){
                    $authorID = $author->post_author;
                    $noOfPosts = $author->post_count;
                    $userData = get_userdata($authorID);
                    //echo'<pre>';print_r($userLogin);die;
                    $userLogin = $userData->data->user_login;
                    $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
                    echo"<tr style='background:{$bgColor}'>
                        <td>{$authorID}</td>
                        <td>{$userLogin}</td>
                        <td>{$noOfPosts}</td>
                        <td><a href='users-post.php?author_id=$authorID&author_login=$userLogin&no_of_post=$noOfPosts'>View Posts</a></td>
                        </tr>
                        ";
                }
                echo"</table>";
                echo '<div class="pagination">';
                        echo paginate_links( array(
                        'base' => add_query_arg( 'cpage', '%#%' ),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => ceil($totalRes / $post_per_page),
                        'current' => $page,
                        'type' => 'plain'
                        ));
                        echo '</div>';
            }
        ?>
    
</html>