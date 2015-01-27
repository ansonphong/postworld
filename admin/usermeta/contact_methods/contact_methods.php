<?php

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

	// Get the settings
	global $pwSiteGlobals;
	$settings = _get( $pwSiteGlobals, 'wp_admin.usermeta.contact_methods' );
	if( !$settings || !is_array( $settings ) )
		return $profile_fields;

	// Add new fields
	foreach( $settings as $meta_key => $meta_description ){
		$profile_fields[$meta_key] = $meta_description;
	}

	return $profile_fields;
}
add_filter('user_contactmethods', 'pw_modify_contact_methods');

?>