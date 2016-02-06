<?php
/* ___        _   _                 
  / _ \ _ __ | |_(_) ___  _ __  ___ 
 | | | | '_ \| __| |/ _ \| '_ \/ __|
 | |_| | |_) | |_| | (_) | | | \__ \
  \___/| .__/ \__|_|\___/|_| |_|___/
       |_|                          
///////////// --------- /////////////*/

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
function pw_get_options_meta(){

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
					'name' => _x( 'Default', 'default option', 'postworld' ),
				),
				array(
					'slug' => 'featured_image',
					'name' => __( 'Featured Image', 'postworld' ),
				),
				array(
					'slug' => 'slider',
					'name' => __( 'Slider', 'postworld' ),
				),
			),
		),
		'featured_image' => array(
			'placement' => array(
				array(
					'slug' => 'none',
					'name' => _x( 'None', 'option', 'postworld' ),
				),
				array(
					'slug' => 'header',
					'name' => _x( 'In Header', 'in post/page header', 'postworld' ),
				),
				array(
					'slug' => 'content',
					'name' => _x( 'In Content', 'in post/page content', 'postworld' ),
				),
			),
		),
		'slider' => array(
			'transition' => array(
				array(
					'slug' => false,
					'name' => _x( 'No Transition', 'slider transition', 'postworld' ),
				),
				array(
					'slug' => 'fade',
					'name' => _x( 'Fade', 'slider transition', 'postworld' ),
				),
				array(
					'slug' => 'slide',
					'name' => _x( 'Slide', 'slider transition', 'postworld' ),
				),
			)
		),
		'post_content' => array(
			'columns' => array(
				array(
					'value' => 1,
					'name' => _x( '1 Column', 'post content columns', 'postworld' ),
				),
				array(
					'value' => 2,
					'name' => _x( '2 Columns', 'post content columns', 'postworld' ),
				),
				array(
					'value' => 3,
					'name' => _x( '3 Columns', 'post content columns', 'postworld' ),
				),
			),
		),
		'link_url' => array(
			'show_label' => array(
				array(
					'value' => 'default',
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
				),
			),
		),

	);

	return apply_filters( 'pw_options_data', $options );

}



?>