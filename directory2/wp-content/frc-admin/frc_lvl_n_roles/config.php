<?php
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    global $wpdb;
    $tableName = "{$wpdb->prefix}options";
    $fieldName = "{$wpdb->prefix}user_roles_bak"; 
?>