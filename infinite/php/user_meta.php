<?php
/*_   _                 __  __      _        
 | | | |___  ___ _ __  |  \/  | ___| |_ __ _ 
 | | | / __|/ _ \ '__| | |\/| |/ _ \ __/ _` |
 | |_| \__ \  __/ |    | |  | |  __/ || (_| |
  \___/|___/\___|_|    |_|  |_|\___|\__\__,_|
                                             
///////////////// --------- /////////////////*/

function i_set_user_meta($vars){
	/*
		- Sets meta key for the given user under the defined key
			in the `wp_usermeta` table
		- Object values passed in can be passed as PHP objects or Arrays,
			and they will automatically be converted and stored as JSON
		
		PARAMETERS:
		$vars = array(
			"user_id"	=>	[integer], 	(optional)
			"key"	=>	[string],	(required)
			"value" 	=>	[mixed],	(required)
			"meta_key" 	=>	[string] 	(optional)
			);
	*/

	extract($vars);

	if( !isset( $meta_key ) )
		$meta_key = PW_USERMETA_KEY;

	///// USER ID /////
	$user_id = i_check_user_id( $user_id );
	if( !is_numeric($user_id) )
		return $user_id; // Will be: array('error'=>'[Error message]')

	///// KEY /////
	if( !isset($key) )
		return array( 'error' => 'Sub-key not specified.' ); 

	///// SETUP DATA /////
	// Check if the meta key exists
	$meta_value = get_user_meta( $user_id, $meta_key, true );

	// If it exists, decode it from a JSON string into an object
	if( !empty($meta_value) )
		$meta_value = json_decode($meta_value, true);
	// If it does not exist, define it as an empty array
	else
		$meta_value = array();

	///// SET VALUE /////
	$meta_value = i_set_obj( $meta_value, $key, $value );

	// Encode back into JSON
	$meta_value = json_encode( $meta_value );

	// Set user meta
	$update_user_meta = update_user_meta( $user_id, $meta_key, $meta_value );

	// BOOLEAN : True on successful update, false on failure.
	return $update_user_meta;

}


function i_get_user_meta($vars){
	/*
	- Gets meta key for the given user under the defined key
		in the `wp_usermeta` table
	
		PARAMETERS:
		$vars = array(
			"user_id"	=>	[integer], 	(optional)
			"key"	=>	[string],
			"format" 	=>	[string] 	"JSON" / "ARRAY" (default),
			"meta_key" 	=>	[string] 	(optional)
			);
	*/

	extract($vars);
	$meta_key = PW_USERMETA_KEY;

	///// USER ID /////
	$user_id = i_check_user_id( $user_id );
	if( !is_numeric($user_id) )
		return $user_id;

	///// KEY /////
	if( !isset($key) )
		$key = '';

	///// GET DATA /////
	// Check if the meta key exists
	$meta_value = get_user_meta( $user_id, $meta_key, true );
	if( empty($meta_value) )
		return false;

	// Decode from JSON
	$meta_value = json_decode( $meta_value, true );

	// Get Subkey
	$return = i_get_obj( $meta_value, $key );
	if( $return == false )
		return $return;

	///// FORMAT /////
	if( isset($format) && $format == 'JSON' )
		return json_encode( $return );
	
	return $return;

}





?>