<?php
function directory_register( $atts ) {
	$atts = extract( shortcode_atts( array( 'id'=>'ait-dir-register-shortcode' ),$atts ) );
	ob_start();
	?>
	<!-- register -->
	<div id="<?php echo $id; ?>">
		<form method="post" action="<?php echo home_url('/?dir-register=register'); ?>" class="wp-user-form">
			<div class="register-username">
				<label for="user_login"><?php _e('Username','ait'); ?> </label>
				<input type="text" name="user_login" value="" size="20" id="user_login_register_shortcode" tabindex="101" />
			</div>
			<div class="register-email">
				<label for="user_email"><?php _e('Email','ait'); ?> </label>
				<input type="text" name="user_email" value="" size="25" id="user_email_register_shortcode" tabindex="102" />
			</div>
			<div class="register-role">
				<label for="directory-role"><?php _e('Package','ait'); ?> </label>
				<select name="directory-role">
				<?php
				global $aitThemeOptions;
				$currency = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
				for ($i=1; $i <= 5; $i++) {
					$roleEnable = 'role'.$i.'Enable';
					$roleName = 'role'.$i.'Name';
					$rolePrice = 'role'.$i.'Price';
					if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
						$periodName = 'role'.$i.'Period';
						$rolePeriod = __('year','ait');
						switch ($aitThemeOptions->members->$periodName) {
							case 'Year':
								$rolePeriod = __('year','ait');
								break;
							case 'Month':
								$rolePeriod = __('month','ait');
								break;
							case 'Week':
								$rolePeriod = __('week','ait');
								break;
							case 'Day':
								$rolePeriod = __('day','ait');
								break;
						}
					}
					$free = (trim($aitThemeOptions->members->$rolePrice) == '0') ? true : false;
					if(isset($aitThemeOptions->members->$roleEnable)){
						echo '<option value="directory_'.$i.'"'; if($free) { echo ' class="free"'; } echo '>'.$aitThemeOptions->members->$roleName;
						if(!$free) {
							if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
								echo ' - '.trim($aitThemeOptions->members->$rolePrice).' '.$currency.' '.__('per','ait').' '.$rolePeriod;
							} else {
								echo ' ('.$aitThemeOptions->members->$rolePrice.' '.$currency.')';
							}
						} else {
							echo ' ('.__('Free','ait').')';
						}
						echo '</option>';
					}
				}
				?>
				</select>
			</div>
			<div class="login-fields">
				<?php do_action('register_form'); ?>
				<input type="submit" name="user-submit" value="<?php _e('Sign up!', 'ait'); ?>" class="user-submit" tabindex="103" />
				<input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>" />
				<input type="hidden" name="user-cookie" value="1" />
			</div>
		</form>
	</div>
	<script>
	jQuery(document).ready(function($) {
		var tabRegisterShortcode = $('#<?php echo $id; ?>'),
			selectShortcode = tabRegisterShortcode.find('select[name=directory-role]'),
			buttonSubmitShortcode = tabRegisterShortcode.find('input[name=user-submit]'),
			freeTitleShortcode = '<?php _e('Sign up','ait'); ?>',
			buyTitleShortcode = '<?php _e('Buy with PayPal','ait'); ?>';
		if(selectShortcode.find('option:selected').hasClass('free')){
			buttonSubmitShortcode.val(freeTitleShortcode);
		} else {
			buttonSubmitShortcode.val(buyTitleShortcode);
		}
		selectShortcode.change(function(event) {
			if(selectShortcode.find('option:selected').hasClass('free')){
				buttonSubmitShortcode.val(freeTitleShortcode);
			} else {
				buttonSubmitShortcode.val(buyTitleShortcode);
			}
		});
	});
	</script>
	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}
add_shortcode( "directory_register", "directory_register" );
