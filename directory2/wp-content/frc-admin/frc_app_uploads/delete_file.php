<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $id = "";
    if(array_key_exists('id',$_REQUEST)){
        $name_of_file_nd_id = $_REQUEST['id'];
        $file_id_index = explode('&file=', $name_of_file_nd_id);
        $id = $file_id_index[0];
        $file_name = $file_id_index[1];
        $table_name = "frc_app_uploads";
        $wpdb->query("DELETE  FROM $table_name WHERE `id` = '$id' ");
    }
    if(array_key_exists('file', $_REQUEST)){
        $file_name = $_REQUEST['file'];
        $absFileName = WP_CONTENT_DIR.DS."mobile_uploads".DS.$file_name;
        $msg = array();
        if (file_exists($absFileName)) {
            $msg = array('message' => "Your file is deleted now", 'id' => $id);
            unlink($absFileName);
            $msg['message'] = "file deleted successfully!";
            $msg['id'] = $id;
            $msg['success'] = 1;
        }else{
            $msg['id'] = $id;
            $msg['success'] = 0;
            $msg['message'] = "!!! File $file_name do not exist !!!";    
        }   
    }
    echo json_encode($msg);
?>