=== Gallery Shortcode Style to Head ===
Contributors: achmafooma, sivel
Tags: gallery, shortcode, style, css, xhtml, head, validation
Requires at least: 3.3
Tested up to: 4.1
Stable tag: 2.4

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation; allows disabling or modifying the default gallery styles.

== Description ==

Moves the gallery shortcode styles to the head so it doesn't break XHTML
validation; allows disabling or modifying the default gallery styles.

By default when using the WordPress gallery, the styles are placed into the
post content which breaks XHTML validation. This plugin moves the style into
the head of the page using a look-ahead to determine if the [gallery]
shortcode is used in any posts.

This plugin also gives you the option to modify the default gallery style
CSS or disable the gallery styles entirely (so you can control it from your
template CSS files).

This plugin uses ideas recommended in a patch located at
http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff

Special thanks to

* The original author: Matt Martz, http://sivel.net

* Serbo-Croatian (sr_RS) translation: Borisa Djuraskovic, http://www.webhostinghub.com

* Spanish (es_ES) translation: Ogi Djuraskovic, http://www.webhostinghub.com/

== Installation ==

Installation is just like any other WordPress plugin:

1. Upload the 'gallery-shortcode-style-to-head' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

Immediately after installation, your galleries should pass W3C XHTML validation.
You can make further adjustments under Admin > Settings > Media.

== Frequently Asked Questions ==

= How do I make the WP galleries XHTML compliant? =

1. Just activate the plugin. Nothing else is required.

= How do I adjust the default gallery CSS styles? =

1. In WP admin, go to Settings > Media.
2. Scroll down to "Modify gallery CSS style.'"
3. Adjust to your liking!
4. Click "Save Changes."

= How do I disable the CSS styles (so I can control the CSS in my template)? =

1. In WP admin, go to Settings > Media.
2. Scroll down to "Disable gallery CSS in 'head.'"
3. Check the box.
4. Click "Save Changes."

= How do I reset the gallery CSS styles to default? =

1. In WP admin, go to Settings > Media.
2. Un-check the setting for "Disable gallery CSS in 'head'."
3. Clear everything in the "Modify gallery CSS style" text field (so it's totally empty).
4. Click "Save Changes." The styles will be reset to default.

== Screenshots ==

1. Plugin settings (under Admin > Settings > Media).

== Changelog ==

= 2.4 (2015-04-02): =
* Plugin is now translateable.
* Code cleanup and support for latest WordPress.
* Serbo-Croatian (sr_RS) translation by Borisa Djuraskovic, http://www.webhostinghub.com.
* Spanish (es_ES) translation by Ogi Djuraskovic, http://www.webhostinghub.com/.

= 2.3 (2013-03-08): =
* Corrected 'undefined varibale' notice and RTL language detection.
* Now requires WordPress 3.0 or higher.

= 2.2 (2012-12-11): =
* Support for WordPress 3.5 and its improved galleries.

= 2.1 (2011-01-15): =
* Removed clearing br tags in galleries (replaced with clearing div tags).
* Corrected bug that threw a cryptic error on the media page for some users.
* Support for WordPress 3.1; now requires WordPress 2.9 or higher.

= 2.0 (2010-05-02): =
* New gallery style settings on the Admin > Settings > Media page.
* Ability to modify the default gallery styles.
* Ability to disable the default styles entirely (so you can style the gallery in your template CSS).
* Support for WordPress 3.0; now requires WordPress 2.7 or higher.

= 1.3 (2010-02-09): =
* Re-Sync with WordPress 2.9 gallery code for support of the new 'include' and 'exclude' options.

= 1.2 (2009-09-20): =
* Corrected function of 'Link thumbnails to...' setting on galleries when this plugin is enabled.

= 1.1 (2008-09-22): =
* Added filter so that the default style can be overridden.

= 1.0 (2008-08-27): =
* Initial Public Release

== Upgrade Notice ==

= 2.3 =
Correct an 'undefined variable' warning/error.

= 2.2 =
Re-sync with WP 3.5 code; improved compatibility with WP 3.5 galleries.

= 2.1 =
Bugfixes, re-sync with WP 3.1 code, and support for WP 3.1.

= 2.0 =
Adds ability to modify or disable the default styles from WP admin; supports WP 3.0.