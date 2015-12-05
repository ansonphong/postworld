<?php

/**
 * Generates an outline of all the current modules.
 *
 * @return array An outline of the modules.
 */
function pw_registered_modules( $format = 'arrays' ){

	$modules = array(

		array(
			'name'			=>	'Site Options',
			'slug'			=>	'site',
			'icon'			=>	'pwi-globe',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Layouts',
			'slug'			=>	'layouts',
			'icon'			=>	'pwi-th-large',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Sidebars',
			'slug'	=>	'sidebars',
			'icon'	=>	'pwi-map',
			'description'	=>	'',
			),

		array(
			'name'			=>	'Styles',
			'slug'			=>	'styles',
			'icon'			=>	'pwi-brush',
			'description'	=>	'',
			),
		
		array(
			'name'	=>	'Social',
			'slug'	=>	'social',
			'icon'	=>	'pwi-heart',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Feeds',
			'slug'	=>	'feeds',
			'icon'	=>	'pwi-file',
			'description'	=>	'',
			),

		/*
		array(
			'name'	=>	'Term Feeds',
			'slug'	=>	'term-feeds',
			'icon'	=>	'pwi-tag',
			'description'	=>	'',
			),
		*/

		array(
			'name'	=>	'Backgrounds',
			'slug'	=>	'backgrounds',
			'icon'	=>	'pwi-paint-format',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Iconsets',
			'slug'	=>	'iconsets',
			'icon'	=>	'pwi-circle-medium',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Shortcodes',
			'slug'	=>	'shortcodes',
			'icon'	=>	'pwi-code',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Rank Score',
			'slug'	=>	'rank_score',
			'icon'	=>	'pwi-bars',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Post Cache',
			'slug'	=>	'post_cache',
			'icon'	=>	'pwi-cube',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Layout Cache',
			'slug'	=>	'layout_cache',
			'icon'	=>	'pwi-cubes',
			'description'	=>	'',
			),

		array(
			'name'	=>	'Devices',
			'slug'	=>	'devices',
			'icon'	=>	'pwi-mobile',
			'description'	=>	'Adds support for device detection.',
			),

		array(
			'name'	=>	'Colors',
			'slug'	=>	'colors',
			'icon'	=>	'pwi-droplet',
			'description'	=>	'Adds support for color processing.',
			),

		);

	// Apply filters so themes can override / add new modules
	// TODO : Impliment via a 'pw_register_module()' method
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

	// Get the saved enabled modules array
	// Filter must be set to false otherwise it triggers infinite recursion
	$enabled_modules = pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES, 'filter' => false ) );
	
	pw_log( 'enabled_modules', $enabled_modules );
	
	// Get the theme required modules
	$required_modules = pw_required_modules();

	// If the modules option hasn't been saved yet
	if( empty($enabled_modules) )
		// Enable the supported modules
		$enabled_modules = $required_modules;
	
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

/**
 * Generates an outline of all the current modules.
 *
 * @return array An outline of the modules.
 */
function pw_modules_outline(){
	$enabled_modules = pw_enabled_modules();
	$supported_modules = pw_supported_modules();
	$required_modules = pw_required_modules();

	return array(
		'enabled'	=>	$enabled_modules,
		'supported'	=>	$supported_modules,
		'required'	=>	$required_modules,
		);
}

/**
 * Returns a boolean whether or not the
 * specified module is currently enabled.
 *
 * @param string $module The slug of the specified module.
 * @return boolean Whether or not the module is enabled.
 */
function pw_module_enabled( $module ){
	$enabled_modules = pw_enabled_modules();
	return in_array( $module, $enabled_modules );
}

/**
 * Returns a boolean whether or not the
 * specified module is supported by the theme.
 *
 * @param string $module The slug of the specified module.
 * @return boolean Whether or not the module is supported.
 */
function pw_module_supported( $module ){
	$supported_modules = pw_supported_modules();
	return in_array( $module, $supported_modules );
}

/**
 * Returns a boolean whether or not the
 * specified module is required by the theme.
 *
 * @param string $module The slug of the specified module.
 * @return boolean Whether or not the module is required.
 */
function pw_module_required( $module ){
	$required_modules = pw_required_modules();
	return in_array( $module, $required_modules );
}



////////// DEPRECIATED //////////
function pw_module_is_enabled( $module ){
	// DEPRECIATED
	return pw_module_enabled( $module );
}
function pw_module_is_supported( $module ){
	// DEPRECIATED
	return pw_module_supported( $module );
}
function pw_module_is_required( $module ){
	// DEPRECIATED
	return pw_module_required( $module );
}

?>