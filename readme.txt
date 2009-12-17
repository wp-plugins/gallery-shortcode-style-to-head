=== Gallery Shortcode Style to Head ===
Contributors: achmafooma, sivel
Tags: gallery, shortcode, style, css, xhtml, head, validation
Requires at least: 2.6
Tested up to: 2.9
Stable tag: 1.2

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation

== Description ==

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation.

By default when using the gallery shortcode the styles are placed into the
post content which breaks XHTML validation. This plugin places the style into
the head of the page using a look ahead to determine if the [gallery]
shortcode is used in any posts.

This plugin uses ideas recommended in a patch located at
http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff

There is a ticket associated with this validation issue currently set for WordPress 2.9:
http://core.trac.wordpress.org/ticket/10734

Special thanks to the original author of this plugin, Matt Martz, http://sivel.net.

== Installation ==

1. Upload the `gallery-shortcode-style-to-head` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

NOTE: See "Other Notes" for Upgrade and Usage Instructions as well as other pertinent topics.

== Usage ==

1. To override the default style use `add_filter('gallery_style', 'my_function');` somewhere in your theme, probably functions.php, where my_function returns the new style. The new style should begin with `<style type="text/css">` and end with `</style>`

== Upgrade ==

1. Delete the previous `gallery-shortcode-style-to-head` folder from the `/wp-content/plugins/` directory
1. Upload the new `gallery-shortcode-style-to-head` folder to the `/wp-content/plugins/` directory

== Usage ==

1. Just activate and enjoy. Nothing else is required.

== Changelog ==

= 1.2 (2009-09-20): =
* Corrected function of 'Link thumbnails to...' setting on galleries when this plugin is enabled.

= 1.1 (2008-09-22): =
* Added filter so that the default style can be overridden.

= 1.0 (2008-08-27): =
* Initial Public Release
