<?php
/**
 * DEFAULT FEED SETTINGS
 * Define the default settings for feeds globally
 * This can be overridden with the Postworld › Feeds admin panel.
 */
add_filter( PW_OPTIONS_FEED_SETTINGS, 'pw_default_feed_settings_filter', 5 );
function pw_default_feed_settings_filter( $feed_settings ){
	
	// Create default context, if it doesn't exist
	$context_default = _get( $feed_settings, 'context.default' );
	if( empty( $context_default ) )
		$feed_settings = _set( $feed_settings, 'context.default', array() );
	// Define default feed object
	$default_feed = array(
		'preload' => 10,
		'load_increment' => 10,
		);
	// Filter to allow theme to override default settings
	$default_feed = apply_filters( 'pw_default_feed_settings', $default_feed );
	// Merge defaults into settings
	$feed_settings['context']['default'] = array_replace_recursive( $default_feed, $feed_settings['context']['default']);

	return $feed_settings;

}

/**
 * DEFAULT SITE SETTINGS
 * Define the default settings for the site
 * This can be overridden with the Postworld Site admin panel.
 */
add_filter( PW_OPTIONS_SITE, 'pw_default_site_options' );
function pw_default_site_options( $options ){
	if( empty( $options ) )
		$options = array();

	$default_options = array(
		'images' => array(
			'favicon' => false,
			'avatar' => false,
			),
		'security' => array(
			'disable_xmlrpc' => true,
			'require_login' => false,
			),
		'memory' => array(
			'image_memory_limit' => '256M',
			),
		'wp_core' => array(
			'disable_wp_emojicons' => true,
			),
		'postworld' => array(
			'mode' => 'prod'
			),
		);

	return array_replace_recursive($default_options, $options);
}


/**
 * MEMORY LIMIT OPTIONS
 * Options for image memory limit settings.
 * Use 'pw_options_site_memory' filter at a higher priority than 1
 * to override these options.
 */
add_filter( 'pw_options_site_memory', 'pw_options_site_memory_defaults', 1 );
function pw_options_site_memory_defaults( $options ){
	return array(
		array(
			'label' => '256 MB',
			'value' => '256M'
			),
		array(
			'label' => '512 MB',
			'value' => '512M'
			),
		array(
			'label' => '1.0 GB',
			'value' => '1024M'
			),
		array(
			'label' => '1.5 GB',
			'value' => '1536M'
			),
		array(
			'label' => '2.0 GB',
			'value' => '2048M'
			),
		array(
			'label' => '2.5 GB',
			'value' => '2560M'
			),
		array(
			'label' => '3.0 GB',
			'value' => '3072M'
			),
		);
}

/**
 * Postworld Mode Options
 */
add_filter( 'pw_options_postworld_mode', 'pw_options_postworld_mode_defaults', 1 );
function pw_options_postworld_mode_defaults( $options ){
	return array(
		array(
			'label' => __('Production','postworld'),
			'value' => 'prod'
			),
		array(
			'label' => __('Development','postworld'),
			'value' => 'dev'
			),
		);
}

function pw_default_modules( $modules ){
	return $modules;
}
add_filter( PW_OPTIONS_MODULES, 'pw_default_modules' );


///// GALLERY FIELD FILTERS /////
function pw_add_gallery_field_filter( $fields ){
	// Define the gallery field
	$gallery_field = 'gallery(ids,posts)';
	// If fields is an array
	if( is_array( $fields ) &&
		// And the value isn't in the array already 
		!in_array( $gallery_field, $fields ) )
		// Add it to the array
		return array_merge( $fields, array( $gallery_field ) );
	else
		// Otherwire return the same object
		return $fields;
}

function pw_add_gallery_field( $fields = array() ){
	// Add the filters which add the gallery field to to other field models
	pw_add_gallery_field_filters();
	// Run the filter on any value sent to function
	return pw_add_gallery_field_filter( $fields );
}

/**
 * Adds the gallery field to core field models
 */
function pw_add_gallery_field_filters(){
	add_filter( 'pw_post_field_model_preview', 'pw_add_gallery_field_filter' );
	add_filter( 'pw_post_field_model_micro', 'pw_add_gallery_field_filter' );
}

/**
 * Removes the gallery field from core field models
 */
function pw_remove_gallery_field_filters(){
	remove_filter( 'pw_post_field_model_preview', 'pw_add_gallery_field_filter' );
	remove_filter( 'pw_post_field_model_micro', 'pw_add_gallery_field_filter' );
}


/**
 * Prepares the custom 'date_from' field
 * And converts values into a standard WP_Query 'date_query'
 * Allows date queries to be defined relative to the current time
 * @param $query [array] An array of query vars
 * @return [array] An array of updated query vars
 *
 *		$query = array(
 *			'date_from' => array(
 *				'after' => array(
 *					'period' => 'months',
 *					'multiplier' => 3
 *					),
 *				),
 *			);
 *
 */
