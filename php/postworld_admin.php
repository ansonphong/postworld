<?php

///// ENQUEUE STYLES & SCRIPTS /////
add_action( 'admin_enqueue_scripts', 'pw_admin_enqueue' );
function pw_admin_enqueue() {

	wp_enqueue_style( 'pw-admin-styles', plugins_url( '../' , __FILE__ ).'/admin/less/postworld-admin.less' );
	
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );

}

?>