<?php
/**
 * Register the shortcode for Postworld Feeds
 *
 * @todo Enable full feed customization within VC.
 */
add_action( 'pw_vc_shortcode_'.'feed', 'pw_vc_map_shortcode_feed' );
function pw_vc_map_shortcode_feed(){

	$feeds = pw_grab_option( PW_OPTIONS_FEEDS );
	pw_log( 'FEEDS', $feeds );

	// Extrapolate the feed ID and Title into array

	//foreach()

	pw_vc_map( array(
		'name' => pw_theme_name() . ' ' . __( 'Feed', 'postworld' ),
		'base' => 'pw_feed',
		'icon' => 'vc_icon-vc-gitem-image',
		'category' => __( 'Post', 'postworld' ),
		'description' => __( 'Inserts a custom feed.', 'postworld' ),
		'function' => 'pw_vc_shortcode',
		'params' => array(

			array(
				'type' => 'dropdown',
				'heading' => __( 'Feed', 'postworld' ),
				'description' => __( 'Select which feed to display.', 'postworld' ),
				'param_name' => 'feed_id',
				'save_always' => true,
				'std' => null,
				'value' => array(
					__('Image','postworld') => 'image',
					__('Video','postworld') => 'video',
					),
				),

			),
		));

}