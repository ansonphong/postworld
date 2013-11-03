<?php

/* 	WP Ajax Tips
 *	http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
  
*/


//---------- SET AVATAR ----------//
function pw_set_avatar_admin(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$user_id = $params['user_id'];
	$image_object = $params['image_object'];

	$pw_set_avatar = pw_set_avatar( $image_object, $user_id );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $pw_set_avatar;
	echo json_encode( $response );
	die;

}
//add_action("wp_ajax_nopriv_set_post_points", "set_post_points_admin");
add_action("wp_ajax_pw_set_avatar", "pw_set_avatar_admin");


//---------- GET AVATAR ----------//
function pw_get_avatar_anon(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$pw_get_avatar = pw_get_avatar( $params );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $pw_get_avatar; //$pw_get_avatar;
	echo json_encode( $response );
	die;

}
add_action("wp_ajax_nopriv_pw_get_avatar", "pw_get_avatar_anon");
add_action("wp_ajax_pw_get_avatar", "pw_get_avatar_anon");




//---------- SET POST POINTS ----------//
function set_post_points_admin(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$post_id = $params['post_id'];
	$points = $params['points'];

	$set_post_points = set_post_points( $post_id, $points );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $set_post_points;
	echo json_encode( $response );
	die;

}

//add_action("wp_ajax_nopriv_set_post_points", "set_post_points_admin");
add_action("wp_ajax_set_post_points", "set_post_points_admin");




//---------- SET POST RELATIONSHIP ----------//
function set_post_relationship_admin(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$relationship = $params['relationship'];
	$switch = $params['switch'];
	$post_id = $params['post_id'];

	$set_post_relationship = set_post_relationship( $relationship, $switch, $post_id );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $set_post_relationship;
	echo json_encode( $response );
	die;

}

add_action("wp_ajax_nopriv_set_post_relationship", "set_post_relationship_admin");
add_action("wp_ajax_set_post_relationship", "set_post_relationship_admin");




//---------- TAGS AUTOCOMPLETE ----------//
function tags_autocomplete_anon(){
	list($response, $args, $nonce) = initAjaxResponse();

	$tag_query_results = pw_query_terms( $args['args'] );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $tag_query_results;
	echo json_encode( $response );
	die;

}

add_action("wp_ajax_nopriv_tags_autocomplete", "tags_autocomplete_anon");
add_action("wp_ajax_tags_autocomplete", "tags_autocomplete_anon");




//---------- USER QUERY AUTOCOMPLETE ----------//
function user_query_autocomplete_anon(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args['args'];

	$user_query = new WP_User_Query( $pw_args );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $user_query;
	echo json_encode( $response );
	die;
}
add_action("wp_ajax_nopriv_user_query_autocomplete", "user_query_autocomplete_anon");
add_action("wp_ajax_user_query_autocomplete", "user_query_autocomplete_anon");




//---------- TAXONOMIES OUTLINE MIXED ----------//
function taxonomies_outline_mixed_anon(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args['args'];

	$taxonomies_outline_mixed = taxonomies_outline_mixed( $pw_args );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $taxonomies_outline_mixed;
	echo json_encode( $response );
	die;
}
add_action("wp_ajax_nopriv_taxonomies_outline_mixed", "taxonomies_outline_mixed_anon");
add_action("wp_ajax_taxonomies_outline_mixed", "taxonomies_outline_mixed_anon");




//---------- SAVE POST ADMIN ----------//
function pw_save_post_admin(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args['args'];
	$pw_save_post = pw_insert_post($pw_args);//

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $pw_save_post;
	echo json_encode( $response );
	die;
}
//add_action("wp_ajax_nopriv_pw_save_post_admin", "pw_save_post_admin");
add_action("wp_ajax_pw_save_post_admin", "pw_save_post_admin");


//---------- GET POST ADMIN ----------//

