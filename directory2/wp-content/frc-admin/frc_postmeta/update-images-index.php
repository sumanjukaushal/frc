<html>
    <head>
        <title>FRC - change image indexes for ITEM's</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
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
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Change Image indexes</h3><br/>
        <form action='update-images-index.php' method='get'>
            <!--<fieldset style='width:78%'>
                <legend>Search Items</legend>-->
                <input type='text' name='search_item_id' value = '<?php if(isset($_REQUEST['search_item_id'])){ echo $_REQUEST['search_item_id']; } ?>' placeholder='ITEM ID' style='width:200px;margin-left: 25px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            <!--</fieldset>-->
        </form> 
<?php
    if(isset($_REQUEST['index-submit'])){
        if(array_key_exists("index",$_REQUEST) && is_array($_REQUEST['index']) && !empty($_REQUEST['index'])){
            
            $ID = $_REQUEST['id'];
            $newImgArr = $_REQUEST['index'];
            asort($newImgArr);
            $oldQry = "SELECT `meta_value` FROM `$tblName` WHERE `post_id`='$ID' AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";
            $oldRes = $wpdb->get_results($oldQry);
            $finlImgInxArr = $ImgInxArr = array();
            if(!empty($oldRes)){
                $oldDataArr = unserialize($oldRes[0]->meta_value);
                if($isOldTheme){
                    if(!empty($oldDataArr)){
                        foreach($newImgArr as $imageIndxkey => $indx){
                            if(in_array($imageIndxkey,$oldDataArr)){
                                $ImgInxArr[] = $imageIndxkey;        
                            }
                        }
                    }
                    $finlImgInxArr = serialize($ImgInxArr);
                    $dataArr = array('meta_value'=>$finlImgInxArr);
                    $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$ID,'meta_key'=>$galleryMetaKey),array('%s'));
                    if($updatePostMeta === false){
                        echo"Error while changing Ordering of Item ID:$ID Images.<br/>";
                    }else{
                        echo"Ordering of Item ID:$ID Images Successfully Changed.<br/>";
                    }
                }else{
                    if(array_key_exists("gallery",$oldDataArr) && is_array($oldDataArr['gallery']) && !empty($oldDataArr['gallery'])){
                        $oldImgArr = $oldDataArr['gallery'];
                        foreach($oldImgArr as $key => $oldImg){
                            $oldImgIndxArr[$oldImg['image']] = $oldImg['title'];
                        }
                        
                        foreach($newImgArr as $img => $indx){
                            $ImgInxArr[$img] = $oldImgIndxArr[$img];
                        }
                        
                        $key_count = 0;
                        foreach($ImgInxArr as $path => $title){
                            $finlImgInxArr[$key_count]['title'] = $title;
                            $finlImgInxArr[$key_count]['image'] = $path;
                            $key_count++;
                        }
                        
                        $oldDataArr['gallery'] = $finlImgInxArr;
                        $serializeData = serialize($oldDataArr);
                        $dataArr = array('meta_value'=>$serializeData);
                        
                        $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$ID,'meta_key'=>$galleryMetaKey),array('%s'));
                        if($updatePostMeta === false){
                            echo"Error while changing Ordering of Item ID:$ID Images.<br/>";
                        }else{
                            echo"Ordering of Item ID:$ID Images Successfully Changed.<br/>";
                        }
                    }
                }
            }
        }else{
            echo"Something went wrong!!!";
        }
        
    }elseif(isset($_REQUEST['search_item_id']) && !empty($_REQUEST['search_item_id'])){
        $ItemId = (int)$_REQUEST['search_item_id'];
        $query = "SELECT * FROM $tblName WHERE `post_id`= $ItemId AND `meta_key`='$galleryMetaKey' ORDER BY `meta_id` DESC LIMIT 1";
        $result = $wpdb->get_results($query);
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
                    $headingHTML = "
                        <table style='font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                            <tr><th>Item ID = {$ItemID}</th><th>Item Title = {$itemTitle}</th><th>Item Last Modified = {$dateModified}</th></tr>
                        </table>";
                    $sortOpts = "";
                    for($i=1; $i <= 20; $i++) $sortOpts.="<option value='$i'>$i</option>";
                    $rowHTML = "";
                    foreach($imagesArr as $key => $imageData){
                        $imageTitle = $imageData['title'];
                        $imagePath = $imageData['image'];
                        $rowHTML.="<tr>
                            <td><a href='$imagePath' target='_blank'><img src='$imagePath' style='height:100px;width: 100px;' title='$imageTitle'/></a>&nbsp;&nbsp;&nbsp;&raquo;&raquo;</td>
                            <td style='padding-left: 20px;'><select name='index[$imagePath]'>$sortOpts</select></td>
                        </tr><tr style='height: 15px;'></tr>";
                    }
                    
                    echo $formHTML=<<<FORM_HTML
                    <form>
                        <input type='hidden' name='id' value='$ItemID' />
                        <table style='margin-left: 25px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                            <tr>
                                <th bgcolor='#C6D5FD'>Image</th>
                                <th>No. Of Images = {$sizeOfImgArr}</th>
                                <td><input type='submit' name='index-submit' value='Change Img Order' /></td>
                            </tr>
                            $rowHTML
                            <tr>
                                <td></td>
                                <td><input type='submit' name='index-submit' value='Change Img Order' /></td>
                            </tr>
                        </table>
                    </form>
FORM_HTML;
                }else{
                    echo "$ItemId Item ID Not Containing Any Image";
                }
            }else{
                echo "$ItemId Item ID Not Containing Any Image";
            }
        }else{
            echo "NO Record of $ItemId Item ID Are Found";
        }
    }
?>
    </body>
</html>