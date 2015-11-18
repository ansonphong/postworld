<?php

////////// CSS COLUMN PROPERTY SHORTCODE //////////

///// COLUMNS /////
function pw_pagelist_shortcode( $atts, $content = null, $tag ) {
	
	// Set the internal defaults
	$shortcode_defaults = array(
		"class" 	=> 	"",
		"size" 		=> 	"medium",
		"view" 		=> 	"list-h2o",
		"max"		=>	50,
		"orderby"	=>	"menu_order",
	);

	// Get over-ride defaults from the theme
	$shortcode_defaults = apply_filters( 'pw_pagelist_shortcode_defaults', $shortcode_defaults, $tag );

	// Extract Shortcode Attributes, set defaults
	$atts = shortcode_atts( $shortcode_defaults, $atts );
	extract( $atts );

	///// Generate Query /////
	global $post;
	$query = array();

	// Set Post Type
	$query['post_type'] = $post->post_type;

	// Set Post Status
	$query['post_status'] = array('publish');

	/**
	 * If the user has capabilities to read private pages/posts/CPTs
	 * Widen the post status query to include private posts
	 */
	if( !empty( $post->post_type ) &&
		current_user_can('read_private_'.$post->post_type.'s') )
		$query['post_status'][] = 'private';

	// Number of Posts
	$query['posts_per_page'] = 0;

	// Fields
	$query['fields'] = pw_get_field_model( 'post', $view );
	if( empty( $query['fields'] ) )
		$query['fields'] = 'preview';

	//pw_log( 'fields:'.$view, $query['fields'] );

	// Ordering
	$query['orderby'] = $atts['orderby'];
	$query['order'] = "ASC";

	// Generate query class
	switch($tag){
		case "subpages":
			// Set parent as current post ID
			$query['post_parent'] = $post->ID;
			break;
		case "siblings":
			// Set parent as current post ID
			$query['post_parent'] = $post->post_parent;
			// Exclude the current page
			$query['post__not_in'] = array($post->ID);
			break;
	}
	
	// Add Max Posts
	$query['posts_per_page'] = $max;

	// Setup Feed Query
	$feed_query_args = array(
		'feed_query' => $query,
		'view' => $view,
		);

	// Setup Vars
	$vars = $atts;
	$vars['feed'] = $feed_query_args;
	$vars['tag'] = $tag;

	$template = pw_get_shortcode_template( 'subpages' );
	
	$shortcode = pw_ob_include( $template, $vars );

	return $shortcode;
	
}

add_shortcode( 'subpages', 'pw_pagelist_shortcode' );
add_shortcode( 'siblings', 'pw_pagelist_shortcode' );


?>