<?php
/////////////////////////////////////////////////////////////////////////////////////////
// META FUNCTIONS //

add_action('admin_init','pw_metabox_init_link_url');
function pw_metabox_init_link_url(){    

	// Add to Post Types
	$link_url_post_types = array('post','page');
    foreach( $link_url_post_types as $post_type ){
        add_meta_box(
        	'link_url_meta',
        	'Link URL',
        	'link_url_meta_setup',
        	$post_type,
        	'side',
        	'high'
        	);
    }
    // add a callback function to save any data a user enters in
    add_action('save_post','link_url_meta_save');
}
function link_url_meta_setup(){
    global $post;
	// LOAD LINK SETTINGS
	$pw_post_meta = pw_get_post_meta($post->ID);

	$link_url = $pw_post_meta['link_url'];
	$link_format = $pw_post_meta['link_format'];

  	// THE CONTROLS
	//include( plugin_dir_path(__FILE__).'admin/pw-meta-box.php');
	
	include plugin_dir_path(__FILE__).'link_url-template.php';

}
function link_url_meta_save($post_id){
	// STOP FROM DOING AUTOSAVE TO PRESERVE META DATA
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
	// SAVE URL
	pw_set_post_meta($post_id,
		array(
			'link_url' 		=> $_POST['link_url'],
			'link_format' 	=> $_POST['link_format'],
			)
		);
    return $post_id;
}
 

// END META FUNCTIONS //
/////////////////////////////////////////////////////////////////////////////////////////
?>