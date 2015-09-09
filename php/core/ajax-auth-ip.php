<?php
/**
 * Kills the session if the user is blacklisted.
 */
function pw_auth_ip(){

	if( !defined('PW_IP_TABLE') )
		return false;

	if( !class_exists( 'mysqli' ) )
		return false;

	/**
	 * Connect to the DB
	 */
	$mysqli = new mysqli( 'localhost', DB_USER, DB_PASSWORD, DB_NAME );
	if( $mysqli->connect_error )
		die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);

	/**
	 * Search for reasons which would banish the current user.
	 */
	$ipv4 = ip2long( $_SERVER['REMOTE_ADDR'] );
	$results = $mysqli->query("SELECT * FROM ".PW_IP_TABLE . " WHERE ipv4 = $ipv4 AND reason = 'no_nonce' ");
	$rows = $results->num_rows;

	//echo "IP TABLE : " . PW_IP_TABLE . " // ";
	//echo "WORKING : RESULTS :: ";
	//echo json_encode($rows);


	// Frees the memory associated with a result
	$results->free();

	// Close connection 
	$mysqli->close();

	/**
	 * Conditions for Authorization: No banishment entries.  
	 */
	$auth = ($rows == 0);

	//echo microtime() - $stopwatch;

	/**
	 * If user is banished, die here.
	 */
	if( !$auth ){
		header('HTTP/1.0 403 Forbidden');
		die();
	}

}

pw_auth_ip();


?>