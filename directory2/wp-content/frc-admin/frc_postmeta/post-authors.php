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
        $tblName = $wpdb->prefix.'posts';
        $userTbl = $wpdb->prefix.'users';
    ?>
<html>
    <head>
        <title>Frc: Item Authors</title>
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
         </head>
        <body>
            <h3 style='padding-left:20px;'>ITEM Authors &raquo;</h3><br/>
            <form action='post-authors.php' method='get'>
                    <input type='text' name='search_item_name' value = '<?php if(isset($_REQUEST['search_item_name'])){ echo $_REQUEST['search_item_name']; } ?>' placeholder='User Name and login' style='width:200px;margin-left: 25px;'/>
                    <span style='padding-left:20px;'>
                    <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                    </span>
            </form> 
        </body>
        <?php 
            
            if(isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])){
                $searchedContent = $_REQUEST['search_item_name'];
                $userQry = "SELECT * FROM `$userTbl` WHERE `user_login` LIKE '%{$searchedContent}%' OR `display_name` LIKE '%{$searchedContent}%' OR `user_email` LIKE '%{$searchedContent}%'";
                $userRes = $wpdb->get_results($userQry);//echo'<pre>';print_r($userRes);die;
                if(count($userRes)){
                    $tableBlock ="";
                    $counts = 0;
                    foreach($userRes as $keys => $userData){
                        $bgColor = $counts % 2 == 0 ? '#CCC' : '#FFF';
                        $authorId = $userData->ID;
                        $userDisplayName = $userData->display_name;
                        $userEmail = $userData->user_email;
                        $userLogin = $userData->user_login;
                        $authorPostQry = "SELECT COUNT(*) AS post_count FROM `$tblName` WHERE `post_author` = $authorId AND `post_type` = '$postType' AND `post_status` <> 'inherit' GROUP BY `post_author` ORDER BY `post_count` DESC ";//"SELECT * FROM `$tblName` WHERE `post_author`=$authorId";
                        $authorPostRes = $wpdb->get_results($authorPostQry);//echo'<pre>';print_r($authorPostRes);
                        $noOfPosts = $authorPostRes[0]->post_count;//count_user_posts($authorId,$postType);
                        if(count($authorPostRes)){
                            $counts++;
                            $tableBlock2.= "<tr style='background:{$bgColor}'>
                            <td style='padding-top: 7px;padding-bottom: 7px;'>{$authorId}</td>
                            <td>{$userDisplayName}</td>
                            <td>{$userLogin}</td>
                            <td>{$userEmail}</td>
                            <td style='padding-left: 22px;'>{$noOfPosts}</td>
                            <td><a href='{$contentURL}/frc_postmeta/post-author.php?author_id=$authorId&author_login=$userLogin&no_of_post=$noOfPosts'>View Posts</a></td>
                            </tr>
                            ";
                        }
                    }
                    echo $tableBlock = "<table style='width: 1128px; margin-left: 20px;margin-bottom: 50px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr bgcolor='#C6D5FD'>
                    <th style='padding-top: 7px;padding-bottom: 7px;'>User Id</th>
                    <th style='padding-left: 45px;'>Name</th>
                    <th>User Login</th>
                    <th>Email</th>
                    <th>Post Counts</th>
                    <th>Action</th>
                    </tr>
                    $tableBlock2
                    </table>";
                }else{
                    $msg = "Data Not Found!!!";
                    echo "<div style='padding-left:20px;'>$msg</div>";
                }
                
            }else{
                $qry = "SELECT `post_author`,COUNT(*) AS post_count FROM `$tblName` WHERE `post_type` = '$postType' AND `post_status` <> 'inherit' GROUP BY `post_author` ORDER BY `post_count` DESC ";
                $queryTotl = $wpdb->get_results($qry);
                $totalRes = count($queryTotl);
                $post_per_page = 25;
                $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
                $offset = ( $page * $post_per_page ) - $post_per_page;
                $query = "SELECT `post_author`,COUNT(*) AS post_count FROM `$tblName` WHERE `post_type` = '$postType' AND `post_status` <> 'inherit' GROUP BY `post_author` ORDER BY `post_count` DESC LIMIT ${offset}, ${post_per_page}";
                $results = $wpdb->get_results($query);//echo'<pre>';print_r($results);die("--$query--");
                //echo "<h3 style='padding-left:20px;'>ITEM Authors &raquo;</h3>";
                if(!empty($results)){
                    echo"<table style='width: 1128px; margin-left: 20px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr bgcolor='#C6D5FD'>
                    <th>User Id</th>
                    <th style='padding-left: 45px;'>Name</th>
                    <th>User Login</th>
                    <th>Email</th>
                    <th>Post Counts</th>
                    <th>Action</th>
                    </tr>";
                    foreach($results as $key => $author){
                        $authorID = $author->post_author;
                        $noOfPosts = $author->post_count;
                        $userData = get_userdata($authorID);
                        $userLogin = $userData->data->user_login;
                        $userDisplayName = $userData->data->display_name;
                        $userEmail = $userData->data->user_email;
                        $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
                        echo"<tr style='background:{$bgColor}'>
                            <td>{$authorID}</td>
                            <td>{$userDisplayName}</td>
                            <td>{$userLogin}</td>
                            <td>{$userEmail}</td>
                            <td>{$noOfPosts}</td>
                            <td><a href='{$contentURL}/frc_postmeta/post-author.php?author_id=$authorID&author_login=$userLogin&no_of_post=$noOfPosts'>View Posts</a></td>
                            </tr>
                            ";
                    }
                    echo"</table>";
                    echo '<div class="pagination"  style="padding-left:20px;font-size: 20px;">';
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
            }
        ?>
    
</html>