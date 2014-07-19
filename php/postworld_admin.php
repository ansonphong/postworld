<?php

///// ENQUEUE STYLES & SCRIPTS /////
add_action( 'admin_enqueue_scripts', 'pw_admin_enqueue' );
function pw_admin_enqueue() {

	wp_enqueue_style( 'pw-admin-styles', plugins_url( '../' , __FILE__ ).'/admin/less/postworld-admin.less' );
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );

}

function pw_get_menus(){
	$menus = get_terms( 'nav_menu' );

	// Convert some values to integers
	if( !empty($menus) ){
		$new_menus = array();
		foreach( $menus as $menu ){
			$menu->term_id = intval($menu->term_id);
			$menu->term_group = intval($menu->term_group);
			$menu->term_taxonomy_id = intval($menu->term_taxonomy_id);
			array_push($new_menus, $menu);
		}
		$menus = $new_menus;
	}

	return $menus;

}


?>