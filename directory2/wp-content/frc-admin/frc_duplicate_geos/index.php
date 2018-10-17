<?php
	define('DB_NAME', 'freerang_directory');
	define('DB_USER', 'freerang_rob');
	define('DB_PASSWORD', 'nandini123');
	define('DB_HOST', 'localhost');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATE', '');
	define( 'WP_MEMORY_LIMIT', '512M' ); 
	$dbhandle = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	$selected = mysql_select_db(DB_NAME,$dbhandle);
	$result = mysql_query("SELECT t1.* FROM `ait_items` t1
															JOIN (
																SELECT `str_latitude` , `str_longitude` , `post_id` , `address`
																FROM `ait_items` 
																GROUP BY `str_latitude` , `str_longitude` 
																HAVING COUNT(*) > 1
															) t2 ON t1.`str_latitude` = t2.`str_latitude`  AND t1.`str_longitude`  = t2.`str_longitude`
															ORDER BY `t1`.`str_latitude`  DESC");
	$data = array();
	while ($row = mysql_fetch_array($result)) {
		$data[] = $row;
	}
	$msg = "";
	if(isset($_REQUEST['msg']) && $_REQUEST['msg'] == 1){
		$msg = "<p>List Refereshed!</p>";
	}
?>
<html>
    <head>
    </head>
    <body>
		<?php echo $msg;?>
		<p><a href='add_update_duplicates.php'>Refresh List</a></p>
        <table border = 1px;>
            <tr>
                <thead>
					<th>Post_Id</th>
					<th>Post Title</th>
					<th>address</th>
					<th>Latitude</th>
					<th>Longitude</th>
					<th>City</th>
					<th>State</th>
                </thead>
            </tr>
            <tbody>
			<?php
				foreach($data as $key => $row){
					$postID = $row["post_id"];
			?>
            <tr>
				<td><a href="https://www.freerangecamping.com.au/directory/wp-admin/post.php?post=<?=$postID;?>&action=edit" target='_blank'><?= $row["post_id"];?></a></td>
				<td><?= $row["post_title"];?></td>
				<td><?= $row["address"];?>&nbsp;</td>
				<td><?= $row["str_latitude"];?>&nbsp;</td>
				<td><?= $row["str_longitude"];?>&nbsp;</td>
				<td><?= $row["city"];?>&nbsp;</td>
				<td><?= $row["state"];?>&nbsp;</td>
            </tr>
            <?php
				}
			?>
            </tbody>
        </table>
    </body>
</html>
