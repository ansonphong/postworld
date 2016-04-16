<?php

add_action( 'customize_update_postworld', 'pw_customize_update', 10, 2 );
function pw_customize_update( $value, $setting ){

	pw_log( 'pw_customize_update : value', $value );
	pw_log( 'pw_customize_update : setting', $setting );

}

/**
 * Filters options being read from the database so that they can be
 * Previewed live with the WordPress native theme customizer.
 *
 * Setting IDs must match the following pattern : PW_OPTIONS_THEME[search.show_search]
 * Where 'PW_OPTIONS_THEME' is a defined constant option name in the wp_options table
 * And 'search.show_search' is a subkey within that array
 */
add_action( 'customize_preview_postworld', 'pw_customize_preview', 10, 2 );
function pw_customize_preview( $setting ){
	pw_log( 'pw_customize_preview : setting -> id', $setting->id );
	
	/**
	 * Get and sanitize the value
	 */
	$setting_value = $setting->post_value();
	//pw_log( 'pw_customize_preview : post_value', $setting->post_value() );
	//pw_log( 'pw_customize_preview : value', $setting->value() );
	//$setting_value = pw_typecast_if_boolean( $setting_value );
	//pw_log( 'pw_customize_preview : setting_value', $setting_value );

	if( $setting_value === null )
		return false;

	// Extract the option definition from the setting ID
	$option_definition = pw_extract_option_definition( $setting->id );
	//pw_log( 'pw_customize_preview : option_definition', $option_definition );
	if($option_definition === false)
		return false;

	/**
	 * Filter the value incoming via pw_get_option
	 * @see pw_get_option()
	 */
	add_filter( $option_definition['option_name'], function( $value ) use ( $option_definition, $setting_value ){
		$value = _set( $value, $option_definition['key'], $setting_value );
		return $value;
	});

}


/**
 * Split a setting ID in the form of a string, into an array.
 * @see pw_get_option(), pw_set_option()
 * 
 * @param string $string Example: PW_OPTIONS_THEME[search.show_search]
 * @return array An array of extracted values, for example:
 *	array(
 		'option_definition' => 'PW_OPTIONS_THEME',
 		'option_name' => 'postworld-theme-artdroid',
 		'key' => 'search.show_search'
 		)
 */
function pw_extract_option_definition( $string ){
	
	$parts = explode( '[', $string );
	if( count($parts) > 1 ){
		// Get the first part, which is the option definition, ie. 'PW_OPTIONS_THEME'
		$option_definition = $parts[0];
		if( !defined( $option_definition ) )
			return false;

		// Get the option subkey, ie. 'search.show_search'
		preg_match("/\[(.*?)\]/", $string, $matches);
		if( count( $matches ) == 0 )
			return false;
		$option_subkey = $matches[1];
	}

	return array(
		'option_definition'	=> $option_definition,
		'option_name'		=> constant($option_definition),
		'key'				=> $option_subkey
		);

}


