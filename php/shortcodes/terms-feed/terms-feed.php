<?php

///// POSTORLD SLIDER /////

function pw_terms_feed_shortcode( $atts, $content = null, $tag ) {

	// Extract Shortcode Attributes, set defaults
	$atts = shortcode_atts( array(
		'template'		=> 'default',
		'query' 		=> '{}',
		'taxonomy'		=> 'post_tag',
		'id' 			=> pw_random_hash(),
		'class' 		=> '',
		'max-terms' 	=> 20,
		'max-posts'		=> 50,
		'post-type'		=> 'any',
		'order-terms-by'=> 'count',
		'order-posts-by'=> 'date',
		));

	extract($atts);
	
	// Setup Feed Query
	$vars = array(
		'terms' => array(
			'taxonomies'    =>  array( $atts['taxonomy'] ),
			'args'          =>  array(
				'number'	=>	$atts['max-terms'],
				'orderby'	=>	$atts['order-terms-by'],
				),
			),
		'query'  =>  array(
			'post_type'			=>	$atts['post-type'],
			'orderby'			=>	$atts['order-posts-by'],
			'posts_per_page'	=>	$atts['max-posts'],
			'fields'    		=> 'preview',	//array( 'ID', 'post_title', 'post_permalink' ),    
			),
		);

	// Allow the theme to over-ride the shortcode variables
	$vars = apply_filters( 'pw_terms_feed_shortcode', $vars );

	//$shortcode = json_encode();	
	$shortcode = pw_print_terms_feed( $vars );	

	//$shortcode = json_encode( $vars );

	return $shortcode;

}

add_shortcode( 'terms-feed', 'pw_terms_feed_shortcode' );

function pw_print_terms_feed( $vars ){

	$default_template = 'terms-feed-default';
	$default_vars = array(
		'terms' => array(
			'taxonomies'    =>  array( 'post_tag'),
			'args'          =>  array(
				'number'	=>	20,
				'orderby'	=>	'count',
				),
			),
		'query'  =>  array(
			'post_type'			=>	'any',
			'orderby'			=>	'date',
			'posts_per_page'	=>	'50',
			'fields'    		=> 'preview',
			),
		'template'	=>	$default_template, 	// ID in : templates/shortcodes 
		);

	$vars = pw_set_defaults( $vars, $default_vars ); 


	///// TEMPLATES ////
	$templates = pw_get_templates(
		array(
			'subdirs' => array('shortcodes'),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		);
	$template_id = $vars['template'];
	$template = ( isset( $templates['shortcodes'][$template_id] ) ) ?
		$templates['shortcodes'][$template_id] :
		$templates['shortcodes'][$default_template];

	///// GET TERMS FEED /////
	$vars['terms_feed'] = pw_recursive_term_query( $vars );

	///// INSTANCE /////
	// Generate random ID for slider Instance
	$hash = pw_random_hash();
	$vars['instance'] = "terms_feed_".$hash;

	///// INCLUDE TEMPLATE /////
	// Include the template
	$content = pw_ob_include( $template, $vars );

	// Return with everything in a string
	return $content;
	
}


?>