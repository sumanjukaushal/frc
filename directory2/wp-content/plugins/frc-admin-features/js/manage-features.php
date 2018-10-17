<script src='https://freerangecamping.co.nz/plus/wp-content/jquery-ui.js'></script>

<script type='text/javascript'>
    var prepend = 'ait-_ait-item-';
    var append = '-option';
    //=== /home/freerang/public_html/directory/wp-admin
    //https://freerangecamping.co.nz/plus/wp-content/plugins/frc-admin-features/design/js/admin.js
    Array.prototype.diff = function(a) {
        //return this.filter(function(i) {return a.indexOf(i) < 0;});
        return this.filter(function(i) {return !(a.indexOf(i) > -1);});
    };
</script>
    <?php include('package-features.php');?>
<script type='text/javascript'>
    var prepend = 'ait-_ait-item-';//'ait-_ait-dir-item-';
    function setHash(hash){
        update_hidden_attribute(hash);//_ait-item_item-data[directoryType]
        //jQuery("h3:contains('Opening Hours')" ).hide();
        jQuery( "#find-address-button" ).show();
        switch(hash){
            case 'directory_1':
                //alert('hi1');
                filter_options(directory_1,'directory_1');
                break;
            case 'directory_2':
                filter_options(directory_2,'directory_2');
                jQuery("h3:contains('Opening Hours')" ).show();
                break;
            case 'directory_3':
                filter_options(directory_3,'directory_3');
                break;
            case 'directory_4':
                filter_options(directory_4,'directory_4');
                break;
            case 'directory_5':
                filter_options(directory_5,'directory_5');
                jQuery("#ait-_ait-item-largeSizeMotohomeAccess-option").show();
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_5_street_view,track:true});
                jQuery('#ait-_ait-item-address-option').hide();
                jQuery('#ait-map-option').hide();
                jQuery('#ait-_ait-item-gpsLongitude-option').hide();
                jQuery('#ait-_ait-item-gpsLatitude-option').hide();
                jQuery('#ait-_ait-item-showStreetview-option').hide();
                jQuery('#find-address-button' ).hide();
                break;
            case 'directory_6':
                //alert('hi6');
                filter_options(directory_6,'directory_6');
                jQuery( "#find-address-button" ).hide();
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_6_street_view,track:true});
                break;
            case 'directory_7':
                filter_options(directory_7,'directory_7');
                jQuery("#tooltip_show_streetview" ).tooltip({content: directory_7_street_view,track:true});
                break;
            default:
                filter_options(directory_1,'directory_1');
        }
    }
    function $$(selector, context){
        return jQuery(selector.replace(/(\[|\])/g, '\\$1'),context)
    }
    //selector = selector.replace(/(\[|\])/g, '\$1')
    function update_hidden_attribute(activeDirectory){
        console.log("Active Tab"+activeDirectory);
        //alert(activeDirectory);_ait-item_item-data[directoryType]
        var hiddenVal = jQuery('[name="_ait-item_item-data\\[directoryType\\]"]').val();
        console.log("Hidden directoryType"+activeDirectory);
        jQuery('[name="_ait-item_item-data\\[directoryType\\]"]').val(activeDirectory);
        var hiddenVal = jQuery('[name="_ait-item_item-data\\[directoryType\\]"]').val();
        console.log("Hidden directoryType after update"+activeDirectory);
    }
    
    function filter_options(directory_arr, active_dir) {
        //****Start of changing active tab onClick****
        var tabArr = ['directory_1','directory_2','directory_3','directory_4','directory_5','directory_6','directory_7'];
        for(index = 0; index < tabArr.length; index++) {
            if(document.getElementById(tabArr[index])){
                document.getElementById(tabArr[index]).className = '';
                document.getElementById(tabArr[index]).className = 'nav-tab';
            }            
        }
        
        document.getElementById(active_dir).className = '';
        document.getElementById(active_dir).className = 'nav-tab nav-tab-active';
        //****End of changing active tab onClick****
        
        //--------------------reverse show hide-----------------        
        hideAllOptions(active_dir); //with some exceptions for google map
        //alert(active_dir);
        var t_directory = globalArr.diff(directory_arr);
        console.log("filter_options: active_dir"+active_dir);
        for(index = 0; index < t_directory.length; index++) {
            temp = t_directory[index];//prepend+t_directory[index]+append;
            //code by resource 3
            fieldLabels = t_directory[index];//will return feature names.
            if (jQuery("label:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;})) {
                jQuery("label:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', 'block');
                jQuery("label:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', '');
            }
            if(jQuery("span:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;})){
                jQuery("span:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', 'block');
                jQuery("span:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', '');
            }
            console.log("temp:"+temp);
            if(temp == 'pet_minding_addnl'){
                pet_prepend = 'ait-opt-metabox-_ait-item_item-data-';
                pet_append = '-en_US';
                temp = pet_prepend+temp+pet_append;
                jQuery("label[for="+temp+"]").filter(function() {return jQuery(this).text() === temp;}).parent().parent().parent().parent().css('display', 'block');
                jQuery("label[for="+temp+"]").filter(function() {return jQuery(this).text() === temp;}).parent().parent().parent().parent().css('display', '');
            }
            //code by resource 3
            
            //alert(active_dir + '')
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+t_directory[index]+map_append;
            }else{
                temp = prepend+t_directory[index]+append;
            }
            //console.log('temp:::'+temp);
            if (document.getElementById(temp)) {
                document.getElementById(temp).style.display = 'block';
                document.getElementById(temp).style.display = '';
            }
        }
        
        var excluded_fields = [
                        'address',                  //1
                        'gpsLatitude',              //2
                        'gpsLongitude',             //3
                        'map',                      //4
                        ];
        
        if (active_dir == 'directory_4' || active_dir == 'directory_6') {
            ;
        }else{
            for(index = 0; index < excluded_fields.length; index++) {
                if (excluded_fields[index] == 'map') {
                    map_prepend = 'ait-';
                    map_append = '-option';
                    temp = map_prepend + excluded_fields[index] + map_append;
                }else{
                    temp = prepend + excluded_fields[index] + append;
                }
                
                if (document.getElementById(temp)) {
                   document.getElementById(temp).style.display = '';
                }
            }
        }
        //--------------------reverse show hide-----------------
    }
    
    function hideAllOptions(active_dir) {
        for(index = 0; index < globalArr.length; index++){
            //code by resource 3
            fieldLabels = globalArr[index];//will return field Names
            console.log("Hide:"+fieldLabels);
            if (jQuery("label:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;})) {
                jQuery("label:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', 'none');
            }
            if(jQuery("span:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;})){
                jQuery("span:contains("+fieldLabels+")").filter(function() {return jQuery(this).text() === fieldLabels;}).parent().parent().parent().parent().css('display', 'none');
            }
            if(fieldLabels == 'pet_minding_addnl'){
                pet_prepend = 'ait-opt-metabox-_ait-item_item-data-';
                pet_append = '-en_US';
                temp = pet_prepend+fieldLabels+pet_append;
                jQuery("label[for="+temp+"]").filter(function() {return jQuery(this).text() === temp;}).parent().parent().parent().parent().css('display', 'none');
            }
            //code by resource 3
            if (
                active_dir == 'directory_4' ||
                active_dir == 'directory_6'
            ) {
               ; 
            }
            else if(
               globalArr[index] == 'address' || //rasa
               globalArr[index] == 'gpsLatitude' || //rasa
               globalArr[index] == 'gpsLongitude' || //rasa
               globalArr[index] == 'map'
            ){
                //do not hide map and address fields if directory is other than 4 and 6 
                continue;
            }
            if (globalArr[index] == 'map') {
                map_prepend = 'ait-';
                map_append = '-option';
                temp = map_prepend+globalArr[index]+map_append;
            }else{
                temp = prepend+globalArr[index]+append;
            }
            if (document.getElementById(temp)) {
                document.getElementById(temp).style.display = 'none';
            }            
        }
    }
    
    var directory_1_address = "Place your address in this field in the following format:  Street Number (If available) Street Name, Suburb/Town and State.  Eg.  15 Smith Street, Cooroy, Qld This will allow your GPS Co-Ordinates to be automatically located by Google Maps.  In the event you do not have an exact street number, you can enter more accurate details in the GPS Section Below.";//free
    
    var  directory_1_lattitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    //same for business, free, 
    var  directory_1_longitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_1_street_view = "This field allows us to show a Street view of your property in the More Information window.  Tick this box if you wish to use this option. NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    //same for business
    
    var directory_2_address = directory_1_address;//business
    var directory_2_lattitude = directory_1_lattitude;
    var directory_2_longitude = directory_1_longitude;
    var directory_2_street_view = directory_1_street_view;
    
    var directory_3_address = directory_1_address; //lowcost
    var directory_3_lattitude = directory_1_lattitude;
    var directory_3_longitude = directory_1_longitude;
    var directory_3_street_view = directory_1_street_view;
    
    var directory_4_address = "For privacy reasons, it is important that you do NOT place your full address in this field.  Simply enter your Suburb/Town and State.  This will allow the GPS Co-Ordinates to be automatically located by Google Maps and show up in the center of the Suburb or Town and not disclose your private address to everyone."; //house sitting    
    var directory_4_lattitude = "Do not use this field for House Sitting Listings";
    var directory_4_longitude = "Do not use this field for House Sitting Listings";
    var directory_4_street_view = "Do not mark this box for House Sitting Overs Listings";
    
    var directory_5_address = directory_4_address; //Park Over
    var directory_5_lattitude = "Do not use this field for Park Overs Listings";
    var directory_5_longitude = "Do not use this field for Park Overs Listings";
    var directory_5_street_view = "Do not mark this box for Park Overs Listings";
    
    var directory_6_address = directory_4_address; //Helpout
    var directory_6_lattitude = "Do not use this field for Help Outs Listings";
    var directory_6_longitude = "Do not use this field for Help Outs Listings";
    var directory_6_street_view = "Do not mark this box for Help Outs Listings";
    
    var directory_7_address = directory_1_address; //25+
    var directory_7_lattitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_7_longitude = "Only use this field if you do not have an accurate Street address including Street Number.  This will allow us to accurately locate your address on our maps.";
    var directory_7_street_view = "This field allows us to show a Street view of your property in the More Information window. Tick this box if you wish to use this option. NOTE:  This option is dependent on data availability from Google Maps and may not be available or current in all circumstances.";
    //tinymce or content_ifr add tooltip or wp-content-editor-container for this id also.
    //jQuery( function() {jQuery( a ).tooltip();} );
    //jQuery('#tooltip_9297').tooltip({content:"NOTE8  JELL CASE CLEAR",track:true});
    //<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js-->
    jQuery('#sample_tooltip').tooltip({content:"SAMSUNGS9 PLUS JELL CASE RED",track:true});
    jQuery( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          jQuery( this ).css( position );
          jQuery( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
</script>
<style>
  .ui-tooltip, .arrow:after {
    background: black;
    border: 2px solid white;
    z-index:100000;
    opacity:1!important
  }
  .ui-tooltip {
    padding: 10px 20px;
    color: white;
    border-radius: 20px;
    font: normal 14px "Helvetica Neue", Sans-Serif;
    /*text-transform: uppercase;*/
    box-shadow: 0 0 7px black;
  }
  .arrow {
    width: 70px;
    height: 16px;
    overflow: hidden;
    position: absolute;
    left: 50%;
    margin-left: -35px;
    bottom: -16px;
  }
  .arrow.top {top: -16px;bottom: auto;}
  .arrow.left {left: 20%;}
  .arrow:after {content: "";position: absolute;left: 20px;top: -20px;width: 25px;height: 25px;box-shadow: 6px 5px 9px -9px black;-webkit-transform: rotate(45deg);-ms-transform: rotate(45deg);transform: rotate(45deg);å}
  .arrow.top:after {bottom: -20px;top: auto;}
  </style>

<?php include("show_hide_tab_categories.php");?>
<?php include("misc_tooltip.php");?>
<?php
    $wishlistPlugin = 'wishlist-member/wpm.php';//'WishList Member'
    if(is_plugin_active($wishlistPlugin)){
        global $current_user;
        $wlmObj = new WishListMember();
        $wlmUseraAddress = $wlmObj->Get_UserMeta($current_user->ID,'wpm_useraddress');
        //print_r($wlmUseraAddress);
        $userMeta =<<<USER_META
        <script>
            jQuery('#'+prepend+'wlm_city').val('{$wlmUseraAddress['city']}');
            jQuery('#'+prepend+'wlm_state').val('{$wlmUseraAddress['state']}');
            jQuery('#'+prepend+'wlm_zip').val('{$wlmUseraAddress['zip']}');            
            jQuery('#'+prepend+'address').val('{$wlmUseraAddress['address1']}');
        </script>
USER_META;
        $postID = 0;
        if(array_key_exists('post',$_REQUEST)){
            $postID = (int)$_REQUEST['post'];
        }
        if(!$postID){echo $userMeta;}
    }
?>