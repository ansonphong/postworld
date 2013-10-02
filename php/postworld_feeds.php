<?php

function pw_live_feed ( $args ){
	/*
	
	Description:

	Used for custom search querying, etc.
	Does not access wp_postworld_feeds caches at all
	Helper function for the pw_live_feed() JS method
	Parameters: $args
	
	feed_id : string
	
	preload : integer => Number of posts to fetch data and return as post_data
	feed_query : Array
	pw_query() Query Variables
	
	 
	Process:
	
	Generate return feed_outline , with pw_feed_outline( $args[feed_query] ) method
	Generate return post data by running the defined preload number of the first posts through pw_get_posts( feed_outline, $args['feed_query']['fields'] )
	Usage:
	
	$args = array (
	     'feed_id' => {{string}},
	     'preload'  => {{integer}}
	     'feed_query' => array(
	          // pw_query args    
	     )
	)
	$live_feed = pw_live_feed ( *$args* );
	return : Object
	
	array(
	    'feed_id' => {{string}},
	    'feed_outline' => '12,356,3564,2362,236',
	    'loaded' => '12,356,3564',
	    'preload' => {{integer}},
	    'post_data' => array(), // Output from pw_get_posts() based on feed_query
	)
	 *  
	 
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
	$feed_query = $args["feed_query"];

	$feed_outline = pw_feed_outline( $feed_query );

	
	// Select which posts to preload
	$preload_posts = array_slice( $feed_outline, 0, $preload ); // to get top post ids
	
	// Preload selected posts
	$post_data = pw_get_posts($preload_posts, $feed_query["fields"] );
	
	return (array("feed_id"=>$args["feed_id"], "feed_outline"=>$feed_outline, "loaded"=>$preload_posts,"preload"=>count($post_data),"post_data"=>$post_data ));
	
}



function pw_feed_outline ( $pw_query_args ){
	// • Uses pw_query() method to generate an array of post_ids based on the $pw_query_args

	$pw_query_args["fields"] = "ids";
	$post_array = pw_query($pw_query_args); // <<< TODO : Flatten from returned Object to Array of IDs
	$post_ids  = $post_array->posts;
	

	return $post_ids; // Array of post IDs
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