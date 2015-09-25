<?php
////////// METABOXES //////////
include 'metaboxes/options/metabox-options.php';
include 'metaboxes/link_url/metabox-link_url.php';
include 'metaboxes/event/metabox-event.php';
include 'metaboxes/post_parent/metabox-post_parent.php';	
include 'metaboxes/wp_postmeta/metabox-wp_postmeta.php';	

////////// USER META //////////
include 'usermeta/pw_avatar/pw_avatar.php';
include 'usermeta/contact_methods/contact_methods.php';
include 'usermeta/fields/usermeta_fields.php';

///// MODULE : LAYOUTS /////
if( pw_module_enabled('layouts') )
	include 'metaboxes/layout/metabox-layout.php';	

///// MODULE : BACKGROUNDS /////
if( pw_module_enabled('backgrounds') )
	include 'metaboxes/background/metabox-background.php';	

?>