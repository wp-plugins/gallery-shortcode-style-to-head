<?php
/*
Plugin Name: Gallery Shortcode Style to Head
Plugin URI: http://www.scottbradford.us/software/gallery-shortcode-style-to-head/
Description: Moves the gallery shortcode styles to the head so it doesn't break XHTML validation; allows disabling or modifying the default gallery styles. 
Author: Scott Bradford
Author URI: http://www.scottbradford.us/
Version: 2.3

    Copyright (c) 2008 Matt Martz (http://sivel.net) (original author)
    Copyright (c) 2009-2012 Scott Bradford (http://www.scottbradford.us) (current maintainer)
        
    Gallery Shortcode Style to Head is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
*/

add_action('init','gssth_init');

function gssth_init() {
	// define the default styles
	global $defStyle;
	$float = is_rtl() ? 'right' : 'left';
	$defStyle = ".gallery { margin: auto; }
.gallery .gallery-item { float: {$float}; margin-top: 10px; text-align: center; }
.gallery img { border: 2px solid #cfcfcf; }
.gallery .gallery-caption { margin-left: 0; }";
}

// These functions add a check box to the media settings page in wp-admin so you can disable the default styles entirely
// That way, you can put the styles in your template stylesheet(s) [where they belong] if you want.

// initialize the settings variables
add_action('admin_init', 'add_gssth_setting' );
function add_gssth_setting() {
	register_setting( 'media', 'gssth_disable_gallery_style' );
	register_setting( 'media', 'gssth_override_gallery_style' );
	add_settings_section('gssth','Gallery CSS Styles','display_gssth_description','media');
	add_settings_field('gssth_disable_gallery_style','Disable gallery CSS in \'head\'','build_disable_gallery_styles','media','gssth');
	add_settings_field('gssth_override_gallery_style','Modify gallery CSS style','build_override_gallery_styles','media','gssth');
}

// display the GSSTH description
function display_gssth_description() {
	echo "<p>Override or disable the default WordPress gallery styles. To reset to default styles, un-check the disable option, clear out the style code (so it is completely empty), and save the changes.</p>";
}

// handle the disable/enable check box
function build_disable_gallery_styles() {
	$checked = "";
	if (get_option('gssth_disable_gallery_style'))
		$checked=" checked='checked'";
	echo "<fieldset><legend class=\"screen-reader-text\"><span>Disable the default gallery CSS styles</span></legend>
<label for=\"disable_gallery_styles\"><input name=\"gssth_disable_gallery_style\" type=\"checkbox\" id=\"gssth_disable_gallery_style\" value=\"1\"" . $checked . " /> Disable the default gallery CSS styles (so you can handle it in your template stylesheets)</label>
</fieldset>";
}

// handle the style override field
function build_override_gallery_styles() {
	global $defStyle;
	if (get_option('gssth_override_gallery_style'))
		$content = get_option('gssth_override_gallery_style');
	else
		$content = $defStyle;
	echo "<textarea style='font-size: 90%; width:95%;' name='gssth_override_gallery_style' id='gssth_override_gallery_style' rows='15' >" . $content . "</textarea>";
}


// This function is largely taken from media.php with manual patches based off of 
// http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff

function gallery_shortcode_style_out ( $attr ) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;
	
	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

	$selector = "gallery-{$instance}";
	
	$size_class = sanitize_html_class( $size, '' );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "<{$itemtag} class='gallery-item' style='width:{$itemwidth}%;'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<div style="clear: both"></div>';
	}

	$output .= "
			<div style='clear: both;'></div>
		</div>\n";

	return $output;
}

// Default gallery style taken from media.php with .gallery-item width removed
// .gallery-item width applied inline in gallery_shortcode_style_out().
function gallery_style () { 
	global $defStyle;
	$output = "
<!-- Gallery Shortcode Style to Head -->
<style type='text/css'>
";
	if (get_option('gssth_override_gallery_style')) { // if the style is saved, export the saved style
		$output .= get_option('gssth_override_gallery_style');		
	} else { // if we don't have any styles set right now
		$output .= $defStyle;
	}

	$output .= "
</style>";
	echo $output;
} 

// Look ahead to check if any posts contain the [gallery] shortcode
// if true then add default gallery style to head
function gallery_scan () { 
	global $posts; 
 
    if ( !is_array ( $posts ) ) 
    	return; 
 
    foreach ( $posts as $post ) { 
           if ( false !== strpos ( $post->post_content, '[gallery' ) ) { 
                   add_action ( 'wp_head', 'gallery_style' ); 
                   break; 
           } 
    } 
} 

// Tell WordPress what to do
remove_shortcode ( 'gallery_shortcode' );			// Remove included WordPress [gallery] shortcode function
add_shortcode ( 'gallery' , 'gallery_shortcode_style_out' );	// Add new [gallery] shortcode function
if (!get_option('gssth_disable_gallery_style')) {  // don't do the look-ahead if styles are disabled
	add_action ( 'template_redirect' , 'gallery_scan' );		// Add look ahead for [gallery] shortcode
}
?>