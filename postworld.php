<?php
/******************************************
Plugin Name: Postworld
Plugin URI: htp://phong.com/
Description: Wordpress API extension, with AngularJS client-side framework, LESS support, and standard libraries for developers to display posts in creative ways
Version: 1.3
Author: phong
Author URI: http://phong.com
License: GPL2
******************************************/
//if( !defined( 'POSTWORLD_DIR' ) )
define( 'POSTWORLD', true );
define( 'POSTWORLD_DIR', dirname(__FILE__) );
define( 'POSTWORLD_PATH', POSTWORLD_DIR );

global $wpdb;
$wpdb->pw_prefix = $wpdb->prefix . "postworld_";

function pw_mode(){
	return ( defined('POSTWORLD_MODE') ) ?
		POSTWORLD_MODE : 'deploy';
}

global $pw;
global $pwSiteGlobals;
$pw = array(
	'config' => $pwSiteGlobals,
	'info'	=>	array(
		'version'		=>	1.46,
		'db_version'	=>	1.28,
		'mode'	=>	pw_mode(),
		'slug'	=>	'postworld',
		),
	'angularModules'	=>	array(),
	'vars'	=>	array(
		),

	'db' =>	array(
		'wp_options'	=>	array(
			'option_name'	=>	array(
				'modules'				=>	'postworld-modules',
				'site'					=>	'postworld-site',
				'layouts'				=>	'postworld-layouts',
				'sidebars'				=>	'postworld-sidebars',
				'feeds'					=>	'postworld-feeds',
				'term_feeds'			=>	'postworld-term-feeds',
				'feed_settings'			=>	'postworld-feed-settings',
				'social'				=>	'postworld-social',
				'backgrounds'			=>	'postworld-backgrounds',
				'background_contexts'	=>	'postworld-background-contexts',
				'shortcodes'			=>	'postworld-shortcodes',
				'shortcode_snippets'	=>	'postworld-shortcode-snippets',
				'header_code'			=>	'postworld-header-code',
				'iconsets'				=>	'postworld-iconsets',
				'cache_iconset'			=>	'postworld-cache-iconset-',
				'defaults'				=>	'postworld-defaults',
				'comments'				=>	'postworld-comments',
				),
			),
		'wp_postmeta'	=>	array(
			'pw_meta'	=>	'postworld-postmeta',
			),
		'wp_usermeta'	=>	array(
			'pw_meta'	=>	'postworld-usermeta',
			),
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


///// DEFINE OPTION NAMES /////
// Used in 'wp_options' table as 'option_name' key
define( 'PW_OPTIONS_MODULES', 				$pw['db']['wp_options']['option_name']['modules'] );
define( 'PW_OPTIONS_SITE', 					$pw['db']['wp_options']['option_name']['site'] );
define( 'PW_OPTIONS_LAYOUTS', 				$pw['db']['wp_options']['option_name']['layouts'] );
define( 'PW_OPTIONS_SIDEBARS', 				$pw['db']['wp_options']['option_name']['sidebars'] );
define( 'PW_OPTIONS_FEEDS', 				$pw['db']['wp_options']['option_name']['feeds'] );
define( 'PW_OPTIONS_FEED_SETTINGS', 		$pw['db']['wp_options']['option_name']['feed_settings'] );
//define( 'PW_OPTIONS_TERM_FEEDS', 			$pw['db']['wp_options']['option_name']['term_feeds'] );
define( 'PW_OPTIONS_SOCIAL', 				$pw['db']['wp_options']['option_name']['social'] );
define( 'PW_OPTIONS_ICONSETS', 				$pw['db']['wp_options']['option_name']['iconsets'] );
define( 'PW_OPTIONS_BACKGROUNDS', 			$pw['db']['wp_options']['option_name']['backgrounds'] );
define( 'PW_OPTIONS_BACKGROUND_CONTEXTS', 	$pw['db']['wp_options']['option_name']['background_contexts'] );
define( 'PW_OPTIONS_SHORTCODES', 			$pw['db']['wp_options']['option_name']['shortcodes'] );
define( 'PW_OPTIONS_SHORTCODE_SNIPPETS', 	$pw['db']['wp_options']['option_name']['shortcode_snippets'] );
define( 'PW_OPTIONS_HEADER_CODE', 	$pw['db']['wp_options']['option_name']['header_code'] );
define( 'PW_OPTIONS_DEFAULTS', 				$pw['db']['wp_options']['option_name']['defaults'] );
define( 'PW_OPTIONS_COMMENTS', 				$pw['db']['wp_options']['option_name']['comments'] );



///// DEFINE OPTION CACHES /////
define( 'PW_CACHE_ICONSET', 	$pw['db']['wp_options']['option_name']['cache_iconset'] );

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
define( 'PW_POSTMETA', 	$pw['db']['wp_postmeta']['pw_meta'] );
define( 'PW_USERMETA', 	$pw['db']['wp_usermeta']['pw_meta'] );
define( 'PW_MODULES', 	$pw['db']['wp_options']['option_name']['modules'] );

///// DEFINE META KEYS /////
define( 'PW_POSTMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
define( 'PW_USERMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
define( 'PW_TAXMETA_KEY',	'pw_meta', 		true ); // Case in-sensitive
define( 'PW_AVATAR_KEY',	'pw_avatar', 	true ); // Case in-sensitive
define( 'PW_COLORS_KEY',	'pw_colors', 	true ); // Case in-sensitive

///// DEFINE PRINT FILTERS /////
define( 'PW_GLOBAL_OPTIONS',	'postworld-global-options' ); // Case in-sensitive

///// VERSIONS /////
define( 'PW_DB_VERSION', 'postworld-db-version' );
define( 'PW_THEME_VERSION', 'postworld-theme-version' );

// MUST BE DEFINED BY THE THEME
//define( 'PW_OPTIONS_STYLES', 	'postworld-styles-theme' );

/////////////// HIGH PRIORITY ////////////////

////// UTILITIES //////
include 'php/core/utilities.php';

////// API //////
// Load API functions
include 'php/core/api.php';

////// AJAX AUTHORIZATION //////
if( defined('DOING_AJAX') ){
	//include 'php/core/ajax-auth.php';
}

////// FILTER FUNCTIONS //////
include 'php/core/filters.php';

////// MODULE FUNCTIONS //////
include 'php/core/modules.php';

////// PW GLOBALS //////
// This must come after the API functions
// And before the rest of the Postworld includes
$pw['info']['modules'] = pw_enabled_modules();	// pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES ) );

////// INFINITE //////
// Load Infinite Lineage
include "infinite/functions.php";

////// VARIABLES //////
include 'php/core/variables.php';

////// PATHS //////
define( 'POSTWORLD_URI', get_postworld_uri() );

////// H2O //////
require_once 'lib/h2o/h2o.php';

// GLOBAL VARIABLES
global $pw_settings;
global $pw_queries;
global $wp_rewrite;
$wp_rewrite = new WP_Rewrite();


// INSTALL QUERIES
include 'php/core/install_queries.php';


////////// INSTALL POSTWORLD ///////////
include 'php/core/install.php';

/*
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );
register_activation_hook( __FILE__, 'postworld_install_Foreign_keys' );
register_activation_hook( __FILE__, 'postworld_install_Triggers' );
*/

//include 'php/core/debugger.php';

/////////////// HIGH PRIORITY MODULES ////////////////
//include 'php/modules/security-ip/security-ip.php';


/////////////// MEDIUM PRIORITY ////////////////

////// META FUNCTIONS //////
//include 'php/core/meta.php';

////// SOCIAL //////
include 'php/core/language.php';

////// POINTS FUNCTIONS //////
include 'php/core/points.php';

////// RANK FUNCTIONS //////
include 'php/core/rank.php';

////// TEMPLATE FUNCTIONS //////
include 'php/core/templates.php';
include 'php/core/template_partials.php';

////// FEED FUNCTIONS //////
include 'php/core/feeds.php';

////// CRON / SCHEDULED TASKS //////
include 'php/core/cron.php';

////// USER FUNCTIONS //////
include 'php/core/user_meta.php';
include 'php/core/users.php';

////// TAXONOMY FUNCTIONS //////
include 'php/core/taxonomies.php';
include 'php/core/taxonomy_operations.php';

////// CACHE FUNCTIONS //////
include 'php/core/cache.php';

////// RELATED POST FUNCTIONS //////
include 'php/core/related.php';

////// GET POST FUNCTIONS //////
include 'php/core/fields.php';
include 'php/core/images.php';
include 'php/core/posts.php';

////// QUERY FUNCTIONS //////
include 'php/core/query.php';

////// WIDGETS //////
include 'php/core/widgets.php';

////// ARCHIVES //////
include 'php/core/archives.php';

////// SOCIAL //////
include 'php/core/social.php';

////// WIZARD //////
include 'php/core/wizard.php';

////// OPTIONS //////
include 'php/core/options.php';

////// OPTIONS HELPERS //////
include 'php/core/options-helpers.php';

////// PROGRESS //////
include 'php/core/progress.php';

////// VIEW //////
include 'php/core/view.php';

////// EMBED //////
include 'php/core/embed.php';

////// EMBED //////
include 'php/core/html.php';

////// BUDDYPRESS //////
include 'php/core/buddypress.php';

////// EVENTS //////
include 'php/core/events.php';

////// STYLES //////
include 'php/core/styles.php';

////// MENUS //////
include 'php/core/menus.php';

////// DEV //////
include 'php/core/dev.php';

////// ADMIN //////
include 'admin/postworld_admin.php';

////// ADMIN OPITONS //////
include 'admin/php/admin.php';

////// MODULES //////
include 'php/modules/site/postworld-site.php';
include 'php/modules/backgrounds/postworld-backgrounds.php';
include 'php/modules/sidebars/postworld-sidebars.php';
include 'php/modules/layouts/postworld-layouts.php';
include 'php/modules/iconsets/postworld-iconsets.php';
include 'php/modules/taxonomy-meta/postworld-taxonomy-meta.php';
include 'php/modules/shortcodes/postworld-shortcodes.php';
include 'php/modules/slider/postworld-slider.php';
include 'php/modules/term-feed/postworld-term-feed.php';
include 'php/modules/user-feed/postworld-user-feed.php';
include 'php/modules/gallery/postworld-gallery.php';

////// JSON API //////
// Added support in WordPress 4.4
// It won't work in earlier versions.
global $wp_version;
if( $wp_version >= 4.4 )
	include 'php/modules/rest-api/postworld-rest-api.php';


if( pw_module_enabled( 'devices' ) )
	include 'php/modules/devices/postworld-devices.php';

if( pw_module_enabled( 'colors' ) )
	include 'php/modules/colors/postworld-colors.php';

if( pw_module_enabled( 'comments' ) )
	include 'php/modules/comments/postworld-comments.php';


////// GET AJAX FUNCTIONS AND ACTION ////// 
include 'php/core/ajax.php';
include 'php/core/comments.php';
include 'php/core/share.php';

include 'php/core/meta.php';

////// INCLUDES //////
include 'php/core/includes.php';

////// UPDATE / MIGRATE //////
include 'php/core/update.php';

////// ADD LESS SUPPORT //////
require_once( POSTWORLD_PATH.'/lib/wp-less/wp-less.php' );

///// ADD HEADER CODE /////
add_action('wp_head','pw_add_header_code');
function pw_add_header_code() {
	$output = get_option( PW_OPTIONS_HEADER_CODE, '' );
	echo $output;
}

///// ENABLE WPDB ERRORS IF IN DEV MODE /////
global $wpdb;
if( pw_dev_mode() )
	$wpdb->show_errors();




//To get user id from wordpress
//require_once(realpath(__DIR__.'/../../..').'/wp-includes/pluggable.php' );

?>