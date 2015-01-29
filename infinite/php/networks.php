<?php

function i_social_menu( $vars ){
	// DEPRECIATED
	return pw_social_menu( $vars );
}

function pw_social_menu( $vars = array() ){

	// Set Default Variables
	$default_vars = array(
		'style'		=>	'default',
		'template'	=>	'views/networks/social-menu.php',
		'tooltip_placement'	=>	'bottom',
		'classes'	=>	'network-icon',
		'meta'	=>	array(
			'classes'	=>	'',
			'target'	=>	'_blank',
			),
		);
	$vars = array_replace_recursive( $default_vars, $vars );
	
	extract( $vars );

	// Get Option Values & decode from JSON
	$social_options = pw_get_option( array( 'option_name'	=> PW_OPTIONS_SOCIAL ));

	// Get Networks Social Meta
	$networks_social_meta = pw_find_where( pw_social_meta(), array( 'id' => 'networks' ) );
	$networks = $networks_social_meta["fields"];

	///// PRE-PROCESS DATA /////
	// Iterate through networks
	$networks_menu = array();
	for( $i=0; $i<count($networks); $i++ ){
		// Current Network
		$network = $networks[$i];
		// Get the saved value
		$network_value = _get( $social_options, 'networks.'.$network['id'] );
		// If no value set, or not public, continue
		if( empty( $network_value ) || !_get( $network, '_public' ) )
			continue;
		// Generate the link URL
		$network['link'] = _get($network,'prepend_url').$network_value;
		// Inject 'meta' variables, input into function
		$network = array_replace_recursive( $network, $vars['meta'] );
		// Add to networks menu
		$networks_menu[] = $network;
	}

	// Get template path
	$template = pw_get_template ( 'social', 'links', 'php', 'dir' );

	// OB include
	return pw_ob_include( $template, $networks_menu );

}


?>