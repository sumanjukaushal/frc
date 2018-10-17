<html>
    <head>
        <title>FRC: Restore Post Metas</title>
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
            if(isset($_REQUEST['restore_item'])){
                $ID = $_REQUEST['id'];
                $restoreDataQry = "SELECT * FROM `frc_postmeta_log` WHERE `id`='$ID'";
                $restoreDataQryRes = $wpdb->get_results($restoreDataQry);
                if(!empty($restoreDataQryRes)){
                    $tblName = $wpdb->prefix.'postmeta';
                    $item_id = $restoreDataQryRes[0]->post_id;
                    $dataArr = array('meta_value'=>$restoreDataQryRes[0]->meta_value);
                    $updatePostMeta = $wpdb->update($tblName,$dataArr,array('post_id'=>$item_id,'meta_key'=>$restoreDataQryRes[0]->meta_key),array('%s'));
                    if($updatePostMeta !== false){
                        echo"Restored Successfully";
                    }else{
                        echo"Error While Restoring";
                    }
                }else{
                    echo"Data Not Found!!!";
                }
                
            }
            if(isset($_GET)){
                $id = $_GET['id'];
                $query = "SELECT * FROM `frc_postmeta_log` WHERE `id`='$id'";
                $results = $wpdb->get_results($query);
                if(!empty($results)){
                    $id = $results[0]->id;
                    $ItemID = $results[0]->post_id;
                    $ItemLink = get_permalink($ItemID);
                    $metaKey = $results[0]->meta_key;
                    $itemTitle = get_the_title($ItemID);
                    $dateModified = date( 'M j, y h:i A', strtotime($results[0]->modified) );
                    $ItemData = unserialize($results[0]->meta_value);
                    //echo'<pre>';print_r($ItemData);die;
                    echo"<form><table style='margin-left: 25px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'><tr>
                    <td><b>Item Title</b> = {$itemTitle}</td></tr>
                    <tr>
                    <td style='padding-top: 12px;'><b>Item ID</b> = <a href='$ItemLink' target='_blank'>{$ItemID}</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Item Modified</b> = {$dateModified}</td>
                    
                    </tr>
                    <tr>
                    <input type='hidden' name='id' value='{$id}' />
                    <td style='padding-top: 10px;'><input type='submit' name='restore_item' value='Click Here to Restore these Metas'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/></td>
                    </tr></table></form>
                    ";
                    
                    echo $tableBlock = '
                    <table style="width: 800px;margin-left: 25px;margin-bottom: 50px; font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
                    <tr bgcolor="#C6D5FD">
                    <th style="padding-top: 8px;padding-bottom: 8px;">Field Name</th>
                    <th>Field Value</th>
                    </tr>
                    ';
                    $counts = 0;
                    //echo'<pre>';print_r($ItemData);die;
                    foreach($ItemData as $data => $item_data){
                        $bgColor = $counts % 2 == 0 ? '#CCC' : '#FFF';
                        if($metaKey == '_ait_dir_gallery'){
                            if(!empty($item_data)){
                                $counts++;
                                echo"<tr style='background:{$bgColor}'><td style='padding-left: 30px;padding-top: 8px;padding-bottom: 8px;'>{$data}</td><td><img src='{$item_data}' style='height: 30px;width: 50px;' title='{$data}'/></td></tr>";
                            }
                        }else{
                            if($data == 'featuredItem' || $data == 'displayOpeningHours' || $data == 'headerType'){
                                continue;
                            }
                            if($data == 'map'){
                                foreach($item_data as $data_key => $data_value){
                                    if($data_key == 'swpitch' || $data_key == 'swzoom' || $data_key == 'swheading'){
                                        continue;
                                    }                
                                    if(!empty($data_value)){
                                        $counts++;
                                        echo"<tr style='background:{$bgColor}'><td style='padding-left: 30px;padding-top: 8px;padding-bottom: 8px;'>{$data_key}</td><td>{$data_value}</td></tr>";
                                    }
                                }    
                            }elseif($data == 'gallery'){
                                foreach($item_data as $data_key => $data_value){
                                    $counts++;
                                    echo"<tr style='background:{$bgColor}'><td style='padding-left: 30px;padding-top: 8px;padding-bottom: 8px;'>{$data_value['title']}</td><td><img src='{$data_value['image']}' style='height: 30px;width: 50px;' title='{$data_value['title']}'/></td></tr>";
                                }
                            }else{
                                if(!empty($item_data)){
                                    if(is_array($item_data)){
                                        $counts++;
                                        echo"<tr style='background:{$bgColor}'><td style='padding-left: 30px;padding-top: 8px;padding-bottom: 8px;'>{$data}</td><td>Enable</td></tr>";
                                    }else{
                                        $counts++;
                                        echo"<tr style='background:{$bgColor}'><td style='padding-left: 30px;padding-top: 8px;padding-bottom: 8px;'>{$data}</td><td>{$item_data}</td></tr>";
                                    }
                                }
                            }
                        }
                    }
                    echo"<tr>
                    <form>
                    <input type='hidden' name='id' value='{$id}' />
                    <td colspan='2' style='padding-top: 10px;'><input type='submit' name='restore_item' value='Click Here to Restore these Metas'style='color:#333;height: 23px;padding-left: 5px;width: 300px;font-size: 16px;background-color:#C6D5FD;border:1px solid #333;'/></td></tr>
                    </form></table>";
                }else{
                    echo'Data Not Found!!!';
                }
            }
        ?>
    </head>
    
</html>