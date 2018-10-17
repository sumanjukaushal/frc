<?php
    $serverName = $_SERVER['SERVER_NAME'];
    $uriArr = explode("/",$_SERVER['REQUEST_URI']);
    $subDir = $uriArr[1];
    $contentURL = "https://{$serverName}/$subDir/wp-content";
	$themeAllowed = array('Directory Plus', 'Directory Plus Child Theme');
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
            <td><a href='#-1'>Reserved 4</a></td>
            <td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='#-1'>Reserved 3</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
			<td><a href='#-1'>Reserved 2</a></td>
			<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
            <td><a href='#-1'>Reserved 1</a></td>
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
			if(is_array($return) && array_key_exists('ait-dir-item', $return)){
				$oldTheme = true;
			}elseif($currentWPTheme->Name == 'Directory Plus' || $currentWPTheme->Name == 'Directory Plus Child'){
				$oldTheme = false;
			}
			return $oldTheme;
		}
	}
?>