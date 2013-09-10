<?php

function get_post_data( $post_id, $fields='all', $user_id ){
	//• Gets data fields for the specified post

	////////// FIELDS MODEL //////////
	$preview_fields =	array('ID','post_title','post_excerpt', 'post_permalink', 'post_path', 'post_type', 'post_date', 'post_time_ago', 'comment_count', 'link_url', 'points' );
	$detail_fields =	array('image_thumbnail', 'more', 'has_voted');
	$user_fields =		array('user_vote', 'user_data');

	// Add Preview Fields
	if ($fields == 'preview')
		$fields = $preview_fields;

	// Add Detail Fields
	if ($fields == 'all'){
		$fields = array_merge($preview_fields, $detail_fields);
	}

	// Add User Fields
	if (is_int($user_id)){
		// Get User Data
		$user_data = get_userdata( $user_id );
		// If user exists, add user fields
		if( $user_data != false ){
			$fields = array_merge($fields, $user_fields);
		}
	}

	////////// WP GET_POST METHOD //////////
	// Get post data from Wordpress standard function
	$get_post = get_post($post_id, ARRAY_A);
	foreach ($get_post as $key => $value) {
		if( in_array($key, $fields) )
			$post_data[$key] = $value;
	}

	////////// WP GET_POST_CUSTOM METHOD //////////
	// Get post data from Wordpress standard function
	$get_post_custom = get_post_custom($post_id);
	foreach ($get_post_custom as $key => $value) {
		if( in_array($key, $fields) )
			$post_data[$key] = $value;
	} 

	////////// POSTWORLD //////////
	// Points
	if( in_array('points', $fields) )
		$post_data['points'] = get_points( $post_id );

	// User Has Voted
	if( in_array('has_voted', $fields) )
		$post_data['has_voted'] = has_voted( $post_id, $user_data->ID );


	return json_encode($post_data);

}


?>