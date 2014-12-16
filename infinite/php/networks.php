<?php

function i_social_menu( $vars ){
	// DEPRECIATED
	return pw_social_menu( $vars );
}

function pw_social_menu( $vars = array() ){

	$default_vars = array(
		'size'		=>	32,
		'style'		=>	'default',
		'template'	=>	'views/networks/social-menu.php',
		'tooltip_placement'	=>	'bottom',
		'classes'	=>	'network-icon',
		);

	$vars = array_replace_recursive( $default_vars, $vars );

	extract($vars);

	// Get Option Values & decode from JSON
	$social_options = pw_get_option( array( 'option_name'	=> PW_OPTIONS_SOCIAL ));

	// Get Networks Social Meta
	$networks_social_meta = pw_find_where( pw_social_meta(), array( 'id' => 'networks' ) );
	$networks = $networks_social_meta["fields"];

	$skip_networks = array( 'facebook_app_id', 'twitter_hashtags' );

	$template = pw_get_template ( 'panels', 'social-menu', 'php', 'dir' );

	$output = '';

	// Iterate through each network
	foreach( $networks as $network ){
		$meta = array();
		
		// Get the saved value
		$network_value = _get( $social_options, 'networks.'.$network['id'] );

		// If no value set, continue with next network
		if( empty( $network_value ) || in_array( $network["id"], $skip_networks ) )
			continue;
		
		// Generate the link URL
		$meta['network_link'] = $network['prepend_url'].$network_value;

		// Get Logo Image URL
		$meta['network_image_url'] = i_image_url('logos/'.$network["id"].'/'.$network["id"].'-'.$style.'-'.$size.'.png');

		// Specify Variables
		$meta['network_id'] = $network['id'];
		$meta['network_name'] = $network['name'];
		$meta['network_icon'] = $network['icon'];
		
		$meta = array_replace_recursive( $vars, $meta );

		// Generate HTML from template
		$output .= pw_ob_include( $template, $meta );

	}

	//pw_log( 'pw_social_menu' ); //  . json_encode( $template )

	return $output;

}


?>