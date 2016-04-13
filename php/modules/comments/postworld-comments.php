<?php
/**
 * @todo properly impliment comment switch.
 */

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
			// Native WP Comment settings are added to JS via core/includes 
			),
		);
	$settings = array_replace_recursive( $default_settings, $settings );
	return $settings;
}

/**
 * Pass the comments options to Javascript if the module is enabled.
 */
add_filter( PW_GLOBAL_OPTIONS, 'pw_module_comments_options' );
function pw_module_comments_options( $options ){
	if( !pw_module_enabled('comments') )
		return $options;

	// Get the saved comment options
	$comments_options = pw_grab_option( PW_OPTIONS_COMMENTS );

	// Merge in the native WordPress comment settings, to pass them to JS
	if( is_array($comments_options['wordpress']) )
		$comments_options['wordpress'] = array_replace( pw_get_wp_comment_settings(), $comments_options['wordpress'] );
	
	$options['comments'] = $comments_options;

	return $options;
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