<!DOCTYPE html>
<html class="ie" lang="en">
    <head>
        <title>FRC Mobile App offline DB Versions</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
         <?php 
            require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        ?>
        <script>
            $(function() {
                $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
            $(function() {
                $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
            });
         </script>
    </head>
    <body>
    <?php
        $post_per_page = 10;
        $tblName = "frc_offline_db_versions";
        $start_date = $end_date = $search_data = "";
        if(isset($_GET['search'])){
            $start_date = $_GET['start'];
            $end_date = $_GET['end'];
        }
            
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        
        if(isset($_GET['submit'])){
            $search_data = $_GET['search'];
        }
    ?>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Mobile App offline DB Versions</h3>
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
        <form action='offline_db_versions.php' method='get'>
            <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
                <tr bgcolor="#C6D5FD">
					<th colspan="3" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Search Published Items:</th>
				</tr>
                <tr>
                    <td><input type='text' name='search' value = '<?php echo $search_data;?>' placeholder='search' style='width:200px;'/></td>
                    <td><input type='text' autocomplete="off" name='start' value='<?php echo $start_date;?>' placeholder='Start Date' id='datepicker'/></td>
                    <td><input type='text' autocomplete="off" name='end' value='<?php echo $end_date;?>' placeholder='End Date' id='datepicker1'/></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2"><input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/>&nbsp;&nbsp;
                        <input type="reset" name="Reset" value="Reset" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' ></td>
                </tr>
            </table>
        </form>
        </div>
<?php
    //URL:https://www.freerangecamping.co.nz/directory/wp-content/frc_offline_db_versions/offline_db_versions.php
    
    //Search data display...
    if(isset($_GET['submit'])){
        
        $search_data = $_GET['search'];
        $start_date = $_GET['start'];
        $end_date = $_GET['end'];
        //echo $search_data.$start_date.$end_date; die;
        if(!empty($search_data) && !empty($start_date) && !empty($end_date)){
            $cntQry = "SELECT COUNT(*) FROM `$tblName` WHERE `description` LIKE '%$search_data%'";
            if($start_date == $end_date){   
                $query = "$cntQry AND `created` >='$start_date'";   
            }else{
                $query = "$cntQry AND `created` >='$start_date' AND `created` <= '$end_date'";
                //echo $query; die;
            }
        }elseif(empty($search_data) && !empty($end_date) && !empty($start_date)){
            $dtQry = "SELECT COUNT(*) FROM `$tblName` WHERE `created` >= '$start_date'";
            if($start_date == $end_date){
                $query = $dtQry;
            }else{
                $query = "$dtQry AND `created` <= '$end_date'";
            }
        }elseif(!empty($search_data) && empty($end_date) && !empty($start_date)){
            $query = "SELECT COUNT(*) FROM `$tblName` WHERE `description` LIKE '%$search_data%' AND `created` >= '$start_date'";
        }elseif(!empty($search_data) && !empty($end_date) && empty($start_date)){
            $query = "SELECT COUNT(*) FROM `$tblName` WHERE `description` LIKE '%$search_data%' AND `created` >= '$end_date'";
        }elseif(!empty($search_data) && empty($end_date) && empty($start_date)){
             $query = "SELECT COUNT(*) FROM `$tblName` WHERE `description` LIKE '%$search_data%'";
            }/*elseif(empty($search_data) && !empty($end_date) && empty($start_date)){
            $query = "SELECT COUNT(*) FROM `$tblName` WHERE `created` = '$end_date'";
        }*//*elseif(empty($search_data) || (!empty($end_date) && empty($start_date))){
            $query = "SELECT COUNT(*) FROM `$tblName` WHERE `created` = '$end_date'";
        }*/
        
        if(!empty($search_data) && !empty($start_date) && !empty($end_date)){
            $likeQry = "SELECT * FROM `$tblName` WHERE `description` LIKE '%$search_data%'";
            if($start_date == $end_date){
                $qry = "$likeQry AND `created` >= '$start_date' ";  
            }else{
                $qry = "$likeQry AND `created` >= '$start_date' AND `created` <= '$end_date'";
                //echo $qry; die;
            }
        }elseif(empty($search_data) && !empty($end_date) && !empty($start_date)){
            if($start_date == $end_date){
                $qry = "SELECT * FROM `$tblName` WHERE `created` >='$start_date'";
            }else{
                $qry = "SELECT * FROM `$tblName` WHERE `created` >= '$start_date' AND `created` <= '$end_date'";
                
            }
        }elseif(!empty($search_data) && !empty($end_date) && empty($start_date)){
            $qry = $qry = "SELECT * FROM `$tblName` WHERE `description` LIKE '%$search_data%' AND `created` >= '$end_date'";
        }elseif(!empty($search_data) && empty($end_date) && !empty($start_date)){
            $qry = "SELECT * FROM `$tblName` WHERE `description` LIKE '%$search_data%' AND `created` >= '$start_date'";
        }elseif(!empty($search_data) && empty($end_date) && empty($start_date)){
            $qry = "SELECT * FROM `$tblName` WHERE `description` LIKE '%$search_data%'";
        }
        
        /*elseif(empty($search_data) && !empty($end_date) && empty($start_date)){
            $qry = "SELECT * FROM `$tblName` WHERE `created` ='$end_date'";
        }elseif(empty($search_data) || (!empty($end_date) && empty($start_date))){
            $qry = "SELECT * FROM `$tblName` WHERE `created` = '$end_date'";
        }  */
    }
    if(!empty($qry) && !empty($query)){
       //echo $query; die;
        $total = $wpdb->get_var($query);
        $rsltObjs = $wpdb->get_results("{$qry} ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
    }else{
        //echo "ami";die;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM frc_offline_db_versions");
        $qry = "SELECT * FROM `$tblName` ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}";
        $rsltObjs = $wpdb->get_results($qry );
    }
    
    if(!empty($rsltObjs)){
        echo "<span style='font-size:200%; color:grey; text-align:center; padding-left:20px;'><u>Offline DB Versions</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]";
        $paginationStr = '<div class="pagination"  style="text-align:right;font-size: 150%;padding-left:20px;">';
        $paginationStr.= paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total / $post_per_page),
            'current' => $page,
            'type' => 'plain'
        ));
        $paginationStr.='</div>';
        echo $tableBlock =<<<TBL_BLK
            <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
            <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
                <tr><td colspan='3' style='right'>$paginationStr</td></tr>
                <tr bgcolor="#C6D5FD" style='font-size: 120%;'>
                    <td><strong>Versions</strong></td>
                    <td><strong>Published Data</strong></td>
                    <td><strong>Date</strong></td>
                </tr>
TBL_BLK;
        
        foreach($rsltObjs as $key => $value){
            $bgColor = $key % 2 == 0 ? '#FFFFCC': '#D0D0D0';
            $date = $value->created;
            $time = date('d-m-Y g:i a', strtotime($date));
            
            echo $tableRow =<<<TBL_ROW
            <tr style='background-color:$bgColor'>
                <td valign='top'>{$value->version}</td>
                <td valign='top'>{$value->description}</td>
                <td valign='top'>{$time}</td>
            </tr>
TBL_ROW;
        }
        echo "<tr><td colspan='3' style='right'>$paginationStr</td></tr>";
        echo "</table></div>";
    }else{
        echo "Data not Found!!";
    }
?>
    </body>
</html>