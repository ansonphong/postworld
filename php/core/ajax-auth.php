<?php
/**
 * Verifies the NONCE in an AJAX request
 * If the NONCE is not valid, the request is immeadiately cancelled
 * And the user's IP is added to the Postworld IP table.
 */
function pwAjaxAuth() {
	$params_json = file_get_contents("php://input");
	$params = json_decode($params_json,true);
	$nonce = $params['nonce'];

	// Authorize the NONCE
	$auth = wp_verify_nonce( $nonce, 'postworld_ajax' );
	pw_log( 'load time', pw_get_microtimer( 'load' ) );

	// If it isn't authorized, end here
	if( $auth == false ){
		global $wpdb;
		$wpdb->insert(
			$wpdb->pw_prefix.'ips',
			array(
				'ipv4' => ip2long( $_SERVER['REMOTE_ADDR'] ),
				'PTR' => $_SERVER['REMOTE_ADDR'],
				'reason' => 'no_nonce',
				'time' => date('Y-m-d H:i:s',time())
				)
			);

		pw_log( 'bad request : IP', $_SERVER['REMOTE_ADDR'] );

		header('HTTP/1.0 403 Forbidden');
		die();

	}
	else
		pw_log( 'NONCE VERIFIED', $nonce );
	
	return true;

}
pwAjaxAuth();

?>