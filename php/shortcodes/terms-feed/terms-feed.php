<?php
////////// POSTWORLD TERMS FEED //////////
function pw_term_feed_shortcode( $atts, $content = null, $tag ) {

	// TODO : Rename all 'term_feed' to 'term_feed'
	

	// Extract Shortcode Attributes, set defaults
	$atts = shortcode_atts( array(
		'template'		=> 'default',
		'query' 		=> '{}',
		'taxonomy'		=> 'post_tag',
		'id' 			=> pw_random_hash(),
		'class' 		=> '',
		'max_terms' 	=> 50,
		'max_posts'		=> 10,
		'post_type'		=> 'any',
		'order_terms_by'=> 'count',
		'order_terms'	=> 'DESC',
		'order_posts_by'=> 'rand',
		), $atts);

	//extract($atts);

	//pw_log( "INIT SHORTCODE : " . $tag . " : ATTS : " . json_encode( $atts ) );

	// Setup Feed Query
	$vars = array(
		'terms' => array(
			'taxonomies'    =>  array( $atts['taxonomy'] ),
			'args'          =>  array(
				'number'	=>	$atts['max_terms'],
				'orderby'	=>	$atts['order_terms_by'],
				'order'		=>	$atts['order_terms'],
				),
			),
		'query'  =>  array(
			'post_type'			=>	$atts['post_type'],
			'orderby'			=>	$atts['order_posts_by'],
			'posts_per_page'	=>	$atts['max_posts'],
			'fields'    		=> 	array( 'ID', 'post_title', 'image(thumbnail)', 'fields' ),    
			),
		'options'	=>	array(
			'include_galleries'	=>	false,	// Deep-scan posts content for gallery shortcodes
					// CAUSING A RECURSION ISSUE
			'move_galleries'	=>	true,	// Moves the galleries from the post to the feed
			'require_image'		=>	true,	// Only posts with a featured image are used
			),
		);

	// Allow the theme to over-ride the shortcode variables
	$vars = apply_filters( 'pw_term_feed_shortcode', $vars );

	//$shortcode = json_encode();	
	$shortcode = pw_print_term_feed( $vars );	

	//$shortcode = json_encode( $vars );

	return $shortcode;

}

add_shortcode( 'terms-feed', 'pw_term_feed_shortcode' );
add_shortcode( 'term-feed', 'pw_term_feed_shortcode' );

