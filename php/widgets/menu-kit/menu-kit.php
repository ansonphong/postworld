<?php
/*__  __                    _  ___ _   
 |  \/  | ___ _ __  _   _  | |/ (_) |_ 
 | |\/| |/ _ \ '_ \| | | | | ' /| | __|
 | |  | |  __/ | | | |_| | | . \| | |_ 
 |_|  |_|\___|_| |_|\__,_| |_|\_\_|\__|

////////////// MENU KIT //////////////*/

// MODULES
include 'menu-kit-pages.php';
include 'menu-kit-categories.php';
include 'menu-kit-authors.php';
include 'menu-kit-custom-menu.php';
include 'menu-kit-widget.php';

// INSERT STYLE SHEET	
add_action( 'admin_enqueue_scripts', 'menu_kit_admin_styles' );
function menu_kit_admin_styles(){
	wp_register_style( 'menu-kit-widget-style', get_template_directory_uri() . '/menu-kit/css/menu-widget.css');
	wp_enqueue_style( 'menu-kit-widget-style' );
}

?>