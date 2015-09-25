<?php

///// POSTORLD SLIDER /////
function pw_slider_shortcode( $atts, $content = null, $tag ) {

	// Extract Shortcode Attributes, set defaults
	extract( shortcode_atts( array(
		'mode'			=>	'query',
		//'posts'			=>	array(),
		'template'		=> 'slider-default',
		'query' 		=> '{}',
		'id' 			=> pw_random_hash(),
		'class' 		=> 'shortcode-slider',
		'interval' 		=> 5000,
		'category' 		=> '',
		'no_pause'		=> false,
		'transition'	=> 'fade',
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
		'mode' 			=> $mode,
		'template' 		=> $template,
		'query' 		=> $query,
		'id' 			=> $id,
		'class' 		=> $class,
		'interval' 		=> $interval,
		'category'		=> $category,
		'transition'	=> $transition,
		);

	$shortcode = pw_print_slider( $slider_args );	

	return $shortcode;

}

function pw_print_slider( $slider ){

	///// Setup /////
	// Localize Variables
	//extract( $slider );

	///// POSTS /////
	// The slides go in here
	$slider['posts'] = array();

	///// Set Defaults /////
	$default_template = "slider-default";

	$slider_defaults = array(
		'mode'			=>	'query',
		//'post_ids' 		=> 	array(), // Not developed
		'posts'			=>	array(),
		'template' 		=> 	$default_template,
		'id'			=> 	hash('md5', '1' ),
		'class'			=> 	'',
		'interval'		=> 	5000,
		'category'		=> 	'',
		'category_id'	=> 	0,
		'no_pause'		=> 	false,
		'transition'	=>	'fade',
		);

	$slider = array_replace_recursive( $slider_defaults, $slider );

	///// TEMPLATES ////
	$slider_templates = pw_get_templates(
		array(
			'subdirs' 	=> 	array('sliders'),
			'path_type' => 	'dir',
			'ext'		=>	'php',
			)
		);
	$template_id = $slider['template'];
	$slider_template = ( isset( $slider_templates['sliders'][$template_id] ) ) ?
		$slider_templates['sliders'][$template_id] :
		$slider_templates['sliders'][$default_template];

	///// CACHING LAYER /////
	$slider_hash = hash( 'sha256', json_encode( $slider ) );
	if( in_array( 'post_cache', pw_enabled_modules() ) ){
		$get_cache = pw_get_cache( array( 'cache_hash' => $slider_hash ) );
		if( !empty( $get_cache ) ){
			$slider = json_decode($get_cache['cache_content'], true);
			return pw_ob_include( $slider_template, $slider );
		}
	}

	$slider = apply_filters( 'pw_slider_preprocess', $slider );
	//pw_log( 'slider', $slider );

	///// SET OVERRIDE MODE /////
	if( !empty( $slider['posts'] ) && is_array( $slider['posts'] ) )
		$slider['mode'] = 'override';

	///// GET DEFAULT FIELDS /////
	$fields = _get( $slider, 'query.fields' );
	if( empty($fields) )
		$fields = "preview";

	global $post;

	////////// MODE //////////
	switch( $slider['mode'] ){

		case 'override':
			///// OVERRIDE POSTS /////
			break;

		case 'query':
			///// QUERIES & OPTIONS /////
			// If no 'posts' object provided, and mode set to query, query for the slides

			///// SETUP QUERY /////

			// Localize Query
			$query = $slider['query'];
			
			// POST STATUS
			// Set Post Status
			if( !isset( $query['post_status'] ) )
				$query['post_status'] = 'publish';

			// POST TYPE
			// Set Post Types
			if( !isset( $query['post_type'] ) )
				$query['post_type'] = array('page','post','attachment');

			// SHOW CHILDREN
			// Set Post Parent
			if( $slider['query_vars']['show_children'] == true )
				$query['post_parent'] = $post->ID;

			// MAX POSTS
			// Posts Per Page
			if( !isset( $query['posts_per_page'] ) )
				$query['posts_per_page'] = 25;
			if( isset( $slider['query_vars']['max_posts'] ) )
				$query['posts_per_page'] = intval($slider['query_vars']['max_posts']);

			// FIELDS
			if( !isset( $query['fields'] ) )
				$query['fields'] = $fields;

			// CATEGORY
			// Add Category
			if( !empty( $slider['query_vars']['category'] ) )
				$query['category_name'] = $slider['category'];

			// CATEGORY ID
			// Add Category ID
			if( !empty( $slider['query_vars']['category_id'] ) )
				$query['cat'] = $slider['category_id'];

			// TAXONOMY
			// Check for Taxonomy & Term definitions
			if( !empty( $slider['query_vars']['tax_query_taxonomy'] ) &&
				!empty( $slider['query_vars']['tax_query_term_id'] ) ){
				$query['tax_query'] = array(
					array(
						'taxonomy' 	=> $slider['query_vars']['tax_query_taxonomy'],
						'field'		=> 'id',
						'terms' 	=> $slider['query_vars']['tax_query_term_id']
						),
					);
			}

			//$query['order'] = "";

			///// RUN QUERY /////
			$slider['posts'] = (array) pw_query( $query )->posts;


		// Do not break case 'query' here, continue with this_post mode
		case 'this_post':

			///// THIS POST /////
			// Prepend the current post
			if( $slider['query_vars']['this_post'] == true ){
				// Get current post
				$this_post = array( pw_get_post( $post->ID, $fields ) );
				if( !is_array( $slider['posts'] ) ){
					$slider['posts'] = array();
				}
				// Prepend to the posts array
				$slider['posts'] = array_merge( $this_post, $slider['posts'] );
			}
			
			///// GET GALLERIES /////
			// Get attachments from all galleries in found posts
			if( $slider['query_vars']['include_galleries'] == true ){
				
				// Get all the IDs of the queried posts
				$post_ids = pw_get_post_ids( $slider['posts'] );

				// Add the current post ID
				if( $slider['query_vars']['this_post'] == true ){
					$post_ids = array_merge( array( $post->ID ), $post_ids );
					$post_ids = array_unique( $post_ids );
				}

				// Get Attachments from Galleries from all posts
				$gallery_attachment_ids = pw_get_posts_galleries_attachment_ids( $post_ids );

				// Get Post Data for Attachments
				$gallery_posts = pw_get_posts( $gallery_attachment_ids, $fields );

				// Append Galleries 
				$slider['posts'] = array_merge( $slider['posts'], $gallery_posts );

			}

			///// GALLERIES /////
			// Hide Galleries from the current post
			if(
				$slider['query_vars']['include_galleries'] == true &&
				$slider['query_vars']['hide_galleries'] == true ){

				// Remove the Gallery shortcode
				remove_shortcode('gallery');

				// Replace it with an empty shortcode
				function shortcode_gallery_empty( $atts ) {
				     return "";
				}
				add_shortcode('gallery', 'shortcode_gallery_empty');
			}



			///// FILTERING /////
			// HAS IMAGES
			// Only show posts which have featured images
			// Image fields must be populated
			if( $slider['query_vars']['has_image'] == true ){
				
				$filtered_posts = array();
				foreach( $slider['posts'] as $this_post ){
					// If the image width is present
					$image_sizes = _get( $this_post, 'image.sizes' );
					if( !empty( $image_sizes ) )
						$filtered_posts[] = $this_post;
				}
				$slider['posts'] = $filtered_posts;
				
			}
			
			// ONLY GALLERIES
			// Only show posts which appear in galleries
			// Only post_type = attachment
			if(	$slider['query_vars']['include_galleries'] == true &&
				$slider['query_vars']['only_galleries'] == true ){
				$filtered_posts = array();
				foreach( $slider['posts'] as $this_post ){
					// If the image width is present
					if( $this_post['post_type'] == 'attachment' )
						$filtered_posts[] = $this_post;
				}
				$slider['posts'] = $filtered_posts;
			}

			break; // break case for 'query' and 'this_post'

		case 'menu':
			///// MENU MODE /////
			// Get the posts from a menu by menu_id
			// Get the Menu ID
			$menu_id = _get( $slider, 'menu_vars.menu_id' );

			// Define the posts
			$slider['posts'] = ( !$menu_id ) ?
				array() :
				pw_get_menu_posts( $menu_id, $fields );

			break;
			
	}

	///// TRANSITION CLASS /////
	if( $slider['transition'] == 'fade' || !isset($slider['transition']) )
		$slider['class'] .= " carousel-fade";

	///// PROPORTION CLASS /////
	$proportion = _get( $slider, 'proportion' );
	if( $proportion === false )
		$slider['class'] .= " proportion-flex";
	else
		$slider['class'] .= " proportion-" . str_replace('.','_',$proportion);

	///// INSTANCE /////
	// Generate slider Instance string
	$slider['instance'] = "slider_".$slider_hash;

	///// CACHING LAYER /////
	if( in_array( 'post_cache', pw_enabled_modules() ) ){
		pw_set_cache( array(
			'cache_type'	=>	'slider',
			'cache_hash' 	=> 	$slider_hash,
			'cache_content'	=>	json_encode($slider),
			));
	}

	///// INCLUDE TEMPLATE /////
	// Include the template
	// Return with everything in a string
	return pw_ob_include( $slider_template, $slider );
	
}

?>