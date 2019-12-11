<?php
/**
 * @todo Refactor into PW_Query
 */

/**
 * Postworld Query
 * Sets up custom filters for WP_Query which
 * enables querying of custom Postworld Post fields
 */
function pw_query_posts( $vars ){

	// Setup series of query filters which enable advanded Postworld Querying
	add_filter( 'posts_where', 'pw_query_posts_where', 10, 2 );
	add_filter( 'posts_join', 'pw_query_posts_join', 10, 2 );
	add_filter( 'posts_orderby', 'pw_query_posts_orderby', 10, 2 );
	//add_filter( 'posts_fields', 'pw_query_posts_fields', 10, 2 );

	$pw_fields = ( isset($vars['fields']) ) ? $vars['fields'] : 'micro';
	$vars['fields'] = 'ids';

	// Get the Post IDs from the Query
	$wp_query = new WP_Query();
	$post_ids = $wp_query->query($vars);

	// Prepend the sticky posts if requested
	if( _get( $vars, 'show_sticky_posts' ) ){
		// Get the sticky posts
		$sticky_posts = get_option( 'sticky_posts', array() );
		// If Sticky Posts
		if(!empty($sticky_posts)){
			// Remove the sticky posts from the post ids
			$post_ids = array_diff( $post_ids, $sticky_posts );
			// Prepend the sticky posts to the beginning
			$post_ids = array_merge( $sticky_posts, $post_ids );
		}
	}

	if( $pw_fields == 'ids' )
		return $post_ids;
	else
		return pw_get_posts( $post_ids, $pw_fields );

}

/**
 * Generate custom Postworld JOIN clauses for WP_Query
 */
function pw_query_posts_join( $join, $this_ ){
	global $wpdb;
	
	if( !pw_config_in_db_tables( 'post_meta' ) )
		return $join;

	// Localize current query vars
	$query = pw_access_protected( $this_, 'query' );

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
		$join = 'JOIN ' . $wpdb->postworld_prefix.'post_meta ON '.$wpdb->prefix.'posts.ID = '.$wpdb->postworld_prefix.'post_meta.post_id';
	}

	//pw_log( 'QUERY : JOIN', $join );
	
	return $join;
}


/**
 * Generate custom Postworld ORDERBY clauses for WP_Query
 */
