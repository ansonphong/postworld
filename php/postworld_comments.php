<?php

function get_comment_points($comment_id){
	
	/*
		Get the total number of points of the given comment from the points column in wp_postworld_comment_meta
		return : integer (number of points) 
	*/
	global $wpdb;
	$wpdb -> show_errors();

	$query = "SELECT comment_points FROM " . $wpdb->pw_prefix.'comment_meta' . " WHERE comment_id=" . $comment_id;
	//echo ($query);
	$comment_points = $wpdb -> get_var($query);
	if ($comment_points == null)
		$comment_points = 0;
	
	return $comment_points;
} 

function calculate_comment_points($comment_id){
	/* 
		Adds up the points from the specified comment, stored in wp_postworld_comment_points
		Stores the result in the points column in wp_postworld_comment_meta 
	 	return : integer (number of points)
	 */
	global $wpdb;
	$wpdb -> show_errors();

	//first sum points
	$query = "select SUM(points) from ".$wpdb->pw_prefix.'comment_points'." where comment_id=" . $comment_id;
	$points_total = $wpdb -> get_var($query);
	//echo("\npoints cal" . $points_total);
	if($points_total==null || $points_total =='') $points_total=0;
	
	return $points_total;
} 



function cache_comment_points($comment_id){
	
	/*
		Calculates given post's current points with calculate_comment_points()
		Stores points it in wp_postworld_post_meta table_ in the post_points column
		return : integer (number of points)
	*/
	global $wpdb;
	$wpdb -> show_errors();
	$total_points = calculate_comment_points($comment_id);
	 //update wp_postworld_meta
	$query = "update ".$wpdb->pw_prefix.'comment_meta'." set comment_points=" . $total_points . " where comment_id=" . $comment_id;
	$result =$wpdb -> query($query);
	//echo(json_encode($result));
	
	if ($result === FALSE || $result === 0){
		//echo 'false <br>';
		//insertt new row for this comment in comment_meta, no points was added
		add_record_to_comment_meta($comment_id,$total_points);
	}
	return $total_points;
	
	
}    


function add_record_to_comment_meta($comment_id,$total_points=0){
	/*
	 This function gets comment data inserts a record in wp_postworld_comment_meta table
	 * Parameters:
	 * -comment_id
	 * -optional : points (default value=0)
	 * optional parameters are not sent if the function adds a new row
	 */
	 //echo "comment_id=".$comment_id;
	global $wpdb;	
	$wpdb-> show_errors();
	
	 
	$query = "select * from ".$wpdb->pw_prefix."comment_meta where comment_id=".$comment_id;
	$row = $wpdb->get_row($query);
	
	if($row ==null){
		
		//$comment_data = get_comment($comment_id);
		//echo("<br>".json_encode($comment_data)); 
		//print_r($comment_data);
	
		
		//$comment_data =  get_comment( $comment_id, ARRAY_A );
		//echo json_encode($post_data);
		$query = "insert into ".$wpdb->pw_prefix.'comment_meta'." values("
				.$comment_id.","
				.get_comment_post_id($comment_id).","
				.$total_points
				.")";
				
		//echo $query."<br>";
		$wpdb -> query($query);
	}
}


