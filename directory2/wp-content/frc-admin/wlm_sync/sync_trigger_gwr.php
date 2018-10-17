<?php 
  require_once("../../wp-config.php");
  require_once('../../wp-load.php');
  global $wpdb;
  //1. Synch new capabilities from directory -> to others
  echo "<pre>";
  $query = "SELECT * FROM `gwr_usermeta` WHERE `processed_by_script` = 2 ORDER BY `user_id` DESC";
  //(`modified` > ( NOW( ) - INTERVAL 5 MINUTE )) OR 
  $resultObj = $wpdb->get_results($query);
  $tableName = 'gwr_usermeta';
  foreach($resultObj as $sngObj){
    $flag = $sngObj->processed_by_script;
    $umetaID = $sngObj->umeta_id;
    $status = $wpdb->update($tableName, array( 'processed_by_script' => 1), array( 'umeta_id' => $umetaID));
  }
  /*
   *$second_db = new wpdb(DB_USER, DB_PASSWORD, $database_name, DB_HOST);
   *While these will work, you'll lose the ability to use the "other" custom features such as get_post_custom and wordpress queries.
   *The simple solution is $wpdb->select('database_name');
   **/
  //2. Synch new capabilities from premium -> to others
  $wpdb->select('freerang_premium');
  wp_cache_flush();
  $query = "SELECT * FROM `gwr_usermeta` WHERE `processed_by_script` = 2 ORDER BY `user_id` DESC";
  $resultObj = $wpdb->get_results($query);
  foreach($resultObj as $sngObj){
    $flag = $sngObj->processed_by_script;
    $umetaID = $sngObj->umeta_id;
    $status = $wpdb->update($tableName, array( 'processed_by_script' => 1), array( 'umeta_id' => $umetaID));
  }
  
  //3. Synch new capabilities from shop -> to others
  $wpdb->select('freerang_shop');
  wp_cache_flush();
  $query = "SELECT * FROM `gwr_usermeta` WHERE `processed_by_script` = 2 ORDER BY `user_id` DESC";
  $resultObj = $wpdb->get_results($query);
  foreach($resultObj as $sngObj){
    $flag = $sngObj->processed_by_script;
    $umetaID = $sngObj->umeta_id;
    $status = $wpdb->update($tableName, array( 'processed_by_script' => 1), array( 'umeta_id' => $umetaID));
  }
  
  //4. Synch new capabilities from classified -> to others
  $wpdb->select('freerang_classified');
  wp_cache_flush();
  $query = "SELECT * FROM `gwr_usermeta` WHERE `processed_by_script` = 2 ORDER BY `user_id` DESC";
  $resultObj = $wpdb->get_results($query);
  foreach($resultObj as $sngObj){
    $flag = $sngObj->processed_by_script;
    $umetaID = $sngObj->umeta_id;
    $status = $wpdb->update($tableName, array( 'processed_by_script' => 1), array( 'umeta_id' => $umetaID));
  }
  
  echo "Script Executed successfully";
?>