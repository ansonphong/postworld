<?php
/******************************************
Plugin Name: Postworld
Plugin URI: htp://phong.com/
Description: Wordpress API extension, with AngularJS client-side framework, LESS support, and standard libraries for developers to display posts in creative ways
Version: 2.2
Author: phong
Author URI: http://phong.com
License: GPL2
******************************************/

function pw_mode(){
	return ( defined('POSTWORLD_MODE') ) ?
		POSTWORLD_MODE : 'deploy';
}

function pw_config(){
	global $pwSiteGlobals;
	$pwSiteGlobals = apply_filters( 'pw_config', $pwSiteGlobals );
	return $pwSiteGlobals;
}

global $pw;
$pw = array(
	'info'	=>	array(
		'version'		=>	1.88,
		'db_version'	=>	1.14,
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
		),
	'iconsets'	=>	array(),
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

///// DEFINE OPTION CACHES /////
define( 'PW_CACHE_ICONSET', 	$pw['db']['wp_options']['option_name']['cache_iconset'] );

///// DEFINE MODEL FILTER NAMES /////
define( 'PW_MODEL_FIELDS', 		$pw['models']['fields'] );
define( 'PW_MODEL_POST_FIELDS', $pw['models']['post_fields'] );
define( 'PW_MODEL_USER_FIELDS', $pw['models']['user_fields'] );

define( 'PW_MODEL_STYLES', 		$pw['models']['styles'] );
define( 'PW_MODEL_BACKGROUNDS', $pw['models']['backgrounds'] );

define( 'PW_TERM_FEED', 		$pw['filters']['term_feed'] );
define( 'PW_FEED_DEFAULT', 		$pw['filters']['feed_default'] );
define( 'PW_FEED_OVERRIDE', 	$pw['filters']['term_feed'] );

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

///// DEFINE PRINT FILTERS /////
define( 'PW_GLOBAL_OPTIONS',	'postworld-global-options' ); // Case in-sensitive

///// DB VERSION /////
define( 'PW_DB_VERSION', 'postworld-db-version' );

// MUST BE DEFINED BY THE THEME
//define( 'PW_OPTIONS_STYLES', 	'postworld-styles-theme' );



/////////////// HIGH PRIORITY ////////////////

////// UTILITIES //////
include 'php/postworld_utilities.php';


////// API //////
// Load API functions
include 'php/postworld_api.php';

////// FILTER FUNCTIONS //////
include 'php/postworld_filters.php';

////// MODULE FUNCTIONS //////
include 'php/postworld_modules.php';

////// PW GLOBALS //////
// This must come after the API functions
// And before the rest of the Postworld includes
$pw['info']['modules'] = pw_enabled_modules();	// pw_get_option( array( 'option_name' => PW_OPTIONS_MODULES ) );

////// INFINITE //////
// Load Infinite Lineage
include "infinite/functions.php";

////// VARIABLES //////
include 'php/postworld_variables.php';

////// PATHS //////
define( 'POSTWORLD_PATH', dirname(__FILE__) );
define( 'POSTWORLD_URI', get_postworld_uri() );

////// H2O //////
require_once 'lib/h2o/h2o.php';

// GLOBAL VARIABLES
global $pw_settings;
global $pw_queries;
global $wp_rewrite;
$wp_rewrite = new WP_Rewrite();


//global $pw_prefix;
//$pw_prefix = "postworld_";

global $wpdb;
$wpdb->pw_prefix = $wpdb->prefix . "postworld_";


// INSTALL QUERIES
include 'php/postworld_install_queries.php';


////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';

/*
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );
register_activation_hook( __FILE__, 'postworld_install_Foreign_keys' );
register_activation_hook( __FILE__, 'postworld_install_Triggers' );
*/

//include 'php/postworld_debugger.php';


/////////////// MEDIUM PRIORITY ////////////////

////// META FUNCTIONS //////
//include 'php/postworld_meta.php';

////// SOCIAL //////
include 'php/postworld_language.php';

////// POINTS FUNCTIONS //////
include 'php/postworld_points.php';

////// RANK FUNCTIONS //////
include 'php/postworld_rank.php';

////// TEMPLATE FUNCTIONS //////
include 'php/postworld_templates.php';

////// FEED FUNCTIONS //////
include 'php/postworld_feeds.php';

////// CRON / SCHEDULED TASKS //////
include 'php/postworld_cron.php';

////// USER FUNCTIONS //////
include 'php/postworld_user_meta.php';
include 'php/postworld_users.php';

////// TAXONOMY FUNCTIONS //////
include 'php/postworld_taxonomies.php';

////// CACHE FUNCTIONS //////
include 'php/postworld_cache.php';

////// GET POST FUNCTIONS //////
include 'php/postworld_fields.php';
include 'php/postworld_images.php';
include 'php/postworld_posts.php';

////// QUERY FUNCTIONS //////
include 'php/postworld_query.php';

////// WIDGETS //////
include 'php/postworld_widgets.php';

////// ARCHIVES //////
include 'php/postworld_archives.php';

////// SOCIAL //////
include 'php/postworld_social.php';

////// WIZARD //////
include 'php/postworld_wizard.php';

////// OPTIONS //////
include 'php/postworld_options.php';

////// VIEW //////
include 'php/postworld_view.php';

////// EMBED //////
include 'php/postworld_embed.php';

////// BUDDYPRESS //////
include 'php/postworld_buddypress.php';

////// ADMIN //////
include 'admin/postworld_admin.php';

////// ADMIN OPITONS //////
include 'admin/php/admin.php';

////// MODULES //////
include 'postworld-modules/backgrounds/postworld-backgrounds.php';
include 'postworld-modules/sidebars/postworld-sidebars.php';
include 'postworld-modules/layouts/postworld-layouts.php';
include 'postworld-modules/iconsets/postworld-iconsets.php';
include 'postworld-modules/taxonomy-meta/postworld-taxonomy-meta.php';
include 'postworld-modules/shortcodes/postworld-shortcodes.php';
include 'postworld-modules/slider/postworld-slider.php';
include 'postworld-modules/term-feed/postworld-term-feed.php';
include 'postworld-modules/user-feed/postworld-user-feed.php';
include 'postworld-modules/gallery/postworld-gallery.php';

////// GET AJAX FUNCTIONS AND ACTION ////// 
include 'php/postworld_ajax.php';
include 'php/postworld_comments.php';
include 'php/postworld_share.php';

include 'php/postworld_meta.php';

////// INCLUDES //////
include 'php/postworld_includes.php';

////// UPDATE / MIGRATE //////
include 'php/postworld_update.php';


///// ADD HEADER CODE /////
add_action('wp_head','pw_add_header_code');
function pw_add_header_code() {
	$output = get_option( PW_OPTIONS_HEADER_CODE, '' );
	echo $output;
}

//To get user id from wordpress

//require_once(realpath(__DIR__.'/../../..').'/wp-includes/pluggable.php' );

?>