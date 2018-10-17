<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <title>FRC: Role Archieve</title>
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    require("inner_navigation.php");
    $query = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_gwr_user_roles%' ORDER BY `id` ASC ";
    $versions = $wpdb->get_results($query); //echo'<pre>';print_r($versions);die;
    $count = 0;
    $rowStr = "";
    
    if(array_key_exists('status',$_REQUEST)){
        $version = $_REQUEST['version'];
        $status = $_REQUEST['status'];
        if($status == 0){
            $message = "Failed to restore roles from version <strong>$version</strong> of archieve";
        }else{
            $message = "Roles are successfully restored from version <strong>$version</strong> of archieve";
        }
    }
    
    foreach($versions as $version => $value){
        $memo = $value->memo;
		if($memo == ''){
			$memo = "--";
		}
        $vrsn = $value->option_name;
        if(strpos($vrsn,'_v')===false){
                continue;
        }
        $bgColor = $version % 2 == 0 ? '#edd6be': '#dac292';
        $count++;
        $modDate = date('d M Y, g:i a', strtotime($value->modified));
        $ver = str_replace("frc_gwr_user_roles_","",$value->option_name);
        $detailLink = "view_role_version.php?version=$value->option_name";
        $rowStr .="
        <tr>
            <td style='padding-left:5px;'>$count</td>
            <td style='padding-left:5px;'>$modDate</td>
            <td style='padding-left:5px;'>Version : <a href='update_role_version.php?version=$value->option_name'alt=$vrsn title=$vrsn id='confirm_restore'>{$ver}</a>&nbsp;&nbsp;&nbsp;Memo: $memo&nbsp;&nbsp;&nbsp;<a href='$detailLink' name='view_detail' />View Details</a></td>
        </tr>";
    }
    
    echo $htmlBlock = "
    <div style='width:80%; text-align: center;color: red;'>$message</div>
    <div class='table-responsive' style='padding-left:20px;padding-top:5px;'>
        <table class='table-bordered table-hover' style='width:80%; padding-top:5px;margin-bottom: 30px;' >
            <tr bgcolor='#C6D5FD'>
                <th colspan='4' style='line-height: 35px;min-height: 35px;margin-top:5px;'>&nbsp;»&nbsp;Roles Details:</th>
            </tr>
            <tr style='background-color:lightsteelblue'>
                <td style='line-height: 35px;min-height: 35px;margin-top:5px;'>Sr. No</td>
                <td>Version Date</td>
                <td>Restore Item features from <strong>frc_gwr_user_roles</strong> </td>
            </tr>
            $rowStr
            
        </table>
    </div>";
?>
</body>
</html>
<script>
	$("a#confirm_restore").click(function() {
		var version = ($(this).text());
		if (!confirm('Do you want to restore '+version+' version')) {
			return false;
		}
	});
</script>