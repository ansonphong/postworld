<?php
/**
 * Setup core definitions
 */
add_action( 'postworld_config', 'postworld_definitions', 11 );
function postworld_definitions(){

	// Localize the registered theme slug
	$theme_slug = 'postworld';// pw_theme_slug();

	// Used for the core PW_Scripts grouping
	define( 'POSTWORLD_APP', $theme_slug.'-app' );

	// Empty array for footer scripts to be held in
	$GLOBALS['pw_footer_scripts'] = array();

	global $wpdb;
	$wpdb->pw_prefix = $wpdb->prefix . "postworld_";

	/**
	 * @todo Rename from 'pw' to 'postworld' for less chance of collisions.
	 */
	$GLOBALS['pw'] = array(
		'info'	=>	array(
			'version'		=>	1.602,
			'db_version'	=>	1.29,
			'mode'	=>	pw_mode(),
			'slug'	=>	'postworld',
			),
		'angularModules'	=>	array(),
		'vars'	=>	array(
			),

		'models'	=>	array(
			'fields'			=>	'postworld-model-fields',
			'post_fields'		=>	'postworld-model-post-fields',
			'user_fields'		=>	'postworld-model-user-fields',
			'styles'			=>	'postworld-model-styles',
			'backgrounds'		=>	'postworld-model-backgrounds',
			),
		'filters'	=>	array(
			'feed_default'		=>	'postworld-feed-default',
			'feed_override'		=>	'postworld-feed-override',
			'term_feed'			=>	'postworld-term-feed-',
			'style_default'		=>	'postworld-style-defaults'
			),

		'iconsets'	=>	array(),
		'fields' => array(
			'post'	=> array(),
			'user'	=>	array(),
			),

		);

	global $pw;

	///// DEFINE MODEL FILTER NAMES /////
	define( 'PW_FIELD_MODELS', 		$pw['models']['fields'] );
	define( 'PW_POST_FIELD_MODELS', $pw['models']['post_fields'] );
	define( 'PW_USER_FIELD_MODELS', $pw['models']['user_fields'] );

	define( 'PW_MODEL_STYLES', 		$pw['models']['styles'] );
	define( 'PW_MODEL_BACKGROUNDS', $pw['models']['backgrounds'] );

	define( 'PW_TERM_FEED', 		$pw['filters']['term_feed'] );
	define( 'PW_FEED_DEFAULT', 		$pw['filters']['feed_default'] );
	define( 'PW_FEED_OVERRIDE', 	$pw['filters']['feed_override'] );
	define( 'PW_STYLES_DEFAULT', 	$pw['filters']['style_default'] );

	///// DEFINE META FILTER NAMES /////
	define( 'PW_POSTS', 	'pw_posts' );
	define( 'PW_USERS', 	'pw_users' );
	define( 'PW_POSTMETA', 	'postworld-postmeta' );
	define( 'PW_USERMETA', 	'postworld-usermeta' );
	define( 'PW_MODULES', 	'postworld-modules-filter' );

	///// DEFINE PRINT FILTERS /////
	define( 'PW_GLOBAL_OPTIONS',	'postworld-global-options' ); // Case in-sensitive

	///// VERSIONS /////
	define( 'PW_DB_VERSION', 'postworld-db-version' );
	define( 'PW_VERSIONS', 'postworld-versions' );

	// MUST BE DEFINED BY THE THEME
	//define( 'PW_OPTIONS_STYLES', 	'postworld-styles-theme' );




	/**
	 * Define the META keys.
	 * Used in 'wp_postmeta' and 'pw_termmeta' tables
	 */
	define( 'PW_POSTMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
	define( 'PW_USERMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
	define( 'PW_TAXMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
	define( 'PW_AVATAR_KEY',	'pw_avatar', 	true ); // Case in-sensitive
	define( 'PW_COLORS_KEY',	'pw_colors', 	true ); // Case in-sensitive

	/**
	 * Define the OPTIONS keys.
	 * Used in 'wp_options' table as 'option_name' key
	 */
	define( 'PW_OPTIONS_CORE', 					'postworld-core' );
	define( 'PW_OPTIONS_MODULES', 				'postworld-modules' );
	define( 'PW_OPTIONS_SITE', 					'postworld-site' );
	define( 'PW_OPTIONS_LAYOUTS', 				'postworld-layouts' );
	define( 'PW_OPTIONS_SIDEBARS', 				'postworld-sidebars' );
	define( 'PW_OPTIONS_FEEDS', 				'postworld-feeds' );
	define( 'PW_OPTIONS_FEED_SETTINGS', 		'postworld-feed-settings' );
	define( 'PW_OPTIONS_SOCIAL', 				'postworld-social' );
	define( 'PW_OPTIONS_ICONSETS', 				'postworld-iconsets' );
	define( 'PW_OPTIONS_BACKGROUNDS', 			'postworld-backgrounds' );
	define( 'PW_OPTIONS_BACKGROUND_CONTEXTS', 	'postworld-background-contexts' );
	define( 'PW_OPTIONS_SHORTCODES', 			'postworld-shortcodes' );
	define( 'PW_OPTIONS_SHORTCODE_SNIPPETS', 	'postworld-shortcode-snippets' );
	define( 'PW_OPTIONS_HEADER_CODE', 			'postworld-header-code' );
	define( 'PW_OPTIONS_DEFAULTS', 				'postworld-defaults' );
	define( 'PW_OPTIONS_COMMENTS', 				'postworld-comments' );
	define( 'PW_CACHE_ICONSET', 				'postworld-cache-iconset-' );
}