<?php

/* 	WP Ajax Tips
 *	http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
  
*/
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

function pw_test_code_admin() {
	// list($response, $args, $nonce) = initAjaxResponse();
	global $postworld_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $postworld_api_version;
	
	$args1 = array (
        'feed_id' => 'front_page_features',
        'write_cache'  => false,
        'feed_query' => array(
            'post_count' => 200,
            'fields' => 'all',
            'post_type' => 'post',
            'orderby' => 'date',
            'offset' => 3,
            'post_format' => null,
            'post_class' => null,
            'posts_per_page' => 200
        )
    );    
		
	// TODO check results are ok
	/* set the response type as JSON */
	// TODO check values are correct
	$results = pw_register_feed ($args1);
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

/* Action Hook for pw_get_posts() - Logged in users */
add_action("wp_ajax_pw_get_posts", "pw_get_posts_anon");

/* Action Hook for pw_get_posts() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_posts", "pw_get_posts_anon");

/* Action Hook for pw_load_feed() - Logged in users */
add_action("wp_ajax_pw_load_feed", "pw_load_feed_anon");

/* Action Hook for pw_load_feed() - Anonymous users */
add_action("wp_ajax_nopriv_pw_load_feed", "pw_load_feed_anon");


/* Action Hook for pw_get_templates() - Logged in users */
add_action("wp_ajax_pw_get_templates", "pw_get_templates_anon");

/* Action Hook for pw_get_templates() - Anonymous users */
add_action("wp_ajax_nopriv_pw_get_templates", "pw_get_templates_anon");

/* Action Hook for pw_register_feed() - Logged in users */
add_action("wp_ajax_pw_register_feed", "pw_register_feed_admin");


/* Action Hook for pw_test_code() - Logged in users */
add_action("wp_ajax_pw_test_code", "pw_test_code_admin");

/* Action Hook for pw_register_feed() - Anonymous users */
// add_action("wp_ajax_nopriv_pw_register_feed", "pw_register_feed_anon");

?>