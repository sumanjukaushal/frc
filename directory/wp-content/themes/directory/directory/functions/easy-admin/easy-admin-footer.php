<?php $screen = get_current_screen(); ?>
<div id="ait-easy-admin-footer" class="ait-easy-admin-footer">
	<?php if ( $screen->base == 'edit' && $screen->post_type == 'ait-dir-item' ) { ?>
	<a href="<?php echo admin_url('post-new.php?post_type=ait-dir-item'); ?>" class="add-item button button-primary">
		<?php _e('Add new item','ait'); ?>
	</a>
	<?php } ?>
</div>