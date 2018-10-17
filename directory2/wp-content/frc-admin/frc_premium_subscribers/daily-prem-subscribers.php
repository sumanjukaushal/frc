<?php
    //URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/daily-prem-subscribers.php
    define('WP_USE_THEMES', false);
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    $date = date("Y-m-d");
    $totalUsers_query = "SELECT COUNT(*) FROM `frc_premium_users` WHERE DATE(`paid_date`)='$date'";
    $totalUsers = $wpdb->get_var($totalUsers_query);
    
    
    if($totalUsers >= 1){
        $totalUsersData_query = "SELECT * FROM `frc_premium_users` WHERE DATE(`paid_date`) = '$date'";
        $totalUsersData = $wpdb->get_results($totalUsersData_query);
        $tableBlock = <<<TBL_BLK
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
        <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
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
            </tr>
TBL_BLK;

        foreach($totalUsersData as $key => $reslt){
                $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
                $frc = 'FRC-';
                $billData = unserialize($reslt->billing_shipping);
                extract($billData);
                $phpDate = strtotime( $reslt->paid_date );
                $paidDate = date( 'M j, y h:i A', $phpDate );
                $tableBlock.="<tr style='background:{$bgColor}'>
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
        
        $tableBlock.='</table><br><br>';
        $body = "<span style='padding-left:20px;'>[</span><strong>Newly Registered Premium Users:</strong>: <span style='background-color: yellow;'> &nbsp;$totalUsers </span>]";
        $body.= $tableBlock;
        
    }else{
        $date = date("M j");
        $body = <<<TBL_BLK
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
        <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
            <tr><td>No Premium Users registered on $date</td></tr></table></div>
TBL_BLK;
    }
?>
<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Daily Premium Users</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
         <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
         <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
         <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
	</head>
	<body>
        <?php require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');?>
        <?php require("inner-navigation.php");?>
        <?php echo $body;
?>