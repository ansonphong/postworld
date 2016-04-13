<?php

/**
 * Enable or disable comments based on admin settings.
 */
add_filter( 'pw_enable_wp_comments', 'pw_enable_wp_comments_filter', 1 );
function pw_enable_wp_comments_filter($bool){
	return pw_grab_option( PW_OPTIONS_COMMENTS, 'wordpress.enable' );
}

/**
 * Returns the comments form as a string, so it can be injected into templates.
 */
function pw_comment_form( $args, $post_id ){
	$comments_enabled = apply_filters('pw_enable_wp_comments', true);
	if(!$comments_enabled)
		return false;

	ob_start();
	comment_form( $args, $post_id );
	$output = ob_get_contents();
	ob_end_clean();
	return $output;

}

/**
 * Returns the comments template as a string, so it can be injected into templates.
 */
function pw_comments_template( $file = '/comments.php', $separate_comments = false ){
	$comments_enabled = apply_filters('pw_enable_wp_comments', true);
	if(!$comments_enabled)
		return false;
	ob_start();
	comments_template($file, $separate_comments);
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}



/**
 * Get all of the native WordPress discussion/comment settings
 * And returns them in an associative array with the option names as keys.
 * @return array
 */
function pw_get_wp_comment_settings(){

	return array(
		'default_comment_status' => (string) get_option( 'default_comment_status', 'open' ),
		'require_name_email' => (bool) get_option( 'require_name_email', true ),
		'comment_registration' => (bool) get_option( 'comment_registration', true ),
		'comment_registration' => (bool) get_option( 'comment_registration', true ),

		'close_comments_for_old_posts' => (bool) get_option( 'close_comments_for_old_posts', false ),
		'close_comments_days_old' => (int) get_option( 'close_comments_days_old', 14 ),
		'thread_comments' => (bool) get_option( 'thread_comments', true ),
		'thread_comments_depth' => (bool) get_option( 'thread_comments_depth', 5 ),

		'page_comments' => (bool) get_option( 'page_comments', false ),
		'comments_per_page' => (int) get_option( 'comments_per_page', 50 ),
		'comment_order' => (string) get_option( 'comment_order', 'asc' ),

		'comments_notify' => (bool) get_option( 'comments_notify', true ),
		'moderation_notify' => (bool) get_option( 'moderation_notify', true ),

		'comment_max_links' => (int) get_option( 'comment_max_links', 2 ),
		'moderation_keys' => (string) get_option( 'moderation_keys', '' ),
		'blacklist_keys' => (string) get_option( 'blacklist_keys', '' ),
		);

}


function pw_get_comment_points($comment_id){
	if( !pw_config_in_db_tables('comment_meta') ||
		!pw_config_in_db_tables('comment_points') )
		return 0;

	/*
		Get the total number of points of the given comment from the points column in wp_postworld_comment_meta
		return : integer (number of points) 
	*/
		
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb -> show_errors();

	$query = "SELECT comment_points FROM " . $wpdb->pw_prefix.'comment_meta' . " WHERE comment_id=" . $comment_id;
	//echo ($query);
	$comment_points = $wpdb -> get_var($query);
	if ($comment_points == null)
		$comment_points = 0;
	
	return $comment_points;
} 

function pw_calculate_comment_points($comment_id){
	if( !pw_config_in_db_tables('comment_meta') ||
		!pw_config_in_db_tables('comment_points') )
		return false;

	/* 
		Adds up the points from the specified comment, stored in wp_postworld_comment_points
		Stores the result in the points column in wp_postworld_comment_meta 
	 	return : integer (number of points)
	 */
	global $wpdb;
	if( pw_dev_mode() )
		$wpdb -> show_errors();

	//first sum points
	$query = "select SUM(points) from ".$wpdb->pw_prefix.'comment_points'." where comment_id=" . $comment_id;
	$points_total = $wpdb -> get_var($query);
	//echo("\npoints cal" . $points_total);
	if($points_total==null || $points_total =='') $points_total=0;
	
	return $points_total;
} 



