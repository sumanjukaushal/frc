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
            ?>
            <body>
                <form action='cp-imgs-2-others.php' method='get'>
                    <fieldset style='width:78%'>
                        <legend>Search Items</legend>
                        <input type='text' name='search_item_id' value = '<?php if(isset($_REQUEST['search_item_id'])){ echo $_REQUEST['search_item_id']; } ?>' placeholder='ITEM ID' style='width:200px;'/>
                        <span style='padding-left:20px;'>
                        <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                        </span>
                    </fieldset>
                </form>        
            </body>
            <?php
            if(isset($_REQUEST['copy-submit'])){
                $overwrite = $_REQUEST['overwrite'];
                $sourceId = $_REQUEST['id'];
                $tblName = $wpdb->prefix.'postmeta';
                $sourceQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$sourceId' AND `meta_key`='_ait-item_item-data'";
                $sourceRes = $wpdb->get_results($sourceQry);
                $finalArr = $sourceUniqueArray = $newUniqueArray = array();
                if(!empty($sourceRes)){
                    $sourceDataArr = unserialize($sourceRes[0]->meta_value);
                    
                    if(array_key_exists("gallery",$sourceDataArr)){
                        $imagesArr = $sourceDataArr['gallery'];
                    }
                    
                    $destinationIds = explode(",",$_REQUEST['ids_for_copy_image']);
                    
                    foreach($destinationIds as $key => $destinationId){
                        $destinationQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$destinationId' AND `meta_key`='_ait-item_item-data'";
                        $destinationRes = $wpdb->get_results($destinationQry);
                        if(!empty($destinationRes)){
                            $destinationDataArr = unserialize($destinationRes[0]->meta_value);
                            
                            if($overwrite == 'on'){
                                $destinationDataArr["displayGallery"] = 1;
                                $destinationDataArr["gallery"] = $imagesArr;
                            }else{
                                if(array_key_exists("gallery",$destinationDataArr)){
                                    
                                    foreach($destinationDataArr["gallery"] as $key => $destinationImageData){
                                        $destinationUniqueArray[$destinationImageData['title']] = $destinationImageData['image'];
                                    }
                                    
                                    foreach($imagesArr as $key => $sourceImageData){
                                        $sourceUniqueArray[$sourceImageData['title']] = $sourceImageData['image'];
                                    }
                                    
                                    $CombinedArray = array_merge($destinationUniqueArray,$sourceUniqueArray);
                                    $CombinedArray = array_unique($CombinedArray);
                                    $key = 0;
                                    foreach($CombinedArray as $title => $image){
                                        $finalArr[$key]['title'] = $title;
                                        $finalArr[$key]['image'] = $image;
                                        $key++;
                                    }
                                    $destinationDataArr["gallery"] = $finalArr;
                                }else{
                                    $destinationDataArr["displayGallery"] = 1;
                                    $destinationDataArr["gallery"] = $imagesArr;
                                }
                            }
                            $destinationDataSerl = serialize($destinationDataArr);
                            $dataArr = array('meta_value'=>$destinationDataSerl);
                            $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$destinationId,'meta_key'=>'_ait-item_item-data'),array('%s'));
                            
                            if($updatePostMeta){
                                echo"Copied Images Successfully TO Item ID:$destinationId<br/>";
                            }else{
                                echo"Error While Coping Images TO Item ID:$destinationId<br/>";
                            }
                            
                        }else{
                            echo"Item ID:$destinationId does't exists</br>";
                        }
                    }
                }
            }
            
            if(isset($_REQUEST['search_item_id']) && !empty($_REQUEST['search_item_id'])){
                $ItemId = $_REQUEST['search_item_id'];
                $tblName = $wpdb->prefix.'postmeta';
                $query = "SELECT * FROM $tblName WHERE `post_id`=$ItemId AND `meta_key`='_ait-item_item-data'";
                $result = $wpdb->get_results($query);
                if(!empty($result)){
                    $serializeData = unserialize($result[0]->meta_value);
                    if(array_key_exists("gallery",$serializeData)){
                        $imagesArr = $serializeData['gallery'];
                        if(!empty($imagesArr)){
                            $sizeOfImgArr = sizeof($imagesArr);
                            $ItemID = $result[0]->post_id;
                            $itemTitle = get_the_title($ItemID);
                            $dateModified = date( 'M j, y h:i A', strtotime($result[0]->modified) );
                            echo"<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr>
                                <th>Item ID = {$ItemID}</th><th></th><th></th><th></th>
                                <th>Item Title = {$itemTitle}</th><th></th><th></th><th></th>
                                <th>Item Last Modified = {$dateModified}</th><th></th><th></th><th></th>
                                </table>
                                ";
                            echo $tableBlock = "
                            <table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                            <tr>
                            <th bgcolor='#C6D5FD'>Image</th>
                            <th>No. Of Images = {$sizeOfImgArr}</th>
                            </tr>";
                            
                            $rowspan = intval($sizeOfImgArr/2);
                            foreach($imagesArr as $key => $imageData){
                                $imageTitle = $imageData['title'];
                                $imagePath = $imageData['image'];
                                echo"<tr>
                                <td><img src='$imagePath' style='height:170px;width: 170px;' title='$imageTitle'/></td>
                                ";
                                if($rowspan == $key || $sizeOfImgArr == 1){
                                    echo"<td>
                                    <form>
                                    <input type='hidden' name='id' value='$ItemId' />
                                    <table>
                                        <tr><td rowspan='3' valign='center'>Copy Images to Other ITEM's>></td></tr>
                                        <tr><td>
                                        <textarea name='ids_for_copy_image' value='', placeholder='Add ITEM IDs in comma separated format' rows='5' cols='50'/></textarea>
                                        </td></tr>
                                        <tr><td>
                                        <input type='checkbox' name='overwrite' checked='checked'/>Overwrite Existing
                                        </td></tr>
                                    </table>
                                    <center><input type='submit' name='copy-submit' value='Submit' /><br/></center>
                                    </form></td>
                                    ";        
                                }
                            }
                            
                        }else{
                            echo"$ItemId Item ID Not Containing Any Image";
                        }
                    }else{
                        echo"$ItemId Item ID Not Containing Any Image";
                    }
                }else{
                    echo"NO Record of $ItemId Item ID Are Found";
                }
            }
            ?>
</html>