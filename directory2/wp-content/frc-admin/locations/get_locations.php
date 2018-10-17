<?php
     //require("../../wp-config.php");
     $search = str_replace(",", " ", strtolower($_REQUEST['search']));
     //$link = mysql_connect('localhost', DB_USER, DB_PASSWORD);
     $link = mysql_connect('localhost', 'ozdobcom_mohit', 'nandini123');
     /*
     UPDATE `rasu_locations` SET output = CONCAT_WS(',',`state`,`region`, `sub_region`);
     */
     if (!$link) {
         die('Could not connect: ' . mysql_error());
     }
     //mysql_select_db(DB_NAME, $link) or die('Could not select database.');
     mysql_select_db('ozdobcom_locations', $link) or die('Could not select database.');

     function pc_permute($items, $perms = array( )) {
          //return $items;
          if (empty($items)) { 
              print join(' ', $perms) . "\n";
          }else{
              for ($i = count($items) - 1; $i >= 0; --$i) {
                  $newitems = $items;
                  $newperms = $perms;
                  list($foo) = array_splice($newitems, $i, 1);
                  array_unshift($newperms, $foo);
                  pc_permute($newitems, $newperms);
              }
          }
     }
     
     $searchArray = array();
     $splittedArr = split(' ', $search);

     ob_start();
     pc_permute($splittedArr);
     $permutation = ob_get_clean();
     $wordArray = explode("\n", $permutation);



     foreach($wordArray as $value){
         if(empty($value)) continue;
         $searchArray[] = "LOWER(`rasu_locations`.`output`) like '%".str_replace(" ","%",$value)."%'";
     }
     $searchStr = implode(' OR ', $searchArray);

     $query = "SELECT `output` FROM `rasu_locations` WHERE $searchStr";
     $rs = mysql_query($query);
     if (!$rs) {
        //echo "Could not successfully run query ($query) from DB: " . mysql_error();
        //exit;
     }
     $locationArr = array();
     while($rowObj = mysql_fetch_object($rs)){
       //print_r($rowObj);
       $locationArr[] = array('location' => $rowObj->output);
     }
     echo json_encode($locationArr);
?>