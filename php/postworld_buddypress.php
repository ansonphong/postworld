<?php
function pw_get_bp_xprofile_fields( $fields = array() ){
	// Returns multiple xProfile fields simultaneously
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

function pw_get_xprofile_fields(){
	// Returns an array with all the names of the xProfile fields
	global $wpdb;
	$table_name =  $wpdb->prefix.'bp_xprofile_fields';

	//If table is not created
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    	return array();
	}

	$column_name = 'name';
	$xProfileFields = $wpdb->get_col( "SELECT $column_name FROM $table_name" );

	return $xProfileFields;
}

function pw_get_xprofile( $user_id, $fields = array(), $vars = array() ){
	// Get info from Bussypress extended profile
	// 	$fields = [array/string] // IE. 'all' or array( 'First Field Name', 'Second Field Name' )

	// If Buddypress isn't installed, return false
	if( !function_exists('xprofile_get_field_data') )
		return false;

	///// DEFAULTS /////
	$default_vars = array(
		'sanitize_keys' => true,	// Sanitizes the keys before output to lowercase & space = _
		);

	$vars = array_replace_recursive( $default_vars, $vars );

	///// ALL FIELDS /////
	if( $fields == 'all' || in_array( 'all', $fields ) )
		$fields = pw_get_xprofile_fields();

	// Get each requested field
	$xprofile = array();
	foreach ( $fields as $field ){
		// Get data from Buddypress API
		$field_value = xprofile_get_field_data( $field, $user_id );
		$field = str_replace(' ', '_', $field);

		// Sanitize Keys if option is set
		if( $vars['sanitize_keys'] )
			$field = sanitize_key( $field );

		// If a value is set
		if( isset($field_value) )
			$xprofile[ $field ] = $field_value;

	}

	return $xprofile;

}

function pw_set_bp_nav( $obj = 'bp_nav', $subkey, $value ){
	// Sets the value of a bp nav item
	// $obj = 		[ string ] 	// Possible values : 'bp_nav' / 'bp_options_nav'
	// $subkey = 	[ string ] 	// Dot delinated subkey to set
	// $value = 	[ string ] 	// What value to set it to
	global $bp;
	if( empty( $bp ) )
		return false;
	$bp->$obj = pw_set_obj( $bp->$obj, $subkey, $value );
	return $bp->$obj;
}

function pw_set_bp_nav_name( $subkey, $name, $icon_class, $icon_side ){
	// Sets the name of a BuddyPress nav item
	// $subkey = 		[ string ]	// Which subkey to set, ie. 'profile' / 'settings'
	// $name = 			[ string ]	// What name to set it to, such as an updated name
	// $icon_class =	[ string ]	// What icon class to use, ie. 'icon-flag'
	// $icon_side = 	[ string ]	// Which side to place the icon : 'left' / 'right'
	$name = pw_add_icon( $name, $icon_class, $icon_side );
	return pw_set_bp_nav( 'bp_nav', $subkey . '.name', $name );
}

function pw_set_bp_subnav_name( $subkey, $name, $icon_class, $icon_side ){
	// Sets the name of a BuddyPress subnav item
	// $subkey = 		[ string ]	// Which subkey to set, ie. 'profile.public' / 'settings.notifications'
	// $name = 			[ string ]	// What name to set it to, such as an updated name
	// $icon_class =	[ string ]	// What icon class to use, ie. 'icon-flag'
	// $icon_side = 	[ string ]	// Which side to place the icon : 'left' / 'right'
	$name = pw_add_icon( $name, $icon_class, $icon_side );
	return pw_set_bp_nav( 'bp_options_nav', $subkey . '.name', $name );
}

function pw_add_icon( $string, $icon_class='', $icon_side='left' ){
	// Adds an icon to a string
	$icon = '';
	if( !empty( $icon_class ) )
		$icon = ' <i class="'.$icon_class.'"></i> ';
	if( $icon_side == "right" )
		$string = $string . $icon;
	else
		$string = $icon . $string;
	return $string;
}

?>