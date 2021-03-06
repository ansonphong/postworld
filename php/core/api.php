<?php
/*
     _    ____ ___       _   _ _   _ _ _ _   _           
    / \  |  _ \_ _|  _  | | | | |_(_) (_) |_(_) ___  ___ 
   / _ \ | |_) | |  (_) | | | | __| | | | __| |/ _ \/ __|
  / ___ \|  __/| |   _  | |_| | |_| | | | |_| |  __/\__ \
 /_/   \_\_|  |___| (_)  \___/ \__|_|_|_|\__|_|\___||___/
                                                         
/////////////////////////////////////////////////////////*/
/**
 * @todo Cleanup and improve function documentation.
 */

/**
 * Gets the sub-key value from an associative array
 *
 * @param array $obj An associative array
 * @param string $key A string denoting which subkey to retreive
 *			- ie. "key.subkey.subsubkey"
 * @return mixed The object at the specified subkey, false if it doesn't exist
 *
 * @todo Revisit this so it returns an undefined value, not false 
 */
function _get( $obj, $key ){

	// If the obj is not an array, return false
	if( !is_array( $obj ) )
		return false;

	// If $key is empty, return the whole $obj
	if( empty($key) )
		return $obj;

	///// KEY PARTS /////
	// FROM : "key.subkey.sub.subkey"
	// TO 	: array( "key", "subkey", "subkey" )
	$key_parts = explode( '.', $key );
	// Count how many parts
	$key_parts_count = count( $key_parts );

	foreach($key_parts as $key_part){
		if( isset( $obj[$key_part] ) )
			$obj = $obj[$key_part];
		else
			return false;
	}

	return $obj;

}

/**
 * Sets the sub-key value of an associative array
 * even if it or it's parent(s) doesn't exist yet.
 * @param array $obj An associative array
 * @param string $key A period deliniated string denoting which subkey to set
 *			- ie. "key.subkey.subsubkey"
 * @param mixed $value The value which to set
 * @return mixed The object with the new value set
 */
function _set( $obj, $key, $value ){
	/* // For Debuggin'
	if( !is_array($obj) ){
		pw_log( '_SET : NOT ARRAY : OBJ', $obj );
		pw_log( '_SET : NOT ARRAY : KEY', $key );
		pw_log( '_SET : NOT ARRAY : VALUE', $value );
	}
	*/
	///// KEY PARTS /////
	// FROM : "key.subkey.sub.subkey"
	// TO 	: array( "key", "subkey", "subkey" )
	$key_parts = array_reverse( explode( '.', $key ) );
	// Count how many parts
	$key_parts_count = count( $key_parts );
	
	// Prepare to catch finished key parts
	$key_parts_done = array();

	// Iterate through each key part
	$seed = array();
	$i = 0;
	foreach( $key_parts as $key_part ){
		$i++;
		// First Key
		if( $i == 1 ){
			// Create seed with first key
			$seed[$i][$key_part] = $value;
		// Other Keys
		} else{
			// Nest previous seed in current key
			$seed[$i][$key_part] = $seed[($i-1)];
		}
		// Last Key
		if( $i == $key_parts_count ){
			// Return final seed result
			$seed = $seed[$i];
		}
	}

	// Merge $seed array with input $array
	$obj = array_replace_recursive( $obj, $seed );

	return $obj;
}

/**
 * Pushes a value to a nested array.
 * @param array $obj A multi-dimentional array, with keys.
 * @param string $key Dot notation location of the array to modify.
 * @param any $value The value to push to the specified array.
 */
function _push( $obj, $key, $value ){

	// Get the original key value
	$array = _get( $obj, $key );

	// If it's not an array, make it an empty array
	if( !is_array( $array ) ){
		$array = array();
		$obj = _set( $obj, $key, $array );
	}

	// Add the value to the array
	$array[] = $value;

	// Add it to the object
	$obj = _set( $obj, $key, $array );

	return $obj;

}


