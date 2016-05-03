<?php
/**
 * The default theme model.
 *
 * @since Postworld 1.602
 */
function pw_default_theme(){
	return array(
		'slug' => 'postworld',
		'version' => '0.0.0',
		);
}

/**
 * Registers a theme within Postworld.
 *
 * @since Postworld 1.602
 */
function pw_register_theme( $theme = array() ){
	// Set the default theme name
	$theme = array_replace( pw_default_theme(), $theme );
	// Add theme to the globals
	$GLOBALS['pw']['theme'] = $theme;
}

/**
 * Runs theme upgrades and updates current theme version in DB.
 */
add_action( 'after_setup_theme', 'pw_upgrade_theme' );
function pw_upgrade_theme(){

	$theme = pw_theme();

	// Generate the option_name for the theme version
	$option_name = pw_theme_version_option_name();

	// Get the previous version of the theme, the last time it was run
	$previous_version = get_option( $option_name, '0' );

	// Get the current version of the theme
	$current_version = $theme['version'];

	pw_log('PREVIOUS VERSION', $previous_version );
	pw_log('CURRENT VERSION', $current_version );

	// If the theme version has increased, run upgrades
	if( version_compare( $previous_version, $current_version ) === -1 ){

		pw_log('DO THEME UPGRADE');

		// Do action on theme upgrade
		$upgrade_action = $theme['slug'] . '_theme_upgrade';
		do_action( $upgrade_action, array(
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
	$theme = _get( $GLOBALS['pw'], 'theme' );
	if( $theme === false )
		$theme = pw_default_theme();
	return $theme;
}

/**
 * Gets the current registered theme slug.
 *
 * @since Postworld 1.602
 */
function pw_theme_slug(){
	return _get( pw_theme(), 'slug' );
}

/**
 * Gets the current registered theme version.
 *
 * @since Postworld 1.602
 */
function pw_theme_version(){
	return _get( pw_theme(), 'version' );
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