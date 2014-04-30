<?php
/* ___        _   _                 
  / _ \ _ __ | |_(_) ___  _ __  ___ 
 | | | | '_ \| __| |/ _ \| '_ \/ __|
 | |_| | |_) | |_| | (_) | | | \__ \
  \___/| .__/ \__|_|\___/|_| |_|___/
       |_|                          
///////////// --------- /////////////*/

define( 'pw_option_name',	'pw-options' );

function pw_set_option_obj($vars){
	/*
		- Sets a value for the given option under the defined key
			in the `wp_options` table
		- Object values passed in can be passed as PHP objects or Arrays,
			and they will automatically be converted and stored as JSON
		
		PARAMETERS:
		$vars = array(
			"option_name" 	=>	[string] 	(optional)
			"sub_key"		=>	[string],	(required)
			"value" 		=>	[mixed],	(required)
			);
	*/

	// Security Check
	if( !current_user_can( 'manage_options' ) )
		return false;

	// Extract Variables
	extract($vars);

	if( !isset( $option_name ) )
		$option_name = pw_option_name;

	///// KEY /////
	if( !isset($sub_key) )
		return array( 'error' => 'Sub-key not specified.' ); 

	///// SETUP DATA /////
	// Check if the option exists
	$option_value = get_option( $option_name, '' );

	// If it exists, decode it from a JSON string into an object
	if( !empty($option_value) )
		$option_value = json_decode($option_value, true);
	// If it does not exist, define it as an empty array
	else
		$option_value = array();

	///// SET VALUE /////
	$option_value = pw_set_obj( $option_value, $sub_key, $value );

	// Encode back into JSON
	$option_value = json_encode( $option_value );

	// Set user meta
	$update_option = update_option( $option_name, $option_value );

	// BOOLEAN : True on successful update, false on failure.
	return $update_option;

}


function pw_get_option_obj($vars){
	/*
	- Gets meta key for the given user under the defined key
		in the `wp_usermeta` table
	
		PARAMETERS:
		$vars = array(
			"option_name" 	=>	[string] 	(optional)
			"sub_key"		=>	[string],
			"format" 		=>	[string] 	"JSON" / "ARRAY" (default),
			
			);
	*/

	extract($vars);
	$option_name = pw_option_name;

	///// KEY /////
	if( !isset($sub_key) )
		$sub_key = '';

	///// GET DATA /////
	// Check if the meta key exists
	$option_value = get_option( $option_name, '' );
	if( empty($option_value) )
		return false;

	// Decode from JSON
	$option_value = json_decode( $option_value, true );

	// Get Subkey
	$return = pw_get_obj( $option_value, $sub_key );
	if( $return == false )
		return $return;

	///// FORMAT /////
	if( isset($format) && $format == 'JSON' )
		return json_encode( $return );
	
	return $return;

}


function pw_update_option( $option, $value ){
	if( current_user_can('manage_options') ){
		update_option( $option, $value );
		return get_option($option);
	}
	else
		return array('error'=>'No access.');
}



?>