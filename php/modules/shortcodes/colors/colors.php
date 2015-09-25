<?php
///// GENERAL SHORTCODE FUNCTION /////
function pw_color_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "colors" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);
}

add_shortcode( 'color-primary', 'pw_color_shortcode' );
add_shortcode( 'color-secondary', 'pw_color_shortcode' );

add_shortcode( 'color-primary-dark', 'pw_color_shortcode' );
add_shortcode( 'color-secondary-dark', 'pw_color_shortcode' );

?>