<?php
add_shortcode( 'subpages', 'pw_pagelist_shortcode' );
add_shortcode( 'siblings', 'pw_pagelist_shortcode' );

function pw_pagelist_shortcode( $atts, $content = null, $tag ) {
	global $post;

	if( !is_array($atts) )
		$atts = array();

	// Set the internal defaults
	$default_atts = array(
		"class" 	=> 	"",
		"size" 		=> 	"medium",
		"view" 		=> 	"list-h2o",
		"max"		=>	50,
		"orderby"	=>	"menu_order",
		"order"		=>	"ASC",
		"post-type"	=>	$post->post_type,
		"post-id"	=>	false,
	);

	// Get over-ride defaults from the theme
	$default_atts = apply_filters( 'pw_shortcode_pagelist_defaults', $default_atts, $tag );

	$atts = array_replace_recursive( $default_atts, $atts );

	$query = array();

	// Set Post Type
	$query['post_type'] = $atts['post-type'];

	// Set Post Status
	$query['post_status'] = array('publish');

	/**
	 * If the user has capabilities to read private pages/posts/CPTs
	 * Widen the post status query to include private posts
	 *
	 * @todo Include a switch in Site Options for this (Show private posts to admins)
	 */
	/*
	if( !empty( $post->post_type ) &&
		current_user_can('read_private_'.$post->post_type.'s') )
		$query['post_status'][] = 'private';
	*/

	// Number of Posts
	$query['posts_per_page'] = 0;

	// Fields
	$query['fields'] = pw_get_field_model( 'post', $atts['view'] );
	if( empty( $query['fields'] ) )
		$query['fields'] = 'preview';

	// Ordering
	$query['orderby'] = $atts['orderby'];
	$query['order'] = $atts['order'];

	// Generate query class
	switch($tag){
		case "subpages":
			if( $atts['post-id'] !== false )
				$query['post_parent'] = (int) $atts['post-id'];
			else
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
	$query['posts_per_page'] = $atts['max'];

	// Setup Vars
	$vars = $atts;
	$vars['feed'] = array(
		'query' => $query,
		'view' => $atts['view'],
		);
	$vars['tag'] = $tag;

	$template = pw_get_shortcode_template( 'pagelist' );
	
	$shortcode = pw_ob_include( $template, $vars );

	return $shortcode;
	
}
