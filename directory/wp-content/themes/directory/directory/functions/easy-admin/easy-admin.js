$j = jQuery.noConflict();

$j(document).ready(function() {
	
	// SubmenuClass();
	// if(!isResponsive()) {
	// 	CustomizeMenu();
	// 	RollUpMenu();
	// }

	// FloatAdminPublishBox();
	
});

function FloatAdminPublishBox () {
	var submitBox = $j('#postbox-container-1 #side-sortables');

	if (submitBox.length) {
		var submitInitTop = submitBox.offset().top;
		submitBox.css('z-index', '100');
		$j(window).scroll(function(event){
			if ($j(window).scrollTop() > submitInitTop) {
				submitBox.css('position', 'fixed').css('top', '0px');//.css('right', '0px');;
			} else {
				submitBox.css('position', 'relative');
			}
		});
	}
}

function SubmenuClass() {
	var menuLinks = $j('nav.mainmenu a');
	$j(menuLinks).each(function () {
		if($j(this).next().is('ul')){
			$j(this).addClass('has-submenu');
		}
	});
}

function RollUpMenu() {
	$j("nav.mainmenu ul li").hover(function(){
		var submenu = $j(this).find('> ul');
		var submenuHeight = submenu.innerHeight();
		submenu.show().height(0).stop(true,true).animate({
			height: submenuHeight
		});
	}, function(){
		$j(this).find('> ul').hide();
	});
}

function CustomizeMenu() {
	$j(".mainmenu > ul > li").each(function(){
		if($j(this).has('ul').length){
			$j(this).addClass("parent");
		}
	});
}

function isResponsive(){
	result = false;
	if($j(window).width() <= 497){
		result = true;
	}
	return result;
}