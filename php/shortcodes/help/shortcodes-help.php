<?php
///// SHORTCODE /////
function pw_help_shortcodes( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "shortcodes-help" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'shortcodes', 'pw_help_shortcodes' );

?>