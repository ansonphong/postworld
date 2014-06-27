<?php
/*
  ____  _                _                _           
 / ___|| |__   ___  _ __| |_ ___ ___   __| | ___  ___ 
 \___ \| '_ \ / _ \| '__| __/ __/ _ \ / _` |/ _ \/ __|
  ___) | | | | (_) | |  | || (_| (_) | (_| |  __/\__ \
 |____/|_| |_|\___/|_|   \__\___\___/ \__,_|\___||___/

/////////////// GENERAL SHORTCODE FUNCTION //////////*/
function pw_general_shortcode( $atts, $content=null, $tag ) {
	extract( shortcode_atts( array(
		'class' => '',
		'color' => '',
	), $atts ) );
	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( $tag );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);
}

/////////////// BASIC SHORTCODES //////////
// BLOCKS
add_shortcode( 'block', 'pw_general_shortcode' );
add_shortcode( 'block-sub', 'pw_general_shortcode' );
add_shortcode( 'blockquote', 'pw_general_shortcode' );

// CALLOUTS
add_shortcode( 'callout', 'pw_general_shortcode' );
add_shortcode( 'callout-xl', 'pw_general_shortcode' );


/////////////// ADVANCED SHORTCODES //////////
include 'shortcodes/headings/headings.php';
include 'shortcodes/fonts/fonts.php';
include 'shortcodes/slider/slider.php';
include 'shortcodes/pagelist/pagelist.php';
include 'shortcodes/columns/columns.php';


?>