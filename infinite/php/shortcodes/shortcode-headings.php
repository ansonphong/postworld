<?php

///// SHORTCODE /////
function i_headings_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => 'primary-color',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include i_locate_template("/views/shortcodes/heading-".$tag.".php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'h2', 'i_headings_shortcode' );
add_shortcode( 'h3', 'i_headings_shortcode' );

?>