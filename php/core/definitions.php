<?php
/**
 * Setup core definitions
 */
// @todo - REFACTOR THIS FILE SO ALL VARIABLES USE THEME SLUG
// @todo - LOOK FOR ANY STRAY FILTER NAMES THAT NEED CONSTANTS
// @todo - DEVELOP THEME MIGRATIONS TO RENAME DB KEYS AND DATABASE TABLES
// @todo - IN THIS ORDER : OPTIONS, POSTMETA, TERMMETA, USERMETA, DB TABLES
add_action( POSTWORLD_CONFIG, 'postworld_definitions', 11 );
function postworld_definitions(){
	// Localize the registered theme slug
	$theme_slug = pw_theme_slug();

	/**
	 * Postworld Info
	 */
	$GLOBALS['pw']['info'] = array(
		'version'		=>	1.603,
		'db_version'	=>	1.29,
		'mode'	=>	pw_mode(),
		'slug'	=>	'postworld',
		);
	$GLOBALS['pw']['angular_modules'] = array();
	$GLOBALS['pw']['iconsets'] = array();

	/**
	 * @todo Prefix this with the theme slug.
	 */
	global $wpdb;
	$wpdb->pw_prefix = $wpdb->prefix . "postworld_";

	// Empty array for footer scripts to be held in
	$GLOBALS['pw_footer_scripts'] = array();

	// Define the Javascript App name
	define( 'POSTWORLD_APP', 		$theme_slug.'-app' );

	///// DEFINE MODEL FILTER NAMES /////
	define( 'PW_FIELD_MODELS', 		$theme_slug.'-model-fields' );
	define( 'PW_POST_FIELD_MODELS', $theme_slug.'-model-post-fields' );
	define( 'PW_USER_FIELD_MODELS', $theme_slug.'-model-user-fields' );

	define( 'PW_MODEL_STYLES', 		$theme_slug.'-model-styles' );
	define( 'PW_MODEL_BACKGROUNDS', $theme_slug.'-model-backgrounds' );

	define( 'PW_TERM_FEED', 		$theme_slug.'-term-feed-' );
	define( 'PW_FEED_DEFAULT', 		$theme_slug.'-feed-default' );
	define( 'PW_FEED_OVERRIDE', 	$theme_slug.'-feed-override' );
	define( 'PW_STYLES_DEFAULT', 	$theme_slug.'-style-defaults' );

	///// DEFINE META FILTER NAMES /////
	define( 'PW_POSTS', 	$theme_slug.'-posts' );
	define( 'PW_USERS', 	$theme_slug.'-users' );
	define( 'PW_POSTMETA', 	$theme_slug.'-postmeta' );
	define( 'PW_USERMETA', 	$theme_slug.'-usermeta' );
	define( 'PW_MODULES', 	$theme_slug.'-modules-filter' );

	///// DEFINE PRINT FILTERS /////
	define( 'PW_GLOBAL_OPTIONS',	$theme_slug.'-global-options' ); // Case in-sensitive

	///// VERSIONS /////
	define( 'PW_DB_VERSION', $theme_slug.'-db-version' );
	define( 'PW_VERSIONS', $theme_slug.'-versions' );

	/**
	 * Define the META keys.
	 * Used in 'wp_postmeta' and 'pw_termmeta' tables
	 */
	define( 'PW_POSTMETA_KEY',	$theme_slug.'_meta', 		true ); // Case in-sensitive
	define( 'PW_USERMETA_KEY',	$theme_slug.'_meta', 		true ); // Case in-sensitive
	define( 'PW_TAXMETA_KEY',	$theme_slug.'_meta', 		true ); // Case in-sensitive
	define( 'PW_AVATAR_KEY',	$theme_slug.'_avatar', 		true ); // Case in-sensitive
	define( 'PW_COLORS_KEY',	$theme_slug.'_colors', 		true ); // Case in-sensitive

	/**
	 * Define the OPTIONS keys.
	 * Used in 'wp_options' table as 'option_name' key
	 */
	define( 'PW_OPTIONS_CORE', 					$theme_slug.'-core' );
	define( 'PW_OPTIONS_MODULES', 				$theme_slug.'-modules' );
	define( 'PW_OPTIONS_SITE', 					$theme_slug.'-site' );
	define( 'PW_OPTIONS_STYLES', 				$theme_slug.'-styles' );
	define( 'PW_OPTIONS_LAYOUTS', 				$theme_slug.'-layouts' );
	define( 'PW_OPTIONS_SIDEBARS', 				$theme_slug.'-sidebars' );
	define( 'PW_OPTIONS_FEEDS', 				$theme_slug.'-feeds' );
	define( 'PW_OPTIONS_FEED_SETTINGS', 		$theme_slug.'-feed-settings' );
	define( 'PW_OPTIONS_SOCIAL', 				$theme_slug.'-social' );
	define( 'PW_OPTIONS_ICONSETS', 				$theme_slug.'-iconsets' );
	define( 'PW_OPTIONS_BACKGROUNDS', 			$theme_slug.'-backgrounds' );
	define( 'PW_OPTIONS_BACKGROUND_CONTEXTS',	$theme_slug.'-background-contexts' );
	define( 'PW_OPTIONS_SHORTCODES', 			$theme_slug.'-shortcodes' );
	define( 'PW_OPTIONS_SHORTCODE_SNIPPETS', 	$theme_slug.'-shortcode-snippets' );
	define( 'PW_OPTIONS_HEADER_CODE', 			$theme_slug.'-header-code' );
	define( 'PW_OPTIONS_DEFAULTS', 				$theme_slug.'-defaults' );
	define( 'PW_OPTIONS_COMMENTS', 				$theme_slug.'-comments' );
	define( 'PW_CACHE_ICONSET', 				$theme_slug.'-cache-iconset-' );
	define( 'PW_OPTIONS_THEME', 				$theme_slug.'-theme' );

}

