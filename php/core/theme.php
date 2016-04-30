<?php
/**
 * Registers a theme within Postworld.
 *
 * @since Postworld 1.602
 */
add_action( 'after_setup_theme', 'pw_register_theme', 2 );
function pw_register_theme(){

	// Set the default theme name
	$default_theme = array(
		'slug' => 'postworld',
		'version' => '0.0.0',
		);
	
	// Allow theme to register itself and add theme to the globals
	$theme = apply_filters( 'pw_register_theme', $default_theme );
	$GLOBALS['pw']['theme'] = $theme;

	// Generate the option_name for the theme version
	$option_name = pw_theme_version_option_name();

	// Get the previous version of the theme, the last time it was run
	$previous_version = get_option( $option_name, '0' );

	// Get the current version of the theme
	$current_version = $theme['version'];

	// If the theme version has increased, run upgrades
	if( version_compare( $previous_version, $current_version ) === -1 ){
		// Do action on theme upgrade
		do_action( $theme['slug'] . '_theme_upgrade', array(
			'previous_version' => $previous_version,
			'current_version' => $current_version,
			));

		// Update the database to the current version of Artdroid
		update_option( $option_name, $current_version );

	}

}


/**
 * Gets the current registered theme.
 *
 * @since Postworld 1.602
 */
function pw_theme(){
	return _get( $GLOBALS['pw'], 'theme' );
}

/**
 * Gets the current registered theme slug.
 *
 * @since Postworld 1.602
 */
function pw_theme_slug(){
	return _get( $GLOBALS['pw'], 'theme.slug' );
}

/**
 * Gets the current registered theme version.
 *
 * @since Postworld 1.602
 */
function pw_theme_version(){
	return _get( $GLOBALS['pw'], 'theme.version' );
}

/**
 * Generate the option name under which the
 * Current theme version is stored
 *
 * @since Postworld 1.602
 */
function pw_theme_version_option_name(){
	return pw_theme_slug() . '-' . 'theme-version';
}

/**
 * Gets the current site version, including current version
 * of Postworld as well as the current version of WordPress
 * also filterable so the current theme version can be added.
 *
 * @since Postworld 1.602
 *
 * @return string The site version.
 */
function pw_site_version(){
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