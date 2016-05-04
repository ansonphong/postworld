<?php
/**
 * Sets post or comment points and updates caches
 * @internal A meta function for pw_set_post_points() and pw_set_comment_points() 
 * @see pw_get_user_vote_power(), pw_get_points_row()
 * @param $post_type [string] The type of points to be setting. Options: 'post' / 'comment'
 * @param $id [integer] The ID of the post/comment to set the points for
 * @param $set_points [integer] A positive or negative integer, how many points to set for the user
 * @return [array] A report of the current status of the post points
 */
function pw_set_points ( $point_type = 'post', $id = 0, $set_points ){
	if( !pw_config_in_db_tables('comment_points') )
		return false;

	global $wpdb;

	$user_id = get_current_user_id();
	if( $user_id === 0)
		return array('error'=>'Must be logged in to add points.');
	if( $id === 0 )
		return array('error'=>'ID must be provided.');

	// Get the user's vote power
	$user_vote_power = pw_get_user_vote_power($user_id);
	// If $set_points is greater than the user's role vote_points , reduce to vote_points
	if ( abs($user_vote_power) < abs($set_points) )
		$set_points = $user_vote_power * (abs($set_points)/$set_points); 

	// Define the table and column names to work with
	switch( $point_type ){
		case 'post':
			$table_name = $wpdb->postworld_prefix."post_points";
			$points_column = 'post_points';
			$id_column ='post_id';
			break;
		case 'comment':
			$table_name = $wpdb->postworld_prefix."comment_points";
			$points_column = 'points';
			$id_column = 'comment_id';
			break;
		default:
			return array('error'=>'Wrong point type specified.');
			break;
	}

	// Get the existing points row
	$points_row = pw_get_points_row( $point_type, $id, $user_id );

	//pw_log('points_row ',$point_type . $id . $user_id);

	if( !empty($points_row) ){
		$old_points = $points_row[ $points_column ];
		$update_points = intval($set_points) - intval($old_points);	
	}
	else
		$update_points = 0;
	

	///// DELETE /////
	// If setting points to 0
	if( $set_points === 0 ){
		// And a points row exists
		if( $points_row ){
			// Delete the row
			$query = "
				DELETE from ".$table_name."
				WHERE $id_column=" . $id . "
				AND user_id=" . $user_id;
			$wpdb->query($query);
		}
	}
	
	// If there are points to add / update
	else {

		switch( $point_type ){
			case 'post':
				if($points_row){
					// Update
					pw_update_post_points( $id, $user_id, $set_points );
				}
				else{
					// Add
					pw_insert_post_points( $id, $user_id, $set_points );
				}
				break;
			case 'comment':
				if($points_row)
					// Update
					pw_update_comment_points( $id, $user_id, $set_points );
				else
					// Add
					pw_insert_comment_points( $id, $user_id, $set_points );
				break;
		}

		if(!$points_row)
			$update_points = $set_points;

	}

	switch( $point_type ){
		case 'post':
			// Cache Post Points
			$points_total = pw_cache_post_points( $id );
			//pw_log( 'points_total : ', $points_total );
			// Cache Points of the Author
			$postdata = get_post( $id );
			pw_cache_user_posts_points( $postdata->post_author );
			break;
		case 'comment':
			// Cache Comment Points
			$points_total = pw_cache_comment_points( $id );
			// Cache Points of the Author
			$commentdata = get_comment( $id );
			pw_cache_user_comments_points( $commentdata->user_id );
			break;
	}

	$output = array(
	     'point_type' 	=>	$point_type,	// post / comment
	     'user_id' 		=> 	$user_id, 		// user ID
	     'id' 			=> 	$id, 			// post/comment ID
	     'user_points' 	=> 	$set_points, 	// total points from user to this item
	     'points_added' => 	$update_points, // points which were successfully added
	     'points_total' => 	$points_total 	// from wp_postworld_meta
	);
	 
	return $output;
}


/**
 * Get the total number of points of the given post from the points column in Postworld Post Meta table
 * @param $post_id [integer] The post ID to get the post points of
 * @return [integer] Number of points
 */
