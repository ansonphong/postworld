<?php

function pw_current_background( $vars ){
	// Returns the background for the current context
	
	// Get Globals
	global $post;
	global $pw;

	if( !in_array( 'backgrounds', $pw['modules'] ) )
		return false;

	// Setup our variables
	$background_id = "";
	$background = array();

	// Define the default passed-in vars
	$default_vars = array(
		'post_id'	=>	$post->ID,
		);

	// Override the default vars with those passed in
	$vars = array_replace_recursive( $default_vars, $vars );

	// If on a single post, check for post over-ride
	if( is_single() ){

		// Get the postmeta

		// Check for the background key

		// If it's not 'default' / 'null' or otherwise empty, set it

		$background_id = 0;

	}

	// Otherwise, check for context
	else{

		// Check for context
		$context = pw_current_context();
		$background_contexts = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUND_CONTEXTS ) );
		
		// Interate through each context, and see if there's a matching background
		foreach( $context as $c ){
			// Keep the last match

		}

	}

	// If a background is not defined yet
	if( !isset( $background ) || empty( $background ) ){
		// Check for a default

		// If no default yet, then see if any backgrounds are defined, if so use the last one

	}

	return array( 'context' => $context, 'background_contexts' => $background_contexts );

}


?>