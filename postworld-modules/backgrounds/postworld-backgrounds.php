<?php

function pw_current_background( $vars ){
	// Returns the background for the current context
	
	// Get Globals
	global $post;
	global $pw;

	// If backgrounds module is not enabled, return here
	if( !in_array( 'backgrounds', $pw['modules'] ) )
		return false;

	// Define the default passed-in vars
	$default_vars = array(
		'post_id'	=>	$post->ID,
		);

	// Override the default vars with those passed in
	$vars = array_replace_recursive( $default_vars, $vars );

	// Setup variables
	$background_id = false;
	$background = array();

	///// FROM : POSTMETA /////
	// If on a single post, check for post over-ride
	if( is_singular() ){
		// Get the postmeta
		$postmeta_background = pw_get_wp_postmeta( array(
			"post_id"	=>	$vars['post_id'],
			"meta_key" 	=>	PW_POSTMETA_KEY,
			"sub_key"	=>	'background',
			));

		// If the background postmeta is a string, and not 'default'
		if( is_string( $postmeta_background ) &&
			$postmeta_background !== 'default' )
			$background_id = $postmeta_background;
		else
			$background_id = false;
		
		//pw_log( "BACKGROUND ID FROM SINGLE : " . json_encode($postmeta_background) );

	}

	

	///// FROM : CONTEXT /////
	if( $background_id == false ){

		// Check for context
		$context = pw_current_context();
		$background_contexts = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUND_CONTEXTS ) );
		
		// Interate through each context
		foreach( $context as $c ){
			// Iterate through each background context
			foreach( $background_contexts as $c_key => $bg_id ){
				// If there's a matching background, keep the last match
				if( $c == $c_key )
					$background_id = $bg_id;
			}
		}
	}



	///// FROM : DEFAULT /////
	// If a background is not defined yet
	if( $background_id == false ){
		// Check for a default
		// If no default yet, then see if any backgrounds are defined, if so use the last one
	}



	///// GET BACKGROUND OBJECT /////
	// If a background ID has been found
	if( $background_id !== false && is_string( $background_id ) ){
		// Get the backgrounds
		$backgrounds = pw_get_option( array( 'option_name' => PW_OPTIONS_BACKGROUNDS ) );
		// If backgrounds are defined
		if( !empty( $backgrounds ) && is_array( $backgrounds ) ){
			// Iterate through each of the saved backgrounds
			foreach( $backgrounds as $background ){
				// If a background ID matches the set background ID
				if( $background['id'] == $background_id )
					// Return that background
					return $background;
			}
		}
	}

	// If nothing returned yet
	return false;

	//return $background;
	//array( 'context' => $context, 'background_contexts' => $background_contexts );

}


?>