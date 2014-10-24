<?php

function pw_get_bp_xprofile_fields( $fields = array() ){
	// $fields = [An array of strings] = array( 'Field Name One', 'Field Name Two' );

	// Return if missing function dependency
	if( !function_exists('xprofile_get_field_data') )
		return false;
	// Init return object
	$xProfile = array();
	// Iterate through fields
	foreach( $fields as $field ){
		// Sanitize key
		$key = pw_sanitize_key( $field );
		// Set it into the return object under the sanitized key
		$xProfile[ $key ] = xprofile_get_field_data( $field );
	}

	return $xProfile;

}


?>