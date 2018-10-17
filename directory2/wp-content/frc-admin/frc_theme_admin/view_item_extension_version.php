<html>
    <head>
        <title>FRC: View Item Extension Feature Versions Detail</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    require("inner_features_navigation.php");
    $version = $_REQUEST['version'];
	$query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version'";
    $versions = $wpdb->get_results($query);
	if(count($versions) >= 0){
        $unsearlizeData = unserialize($versions[0]->option_value);
        $tableBlock = "";
        foreach($unsearlizeData as $key => $features){
            $bgColor = $key % 2 == 0 ? '': '#D0D0D0';
            $featureID = $features['uid'];
            $featureName = $features['label']['en_US'];
            $featureType = $features['type'];
            $featureHelp = $features['help']['en_US'];
            $tableBlock.= "<tr style='background-color:$bgColor'>
                                <td style='padding: 5px;'>$featureID</td>
                                <td style='padding: 5px;'>$featureName</td>
                                <td style='padding: 5px;'>$featureType</td>
                                <td style='padding: 5px;'>$featureHelp</td>
                            </tr>";
        }
        echo $formRow =<<<FORM_HTML
        <table class="table-bordered table-hover" style='margin-left: 20px;margin-bottom: 30px;font-family:  Serif, Georgia, \'Times New Roman\';'>
            <tr style='background-color:#C6D5FD;'>
            <th style="padding: 5px;">Features Code</th>
            <th style="padding: 5px;">Feature Name</th>
            <th style="padding: 5px;">Feature Type</th>
            <th style="padding: 5px;">Help</th>
            </tr>
            $tableBlock
            </table>
FORM_HTML;
    }
    
    
?>

</body>
</html>