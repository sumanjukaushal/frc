<html>
    <head>
        <title>FRC Mobile App User Uploads</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <!--script src="jquery-2.2.1.min.js"></script-->
    </head>
    <body>
        <?php 
            require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
            global $wpdb;
            defined('DS') or define('DS', DIRECTORY_SEPARATOR);
            require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
            if(isset($_GET['submit'])){
                $search = $_GET['search'];
            }
        ?>
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Mobile App User Uploads</h3>
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
        <form action='scan_dir.php' method='get'>
            <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
                <tr bgcolor="#C6D5FD">
					<th colspan="2" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Search File Content:</th>
				</tr>
                <tr>
                    <td style="padding-left: 10px;">Search</td>
                    <td><input type='text' name='search' value = '' placeholder='search file content'/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type='submit' name='submit' value='Search'style='color:#333;background-color:#C6D5FD;border:1px solid #333;'/></td>
                </tr>
            </table>
        </form>
        </div>
<!-- Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_app_uploads/app_uploads.php -->
<?php
    
    $qry = "SELECT COUNT(*) FROM `frc_app_uploads` ";
    $query = "SELECT * FROM `frc_app_uploads`";
    $post_per_page = 20;
    $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
    $offset = ( $page * $post_per_page ) - $post_per_page;
    if(!empty($search)){
        $qry .= " WHERE `file_content` LIKE '%$search%' ORDER BY `created` DESC";
        $total = $wpdb->get_var($qry);
        $query .= "WHERE `file_content` LIKE '%$search%'";
        $rsltObjs = $wpdb->get_results( $query . " ORDER BY `created` DESC LIMIT ${offset}, ${post_per_page}");
    }
    $total = $wpdb->get_var($qry);
    $rsltObjs = $wpdb->get_results( $query . " ORDER BY `created` DESC LIMIT ${offset}, ${post_per_page}");
    $pageLnks = paginate_links( array(
                'base' => add_query_arg( 'cpage', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => ceil($total / $post_per_page),
                'current' => $page,
                'type' => 'plain'
            ));
    if(!empty($rsltObjs)){
        $url = WP_CONTENT_URL.DS."frc-admin/frc_app_uploads/scan_uploads.php";
        
        echo "<span style='font-size:200%; color:grey; text-align:center; padding-left:20px;'><u>Mobile App Uploads</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>] <a href=$url>Refresh List<a>";
        $paginationStr = "<div class='pagination' style='text-align:right;font-size:150%; padding-left:20px;'>$pageLnks</div>";
        echo $tableBlock = <<< TBL_BLK
                <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
                <table class="table-bordered table-hover" style="width:95%; padding-top:5px;">
                    <tr><td colspan='5'>$paginationStr</td></tr>
                    <tr bgcolor='#C6D5FD' style='font-size: 120%'>
                        <th>Site Title</th>
                        <th>Uploaded By</th>
                        <!--th>File Name</th-->
                        <th style='width:400px;'>File Content</th>
                        <th style='width:150px;'>Created On</th>
                        <th style='width:150px; width:70px;'>Action</th>
                    </tr>
TBL_BLK;
                    
        $file_path = WP_CONTENT_DIR.DS."mobile_uploads";
        foreach($rsltObjs as $key => $value){
                $bgColor = $key % 2 == 0 ? '#FFFFCC' : '#D0D0D0';
                $phpDate = strtotime( $value->created );
                $uploadDate = date( 'M j, y h:i A', $phpDate );
                $user_info = get_userdata($value->user_id);
                $username = $user_info->user_login;
                $first_name = $user_info->first_name;
                $last_name = $user_info->last_name;
                $userName = $username;
                $fullName = $first_name. ' '. $last_name;
                
                $id = $value->id;
                echo "<tr id='row_{$id}' style='background:{$bgColor}'>";
                echo "<td valign='top'>".get_the_title($value->post_id)."</td>";
                echo "<td valign='top'>$userName [<a href='#-1' alt='$value->file_name' title='$value->file_name'>$fullName</a>]</td>";
                //echo "<td valign='top'>".$value->file_name."</td>";
                $path_parts = pathinfo($file_path.DS.$value->file_name);
                if($path_parts['extension'] == 'txt' ){
                    echo "<td valign='top'>".$value->content."</td>";
                }else{
                    $fileURL = WP_CONTENT_URL.DS."mobile_uploads/{$value->file_name}";
                    echo "<td valign='top'><a href='$fileURL' target='_blank'><img src='$fileURL' width='100' heigh='100' /></a></td>";
                }
                echo "<td valign='top' nowrap='nowrap'>$uploadDate</td>";
                echo "<td><a href = '#' rel ='{$id}&file={$value->file_name}' class='delete'>Delete</a></td>";
                echo "</tr>";
        }
        echo "<tr><td colspan='5'>$paginationStr</td></tr></table></div>";
    }
?>
    </body>
</html>

<script>
    $(document).on('click', '.delete', function () {
        var id = $(this).attr('rel');
        var result = confirm('Are you sure you want to delete this record?');
        if (result === false) {
            e.preventDefault();
        }else{
            $.blockUI({ message: 'Deleting Record...' });
            var targeturl = "<?php echo WP_CONTENT_URL;?>/frc-admin/frc_app_uploads/delete_file.php?id=";
            targeturl += id;
            $.ajax({
                type: "POST",
                url: targeturl,
                beforeSend: function(xhr) {
				    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			    },
                success: function (msg) {
                    var res = $.parseJSON(msg);
                    alert(res.message);
                    if(res.success == 1){
                        $('#row_'+res.id).hide();
                    }
                    $.unblockUI();
                },
    			error: function(e) {
    				$.unblockUI();
    				alert("Failed to delete record");
    				console.log(e);
    			}
            });
        }
    });
</script>