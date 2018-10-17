<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
	 
    //https://www.freerangecamping.co.nz/plus/wp-content/frc_misc/trash_post.php
	echo "<h3 style='padding-left:20px;'>&raquo;&nbsp;Trash Posts</h3>";
	$tableName = 'trash_posts';
		 
		$perPage = 20;
		$customPagHTML = "";
		$query = "SELECT * FROM `$tableName` ORDER BY `ID` DESC";
		$total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total = $wpdb->get_var( $total_query );
		$items_per_page = $perPage;
		$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset = ( $page * $items_per_page ) - $items_per_page;
		$results = $wpdb->get_results( $query . " LIMIT ${offset}, ${items_per_page}" );
		$totalPage = ceil($total / $items_per_page);
		if($totalPage > 1){
				$customPagHTML =  '<div>'.paginate_links( array(
				'base' => add_query_arg( 'cpage', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $totalPage,
				'current' => $page
				))."<span>(Page $page of $totalPage ,showing $perPage records Per Page )</span></div>";
		}
		echo $customPagHTML;
		 
 
		?>
		
	 <table id = 'example' border = '1'>
	 <th>Id </th>
	 <th>Post Title</th>
	 <th>Post Date</th>
	 <?php
    if(!empty($results)){
        foreach ($results as $trashResult) {
			$trashPostID = $trashResult->ID;
            $trashPostTitle = $trashResult->post_title;
			$trashPostdate = $trashResult->post_date;
			?>
			<tr>
				<td><?php echo $trashPostID;?></php></td>
				<td><?php echo $trashPostTitle;?></td>
				<td><?php echo $trashPostdate;?></td>
			</tr>
        <?php }
    }else{
		echo "No Result Found";
	 
    }
	?>
	</table>
	<?php echo $customPagHTML;?>