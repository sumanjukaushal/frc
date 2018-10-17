<?php
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/export_users.php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $query = "SELECT * FROM `frc_premium_users` ORDER BY `id` DESC";
    $resultObjs = $wpdb->get_results($query);
    //a:23:{s:19:"_billing_first_name";s:6:"Graham";s:18:"_billing_last_name";s:6:"Ahrens";s:16:"_billing_company";s:0:"";s:18:"_billing_address_1";s:15:"42 silky oak dr";s:18:"_billing_address_2";s:0:"";s:13:"_billing_city";s:11:"Caves beach";s:14:"_billing_state";s:3:"NSW";s:17:"_billing_postcode";s:4:"2281";s:16:"_billing_country";s:2:"AU";s:14:"_billing_email";s:21:"gwarhrens@bigpond.com";s:14:"_billing_phone";s:10:"0408713543";s:20:"_shipping_first_name";s:0:"";s:19:"_shipping_last_name";s:0:"";s:17:"_shipping_company";s:0:"";s:19:"_shipping_address_1";s:0:"";s:19:"_shipping_address_2";s:0:"";s:14:"_shipping_city";s:0:"";s:15:"_shipping_state";s:0:"";s:18:"_shipping_postcode";s:0:"";s:17:"_shipping_country";s:0:"";s:19:"_order_shipping_tax";s:1:"0";s:23:"_shipping_address_index";s:8:"        ";s:16:"_shipping_method";s:0:"";}
    
    if(count($resultObjs) >= 1){
        header('Content-Type: text/csv; charset=utf-8');  
        header('Content-Disposition: attachment; filename=data.csv');
        $fp = fopen('php://output', 'w');
        $headers = array(
                         'User ID',
                         //'Login',
                         'First Name',
                         'Last Name',
                         'Email',
                         "Phone",
                         'Payment Date',
                         //'Amount'
                         "Company",
                         "Address1",
                         "Address2",
                         "City",
                         "State",
                         "Post Code",
                         "Country"
                         );
        fputcsv($fp, $headers);
        
        foreach($resultObjs as $resultObj){
            $billShipArr = unserialize($resultObj->billing_shipping);
            $phone = array_key_exists('_billing_phone', $billShipArr) ? $billShipArr['_billing_phone'] : "----";
            $firstName = array_key_exists('_billing_first_name', $billShipArr) ? $billShipArr['_billing_first_name'] : "----";
            $lastName = array_key_exists('_billing_last_name', $billShipArr) ? $billShipArr['_billing_last_name'] : "----";
            $company = array_key_exists('_billing_company', $billShipArr) ? $billShipArr['_billing_company'] : "----";
            $address1 = array_key_exists('_billing_address_1', $billShipArr) ? $billShipArr['_billing_address_1'] : "----";
            $address2 = array_key_exists('_billing_address_2', $billShipArr) ? $billShipArr['_billing_address_2'] : "----";
            $city = array_key_exists('_billing_city', $billShipArr) ? $billShipArr['_billing_city'] : "----";
            $state = array_key_exists('_billing_state', $billShipArr) ? $billShipArr['_billing_state'] : "----";
            $postCode = array_key_exists('_billing_postcode', $billShipArr) ? $billShipArr['_billing_postcode'] : "----";
            $country = array_key_exists('_billing_country', $billShipArr) ? $billShipArr['_billing_country'] : "----";
            
            $phpDate = strtotime( $resultObj->paid_date );
            $paidDate = date( 'M j, y h:i A', $phpDate );
            
            $data = array(
                          "FRC-".$resultObj->user_id,
                          //$resultObj->login,
                          $firstName,
                          $lastName,
                          $resultObj->email,
                          $phone,
                          $paidDate,
                          //$resultObj->order_total,
                          $company,
                          $address1,
                          $address2,
                          $city,
                          $state,
                          $postCode,
                          $country
                          );
            fputcsv($fp, $data);
        }
        fclose($fp);  
    }
?>