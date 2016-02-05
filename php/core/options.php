<?php
/* ___        _   _                 
  / _ \ _ __ | |_(_) ___  _ __  ___ 
 | | | | '_ \| __| |/ _ \| '_ \/ __|
 | |_| | |_) | |_| | (_) | | | \__ \
  \___/| .__/ \__|_|\___/|_| |_|___/
       |_|                          
///////////// --------- /////////////*/

define( 'pw_option_name',	'pw-options' );

function pw_set_option_obj($vars){
	/*
		- Sets a value for the given option under the defined key
			in the `wp_options` table
		- Object values passed in can be passed as PHP objects or Arrays,
			and they will automatically be converted and stored as JSON
		
		PARAMETERS:
		$vars = array(
			"option_name" 	=>	[string] 	(optional)
			"key"		=>	[string],	(required)
			"value" 		=>	[mixed],	(required)
			);
	*/

	// Security Check
	if( !current_user_can( 'manage_options' ) )
		return false;

	// Extract Variables
	extract($vars);

	if( !isset( $option_name ) )
		$option_name = pw_option_name;

	///// KEY /////
	if( !isset($key) )
		return array( 'error' => 'Sub-key not specified.' ); 

	///// SETUP DATA /////
	// Check if the option exists
	$option_value = get_option( $option_name, '' );

	// If it exists, decode it from a JSON string into an object
	if( !empty($option_value) )
		$option_value = json_decode($option_value, true);
	// If it does not exist, define it as an empty array
	else
		$option_value = array();

	///// SET VALUE /////
	$option_value = pw_set_obj( $option_value, $key, $value );

	// Encode back into JSON
	$option_value = json_encode( $option_value );

	// Set user meta
	$update_option = update_option( $option_name, $option_value );

	// BOOLEAN : True on successful update, false on failure.
	return $update_option;

}


function pw_get_option_obj($vars){
	/*
	- Gets meta key for the given user under the defined key
		in the `wp_usermeta` table
	
		PARAMETERS:
		$vars = array(
			"option_name" 	=>	[string] 	(optional)
			"key"			=>	[string],
			"format" 		=>	[string] 	"JSON" / "ARRAY" (default),
			
			);
	*/

	extract($vars);
	$option_name = pw_option_name;

	///// KEY /////
	if( !isset($key) )
		$key = '';

	///// GET DATA /////
	// Check if the meta key exists
	$option_value = get_option( $option_name, '' );
	if( empty($option_value) )
		return false;

	// Decode from JSON
	$option_value = json_decode( $option_value, true );

	// Get Subkey
	$return = pw_get_obj( $option_value, $key );
	if( $return == false )
		return $return;

	///// FORMAT /////
	if( isset($format) && $format == 'JSON' )
		return json_encode( $return );
	
	return $return;

}

function pw_update_option( $option, $value ){
	if( current_user_can('manage_options') ){
		update_option( $option, $value );
		return get_option($option);
	}
	else
		return array('error'=>'No access.');
}


/**
 * Provides the standard re-usable options
 * in a multi-dimentional array.
 */
