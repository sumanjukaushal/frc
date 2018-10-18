<?php $screen = get_current_screen(); ?>
<div id="ait-easy-admin-header" class="ait-easy-admin-header">
	<div class="buttons ait-easy-admin-wrap clearfix">
		<a href="<?php echo admin_url('edit.php?post_type=ait-dir-item'); ?>" class="button button<?php if ( ($screen->base == 'edit' && $screen->post_type == 'ait-dir-item') || ($screen->base == 'post' && $screen->post_type == 'ait-dir-item') ) echo '-primary'; ?>">
			<?php _e('My Items','ait'); ?>
		</a>
		<a href="<?php echo admin_url('edit.php?post_type=ait-rating'); ?>" class="button button<?php if ($screen->base == 'edit' && $screen->post_type == 'ait-rating') echo '-primary'; ?>">
			<?php _e('Ratings','ait'); ?>
		</a>
		<a href="<?php echo admin_url('profile.php'); ?>" class="button button<?php if ($screen->base == 'profile') echo '-primary'; ?>">
			<?php _e('Account','ait'); ?>
		</a>
	</div>
</div>