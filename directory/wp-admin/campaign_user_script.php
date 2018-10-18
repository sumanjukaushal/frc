<?php
    ob_start();
    $postID = 0;
    if(array_key_exists('post',$_REQUEST)){
        $postID = (int)$_REQUEST['post'];
    }
    include("directory_attributes.php");
    global $tabArr;    global $current_user;
    get_currentuserinfo();
    $userRolesArr = $current_user->roles;
    //declared in /wp-admin/edit-form-advanced.php
    //print_r($tabArr);
    reset($tabArr);
    foreach ($tabArr as $firstDir => $firstTab) break;
?>
<script type="text/javascript">
    var prepend = 'ait-_ait-dir-item-';
    var append = '-option';
<?php
    echo "var first_active_dir = '$firstDir';\n";
    echo "var first_active_tab = '$firstTab';\n";
?>
    if (document.getElementById(prepend+'Hidden Flags'+append)) { //ait-_ait-dir-item-Hidden Flags-option
        document.getElementById(prepend+'Hidden Flags'+append).style.display = 'none';
        document.getElementById(prepend+'directoryType'+append).style.display = 'none';
        document.getElementById(prepend+'directoryType').value = '<?php echo $userRolesArr[0];?>';
    }
<?php
    echo "/* user_script".$userRolesArr[0];echo $firstDir."*/";
    
    if($userRolesArr[0] != $firstDir){
        //show attribute of first tab
        echo "/* $userRolesArr[0]  $firstDir */\n";
        if($postID != 0){ 
            echo "setHash('{$firstDir}');\n";
        }
    }
?>    
    jQuery("h3:contains('Hidden Flags')" ).hide();
    //jQuery("h3:contains('Opening Hours')" ).show();
    jQuery("h3:contains('Alternative Content')" ).hide();
    
    jQuery("#ait-_ait-dir-item-location-option").hide();
    jQuery("#ait-_ait-dir-item-access-option").hide();
    
    //jQuery("label[for='ait-_ait-dir-item-location']").hide();
    //jQuery("#tooltip_location").hide();
    //jQuery("#ait-_ait-dir-item-location").hide();
    
    var prepend = 'ait-_ait-dir-item-'; //ait-_ait-dir-item-per_month_uses
    var usesField = prepend+'per_month_uses';
    jQuery("#"+usesField).css('width','7%');    
    var helpText = "<span> e.g:5 This will mean they can only use the card 5 times per calendar month in that location or store.</span>";
    jQuery( helpText ).insertAfter("#"+usesField);
<?php
    if(!$postID){
        if($firstTab == 'Business Listing'){
            echo "jQuery(\"h3:contains('Opening Hours')\" ).show();";
        }    
        echo "jQuery( \"#{$firstDir}\" ).trigger( \"click\" );";
    }else{
        //if user would have multiple registration, our code will pick first from tab
        if(!empty($currentDirectory)){
            echo "jQuery('#{$currentDirectory}').trigger( \"click\" );";
        }else{
            echo "jQuery('#{$userRolesArr[0]}').trigger( \"click\" );";        
            echo "setHash('{$userRolesArr[0]}');";
        }
    }
    echo "/*** currentDirectory = {$currentDirectory} **/";
    if($postID && $currentDirectory == 'directory_7'){
        echo 'jQuery("#ait-_ait-dir-item-discount-option").show();';
        echo 'jQuery("#ait-_ait-dir-item-discount_detail-option").show();';
        echo 'jQuery("#ait-_ait-dir-item-offer-option").show();';
        echo 'jQuery("#ait-_ait-dir-item-offer_detail-option").show();';
        echo 'jQuery("#ait-_ait-dir-item-emergencyPhone-option").show();';
        
        echo 'jQuery("#ait-_ait-dir-item-pet_minding_required-option").hide();';
        echo 'jQuery("#ait-_ait-dir-item-pet_minding_addnl-option").hide();';
        echo 'jQuery("#ait-_ait-dir-item-police_check_required-option").hide();';
        //echo "jQuery('#$currentDirectory').trigger( \"click\" );";
    }elseif(($postID && $currentDirectory == 'directory_5') || (!$postID && $firstTab == 'Park Overs')){
        echo 'jQuery("#ait-_ait-dir-item-largeSizeMotohomeAccess-option").show();';
    }elseif(
            ($postID && $currentDirectory == 'directory_3') ||
            (!$postID && $firstTab == 'Low Cost Sites') ||
            (!$postID && $firstTab == 'Campgrounds')
            ){
        echo 'jQuery("#ait-_ait-dir-item-emergencyPhone-option").show();';
        //Note: still problem if Low cost sites is not first tab for multiple registration  
    }
?>    
</script>
<?php   
    echo $js = ob_get_clean();
    include("tooltip.php");
?>
