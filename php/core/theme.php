<?php
/**
 * Registers a theme within Postworld.
 * @since 1.602
 */
add_action( 'after_setup_theme', 'pw_register_theme', 2 );
function pw_register_theme(){
	$default_theme = array(
		'slug' => 'postworld',
		'version' => '0.0.0',
		);
	$GLOBALS['pw']['theme'] = apply_filters( 'pw_register_theme', $default_theme );
}

/**
 * Gets the current registered theme.
 */
function pw_theme(){
	return _get( $GLOBALS['pw'], 'theme' );
}

/**
 * Gets the current registered theme slug.
 */
function pw_theme_slug(){
	return _get( $GLOBALS['pw'], 'theme.slug' );
}

/**
 * Gets the current registered theme version.
 */
function pw_theme_version(){
	return _get( $GLOBALS['pw'], 'theme.version' );
}

/**
 * Gets the current site version, including current version
 * of Postworld as well as the current version of WordPress
 * also filterable so the current theme version can be added.
 *
 * @return string The site version.
 */
function pw_site_version(){
	global $pw;
	$versions = array(
		'wordpress' => $GLOBALS['wp_version'],
		'postworld' => $GLOBALS['pw']['info']['version'],
		'theme' => pw_theme_version(),
		);

	// Apply filters so that the theme and plugins can add their versions
	$versions = apply_filters( PW_VERSIONS, $versions );

	// Generate a string with the versions
	$version_string = '';
	$i = 0;
	foreach( $versions as $key => $value ){
		if( $i > 0 )
			$version_string .= '--';
		$version_string .= $key . '-' . $value;
		$i++;
	}

	return $version_string; 
}