<?php

///// PREPARE QUERY FILTER : POST PARENT FROM /////
function pw_prepare_query_post_parent_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['post_parent_from'] ) ){
		switch( $query['post_parent_from'] ){
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
add_filter( 'pw_prepare_query', 'pw_prepare_query_post_parent_from' );


///// PREPARE QUERY FILTER : EXCLUDE POST FROM /////
function pw_prepare_query_exclude_posts_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['exclude_posts_from'] ) ){
		switch( $query['exclude_posts_from'] ){
			case 'this_post_id':
				$query['post__not_in'] = array( $post->ID );
				break;
		}
	}
	return $query;
}
add_filter( 'pw_prepare_query', 'pw_prepare_query_exclude_posts_from' );


///// PREPARE QUERY FILTER : INCLUDE POST FROM /////
function pw_prepare_query_include_posts_from( $query ){
	global $post;
	/// POST PARENT FROM FIELD ///
	if( isset( $query['include_posts_from'] ) ){
		switch( $query['include_posts_from'] ){
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

		//pw_log( '$query : ' . json_encode($query) );
	}

	return $query;
}
add_filter( 'pw_prepare_query', 'pw_prepare_query_include_posts_from' );

///// PREPARE QUERY FILTER : INCLUDE POST FROM /////
function pw_prepare_query_default_post( $query ){
	// Set the post type to 'any' if not defined
	$post_type = pw_get_obj( $query, 'post_type' );
	if( $post_type == null || empty( $post_type ) || !isset( $post_type ) )
		$query['post_type'] = 'any';
	return $query;
}
add_filter( 'pw_prepare_query', 'pw_prepare_query_default_post' );

?>