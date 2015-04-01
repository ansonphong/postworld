<?php
/**
 * Retrieve related post IDs of related posts, based a list of Related By Clauses.
 *
 * @since Postworld 1.89
 * @uses pw_query(), pw_related_posts_by_taxonomy()
 *
 * @param string $var       Ann array of variables
 * @return array 			Post IDs
 */
function pw_related_query( $vars = array() ){

	global $post;

	///// SET DEFAULTS /////
	$defaultVars = array(
		'post_id' 		=>	'this_post',
		'number'		=>	10,				// The number of items to return
		'depth'			=>	1000,			// The depth at which to query
		'order_by'		=>	'score',		// By what to order items
		'order'			=>	'DESC',			// How to order items
		'output'		=>	'ids',			// Item contents, values: ids
		'cache'			=>	true,			// Whether or not to use Postworld DB cache
		'query'			=>	array(			// Query vars to filter posts by
			'post_type' => 'any'
			),
		'related_by'	=>	array( 			// An array of arrays representing Related By Clauses

			// TYPE : TAXONOMY
			array(
				'type' => 'taxonomy',
				'weight' => 1,
				'taxonomies'	=>	array(
					array(
						'taxonomy' 	=> 	'post_tag',
						'weight'	=>	1.5
						),
					array(
						'taxonomy' 	=> 	'category',
						'weight'	=>	1
						)
					)
				),

			// TYPE : FIELD (IN DEVELOPMENT)
			array(
				'type'		=>	'field',
				'weight'	=>	1,
				'fields'	=>	array(
					array(
						'field' => 'post_title',
						'weight' => 2
						),
					array(
						'field' => 'post_excerpt',
						'weight' => 1.5
						),
					array(
						'field' => 'post_parent',
						'weight' => 1.25
						),
					array(
						'field' => 'post_author',
						'weight' => 1
						)
					)
				),


			),
		);
	$vars = array_replace( $defaultVars, $vars );

	///// SMART VARIABLES /////
	if( $vars['post_id'] == 'this_post' )
		$vars['post_id'] = $post->ID;
	if( $vars['post_id'] === null )
		return false;

	///// CACHING LAYER /////

	///// POSTS /////
	// An array of objects, with the following structure
	// [{ post_id:42, score:3 },{ post_id:82, score:2 }]
	$posts = array();
	
	// Posts from respective clauses will be stored in here as an array of arrays
	// Before being merged together
	$all_clause_posts = array();

	///// ITERATE THROUGH EACH CLAUSE /////
	foreach( $vars['related_by'] as $clause ){

		/// CONSTRUCT SUBFUNCTION VARIABLES ///
		// Theses variables are fed into the respective clause type functions
		$by_vars = array(
			'post_id'	=>	$vars['post_id'],
			'depth'		=>	$vars['depth'],
			'query'		=>	$vars['query'],
			'number'	=>	0,
			'order_by'	=>	'none',
			);

		/// CLAUSE TYPE : SWITCH ///
		switch( $clause['type'] ){

			// TAXONOMY
			case 'taxonomy':
				$by_vars['taxonomies'] = $clause['taxonomies'];
				$clause_posts = pw_related_posts_by_taxonomy( $by_vars );
				break;

			// FIELDS (IN DEVELOPMENT)
			case 'fields':
				continue;
				//$by_vars['fields'] = $clause['fields'];
				//$clause_posts = pw_related_posts_by_field( $by_vars );
				break;
			
		}

		/// CLAUSE WEIGHT : DEFAULT ///
		if( !isset( $clause['weight'] ) || !is_numeric( $clause['weight'] ) )
			$clause['weight'] = 1;
		else
			$clause['weight'] = (float) $clause['weight'];

		/// CLAUSE WEIGHT : APPLY ///
		if( $clause['weight'] !== 1 )
			$clause_posts = pw_multiply_scores( $clause_posts, $clause['weight'] );

		/// ADD TO ALL ///
		$all_clause_posts[] = $clause_posts;

	}

	/// CLAUSE POSTS : MERGE ///
	// Merge all the clause posts arrays
	if( count( $all_clause_posts ) > 1 )
		$posts = pw_merge_score_values( $all_clause_posts, 'post_id' );
	else if( count( $all_clause_posts ) == 1 )
		$posts = $all_clause_posts[0];
	else
		return array();

	/// ORDER BY : SCORE ///
	if( $vars['order_by'] == 'score' )
		$posts = pw_order_by_score( $posts );

	/// ORDER : ASCENDING ///
	if( $vars['order'] == 'ASC' )
		$posts = array_reverse($posts);

	/// NUMBER : MAX RETURN ITEMS ///
	if( $vars['number'] != 0 && is_numeric($vars['number']) ){
		$number = (int) $vars['number'];
		// Get only the first number of items
		$posts = array_slice( $posts, 0, $number );
	}

	/// OUTPUT : IDS /////
	// Extract just the IDs
	if( $vars['output'] === 'ids' ){
		$post_ids = array();
		foreach( $posts as $post ){
			$post_ids[] = $post['post_id'];			
		}
		$posts = $post_ids;
	}


	///// CACHING LAYER /////


	return $posts;

}


