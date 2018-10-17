<?php
   //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/export_filter.php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    if(isset($_POST['submit'])){
        //print_r($_REQUEST);die;
        $startDate = $endDate ="";
        extract($_POST);
        if(!empty($startDate) && !empty($endDate)){
            if($startDate == $endDate){
                $query = "SELECT * FROM `frc_premium_users` WHERE DATE(`paid_date`) = '$startDate'";
            }else{
                $endDate = date('Y-m-d',strtotime($endDate . "+1 days"));
                $query = "SELECT * FROM `frc_premium_users` WHERE `paid_date` >= '$startDate' and `paid_date` <= '$endDate'";
            }
            $resultObjs = $wpdb->get_results($query);
        }elseif(!empty($startDate) && empty($endDate)){
            $endDate = date("Y-m-d");
            $query = "SELECT * FROM `frc_premium_users` WHERE `paid_date` >= '$startDate' and `paid_date` <= '$endDate'";
            $resultObjs = $wpdb->get_results($query);
        }elseif(empty($startDate) && !empty($endDate)){
            echo 'Error : Please Enter Starting Date';
            $resultObjs = array();
        }else{
            $query = "SELECT * FROM `frc_premium_users` ORDER BY `id` DESC";
            $resultObjs = $wpdb->get_results($query);
        }
          
        if(count($resultObjs) >= 1){
            header('Content-Type: text/csv; charset=utf-8');  
            header('Content-Disposition: attachment; filename=data.csv');
            $fp = fopen('php://output', 'w');
            $headers = array('User ID', 'Login', 'Email', 'Payment Date', 'Amount','First Name','Last Name','Company','Address1','Address2','City','State','PostCode','Country','Email','Phone');
            fputcsv($fp, $headers);
            foreach($resultObjs as $resultObj){
                $billingData = unserialize($resultObj->billing_shipping);
                extract($billingData);
                $data = array(
                              $resultObj->user_id,
                              $resultObj->login,
                              $resultObj->email,
                              $resultObj->paid_date,
                              $resultObj->order_total,
                              $_billing_first_name,
                              $_billing_last_name,
                              $_billing_company,
                              $_billing_address_1,
                              $_billing_address_2,
                              $_billing_city,
                              $_billing_state,
                              $_billing_postcode,
                              $_billing_country,
                              $_billing_email,
                              $_billing_phone
                              );
                fputcsv($fp, $data);
             } 
             fclose($fp);die;  
        }
   }
?>
<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Export Premium Users</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
         <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
         <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
         <!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
	</head>
	<body>
        <?php require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');?>
        <script>
             $(function() {
                 $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
             });
             $(function() {
                 $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
             });
        </script>
        <?php require("inner-navigation.php");?>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Premium Users</h3>
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
             <form action="export_filter.php" method="post">
                <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
                <tr bgcolor="#C6D5FD">
					<th colspan="4" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Export Premium Users:</th>
				</tr>
                <tr style="line-height: 50px;">
					<td>Start Date: </td>
					<td style="line-height: 50px;"><input name='startDate' style = "width:150px;height: 35px;" type="date" id="datepicker" /></td>
                    <td style="line-height: 50px;">End Date:</td>
					<td><input name='endDate' style = "width:150px;height: 35px;" type="date" id = "datepicker1" /></td>
				</tr>
                <tr>
                    <td></td>
                    <td colspan='3' align='center'>
                        <input type="submit" name="submit" value="Download" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' >&nbsp;&nbsp;
                        <input type="reset" name="Reset" value="Reset" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' >
                    </td></tr>
                </table>
               </form>
        </div>
        <br/><br/><br/><br/><br/><br/>
     </body>
</html>
