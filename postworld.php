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

global $pw;
$pw = array(
	'version'	=>	"1.6.4",
	'slug'	=>	'postworld',
	'vars'	=>	array(
		),
	'db' =>	array(
		'wp_options'	=>	array(
			'option_name'	=>	array(
				'site'				=>	'postworld-site',
				'layouts'			=>	'postworld-layouts',
				'sidebars'			=>	'postworld-sidebars',
				'styles'			=>	'postworld-styles',
				'feeds'				=>	'postworld-feeds',
				'feed_settings'		=>	'postworld-feed-settings',
				'social'			=>	'postworld-social',
				'backgrounds'		=>	'postworld-backgrounds',
				'header_code'		=>	'postworld-header-code',
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
		'styles'		=>	'postworld-model-styles',
		'backgrounds'	=>	'postworld-model-backgrounds',
		),
	);


///// DEFINE OPTION NAMES /////
// Used in 'wp_options' table as 'option_name' key
define( 'PW_OPTIONS_SITE', 			$pw['db']['wp_options']['option_name']['site'] );
define( 'PW_OPTIONS_LAYOUTS', 		$pw['db']['wp_options']['option_name']['layouts'] );
define( 'PW_OPTIONS_SIDEBARS', 		$pw['db']['wp_options']['option_name']['sidebars'] );
define( 'PW_OPTIONS_STYLES', 		$pw['db']['wp_options']['option_name']['styles'] );
define( 'PW_OPTIONS_FEEDS', 		$pw['db']['wp_options']['option_name']['feeds'] );
define( 'PW_OPTIONS_FEED_SETTINGS', $pw['db']['wp_options']['option_name']['feed_settings'] );
define( 'PW_OPTIONS_SOCIAL', 		$pw['db']['wp_options']['option_name']['social'] );
define( 'PW_OPTIONS_BACKGROUNDS', 	$pw['db']['wp_options']['option_name']['backgrounds'] );

define( 'PW_OPTIONS_HEADER_CODE', 	$pw['db']['wp_options']['option_name']['header_code'] );


///// DEFINE MODEL FILTER NAMES /////
define( 'PW_MODEL_STYLES', 		$pw['models']['styles'] );
define( 'PW_MODEL_BACKGROUNDS', $pw['models']['backgrounds'] );

///// DEFINE META FILTER NAMES /////
define( 'PW_POSTMETA', $pw['db']['wp_postmeta']['pw_meta'] );
define( 'PW_USERMETA', $pw['db']['wp_usermeta']['pw_meta'] );

///// DEFINE META KEYS /////
define( 'PW_POSTMETA_KEY',	'pw_meta', true ); // Case in-sensitive
define( 'PW_USERMETA_KEY',	'pw_meta', true ); // Case in-sensitive


// MUST BE DEFINED BY THE THEME
//define( 'PW_OPTIONS_STYLES', 	$pw['db']['wp_options']['option_name']['styles'] );


/////////////// API ////////////////
include 'php/postworld_api.php';

/////////////// VARIABLES ///////////////
include 'php/postworld_variables.php';

/////////////// PATHS ///////////////
define( 'POSTWORLD_PATH', dirname(__FILE__) );
define( 'POSTWORLD_URI', get_postworld_uri() );

////////// UTILITIES ///////////
include 'php/postworld_utilities.php';

/////////////// H2O ////////////////
require_once 'lib/h2o/h2o.php';

// GLOBAL VARIABLES
global $pw_settings;
global $postworld_version;
global $postworld_db_version;
global $pw_queries;
global $wp_rewrite;
$wp_rewrite = new WP_Rewrite();

$postworld_db_version = 0;

//global $pw_prefix;
//$pw_prefix = "postworld_";

global $wpdb;
$wpdb->pw_prefix = $wpdb->prefix . "postworld_";


// INSTALL QUERIES
include 'php/postworld_install_queries.php';

///// SET TEMPLATE PATHS /////

function set_pw_paths(){
	$pw_paths['PLUGINS_URL'] = plugins_url();
	$pw_paths['POSTWORLD_URL'] = $pw_paths['PLUGINS_URL'].'/postworld';
	$pw_paths['POSTWORLD_PATH'] = POSTWORLD_PATH;

	$pw_paths['THEME_URL'] = get_stylesheet_directory_uri(); 		// ABSOLUTE URI http://...
	$pw_paths['THEME_PATH'] = get_stylesheet_directory();			// ABSOLUTE PATH /home/user/... 

	$pw_paths['CSS_PATH'] = '/postworld/css/';
	$pw_paths['JS_PATH'] = '/postworld/js/';
	$pw_paths['IMAGES_PATH'] = '/postworld/images/';
	$pw_paths['TEMPLATES_PATH'] = '/postworld/templates/';

	$pw_paths['PW_CSS_URL'] = $pw_paths['POSTWORLD_URL'].'/css/';
	$pw_paths['PW_JS_URL'] = $pw_paths['POSTWORLD_URL'].'/js/';
	$pw_paths['PW_IMAGES_URL'] = $pw_paths['POSTWORLD_URL'].'/images/';
	$pw_paths['PW_TEMPLATES_URL'] = $pw_paths['POSTWORLD_URL'].'/templates/';


	return $pw_paths;
}
global $pw_paths;
$pw_paths = set_pw_paths();

////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );
register_activation_hook( __FILE__, 'postworld_install_Foreign_keys' );
register_activation_hook( __FILE__, 'postworld_install_Triggers' );

//include 'php/postworld_debugger.php';

////////// META FUNCTIONS ///////////
//include 'php/postworld_meta.php';

////////// FILTER FUNCTIONS ///////////
include 'php/postworld_filters.php';

/////////////// SOCIAL ////////////////
include 'php/postworld_language.php';

////////// POINTS FUNCTIONS ///////////
include 'php/postworld_points.php';

////////// RANK FUNCTIONS ///////////
include 'php/postworld_rank.php';

////////// TEMPLATE FUNCTIONS ///////////
include 'php/postworld_templates.php';

////////// FEED FUNCTIONS ///////////
include 'php/postworld_feeds.php';

////////// CRON / SCHEDULED TASKS ///////////
include 'php/postworld_cron.php';

////////// USER FUNCTIONS ///////////
include 'php/postworld_user_meta.php';
include 'php/postworld_users.php';

////////// TAXONOMY FUNCTIONS ///////////
include 'php/postworld_taxonomies.php';

////////// CACHE FUNCTIONS ///////////
include 'php/postworld_cache.php';

////////// GET POST FUNCTIONS ///////////
include 'php/postworld_images.php';
include 'php/postworld_posts.php';

////////// QUERY FUNCTIONS ///////////
include 'php/postworld_query.php';

/////////////// WIDGETS ////////////////
include 'php/postworld_widgets.php';

/////////////// SOCIAL ////////////////
include 'php/postworld_social.php';

/////////////// WIZARD ////////////////
include 'php/postworld_wizard.php';

/////////////// OPTIONS ////////////////
include 'php/postworld_options.php';

/////////////// VIEW ////////////////
include 'php/postworld_view.php';

/////////////// BUDDYPRESS ////////////////
include 'php/postworld_buddypress.php';

/////////////// ADMIN ////////////////
include 'admin/postworld_admin.php';

/////////////// ADMIN OPITONS ////////////////
include 'admin/php/admin.php';

/////////////// MODULES ////////////////
include 'postworld-modules/backgrounds/postworld-backgrounds.php';


////////// GET AJAX FUNCTIONS AND ACTION ///////////
include 'php/postworld_ajax.php';
include 'php/postworld_comments.php';
include 'php/postworld_share.php';

include 'php/postworld_meta.php';

/////////////// INCLUDES ////////////////
include 'php/postworld_includes.php';

/////////////// SHORTCODES ////////////////
include 'php/postworld_shortcodes.php';

/////////////// UPDATE / MIGRATE ////////////////
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