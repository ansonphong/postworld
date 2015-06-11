<?php

//////////////////// SETTINGS ////////////////////
global $infinite_version;
$infinite_version = "0.2";

define( 'INFINITEPATH', dirname(__FILE__) );

// ADD THEME SUPPORT
add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails' );

// ADD LESS SUPPORT
require_once( 'packages/wp-less/wp-less.php' );

// UNIVERSAL PHP VARIABLES
//$theme_url = "";


//////////////////// INCLUDES ////////////////////
// IMPORT ALL UNIVERSAL PHP VARIABLES
include_once 'php/variables.php';

// UTILITIES
include_once 'php/utilities.php';


// DEFINE PATHS
include_once 'php/paths.php';

// GLOBALS
include_once 'php/globals.php';

// OPTIONS
include_once 'php/options.php';

// POST META
include_once 'php/postmeta.php';

// ADMIN
//include_once 'admin/php/admin.php';

// LAYOUT & SIDEBAR FUNCTIONS
//include_once 'php/layout-sidebars.php';

// TEMPLATES
include_once 'php/templates.php';

// FEEDS
include_once 'php/feeds.php';

// USER META
include_once 'php/user_meta.php';

// NETWORKS
include "php/networks.php";

// TEMPLATE PARTIALS
include "php/template_partials.php";


////////// POSTWORLD //////////
//include_once locate_template('postworld/pw-config.php');
//include_once locate_template('postworld/pw-language.php');


////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////


//////////////////// IMAGE SIZES ////////////////////

//add_image_size( 'banner', 550, 275, true );
//add_image_size( 'grid', 550, 275, true );

//////////////////// FORCE ON RICH TEXT EDITING ////////////////////
//add_filter( 'user_can_richedit', '__return_true' );

//////////////////// DISABLE HTML ON COMMENTS ////////////////////
add_filter( 'comment_text', 'wp_filter_nohtml_kses' );
add_filter( 'comment_text_rss', 'wp_filter_nohtml_kses' );
add_filter( 'comment_excerpt', 'wp_filter_nohtml_kses' );

//remove_filter( 'the_content', 'wpautop' );
//remove_filter( 'the_content', 'wptexturize' );

//////////////////// REDIRECT WRONG PASSWORD LOGIN ////////////////////
/*
add_action('wp_login_failed', 'redirect_login_failed');
function redirect_login_failed() {
		wp_redirect(get_bloginfo('url') . '/login/?status=wrong_password' );
}
*/

/*
//////////////////// REMOVE ADMIN BAR FOR NON-ADMINS ////////////////////
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
		if (!current_user_can('administrator') && !is_admin()) {
			show_admin_bar(false);
		}
}


// ADJUST STYLING IN THE ADMIN ZONE
function hide_admin_nags() {
	?>
	
	<style type="text/css">
		.wp-admin .update-nag{ display:none }
	</style>

	<?php
}
add_action('admin_head', 'hide_admin_nags');
*/




//////////////////// INCLUDES ////////////////////


?>