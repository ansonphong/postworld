<?php

///// POSTORLD SLIDER /////

function pw_slider_shortcode( $atts, $content = null, $tag ) {
	
	// Extract Shortcode Attributes, set defaults
	extract( shortcode_atts( array(
		"template"	=>	"default",
		"query" => "{}"
	), $atts ) );

	///// Locate Template ////
	$slider_templates = pw_get_templates( array('subdirs' => array('sliders'), 'ext'=>'html') );

	$slider_template = ( isset( $slider_templates[ $template ] ) ) ?
		$slider_templates[ $template ] : false;

	if( !$slider_template )
		return false;

	///// Generate Query /////
	global $post;
	$query = array();

	// Set Post Type
	$query['post_type'] = 'slider';

	// Set Post Status
	$query['post_status'] = 'publish';

	// Set Other Properties
	$query['posts_per_page'] = 25;
	$query['fields'] = array(
		'ID',
		'post_title',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'image(all)'
		);

	// Setup Feed Query
	$slider_args = array(
		'feed_query' => $query,
		);

	//return json_encode( pw_query( $query ) );
	
	$shortcode = pw_print_slider( $slider_args );	

}

function pw_print_slider( $args ){

	// Do query

	// Init H2O

	// Return Result

}

?>