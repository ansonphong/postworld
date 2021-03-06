<?php
/*____           _                  _        
 |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
 | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
 |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
 |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
                                             
/////////////////////////////////////////////*/

///// META BOX FUNCTIONS /////
add_action('admin_init','pw_postmeta_metabox_init');
function pw_postmeta_metabox_init(){    

	// Add to Post Types
	$post_types = pw_config('wp_admin.metabox.'.PW_POSTMETA_KEY.'.post_types');
	// Default Post Types
	if( !$post_types )
		$post_types = array('post','page');

    foreach( $post_types as $post_type ){
        add_meta_box(
        	'pw_metabox_options',
        	'Options',
        	'pw_metabox_options_setup',
        	$post_type,
        	'normal',
        	'high'
        	);
    }

    // Add a callback function to save any data a user enters in
    add_action('save_post','pw_metabox_options_save');

}

///// SETUP META DATA /////
function pw_metabox_options_setup(){
	/*
	// Load Meta Model
	$meta_model_file = plugin_dir_path(__FILE__).'meta-model.php';
	if( file_exists( $meta_model_file ) )
		include $meta_model_file;
    */
	// Load Template
	include 'metabox-template.php';

}

///// SAVE THE DATA /////
function pw_metabox_options_save( $post_id ){
	// Stop autosave to preserve meta data
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
        return $post_id;

    // Security Layer 
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

	$meta_key = PW_POSTMETA_KEY;

    $pwMeta = _get( $_POST, $meta_key );

	// SAVE I META
	if( !empty( $pwMeta ) && is_string( $pwMeta ) ){
		// Sanitize JSON string
		$pwMeta_json = (string) sanitize_text_field( $pwMeta );
		// Update the post meta
		update_post_meta( $post_id, $meta_key, $pwMeta_json );
		//pw_log( "$pwMeta_json" . $pwMeta_json );
	}
	
    return $post_id;
}
