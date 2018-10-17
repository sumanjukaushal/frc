<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    $taxTbl = $wpdb->prefix."term_taxonomy";
    $taxRelTbl = $wpdb->prefix."term_relationships";
    $selQry = "SELECT `term_taxonomy_id` FROM $taxTbl";
    $taxResults = $wpdb->get_results($selQry);
    //https://www.freerangecamping.co.nz/frc2/wp-content/frc_misc/fix-category-count.php
    if(count($taxResults) > 1){
        foreach ($taxResults as $taxResult) {
            $term_taxonomy_id = $taxResult->term_taxonomy_id;
            $countQry = "SELECT count(*) as `taxCounts` FROM $taxRelTbl WHERE term_taxonomy_id = '$term_taxonomy_id'";
            $countResults = $wpdb->get_results($countQry);
            if(count($countResults) >= 1){
                $counts = $countResults[0]->taxCounts;
                $extUpdated = $wpdb->update( $taxTbl,
								 array('count' => $counts),
								 array('term_taxonomy_id' => $term_taxonomy_id)
                                 );
            }
        }
    }
die("---");
$result = mysql_query("SELECT ID FROM ".$table_prefix."posts");
while ($row = mysql_fetch_array($result)) {
  $post_id = $row['ID'];
  echo "post_id: ".$post_id." count = ";
  $countresult = mysql_query("SELECT count(*) FROM ".$table_prefix."comments WHERE comment_post_ID = '$post_id' AND comment_approved = 1");
  $countarray = mysql_fetch_array($countresult);
  $count = $countarray[0];
  echo $count."<br />";
  mysql_query("UPDATE ".$table_prefix."posts SET comment_count = '$count' WHERE ID = '$post_id'");
        }
?>