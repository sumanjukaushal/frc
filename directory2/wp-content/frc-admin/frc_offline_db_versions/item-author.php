<?php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    global $wpdb;
    $authArr = frc_author_details(33957);
    echo "<pre>"; print_r($authArr); 
    $authArr = frc_author_details(32970);
    echo "<pre>"; print_r($authArr); 
    $authArr = frc_author_details(33937);
    echo "<pre>"; print_r($authArr); 
    $authArr = frc_author_details(27948);
    echo "<pre>"; print_r($authArr);
    
    function frc_author_details($post_ID){
        $user_id = get_post_meta( $post_ID, '_edit_last', TRUE );
        $userInfoObj = get_userdata( $user_id );
        $userEmail = $nickname = $fName = $lName = "";
        $fName = $userInfoObj->first_name;
        $lName = $userInfoObj->last_name;
        $userEmail = $userInfoObj->user_email;
        $nickname = $userInfoObj->nickname;
        $fullName = implode(" ", array($fName, $lName));
        return array($user_id, $userEmail, $fullName, $nickname);//$auth,
    }   
?>