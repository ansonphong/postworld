<?php

////////// POSTWORLD FEED //////////
function pw_feed_shortcode( $atts, $content = null, $tag ) {

	$default_atts = array(
		'id'	=>	'',
		);

	$atts = array_replace_recursive($default_atts, $atts);

	// Return here if no feed ID
	if( empty( $atts['id'] ) )
		return false;

	// Allow the theme to over-ride the shortcode variables
	//$vars = apply_filters( 'pw_feed_shortcode', $vars );

	$feed = array(
		'feed_id'	=>	$atts['id'],
		'echo'		=>	false,
		);

	$shortcode = pw_feed( $feed );	
	return $shortcode;

}

add_shortcode( 'pw-feed', 	'pw_feed_shortcode' );
add_shortcode( 'feed', 		'pw_feed_shortcode' );

?>