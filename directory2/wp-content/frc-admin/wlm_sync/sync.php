 <?php 
   require_once("../../wp-config.php");
   require_once('../../wp-load.php');
   global $wpdb;
   $id = 1406427506;
   //foundation members=1496600500(1070)
   //Premium Members=1406427506(1240)
   $premWLMID = 1406427506;
   $founWLMID = 1496600500;
   $query = "SELECT * FROM `wp_wlm_userlevels` WHERE `level_id` IN (%d, %d) ORDER BY `ID` ASC LIMIT 2325, 500";
   $res = $wpdb->get_results($wpdb->prepare($query, $premWLMID, $founWLMID));
   echo "<br/><span style='color:grey;'>Zero Qry:".$wpdb->last_query."</span><br/>";
   $table_arr = array('gwr', 'shop','classified');
   $counter = 0;
   
   foreach($res as $key => $value){
      $counter++;
      if($counter == 500) break;
      $userLvlID = $value->ID; 
      $user_id = $value->user_id;
      $level_id = $value->level_id;
        
      // get data from "wp_wlm_userlevel_options" table
      $wp_option_query = "SELECT * FROM `wp_wlm_userlevel_options` WHERE `userlevel_id` = %d";
      $wp_option_res = $wpdb->get_results($wpdb->prepare($wp_option_query, $userLvlID));
      echo "<br/><span style='color:blue;'>First Qry ($key)".$lastQry0 = $wpdb->last_query."</span><br/>";
      if(empty($wp_option_res))continue;
      foreach($table_arr as $table_key => $tblSuffix){
         $user_level_table_name = "{$tblSuffix}_wlm_userlevels";
         $userLvlOptTbl = "{$tblSuffix}_wlm_userlevel_options";
         
         // check if "gwr_wlm_userlevels" have record
         $gwr_query = "SELECT * FROM `$user_level_table_name` WHERE `user_id` = %d AND `level_id` = %d";
         $gwr_res = $wpdb->get_results($wpdb->prepare($gwr_query, $user_id, $level_id));
         echo "<br/>Qry 2:".$lastQry = $wpdb->last_query;
         
         if(count($gwr_res) >= 1){
            //Ideally record must be there and count should be 1; updating in this block
            $resObj = $gwr_res[0];
            $wlmUserlvlOptId = $resObj->ID;
            $userLvlOptQry = "SELECT * FROM `$userLvlOptTbl` WHERE `userlevel_id` = %d";
            $userLvlOptRs = $wpdb->get_results($wpdb->prepare($userLvlOptQry, $wlmUserlvlOptId));
            echo "<br/>Qry 3:".$lastQry1 = $wpdb->last_query."<br/>";
            
            if(count($userLvlOptRs) >= 1){
               //echo "<pre>#46";print_r($wp_option_res);
               foreach($wp_option_res as $optKey => $optObj){
                  $optName = $optObj->option_name;
                  $optVal = $optObj->option_value;
                  $recFound = false;
                  foreach($userLvlOptRs as $userLvlObj){
                     //echo "<br/>$optName == $userLvlObj->option_name";
                     if($optName == $userLvlObj->option_name){
                        //update existing record
                        $recFound=true;
                        //echo "<br/>#57: $optVal != $userLvlObj->option_value";
                        if($optVal != $userLvlObj->option_value){
                           //print_r($wp_option_res);print_r($userLvlOptRs);
                           $updateQry = "UPDATE `$userLvlOptTbl` SET `option_value`='$optObj->option_value' WHERE `ID` = $userLvlObj->ID";
                           $selQry = "SELECT * FROM `$userLvlOptTbl` WHERE `ID` = $userLvlObj->ID";
                           echo "<br/><strong style='background-color:yellow;'>Yellow(Old Date: $userLvlObj->option_value): $updateQry<br/>$user_id: $selQry</strong><br/>";
                           $dataArr = array('option_value' => $optObj->option_value);
                           $wpdb->update($userLvlOptTbl, $dataArr, array('ID' => $userLvlObj->ID), array('%s'), array('%d'));
                           echo "<br/><span color:blue;>123:";echo $wpdb->last_query;echo "</span><br/>";
                        }else{
                           $updateQry = "UPDATE `$userLvlOptTbl` SET `option_value`='$optObj->option_value' WHERE `ID` = $userLvlObj->ID";
                           //echo "<br/>No effect of update qry:$updateQry";
                        }
                     }
                  }
                  if(!$recFound){
                     //create new record
                     $dataArr = array('userlevel_id' => $wlmUserlvlOptId, 'option_name' => $optName, 'option_value' => $optVal, 'autoload' => 'yes');
                     $insQry_option = $wpdb->insert($userLvlOptTbl, $dataArr, array('%d', '%s', '%s', '%s'));
                     echo "<br/>insert qry 1:".$insQry1 = $wpdb->last_query;
                  }
               }
            }else{
               // insert 'wp_wlm_userlevel_options' data in 'gwr_wlm_userlevel_options' table
               if(count($wp_option_res) >= 1){
                  foreach($wp_option_res as $optKey => $optObj){
                     $option_name = $optObj->option_name;
                     $option_value = $optObj->option_value;
                     $autoload = $optObj->autoload;
                     
                     $dataArr_option = array('userlevel_id' => $wlmUserlvlOptId, 'option_name' => $option_name, 'option_value' => $option_value, 'autoload' => 'yes');
                     $insQry_option = $wpdb->insert($userLvlOptTbl, $dataArr_option, array('%d', '%s', '%s', '%s'));
                     echo "<br/>insert qry 2:".$insQry2 = $wpdb->last_query;
                  }
               }else{
                  ;//nothing to update
               }
            }
         }else{
            // insert into 'gwr_wlm_userlevels' table
             $dataArr = array(
                              'user_id' => $user_id,
                              'level_id' => $level_id
                              );
            $insQry = $wpdb->insert($user_level_table_name, $dataArr, array('%d', '%d'));
            $lastid = $wpdb->insert_id;
            echo "<br/>insert qry 3:".$insQry3 = $wpdb->last_query;
             // insert 'wp_wlm_userlevel_options' data in 'gwr_wlm_userlevel_options' table
            if(!empty($wp_option_res)){
               foreach($wp_option_res as $option_key => $optObj){
                  $dataArr = array(
                                          'userlevel_id' => $lastid,
                                          'option_name' => $optObj->option_name,
                                          'option_value' => $optObj->option_value,
                                          'autoload' => $optObj->autoload,
                                          );
                   $insQry_option = $wpdb->insert('gwr_wlm_userlevel_options', $dataArr, array('%d', '%s', '%s', '%s'));
                   echo "<br/>insert qry 3:".$insQry4 = $wpdb->last_query;
               }   
            }
         }
      }
   }
?>