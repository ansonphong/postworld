<?php

////////// USER META FIELD : UI //////////
add_action( 'show_user_profile', 'pw_user_meta_field_input' );
add_action( 'edit_user_profile', 'pw_user_meta_field_input' );
function pw_user_meta_field_input( $user ) { 

	// Get the settings
	global $pwSiteGlobals;
	$fields = _get( $pwSiteGlobals, 'wp_admin.user_meta.fields' );
	if( $fields == false )
		return false;

	// Iterate through each field
	foreach( $fields as $field ){
		/*
		/// EXAMPLE FIELD ///
		array(
			'type'				=>	'editor',
			'label'				=>	'Extended Bio',
			'description'		=>	'An extended biography',	
			'meta_key'			=>	'theme_biography',
			'icon'				=>	'icon-profile',
			'settings'	=>	array(
				'drag_drop_upload'	=>	true,
				),
			),
		*/

		// Get the attachment ID if it exists
		$field['value'] = get_user_meta( $user->ID, $field['meta_key'], true );

		// Include Template
		// Field types supported : 'editor'
		echo pw_ob_admin_template( 'user-meta-'.$field['type'], $field );

	}

}

////////// USER META FIELD : SAVE //////////
add_action( 'personal_options_update', 'pw_user_meta_field_save' );
add_action( 'edit_user_profile_update', 'pw_user_meta_field_save' );
function pw_user_meta_field_save( $user_id ) {

	// Security Layer
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Get the image ID
	$pwusermeta = $_POST['pwusermeta'];

	//pw_log( $pwusermeta );

	if( !is_array( $pwusermeta ) )
		return false;

	foreach( $pwusermeta as $meta_key => $meta_value ){
		if( !empty( $meta_value ) )
			update_user_meta( $user_id, $meta_key, $meta_value );
		else
			delete_user_meta( $user_id, $meta_key );
	}

}

?>