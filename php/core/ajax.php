<?php
/* 	WP Ajax Tips
 *	http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
*/

function pwAjaxRespond( $response_data ){
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $response_data;
	echo json_encode( $response );
	die;
}



//---------- TAXONOMY OPERATIONS ----------//
function pw_taxonomy_operation_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	//pw_log( 'args', $args );
	$response_data = pw_taxonomy_operation( $args['type'], $args['vars'] );
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_taxonomy_operation", "pw_taxonomy_operation_ajax");


//---------- PW END PROGRESS ----------//
function pw_end_progress_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$response_data = pw_end_progress( $args['key'] );
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_end_progress", "pw_end_progress_ajax");


//---------- PW GET PROGRESS ----------//
function pw_get_progress_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	if( current_user_can('manage_options') )
		$response_data = pw_get_progress( $args['key'] );
	else
		return false;

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_get_progress", "pw_get_progress_ajax");


//---------- PW CACHE ALL COMMENT POINTS ----------//
function pw_cache_all_comment_points_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	if( current_user_can('manage_options') )
		$response_data = pw_cache_all_comment_points();
	else
		return false;

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_cache_all_comment_points", "pw_cache_all_comment_points_ajax");


//---------- PW CACHE ALL USER POINTS ----------//
function pw_cache_all_user_points_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	if( current_user_can('manage_options') )
		$response_data = pw_cache_all_user_points();
	else
		return false;

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_cache_all_user_points", "pw_cache_all_user_points_ajax");


//---------- PW CACHE ALL POST POINTS ----------//
function pw_cache_all_post_points_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	if( current_user_can('manage_options') )
		$response_data = pw_cache_all_post_points();
	else
		return false;

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_cache_all_post_points", "pw_cache_all_post_points_ajax");



//---------- PW CACHE ALL RANK SCORES ----------//
function pw_cache_all_rank_scores_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	//pw_log( 'pw_cache_all_rank_scores_ajax : INIT : ' );

	if( current_user_can('manage_options') )
		$response_data = pw_cache_all_rank_scores();
	else
		return false;

	//pw_log( 'pw_cache_all_rank_scores_ajax : COMPLETE : ' . json_encode($response_data) );

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_cache_all_rank_scores", "pw_cache_all_rank_scores_ajax");


//---------- PW CLEANUP META ----------//
function pw_cleanup_meta_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	
	if( current_user_can('manage_options') )
		$response_data = pw_cleanup_meta( $args['type'] );
	else
		return false;

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_cleanup_meta", "pw_cleanup_meta_ajax");


//---------- PW TRUNCATE CACHE ----------//
function pw_truncate_cache_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	if( current_user_can('manage_options') )
		$truncate_cache = pw_truncate_cache();
	$response_data = pw_get_cache_types_readout();
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_truncate_cache", "pw_truncate_cache_ajax");


//---------- PW DELTE CACHE TYPE ----------//
function pw_delete_cache_type_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	if( current_user_can('manage_options') )
		$delete_cache = pw_delete_cache_type( $args['cache_type'] );
	$response_data = pw_get_cache_types_readout();
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_delete_cache_type", "pw_delete_cache_type_ajax");


//---------- PW GET CACHE TYPES READOUT ----------//
function pw_get_cache_types_readout_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$response_data = pw_get_cache_types_readout();
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_get_cache_types_readout", "pw_get_cache_types_readout_ajax");


//---------- PW SET USER META ----------//
function pw_set_wp_usermeta_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_set_wp_usermeta( $params ); 

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_set_wp_usermeta", "pw_set_wp_usermeta_ajax");
add_action("wp_ajax_pw_set_wp_usermeta", "pw_set_wp_usermeta_ajax");


//---------- PW GET TEMPLATE PARTIAL ----------//
function pw_get_term_feed_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_get_term_feed( $params ); 

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_term_feed", "pw_get_term_feed_ajax");
add_action("wp_ajax_pw_get_term_feed", "pw_get_term_feed_ajax");


