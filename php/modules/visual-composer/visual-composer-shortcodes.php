<?php
/**
 * Register the shortcode for Postworld Feeds
 *
 * @todo Enable full feed customization within VC.
 */
add_filter( 'pw_vc_map_shortcodes', 'pw_vc_map_shortcode_feed' );
function pw_vc_map_shortcode_feed( $elements ){

	// Get the custom user-created Postworld Feeds
	$feeds = pw_grab_option( PW_OPTIONS_FEEDS );

	// Extrapolate the feed ID and Title into array
	$options = array();
	foreach( $feeds as $feed ){
		$options[ $feed['name'] ] = $feed['id'];
	}

	// Add it to the elements to be mapped
	$elements['pw-feed'] = array(
		'name' => pw_theme_name() . ' ' . __( 'Feed', 'postworld' ),
		'base' => 'pw-feed',
		'icon' => 'vc_icon-vc-gitem-image',
		'category' => __( 'Post', 'postworld' ),
		'description' => __( 'Inserts a custom feed.', 'postworld' ),
		'function' => 'pw_vc_shortcode',
		'params' => array(

			array(
				'type' => 'dropdown',
				'heading' => __( 'Feed', 'postworld' ),
				'description' => __( 'Select which feed to display.', 'postworld' ),
				'param_name' => 'id',
				'save_always' => true,
				'std' => null,
				'value' => $options,
				),

			),
		);

	return $elements;

}