add_filter( 'pw_prepare_query', 'pw_prepare_date_from' );
function pw_prepare_date_from( $query ){

	// Check if 'date_from' is set
	if( !isset( $query['date_from'] ) )
		return $query;
	// Check if 'date_from' is empty
	$date_from = _get( $query, 'date_from' );
	if( empty( $date_from ) )
		return $query;
	
	// Check for an already existing date query
	$date_query = _get( $query, 'date_query' );
	if( $date_query === false )
		$date_query = array();

	// Create the new date query element
	$new = array();

	// Get 'after' vars
	$after_period = _get( $date_from, 'after_ago.period' );
	$after_multiplier = _get( $date_from, 'after_ago.multiplier' );

	///// AFTER /////
	if( is_string($after_period) && is_integer($after_multiplier) ){
		$new['after'] = array(
			'year' => (int) pw_date_unit_at_ago('year', $after_multiplier, $after_period),
			'month' => (int) pw_date_unit_at_ago('month', $after_multiplier, $after_period),
			'day' => (int) pw_date_unit_at_ago('day', $after_multiplier, $after_period)
			);
	}

	// Add to query
	if( !empty( $new ) )
		$date_query[] = $new;
	if( !empty( $date_query ) )
		$query['date_query'] = $date_query;

	return $query;

}





///// PREPARE QUERY FILTER : POST PARENT FROM /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_post_parent_from' );
function pw_prepare_query_post_parent_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['post_parent_from'] ) ){
		switch( $query['post_parent_from'] ){
			case 'this_post':
			case 'this_post_id':
				$query['post_parent'] = $post->ID;
				break;
			case 'this_post_parent':
				$query['post_parent'] = $post->post_parent;
				break;
		}
	}
	return $query;
}




///// PREPARE QUERY FILTER : EXCLUDE POST FROM /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_exclude_posts_from' );
function pw_prepare_query_exclude_posts_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['exclude_posts_from'] ) ){
		switch( $query['exclude_posts_from'] ){
			case 'this_post':
			case 'this_post_id':
				$query['post__not_in'] = array( $post->ID );
				break;
		}
	}
	return $query;
}



///// PREPARE QUERY FILTER : INCLUDE POST FROM /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_include_posts_from' );
function pw_prepare_query_include_posts_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['include_posts_from'] ) ){
		switch( $query['include_posts_from'] ){
			case 'this_post':
			case 'this_post_id':
				$query['post__in'] = array( $post->ID );
				break;
			case 'this_post_parent':
				$query['post__in'] = array( $post->post_parent );
				break;
		}
		// Set the post type to 'any' if not defined
		$post_type = pw_get_obj( $query, 'post_type' );
		if( $post_type == null || !isset( $post_type ) )
			$query['post_type'] = 'any';
	}

	return $query;
}



///// PREPARE QUERY FILTER : AUTHOR FROM /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_author_from' );
function pw_prepare_query_author_from( $query ){
	global $post;
	/// AUTHOR FROM FIELD ///
	if( isset( $query['author_from'] ) ){
		switch( $query['author_from'] ){
			// If 'this author', get the current post's author
			case 'this_author':
				$query['author'] = $post->post_author;
				break;
			// If author id, then author ID is already set, so unset author_from
			case 'author_id':
				unset( $query['author_from'] );
				break;
		}
	}
	return $query;
}



///// PREPARE QUERY FILTER : RELATED /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_related_posts' );
function pw_prepare_query_related_posts( $query ){
	global $post;
	/// CHECK FOR RELATED QUERY FIELD ///
	if( isset( $query['related_query'] ) && !empty($query['related_query']) ){

		// Construct related posts vars from query
		$related_vars = pw_construct_related_posts_from_query( $query );
		if( $related_vars === false )
			return $query;

		// Get the related post IDs
		$post_ids = pw_related_posts( $related_vars );

		// Add post IDs to `post__in` array
		if( is_array( $query['post__in'] ) )
			$query['post__in'] = array_merge( $query['post__in'], $post_ids );
		else
			$query['post__in'] = $post_ids;
		
		// Remove duplicates
		$query['post__in'] = array_unique($query['post__in']);
	
	}
	return $query;
}



///// PREPARE QUERY FILTER : DEFAULT POST TYPE /////
add_filter( 'pw_prepare_query', 'pw_prepare_query_default_post_type' );
function pw_prepare_query_default_post_type( $query ){
	// Set the post type to 'any' if not defined
	$post_type = pw_get_obj( $query, 'post_type' );
	if( $post_type == null || empty( $post_type ) || !isset( $post_type ) )
		$query['post_type'] = 'any';
	return $query;
}




