<html>
    <head>
        <title>FRC: View Roles Detail</title>
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
    require("inner_navigation.php");
    $version = $_REQUEST['version'];
	$query = "SELECT * FROM `frc_admin_options` WHERE `option_name` = '$version'";
    $versions = $wpdb->get_results($query);
	if(count($versions) >= 0){
        $unsearlizeData = unserialize($versions[0]->option_value);
        $tableBlock1 = "";
        $tableBlock2 = "";
        $keyCount = 0;
        foreach($unsearlizeData as $role => $rolesNCapblty){
            $capabilites = "";
            foreach($rolesNCapblty['capabilities'] as $CapbilityName => $CapbilityValue) {
                if($CapbilityValue == 1){
                    $capabilites.= "<li>$CapbilityName</li>";
                }
            }
            $bgColor = $keyCount % 2 == 0 ? '': '#D0D0D0';
            $tableBlock1.= "<tr style='background-color:$bgColor';font-size: 110%;><td style='padding: 5px;'>$rolesNCapblty[name]&nbsp;</td>
            <td><div class='header' style='text-align: right;color: blue;font-size: 80%;'><span>Expand</span></div>
            <div class='content' style='display:none'>
                <ul>
                    $capabilites
                </ul>
            </div></td></tr>";
            $keyCount++;
        }
        echo $formRow =<<<FORM_HTML
        <table style='margin-left: 20px;margin-bottom: 30px;font-family:  Serif, Georgia, \'Times New Roman\';'>
            <tr><th colspan='2' style='background-color:#C6D5FD;font-size: 120%;text-align: center;width: 600px;padding: 6px;'>Roles</th></tr>
            $tableBlock1 &nbsp;&nbsp;&nbsp;&nbsp;
            
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