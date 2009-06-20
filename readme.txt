=== Gallery Shortcode Style to Head ===
Contributors: sivel
Tags: gallery, shortcode, style, css, xhtml, head
Requires at least: 2.6
Tested up to: 2.6.1
Stable tag: 1.0

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation

== Description ==

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation.

By default when using the gallery shortcode the styles are placed into the
post content which breaks XHTML validation.  This plugin places the style into
the head of the page using a look ahead to determine if the [gallery]
shortcode is used in any posts.

This plugin uses ideas recommended in a patch located at
http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff

The ticket ticket associated with the above diff is set to milestone 2.9.  So it may take a while for the patch to be added to the WordPress core.

== Installation ==

1. Upload the `gallery-shortcode-style-to-head` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

NOTE: See "Other Notes" for Upgrade and Usage Instructions as well as other pertinent topics.

== Upgrade ==

1. Delete the previous `gallery-shortcode-style-to-head` folder from the `/wp-content/plugins/` directory
1. Upload the new `gallery-shortcode-style-to-head` folder to the `/wp-content/plugins/` directory

== Usage ==

1. Just activate and enjoy.  Nothing else is required.

== Changelog ==

= 1.0 (2008-08-27): =
* Initial Public Release
