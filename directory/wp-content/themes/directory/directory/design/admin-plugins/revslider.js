jQuery(document).ready(function() {
    chooseDisplay(jQuery("select#ait-sliderType").val());

    jQuery("select#ait-sliderType").click(function(){
        chooseDisplay(jQuery("select#ait-sliderType").val());
    });
});

function chooseDisplay(value){
    switch(value){
        case "anything":
	        hideMetabox('#ait-sliderAliases-option');
	        hideMetabox('#ait-sliderAlternative-option');
	        showMetabox('#ait-sliderCategory-option');
            showMetabox('#ait-sliderHeight-option');
        break;
        case "revolution":
	        showMetabox('#ait-sliderAliases-option');
	        showMetabox('#ait-sliderAlternative-option');
	        hideMetabox('#ait-sliderCategory-option');
            hideMetabox('#ait-sliderHeight-option');
        break;
    }
} 

function hideMetabox(id){
    jQuery(id).hide();
}

function showMetabox(id){
    jQuery(id).show();
}