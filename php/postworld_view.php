<?php

function pw_current_context(){
	// This function maps out the current relavant contexts of the current page
	// The contexts are generally listed in reverse priority order
	// So the lower down on the list, the higher priority it will get when selecting a layout
	// As the layout selection will select the last relavant context

	$context = array();

	/// DEFINE CLASS ///
	// home / archive / blog / page / single / attachment / default

	if( is_front_page() )
		$context[] = 'home';

	if( is_archive() )
		$context[] = 'archive'; 	

	if( is_search() )
		$context[] = 'search';

	if( is_tag() )
		$context[] = 'tag'; 		

	if( is_category() )
		$context[] = 'category'; 

	if( is_page() )
		$context[] = 'page';

	if( is_single() )
		$context[] = 'single';

	if( is_attachment() )
		$context[] = 'attachment';

	if( is_author() )
		$context[] = 'author';

	if( is_admin() )
		$context[] = 'admin';

	if( is_tax() || is_tag() )
		$context[] = 'archive-taxonomy';

	if( is_year() || is_month() || is_day() )
		$context[] = 'archive-date'; 

	if( is_year() )
		$context[] = 'archive-year'; 

	if( is_month() )
		$context[] = 'archive-month'; 

	if( is_day() )
		$context[] = 'archive-day'; 

	if( is_post_type_archive() )
		$context[] = 'archive-post-type'; 
	

	// TAXONOMIES
	if( in_array( 'archive-taxonomy', $context ) ){
		// Define Taxonomy
		if( is_tag() )
			$taxonomy = 'post_tag';
		else if( is_category() )
			$taxonomy = 'category';
		else
			$taxonomy = get_query_var( 'taxonomy' );

		// Get the taxonomy object
		$taxonomy_obj = get_taxonomy( $taxonomy );

		// Check if taxonomy is builtin
		if( isset( $taxonomy_obj )  )
			// If it's a custom taxonomy, assign custom layout ID
			$context[] = 'archive-taxonomy-' . $taxonomy;

	}

	// SINGLE : POST TYPES
	if( in_array( 'single', $context ) ){
		global $post;
		$post_type = $post->post_type;
		$post_type_obj = get_post_type_object( $post_type );
		// Check if the post type is builtin
		if( isset( $post_type_obj ) )
			// If it's a custom point type, assign custom layout ID
			$context[] = 'single-'.$post_type;
	}

	// ARCHIVE : POST TYPE
	if( in_array( 'archive-post-type', $context ) ){

		$post_type = get_query_var( 'post_type' );
		$post_type_obj = get_post_type_object( $post_type );
		// Check if the post type is builtin
		if( isset( $post_type_obj ) )
			// If it's a custom point type, assign custom layout ID
			$context[] = 'archive-post-type-'.$post_type;
	}


	// BUDDYPRESS
	if( pw_is_buddypress_active() ){
		// SEE : plugins/buddypress/bp-core/bp-core-template.php
		if( is_buddypress() )
			$context[] = 'buddypress';
		// USER
		if( bp_is_user_activity() || bp_is_user() )
			$context[] = "buddypress-user";
	}


	// Apply Filters
	$context = apply_filters( 'pw_current_context', $context );

	return $context;
}

function pw_get_current_view(){
	// TODO : Refactor for efficientcy
	global $wp_query;
	$viewdata = array();

	// URL
	$protocol = (!empty($_SERVER['HTTPS'])) ?
		"https" : "http";
	$viewdata['url'] = $protocol."://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
	$viewdata['protocol'] = $protocol;
	$viewdata["context"] = pw_current_context();
	$viewmeta = pw_get_view_meta( $viewdata["context"] );
	$viewdata = array_replace_recursive( $viewdata, $viewmeta );
	$viewdata["query"] = pw_to_array( $wp_query )['query_vars'];
	$viewdata = pw_to_array( $viewdata );
	return $viewdata;
}

function pw_get_view_meta( $context = array() ){
	global $post;

	// If no context provided
	if( empty( $context ) )
		// Use the current context
		$context = pw_current_context();

	////////// SWITCH : CLASS //////////
	$meta = array();

	////// SINGLE /////
	if( in_array( 'single', $context ) ){
		/// POST TYPE ///
		$post_type = $post->post_type;
		$post_type_obj = get_post_type_object( $post_type );
		$meta['post_type'] = $post_type_obj;
	}

	

	////// ARCHIVE /////
	if( in_array( 'archive', $context ) ){

		// Check Context
		//$taxonomy = get_query_var( 'taxonomy' );

		/// POST TYPE ///
		if( in_array( 'archive-post-type', $context ) ){
			$post_type = get_query_var( 'post_type' );
			$post_type_obj = get_post_type_object( $post_type );
			$meta['post_type'] = $post_type_obj;
		}

		///// TAG /////
		else if( in_array( 'tag', $context ) ){
			// Get the tag
			$taxonomy = 'post_tag';
			$tag_slug = get_query_var( 'tag' );
			$meta['term'] = get_term_by( 'slug', $tag_slug, $taxonomy, 'ARRAY_A' );
			$meta['term']['url'] = get_term_link( $tag_slug, $taxonomy );
			$meta['taxonomy'] = get_taxonomy( 'post_tag' );
		}

		/// TAXONOMY ///
		else if( !empty( $taxonomy ) ){

			$taxonomy = get_query_var( 'taxonomy' );
			$term_id = get_queried_object()->term_id;
			$term = (array) get_term( $term_id, $taxonomy );
			$meta['term'] = $term;
			$meta['term']['url'] = get_term_link( intval($term_id) , $taxonomy );
			// Taxonomy Object
			$taxonomy_obj = get_taxonomy( $taxonomy );
			$meta['taxonomy'] = $taxonomy_obj;

			// If parent term exists
			if( $term['parent'] != 0 ){
				$term_parent = (array) get_term( $term['parent'], $taxonomy );
				$meta['term']['parent'] = (array) $term_parent;
				$meta['term']['parent']['url'] = get_term_link( intval($term_parent['term_id']) , $taxonomy );
			}

		}

	}

	///// POST OR PAGE /////
	if( in_array( 'single', $context ) )
		$meta["post"] = $GLOBALS['post'];

	return $meta;

}

function pw_get_bp_contexts(){
	// For adding meta keys to the layouts

	// If BuddyPress is not active
	if( !pw_is_buddypress_active() )
		return array();

	$bp_contexts = array();
	/*
		::: COMPONENTS :::
		"xprofile": "1",
        "settings": "1",
        "friends": "1",
        "messages": "1",
        "activity": "1",
        "notifications": "1",
        "groups": "1",
        "blogs": "1",
        "members": "1"
	*/

	// bp_is_members_component()
    // bp_is_profile_component()


    $bp_contexts[] = array(
    	'name'	=>	'BuddyPress',
    	'slug'	=>	'buddypress',
    	);


	if( bp_is_active( 'xprofile' ) )
		$bp_contexts[] = array(
				'slug' => 'user_activity',
				);


	return $bp_contexts;
}


?>