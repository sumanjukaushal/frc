<html>
    <head>
		<title>FRC: Grab package from theme Admin</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	require("inner_package_navigation.php");
    $themeObj = wp_get_theme();
    if(!in_array(trim($themeObj->Name),$themeAllowed)){
        echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
        die;
    }else{
		switch(trim($themeObj->Name)){
			case 'Directory Plus':
				$packageOptionName = '_ait_directory2_theme_opts';
				break;
			case 'Directory Plus Child Theme':
				$packageOptionName = '_ait_directory2-child_theme_opts';
				break;
		}
	}
?>
<h3 style='padding-left:20px;'>&raquo;&nbsp;Grab Packages</h3>
<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
    <form method='post' >
        <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
            <tr bgcolor="#C6D5FD">
				<th colspan="1" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Grab Packages:</th>
				<td style='text-align: right;padding-right: 5px;'><a href='package-versions.php'>View Archieve</a></th>
			</tr>
            <tr><td colspan='2' style='padding-left:20px;'><span>Do you want to import fresh packages n their attributes from <strong style='background-color: yellow;'><?php echo $themeObj->Name;?></strong> theme? This is required only if you did any changes in packages or their attributes recently.</span></td></tr>
            <tr><td colspan='2' style='padding-left:20px;'>Note: we are saving this information in archieve. If we need to restore older state of packages and their attributes, we can use this utility.</td></tr>
            <tr>
                <td colspan='2' style='padding-left:20px;'><input type="radio" name="yes" value="true" checked/> Yes &nbsp;<input type="radio" name="yes" value="false" /> No</td>
            </tr>
			<tr><td colspan='2' style='padding-left:20px;'>Memo: <textarea rows="2" cols="20" placeholder="Enter Notes" name="memo" required></textarea></td></tr>
            <tr>
            <tr>
            <tr>
				<td colspan="2" style="text-align:center; padding-top:3px; padding-bottom:3px;"><input type="submit" name="submit" value="Submit" />
				</td>
			</tr>
        </table>
    </form>
</div>
<?php
	if(isset($_REQUEST['submit']) && isset($_REQUEST['yes'])){
		$memo = $_REQUEST['memo'];
		$optionsTbl = $wpdb->prefix."options";
		$query = "SELECT * FROM `$optionsTbl` WHERE `option_name` = '$packageOptionName'"; //echo "<br/>".$query;
		$results = $wpdb->get_results($query);
		if(count($results) >= 1){
			foreach($results as $result => $value){
				$data = $value->option_value;
				$pkgData = unserialize($data);
				$pkgDataArr = $pkgData['packages']['packageTypes'];
			}
			$qry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_ait_directory2_theme_opts_%' ORDER BY `id` DESC LIMIT 1";
			$featureRS = $wpdb->get_results($qry);
			if(count($featureRS) >= 1){
				$newVersion = str_replace("frc_ait_directory2_theme_opts_v","",$featureRS[0]->option_name)+1;
				$nameOptName = "frc_ait_directory2_theme_opts_v{$newVersion}";
			}else{
				//if there is no record with any version, create first version
				$nameOptName = "frc_ait_directory2_theme_opts_v1";
			}
			$wpdb->query("UPDATE `frc_admin_options` SET `option_name` = '$nameOptName' WHERE `option_name` = 'frc_ait_directory2_theme_opts'");
			
			$wpdb->insert( 'frc_admin_options',
								  array( 'option_name' => 'frc_ait_directory2_theme_opts', 'option_value' => $data, 'memo' => $memo),
								  array(   '%s', '%s'));
			
			$updated1 = $wpdb->update( 'frc_admin_options',
								 array('option_value' => serialize($pkgDataArr)),
								 array('option_name' => 'frc_package_types') );		//echo "<br/>".$wpdb->last_query;
			
			$selQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_ait_directory2_theme_opts_v%' ORDER BY `id` DESC LIMIT 1";
			$selOBJ = $wpdb->get_results($selQry);
            if(!count($selOBJ)){
                $selQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` LIKE 'frc_ait_directory2_theme_opts%' ORDER BY `id` DESC LIMIT 1";
                $selOBJ = $wpdb->get_results($selQry);
            }
			//echo'<pre>';print_r($selOBJ);die;
			echo "<p style='padding-left:20px;color:green'>Packages are grabbed successfully from theme (<span style='background-color:yellow'>$themeObj->Name</span>) and old version is saved here:<strong>".$selOBJ[0]->option_name."</strong></p>";
		}else{
			echo "<p style='padding-left:20px;color:red'>No record found for grabbing packages</p>";
		}
	}
?>
</body>
</html>