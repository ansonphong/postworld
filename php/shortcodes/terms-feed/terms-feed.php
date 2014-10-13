<?php
////////// POSTWORLD TERMS FEED //////////
function pw_terms_feed_shortcode( $atts, $content = null, $tag ) {
	// TODO : Rename all 'terms_feed' to 'term_feed'

	// Extract Shortcode Attributes, set defaults
	$atts = shortcode_atts( array(
		'template'		=> 'default',
		'query' 		=> '{}',
		'taxonomy'		=> 'post_tag',
		'id' 			=> pw_random_hash(),
		'class' 		=> '',
		'max-terms' 	=> 50,
		'max-posts'		=> 10,
		'post-type'		=> 'any',
		'order-terms-by'=> 'count',
		'order-terms'	=> 'DESC',
		'order-posts-by'=> 'rand',
		), $atts);

	extract($atts);

	// Setup Feed Query
	$vars = array(
		'terms' => array(
			'taxonomies'    =>  array( $atts['taxonomy'] ),
			'args'          =>  array(
				'number'	=>	$atts['max-terms'],
				'orderby'	=>	$atts['order-terms-by'],
				'order'		=>	$atts['order-terms'],
				),
			),
		'query'  =>  array(
			'post_type'			=>	$atts['post-type'],
			'orderby'			=>	$atts['order-posts-by'],
			'posts_per_page'	=>	$atts['max-posts'],
			'fields'    		=> 	array( 'ID', 'post_title', 'image(thumbnail)', 'fields' ),    
			),
		'options'	=>	array(
			'include_galleries'	=>	true,	// Deep-scan posts content for gallery shortcodes
			'move_galleries'	=>	true,	// Moves the galleries from the post to the feed
			'require_image'		=>	true,	// Only posts with a featured image are used
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

	$default_template = 'term-feed-default';
	$default_vars = array(
		'template'	=>	$default_template, 	// ID in : templates/shortcodes 

		'terms' => array(
			'taxonomies'    =>  array( 'post_tag' ),
			'args'          =>  array(
				'number'	=>	50,
				'orderby'	=>	'count',
				'order'		=>	'ASC',
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
			),

		);

	$vars = pw_set_defaults( $vars, $default_vars ); 

	///// TEMPLATES ////
	$template_subdir = 'term-feeds';
	$templates = pw_get_templates(
		array(
			'subdirs' => array($template_subdir),
			'path_type' => 'dir',
			'ext'=>'php',
			)
		);

	$template_id = $vars['template'];
	$template = ( isset( $templates[$template_subdir][$template_id] ) ) ?
		$templates[$template_subdir][$template_id] :
		$templates[$template_subdir][$default_template];

	///// GET TERMS FEED /////
	$vars['terms_feed'] = pw_get_terms_feed( $vars );

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


////////// POSTWORLD RECURSIVE TERM QUERY //////////

function pw_add_gallery_field( $fields ){
	return array_merge( $fields, array( 'gallery(ids,posts)' ) );
}

function pw_get_terms_feed( $vars ){
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
			),
		'options'	=>	array(
			'include_galleries'	=>	false, // set to false
			'move_galleries'	=>	true,
			'require_image'		=>	false, // set to false
			),
		);

	$vars = pw_set_defaults( $vars, $default_vars ); 

	// Localize Options
	$max_posts = (int) pw_get_obj( $vars, 'query.posts_per_page' );
	$require_image = (bool) pw_get_obj( $vars, 'options.require_image' );
	//echo "REQUIRE IMAGE : " . json_encode($require_image) . " // ";
	$include_galleries = (bool) pw_get_obj( $vars, 'options.include_galleries' );

	// Get the terms with get_terms()
	$terms = get_terms( $vars['terms']['taxonomies'], $vars['terms']['args'] );
	//$terms = pw_to_array( $terms );

	///// OPTION : INCLUDE GALLERIES /////
	// Filter get_posts
	if( $include_galleries ){
		// Add Galleries to Preview Fields
		if( $vars['query']['fields'] == 'preview' )
			add_filter( 'pw_get_post_preview_fields', 'pw_add_gallery_field' );
		// Add galleries to fields
		else if( is_array( $vars['query']['fields'] ) )
			array_push( $vars['query']['fields'], 'gallery(ids,posts)' );
	}

	///// FOR EACH TERM /////
	// Iterate through each term, and collect the posts
	$output = array();

	if( isset( $vars['query'] ) && !empty($terms) )
		foreach( $terms as $term ){
			$term_output = array();
			$term_obj = $term;
			$term = pw_to_array( $term );

			$query = $vars['query'];
			$query['tax_query']	=	array(
					array(
						'taxonomy' 	=> $term['taxonomy'],
						'field' 	=> 'id',
						'terms' 	=> $term['term_id']
						)
				);

			$query_results = pw_query( $query );
			$posts = pw_to_array( $query_results->posts );
			
			//echo "INCLUDE GALLERIES : " . $include_galleries;

			////// OPTION : INCLUDE GALLERIES /////
			// Iterate through each post and check if it has a gallery
			// If so, get the posts from the gallery and push them to the new array
			if( $include_galleries && !empty( $posts ) ){
				$new_posts = array();
				// Iterate through each post
				foreach( $posts as $post ){
					// If it has a gallery object and posts, get them
					$gallery_posts = ( isset( $post['gallery'] ) && !empty( $post['gallery']['posts'] )  ) ?
						$post['gallery']['posts'] : array();

					///// OPTION : MOVE GALLERIES /////
					if( $vars['options']['move_galleries'] == true )
						// Clear the array
						$post['gallery']['posts'] = array();

					///// OPTION : REQUIRE IMAGE /////
					// If require image is on
					if( $require_image == true ){
						// Test if the post has an image
						$post_array = pw_require_image( array( $post ) );
						// If it failed the test
						if( empty( $post_array ) )
							// Empty the post
							$post = array();
					}
					// If the post isn't empty
					if( !empty( $post ) )
						// Add it to the posts array
						array_push( $new_posts, $post );

					//echo "REQUIRE IMAGE : " . json_encode( pw_require_image( array( $post ) )) . " // ";

					// Add the gallery posts to the new posts array
					$new_posts = array_merge( $new_posts, $gallery_posts );

					///// OPTION : MAX POSTS /////
					// If the maximum number of posts is reached already, stop here
					if( $max_posts && count( $new_posts ) >= $max_posts ){
						// Slice the number of posts to the max number
						$new_posts = array_slice( $new_posts, 0, $max_posts );
						// Stop iterating here
						break;
					}
				}

				$posts = $new_posts;
			}
			
			///// OPTION : REQUIRE IMAGE /////
			if( $require_image )
				$posts = pw_require_image( $posts );

			///// PROCESS DATA /////
			// Convert name back to normal characters
			$term['name'] = htmlspecialchars_decode( $term['name'] );

			///// COMPILE DATA /////
			$term_output['term'] = $term;
			$term_output['term']['post_count'] = count( $posts );
			$term_output['term']['url'] = get_term_link( $term_obj );
			$term_output['posts'] = $posts;
			
			array_push( $output, $term_output );		

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