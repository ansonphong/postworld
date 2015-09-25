<?php

function i_locate_template( $template_names, $load = false, $require_once = true ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( !$template_name )
			continue;
		if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
			$located = STYLESHEETPATH . '/' . $template_name;
			break;
		} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
			$located = TEMPLATEPATH . '/' . $template_name;
			break;
		}
		///// INFINITE PATH /////
		else{
			if ( file_exists(INFINITEPATH . '/' . $template_name) ) {
				$located = INFINITEPATH . '/' . $template_name;
				break;
			}
		}
	}

	if ( $load && '' != $located )
		load_template( $located, $require_once );

	return $located;
}


function get_infinite_directory(){

	// Get the base dir, ie. "php"
	$base_dir = basename(__DIR__);

	// Get the current directory of this file
	$this_dir = dirname(__FILE__);

	// Subtract the base dir from this dir
	$infinite_dir = str_replace( '/'.$base_dir, '', $this_dir );

	return $infinite_dir; // $theme_root_uri; //TEMPLATEPATH;
}

function get_infinite_directory_uri(){
	// Setup variables
	$template_uri = get_template_directory_uri();
	$template_dir = get_template_directory();
	$infinite_dir = get_infinite_directory();

	// Subtract the Infinite Directory from the Template Dir
	$relative_path = str_replace( $template_dir, '', $infinite_dir );

	// Add the difference to the Template URI
	$infinite_uri = $template_uri . $relative_path;

	return $infinite_uri; // $theme_root_uri; //TEMPLATEPATH;
}


function i_ob_include( $file, $vars = array() ){
	if( !empty( $vars ) && is_array( $vars ) )
		extract($vars);

	if( empty( $file ) )
		return "i_ob_include : No file path provided.";

	ob_start();
	include $file;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function i_ob_include_template( $template_path, $vars = array() ){
	return i_ob_include( i_locate_template( $template_path ), $vars );
}

function i_ob_function( $function, $vars = array() ){
	ob_start();
	call_user_func( $function, $vars );
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}


function i_user_id_exists($user_id){
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = '$user_id'"));
    if($count == 1){ return true; }else{ return false; }
}

function i_check_user_id($user_id){
	///// USER ID /////
	$current_user_id = get_current_user_id();
	if( !isset( $user_id ) || !i_user_id_exists( $user_id )  )
		$user_id = $current_user_id;
	if( $user_id == 0 )
		return array( 'error' => 'No user ID.' );
	// Security Layer
	// Check if setting for current user, or if current user can edit users
	if(	$user_id != $current_user_id &&
		!current_user_can( 'edit_users' ) )
		return array( 'error' => 'No permissions.' );
	
	return $user_id;
}

function i_get_obj( $obj, $key ){
	// Checks to see if a key exists in an object,
	// and returns it if it does exist.

	/*	PARAMETERS:
		$obj 	= 	[array]
		$key 	= 	[string] ie. ( "key.subkey.subsubkey" )
	*/

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


function i_set_obj( $obj, $key, $value ){
	// Sets the value of an object,
	// even if it or it's parent(s) doesn't exist.
	
	/*	PARAMETERS:
		$obj 	= 	[array]
		$key 	= 	[string] ie. ( "key.subkey.subsubkey" )
		$value 	= 	[string/array/object]
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



function i_add_terms( $terms, $taxonomy ){
	// Adds a series of terms up to two levels of depth

	foreach( $terms as $term ){
	
		$term_exists = term_exists( $term['term'], $taxonomy );

		// Add top level terms
		if( !$term_exists ){
			// Insert the term
			$term_ids = wp_insert_term(
				$term['term'],
				$taxonomy,
				$term['meta']
				);
		} else{
			$term_ids = $term_exists;
		}

		// Add Child Terms
		if( isset( $term['children'] ) ){

			// Iterate through each child term
			foreach( $term['children'] as $child_term ){

				// Check if Terms Exists
				$term_exists = term_exists( $child_term['term'], $taxonomy );

				if( !$term_exists ){
					// Define the parent term
					$child_term['meta']['parent'] = $term_ids['term_id'];

					// Insert the term
					$child_term_ids = wp_insert_term(
						$child_term['term'],
						$taxonomy,
						$child_term['meta']
						);

				}

			}

		}

	}

}

function i_print_filters_for( $hook = '' ) {
    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
        return;

    print '<pre>';
    print_r( $wp_filter[$hook] );
    print '</pre>';
}

// Put this where you want to see the filters
//i_print_filters_for( 'the_content' );


?>