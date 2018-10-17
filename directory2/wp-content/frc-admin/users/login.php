<?php
    require('../../../wp-load.php' );
	$url = site_url().DS."wp-admin/load-styles.php?c=0&amp;dir=ltr&amp;load%5B%5D=dashicons,buttons,forms,l10n,login&amp;ver=4.9.8";
    if(isset($_POST['testcookie']) && $_POST['testcookie'] == '1' ){
        $username = $wpdb->escape($_POST['log']);
        $password = $wpdb->escape($_POST['pwd']);
        $remember = $wpdb->escape($_POST['rememberme']);

        if( $username == "" || $password == "" ) {
            $err = 'Please don\'t leave the required field.';
        } else {
            $user_data = array();
            $user_data['user_login'] = $username;
            $user_data['user_password'] = $password;
            $user_data['remember'] = $remember;
            $user = wp_signon( $user_data, false );
            if ( is_wp_error($user) ) {
                //$err = $user->get_error_message();
				$err = "ERROR: The password you entered for the username $username is incorrect.";
                exit();
            } else {
                wp_set_current_user( $user->ID, $username );
                do_action('set_current_user');
                //die("fsfadfs");
                $siteUrl = WP_CONTENT_URL.DS.'frc-admin/frc_offline_db_versions'.DS.'offline_db_versions.php';
                wp_redirect($siteUrl);
                exit();
            }
        }
    }
?>

<html>
    <head>
        <title>FRC-LogIn</title>
		<link rel='stylesheet' href='<?php echo $url; ?>' type='text/css' media='all' />
    </head>
    <body class="login login-action-login wp-core-ui  locale-en-us">
        <div id="login">
            <h1>Log In</h1>
            <?php
			if( !empty($err) )
				echo "<p class='message'>$err</p>";
				
			if(isset($_REQUEST['ait-notification'])){
				echo "<p class='message'>ERROR: Username or Password is incorrect.</p>";
			}
			?>
            </p>
            <form id="loginform" name="loginform" action="" method="post">
                <p>
                    <label for="user_login">Username or Email Address<br />
                    <input type="text" name="log" id="user_login" class="input" value="" size="20" /></label>
                </p>
                <p>
                    <label for="user_pass">Password<br />
                    <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" /></label>
                </p>
                    <p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever"  /> Remember Me</label></p>
                <p class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In" />
                    <input type="hidden" name="testcookie" value="1" />
                </p>
            </form>
        </div>
    </body>
</html>