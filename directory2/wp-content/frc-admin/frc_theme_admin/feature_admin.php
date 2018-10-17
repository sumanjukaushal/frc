<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC User Role and Level Management</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
	</head>
	<body>
	    <?php
	        require_once("../../../wp-config.php");
            require_once('../../../wp-load.php');
	        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
	        $themeObj = wp_get_theme();
	        if(!in_array(trim($themeObj->Name),$themeAllowed)){
	            echo "<p style='padding-left:20px;padding-top:50px;color:red'>This script is only for Directory Theme Purpose</script>";
	            die;
	        }
	    ?>
		<h3 style='padding-left:20px;'>&raquo;&nbsp;Manage Item Extension Features (Step 1)</h3>
		<h4 style='padding-left:20px;'>After save: This script will keep data in safezone.</h4>
		<h5 style='padding-left:20px;color:blue;'>Safezone data can be used to restore Directory plus theme to normal state if anything goes wrong anytime or when we setup theme fresh.</h5>
<?php
	//Calling URL: https://www.freerangecamping.co.nz/directory7/wp-content/frc_theme_admin/feature_admin.php
	
    global $wpdb, $wp_roles;
    $allRoles = $wp_roles->roles;
	$flashMsg = "";
	//echo uniqid("", true);
	$uniqID = rand(11, 99).str_replace(".", "", uniqid("", true)).".".rand(11111111, 99999999);
	if(isset($_REQUEST['Submit'])){
		//if(count($_REQUEST['remove']))
		$featureArr = array();
		foreach($_REQUEST['label'] as $key => $label){
			if(array_key_exists('remove', $_REQUEST)){
				$remArr = array_keys($_REQUEST['remove']);
				if(in_array($key, $remArr)){continue;}
			}
			$featureArr[] = array(
								  'uid' => $_REQUEST['uid'][$key],
								  'type' => $_REQUEST['type'][$key],
								  'label' => array('en_US' => $label),
								  'help' => array('en_US' => $_REQUEST['help'][$key]),
								  'value' => $_REQUEST['value'][$key],
								  'min' => $_REQUEST['min'][$key],
								  'max' => $_REQUEST['max'][$key],
								  'step' => $_REQUEST['step'][$key],
								  //'tooltip' => $_REQUEST['tooltip'][$key],
								  );
		}
		if(count($featureArr ) >= 1){
			$optVal = serialize($featureArr);
			$updated = $wpdb->update( 'frc_admin_options',
								 array('option_value' => $optVal),
								 array('option_name' => 'frc_features') );
			if($updated){
				$flashMsg = "Features updated successfully";
			}
		}
	}
	$featureQry = "SELECT * FROM `frc_admin_options` WHERE `option_name` = 'frc_features'";
	$featureObj = $wpdb->get_results($featureQry);
	
	if(count($featureObj) >= 1){
		$featureArrays = unserialize($featureObj[0]->option_value);
		$formRow = "";
		foreach($featureArrays as $key => $featureArray){
			extract($featureArray);
			$feature = $featureArray['label']['en_US'];
			$helpTxt = $featureArray['help']['en_US'];
			$bgColor = $key % 2 ? 'style="background-color:#FFFFCC"' : 'style="background-color:#D0D0D0"';
			$formRow.=<<<FORM_ROW
				<tr $bgColor>
					<td><textarea rows="3" cols="30" name='label[$key]'>$feature</textarea></td>
					<td><textarea rows="3" cols="30" name='help[$key]'>$helpTxt</textarea></td>
					<!--td><textarea rows="3" cols="30" name='tooltip[$key]' value='$tooltip'></textarea></td-->
					<td><input type='checkbox' name='remove[$key]' /> Remove</td>
					<!--hidden fields-->
					<input type='hidden' name='uid[$key]' value='$uid' />
					<input type='hidden' name='type[$key]' value='$type' />
					<input type='hidden' name='value[$key]' value='$value' />
					<input type='hidden' name='min[$key]' value='$min' />
					<input type='hidden' name='max[$key]' value='$max' />
					<input type='hidden' name='sleep[$key]' value='$step' />
					<!--/hidden fields-->
				</tr>\n
FORM_ROW;
		}
		$headRow = "<tr><th>Feature</th><th>Help Text</th><!--th>Tooltip</th--><th>Action</th></tr>";
		$submitRow = '<tr>
						<td colspan="5" style="text-align:center; padding-top:3px; padding-bottom:3px;">
							<input type="submit" name="Submit" value="Submit Features">
					</td></tr>';
	}
	echo $featureForm = <<<FEA_FORM
	<div style='text-align:center;color:red'>$flashMsg</div>
	<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
		<form method='post'>
			<table class="table-bordered table-hover" style="width:80%; padding-top:5px;">
				<thead>
					<tr bgcolor="#C6D5FD">
						<th colspan="5" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Directory 2 Theme Features:</th>
					</tr>
					$headRow
				</thead>
				<tbody>
					$submitRow
					$formRow
					$submitRow
				</tbody>
			</table>
		</form>
	</div>		
    </body>
FEA_FORM;
?>
</html>