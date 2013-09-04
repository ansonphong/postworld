<?php

function pw_live_feed ( $args ){
	/*
	• Helper function for the pw_live_feed() JS method
	• Used for custom search querying, etc.
	• Does not access wp_postworld_feeds caches at all

	INPUT :
	$args = array (
		'feed_id'		=> string,
		'bootload'		=> integer,
		'feed_query'	=> array( pw_query )
	)
	*/

	extract($args);

	$feed_outline = pw_feed_outline( $feed_query );
	$bootload_posts = array_slice( $feed_outline, 0, $bootload );
	$post_data = pw_get_posts( $bootload_posts, $feed_query['data_fields'] );

	return compact( $feed_outline, $post_data );
}



function pw_feed_outline ( $pw_query_args ){
	// • Uses pw_query() method to generate an array of post_ids based on the $pw_query_args

	$pw_query_args['data_fields'] = array('id');
	$post_array = pw_query($pw_query_args); // <<< TODO : Flatten from returned Object to Array of IDs

	return $post_array; // Array of post IDs
}


?>