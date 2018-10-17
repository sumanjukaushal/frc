<!--link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet"-->
<!--script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script-->
<?php
    global $current_user;
    get_currentuserinfo();
    $userRolesArr = $current_user->roles;
    global $post_type;
    
    if($post_type == 'ait-item'){
        if(in_array('administrator',$userRolesArr)){
            include("campaign_script.php");
            $currentTheme = wp_get_theme();
            if("Directory" == $currentTheme){
               
            }else{
                echo "<!--rasa $currentTheme do not exists -->";
            }
        }else{
            include("campaign_user_script.php");
        }
    }
?>  
<!-- custom code -->
<div style='display:none'><?php
    //print_r($userRolesArr);
    function frc_update_user_role(){
        global $current_user;
        $userRolesArr = $current_user->roles;
        if(in_array('administrator',$userRolesArr )){
            return;
        }
        $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
        if(is_plugin_active($wishlistPlugin)){
            $roleArr = array('directory_1', 'directory_2', 'directory_3', 'directory_4', 'directory_5', 'directory_6', 'directory_7',);
            $directoryRoleExist = false;
            foreach($userRolesArr as $userRole){
                if(in_array($userRole, $roleArr)){
                    $directoryRoleExist = true;
                    break;
                }
            }
            if(!$directoryRoleExist){
                //if all 7 directory role are missing, assigning role equal to wlm level to user
                $directoryRolesArr = array(
                                    'Free Listings' => 'directory_1',
                                    'Businesses Listings' => 'directory_2',
                                    'Low Cost Sites' => 'directory_3',
                                    'Low Cost Camps' => 'directory_3',
                                    'House Sitting' => 'directory_4',
                                    'Park Overs' => 'directory_5',
                                    'Help Outs' => 'directory_6',
                                    '$25 +' => 'directory_7',
                                    '25plus' => 'directory_7',
                                    'Caravan Parks' => 'directory_7',
                                );
                //check if user have any of directory levels assigned.
                $wlmObj = new WishListMember();
                $levels = WLMAPI::GetUserLevels($current_user->ID);
                if('27.255.161.112' == $_SERVER["REMOTE_ADDR"]){echo "<pre>";print_r($levels);echo "</pre>";}
                $wlmLevelExist = false;
                $directoryLevel = '';
                
                foreach($levels as $level){
                    if(array_key_exists($level, $directoryRolesArr)){
                        $directoryLevel = $directoryRolesArr[$level];
                        $wlmLevelExist = true;
                        break;
                    }
                }
                
                //if user have WLM levels assigned but no respective role assigned
                if($wlmLevelExist){
                    $current_user->remove_role( 'subscriber' );
                    $current_user->add_role( $directoryLevel );
                    wp_redirect( admin_url('post-new.php?post_type=ait-dir') );
                    //echo "Assigning role = $directoryLevel to user with id=".$current_user->ID;
                }//print_r($userRolesArr);
            }
        }
    }
    frc_update_user_role();
?></div>
<!-- /custom code 123-->
<?php
    global $post_type;
    $frcTabs = frc_get_current_tabs();
    $tabArr = $frcTabs[0];
    $firstTab = $frcTabs[1];
    //echo "<pre>";global $tabArr;print_r($tabArr);echo "</pre>";?>
<script>
var firstTab = 'tabdirectory_1';
<?php
    if(is_array($tabArr)){
        if(empty($firstTab))foreach($tabArr as $firstTab => $value){break;}
        switch($firstTab){
            case 'directory_1':
                echo "firstTab = 'tabdirectory_1';";
                break;
            case 'directory_2':
                echo "firstTab = 'tabdirectory_2';";
                break;
            case 'directory_3':
                echo "firstTab = 'tabdirectory_3';";
                break;
            case 'directory_4':
                echo "firstTab = 'tabdirectory_4';";
                break;
            case 'directory_5':
                echo "firstTab = 'tabdirectory_5';";
                break;
            case 'directory_6':
                echo "firstTab = 'tabdirectory_6';";
                break;
            case 'directory_7':
                echo "firstTab = 'tabdirectory_7';";
                break;            
        }
    //echo "alert('$firstTab');";
    }
?>
    function getHash(){
        //alert(firstTab);
        console.log("first tab:"+firstTab);
        //jQuery("h3:contains('Opening Hours')" ).hide();
        var hash = window.location.hash.substring(1);
        //console.log(hash);
        if(hash == ""){hash = firstTab;}
        //console.log(hash);
        switch (hash) {          
            case 'tabdirectory_1':
                jQuery( "#directory_1" ).trigger( "click" );                
                break;
            case 'tabdirectory_2':
                jQuery("h3:contains('Opening Hours')" ).show();
                jQuery( "#directory_2" ).trigger( "click" );                
                break;
            case 'tabdirectory_3':
                jQuery( "#directory_3" ).trigger( "click" );
                break;
            case 'tabdirectory_4':
                jQuery( "#directory_4" ).trigger( "click" );
                break;
            case 'tabdirectory_5':
                jQuery( "#directory_5" ).trigger( "click" );
                break;
            case 'tabdirectory_6':
                jQuery( "#directory_6" ).trigger( "click" );
                break;
            case 'tabdirectory_7':
                jQuery( "#directory_7" ).trigger( "click" );
                break;
            default:
                jQuery( "#directory_1" ).trigger( "click" );
        }
        //alert(hash);
        //return false;
    }
    getHash(<?php echo $firstTab;?>);
    jQuery( document ).ready(function() {
        if(firstTab == 'tabdirectory_4' || firstTab == 'tabdirectory_6'){
            jQuery( "#ait-map-option" ).hide();
            jQuery( "#find-address-button" ).hide();
        }  
    });
    <?php
    //echo $currentDirectory;die;
    if(isset($currentDirectory) && !empty($currentDirectory) ){
        //Note: We are setting this variable in Metabox.inc
        //(Path: \themes\directory\AIT\Framework\Libs\WPAlchemy) function textControl
        //echo "setHash('$currentDirectory');";
        echo "jQuery('#$currentDirectory').trigger( \"click\" );";
    }
     ?>
</script>
<?php
    $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'    
    if(is_plugin_active($wishlistPlugin)){
        global $current_user;
        $wlmObj = new WishListMember();
        $wlmUseraAddress = $wlmObj->Get_UserMeta($current_user->ID,'wpm_useraddress');
        $userMeta =<<<USER_META
        <script>
            var frc_prepend = "ait-opt-metabox-_ait-item_item-data-";
            var frc_append = '-en_US';
            jQuery('#'+frc_prepend+'wlm_city'+frc_append).val('{$wlmUseraAddress['city']}');
            jQuery('#'+frc_prepend+'wlm_state'+frc_append).val('{$wlmUseraAddress['state']}');
            jQuery('#'+frc_prepend+'wlm_zip'+frc_append).val('{$wlmUseraAddress['zip']}');            
            jQuery('#'+frc_prepend+'address'+frc_append).val('{$wlmUseraAddress['address1']}');
        </script>
USER_META;
        $postID = 0;
        if(array_key_exists('post',$_REQUEST)){
            $postID = (int)$_REQUEST['post'];
        }
        if(!$postID){echo $userMeta;}
        
    }
?>
</script>