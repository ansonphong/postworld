<?php

function pw_live_feed ( $args ){
	/*
	• Used for custom search querying, etc.
	• Does not access wp_postworld_feeds caches at all
	• Helper function for the pw_live_feed() JS method
	
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



?>