function pw_print_term_feed( $vars ){

	$default_template = 'term-feed-default';
	$default_vars = array(
		'template'	=>	$default_template, 	// PHP file in : templates/term-feeds 
		'terms' => array(
			'taxonomies'    =>  array( 'post_tag' ),
			'args'          =>  array(
				'number'	=>	50,
				'orderby'	=>	'count',
				'order'		=>	'DESC',
				),
			),
		'query'  =>  array(
			'post_type'			=>	'any',
			'orderby'			=>	'date',
			'posts_per_page'	=>	10,
			'fields'    		=> 'preview',
			),
		'options'	=>	array(
			'include_galleries'	=>	false,	// Deep-scan posts content for gallery shortcodes
			'move_galleries'	=>	true,	// Moves the galleries from the post to the feed
			'require_image'		=>	false,	// Only posts with a featured image are used
			'include_posts'		=>	true,	// Whether or not to keep the original posts
			),
		);

	//pw_log( "PRINT TERM FEED : VARS : " . json_encode( $vars ) );

	$vars = array_replace_recursive( $default_vars, $vars ); 



	///// TEMPLATES ////
	$template_subdir = 'term-feeds';
	$templates = pw_get_templates(
		array(
			'subdirs' => array( $template_subdir ),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		)[ $template_subdir ];
	
	// Confirm that the specified template actually exists
	$vars['template'] = ( isset( $templates[ $vars['template'] ] ) ) ?
		$vars['template'] :
		$default_template;

	$template = $templates[ $vars['template'] ];

	///// TEMPLATE : OVER-RIDE VARIABLES /////
	// Allow term feed templates to filter the term feed variables
	$vars = apply_filters( PW_TERM_FEED . $vars['template'], $vars );

	///// GET TERMS FEED /////
	$vars['term_feed'] = pw_get_term_feed( $vars );

	///// INSTANCE /////
	// Generate random ID for slider Instance
	$hash = pw_random_hash();
	$vars['instance'] = "term_feed_".$hash;

	///// INCLUDE TEMPLATE /////
	// Include the template
	$content = pw_ob_include( $template, $vars );

	// Return with everything in a string
	return $content;
	
}


////////// POSTWORLD RECURSIVE TERM QUERY //////////

function pw_get_term_feed( $vars ){
	/*
		Gets a series of terms using get_terms()
		And then gets the posts associated with each term

		$vars = array(
			'terms' => array(
				'taxonomies'    =>  [array] // Pass to get_terms()
				'args'          =>  [array] // Pass to get_terms()
				),
			'query'  =>  array( 			// Pass to pw_query()
				'post_type'	=>	
				'fields'    =>  
				),
			'options'	=>	array(
				'include_galleries'	=>	[boolean],	// Deep-scan posts content for gallery shortcodes
				'move_galleries'	=>	[boolean],	// Moves the galleries from the post to the feed
				'require_image'		=>	[boolean],	// Only posts with a featured image are used
				'output'			=>	[string],	// Optional: 'flat'
				'post_term_fields'	=>	[array]		// Optional, which term values to transfer to posts: 'name', 'slug'
			),
		)

		TODO : 	- Add option for include_galleries only if there's less than the perscribed
				number of posts found. Then it first queries not getting galleries
				and then if it doesn't have enough, it scans each sequential post for galleries
				- In this case, get the post_content but don't scan for galleries yet
				On the second round, if there is not enough posts, then scan the posts
				one at a time for galleries

	 */

	// Set defaults
	$default_vars = array(
		'query'	=>	array(
			'fields'	=>	'preview',
			'post_type'	=>	'any',
			),
		'options'	=>	array(
			'include_galleries'	=>	false, // set to false
			'move_galleries'	=>	true,
			'require_image'		=>	false, // set to false
			'include_posts'		=>	true,
			'max_posts'			=> 	(int) pw_get_obj( $vars, 'query.posts_per_page' ),
			),
		);

	$vars = pw_set_defaults( $vars, $default_vars ); 

	// Localize Options
	$include_galleries = (bool) pw_get_obj( $vars, 'options.include_galleries' );

	////////// GET TERMS //////////
	// Get the terms with get_terms()
	$terms = get_terms( $vars['terms']['taxonomies'], $vars['terms']['args'] );
	//$terms = pw_to_array( $terms );

	///// OPTION : INCLUDE GALLERIES /////
	// Filter get_posts
	if( $include_galleries ){
		$vars['query']['fields'] = pw_add_gallery_field( $vars['query']['fields'] );
	}

	///// FOR EACH TERM /////
	// Iterate through each term, and collect the posts
	$output = array();

	if( !empty( $vars['query'] ) && !empty($terms) )
		foreach( $terms as $term ){
			// $term is a standard class object

			$term_data = array();
			$query = $vars['query'];
			$query['tax_query']	=	array(
					array(
						'taxonomy' 	=> $term->taxonomy,
						'field' 	=> 'id',
						'terms' 	=> $term->term_id
						)
				);

			$query_results = pw_query( $query );
			$posts = pw_to_array( $query_results->posts );
			
			////// OPTION : INCLUDE GALLERIES /////
			// Iterate through each post and check if it has a gallery
			// If so, get the posts from the gallery and push them to the new array
			if( $include_galleries && !empty( $posts ) ){
				$posts = pw_merge_galleries( $posts, $vars['options'] );
			}

			///// OPTION : REQUIRE IMAGE /////
			if( pw_get_obj( $vars, 'options.require_image' ) )
				$posts = pw_require_image( $posts );

			// Go through the posts and remove duplicate items
			// TODO : Fix/test this function, wasn't working as expected
			//$posts = pw_unique_sub_key( $posts, 'image.sizes.full.url' );

			///// PROCESS DATA /////
			// Convert name back to normal characters
			$term->name = htmlspecialchars_decode( $term->name );

			///// COMPILE DATA /////
			$term_data['term'] = $term;
			$term_data['term']->post_count = count( $posts );
			$term_data['term']->url = get_term_link( $term );
			$term_data['posts'] = $posts;
			
			array_push( $output, $term_data );		

		}


	///// OPTIONS OUTPUT /////
	if( isset($vars['options']['output']) ){

		///// PROCESS FLAT OUTPUT /////
		if( $vars['options']['output'] == 'flat' ){

			$new_output = array();
			
			// Iterate through each object in output
			foreach( $output as $object ){

				// Iterate through each post in the object
				foreach( $object['posts'] as $post ){

					// Transfer the requested term values to the post
					if( isset($vars['options']['post_term_fields']) ){
						// Create empty object
						$post['term_feed'] = array();
						// Iterate through each requested term field
						foreach( $vars['options']['post_term_fields'] as $term_field ){
							$post['term_feed'][$term_field] = $object['term'][$term_field];
						}
					}
					array_push($new_output, $post);
				}
			}

			$output = $new_output;

		}
	}

	return $output;

}

?>