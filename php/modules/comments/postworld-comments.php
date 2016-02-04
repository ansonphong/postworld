<?php
/**
 * Third party comment handling.
 */
include "thirdparty-facebook.php";
include "thirdparty-disqus.php";

/**
 * Default Comment Settings
 */
add_filter( PW_OPTIONS_COMMENTS, 'pw_module_comments_defaults' );
function pw_module_comments_defaults( $settings ){
	$default_settings = array(
		'facebook'	=>	array(
			'enable'		=>	false,
			'numposts'		=>	5,
			'colorscheme'	=>	'light',	// light, dark
			'order_by'		=>	'social',	// social, reverse_time, time
			'href_from'		=>	'id',		// id, url
			'protocol' 		=>	null,		// null, http, https
			),
		'disqus'	=>	array(
			'enable'	=>	false,
			'shortname'	=>	'',		// site identifier
			),
		'wordpress' => array(
			'enable' => false,
			),
		);
	$settings = array_replace_recursive( $default_settings, $settings );
	return $settings;
}


/**
 * Add the comments template partial.
 */
add_filter( 'pw_template_partials', 'pw_comment_template_partials' );
function pw_comment_template_partials( $template_partials ){
	$template_partials = _set( $template_partials,
		'pw.comments',					// partials model path
		'pw_get_comments_thirdparty'	// function name
		);
	return $template_partials;
}

/**
 * Get the embed codes for all enabled thirdparty comment systems.
 */
function pw_get_comments_thirdparty( $vars = array() ){

	//pw_log( 'pw_get_comments_thirdparty : vars', $vars );

	// Gets the embed codes for all the third-party comments widgets
	//pw_log('comments', $vars);
	$output = '<!-- COMMENTS -->';

	// List slugs of supported comment services
	$services = array( 'facebook', 'disqus' );

	// Iterate through comment services
	foreach( $services as $service ){

		// Check if the service is enabled
		$enabled = pw_grab_option( PW_OPTIONS_COMMENTS, $service.'.enable' );
		
		//pw_log( json_encode($enabled) );

		// If not enabled
		if( $enabled == false )
			// Skip service
			continue;

		// Define the function name
		$function_name = 'pw_get_comments_' . $service;

		// Get the result of the function
		$comment_code = call_user_func( $function_name, $vars );

		// If the result is a string
		if( is_string( $comment_code ) )
			// Add it to the output
			$output .= $comment_code;

	}

	return $output;

}