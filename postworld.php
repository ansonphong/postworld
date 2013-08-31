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

////////// INSTALL POSTWORLD ///////////
include 'php/postworld_install.php';
register_activation_hook( __FILE__, 'postworld_install' );


////////// POINTS FUNCTIONS ///////////
include 'php/postworld_points.php';



?>