function pw_get_post_edit_admin() {
	list($response, $args, $nonce) = initAjaxResponse();	
	// pw_get_post ( $post_id, $fields, [$user_id] );
	$pw_args = $args['args'];


	/* ADD SECURITY CHECK */

	/*
	if($args['post_id']) $query = $args['post_id'];
	else ErrorReturn($response, 400, 'missing argument post_id'); 
	if ($args['fields']) $fields = $args['fields'];
	else $fields = 'all';
	// Get User Id
	$user_ID = get_current_user_id();
	*/

	$fields = array(
		"ID",
		"post_type",
		"post_id",
		"post_status",
		"post_title",
		"post_excerpt",
		"post_content",
		"post_format",
		"post_class",
		"link_url",
		"post_name",
		"post_permalink",
		"taxonomy(all)",
		"taxonomy_obj(post_tag)",
		'author(ID,display_name,user_nicename,posts_url,user_profile_url)',
		);

	/* set the response type as JSON */
	$results = pw_get_post( $pw_args, $fields ); //$post_id,$fields,$user_ID
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_get_post_types() - Logged in users */
add_action("wp_ajax_pw_get_post_edit", "pw_get_post_edit_admin");








//---------- oEMBED GET ----------//
function ajax_oembed_get(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args;
	$oEmbed = wp_oembed_get( $pw_args['link_url'] );
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $oEmbed;
	echo json_encode($response);
	// die ( $oEmbed );
	die;
}
add_action("wp_ajax_nopriv_ajax_oembed_get", "ajax_oembed_get");
add_action("wp_ajax_ajax_oembed_get", "ajax_oembed_get");



/* *************************
 *	General Ajax Functions 
 * 
 ************************** */

global $postworld_api_version;
$postworld_api_version = "0.1";

function ErrorReturn($response, $status, $message) {
	$response['status'] = $status;
	$response['message'] = $message;
	echo json_encode($response);
	die;
}

function initAjaxResponse() {
	global $postworld_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $postworld_api_version;
	// data is received in the raw data, not in the post data http://stackoverflow.com/questions/10494574/what-is-the-difference-between-form-data-and-request-payload
	// $args_text = $_POST['args']; // This will not work unless the Ajax Call is modified to post as a form with url encoded parameters
	$params_text = file_get_contents("php://input");
	// if no parameters then return error
	if (!$params_text) {
		ErrorReturn($response, 400, 'Error in parameters');	
	}
	$params = json_decode($params_text,true);
	if (!$params) {
		ErrorReturn($response, 400, 'Error in parameters');	
	}
	if (!isset($params['nonce'])) ErrorReturn($response, 400, 'Error in parameters');
	$nonce = $params['nonce'];
	// TODO check Nonce value
	if (!isset($params['args'])) ErrorReturn($response, 400, 'Error in parameters');
	$args = $params['args'];	
	return array($response, $args, $nonce);
}


/* *************************
 *	Feed Functions 
 * 
 ************************** */

/* Actions for pw_live_feed() */

function pw_live_feed_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	$pw_args = $args['args'];
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	$results = pw_live_feed ( $pw_args );
	// TODO check results are ok
	// TODO return success code or failure code , as well as version number with the results.
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}



/* Actions for pw_register_feed () */

function pw_register_feed_admin() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	$func_args = $args['args'];
	// TODO check results are ok
	/* set the response type as JSON */
	$results = pw_register_feed($args['args']);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Actions for pw_load_feed () */

function pw_load_feed_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	$func_args = $args['args'];
	// TODO check results are ok
	/* set the response type as JSON */
	// TODO check values are correct
	if ($func_args['feed_id']) $feed_id = $func_args['feed_id']; else $feed_id = ''; 
	if ($func_args['preload']) $preload = $func_args['preload']; else $preload = 0;
	$results = pw_load_feed($feed_id,$preload);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_live_feed() - Logged in users */
add_action("wp_ajax_pw_live_feed", "pw_live_feed_anon");

/* Action Hook for pw_live_feed() - Anonymous users */
add_action("wp_ajax_nopriv_pw_live_feed", "pw_live_feed_anon");

/* Action Hook for pw_load_feed() - Logged in users */
add_action("wp_ajax_pw_load_feed", "pw_load_feed_anon");

/* Action Hook for pw_load_feed() - Anonymous users */
add_action("wp_ajax_nopriv_pw_load_feed", "pw_load_feed_anon");

/* Action Hook for pw_register_feed() - Logged in users */
add_action("wp_ajax_pw_register_feed", "pw_register_feed_admin");

/* Action Hook for pw_register_feed() - Anonymous users */
// add_action("wp_ajax_nopriv_pw_register_feed", "pw_register_feed_anon");


/* *************************
 *	Posts Functions 
 * 
 ************************** */


/* Actions for pw_get_posts () */

function pw_get_posts_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	$pw_args = $args['args'];
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	$results = pw_get_posts ( $args['post_ids'],$args['fields'] );
	
	// TODO check results are ok
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_get_posts() - Logged in users */
add_action("wp_ajax_pw_get_posts", "pw_get_posts_anon");

/* Action Hook for pw_get_posts() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_posts", "pw_get_posts_anon");



function pw_get_post_anon() {
	list($response, $args, $nonce) = initAjaxResponse();	
	// pw_get_post ( $post_id, $fields, [$user_id] );
	
	if($args['post_id']) $post_id = $args['post_id'];
	else ErrorReturn($response, 400, 'missing argument post_id'); 
	if ($args['fields']) $fields = $args['fields'];
	else $fields = 'all';
	// Get User Id
	$user_ID = get_current_user_id();
	/* set the response type as JSON */
	$results = pw_get_post($post_id,$fields,$user_ID);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_get_post() - Logged in users */
add_action("wp_ajax_pw_get_post", "pw_get_post_anon");

/* Action Hook for pw_get_post() - Anonymous Users */
add_action("wp_ajax_nopriv_pw_get_post", "pw_get_post_anon");




/* Actions for pw_get_post_types () */

