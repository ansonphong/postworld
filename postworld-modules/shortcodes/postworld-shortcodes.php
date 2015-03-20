<?php
//////////////////// REGISTER SHORTCODES ////////////////////
global $pw;

if( in_array( 'shortcodes', $pw['info']['modules'] ) )
	add_action( 'init', 'pw_register_shortcodes' );

function pw_register_shortcodes(){

	// Enable shortcodes to be invoked by [pw-shortcode id="shortcodeId"]
	add_shortcode( 'pw-shortcode', 'pw_custom_shortcode_by_id' );

	global $pwShortcodeSnippets;
	$pwShortcodeSnippets = pw_get_option( array( 'option_name' => PW_OPTIONS_SHORTCODE_SNIPPETS ) );
	
	// Iterate through each shortcode, and make custom shortcode
	if( !empty( $pwShortcodeSnippets ) ){
		foreach( $pwShortcodeSnippets as $snippet ){
			// Get the snippet ID
			$id = _get( $snippet, 'id' );
			// Skip if it's empty
			if( empty( $id ) )
				continue;
			// Skip already existing shortcodes
			if( shortcode_exists( $id ) )
				continue;
			// Register the shortcode
			add_shortcode( $id, 'pw_custom_shortcode_snippet' );
		}
	}
	
}


function pw_custom_shortcode_snippet( $atts, $content=null, $tag ){

	// Extract the shortcode ID
	$shortcodeId = $tag;
	if( empty($shortcodeId)  )
		return false;

	// Get the shortcode snippets
	global $pwShortcodeSnippets;
	if( empty($pwShortcodeSnippets)  )
		return false;

	// Get the snippet by ID
	$snippet = pw_find_where( $pwShortcodeSnippets, array( 'id' => $shortcodeId ) );
	if( empty($snippet)  )
		return false;

	// Get the snippet type
	$type = _get( $snippet, 'type' );

	// Self enclosing snippets
	if( $type == 'self-enclosing' )
		return _get( $snippet, 'content' );

	// Enclosing snippets
	elseif( $type == 'enclosing' )
		return _get( $snippet, 'before_content' ) . do_shortcode( $content ) . _get( $snippet, 'after_content' );

	return false;

}


function pw_custom_shortcode_by_id( $atts, $content=null, $tag ){

	// Extract the shortcode ID
	$shortcodeId = _get( $atts, 'id' );
	if( empty($shortcodeId)  )
		return false;

	// Get the shortcode snippets
	$shortcodeSnippets = pw_get_option( array( 'option_name' => PW_OPTIONS_SHORTCODE_SNIPPETS ) );
	if( empty($shortcodeSnippets)  )
		return false;

	// Get the snippet by ID
	$snippet = pw_find_where( $shortcodeSnippets, array( 'id' => $shortcodeId ) );
	if( empty($snippet)  )
		return false;

	// Get the snippet type
	$type = _get( $snippet, 'type' );

	// Self enclosing snippets
	if( $type == 'self-enclosing' )
		return _get( $snippet, 'content' );

	// Enclosing snippets
	elseif( $type == 'enclosing' )
		return _get( $snippet, 'before_content' ) . do_shortcode( $content ) . _get( $snippet, 'after_content' );

	return false;

}

?>