/**
 * Retrieve a scored array of related post IDs based on related taxonomy parameters.
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $var       An array of variables
 * @return array 			Array of objects, scored post IDs
 * 							Example. [{ post_id:42, score:3 },{ post_id:82, score:2 }]
 */
function pw_related_posts_by_taxonomy( $vars ){

	///// DEFAULTS /////
	global $post;
	$defaultVars = array(
		'post_id' 	=> 	$post->ID,	// The post ID by which to find posts in relation to
		'number'	=>	0,			// Numer of posts to return (0 returns all)
		'depth' 	=> 	1000,		// Depth of posts to query
		'order_by'	=>	'score', 	// Optional values : none / score
		'order'		=>	'DESC',		// Optional values : DESC / ASC
		'output'	=>	'scored',	// Optional values : scored / ids
		'query'	=>	array(			// Query vars for returned posts
			'post_type' => 'post'
			),
		'taxonomies' => array(		// Taxonommies to return and their weight
			array(
				'taxonomy' => 'post_tag',
				'weight' => 1.5,
				),
			array(
				'taxonomy' => 'category',
				'weight' => 1,
				),
			),
		);
	$vars = array_replace( $defaultVars, $vars );

	///// GET POST TERMS /////
	// Generates an associative array with the post's terms
	// $post_terms = { category:[34,37], post_tag:[94,21,9,84,23,12] }
	$post_terms = array();
	foreach( $vars['taxonomies'] as $tax ){

		// If taxonomy doesn't exist, continue
		if( !taxonomy_exists( $tax['taxonomy'] ) )
			continue;

		// Get an array of term IDs
		$term_ids = wp_get_post_terms(
			$vars['post_id'],
			$tax['taxonomy'],
			array(
				'fields' => 'ids'
				));

		// If there's results, add them to the terms
		if( !empty( $term_ids ) )
			$post_terms[ $tax['taxonomy'] ] = $term_ids;

	}

	///// GET RELATED POSTS /////
	// Generates an array of associative arrays, with possible duplicate IDs
	// $tax_posts = { category:[53,23,74,475,74,378], post_tag:[234,264,264,856] }
	// Each term in each taxonomy is queried and appended to a list of results,
	// Which may include duplicates, which are then scored with value set per taxonomy

	/// CONSTRUCT QUERY VARS ///
	$query_vars = $vars['query'];
	$query_vars['fields'] = 'ids';
	$query_vars['posts_per_page'] = $vars['depth'];

	// VAR : POSTS
	// The final result, all posts scored
	$posts = array();

	// VAR : POSTS SCORED
	// Associative Array to collect the tax posts scored under taxonomy keys
	// {category:[{post_id:42,score:3},{...}],post_tag:[{post_id:84,score:2},{...}]}	
	$posts_scored = array();

	// VAR : TAXONOMY POSTS
	// Associative Array to collect the tax posts
	// {category:[42,24,64,32],post_tag:[84,237,45,92]}	
	$post_ids = array();

	// Iterate through each taxonomy > terms set
	foreach( $post_terms as $tax => $terms ){
		$post_ids[$tax] = array();

		// Iterate through each term
		foreach( $terms as $term ){
			// Reset taxonomy query
			$query_vars['tax_query'] = array();

			// Construct the tax query
			$query_vars['tax_query'][] = array(
				'taxonomy' 	=> 	$tax,
				'terms'		=> 	array( $term ),
				'field'		=>	'id',
				'operator'	=>	'in'
				);

			// Recursively query
			$query = new PW_Query( $query_vars );

			// Get the post IDs
			$term_posts = $query->get_posts();

			// Add new posts to taxonomy posts array
			if( !empty( $term_posts ) )
				$post_ids[$tax] = array_merge( $post_ids[$tax], $term_posts );

		}

		/// GET TAXONOMY WEIGHT ///
		$taxonomy_obj = pw_find_where( $vars['taxonomies'], array( 'taxonomy' => $tax ) );
		if( isset( $taxonomy_obj['weight'] ) )
			$taxonomy_weight = (float) $taxonomy_obj['weight'];
		else
			$taxonomy_weight = 1;

		/// SCORE POSTS ///
		$posts_scored[$tax] = pw_score_values( array(
			'values' 		=> 	$post_ids[$tax],
			'value_key'		=>	'post_id',
			'value_type'	=>	'integer',
			'value_weight'	=>	$taxonomy_weight
			));

	}

	/// MERGE SCORES ///
	// Merge the scores from results from various taxonomies
	$scores_to_merge = array();
	foreach($posts_scored as $tax => $scored_posts_array ){
		$scores_to_merge[] = $scored_posts_array;
	}
	// If there are multiple arrays, merge them
	if( count($scores_to_merge) > 1 )
		$posts = pw_merge_score_values( $scores_to_merge, 'post_id' );
	// If there is only one, use it
	elseif( count( $scores_to_merge ) == 1 )
		$posts = $scores_to_merge[0];
	// Otherwise return empty
	else
		return array();

	/// ORDER BY : SCORE ///
	if( $vars['order_by'] == 'score' )
		$posts = pw_order_by_score( $posts );

	/// ORDER : ASCENDING ///
	if( $vars['order'] == 'ASC' )
		$posts = array_reverse($posts);

	/// NUMBER : MAX RETURN ITEMS ///
	if( $vars['number'] != 0 && is_numeric($vars['number']) ){
		$number = (int) $vars['number'];
		// Get only the first number of items
		$posts = array_slice( $posts, 0, $number );
	}

	/// OUTPUT : IDS /////
	// Extract just the IDs
	if( $vars['output'] === 'ids' ){
		$post_ids = array();
		foreach( $posts as $post ){
			$post_ids[] = $post['post_id'];			
		}
		$posts = $post_ids;
	}

	return $posts;

}


