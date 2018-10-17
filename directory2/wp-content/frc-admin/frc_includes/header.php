<?php
    $serverName = $_SERVER['SERVER_NAME'];
    $uriArr = explode("/",$_SERVER['REQUEST_URI']);
    $subDir = $uriArr[1]; 
    $contentURL = "https://{$serverName}/$subDir/wp-content/frc-admin";
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    $logoutURL = WP_CONTENT_URL.DS.'frc-admin/frc_utilities'.DS.'logout.php';
	$themeAllowed = array('Directory Plus', 'Directory Plus Child Theme');
    if(is_user_logged_in()){
        $current_user = wp_get_current_user();
        $userLogin = $current_user->user_login;
        $roleArray = $current_user->roles; //caps are equivalent in our case.
        //echo "<!--";print_r($roleArray);echo "-->";
        if(
            in_array('administrator', $roleArray) ||
            in_array('admin_role', $roleArray) ||
            in_array('admin_role2', $roleArray) ||
            in_array('admin_role3', $roleArray) ||
            in_array('admin_role4', $roleArray)
        ){
            ;//all good
        }else{
            $loginAddress = ucfirst($userLogin);
            echo $htmlSnippet = <<<HTML_SNIP
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                <br/><br/>
                <div class="alert alert-info"><strong>Welcome $userLogin</strong>...| <a href='$logoutURL'>Logout</a></div>
                <div class="alert alert-warning"><strong>$loginAddress, you don't have sufficient privileges to manage these scripts! You must have administrator or equivalent roles for managing these scripts. Please contact administrator to get access fo these scripts!</div>
                <br/><br/>
HTML_SNIP;
            die;
        }
    }else{
		$loginURL = WP_CONTENT_URL.DS."frc-admin/users".DS."login.php";//Added by yamini for redirecting to new login screen.
        //$loginURL = wp_login_url( $redirect );//Commented by yamini for redirecting to new login screen.
        echo $htmlSnippet  = <<<HTML_SNIP
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <br/><br/>
        <div class="alert alert-info"><strong>Please login to access these scripts</strong>...</div>
        <div class="alert alert-warning"><a href='$loginURL'>Login</a></div>
        <br/><br/>
HTML_SNIP;
        die;
    }
    echo $nav = <<<NAV
    <style>
    legend {
        display: block;
        padding-left: 2px;
        padding-right: 2px;
        /*border: 1px;;*/
        color: #999999;
        background-color: #C6D5FD;
    }
    </style>
    <table style='padding-left:20px;padding-right:100px;' width='80%'><tr><td align='right'>Welcome $userLogin | <a href='$logoutURL'>Logout</a></td></table>
    <fieldset style='width:80%'>
    <legend>General Navigation:</legend>
    <table style='padding-left:20px;'>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_premium_subscribers/view_users.php'>View Premium Subscribers</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_offline_db_versions/offline_db_versions.php'>Offline DB Versions</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_app_uploads/app_uploads.php'>Mobile Uploads</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_manage_features/update_features.php'>Manage Features</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_lvl_n_roles/wlm_get_user_levels.php'>Manage User Role n Levels</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_misc/find_replace_metas.php'>Find Replace Metas</a></td>
        </tr>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_misc/item_duplicate_content.php'>Find Duplicate Content</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_lvl_n_roles/manage_role_capabilities.php'>Manage Role Capabilities</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='{$contentURL}/frc_theme_admin/grab_roles.php'>Grab Roles</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_misc/feature_n_icons.php'>Feature n Icons</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_terms/item_categories.php'>Item Categories</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_terms/item_locations.php'>Item Locations</a></td>
        </tr>
		<tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_offline_db_versions/item_history_logs.php'>Item History</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_postmeta/manage-item-metas.php'>Restore PostMetas</a><br/><a href='{$contentURL}/frc_postmeta/manage-item-metas.php'>(From Meta Revsions)</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_postmeta/cp-imgs-2-others.php'>Copy Imgs 2 Other Items</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='{$contentURL}/frc_postmeta/post-authors.php'>Post Authors</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='{$contentURL}/frc_postmeta/elastic-related.php'>Elastic Search(Related)</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_misc/assign-author.php'>Change Author</a><br /><a href='{$contentURL}/frc_misc/assign-author.php'>For Bulk Listings</a></td>
        </tr>
        <tr>
            <td style='padding-left:20px;'><a href='{$contentURL}/frc_misc/del-imgs-from-items.php'>Delete Images from Items</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_postmeta/update-images-index.php' title='Re-Order Item Imgs' alt='Re-Order Item Imgs'>Re-Order Item Imgs</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='{$contentURL}/frc_utilities/merge-old-theme-roles.php'>Merge old theme roles</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='{$contentURL}/frc_utilities/map_user_roles_4_pkgs.php' title='Map old directory theme roles to new package roles' alt='Map old directory theme roles to new package roles'>Map User Roles 4 Pkgs</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href="{$contentURL}/frc_postmeta/manage-item-option-metas.php">Manage Item Metas</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href="{$contentURL}/frc_postmeta/update-items-time.php">Update Items Time</a></td>
        </tr>
    </table>
    </fieldset>
NAV;
    include('directory_plus_header.php');
    include('under_developement.php');
	if(!function_exists('is_old_theme')){
		function is_old_theme(){
			$currentWPTheme = wp_get_theme();
			$return = array();
			$args = array('public' => true);
			$post_types = get_post_types($args);
			foreach( $post_types as $post_type ){
				$taxonomies = get_object_taxonomies( $post_type );
				if (!empty($taxonomies)) {
					$return[$post_type] = $taxonomies;
				}
			}
			$oldTheme = false;
			if($currentWPTheme->Name == 'Directory Plus' || $currentWPTheme->Name == 'Directory Plus Child' || $currentWPTheme->Name == 'Directory Plus Child Theme'){
				$oldTheme = false;
			}elseif(is_array($return) && array_key_exists('ait-dir-item', $return)){
				$oldTheme = true;
			}
			return $oldTheme;
		}
	}
    
    if(!function_exists('isOldTheme')){
		function isOldTheme(){
			$currentWPTheme = wp_get_theme();
			$return = array();
			$args = array('public' => true);
			$post_types = get_post_types($args);
			foreach( $post_types as $post_type ){
				$taxonomies = get_object_taxonomies( $post_type );
				if (!empty($taxonomies)) {
					$return[$post_type] = $taxonomies;
				}
			}
			$oldTheme = false;
			if($currentWPTheme->Name == 'Directory Plus' || $currentWPTheme->Name == 'Directory Plus Child' || $currentWPTheme->Name == 'Directory Plus Child Theme'){
				$oldTheme = false;
			}elseif(is_array($return) && array_key_exists('ait-dir-item', $return)){
				$oldTheme = true;
			}
			return $oldTheme;
		}
	}
?>