function pw_get_comment ( $comment_id, $fields = "all", $viewer_user_id = null ){

	// Detect and switch into edit mode
	if( $fields == "edit" ){
		$mode = "edit";
		$fields = "all";
	} else{
		$mode = "display";
	}

	// Access the Comment Data
	$wp_comment_data = get_comment( $comment_id, 'ARRAY_A' );
	if (!$wp_comment_data) return false;

	///// FIELDS /////
	// WORDPRESS FIELD MODEL
	$wp_comment_fields = array(
		'comment_ID',
		'comment_post_ID',
		'comment_author',
		'comment_author_email',
		'comment_author_url',
		'comment_author_IP',
		'comment_date',
		'comment_date_gmt',
		'comment_content',
		'comment_karma',
		'comment_approved',
		'comment_agent',
		'comment_type',
		'comment_parent',
		'user_id'
		);

	// POSTWORLD FIELD MODEL
	$pw_comment_fields = array(
		'comment_points',
		'user_voted',
		'time_ago'
		);

	// POSTWORLD USER FIELDS
	$pw_userdata_fields = array(
		'user_profile_url',
		'location_city',
		'location_region',
		'location_country',
		'display_name',
		);

	// POSTWORLD AVATAR FIELDS
	$pw_avatar_fields = array(
		'avatar(small,96)',
		);

	// All Fields
	if ($fields == 'all'){
		$all_fields = array_merge(
			$wp_comment_fields,
			$pw_comment_fields,
			$pw_userdata_fields,
			$pw_avatar_fields
			);
		$fields = $all_fields;
	}

	// COMMENT DATA OBJECT
	$comment_data = array();

	// TRANSFER FIELD DATA FROM WORDPRESS OBJECT
	foreach ($fields as $field) {
		if( in_array($field, $wp_comment_fields) ){
			$comment_data[$field] = $wp_comment_data[$field];

			// Apply Content Filters
			if ( $field == 'comment_content' && $mode == "display" ){
				// ADDITIONAL FUNCTIONALITY
				$comment_data['comment_content'] = wpautop( $comment_data['comment_content'] );
				$comment_data['comment_content'] = $comment_content = pw_embed_content($comment_data['comment_content']);
			}
		}
		// POSTWORLD COMMENT FIELDS 
		else if( $field == 'comment_points' ){
			$comment_data[$field] = get_comment_points( $comment_id );
		}
		else if( $field == 'user_voted' ){
			$comment_data[$field] = has_voted_on_comment( $comment_id, get_current_user_id() );
		}
		else if( $field == 'time_ago' ){
			$timestamp = strtotime($wp_comment_data['comment_date_gmt']);
			$comment_data[$field] = time_ago($timestamp);
		}
	}

	///// POSTWORLD USER FIELDS /////
	foreach ($fields as $field) {
		// If the current field is a custom author data field
		// Use pw_get_userdata() to get the data
		if ( in_array( $field, $pw_userdata_fields ) ){
			// EXTRACT THE FIELDS WHICH ARE
			$get_pw_userdata_fields = array();
			foreach ($fields as $field) {
				if( in_array($field, $pw_userdata_fields) ){
					array_push($get_pw_userdata_fields, $field);
				}
			}
			// GET THE USER ID FROM THE SLUG
			//$comment_data = get_comment($comment['comment_ID'],'ARRAY_A');
			$user_id = $wp_comment_data['user_id'];
			// GET THE USER FIELD DATA
			$pw_userdata = pw_get_userdata( $user_id, $get_pw_userdata_fields );
			$comment_data['author'] = $pw_userdata;					
			break;
		}
	}

	////////// AVATAR IMAGES //////////
		// AVATAR FIELDS
		$avatars_object = get_avatar_sizes( $user_id, $fields );
		if ( !empty( $avatars_object ) )
			$comment_data["avatar"] = $avatars_object;

	return $comment_data;
}


function pw_get_comments( $query, $fields = 'all', $tree = true ){

	$wp_comments = pw_new_get_comments($query); //get_comments( $query );
	if (!$wp_comments) return false;

	$wp_comments = (array) $wp_comments;

	///// FIELDS /////
	// WORDPRESS FIELD MODEL
	$wp_comment_fields = array(
		'comment_ID',
		'comment_post_ID',
		'comment_author',
		'comment_author_email',
		'comment_author_url',
		'comment_author_IP',
		'comment_date',
		'comment_date_gmt',
		'comment_content',
		'comment_karma',
		'comment_approved',
		'comment_agent',
		'comment_type',
		'comment_parent',
		'user_id'
		);

	// POSTWORLD COMMENT FIELD MODEL
	$pw_comment_fields = array(
		'comment_points',
		'viewer_points',
		'time_ago',
		);

	// POSTWORLD PW_GET_USERDATA() FIELD MODEL
	$pw_userdata_fields = array(
		'user_profile_url',
		'location_city',
		'location_region',
		'location_country',
		'display_name',
		);

	// POSTWORLD AVATAR FIELDS
	$pw_avatar_fields = array(
		'avatar(small,96)',
		);


	// All Fields
	if ($fields == 'all'){
		$all_fields = array_merge(
			$wp_comment_fields,
			$pw_comment_fields,
			$pw_userdata_fields,
			$pw_avatar_fields
			);
		$fields = $all_fields;
	}

	// New Comments Array
	$comments_data = array();

	///// FOR EACH COMMENT /////
	foreach ($wp_comments as $comment) {

		// Cast as Array
		$comment = (array) $comment;

		// New Comment
		$comment_data = array();

		// Setup local vars
		$user_id = $comment['user_id'];		

		///// FOR EACH FIELD /////
			foreach ($fields as $field) {
				///// CUSTOM AUTHOR FIELDS /////
				// If the current field is a custom author data field
				// Use pw_get_userdata() to get the data
				if ( in_array( $field, $pw_userdata_fields ) ){
					// Extracta all the relavant fields
					$get_pw_userdata_fields = array();
					foreach ($fields as $pw_field) {
						if( in_array($pw_field, $pw_userdata_fields) ){
							array_push($get_pw_userdata_fields, $pw_field);
						}
					}
					
					// DELETE
					// Get the user ID from the slug
					//$comment_data = $comment; //get_comment($comment['comment_ID'],'ARRAY_A');
					
					// Get the Postworld user meta data
					$pw_userdata = pw_get_userdata( $user_id, $get_pw_userdata_fields );
					$comment_data['author'] = $pw_userdata;
					// Break the foreach, to prevent multiple calls on pw_get_userdata
					// Since we already got all the relevant fields
					break;
				}
			}
			
		///// FOR EACH FIELD /////
			foreach ($fields as $field) {

				///// WORDPRESS COMMMENT FIELDS /////
				// If the current field is requested, move the data
				if( in_array( $field, $wp_comment_fields ) ){
					$comment_data[$field] = $comment[$field];
				}

				///// POSTWORLD COMMENT FIELDS /////
				if( in_array( $field, $pw_comment_fields ) ){
					if( $field == 'comment_points' ){
						$comment_data['comment_points'] = get_comment_points( $comment['comment_ID'] );
					}
					else if( $field == 'viewer_points' ){
						$comment_data[$field] = has_voted_on_comment( $comment['comment_ID'], get_current_user_id() );
					}
					// Post Time Ago
					else if ( in_array('time_ago', $fields) )
						$comment_data['time_ago'] = time_ago( strtotime ( $comment_data['comment_date_gmt'] ) );
				}
			}

		////////// AVATAR IMAGES //////////
			// AVATAR FIELDS
			$avatars_object = get_avatar_sizes( $user_id, $fields );
			if ( !empty( $avatars_object ) )
				$comment_data["avatar"] = $avatars_object;

		///// FILTER CONTENT /////
			// Apply Content Filters
			if ( isset($comment_data['comment_content']) ){
				$comment_content = $comment_data['comment_content'];

				// ADDITIONAL FUNCTIONALITY
				$comment_content = pw_embed_content($comment_content);
				$comment_content = wpautop( $comment_content );

				$comment_data['comment_content'] = $comment_content;
			}

		array_push($comments_data, $comment_data);
	}

	///// RETURN AS HIERARCHICAL TREE /////
	
	if ( $tree == true ){
		$settings = array(
		    'fields' => 'all', //$fields,
		    'id_key' => 'comment_ID',
		    'parent_key' => 'comment_parent',
		    'child_key' => 'children',
		    'max_depth' => '5',
		    //'callback' => $callback,
		    //'callback_fields' => $callback_fields,
		    );

		$comments_tree = tree_obj( $comments_data, 0, 0, $settings );
		if ($comments_tree){
			$comments_data = $comments_tree;
		}

	}
	

	return $comments_data; //$comments_data;

}


