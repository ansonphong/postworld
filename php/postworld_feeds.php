<?php

function pw_live_feed ( $args ){
	/*
	• Helper function for the pw_live_feed() JS method
	• Used for custom search querying, etc.
	• Does not access wp_postworld_feeds caches at all

	INPUT :
	$args = array (
		'feed_id'		=> string,
		'preload'		=> integer,
		'feed_query'	=> array( pw_query )
	)
	*/

	extract($args);

	// Get the Feed Outline
	$feed_outline = pw_feed_outline( $feed_query );
	
	// Select which posts to preload
	$preload_posts = array_slice( $feed_outline, 0, $preload );
	
	// Preload selected posts
	$post_data = pw_get_posts( $preload_posts, $feed_query['fields'] );

	// Return Data
	return compact( $feed_outline, $post_data );
}



function pw_feed_outline ( $pw_query_args ){
	// • Uses pw_query() method to generate an array of post_ids based on the $pw_query_args

	$pw_query_args['fields'] = array('id');
	$post_array = pw_query($pw_query_args); // <<< TODO : Flatten from returned Object to Array of IDs

	return $post_array; // Array of post IDs
}



//convert object to array $array =  (array) $yourObject;
	class pw_query_args{
		public $post_type;
		public $post_format;//pw
		public $post_class;//pw
		public $author;
		public $author_name;
		public $year;
		public $month;
		public $tax_query;
		public $s;
		public $orderby='date';
		public $order='DESC';
		public $posts_per_page="-1";
		public $fields;
		
		
		
		
		
	}


?>