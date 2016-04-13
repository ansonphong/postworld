<?php

/**
 * Postworld Query
 * Sets up custom filters for WP_Query which
 * enables querying of custom Postworld Post fields
 */
function pw_query_new( $vars ){

	add_filter( 'posts_where', 'pw_query_posts_where', 10, 2 );
	add_filter( 'posts_join', 'pw_query_posts_join', 10, 2 );
	add_filter( 'posts_orderby', 'pw_query_posts_orderby', 10, 2 );
	//add_filter( 'posts_fields', 'pw_query_posts_fields', 10, 2 );

	$pw_fields = ( isset($vars['fields']) ) ? $vars['fields'] : 'micro';
	$vars['fields'] = 'ids';

	$wp_query = new WP_Query();

	return pw_get_posts( $wp_query->query($vars), $pw_fields );

}


/**
 * Generate custom Postworld WHERE clauses for WP_Query
 */
function pw_query_posts_where( $where, $this ){	
	pw_log( 'QUERY : WHERE', $query );

	$query = pw_access_protected( $this, 'query' );

	/**
	 * Define conditions on which to add custom where clause
	 */
	$query_keys = pw_query_conditions('query_vars');
	$has_keys = pw_array_keys_exist( $query_keys, $query );

	if( $has_keys ){
		// Get all the matching keys	
		$intersect = array_intersect( $query_keys, array_keys($query) );
		// Add a query clause for each of the intersecting key values
		foreach( $intersect as $intersect_key ){
			$where .= ' AND '. $intersect_key .' = "'. $query[$intersect_key] .'" ';
		}	
	}

	return $where;
}

/**
 * Generate custom Postworld JOIN clauses for WP_Query
 */
function pw_query_posts_join( $join, $this ){
	global $wpdb;
	
	if( !pw_config_in_db_tables( 'post_meta' ) )
		return $join;

	// Localize current query vars
	$query = pw_access_protected( $this, 'query' );

	/**
	 * Define conditions on which to join the
	 * 'wp_posts' table with 'pw_postworld_post_meta' table
	 */
	// QUERY VARS/KEYS
	$query_keys = pw_query_conditions('query_vars');
	$has_keys = (bool) pw_array_keys_exist( $query_keys, $query );
	
	// ORDERBY VALUE
	$orderby_values = pw_query_conditions('orderby_values');
	$has_orderby = (bool) ( isset( $query['orderby'] ) && in_array( $query['orderby'], $orderby_values ) );

	/**
	 * If any of the conditions are met
	 * JOIN the wp_posts with wp_postworld_post_meta
	 */
	if( $has_keys || $has_orderby ){
		$join = 'JOIN ' . $wpdb->pw_prefix.'post_meta ON '.$wpdb->prefix.'posts.ID = '.$wpdb->pw_prefix.'post_meta.post_id';
	}

	pw_log( 'QUERY : JOIN', $join );
	
	return $join;
}


/**
 * Generate custom Postworld ORDERBY clauses for WP_Query
 */
function pw_query_posts_orderby( $orderby, $this ){
	global $wpdb;

	// Localize current query vars
	$query = pw_access_protected( $this, 'query' );

	if( !isset( $query['order'] ) )
		$query['order'] = 'DESC';

	// ORDERBY VALUE
	$orderby_values = pw_query_conditions('orderby_values');
	$has_orderby = (bool) ( isset( $query['orderby'] ) && in_array( $query['orderby'], $orderby_values ) );

	if( $has_orderby ){
		$orderby = $wpdb->pw_prefix.'post_meta'.".".$query['orderby']." ".$query['order'];	
	}

	pw_log( 'QUERY : ORDER BY', $orderby );
	return $orderby;
}



/**
 * Store centralized arrays of conditions
 * Which would trigger active filtering on the
 * WP_Query class coorosponding with custom
 * Postworld query variables being input.
 */
function pw_query_conditions( $subkey = null ){

	$conditions = array(
		'query_vars' => array(
			'post_class',
			'link_format',
			'related_post'
			),
		'orderby_values' => array(
			'post_points',
			'rank_score',
			'post_shares',
			'event_start',
			'event_end'
			),
		);

	if( $subkey === null )
		return $conditions;
	else
		return _get( $conditions, $subkey );

}


// FILTER : posts_fields : SELECT
// FILTER : posts_clauses : ALL CLAUSES **!!
// FILTER : posts_results : POSTS AFTER QUERYING