function pw_query_posts_orderby( $orderby, $this_ ){
	global $wpdb;

	// Localize current query vars
	$query = pw_access_protected( $this_, 'query' );

	if( !isset( $query['order'] ) )
		$query['order'] = 'DESC';

	// ORDERBY VALUE
	$orderby_values = pw_query_conditions('orderby_values');
	$has_orderby = (bool) ( isset( $query['orderby'] ) && in_array( $query['orderby'], $orderby_values ) );

	if( $has_orderby ){
		$orderby = $wpdb->postworld_prefix.'post_meta'.".".$query['orderby']." ".$query['order'];	
	}

	//pw_log( 'QUERY : ORDER BY', $orderby );
	
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
		'event_vars' => array(
			'event_filter',
			'event_start',
			'event_end',
			'event_before',
			'event_after',
			),
		'geo_vars' => array(
			'geo_range',
			'geo_latitude',
			'geo_longitude',
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




/**
 * Generate custom Postworld WHERE clauses for WP_Query
 */
function pw_query_posts_where( $where, $this_ ){	

	$query = pw_access_protected( $this_, 'query' );

	/**
	 * Define conditions on which to add custom where clause
	 */
	$query_keys = pw_query_conditions('query_vars');
	$has_keys = pw_array_keys_exist( $query_keys, $query );

	/**
	 * Add custom where clause based on key values
	 */
	if( $has_keys ){
		// Get all the matching keys	
		$intersect = array_intersect( $query_keys, array_keys($query) );
		// Add a query clause for each of the intersecting key values
		foreach( $intersect as $intersect_key ){
			$where .= ' AND '. $intersect_key .' = "'. $query[$intersect_key] .'" ';
		}	
	}


	/**
	 * Add custom where clause based on EVENTS query vars
	 */
	$time_query = pw_prepare_time_query( $query );
	$where = pw_query_combine_where_clauses( $where, $time_query );
	
	//pw_log('TIME QUERY', $time_query);

	/**
	 * Add custom where clause based on GEO query vars
	 */
	$geo_query = pw_prepare_geo_query( $query );
	$where = pw_query_combine_where_clauses( $where, $geo_query );

	//pw_log('TIME QUERY', $geo_query);
	//pw_log('WHERE CLAUSE', $where);

	return $where;
}


/**
 * Combines two MySQL WHERE clauses, where one of them might be empty.
 */
function pw_query_combine_where_clauses( $clause1 = '', $clause2 = '', $operator = 'AND' ){

	$output = '';
	$clause1 = trim( $clause1 );
	$clause2 = trim( $clause2 );

	if( !empty($clause1) && !empty($clause2) )
		return $clause1 . " " . $operator . " " . $clause2;

	if( !empty( $clause1 ) )
		return $clause1;

	if( !empty( $clause2 ) )
		return $clause2;

	return '';

}


function pw_prepare_time_query( $query ){

	$time_query = '';
	$add_and = false;

	if(array_key_exists('event_start',  $query)){
		// all events with event_end after this
		$event_start = $query['event_start'];
		
		if($add_and == false){
			$time_query = "event_end > ".$event_start;
			$add_and = true;	
		} else {
			$time_query = " AND event_end > ".$event_start;
		}
	}

	///// event_end /////
	if(array_key_exists('event_end',  $query)){
		// all events with event_start before this
		$event_end = $query['event_end'];

		if($add_and == false){
			$time_query .= "event_start < ".$event_end;
			$add_and = true;
		} else {
			$time_query .= " AND event_start < ".$event_end;
		}
	}

	///// event_before /////
	if(array_key_exists('event_before',  $query)){
		// all events with event_end before this
		$event_before = $query['event_before'];

		if($add_and == false){
			$time_query .= "event_end < ".$event_before;
			$add_and = true;
		} else {
			$time_query .= " AND event_end < ".$event_before;
		}
	}

	///// event_after /////
	if(array_key_exists('event_after',  $query)){
		//pw_log( 'PW_Query : event_after', $query['event_after'] );
		// all events with event_start after this
		$event_after = $query['event_after'];

		if($add_and == false){
			$time_query .= "event_start > ".$event_after;
			$add_and = true;
		} else {
			$time_query .= " AND event_start > ".$event_after;
		}
	}

	/**
	 * FILTER EVENTS
	 * Adds a complex of clauses based on predefined
	 * filter options. Clauses are grouped with OR operator,
	 * and so are internally additive.
	 */
	if( array_key_exists('event_filter',  $query) ){
		$current_timestamp = time();
		$event_filters = $query['event_filter'];

		// Force string value as an array
		if( is_string( $event_filters ) )
			$event_filters = array( $event_filters );	

		// If 'event_filter' is not the first clause, add 'AND'
		if( $add_and )
			$time_query .= " AND ";
		$add_and = true;

		/**
		 * @todo :  Here make sure all the event filters
		 *			are from a list of pre-registered options
		 *			Otherwise it could mess up the rest of the query. //**
		*/
		$filter_count = count( $event_filters );

		// Wrap in brackets if multiple filters
		if( $filter_count > 1 )
			$time_query .= " ( ";

		// Iterate through each of the event filters
		$i = 0;
		foreach( $event_filters as $filter ){
			$i++;
			$last_iteration = ( $i === $filter_count ) ? true : false;

			// Switch queries added based on filter
			switch( $filter ){
				case 'past':
					$time_query .= "event_end < ".$current_timestamp;
					break;

				case 'now':
				case 'current':
					$time_query .= "( event_end > ".$current_timestamp." AND event_start < ".$current_timestamp ." ) ";
					break;

				case 'future':
				case 'upcoming':
					$time_query .= "event_start > ".$current_timestamp;
					break;
			}

			// If there is a following clause, add 'OR'
			if( !$last_iteration )
				$time_query .= " OR ";

		}

		// Wrap in brackets if multiple filters
		if( $filter_count > 1 )
			$time_query .= " ) ";

	}

	// Return Query
	if($time_query == "") return "";
	return $time_query; //." AND ";
}



function pw_prepare_geo_query( $query ){
	///// GEO LATITUDE /////

	$geo_query = '';

	$latitude = _get( $query, 'geo_latitude' );
	$longitude = _get( $query, 'geo_longitude' );

	if( array_key_exists('geo_range',  $query) && ( $latitude || $longitude ) ){
		/**
		 * Range query
		 * Search for values between lat and long ranges
		 */
		$range = $query['geo_range'];

		if( $latitude ){
			$lat_low = $latitude - $range;
			$lat_high = $latitude + $range;
			$lat_query = "geo_latitude BETWEEN ".$lat_low." AND ".$lat_high;
		} else{
			$lat_query = '';
		}

		if( $longitude ){
			$lng_low = $longitude - $range;
			$lng_high = $longitude + $range;
			$lng_query ="geo_longitude BETWEEN ".$lng_low." AND ".$lng_high;
		} else{
			$lng_query = '';
		}

		$geo_query = pw_query_combine_where_clauses( $lat_query, $lng_query, 'AND' );

	} else {
		/**
		 * Default geo query
		 * Search for exact values
		 */
		if( $latitude )
			$geo_query = "geo_latitude = ".$latitude;
		if( $latitude & $longitude )
			$geo_query .= " AND ";
		if( $longitude )
			$geo_query.="geo_longitude = ".$longitude;
	}

	return $geo_query;
}



// FILTER : posts_fields : SELECT
// FILTER : posts_clauses : ALL CLAUSES **!!
// FILTER : posts_results : POSTS AFTER QUERYING
