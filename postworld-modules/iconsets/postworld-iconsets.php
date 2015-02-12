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
	$iconsets = $pw['iconsets'];

	// Remove the SRC key
	$return_iconsets = array();
	foreach( $iconsets as $key => $value ){
		unset($value['src']); 
		$return_iconsets[$key] = $value;
	}

	return $return_iconsets;
}

function pw_register_iconset( $vars = array() ){
	/*	Registers an iconset.
	 *	@param $vars - An associative array with the following structure:
	 *	$vars = array(
			'name'		=>	'IcoMoon',
			'slug'		=>	'icomoon',
			'src'		=>	'http://...icomoon.css',
			'prefix'	=>	'icon-'
			)
	*/
	global $pw;

	// Check for string values	
	if( !is_string( $vars['name'] ) ||
		!is_string( $vars['slug'] ) ||
		!is_string( $vars['url'] ) ||
		!is_string( $vars['src'] ) ||
		!is_string( $vars['prefix'] ) )
		return false;

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
			'class'		=>	'',
			'url'		=>	POSTWORLD_URI  . '/lib/icomoon/style.css',
			'src'		=>	POSTWORLD_PATH . '/lib/icomoon/style.css',
			),
		array(
			'name'		=>	'Glyphicons Halflings',
			'slug'		=>	'glyphicons-halflings',
			'prefix'	=>	'glyphicon-',
			'class'		=>	'glyphicon',
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
	

	///// LOAD ICONSETS ///// 
	// Iterate through iconsets
	foreach( $load_iconsets as $slug => $iconset ){
		// Enqueue styles
		wp_enqueue_style( $slug, $iconset['url'] );
		// Load icon CSS classes
		$pw['iconsets'][$slug]['classes'] = pw_get_iconset_classes( $slug );
	}
	
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
		'src'		=>	null,		// Absolute System Directory Path to CSS file
		'return'	=>	'keys',		// 'all' / 'keys' / 'hash'
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