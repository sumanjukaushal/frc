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
            $latestPostQry = "SELECT * FROM `frc_postmeta_log_1` WHERE `meta_key`IN('_ait_dir_gallery','_ait-dir-item','_ait-item_item-data') AND `modified` > date_sub(NOW(), INTERVAL 1 DAY) ORDER BY `modified` DESC";
            $latestPostRes = $wpdb->get_results($latestPostQry);//echo'<pre>';print_r($latestPostRes);die;
            if(!empty($latestPostRes)){
                $postMetaArray = array();
                $msg = $mailBody = "";
                foreach($latestPostRes as $key => $latestPostData){
                    $message = $removedFields = $deletedFields = "";
                    $postId = $latestPostData->post_id;
                    $metaKey = $latestPostData->meta_key;
                    $latestModifiedTime = date( 'M j, y h:i A', strtotime($latestPostData->modified) );
                    if(array_key_exists($postId,$postMetaArray) && in_array($metaKey,$postMetaArray)){
                        continue;
                    }
                    $lastScndQry = "SELECT * FROM `frc_postmeta_log_1` WHERE `post_id`='$postId' AND `meta_key`='$metaKey' ORDER BY `modified` DESC LIMIT 1,1";
                    $lastScndRes = $wpdb->get_results($lastScndQry);//echo'<pre>';print_r($lastScndRes);die;
                    if(!empty($lastScndRes)){
                        $postMetaArray[$postId] = $metaKey;
                        $LatestPostmetas = unserialize($latestPostData->meta_value);
                        
                        if($metaKey == '_ait_dir_gallery'){
                            $previousPostMetas = unserialize($lastScndRes[0]->meta_value);
                            $noofImage = '0';
                            foreach($previousPostMetas as $key => $image){
                                if(!in_array($image,$LatestPostmetas)){
                                    $noofImage++;
                                }
                            }
                            if($noofImage != 0){
                                $removedFields.= "$noofImage Image is deleted <br/>";
                            }
                        }elseif($metaKey == '_ait-dir-item'){
                            $previousPostMetas = unserialize($lastScndRes[0]->meta_value);
                            
                            foreach($previousPostMetas as $previousPostMetaKey => $previousPostMetaValue){
                                if(strpos($previousPostMetaKey,'_addnl') !== false){
                                    continue;
                                }
                                if(!is_array($previousPostMetaValue)){
                                    if(!array_key_exists($previousPostMetaKey,$LatestPostmetas)){
                                        $deletedFields.= "$previousPostMetaKey <br/>";
                                    }else{
                                        if($LatestPostmetas[$previousPostMetaKey] == '' && $previousPostMetas[$previousPostMetaKey] != ''){
                                            $removedFields.= "$previousPostMetaKey <br/>";
                                        }
                                    }
                                }else{
                                    //is array
                                }
                            }
                        }elseif($metaKey == '_ait-item_item-data'){
                            $previousPostMetas = unserialize($lastScndRes[0]->meta_value);
                            $changedFields = $removedFields = $deletedFields = array();
                            foreach($previousPostMetas as $fieldName => $previousPostMeta){
                                if(!is_array($previousPostMeta)){
                                    if(!array_key_exists($fieldName,$latestPostMetas)){
                                        $deletedFields.= "$fieldName <br/>";
                                    }else{
                                        if($latestPostMetas[$fieldName] == '' && $previousPostMeta != ''){
                                            $removedFields.= "$fieldName <br/>";
                                        }
                                    }
                                }else{
                                    if($fieldName == 'map'){
                                        if(array_key_exists($fieldName,$latestPostMetas)){
                                            foreach($previousPostMeta as $key => $prvsPostMeta){
                                                if(!array_key_exists($key,$latestPostMetas[$fieldName])){
                                                    $deletedFields.= "$key field of $fieldName <br/>";
                                                }else{
                                                    if($latestPostMetas[$fieldName][$key] == '' && $prvsPostMeta != ''){
                                                        $removedFields.= "$key field of $fieldName <br/>";
                                                    }
                                                }
                                            }
                                        }else{
                                            $deletedFields[] = $fieldName;
                                        }
                                    }elseif($fieldName == 'gallery'){
                                        if(!is_array($latestPostMetas[$fieldName])){
                                            $removedFields.= "All Images are Deleted <br/>";
                                        }else{
                                            $latestImageCount = sizeof($latestPostMetas[$fieldName]);
                                            $previousImageCount = sizeof($previousPostMetas[$fieldName]);
                                            if($latestImageCount < $previousImageCount){
                                                $delImageCount = $previousImageCount - $latestImageCount;
                                                $removedFields.= "$delImageCount Image is Deleted <br/>";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                        if(!empty($removedFields)){
                            $message.="Fields(whose value is deleted): <br/> $removedFields";
                        }
                        
                        if(!empty($deletedFields)){
                            $message.="Fields(index of field is deleted): <br/> $removedFields";
                        }
                        
                        if(!empty($message)){
                            $tblName = $wpdb->prefix.'postmeta';
                            $authorQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`=$postId AND `meta_key`='_edit_last'";
                            $authorRes = $wpdb->get_results($authorQry);
                            $userId = $authorRes[0]->meta_value;
                            $author_obj = get_user_by('id', $userId);
                            $authorName = $author_obj->data->user_login;
                            $mailBody.= "Author:<b> $authorName</b> changes some fields of Item Id:<b>$postId</b> on $latestModifiedTime<br/>$message<br/><br/>";
                        }
                        
                    }else{
                        //only one record of this item id
                    }
                    
                }
                $to = 'yamini.proavid@gmail.com';
                $subject = 'Change in post meta fields';
                $body = $mailBody;
                $headers = array('Content-Type: text/html; charset=UTF-8');
                if(!empty($body)){
                    wp_mail( $to, $subject, $body, $headers );
                }
            }
            
        ?>
    </head>
    
</html>