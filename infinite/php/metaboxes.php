<?php
/*____           _                  _        
 |  _ \ ___  ___| |_ _ __ ___   ___| |_ __ _ 
 | |_) / _ \/ __| __| '_ ` _ \ / _ \ __/ _` |
 |  __/ (_) \__ \ |_| | | | | |  __/ || (_| |
 |_|   \___/|___/\__|_| |_| |_|\___|\__\__,_|
                                             
/////////////////////////////////////////////*/

///// META BOX FUNCTIONS /////
add_action('admin_init','i_postmeta_metabox_init');
function i_postmeta_metabox_init(){    

	// Add to Post Types
	$post_types = array('post','page');
    foreach( $post_types as $post_type ){
        add_meta_box(
        	'i_child_meta',
        	'Options',
        	'i_child_postmeta_setup',
        	$post_type,
        	'normal',
        	'high'
        	);
    }

    // Add a callback function to save any data a user enters in
    add_action('save_post','i_postmeta_save');

}

///// ENQUEUE STYLES & SCRIPTS /////
add_action( 'admin_enqueue_scripts', 'i_child_enqueue' );
function i_child_enqueue() {
	$path = "/less/admin/";
	wp_enqueue_style( 'i-child-admin', get_stylesheet_directory_uri().$path.'admin-styles.less' );
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

///// SETUP META DATA /////
function i_child_postmeta_setup(){

	// Load Meta Model
	$meta_model_file = plugin_dir_path(__FILE__).'meta-model.php';
	if( file_exists( $meta_model_file ) )
		include $meta_model_file;
    
	// Load Infinite Post Meta
	global $post;
	global $iMeta;
	$iMeta = i_get_postmeta( $post->ID );

	// Load Template
	include 'metaboxes-template.php';

}

///// SAVE THE DATA /////
function i_postmeta_save( $post_id ){

	pw_log( "i_postmeta_save : POST ID : " . $post_id  );

	$meta_key = "i_meta";

	// STOP FROM DOING AUTOSAVE TO PRESERVE META DATA
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;

    $iMeta = $_POST['i_meta'];

	// SAVE I META
	if( !empty( $iMeta ) && is_string( $iMeta ) ){
		// Sanitize JSON string
		$iMeta_json = (string) sanitize_text_field( $iMeta );
		// Update the post meta
		update_post_meta( $post_id, $meta_key, $iMeta_json );
	}
	
    return $post_id;
}

?>