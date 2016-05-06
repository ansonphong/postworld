<?php
/*___                         _       
 |_ _|___ ___  _ __  ___  ___| |_ ___ 
  | |/ __/ _ \| '_ \/ __|/ _ \ __/ __|
  | | (_| (_) | | | \__ \  __/ |_\__ \
 |___\___\___/|_| |_|___/\___|\__|___/

///////////// ICONSETS /////////////*/

function pw_get_iconset( $slug = '' ){
	// Return an iconset metadata, includning classes
	// @param $slug [string]
	//global $pw;
}

function pw_get_iconsets( $slugs = array() ){
	// Return an iconset metadata, includning classes
	// @param $slug [string]

	if( !pw_module_is_enabled('iconsets') )
		return false;

	global $pw;

	// Get all of the registered iconsets
	$iconsets = $pw['iconsets'];

	// Empty array
	$return_iconsets = array();

	// Iterate through each of the registered iconsets
	foreach( $iconsets as $key => $value ){
		// If the iconset is enabled
		if( pw_iconset_is_enabled( $key ) ){
			// Remove the SRC key
			unset($value['src']); 
			// Transfer the iconset
			$return_iconsets[$key] = $value;
		}
	}

	return $return_iconsets;
}

function pw_register_iconset( $vars = array() ){
	/*	Registers an iconset.
	 *	@param $vars - An associative array with the following structure:
	 */
	global $pw;

	// Check for string values	
	if( !is_string( $vars['name'] ) ||
		!is_string( $vars['slug'] ) ||
		!is_string( $vars['url'] ) ||
		!is_string( $vars['src'] ) ||
		!is_string( $vars['prefix'] ) )
		return false;

	$defaultVars = array(
		'name'		=>	null,		// String
		'slug'		=>	null,		// String
		'prefix'	=>	'icon-',	// The icon class prefix
		'add_class'	=>	'',			// Additional required class
		'url'		=>	null,		// URL to .css file
		'src'		=>	null,		// System path to .css file 
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	// Add to Postworld Globals
	$pw['iconsets'][ $vars['slug'] ] = $vars;

	return true;
}

function pw_get_registered_iconsets(){
	global $pw;
	return $pw['iconsets'];
}

function pw_register_core_iconsets(){
	// Defines and registers the Postworld Core iconsets

	if( !pw_module_is_enabled('iconsets') )
		return false;

	$iconsets = array(
		array(
			'name'		=>	'Postworld Icons',
			'slug'		=>	'postworld-icons',
			'prefix'	=>	'pwi-',
			'add_class'	=>	'',
			'url'		=>	POSTWORLD_URI  . '/lib/Postworld-Icons/style.css',
			'src'		=>	POSTWORLD_PATH . '/lib/Postworld-Icons/style.css',
			),
		array(
			'name'		=>	'Nature',
			'slug'		=>	'nature',
			'prefix'	=>	'ni-',
			'add_class'	=>	'',
			'url'		=>	POSTWORLD_URI  . '/lib/Nature-Icons/style.css',
			'src'		=>	POSTWORLD_PATH . '/lib/Nature-Icons/style.css',
			),
		array(
			'name'		=>	'Far East',
			'slug'		=>	'far-east',
			'prefix'	=>	'fe-',
			'add_class'	=>	'',
			'url'		=>	POSTWORLD_URI  . '/lib/Far-East-Icons/style.css',
			'src'		=>	POSTWORLD_PATH . '/lib/Far-East-Icons/style.css',
			),
		array(
			'name'		=>	'Glyphicons Halflings',
			'slug'		=>	'glyphicons-halflings',
			'prefix'	=>	'glyphicon-',
			'add_class'	=>	'glyphicon',
			'url'		=>	POSTWORLD_URI  . '/lib/glyphicons/glyphicons-halflings.css',
			'src'		=>	POSTWORLD_PATH . '/lib/glyphicons/glyphicons-halflings.css',
			),
		);

	// Filter for themes to modify core iconsets
	$iconsets = apply_filters( 'pw_core_iconsets', $iconsets );

	foreach( $iconsets as $iconset ){
		pw_register_iconset( $iconset );
	}

}
add_action( 'init', 'pw_register_core_iconsets' );

function pw_load_iconsets( $iconsets = array(), $register_classes = true ){
	/* 	An array of strings, indicating the slugs of the iconsets to load
	 *  To be executed on the 'wp_enqueue_scripts' or 'admin_enqueue_scripts' hook
	 * 	@param $iconsets = array( 'icomoon', 'glyphicons-halflings' );
	 */

	if( !pw_module_is_enabled('iconsets') )
		return false;

	global $pw;

	///// SPECIFY ICONSETS ///// 
	// If no iconsets specified
	if( empty( $iconsets ) ){
		// Load all registered iconsets
		$load_iconsets = $pw['iconsets'];
	}
	// If an array of iconsets is provided
	elseif( is_array( $iconsets ) ) {
		// Remove any duplicates
		$iconsets = array_unique($iconsets);
		// Init empty array
		$load_iconsets = array();
		// Search through all registered iconsets
		foreach( $iconsets as $slug ){
			// And find where specified slug matches the key
			$load_iconset = _get( $pw['iconsets'], $slug );
			// If it's an array (will return false otherwise)
			if( is_array( $load_iconset ) )
				// Add it to the array of iconsets to load
				$load_iconsets[] = $load_iconset;
		}
	}
	else
		return false;
	
	// Apply filters
	$load_iconsets = apply_filters( 'pw_load_iconsets', $load_iconsets );

	/**
	 * Silently load the following iconsets.
	 * Silently loaded iconsets are basically like required iconsets
	 * Which load un-impeding the options. This is used for loading
	 * Registered system icon fonts in the admin, for instance.
	 * 
	 * @param Array An array of iconset slugs to silently load.
	 */
	$silent_load = apply_filters( 'pw_silent_load_iconsets', array() );

	///// LOAD ICONSETS ///// 
	// Iterate through iconsets
	foreach( $load_iconsets as $slug => $iconset ){
		// Only load those iconsets which are selected in pw-config
		// Or saved in iconsets PW_OPTIONS_ICONSETS
		// Or ones which are filtered as silently loaded
		if( !pw_iconset_is_enabled( $slug ) &&
			!in_array( $slug, $silent_load ) )
			continue;
		// Enqueue styles
		wp_enqueue_style( $slug, $iconset['url'] );
		// Load icon CSS classes
		$pw['iconsets'][$slug]['classes'] = pw_get_iconset_classes( $slug );
	}
	
}

/**
 * Silently load Postworld Icons in the admin screens.
 */
add_filter( 'pw_silent_load_iconsets', 'pw_admin_silent_load_iconsets' );
function pw_admin_silent_load_iconsets($silent_load){
	if( !is_admin() )
		return 	$silent_load;
	
	if( !in_array( 'postworld-icons', $silent_load ) )
		$silent_load[] = 'postworld-icons';

	if( !in_array( 'glyphicons-halflings', $silent_load ) )
		$silent_load[] = 'glyphicons-halflings';

	return $silent_load;
}

function pw_get_required_iconsets(){
	// Get required iconsets from Postworld Config
	$required_iconsets = pw_module_config( 'iconsets.required' );

	// If it's not defined, set an empty array
	if( $required_iconsets === false )
		$required_iconsets = array();

	$required_iconsets = apply_filters( 'pw_required_iconsets', $required_iconsets );

	return $required_iconsets;
}

/**
 * Returns whether or not the iconset is currently enabled.
 *
 * @param String $iconset_slug The slug of the iconset to check.
 * @return Boolean
 */
function pw_iconset_is_enabled( $iconset_slug ){
	$iconsetOptions = pw_get_option( array( 'option_name' => PW_OPTIONS_ICONSETS ) );
	$enabled_iconsets = $iconsetOptions['enabled'];
	return in_array( $iconset_slug, $enabled_iconsets );
}


function pw_filter_options_iconsets( $options ){
	// Filters options for PW_OPTIONS_ICONSETS
	// Get required iconsets from theme postworld config
	$required_iconsets = pw_get_required_iconsets();
	// Set default options
	$defaultOptions = array(
		'enabled'	=>	$required_iconsets,
		);
	$options = array_replace_recursive( $defaultOptions, $options );
	// Force on enabled iconsets
	foreach( $required_iconsets as $required_iconset ){
		// If the required iconset is not enabled
		if( !in_array( $required_iconset, $options['enabled'] ) )
			// Add it to enabled iconsets
			$options['enabled'][] = $required_iconset;
	}
	return $options;
}
add_filter( PW_OPTIONS_ICONSETS, 'pw_filter_options_iconsets' );


function pw_get_iconset_classes( $iconset_slug ){
	// An all the classes within an iconset

	//pw_set_microtimer('pw_get_iconset_classes-'.$iconset_slug);

	if( !pw_module_is_enabled('iconsets') )
		return false;

	global $pw;

	// Get the iconset data from registered iconsets
	$iconset = _get( $pw['iconsets'], $iconset_slug );

	// If it's empty, return
	if( empty($iconset) )
		return false;


	///// CACHING LAYER /////
	// Get the hash of the iconset
	$iconset_hash = pw_file_hash( $iconset['src'], 'sha256' );
	$get_cache = pw_get_cache( array( 'cache_hash' => $iconset_hash ) );
	if( !empty( $get_cache ) ){
		//pw_log_microtimer( 'pw_get_iconset_classes-'.$iconset_slug, 'CACHED' );
		return json_decode( $get_cache['cache_content'], true);
	}


	///// PROCESS CLASSES /////
	// If the icon cache doesn't exist or the hash is different
	// Get the iconset classes as an array
	$iconset_classes = pw_get_css_icon_classes( array(
		'prefix'	=>	$iconset['prefix'],
		'src'		=>	$iconset['src'],
		'add_class'	=>	$iconset['add_class'],
		'return'	=>	'classes',
		));


	///// CACHING LAYER /////
	pw_set_cache( array(
		'cache_type'	=>	'iconset',
		'cache_hash' 	=> 	$iconset_hash,
		'cache_content'	=>	json_encode($iconset_classes),
		));


	//pw_log_microtimer( 'pw_get_iconset_classes-'.$iconset_slug, 'NOT CACHED' );

	// Return the classes
	return $iconset_classes;

}


function pw_get_css_icon_classes( $vars = array() ){
	/*
	 * Returns an Array of all the icon css classes in a given css file
	 */
	$default_vars = array(
		'prefix'	=>	'icon-',	// The CSS classes prefix
		'add_class'	=>	'',			// Additional classes
		'src'		=>	null,		// Absolute System Directory Path to CSS file
		'return'	=>	'keys',		// 'all' / 'classes' / 'keys'
		'output'	=>	'array',
		);

	$vars = array_replace_recursive( $default_vars, $vars );

	// If no source or slug provided
	if( empty( $vars['src'] ) )
		return false;

	// Get the file
	$file_contents = file_get_contents( $vars['src'] );
	
	///// PROCESS FILE /////
	$pattern = '/\.('.$vars['prefix'].'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
	preg_match_all( $pattern, $file_contents, $matches, PREG_SET_ORDER );

	$icons = array();
	foreach($matches as $match){
	    $icons[ $match[1] ] = $match[2];
	}

	///// EXTRACT KEYS /////
	if( $vars['return'] == 'keys' || $vars['return'] == 'classes' ){
		$icon_keys = array();

		foreach( $icons as $key => $value ){

			if($vars['return'] == 'classes' && !empty($vars['add_class']) )
				$icon_keys[] = $vars['add_class'] . ' ' . $key;
			else
				$icon_keys[] = $key;
		}

		$icons = $icon_keys;
	}

	///// OUTPUT : STRING /////
	if( $vars['output'] === "string" ){
		// Enable this to generate string for icon Array
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
	}

	return $icons;

}

?>