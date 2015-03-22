<?php
/*
Plugin Name: Gallery Shortcode Style to Head
Plugin URI: http://www.intersanity.com/software/
Description: Moves the gallery shortcode styles to the head so it doesn't break XHTML validation; allows disabling or modifying the default gallery styles.
Author: Intersanity Enterprises
Author URI: http://www.intersanity.com/
Version: 2.3.1

    Copyright (c) 2008 Matt Martz (http://sivel.net) (original author)
    Copyright (c) 2009-2015 Intersanity Enterprises (Scott Bradford) (http://www.intersanity.com/software/) (current maintainer)

    Gallery Shortcode Style to Head is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
*/

// initialization function
function gssth_init () {
	load_plugin_textdomain('gssth', false, dirname(plugin_basename(__FILE__)) . '/languages');

	// define the default styles
	global $defStyle;
	$float = is_rtl() ? 'right' : 'left';
	$defStyle = ".gallery {
	margin: auto;
}
.gallery .gallery-item {
	float: {$float};
	margin-top: 10px;
	text-align: center;
}
.gallery img {
	border: 2px solid #cfcfcf;
}
.gallery .gallery-caption {
	margin-left: 0;
}";
}
add_action('init', 'gssth_init');

// These functions add a check box to the media settings page in wp-admin so you can disable the default styles entirely
// That way, you can put the styles in your template stylesheet(s) [where they belong] if you want.

// initialize the settings variables
add_action('admin_init', 'add_gssth_setting');
function add_gssth_setting () {
	register_setting('media', 'gssth_disable_gallery_style');
	register_setting('media', 'gssth_override_gallery_style');
	add_settings_section('gssth', __('Gallery CSS Styles', 'gssth'), 'display_gssth_description', 'media');
	add_settings_field('gssth_disable_gallery_style', __("Disable gallery CSS in 'head'", 'gssth'), 'build_disable_gallery_styles', 'media', 'gssth');
	add_settings_field('gssth_override_gallery_style', __('Modify gallery CSS style', 'gssth'), 'build_override_gallery_styles', 'media', 'gssth');
}

// display the GSSTH description
function display_gssth_description () {
	echo '<p>' . __('Override or disable the default WordPress gallery styles. To reset to default styles, un-check the disable option, clear out the style code (so it is completely empty), and save the changes.', 'gssth') . '</p>';
}

// handle the disable/enable check box
function build_disable_gallery_styles () {
	$checked = "";
	if (get_option('gssth_disable_gallery_style')) {
		$checked=" checked='checked'";
	}
	echo '<fieldset><legend class="screen-reader-text"><span>' . __('Disable the default gallery CSS styles', 'gssth') . '</span></legend>
<label for="disable_gallery_styles"><input name="gssth_disable_gallery_style" type="checkbox" id="gssth_disable_gallery_style" value="1"' . $checked . ' /> ' . __('Disable the default gallery CSS styles (so you can handle it in your template stylesheets)', 'gssth') . '</label>
</fieldset>';
}

// handle the style override field
function build_override_gallery_styles () {
	global $defStyle;
	if (get_option('gssth_override_gallery_style')) {
		$content = get_option('gssth_override_gallery_style');
	} else {
		$content = $defStyle;
	}
	echo "<textarea style='font-size: 90%; width:95%;' name='gssth_override_gallery_style' id='gssth_override_gallery_style' rows='15' >" . $content . "</textarea>";
}


// This function is largely taken from media.php with manual patches based off of
// http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff
function gallery_shortcode_style_out ($attr) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;

	if (!empty( $attr['ids'])) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if (empty($attr['orderby'])) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ($output != '') {
		return $output;
	}

	$html5 = current_theme_supports('html5', 'gallery');
	$atts = shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery');

	$id = intval($atts['id']);

	if (!empty($atts['include'])) {
		$_attachments = get_posts(array('include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));

		$attachments = array();
		foreach ($_attachments as $key => $val) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif (!empty($atts['exclude'])) {
		$attachments = get_children(array('post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
	} else {
		$attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby']));
	}

	if (empty($attachments)) {
		return '';
	}

	if (is_feed()) {
		$output = "\n";
		foreach ($attachments as $att_id => $attachment) {
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		}
		return $output;
	}

	$itemtag = tag_escape($atts['itemtag']);
	$captiontag = tag_escape($atts['captiontag']);
	$icontag = tag_escape($atts['icontag']);
	$valid_tags = wp_kses_allowed_html('post');
	if (!isset($valid_tags[$itemtag])) {
		$itemtag = 'dl';
	}
	if (!isset($valid_tags[$captiontag])) {
		$captiontag = 'dd';
	}
	if (!isset($valid_tags[$icontag])) {
		$icontag = 'dt';
	}

	$columns = intval($atts['columns']);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

	$selector = "gallery-{$instance}";

	$size_class = sanitize_html_class($atts['size']);
	$gallery_div = "<div id=\"$selector\" class=\"gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}\">";
	$output = apply_filters('gallery_style', $gallery_div);

	$i = 0;
	foreach ($attachments as $id => $attachment) {
		$attr = (trim($attachment->post_excerpt)) ? array('aria-describedby' => "$selector-$id") : '';
		if (!empty($atts['link']) && 'file' === $atts['link']) {
			$image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
		} elseif (!empty($atts['link'] ) && 'none' === $atts['link']) {
			$image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
		} else {
			$image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
		}
		$image_meta  = wp_get_attachment_metadata($id);

		$orientation = '';
		if (isset($image_meta['height'], $image_meta['width'])) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
		if ($captiontag && trim($attachment->post_excerpt)) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ($columns > 0 && ++$i % $columns == 0) {
			$output .= '<div style=\'clear: both\'></div>';
		}
	}

	if ($columns > 0 && $i % $columns !== 0) {
		$output .= "
			<div style='clear: both'></div>";
	}

	$output .= "
		</div>\n";

	return $output;
}

// Default gallery style taken from media.php with .gallery-item width removed
// .gallery-item width applied inline in gallery_shortcode_style_out().
function gallery_style () {
	global $defStyle;
	$output = "
<!-- Gallery Shortcode Style to Head -->
<style type=\"text/css\">
";
	if (get_option('gssth_override_gallery_style')) {
		// if the style is saved, export the saved style
		$output .= get_option('gssth_override_gallery_style');
	} else {
		// if we don't have any styles set right now
		$output .= $defStyle;
	}

	$output .= "
</style>

";
	echo $output;
}

// Look ahead to check if any posts contain the [gallery] shortcode
// if true then add default gallery style to head
function gallery_scan () {
	global $posts;

	if (!is_array($posts)) {
		return;
	}

	foreach ($posts as $post) {
		if (false !== strpos($post->post_content, '[gallery')) {
			add_action('wp_head', 'gallery_style');
			break;
		}
	}
}

// Tell WordPress what to do
remove_shortcode('gallery_shortcode'); // Remove included WordPress [gallery] shortcode function
add_shortcode('gallery', 'gallery_shortcode_style_out'); // Add new [gallery] shortcode function
if (!get_option('gssth_disable_gallery_style')) {
	// don't do the look-ahead if styles are disabled
	add_action('template_redirect', 'gallery_scan'); // Add look ahead for [gallery] shortcode
}
