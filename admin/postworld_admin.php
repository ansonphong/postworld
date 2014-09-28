<?php
////////// METABOXES //////////
include 'metaboxes/link_url/metabox-link_url.php';	
include 'metaboxes/event/metabox-event.php';	
include 'metaboxes/post_parent/metabox-post_parent.php';	

///// ENQUEUE STYLES & SCRIPTS /////
add_action( 'admin_enqueue_scripts', 'pw_admin_enqueue' );
function pw_admin_enqueue() {
	wp_enqueue_style( 'pw-admin-styles', POSTWORLD_URI.'/admin/less/style.less' );
}


/*
////////// ADMIN MENU //////////
include 'postworld_admin_panel.php';

$pw_plugin_folder = '/postworld';
$pw_admin_folder = $pw_plugin_folder.'/admin';
$pw_admin_page = $pw_admin_folder.'/panel-main.php';

///// ADD ADMIN MENU PAGE /////
add_action( 'admin_menu', 'postworld_admin_menu_page', 8 );
function postworld_admin_menu_page(){
	global $pw_plugin_folder;
	global $pw_admin_folder;

	//$page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
	$pw_admin_menu_name = 'postworld-settings';
	$pw_admin = array(
		'main' => array(
			'page_title' => 'Postworld',
			'menu_title' => 'Postworld',
			'capability' => 'manage_options',
			'menu_slug' => $pw_admin_menu_name,
			'function' => 'postworld_settings_display',
			'icon_url' => plugins_url( $pw_admin_folder.'/images/logo/pw_symbol-16.png' ),
			'position' => ''
			),
		'roles' => array(
			'parent_slug' => $pw_admin_menu_name,
			'page_title' => 'Roles',
			'menu_title' => 'Roles',
			'capability' => 'manage_options',
			'menu_slug' => $pw_admin_menu_name.'-roles',
			'function' => 'postworld_roles_settings_display',
			)
		);

    add_menu_page(
    	$pw_admin['main']['page_title'],
    	$pw_admin['main']['menu_title'],
    	$pw_admin['main']['capability'],
    	$pw_admin['main']['menu_slug'],
    	$pw_admin['main']['function'],
    	$pw_admin['main']['icon_url']
    	);

    add_submenu_page(
    	$pw_admin['roles']['parent_slug'],
    	$pw_admin['roles']['page_title'],
    	$pw_admin['roles']['menu_title'],
    	$pw_admin['roles']['capability'],
    	$pw_admin['roles']['menu_slug'],
    	$pw_admin['roles']['function']
    	);
	
}

///// REGISTER STYLES /////
//wp_register_style( 'pw_admin_css', plugins_url() . '/postworld/admin/css/pw-admin.css' );
*/

?>