<?php

function pw_default_layouts_filter( $layouts ){
	if( !empty( $layouts ) )
		return $layouts;

	return array(
		'default' => array(
			'template' 	=> 'full-width',
			'header'	=> array(
				'id' => 'theme-header'
				),
			'footer'	=> array(
				'id' => 'theme-footer'
				),
			)
		);
}
add_filter( 'pw_default_layouts', 'pw_default_layouts_filter', 9 );

function pw_get_current_layout( $vars = array() ){

	global $pw;

	// If layouts module is not activated, return false
	if( !in_array( 'layouts', $pw['info']['modules'] ) )
		return false;

	// An array with the current context(s)
	$contexts = pw_current_context();

	// Set Layout Variable
	$layout = false;

	// Get layouts
	$pwLayouts = pw_get_option( array( 'option_name' => PW_OPTIONS_LAYOUTS ) );

	// If no layouts have been saved yet
	if( empty( $pwLayouts ) ){
		// Apply filter to get default layouts configuration
		$pwLayouts = apply_filters( 'pw_default_layouts', array() );
	}

	/// DEFINE POST ID ///
	global $post;
	// Get use provided vars.post_id to override current post
	$get_post_id = _get( $vars, 'post_id' );
	$post_id = ( empty( $get_post_id ) ) ?
		$post->ID : $vars['post_id'];

	/// GET LAYOUT : FROM POSTMETA : OVERRIDE ///
	// Check for layout override in : post_meta.pw_meta.layout
	$override_layout = pw_get_wp_postmeta( array(
		'post_id' => $post_id,
		'sub_key' => 'layout'
		));
	
	// If override layout exists
	if( $override_layout != false && !empty( $override_layout ) ){
		$layout = $override_layout;
		$layout['source'] = 'post_meta';
	}

	/// GET LAYOUT : FROM CONTEXT ///
	if( !$layout || _get( $layout, 'template' ) == 'default' ){
		// Iterate through all the current contexts
		// And find a match for it
		foreach( $contexts as $context ){
			$test_layout = _get( $pwLayouts, $context );
			// If there is a match
			if( (bool) $test_layout ){
				$layout = $test_layout;
				$layout['source'] = $context;
			}
		}
	}

	/// GET LAYOUT : DEFAULT LAYOUT : FALLBACK ///
	if( !$layout || $layout['template'] == 'default' ){ //  || $layout['layout'] == 'default'

		// Get default layout from post parent's layout
		$get_post = get_post( $post_id );
		if( $get_post->post_parent !== 0 )
			$layout = pw_get_current_layout( array(
				'post_id' => $get_post->post_parent
				));

		// Get from 'default' option setting
		else if( !empty( $pwLayouts ) )
			$layout = _get( $pwLayouts, 'default' );
		// Get from theme filter
		else
			$layout = apply_filters( 'pw_default_layout', array() );

		$layout['source'] = 'default';

	}

	// FILL IN DEFAULT VALUES
	// In case of incomplete layout values
	if( _get( $layout, 'source' ) != 'default' ){
		// Get the default layout
		$default_layout = _get( $pwLayouts, 'default' );

		// Merge it with the default layout, in case values are missing
		$layout = array_replace_recursive( $default_layout, $layout );

		// TODO : THIS BETTER TECHNIQUE
		// Fill in default header and footer
		if( empty( $layout['header']['id'] ) )
			$layout['header']['id'] = $default_layout['header']['id'];
		if( empty( $layout['footer']['id'] ) )
			$layout['footer']['id'] = $default_layout['footer']['id'];
	}

	// Autocorrect layout in case of migrations
	$layout = pw_autocorrect_layout( $layout );

	// Apply filter so that $layout can be over-ridden
	$layout = apply_filters( 'pw_layout', $layout );

	return $layout;

}

?>