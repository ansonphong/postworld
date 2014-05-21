<?php

///// POSTORLD SLIDER /////

function pw_slider_shortcode( $atts, $content = null, $tag ) {

	// Extract Shortcode Attributes, set defaults
	extract( shortcode_atts( array(
		"template"	=>	"default",
		"query" => "{}",
	), $atts ) );

	///// Locate Template ////
	$slider_templates = pw_get_templates(
		array(
			'subdirs' => array('sliders'),
			'path_type' => 'dir',
			'ext'=>'html',
			)
		);

	$slider_id = "slider-".$template;

	$slider_template = ( isset( $slider_templates['sliders'][$slider_id] ) ) ?
		$slider_templates['sliders'][$slider_id] :
		$slider_templates['sliders']['default'];


	///// QUERY /////
	global $post;

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

	// Condition Query
	
	// Set Default Post Type
	//$query['post_type'] = 'slider';

	// Set Post Status
	$query['post_status'] = 'publish';

	// Set Other Properties
	$query['posts_per_page'] = 25;
	$query['fields'] = array(
		'ID',
		'post_title',
		'post_type',
		'post_parent',
		'post_permalink',
		'post_excerpt',
		'image(all)'
		);

	// Setup Feed Query
	$slider_args = array(
		'feed_query' => 	$query,
		'template' =>		$slider_template,
		);

	$shortcode = pw_print_slider( $slider_args );	

	return $shortcode;

}

function pw_print_slider( $args ){

	// Do query, return posts
	$posts = pw_query( $args['feed_query'] )->posts;

	//return $posts;

	// Include H2O Template Engine
	pw_include_h2o();

	
	// Init H2O

	// Initialize h2o template engine
	$h2o = new h2o( $args['template'] );

	// Seed the post data with 'post' for use in template, ie. {{post.post_title}}
	$h2o_data['posts'] = $posts;

	// Add rendered HTML to the return data
	$html = $h2o->render($h2o_data);

	// Return Result
	return $html;
	
}

?>