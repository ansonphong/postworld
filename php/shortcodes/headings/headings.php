<?php
///// SHORTCODE /////
function pw_headings_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
		'color' => 'blue',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "heading-" . strtolower($tag) );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Remove any line breaks
	$shortcode = str_replace(array("\r", "\n", "\t"), "", $shortcode);

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'h1', 'pw_headings_shortcode' );
add_shortcode( 'h2', 'pw_headings_shortcode' );
add_shortcode( 'h3', 'pw_headings_shortcode' );
add_shortcode( 'h4', 'pw_headings_shortcode' );

?>