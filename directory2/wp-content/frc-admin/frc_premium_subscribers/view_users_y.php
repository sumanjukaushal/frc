<!doctype html>
<html lang="en">
<head>
<?php
    /*Records to table 'frc_premium_users' is getting added from here: 
        /home/frcconz/public_html/directory/wp-content/frc_premium_subscribers/subscriber.php
        And updating admin by this script 
        https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/daily-prem-subscribers.php
        which is running by scheduler after 4 hours
    */
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/view_users.php
     
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
    require("inner-navigation.php");
    ?>
    
    <link rel="stylesheet" href="js/colorbox-master/example4/colorbox.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="js/colorbox-master/jquery.colorbox.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    
</head>
<body>
    <fieldset>
        <legend style="background-color:#C6D5FD">Search</legend>
        <form action="view_users.php" method="get">
            Search:<input type="text" style="width:300px" name="searched_data" id="searched_data" placeholder="first name,last name,email,address,city,post code">
            User ID:<input type="text" name="user_id" id="user_id" placeholder="user_id" style="width:100px">
            Date:<input type="text" name="start_date" id="start_date" placeholder="start_date" style="width:100px" >
            <input type="text" name="end_date" id="end_date" placeholder="end_date" style="width:100px" > 
            Pmt Counts:
            <select name = 'pmt_counts'>
                <option value=''>All</option>
                <option value='1'>1</option>
                <option value='2'>2</option>
                <option value='3'>3</option>
                <option value='4'>4</option>
                <option value='gt4'>Greater than 4</option>
            </select>
            <input type="submit" name="submit" value="Search" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </form>
    </fieldset>
    
    <?php
    $post_per_page = 50;
    $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
    $offset = ( $page * $post_per_page ) - $post_per_page;
    if(
       array_key_exists('searched_data',$_REQUEST) && !empty($_REQUEST['searched_data']) ||
       (array_key_exists('pmt_counts',$_REQUEST) && !empty($_REQUEST['pmt_counts'])) ||
       (array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])) ||
       (array_key_exists('start_date',$_REQUEST) && !empty($_REQUEST['start_date'])) ||
       (array_key_exists('end_date',$_REQUEST) && !empty($_REQUEST['end_date']))
    ){
        $searchData = $_REQUEST['searched_data'];
        $pmtCounts = $_REQUEST['pmt_counts'];
        $userId = $_REQUEST['user_id'];
        $start_date = $_REQUEST['start_date'];
        $end_date = $_REQUEST['end_date'];
        if(!empty($searchData) && !empty($pmtCounts) && !empty($userId)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $pmtCondStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
            $condStr = "`billing_shipping` like '%$searchData%' AND $pmtCondStr AND `user_id`=$userId";
        }elseif(!empty($pmtCounts) && empty($searchData) && empty($userId)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $condStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
        }elseif(!empty($userId) && empty($searchData) && empty($pmtCounts)){
            $condStr = "`user_id`=$userId";
        }elseif(!empty($searchData) && empty($userId) && empty($pmtCounts)){
            $condStr = "`billing_shipping` like '%$searchData%'";
        }elseif(!empty($searchData) && !empty($pmtCounts) && empty($userId)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $pmtCondStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
            $condStr = "`billing_shipping` like '%$searchData%' AND $pmtCondStr";
        }elseif(empty($searchData) && !empty($pmtCounts) && !empty($userId)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $condStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
            $condStr .= "AND `user_id`=$userId";
        }elseif(!empty($searchData) && empty($pmtCounts) && !empty($userId)){
            $condStr = "`billing_shipping` like '%$searchData%' AND `user_id`=$userId";
        }
          
        if(!empty($start_date) && empty($end_date)){
            $end_date = $start_date;
        }elseif(empty($start_date) && !empty($end_date)){
           $start_date =  $end_date;
        }
        $endDate = date('Y-m-d',strtotime($end_date . "+1 days"));
        if(!empty($condStr)){
            $condStr .= " AND `paid_date` >= '$start_date' and `paid_date` <= '$endDate'";
        }else{
            $condStr .= "`paid_date` >= '$start_date' and `paid_date` <= '$endDate'";
        }
            
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` WHERE $condStr ORDER BY `id` DESC");
        $search_qry ="SELECT * FROM `frc_premium_users` WHERE $condStr";//echo $search_qry;die;
        $rsltObjs = $wpdb->get_results( $search_qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
        
    }else{
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` ORDER BY `id` DESC");
        $qry = "SELECT * FROM `frc_premium_users`";
        $rsltObjs = $wpdb->get_results( $qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
    }
    
    echo "<span style='font-size:200%; color:grey;'><u>Premium Users</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]";
    echo $tableBlock = '
    <table style="font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
        <tr bgcolor="#C6D5FD">
            <td><b>User ID</b></td>
            <td><b>FName</b></td>
            <td><b>LName</b></td>
            <td><b>Email</b></td>
            <td><b>Phone</b></td>
            <td><b>Pmt Counts</b></td>
            <td><b>Pmt Date</b></td>
            <!--th>Amount</th-->
            <td><b>Company</b></td>
            <td><b>Address1</b></td>
            <td><b>Address2</b></td>
            <td><b>City</b></td>
            <td><b>State</b></td>
            <td><b>PostCode</b></td>
        </tr>';
        foreach($rsltObjs as $key => $reslt){
            $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
            $frc = 'FRC-';
            $billData = unserialize($reslt->billing_shipping);
            $pmtCount =  $reslt->pmt_counts;
            extract($billData);
            $phpDate = strtotime( $reslt->paid_date );
            $paidDate = date( 'M j, y h:i A', $phpDate );
            $pmtDetails = unserialize($reslt->pmt_details);
            $titleStr = $rowStr = "";
            if(count($pmtDetails) >= 1){
                $sum = 0;
                $counter = 0;
                foreach($pmtDetails as $pmtDetail){
                    $counter++;
                    //print_r($pmtDetail);die;
                    $postID = $pmtDetail['post_id'];
                    $amount =  $pmtDetail['amount'];
                    $pmtDate =  $pmtDetail['pmt_date'];
                    $sum+=$amount;
                    $rowStr.="<tr><td>$counter</td><td>$postID</td><td>$pmtDate</td><td>$amount</td></tr>";
                }
                $summary = "<tr><td><b>Summary</b></td><td><b>Pmt Count:$counter</b></td><td><b>Total</b></td><td>$sum</td></tr>";
                $titleStr.='<div style="overflow-x:scroll;font-size:10px;">
                                <table>'.$summary.'<tr><td colspan="4"><hr /></td><tr><td><b>Sr.No</b></td><td><b>Post ID</b></td><td><b>Pmt Date</b></td><td><b>Amount</b></td></tr>'.$rowStr.'<tr><td colspan="4"><hr /></td>'.$summary.'</table></div>';
            }
            echo "<tr style='background:{$bgColor}'>
                        <form id=form_$reslt->user_id method=post action=test.php target=_blank>
                        <input type=hidden value=$reslt->user_id id=$reslt->user_id name=user_id>
                        <input type=hidden value=$reslt->email id=$reslt->email name=email>
                        <td nowrap='nowrap' title='$titleStr' >FRC-{$reslt->user_id}</td>
                        <td>{$_billing_first_name}</td>
                        <td>{$_billing_last_name}</td>
                        <td><a href=# id=email_id rel= $reslt->user_id >$reslt->email</a></td>
                        <td>{$_billing_phone}</td>
                        <td>{$pmtCount}</td>
                        <td nowrap='nowrap'><a href=#inline_content id=date class='inline' rel='$reslt->user_id'>$paidDate</a></td>
                        <!--td>$reslt->order_total</td-->
                        <td>{$_billing_company}</td>
                        <td>{$_billing_address_1}</td>
                        <td>{$_billing_address_2}</td>
                        <td>{$_billing_city}</td>
                        <td>{$_billing_state}</td>
                        <td>{$_billing_postcode}</td>
                        </form>
                    </tr>";
        }
    echo'</table><br><br>';
    
    echo '<div class="pagination">';
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
?>
<script>
    //Start:block of showing colobox of roles and levels by curl
    $(document).ready(function(){
        $(document).on('click', '.inline', function() {
            $.blockUI({ message: 'Loading ...' });
            var user_id = $(this).attr('rel');
            targeturl = "test_curl.php?user_id=1";//+user_id;
            $.ajax({
                url: targeturl,
                type: 'get',
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                },
                success: function(response) {
                    $.unblockUI();
                    var res = response;
                    var roles = res['roles'];
                    var levels = res['levels'];
                    var class_roles = res['class_roles'];
                    var class_levels = res['class_levels'];
                    $( "#roles" ).empty();
                    $( "#levels" ).empty();
                    $( "#class_role" ).empty();
                    $( "#class_level" ).empty();
                    
                    roles.forEach(function(role_element) {
                        $('#roles').append("<input type=checkbox name=role_"+role_element+">"+role_element);
                    });
                    
                    levels.forEach(function(level_element) {
                        $('#levels').append("<input type=checkbox name=level_"+level_element+">"+level_element);
                    });
                    
                    class_roles.forEach(function(class_role_element) {
                        $('#class_role').append("<input type=checkbox name=class_role_"+class_role_element+">"+class_role_element);
                    });
                    
                    class_levels.forEach(function(class_level_element) {
                        $('#class_level').append("<input type=checkbox name=class_level_"+class_level_element+">"+class_level_element);
                    });
                    
                    $(".inline").colorbox({ open: true, inline:true, width:"50%"});
                }
            });
        });
        
    //End:block of showing colorbox of roles and levels by curl
        $( document ).tooltip({
            content: function() {
                return $(this).attr('title');
            }
        });
       
        $( function() {
            $( "#start_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
            $( "#end_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
        } );
        
        $(document).on('click', '#email_id', function() {
            var user_id = $(this).attr('rel');
            var form_id = 'form_'+user_id;
            document.getElementById(form_id).submit();
        });
    });
    </script>

<!--Start:block of colobox---->
    <div style='display:none'>
        <div id='inline_content' style='padding:10px; background:#fff;'>
        <p style="color:blue;"><strong>Roles For Premium Installation: </strong></p>
        <p id='roles'> <?php //echo $role; ?></p>
        <p style="color:blue;"><strong>Levels For Premium Installation: </strong></p>
        <p id='levels'><br /></p>
        <p style="color:blue;"><strong>Roles For Classified Installation: </strong></p>
        <p id='class_role'><br /></p>
        <p style="color:blue;"><strong>Levels For Classified Installation: </strong></p>
        <p id='class_level'><br /></p>
        <p><a id="click" href="#" style='padding:5px; background:#ccc;'>Submit</a></p>
        </div>
    </div>
<!--End:block of colobox---->
</body>