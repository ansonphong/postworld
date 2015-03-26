<?php

define( 'PW_OPTIONS_PROGRESS', 'postworld-progress' );

function pw_update_progress( $key, $current, $total, $meta ){

	$value = array();
	$value['status'] = 'active';
	$value['items'] = array(
		'current' 	=>	(int) $current,
		'total'		=>	(int) $total,
		);

	if( isset( $meta ) && !empty( $meta ) )
		$value['meta'] = $meta;

	return pw_set_option( array(
		'option_name'	=>	PW_OPTIONS_PROGRESS,
		'key'			=> 	$key,
		'value'			=>	$value,
		));

}

function pw_get_progress( $key ){
	return pw_grab_option( PW_OPTIONS_PROGRESS, $key );
}

function pw_end_progress( $key ){

	$value = pw_get_progress( $key );

	if( !is_array($value) )
		return false;

	$value = array();
	$value['status'] = 'done';
	$value['ended'] = date("Y-m-d H:i:s");
	unset( $value['items']['current'] );

	return pw_set_option( array(
		'option_name'	=>	PW_OPTIONS_PROGRESS,
		'key'			=> 	$key,
		'value'			=>	$value,
		));
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