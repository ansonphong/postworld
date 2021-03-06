<?php
////////////// ADD METABOX //////////////
if( !pw_is_admin_ajax() )
	add_action('admin_init','pw_metabox_init_link_url');

function pw_metabox_init_link_url(){    

	// Add to Post Types
	$metabox_post_types = pw_config('wp_admin.metabox.link_url.post_types');
	
	// Set the default Post Types
	if( !$metabox_post_types )
		$metabox_post_types = array( 'post', 'page' );

	// Add Metabox to each specified Post Type
	foreach( $metabox_post_types as $post_type ){
		add_meta_box(
			'link_url_meta',
			'Link URL',
			'pw_link_url_meta_ui',
			$post_type,
			'side',
			'high'
			);
	}
	// Add Callback Function on Save
	add_action('save_post','pw_link_url_meta_save');
	add_action('edit_attachment','pw_link_url_meta_save');
	
}

////////////// CREATE UI //////////////
function pw_link_url_meta_ui(){
	global $post;

	$link_url = get_post_meta( $post->ID, PW_LINK_URL_KEY, true );
	$link_format = get_post_meta( $post->ID, PW_LINK_FORMAT_KEY, true );

	// Include the template
	$metabox_template = pw_get_template ( 'admin', 'metabox-link_url', 'php', 'dir' );
	include $metabox_template;

}

////////////// SAVE POST //////////////
function pw_link_url_meta_save($post_id){

	// Stop autosave to preserve meta data
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) )
		return $post_id;

	// Security Layer 
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	// Get Vars
	$link_url = _get( $_POST, 'link_url' );
	$link_format = _get( $_POST, 'link_format' );

	// Link URL
	if( !empty($link_url) )
		update_post_meta( $post_id, PW_LINK_URL_KEY, $link_url );
	else
		delete_post_meta( $post_id, PW_LINK_URL_KEY );

	// Link Format
	if( !empty($link_format) )
		update_post_meta( $post_id, PW_LINK_FORMAT_KEY, $link_format );
	else
		delete_post_meta( $post_id, PW_LINK_FORMAT_KEY );

	return $post_id;
}
 

?>