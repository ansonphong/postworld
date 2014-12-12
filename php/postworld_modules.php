<?php

function pw_registered_modules( $format = 'arrays' ){

	$modules = array(

		array(
			'name'			=>	'Site Options',
			'slug'			=>	'site',
			'icon'			=>	'icon-globe',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Layouts',
			'slug'			=>	'layouts',
			'icon'			=>	'icon-th-large',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Sidebars',
			'slug'	=>	'sidebars',
			'icon'	=>	'icon-map',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Styles',
			'slug'			=>	'styles',
			'icon'			=>	'icon-brush',
			'description'	=>	'',
			),
		
		array(
			'name'	=>	'Social',
			'slug'	=>	'social',
			'icon'	=>	'icon-heart',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Feeds',
			'slug'	=>	'feeds',
			'icon'	=>	'icon-file',
			'description'	=>	'',
			),

		/*
		array(
			'name'	=>	'Term Feeds',
			'slug'	=>	'term-feeds',
			'icon'	=>	'icon-tag',
			'description'	=>	'',
			),
		*/

		array(
			'name'	=>	'Backgrounds',
			'slug'	=>	'backgrounds',
			'icon'	=>	'icon-paint-format',
			'description'	=>	'',
			),

		);

	// Apply filters so themes can override / add new modules
	//$modules = apply_filters( PW_MODULES, $modules );	

	// Just return the names of registered modules
	if( $format == 'names' ){
		$module_names = array();
		foreach( $modules as $module ){
			$module_names[] = _get( $module, 'slug' );
		}
		return $module_names;
	}

	return $modules;
}

function pw_available_modules( $format = 'arrays' ){
	// Return an array available modules
	// $format = [ 'arrays' / 'names' ]

	$registered_modules = pw_registered_modules( $format );
	$supported_modules = pw_supported_modules();
	$available_modules = array();

	foreach( $registered_modules as $module ){

		// If we're working an an array of modules, just use the slug
		if( $format == 'arrays' )
			$module_name = _get( $module, 'slug' );
		// Otherwise use the value itself as the name
		else if( $format == 'names' )
			$module_name = $module;

		// If the module is supported
		if( in_array( $module_name, $supported_modules ) )
			// Add it to available modules
			$available_modules[] = $module;
	}

	return $available_modules;

}

function pw_enabled_modules(){
	// Returns an array of the names of the enabled modules

	global $pwSiteGlobals;

	if( empty($enabled_modules) )
		// Get the saved enabled modules array
		// Filter must be set to false otherwise it triggers infinite recursion
		$enabled_modules = pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES, 'filter' => false ) );
	
	// If the modules option hasn't been saved yet
	if( !get_option( PW_OPTIONS_MODULES ) )
		// Enable the supported modules
		$enabled_modules = pw_supported_modules();
	
	// Get the theme required modules
	$required_modules = pw_required_modules();

	// If there are required modules
	if( !empty( $required_modules ) ){
		// Merge the required and enabled modules
		// Forcing required modules to be enabled
		$enabled_modules = array_merge( $enabled_modules, $required_modules );
	
		// Remove duplicates if any
		$enabled_modules = array_unique( $enabled_modules );
	}

	return $enabled_modules;

}
//add_filter( PW_OPTIONS_MODULES, 'pw_enabled_modules' );


function pw_supported_modules(){
	// Returns an array of the names of the theme supported modules

	global $pwSiteGlobals;
	$supported_modules = _get( $pwSiteGlobals, 'modules.supported' );

	if( !$supported_modules )
		$supported_modules = pw_registered_modules('names');

	return $supported_modules;
}

function pw_required_modules(){
	// Returns an array of the names of the theme supported modules

	global $pwSiteGlobals;
	$required_modules = _get( $pwSiteGlobals, 'modules.required' );

	if( !$required_modules )
		$required_modules = array();

	return $required_modules;
}


function pw_set_modules(){


}


?>