<?php
/**
 * WordPress Administration Template Footer
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

/**
 * @global string $hook_suffix
 */
global $hook_suffix;
?>

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div><!-- wpbody -->
<div class="clear"></div></div><!-- wpcontent -->

<div id="wpfooter" role="contentinfo">
	<?php
	/**
	 * Fires after the opening tag for the admin footer.
	 *
	 * @since 2.5.0
	 */
	do_action( 'in_admin_footer' );
	?>
	<p id="footer-left" class="alignleft">
		<?php
		$text = sprintf( __( 'Thank you for creating with <a href="%s">WordPress</a>.' ), __( 'https://wordpress.org/' ) );
		/**
		 * Filters the "Thank you" text displayed in the admin footer.
		 *
		 * @since 2.8.0
		 *
		 * @param string $text The content that will be printed.
		 */
		echo apply_filters( 'admin_footer_text', '<span id="footer-thankyou">' . $text . '</span>' );
		?>
	</p>
	<p id="footer-upgrade" class="alignright">
		<?php
		/**
		 * Filters the version/update text displayed in the admin footer.
		 *
		 * WordPress prints the current version and update information,
		 * using core_update_footer() at priority 10.
		 *
		 * @since 2.3.0
		 *
		 * @see core_update_footer()
		 *
		 * @param string $content The content that will be printed.
		 */
		echo apply_filters( 'update_footer', '' );
		?>
	</p>
	<div class="clear"></div>
</div>
<?php
/**
 * Prints scripts or data before the default footer scripts.
 *
 * @since 1.2.0
 *
 * @param string $data The data to print.
 */
do_action( 'admin_footer', '' );

/**
 * Prints scripts and data queued for the footer.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 * @since 4.6.0
 *
 * @global string $hook_suffix
 *
 * @param string $hook_suffix The current admin page.
 */
do_action( "admin_print_footer_scripts-{$hook_suffix}" );

/**
 * Prints any scripts and data queued for the footer.
 *
 * @since 2.8.0
 */
do_action( 'admin_print_footer_scripts' );

/**
 * Prints scripts or data after the default footer scripts.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 * @since 2.8.0
 *
 * @global string $hook_suffix
 * @param string $hook_suffix The current admin page.
 */
do_action( "admin_footer-{$hook_suffix}" );

// get_site_option() won't exist when auto upgrading from <= 2.7
if ( function_exists('get_site_option') ) {
	if ( false === get_site_option('can_compress_scripts') )
		compression_test();
}

?>
<link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet"> 
<!--script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script-->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<?php
//rasa
global $current_user;
get_currentuserinfo();
$userRolesArr = $current_user->roles;
$postType = get_post_type();

if($postType == 'ait-dir-item'){
	if(in_array('administrator',$userRolesArr)){
		include("campaign_script.php");
		//--  /home/ozdobcom/public_html/frc/directory/wp-content/themes/directory				
		$currentTheme = wp_get_theme();
		if("Directory" == $currentTheme){
			$templateDir = get_template_directory();
			$neonLib = $templateDir.DS.'AIT'.DS.'Framework'.DS.'Libs'.DS.'Nette'.DS.'nette.min.inc';
			$neonFile = $templateDir.DS.'AIT'.DS.'Framework'.DS.'CustomTypes'.DS.'dir-item'.DS.'dir-item.neon';			
			if(file_exists($neonLib)){
				$neonData = NNeon::decode(file_get_contents($neonFile, true));
				foreach($neonData as $key => $fieldArr){
				if(!is_array($fieldArr))continue;
					if(array_key_exists('label',$fieldArr)){
						$fieldArr['label'];
					}
				}
			}
		}else{
			echo "<!--rasa $currentTheme do not exists -->";
		}
	}else{
		include("campaign_user_script.php");
	}
    //$result1 = wp_update_user( array( 'ID' => 15266, 'role' => 'directory_5' ) );
}

?>
<div class="clear"></div></div><!-- wpwrap -->
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
				$directoryLevelExist = false;
				$directoryLevel = '';
				
				foreach($levels as $level){
					if(array_key_exists($level, $directoryRolesArr)){
						$directoryLevel = $directoryRolesArr[$level];
						$directoryLevelExist = true;
						break;
					}
				}
				
				//if user have directory levels assigned but no respective role assigned
				if($directoryLevelExist){
					$current_user->remove_role( 'subscriber' );
					$current_user->add_role( $directoryLevel );
					wp_redirect( admin_url('post-new.php?post_type=ait-dir-item') );
					//echo "Assigning role = $directoryLevel to user with id=".$current_user->ID;
				}//print_r($userRolesArr);
			}
		}
	}
	frc_update_user_role();
?></div>
<!-- /custom code 123-->
<?php $levels = WLMAPI::GetUserLevels($current_user->ID);
if('27.255.161.112' == $_SERVER["REMOTE_ADDR"]){echo "<pre>";print_r($levels);echo "</pre>";}?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
