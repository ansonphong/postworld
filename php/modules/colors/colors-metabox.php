<?php
add_action('admin_init','pw_metabox_init_colors');
function pw_metabox_init_colors(){    

	global $pwSiteGlobals;
	$post_id = (int) $_GET['post'];
	$this_post_type = get_post_type( $post_id );

	// Get the settings
	$metabox_settings = _get( $pwSiteGlobals, 'wp_admin.metabox.colors' );
	if( empty($metabox_settings) || !is_array( $metabox_settings ) )
		return false;
	
	// Get the post types
	$post_types = _get( $metabox_settings, 'post_types' );

	// Iterate through the post types
	if( !empty( $post_types ) ){
		foreach( $post_types as $post_type ){
			
			// Construct Variables for Callback
			$args = array(
				'post_type' =>  $post_type,
				);

			// Add the metabox
			add_meta_box(
				'pw_color_meta',
				'Colors',
				'pw_colors_meta_init',
				$post_type,
				'side',
				'low',
				$args //  Pass callback variables
				);

		} // End Foreach : Post Type

		// Add a callback function to save any data a user enters in
		//if( in_array( $this_post_type, $post_types ) )
			add_action( 'save_post','pw_colors_meta_save' );

	}

}

////////////// CREATE UI //////////////
function pw_colors_meta_init( $post, $metabox ){
	global $pw;
	global $pwSiteGlobals;

	extract( $metabox['args'] );

	$colors = pw_generate_attachment_colors( array( 'attachment_id' => $post->ID ) );

	$pw_post = pw_get_post( $post->ID, array( 'ID', 'post_type', 'image(colors)' ) );

	// Apply filters for themes to over-ride
	//$query = apply_filters( 'pw_colors_metabox_vars', $query );

	///// INCLUDE TEMPLATE /////
	include "colors-metabox-controller.php";

}


?>