function pw_get_post_points($post_id) {
	if( !pw_config_in_db_tables('post_meta') )
		return false;

	global $wpdb;
	
	$query = "
		SELECT *
		FROM " . $wpdb->postworld_prefix.'post_meta' . "
		WHERE post_id=" . $post_id;

	$row = $wpdb->get_row($query);

	if ($row != null)
		return $row->post_points;
	else
		return 0;
}

/**
 * Adds up the points from the specified post, stored in Postworld Post Points table"
 * @param $post_id [integer] The post ID to calculate the post points of
 * @return [integer] Number of points
 */
function pw_calculate_post_points( $post_id ) {
	if( !pw_config_in_db_tables('post_points') )
		return false;

	global $wpdb;

	$query = "
		SELECT SUM(post_points)
		FROM ".$wpdb->postworld_prefix.'post_points'."
		WHERE post_id=" . $post_id;

	$points_total = $wpdb->get_var( $query );

	if( $points_total == null || $points_total == '' )
		$points_total = 0;

	return $points_total;

}

/**
 * Calculates and then caches the number of points for a post
 * @see pw_calculate_post_points()
 * @param $post_id [integer] The post ID to calculate the post points of
 * @return [integer] Number of points
 */
function pw_cache_post_points ( $post_id ){

	if( !pw_config_in_db_tables('post_meta') )
		return false;

	global $wpdb;

	$total_points = pw_calculate_post_points($post_id);

	$query = "
		UPDATE ".$wpdb->postworld_prefix.'post_meta'."
		SET post_points=" . $total_points . "
		WHERE post_id=" . $post_id;

	$result = $wpdb->query($query);
	
	if ($result === FALSE || $result === 0){
		//insertt new row for this post in post_meta, no points was added
		
		/*1- get post data
		 2-  Insert new record into post_meta
		 */
		pw_insert_post_meta($post_id,$total_points);
		
	}
	return $total_points;
}



/**
 * Gets all post data and inserts a record in Postworld Post Meta table
 * @todo Refactor to accept an array
 */
