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

function pw_register_core_iconsets(){
	// Defines and registers the Postworld Core iconsets

	if( !pw_module_is_enabled('iconsets') )
		return false;

	$iconsets = array(
		array(
			'name'		=>	'IcoMoon',
			'slug'		=>	'icomoon',
			'prefix'	=>	'icon-',
			'add_class'	=>	'',
			'url'		=>	POSTWORLD_URI  . '/lib/icomoon/style.css',
			'src'		=>	POSTWORLD_PATH . '/lib/icomoon/style.css',
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

	///// LOAD ICONSETS ///// 
	// Iterate through iconsets
	foreach( $load_iconsets as $slug => $iconset ){

		// Only load those iconsets which are selected in pw-config
		// Or saved in iconsets PW_OPTIONS_ICONSETS
		if( !pw_iconset_is_enabled( $slug ) )
			continue;

		// Enqueue styles
		wp_enqueue_style( $slug, $iconset['url'] );
		// Load icon CSS classes
		$pw['iconsets'][$slug]['classes'] = pw_get_iconset_classes( $slug );
	}
	
}

function pw_iconset_is_enabled( $iconset_slug ){
	// Returns which iconsets are enabled
	global $pwSiteGlobals;
	$required_iconsets = _get( $pwSiteGlobals, 'iconsets.required' );

	$enabled_iconsets = $required_iconsets;

	return in_array( $iconset_slug, $enabled_iconsets );

}


function pw_get_iconset_classes( $iconset_slug ){
	// An all the classes within an iconset
	
	if( !pw_module_is_enabled('iconsets') )
		return false;

	global $pw;

	// Get the iconset data from registered iconsets
	$iconset = _get( $pw['iconsets'], $iconset_slug );

	// If it's empty, return
	if( empty($iconset) )
		return false;

	///// CACHING LAYER /////
	// Get the option cache of the iconset
	// If the icon cache exists it will look like :
	// { 'hash':'asdf987sd', 'classes':['icon-tag','icon-circle'] }
	$cache = get_option( PW_CACHE_ICONSET . $iconset['slug'], false );

	// If there's a value
	if( !empty( $cache ) )
		// Assume it's JSON, and decode
		$cache = json_decode( $cache, true );


	///// GET THE ICONSET HASH /////
	// Get the hash of the iconset
	$iconset_hash = pw_file_hash( $iconset['src'], 'sha256' );

	// Compare iconset cache to the cached hash
	if( $iconset_hash == _get( $cache, 'hash' ) ){
		// If the hash is the same, return the classes from the cached
		return $cache['classes'];
	}

	///// PROCESS CLASSES /////
	// If the icon cache doesn't exist or the hash is different
	// Get the iconset classes  as an array
	$iconset_classes = pw_get_css_icon_classes( array(
		'prefix'	=>	$iconset['prefix'],
		'src'		=>	$iconset['src'],
		'add_class'	=>	$iconset['add_class'],
		'return'	=>	'classes',
		));

	///// CACHING LAYER /////
	// Setup the new cache object
	$cache = array(
		'hash'		=>	$iconset_hash,
		'classes'	=>	$iconset_classes,
		);

	// Update the cache
	$update_cache = update_option( PW_CACHE_ICONSET . $iconset['slug'], json_encode( $cache ) );

	// Return the classes
	return $iconset_classes;

}


function pw_get_css_icon_classes( $vars = array() ){
	/*
	 * Returns an Array of all the icon css classes in a given css file
	 */
	$defaultVars = array(
		'prefix'	=>	'icon-',	// The CSS classes prefix
		'add_class'	=>	'',			// Additional classes
		'src'		=>	null,		// Absolute System Directory Path to CSS file
		'return'	=>	'keys',		// 'all' / 'classes' / 'keys'
		'output'	=>	'array',
		);

	$vars = array_replace_recursive( $defaultVars, $vars );

	// If no source or slug provided
	if( empty( $vars['src'] ) )
		return false;

	// Get the file
	$file_contents = file_get_contents( $vars['src'] );
	
	///// PROCESS FILE /////
	$pattern = '/\.('.$icon_prefix.'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
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
	if( $output == "string" ){
		// Enable this to generate string for icon Array
		$icons = var_export($icons, TRUE);
		$icons = stripslashes($icons);
	}

	return $icons;

}

?>