//---------- PW GET TEMPLATE PARTIAL ----------//
function pw_get_template_partial_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_get_template_partial( $params ); 

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_template_partial", "pw_get_template_partial_ajax");
add_action("wp_ajax_pw_get_template_partial", "pw_get_template_partial_ajax");


//---------- PW GET MENUS ----------//
function pw_get_menus_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_get_menus( $params ); 
	pwAjaxRespond( $response_data );
}
//add_action("wp_ajax_nopriv_pw_get_menus", "pw_set_option_obj_ajax");
add_action("wp_ajax_pw_get_menus", "pw_get_menus_ajax");


//---------- PW SET OPTION OBJECT ----------//
function pw_set_option_obj_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_set_option_obj( $params ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_set_option_obj", "pw_set_option_obj_ajax");
add_action("wp_ajax_pw_set_option_obj", "pw_set_option_obj_ajax");


//---------- PW GET OPTION OBJECT ----------//
function pw_get_option_obj_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_get_option_obj( $params ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_option_obj", "pw_get_option_obj_ajax");
add_action("wp_ajax_pw_get_option_obj", "pw_get_option_obj_ajax");


//---------- PW UPDATE OPTION ----------//
function pw_update_option_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	extract($params);

	$response_data = pw_update_option( $option, $value ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_update_option", "pw_update_option_ajax");
add_action("wp_ajax_pw_update_option", "pw_update_option_ajax");


//---------- PW LOAD IMAGE ----------//
function pw_get_image_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	extract($params);

	$response_data = pw_get_image( $params ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_image", "pw_get_image_ajax");
add_action("wp_ajax_pw_get_image", "pw_get_image_ajax");


//---------- PW SET WIZARD STATUS ----------//
function pw_set_wizard_status_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	//extract($params);

	$response_data = pw_set_wizard_status( $params ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_set_wizard_status", "pw_set_wizard_status_ajax");


//---------- PW GET WIZARD STATUS ----------//
function pw_get_wizard_status_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	//extract($params);

	$response_data = pw_get_wizard_status( $params ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_pw_get_wizard_status", "pw_get_wizard_status_ajax");


//---------- PW GET USER DATA ----------//
function pw_get_userdata_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	extract($params);

	$response_data = pw_get_userdata( $user_id,  $fields ); 
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_userdata", "pw_get_userdata_ajax");
add_action("wp_ajax_pw_get_userdata", "pw_get_userdata_ajax");


//---------- PW GET USER DATAS ----------//
function pw_get_userdatas_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	extract($params);

	$response_data = pw_get_userdatas( $user_ids,  $fields );
	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_get_userdatas", "pw_get_userdatas_ajax");
add_action("wp_ajax_pw_get_userdatas", "pw_get_userdatas_ajax");


//---------- PW SET POST IMAGE ----------//
function pw_set_post_image_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	extract($params);

	// Set the thumbnail
	$set_post_thumbnail = set_post_thumbnail( $params['post_id'], $params['thumbnail_id'] );

	// Return 
	if( $set_post_thumbnail ){
		$response_data = pw_get_post( $params['post_id'], $params['return_fields'] );	
	} else {
		$response_data = false;
	}

	pwAjaxRespond( $response_data );
}
//add_action("wp_ajax_nopriv_pw_set_post_image_ajax", "set_post_image_ajax");
add_action("wp_ajax_set_post_image", "pw_set_post_image_ajax");


//---------- PW QUERY ----------//
function pw_query_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$response_data = pw_query($params);

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_nopriv_pw_query", "pw_query_ajax");
add_action("wp_ajax_pw_query", "pw_query_ajax");


//---------- FLAG COMMENTS ----------//
function flag_comment_admin(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	// FLAG COMMENT >>> Requires plugin : "Safe Report Comments"
	// If plugin is installed
	if (class_exists('Safe_Report_Comments')){
		$flag_comments = new Safe_Report_Comments();
		$flag_comments->mark_flagged( $params['comment_ID'] );
		$response_data = true;
	// If plugin is not installed
	} else{
		$response_data = false;
	}

	pwAjaxRespond( $response_data );
}
add_action("wp_ajax_flag_comment", "flag_comment_admin");


//---------- SHARE REPORT - USER - OUTGOING ----------//
function pw_user_share_report_outgoing_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	$user_share_report_outgoing = pw_user_share_report_meta( pw_user_share_report_outgoing( $params['user_id'] ) );

	pwAjaxRespond( $user_share_report_outgoing );
}
add_action("wp_ajax_nopriv_pw_user_share_report_outgoing", "pw_user_share_report_outgoing_ajax");
add_action("wp_ajax_pw_user_share_report_outgoing", "pw_user_share_report_outgoing_ajax");


//---------- SHARE REPORT - POST ----------//
function pw_post_share_report_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$post_share_report = pw_post_share_report_meta( pw_post_share_report( $params['post_id'] ) );

	pwAjaxRespond( $post_share_report );
}
add_action("wp_ajax_nopriv_pw_post_share_report", "pw_post_share_report_ajax");
add_action("wp_ajax_pw_post_share_report", "pw_post_share_report_ajax");


//---------- TRASH POST ----------//
function pw_trash_post_admin(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$pw_trash_post = pw_trash_post( $params );

	pwAjaxRespond( $pw_trash_post );
}
add_action("wp_ajax_pw_trash_post", "pw_trash_post_admin");



//---------- RESET PASSWORD SUBMIT ----------//
function pw_reset_password_submit_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$reset_password_submit = pw_reset_password_submit( $params );

	pwAjaxRespond( $reset_password_submit );
}
add_action("wp_ajax_nopriv_reset_password_submit", "pw_reset_password_submit_ajax");
add_action("wp_ajax_reset_password_submit", "pw_reset_password_submit_ajax");




//---------- SEND RESET PASSWORD LINK ----------//
function pw_reset_password_email_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$send_reset_password_link = pw_reset_password_email( $params );

	pwAjaxRespond( $send_reset_password_link );
}
add_action("wp_ajax_nopriv_reset_password_email", "pw_reset_password_email_ajax");
add_action("wp_ajax_reset_password_email", "pw_reset_password_email_ajax");



//---------- ACTIVATE USER ----------//
function pw_activate_user_anon(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$pw_activate_user = pw_activate_user( $params );

	pwAjaxRespond( $pw_activate_user );
}
add_action("wp_ajax_nopriv_pw_activate_user", "pw_activate_user_anon");
add_action("wp_ajax_pw_activate_user", "pw_activate_user_anon");



//---------- SEND ACTIVATION LINK ----------//
function pw_activation_email_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$send_activation_link = pw_activation_email( $params );

	pwAjaxRespond( $send_activation_link );
}
add_action("wp_ajax_nopriv_pw_activation_email", "pw_activation_email_ajax");
add_action("wp_ajax_pw_activation_email", "pw_activation_email_ajax");


