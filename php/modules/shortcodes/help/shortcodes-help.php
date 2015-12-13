<?php
/**
 * Displays information about all available shortcodes.
 */
add_shortcode( 'shortcodes', 'pw_help_shortcodes' );
add_shortcode( 'shortcodes-help', 'pw_help_shortcodes' );
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

/**
 * Used to display a shortcode without actually executing the shortcode.
 */
add_shortcode( 'shortcode', 'pw_display_shortcode' );
function pw_display_shortcode( $atts, $content = null, $tag ){
	/**
	 * @todo	Add option here to be able to customize the shortcode
	 * 			Based on preset options
	 */

	// Do standard formatting for the shortcode
	$output = '<input select-on-click class="pw-shortcode display-shortcode" value="'. htmlentities($content).'">';

	// Allow theme to customize the shortcode content
	$output_obj = apply_filters( 'pw_display_shortcode_content', array(
		'content' => $content,
		'output' => $output,
		'atts' => $atts,
		'tag' => $tag
		));

	$filtered_output = $output_obj['output'];

	if( _get( $atts, 'display' ) == 'true' )
		$filtered_output .= do_shortcode($content);

	return $filtered_output;

}

?>