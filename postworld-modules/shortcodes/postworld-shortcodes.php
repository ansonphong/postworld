<?php
//////////////////// REGISTER SHORTCODES ////////////////////
global $pw;

if( in_array( 'shortcodes', $pw['info']['modules'] ) )
	add_action( 'init', 'pw_register_shortcodes' );

function pw_register_shortcodes(){
	
	add_shortcode( 'pw-shortcode', 'pw_custom_shortcode' );

	/*
	$sidebars = pw_get_option( array( 'option_name' => PW_OPTIONS_SIDEBARS ) );
	
	if( is_array( $sidebars ) ){
		foreach($sidebars as $sidebar){
			register_sidebar( $sidebar );
		}
	}
	*/
}

function pw_custom_shortcode( $atts, $content=null, $tag ){

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
		return _get( $snippet, 'before_content' ) . $content . _get( $snippet, 'after_content' );

	return false;

}

?>