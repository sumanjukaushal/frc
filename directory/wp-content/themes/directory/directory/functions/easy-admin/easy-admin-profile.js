jQuery(document).ready(function($) {
	
	var form = $('#your-profile'),
		submits = form.find('input[type="submit"]');

	// redirect form when user want to upgrade directory account
	submits.click(function(event) {
		if ($(this).attr('name') == 'user-submit') {
			form.attr('action','<?php echo admin_url('profile.php/?dir-register=upgrade'); ?>');
			// form.trigger('submit');
			// return false;	
		}
	});

	// rename profile to Account
	form.parent().find('> h2').text('<?php _e("Account","ait"); ?>');

	// first show entire form
	$('#wpbody-content').show();
	
	// Hide h3 titles
	form.find('h3').hide();
	
	// Hide personal info form
	form.find('> table').eq(0).hide();
	
	// Hide some contact info inputs
	// Website
	form.find('> table').eq(3).find('tr').eq(1).hide();
	// AIM
	form.find('> table').eq(3).find('tr').eq(2).hide();
	// Yahoo IM
	form.find('> table').eq(3).find('tr').eq(3).hide();
	// Jabber / Google Talk
	form.find('> table').eq(3).find('tr').eq(4).hide();

	// Hide some about yourself inputs 
	form.find('> table').eq(4).find('tr').eq(0).hide();

});