=== Plugin Name ===
Contributors: Andrew MAGIK
Tags: wp-api, api, rest-api, post type, custom post type, post type taxonomies, taxonomies, all terms, categories, tags, json, wp-json, custom taxonomy, custom taxonomies, Andrew MAGIK, Andrew MAGIK REST API
Requires at least: 4.4
Tested up to: 4.4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin show all relations between existing post types and attached to them terms (taxonomies) in separate WordPress REST API (v2) endpoint.

== Description ==

This plugin will add separate WordPress REST API (v2) endpoint, with all relations between existing post types and attached to them terms (taxonomies).
It is very useful when you need to create some filters, when you want to know what taxonomies are attached to the current post type.

For example what can you get, using `wp-json/wp/v2/post-type-taxonomies` request (empty results not included):

`{
	post: [
		"category",
		"post_tag",
		"post_format"
	],
	portfolio: [
		"technologies",
		"clients",
		"work_types"
	]
}`

Check my other useful rest-api plugins: [https://wordpress.org/plugins/tags/andrew-magik-rest-api](https://wordpress.org/plugins/tags/andrew-magik-rest-api).

== Installation ==

1. Double check you have the WordPress REST (v2) API installed and active
1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==

= What is the URL of this new WordPress REST API (v2) endpoint? =
You get get all relations between post types and attached terms using GET request:
`http://yoursite.name/wp-json/wp/v2/post-type-taxonomies`

= When this plugin is useful? =
This plugin is very useful when you need to create some filters and when you want to know what taxonomies are attached to the current post type.

= Do you have other useful REST-API plugins? =
Yes, I have. You can check them by tag: [https://wordpress.org/plugins/tags/andrew-magik-rest-api](https://wordpress.org/plugins/tags/andrew-magik-rest-api).


== Changelog ==

= 1.0 =
* Initial release!

