<?php 
        require_once("../../../wp-config.php");
        require_once('../../../wp-load.php');
        global $wpdb;
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        $isOldTheme = is_old_theme();
        $tblPosts = $wpdb->prefix.'posts';
        $post_per_page = $perPage = 50;	//No of Items to be shown on page
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;	//Page No
        $offset = ( $page * $post_per_page ) - $post_per_page;	//Page offset
        if(isset($_REQUEST['submit'])){
            if(!empty($_REQUEST['search_item_title'])){
                $searchedContent = $_REQUEST['search_item_title'];
                $totalPostQry = "SELECT COUNT(*) FROM `$tblPosts` WHERE `post_title` LIKE '%$searchedContent%' AND `post_status` in ('publish', 'draft', 'pending') ORDER BY `post_modified` DESC";
                $total = $wpdb->get_var($totalPostQry);
                $postQry = "SELECT * FROM `$tblPosts` WHERE `post_title` LIKE '%$searchedContent%' AND `post_status` in ('publish', 'draft', 'pending') ORDER BY `post_modified` DESC LIMIT ${offset}, ${perPage}";
                $postRes = $wpdb->get_results($postQry);
                //echo'<pre>';print_r($postRes);die;
                if(count($postRes)){
                    $tableblock = "";
                    foreach($postRes as $key => $postData){
                        $bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
                        $itemID = $postData->ID;
			            $siteUrl = get_permalink($itemID);
                        $itemTitle = $postData->post_title;
                        $dateModified = date( 'M j, y h:i A', strtotime($postData->post_modified) );
                        $tableblock.= "<tr style='background-color:$bgColor'>
                        <td>$itemID</td>
                        <td><a href='$siteUrl' target='_blank'>$itemTitle</a></td>
                        <td>$dateModified</td>
                        <td style='text-align: center;'><input type=checkbox name=image_checked[] value='$itemID' class='case' checked='checked'></td>
                        </tr>"; 
                    }
                    $iniFormHTML = <<< FORM_HTML
                        <table style='margin-left: 20px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;'>
                            <p style='margin-left: 20px;'>[<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]</p>
                            <tr bgcolor='#C6D5FD'>
                                <th>Item ID</th>
                                <th style='padding-left: 60px;'>Item Title</th>
                                <th>Last Modified</th>
                                <th>Check All<input type=checkbox class='selectall' id='selectall' checked='checked'></th>
                            </tr>
                            $tableblock
                            <tr><td colspan='3' align='center'><input type='reset' name='Reset' /> | <input type='submit' name='update_time' value='submit' /></td></tr>
                        </table>
                        
FORM_HTML;
                }else{
                    echo"Record Not Found!!!";
                }
            }
        }elseif(isset($_REQUEST['update_time'])){
            //echo'<pre>';print_r($_REQUEST);die;
            if(array_key_exists('image_checked',$_REQUEST) && !empty($_REQUEST['image_checked'])){
                foreach($_REQUEST['image_checked'] as $key => $siteID){
                    $dbGMTNow = gmdate("Y-m-d H:i:s", time());
					$currentDBTime = Date("Y-m-d H:i:s");
                    $update = $wpdb->query("UPDATE `$tblPosts` SET `post_modified_gmt` = '{$dbGMTNow}', post_modified = '{$currentDBTime}'  WHERE ID = {$siteID}" );
                    if($update !== false){
                        $successArr[] = $siteID;
                    }
                }
            }
        }
?>
<html>
    <head>
        <title>FRC - Update Items Time</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>    
    <body>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Update Items Time</h3><br/>
        <?php
            if(count($successArr)){
					$message = "Following Items are updated successfully: ".implode(", ", $successArr);
					echo $messageHTML = <<<MSG_HTML
					<div class="table-responsive">
						<table class="table" style='width:80%'>
							<tr class='success'><td>$message</td></tr>
						</table>
					</div>
MSG_HTML;
				}
        ?>
        <form action='update-items-time.php' method='get'>
            <input type='text' name='search_item_title' value = '<?php if(isset($_REQUEST['search_item_title'])){ echo $_REQUEST['search_item_title']; } ?>' placeholder='Item Title' style='width:200px;margin-left: 25px;'/>
            <span style='padding-left:20px;'>
            <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
            </span><br/><br/>
            <?php if(!empty($iniFormHTML)){
                echo $iniFormHTML;
                echo '<div class="pagination" style="padding-left:20px;">';
                echo paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => ceil($total / $post_per_page),
                'current' => $page,
                'type' => 'plain'
                ));
                echo '</div>';
            }
?>
        </form> 
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