<?php
/*
Plugin Name: Gallery Shortcode Style to Head
Plugin URI: http://www.scottbradford.us/software/gallery-shortcode-style-to-head/
Description: Moves the gallery shortcode styles to the head so it doesn't break XHTML validation
Author: Scott Bradford
Author URI: http://www.scottbradford.us/
Version: 1.2

        Copyright (c) 2008 Matt Martz (http://sivel.net) (original author)
        Copyright (c) 2009 Scott Bradford (http://www.scottbradford.us) (current maintainer)
        
    Gallery Shortcode Style to Head is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
*/

// This function is largely taken from media.php with manual patches based off of 
// http://trac.wordpress.org/attachment/ticket/6380/6380-style.diff
function gallery_shortcode_style_out ( $attr ) {
	global $post;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters ( 'post_gallery' , '' , $attr );
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract ( shortcode_atts ( apply_filters ( 'gallery_defaults' , array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	) ) , $attr ) );

	$id = intval ( $id );
	$attachments = get_children ( array ( 'post_parent' => $id , 'post_status' => 'inherit' , 'post_type' => 'attachment' , 'post_mime_type' => 'image' , 'order' => $order , 'orderby' => $orderby ) );

	if ( empty( $attachments ) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, false ) . "\n";
		return $output;
	}

	$listtag = tag_escape ( $listtag );
	$itemtag = tag_escape ( $itemtag );
	$captiontag = tag_escape ( $captiontag );
	$columns = intval ( $columns );
	$itemwidth = $columns > 0 ? floor ( 100/$columns ) : 100;
	
	$output = "
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<div class='gallery'>";

	foreach ( $attachments as $id => $attachment ) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
		$output .= "<{$itemtag} class='gallery-item' style='width:{$itemwidth}%'>";
		$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";
		if ( $captiontag && trim ( $attachment->post_excerpt ) ) {
			$output .= "
				<{$captiontag} class='gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<br style="clear: both" />';
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}

// Default gallery style take from media.php with .gallery-item width removed
// .gallery-item width applied inline in gallery_shortcode_style_out().
function gallery_style () { 

$output = apply_filters('gallery_style', "
<!-- [gallery] shortcode style -->
<style type='text/css'>
       .gallery {
                margin: auto;
        }
        .gallery-item {
                float: left;
                margin-top: 10px;
                text-align: center;
	}
        .gallery img {
                border: 2px solid #cfcfcf;
        }
        .gallery-caption {
                margin-left: 0;
        }
</style>
");
 
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
add_shortcode ( 'gallery' , 'gallery_shortcode_style_out' );	// Add not [gallery] shortcode function
add_action ( 'template_redirect' , 'gallery_scan' );		// Add look ahead for [gallery] shortcode
?>
