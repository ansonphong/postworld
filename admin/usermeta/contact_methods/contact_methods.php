<?php

function pw_contact_methods_config(){
	// Get Contact Methods from the Postworld Config
	global $pwSiteGlobals;
	$contact_methods = _get( $pwSiteGlobals, 'wp_admin.user_meta.contact_methods' );
	if( !$contact_methods || !is_array( $contact_methods ) )
		return false;
	return $contact_methods;
}

function pw_get_contact_methods_meta(){
	// Get the contact methods meta which has been configured for the site

	// The configured contact methods
	// ie. array( 'twitter', 'facebook', 'instagram' )
	$contact_methods_config = pw_contact_methods_config();

	// The available build-in options meta data
	$contact_methods_options = pw_get_social_media_meta();

	// Setup the meta array to return
	$contact_methods_meta = array();

	// Iterate through each method in the config
	foreach( $contact_methods_config as $contact_method ){
		// Get the meta data from the options
		$contact_method_meta = _get( $contact_methods_options, $contact_method );
		// If it exists
		if( !empty( $contact_method_meta ) )
			// Add it to the methods meta to return
			$contact_methods_meta[$contact_method]	= $contact_method_meta;			
	}

	return $contact_methods_meta;

}

function pw_user_contact_methods( $user_id ){
	// Get the contact method meta data which the user has saved

	// Get the available configured contact methods
	$contact_methods_meta = pw_get_contact_methods_meta();
	// Get User
	$user = pw_to_array( get_user_by( 'id', $user_id ) );
	// Get the user's meta fields
	$usermeta = get_user_meta( $user_id );
	// Setup the known contact methods
	$contact_methods = array();

	// Iterate through each of the contact methods
	foreach( $contact_methods_meta as $key => $value ){

		switch( $key ){
			case 'website':
				// Add the key in the meta
				$value['key'] = $key;
				// Embed the URL into the meta
				$value['url'] = _get($value, 'prepend_url') . _get( $user, 'data.user_url' );
				// Add URL as the value
				$value['value'] = $value['url'];
				// Add the meta to the contact methods
				$contact_methods[] = $value;
				break;

			default:
				// Get the contact method key from user meta
				$usermeta_value = _get( $usermeta, $key );
				// If a value exists
				if( $usermeta_value != false ){
					// Add the key in the meta
					$value['key'] = $key;
					// Embed the value into the meta
					$value['value'] = $usermeta_value[0];
					// Embed the URL into the meta
					$value['url'] = _get($value, 'prepend_url') . $value['value'];
					// Add the meta to the contact methods
					$contact_methods[] = $value;
				}
			break;

		}

	}
	return $contact_methods;

}


function pw_contact_methods_user_menu( $user_id ){
	// Build a menu from the contact methods the user has saved

	// Get the user saved contact methods
	$contact_methods = pw_user_contact_methods( $user_id );

	// Use an ob_include on an admin template, so it can be customized by the theme
	return pw_ob_social_template( 'user-contact-methods', $contact_methods );

}

add_filter('user_contactmethods', 'pw_modify_contact_methods');
function pw_modify_contact_methods( $profile_fields ) {
	/*
	• Fields are stored using the given keys as the `meta_key` in `wp_usermeta`
	• Example usage of `wp_admin.usermeta.contact_methods` in PW Config
	array(
	    'twitter'   =>  'Twitter Username',
	    'facebook'  =>  'Facebook URL',
	    'gplus'     =>  'Google+ URL',
	    )
	*/
	$contact_methods = pw_get_contact_methods_meta();
	if( !$contact_methods )
		return $profile_fields;

	// Add new fields
	foreach( $contact_methods as $meta_key => $value ){
		// If the value is an array
		// ie. array( 'icon' => 'icon-twitter', 'description' => 'Twitter Username' )
		if( is_array( $value ) ){
			$meta_icon = _get( $value, 'icon' );
			$meta_icon = '<i class="icon '.$meta_icon.'"></i> ';
			$value = $meta_icon . _get( $value, 'description' );
		}
		$profile_fields[$meta_key] = $value;
	}

	return $profile_fields;
		
}

?>