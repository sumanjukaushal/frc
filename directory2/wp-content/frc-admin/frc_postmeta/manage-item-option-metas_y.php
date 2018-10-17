<!DOCTYPE html>
<html>
    <head>
        <title>FRC: Feature n Icons</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <?php 
        require_once("../../wp-config.php");
        require_once('../../wp-load.php');
        global $wpdb;
        //Table Required: gwr_postmeta
        //meta_key: _ait-dir-item
        $optionTbl = $wpdb->prefix."postmeta";
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once(WP_CONTENT_DIR.DS.'frc_includes/header.php');
        if(isOldTheme()){
			include(WP_CONTENT_DIR.DS."frc_postmeta/copyDiscountOldTheme.php");
		}else{
			include(WP_CONTENT_DIR.DS."frc_postmeta/copyDiscountNewTheme.php");
		}
        
    ?>
    <body>
    </body>
</html>