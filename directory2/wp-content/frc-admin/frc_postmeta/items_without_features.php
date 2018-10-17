<html>
    <head>
        <title>FRC: Items (without features)</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <body>
        
    <?php
        require_once("../../../wp-config.php");
        require_once('../../../wp-load.php');
        global $wpdb;
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        include("feature.php");
        $isOldTheme = is_old_theme();
        $postTble = $wpdb->prefix.'posts';
        $postMetaTbl = $wpdb->prefix.'postmeta';
        $metaKey = ($isOldTheme) ? "_ait-dir-item" : "_ait-item_item-extension";
        $siteUrl = site_url();
        
        $itemsQry = "SELECT * FROM `$postTble` WHERE `post_status` IN('publish','draft','pending') ORDER BY ID DESC";
        $itemsRes = $wpdb->get_results($itemsQry);//echo'<pre>';print_r($itemsRes);die;
        if(count($itemsRes)){
            $tabledata = array();
            if($isOldTheme){
                foreach($itemsRes as $key => $itemsData){
                    $postID = $itemsData->ID;
                    $postTitle  = $itemsData->post_title;
                    $postMetaQry = "SELECT * FROM `$postMetaTbl` WHERE `post_id`= $postID AND `meta_key`='$metaKey'";
                    $postmetaRes = $wpdb->get_results($postMetaQry);
                    if(count($postmetaRes)){
                        foreach($postmetaRes as $keys => $postmetas){
                            $metaValue = unserialize($postmetas->meta_value);
                            $isWithoutFeatureArray = contains_feature_old($metaValue);
                            if($isWithoutFeatureArray == false){
                                $tabledata[$postID] = $postTitle;
                            }
                        }
                    }
                }
            }else{
                foreach($itemsRes as $key => $itemsData){
                    $postID = $itemsData->ID;
                    $postTitle  = $itemsData->post_title;
                    $postMetaQry = "SELECT * FROM `$postMetaTbl` WHERE `post_id`= $postID AND `meta_key`='$metaKey'";
                    $postmetaRes = $wpdb->get_results($postMetaQry);
                    if(count($postmetaRes)){
                        //echo'<pre>';print_r($postmetaRes);die;
                        foreach($postmetaRes as $keys => $postmetas){
                            $metaValue = unserialize($postmetas->meta_value);
                            //echo'<pre>';print_r($metaValue);die;contains_feature_new
                            $isWithoutFeatureArray = contains_feature_new($metaValue);
                            if($isWithoutFeatureArray == false){
                                $tabledata[$postID] = $postTitle;
                            }
                        }
                    }
                }
            }
            $per_page = 25;
            $total_rows = count($tabledata);
            $pages = ceil($total_rows / $per_page);
            $current_page = isset($_GET['cpage']) ? $_GET['cpage'] : 1;
            $start = $current_page * $per_page - $per_page;
            $slicedArray = array_slice($tabledata, $start, $per_page, true);
            $tableBlock = "";
            $counts = 0;
            foreach ($slicedArray as $postId => $postTitle) {
                $itemEditUrl = "{$siteUrl}/wp-admin/post.php?post=$postId&action=edit";
                $counts++;
                $bgColor = $counts % 2 == 0 ? '#CCC' : '#FFF';
                $itemLink = get_permalink($postId);
                $tableBlock.= "<tr style='background:{$bgColor}'>
                                    <td style='padding-top: 8px;padding-bottom: 8px;'>{$postId}</td>
                                    <td><a href='$itemLink' target='_blank'>{$postTitle}</a></td>
                                    <td><a href='$itemLink' target='_blank'>View</a> | <a href='$itemEditUrl' target='_blank'>Edit</a></td>
                                </tr>";
                             
            }
            
            echo $formHTML=<<<FORM_HTML
                    <h3 style='padding-left:20px;'>&raquo;&nbsp;Items (without features)</h3>
                    <h5 style='padding-left:25px;'><b>No. of Items : </b><b style="background-color: yellow;">{$total_rows}</b></h5>
                    <table style="margin-left: 25px;margin-top: 20px;margin-bottom: 50px;width: 800px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
                        <tr bgcolor="#C6D5FD">
                            <th style= "padding-top: 8px;padding-bottom: 8px;">Item ID</th>
                            <th>Item Title</th>
                            <th>Action</th>
                        </tr>
                        $tableBlock
                    </table>
FORM_HTML;
        
        if ($pages > 0) {
            echo '<div class="pagination"  style="padding-left:20px;font-size: 20px;">';
                    echo paginate_links( array(
                    'base' => add_query_arg( 'cpage', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($total_rows / $per_page),
                    'current' => $current_page,
                    'type' => 'plain'
                    ));
                    echo '</div>';
        }
        }
        function contains_feature_old($metaArray){
            include("feature.php");
            foreach($metaArray as $featureKey => $featureValue){
                if(in_array($featureKey,$features)) {
                  $result =  true;
                  return $result;
                }
            }
            $result = false;
            return $result;
        }
        
        function contains_feature_new($metaArray){
            //include("feature.php");
            foreach($metaArray['onoff'] as $featureKey => $featureValue){
                if($featureValue == 'on') {
                  $result =  true;
                  return $result;
                }
            }
            $result = false;
            return $result;
        }
    ?>   
    </body>
</html>