/*
     _    ____ ___       _   _                 __  __      _        
    / \  |  _ \_ _|  _  | | | |___  ___ _ __  |  \/  | ___| |_ __ _ 
   / _ \ | |_) | |  (_) | | | / __|/ _ \ '__| | |\/| |/ _ \ __/ _` |
  / ___ \|  __/| |   _  | |_| \__ \  __/ |    | |  | |  __/ || (_| |
 /_/   \_\_|  |___| (_)  \___/|___/\___|_|    |_|  |_|\___|\__\__,_|

////////////////////////////////////////////////////////////////////*/
 /**
  * Sets meta key for the given user under the given key
  * in the `wp_usermeta` table.
  * Object values passed in can be passed as PHP objects or Arrays,
  * and they will automatically be converted and stored as JSON
  */
function pw_set_wp_usermeta( $vars ){
	$default_vars = array(
		'user_id'	=>	get_current_user_id(),
		'meta_key'	=>	PW_USERMETA_KEY,
		'sub_key'	=>	'',
		'value'		=>	'',
		);

	$vars = array_replace_recursive( $default_vars, $vars );

	//pw_log( json_encode( $vars ) );

	extract($vars);

	// TODO : Use pw_auth_user() here

	///// USER ID /////
	// Security check to see if user can access user meta
	$user_id = pw_check_user_id( $user_id );
	if( !is_numeric( $user_id ) )
		return $user_id; // Will be: array('error'=>'[Error message]')

	///// SUBKEY ////
	if( !empty( $sub_key ) ){
		///// SETUP DATA /////
		// Check if the meta key exists
		$meta_value = get_user_meta( $user_id, $meta_key, true );

		// If it exists, decode it from a JSON string into an object
		if( is_string( $meta_value ) && !empty($meta_value) )
			$meta_value = json_decode($meta_value, true);
		// If it does not exist, define it as an empty array
		else
			$meta_value = array();

		///// SET VALUE /////
		$meta_value = _set( $meta_value, $sub_key, $value );

		// Encode back into JSON
		$meta_value = json_encode( $meta_value );

	}

	///// NO SUBKEY /////
	else if( !empty( $value ) ){
		
		if( is_array( $value ) )
			$value = json_encode($value);

		$meta_value = $value;

	}
	// Set user meta
	$update_user_meta = update_user_meta( $user_id, $meta_key, $meta_value );

	// Boolean, true on successful update, false on failure.
	return $update_user_meta;

}

/**
 * Gets meta key for the given user under the `pw_meta` key
 * in the `wp_usermeta` table.
 */
function pw_get_wp_usermeta($vars){
	/*
	PARAMETERS:
	$vars = array(
		"user_id"	=>	[integer], 	(optional)
		"sub_key"	=>	[string],
		"format" 	=>	[string] 	"JSON" / "ARRAY" (default),
		"meta_key" 	=>	[string] 	(optional)
		);
	*/

	extract($vars);
	$meta_key = PW_USERMETA_KEY;

	///// USER ID /////
	// Security check to see if user can access user meta
	$user_id = pw_check_user_id( $user_id );
	if( !is_numeric($user_id) )
		return $user_id;

	///// KEY /////
	if( !isset($sub_key) )
		$sub_key = '';

	///// GET DATA /////
	// Check if the meta key exists
	$meta_value = get_user_meta( $user_id, $meta_key, true );
	if( empty($meta_value) )
		return false;

	// Decode from JSON
	$meta_value = json_decode( $meta_value, true );

	// Get Subkey
	$return = _get( $meta_value, $sub_key );
	if( $return == false )
		return $return;

	///// FORMAT /////
	if( isset($format) && $format == 'JSON' )
		return json_encode( $return );
	
	return $return;

}


/*   _    ____ ___       ____           _     __  __      _        
    / \  |  _ \_ _|  _  |  _ \ ___  ___| |_  |  \/  | ___| |_ __ _ 
   / _ \ | |_) | |  (_) | |_) / _ \/ __| __| | |\/| |/ _ \ __/ _` |
  / ___ \|  __/| |   _  |  __/ (_) \__ \ |_  | |  | |  __/ || (_| |
 /_/   \_\_|  |___| (_) |_|   \___/|___/\__| |_|  |_|\___|\__\__,_|
                                                                   
////////////////////////////////////////////////////////////////////*/

/**
 * Sets meta key for the given post under the given key
 * in the `wp_postmeta` table.
 * Object values passed in can be passed as PHP objects or Arrays,
 * and they will automatically be converted and stored as JSON
 */