function pw_get_options_data(){

	$options = array(

		'general' => array(
			'none'  => false,
			'doubleSwitch' => array(
				array(
					'value' => true,
					'name' => _x( 'Yes', 'switch yes/no', 'postworld' ),
				),
				array(
					'value' => false,
					'name' => _x( 'No', 'switch yes/no', 'postworld' ),
				),
			),
			'tripleSwitch' => array(
				array(
					'value' => "default",
					'name' => _x( 'Default', 'default option', 'postworld' ),
				),
				array(
					'value' => true,
					'name' => _x( 'Yes', 'switch yes/no', 'postworld' ),
				),
				array(
					'value' => false,
					'name' => _x( 'No', 'switch yes/no', 'postworld' ),
				),
			),
			'customSwitch' => array(
				array(
					'value' => false,
					'name' => _x( 'None', 'switch', 'postworld' ),
				),
				array(
					'value' => 'custom',
					'name' => _x( 'Custom', 'switch', 'postworld' ),
				)
			),
			'defaultAndCustomDoubleSwitch' => array(
				array(
					'value' => "default",
					'name' => _x( 'Default', 'default option', 'postworld' ),
				),
				array(
					'value' => false,
					'name' => _x( 'No', 'switch yes/no', 'postworld' ),
				),
				array(
					'value' => true,
					'name' => _x( 'Yes', 'switch yes/no', 'postworld' ),
				),
				array(
					'value' => 'custom',
					'name' => _x( 'Custom', 'switch', 'postworld' ),
				)
			),
			'defaultCustomSwitch' => array(
				array(
					'value' => 'default',
					'name' => _x( 'Default', 'default option', 'postworld' ),
				),
				array(
					'value' => 'custom',
					'name' => _x( 'Custom', 'switch', 'postworld' ),
				)
			),
		),

		'style' => array(
			'backgroundPosition' => array(
				'parallax',
				'center top',
				'center center',
				'center bottom',
				'left top',
				'left center',
				'left bottom',
				'right top',
				'right center',
				'right bottom',
				'initial',
			),
			'backgroundAttachment' => array(
				'scroll',
				'fixed',
				'local',
			),
			'backgroundRepeat' => array(
				'repeat',
				'repeat-x',
				'repeat-y',
				'no-repeat',
			),
			'backgroundSize' => array(
				'cover',
				'contain',
			),
			'textAlign' => array(
				'left',
				'center',
				'right',
			),
		),

		'share' => array(
			'meta' => array(
				array(
					'name' => _x( 'Facebook', 'social network', 'postworld' ),
					'id' => 'facebook',
					'icon' => 'pwi-facebook-square',
					'selected' => true,
				),
				array(
					'name' => _x( 'Twitter', 'social network', 'postworld' ),
					'id' => 'twitter',
					'icon' => 'pwi-twitter-square',
					'selected' => true,
				),
				array(
					'name' => _x( 'Reddit', 'social network', 'postworld' ),
					'id' => 'reddit',
					'icon' => 'pwi-reddit-square',
					'selected' => true,
				),
				array(
					'name' => _x( 'Tumblr', 'social network', 'postworld' ),
					'id' => 'tumblr',
					'icon' => 'pwi-tumblr-square',
					'selected' => false,
				),
				array(
					'name' => _x( 'Google Plus', 'social network', 'postworld' ),
					'id' => 'google_plus',
					'icon' => 'pwi-google-plus-square',
					'selected' => true,
				),
				array(
					'name' => _x( 'Pinterest', 'social network', 'postworld' ),
					'id' => 'pinterest',
					'icon' => 'pwi-pinterest-square',
					'selected' => false,
				),
				array(
					'name' => _x( 'Email', 'sharing option', 'postworld' ),
					'id' => 'email',
					'icon' => 'pwi-mail-square',
					'selected' => true,
				),
			),
		),
		'header' => array(
			'type' => array(
				array(
					'slug' => 'default',
					'name' => 'Default',
				),
				array(
					'slug' => 'featured_image',
					'name' => 'Featured Image',
				),
				array(
					'slug' => 'slider',
					'name' => 'Slider',
				),
			),
		),
		'featured_image' => array(
			'placement' => array(
				array(
					'slug' => 'none',
					'name' => 'None',
				),
				array(
					'slug' => 'header',
					'name' => 'In Header',
				),
				array(
					'slug' => 'content',
					'name' => 'In Content',
				),
			),
		),
		'slider' => array(
			'transition' => array(
				array(
					'slug' => false,
					'name' => 'No Transition',
				),
				array(
					'slug' => 'fade',
					'name' => 'Fade',
				),
				array(
					'slug' => 'slide',
					'name' => 'Slide',
				),
			)
		),
		'post_content' => array(
			'columns' => array(
				array(
					'value' => 1,
					'name' => '1 Column',
				),
				array(
					'value' => 2,
					'name' => '2 Columns',
				),
				array(
					'value' => 3,
					'name' => '3 Columns',
				),
			),
		),
		'link_url' => array(
			'show_label' => array(
				array(
					'value' => 'default',
					'name' => 'Default',
				),
				array(
					'value' => false,
					'name' => 'No',
				),
				array(
					'value' => true,
					'name' => 'Yes',
				),
				array(
					'value' => 'custom',
					'name' => 'Custom',
				),
			),
		),

	);

	return apply_filters( 'pw_options_data', $options );

}



?>