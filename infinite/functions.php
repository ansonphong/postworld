<?php
//////////////////// SETTINGS ////////////////////
global $infinite_version;
$infinite_version = "0.3";

define( 'INFINITEPATH', dirname(__FILE__) );

// ADD THEME SUPPORT
add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails' );

//////////////////// DISABLE HTML ON COMMENTS ////////////////////
add_filter( 'comment_text', 'wp_filter_nohtml_kses' );
add_filter( 'comment_text_rss', 'wp_filter_nohtml_kses' );
add_filter( 'comment_excerpt', 'wp_filter_nohtml_kses' );

//////////////////// INCLUDES ////////////////////

// UTILITIES
include_once 'php/utilities.php';

?>