<?php
/**
 * Retrieve related post IDs of related posts.
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
		'order_by'		=>	'relevance',
		'related_by'	=>	array(),	// An array of objects, executed in order
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	///// SMART VARIABLES /////
	if( $vars['post_id'] == 'this_post' )
		$vars['post_id'] = $post->ID;

	///// POST IDS /////
	$post_ids = array();

	///// TAXONOMY /////
	if( !empty( $vars['taxonomy'] ) ){
		$post_ids_taxonomy = pw_related_posts_taxonomy( $vars['post_id'], $vars['number'], $vars['taxonomy'] );
		$post_ids = array_merge( $post_ids, $post_ids_taxonomy );
	
		// Run Taxonomy Query
		// Merge scores with existing IDs

	}

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
 */
function pw_related_posts_by_taxonomy( $post_id, $number, $vars ){

	$defaultVars = array(
		'type' => 'taxonomy',	
		'taxonomies' => array( 'post_tag', 'category' ),
		'fields' => array(
			'terms',		// Searches in other terms
			'post_title',	// Searches for the term titles in post titles
			'post_excerpt', // Searches for the term titles in post excerpts
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
function pw_related_posts_by_fields( $post_id, $number, $vars ){

	$defaultVars = array(
		'type' => 'fields',
		'fields' => array(
			'post_title',	// 	Searches for related posts by post title
			'post_excerpt',	// 	Searches for related posts by post excerpt
			'post_author',	//	Searches for related posts by post author
			'post_parent', 	// 	Searches for related posts by post parent
			),
		);

}



?>