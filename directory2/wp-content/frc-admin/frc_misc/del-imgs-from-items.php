<html>
    <head>
        <title>Frc: Delete Images From Items</title>
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
<?php 
   require_once("../../../wp-config.php");
   require_once('../../../wp-load.php');
   global $wpdb;
   defined('DS') or define('DS', DIRECTORY_SEPARATOR);
   require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
   $isOldTheme = is_old_theme();
   $galleryMetaKey =  $isOldTheme ? '_ait_dir_gallery' : '_ait-item_item-data';
?>
    <body>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Delete Images From Items</h3><br/>
        <form action='del-imgs-from-items.php' method='get'>
            <!--<fieldset style='width:78%'>
                <legend>Search Items</legend>-->
                <input type='text' name='search_item_id' value = '<?php if(isset($_REQUEST['search_item_id'])){ echo $_REQUEST['search_item_id']; } ?>' placeholder='ITEM ID' style='width:200px;margin-left: 25px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            <!--</fieldset>-->
        </form>        
    </body>
<?php
    if(isset($_REQUEST['delete-submit'])){
        $sourceId = $_REQUEST['id'];
        $tblName = $wpdb->prefix.'postmeta';
        $sourceQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$sourceId' AND `meta_key`='$galleryMetaKey'";
        $sourceRes = $wpdb->get_results($sourceQry);
        $updatedimgArr = $finalArr = $sourceUniqueArray = $newUniqueArray = array();
        if($isOldTheme){
            $imgs2BeDeletedArr = $_REQUEST['itemImgs'];
            $itemsFromWhereDeletedArr = explode(",", $_REQUEST['ids_for_copy_image']);
            if(count($imgs2BeDeletedArr)){
                foreach($itemsFromWhereDeletedArr as $itemID){
                    $imgQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$itemID' AND `meta_key`='$galleryMetaKey'";
                    $imgRes = $wpdb->get_results($imgQry);
                    $itemImgs = unserialize($imgRes[0]->meta_value);        //echo "<pre>";print_r($itemImgs);echo "</pre>";
                    $newImgArr = array_diff($itemImgs,$imgs2BeDeletedArr);
                    $refinedArr = array();
                    foreach($newImgArr as $imgURL){
                        if(!empty($imgURL)){$refinedArr[] = $imgURL;}
                    }
                    //echo "<pre>";print_r($refinedArr);echo "</pre>";die;
                    $dataArr = array('meta_value' => serialize($refinedArr));
                    //echo "<pre>";print_r($dataArr);echo "</pre>";echo "<pre>";print_r(array('post_id' => $itemID, 'meta_key' => $galleryMetaKey ));echo "</pre>";
                    $updatePostMeta = $wpdb->update($tblName, $dataArr, array('post_id' => $itemID, 'meta_key' => $galleryMetaKey ));
                }
                echo "Images deleted successfully for Item IDs:".implode(", ", $itemsFromWhereDeletedArr)."<br/>";
            }else{
                echo "No image selected!";
            }
        }elseif(!empty($sourceRes)){
            $sourceDataArr = unserialize($sourceRes[0]->meta_value);
            
            if(array_key_exists("gallery",$sourceDataArr)){
                $imagesArr = $sourceDataArr['gallery'];
            }
            
            $destinationIds = explode(",",$_REQUEST['ids_for_copy_image']);
            
            foreach($destinationIds as $key => $destinationId){
                $destinationQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$destinationId' AND `meta_key`='$galleryMetaKey'";
                $destinationRes = $wpdb->get_results($destinationQry);
                if(!empty($destinationRes)){
                    $destinationDataArr = unserialize($destinationRes[0]->meta_value);
                    
                    if(array_key_exists("gallery",$destinationDataArr)){
                        
                        foreach($destinationDataArr["gallery"] as $key => $destinationImageData){
                            $destinationUniqueArray[$destinationImageData['title']] = $destinationImageData['image'];
                        }
                        
                        foreach($imagesArr as $key => $sourceImageData){
                            $sourceUniqueArray[$sourceImageData['title']] = $sourceImageData['image'];
                        }
                        
                        foreach($destinationUniqueArray as $imgTitle => $imgpath){
                            if(in_array($imgpath,$sourceUniqueArray)){
                                continue;
                            }
                            $updatedimgArr[$imgTitle] = $imgpath;
                        }
                        
                        $keY = 0;
                        foreach($updatedimgArr as $title => $image){
                            $finalArr[$keY]['title'] = $title;
                            $finalArr[$keY]['image'] = $image;
                            $keY++;
                        }
                        $destinationDataArr["gallery"] = $finalArr;
                    }else{
                        //
                    }
                    
                    $destinationDataSerl = serialize($destinationDataArr);
                    $dataArr = array('meta_value'=>$destinationDataSerl);
                    $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$destinationId,'meta_key'=>'_ait-item_item-data'),array('%s'));
                    
                    if($updatePostMeta){
                        echo"Delete Images Successfully From Item ID:$destinationId<br/>";
                    }else{
                        echo"Error While Deleting Images From Item ID:$destinationId<br/>";
                    }
                    
                }else{
                    echo"Item ID:$destinationId does't exists</br>";
                }
            }
        }
    }elseif(isset($_REQUEST['search_item_id']) && !empty($_REQUEST['search_item_id'])){
        $ItemId = $_REQUEST['search_item_id'];
        $tblName = $wpdb->prefix.'postmeta';
        $query = "SELECT * FROM $tblName WHERE `post_id`= $ItemId AND `meta_key`='$galleryMetaKey'";
        $result = $wpdb->get_results($query);
        $ItemID = $result[0]->post_id;
        $itemTitle = get_the_title($ItemID);
        $dateModified = date( 'M j, y h:i A', strtotime($result[0]->modified) );
        $headerTbl = "<table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr>
                        <th>Item ID = {$ItemID}</th><th></th><th></th><th></th>
                        <th>Item Title = {$itemTitle}</th><th></th><th></th><th></th>
                        <th>Item Last Modified = {$dateModified}</th><th></th><th></th><th></th>
                        </table>
                        ";
        if(!empty($result)){//echo "<pre>";print_r($result[0]->meta_value);echo "</pre>";
            $serializeData = unserialize($result[0]->meta_value);
            if($isOldTheme){
                //echo "<pre>";print_r($serializeData);echo "</pre>";die;
                echo $headerTbl;
                $imgURLArr = array();
                foreach($serializeData as $imgURL){
                    if(!empty($imgURL)){
                        $imgURLArr[] = $imgURL;
                    }
                }
                $sizeOfImgArr = sizeof($imgURLArr);
                $imgRows = "";
                foreach($imgURLArr as $key => $imgURL){
                    $imgRows.="<tr><td><img src='$imgURL' style='height:170px;width: 170px;' />&nbsp;Delete <input type='checkbox' name='itemImgs[]' value='$imgURL' /></td></tr>";
                }
                    
                echo $htmlForm = <<<HTML_FORM
                <form method='post'>
                    <table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                        <tr>
                            <th bgcolor='#C6D5FD'>Image</th>
                            <th>No. Of Images = {$sizeOfImgArr}</th>
                            <td></td>
                        </tr>
                        <tr>
                            <td><table>$imgRows</table></td>
                            <td valign='top'>
                                <input type='hidden' name='id' value='$ItemId' />
                                <table>
                                    <tr><td rowspan='3' valign='center'>Delete Images From Other ITEM's&raquo;</td></tr>
                                    <tr><textarea name='ids_for_copy_image' placeholder='Add ITEM IDs in comma separated format' rows='5' cols='50'/></textarea></td></tr>
                                    <tr><td><input type='submit' name='delete-submit' value='Submit' /></td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
HTML_FORM;
                    
            }elseif(array_key_exists("gallery",$serializeData)){
                $imagesArr = $serializeData['gallery'];
                if(!empty($imagesArr)){
                    $sizeOfImgArr = sizeof($imagesArr);
                    echo $headerTbl;
                    
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
                                <tr><td rowspan='3' valign='center'>Delete Images From Other ITEM's>></td></tr>
                                <tr><td>
                                <textarea name='ids_for_copy_image' value='', placeholder='Add ITEM IDs in comma separated format' rows='5' cols='50'/></textarea>
                                </td></tr>
                            </table>
                            <center><input type='submit' name='delete-submit' value='Submit' /><br/></center>
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
            echo "NO Record of $ItemId Item ID Are Found";
        }
    }
?>
</html>