function get_comment_author_id($comment_id){
	$comment_data = get_comment($comment_id,ARRAY_A);		
	return $comment_data['user_id'];
}

function get_comment_post_id($comment_id){
	$comment_data = get_comment($comment_id,ARRAY_A);
	return $comment_data['comment_post_ID'];
}


function pw_save_comment($comment_data, $return = 'data'){
	//extract($comment_data);

	// Get the Current User's Data
	$current_user_id = get_current_user_id();
	$current_userdata = (array) get_userdata( $current_user_id );

	// UNSET SECURE FIELDS
	// If not a comment moderator
	if ( $current_userdata['allcaps']['moderate_comments'] == false ){
		// Disable editing of comment approval & change of user
		unset( $comment_data['comment_approved'], $comment_data['user_id'] );
	}

	// GET THE COMMENT DATA
	// If a comment ID is supplied
	if( !empty( $comment_data['comment_ID'] ) ){
		$current_comment_data = (array) get_comment( $comment_data['comment_ID'], 'ARRAY_A' );
	}

	///// SECURITY CHECK & SET METHOD /////
	// If the post ID exists after getting it
	if( !empty( $current_comment_data['comment_ID'] ) ){

		///// SECURITY /////
		// Define the author ID
		$author_id = $current_comment_data['user_id'];

		// Is the current user the author of the post?
		( $author_id == $current_user_id ) ? $user_is_author = true : $user_is_author = false;

		// If user doesn't own post or annot moderate comments
		if( $user_is_author == false && $current_userdata['allcaps']['moderate_comments'] == false ){
			// Return false, exit out of the function
			return array( 'error' => 'No permissions to edit comment.' );
		}
		
		$method = 'update';

	}
	else{
		$method = 'insert';
	}

	///// INSERT POST METHOD /////
	if( $method == 'insert' ){
		$comment_data['user_id'] = $current_user_id;
		$comment_ID = wp_insert_comment( $comment_data );
	}

	///// UPDATE POST METHOD /////
	else if ( $method == 'update' ) {
		$update_comment = wp_update_comment( $comment_data );
		if ( $update_comment == 1 ){
			$comment_ID = $current_comment_data['comment_ID'];
		}
		else{
			$comment_ID = array( 'error' => 'Comment not saved.' );
		}
	}

	if ($return == 'data')
		return pw_get_comment($comment_ID);

	else if ($return == 'id')
		return $comment_ID; 

}




?>