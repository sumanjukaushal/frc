<html>
    <head>
        <title>FRC: View Packages Detail</title>
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
    require("inner_package_navigation.php");
    $version = $_REQUEST['version'];
	$query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version'";
    $versions = $wpdb->get_results($query);
	if(count($versions) >= 0){
        $unsearlizeData = unserialize($versions[0]->option_value);
        $tableBlock = "";
        $tableBlock2 = "";
        $keyCount = 0;
        //echo'<pre>';print_r($unsearlizeData);die;
        foreach($unsearlizeData['packages']['packageTypes'] as $key => $packageData){
            $bgColor = $keyCount % 2 == 0 ? '': '#D0D0D0';
            $tableBlock.= "<tr style='background-color:$bgColor';font-size: 110%;><td style='padding: 5px;'>$packageData[name]&nbsp;</td>
            <td style='padding: 5px;'>$packageData[desc]&nbsp;</td>
            <td style='padding: 5px;'>$packageData[maxItems]&nbsp;</td>
            </tr>";
            $keyCount++;
        }
        echo $formRow =<<<FORM_HTML
        <table class="table-bordered table-hover" style='margin-left: 20px;margin-bottom: 30px;font-family:  Serif, Georgia, \'Times New Roman\';'>
            <tr style='background-color:#C6D5FD;'>
            <th style="padding: 5px;">Package Name</th>
            <th style="padding: 5px;">Package Description</th>
            <th style="padding: 5px;">Max Items</th>
            </tr>
            $tableBlock
            </table>
FORM_HTML;
    }
    
    
?>

</body>
</html>
<script>
    $(".header").click(function () {

    $header = $(this);
    
    $content = $header.next();
    
    $content.slideToggle(500, function () {
        
    $header.text(function () {
        return $content.is(":visible") ? "Collapse" : "Expand";
    });
    });

});
</script>