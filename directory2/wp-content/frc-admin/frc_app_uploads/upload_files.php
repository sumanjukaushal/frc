<?php
    //URL:https://www.freerangecamping.co.nz/directory/wp-content/frc_app_uploads/upload_files.php
    require_once("../../../wp-config.php");
    require_once("../../../wp-load.php");
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    //$file_path = "/home/frcconz/public_html/directory/wp-content/mobile_uploads";
    $file_path = WP_CONTENT_DIR.DS."frc-admin/frc-admin/mobile_uploads";
    $files = scandir( $file_path );
    $total_files = array_slice($files,2); //1. ., 2. .., 3. ftc_quota
    //echo "<pre>"; print_r($total_files);die;
    foreach($total_files as $total_file => $file_name){
        $absFileName = $file_path.DS.$file_name;
        $mimeType = mime_content_type ($absFileName );
        $myfile = fopen($absFileName,"r"); // opening file in read mode.
        $size_of_file = filesize($absFileName);  // Size of file..
        $file_content = "";
        if("text/plain" == $mimeType || "image/gif" == $mimeType){
            $file_content = fread($myfile,$size_of_file);
        }
        //$user_id = substr($file_name,0,5); //10233_FRC_28151_1493689668445.txt
        //$post_id = substr($file_name,10,5);
        //$file_create_time = substr($file_name,16,13);
        
        if(substr($file_name,9,1) == '-'){
            $file_details = explode("-",$file_name);
            foreach($file_details as $$file_detail => $val){
                $post_ids = explode("_",$file_details[0]);
                foreach($post_ids as $post_ds => $pst_ids){
                    $post_id = $post_ids[0];
                }
                $user_time = explode("_",$file_details[1]);
                $user_id = $user_time[0];
                 $file_create_time1 = $user_time[1];
                $file_create_time = substr($file_create_time1,0,-4);
            } 
        } 
        //echo $post_id."<br>";
        //echo $user_id."<br>";
        //echo $file_create_time."<br>";
        
        $file_diff_details = explode("_",$file_name);
        
        foreach($file_diff_details as $key => $value){
            $post_id = $file_diff_details[0];
            $user_id = $file_diff_details[2];
            $file_create_time = $file_diff_details[3];
        }
        //$file_creation_time = date ('Y-m-d h:i:s',@filemtime(utf8_decode($file_name)));
         $file_creation_time = date ('Y-m-d h:i:s',strtotime($file_name));
        $table_name = $wpdb->prefix."users_files";
        $query = $wpdb->query("INSERT INTO `frc_app_uploads` (`file_name`,`content`,`post_id`,`user_id`,`created`) VALUES ('$file_name', '$file_content','$post_id','$user_id','$file_creation_time')" );
    }
    //echo "<pre>"; print_r($file_create_time);
?>

