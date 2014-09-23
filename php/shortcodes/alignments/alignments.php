<?php
///// SHORTCODE /////
function pw_alignments_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "alignments" );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'align-center', 'pw_alignments_shortcode' );
add_shortcode( 'align-left', 'pw_alignments_shortcode' );
add_shortcode( 'align-right', 'pw_alignments_shortcode' );
add_shortcode( 'float-left', 'pw_alignments_shortcode' );
add_shortcode( 'float-right', 'pw_alignments_shortcode' );

?>