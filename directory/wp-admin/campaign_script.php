<?php
    ob_start();
    $postID = 0;
    if(array_key_exists('post',$_REQUEST)){
        $postID = (int)$_REQUEST['post'];
    }
    include("directory_attributes.php");
?>
<script type="text/javascript">    
    var prepend = 'ait-_ait-dir-item-';
    var append = '-option';
    //#ait-_ait-dir-item-directoryType-option
    if (document.getElementById(prepend+'Hidden Flags'+append)) {
        document.getElementById(prepend+'Hidden Flags'+append).style.display = 'none';
        document.getElementById(prepend+'directoryType'+append).style.display = 'none';
        if (document.getElementById(prepend+'directoryType').value == "") {
            document.getElementById(prepend+'directoryType').value = '<?php echo $userRolesArr[0];?>';
        }        
    }
    
    if (document.getElementById('ait-custom-fields-5')) {
        document.getElementById('ait-custom-fields-5').style.display = 'none';
        document.getElementById(prepend+'directoryType'+append).style.display = 'none';
        if (document.getElementById(prepend+'directoryType').value == "") {
            document.getElementById(prepend+'directoryType').value = '<?php echo $userRolesArr[0];?>';
        }        
    }
    
    if (document.getElementById('ait-custom-fields-4')) {
        document.getElementById('ait-custom-fields-4').style.display = 'none';
    }
<?php
    echo $js = ob_get_clean();
    global $current_user;
    get_currentuserinfo();
    $userRolesArr = $current_user->roles;
    
    if(in_array('directory_1',$userRolesArr)){  ?>
        showAllOptions();
        for(index = 0; index < directory_1.length; index++) {
            if (directory_1[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                dynamicID = map_prepend+directory_1[index]+map_append;
            }else{
                dynamicID = prepend+directory_1[index]+append;
            }              
            if (document.getElementById(dynamicID)) {
                document.getElementById(dynamicID).style.display = 'none';
            }         
        }
<?php   }elseif(in_array('directory_2',$userRolesArr)){ ?>
            showAllOptions();
            for	(index = 0; index < directory_2.length; index++) {
                if (directory_2[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_2[index]+map_append;
                }else{
                    dynamicID = prepend+directory_2[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }                
            }
<?php   }elseif(in_array('directory_3',$userRolesArr)){ ?>
            for	(index = 0; index < directory_3.length; index++) {
                if (directory_3[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_3[index]+map_append;
                }else{
                    dynamicID = prepend+directory_3[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }elseif(in_array('directory_4',$userRolesArr)){ ?>
            for	(index = 0; index < directory_4.length; index++) {
                if (directory_4[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_4[index]+map_append;
                }else{
                    dynamicID = prepend+directory_4[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }elseif(in_array('directory_5',$userRolesArr)){ ?>
            for	(index = 0; index < directory_5.length; index++) {
                if (directory_5[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_5[index]+map_append;
                }else{
                    dynamicID = prepend+directory_5[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }elseif(in_array('directory_6',$userRolesArr)){ ?>
            for	(index = 0; index < directory_6.length; index++) {
                if (directory_6[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_6[index]+map_append;
                }else{
                    dynamicID = prepend+directory_6[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }elseif(in_array('directory_7',$userRolesArr)){ ?>
            for	(index = 0; index < directory_7.length; index++) {
                if (directory_7[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_7[index]+map_append;
                }else{
                    dynamicID = prepend+directory_7[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }else{  ?>
            for	(index = 0; index < directory_1.length; index++) {
                if (directory_1[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    dynamicID = map_prepend+directory_1[index]+map_append;
                }else{
                    dynamicID = prepend+directory_1[index]+append;
                }
                if (document.getElementById(dynamicID)) {
                    document.getElementById(dynamicID).style.display = 'none';
                }         
            }
<?php   }
?>
    jQuery("h3:contains('Hidden Flags')" ).hide();
    //jQuery("h3:contains('Opening Hours')" ).show();
    jQuery("h3:contains('Business Features')" ).hide();
    jQuery("h3:contains('Alternative Content')" ).hide();
    
    //jQuery("#"+usesField).attr('size',5);
    var prepend = 'ait-_ait-dir-item-'; //ait-_ait-dir-item-per_month_uses
    var usesField = prepend+'per_month_uses';
    jQuery("#"+usesField).css('width','7%');    
    var helpText = "<span> e.g:5 This will mean they can only use the card 5 times per calendar month in that location or store.</span>";
    jQuery( helpText ).insertAfter("#"+usesField);
</script>    
<?php
  include("tooltip.php");  
?>    