<html>
    <head>
        <title>Frc: Elastic Search</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
    </head>
    <body>
    <?php 
        require_once("../../../wp-config.php");
        require_once('../../../wp-load.php');
        global $wpdb;
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
        echo"<h3 style='padding-left:20px;'>Elastic Search &raquo;</h3>";
        $isOldTheme = is_old_theme();
        $galleryMetaKey =  $isOldTheme ? '_ait_dir_gallery' : '_ait-item_item-data';
        $tblName = $wpdb->prefix.'postmeta';
        echo $postID = 30978;
        $elaticRS = ep_find_related( $postID, $return = 5 );
        echo "<pre>";print_r($elaticRS);echo "</pre>";
    ?>
    
    </body>
</html>