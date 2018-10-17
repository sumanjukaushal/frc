<html>
    <head>
		<title>FRC Replace Metas</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<?php 
		   require_once("../../../wp-config.php");
		   require_once('../../../wp-load.php');
		   global $wpdb, $aitThemeOptions;
		   defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
			$themeObj = wp_get_theme();
			if(!in_array(trim($themeObj->Name),$themeAllowed)){
				echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
				die;
			}	
        ?>
    </head>
    <body>
<?php $selected = isset($_REQUEST['submit']) ? $_REQUEST['yes']: 'true';?>
		<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
			<form method='post' >
				<table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
					<tr bgcolor="#C6D5FD">
						<th colspan="2" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Update GMT for Scheduler:</th>
						</tr>
					<tr><td colspan='2' style='padding-left:20px;'><span>Do you want to modify Post GMT ?</span></td></tr>
					<tr>
						<td colspan='2' style=' padding-left:20px;'><input type="radio" name="yes" value="true" <?php if($selected == 'true'){echo checked;} ?>/> Yes &nbsp;<input type="radio" name="yes" value="false" <?php if($selected == 'false'){echo checked;} ?>/> No</td>
					</tr>
     <tr>
						<td colspan='2' style="padding-left:20px;line-height: 35px;min-height: 35px;margin-top:5px;">Choosing yes option will update gmt and that will make these items eligible for re-publishing for Mobile App's</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center; padding-top:3px; padding-bottom:3px;"><input type="submit" name="submit" value="Submit" /></td>
					</tr>
				</table>
			</form>
		</div>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_misc/find_replace_metas.php
    if(isset($_REQUEST['submit']) && ($_REQUEST['yes'] == 'true')){
		//print_r($_REQUEST);die;
		$args = array(
						'post_type' => 'ait-dir-item',
						'tax_query' => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'ait-dir-item-location', //New Theme: ait-locations
								//'field'    => 'term_id',
								//'terms'    => array( 1564 ),
								'field'    => 'slug',
								'terms'    => array( 'nsw' ),
								'include_children' => false
							),
							array(
								'taxonomy' => 'ait-dir-item-category', //New Theme: ait-items
								//'field'    => 'term_id',
								//'terms'    => array( 30 ),
								'field'    => 'slug',
								'terms'    => array( 'rest-areas' ),
								'include_children' => false
							),
						),
					);
	
		$timeNow = gmdate("Y-m-d H:i:s", mktime());
		$query = "SELECT  gwr_posts.ID, gwr_posts.post_title, gwr_posts.post_modified_gmt FROM gwr_posts  LEFT JOIN gwr_term_relationships ON (gwr_posts.ID = gwr_term_relationships.object_id)  LEFT JOIN gwr_term_relationships AS tt1 ON (gwr_posts.ID = tt1.object_id) WHERE (gwr_term_relationships.term_taxonomy_id IN (1594) AND tt1.term_taxonomy_id IN (30)) AND gwr_posts.post_type = 'ait-dir-item' AND (gwr_posts.post_status = 'publish' OR gwr_posts.post_status = 'private') GROUP BY gwr_posts.ID ORDER BY gwr_posts.post_modified_gmt ASC LIMIT 0, 50";
		$limitRS = $wpdb->get_results($query);
		
		foreach($limitRS as $sngLt){
			$postID = $sngLt->ID;
			$postMetas = get_post_meta($postID,'_ait-dir-item');
			if(count($postMetas) >= 0){
				$postMeta = $postMetas[0];
				$postMeta['telephone'] = $postMeta['email'] = '';
				update_post_meta( $sngLt->ID, '_ait-dir-item', $postMeta );
				//echo "<pre>Post ID:$sngLt->ID\n";print_r($postMeta);echo "</pre>;die;*/
				echo "<br/>".$updateQry = "UPDATE `gwr_posts` SET `post_modified_gmt` = '$timeNow' WHERE `ID` = $postID";
				$wpdb->query($updateQry);
			}
		}
		
		echo "
			<p style='color:brown'>This script is finding all Items whose Category: Rest Area and whose location : NSW
			<br/>After finding those records, this script is removing phone number and emails.
			<br/>This script should be run after regular interval of 10 mins and should be stopped after processing all items.
			<br/>If records are 500 than it should be run 10 times.</p>
			<strong>Purpose:</strong>
			<p>
			This script is created for Mobile App Database purpose. We were hiding phone and email for these sites in web by applying logics.
			Because telephone and email was present in the database, and offline database for mobile app was picking this information from database and it was getting displayed for Andoid App. Now this is handled by this script.
			</p>
			<strong>Script Creation time:</strong>
			<p style='color:brown'>1st Sep, 2017</p>
			<strong>Script Modification time:</strong>
			<p style='color:brown'>7th Sep, 2018</p>
			<p>**Seems we don't need this to be run again**</p>
			";
		die("Script Executed Successfully");
	}
    
?>
 </body>
</html>