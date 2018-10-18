{ifset $registerErrors}
<div id="ait-dir-register-notifications" class="error">
	<div class="message defaultContentWidth">
	{!$registerErrors}
	<div class="close"></div>
	</div>
</div>
{/ifset}

{ifset $registerMessages}
<div id="ait-dir-register-notifications" class="info">
	<div class="message defaultContentWidth">
	{!$registerMessages}
	<div class="close"></div>
	</div>
</div>
{/ifset}

{ifset $themeOptions->advertising->showBox1}
<div id="advertising-box-1" class="advertising-box">
	<div class="defaultContentWidth clearfix">
		<div>{!$themeOptions->advertising->box1Content}</div>
	 </div>
</div>
{/ifset}

<div class="topbar clearfix">
		{if !empty($themeOptions->general->topBarContact)}
		<div id="tagLineHolder">
			<div class="defaultContentWidth clearfix">
				<p class="left info">{$themeOptions->general->topBarContact}</p>
				{include 'social-icons.php'}
				{if !is_admin()}
					{include 'wpml-flags.php'}
				{/if}
				<!-- {include 'search-form.php'} -->
			</div>
		</div>
		{/if}
</div>

<header id="branding" role="banner">
	<div class="defaultContentWidth clearfix">
		<div id="logo" class="left">
			{if is_admin()}
				{if !empty($themeOptions->general->easyadmin_logo_img)}
				<a class="trademark" href="{$homeUrl}">
					<img src="{linkTo $themeOptions->general->easyadmin_logo_img}" alt="logo" />
				</a>
				{else}
				<a href="{$homeUrl}">
					<span>{$themeOptions->general->logo_text}</span>
				</a>
				{/if}
			{else}
				{if !empty($themeOptions->general->logo_img)}
				<a class="trademark" href="{$homeUrl}">
					<img src="{linkTo $themeOptions->general->logo_img}" alt="logo" />
				</a>
				{else}
				<a href="{$homeUrl}">
					<span>{$themeOptions->general->logo_text}</span>
				</a>
				{/if}
			{/if}
		</div>

		<nav id="access" role="navigation">

			{if !is_admin()}
				<h3 class="assistive-text">{__ 'Main menu'}</h3>
				{menu 'theme_location' => 'primary-menu', 'fallback_cb' => 'default_menu', 'container' => 'nav', 'container_class' => 'mainmenu', 'menu_class' => 'menu' }
			{else}
				
				<!-- EASY ADMIN MENU -->
				{var $screen = get_current_screen()}
				{var $subscriber = in_array('subscriber', $GLOBALS['current_user']->roles)}

				{if !$subscriber}
					<a href="{!admin_url('edit.php?post_type=ait-dir-item')}" class="items button{if (($screen->base == 'edit' && $screen->post_type == 'ait-dir-item') || ($screen->base == 'post' && $screen->post_type == 'ait-dir-item'))} button-primary{/if}">
						{__ 'My Items'}
					</a>
					{if isset($themeOptions->rating->enableRating) and !isset($themeOptions->rating->disallowDirectoryUsers) }
					<a href="{!admin_url('edit.php?post_type=ait-rating')}" class="ratings button{if ($screen->base == 'edit' && $screen->post_type == 'ait-rating')} button-primary{/if}">
						{__ 'Ratings'}
					</a>
					{/if}
				{/if}
				<a href="{!admin_url('profile.php')}" class="account button{if ($screen->base == 'profile')} button-primary{/if}">
					{__ 'Account'}
				</a>
				<a href="{home_url()}" class="view-site button">
					{__ 'View site'}
				</a>

				{if is_user_logged_in()}
				<a href="{!wp_logout_url(home_url())}" class="menu-login menu-logout">{__ "Logout"}</a>
				{/if}

			{/if}

		</nav><!-- #accs -->

	</div>
</header><!-- #branding -->