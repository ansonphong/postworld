<?php
////////// METABOXES //////////
include 'metaboxes/options/metabox-options.php';
include 'metaboxes/link_url/metabox-link_url.php';
include 'metaboxes/event/metabox-event.php';
include 'metaboxes/post_parent/metabox-post_parent.php';	

///// MODULE : LAYOUTS /////
if( in_array( 'layouts', $pw['modules'] ) )
	include 'metaboxes/layout/metabox-layout.php';	

///// MODULE : BACKGROUNDS /////
if( in_array( 'backgrounds', $pw['modules'] ) )
	include 'metaboxes/background/metabox-background.php';	

///// ENQUEUE STYLES & SCRIPTS /////
add_action( 'admin_enqueue_scripts', 'pw_admin_enqueue' );
function pw_admin_enqueue() {
	wp_enqueue_style( 'pw-admin-styles', POSTWORLD_URI.'/admin/less/style.less' );
	pwAdminGlobals_include();
}

?>