/**
 * Boil down arrays of values into an array of scored values
 *
 * @since Postworld 1.89
 *
 * @param string $arr       	A 1D array of values
 * @param string $value_key     The key to label the values in the associative array
 * @param string $score_key     The key to label the score in the associative array
 * @return array 				Associative array of scored values
 * 								[{id:42,score:8},{id:87,score:2},{id:34,score:42}]
 */
function pw_score_values( $vars = array() ){

	///// DEFAULT VALUES /////
	$defaultVars = array(
		'values' 		=>	array(),
		'value_key'		=>	'id',
		'score_key'		=>	'score',
		'value_type'	=>	'integer',
		'value_weight'	=>	1,
		);
	$vars = array_replace( $defaultVars, $vars );
	extract( $vars );

	// Return early if no or wrong values
	if( empty($values) || !is_array( $values ) ){
		if( is_array( $values ) )
			return array();
		return false; 
	}

	///// SCORE VALUES /////
	// Scores an array of values into an array of arrays
	// [42,24,42,64,32,42] >> [{id:42,score:3},{id:24,score:1}...]
	$score_objs = array();
	// Iterate through each value
	foreach( $values as $value ){
		// Typecast
		switch($value_type){
			case 'integer':
			case 'int':
				$value = (int) $value;
				break;
			case 'string':
				$value = (string) $value;
				break;
			case 'double':
				$value = (double) $value;
				break;
			case 'float':
				$value = (float) $value;
				break;
		}

		// Check if it's already in the scored values array
		$already_scored = false;
		// Current Index
		$i=0;
		foreach( $score_objs as $score_obj ){
			$i++;
			// If a match is found in already scored objects
			if( $score_obj[$value_key] == $value ){
				// Increase the score
				$score_objs[$i][$score_key] = $score_objs[$i][$score_key] + $value_weight;
				// Mark it already scored
				$already_scored = true;
			}
		}

		// If it hasn't been scored yet
		if( !$already_scored ){
			// Create the score object
			$new_obj = array();
			$new_obj[$value_key] = $value;
			$new_obj[$score_key] = $value_weight;
			// Add it to the other score objs
			$score_objs[] = $new_obj;
		}

	}

	return $score_objs;

}



