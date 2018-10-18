<?php

/**
 * Login changes
 */
add_action('login_head', 'aitEasyLoginHead',9);
function aitEasyLoginHead() {
	echo '<style>';
	include_once dirname(__FILE__) . '/easy-admin-login.css';
	echo '</style>';
}
add_filter('login_headerurl', 'aitEasyLoginUrl');
function aitEasyLoginUrl($url) {
	return home_url();
}
add_filter('login_headertitle', 'aitEasyLoginTitle');
function aitEasyLoginTitle($title) {
	if (!empty($GLOBALS['aitBrandingOptions']->branding->loginScreenLogoTooltip)) {
		return $GLOBALS['aitBrandingOptions']->branding->loginScreenLogoTooltip;
	} else {
		return '';
	}
}

/**
 * Login redirect
 */
add_filter('login_redirect', 'aitEasyAdminRedirect',10,3);
function aitEasyAdminRedirect($redirectTo, $request, $user) {
	if( isset( $user->roles ) && is_array( $user->roles ) && ( in_array('subscriber', $user->roles) || in_array('directory_1', $user->roles) || in_array('directory_2', $user->roles) || in_array('directory_3', $user->roles) || in_array('directory_4', $user->roles) || in_array('directory_5', $user->roles) ) ) {
		if (in_array('subscriber', $user->roles)) {
			return admin_url('profile.php');
		} else {
			return admin_url('edit.php?post_type=ait-dir-item');
		}
	}
	return $redirectTo;
}

global $current_user;
if( isset( $current_user->roles ) && is_array( $current_user->roles ) && ( in_array('subscriber', $current_user->roles) || in_array('directory_1', $current_user->roles) || in_array('directory_2', $current_user->roles) || in_array('directory_3', $current_user->roles) || in_array('directory_4', $current_user->roles) || in_array('directory_5', $current_user->roles) ) ) {

	// hide from frontend
	show_admin_bar( false );

	// admin
	if (is_admin()) {

		/**
		 * Hide admin bar from admin and frontend
		 */
		// hide from backend
		function aitEasyAdminDisableAdminBar() {
			echo '<style>#wpadminbar {display:none;} html.wp-toolbar { padding-top: 0px !important; }</style>';
		}
		add_filter('admin_head','aitEasyAdminDisableAdminBar');

		/**
		 * Dashboard redirect
		 */
		if ($pagenow == 'index.php') {
			if (in_array('subscriber', $current_user->roles)) {
				wp_redirect( admin_url('profile.php') );
			} else {
				wp_redirect( admin_url('edit.php?post_type=ait-dir-item') );
			}
			exit();
		}

		/**
		 * Easy admin head
		 */
		add_action('admin_head', 'aitEasyAdminHead');
		function aitEasyAdminHead() {

			// frontend styles
			echo '<link id="ait-style" rel="stylesheet" type="text/css" media="all" href="'.WpLatteFunctions::lessify().'">';
			// frontend javascript
			// echo '<script>';
			// include_once dirname(__FILE__) . '/easy-admin.js';
			// echo '</script>';

			echo '<style>';
			include_once dirname(__FILE__) . '/easy-admin.css';
			echo '</style>';

			$screen = get_current_screen();
			if ($screen->base == 'profile') {
				echo '<script>';
				include_once dirname(__FILE__) . '/easy-admin-profile.js';
				echo '</script>';
			}

		}

		/**
		 * Prepare variables for templates
		 */
		WpLatte::$cacheDir = realpath(AIT_CACHE_DIR);
		WpLatte::$templatesDir = realpath(AIT_TEMPLATES_DIR);
		$GLOBALS['latteParams'] = array(
			'themeUrl' => THEME_URL,
			'homeUrl' =>  home_url('/'),
			'themeOptions' => $GLOBALS['aitThemeOptions']
		);

		/**
		 * Easy admin header branding
		 */
		add_action('in_admin_header', 'aitEasyAdminBrandingHeader',1);
		function aitEasyAdminBrandingHeader() {
			echo '<div id="ait-easy-admin-branding-header" class="ait-easy-admin-branding-header">';
			WPLatte::createTemplate(THEME_DIR.'/Templates/snippets/branding-header.php', $GLOBALS['latteParams'], true)->render();
			echo '</div>';
		}

		/**
		 * Easy admin header
		 */
		// add_action('in_admin_header', 'aitEasyAdminHeader',2);
		// function aitEasyAdminHeader() {
		// 	include_once dirname(__FILE__) . '/easy-admin-header.php';
		// }

		/**
		 * Easy admin footer
		 */
		add_action('admin_footer', 'aitEasyAdminFooter',1);
		function aitEasyAdminFooter() {
			include_once dirname(__FILE__) . '/easy-admin-footer.php';
		}

		/**
		 * Easy admin footer branding
		 */
		add_action('admin_footer', 'aitEasyAdminBrandingFooter',2);
		function aitEasyAdminBrandingFooter() {
			echo '<div id="ait-easy-admin-branding-footer" class="ait-easy-admin-branding-footer">';
			WPLatte::createTemplate(THEME_DIR.'/Templates/snippets/branding-footer.php', $GLOBALS['latteParams'], true)->render();
			echo '</div>';
		}

		/**
		 * Profile page add directory form
		 */
		add_action('profile_personal_options', 'aitShowDirectoryAccountPage');
		function aitShowDirectoryAccountPage() {
			aitRenderDirectoryAccountPage();
		}

	}

}