//---------- PW INSERT USER ----------//
function pw_insert_user_anon(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];

	$pw_insert_user = pw_insert_user( $params );

	pwAjaxRespond( $pw_insert_user );
}
add_action("wp_ajax_nopriv_pw_insert_user", "pw_insert_user_anon");
add_action("wp_ajax_pw_insert_user", "pw_insert_user_anon");


//---------- WP USER QUERY ----------//
function wp_user_query_anon(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];

	//pw_log("wp_user_query_anon : PARAMS : ",$params);

	$user_query = new WP_User_Query( $params );

	$results = $user_query->get_results();

	//pw_log("wp_user_query_anon : RESULTS : ",$results);

	pwAjaxRespond( $results );
}
add_action("wp_ajax_nopriv_wp_user_query", "wp_user_query_anon");
add_action("wp_ajax_wp_user_query", "wp_user_query_anon");


//---------- GET AVATAR SIZES ----------//
function pw_get_avatars_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	$return = pw_get_avatars( $params );
	pwAjaxRespond( $return );
}
add_action("wp_ajax_pw_get_avatars", "pw_get_avatars_ajax");


//---------- SET AVATAR ----------//
function pw_set_avatar_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$params = $args['args'];
	$pw_set_avatar = pw_set_avatar( $params );
	pwAjaxRespond( $pw_set_avatar );
}
add_action("wp_ajax_pw_set_avatar", "pw_set_avatar_ajax");


