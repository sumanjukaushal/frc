<html>
    <head>
        <title>FRC: Feature n Icons</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <body>
<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb, $aitThemeOptions;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $query = "SELECT  * FROM `gwr_wlm_userlevel_options_bak`";
	$limitRS = $wpdb->get_results($query);
    if(count($limitRS)){
        foreach($limitRS as $sngLt){
            $userLvlID = $sngLt->userlevel_id;
            $optName = $sngLt->option_name;
            $optValue = $sngLt->option_value;
            $query = "SELECT  * FROM `gwr_wlm_userlevels` WHERE `ID` = $userLvlID";
            $lvsRS = $wpdb->get_results($query);
            if(count($lvsRS)){
                $lvlID = $lvsRS[0]->level_id;
                //echo "<br/>\nRecord $userLvlID is good $lvlID";
                echo "<br/>\n".$selQry = "SELECT `ID` FROM `gwr_wlm_userlevel_options` WHERE `userlevel_id` = $userLvlID AND `option_name` = '$optName'";
                $selRS = $wpdb->get_results($selQry);
                if(count($selRS) >= 1){
                    ;//do nothing
                }else{
                    $updated = $wpdb->insert( 'gwr_wlm_userlevel_options', array(
                                                                                 'option_value' => $optValue,
                                                                                 'option_name' => $optName,
                                                                                 'autoload' => 'yes',
                                                                                 'userlevel_id' => $userLvlID,
                                                                                 'flag' => 1,
                                                                                 ));
                    echo "<br/><br/>".$wpdb->last_query;
                }
            }
        }
    }
?>
    
</body>
</html>