<?php
/******************************************
Plugin Name: Post World
Plugin URI: htp://phong.com/
Description: Postworld extends Wordpress to display posts in creative ways
Version: 2.0
Author: phong
Author URI: http://phong.com
License: GPL2
******************************************/

////////// POSTWORLD VARIABLES ///////////
include 'php/postworld_variables.php';
include 'php/postworld_install_queries.php';


// GLOBAL VARIABLES
global $pw_defaults;
global $postworld_db_version;
global $pw_queries;
$postworld_db_version = "1.0";

global $pw_prefix;
$pw_prefix = "postworld_";

global $wpdb;
global $wppw_prefix;
$wppw_prefix = $wpdb->prefix . $pw_prefix;

// TABLE NAMES
global $pw_table_names;
$pw_table_names = array(
  'post_meta'           =>  $wppw_prefix . "post_meta",
  'post_points'         =>  $wppw_prefix . "post_points",
  'comment_meta' 		=>  $wppw_prefix . "comment_meta",
  'comment_points' 		=>  $wppw_prefix . "comment_points",
  'user_meta'       	=>  $wppw_prefix . "user_meta",
  'user_shares'     	=>  $wppw_prefix . "user_shares",
  'user_roles'      	=>  $wppw_prefix . "user_roles",
  );


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
$template_paths = set_template_paths();

////////// WP OPTIONS ///////////
include 'php/postworld_options.php';

////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );
register_activation_hook( __FILE__, 'postworld_install_Foreign_keys' );
register_activation_hook( __FILE__, 'postworld_install_Triggers' );

////////// META FUNCTIONS ///////////
include 'php/postworld_meta.php';

////////// POINTS FUNCTIONS ///////////
include 'php/postworld_points.php';

////////// RANK FUNCTIONS ///////////
include 'php/postworld_rank.php';

////////// FEED FUNCTIONS ///////////
//include 'php/postworld_feed.php';

////////// CRON / SCHEDULED TASKS ///////////
//include 'php/postworld_cron.php';

////////// USER FUNCTIONS ///////////
include 'php/postworld_users.php';

////////// GET POST FUNCTIONS ///////////
include 'php/postworld_images.php';
include 'php/postworld_posts.php';


/*
//TO get user id from wordpress
require_once('/wp-config.php');
require_once('/wp-includes/wp-db.php');
require_once('/wp-includes/pluggable.php');
*/



?>