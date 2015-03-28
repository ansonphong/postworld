<?php

define( 'PW_OPTIONS_PROGRESS', 'postworld-progress' );

function pw_update_progress( $key, $current, $total, $meta = array() ){

	$value = array();
	$value['active'] = true;

	$value['items'] = array(
		'current' 	=>	(int) $current,
		'total'		=>	(int) $total,
		);

	if( isset( $meta ) && !empty( $meta ) )
		$value['meta'] = $meta;

	wp_cache_flush();
	
	return pw_set_option( array(
		'option_name'	=>	PW_OPTIONS_PROGRESS,
		'key'			=> 	$key,
		'value'			=>	$value,
		));

}

function pw_get_progress( $key, $flush = false ){

	if( $flush === true )
		// Flushes the WordPress Object Cache
		wp_cache_flush();

	$value = pw_get_option( array(
		'option_name' 	=> PW_OPTIONS_PROGRESS,
		'cache'		 	=> false,
		));

	return _get( $value, $key );

}

function pw_end_progress( $key ){
	$value = pw_get_progress( $key, true );

	if( !is_array($value) )
		return $value;

	$value = array();
	$value['active'] = false;
	$value['ended'] = date("Y-m-d H:i:s");
	$value['items']['current'] = 0;
	$value['meta'] = array(
		'current_label' => '',
		'current' => 0,
		'total' => 0
		);

	pw_set_option( array(
		'option_name'	=>	PW_OPTIONS_PROGRESS,
		'key'			=> 	$key,
		'value'			=>	$value,
		));

	return $value;

}

function pw_progress_is_active( $key ){
	// Flush the options object cache so we don't get an old value
	wp_cache_flush();
	// Get the active progress key
	return pw_grab_option( PW_OPTIONS_PROGRESS, $key.'.active', true );
}

function pw_progress_kill_if_inactive( $key ){
	if( pw_progress_is_active( $key ) === false )
		die;
}

function pw_delete_progress( $key ){

	$value = pw_get_option( array(
		'option_name' => PW_OPTIONS_PROGRESS,
		));

	if( isset( $value[$key] ) )
		unset( $value[$key] );

	return pw_set_option( array(
		'option_name' => PW_OPTIONS_PROGRESS,
		'option_value' => $value
		));

}


?>