<?php

////////// PW AVATAR : UI //////////
add_action( 'show_user_profile', 'pw_avatar_profile_field' );
add_action( 'edit_user_profile', 'pw_avatar_profile_field' );
function pw_avatar_profile_field( $user ) { 

	// Get the settings
	global $pwSiteGlobals;
	$settings = _get( $pwSiteGlobals, 'wp_admin.usermeta.pw_avatar' );
	if( $settings == false )
		return false;

	// Avatar Meta Key
	$avatar_meta_key = pw_get_avatar_meta_key();
	// Get the attachment ID if it exists
	$avatar_image_id = get_user_meta( $user->ID, $avatar_meta_key, true );
	// Include Template
	echo pw_ob_admin_template( 'user-meta-pw-avatar', $avatar_image_id );

}

////////// PW AVATAR : SAVE //////////
add_action( 'personal_options_update', 'pw_avatar_profile_field_save' );
add_action( 'edit_user_profile_update', 'pw_avatar_profile_field_save' );
function pw_avatar_profile_field_save( $user_id ) {

	// Security Layer
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Avatar Meta Key
	$avatar_meta_key = pw_get_avatar_meta_key();

	// Get the image ID
	$avatar_image_id = $_POST['pw_avatar'];

	if( !empty( $avatar_image_id ) )
		// Update the table
		update_user_meta( $user_id, $avatar_meta_key, $avatar_image_id );
	else
		delete_user_meta( $user_id, $avatar_meta_key );

}

function pw_get_avatar_meta_key(){
	// Returns the configured Postworld Avatar User Meta Key

	global $pwSiteGlobals;
	// Get config override
	$avatar_meta_key = _get( $pwSiteGlobals, 'wp_admin.usermeta.pw_avatar.meta_key' );
	// Set default
	if( !$avatar_meta_key )
		$avatar_meta_key = PW_AVATAR_KEY;

	return $avatar_meta_key;
}

?>