function pw_insert_post_meta( $post_id, $points=0, $rank_score=0, $favorites=0, $post_shares=0 ){
	if( !pw_config_in_db_tables('post_meta') )
		return false;

	global $wpdb;	
	
	if(!pw_post_meta_exists($post_id)){
		$format = get_post_format(); // TODO : Phong - Why this function used?
		if ( false === $format )
		$format = 'standard';
		
		$post_data = get_post( $post_id, ARRAY_A );

		$query = "
			INSERT INTO ".$wpdb->postworld_prefix.'post_meta'." 
				(`post_id`,
				`author_id`,
				`post_class`,
				`link_format`,
				`link_url`,
				`post_points`,
				`rank_score`,
				`post_shares`
				)
			VALUES("
				.$post_id.","
				.$post_data['post_author'].","
				."'post_class'"."," //TODO
				."'".$format."',"
				."'"."',"
				.$points.","
				.$rank_score.","
				//.$favorites.","
				.$post_shares
				.")";
				
		//echo $query."<br>";
		$wpdb -> query($query);
	}
}


/**
 * Get the cached number of points voted to posts authored by the given user
 * @param $user_id The user ID to retreive points for
 * @return [integer] Number of points
 */
function pw_get_user_post_points( $user_id ){

	if( !pw_config_in_db_tables('user_meta') )
		return false;

	global $wpdb;

	$query = "
		SELECT post_points
		FROM ".$wpdb->postworld_prefix.'user_meta'."
		WHERE user_id=".$user_id;

	$user_votes_points = $wpdb->get_var($query);

	if( $user_votes_points !== null )
		return $user_votes_points;
	else
		return false;

}

function pw_get_user_post_points_meta ( $user_id ){
	
	if( !pw_config_in_db_tables('user_meta') )
		return false;

	/*
	 * • Get the number of points voted to posts authored by the given user
	 * • Get cached points of user from wp_postworld_user_meta table post_points column
		return : integer (number of points)
	 * */
	global $wpdb;

	$query = "
		SELECT post_points_meta
		FROM ".$wpdb->postworld_prefix.'user_meta'."
		WHERE user_id=".$user_id;
	$user_post_points_meta = $wpdb -> get_var($query);
	if ($user_post_points_meta != null) {
		$user_post_points_meta = json_decode( $user_post_points_meta, true );
		$user_post_points_meta["total"] = pw_get_user_post_points( $user_id );
		return $user_post_points_meta;
	} else
		return false;

}

function pw_calculate_user_posts_points( $user_id ){

	if( !pw_config_in_db_tables('user_meta') )
		return false;

	global $wpdb;

	/*	• Adds up the points voted to given user's posts, stored in wp_postworld_post_points
		• Stores the result in the post_points column in wp_postworld_user_meta
	return : integer (number of points)*/
	
	// Gets the number of posts voted to the user's authored posts by broken down by post type
	$total_user_points_breakdown = pw_get_user_points_voted_to_posts( $user_id, TRUE );
	/*
		Returns an object like this:
			[
		     {"post_id":"13","author_id":"1","total_points":"10","post_type":"post"},
		     {"post_id":"19","author_id":"1","total_points":"10","post_type":"link"}
			]
	*/

	$total_user_points = 0;

	$post_points_meta = array(
		"post_type" => array()
		);
	
	// Iterate through each item breakdown, and add-up totals by overall and by post type
	foreach( $total_user_points_breakdown as $row ) {
		$total_user_points += $row->total_points;
		$post_points_meta['post_type'][$row->post_type] += $row->total_points;
	}
	
	pw_insert_user_meta( $user_id );

	$query = "
		UPDATE ".$wpdb->postworld_prefix.'user_meta'."
		SET post_points=".$total_user_points.", post_points_meta='".json_encode($post_points_meta)."'
		WHERE user_id=".$user_id;
	
	$result = $wpdb->query( $query );
	
	return array( "total" => $total_user_points, "post_type" => $post_points_meta['post_type'] );
	
}

function pw_cache_user_posts_points ( $user_id ){
/*  • Runs calculate_user_post_points() Method
	• Caches value in post_points column in wp_postworld_user_meta table
	return : integer (number of points)*/
	return pw_calculate_user_posts_points( $user_id );
}

///////////// COMMENT POINTS ////////////////////

function pw_get_user_comments_points ( $user_id ){
	if( !pw_config_in_db_tables('user_meta') )
		return false;

 	// IN DEV
	/*• Get the number of points voted to comments authored by the given user
	  • Get cached points of user from wp_postworld_user_meta table comment_points column
	return : integer (number of points)*/
	
	global $wpdb;

	$query = "
		SELECT comment_points
		FROM ".$wpdb->postworld_prefix.'user_meta'."
		WHERE user_id=".$user_id;
	$total_points = $wpdb -> get_var($query);
	
	if($total_points==null)
		$total_points=0;
	
	return $total_points;

}

/*Later*/
function pw_calculate_user_comments_points ( $user_id ){
	if( !pw_config_in_db_tables('comment_points') ||
		!pw_config_in_db_tables('user_meta') )
		return false;
	/*• Adds up the points voted to given user's comments, stored in wp_postworld_comment_points
	  • Stores the result in the post_points column in wp_postworld_user_meta
	return : integer (number of points)*/
	
	global $wpdb;	
	
	//$total_comment_points = pw_get_user_comments_points($user_id);
	$query = "
		SELECT SUM(points)
		FROM ".$wpdb->postworld_prefix.'comment_points'."
		WHERE comment_author_id=".$user_id;
	$total_comment_points = $wpdb->get_var($query);
	
	if($total_comment_points==null)
		$total_comment_points=0;
	
	$query = "
		UPDATE ".$wpdb->postworld_prefix.'user_meta'."
		SET comment_points=".$total_comment_points."
		WHERE user_id=".$user_id;
	$wpdb->query($query);
	
	return $total_comment_points;
}

 
 /*Later*/
function pw_cache_user_comments_points ( $user_id ){
	/*• Runs calculate_user_comment_points() Method
	  • Caches value in comment_points column in wp_postworld_user_meta table
	return : integer (number of points)*/
	return pw_calculate_user_comments_points($user_id);
}
function pw_set_comment_points( $comment_id, $set_points ){
	/*Description

	Wrapper for pw_set_points() Method for setting comment points
	Parameters
	
	$comment_id : integer
	
	$set_points : integer
	
	Process
	
	Run pw_set_points( 'comment', $post_id, $set_points )
	return : Array (same as pw_set_points() )
	 * */	
	return pw_set_points( 'comment', $comment_id, $set_points );
	
}

/////////////// GENERAL POINTS  ///////////////////

function pw_get_post_points_meta($user_id){

	if( !pw_config_in_db_tables('user_meta') )
		return false;

	global $wpdb;	
	$query = "
		SELECT post_points_meta
		FROM $wpdb->postworld_prefix"."user_meta
		WHERE user_id=".$user_id;
	return $wpdb -> get_var($query);
}


function pw_cache_post_points_meta($user_id, $post_points_meta_object){
	if( !pw_config_in_db_tables('user_meta') )
		return false;

	global $wpdb;	
	$query = "
		UPDATE $wpdb->postworld_prefix"."user_meta 
		SET post_points_meta ='".$post_points_meta_object ."'
		WHERE user_id=".$user_id;
	$wpdb ->query($query);		
}

function pw_update_post_points_meta($user_id,$post_id, $update_points){
	//echo ("<br> update_points in cache " .$update_points);
	$post_type = get_post_type( $post_id ); // check post_type of given post
	$post_points_meta = pw_get_post_points_meta($user_id);
	
	if($post_points_meta == null)
		$post_points_meta = array("post_type"=> array ("post"=> 0));
	else
		$post_points_meta = json_decode( $post_points_meta,true ); // decode from JSON
	
	$post_type_points = $post_points_meta['post_type'][$post_type]; // Get the number of points in given post_type
	$post_points_meta['post_type'][$post_type] = ((int)$post_type_points + $update_points); // Add new points
	$post_points_meta = json_encode($post_points_meta); // encode back into JSON
	
	//echo $post_points_meta;
	// Write new post_points_meta object to user_meta table
	pw_cache_post_points_meta($user_id, $post_points_meta);
} 


function pw_insert_post_points( $post_id, $user_id, $points ){
	if( !pw_config_in_db_tables('post_points') )
		return false;

	global $wpdb;
	$wpdb->insert(
		$wpdb->postworld_prefix.'post_points',
		array(
			'post_id' 		=> 	$post_id,
			'user_id'		=>	$user_id,
			'post_points'	=>	$points,
			),
		array(
			'%d', '%d', '%d'
			)
		);
}

function pw_update_post_points( $post_id, $user_id, $points ){
	if( !pw_config_in_db_tables('post_points') )
		return false;

	global $wpdb;

	$wpdb->update(
		$wpdb->postworld_prefix."post_points",
		// DATA
		array(
			'post_points' => $points
			),
		// WHERE
		array(
			'post_id' => $post_id,
			'user_id' => $user_id
			),
		// DATA FORMATS
		array(
			'%d'
			),
		// WHERE FORMATS
		array(
			'%d', '%d'
			)
		);

	// Caching causing an issue
	// Update the post_points value in postworld_post_meta

	/*
		update wp_postworld_post_meta set post_points = (select COALESCE(SUM(post_points),0) from wp_postworld_post_points where post_id = NEW.post_id) where post_id = NEW.post_id
	*/

}

function pw_update_comment_points($comment_id, $user_id, $points){

	if( !pw_config_in_db_tables('comment_points') )
		return false;

	global $wpdb;
	$query = "
		UPDATE $wpdb->postworld_prefix"."comment_points
		SET points=".$points."
		WHERE comment_id=".$comment_id."
		AND user_id=".$user_id;
	$wpdb->query($query);
}

function pw_insert_comment_points( $comment_id, $user_id, $points ){

	if( !pw_config_in_db_tables('comment_points') )
		return false;

	global $wpdb;

	//pw_log('pw_insert_comment_points');
	//pw_log('pw_insert_comment_points : comment_id :', $comment_id);
	//pw_log('pw_insert_comment_points : user_id :', $user_id);
	//pw_log('pw_insert_comment_points : points :', $points);

	$comment_post_id = pw_get_comment_post_id( $comment_id );
	$comment_author_id = pw_get_comment_author_id( $comment_id );

	$wpdb->insert(
		$wpdb->postworld_prefix."comment_points",
		array(
			'comment_id'		=>	$comment_id,
			'user_id'			=>	$user_id,
			'comment_post_id'	=>	$comment_post_id,
			'comment_author_id' =>	$comment_author_id,
			'points'			=>	$points,
			),
		array(
			'%d', '%d', '%d', '%d', '%d'
			)
		);
}


function pw_get_points_row( $point_type, $id, $user_id ){

	if( !pw_config_in_db_tables('comment_points') )
		return false;

	//pw_log('pw_get_points_row : ', $point_type);
	//pw_log('pw_get_points_row : id : ', $id);
	//pw_log('pw_get_points_row : user_id : ', $user_id);

	global $wpdb;

	switch( $point_type ){
		case 'post':
			$query = "
				SELECT *
				FROM ".$wpdb->postworld_prefix.'post_points'."
				WHERE post_id=" . $id . "
				AND user_id=" . $user_id;
			break;
		case 'comment':
			$query = "
				SELECT *
				FROM ".$wpdb->postworld_prefix.'comment_points'."
				WHERE comment_id=" . $id . "
				AND user_id=" . $user_id;
			break;
	}

	$points_row = $wpdb->get_row( $query, 'ARRAY_A' );
	
	//pw_log('pw_get_points_row : points_row : ', $points_row);

	if( $points_row )
		return $points_row;
	else
		return false;
}

function pw_set_post_points( $post_id, $set_points ) {
	$post_points = pw_set_points( 'post', $post_id, $set_points );
	if ( isset( $post_points ) )
		pw_cache_rank_score ( $post_id );
	return $post_points;
}


function pw_has_voted_on_post( $post_id, $user_id ) {
	/*
	 • Check wp_postworld_points to see if the user has voted on the post
	 • Return the number of points
	 return : integer
	 */
	
	global $wpdb;

	$query = "
		SELECT *
		FROM ".$wpdb->postworld_prefix.'post_points'."
		WHERE post_id=" . $post_id . "
		AND user_id=" . $user_id;

	$row = $wpdb -> get_row($query);

	if ( $row != null )
		return $row->post_points;
	else
		return 0;

}

/*Later*/
function pw_has_voted_on_comment ( $comment_id, $user_id ){ 
	if( !pw_config_in_db_tables('comment_points') )
		return false;
/*	• Check wp_postworld_comment_points to see if the user has voted on the comment
	• Return the number of points voted
	return : integer*/
	global $wpdb;

	if(empty($user_id))
		$user_id = get_current_user_id();

	$query = "
		SELECT *
		FROM ".$wpdb->postworld_prefix.'comment_points'."
		WHERE comment_id=" . $comment_id . "
		AND user_id=" . $user_id;
	$commentPointsRow = $wpdb -> get_row($query);

	if ($commentPointsRow != null)
		return $commentPointsRow -> points;
	else
		return 0;
}


function pw_get_user_points_voted_to_posts($user_id, $breakdown=FALSE) {
	/*
	 * 
	 * Parameters: $user_id 
	 * 			   $breakdown =FALSE
	 * If $breakdown == false then
	 • Get array of all posts by given user
	 • Get points of each post from wp_postworld_post_meta
	 • Add all the points up
	 return : integer (number of points)
	 * If $breakdown == true 
	 Get total points voted to posts authored by the given user grouped by post_type

	Output :
	[
     {"post_id":"13","author_id":"1","total_points":"10","post_type":"post"},
     {"post_id":"19","author_id":"1","total_points":"10","post_type":"link"}
	]*/

	global $wpdb;

	if($breakdown === FALSE){

		//SELECT * FROM wp_postworld_a1.get_user_points_view;
		$query = "
			SELECT SUM(post_points)
			AS total_points
			FROM ".$wpdb->postworld_prefix.'post_meta'."
			WHERE author_id=" . $user_id;

		$total_points = $wpdb -> get_var($query);
		if ($total_points != null) {
			//echo("total_points:" . $total_points);
			return $total_points;
		} else
			return 0;
	
	} else{
		
		$query = "
			SELECT post_id,author_id ,(post_points)
			AS total_points, wp_posts.post_type
			FROM $wpdb->postworld_prefix"."post_meta left join wp_posts on (wp_posts.ID = $wpdb->postworld_prefix"."post_meta.post_id
			AND wp_posts.post_author = $wpdb->postworld_prefix"."post_meta.author_id)
			WHERE author_id=$user_id ";

		$user_votes_points_breakdown = $wpdb->get_results( $query );

		return $user_votes_points_breakdown;	
	}
}


function pw_get_user_votes_on_posts( $user_id, $fields, $direction = null ) {
	/*
	 • Get all posts which user has voted on from wp_postworld_points
	 return : Object
	 #for_each
	 post_id : {{integer}}
	 votes : {{integer}}
	 time : {{timestamp}}
	 */

	// Default Fields
	/*if ( !is_set($fields) || empty($fields) ){
		$fields = array( 'post_id', 'votes', 'time' );
	}*/

	global $wpdb;

	$query = "
		SELECT *
		FROM ".$wpdb->postworld_prefix.'post_points'."
		WHERE user_id=" . $user_id;
	//echo($query);
	$user_votes_per_post = $wpdb -> get_results($query);

	$output = array();
	foreach ($user_votes_per_post as $row) {

		if( $fields = "post_id" )
			$singlePost = $row->post_id;
		else if( $fields = "all" ){
			$singlePost = array();//get_user_votes_output();
			$singlePost['post_id'] = $row->post_id;
			$singlePost['votes'] = $row->post_points;
			$singlePost['time'] = $row->time;
		}

		//echo(serialize($singlePost));
		
		if( $direction == null )
			$output[] = $singlePost;
		else if( $row->post_points > 0 && $direction == 'up' )
			$output[] = $singlePost;
		else if( $row->post_points < 0 && $direction == 'down' )
			$output[] = $singlePost;
			
	}
	//echo(json_encode($output));
	 return $output;
	 
}


function pw_get_user_votes_report($user_id) {
	/*
	 • Returns the 'recent/active' points activity of the user
	 • Get all posts which user has recently voted on from wp_postworld_points ( total_posts )
	 • Add up all points cast (total_points)
	 • Generate average (total_points/total_posts)
	 return : Object
	 total_posts: {{integer}} (number of posts voted on)
	 total_points: {{integer}} (number of points cast by up/down votes)
	 average_points: {{decimal}} (average number of points per post)
	 */

	global $wpdb;

	$query = "
		SELECT SUM(post_points)
		AS total_points, COUNT(*) AS total_posts
		FROM ".$wpdb->postworld_prefix.'post_points'."
		WHERE user_id=" . $user_id;

	$total_points = $wpdb->get_results($query);

	foreach ($total_points as $row) {
	//	echo $row -> total_points . ",";
	//	echo $row -> total_posts . ",";
	//	echo($row -> total_points / $row -> total_posts);

		$output = array();
		$output["total_posts"] = $row -> total_posts;
		if ( $row -> total_points != 0 )
			$output["total_points"] = $row -> total_points;
		else
			$output["total_points"] = 0;
		if ( $row -> total_posts != 0 )
			$output["average_points"] = ($row -> total_points / $row -> total_posts);
		else
			$output["average_points"] = 0;
		return $output;
		
	}

}

function pw_get_user_vote_power ( $user_id ){
	/*
	 	• Checks to see user's WP roles with pw_get_user_role()
		• Checks how many points the user's role can cast, from config
		return : integer (the number of points the user can cast)
	 */

	global $wpdb;

	$current_user_role_output = pw_get_user_role($user_id);

	if( gettype($current_user_role_output) == "array" ) 
		$current_user_role = $current_user_role_output[0];

	else if ( gettype($current_user_role_output) == "string" )
		$current_user_role = $current_user_role_output;
	
	$current_user_role = strtolower($current_user_role);

	$vote_points = pw_config( 'roles.'.$current_user_role.'.vote_points' );

	if( $vote_points != false )
		return $vote_points;
	else
		return 0;
}

function pw_can_user_add_more_points($user_id,$current_number_of_points,$added_points){
	$user_allowed_points = pw_get_user_vote_power($user_id);
	if($user_allowed_points >=( $current_number_of_points + $added_points))
	return true;
	else return false;
}

function pw_post_meta_exists($post_id){
	global $wpdb;	
	$query = "
		SELECT *
		FROM ".$wpdb->postworld_prefix."post_meta
		WHERE post_id=".$post_id;
	$row = $wpdb->get_row($query);
	return ( $row == null ) ? false : true;
}


?>