/**
 * Merges an array of scored value arrays on the value key, into a single array
 *
 * @since Postworld 1.89
 *
 * @param string $arrays       	An array of scored item arrays
 * @param string $value_key 	The key to label the values in the associative array
 * @param string $score_key     The key to label the score in the associative array
 * @return array 				Associative array of scored values
 * 								[{id:42,score:8},{id:87,score:2},{id:34,score:42}]
 */
function pw_merge_score_values( $arrays, $value_key = 'id', $score_key = 'score' ){
	/*
		$arrays = [
			[ { id:42, score:3 }, { id:64, score:2 } ],
			[ { id:42, score:1 } ]
		];
	*/

	// If the format is wrong, return empty array
	if( !is_array( $arrays ) ||
		!is_array( $arrays[0] ) ||
		!is_array( $arrays[0][0] ) )
		return array();

	// The container for the final merged items
	$merged_items = array();

	// Iterate through each of the provided arrays
	foreach( $arrays as $scored_items ){
		// Iterate through each item in the array
		foreach( $scored_items as $scored_item ){
			// Iterate through the merged items
			$i = 0;
			$in_merged = false;
			foreach( $merged_items as $merged_item ){
				$i++;
				// Check if the scored item equals the merged item
				if( $scored_item[$value_key] === $merged_item[$value_key] ){
					// If so, add the item's score to the merged item's score
					$merged_items[$i][$score_key] = $merged_items[$i][$score_key] + $scored_item[$score_key];
					$in_merged = true;
					break;
				}
			}
			// If the item isn't already in merged items, add it
			if( !$in_merged )
				$merged_items[] = $scored_item;
		}
	}

	//pw_log( 'merged_items = ' . json_encode($merged_items,JSON_PRETTY_PRINT) );
	return $merged_items;

}

/**
 * Orders an array of scored items by score
 *
 * @since Postworld 1.89
 *
 * @param string $items       	Array of associative arrays
 * @param string $score_key     The score key to order the associative arrays by
 * @return array 				Re-ordered array of associative arrays
 * 								[{id:42,score:8},{id:87,score:3},{id:34,score:1}]
 */
function pw_order_by_score( $items, $score_key = 'score' ){
	$sorted = pw_array_order_by( $items, $score_key, SORT_DESC );
	return $sorted;
}



/**
 * Multiplies the score values of a scored list
 *
 * @since Postworld 1.89
 *
 * @param string $items       	Array of associative arrays
 * @param float $multiplier		A number to muliply the scores by
 * @param string $score_key     The score key identify the score by
 * @return array 				Re-scorred array of associative arrays
 * 								[{id:42,score:8},{id:87,score:3},{id:34,score:1}]
 */
function pw_multiply_scores( $items, $multiplier = 1, $score_key = 'score' ){

	$multiplier = (float) $multiplier;

	if( $multiplier === 1 )
		return $items;

	$item_count = count($items);

	if( $item_count < 1 )
		return array();

	for( $i=0; $i < $item_count; $i++ ){
		$item[$i][$score_key] = $item[$i][$score_key] * $multiplier;
	}

	return $items;

}

/**
 * Retrieve a scored array of related post IDs based on related field parameters.
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $var       Ann array of variables
 * @return array 			Array of objects, scored post IDs
 */
function pw_related_posts_by_field( $vars ){

	$defaultVars = array(
		'fields' => array(
			array(
				'field' 	=> 'post_title',
				'weight' 	=> 2
				),
			array(
				'field' 	=> 'post_excerpt',
				'weight' 	=> 1.5
				),
			array(
				'field' 	=> 'post_parent',
				'weight' 	=> 1.25
				),
			array(
				'field' 	=> 'post_author',
				'weight' 	=> 1
				),
			)
		);

}



?>