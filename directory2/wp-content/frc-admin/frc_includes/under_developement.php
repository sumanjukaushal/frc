<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> 
<?php
    echo $nav = <<<NAV
    <style>
    legend {
        display: block;
        padding-left: 2px;
        padding-right: 2px;
        /*border: 1px;;*/
        color: #999999;
    }
    </style>
    <fieldset style='width:80%'>
    <legend style='background-color: yellow;'>For Developers:</legend><div class='headers' style='text-align: right;color: blue;font-size: 80%;margin-top: -35px;'><span>Expand</span></div>
    <div class='content' style='display:none'>
        <table style="margin-left: 20px;margin-top: 20px;margin-bottom: 20px;">
            <tr>
                <td><a href='{$contentURL}/frc_misc/csv_export.php'>Export Items</a></td>
                <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
                <td><a href='{$contentURL}/frc_postmeta/items_without_features.php'>Items (without features)</a></td>
                <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
                <td><a href=''>3</a></td>
                <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
                <td><a href=''>4</a></td>
                <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
                <td><a href=''>5</a></td>
            </tr>
        </table>
    </div>
    <table style='padding-left:20px;margin-top: 20px;margin-bottom: 20px;'>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_utilities/role_utility.php'>Role Array</a></td>
        </tr>
    </table>
    </fieldset>
NAV;
?>
<script>
    $(".headers").click(function () {

    $header = $(this);
    
    $content = $header.next();
    
    $content.slideToggle(500, function () {
        
    $header.text(function () {
        return $content.is(":visible") ? "Collapse" : "Expand";
    });
    });

});
</script>