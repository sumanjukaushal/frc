<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    $url = WP_CONTENT_URL."/image/clipart-smiley-face-3.png";
    echo"<div style='text-align: center;'><img src='$url' style='height: 200px;' /></div>";
    die();
?>