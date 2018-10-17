<?php
    //Script URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_app_uploads/scan_uploads.php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    
    $file_path = WP_CONTENT_DIR.DS."frc-admin/mobile_uploads";
    $files = scandir( $file_path );
    $total_files = array_slice($files,3); //1. ., 2. .., 3. ftc_quota
    $counter = 0;
    foreach($total_files as $total_file => $file_name){
        $absFileName = $file_path.DS.$file_name;
        $mimeType = mime_content_type ($absFileName );
        $myfile = fopen($absFileName,"r"); // opening file in read mode.
        $size_of_file = filesize($absFileName);  // Size of file..
        $file_content = ""; if("text/plain" == $mimeType){$file_content = fread($myfile,$size_of_file);}
        $splitArr = explode("_", $file_name);
        $postID = $splitArr[0];
        $temp = $splitArr[1];
        $temp1 = $splitArr[2];
        //list($postID, $temp, $temp1) = split("_",$file_name);
        if($temp == 'FRC'){
            $userID = $temp1;
        }else{
            //list($t, $userID) = split("-",$temp);
            $expArr = explode("-",$temp);
            $t = $expArr[0];
            $userID = $expArr[1];
        }
		$created = date ('Y-m-d h:i:s',@filemtime(utf8_decode($absFileName)));
		$dataArr = array( 'post_id' => $postID, 'user_id' => $userID, 'file_name' => $file_name, 'content' => $file_content, 'created' => $created);
		$dataTypeArr = array( '%d', '%d', '%s', '%s','%s' );
		$query = "SELECT * FROM  `frc_app_uploads` WHERE `post_id` = $postID AND  `user_id` =  '$userID' AND `file_name` = '$file_name'";
		$uploadResult = $wpdb->get_results($query, OBJECT);
		
		if(count($uploadResult) >= 1){
		    ;//do nothing
		}else{
		    $status = $wpdb->insert( 'frc_app_uploads', $dataArr, $dataTypeArr);
		    $counter++;
		}
    }
	$redirectUrl = WP_CONTENT_URL.DS."frc-admin/frc_app_uploads/app_uploads.php";
    echo "<script>alert('$counter files are added to the database');window.location.href = '$redirectUrl';</script>";