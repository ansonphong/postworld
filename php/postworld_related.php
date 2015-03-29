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
		'related_by'	=>	array(),	// An array of objects representing Related By Clauses
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	///// SMART VARIABLES /////
	if( $vars['post_id'] == 'this_post' )
		$vars['post_id'] = $post->ID;

	///// POSTS /////
	// An array of objects, with the following structure
	// [{ post_id:42, score:3 },{ post_id:82, score:2 }]
	$posts = array();

	///// ITERATE THROUGH EACH CLAUSE /////
	foreach( $vars['related_by'] as $clause ){

		/// CONSTRUCT SUBFUNCTION VARIABLES ///
		// Theres variables are fed into the respective clause type functions
		$clauseVars = array(
			'post_id'	=>	$vars['post_id'],
			'depth'		=>	$vars['depth'],
			'clause'	=>	$clause,
			);

		/// CLAUSE TYPE : SWITCH ///
		switch( $clause['type'] ){
			case 'taxonomy':
				$get_posts = pw_related_posts_by_taxonomy( $vars );
				break;
			case 'fields':
				$get_posts = pw_related_posts_by_field( $vars );
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
	// http://stackoverflow.com/questions/4282413/sort-array-of-objects-by-object-fields


	///// CACHE /////


}

/**
 * Retrieve a scored array of related post IDs based on related taxonomy parameters.
 *
 * @since Postworld 1.89
 * @uses pw_query()
 *
 * @param string $var       Ann array of variables
 * @return array 			Array of objects, scored post IDs
 * 							Example. [{ post_id:42, score:3 },{ post_id:82, score:2 }]
 */
function pw_related_posts_by_taxonomy( $vars ){

	$defaultVars = array(
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