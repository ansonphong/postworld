<?php
// If site options are enabled
if( pw_module_enabled('site') )
	// Run early on init action hook
	add_action('after_setup_theme', 'pw_init_site_options', 5);

/**
 * Setup / configure site options based on settings
 */
function pw_init_site_options(){
	$options = pw_get_option( array( 'option_name' => PW_OPTIONS_SITE) );

	// Cache site options
	global $pw;
	$pw['site_options'] = $options;

	/**
	 * REQUIRE LOGIN
	 */
	if( _get( $options, 'security.require_login' ) ){
		if( !defined( 'PW_REQUIRE_LOGIN' ) )
			define( 'PW_REQUIRE_LOGIN', true );
	}

	/**
	 * DISABLE XMLRPC
	 */
	if( _get( $options, 'security.disable_xmlrpc' ) ){
		//Prevent this site from being a host for XML-RPC DDoS Attacks
		add_filter( 'xmlrpc_methods', function( $methods ) {
			unset( $methods['pingback.ping'] );
			return $methods;
		});
		// Disable XML-RPC to prevent from being gateway for incoming DDoS Attacks
		add_filter('xmlrpc_enabled', '__return_false');
	}

	/**
	 *  MEMORY LIMITS
	 */
	// Image Memory limit
	$image_memory_limit = _get( $options, 'memory.image_memory_limit' );
	if( !empty( $image_memory_limit ) )
		add_filter( 'image_memory_limit', 'pw_set_image_memory_limit' );
	
	/**
	 * DISABLE EMOJIS
	 * For some reason, emojis were added to the WordPress Core
	 * This adds to load time and is never used.
	 */
	if( _get( $options, 'wp_core.disable_wp_emojicons' ) ){
		// All actions related to emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		// Filter to remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', 'pw_disable_emojicons_tinymce' );
	}

	/**
	 * ENABLE MEDIA TRASH
	 */
	if( _get( $options, 'wp_core.media_trash' ) )
		if( !defined('MEDIA_TRASH') )
			define('MEDIA_TRASH', true);

	/**
	 * ENABLE DEVELOPMENT MODE
	 */
	if( _get( $options, 'postworld.mode' ) === 'dev' )
		if( !defined('POSTWORLD_MODE') )
			define('POSTWORLD_MODE', 'dev');

	return;

}


/**
 * Set the image memory limit based on preset site options.
 */
function pw_set_image_memory_limit( $limit ){
	global $pw;
	$options = $pw['site_options'];
	$image_memory_limit = _get( $options, 'memory.image_memory_limit' );
	//pw_log('image_memory_limit', $image_memory_limit);
	return $image_memory_limit;
}


/**
 * Disable Emojicons in TinyMCE
 * @note For use with 'tiny_mce_plugins' filter.
 */
function pw_disable_emojicons_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}