function pw_cache_comment_points($comment_id){
	
	if( !pw_config_in_db_tables('comment_meta') ||
		!pw_config_in_db_tables('comment_points') )
		return false;

	/*
		Calculates given post's current points with pw_calculate_comment_points()
		Stores points it in wp_postworld_post_meta table_ in the post_points column
		return : integer (number of points)
	*/
	global $wpdb;
	
	if( pw_dev_mode() )
		$wpdb -> show_errors();
	
	$total_points = pw_calculate_comment_points($comment_id);
	 //update wp_postworld_meta
	$query = "update ".$wpdb->pw_prefix.'comment_meta'." set comment_points=" . $total_points . " where comment_id=" . $comment_id;
	$result =$wpdb -> query($query);
	//echo(json_encode($result));
	
	if ($result === FALSE || $result === 0){
		//echo 'false <br>';
		//insertt new row for this comment in comment_meta, no points was added
		pw_insert_comment_meta($comment_id,$total_points);
	}
	return $total_points;
	
	
}    


function pw_insert_comment_meta($comment_id,$total_points=0){

	if( !pw_config_in_db_tables('comment_meta') )
		return false;

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
				.pw_get_comment_post_id($comment_id).","
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
			$comment_data[$field] = pw_get_comment_points( $comment_id );
		}
		else if( $field == 'user_voted' ){
			$comment_data[$field] = pw_has_voted_on_comment( $comment_id, get_current_user_id() );
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
		$avatars_object = pw_get_avatar_sizes( $user_id, $fields );
		if ( !empty( $avatars_object ) )
			$comment_data["avatar"] = $avatars_object;

	return $comment_data;
}


function pw_get_comments( $query, $fields = 'all', $tree = true ){

	$wp_comments = pw_new_get_comments($query);
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
		'comment_excerpt(128)',
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

		///// CUSTOM AUTHOR FIELDS /////
			foreach ($fields as $field) {	
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
			
		///// WORDPRESS COMMMENT FIELDS /////
			foreach ($fields as $field) {
				// If the current field is requested, move the data
				if( in_array( $field, $wp_comment_fields ) ){
					$comment_data[$field] = $comment[$field];
				}
			}

		///// POSTWORLD COMMENT FIELDS /////
			if( in_array( 'comment_points', $fields ) )
				$comment_data['comment_points'] = pw_get_comment_points( $comment['comment_ID'] );
			
			if( in_array( 'viewer_points', $fields ) )
				$comment_data['viewer_points'] = pw_has_voted_on_comment( $comment['comment_ID'], get_current_user_id() );
			
			if( in_array( 'time_ago', $fields ) )
				$comment_data['time_ago'] = time_ago( strtotime ( $comment_data['comment_date_gmt'] ) );
			

		////////// AVATAR IMAGES //////////
			// AVATAR FIELDS
			$avatars_object = pw_get_avatar_sizes( $user_id, $fields );
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


		///// COMMENT EXCERPT /////
			// This must come after the content is filtered so that embedded content can be removed
			// comment_excerpt(100)
			$comment_excerpt_fields = pw_extract_linear_fields( $fields, 'comment_excerpt', true );
			if ( !empty( $comment_excerpt_fields ) ){
				// If a number is provided in the first field
				if( is_numeric( $comment_excerpt_fields[0] ) ){
					// Set the max characters as an integer
					$comment_excerpt_max_chars = intval($comment_excerpt_fields[0]);	
					// Get it from comment content
					$comment_excerpt = $comment['comment_content'];
					// Strip all shortcodes
					$comment_excerpt = strip_shortcodes( $comment_excerpt );
					// Strip all HTML tags
					$comment_excerpt = wp_strip_all_tags( $comment_excerpt, true );
					// Remove all URLs
					$comment_excerpt = preg_replace('|([A-Za-z]{3,9})://([-;:&=\+\$,\w]+@{1})?([-A-Za-z0-9\.]+)+:?(\d+)?((/[-\+~%/\.\w]+)?\??([-\+=&;%@\.\w]+)?#?([\w]+)?)?|', '', $comment_excerpt);
					// Crop it down to the number of max characters
					$comment_excerpt = pw_crop_string_to_word( $comment_excerpt, $comment_excerpt_max_chars, "..." );
					// Set it into the post object
					$comment_data['comment_excerpt'] = $comment_excerpt;	
				}
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

		$comments_tree = pw_make_tree_obj( $comments_data, 0, 0, $settings );
		if ($comments_tree){
			$comments_data = $comments_tree;
		}

	}
	

	return $comments_data; //$comments_data;

}


function pw_get_comment_author_id($comment_id){
	$comment_data = get_comment($comment_id,ARRAY_A);		
	return $comment_data['user_id'];
}

function pw_get_comment_post_id($comment_id){
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