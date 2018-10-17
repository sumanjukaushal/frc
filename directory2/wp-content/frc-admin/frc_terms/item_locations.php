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
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    $locTax = is_old_theme() ? 'ait-dir-item-location': 'ait-locations';
    $locTerms = get_terms(array('taxonomy' => $locTax, 'number' => 0,  'orderby' => 'term_id','order' => 'ASC',));
    
    $count = 0;
    echo "<pre>"; print_r($locTerms);
    foreach($locTerms as $locObj){
        if(!$locObj->parent){
            
            //check record if with same slug exists otherwise add new
            echo $query = "SELECT COUNT(*) FROM `frc_location_terms` WHERE slug='$locObj->slug'";
            $user_count = $wpdb->get_var( $query);
            print_r($user_count);
            die;
        }
    }
    die;
    foreach($featureArr as $sngArr){
        $iconURL = WP_CONTENT_URL.DS."frc-admin".DS."frc_misc".DS."icons".DS."30X30".DS.$sngArr[2];
        $largeIconURL = WP_CONTENT_URL.DS."frc-admin".DS."frc_misc".DS."icons".DS."120X120".DS.$sngArr[2];
        $iconFeature = $sngArr[1];
        $Sr = ++$count;
        $tooltip_msg = ""; 
        foreach($tooltip as $tooltips => $value){ 
            if($tooltips == $sngArr[1]){
                $tooltip_msg = $value;
            }
        }
        $rowStr.="<tr>
                        <td>$Sr</td>
                        <td>$sngArr[0]</td>
                        <td>$iconFeature</td>
                        <td align='center' style='text-align='center'><img src='$iconURL' title='$iconFeature' alt='$iconFeature' /></td>
                        <td align='center' style='text-align='center'><img src='$largeIconURL' title='$iconFeature' alt='$iconFeature' /></td>
                        <td>$tooltip_msg</td>
                    </tr>";
    }
?>
    <table border='1' width='80%'>
        <tr>
            <td>Sr. No</td>
            <td>Feature Slug</td>
            <td>Feature</td>
            <td>Icon (30X30)</td>
            <td>Icon (120X120)</td>
            <td>Tooltip</td>
        </tr>
        <?php echo $rowStr;?>
    </table>
</body>
</html>