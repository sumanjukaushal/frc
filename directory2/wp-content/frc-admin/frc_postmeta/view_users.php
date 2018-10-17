<?php
    /*Records to table 'frc_premium_users' is getting added from here: 
        /home/freerang/public_html/directory/wp-content/frc_premium_subscribers/subscriber.php
        And updating admin by this script 
        https://www.freerangecamping.com.au/directory/wp-content/frc_premium_subscribers/daily-prem-subscribers.php
        which is running by scheduler after 4 hours
    */
    //Calling URL: https://www.freerangecamping.com.au/directory/wp-content/frc_premium_subscribers/view_users.php
     
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
    require("inner-navigation.php");
    ?>
    
    <fieldset>
        <legend style="background-color:#C6D5FD">Search</legend>
        <form action="view_users.php" method="get">
            Search:<input type="text" style="width:300px" name="searched_data" id="searched_data" placeholder="first name,last name,email,address,city,post code">
            <input type="submit" name="submit" value="Search" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        
        </form>
    </fieldset>
    
    <?php
    if(array_key_exists('searched_data',$_REQUEST) && !empty($_REQUEST['searched_data'])){
        //print_r($_REQUEST);die;
        $searchData = $_REQUEST['searched_data'];
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` WHERE `billing_shipping` like '%$searchData%' ORDER BY `id` DESC");
        $post_per_page = 25;
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        $search_qry ="SELECT * FROM `frc_premium_users` WHERE `billing_shipping` like '%$searchData%'";
        $rsltObjs = $wpdb->get_results( $search_qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
        
    }else{
        //print_r($_REQUEST);die;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` ORDER BY `id` DESC");
        $post_per_page = 25;
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        $qry = "SELECT * FROM `frc_premium_users`";
        $rsltObjs = $wpdb->get_results( $qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
    }
    echo "<span style='font-size:200%; color:grey;'><u>Premium Users</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]";
    echo $tableBlock = '
    <table style="font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
        <tr bgcolor="#C6D5FD">
            <th>User ID</th>
            <th>FName</th>
            <th>LName</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Pmt Date</th>
            <!--th>Amount</th-->
            <th>Company</th>
            <th>Address1</th>
            <th>Address2</th>
            <th>City</th>
            <th>State</th>
            <th>PostCode</th>
        </tr>';
        foreach($rsltObjs as $key => $reslt){
            $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
            $frc = 'FRC-';
            $billData = unserialize($reslt->billing_shipping);
            extract($billData);
            $phpDate = strtotime( $reslt->paid_date );
            $paidDate = date( 'M j, y h:i A', $phpDate );
            echo "<tr style='background:{$bgColor}'>
                        <td nowrap='nowrap'>FRC-{$reslt->user_id}</td>
                        <td>{$_billing_first_name}</td>
                        <td>{$_billing_last_name}</td>
                        <td>$reslt->email</td>
                        <td>{$_billing_phone}</td>
                        <td nowrap='nowrap'>$paidDate</td>
                        <!--td>$reslt->order_total</td-->
                        <td>{$_billing_company}</td>
                        <td>{$_billing_address_1}</td>
                        <td>{$_billing_address_2}</td>
                        <td>{$_billing_city}</td>
                        <td>{$_billing_state}</td>
                        <td>{$_billing_postcode}</td>
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