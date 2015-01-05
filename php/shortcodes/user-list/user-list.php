<?php
////////// CSS COLUMN PROPERTY SHORTCODE //////////

///// COLUMNS /////
function pw_user_list_shortcode( $atts, $content = null, $tag ) {
	
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

	}

	///// USER IDS INPUT /////
	else if( !empty( $vars['user_ids'] ) ){
		// Explode user_ids on comma as delimiter
		$vars['user_ids'] = explode( ',', $vars['user_ids'] );
		// Trim white space
		$vars['user_ids'] = array_filter( array_map( 'trim', $vars['user_ids'] ) );
	}

	// If no user IDs in the list, return here
	if( empty($vars['user_ids']) )
		return false;
	else
		// Sanitize the numeric user IDs into numeric values
		$vars['user_ids'] = pw_sanitize_numeric_array( $vars['user_ids'], true );


	///// USERS DATA /////
	$users = pw_get_users( $vars['user_ids'], $vars['fields'] );

	///// GET TEMPLATE /////
	$template_path = pw_get_user_template( 'user-' . $vars['view'] );
	// Get fallback template
	if( empty( $template_path ) )
		$template_path = pw_get_user_template( 'user-' . 'list-h2o' );

	///// GENERATE OUTPUT /////
	$output = '';

	// Iterate through each provided user
	foreach( $users as $user ){
		// Initialize h2o template engine
		$h2o = new h2o($template_path);
		// Inject the user for use in template, ie. {{post.post_title}}
		$inject['user'] = $user;
		// Inject local vars for referrence in template
		$inject['vars'] = $vars;
		// Inject JSON string for development referrence
		$inject['json'] = json_encode( array( 'user' => $user, 'vars' => $vars ) );

		// Add rendered HTML to the return data
		$output .= $h2o->render($inject);
	}

	return $output;
	
}

add_shortcode( 'user-list', 'pw_user_list_shortcode' );
add_shortcode( 'userlist', 'pw_user_list_shortcode' );


?>