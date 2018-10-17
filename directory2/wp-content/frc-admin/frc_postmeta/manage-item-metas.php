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
            if(isset($_REQUEST['search_item_id']) && !empty($_REQUEST['search_item_id'])){
                $itemID = $_REQUEST['search_item_id'];
                $query = "SELECT * FROM `frc_postmeta_log` WHERE `post_id`='$itemID'";
                $results = $wpdb->get_results($query);//echo'<pre>';print_r($results);die;
                if(!empty($results)){
                    echo $tableBlock = '
                    <table style="margin-left: 25px;margin-top: 25px;margin-bottom: 50px;width: 800px;font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
                    <tr bgcolor="#C6D5FD">
                    <th style= "padding-top: 8px;padding-bottom: 8px;">Item ID</th>
                    <th>Item Title</th>
                    <th>Date Modified</th>
                    <th>Action</th>
                    </tr>';
                    
                    foreach($results as $key => $result){
                        $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
                        $ID = $result->id;
                        $ItemID = $result->post_id;
                        $itemTitle = get_the_title($ItemID);
                        $dateModified = date( 'M j, y h:i A', strtotime($result->modified) );
                        echo "<tr style='background:{$bgColor}'>
                        <td style='padding-top: 8px;padding-bottom: 8px;'>{$ItemID}</td>
                        <td>{$itemTitle}</td>
                        <td>{$dateModified}</td>
                        <td>
                        <a href=manage-item-metas-review.php?id=$ID>Review</a>
                        </td>
                        </tr>";
                    }
                }else{
                    echo'Data Not Found!!!';
                }
            }
           
        ?>
    </head>
    <body>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Restore Post Metas</h3><br/>
        <form action='manage-item-metas.php' method='get'>
            <!--<fieldset style='width:78%'>
                <legend>Search Items</legend>-->
                <input type='text' name='search_item_id' value = '<?php if(isset($_REQUEST['search_item_id'])){ echo $_REQUEST['search_item_id'];} ?>' placeholder='ITEM ID or ITEM TITLE' style='width:200px;margin-left: 25px;'/>
                <span style='padding-left:20px;'>
                <input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>
                </span>
            <!--</fieldset>-->
        </form>        
    </body>
</html>