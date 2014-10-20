<?php
///// GENERAL SHORTCODE FUNCTION /////
function i_general_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	// Start Output Buffering
	ob_start();
	// Get the template
	include i_locate_template("/views/shortcodes/".$tag.".php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);
}

include_once 'shortcodes/shortcode-columns.php';
include_once 'shortcodes/shortcode-slider.php';
include_once 'shortcodes/shortcode-pagelist.php';
include_once 'shortcodes/shortcode-feeds.php';
include_once 'shortcodes/shortcode-callouts.php';
include_once 'shortcodes/shortcode-colors.php';

?>