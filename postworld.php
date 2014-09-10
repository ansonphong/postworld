<?php
/******************************************
Plugin Name: Postworld
Plugin URI: htp://phong.com/
Description: Wordpress API extension, with AngularJS client-side framework, LESS support, and standard libraries for developers to display posts in creative ways
Version: 2.0
Author: phong
Author URI: http://phong.com
License: GPL2
******************************************/

////////// POSTWORLD VARIABLES & HELPER FUNCTIONS ///////////

include 'php/postworld_variables.php';
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

$postworld_version = "1.5.5";
$postworld_db_version = $postworld_version;

//global $pw_prefix;
//$pw_prefix = "postworld_";

global $wpdb;
$wpdb->pw_prefix = $wpdb->prefix . "postworld_";


// INSTALL QUERIES
include 'php/postworld_install_queries.php';

///// SET TEMPLATE PATHS /////
function set_template_paths(){
	$template_paths['PLUGINS_URL'] = plugins_url();
	$template_paths['POSTWORLD_URL'] = $template_paths['PLUGINS_URL'].'/postworld';
	$template_paths['POSTWORLD_PATH'] = plugin_dir_path(__FILE__);

	$template_paths['THEME_URL'] = get_stylesheet_directory_uri(); 		// ABSOLUTE URI http://...
	$template_paths['THEME_PATH'] = get_stylesheet_directory();			// ABSOLUTE PATH /home/user/... 

	$template_paths['CSS_PATH'] = '/postworld/css/';
	$template_paths['JS_PATH'] = '/postworld/js/';
	$template_paths['IMAGES_PATH'] = '/postworld/images/';
	$template_paths['TEMPLATES_PATH'] = '/postworld/templates/';

	$template_paths['PW_CSS_URL'] = $template_paths['POSTWORLD_URL'].'/css/';
	$template_paths['PW_JS_URL'] = $template_paths['POSTWORLD_URL'].'/js/';
	$template_paths['PW_IMAGES_URL'] = $template_paths['POSTWORLD_URL'].'/images/';
	$template_paths['PW_TEMPLATES_URL'] = $template_paths['POSTWORLD_URL'].'/templates/';


	return $template_paths;
}
global $template_paths;
$template_paths = set_template_paths();

////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );
register_activation_hook( __FILE__, 'postworld_install_Foreign_keys' );
register_activation_hook( __FILE__, 'postworld_install_Triggers' );

//include 'php/postworld_debugger.php';

////////// META FUNCTIONS ///////////
//include 'php/postworld_meta.php';

/////////////// API ////////////////
include 'php/postworld_api.php';

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

/////////////// META BOXES ////////////////
include 'php/postworld_metabox.php';

/////////////// ADMIN ////////////////
include 'php/postworld_admin.php';

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




//To get user id from wordpress

//require_once(realpath(__DIR__.'/../../..').'/wp-includes/pluggable.php' );


// ADMIN
//include 'admin/postworld_admin.php';


?>