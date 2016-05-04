<?php
/**
 * Verifies the NONCE in an AJAX request
 * If the NONCE is not valid, the request is immeadiately canceled
 * And the user's IP is added to the Postworld IP table.
 */
function pwAjaxAuth() {

	if( !pw_config_in_db_tables('ips') )
		return true;

	// Get the current action var
	$action = _get( $_GET, 'action' );

	/**
	 * The Postworld actions to verify authorization on.
	 * @todo Make a standard way of registering actions in core/ajax.php
	 */
	$postworld_actions = array(
		'pw_get_posts',
		'pw_query',
		'pw_post_share_report',
		'pw_get_comments'		
		);

	if( !in_array( $action, $postworld_actions ) )
		return false;

	$params_json = file_get_contents("php://input");
	$params = json_decode($params_json,true);
	$nonce = $params['nonce'];

	// Authorize the NONCE
	$auth = wp_verify_nonce( $nonce, 'postworld_ajax' );
	//pw_log( 'load time', pw_get_microtimer( 'load' ) );

	// If it isn't authorized
	if( $auth == false ){
		global $wpdb;
		$ip_table = $wpdb->postworld_prefix.'ips';
		$ipv4 = ip2long( $_SERVER['REMOTE_ADDR'] );

		// Count the number of times the IP is listed
		$ip_count = $wpdb->get_var( "SELECT COUNT(*) FROM $ip_table WHERE ipv4 = $ipv4" );

		// If the IP address isn't listed yet
		if( $ip_count == 0 )
			// Add the user's IP address to list of banished users
			$wpdb->insert(
				$ip_table,
				array(
					'ipv4' => $ipv4,
					'PTR' => $_SERVER['REMOTE_ADDR'],
					'reason' => 'no_nonce',
					'time' => date('Y-m-d H:i:s',time())
					)
				);

		header('HTTP/1.0 403 Forbidden');
		die();
	}
	else{
		//pw_log( 'NONCE VERIFIED', $nonce );
		// Remove from IP List
	}
	
	return true;

}
pwAjaxAuth();

?>