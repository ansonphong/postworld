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
	$posts = array();

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

	$slider = pw_set_defaults( $slider, $slider_defaults ); 

	///// TEMPLATES ////
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

	///// SET OVERRIDE MODE /////
	if( !empty( $slider['posts'] ) && is_array( $slider['posts'] ) ){
		$slider['mode'] = 'override';
	}

	////////// MODE //////////
	switch( $slider['mode'] ){

		case 'override':
			///// OVERRIDE POSTS /////
			$posts = $slider['posts'];
			break;

		case 'query':
			///// QUERIES & OPTIONS /////
			// If no 'posts' object provided, and mode set to query, query for the slides

			///// SETUP QUERY /////
			global $post;

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
			$posts = (array) pw_query( $query )->posts;


		// Do not break case 'query' here, continue with this_post mode
		case 'this_post':

			///// THIS POST /////
			// Prepend the current post
			if( $slider['query_vars']['this_post'] == true ){
				// Get current post
				$this_post = array( pw_get_post( $post->ID, $query['fields'] ) );
				// Prepend to the posts array
				$posts = array_merge( $this_post, $posts );
			}
			
			///// GET GALLERIES /////
			// Get attachments from all galleries in found posts
			if( $slider['query_vars']['include_galleries'] == true ){
				// Get all the IDs of the queried posts
				$post_ids = pw_get_post_ids( $posts );

				// Add the current post ID
				if( $slider['query_vars']['this_post'] == true ){
					$post_ids = array_merge( array( $post->ID ), $post_ids );
					$post_ids = array_unique( $post_ids );
				}


				// Get Attachments from Galleries from all posts
				$gallery_attachment_ids = pw_get_posts_galleries_attachment_ids( $post_ids );

				// Get Post Data for Attachments
				$gallery_posts = pw_get_posts( $gallery_attachment_ids, $query['fields'] );

				// Append Galleries 
				$posts = array_merge( $posts, $gallery_posts );

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
				foreach( $posts as $this_post ){
					// If the image width is present
					if( !empty($this_post['image']['sizes']['full']['width']) )
						$filtered_posts[] = $this_post;
				}
				$posts = $filtered_posts;
				
			}
			
			// ONLY GALLERIES
			// Only show posts which appear in galleries
			// Only post_type = attachment
			if(	$slider['query_vars']['include_galleries'] == true &&
				$slider['query_vars']['only_galleries'] == true ){
				$filtered_posts = array();
				foreach( $posts as $this_post ){
					// If the image width is present
					if( $this_post['post_type'] == 'attachment' )
						$filtered_posts[] = $this_post;
				}
				$posts = $filtered_posts;
			}

			break; // break case for 'query' and 'this_post'

		case 'menu':
			///// MENU MODE /////
			// Get the posts from a menu by menu_id
			$fields = "preview";

			// Get the Menu ID
			$menu_id = pw_get_obj( $slider, 'menu_vars.menu_id' );

			// Define the posts
			$posts = ( !$menu_id ) ?
				array() :
				pw_get_menu_posts( $menu_id, $fields );

			break;
	}

	///// INSTANCE /////
	// Generate random ID for slider Instance
	$slider_hash = pw_random_hash();
	$slider['instance'] = "slider_".$slider_hash;

	///// CLASS /////
	if( $slider['transition'] == 'fade' || !isset($slider['transition']) )
		$slider['class'] .= " carousel-fade ";

	///// INCLUDE TEMPLATE /////
	// Include the template
	ob_start();
	include $slider_template;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;

	// Return with everything in a string
	return $content;
	
}


?>