function pw_get_post_types_admin() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args']['feed_query'];
	
	$pw_args = $args['args'];
	
	// Get the results in array format, so that it is converted once to json along with the rest of the response
	
	//$results = pw_get_posts ( $args['post_ids'],$args['fields'] );
	
	// TODO check results are ok
	/* set the response type as JSON */
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = 'hello universe!'; //$results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

/* Action Hook for pw_get_post_types() - Logged in users */
add_action("wp_ajax_pw_get_post_types", "pw_get_post_types_admin");





/* *************************
 *	Tempalte Functions 
 * 
 ************************** */

 /* Actions for pw_get_templates () */

function pw_get_templates_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	// $pw_args = $args['args'];
	// TODO check results are ok
	// TODO return success code or failure code , as well as version number with the results.
	/* set the response type as JSON */
	$results = pw_get_templates($args['templates_object']);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}
 
/* Action Hook for pw_get_templates() - Logged in users */
add_action("wp_ajax_pw_get_templates", "pw_get_templates_anon");

/* Action Hook for pw_get_templates() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_templates", "pw_get_templates_anon");


/* *************************
 *	Comments Functions 
 * 
 ************************** */

 /* Actions for pw_get_comments () */

function pw_get_comments_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['query']) $query = $args['query'];
	else ErrorReturn($response, 400, 'missing argument query'); 
	if ($args['fields']) $fields = $args['fields'];
	else $fields = null;
	if ($args['tree'])	$tree = $args['tree'];
	else $tree = null;
	/* set the response type as JSON */
	$results = pw_get_comments($query,$fields,$tree);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

 /* Actions for pw_save_comment () */

function pw_save_comment_loggedIn() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['comment_data']) $commentdata = $args['comment_data'];
	else ErrorReturn($response, 400, 'missing argument comment_data');
	// had to rename it to return_value, since return in ajax javascript is a reserved word 
	if ($args['return_value']) $return = $args['return_value'];
	else $return = null;
	
	// TODO should we use wp_new_comment instead of wp_insert_comment http://codex.wordpress.org/Function_Reference/wp_new_comment?
	// Sanitize
	$commentdata = apply_filters('preprocess_comment', $commentdata);
	
	
	// Get User ID, it must be real, since this function is called for logged in users only
	$user_ID = get_current_user_id();
	if (!$user_ID) ErrorReturn($response, 400, 'User must be authenticated to perform this action');
	$commentdata['user_id'] = $user_ID;
	
	// Get Author Info
	 $user_data = get_userdata( $user_ID );
	if ($user_data->display_name) {
		$commentdata['comment_author'] = $user_data->display_name; 
	} else if ($user_data->user_nicename) {
		$commentdata['comment_author'] = $user_data->user_nicename; 
	} else 
		$commentdata['comment_author'] = $user_data->user_login; 
	
	if ($user_data->user_email) {
		$commentdata['comment_author_email'] = $user_data->user_email; 
	}
	  
	if ($user_data->user_url) {
		$commentdata['comment_author_url'] = $user_data->user_url; 
	}  	
	
	// Get IP, Agent
	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';
	// Get Date
	// $commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	// Sanitize
	$commentdata = wp_filter_comment($commentdata);
	$commentdata['comment_approved'] = wp_allow_comment($commentdata);	
			
	
	$results = pw_save_comment($commentdata,$return);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

 /* Actions for pw_delete_comment () */

function pw_delete_comment_loggedIn() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['comment_id']) $comment_id = $args['comment_id'];
	else ErrorReturn($response, 400, 'missing argument comment_id');
		
	// Get User ID, it must be real, since this function is called for logged in users only
//	$user_ID = get_current_user_id();
//	if (!$user_ID) ErrorReturn($response, 400, 'User must be authenticated to perform this action');
//	$commentdata['user_id'] = $user_ID;					
	
	// $results = pw_save_comment($commentdata,$return);
	$results = wp_delete_comment( $comment_id);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}



/* Action Hook for pw_delete_comment() - Logged in users */
add_action("wp_ajax_pw_delete_comment", "pw_delete_comment_loggedIn");

 
/* Action Hook for pw_save_comment() - Logged in users */
add_action("wp_ajax_pw_save_comment", "pw_save_comment_loggedIn");


/* Action Hook for pw_get_comments() - Logged In users */
add_action("wp_ajax_pw_get_comments", "pw_get_comments_anon");

/* Action Hook for pw_get_comments() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_comments", "pw_get_comments_anon");



/* *************************
 *	Test Functions - used for testing and can be removed 
 * 
 ************************** */
 
 function pw_test_code_admin() {
	// list($response, $args, $nonce) = initAjaxResponse();
	global $postworld_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $postworld_api_version;
	
	$query = array(
            'post_id' => 166220,            
    	);
		
	// TODO check results are ok
	/* set the response type as JSON */
	// TODO check values are correct
	$results = pw_get_comments ($query,'all',true);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}
 

/* Action Hook for pw_test_code() - Logged in users */
add_action("wp_ajax_pw_test_code", "pw_test_code_admin");


?>