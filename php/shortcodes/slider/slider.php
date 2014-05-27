<?php

///// POSTORLD SLIDER /////

function pw_slider_shortcode( $atts, $content = null, $tag ) {

	// Extract Shortcode Attributes, set defaults
	extract( shortcode_atts( array(
		"template"	=> "slider-default",
		"query" 	=> "{}",
		"id" 		=> hash( "md5", "1" ),
		"class" 	=> "shortcode-slider",
		"interval" 	=> 5000,
		"category" 	=> "",
	), $atts ) );


	// Condition Query Values
	if( $query != "{}" ){
		// Replace single quote with double quote, for JSON decode
		$query = str_replace( "'", '"', $query );
		$query = json_decode( $query, true );

		// If JSON decode failed due to syntax errors
		if( $query == 'null' ){
			$query = array();
		}
	}
	else
		// Init Empty Query
		$query = array();


	// Setup Feed Query
	$slider_args = array(
		'template' 	=> $template,
		'query' 	=> $query,
		'id' 		=> $id,
		'class' 	=> $class,
		'interval' 	=> $interval,
		'category'	=> $category
		);

	$shortcode = pw_print_slider( $slider_args );	

	return $shortcode;

}

function pw_print_slider( $slider ){

	///// Setup /////
	// Localize Variables
	//extract( $slider );

	///// Set Defaults /////
	$default_template = "slider-default";

	$slider_defaults = array(
		'template' 		=> $default_template,
		'id'			=> hash('md5', '1' ),
		'class'			=> '',
		'interval'		=> 5000,
		'category'		=> '',
		'category_id'	=> 0,
		);

	$slider = pw_set_defaults( $slider, $slider_defaults ); 


	///// TEMPLATES ////
	$template_id = $slider['template'];
	$slider_templates = pw_get_templates(
		array(
			'subdirs' => array('sliders'),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		);

	$template_id = $slider['template'];

	$slider_template = ( isset( $slider_templates['sliders'][$template_id] ) ) ?
		$slider_templates['sliders'][$template_id] :
		$slider_templates['sliders'][$default_template];


	///// QUERY /////
	global $post;

	// Localize Query
	$query = $slider['query'];
	
	// Set Post Status
	if( !isset( $query['post_status'] ) )
		$query['post_status'] = 'publish';

	// Set Post Types
	if( !isset( $query['post_type'] ) )
		$query['post_type'] = array('page','post','attachment');

	// Set Post Parent
	if( $slider['query_vars']['show_children'] == true )
		$query['post_parent'] = $post->ID;

	// Posts Per Page
	if( !isset( $query['posts_per_page'] ) )
		$query['posts_per_page'] = 25;
	if( isset( $slider['query_vars']['max_posts'] ) )
		$query['posts_per_page'] = intval($slider['query_vars']['max_posts']);

	$query['fields'] = array(
		'ID',
		'post_title',
		'post_excerpt',
		'post_type',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'image(all)',
		);

	// Add Category
	if( !empty( $slider['query_vars']['category'] ) )
		$query['category_name'] = $slider['category'];

	// Add Category ID
	if( !empty( $slider['query_vars']['category_id'] ) )
		$query['cat'] = $slider['category_id'];

	// Check for Taxonomy & Term definitions
	if( !empty( $slider['query_vars']['taxonomy'] ) &&
		!empty( $slider['query_vars']['term'] ) ){
		$query['tax_query'] = array(
			array(
				'taxonomy' 	=> $slider['query_vars']['taxonomy'],
				'field'		=> 'slug',
				'terms' 	=> $slider['query_vars']['term']
				),
			);
		//echo json_encode($query['tax_query']);
	}

	// Do query, return posts
	$posts = pw_query( $query )->posts;

	///// INSTANCE /////
	// Generate random ID for slider Instance
	$slider_hash = hash('md5', json_encode($query));
	$slider['instance'] = "slider_".substr( $slider_hash, 1, 8 );

	///// CLASS /////
	if( $slider['transition'] == 'fade' )
		$slider['class'] .= " carousel-fade";

	///// INCLUDE TEMPLATE /////
	// Include the template
	ob_start();
	include $slider_template;
	$content = ob_get_contents();
	ob_end_clean();

	// Return with everything in a string
	return $content;
	
}


?>