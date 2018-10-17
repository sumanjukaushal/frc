<?php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    require_once('../../wp-settings.php');
    require_once("../../wp-includes/registration.php");
	require_once('../../wp-blog-header.php');
    ini_set('memory_limit','2048M');
    set_time_limit(0);
    ini_set('max_execution_time', 0);
    $query = "SELECT `ID` FROM  `gwr_users` order by `ID` asc";
    $rs = mysql_query($query);
    $counter = 0; 
   
    
    while($rowObj = mysql_fetch_object($rs)){
        $userID = $rowObj->ID;
        $user = new WP_User($userID);
        $numberOfRoles = count($user->roles);
        //echo "<pre>user: $userID";print_r($user->roles);echo "</pre>";
        if($numberOfRoles > 0){
            //save only if roles > 0
            if($numberOfRoles == 1 && $user->roles[0] == 'subscriber'){
                //if user have only one role and if it is subscriber do not save anything
                continue;
            }else{
                $userRoles = serialize($user->roles);
                $query = "SELECT `id` FROM `user_roles` WHERE `user_id` = $userID";
                $checkIfExistRS = mysql_query($query);
                if($onlySubscriber == 0 && $numberOfRoles > 0){
                    if(mysql_numrows($checkIfExistRS) > 0){
                        $userObj = mysql_fetch_object($checkIfExistRS);
                        $recID = $userObj->id;
                        $insertORupdateQry = "UPDATE `user_roles` SET `user_id` = $userID, `roles` = '$userRoles', `no_of_roles` = $numberOfRoles, `subscriber` = 0 WHERE `id` = $recID";
                    }else{
                        $insertORupdateQry = "INSERT INTO `user_roles` SET `user_id` = $userID, `roles` = '$userRoles', `no_of_roles` = $numberOfRoles, `subscriber` = 0";
                    }
                    echo "<br/>$insertORupdateQry";
                }
                
                mysql_query($insertORupdateQry);
            }
        }
    }
    die("Executed");
?>