function pw_set_wp_postmeta($vars){
	/*	
	PARAMETERS:
	$vars = array(
		"post_id"		=>	[integer], 	(optional)
		"sub_key"		=>	[string],	(optional)
		"meta_value" 	=>	[mixed],	(required)
		"meta_key" 		=>	[string] 	(optional)
		);
	*/
	global $post;
	$default_post_id = ( gettype($post) == 'object' ) ? $post->ID : false;

	$default_vars = array(
		'post_id' => $default_post_id,
		'meta_key' => pw_postmeta_key,
		'sub_key' => null,
		'meta_value' => null,
		);
	$vars = array_replace( $default_vars, $vars );

	extract($vars);

	///// USER ID /////
	$post_id = pw_check_user_post( $post_id );
	if( !is_numeric($post_id) )
		return $post_id; // Will be: array('error'=>'[Error message]')

	///// SUB KEY /////
	if( !empty($sub_key) ){

		///// SETUP DATA /////
		// Check if the meta key exists
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// If it exists, decode it from a JSON string into an object
		if( !empty($meta_value) )
			$meta_value = json_decode($meta_value, true);
		// If it does not exist, define it as an empty array
		else
			$meta_value = array();

		///// SET VALUE /////
		$meta_value = _set( $meta_value, $sub_key, $value );

		// Encode back into JSON
		$meta_value = json_encode( $meta_value );

	} else{
		if( is_array( $meta_value ) || is_object( $meta_value ) )
			// Encode arrays and objects into JSON
			$meta_value = json_encode( $meta_value );	
	}

	// Set user meta
	$update_post_meta = update_post_meta( $post_id, $meta_key, $meta_value );

	// BOOLEAN : True on successful update, false on failure.
	return $update_post_meta;

}

/**
 * Wrapper function to make pw_get_wp_postmeta easier to use.
 */
function pw_grab_postmeta( $post_id = 0, $meta_key = PW_POSTMETA_KEY, $sub_key = null ){
	if( $post_id === 0 ){
		global $post;
		$post_id = $post->ID;
	}

	return pw_get_wp_postmeta( array(
		"post_id"	=>	$post_id,
		"meta_key" 	=>	$meta_key,
		"sub_key"	=>	$sub_key
		));
}

/**
 * Gets meta key for the given post under the given key
 * in the `wp_postmeta` table.
 */
function pw_get_wp_postmeta($vars){
	/*
		PARAMETERS:
		$vars = array(
			"post_id"	=>	[integer], 	(optional)
			"meta_key" 	=>	[string] 	(optional)
			"sub_key"	=>	[string],
			);
	*/

	///// SET DEFAULTS /////
	if( !isset( $vars['meta_key'] ) )
		$vars['meta_key'] = PW_POSTMETA_KEY;

	if( !isset( $vars['post_id'] ) ){
		global $post;
		$vars['post_id'] = $post->ID;
	}

	extract($vars);

	///// KEY /////
	if( !isset($sub_key) )
		$sub_key = '';

	///// GET DATA /////
	// Check if the meta key exists
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if( empty($meta_value) )
		return false;

	// Decode from JSON
	$meta_value = json_decode( $meta_value, true );

	// Get Subkey
	$value = _get( $meta_value, $sub_key );
	
	return $value;

}


