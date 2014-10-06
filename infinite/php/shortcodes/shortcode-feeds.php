<?php

///// SHORTCODE /////
function i_feeds_shortcode( $atts, $content = null, $tag ) {
	extract( shortcode_atts( array(
		'class' =>	'primary-color',
		'term'	=>	'', // feture term name
		'name'	=>	'', // menu name
	), $atts ) );
	
	// Start Output Buffering
	ob_start();
	// Get the template
	include locate_template("/views/shortcodes/feed-".$tag.".php");
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);

}

add_shortcode( 'features', 'i_feeds_shortcode' );
//add_shortcode( 'menu', 'i_feeds_shortcode' );

?>