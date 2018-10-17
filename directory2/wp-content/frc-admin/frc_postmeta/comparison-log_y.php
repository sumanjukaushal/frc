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
            //$tblName = $wpdb->prefix.'postmeta_log';
            $latestPostQry = "SELECT * FROM `frc_postmeta_log` ORDER BY `modified` DESC LIMIT 1";
            $latestPostRes = $wpdb->get_results($latestPostQry);
            if(!empty($latestPostRes)){
                $latestPostID = $latestPostRes[0]->post_id;
                $latestModifiedTime = date( 'M j, y h:i A', strtotime($latestPostRes[0]->modified) );
                $latestPostMetas = unserialize($latestPostRes[0]->meta_value);//echo'<pre>';print_r($latestPostMetas);die;
                $previousPostQry = "SELECT * FROM `frc_postmeta_log` WHERE `post_id`='$latestPostID' ORDER BY `modified` DESC LIMIT 1,1";
                $previousPostRes = $wpdb->get_results($previousPostQry);
                if(!empty($previousPostRes)){
                    $previousPostId = $previousPostRes[0]->post_id;
                    $previousPostMetas = unserialize($previousPostRes[0]->meta_value);//echo'<pre>';print_r($previousPostMetas);die;
                    foreach($previousPostMetas as $fieldName => $previousPostMeta){
                        if(!is_array($previousPostMeta)){
                            if(!array_key_exists($fieldName,$latestPostMetas)){
                                echo "$fieldName field is deleted from Post ID $latestPostID On $latestModifiedTime<br/>";
                            }else{
                                if($latestPostMetas[$fieldName] == '' && $previousPostMeta != ''){
                                    echo"Data of $fieldName field is removed from Post ID $latestPostID On $latestModifiedTime<br/>";
                                }
                            }
                        }else{
                            if($fieldName == 'map'){
                                if(array_key_exists($fieldName,$latestPostMetas)){
                                    foreach($previousPostMeta as $key => $prvsPostMeta){
                                        if(!array_key_exists($key,$latestPostMetas[$fieldName])){
                                            echo"$key Field of $fieldName array is deleted from Post ID $latestPostID On $latestModifiedTime<br/>";
                                        }else{
                                            if($latestPostMetas[$fieldName][$key] == '' && $prvsPostMeta != ''){
                                                echo "Data Of $key field of $fieldName array is removed from Post ID $latestPostID On $latestModifiedTime<br/>";
                                            }
                                        }
                                    }
                                }else{
                                    echo"$fieldName Field Is Deleted From Post ID$latestPostID On $latestModifiedTime<br/>";
                                }
                            }elseif($fieldName == 'gallery'){
                                if(!is_array($latestPostMetas[$fieldName])){
                                    echo"All Images Of Post ID $latestPostID Is Deleted On $latestModifiedTime<br/>";
                                }else{
                                    if(sizeof($latestPostMetas[$fieldName]) < sizeof($previousPostMetas[$fieldName])){
                                        echo"Some Images of Post ID $latestPostID Is Deleted On $latestModifiedTime<br/>";
                                    }
                                }
                            }
                        }
                    }
                }else{
                    echo"LOg Table has Only One Record Regarding to Post ID $latestPostID<br/>";
                }
            }else{
                echo"LOg Table Is Empty";
            }
        ?>
    </head>
    
</html>