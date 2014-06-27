<?php
///// SHORTCODE /////
function pw_fonts_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
		'color' => '',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "fonts" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Remove any line breaks
	//$shortcode = str_replace(array("\r", "\n", "\t"), "", $shortcode);

	// Return template
	return do_shortcode($shortcode);

}
add_shortcode( 'smaller', 'pw_fonts_shortcode' );
add_shortcode( 'larger', 'pw_fonts_shortcode' );
add_shortcode( 'font', 'pw_fonts_shortcode' );

?>