<?php
require_once("../../../wp-config.php");
require_once('../../../wp-load.php');
$url = WP_CONTENT_URL.'/frc-admin/frc_misc/assign-author.php';
?>
<div class="table-responsive" style="padding-left:20px;padding-top:5px;">
    <table class="table-bordered table-hover" style="width:80%; padding-top:5px;" style='width:80%'>
        <tr><td style="padding-left: 10px;"><a href= 'post-authors.php' >List Item Authors</a> | <a href= "<?php echo $url; ?>" >Update Authors</a><br><br></td>
        </tr></table><br/></div>