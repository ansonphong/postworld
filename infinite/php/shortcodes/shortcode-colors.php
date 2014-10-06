<?php
///// GENERAL SHORTCODE FUNCTION /////
function i_color_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	// Start Output Buffering
	ob_start();
	// Get the template
	include i_locate_template("/views/shortcodes/colors.php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);
}

add_shortcode( 'color-primary', 'i_color_shortcode' );
add_shortcode( 'color-secondary', 'i_color_shortcode' );

add_shortcode( 'color-primary-dark', 'i_color_shortcode' );
add_shortcode( 'color-secondary-dark', 'i_color_shortcode' );

?>