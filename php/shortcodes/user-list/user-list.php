<?php
add_shortcode( 'user-feed', 'pw_user_feed_shortcode' );
add_shortcode( 'user-list', 'pw_user_feed_shortcode' );

function pw_user_feed_shortcode( $atts, $content = null, $tag ) {
	
	// Set the internal defaults
	$shortcode_defaults = array(
		"class" 	=> 	"",
		"size" 		=> 	"medium",
		"view" 		=> 	"list-h2o",
		"usernames"	=>	"",
		"user_ids"	=>	"",
		"fields"	=>	"all",
		"max"		=>	50,
		"orderby"	=>	"menu_order",
	);

	// Get over-ride defaults from the theme
	$shortcode_defaults = apply_filters( 'pw_user_list_shortcode_defaults', $shortcode_defaults, $tag );

	// Extract Shortcode Attributes, set defaults
	$vars = shortcode_atts( $shortcode_defaults, $atts );

	///// USERNAMES INPUT /////
	// If usernames attribute is provided
	if( !empty( $vars['usernames'] ) ){
		// Explode usernames on comma as delimiter
		$vars['usernames'] = explode( ',', $vars['usernames'] );
		// Trim white space
		$vars['usernames'] = array_filter( array_map( 'trim', $vars['usernames'] ) );
		// Convert usernames to IDs
		$vars['user_ids'] = pw_usernames_to_ids( $vars['usernames'] );
	}

	///// USER IDS INPUT /////
	else if( !empty( $vars['user_ids'] ) ){
		// Explode user_ids on comma as delimiter
		$vars['user_ids'] = explode( ',', $vars['user_ids'] );
		// Trim white space
		$vars['user_ids'] = array_filter( array_map( 'trim', $vars['user_ids'] ) );
	}

	///// TODO : Create way to create user lists with Postworld, embed by saved ID in postworld-user-lists

	// If no user IDs in the list, return here
	if( empty($vars['user_ids']) )
		return false;
	else
		// Sanitize the numeric user IDs into numeric values
		$vars['user_ids'] = pw_sanitize_numeric_array( $vars['user_ids'], true );


	///// PRINT USER LIST /////
	$output = pw_print_user_feed( array(
		'user_ids'	=>	$vars['user_ids'],
		'fields'	=>	$vars['fields'],
		'i'			=>	0,
		'atts'		=>	$atts,
		) );

	return $output;
	
}



function pw_print_user_feed( $vars = array() ){
	pw_get_xprofile_fields();

	$default_vars = array(
		'user_ids'	=>	array(),
		'fields'	=>	array(),
		'atts'		=>	array(),
		'view'		=>	'list-h2o',
		);

	$vars = array_replace_recursive( $default_vars, $vars );

	///// USERS DATA /////
	$users = pw_get_users( $vars['user_ids'], $vars['fields'] );

	///// GET TEMPLATE /////
	$default_template = 'list-h2o';
	$template_path = pw_get_user_template( $vars['view'] );

	// Get fallback template
	if( empty( $template_path ) ){
		$template_path = pw_get_user_template( $default_template );
		$vars['view'] = $default_template;
	}

	///// GENERATE OUTPUT /////
	$users_html = '';

	// Iterate through each provided user
	foreach( $users as $user ){
		// Initialize h2o template engine
		$h2o = new h2o($template_path);
		// Inject the user for use in template, ie. {{post.post_title}}
		$inject['user'] = $user;
		// Inject local vars for referrence in template
		$inject['atts'] = $vars;
		// Inject JSON string for development referrence. Access in template with : {{ json }}
		$inject['json'] = json_encode( array( 'user' => $user, 'atts' => $atts ) );
		// Add rendered HTML to the return data
		$users_html .= $h2o->render($inject);
	}

	// Add the users HTML to the $vars array
	$vars['users_html'] = $users_html;

	// Get the user feed template
	$list_template = pw_get_user_feed_template( $vars['view'], 'php', 'dir' );

	// Return with the string continaing the list template
	return pw_ob_include( $list_template, $vars );

}



?>