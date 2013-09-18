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


function pw_query($args) {
	
	/*
	 * Description:
		• Similar to the functionality of WP_Query : http://codex.wordpress.org/Class_Reference/WP_Query 
		
		• Query by Postworld data fields post_format & post_class
		• Sort by points & rank_score
		• Define which fields are returned using pw_get_posts() method
		• Can determine the return_format as JSON, PHP Associative Array or WP post objects
		
		
		Process:
		• After querying and ordering is finished, if more than IDs are required to return, use pw_get_posts() method to return specified fields
		
		return : PHP Object / JSON / WP_Query
	 
	 * */
	
		$the_query = new PW_Query($args);
	
		return ("<br>".json_encode($the_query))."<br>";
	
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