//---------- GET AVATAR ----------//
function pw_get_avatar_anon(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$pw_get_avatar = pw_get_avatar( $params );

	pwAjaxRespond( $pw_get_avatar );
}
add_action("wp_ajax_nopriv_pw_get_avatar", "pw_get_avatar_anon");
add_action("wp_ajax_pw_get_avatar", "pw_get_avatar_anon");


//---------- SET COMMENT POINTS ----------//
function pw_set_comment_points_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();

	$params = $args['args'];
	$comment_id = $params['comment_id'];
	$points = $params['points'];

	$response = pw_set_comment_points( $comment_id, $points );

	pwAjaxRespond( $response );
}

//add_action("wp_ajax_nopriv_pw_set_post_points", "pw_set_post_points_admin");
add_action("wp_ajax_pw_set_comment_points", "pw_set_comment_points_ajax");


//---------- SET POST POINTS ----------//
function pw_set_post_points_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();

	$vars = $args['args'];
	$response_data = pw_set_post_points( $vars['post_id'], $vars['points'] );

	pwAjaxRespond( $response_data );
}

//add_action("wp_ajax_nopriv_set_post_points", "set_post_points_admin");
add_action("wp_ajax_pw_set_post_points", "pw_set_post_points_ajax");




//---------- SET POST RELATIONSHIP ----------//
function pw_set_post_relationship_ajax(){
	list($response, $args, $nonce) = initAjaxResponse();
	$vars = $args['args'];

	$set_post_relationship = pw_set_post_relationship(
		$vars['relationship'],
		$vars['switch'],
		$vars['post_id'] );

	pwAjaxRespond( $set_post_relationship );
}
add_action("wp_ajax_pw_set_post_relationship", "pw_set_post_relationship_ajax");




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
	$vars = $args['args'];
	$vars['fields'] = array( 'user_nicename', 'display_name', 'ID', 'user_login' );
	$vars['search_columns'] = array( 'user_login', 'user_nicename', 'user_email', 'display_name', 'user_url' );
	$user_query = new WP_User_Query( $vars );
	$results = $user_query->get_results();
	pwAjaxRespond( $results );
}
add_action("wp_ajax_nopriv_user_query_autocomplete", "user_query_autocomplete_anon");
add_action("wp_ajax_user_query_autocomplete", "user_query_autocomplete_anon");




//---------- TAXONOMIES OUTLINE MIXED ----------//
function taxonomies_outline_mixed_anon(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args['args'];

	$taxonomies_outline_mixed = taxonomies_outline_mixed( $pw_args );
	
	pwAjaxRespond( $taxonomies_outline_mixed );
}
add_action("wp_ajax_nopriv_taxonomies_outline_mixed", "taxonomies_outline_mixed_anon");
add_action("wp_ajax_taxonomies_outline_mixed", "taxonomies_outline_mixed_anon");




//---------- SAVE POST ADMIN ----------//
function pw_save_post_admin(){
	list($response, $args, $nonce) = initAjaxResponse();
	$pw_args = $args['args'];
	$pw_save_post = pw_save_post($pw_args);

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $pw_save_post;
	echo json_encode( $response );
	die;
}
//add_action("wp_ajax_nopriv_pw_save_post", "pw_save_post_admin");
add_action("wp_ajax_pw_save_post", "pw_save_post_admin");


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
	

	$fields = array(
		"ID",
		"post_type",
		"post_id",
		"post_status",
		"post_title",
		"post_excerpt",
		"post_content",
		"link_format",
		"post_class",
		"link_url",
		"post_name",
		"post_permalink",
		"taxonomy(all)",
		"taxonomy_obj(post_tag)",
		"author(ID,display_name,user_nicename,posts_url,user_profile_url)",
		"image(id)",
		"image(meta)"
		);
	*/

	/* set the response type as JSON */
	$results = pw_get_post( $pw_args, 'edit' ); //$post_id,$fields,$user_ID

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
	// GET OEMBED
	$oEmbed = pw_oembed_get( $args );
	pwAjaxRespond( $oEmbed );
}
add_action("wp_ajax_nopriv_ajax_oembed_get", "ajax_oembed_get");
add_action("wp_ajax_ajax_oembed_get", "ajax_oembed_get");



