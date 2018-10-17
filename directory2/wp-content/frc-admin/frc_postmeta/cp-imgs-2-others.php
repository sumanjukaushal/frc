<html>
    <head>
        <title>FRC: Copy Imgs 2 Other Items</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <?php 
        require_once("../../../wp-config.php");
        require_once('../../../wp-load.php');
        global $wpdb;
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        $isOldTheme = is_old_theme();
        $galleryMetaKey =  $isOldTheme ? '_ait_dir_gallery' : '_ait-item_item-data';
        $tblName = $wpdb->prefix.'postmeta';
        $tblPosts = $wpdb->prefix.'posts';
    ?>
    <body>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Copy Imgs 2 Other Items</h3><br/>
        <form action='cp-imgs-2-others.php' method='get'>
            <!--<fieldset style='width:78%'>
                <legend>Search Items</legend>-->
                <input type='text' name='search_item_id' value = '<?php if(isset($_REQUEST['search_item_id'])){ echo $_REQUEST['search_item_id']; } ?>' placeholder='ITEM ID' style='width:200px;margin-left: 25px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            <!--</fieldset>-->
        </form> 
<?php
    if(isset($_REQUEST['copy-submit'])){
        if(array_key_exists("image_checked", $_REQUEST) && is_array($_REQUEST['image_checked']) && !empty($_REQUEST['image_checked'])){
            if(array_key_exists("search_title",$_REQUEST) && !empty($_REQUEST['search_title'])){
                $searchedPostTitle = $_REQUEST['search_title'];
                $ItemId = (int)$_REQUEST['search_item_id'];
                $query = "SELECT * FROM $tblName WHERE `post_id`= $ItemId AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";    //echo $query;
                $result = $wpdb->get_results($query);   //echo "<pre>";print_r($result); echo "</pre>";die;
                if(!empty($result)){
                    $serializeData = unserialize($result[0]->meta_value);
                    $imagesFound = false;
                    if(is_old_theme()){
                        $imagesFound = true;
                        $imagesArr = array();
                        foreach($serializeData as $imageURL){
                            if(!empty($imageURL)){
                                $imagesArr[] = array('title' => '', 'image' => $imageURL);
                            }
                        }
                    }elseif(array_key_exists("gallery",$serializeData)){
                        $imagesArr = $serializeData['gallery'];
                        $imagesFound = true;
                    }
                    
                    if($imagesFound){
                        if(!empty($imagesArr)){
                            $itemTitleQry = "SELECT * FROM `$tblPosts` WHERE `post_title` LIKE '%$searchedPostTitle%'";
                            $itemTitleRes = $wpdb->get_results($itemTitleQry);
                            //echo'<pre>';print_r($itemTitleRes);die;
                            if(count($itemTitleRes)){
                                $srNo = 0;
                                $htmlBlock = "";
                                foreach($itemTitleRes as $titleKey => $itemData){
                                    $srNo++;
                                    $PostID = $itemData->ID;
                                    $postTitle = $itemData->post_title;
                                    $postStatus = $itemData->post_status;
                                    $htmlBlock.= "<tr>
                                                    <td>$srNo</td>
                                                    <td>$PostID</td>
                                                    <td>$postTitle</td>
                                                    <td>$postStatus</td>
                                                    <td><input type='checkbox' id='check' name='checked[]' value='$PostID' class='check'  /></td>
                                                </tr>";
                                }
                            }
                            $sizeOfImgArr = sizeof($imagesArr);
                            $ItemID = $result[0]->post_id;
                            $itemTitle = get_the_title($ItemID);
                            $dateModified = date( 'M j, y h:i A', strtotime($result[0]->modified) );
                            $tableBlock_1 = "<table class='table-striped' width='70%' style='margin-left: 25;'>
                                <tr>
                                    <th>Item ID = {$ItemID}</th>
                                    <th>Item Title = {$itemTitle}</th>
                                    <th>Item Last Modified = {$dateModified}</th>
                                    <th>No. Of Images = {$sizeOfImgArr}</th>
                                </tr>
                                </table>
                                ";
                            
                            
                            $rowspan = intval($sizeOfImgArr/2);
                            $tableBlock_2 = "";
                            $counter = 0;
                            foreach($imagesArr as $key => $imageData){
                                $counter++;
                                $imageTitle = $imageData['title'];
                                $imagePath = $imageData['image'];
                                $tableBlock_2.= "
                                <td><img src='$imagePath' style='height:150px;width: 150px;' title='$imageTitle'/><br/>
                                <p style='text-align: center;'><input type=checkbox name=image_checked[$imagePath] class='case' checked='checked'><p></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                ";
                                if($counter%5==0){
                                    $tableBlock_2.= "</tr><tr>";
                                }
                            }
                            echo $tableBlock = "$tableBlock_1
                            <form>
                                <div style='overflow:scroll; height:250px;width:880px;'>
                                    <table style='margin-left: 25;'>
                                        <tr>
                                        <th>Check All <input type=checkbox class='selectall' id='selectall' checked='checked'></th>   
                                    </tr><tr>
                                    $tableBlock_2
                                    </table>
                                </div><br/><br/>
                                <div style='margin-left: 25;'>
                                    <input type='hidden' name='search_item_id' value='$ItemId' />
                                    <table>
                                        <tr><td rowspan='3' valign='center'>Copy Images to Other ITEM's>></td></tr>
                                        <tr><td>
                                        <textarea name='ids_for_copy_image' value='', placeholder='Add ITEM IDs in comma separated format' rows='5' cols='50'/></textarea>
                                        </td></tr>
                                        <tr><td>
                                            <input type='radio' name='overwrite' value='overwrite' checked='checked' class='overwrite'/>Overwrite All
                                            <input type='radio' name='overwrite' value='append' class='append' />Append To Existing Images<br/><br/>
                                            <div class='position' style='display: none;'>
                                            Push To:  <input type='radio' name='push' value='Bottom' checked='checked'/> Bottom
                                            <input type='radio' name='push' value='Top' />Top
                                            </div>
                                        </td></tr>
                                        <tr><td></td><td>Or</td></tr>
                                        <tr><td></td><td colspan='2'>Search Listing by title: <input type='text' name='search_title' id='search_title' value='$searchedPostTitle' placeholder='search by title' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='copy-submit' id='copy-submit' value='Submit' /></td></tr>
                                    </table>
                                    <br/><br/>
                                    <table class='table-striped' width='70%'>
                                        <tr bgcolor='#C6D5FD'><th>Sr NO.</th><th>Item ID</th><th>Item Title</th><th>Item Status</th><th>Apply To <input type=checkbox class='checkall' id='checkall'></th></tr>
                                        $htmlBlock
                                        <tr><td colspan='3' align='center'><input type='reset' name='Reset' /> | <input type='submit' name='copy_selctd_ids' value='submit' /></td></tr>
                                    </table>
                                </div>
                            </form>
                            ";
                            //echo"</form>";
                            
                        }else{
                            echo"$ItemId Item ID Not Containing Any Image";
                        }
                    }else{
                        echo"$ItemId Item ID Not Containing Any Image";
                    }
                }else{
                    echo"NO Record of $ItemId Item ID Are Found";
                }
            }else{
                //echo'<pre>';print_r($_REQUEST);die;
                $overwrite = $_REQUEST['overwrite'];
                $pushPosition = $_REQUEST['push'];
                $sourceId = (int)$_REQUEST['search_item_id'];
                $selected_imagesArr = $_REQUEST['image_checked'];
                $themeObj = wp_get_theme();
                //if(!in_array(trim($themeObj->Name),$themeAllowed))
                $sourceQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$sourceId' AND `meta_key`='$galleryMetaKey'";
                $sourceRes = $wpdb->get_results($sourceQry);
                $finalArr = $sourceUniqueArray = $newUniqueArray = array();
                
                if(true){
                    if(!empty($sourceRes)){
                        $sourceDataArr = unserialize($sourceRes[0]->meta_value);
                        $imagesFound = false;
                        if($isOldTheme){
                            $imagesFound = true;
                            $imagesArr = array();
                            foreach($sourceDataArr as $imageURL){
                                if(!empty($imageURL))$imagesArr[] = $imageURL;
                            }
                        }elseif(array_key_exists("gallery",$sourceDataArr)){
                            $imagesArr = $sourceDataArr['gallery'];
                            $imagesFound = true;
                        }//echo'<pre>';print_r($selected_imagesArr);//echo'<pre>';print_r($imagesArr);die;
                        $finalImgArr = array();
                        $key_count = 0;
                        if($isOldTheme){
                            foreach($selected_imagesArr as $imgURL => $onOff){
                                if(!empty($imgURL))$finalImgArr[] = $imgURL;
                            }
                        }else{
                            foreach($imagesArr as $img_key => $images_data){
                                if(array_key_exists($images_data['image'], $selected_imagesArr)){
                                    $finalImgArr[$key_count]['title'] = $images_data['title'];
                                    $finalImgArr[$key_count]['image'] = $images_data['image'];
                                    $key_count++;
                                }
                            }
                        }
                        //echo'<pre>';print_r($finalImgArr);die;
                        $destinationIds = explode(",",$_REQUEST['ids_for_copy_image']);
                        if(is_array($destinationIds)){
                            $successIDs = $failedIDs = $createdIDs = array();
                            foreach($destinationIds as $key => $destinationId){ //if($destinationId == 2126)continue;
                                $destinationId = (int)$destinationId;
                                $destinationQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$destinationId' AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";
                                $destinationRes = $wpdb->get_results($destinationQry);
                                if($isOldTheme){
                                    if(count($destinationRes) ){
                                        if($overwrite == 'overwrite'){
                                            //replace old meta value
                                            $serializedImgs = serialize($finalImgArr);    //echo "<pre>";print_r($destinationRes);echo "</pre>";
                                            $dataArr = array('meta_value' => $serializedImgs);
                                            $updatePostMeta = $wpdb->update($tblName, $dataArr, array('post_id'=> $destinationId, 'meta_key' => $galleryMetaKey));
                                            if($updatePostMeta) $successIDs[] = $destinationId;
                                        }else{
                                            //merge in old meta value
                                            $existingImgArr = array();  //echo $destinationQry;
                                            $existingImgs = unserialize($destinationRes[0]->meta_value);
                                            foreach($existingImgs as $key => $existingImg) if(!empty($existingImg))$existingImgArr[] = $existingImg;
                                            //echo "<pre>";print_r($existingImgArr);
                                            if($pushPosition == 'Bottom'){
                                                $CombinedArray = array_merge($existingImgArr, $finalImgArr);
                                            }else{
                                                $CombinedArray = array_merge($finalImgArr, $existingImgArr);    
                                            }//echo "<pre>";print_r($finalImgArr);echo "</pre>";echo "<pre>";print_r($existingImgArr);echo "</pre>";echo "<pre>";print_r($CombinedArray);echo "</pre>";
                                            $CombinedArray = array_unique($CombinedArray);  //print_r($CombinedArray);
                                            $serializedImgs = serialize($CombinedArray);
                                            $dataArr = array('meta_value' => $serializedImgs);
                                            $updatePostMeta = $wpdb->update($tblName, $dataArr, array('post_id' => $destinationId, 'meta_key' => $galleryMetaKey));
                                            if($updatePostMeta) $successIDs[] = $destinationId;
                                        }
                                    }else{
                                        //create new record
                                        $serializedImgs = serialize($finalImgArr);
                                        $dataArr = array('post_id' => $destinationId, 'meta_key' => $galleryMetaKey, 'meta_value' => $serializedImgs);
                                        $updated = $wpdb->insert( $tblName, $dataArr);
                                        if($updated)$createdIDs[] = $destinationId;
                                    }   //echo $wpdb->last_query;
                                }else{
                                    //die("--wrongly reached---");
                                    //echo'<pre>';print_r($finalImgArr);die;
                                    if(!empty($destinationRes)){
                                        $destinationDataArr = unserialize($destinationRes[0]->meta_value);
                                        if($overwrite == 'overwrite'){
                                            $destinationDataArr["displayGallery"] = 1;
                                            $destinationDataArr["gallery"] = $finalImgArr;
                                        }else{
                                            if(array_key_exists("gallery",$destinationDataArr)){
                                                
                                                foreach($destinationDataArr["gallery"] as $key => $destinationImageData){
                                                    $destinationUniqueArray[$destinationImageData['title']] = $destinationImageData['image'];
                                                }
                                                
                                                foreach($finalImgArr as $key => $sourceImageData){
                                                    $sourceUniqueArray[$sourceImageData['title']] = $sourceImageData['image'];
                                                }
                                                if($pushPosition == 'Bottom'){
                                                    $CombinedArray = array_merge($destinationUniqueArray,$sourceUniqueArray);
                                                }else{
                                                    $CombinedArray = array_merge($sourceUniqueArray,$destinationUniqueArray);    
                                                }
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
                                                $destinationDataArr["gallery"] = $finalImgArr;
                                            }
                                        }
                                        $destinationDataSerl = serialize($destinationDataArr);
                                        $dataArr = array('meta_value'=>$destinationDataSerl);
                                        $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$destinationId,'meta_key'=> $galleryMetaKey));
                                        if($updatePostMeta) $successIDs[] = $destinationId;
                                        
                                    }else{
                                        $postMetaArr = array();
                                        $postExistQry = "SELECT * FROM `$tblPosts` WHERE `ID`='$destinationId'";
                                        $postExistRes = $wpdb->get_results($postExistQry);
                                        
                                        if(!empty($postExistRes)){
                                            $postMetaArr['map'] = array('addess'=>'test',
                                                                        'latitude'=>'0',
                                                                        'longitude'=>'0'
                                                                        );
                                            $postMetaArr['displayGallery'] = 1;
                                            $postMetaArr['gallery'] = $finalImgArr;
                                            $postMetaArrSer = serialize($postMetaArr);
                                            $finalDataArr = array(
                                                                  'post_id'=>$destinationId,  
                                                                  'meta_key'=>'_ait-item_item-data',
                                                                  'meta_value'=>$postMetaArrSer
                                                                  );
                                            //echo'<pre>';print_r($finalDataArr);die;
                                            $insertPostMeta = $wpdb->insert($tblName,$finalDataArr,array('%d','%s','%s'));
                                            if($insertPostMeta){
                                                $createdIDs[] = $destinationId;
                                            }
                                        }else{
                                            echo"Item ID:$destinationId does't exists</br>";
                                        }
                                    }
                                }
                            }//foreach
                            //echo "<pre> 1. ";print_r($successIDs);echo "</pre>";
                            //echo "<pre> 2. ";print_r($createdIDs);echo "</pre>";
                            if(count($successIDs)){
                                $updatedIDs = implode(",", $successIDs);
                                echo "Record updated for the following ids $updatedIDs<br/>";
                            }
                            if(count($createdIDs)){
                                $createdIDs = implode(",", $createdIDs);
                                echo "<br/>Images added for the following ids $createdIDs<br/>";
                            }
                            echo "Script successfully executed";
                        } //if destination ids
                    }
                }
            }
        }else{
            echo"Please Select a Image";
        }
    }elseif(isset($_REQUEST['search_item_id']) && !empty($_REQUEST['search_item_id'])){
        //copy images
        if(isset($_REQUEST['copy_selctd_ids'])){
            if(array_key_exists("image_checked", $_REQUEST) && is_array($_REQUEST['image_checked']) && !empty($_REQUEST['image_checked'])){
                $overwrite = $_REQUEST['overwrite'];
                $pushPosition = $_REQUEST['push'];
                $sourceId = (int)$_REQUEST['search_item_id'];
                $selected_imagesArr = $_REQUEST['image_checked'];
                $themeObj = wp_get_theme();
                //if(!in_array(trim($themeObj->Name),$themeAllowed))
                $sourceQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$sourceId' AND `meta_key`='$galleryMetaKey'";
                $sourceRes = $wpdb->get_results($sourceQry);
                $finalArr = $sourceUniqueArray = $newUniqueArray = array();
                
                if(true){
                    if(!empty($sourceRes)){
                        $sourceDataArr = unserialize($sourceRes[0]->meta_value);
                        $imagesFound = false;
                        if($isOldTheme){
                            $imagesFound = true;
                            $imagesArr = array();
                            foreach($sourceDataArr as $imageURL){
                                if(!empty($imageURL))$imagesArr[] = $imageURL;
                            }
                        }elseif(array_key_exists("gallery",$sourceDataArr)){
                            $imagesArr = $sourceDataArr['gallery'];
                            $imagesFound = true;
                        }//echo'<pre>';print_r($selected_imagesArr);//echo'<pre>';print_r($imagesArr);die;
                        $finalImgArr = array();
                        $key_count = 0;
                        if($isOldTheme){
                            foreach($selected_imagesArr as $imgURL => $onOff){
                                if(!empty($imgURL))$finalImgArr[] = $imgURL;
                            }
                        }else{
                            foreach($imagesArr as $img_key => $images_data){
                                if(array_key_exists($images_data['image'], $selected_imagesArr)){
                                    $finalImgArr[$key_count]['title'] = $images_data['title'];
                                    $finalImgArr[$key_count]['image'] = $images_data['image'];
                                    $key_count++;
                                }
                            }
                        }
                        //echo'<pre>';print_r($finalImgArr);die;
                        $destinationIds = $_REQUEST['checked'];//explode(",",$_REQUEST['ids_for_copy_image']);
                        if(is_array($destinationIds)){
                            $successIDs = $failedIDs = $createdIDs = array();
                            foreach($destinationIds as $key => $destinationId){ //if($destinationId == 2126)continue;
                                $destinationId = (int)$destinationId;
                                $destinationQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$destinationId' AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";
                                $destinationRes = $wpdb->get_results($destinationQry);
                                if($isOldTheme){
                                    if(count($destinationRes) ){
                                        if($overwrite == 'overwrite'){
                                            //replace old meta value
                                            $serializedImgs = serialize($finalImgArr);    //echo "<pre>";print_r($destinationRes);echo "</pre>";
                                            $dataArr = array('meta_value' => $serializedImgs);
                                            $updatePostMeta = $wpdb->update($tblName, $dataArr, array('post_id'=> $destinationId, 'meta_key' => $galleryMetaKey));
                                            if($updatePostMeta) $successIDs[] = $destinationId;
                                        }else{
                                            //merge in old meta value
                                            $existingImgArr = array();  //echo $destinationQry;
                                            $existingImgs = unserialize($destinationRes[0]->meta_value);
                                            foreach($existingImgs as $key => $existingImg) if(!empty($existingImg))$existingImgArr[] = $existingImg;
                                            //echo "<pre>";print_r($existingImgArr);
                                            if($pushPosition == 'Bottom'){
                                                $CombinedArray = array_merge($existingImgArr, $finalImgArr);
                                            }else{
                                                $CombinedArray = array_merge($finalImgArr, $existingImgArr);    
                                            }//echo "<pre>";print_r($finalImgArr);echo "</pre>";echo "<pre>";print_r($existingImgArr);echo "</pre>";echo "<pre>";print_r($CombinedArray);echo "</pre>";
                                            $CombinedArray = array_unique($CombinedArray);  //print_r($CombinedArray);
                                            $serializedImgs = serialize($CombinedArray);
                                            $dataArr = array('meta_value' => $serializedImgs);
                                            $updatePostMeta = $wpdb->update($tblName, $dataArr, array('post_id' => $destinationId, 'meta_key' => $galleryMetaKey));
                                            if($updatePostMeta) $successIDs[] = $destinationId;
                                        }
                                    }else{
                                        //create new record
                                        $serializedImgs = serialize($finalImgArr);
                                        $dataArr = array('post_id' => $destinationId, 'meta_key' => $galleryMetaKey, 'meta_value' => $serializedImgs);
                                        $updated = $wpdb->insert( $tblName, $dataArr);
                                        if($updated)$createdIDs[] = $destinationId;
                                    }   //echo $wpdb->last_query;
                                }else{
                                    //die("--wrongly reached---");
                                    //echo'<pre>';print_r($finalImgArr);die;
                                    if(!empty($destinationRes)){
                                        $destinationDataArr = unserialize($destinationRes[0]->meta_value);
                                        if($overwrite == 'overwrite'){
                                            $destinationDataArr["displayGallery"] = 1;
                                            $destinationDataArr["gallery"] = $finalImgArr;
                                        }else{
                                            if(array_key_exists("gallery",$destinationDataArr)){
                                                
                                                foreach($destinationDataArr["gallery"] as $key => $destinationImageData){
                                                    $destinationUniqueArray[$destinationImageData['title']] = $destinationImageData['image'];
                                                }
                                                
                                                foreach($finalImgArr as $key => $sourceImageData){
                                                    $sourceUniqueArray[$sourceImageData['title']] = $sourceImageData['image'];
                                                }
                                                if($pushPosition == 'Bottom'){
                                                    $CombinedArray = array_merge($destinationUniqueArray,$sourceUniqueArray);
                                                }else{
                                                    $CombinedArray = array_merge($sourceUniqueArray,$destinationUniqueArray);    
                                                }
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
                                                $destinationDataArr["gallery"] = $finalImgArr;
                                            }
                                        }
                                        $destinationDataSerl = serialize($destinationDataArr);
                                        $dataArr = array('meta_value'=>$destinationDataSerl);
                                        $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$destinationId,'meta_key'=> $galleryMetaKey));
                                        if($updatePostMeta) $successIDs[] = $destinationId;
                                        
                                    }else{
                                        $postMetaArr = array();
                                        $postExistQry = "SELECT * FROM `$tblPosts` WHERE `ID`='$destinationId'";
                                        $postExistRes = $wpdb->get_results($postExistQry);
                                        
                                        if(!empty($postExistRes)){
                                            $postMetaArr['map'] = array('addess'=>'test',
                                                                        'latitude'=>'0',
                                                                        'longitude'=>'0'
                                                                        );
                                            $postMetaArr['displayGallery'] = 1;
                                            $postMetaArr['gallery'] = $finalImgArr;
                                            $postMetaArrSer = serialize($postMetaArr);
                                            $finalDataArr = array(
                                                                  'post_id'=>$destinationId,  
                                                                  'meta_key'=>'_ait-item_item-data',
                                                                  'meta_value'=>$postMetaArrSer
                                                                  );
                                            //echo'<pre>';print_r($finalDataArr);die;
                                            $insertPostMeta = $wpdb->insert($tblName,$finalDataArr,array('%d','%s','%s'));
                                            if($insertPostMeta){
                                                $createdIDs[] = $destinationId;
                                            }
                                        }else{
                                            echo"Item ID:$destinationId does't exists</br>";
                                        }
                                    }
                                }
                            }//foreach
                            //echo "<pre> 1. ";print_r($successIDs);echo "</pre>";
                            //echo "<pre> 2. ";print_r($createdIDs);echo "</pre>";
                            if(count($successIDs)){
                                $updatedIDs = implode(",", $successIDs);
                                echo "Record updated for the following ids $updatedIDs<br/>";
                            }
                            if(count($createdIDs)){
                                $createdIDs = implode(",", $createdIDs);
                                echo "<br/>Images added for the following ids $createdIDs<br/>";
                            }
                            echo "Script successfully executed";
                        } //if destination ids
                    }
                }
            }
            //echo'<pre>';print_r($_REQUEST);die;
        }
        //copy images
        $ItemId = (int)$_REQUEST['search_item_id'];
        $query = "SELECT * FROM $tblName WHERE `post_id`= $ItemId AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";    //echo $query;
        $result = $wpdb->get_results($query);   //echo "<pre>";print_r($result); echo "</pre>";die;
        if(!empty($result)){
            $serializeData = unserialize($result[0]->meta_value);
            $imagesFound = false;
            if(is_old_theme()){
                $imagesFound = true;
                $imagesArr = array();
                foreach($serializeData as $imageURL){
                    if(!empty($imageURL)){
                        $imagesArr[] = array('title' => '', 'image' => $imageURL);
                    }
                }
            }elseif(array_key_exists("gallery",$serializeData)){
                $imagesArr = $serializeData['gallery'];
                $imagesFound = true;
            }
            
            if($imagesFound){
                if(!empty($imagesArr)){
                    $sizeOfImgArr = sizeof($imagesArr);
                    $ItemID = $result[0]->post_id;
                    $itemTitle = get_the_title($ItemID);
                    $dateModified = date( 'M j, y h:i A', strtotime($result[0]->modified) );
                    $tableBlock_1 = "<table class='table-striped' width='70%' style='margin-left: 25;'>
                        <tr>
                            <th>Item ID = {$ItemID}</th>
                            <th>Item Title = {$itemTitle}</th>
                            <th>Item Last Modified = {$dateModified}</th>
                            <th>No. Of Images = {$sizeOfImgArr}</th>
                        </tr>
                        </table>
                        ";
                    
                    
                    $rowspan = intval($sizeOfImgArr/2);
                    $tableBlock_2 = "";
                    $counter = 0;
                    //echo'<pre>';print_r($imagesArr);die;
                    foreach($imagesArr as $key => $imageData){
                        $counter++;
                        $imageTitle = $imageData['title'];
                        $imagePath = $imageData['image'];
                        $tableBlock_2.= "
                        <td><img src='$imagePath' style='height:150px;width: 150px;' title='$imageTitle'/><br/>
                        <p style='text-align: center;'><input type=checkbox name=image_checked[$imagePath] class='case' checked='checked'><p></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        ";
                        if($counter%5==0){
                            $tableBlock_2.= "</tr><tr>";
                        }
                    }
                    echo $tableBlock = "$tableBlock_1
                    <form>
                        <div style='overflow:scroll; height:250px;width:880px;'>
                            <table style='margin-left: 25;'>
                                <tr>
                                <th>Check All <input type=checkbox class='selectall' id='selectall' checked='checked'></th>   
                            </tr><tr>
                            $tableBlock_2
                            </table>
                        </div>
                        <br/><br/>
                        <div style='margin-left: 25;'>
                            <input type='hidden' name='search_item_id' value='$ItemId' />
                            <table>
                                <tr><td rowspan='3' valign='center'>Copy Images to Other ITEM's>></td></tr>
                                <tr><td>
                                <textarea name='ids_for_copy_image' value='', placeholder='Add ITEM IDs in comma separated format' rows='5' cols='50'/></textarea>
                                </td></tr>
                                <tr><td>
                                    <input type='radio' name='overwrite' value='overwrite' checked='checked' class='overwrite'/>Overwrite All
                                    <input type='radio' name='overwrite' value='append' class='append' />Append To Existing Images<br/><br/>
                                    <div class='position' style='display: none;'>
                                    Push To:  <input type='radio' name='push' value='Bottom' checked='checked'/> Bottom
                                    <input type='radio' name='push' value='Top' />Top
                                    </div>
                                </td></tr>
                                <tr><td></td><td>Or</td></tr>
                                <tr><td></td><td colspan='2'>Search Listing by title: <input type='text' name='search_title' id='search_title' placeholder='search by title' />&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type='submit' name='copy-submit' id='copy-submit' value='Submit' /></td></tr>
                            </table>
                        </div>
                    </form>
                    ";
                    //echo"</form>";
                    
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
    </body>
</html>

<script language="javascript">
$(function(){
 
    $("#selectall").click(function () {
          $('.case').attr('checked', this.checked);
    });
 
    $(".case").click(function(){
 
        if($(".case").length == $(".case:checked").length) {
            $("#selectall").attr("checked", "checked");
        } else {
            $("#selectall").removeAttr("checked");
        }
 
    });
       
    
});
</script>
<script language="javascript">
$(function(){
 
    $("#checkall").click(function () {
          $('.check').attr('checked', this.checked);
    });
 
    $(".check").click(function(){
 
        if($(".check").length == $(".check:checked").length) {
            $("#checkall").attr("checked", "checked");
        } else {
            $("#checkall").removeAttr("checked");
        }
 
    });
       
    
});
</script>
<script>
    $(".append").click(function(){
        $(".position").show();
    });
    
    $(".overwrite").click(function(){
        $(".position").hide();
    });
</script>