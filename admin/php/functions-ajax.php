<?php

/*   _       _   _    __  __  _____                 _   _                 
    / \     | | / \   \ \/ / |  ___|   _ _ __   ___| |_(_) ___  _ __  ___ 
   / _ \ _  | |/ _ \   \  /  | |_ | | | | '_ \ / __| __| |/ _ \| '_ \/ __|
  / ___ \ |_| / ___ \  /  \  |  _|| |_| | | | | (__| |_| | (_) | | | \__ \
 /_/   \_\___/_/   \_\/_/\_\ |_|   \__,_|_| |_|\___|\__|_|\___/|_| |_|___/
////////////////////////////////////////////////////////////////////////*/              
/* 	WP Ajax Tips
 *	http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
*/


//---------- SAVE OPTIONS ----------//
function pw_save_option(){
	list($response, $args, $nonce) = i_initAjaxResponse();
	$params = $args['args'];
	extract($params);

	/*
	// If passing an Array or Object, convert it to JSON
	if(
		gettype($option_value) == 'array' || 
		gettype($option_value) == 'object'
		){
		$option_value_sanitized = json_encode($option_value, JSON_FORCE_OBJECT);
	
	} else{
		$option_value_sanitized = $option_value;
	}
	*/

	$option_value_sanitized = $option_value;

	// Update Option
	$update_option = update_option( $option_name, $option_value_sanitized );

	// If saving the styles
	if( defined( 'PW_OPTIONS_STYLES' ) &&
		$option_name == PW_OPTIONS_STYLES )
		// Reset PHP LESS Cache
		pw_reset_less_php_cache();

	// If saving the theme settings
	if( defined( 'PW_OPTIONS_THEME' ) &&
		$option_name == PW_OPTIONS_THEME )
		// Reset PHP LESS Cache
		pw_reset_less_php_cache();

	if( $update_option == true ){
		// Return with the Option Value
		$response_data = get_option( $option_name );
	} else{
		// Return with an Error
		$response_data = array( 'message' => 'Not updated. Either there is an error or no changes have been made since the last save.' );
	}

	/**
	 * Run specified callbacks.
	 */
	if( isset($callbacks) ){

		if( in_array( 'flush_permalinks', $callbacks ) ){
			/**
			 * This is having to be done twice in order to be effective. (?)
			 * @todo Fix this so it only needs to be run once.
			 */
			//pw_log('flush_rewrite_rules');
			flush_rewrite_rules(false);
		}

	}

	/**
	 * In all instances, flush cached javascript files.
	 */
	pw_scripts_flush( null, '.js' );

	header('Content-Type: application/json');
	$response['status'] = 200;
	$response['data'] = $response_data; //$response_data;
	echo json_encode( $response );
	die;
}

add_action("wp_ajax_pw_save_option", "pw_save_option");


////////// GENERAL AJAX FUNCTIONS //////////

function i_ErrorReturn($response, $status, $message) {
	$response['status'] = $status;
	$response['message'] = $message;
	echo json_encode($response);
	die;
}

function i_initAjaxResponse() {
	global $infinite_api_version;
	// Create Response JSON Object, to include api version, status, error code if any, data results
	$response = array();
	$response['version'] = $infinite_api_version;
	// data is received in the raw data, not in the post data http://stackoverflow.com/questions/10494574/what-is-the-difference-between-form-data-and-request-payload
	// $args_text = $_POST['args']; // This will not work unless the Ajax Call is modified to post as a form with url encoded parameters
	$params_text = file_get_contents("php://input");
	// if no parameters then return error
	if (!$params_text) {
		i_ErrorReturn($response, 400, 'Error in parameters');	
	}
	$params = json_decode($params_text,true);
	if (!$params) {
		i_ErrorReturn($response, 400, 'Error in parameters');	
	}
	if (!isset($params['nonce'])) i_ErrorReturn($response, 400, 'Error in parameters');
	$nonce = $params['nonce'];
	// TODO check Nonce value
	if (!isset($params['args'])) i_ErrorReturn($response, 400, 'Error in parameters');
	$args = $params['args'];	
	return array($response, $args, $nonce);
}


?>