function pw_get_postmeta( $vars = array() ){
	global $post;
	
	///// DEFAULT VALUES /////
	$defaultVars = array(
		'post_id' 	=> $post->ID,
		'meta_key' 	=> PW_POSTMETA_KEY,
		'sub_key'	=> false,
		'type' 		=> 'json',
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	extract( $vars );

	///// CACHING LAYER /////
	// Make a global to cache data at runtime
	// To prevent making multiple queries on the same postmeta
	global $pw_postmeta_cache;

	// If cached data is already found
	if( isset( $pw_postmeta_cache[$post_id][$meta_key] ) ){
		// If no subkey
		if( empty( $vars['sub_key'] ) )
			// Return the whole value
			return $pw_postmeta_cache[$post_id][$meta_key];
		else
			// Otherwise, get and return the subkey value
			return _get( $pw_postmeta_cache[$post_id][$meta_key], $vars['sub_key'] );
	}

	///// GET POST META /////
	// Get Post Meta
	$metadata = get_post_meta( $vars['post_id'], $vars['meta_key'], true );
	
	if( empty( $metadata ) )
		$metadata = array();

	// Convert from JSON to A_ARRAY
	if( $vars['type'] == "json" && is_string( $metadata ) )
		$metadata = json_decode( $metadata, true );

	///// APPLY FILTERS /////
	// If the value is from the default postmeta key
	if( $meta_key == PW_POSTMETA_KEY )
		// Apply the standard filter ID
		$metadata = apply_filters( PW_POSTMETA, $metadata );
	else
		// Apply custom filter ID
		$metadata = apply_filters( PW_POSTMETA.'-'.$meta_key, $metadata );

	///// CACHING LAYER /////
	// Store meta data in a runtime cache
	if( !isset( $pw_postmeta_cache[$post_id] ) ) 
		$pw_postmeta_cache[ $post_id ] = array();
	$pw_postmeta_cache[ $post_id ][ $meta_key ] = $metadata;

	///// SUB KEY /////
	// If no subkey
	if( empty( $vars['sub_key'] ) )
		// Return the whole value
		return $metadata;
	else
		// Otherwise, get and return the subkey value
		return _get( $metadata, $vars['sub_key'] );

}






/*   _    ____ ___       _____            __  __      _        
    / \  |  _ \_ _|  _  |_   _|_ ___  __ |  \/  | ___| |_ __ _ 
   / _ \ | |_) | |  (_)   | |/ _` \ \/ / | |\/| |/ _ \ __/ _` |
  / ___ \|  __/| |   _    | | (_| |>  <  | |  | |  __/ || (_| |
 /_/   \_\_|  |___| (_)   |_|\__,_/_/\_\ |_|  |_|\___|\__\__,_|
                                                               
////////////////////////////////////////////////////////////////////*/


function pw_set_wp_taxonomymeta($vars){
	/*
		- Sets meta key for the given term under the given key
			in the `wp_taxonomymeta` table
		- Object values passed in can be passed as PHP objects or Arrays,
			and they will automatically be converted and stored as JSON
		
		PARAMETERS:
		$vars = array(
			"term_id"	=>	[integer], 	(optional)
			"sub_key"	=>	[string],	(required)
			"value" 	=>	[mixed],	(required)
			"meta_key" 	=>	[string] 	(optional)
			);
	*/

	// If the Taxonomy Metadata functions aren't loaded
	if( !function_exists('update_term_meta') )
		return false;

	extract($vars);

	///// DEFAULT META KEY /////
	if( !isset( $meta_key ) )
		$meta_key = PW_TAXMETA_KEY;

	///// TERM ID /////
	if( !isset($term_id) ){
		global $pw;
		$term_id = _get( $pw, 'view.term.term_id' );
	}

	///// SECURITY LAYER /////
	if( !user_can('manage_categories') )
		return array( 'error' => 'No user capabilities.' );

	///// SUB KEY /////
	if( isset($sub_key) ){

		///// SETUP DATA /////
		// Check if the meta key exists
		$meta_value = get_term_meta( $term_id, $meta_key, true );

		// If it exists, decode it from a JSON string into an object
		if( !empty($meta_value) )
			$meta_value = json_decode($meta_value, true);
		// If it does not exist, define it as an empty array
		else
			$meta_value = array();

		///// SET VALUE /////
		$meta_value = _set( $meta_value, $sub_key, $value );

		// Encode back into JSON
		$meta_value = json_encode( $meta_value );

	} else{
		if( is_array( $meta_value ) || is_object( $meta_value ) )
			// Encode arrays and objects into JSON
			$meta_value = json_encode( $meta_value );	
	}

	// Set term meta
	$update_term_meta = update_term_meta( $term_id, $meta_key, $meta_value );

	// BOOLEAN : True on successful update, false on failure.
	return $update_term_meta;

}


function pw_get_wp_taxonomymeta($vars){
	/*
	- Gets meta key for the given post under the given key
		in the `wp_taxonomymeta` table
	
		PARAMETERS:
		$vars = array(
			"term_id"	=>	[integer], 	(optional)
			"meta_key" 	=>	[string] 	(optional)
			"sub_key"	=>	[string],
			);
	*/

	// If the Taxonomy Metadata functions aren't loaded
	if( !function_exists('get_term_meta') )
		return false;

	extract($vars);

	///// TERM ID /////
	if( !isset($term_id) ){
		global $pw;
		$term_id = _get( $pw, 'view.term.term_id' );
	}

	///// KEY /////
	if( !isset($sub_key) )
		$sub_key = '';

	///// IF NO META KEY /////
	if( !isset( $meta_key ) ){
		// Get all the meta values for that term
		$meta_values = get_term_meta( $term_id, '' );

		// If no values
		if( empty( $meta_values ) )
			return false;

		// Iterate through each key
		foreach( $meta_values as $key => $value ){
			// If the value is array, and only one value
			if( is_array( $value ) && count( $value ) == 1 ){
				// Transform value to first value in array
				$meta_values[$key] = pw_sanitize_numeric( $value[0] );
			}
		}
		$value = $meta_values;

	}
	else{
		///// GET DATA /////
		// Check if the meta key exists
		$meta_value = get_term_meta( $term_id, $meta_key, true );
		if( empty($meta_value) )
			return false;

		// If a subkey is set
		if( !empty( $sub_key ) ){
			// Decode from JSON
			$meta_value = json_decode( $meta_value, true );
			// Get Subkey
			$value = _get( $meta_value, $sub_key );
		}
		else
			$value = $meta_value;
	}

	return $value;

}





/*   _    ____ ___        ___        _   _                 
    / \  |  _ \_ _|  _   / _ \ _ __ | |_(_) ___  _ __  ___ 
   / _ \ | |_) | |  (_) | | | | '_ \| __| |/ _ \| '_ \/ __|
  / ___ \|  __/| |   _  | |_| | |_) | |_| | (_) | | | \__ \
 /_/   \_\_|  |___| (_)  \___/| .__/ \__|_|\___/|_| |_|___/
                              |_|                          
//////////////////////////////////////////////////////////*/

function pw_get_option( $vars ){
	// Returns a sub key stored in the specified option name
	//	From `wp_options` table
	/*
		vars = array(
			'option_name'	=>	[string], // "i-options",
			'key'			=> 	[string], // "images.logo",
			'filter'		=>	[boolean] // Gives option to disable filtering
			'cache'			=>	[boolean] // Gives option to disable caching
	*/

	$default_vars = array(
		'option_name'	=>	PW_OPTIONS_SITE,
		'key'			=>	false,
		'filter'		=>	true,
		'cache'			=>	true,
		);
	$vars = array_replace_recursive( $default_vars, $vars );

	extract($vars);

	///// CACHING LAYER /////
	// Make a global to cache data at runtime
	// To prevent making multiple queries and json_decodes on the same option
	global $pw_options_cache;
	// Get the number of filters on the option name
	$filter_count = pw_filter_count( $option_name );
	// Get the number of filtered on (possible) cached value
	$cached_filter_count = _get( $pw_options_cache, $option_name .'.filter_count' );
	// If there is the name number of filters (no new filters)
	if( $filter_count === $cached_filter_count && $cache == true ){
		// Get the cached value
		$value = _get( $pw_options_cache, $option_name .'.value' );
	}
	// Otherwise, go through and get the option again and re-filter the value
	else{
		///// GET OPTION /////
		// Retreive Option
		$value = get_option( $option_name, array() );
		
		// Decode from JSON, assuming it's a JSON string
		if( !empty( $value ) )
			$value = json_decode( $value, true );

		///// APPLY FILTERS /////
		// This allows themes to over-ride default settings for options
		// ie. pwGetOption-postworld-styles-theme, to modify the default values
		if( $filter ){
			// Apply Filters
			$value = apply_filters( $option_name , $value );
		}

		///// CACHING LAYER /////
		// Set the decoded data into runtime cache
		$pw_options_cache[$option_name] = array();
		$pw_options_cache[$option_name]['filter_count']	= pw_filter_count( $option_name );
		$pw_options_cache[$option_name]['value'] = $value;

	}

	// If no key set, return the value directly
	if( empty( $key ) )
		return $value;
	// Get Option Value Object Key Value
	else
		return _get( $value, $key );

}

function pw_set_option( $vars ){
	do_action('pw_set_option', $vars );
	
	// Modifies a sub key stored in the specified option name
	//	From `wp_options` table
	/*
		$vars = array(
			'option_name'	=>	[string], // "postworld-options",
			'key'			=> 	[string], // "images.logo",
			'value'			=>	[mixed],
	*/

	// If no option name or value is set, return
	if( !isset( $vars['option_name'] ) || 
		!isset( $vars['value'] ) )
		return false;

	extract($vars);

	///// EMPTY KEY /////
	// If there's no or an empty key
	if( !isset( $vars['key'] ) || empty($vars['key']) ){
		if( is_array($option_value) || is_object($option_value) )
			$option_value = json_encode($option_value);
		return update_option( $option_name, $option_value );
	}

	///// GET STORED VALUE /////
	$option_value = get_option( $option_name, array() );


	// Decode from JSON, assuming it's a JSON string
	if( !empty( $option_value ) )
		$option_value = json_decode( $option_value, true );


	///// SET VALUE /////
	$option_value = _set( $option_value, $key, $value );


	// Encode to JSON
	$option_value = json_encode($option_value);

	// Update DB
	$update_option = update_option( $option_name, $option_value );

	return $update_option;

}


function pw_grab_option( $option_name, $key = false, $disable_cache = false ){
	// Quick routine method to get option subkey
	return pw_get_option( array(
		'option_name' => $option_name,
		'key' => $key,
		'cache' => !$disable_cache,
		));
}


/**
 * SET CUSTOM DEFAULT
 * Cherry-picks data from the 'postworld-defaults' wp_option
 * and injects it into the subject array if the value doesn't exist.
 * This is used in conjunction with Postworld Custom Defaults
 * to streamline setting default values.
 *
 * @param array $vars Set of input values. 
 *		['subject'] - (any) The subject to operate on, usually an array.
 *		['type'] - (string) The primary defaults subkey, ie. 'wp_postmeta'
 *		['default_key'] -  (string) Dot notation path to the nested key value in the defaults.
 *		['subject_key'] - (string) Optional. Dot notation path to destination in subject, if different from key.
 *
 * @param any The subject with the specified key replaced with the default.
 */
function pw_set_custom_default( $vars ){
	/*
	EXAMPLE : 
	$vars = array(
		'subject' => $post['post_meta'],
		'type' => 'wp_postmeta',
		'default_key' => PW_POSTMETA_KEY.'.featured_image.display'
		'subject_key' => PW_POSTMETA_KEY.'.featured_image.display'
		)
	*/
	// Require types and values
	if( !isset( $vars['subject'] ) || 
		!is_array( $vars['subject'] ) ||
		!isset( $vars['type'] ) ||
		!is_string( $vars['type'] ) ||
		!isset( $vars['default_key'] ) ||
		!is_string( $vars['default_key'] ) )
		return false;

	// Assume subject key is the same as default key
	if( !isset( $vars['subject_key'] ) || !is_string( $vars['subject_key'] ) )
		$vars['subject_key'] = $vars['default_key'];
	
	// Get the array of defaults saved for specified type
	$defaults = $custom_defaults = pw_get_option(array(
		'option_name' => PW_OPTIONS_DEFAULTS,
		'key' => $vars['type']
		));

	// Get the specified default value
	$default_value = _get( $defaults, $vars['default_key'] );

	// Get the specified destination value
	$subject_value = _get( $vars['subject'], $vars['subject_key'] );

	// If the subject value is empty, or the magic string 'default'
	if( empty($subject_value) || $subject_value === 'default' )
		$vars['subject'] = _set( $vars['subject'], $vars['subject_key'], $default_value );

	return $vars['subject'];

}

//////////////////////////////////////////////////////////////
//////////////////// GRAVEYARD //////////////////////////////
////////////////////////////////////////////////////////////

/**
 * DEPRECIATED - use _get()
 */
function pw_get_obj( $obj, $key ){
	return _get( $obj, $key );
}

function pw_set_obj( $obj, $key, $value ){
	// DEPRECIATED
	return _set( $obj, $key, $value );
}
