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

// GLOBAL VARIABLES
global $pw_defaults;
global $postworld_db_version;
$postworld_db_version = "1.0";

global $pw_prefix;
$pw_prefix = "postworld_";

global $wpdb;
global $wppw_prefix;
$wppw_prefix = $wpdb->prefix . $pw_prefix;

// TABLE NAMES
global $pw_table_names;
$pw_table_names = array(
  'meta'            =>  $wppw_prefix . "meta",
  'points'          =>  $wppw_prefix . "points",
  'points_comments' =>  $wppw_prefix . "points_comments",
  'user_meta'       =>  $wppw_prefix . "user_meta",
  'user_shares'     =>  $wppw_prefix . "user_shares",
  'user_roles'      =>  $wppw_prefix . "user_roles",
  );

////////// WP OPTIONS ///////////
include 'php/postworld_options.php';

////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';
register_activation_hook( __FILE__, 'postworld_install' );
register_activation_hook( __FILE__, 'postworld_install_data' );

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
include 'php/postworld_posts.php';

?>