<?php
    echo $nav = <<<NAV
    <style>
    legend {
        display: block;
        padding-left: 2px;
        padding-right: 2px;
        /*border: 1px;;*/
        color: #999999;
        background-color: #333333;
    } 
    </style>
    <fieldset style='width:80%'>
    <legend>Plus Navigation:</legend>
    <table style='padding-left:20px;'>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/feature_admin.php'>Manage Theme Features</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_theme_admin/manage_packages.php'>Add Update Package(s)</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_theme_admin/transfer_pkg_features.php'>Transfer Pkg Features</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_theme_admin/grab_admin_features.php'>Grab Item Extension Feature(s)</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_theme_admin/grab_packages.php'>Grab Packages</a></td>
        </tr>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/feature-array.php'>Generate Feature Array</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/feature-array2.php'>Generate Feature Array(v2)</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/feature-array3.php'>Generate Feature Array(v3)</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/feature_n_icons.php'>Serialized icon n Features</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/rate_n_review_opts.php'>Modify Rating n Review Options</a></td>
        </tr>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_theme_admin/manage_uploads.php'>Manage Item Uploads</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_duplicate_geos/index.php'>Duplicate Geos</a></td>
        </tr>
    </table>
    </fieldset>
NAV;
?>