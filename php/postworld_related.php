<?php
/**
 * Retrieve related post IDs of related posts, based a list of Related By Clauses.
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $var       Ann array of variables
 * @return array 			Post IDs
 */
function pw_related_query( $vars = array() ){

	global $post;

	///// SET DEFAULTS /////
	$defaultVars = array(
		'post_id' 		=>	'this_post',
		'number'		=>	10,
		'depth'			=>	1000,
		'order_by'		=>	'relevance',
		'cache'			=>	true,
		'related_by'	=>	array(),	// An array of objects representing Related By Clauses
		);
	$vars = array_replace_recursive( $defaultVars, $vars );

	///// SMART VARIABLES /////
	if( $vars['post_id'] == 'this_post' )
		$vars['post_id'] = $post->ID;


	///// CACHING LAYER /////


	///// POSTS /////
	// An array of objects, with the following structure
	// [{ post_id:42, score:3 },{ post_id:82, score:2 }]
	$posts = array();

	///// ITERATE THROUGH EACH CLAUSE /////
	foreach( $vars['related_by'] as $clause ){

		/// CONSTRUCT SUBFUNCTION VARIABLES ///
		// Theses variables are fed into the respective clause type functions
		$by_vars = array(
			'post_id'	=>	$vars['post_id'],
			'depth'		=>	$vars['depth'],
			);

		/// CLAUSE TYPE : SWITCH ///
		switch( $clause['type'] ){
			case 'taxonomy':
				$by_vars['taxonomies'] = $clause['taxonomies'];
				$get_posts = pw_related_posts_by_taxonomy( $by_vars );
				break;
			case 'fields':
				$by_vars['fields'] = $clause['fields'];
				$get_posts = pw_related_posts_by_field( $by_vars );
				break;
		}

		/// CLAUSE WEIGHT : DEFAULT ///
		if( !isset( $clause['weight'] ) )
			$clause['weight'] = 1;
		else
			$clause['weight'] = (double) $clause['weight'];

		/// CLAUSE WEIGHT : APPLY ///



		/// CLAUSE POSTS : MERGE ///
		// Merge the clause posts with the primary posts array
		// Iteratively merge by post ids, adding their scores


	}


	///// ORDER POSTS /////
	// Order the posts by the specified ordering method


	///// CACHING LAYER /////




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
		'post_id' => $post->ID,
		'depth' => 1000,
		'taxonomies' => array(
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
	$vars = array_replace_recursive( $defaultVars, $vars );


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




}



/**
 * Boil down arrays of values into an array of scored values
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $arr       	A 1D array of values
 * @param string $value_key     The key to label the values in the associative array
 * @param string $score_key     The key to label the score in the associative array
 * @return array 				Associative array of scored values
 * 								[{id:42,score:8},{id:87,score:2},{id:34,score:42}]
 */
function pw_score_values( $values, $value_key = 'id', $score_key = 'score' ){



}



/**
 * Merge
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $arr       	A 1D array of values
 * @param string $value_key 	The key to label the values in the associative array
 * @param string $score_key     The key to label the score in the associative array
 * @return array 				Associative array of scored values
 * 								[{id:42,score:8},{id:87,score:2},{id:34,score:42}]
 */
function pw_merge_score_values( $arr, $value_key = 'id', $score_key = 'score' ){

}


function pw_multiply_score_values( $arr, $score_key = 'score' ){

}


function pw_order_score_values( $arr, $score_key = 'score' ){
	// http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields

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