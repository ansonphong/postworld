<?php
/*____  _                _                _           
 / ___|| |__   ___  _ __| |_ ___ ___   __| | ___  ___ 
 \___ \| '_ \ / _ \| '__| __/ __/ _ \ / _` |/ _ \/ __|
  ___) | | | | (_) | |  | || (_| (_) | (_| |  __/\__ \
 |____/|_| |_|\___/|_|   \__\___\___/ \__,_|\___||___/

////////////////////////////////////////////////////////////*/
//////////////////// REGISTER SHORTCODES ////////////////////

if( pw_module_enabled('shortcodes') )
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
		return do_shortcode( _get( $snippet, 'content' ) );

	// Enclosing snippets
	elseif( $type == 'enclosing' )
		return do_shortcode( _get( $snippet, 'before_content' ) . do_shortcode( $content ) . _get( $snippet, 'after_content' ) );

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

////////// SHORTCODE //////////
function pw_icons_shortcode( $atts, $content = null, $tag ) {

	$atts = shortcode_atts( array(
		'icon' => '',
		'class' => '',
		'color' => '',
		'size' => '',
	), $atts );
	$vars = $atts;

	$vars['tag'] = $tag;
	$template = pw_get_shortcode_template( "icons" );

	// Generate output	
	$output = pw_ob_include( $template, $vars );

	// Remove any line breaks
	$output = str_replace(array("\r", "\n", "\t"), "", $output);

	// Return template
	return $output; //do_shortcode();

}

function pw_general_shortcode( $atts, $content=null, $tag ) {
	return pw_shortcode( $atts, $content, $tag );
}

function pw_shortcode( $atts, $content=null, $tag ) {

	if( empty( $atts ) )
		$atts = array();
	else
		extract( shortcode_atts( array(
			'class' 	=> 	'',
			'color' 	=> 	'',
			'id'		=> 	'',
			'target' 	=> 	'',
			'size'		=>	'',
			'href'		=>	'',
			'icon'		=>	'',
		), $atts ) );

	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( $tag );
	// Set included file into a string/variable
	$shortcode = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	// Return template
	return do_shortcode($shortcode);
}

/**
 * Visual Composer Shortcode
 */
function pw_vc_shortcode( $atts, $content, $tag ){
	$vars = array(
		'atts' => $atts,
		'content' => $content,
		'tag' => $tag
		);
	$template_path = pw_get_shortcode_template( $tag );
	$output = pw_ob_include( $template_path, $vars );
	return do_shortcode($output);
}

function pw_skip_shortcode( $string ){
	$string = str_replace("[", "&#91;", $string);
	$string = str_replace("]", "&#93;", $string);
	return $string;
}

function pw_shortcode_example( $string, $echo = true ){

	$shortcode_string = pw_skip_shortcode($string);
	$shortcode_parsed = do_shortcode($string);

	// Start Output Buffering
	ob_start();
	// Get the template
	include pw_get_shortcode_template( "shortcodes-help-example" );
	// Set included file into a string/variable
	$html = ob_get_contents();
	// End Output Buffering
	ob_end_clean();

	if( $echo )
		echo $html;
	else
		return $html;

}

function pw_empty_shortcode(){
	return "";
}

/**
 * Counts the numer of instances of a shortcode
 * In a string of content.
 *
 * @param string $tag The shortcode tag to search for
 * @param string $content The content to search within
 * @return integer The number of instances of the shortcode in the content
 */
function pw_shortcode_count( $content, $tag ){
	return substr_count( $content, '['.$tag );
}


if( pw_module_enabled('shortcodes') ){

	add_shortcode( 'pw-icon', 	'pw_icons_shortcode' );

	/////////////// BASIC SHORTCODES //////////
	// BLOCKS
	add_shortcode( 'blocks', 	'pw_shortcode' );
	add_shortcode( 'block', 	'pw_shortcode' );
	add_shortcode( 'block-sub', 'pw_shortcode' );
	add_shortcode( 'blockquote','pw_shortcode' );

	// CALLOUTS
	add_shortcode( 'callout', 	'pw_shortcode' );
	add_shortcode( 'callout-xl','pw_shortcode' );

	// HTML
	add_shortcode( 'br', 'pw_shortcode' );
	add_shortcode( 'hr', 'pw_shortcode' );

	// BUTTONS
	add_shortcode( 'button', 'pw_shortcode' );

	/*
	add_shortcode( 'h1', 'pw_shortcode' );
	add_shortcode( 'h2', 'pw_shortcode' );
	add_shortcode( 'h3', 'pw_shortcode' );
	add_shortcode( 'h4', 'pw_shortcode' );
	*/

	/* // Currently not practical, so decomissioned
	add_shortcode( 'pw-slider', 'pw_slider_shortcode' );
	*/

	/////////////// ADVANCED SHORTCODES //////////
	include 'feed/feed.php';
	include 'menu/menu.php';
	include 'fonts/fonts.php';
	include 'pagelist/pagelist.php';
	include 'columns/columns.php';
	include 'alignments/alignments.php';
	include 'help/shortcodes-help.php';
	include 'colors/colors.php';
	include 'module/module.php';

}

?>