/* *************************
 *	General Ajax Functions 
 * 
 ************************** */

global $pw;
$postworld_api_version = $pw['info']['version'];

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

	if (!isset($params['args'])) ErrorReturn($response, 400, 'Error in parameters');
	$args = $params['args'];	
	return array($response, $args, $nonce);
}




/* *************************
 *	Feed Functions 
 * 
 ************************** */

function pw_get_live_feed_ajax() {
	list($response, $args, $nonce) = initAjaxResponse();
	$vars = $args['args'];
	$results = pw_get_live_feed ( $vars );
	pwAjaxRespond( $results );
}
add_action("wp_ajax_pw_get_live_feed", "pw_get_live_feed_ajax");
add_action("wp_ajax_nopriv_pw_get_live_feed", "pw_get_live_feed_ajax");


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
	//$pw_args = $args['args'];
	
	// Setup default args
	$default_args = array(
		'post_ids'	=>	array(),
		'fields'	=>	'preview',
		'options'	=>	array(),
		);
	$args = array_replace_recursive( $default_args, $args );

	// Get the results in array format, so that it is converted once to json along with the rest of the response
	$results = pw_get_posts ( $args['post_ids'], $args['fields'], $args['options'] );
	
	// RETURN
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
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

	// Apply O-Embed and Hot-links
	if( isset($results['post_content']) )
		$results['post_content'] = pw_embed_content( $results['post_content'] );

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

/* Actions for pw_get_comment () */

function pw_get_comment_anon() {
	list($response, $args, $nonce) = initAjaxResponse();
	// $args has all function arguments. in this case it has only one argument
	if($args['comment_id']) $comment_id = $args['comment_id'];
	else ErrorReturn($response, 400, 'missing argument comment_id'); 
	
	if ($args['fields']) $fields = $args['fields'];
	else $fields = 'all';
	
	if ($args['viewer_user_id']) $viewer_user_id = $args['viewer_user_id'];
	else $viewer_user_id = null;
	
	/* set the response type as JSON */
	$results = pw_get_comment($comment_id, $fields, $viewer_user_id);
	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $results;
	echo json_encode($response);
	// documentation says that die() should be the end...
	die();
}

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
	$response_data = pw_get_comments( $query, $fields, $tree);

	pwAjaxRespond( $response_data );

}

/* Action Hook for pw_get_comment() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_comment", "pw_get_comment_anon");
add_action("wp_ajax_pw_get_comment", "pw_get_comment_anon");

/* Action Hook for pw_get_comments() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_comments", "pw_get_comments_anon");
add_action("wp_ajax_pw_get_comments", "pw_get_comments_anon");

/* Action Hook for pw_get_comments() - Anonymous users */
//add_action("wp_ajax_nopriv_pw_get_comments", "pw_get_comments_anon");
//add_action("wp_ajax_pw_get_comments", "pw_get_comments_anon");



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
	
	//wp_filter_nohtml_kses( $data )
	
	// If comment ID is provided
	if ( $commentdata['comment_ID'] ){
	// Check to see if comment already exists
		$current_comment = get_comment( $commentdata['comment_ID'], "ARRAY_A" );
		// If comment exists
		if( $current_comment != null ){
			$user_ID = $current_comment["user_id"];
			// If user doesn't have access to moderate
			if ( !current_user_can( 'moderate_comments' ) )
				return array( "error" => "No access to moderate comments." );
		}

	} else{
		// Get User ID, it must be real, since this function is called for logged in users only
		$user_ID = get_current_user_id();
		if (!$user_ID) ErrorReturn($response, 400, 'User must be authenticated to perform this action');
		$commentdata['user_id'] = $user_ID;
	}

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
	
	// Remove HTML
	$commentdata['comment_content'] = wp_filter_nohtml_kses( $commentdata['comment_content'] );

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