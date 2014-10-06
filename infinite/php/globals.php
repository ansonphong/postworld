<?php
function iGlobals(){
	global $post;

	// Import Options
	// TODO : Use i_get_option() function
	$i_options = json_decode(get_option('i-options'),true);
	$i_layouts = json_decode(get_option('i-layouts'),true);
	$i_sidebars = json_decode(get_option('i-sidebars'),true);
	$i_social = json_decode(get_option('i-social'),true);

	////////// CONTEXT //////////
	$context = array();

	/// DEFINE CLASS ///
	// home / archive / blog / page / single / attachment / default
	if( is_front_page() ){
		 $context['class'] = 'home';
	} else if( is_search() ) {
		$context['class'] = 'search'; 	// Must come before Archive	 
	} else if( is_tag() ) {
		$context['class'] = 'tag'; 		// Must come before Archive
	} else if( is_archive() ) {
		$context['class'] = 'archive'; 	// Must come before Blog
	} else if( is_blog_page() ){
		$context['class'] = 'blog';
	} else if( is_page() ) {
		$context['class'] = 'page';
	} else if( is_single() ) {
		$context['class'] = 'single';
	} else if( is_attachment() ) {
		$context['class'] = 'attachment';
	} else {
		$context['class'] = 'default';
	}

	/// CONTEXT : LAYOUT ///
	$context['layout'] = array();
	// Set default layout ID as the context class
	$context['layout']['id'] = $context['class'];

	////////// SWITCH : CLASS //////////
	$context['meta'] = array();

	switch( $context['class'] ){

		////// SINGLE /////
		case 'single':
			/// POST TYPE ///
			$post_type = $post->post_type;
			$context['type'] = $post_type;
			$post_type_obj = get_post_type_object( $post_type );
			$context['meta']['post_type'] = $post_type_obj;
			break;

		///// TAG /////
		case 'tag':
			// Swap the context class and type
			$context['class'] = 'archive';
			$context['type'] = 'term';

			// Get the tag
			$taxonomy = 'post_tag';
			$tag_slug = get_query_var( 'tag' );
			$context['meta']['term'] = get_term_by( 'slug', $tag_slug, $taxonomy, 'ARRAY_A' );
			$context['meta']['term']['url'] = get_term_link( $tag_slug, $taxonomy );
			$context['meta']['taxonomy'] = get_taxonomy( 'post_tag' );
			break;

		////// ARCHIVE /////
		case 'archive':
			// Check Context
			$post_type = get_query_var( 'post_type' );
			$taxonomy = get_query_var( 'taxonomy' );

			/// POST TYPE ///
			if( !empty($post_type) ){
				$context['type'] = 'post_type';
				$post_type_obj = get_post_type_object( $post_type );
				$context['slug'] = $post_type_obj->name;
				$context['meta']['post_type'] = $post_type_obj;

			/// TAXONOMY ///
			} else if( !empty( $taxonomy ) ){
				$context['type'] = 'term';

				$taxonomy = get_query_var( 'taxonomy' );
				$term_id = get_queried_object()->term_id;
				$term = (array) get_term( $term_id, $taxonomy );
				$context['meta']['term'] = $term;
				$context['meta']['term']['url'] = get_term_link( intval($term_id) , $taxonomy );
				// Taxonomy Object
				$taxonomy_obj = get_taxonomy( $taxonomy );
				$context['meta']['taxonomy'] = $taxonomy_obj;

				// If parent term exists
				if( $term['parent'] != 0 ){
					$term_parent = (array) get_term( $term['parent'], $taxonomy );
					$context['meta']['term']['parent'] = (array) $term_parent;
					$context['meta']['term']['parent']['url'] = get_term_link( intval($term_parent['term_id']) , $taxonomy );
				}

			}
			break;

	}


	//////// GENERATE LAYOUT ID //////////
	// The Layout ID is used to switch between layout settings
	// This assigns the current context to the correct layout
	// For custom post types and custom taxonomies
	switch( $context['class'] ){
		case 'archive':
			// Check if taxonomy is builtin
			if( isset( $taxonomy_obj ) && !$taxonomy_obj->_builtin )
				// If it's a custom taxonomy, assign custom layout ID
				$context['layout']['id'] = 'term_'.$taxonomy.'_' . $context['class'];
			// Don't break 'archive' case here.
		case 'single':
			// Check if the post type is builtin
			if( isset( $post_type_obj ) && !$post_type_obj->_builtin )
				// If it's a custom point type, assign custom layout ID
				$context['layout']['id'] = 'cpt_'.$post_type.'_' . $context['class'];
			break;
	}


	////////// CURRENT LAYOUT //////////
	// Set the Current Layout
	$layout_id = $context['layout']['id'];
	$current_layout = $i_layouts[ $layout_id ];

	// If the layout is not defined or is set to 'default', load default layout
	if( empty($current_layout['layout']) ||
		$current_layout['layout'] == 'default' ){
		$current_layout = $i_layouts['default'];
	}
	
	// Embed the layout ID into the layout object
	$current_layout['id'] = $layout_id;


	////////// DEFINE GLOBALS //////////
	global $iGlobals;
	$iGlobals = array(
		"options" 	=> $i_options,
		"layouts" 	=> $i_layouts,
		"layout"	=> $current_layout,
		"context"	=> $context,
		"sidebars"	=> $i_sidebars,
		"social"	=> $i_social,
		);

	return $iGlobals;

}
?>